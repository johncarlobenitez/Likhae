<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\ParcelAssignment;
use App\Models\User;
use App\Models\WorkspaceNotification;
use App\Services\ParcelWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RiderController extends Controller
{
    public function __construct(private readonly ParcelWorkflow $workflow) {}

    public function dashboard(): View
    {
        $rider = $this->rider();
        $deliveries = $this->baseDeliveryQuery($rider)->latest()->get();

        return view('rider.dashboard', [
            'riderStats' => [
                ['label' => 'Pickup Requests', 'value' => $deliveries->where('status', 'pickup_assigned')->count()],
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
        $pickups = Delivery::with(['order.seller', 'order.buyer', 'order.items.product', 'assignments'])
            ->whereHas('assignments', fn ($query) => $query
                ->where('rider_id', $rider->id)
                ->where('assignment_type', 'seller_pickup'))
            ->whereIn('status', ['pickup_assigned', 'pickup_accepted', 'picked_up'])
            ->latest()
            ->get();

        return view('rider.pickups.index', [
            'pickups' => $pickups->map(fn (Delivery $delivery) => $this->row($delivery))->values(),
            'pickupStats' => [
                'ready' => $pickups->where('status', 'pickup_assigned')->count(),
                'accepted' => $pickups->where('status', 'pickup_accepted')->count(),
                'picked_up' => $pickups->where('status', 'picked_up')->count(),
            ],
        ]);
    }

    public function pickupShow(Request $request, Delivery $delivery): View
    {
        $this->authorizePickup($delivery);

        return view('rider.pickups.show', [
            'pickup' => $this->row($delivery->load(['order.seller', 'order.buyer', 'order.items.product'])),
            'delivery' => $delivery,
            'verified' => $this->trackingMatches($request, $delivery),
        ]);
    }

    public function acceptPickup(Delivery $delivery): RedirectResponse
    {
        $assignment = $this->authorizePickup($delivery);
        if (in_array($delivery->status, ['pickup_accepted', 'picked_up'], true)) {
            return redirect()->route('rider.pickups.show', $delivery)->with('status', 'Pickup was already accepted.');
        }
        abort_unless($delivery->status === 'pickup_assigned', 422, 'Only your assigned pickups can be accepted.');

        DB::transaction(function () use ($delivery, $assignment) {
            $assignment->update(['status' => 'accepted', 'accepted_at' => now()]);
            $this->workflow->transition($delivery, 'pickup_accepted', $this->rider(), 'Pickup rider accepted the assignment.', ['pickup_accepted_at' => now()]);
        });

        return redirect()->route('rider.pickups.show', $delivery)->with('status', 'Pickup accepted.');
    }

    public function confirmPickedUp(Request $request, Delivery $delivery): RedirectResponse
    {
        $assignment = $this->authorizePickup($delivery);
        if ($delivery->status === 'picked_up') {
            return redirect()->route('rider.pickups.show', $delivery)->with('status', 'Parcel pickup was already confirmed.');
        }
        $this->validateTracking($request, $delivery);
        abort_unless($delivery->status === 'pickup_accepted', 422, 'Accept the pickup before confirming parcel collection.');

        DB::transaction(function () use ($delivery, $assignment) {
            $assignment->update(['status' => 'picked_up', 'picked_up_at' => now()]);
            $this->workflow->scan($delivery, 'seller_pickup', $this->rider(), $assignment);
            $this->workflow->transition($delivery, 'picked_up', $this->rider(), 'Parcel collected from seller.', ['picked_up_at' => now()]);
        });

        return back()->with('status', 'Parcel pickup confirmed.');
    }

    public function deliverToSorting(Delivery $delivery): RedirectResponse
    {
        $this->authorizePickup($delivery);
        abort_unless($delivery->status === 'picked_up', 422, 'Confirm parcel pickup before delivering to the sorting center.');

        $this->notifyLogistics('Parcel waiting for center receiving', "Parcel {$delivery->tracking_number} has arrived and must be scanned by logistics staff.", route('logistics.parcels.receive', ['tracking' => $delivery->tracking_number]));

        return back()->with('status', 'Arrival reported. Logistics staff must scan and receive the parcel.');
    }

    public function deliveries(): View
    {
        $rider = $this->rider();
        $deliveries = Delivery::with(['order.seller', 'order.buyer', 'order.items.product', 'assignments'])
            ->whereHas('assignments', fn ($query) => $query->where('rider_id', $rider->id)->where('assignment_type', 'final_delivery'))
            ->whereIn('status', ['assigned_to_rider', 'out_for_delivery'])
            ->latest()
            ->get();

        return view('rider.deliveries.index', [
            'deliveries' => $deliveries->map(fn (Delivery $delivery) => $this->row($delivery))->values(),
            'deliveryStats' => [
                'assigned' => $deliveries->where('status', 'assigned_to_rider')->count(),
                'out_for_delivery' => $deliveries->where('status', 'out_for_delivery')->count(),
                'delivered_today' => Delivery::where('rider_id', $rider->id)->where('status', 'delivered')->whereDate('delivered_at', today())->count(),
                'failed_today' => Delivery::where('rider_id', $rider->id)->where('status', 'delivery_failed')->whereDate('failed_at', today())->count(),
            ],
        ]);
    }

    public function deliveryShow(Request $request, Delivery $delivery): View|RedirectResponse
    {
        if ($delivery->rider_id !== $this->rider()->id && $this->canViewPickup($delivery)) {
            return redirect()->route('rider.pickups.show', $delivery);
        }

        $this->authorizeDelivery($delivery);

        return view('rider.deliveries.show', [
            'delivery' => $delivery->load(['order.seller', 'order.buyer', 'order.items.product']),
            'parcel' => $this->row($delivery),
            'verified' => $this->trackingMatches($request, $delivery),
            'released' => $delivery->scanEvents()->where('scan_type', 'delivery_release_scan')->exists(),
        ]);
    }

    public function pickupFromSorting(Request $request, Delivery $delivery): RedirectResponse
    {
        $this->authorizeDelivery($delivery);
        $this->validateTracking($request, $delivery);
        abort_unless($delivery->status === 'assigned_to_rider', 422, 'Only assigned parcels can be picked up from sorting.');

        $assignment = $this->finalAssignment($delivery);
        abort_unless($delivery->scanEvents()->where('scan_type', 'delivery_release_scan')->where('assignment_id', $assignment->id)->exists(), 422, 'Logistics must scan and authorize this parcel before rider collection.');
        DB::transaction(function () use ($delivery, $assignment) {
            $assignment->update(['status' => 'picked_up', 'accepted_at' => $assignment->accepted_at ?: now(), 'picked_up_at' => now()]);
            $this->workflow->scan($delivery, 'final_rider_scan', $this->rider(), $assignment);
            $this->workflow->transition($delivery, 'out_for_delivery', $this->rider(), 'Final rider collected parcel from sorting center.', ['delivery_picked_up_at' => now()]);
        });

        return back()->with('status', 'Parcel picked up from sorting center and marked out for delivery.');
    }

    public function acceptDelivery(Delivery $delivery): RedirectResponse
    {
        $assignment = $this->finalAssignment($delivery);
        abort_unless($delivery->status === 'assigned_to_rider', 422, 'This final-delivery assignment is no longer available.');
        if ($assignment->status === 'accepted') return back()->with('status', 'Delivery assignment was already accepted.');
        abort_unless($assignment->status === 'assigned', 422, 'This assignment cannot be accepted.');
        $assignment->update(['status' => 'accepted', 'accepted_at' => now()]);

        return back()->with('status', 'Delivery assignment accepted. Wait for Logistics to authorize parcel release.');
    }

    public function markDelivered(Delivery $delivery): RedirectResponse
    {
        $this->authorizeDelivery($delivery);
        abort_unless($delivery->status === 'out_for_delivery', 422, 'Parcel must be out for delivery first.');

        DB::transaction(function () use ($delivery) {
            $assignment = $this->finalAssignment($delivery);
            $assignment->update(['status' => 'completed', 'completed_at' => now()]);
            $this->workflow->scan($delivery, 'delivered', $this->rider(), $assignment);
            $this->workflow->transition($delivery, 'delivered', $this->rider(), 'Parcel delivered to buyer.', ['delivered_at' => now()]);

            $delivery->order?->update(['status' => 'shipping']);
        });

        return redirect()->route('rider.history')->with('status', 'Delivery marked as delivered.');
    }

    public function markFailed(Request $request, Delivery $delivery): RedirectResponse
    {
        $this->authorizeDelivery($delivery);
        abort_unless(in_array($delivery->status, ['assigned_to_rider', 'out_for_delivery'], true), 422, 'Only active deliveries can be marked failed.');

        $validated = $request->validate([
            'failure_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->workflow->transition($delivery, 'delivery_failed', $this->rider(), $validated['failure_reason'], ['failed_at' => now(), 'failure_reason' => $validated['failure_reason']]);

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
                    ;
            });
    }

    private function authorizePickup(Delivery $delivery): ParcelAssignment
    {
        return ParcelAssignment::where('delivery_id', $delivery->id)
            ->where('assignment_type', 'seller_pickup')
            ->where('rider_id', $this->rider()->id)
            ->firstOrFail();
    }

    private function canViewPickup(Delivery $delivery): bool
    {
        $riderId = $this->rider()->id;

        return ParcelAssignment::where('delivery_id', $delivery->id)->where('assignment_type', 'seller_pickup')->where('rider_id', $riderId)->exists();
    }

    private function authorizeDelivery(Delivery $delivery): void
    {
        $this->finalAssignment($delivery);
    }

    private function finalAssignment(Delivery $delivery): ParcelAssignment
    {
        return ParcelAssignment::where('delivery_id', $delivery->id)
            ->where('assignment_type', 'final_delivery')
            ->where('rider_id', $this->rider()->id)
            ->firstOrFail();
    }

    private function trackingMatches(Request $request, Delivery $delivery): bool
    {
        $tracking = trim((string) $request->query('tracking', ''));
        return $tracking !== '' && hash_equals((string) $delivery->tracking_number, $tracking);
    }

    private function validateTracking(Request $request, Delivery $delivery): void
    {
        $validated = $request->validate(['tracking' => ['required', 'string', 'max:120']]);
        abort_unless(hash_equals((string) $delivery->tracking_number, trim($validated['tracking'])), 422, 'The scanned tracking number does not match this assignment.');
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
