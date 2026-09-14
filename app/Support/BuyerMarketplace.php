<?php

namespace App\Support;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use App\Models\WorkspaceNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BuyerMarketplace
{
    public static function product(Product $product): array
    {
        static $profiles = [];
        $profile = $profiles[$product->seller_id] ??= SellerProfile::where('seller_id', $product->seller_id)->first();
        $sellerName = $profile?->shop_name
            ?: $product->seller?->store_name
            ?: $product->seller?->business_name
            ?: $product->seller?->name
            ?: 'LIKHAE Seller';
        $variants = $product->relationLoaded('variations')
            ? $product->variations
                ->groupBy('name')
                ->map(fn ($rows) => $rows
                    ->filter(fn ($row) => trim((string) $row->value) !== '')
                    ->map(fn ($row) => [
                        'id' => $row->id,
                        'value' => $row->value,
                        'sku' => $row->sku,
                        'price' => $row->price !== null ? (float) $row->price : (float) $product->price,
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
        $image = $product->image_path
            ? (Str::startsWith($product->image_path, ['http://', 'https://'])
                ? $product->image_path
                : Storage::url($product->image_path))
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
        $sellerAvatar = $profile?->avatar_path
            ? (Str::startsWith($profile->avatar_path, ['http://', 'https://'])
                ? $profile->avatar_path
                : Storage::url($profile->avatar_path))
            : 'https://ui-avatars.com/api/?name='.urlencode($sellerName).'&background=561C17&color=fff';
        $reviews = $product->relationLoaded('reviews') ? $product->reviews : collect();
        $sold = $product->relationLoaded('orderItems')
            ? (int) $product->orderItems->filter(fn ($item) => $item->order && ! in_array($item->order->status, ['cancelled', 'returns', 'disputed'], true))->sum('quantity')
            : 0;

        return [
            'db_id' => $product->id,
            'id' => $product->id,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'name' => $product->name,
            'category' => $product->category?->name ?: 'Uncategorized',
            'category_slug' => $product->category?->slug ?: Str::slug($product->category?->name ?: 'uncategorized'),
            'parent_category' => $product->category?->parent?->name,
            'parent_category_slug' => $product->category?->parent?->slug,
            'seller' => $sellerName,
            'seller_id' => $product->seller_id,
            'seller_slug' => Str::slug($sellerName).'-'.$product->seller_id,
            'seller_avatar' => $sellerAvatar,
            'location' => $profile?->location ?: self::userAddress($product->seller),
            'price' => (float) $product->price,
            'old_price' => (float) $product->price,
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
            'stock' => (int) $product->stock,
            'image' => $image,
            'image_url' => $image,
            'gallery' => $gallery->filter()->unique()->values()->all(),
            'description' => $product->description,
            'specs' => $specs,
            'variations' => $variants,
            'variant_stock' => $variantStock,
        ];
    }

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
                $image = $item->product?->image_path;
                if ($image && ! Str::startsWith($image, ['http://', 'https://'])) {
                    $image = Storage::url($image);
                }
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->product_name ?: $item->product?->name ?: 'Product',
                    'image' => $image ?: asset('images/product-placeholder.svg'),
                    'variant' => $item->variant ?: 'Standard',
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

        $cartId = Cart::where('buyer_id', $buyer->id)->value('id');

        return $cache[$buyer->id] = [
            'cart' => $cartId ? (int) CartItem::where('cart_id', $cartId)->sum('quantity') : 0,
            'messages' => Message::where('recipient_id', $buyer->id)->whereNull('read_at')->count(),
            'notifications' => WorkspaceNotification::where('user_id', $buyer->id)->whereNull('read_at')->count(),
        ];
    }

    public static function buyerStatus(Order $order): string
    {
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
        if (! $user) return 'Philippines';
        return collect([$user->barangay, $user->municipality, $user->province])->filter()->implode(', ') ?: 'Philippines';
    }

    private static function timeline(Order $order): array
    {
        $steps = [
            ['label' => 'Order Placed', 'done' => true],
            ['label' => 'Seller Processing', 'done' => in_array($order->status, ['to_prepare','ready_pickup','shipping','shipped','completed'], true)],
            ['label' => 'Ready for Pickup', 'done' => in_array($order->status, ['ready_pickup','shipping','shipped','completed'], true)],
            ['label' => 'Out for Delivery', 'done' => in_array($order->status, ['shipping','shipped','completed'], true)],
            ['label' => 'Completed', 'done' => $order->status === 'completed'],
        ];
        return collect($steps)->map(fn ($step) => $step + ['time' => $step['done'] ? ($order->updated_at?->format('M d, Y · g:i A') ?: '') : 'Pending'])->all();
    }
}
