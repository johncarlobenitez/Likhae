<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\ServiceArea;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function select(Request $request): RedirectResponse
    {
        $decoded = json_decode((string) $request->validate(['items' => ['required', 'json']])['items'], true);
        $validated = Validator::make(['items' => $decoded], [
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'distinct', 'min:1'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ])->validate();
        $rows = collect($validated['items']);
        $ids = $rows->pluck('id')->filter()->map(fn ($id) => (int) $id)->unique();
        abort_if($ids->isEmpty(), 422, 'Select at least one cart item to checkout.');
        $cart = Cart::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($cart->items()->whereIn('id', $ids)->count() === $ids->count(), 403);
        DB::transaction(function () use ($cart, $rows): void {
            $cart->items()->update(['selected' => false]);
            foreach ($rows as $row) {
            $item = $cart->items()->with('variation')->findOrFail((int) $row['id']);
            $quantity = max(1, (int) ($row['quantity'] ?? 1));
            abort_unless($item->variation?->is_active && $item->variation->product?->is_active, 422, 'A selected item is no longer available.');
            abort_if($quantity > $item->variation->stock, 422, 'Requested quantity exceeds available stock.');
            $item->update(['quantity' => $quantity, 'selected' => true]);
            }
        });
        $request->session()->forget(['buyer_buy_now', 'checkout_token']);
        return redirect()->route('buyer.checkout');
    }

    public function show(Request $request): View|RedirectResponse
    {
        $address = $request->user()->addresses()->whereKey($request->integer('address_id'))->first()
            ?? $request->user()->addresses()->orderByDesc('is_default')->first();
        if (! $address) return redirect()->route('buyer.account.addresses')->withErrors(['address' => 'Add a delivery address before checkout.']);

        $buyNow = $request->session()->get('buyer_buy_now');
        if ($request->filled('buy')) {
            $product = \App\Models\Product::query()->where('is_active', true)->where('slug', (string) $request->input('buy'))->first();
            $variantId = $request->input('product_variant_id');
            $variant = $variantId ? \App\Models\ProductVariant::query()->with(['product.seller', 'product.images'])->where('product_id', $product?->id)->whereKey((int) $variantId)->first() : null;
            $quantity = max(1, (int) $request->input('quantity', 1));

            if (! $product || ! $variant) {
                return redirect()->route('buyer.cart')->withErrors(['cart' => 'The selected item is no longer available.']);
            }

            abort_unless($variant->is_active && $variant->product_id === $product->id, 422, 'Selected product option is invalid.');
            abort_if($quantity > (int) $variant->stock, 422, 'Requested quantity exceeds available stock for this option.');

            $request->session()->put('buyer_buy_now', ['product_variant_id' => $variant->id, 'quantity' => $quantity]);
            $buyNow = $request->session()->get('buyer_buy_now');
        }

        $rows = collect();

        if (is_array($buyNow) && ! empty($buyNow['product_variant_id'])) {
            $variant = \App\Models\ProductVariant::query()->with(['product.seller', 'product.images'])->whereKey((int) $buyNow['product_variant_id'])->first();
            $quantity = max(1, (int) ($buyNow['quantity'] ?? 1));

            if (! $variant || ! $variant->is_active || ! $variant->product || ! $variant->product->is_active || $variant->product->seller?->status !== 'approved') {
                $request->session()->forget('buyer_buy_now');
                return redirect()->route('buyer.cart')->withErrors(['cart' => 'The selected item is no longer available.']);
            }

            if ($quantity > (int) $variant->stock) {
                $request->session()->forget('buyer_buy_now');
                return redirect()->route('buyer.cart')->withErrors(['cart' => 'Requested quantity exceeds available stock for this option.']);
            }

            $rows = collect([
                [
                    'cart_item_id' => null,
                    'id' => $variant->id,
                    'name' => $variant->product->name,
                    'seller_id' => $variant->product->seller_id,
                    'seller' => $variant->product->seller->name,
                    'variant' => $variant->name,
                    'quantity' => $quantity,
                    'price_minor' => $variant->price_minor,
                    'price' => $variant->price_minor / 100,
                    'image' => optional($variant->product->images->first())->path,
                    'weight_grams' => $variant->weight_grams ?? 500,
                ],
            ]);
        } else {
            $cart = Cart::query()->where('user_id', $request->user()->id)->first();
            $rows = ($cart?->items()->where('selected', true)->with(['variation.product.seller', 'variation.product.images'])->get() ?? collect())
                ->filter(fn ($item) => $item->variation?->is_active && $item->variation->product?->is_active && $item->variation->product?->seller?->status === 'approved');
            if ($rows->isEmpty()) return redirect()->route('buyer.cart')->withErrors(['cart' => 'Select at least one available item before checkout.']);
        }

        $rows = $rows->map(fn ($item) => $this->normalizeCheckoutRow($item));
        $groups = $rows->groupBy(fn ($item) => (int) ($item['seller_id'] ?? 0));
        $couriers = $groups->map(function ($items) use ($address) {
            $weight = collect($items)->sum(fn ($item) => ((int) ($item['weight_grams'] ?? 500)) * (int) ($item['quantity'] ?? 1));
            return ServiceArea::with('provider')->where('is_active', true)
                ->forAddress($address)
                ->whereHas('provider', fn ($q) => $q->where('status', 'approved'))->get()
                ->map(fn ($area) => ['id' => $area->logistics_provider_id, 'name' => $area->provider->name, 'fee_minor' => $area->feeFor($weight)]);
        });
        $token = Str::random(48);
        $request->session()->put('checkout_token', $token);
        return view('Buyer.checkout', ['items' => $rows, 'defaultAddress' => $address, 'addresses' => $request->user()->addresses, 'couriers' => $couriers, 'checkoutToken' => $token]);
    }

    public function store(Request $request, CheckoutService $checkout): RedirectResponse
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer'], 'payment_method' => ['required', Rule::in(['cod'])],
            'courier' => ['required', 'array'], 'courier.*' => ['required', 'integer'],
            'notes' => ['nullable', 'array'], 'notes.*' => ['nullable', 'string', 'max:500'],
            'checkout_token' => ['required', 'string'],
        ]);
        abort_unless(hash_equals((string) $request->session()->get('checkout_token'), $data['checkout_token']), 409, 'This checkout was already submitted or expired.');
        $address = Address::query()->where('user_id', $request->user()->id)->findOrFail($data['address_id']);

        $selection = null;
        $buyNow = $request->session()->get('buyer_buy_now');
        if (is_array($buyNow) && ! empty($buyNow['product_variant_id'])) {
            $selection = [[
                'product_variant_id' => (int) $buyNow['product_variant_id'],
                'quantity' => max(1, (int) ($buyNow['quantity'] ?? 1)),
            ]];
        }

        $order = $checkout->place($request->user(), $address, $data['payment_method'], $data['courier'], $data['notes'] ?? [], $selection);
        $request->session()->forget(['checkout_token', 'buyer_buy_now']);
        return redirect()->route('buyer.orders.success')->with('buyer_notice', 'Order placed successfully.');
    }

    private function normalizeCheckoutRow(mixed $item): array
    {
        if (is_array($item)) {
            return [
                'cart_item_id' => data_get($item, 'cart_item_id') ?? data_get($item, 'id'),
                'id' => data_get($item, 'id') ?? data_get($item, 'cart_item_id'),
                'name' => data_get($item, 'name'),
                'seller_id' => (int) (data_get($item, 'seller_id') ?? 0),
                'seller' => data_get($item, 'seller'),
                'variant' => data_get($item, 'variant'),
                'quantity' => (int) (data_get($item, 'quantity') ?? 1),
                'price_minor' => (int) (data_get($item, 'price_minor') ?? 0),
                'price' => (float) (data_get($item, 'price') ?? 0),
                'image' => data_get($item, 'image'),
                'weight_grams' => (int) (data_get($item, 'weight_grams') ?? 500),
            ];
        }

        $variant = $item->variation;
        $product = $variant?->product;

        return [
            'cart_item_id' => $item->id,
            'id' => $item->id,
            'name' => $product?->name,
            'seller_id' => (int) ($product?->seller_id ?? 0),
            'seller' => $product?->seller?->name,
            'variant' => $variant?->name,
            'quantity' => (int) ($item->quantity ?? 1),
            'price_minor' => (int) ($variant?->price_minor ?? 0),
            'price' => (float) (($variant?->price_minor ?? 0) / 100),
            'image' => optional($product?->images?->first())->path,
            'weight_grams' => (int) ($variant?->weight_grams ?? 500),
        ];
    }
}
