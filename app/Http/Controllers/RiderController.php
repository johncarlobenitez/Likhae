<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\User;
use App\Models\WorkspaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RiderController extends Controller
{
    public function dashboard(): View
    {
        $rider = $this->rider();
        $deliveries = $this->baseDeliveryQuery($rider)->latest()->get();

        return view('rider.dashboard', [
            'riderStats' => [
                ['label' => 'Pickup Requests', 'value' => $deliveries->where('status', 'requested')->count()],
                ['label' => 'For Sorting Center', 'value' => $deliveries->whereIn('status', ['pickup_accepted', 'picked_up'])->count()],
                ['label' => 'Delivery Assignments', 'value' => $deliveries->whereIn('status', ['assigned', 'out_for_delivery'])->count()],
                ['label' => 'Completed Today', 'value' => $deliveries->where('status', 'delivered')->filter(fn (Delivery $delivery) => $delivery->delivered_at?->isToday())->count()],
            ],
            'recentDeliveries' => $deliveries->take(8)->map(fn (Delivery $delivery) => $this->row($delivery))->values(),
        ]);
    }

    public function pickups(): View
    {
        $rider = $this->rider();
        $pickups = Delivery::with(['order.seller', 'order.buyer', 'order.items.product'])
            ->where(function ($query) use ($rider) {
                $query->where('pickup_rider_id', $rider->id)
                    ->orWhere(function ($available) {
                        $available->whereNull('pickup_rider_id')->where('status', 'requested');
                    });
            })
            ->whereIn('status', ['requested', 'pickup_accepted', 'picked_up'])
            ->latest()
            ->get();

        return view('rider.pickups.index', [
            'pickups' => $pickups->map(fn (Delivery $delivery) => $this->row($delivery))->values(),
            'pickupStats' => [
                'ready' => $pickups->where('status', 'requested')->count(),
                'accepted' => $pickups->where('status', 'pickup_accepted')->count(),
                'picked_up' => $pickups->where('status', 'picked_up')->count(),
            ],
        ]);
    }

    public function pickupShow(Delivery $delivery): View
    {
        $this->authorizePickup($delivery);

        return view('rider.pickups.show', [
            'pickup' => $this->row($delivery->load(['order.seller', 'order.buyer', 'order.items.product'])),
            'delivery' => $delivery,
        ]);
    }

    public function acceptPickup(Delivery $delivery): RedirectResponse
    {
        $this->authorizePickup($delivery, true);
        abort_unless($delivery->status === 'requested', 422, 'Only requested pickups can be accepted.');

        $delivery->forceFill([
            'pickup_rider_id' => $this->rider()->id,
            'pickup_assigned_at' => $delivery->pickup_assigned_at ?: now(),
            'pickup_accepted_at' => now(),
            'status' => 'pickup_accepted',
        ])->save();

        return redirect()->route('rider.pickups.show', $delivery)->with('status', 'Pickup accepted.');
    }

    public function confirmPickedUp(Delivery $delivery): RedirectResponse
    {
        $this->authorizePickup($delivery);
        abort_unless($delivery->status === 'pickup_accepted', 422, 'Accept the pickup before confirming parcel collection.');

        $delivery->forceFill([
            'picked_up_at' => now(),
            'status' => 'picked_up',
        ])->save();

        return back()->with('status', 'Parcel pickup confirmed.');
    }

    public function deliverToSorting(Delivery $delivery): RedirectResponse
    {
        $this->authorizePickup($delivery);
        abort_unless($delivery->status === 'picked_up', 422, 'Confirm parcel pickup before delivering to the sorting center.');

        DB::transaction(function () use ($delivery) {
            $delivery->forceFill([
                'arrived_at_sorting_center_at' => now(),
                'status' => 'at_sorting_center',
            ])->save();

            $this->notifyLogistics('Parcel arrived at sorting center', "Parcel {$delivery->tracking_number} is ready for sorting.", route('logistics.parcels.show', $delivery));
        });

        return redirect()->route('rider.history')->with('status', 'Parcel delivered to sorting center.');
    }

    public function deliveries(): View
    {
        $rider = $this->rider();
        $deliveries = Delivery::with(['order.seller', 'order.buyer', 'order.items.product'])
            ->where('rider_id', $rider->id)
            ->whereIn('status', ['assigned', 'out_for_delivery'])
            ->latest()
            ->get();

        return view('rider.deliveries.index', [
            'deliveries' => $deliveries->map(fn (Delivery $delivery) => $this->row($delivery))->values(),
            'deliveryStats' => [
                'assigned' => $deliveries->where('status', 'assigned')->count(),
                'out_for_delivery' => $deliveries->where('status', 'out_for_delivery')->count(),
                'delivered_today' => Delivery::where('rider_id', $rider->id)->where('status', 'delivered')->whereDate('delivered_at', today())->count(),
                'failed_today' => Delivery::where('rider_id', $rider->id)->where('status', 'delivery_failed')->whereDate('failed_at', today())->count(),
            ],
        ]);
    }

    public function deliveryShow(Delivery $delivery): View|RedirectResponse
    {
        if ($delivery->rider_id !== $this->rider()->id && $this->canViewPickup($delivery)) {
            return redirect()->route('rider.pickups.show', $delivery);
        }

        $this->authorizeDelivery($delivery);

        return view('rider.deliveries.show', [
            'delivery' => $delivery->load(['order.seller', 'order.buyer', 'order.items.product']),
            'parcel' => $this->row($delivery),
        ]);
    }

    public function pickupFromSorting(Delivery $delivery): RedirectResponse
    {
        $this->authorizeDelivery($delivery);
        abort_unless($delivery->status === 'assigned', 422, 'Only assigned parcels can be picked up from sorting.');

        $delivery->forceFill([
            'delivery_picked_up_at' => now(),
            'status' => 'out_for_delivery',
        ])->save();

        return back()->with('status', 'Parcel picked up from sorting center and marked out for delivery.');
    }

    public function markDelivered(Delivery $delivery): RedirectResponse
    {
        $this->authorizeDelivery($delivery);
        abort_unless($delivery->status === 'out_for_delivery', 422, 'Parcel must be out for delivery first.');

        DB::transaction(function () use ($delivery) {
            $delivery->forceFill([
                'delivered_at' => now(),
                'status' => 'delivered',
            ])->save();

            $delivery->order?->update(['status' => 'shipping']);
        });

        return redirect()->route('rider.history')->with('status', 'Delivery marked as delivered.');
    }

    public function markFailed(Request $request, Delivery $delivery): RedirectResponse
    {
        $this->authorizeDelivery($delivery);
        abort_unless(in_array($delivery->status, ['assigned', 'out_for_delivery'], true), 422, 'Only active deliveries can be marked failed.');

        $validated = $request->validate([
            'failure_reason' => ['required', 'string', 'max:1000'],
        ]);

        $delivery->forceFill([
            'failed_at' => now(),
            'failure_reason' => $validated['failure_reason'],
            'status' => 'delivery_failed',
        ])->save();

        return redirect()->route('rider.history')->with('status', 'Delivery failure recorded.');
    }

    public function tracking(Delivery $delivery): View|RedirectResponse
    {
        if ($delivery->rider_id !== $this->rider()->id && $this->canViewPickup($delivery)) {
            return redirect()->route('rider.pickups.show', $delivery);
        }

        $this->authorizeDelivery($delivery);

        return view('rider.deliveries.tracking', [
            'delivery' => $delivery->load(['order.seller', 'order.buyer', 'order.items.product']),
            'parcel' => $this->row($delivery),
        ]);
    }

    public function history(): View
    {
        $rider = $this->rider();
        $history = $this->baseDeliveryQuery($rider)
            ->whereIn('status', ['at_sorting_center', 'delivered', 'delivery_failed', 'returned'])
            ->latest('updated_at')
            ->get();

        return view('rider.history.index', ['history' => $history->map(fn (Delivery $delivery) => $this->row($delivery))->values()]);
    }

    public function earnings(): View
    {
        return view('rider.earnings.index', [
            'earningsRows' => collect(),
            'earningsNotice' => 'No rider payout rule or earnings table is configured yet, so LIKHAE is not showing fake earnings.',
        ]);
    }

    public function profile(): View
    {
        return view('rider.profile.index', ['rider' => $this->rider()]);
    }

    private function baseDeliveryQuery(User $rider)
    {
        return Delivery::with(['order.seller', 'order.buyer', 'order.items.product'])
            ->where(function ($query) use ($rider) {
                $query->where('pickup_rider_id', $rider->id)
                    ->orWhere('rider_id', $rider->id)
                    ->orWhere(function ($available) {
                        $available->whereNull('pickup_rider_id')->where('status', 'requested');
                    });
            });
    }

    private function authorizePickup(Delivery $delivery, bool $allowUnassigned = false): void
    {
        $riderId = $this->rider()->id;
        abort_unless(
            $delivery->pickup_rider_id === $riderId || ($allowUnassigned && $delivery->pickup_rider_id === null),
            403
        );
    }

    private function canViewPickup(Delivery $delivery): bool
    {
        $riderId = $this->rider()->id;

        return $delivery->pickup_rider_id === $riderId
            || ($delivery->pickup_rider_id === null && $delivery->status === 'requested');
    }

    private function authorizeDelivery(Delivery $delivery): void
    {
        abort_unless($delivery->rider_id === $this->rider()->id, 403);
    }

    private function notifyLogistics(string $title, string $body, string $url): void
    {
        User::where('role', 'logistics')->where('status', 'active')->each(function (User $user) use ($title, $body, $url) {
            WorkspaceNotification::create([
                'user_id' => $user->id,
                'type' => 'logistics',
                'title' => $title,
                'body' => $body,
                'action_url' => $url,
            ]);
        });
    }

    private function row(Delivery $delivery): array
    {
        $order = $delivery->order;
        $items = $order?->items?->map(fn ($item) => [
            'name' => $item->product?->name ?: $item->product_name ?: 'Order item',
            'quantity' => (int) $item->quantity,
            'image' => $this->productImageUrl($item->product?->image_path),
        ])->values() ?? collect();
        $firstImage = $items->firstWhere('image')['image'] ?? asset('images/product-placeholder.svg');

        return [
            'id' => $delivery->id,
            'tracking' => $delivery->tracking_number ?: 'DEL-'.$delivery->id,
            'status' => strtoupper($delivery->status),
            'status_label' => str($delivery->status)->replace('_', ' ')->headline()->toString(),
            'seller' => $order?->seller?->business_name ?: $order?->seller?->store_name ?: $order?->seller?->name ?: 'Seller not found',
            'buyer' => $order?->buyer?->name ?: 'Buyer not found',
            'contact' => $order?->buyer?->contact_number ?: 'No contact recorded',
            'address' => $delivery->address ?: $order?->shipping_address ?: 'No address recorded',
            'items' => $items->sum('quantity'),
            'item_rows' => $items,
            'image' => $firstImage,
            'amount' => 'PHP '.number_format((float) ($order?->total_amount ?? 0), 2),
            'updated' => $delivery->updated_at?->format('M d, Y h:i A') ?: 'Not recorded',
            'failure_reason' => $delivery->failure_reason,
        ];
    }

    private function productImageUrl(?string $path): string
    {
        if (! $path) {
            return asset('images/product-placeholder.svg');
        }

        return Str::startsWith($path, ['http://', 'https://']) ? $path : Storage::url($path);
    }

    private function rider(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
