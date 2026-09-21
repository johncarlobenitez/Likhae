<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DispatchController extends Controller
{
    public function index(Request $request): View
    {
        $provider = $request->user()->logisticsProvider()->where('status','approved')->firstOrFail();
        $tracking = trim((string) ($request->input('tracking') ?: $request->route('tracking')));
        $shipmentRoute = $request->route('shipment');
        $shipmentId = $shipmentRoute instanceof Shipment ? $shipmentRoute->id : $shipmentRoute;
        $shipments = Shipment::with(['sellerOrder.order','sellerOrder.seller.pickupAddress','rider.user'])
            ->where('logistics_provider_id',$provider->id)
            ->when($tracking !== '', fn ($query) => $query->where('tracking_code', $tracking))
            ->when($shipmentId, fn ($query) => $query->whereKey($shipmentId))
            ->latest()->paginate(25)->withQueryString();
        $riders = $provider->riders()->where('is_active',true)->with('user')->orderBy('id')->get();
        return view('Logistics.dispatch',compact('shipments','riders'));
    }

    public function parcels(Request $request): View
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();

        $shipments = Shipment::with(['sellerOrder.order.buyer', 'rider.user'])
            ->where('logistics_provider_id', $provider->id)
            ->latest()
            ->get();

        $rows = $shipments->map(function (Shipment $shipment) {
            return [
                'id' => $shipment->id,
                'tracking' => $shipment->tracking_code,
                'order' => $shipment->sellerOrder?->order?->reference ?? 'N/A',
                'buyer' => $shipment->sellerOrder?->order?->buyer?->name ?? 'Buyer',
                'destination' => collect([$shipment->sellerOrder?->order?->shipping_address_snapshot['barangay'] ?? null, $shipment->sellerOrder?->order?->shipping_address_snapshot['city'] ?? null, $shipment->sellerOrder?->order?->shipping_address_snapshot['province'] ?? null])->filter()->implode(', '),
                'area' => $shipment->sellerOrder?->order?->shipping_address_snapshot['province'] ?? 'Unassigned',
                'rider' => $shipment->rider?->user?->name ?? 'Unassigned',
                'status' => str($shipment->status)->headline()->toString(),
            ];
        })->all();

        return view('Logistics.parcels.index', [
            'logisticsParcels' => $rows,
            'logisticsOverview' => [
                ['label' => 'Total Parcels', 'value' => $shipments->count(), 'tone' => 'primary'],
                ['label' => 'Waiting Sorting', 'value' => $shipments->where('status', 'unassigned')->count(), 'tone' => 'warning'],
                ['label' => 'Awaiting Rider', 'value' => $shipments->where('status', 'assigned')->count(), 'tone' => 'info'],
                ['label' => 'Delivered', 'value' => $shipments->where('status', 'delivered')->count(), 'tone' => 'success'],
            ],
        ]);
    }

    public function pickups(Request $request): View
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        $shipments = Shipment::with(['sellerOrder.order.buyer', 'sellerOrder.seller.user', 'rider.user', 'sellerOrder.order'])
            ->where('logistics_provider_id', $provider->id)
            ->whereIn('status', ['assigned', 'picked_up'])
            ->latest()
            ->get();

        $pickupRows = $shipments->map(function (Shipment $shipment) use ($provider) {
            $delivery = $shipment;
            $riders = $provider->riders()->where('is_active', true)->with('user')->get()->map(fn ($rider) => [
                'id' => $rider->id,
                'name' => $rider->user?->name ?? 'Rider',
                'area' => $rider->service_area ?? 'General',
                'workload' => Shipment::where('rider_id', $rider->id)->whereIn('status', ['assigned', 'picked_up', 'in_transit', 'out_for_delivery'])->count(),
            ]);

            return [
                'delivery' => $delivery,
                'parcel' => [
                    'tracking' => $shipment->tracking_code,
                    'order' => $shipment->sellerOrder?->order?->reference ?? 'N/A',
                    'seller' => $shipment->sellerOrder?->seller?->name ?? $shipment->sellerOrder?->seller?->user?->name ?? 'Seller',
                ],
                'riders' => $riders,
            ];
        })->all();

        return view('Logistics.pickups.index', compact('pickupRows'));
    }

    public function receive(Request $request): View
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        $tracking = trim((string) $request->query('tracking', ''));
        $delivery = $tracking !== '' ? Shipment::with(['sellerOrder.order.buyer', 'sellerOrder.items.product', 'rider.user'])
            ->where('logistics_provider_id', $provider->id)
            ->where('tracking_code', $tracking)
            ->first() : null;

        return view('Logistics.parcels.receive', [
            'tracking' => $tracking,
            'delivery' => $delivery,
            'logisticsReceiveParcel' => $delivery ? [
                'tracking' => $delivery->tracking_code,
                'order' => $delivery->sellerOrder?->order?->reference ?? 'N/A',
                'payment' => $delivery->sellerOrder?->order?->payment_method ?? 'N/A',
                'seller' => $delivery->sellerOrder?->seller?->name ?? 'Seller',
                'buyer' => $delivery->sellerOrder?->order?->buyer?->name ?? 'Buyer',
                'destination' => collect([$delivery->sellerOrder?->order?->shipping_address_snapshot['barangay'] ?? null, $delivery->sellerOrder?->order?->shipping_address_snapshot['city'] ?? null, $delivery->sellerOrder?->order?->shipping_address_snapshot['province'] ?? null])->filter()->implode(', '),
                'address' => $delivery->sellerOrder?->order?->shipping_address_snapshot['line1'] ?? 'Address not recorded',
                'items' => $delivery->sellerOrder?->items->map(fn ($item) => [
                    'name' => $item->product?->name ?? 'Item',
                    'variation' => $item->variant_name ?? 'Standard',
                    'price' => (float) ($item->unit_price_minor ?? 0) / 100,
                    'quantity' => (int) ($item->quantity ?? 1),
                ])->all() ?? [],
                'value' => (float) ($delivery->sellerOrder?->subtotal_minor ?? 0) / 100,
            ] : null,
        ]);
    }

    public function sorting(Request $request): View
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        $tracking = trim((string) $request->query('tracking', ''));
        $shipments = Shipment::with(['sellerOrder.order.buyer'])
            ->where('logistics_provider_id', $provider->id)
            ->where('status', 'picked_up')
            ->latest()->get();

        $selectedDelivery = $tracking !== '' ? $shipments->firstWhere('tracking_code', $tracking) : null;

        return view('Logistics.sorting.index', [
            'tracking' => $tracking,
            'selectedDelivery' => $selectedDelivery,
            'logisticsParcels' => $shipments->map(fn (Shipment $shipment) => [
                'id' => $shipment->id,
                'tracking' => $shipment->tracking_code,
                'order' => $shipment->sellerOrder?->order?->reference ?? 'N/A',
                'buyer' => $shipment->sellerOrder?->order?->buyer?->name ?? 'Buyer',
                'destination' => collect([$shipment->sellerOrder?->order?->shipping_address_snapshot['barangay'] ?? null, $shipment->sellerOrder?->order?->shipping_address_snapshot['city'] ?? null, $shipment->sellerOrder?->order?->shipping_address_snapshot['province'] ?? null])->filter()->implode(', '),
                'area' => $shipment->sellerOrder?->order?->shipping_address_snapshot['province'] ?? 'Unassigned',
                'status' => str($shipment->status)->headline()->toString(),
                'received' => $shipment->updated_at?->format('M d, Y') ?? 'Recently',
                'image' => asset('images/product-placeholder.svg'),
            ])->all(),
            'logisticsOverview' => [
                ['label' => 'Ready to Sort', 'value' => $shipments->count(), 'class' => 'bg-warning-soft text-warning'],
                ['label' => 'Awaiting Rider', 'value' => Shipment::where('logistics_provider_id', $provider->id)->where('status', 'assigned')->count(), 'class' => 'bg-primary-soft text-primary'],
                ['label' => 'In Transit', 'value' => Shipment::where('logistics_provider_id', $provider->id)->whereIn('status', ['in_transit', 'out_for_delivery'])->count(), 'class' => 'bg-info-soft text-info'],
                ['label' => 'Delivered', 'value' => Shipment::where('logistics_provider_id', $provider->id)->where('status', 'delivered')->count(), 'class' => 'bg-success-soft text-success'],
            ],
        ]);
    }

    public function tracking(Request $request): View
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        $tracking = trim((string) $request->query('tracking', ''));
        $delivery = $tracking !== '' ? Shipment::with(['sellerOrder.order.buyer'])
            ->where('logistics_provider_id', $provider->id)
            ->where('tracking_code', $tracking)
            ->first() : null;

        return view('Logistics.tracking.index', [
            'tracking' => $tracking,
            'delivery' => $delivery,
            'parcel' => $delivery ? [
                'tracking' => $delivery->tracking_code,
                'order' => $delivery->sellerOrder?->order?->reference ?? 'N/A',
                'buyer' => $delivery->sellerOrder?->order?->buyer?->name ?? 'Buyer',
                'destination' => collect([$delivery->sellerOrder?->order?->shipping_address_snapshot['barangay'] ?? null, $delivery->sellerOrder?->order?->shipping_address_snapshot['city'] ?? null, $delivery->sellerOrder?->order?->shipping_address_snapshot['province'] ?? null])->filter()->implode(', '),
                'status' => str($delivery->status)->headline()->toString(),
            ] : null,
        ]);
    }

    public function waybill(Request $request, string $tracking): View
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        $shipment = Shipment::with(['sellerOrder.order.buyer', 'rider.user'])
            ->where('logistics_provider_id', $provider->id)
            ->where('tracking_code', $tracking)
            ->firstOrFail();
        return view('Logistics.waybill', ['delivery' => $shipment, 'tracking' => $tracking]);
    }

    public function show(Request $request, Shipment $shipment): View
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        abort_unless($shipment->logistics_provider_id === $provider->id, 403);

        return view('Logistics.parcels.show', [
            'logisticsParcel' => [
                'id' => $shipment->id,
                'tracking' => $shipment->tracking_code,
                'order' => $shipment->sellerOrder?->order?->reference ?? 'N/A',
                'status' => str($shipment->status)->headline()->toString(),
                'status_key' => $shipment->status,
                'seller' => $shipment->sellerOrder?->seller?->name ?? 'Seller',
                'buyer' => $shipment->sellerOrder?->order?->buyer?->name ?? 'Buyer',
                'payment' => $shipment->sellerOrder?->order?->payment_method ?? 'N/A',
                'value' => (float) ($shipment->sellerOrder?->subtotal_minor ?? 0) / 100,
                'destination' => collect([$shipment->sellerOrder?->order?->shipping_address_snapshot['barangay'] ?? null, $shipment->sellerOrder?->order?->shipping_address_snapshot['city'] ?? null, $shipment->sellerOrder?->order?->shipping_address_snapshot['province'] ?? null])->filter()->implode(', '),
                'address' => $shipment->sellerOrder?->order?->shipping_address_snapshot['line1'] ?? 'Address not recorded',
                'rider' => $shipment->rider?->user?->name ?? 'Unassigned',
                'area' => $shipment->sellerOrder?->order?->shipping_address_snapshot['province'] ?? 'Unassigned',
                'condition' => 'As shipped',
                'contact' => $shipment->sellerOrder?->order?->buyer?->contact_number ?? 'Not recorded',
                'received_from' => $shipment->sellerOrder?->seller?->name ?? 'Seller',
                'notes' => $shipment->events->last()?->note ?? 'No logistics notes recorded.',
                'received_at' => $shipment->created_at?->format('M d, Y') ?? 'Recently',
                'items' => $shipment->sellerOrder?->items->map(fn ($item) => [
                    'name' => $item->product?->name ?? 'Item',
                    'variation' => $item->variant_name ?? 'Standard',
                    'price' => (float) ($item->unit_price_minor ?? 0) / 100,
                    'quantity' => (int) ($item->quantity ?? 1),
                ])->all() ?? [],
            ],
        ]);
    }

    public function assign(Request $request, Shipment $shipment): RedirectResponse
    {
        $provider = $request->user()->logisticsProvider()->where('status','approved')->firstOrFail();
        abort_unless($shipment->logistics_provider_id === $provider->id,403);
        abort_unless(in_array($shipment->status,['unassigned','assigned','failed'],true),409,'Shipment cannot be reassigned after pickup.');
        $rider = Rider::where('logistics_provider_id',$provider->id)->where('is_active',true)->findOrFail($request->validate(['rider_id'=>['required','integer']])['rider_id']);
        $shipment->assignTo($rider, $request->user());
        return back()->with('status','Rider assigned.');
    }

    public function assignPickup(Request $request, Shipment $delivery): RedirectResponse
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        abort_unless($delivery->logistics_provider_id === $provider->id, 403);
        $rider = Rider::where('logistics_provider_id', $provider->id)->where('is_active', true)->findOrFail($request->validate(['rider_id' => ['required', 'integer']])['rider_id']);
        $delivery->assignTo($rider, $request->user());
        return back()->with('status', 'Rider assigned for pickup.');
    }

    public function confirmReceive(Request $request, Shipment $delivery): RedirectResponse
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        abort_unless($delivery->logistics_provider_id === $provider->id, 403);
        return redirect()->route('logistics.parcels.show', $delivery)->with('status', 'Parcel verified. Pickup status can only be confirmed by the assigned rider.');
    }

    public function sortParcel(Request $request, Shipment $delivery): RedirectResponse
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        abort_unless($delivery->logistics_provider_id === $provider->id, 403);
        return redirect()->route('logistics.parcels.show', $delivery)->with('status', 'Parcel verified for sorting. Assign an active rider from Dispatch when ready.');
    }
}
