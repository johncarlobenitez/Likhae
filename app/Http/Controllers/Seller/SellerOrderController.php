<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\SellerOrder;
use App\Services\Fulfillment\ShipmentWorkflowService;
use App\Services\Maps\MapDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);

        $orders = SellerOrder::query()
            ->where('seller_profile_id', $seller->id)
            ->with(['order.buyer', 'order.address', 'order.payments', 'items.product.images', 'shipment.events', 'shipment.pickupRequests', 'shipment.riderAssignments.riderProfile.user', 'shipment.riderAssignments.liveLocation', 'shipment.logisticsCenter.address', 'shipment.sellerOrder.sellerProfile.businessAddress'])
            ->latest()
            ->get();

        $counts = SellerOrder::query()
            ->where('seller_profile_id', $seller->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('Seller.orders', [
            'seller' => $seller,
            'orders' => $orders,
            'counts' => $counts,
            'activeStatus' => strtoupper((string) $request->query('status', '')),
            'sellerOrders' => $orders->map(fn (SellerOrder $order): array => $this->viewOrder($order)),
            'statusCounts' => $orders
                ->groupBy(fn (SellerOrder $order): string => $this->statusKey($order->status))
                ->map->count(),
        ]);
    }

    public function transition(Request $request, SellerOrder $sellerOrder, ShipmentWorkflowService $workflow): RedirectResponse
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller && (int) $sellerOrder->seller_profile_id === (int) $seller->id, 403);

        $data = $request->validate([
            'action' => ['required', 'string', 'in:confirm,prepare,ready,handover_confirm,cancel'],
        ]);

        $workflow->sellerTransition($sellerOrder, $data['action'], $request->user());

        return back()->with('status', 'Seller order updated successfully.');
    }

    public function handover(Request $request, SellerOrder $sellerOrder, ShipmentWorkflowService $workflow): RedirectResponse
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller && (int) $sellerOrder->seller_profile_id === (int) $seller->id, 403);
        $data = $request->validate([
            'handover_method' => ['required', 'string', 'in:logistics_pickup,seller_dropoff'],
            'pickup_date' => ['nullable', 'date', 'after_or_equal:today'],
            'pickup_window' => ['nullable', 'string', 'max:80'],
            'pickup_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $workflow->requestSellerHandover(
            $sellerOrder,
            $request->user(),
            $data['handover_method'] === 'seller_dropoff',
            collect([$data['pickup_note'] ?? null, $data['pickup_date'] ?? null, $data['pickup_window'] ?? null])->filter()->implode(' · '),
        );

        return redirect()->route('seller.orders')->with('status', $data['handover_method'] === 'seller_dropoff'
            ? 'Drop-off registered. Bring the parcel and tracking number to your assigned logistics center.'
            : 'Logistics pickup requested.');
    }

    private function viewOrder(SellerOrder $order): array
    {
        $address = $order->order?->address;
        $items = $order->items;
        $assignment = $order->shipment?->riderAssignments
            ?->whereIn('status', ['ASSIGNED', 'ACCEPTED', 'IN_PROGRESS'])
            ->sortByDesc('id')
            ->first();

        return [
            'id' => $order->seller_order_number,
            'db_id' => $order->id,
            'date' => $order->created_at?->format('M d, Y g:i A'),
            'buyer_id' => $order->order?->buyer_user_id,
            'buyer' => $order->order?->buyer?->name ?? 'Buyer',
            'product' => $items->pluck('product_name')->filter()->join(', '),
            'first_item' => $items->first(),
            'variant' => $items->pluck('variant_description')->filter()->join(', ') ?: 'Standard',
            'quantity' => (int) $items->sum('quantity'),
            'total' => (float) $order->grand_total,
            'payment' => str($order->order?->payments?->first()?->method ?? 'PENDING')->headline()->toString(),
            'logistics_provider' => $order->shipment?->logisticsCenter?->business_name ?? 'Not assigned',
            'shipping' => $assignment?->riderProfile?->user?->name ?? 'Waiting for Logistics',
            'shipping_address' => $address?->formatted() ?? 'Address unavailable',
            'status' => str($order->status)->replace('_', ' ')->title()->toString(),
            'status_key' => $this->statusKey($order->status),
            'delivery' => $order->shipment,
            'map_markers' => app(MapDataService::class)->forShipment($order->shipment, true, true),
        ];
    }

    private function statusKey(string $status): string
    {
        return match ($status) {
            'PLACED' => 'placed',
            'CONFIRMED' => 'confirmed',
            'PREPARING' => 'preparing',
            'READY_FOR_PICKUP' => 'ready-for-pickup',
            'PICKED_UP' => 'shipping',
            'COMPLETED' => 'completed',
            'CANCELLED' => 'cancelled',
            default => strtolower(str_replace('_', '-', $status)),
        };
    }
}
