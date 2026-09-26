@extends('layouts.buyer')
@section('title', 'Checkout')
@section('active', 'cart')

@section('content')
@php
    $cartItems = collect($cartItems ?? []);
    $addresses = collect($addresses ?? []);
    $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
    $groups = collect(data_get($preview ?? [], 'groups', []));
    $imageUrl = static function ($product): string {
        $image = collect(data_get($product, 'images', []))->firstWhere('is_primary', true) ?? collect(data_get($product, 'images', []))->first();
        $path = data_get($image, 'file_path');
        if (! filled($path)) return asset('images/product-placeholder.svg');
        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://']) ? $path : \Illuminate\Support\Facades\Storage::url($path);
    };
@endphp

<div class="lk-page-narrow">
    <div class="lk-page-title"><div><span class="lk-kicker">Secure Checkout</span><h1>Review and place your order</h1><p>Confirm your delivery address and payment method.</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.cart') }}">Back to Cart</a></div>

    @if($cartItems->isEmpty())
        <x-buyer.empty-state title="Your cart is empty" message="Add a product before checking out." />
    @elseif($addresses->isEmpty())
        <section class="mt-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm"><h2 class="text-lg font-bold text-stone-900">A delivery address is required</h2><p class="mt-2 text-sm text-stone-500">Save an address before placing this order.</p><a class="lk-btn lk-btn-red mt-5" href="{{ route('buyer.account.addresses') }}">Add delivery address</a></section>
    @else
        <form method="POST" action="{{ route('buyer.order.store') }}" class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
            @csrf
            <div class="space-y-5">
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-bold text-stone-900">Delivery address</h2>
                    <select required name="address_id" class="mt-4 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm">
                        @foreach($addresses as $address)<option value="{{ $address->id }}" @selected($address->id === $defaultAddress?->id)>{{ $address->label }} — {{ $address->recipient_name }} — {{ $address->formatted() }}</option>@endforeach
                    </select>
                </section>

                @foreach($groups as $sellerId => $group)
                    <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                        <h2 class="text-base font-bold text-stone-900">{{ data_get($group, 'seller.business_name', 'LIKHAE Seller') }}</h2>
                        <div class="mt-4 space-y-3">
                            @foreach(collect(data_get($group, 'items', [])) as $item)
                                @php($variant = $item->productVariant)
                                @php($product = $variant->product)
                                <div class="flex gap-3 text-sm"><img class="h-14 w-14 rounded-lg object-cover" src="{{ $imageUrl($product) }}" alt="{{ $product->name }}"><div class="min-w-0 flex-1"><strong class="line-clamp-2 text-stone-800">{{ $product->name }}</strong><span class="block text-xs text-stone-500">{{ $variant->description ?: 'Standard' }} · Qty {{ $item->quantity }}</span></div><strong>&#8369;{{ number_format((float) $variant->price * $item->quantity, 2) }}</strong></div>
                            @endforeach
                        </div>
                        <label class="mt-4 block text-xs font-semibold text-stone-700">Voucher code<input name="voucher_codes[{{ $sellerId }}]" value="{{ $voucherCodes[$sellerId] ?? '' }}" maxlength="80" class="mt-2 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm" placeholder="Optional"></label>
                    </section>
                @endforeach

                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Payment method</h2><label class="mt-4 block rounded-xl border border-red-800 bg-red-50 p-3 text-sm font-semibold text-red-900"><input checked type="radio" name="payment_method" value="COD"> Cash on Delivery</label><label class="mt-2 block rounded-xl border border-stone-300 p-3 text-sm font-semibold text-stone-700"><input type="radio" name="payment_method" value="ONLINE"> Online Payment</label></section>
            </div>

            <aside class="h-fit rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Order summary</h2><dl class="mt-4 space-y-3 text-sm"><div class="flex justify-between"><dt class="text-stone-500">Subtotal</dt><dd>&#8369;{{ number_format((float) data_get($preview, 'subtotal', 0), 2) }}</dd></div><div class="flex justify-between"><dt class="text-stone-500">Discount</dt><dd>-&#8369;{{ number_format((float) data_get($preview, 'discount_total', 0), 2) }}</dd></div><div class="flex justify-between"><dt class="text-stone-500">Shipping</dt><dd>&#8369;{{ number_format((float) data_get($preview, 'shipping_total', 0), 2) }}</dd></div><div class="flex justify-between border-t border-stone-200 pt-3 text-base font-bold"><dt>Total</dt><dd>&#8369;{{ number_format((float) data_get($preview, 'grand_total', 0), 2) }}</dd></div></dl><button class="lk-btn lk-btn-red lk-btn-full mt-5" type="submit">Place Order</button></aside>
        </form>
    @endif
</div>
@endsection
