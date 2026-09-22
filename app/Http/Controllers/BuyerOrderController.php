<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use App\Models\ReturnRequest;
use App\Models\SellerOrder;
use App\Models\WorkspaceNotification;
use App\Services\LedgerService;
use App\Support\BuyerMarketplace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BuyerOrderController extends Controller
{
    public function index(Request $request, string $mode = 'index', ?string $reference = null): View
    {
        $selected = null;
        $orders = SellerOrder::query()
            ->with(['order.payments', 'seller', 'items.product.images', 'items.productVariant', 'shipment.provider', 'shipment.events', 'events'])
            ->whereHas('order', fn ($query) => $query->where('buyer_id', $request->user()->id))
            ->latest()->get();

        if ($reference !== null && in_array($mode, ['show', 'return', 'review'], true)) {
            $selected = SellerOrder::query()->with('order')->where(function ($query) use ($reference) {
                if (ctype_digit($reference)) $query->orWhere('seller_orders.id', (int) $reference);
                $query->orWhereHas('order', fn ($order) => $order->where('reference', $reference));
            })->firstOrFail();
            abort_unless($selected->order->buyer_id === $request->user()->id, 403);
        }

        return view('Buyer.orders', [
            'mode' => $mode,
            'selectedOrderId' => $selected?->id ?? $reference,
            'buyerOrders' => $orders->map(fn (SellerOrder $order) => BuyerMarketplace::sellerOrder($order)),
        ]);
    }

    public function cancel(Request $request): RedirectResponse
    {
        $data = $request->validate(['order_id' => ['required', 'string'], 'reason' => ['required', 'string', 'max:255'], 'note' => ['nullable', 'string', 'max:1000']]);
        $order = $this->owned($request, $data['order_id']);
        abort_unless(in_array($order->status, ['pending', 'accepted', 'packed'], true), 422, 'This shop order can no longer be cancelled.');

        DB::transaction(function () use ($order, $request, $data): void {
            $order->transitionTo('cancelled', $request->user(), $data['reason'].($data['note'] ? ': '.$data['note'] : ''));
            foreach ($order->items as $item) {
                $item->productVariant?->increment('stock', $item->quantity);
            }
            WorkspaceNotification::create([
                'user_id' => $order->seller->user_id, 'type' => 'orders', 'title' => 'Order cancelled',
                'body' => "Buyer cancelled {$order->order->reference}.",
                'action_url' => route('seller.orders', ['order' => $order->id], false),
            ]);
        });

        return redirect()->route('buyer.orders')->with('buyer_notice', 'Shop order cancelled and variant stock restored.');
    }

    public function received(Request $request, string $reference): RedirectResponse
    {
        $order = $this->owned($request, $reference);
        if ($order->status === 'completed') {
            return back()->with('buyer_notice', 'This shop order was already completed.');
        }
        abort_unless($order->status === 'delivered' && $order->shipment?->status === 'delivered', 422, 'This parcel is not ready for receipt confirmation.');
        abort_if($order->returnRequest()->whereIn('status', ['requested', 'approved', 'disputed'])->exists(), 409, 'Resolve the open return before completing this order.');

        app(LedgerService::class)->complete($order, $request->user());

        return redirect()->route('buyer.orders.review', ['id' => $order->id])
            ->with('buyer_notice', 'Order received and completed. You can now review each item.');
    }

    public function review(Request $request, string $reference): RedirectResponse
    {
        $order = $this->owned($request, $reference);
        abort_unless(in_array($order->status, ['delivered', 'completed'], true), 422, 'Only delivered purchases can be reviewed.');
        abort_if($order->status === 'refunded', 422, 'Refunded purchases cannot be reviewed.');
        $data = $request->validate(['order_item_id' => ['nullable', 'integer'], 'rating' => ['required', 'integer', 'between:1,5'], 'review' => ['required', 'string', 'max:3000']]);
        $item = $data['order_item_id']
            ? $order->items->firstWhere('id', (int) $data['order_item_id'])
            : ($order->items->count() === 1 ? $order->items->first() : null);
        abort_unless($item, 422, 'Select the purchased item to review.');

        ProductReview::create([
            'order_item_id' => $item->id, 'product_id' => $item->product_id,
            'buyer_id' => $request->user()->id, 'seller_id' => $order->seller_id,
            'rating' => $data['rating'], 'body' => $data['review'], 'has_photo' => false,
        ]);

        return redirect()->route('buyer.orders.show', ['id' => $order->id])->with('buyer_notice', 'Review submitted.');
    }

    public function requestReturn(Request $request, string $reference): RedirectResponse
    {
        $order = $this->owned($request, $reference);
        abort_unless($order->delivered_at && in_array($order->status, ['delivered', 'completed'], true), 422, 'Only delivered purchases are eligible for return.');
        $days = (int) \App\Models\PlatformSetting::valueOf('return_window_days', 7);
        abort_if($order->delivered_at->lt(now()->subDays($days)), 422, 'The return window has ended.');
        abort_if($order->returnRequest()->exists(), 409, 'A return request already exists for this shop order.');
        $data = $request->validate(['request_type' => ['required', 'string', 'max:80'], 'reason' => ['required', 'string', 'max:255'], 'details' => ['required', 'string', 'min:20', 'max:3000']]);
        ReturnRequest::create(['seller_order_id' => $order->id, 'buyer_id' => $request->user()->id, 'reason' => $data['reason'], 'details' => $data['request_type'].': '.$data['details'], 'status' => 'requested']);

        return redirect()->route('buyer.orders.show', ['id' => $order->id])->with('buyer_notice', 'Return or refund request submitted.');
    }

    private function owned(Request $request, string $reference): SellerOrder
    {
        $order = SellerOrder::query()->with(['order.payments', 'seller', 'items.productVariant', 'shipment', 'returnRequest'])
            ->where(function ($query) use ($reference) {
                if (ctype_digit($reference)) $query->orWhere('seller_orders.id', (int) $reference);
                $query->orWhereHas('order', fn ($order) => $order->where('reference', $reference));
            })->firstOrFail();
        abort_unless($order->order->buyer_id === $request->user()->id, 403);
        return $order;
    }

    private function matches(SellerOrder $order, string $reference): bool
    {
        return (string) $order->id === $reference || $order->order->reference === $reference;
    }
}
