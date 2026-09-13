@extends('layouts.buyer')

@section('title', 'Checkout')
@section('active', 'cart')

@section('content')
@php
    $items = collect(session('cart', []));
    $productTotal = $items->sum(fn ($item) => (float) data_get($item, 'price', 0) * max(1, (int) data_get($item, 'quantity', 1)));
    $deliveryFee = $items->isEmpty() ? 0 : 80;
@endphp

<div class="lk-page-narrow">
    <div class="lk-page-title"><div><span class="lk-kicker">Secure Checkout</span><h1>Review and place your order</h1><p>Confirm recipient details, delivery address, and payment method.</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.cart') }}">Back to Cart</a></div>

    @if($items->isEmpty())
        <x-buyer.empty-state title="Your cart is empty" message="Add a product before checking out." />
    @else
        <form method="POST" action="{{ route('buyer.order.store') }}" class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
            @csrf
            <div class="space-y-5">
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Recipient</h2><div class="mt-4 grid gap-4 sm:grid-cols-2"><label class="text-xs font-semibold text-stone-700">Full name<input required name="recipient_name" value="Maria Santos" class="mt-2 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm"></label><label class="text-xs font-semibold text-stone-700">Contact number<input required name="contact_number" inputmode="tel" value="0917 123 4567" class="mt-2 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm"></label></div></section>
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Delivery address</h2><label class="mt-4 block text-xs font-semibold text-stone-700">Address<textarea required name="delivery_address" rows="3" class="mt-2 block w-full rounded-xl border border-stone-300 p-3 text-sm">123 Sampaguita Street, Barangay Central, Quezon City 1100</textarea></label></section>
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Payment method</h2><div class="mt-4 grid gap-2 sm:grid-cols-2"><label class="rounded-xl border border-red-800 bg-red-50 p-3 text-sm font-semibold text-red-900"><input checked type="radio" name="payment_method" value="cash_on_delivery"> Cash on Delivery</label><label class="rounded-xl border border-stone-300 p-3 text-sm font-semibold text-stone-700"><input type="radio" name="payment_method" value="online"> Online Payment</label></div></section>
            </div>
            <aside class="h-fit rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Order summary</h2><div class="mt-4 space-y-3">@foreach($items as $item)<div class="flex gap-3 text-xs"><img class="h-12 w-12 rounded-lg object-cover" src="{{ data_get($item, 'image') }}" alt="{{ data_get($item, 'name') }}"><div class="min-w-0 flex-1"><strong class="line-clamp-2 text-stone-800">{{ data_get($item, 'name') }}</strong><span class="block text-stone-500">Qty {{ data_get($item, 'quantity', 1) }}</span></div><strong class="text-stone-800">&#8369;{{ number_format((float) data_get($item, 'price', 0) * max(1, (int) data_get($item, 'quantity', 1)), 2) }}</strong></div>@endforeach</div><dl class="mt-5 space-y-2 border-t border-stone-100 pt-4 text-xs"><div class="flex justify-between"><dt class="text-stone-500">Product total</dt><dd>&#8369;{{ number_format($productTotal, 2) }}</dd></div><div class="flex justify-between"><dt class="text-stone-500">Delivery fee</dt><dd>&#8369;{{ number_format($deliveryFee, 2) }}</dd></div><div class="flex justify-between border-t border-stone-100 pt-3 text-sm font-bold"><dt>Total</dt><dd class="text-red-900">&#8369;{{ number_format($productTotal + $deliveryFee, 2) }}</dd></div></dl><button class="lk-btn lk-btn-red lk-btn-full mt-5" type="submit">Place Order</button></aside>
        </form>
    @endif
</div>
@endsection
