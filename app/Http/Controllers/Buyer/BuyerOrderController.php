<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer\Order;
use App\Models\Buyer\OrderItem;
use App\Models\Buyer\Review;
use App\Models\Logistics\ShipmentEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BuyerOrderController extends Controller
{
    public function success(Request $request): View
    {
        $order = null;
        if ($request->filled('order')) {
            $order = $request->user()
                ->orders()
                ->with(['sellerOrders.items.product', 'sellerOrders.shipment', 'address', 'payments'])
                ->where('order_number', $request->query('order'))
                ->first();
        }

        return view('Buyer.orders', [
            'mode' => 'success',
            'selectedOrder' => $order,
            'orders' => $request->user()->orders()->with(['sellerOrders.shipment', 'payments'])->latest()->paginate(10),
        ]);
    }

    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with(['sellerOrders.items', 'sellerOrders.shipment', 'payments'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('Buyer.orders', [
            'mode' => 'index',
            'orders' => $orders,
            'selectedOrder' => null,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless((int) $order->buyer_user_id === (int) $request->user()->id, 403);

        return view('Buyer.orders', [
            'mode' => 'show',
            'selectedOrder' => $order->load(['sellerOrders.items.review', 'sellerOrders.shipment.events', 'address', 'payments']),
            'orders' => $request->user()->orders()->with(['sellerOrders.shipment'])->latest()->paginate(10),
        ]);
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless((int) $order->buyer_user_id === (int) $request->user()->id, 403);
        abort_unless(in_array($order->status, ['PLACED', 'PROCESSING'], true), 409, 'This order can no longer be cancelled.');

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $order->update([
            'status' => 'CANCELLED',
            'payment_status' => 'CANCELLED',
            'cancelled_at' => now(),
            'cancellation_reason' => $data['reason'] ?? 'Cancelled by buyer.',
        ]);

        foreach ($order->sellerOrders as $sellerOrder) {
            $sellerOrder->update(['status' => 'CANCELLED']);
            if ($sellerOrder->shipment) {
                $sellerOrder->shipment->update(['current_status' => 'RETURNED']);
                ShipmentEvent::query()->create([
                    'shipment_id' => $sellerOrder->shipment->id,
                    'status' => 'RETURNED',
                    'actor_user_id' => $request->user()->id,
                    'notes' => 'Order cancelled by buyer before fulfillment.',
                    'occurred_at' => now(),
                ]);
            }
        }

        return back()->with('buyer_notice', 'Order cancelled.');
    }

    public function received(Request $request, Order $order): RedirectResponse
    {
        abort_unless((int) $order->buyer_user_id === (int) $request->user()->id, 403);

        $order->update([
            'status' => 'COMPLETED',
            'completed_at' => now(),
        ]);

        foreach ($order->sellerOrders as $sellerOrder) {
            $sellerOrder->update(['status' => 'COMPLETED']);
            if ($sellerOrder->shipment) {
                $sellerOrder->shipment->update(['current_status' => 'COMPLETED']);
                ShipmentEvent::query()->create([
                    'shipment_id' => $sellerOrder->shipment->id,
                    'status' => 'COMPLETED',
                    'actor_user_id' => $request->user()->id,
                    'notes' => 'Buyer confirmed receipt.',
                    'occurred_at' => now(),
                ]);
            }
        }

        return back()->with('buyer_notice', 'Order marked as completed.');
    }

    public function review(Request $request, OrderItem $item): RedirectResponse
    {
        $item->loadMissing('sellerOrder.order');
        abort_unless((int) $item->sellerOrder->order->buyer_user_id === (int) $request->user()->id, 403);
        abort_unless($item->sellerOrder->order->status === 'COMPLETED', 409, 'You can review after completing the order.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        Review::query()->updateOrCreate(
            ['order_item_id' => $item->id],
            [
                'buyer_user_id' => $request->user()->id,
                'rating' => (int) $data['rating'],
                'comment' => $data['comment'] ?? null,
                'status' => 'PUBLISHED',
            ]
        );

        return back()->with('buyer_notice', 'Review saved.');
    }
}
