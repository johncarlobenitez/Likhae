<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\ServiceArea;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function select(Request $request): RedirectResponse
    {
        $rows = collect(json_decode((string) $request->validate(['items' => ['required', 'json']])['items'], true));
        $ids = $rows->pluck('id')->filter()->map(fn ($id) => (int) $id)->unique();
        abort_if($ids->isEmpty(), 422, 'Select at least one cart item to checkout.');
        $cart = Cart::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($cart->items()->whereIn('id', $ids)->count() === $ids->count(), 403);
        $cart->items()->update(['selected' => false]);
        foreach ($rows as $row) {
            $item = $cart->items()->with('variation')->findOrFail((int) $row['id']);
            $quantity = max(1, (int) ($row['quantity'] ?? 1));
            abort_unless($item->variation?->is_active && $item->variation->product?->is_active, 422, 'A selected item is no longer available.');
            abort_if($quantity > $item->variation->stock, 422, 'Requested quantity exceeds available stock.');
            $item->update(['quantity' => $quantity, 'selected' => true]);
        }
        return redirect()->route('buyer.checkout');
    }

    public function show(Request $request): View|RedirectResponse
    {
        $address = $request->user()->addresses()->whereKey($request->integer('address_id'))->first()
            ?? $request->user()->addresses()->orderByDesc('is_default')->first();
        if (! $address) return redirect()->route('buyer.account.addresses')->withErrors(['address' => 'Add a delivery address before checkout.']);

        $cart = Cart::query()->where('user_id', $request->user()->id)->first();
        $rows = ($cart?->items()->where('selected', true)->with(['variation.product.seller', 'variation.product.images'])->get() ?? collect())
            ->filter(fn ($item) => $item->variation?->is_active && $item->variation->product?->is_active && $item->variation->product?->seller?->status === 'approved');
        if ($rows->isEmpty()) return redirect()->route('buyer.cart')->withErrors(['cart' => 'Select at least one available item before checkout.']);
        $groups = $rows->groupBy(fn ($item) => $item->variation->product->seller_id);
        $couriers = $groups->map(function ($items) use ($address) {
            $weight = $items->sum(fn ($item) => ($item->variation->weight_grams ?? 500) * $item->quantity);
            return ServiceArea::with('provider')->where('is_active', true)
                ->where(fn ($q) => $address->city_code ? $q->where('city_code', $address->city_code) : $q->where('city', $address->city))
                ->whereHas('provider', fn ($q) => $q->where('status', 'approved'))->get()
                ->map(fn ($area) => ['id' => $area->logistics_provider_id, 'name' => $area->provider->name, 'fee_minor' => $area->feeFor($weight)]);
        });
        $token = Str::random(48);
        $request->session()->put('checkout_token', $token);
        $items = $rows->map(fn ($item) => [
            'cart_item_id' => $item->id, 'name' => $item->variation->product->name,
            'seller_id' => $item->variation->product->seller_id, 'seller' => $item->variation->product->seller->name,
            'variant' => $item->variation->name, 'quantity' => $item->quantity,
            'price_minor' => $item->variation->price_minor, 'price' => $item->variation->price_minor / 100,
            'image' => optional($item->variation->product->images->first())->path,
        ]);
        return view('Buyer.checkout', ['items' => $items, 'defaultAddress' => $address, 'addresses' => $request->user()->addresses, 'couriers' => $couriers, 'checkoutToken' => $token]);
    }

    public function store(Request $request, CheckoutService $checkout): RedirectResponse
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer'], 'payment_method' => ['required', Rule::in(['cod', 'online'])],
            'courier' => ['required', 'array'], 'courier.*' => ['required', 'integer'],
            'notes' => ['nullable', 'array'], 'notes.*' => ['nullable', 'string', 'max:500'],
            'checkout_token' => ['required', 'string'],
        ]);
        abort_unless(hash_equals((string) $request->session()->get('checkout_token'), $data['checkout_token']), 409, 'This checkout was already submitted or expired.');
        $address = Address::query()->where('user_id', $request->user()->id)->findOrFail($data['address_id']);
        $order = $checkout->place($request->user(), $address, $data['payment_method'], $data['courier'], $data['notes'] ?? []);
        $request->session()->forget('checkout_token');
        return redirect()->route('buyer.orders.show', $order->reference)->with('buyer_notice', 'Order placed successfully.');
    }
}
