<?php

namespace App\Services\Marketplace;

use App\Models\Buyer\Cart;
use App\Models\Buyer\CartItem;
use App\Models\Seller\Product;
use App\Models\Seller\ProductVariant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function activeCart(User $buyer): Cart
    {
        return Cart::query()->firstOrCreate(
            [
                'buyer_user_id' => $buyer->id,
                'status' => Cart::STATUS_ACTIVE,
            ],
            [
                'converted_at' => null,
            ]
        );
    }

    public function items(User $buyer): Collection
    {
        return $this->activeCart($buyer)
            ->items()
            ->with(['productVariant.product.images', 'productVariant.product.sellerProfile', 'productVariant.optionValues.option'])
            ->latest()
            ->get();
    }

    public function add(User $buyer, Product $product, int $variantId, int $quantity): CartItem
    {
        $variant = ProductVariant::query()
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->whereKey($variantId)
            ->firstOrFail();

        if ($variant->stock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Requested quantity exceeds available stock.',
            ]);
        }

        $cart = $this->activeCart($buyer);

        $item = $cart->items()->firstOrNew([
            'product_variant_id' => $variant->id,
        ]);

        $newQuantity = (int) $item->quantity + $quantity;

        if ($newQuantity > $variant->stock) {
            throw ValidationException::withMessages([
                'quantity' => 'Your cart quantity exceeds available stock.',
            ]);
        }

        $item->quantity = $newQuantity;
        $item->save();

        return $item;
    }

    public function update(User $buyer, CartItem $item, int $quantity): CartItem
    {
        $this->ensureOwner($buyer, $item);
        $item->loadMissing('productVariant');

        if ($quantity > (int) $item->productVariant->stock) {
            throw ValidationException::withMessages([
                'quantity' => 'Requested quantity exceeds available stock.',
            ]);
        }

        $item->update(['quantity' => $quantity]);

        return $item;
    }

    public function remove(User $buyer, CartItem $item): void
    {
        $this->ensureOwner($buyer, $item);
        $item->delete();
    }

    public function ensureOwner(User $buyer, CartItem $item): void
    {
        $item->loadMissing('cart');
        abort_unless((int) $item->cart->buyer_user_id === (int) $buyer->id, 403);
    }

    public function selectedItems(User $buyer, array $ids = []): Collection
    {
        $query = $this->activeCart($buyer)
            ->items()
            ->with(['productVariant.product.images', 'productVariant.product.sellerProfile', 'productVariant.optionValues.option']);

        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        return $query->get();
    }
}
