<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\ServiceArea;
use App\Models\User;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function place(User $buyer, Address $address, string $method, array $couriers, array $notes = [], ?array $selection = null): Order
    {
        abort_unless($address->user_id === $buyer->id, 403);

        return DB::transaction(function () use ($buyer, $address, $method, $couriers, $notes, $selection) {
            $cart = $selection === null ? Cart::query()->where('user_id', $buyer->id)->firstOrFail() : null;
            $cartItems = $selection === null
                ? $cart->items()->where('selected', true)->lockForUpdate()->get()
                : collect($selection)->map(fn ($row) => (object) [
                    'product_variant_id' => (int) ($row['product_variant_id'] ?? $row['id'] ?? 0),
                    'quantity' => max(1, (int) ($row['quantity'] ?? 1)),
                ]);

            if ($cartItems->isEmpty()) throw ValidationException::withMessages(['cart' => 'Select at least one available item.']);

            $variants = ProductVariant::query()->with(['product.seller', 'product.category.parent'])
                ->whereIn('id', $cartItems->pluck('product_variant_id'))->lockForUpdate()->get()->keyBy('id');

            $groups = $cartItems->groupBy(function ($item) use ($variants) {
                $variant = $variants->get($item->product_variant_id);
                $this->assertPurchasable($variant, $item->quantity);
                return $variant->product->seller_id;
            });

            $sellerData = [];
            $grandTotal = 0;
            foreach ($groups as $sellerId => $items) {
                $providerId = (int) ($couriers[$sellerId] ?? 0);
                $weight = $items->sum(fn ($item) => ($variants[$item->product_variant_id]->weight_grams ?? 500) * $item->quantity);
                $area = ServiceArea::query()->where('logistics_provider_id', $providerId)->where('is_active', true)
                    ->where(fn ($q) => $address->city_code ? $q->where('city_code', $address->city_code) : $q->where('city', $address->city))
                    ->whereHas('provider', fn ($q) => $q->where('status', 'approved'))->first();
                if (! $area) throw ValidationException::withMessages(["courier.$sellerId" => 'The selected courier does not serve this address.']);
                $subtotal = $items->sum(fn ($item) => $variants[$item->product_variant_id]->price_minor * $item->quantity);
                $fee = $area->feeFor((int) $weight);
                $seller = $variants[$items->first()->product_variant_id]->product->seller;
                $commission = intdiv($subtotal * $seller->commission_bps, 10000);
                $sellerData[$sellerId] = compact('items', 'providerId', 'subtotal', 'fee', 'commission');
                $grandTotal += $subtotal + $fee;
            }

            if (in_array($method, ['cod','cash_on_delivery'], true) && $grandTotal > (int) PlatformSetting::valueOf('cod_limit_minor', 500000)) {
                throw ValidationException::withMessages(['payment_method' => 'This order exceeds the Cash on Delivery limit.']);
            }

            $reference = 'LKH-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));
            $order = Order::create([
                'reference' => $reference, 'buyer_id' => $buyer->id,
                'total_minor' => $grandTotal, 'payment_method' => $method,
                'shipping_address_snapshot' => $address->only(['recipient','phone','line1','barangay','barangay_code','city','city_code','province','province_code','postal_code']),
            ]);

            foreach ($sellerData as $sellerId => $data) {
                $sellerOrder = $order->sellerOrders()->create([
                    'seller_id' => $sellerId, 'logistics_provider_id' => $data['providerId'],
                    'subtotal_minor' => $data['subtotal'], 'shipping_fee_minor' => $data['fee'],
                    'commission_minor' => $data['commission'], 'status' => 'pending', 'note' => $notes[$sellerId] ?? null,
                ]);
                $sellerOrder->events()->create(['from_status' => null, 'to_status' => 'pending', 'user_id' => $buyer->id, 'note' => 'Order placed.']);
                foreach ($data['items'] as $item) {
                    $variant = $variants[$item->product_variant_id];
                    $sellerOrder->items()->create([
                        'product_id' => $variant->product_id, 'product_variant_id' => $variant->id,
                        'product_name' => $variant->product->name, 'variant_name' => $variant->name,
                        'sku' => $variant->sku, 'quantity' => $item->quantity,
                        'unit_price_minor' => $variant->price_minor, 'subtotal_minor' => $variant->price_minor * $item->quantity,
                    ]);
                    $variant->decrement('stock', $item->quantity);
                }
            }
            Payment::create(['order_id' => $order->id, 'method' => $method, 'amount_minor' => $grandTotal, 'status' => 'pending']);
            if ($selection === null) {
                $cart->items()->whereKey($cartItems->modelKeys())->delete();
            }
            return $order->load('sellerOrders.items');
        }, 3);
    }

    private function assertPurchasable(?ProductVariant $variant, int $quantity): void
    {
        if (! $variant || ! $variant->is_active || $variant->stock < $quantity || ! $variant->product || ! $variant->product->is_active
            || $variant->product->seller?->status !== 'approved' || ! $variant->product->category?->is_active
            || ($variant->product->category->parent && ! $variant->product->category->parent->is_active)) {
            throw ValidationException::withMessages(['cart' => 'An item is unavailable or no longer has enough stock.']);
        }
    }
}
