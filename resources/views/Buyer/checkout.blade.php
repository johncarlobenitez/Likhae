@extends('layouts.buyer')

@section('title', 'Checkout')
@section('active', 'cart')

@section('content')
@php
    $items = collect($items ?? []);
    $user = auth()->user();
    $recipientName = data_get($defaultAddress, 'recipient', data_get($user, 'name', ''));
    $contactNumber = data_get($defaultAddress, 'phone', data_get($user, 'contact_number', ''));
    $deliveryAddress = collect([data_get($defaultAddress,'line1'),data_get($defaultAddress,'barangay'),data_get($defaultAddress,'city'),data_get($defaultAddress,'province'),data_get($defaultAddress,'postal_code')])->filter()->implode(', ');
    $productTotal = $items->sum(fn ($item) => (float) data_get($item, 'price', 0) * max(1, (int) data_get($item, 'quantity', 1)));
    $deliveryFee = 0;
    $shopGroups = $items->groupBy('seller_id');
    $hasMissingCourier = $shopGroups->contains(fn ($shopItems, $sellerId) => collect(data_get($couriers, $sellerId, []))->isEmpty());

    $displayImage = function ($image) {
        if (! filled($image)) {
            return asset('images/product-placeholder.svg');
        }

        if (Str::startsWith((string) $image, ['http://', 'https://'])) {
            return (string) $image;
        }

        return Storage::url((string) $image);
    };
@endphp

<div class="lk-page-narrow">
    <div class="lk-page-title"><div><span class="lk-kicker">Secure Checkout</span><h1>Review and place your order</h1><p>Confirm recipient details, delivery address, and payment method.</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.cart') }}">Back to Cart</a></div>

    @if($items->isEmpty())
        <x-buyer.empty-state title="Your cart is empty" message="Add a product before checking out." />
    @else
        <form method="POST" action="{{ route('buyer.order.store', [], false) }}" class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
            @csrf
            <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">
            <div class="space-y-5">
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Recipient</h2><div class="mt-4 grid gap-4 sm:grid-cols-2"><label class="text-xs font-semibold text-stone-700">Full name<input required name="recipient_name" value="{{ $recipientName }}" class="mt-2 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm"></label><label class="text-xs font-semibold text-stone-700">Contact number<input required name="contact_number" inputmode="tel" value="{{ $contactNumber }}" class="mt-2 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm"></label></div></section>
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Delivery address</h2><select required name="address_id" class="mt-4 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm">@foreach($addresses as $address)<option value="{{ $address->id }}" @selected($address->id === $defaultAddress->id)>{{ $address->label }} - {{ collect([$address->line1,$address->barangay,$address->city,$address->province])->filter()->implode(', ') }}</option>@endforeach</select><p class="mt-3 text-xs text-stone-500">{{ $deliveryAddress }}</p></section>
                @foreach($shopGroups as $sellerId => $shopItems)<section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">{{ data_get($shopItems->first(),'seller') }}</h2><label class="mt-4 block text-xs font-semibold text-stone-700">Courier<select required name="courier[{{ $sellerId }}]" class="mt-2 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm"><option value="">Choose courier</option>@foreach(data_get($couriers,$sellerId,[]) as $option)<option value="{{ $option['id'] }}">{{ $option['name'] }} - &#8369;{{ number_format($option['fee_minor']/100,2) }}</option>@endforeach</select></label><label class="mt-4 block text-xs font-semibold text-stone-700">Message to shop<textarea name="notes[{{ $sellerId }}]" maxlength="500" rows="2" class="mt-2 block w-full rounded-xl border border-stone-300 p-3 text-sm"></textarea></label>@if(collect(data_get($couriers,$sellerId,[]))->isEmpty())<p class="mt-3 text-xs font-semibold text-red-700">No approved courier currently serves this address.</p>@endif</section>@endforeach
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Payment method</h2><div class="mt-4 grid gap-2 sm:grid-cols-2"><label class="rounded-xl border border-red-800 bg-red-50 p-3 text-sm font-semibold text-red-900"><input checked type="radio" name="payment_method" value="cod"> Cash on Delivery</label><label class="rounded-xl border border-stone-300 p-3 text-sm font-semibold text-stone-700"><input type="radio" disabled> Online Payment (unavailable)</label></div></section>
            </div>
            <aside class="h-fit rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Order summary</h2><div class="mt-4 space-y-3">@foreach($items as $item)<div class="flex gap-3 text-xs"><img class="h-12 w-12 rounded-lg object-cover" src="{{ $displayImage(data_get($item,'image')) }}" alt="{{ data_get($item, 'name') }}"> <div class="min-w-0 flex-1"><strong class="line-clamp-2 text-stone-800">{{ data_get($item, 'name') }}</strong><span class="block text-stone-500">{{ data_get($item,'variant') }} · Qty {{ data_get($item, 'quantity', 1) }}</span></div><strong class="text-stone-800">&#8369;{{ number_format((float) data_get($item, 'price', 0) * max(1, (int) data_get($item, 'quantity', 1)), 2) }}</strong></div>@endforeach</div><dl class="mt-5 space-y-2 border-t border-stone-100 pt-4 text-xs"><div class="flex justify-between"><dt class="text-stone-500">Product total</dt><dd>&#8369;{{ number_format($productTotal, 2) }}</dd></div><p class="text-stone-500">Courier fees are recalculated securely when you place the order.</p></dl><button class="lk-btn lk-btn-red lk-btn-full mt-5" type="submit" @disabled($hasMissingCourier)>Place Order</button></aside>
        </form>
    @endif
</div>
@endsection
