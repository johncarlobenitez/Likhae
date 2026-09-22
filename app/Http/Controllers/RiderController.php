<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RiderController extends Controller
{
    public function dashboard(Request $request): View
    {
        $rider = $request->user()->rider()->where('is_active', true)->firstOrFail();
        $shipments = Shipment::with(['sellerOrder.order.buyer','sellerOrder.items.product.images'])->where(fn ($query) => $query->where('pickup_rider_id', $rider->id)->orWhere('delivery_rider_id', $rider->id))->latest()->get();
        return view('rider.dashboard', [
            'riderStats' => [
                ['label'=>'Assigned Parcels','value'=>$shipments->where('status','assigned')->count()],
                ['label'=>'In Transit','value'=>$shipments->whereIn('status',['picked_up','in_transit'])->count()],
                ['label'=>'Out for Delivery','value'=>$shipments->where('status','out_for_delivery')->count()],
                ['label'=>'Completed Today','value'=>$shipments->where('status','delivered')->filter(fn($shipment)=>$shipment->updated_at?->isToday())->count()],
            ],
            'recentDeliveries' => $shipments->take(8)->map(function($shipment){$snapshot=$shipment->sellerOrder->order->shipping_address_snapshot??[];$path=$shipment->sellerOrder->items->first()?->product?->images?->first()?->path;return ['id'=>$shipment->id,'tracking'=>$shipment->tracking_code,'buyer'=>$shipment->sellerOrder->order->buyer?->name??'Buyer','address'=>collect([$snapshot['line1']??null,$snapshot['barangay']??null,$snapshot['city']??null])->filter()->implode(', '),'status'=>$shipment->status,'status_label'=>Str::headline($shipment->status),'image'=>$path?Storage::url($path):asset('images/product-placeholder.svg')];})->values(),
        ]);
    }

    public function pickups(Request $request): View
    {
        $rider = $request->user()->rider()->where('is_active', true)->firstOrFail();
        $shipments = Shipment::with(['sellerOrder.order.buyer', 'sellerOrder.seller.owner', 'sellerOrder.seller.pickupAddress', 'sellerOrder.items.product.images'])
            ->where('pickup_rider_id', $rider->id)
            ->latest()->get();

        $pickupStats = [
            'ready' => $shipments->where('status', 'pickup_assigned')->count(),
            'accepted' => $shipments->where('status', 'pickup_accepted')->count(),
            'picked_up' => $shipments->where('status', 'picked_up')->count(),
        ];

        $pickups = $shipments->filter(fn ($shipment) => in_array($shipment->status, ['pickup_assigned', 'pickup_accepted', 'picked_up', 'in_transit_to_hub'], true))->map(function ($shipment) {
            $snapshot = $shipment->sellerOrder->order->shipping_address_snapshot ?? [];
            $buyer = $shipment->sellerOrder->order->buyer?->name ?? 'Buyer';
            $seller = $shipment->sellerOrder->seller?->name ?? $shipment->sellerOrder->seller?->owner?->name ?? 'Seller';
            $pickupAddress = $shipment->sellerOrder->seller?->pickupAddress;
            $path = $shipment->sellerOrder->items->first()?->product?->images?->first()?->path;

            return [
                'id' => $shipment->id,
                'tracking' => $shipment->tracking_code,
                'seller' => $seller,
                'buyer' => $buyer,
                'address' => collect([$pickupAddress?->line1, $pickupAddress?->barangay, $pickupAddress?->city, $pickupAddress?->province])->filter()->implode(', ') ?: 'Seller pickup address not recorded',
                'items' => $shipment->sellerOrder->items->count(),
                'amount' => '₱'.number_format(($shipment->sellerOrder->subtotal_minor ?? 0) / 100, 2),
                'status' => match ($shipment->status) {
                    'pickup_assigned' => 'PICKUP_ASSIGNED',
                    'pickup_accepted' => 'PICKUP_ACCEPTED',
                    'picked_up' => 'PICKED_UP',
                    default => strtoupper($shipment->status),
                },
                'status_label' => str($shipment->status)->replace('_', ' ')->title(),
                'image' => $path ? Storage::url($path) : asset('images/product-placeholder.svg'),
            ];
        })->values();

        return view('rider.pickups.index', compact('pickups', 'pickupStats'));
    }

    public function pickupShow(Request $request, Shipment $shipment): View
    {
        $rider = $request->user()->rider()->where('is_active', true)->firstOrFail();
        abort_unless($shipment->pickup_rider_id === $rider->id, 403);

        $snapshot = $shipment->sellerOrder->order->shipping_address_snapshot ?? [];
        $pickupAddress = $shipment->sellerOrder->seller?->pickupAddress;
        $delivery = $shipment;
        $pickup = [
            'id' => $shipment->id,
            'tracking' => $shipment->tracking_code,
            'seller' => $shipment->sellerOrder->seller?->name ?? 'Seller',
            'buyer' => $shipment->sellerOrder->order->buyer?->name ?? 'Buyer',
            'address' => collect([$pickupAddress?->line1, $pickupAddress?->barangay, $pickupAddress?->city, $pickupAddress?->province])->filter()->implode(', ') ?: 'Seller pickup address not recorded',
            'items' => $shipment->sellerOrder->items->count(),
            'amount' => '₱'.number_format(($shipment->sellerOrder->subtotal_minor ?? 0) / 100, 2),
            'status' => match ($shipment->status) {
                'pickup_assigned' => 'PICKUP_ASSIGNED',
                'pickup_accepted' => 'PICKUP_ACCEPTED',
                'picked_up' => 'PICKED_UP',
                default => strtoupper($shipment->status),
            },
            'status_label' => str($shipment->status)->replace('_', ' ')->title(),
        ];

        $verified = $request->filled('tracking') && hash_equals($shipment->tracking_code, trim((string) $request->query('tracking')));
        return view('rider.pickups.show', compact('pickup', 'delivery', 'shipment', 'verified'));
    }

    public function deliveries(Request $request): View
    {
        $rider = $request->user()->rider()->where('is_active', true)->firstOrFail();
        $shipments = Shipment::with(['sellerOrder.order.buyer', 'sellerOrder.items.product.images'])
            ->where('delivery_rider_id', $rider->id)
            ->latest()->get();

        $deliveryStats = [
            'assigned' => $shipments->where('status', 'delivery_assigned')->count(),
            'out_for_delivery' => $shipments->where('status', 'out_for_delivery')->count(),
            'delivered_today' => $shipments->where('status', 'delivered')->filter(fn ($shipment) => $shipment->updated_at?->isToday())->count(),
            'failed_today' => $shipments->where('status', 'failed')->filter(fn ($shipment) => $shipment->updated_at?->isToday())->count(),
        ];

        $deliveries = $shipments->filter(fn ($shipment) => in_array($shipment->status, ['delivery_assigned', 'delivery_accepted', 'delivery_collected', 'out_for_delivery', 'delivered', 'failed'], true))->map(function ($shipment) {
            $snapshot = $shipment->sellerOrder->order->shipping_address_snapshot ?? [];
            $path = $shipment->sellerOrder->items->first()?->product?->images?->first()?->path;

            return [
                'id' => $shipment->id,
                'tracking' => $shipment->tracking_code,
                'buyer' => $shipment->sellerOrder->order->buyer?->name ?? 'Buyer',
                'address' => collect([$snapshot['line1'] ?? null, $snapshot['barangay'] ?? null, $snapshot['city'] ?? null, $snapshot['province'] ?? null])->filter()->implode(', '),
                'amount' => '₱'.number_format(($shipment->sellerOrder->subtotal_minor ?? 0) / 100, 2),
                'status' => $shipment->status,
                'status_label' => str($shipment->status)->replace('_', ' ')->title(),
                'image' => $path ? Storage::url($path) : asset('images/product-placeholder.svg'),
            ];
        })->values();

        return view('rider.deliveries.index', compact('deliveries', 'deliveryStats'));
    }

    public function deliveryShow(Request $request, Shipment $shipment): View
    {
        $rider = $request->user()->rider()->where('is_active', true)->firstOrFail();
        abort_unless($shipment->delivery_rider_id === $rider->id, 403);

        $snapshot = $shipment->sellerOrder->order->shipping_address_snapshot ?? [];
        $parcel = [
            'id' => $shipment->id,
            'tracking' => $shipment->tracking_code,
            'buyer' => $shipment->sellerOrder->order->buyer?->name ?? 'Buyer',
            'contact' => $shipment->sellerOrder->order->buyer?->contact_number ?? 'Not available',
            'address' => collect([$snapshot['line1'] ?? null, $snapshot['barangay'] ?? null, $snapshot['city'] ?? null, $snapshot['province'] ?? null])->filter()->implode(', '),
            'amount' => '₱'.number_format(($shipment->sellerOrder->subtotal_minor ?? 0) / 100, 2),
            'status' => $shipment->status,
            'status_label' => str($shipment->status)->replace('_', ' ')->title(),
        ];

        $verified = $request->filled('tracking') && hash_equals($shipment->tracking_code, trim((string) $request->query('tracking')));
        return view('rider.deliveries.show', [
            'parcel' => $parcel,
            'delivery' => $shipment,
            'released' => in_array($shipment->status, ['delivery_accepted', 'delivery_collected', 'out_for_delivery', 'delivered'], true),
            'verified' => $verified,
        ]);
    }

    public function history(Request $request): View
    {
        $rider = $request->user()->rider()->where('is_active', true)->firstOrFail();
        $shipments = Shipment::with(['sellerOrder.order.buyer', 'sellerOrder.items.product.images'])
            ->where(fn ($query) => $query->where('pickup_rider_id', $rider->id)->orWhere('delivery_rider_id', $rider->id))
            ->latest()->get();

        $history = $shipments->map(function ($shipment) {
            $snapshot = $shipment->sellerOrder->order->shipping_address_snapshot ?? [];
            $path = $shipment->sellerOrder->items->first()?->product?->images?->first()?->path;

            return [
                'id' => $shipment->id,
                'tracking' => $shipment->tracking_code,
                'buyer' => $shipment->sellerOrder->order->buyer?->name ?? 'Buyer',
                'status' => $shipment->status,
                'status_label' => str($shipment->status)->replace('_', ' ')->title(),
                'updated' => $shipment->updated_at?->format('M d, Y') ?? 'Recently',
                'failure_reason' => $shipment->events->where('status', 'failed')->last()?->note,
                'image' => $path ? Storage::url($path) : asset('images/product-placeholder.svg'),
            ];
        })->values();

        return view('rider.history.index', compact('history'));
    }

    public function earnings(Request $request): View
    {
        $request->user()->rider()->where('is_active',true)->firstOrFail();
        return view('rider.earnings.index',['earningsRows'=>collect(),'earningsNotice'=>'Rider compensation is managed by your logistics provider and no rider earning ledger is configured.']);
    }

    public function profile(Request $request): View
    {
        return view('rider.profile.index',['rider'=>$request->user()->rider()->where('is_active',true)->with(['user','provider'])->firstOrFail()]);
    }
}
