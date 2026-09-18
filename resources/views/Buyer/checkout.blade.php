@extends('layouts.buyer')

@section('title', 'Checkout')
@section('active', 'cart')

@section('content')
@php
    $items = collect($items ?? []);
    $source = $source ?? 'cart';
    $checkoutItems = $checkoutItems ?? json_encode($items->map(fn ($item) => [
        'id' => data_get($item, 'cart_item_id', data_get($item, 'id')),
        'quantity' => max(1, (int) data_get($item, 'quantity', 1)),
    ])->values());
    $user = auth()->user();
    $recipientName = old('recipient_name', data_get($defaultAddress, 'recipient_name', data_get($user, 'name', '')));
    $contactNumber = old('contact_number', data_get($defaultAddress, 'contact_number', data_get($user, 'contact_number', '')));
    $deliveryAddress = old('delivery_address', $defaultAddress ? $defaultAddress->formatted() : collect([data_get($user,'house_number'),data_get($user,'street'),data_get($user,'barangay'),data_get($user,'municipality'),data_get($user,'province'),data_get($user,'postal_code')])->filter()->implode(', '));
    $productTotal = $items->sum(fn ($item) => (float) data_get($item, 'price', 0) * max(1, (int) data_get($item, 'quantity', 1)));
    $deliveryFee = 0;
    $voucherCode = $voucherCode ?? old('voucher_code', '');
    $voucherDiscount = (float) data_get($voucher, 'discount', 0);
@endphp

<div class="lk-page-narrow">
    <div class="lk-page-title"><div><span class="lk-kicker">Secure Checkout</span><h1>Review and place your order</h1><p>Confirm recipient details, delivery address, and payment method.</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.cart') }}">Back to Cart</a></div>

    @if($items->isEmpty())
        <x-buyer.empty-state title="Your cart is empty" message="Add a product before checking out." />
    @else
        <section class="mt-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-stone-900">Seller voucher</h2>
            <p class="mt-1 text-xs text-stone-500">Enter a voucher code created by the seller. It applies only to that seller's products.</p>
            <form method="POST" action="{{ route('buyer.checkout.post', [], false) }}" class="mt-4 flex flex-col gap-2 sm:flex-row">
                @csrf
                <input type="hidden" name="checkout_source" value="{{ $source }}">
                <input type="hidden" name="items" value="{{ $checkoutItems }}">
                <label class="sr-only" for="voucher_code">Voucher code</label>
                <input id="voucher_code" name="voucher_code" value="{{ $voucherCode }}" maxlength="40" placeholder="Enter voucher code" class="min-w-0 flex-1 rounded-xl border border-stone-300 px-3 py-2.5 text-sm uppercase">
                <button class="lk-btn lk-btn-light" type="submit">Apply Voucher</button>
            </form>
            @if($voucher)
                <p class="mt-3 text-xs font-semibold text-emerald-700">{{ data_get($voucher, 'campaign.name') }} from {{ data_get($voucher, 'campaign.seller.store_name', data_get($voucher, 'campaign.seller.name', 'seller')) }} applied. You save &#8369;{{ number_format($voucherDiscount, 2) }}.</p>
            @elseif($voucherError)
                <p class="mt-3 text-xs font-semibold text-red-700">{{ $voucherError }}</p>
            @endif
        </section>
        <form method="POST" action="{{ route('buyer.order.store', [], false) }}" class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
            @csrf
            <input type="hidden" name="checkout_source" value="{{ $source }}">
            <input type="hidden" name="items" value="{{ $checkoutItems }}">
            <input type="hidden" name="voucher_code" value="{{ $voucher ? $voucherCode : '' }}">
            <div class="space-y-5">
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Recipient</h2><div class="mt-4 grid gap-4 sm:grid-cols-2"><label class="text-xs font-semibold text-stone-700">Full name<input required name="recipient_name" value="{{ $recipientName }}" class="mt-2 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm"></label><label class="text-xs font-semibold text-stone-700">Contact number<input required name="contact_number" inputmode="tel" value="{{ $contactNumber }}" class="mt-2 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm"></label></div></section>
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Delivery address</h2><label class="mt-4 block text-xs font-semibold text-stone-700">Address<textarea required name="delivery_address" rows="3" class="mt-2 block w-full rounded-xl border border-stone-300 p-3 text-sm">{{ $deliveryAddress }}</textarea></label></section>
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Payment method</h2><div class="mt-4 grid gap-2 sm:grid-cols-2"><label class="rounded-xl border border-red-800 bg-red-50 p-3 text-sm font-semibold text-red-900"><input checked type="radio" name="payment_method" value="cash_on_delivery"> Cash on Delivery</label><label class="rounded-xl border border-stone-300 p-3 text-sm font-semibold text-stone-700"><input type="radio" name="payment_method" value="online"> Online Payment</label></div></section>
            </div>
            <aside class="h-fit rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Order summary</h2><div class="mt-4 space-y-3">@foreach($items as $item)<div class="flex gap-3 text-xs"><img class="h-12 w-12 rounded-lg object-cover" src="{{ data_get($item, 'image') }}" alt="{{ data_get($item, 'name') }}"><div class="min-w-0 flex-1"><strong class="line-clamp-2 text-stone-800">{{ data_get($item, 'name') }}</strong><span class="block text-stone-500">Qty {{ data_get($item, 'quantity', 1) }}</span></div><strong class="text-stone-800">&#8369;{{ number_format((float) data_get($item, 'price', 0) * max(1, (int) data_get($item, 'quantity', 1)), 2) }}</strong></div>@endforeach</div><dl class="mt-5 space-y-2 border-t border-stone-100 pt-4 text-xs"><div class="flex justify-between"><dt class="text-stone-500">Product total</dt><dd>&#8369;{{ number_format($productTotal, 2) }}</dd></div>@if($voucherDiscount > 0)<div class="flex justify-between text-emerald-700"><dt>Seller voucher</dt><dd>-&#8369;{{ number_format($voucherDiscount, 2) }}</dd></div>@endif<div class="flex justify-between"><dt class="text-stone-500">Delivery fee</dt><dd>&#8369;{{ number_format($deliveryFee, 2) }}</dd></div><div class="flex justify-between border-t border-stone-100 pt-3 text-sm font-bold"><dt>Total</dt><dd class="text-red-900">&#8369;{{ number_format(max(0, $productTotal + $deliveryFee - $voucherDiscount), 2) }}</dd></div></dl><button class="lk-btn lk-btn-red lk-btn-full mt-5" type="submit">Place Order</button></aside>
        </form>
    @endif
</div>
@endsection
