<?php

namespace App\Support;

use App\Models\Buyer\Cart;
use App\Models\Buyer\CartItem;
use App\Models\Seller\Message;
use App\Models\Buyer\Order;
use App\Models\Seller\Product;
use App\Models\User;
use App\Models\Seller\WorkspaceNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BuyerMarketplace
{
    public static function product(Product $product): array
    {
        $seller = $product->seller;
        $sellerName = $seller?->name ?: $seller?->owner?->name ?: 'LIKHAE Seller';
        $availableVariants = $product->relationLoaded('variants')
            ? $product->variants->where('is_active', true)->whereNull('deleted_at')->values()
            : collect();
        $primaryVariant = $availableVariants->sortBy('price_minor')->first();
        $price = ($primaryVariant?->price_minor ?? $product->min_price_minor ?? 0) / 100;
        $stock = (int) $availableVariants->sum('stock');
        $variants = $product->relationLoaded('variants')
            ? $availableVariants
                ->groupBy('option_name')
                ->map(fn ($rows) => $rows
                    ->filter(fn ($row) => trim((string) $row->value) !== '')
                    ->map(fn ($row) => [
                        'id' => $row->id,
                        'value' => $row->value,
                        'sku' => $row->sku,
                        'price' => $row->price_minor / 100,
                        'stock' => (int) ($row->stock ?? 0),
                    ])
                    ->values()
                    ->all())
                ->filter()
                ->all()
            : [];
        $variantStock = collect($variants)
            ->flatMap(fn ($options) => collect($options)->mapWithKeys(fn ($option) => [
                (string) $option['id'] => (int) $option['stock'],
            ]))
            ->all();
        $imagePath = $product->relationLoaded('images') ? $product->images->first()?->path : null;
        $image = $imagePath
            ? (Str::startsWith($imagePath, ['http://', 'https://'])
                ? $imagePath
                : Storage::url($imagePath))
            : asset('images/product-placeholder.svg');
        $gallery = collect([$image]);
        if ($product->relationLoaded('images')) {
            $gallery = $gallery
                ->merge($product->images->map(fn ($row) => Str::startsWith($row->path, ['http://', 'https://'])
                    ? $row->path
                    : Storage::url($row->path)));
        }
        $specs = $product->relationLoaded('specifications')
            ? $product->specifications->mapWithKeys(fn ($row) => [$row->name => $row->value])->all()
            : [];
        $sellerAvatar = $seller?->logo_path
            ? (Str::startsWith($seller->logo_path, ['http://', 'https://'])
                ? $seller->logo_path
                : Storage::url($seller->logo_path))
            : 'https://ui-avatars.com/api/?name='.urlencode($sellerName).'&background=561C17&color=fff';
        $reviews = $product->relationLoaded('reviews') ? $product->reviews : collect();
        $sold = $product->relationLoaded('orderItems')
            ? (int) $product->orderItems->filter(fn ($item) => $item->sellerOrder?->order && ! in_array($item->sellerOrder->status, ['cancelled', 'refunded'], true))->sum('quantity')
            : 0;

        return [
            'db_id' => $product->id,
            'id' => $product->id,
            'slug' => $product->slug,
            'sku' => $primaryVariant?->sku,
            'name' => $product->name,
            'category' => $product->category?->name ?: 'Uncategorized',
            'category_slug' => $product->category?->slug ?: Str::slug($product->category?->name ?: 'uncategorized'),
            'parent_category' => $product->category?->parent?->name,
            'parent_category_slug' => $product->category?->parent?->slug,
            'seller' => $sellerName,
            'seller_id' => $product->seller_id,
            'seller_slug' => Str::slug($sellerName).'-'.$product->seller_id,
            'seller_avatar' => $sellerAvatar,
            'location' => collect([$seller?->pickupAddress?->city, $seller?->pickupAddress?->province])->filter()->implode(', ') ?: 'Philippines',
            'price' => $price,
            'old_price' => $price,
            'discount' => 0,
            'rating' => round((float) ($reviews->avg('rating') ?: 0), 1),
            'reviews' => $reviews->count(),
            'customer_reviews' => $reviews->map(fn ($review) => [
                'rating' => (int) $review->rating,
                'body' => $review->body,
                'comment' => $review->body,
                'buyer' => $review->buyer?->name ?: 'Buyer',
                'name' => $review->buyer?->name ?: 'Buyer',
                'date' => $review->created_at?->format('M d, Y'),
                'reply' => $review->reply,
            ])->values()->all(),
            'sold' => $sold,
            'stock' => $stock,
            'image' => $image,
            'image_url' => $image,
            'gallery' => $gallery->filter()->unique()->values()->all(),
            'description' => $product->description,
            'specs' => $specs,
            'variations' => $variants,
            'variant_stock' => $variantStock,
        ];
    }

    public static function sellerOrder(\App\Models\Seller\SellerOrder $sellerOrder): array
    {
        $order = $sellerOrder->order;
        $shipment = $sellerOrder->shipment;
        $snapshot = $order->shipping_address_snapshot ?? [];
        $payment = $order->payments->sortByDesc('id')->first();
        $status = match ($sellerOrder->status) {
            'pending' => ($payment?->status === 'pending' && ! in_array($order->payment_method, ['cod', 'cash_on_delivery'], true)) ? 'to-pay' : 'to-ship',
            'accepted', 'packed', 'ready_to_ship', 'shipped' => 'to-ship',
            'delivered' => 'to-receive',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            'refunded' => 'returns',
            default => 'to-ship',
        };
        $address = collect([$snapshot['line1'] ?? null, $snapshot['barangay'] ?? null, $snapshot['city'] ?? null, $snapshot['province'] ?? null, $snapshot['postal_code'] ?? null])->filter()->implode(', ');

        return [
            'db_id' => $sellerOrder->id,
            'id' => (string) $sellerOrder->id,
            'parent_reference' => $order->reference,
            'tracking' => $shipment?->tracking_code,
            'placed_at' => $order->created_at?->format('F j, Y, g:i A'),
            'payment' => self::paymentLabel($order->payment_method),
            'status' => $status,
            'status_label' => self::buyerStatusLabel($status),
            'total' => ($sellerOrder->subtotal_minor + $sellerOrder->shipping_fee_minor) / 100,
            'seller' => $sellerOrder->seller?->name ?? 'LIKHAE Seller',
            'shipping_address' => $address,
            'buyer_name' => $snapshot['recipient'] ?? $order->buyer?->name ?? 'Buyer',
            'buyer_contact' => $snapshot['phone'] ?? null,
            'delivery_status' => $shipment?->status,
            'delivery_provider' => $shipment?->provider?->name,
            'delivered_at' => $sellerOrder->delivered_at?->format('M d, Y - g:i A'),
            'products' => $sellerOrder->items->map(function ($item) {
                $image = $item->product?->images?->first()?->path;
                if ($image && ! Str::startsWith($image, ['http://', 'https://'])) $image = Storage::url($image);
                return [
                    'order_item_id' => $item->id, 'product_id' => $item->product_id,
                    'name' => $item->product_name, 'image' => $image ?: asset('images/product-placeholder.svg'),
                    'variant' => $item->variant_name ?: 'Standard', 'quantity' => (int) $item->quantity,
                    'price' => $item->unit_price_minor / 100,
                ];
            })->values()->all(),
            'timeline' => collect([['label' => 'Order Placed', 'time' => $order->created_at]])
                ->merge($sellerOrder->events->map(fn ($event) => ['label' => Str::headline($event->to_status), 'time' => $event->created_at]))
                ->merge(($shipment?->events ?? collect())->map(fn ($event) => ['label' => Str::headline($event->status), 'time' => $event->occurred_at]))
                ->sortBy('time')->map(fn ($event) => ['label' => $event['label'], 'done' => true, 'time' => $event['time']?->format('M d, Y - g:i A') ?? ''])->values()->all(),
        ];
    }

    /** @deprecated Legacy parent-order mapper; no active route calls this method. */
    public static function order(Order $order): array
    {
        $status = self::buyerStatus($order);

        return [
            'db_id' => $order->id,
            'id' => $order->order_number,
            'tracking' => $order->delivery?->tracking_number,
            'placed_at' => $order->created_at?->format('F j, Y, g:i A'),
            'payment' => self::paymentLabel($order->payment_method),
            'status' => $status,
            'status_label' => self::buyerStatusLabel($status),
            'total' => (float) $order->total_amount,
            'seller' => $order->seller?->store_name ?: $order->seller?->business_name ?: $order->seller?->name ?: 'LIKHAE Seller',
            'shipping_address' => $order->shipping_address,
            'buyer_name' => $order->buyer?->name ?: 'Buyer',
            'buyer_contact' => $order->buyer?->contact_number,
            'delivery_status' => $order->delivery?->status,
            'delivery_provider' => $order->delivery?->provider,
            'delivered_at' => $order->delivery?->delivered_at?->format('M d, Y · g:i A'),
            'products' => $order->items->map(function ($item) {
                $image = $item->product?->images?->first()?->path;
                if ($image && ! Str::startsWith($image, ['http://', 'https://'])) {
                    $image = Storage::url($image);
                }

                return [
                    'product_id' => $item->product_id,
                    'name' => $item->product_name ?: $item->product?->name ?: 'Product',
                    'image' => $image ?: asset('images/product-placeholder.svg'),
                    'variant' => $item->variant_name ?: 'Standard',
                    'quantity' => (int) $item->quantity,
                    'price' => (float) $item->unit_price,
                ];
            })->values()->all(),
            'timeline' => self::timeline($order),
        ];
    }

    public static function buyerCounts(?User $buyer): array
    {
        if (! $buyer) {
            return ['cart' => 0, 'messages' => 0, 'notifications' => 0];
        }

        static $cache = [];
        if (isset($cache[$buyer->id])) {
            return $cache[$buyer->id];
        }

        $cartId = Cart::where('user_id', $buyer->id)->value('id');

        return $cache[$buyer->id] = [
            'cart' => $cartId ? (int) CartItem::where('cart_id', $cartId)->sum('quantity') : 0,
            'messages' => Message::where('recipient_id', $buyer->id)->whereNull('read_at')->count(),
            'notifications' => WorkspaceNotification::where('user_id', $buyer->id)->whereNull('read_at')->count(),
        ];
    }

    public static function buyerStatus(Order $order): string
    {
        $deliveryStatus = $order->delivery?->status;

        if ($order->status === 'completed' || $deliveryStatus === 'completed') {
            return 'completed';
        }

        if (in_array($deliveryStatus, ['out_for_delivery', 'delivered'], true)) {
            return 'to-receive';
        }

        if ($deliveryStatus === 'delivery_failed') {
            return 'returns';
        }

        if (in_array($deliveryStatus, ['ready_for_pickup', 'awaiting_pickup_assignment', 'awaiting_dropoff', 'pickup_assigned', 'pickup_accepted', 'picked_up', 'at_sorting_center', 'sorted', 'assigned_to_rider'], true)) {
            return 'to-ship';
        }

        return match ($order->status) {
            'cancelled' => 'cancelled',
            'returns', 'disputed' => 'returns',
            'completed' => 'completed',
            'shipping', 'shipped' => 'to-receive',
            'to_prepare', 'ready_pickup' => 'to-ship',
            default => ($order->payment_method !== 'cash_on_delivery' && $order->payment_method !== 'cod' && $order->payment_status === 'pending') ? 'to-pay' : 'to-ship',
        };
    }

    private static function buyerStatusLabel(string $status): string
    {
        return match ($status) {
            'to-pay' => 'To Pay', 'to-ship' => 'To Ship', 'to-receive' => 'To Receive',
            'completed' => 'Completed', 'cancelled' => 'Cancelled', 'returns' => 'Returns / Refunds',
            default => Str::headline($status),
        };
    }

    private static function paymentLabel(?string $method): string
    {
        return match (strtolower((string) $method)) {
            'gcash' => 'GCash', 'maya' => 'Maya', 'online' => 'Online Payment',
            'credit_card', 'card' => 'Credit Card', 'cod', 'cash_on_delivery' => 'Cash on Delivery',
            default => $method ? Str::headline($method) : 'Cash on Delivery',
        };
    }

    private static function userAddress($user): string
    {
        if (! $user) {
            return 'Philippines';
        }

        return collect([$user->barangay, $user->municipality, $user->province])->filter()->implode(', ') ?: 'Philippines';
    }

    private static function timeline(Order $order): array
    {
        $delivery = $order->delivery;
        if ($delivery && $delivery->relationLoaded('statusHistory') && $delivery->statusHistory->isNotEmpty()) {
            return collect([['new_status' => 'placed', 'created_at' => $order->created_at]])
                ->merge($delivery->statusHistory)
                ->map(fn ($event) => [
                    'label' => Str::headline(data_get($event, 'new_status')),
                    'done' => true,
                    'time' => data_get($event, 'created_at')?->format('M d, Y - g:i A') ?: '',
                ])->values()->all();
        }

        $deliveryStatus = $delivery?->status;

        $sellerProcessing = in_array($order->status, ['to_prepare', 'ready_pickup', 'shipping', 'shipped', 'completed'], true) || $delivery !== null;
        $readyForPickup = in_array($order->status, ['ready_for_pickup', 'ready_pickup', 'shipping', 'shipped', 'completed'], true) || in_array($deliveryStatus, ['ready_for_pickup', 'awaiting_pickup_assignment', 'awaiting_dropoff', 'pickup_assigned', 'pickup_accepted', 'picked_up', 'at_sorting_center', 'sorted', 'assigned_to_rider', 'out_for_delivery', 'delivered'], true);
        $atSortingCenter = in_array($deliveryStatus, ['at_sorting_center', 'sorted', 'assigned_to_rider', 'out_for_delivery', 'delivered'], true);
        $outForDelivery = in_array($deliveryStatus, ['out_for_delivery', 'delivered'], true) || in_array($order->status, ['shipping', 'shipped', 'completed'], true);
        $delivered = $deliveryStatus === 'delivered' || $order->status === 'completed';

        $steps = [
            ['label' => 'Order Placed', 'done' => true, 'time' => $order->created_at],
            ['label' => 'Seller Processing', 'done' => $sellerProcessing, 'time' => $order->updated_at],
            ['label' => 'Ready for Pickup', 'done' => $readyForPickup, 'time' => $delivery?->requested_at ?: $delivery?->created_at],
            ['label' => 'At Sorting Center', 'done' => $atSortingCenter, 'time' => $delivery?->arrived_at_sorting_center_at],
            ['label' => 'Out for Delivery', 'done' => $outForDelivery, 'time' => $delivery?->delivery_picked_up_at ?: $delivery?->assigned_at],
            ['label' => 'Delivered', 'done' => $delivered, 'time' => $delivery?->delivered_at],
            ['label' => 'Completed', 'done' => $order->status === 'completed', 'time' => $order->updated_at],
        ];

        return collect($steps)->map(function ($step) use ($order) {
            $time = $step['time'] ?: ($step['done'] ? $order->updated_at : null);

            return [
                'label' => $step['label'],
                'done' => $step['done'],
                'time' => $step['done'] ? ($time?->format('M d, Y · g:i A') ?: '') : 'Pending',
            ];
        })->all();
    }
}
