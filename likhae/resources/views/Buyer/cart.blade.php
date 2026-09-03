@extends('layouts.buyer')
@section('title', 'Cart — LIKHAE')
@section('active', 'cart')

@section('content')
@php
    /* Sample cart items — replace with real $cartItems from controller */
    $cartItems = $cartItems ?? collect([
        [
            'id'       => 1,
            'name'     => 'Handwoven Everyday Tote Bag',
            'variant'  => 'Natural Brown',
            'price'    => 849.00,
            'quantity' => 1,
            'seller'   => 'Habi Local Crafts',
            'image'    => null,
        ],
        [
            'id'       => 2,
            'name'     => 'Minimalist Ceramic Coffee Mug',
            'variant'  => 'Cream · 350 ml',
            'price'    => 329.00,
            'quantity' => 2,
            'seller'   => 'Clay & Co. Studio',
            'image'    => null,
        ],
    ]);

    $cartItems  = collect($cartItems);
    $subtotal   = $cartItems->sum(fn($i) => data_get($i,'price',0) * data_get($i,'quantity',1));
    $shipping   = $subtotal > 0 ? 80 : 0;
    $discount   = 0;
    $total      = $subtotal + $shipping - $discount;
@endphp

<div class="lk-page">

    {{-- Page title --}}
    <div class="lk-page-title">
        <div>
            <span class="lk-kicker">Your Selection</span>
            <h1>Shopping Cart</h1>
            <p>Review your items before checkout.</p>
        </div>
        <a href="{{ route('buyer.products') }}" class="lk-btn lk-btn-light">Continue Shopping</a>
    </div>

    <div class="lk-cart-layout">

        {{-- ── CART ITEMS ────────────────────────────── --}}
        <section class="lk-cart-items">

            @if($cartItems->isEmpty())
                <div class="lk-cart-empty">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 6h15l-2 8H8z"/><path d="M6 6 5 3H2"/>
                        <circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>
                    </svg>
                    <h3>Your cart is empty</h3>
                    <p>Add products from the marketplace to see them here.</p>
                    <a href="{{ route('buyer.products') }}" class="lk-btn lk-btn-red">Browse Products</a>
                </div>
            @else
                {{-- Group by seller --}}
                @foreach($cartItems->groupBy('seller') as $seller => $items)
                    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
                        {{-- Seller header --}}
                        <div class="flex items-center gap-3 border-b border-stone-100 bg-stone-50 px-5 py-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-700">
                                {{ strtoupper(mb_substr($seller,0,1)) }}
                            </div>
                            <strong class="text-sm font-semibold text-stone-900">{{ $seller }}</strong>
                        </div>

                        {{-- Items --}}
                        <div class="divide-y divide-stone-100">
                            @foreach($items as $item)
                            @php
                                $price = data_get($item,'price',0);
                                $qty   = data_get($item,'quantity',1);
                            @endphp
                            <div class="flex gap-4 p-5" data-cart-item>
                                {{-- Image --}}
                                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-stone-200 bg-stone-50">
                                    @if(data_get($item,'image'))
                                        <img src="{{ data_get($item,'image') }}" alt="{{ data_get($item,'name') }}" class="h-full w-full object-cover">
                                    @else
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-stone-300">
                                            <path d="M4 5h16v14H4z"/><path d="m4 15 4-4 4 4 3-3 5 5"/><circle cx="15.5" cy="8.5" r="1.5"/>
                                        </svg>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex min-w-0 flex-1 flex-col gap-2">
                                    <h3 class="text-sm font-semibold text-stone-900 leading-5">{{ data_get($item,'name') }}</h3>
                                    <p class="text-xs text-stone-400">{{ data_get($item,'variant') }}</p>

                                    <div class="mt-auto flex items-center justify-between gap-4">
                                        {{-- Qty stepper --}}
                                        <div class="flex items-center gap-1.5">
                                            <button type="button"
                                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600 text-sm font-bold hover:bg-stone-50 transition">−</button>
                                            <span class="w-8 text-center text-sm font-semibold text-stone-900">{{ $qty }}</span>
                                            <button type="button"
                                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600 text-sm font-bold hover:bg-stone-50 transition">+</button>
                                        </div>

                                        {{-- Price + remove --}}
                                        <div class="flex items-center gap-4">
                                            <strong class="text-sm font-bold text-stone-900">₱{{ number_format($price * $qty, 2) }}</strong>
                                            <button type="button"
                                                    class="text-xs font-medium text-red-500 hover:text-red-700 transition">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </section>

        {{-- ── ORDER SUMMARY ───────────────────────── --}}
        <aside class="lk-cart-summary">
            <div class="lk-summary-card">
                <h3>Order Summary</h3>

                <div class="flex flex-col gap-3">
                    <div class="lk-summary-row">
                        <span>Subtotal ({{ $cartItems->sum('quantity') }} items)</span>
                        <span>₱{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="lk-summary-row">
                        <span>Shipping fee</span>
                        <span>₱{{ number_format($shipping, 2) }}</span>
                    </div>
                    @if($discount > 0)
                    <div class="lk-summary-row" style="color:#16a34a;">
                        <span>Discount</span>
                        <span>−₱{{ number_format($discount, 2) }}</span>
                    </div>
                    @endif
                </div>

                <hr class="lk-summary-divider">

                <div class="lk-summary-row lk-summary-total">
                    <span>Total</span>
                    <span>₱{{ number_format($total, 2) }}</span>
                </div>

                {{-- Voucher --}}
                <div class="flex gap-2 rounded-xl border border-stone-200 bg-stone-50 p-1 pl-3">
                    <input type="text" placeholder="Enter voucher code"
                           class="flex-1 border-0 bg-transparent text-xs outline-none placeholder:text-stone-400">
                    <button type="button"
                            class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-700 transition">Apply</button>
                </div>

                <a href="{{ route('buyer.checkout') }}"
                   class="lk-btn lk-btn-red lk-btn-full mt-1 {{ $cartItems->isEmpty() ? 'pointer-events-none opacity-50' : '' }}">
                    Proceed to Checkout
                </a>

                <a href="{{ route('buyer.products') }}" class="lk-btn lk-btn-light lk-btn-full">
                    Continue Shopping
                </a>
            </div>

            {{-- Assurance --}}
            <div class="mt-3 rounded-xl border border-stone-200 bg-white p-4">
                <p class="mb-2 text-xs font-semibold text-stone-700">Buyer Protection</p>
                @foreach([
                    '✓ Secure checkout',
                    '✓ Easy returns & refunds',
                    '✓ Verified local sellers',
                ] as $point)
                    <p class="text-xs text-stone-500 leading-6">{{ $point }}</p>
                @endforeach
            </div>
        </aside>

    </div>
</div>
@endsection
