<?php

namespace App\Http\Controllers;

use App\Models\SellerOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $seller = $request->user()->sellers()->where('status','approved')->firstOrFail();
        $orders = SellerOrder::with(['order.buyer','items.product.images','shipment.provider'])
            ->where('seller_id',$seller->id)->latest()->get();
        $rows = $orders->map(function (SellerOrder $sellerOrder) {
            $first = $sellerOrder->items->first();
            $statusKey = match ($sellerOrder->status) {
                'pending' => 'placed', 'accepted' => 'confirmed', 'packed' => 'preparing',
                'ready_to_ship' => 'ready-for-pickup', 'shipped','delivered' => 'shipping',
                'refunded' => 'returns', default => $sellerOrder->status,
            };
            return [
                'id' => $sellerOrder->order->reference, 'db_id' => $sellerOrder->id,
                'buyer_id' => $sellerOrder->order->buyer_id, 'buyer' => $sellerOrder->order->buyer->name,
                'product' => $first?->product_name ?? 'Order items', 'variant' => $first?->variant_name ?? 'Standard',
                'quantity' => $sellerOrder->items->sum('quantity'), 'total' => ($sellerOrder->subtotal_minor + $sellerOrder->shipping_fee_minor) / 100,
                'payment' => str($sellerOrder->order->payment_method)->headline(), 'status_key' => $statusKey,
                'status' => str($sellerOrder->status)->headline(), 'date' => $sellerOrder->created_at?->format('M d, Y'),
                'shipping' => $sellerOrder->shipment?->provider?->name ?? 'Selected courier',
                'shipping_address' => collect($sellerOrder->order->shipping_address_snapshot)->only(['line1','barangay','city','province'])->filter()->implode(', '),
                'delivery' => $sellerOrder->shipment,
            ];
        });
        $selected = $request->input('order') ?: data_get($rows->first(),'id');
        return view('Seller.orders', [
            'pageMode'=>'orders', 'mode'=>$request->input('mode','index'), 'status'=>$request->input('status','all'),
            'selectedOrder'=>$selected, 'sellerOrders'=>$rows,
            'statusCounts'=>$rows->groupBy('status_key')->map->count(),
        ]);
    }

    public function transition(Request $request, SellerOrder $sellerOrder): RedirectResponse
    {
        $seller = $request->user()->sellers()->where('status','approved')->firstOrFail();
        abort_unless($sellerOrder->seller_id === $seller->id, 403);
        $status = $request->validate(['status'=>['required',Rule::in(['accepted','packed','ready_to_ship','cancelled'])]])['status'];
        $sellerOrder->transitionTo($status, $request->user());
        return back()->with('status','Order moved to '.str($status)->headline().'.');
    }
}
