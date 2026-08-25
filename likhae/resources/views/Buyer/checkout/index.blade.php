<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout — LIKHAE</title>
    @vite(['resources/css/Buyer/home.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    $categories = [
        'Electronics', 'Fashion', 'Home & Living', 'Beauty & Care',
        'Food & Grocery', 'Sports', 'Books', 'Toys & Games',
    ];

    $orderItems = [
        [
            'name'     => 'Baseus Wireless Earbuds A3i Pro',
            'slug'     => 'baseus-wireless-earbuds-a3i-pro',
            'seller'   => 'TECHHUB PH',
            'price'    => 599,
            'quantity' => 1,
            'image'    => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=300&q=80',
        ],
        [
            'name'     => 'Mechanical Keyboard TKL RGB',
            'slug'     => 'mechanical-keyboard-tkl-rgb',
            'seller'   => 'TECHHUB PH',
            'price'    => 1799,
            'quantity' => 1,
            'image'    => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=300&q=80',
        ],
        [
            'name'     => 'Premium Cotton Polo Shirt',
            'slug'     => 'premium-cotton-polo-shirt',
            'seller'   => 'STYLE MANILA',
            'price'    => 249,
            'quantity' => 2,
            'image'    => 'https://images.unsplash.com/photo-1603252109303-2751441dd157?auto=format&fit=crop&w=300&q=80',
        ],
    ];

    $subtotal = collect($orderItems)->sum(fn($i) => $i['price'] * $i['quantity']);
    $shipping = 60;
    $total    = $subtotal + $shipping;
@endphp

{{-- HEADER --}}
<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container">
        <div class="flex h-16 items-center gap-4">
            <a href="{{ url('/buyer/home') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">L</span>
                <span class="text-xl font-black tracking-tight">LIKHAE</span>
            </a>

            <nav class="ml-auto flex items-center gap-1 sm:gap-3">
                <a href="{{ url('/buyer/cart') }}" class="buyer-header-action">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6"></path>
                        <circle cx="9" cy="20" r="1"></circle>
                        <circle cx="17" cy="20" r="1"></circle>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Cart</span>
                </a>
                <a href="{{ url('/buyer/account') }}" class="buyer-header-action">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <circle cx="12" cy="8" r="3.2"></circle>
                        <path d="M5.5 20c.7-4.2 3-6.3 6.5-6.3s5.8 2.1 6.5 6.3"></path>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Account</span>
                </a>
            </nav>
        </div>
    </div>

    {{-- Checkout steps --}}
    <div class="border-t border-[#ece8e1] bg-white">
        <div class="likhae-container flex h-10 items-center gap-2 text-[10px] font-bold uppercase tracking-[0.14em]">
            <a href="{{ url('/buyer/cart') }}" class="text-[#aaa39b] transition hover:text-[#d92d2f]">Cart</a>
            <span class="text-[#ddd6ce]">›</span>
            <span class="text-[#d92d2f]">Checkout</span>
            <span class="text-[#ddd6ce]">›</span>
            <span class="text-[#aaa39b]">Confirmation</span>
        </div>
    </div>
</header>

<main>
    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container py-7">
            <div class="text-[11px] text-[#9b958d]">
                <a href="{{ url('/buyer/home') }}" class="transition hover:text-[#d92d2f]">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ url('/buyer/cart') }}" class="transition hover:text-[#d92d2f]">Cart</a>
                <span class="mx-2">/</span>
                <span class="text-[#4d4944]">Checkout</span>
            </div>
            <h1 class="mt-4 text-3xl font-black tracking-[-0.04em] sm:text-4xl">Checkout</h1>
        </div>
    </section>

    <section class="py-8 lg:py-12">
        <div class="likhae-container">
            <form action="{{ url('/buyer/order') }}" method="POST">
                @csrf
                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px]">

                    {{-- LEFT --}}
                    <div class="space-y-6">

                        {{-- Delivery Address --}}
                        <section class="border border-[#dfd9d2] bg-white p-6">
                            <h2 class="text-xs font-black uppercase tracking-[0.16em]">Delivery Address</h2>

                            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-[#8d867e]">First Name</label>
                                    <input type="text" name="first_name" placeholder="Juan" class="w-full border border-[#ddd6ce] bg-white px-4 py-3 text-sm outline-none focus:border-[#d92d2f]">
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-[#8d867e]">Last Name</label>
                                    <input type="text" name="last_name" placeholder="Dela Cruz" class="w-full border border-[#ddd6ce] bg-white px-4 py-3 text-sm outline-none focus:border-[#d92d2f]">
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-[#8d867e]">Phone Number</label>
                                    <input type="tel" name="phone" placeholder="09XX XXX XXXX" class="w-full border border-[#ddd6ce] bg-white px-4 py-3 text-sm outline-none focus:border-[#d92d2f]">
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-[#8d867e]">Region</label>
                                    <select name="region" class="w-full border border-[#ddd6ce] bg-white px-4 py-3 text-sm outline-none focus:border-[#d92d2f]">
                                        <option value="">Select Region</option>
                                        <option>NCR</option>
                                        <option>Region III</option>
                                        <option>Region IV-A</option>
                                        <option>Region VII</option>
                                        <option>Region XI</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-[#8d867e]">Street Address</label>
                                    <input type="text" name="address" placeholder="House No., Street, Barangay" class="w-full border border-[#ddd6ce] bg-white px-4 py-3 text-sm outline-none focus:border-[#d92d2f]">
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-[#8d867e]">City / Municipality</label>
                                    <input type="text" name="city" placeholder="Quezon City" class="w-full border border-[#ddd6ce] bg-white px-4 py-3 text-sm outline-none focus:border-[#d92d2f]">
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-[#8d867e]">ZIP Code</label>
                                    <input type="text" name="zip" placeholder="1100" class="w-full border border-[#ddd6ce] bg-white px-4 py-3 text-sm outline-none focus:border-[#d92d2f]">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-[#8d867e]">Delivery Notes <span class="font-normal text-[#aaa39b]">(Optional)</span></label>
                                    <textarea name="notes" rows="2" placeholder="e.g. Leave at the gate, call before delivery..." class="w-full border border-[#ddd6ce] bg-white px-4 py-3 text-sm outline-none focus:border-[#d92d2f] resize-none"></textarea>
                                </div>
                            </div>
                        </section>

                        {{-- Payment Method --}}
                        <section class="border border-[#dfd9d2] bg-white p-6">
                            <h2 class="text-xs font-black uppercase tracking-[0.16em]">Payment Method</h2>

                            <div class="mt-5 space-y-3">
                                @foreach([
                                    ['value' => 'cod',      'label' => 'Cash on Delivery',  'desc' => 'Pay when your order arrives'],
                                    ['value' => 'gcash',    'label' => 'GCash',              'desc' => 'Pay via GCash e-wallet'],
                                    ['value' => 'maya',     'label' => 'Maya',               'desc' => 'Pay via Maya e-wallet'],
                                    ['value' => 'card',     'label' => 'Credit / Debit Card','desc' => 'Visa, Mastercard, JCB'],
                                ] as $method)
                                    <label class="flex cursor-pointer items-center gap-4 border border-[#ddd6ce] bg-[#faf8f5] px-4 py-4 transition has-[:checked]:border-[#d92d2f] has-[:checked]:bg-[#fff5f5]">
                                        <input type="radio" name="payment_method" value="{{ $method['value'] }}" {{ $method['value'] === 'cod' ? 'checked' : '' }} class="h-4 w-4 accent-[#d92d2f]">
                                        <div>
                                            <p class="text-sm font-bold">{{ $method['label'] }}</p>
                                            <p class="text-[10px] text-[#9b948c]">{{ $method['desc'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </section>

                    </div>

                    {{-- RIGHT: Order Summary --}}
                    <aside>
                        <div class="sticky top-[108px] space-y-4">

                            <section class="border border-[#dfd9d2] bg-white p-5">
                                <h2 class="text-xs font-black uppercase tracking-[0.16em]">Order Summary</h2>

                                <div class="mt-5 space-y-4">
                                    @foreach($orderItems as $item)
                                        <div class="flex gap-3">
                                            <a href="{{ url('/buyer/products/' . $item['slug']) }}" class="block h-16 w-16 shrink-0 overflow-hidden bg-[#eee8e0]">
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                            </a>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[9px] font-bold uppercase tracking-[0.12em] text-[#a59d93]">{{ $item['seller'] }}</p>
                                                <a href="{{ url('/buyer/products/' . $item['slug']) }}" class="mt-0.5 block text-xs font-medium leading-5 hover:text-[#d92d2f]">{{ $item['name'] }}</a>
                                                <p class="mt-1 text-[10px] text-[#9b948c]">Qty: {{ $item['quantity'] }}</p>
                                            </div>
                                            <p class="shrink-0 text-sm font-black text-[#d92d2f]">₱{{ number_format($item['price'] * $item['quantity']) }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-5 space-y-3 border-t border-[#e9e3dc] pt-5 text-sm">
                                    <div class="flex justify-between gap-4">
                                        <span class="text-[#817a72]">Subtotal</span>
                                        <strong>₱{{ number_format($subtotal) }}</strong>
                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <span class="text-[#817a72]">Shipping</span>
                                        <strong>₱{{ number_format($shipping) }}</strong>
                                    </div>
                                </div>

                                <div class="mt-4 border-t border-[#e9e3dc] pt-4 flex items-end justify-between gap-4">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#908981]">Total</p>
                                    <strong class="text-2xl font-black text-[#d92d2f]">₱{{ number_format($total) }}</strong>
                                </div>

                                <button type="submit" class="mt-6 flex h-12 w-full items-center justify-center gap-2 bg-[#d92d2f] px-5 text-sm font-bold text-white transition hover:bg-[#bd2024]">
                                    Place Order →
                                </button>

                                <p class="mt-3 text-center text-[9px] leading-4 text-[#aaa39b]">
                                    By placing your order you agree to LIKHAE's Terms & Buyer Protection policy.
                                </p>
                            </section>

                            <section class="border border-[#dfd9d2] bg-[#faf8f5] p-4">
                                <div class="flex gap-3">
                                    <span class="mt-0.5 text-[#079b72]">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"></path>
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-[11px] font-bold">LIKHAE Buyer Protection</p>
                                        <p class="mt-1 text-[10px] leading-5 text-[#9b948c]">Your payment is protected until your order is successfully completed.</p>
                                    </div>
                                </div>
                            </section>

                        </div>
                    </aside>

                </div>
            </form>
        </div>
    </section>
</main>

<footer class="mt-6 bg-[#0a0a0a] text-white">
    <div class="likhae-container py-10">
        <div class="flex flex-col gap-4 text-[10px] text-white/25 md:flex-row md:items-center md:justify-between">
            <p>© {{ date('Y') }} LIKHAE, Inc. — Made with pride in the Philippines.</p>
            <p>GCASH · MAYA · VISA · MASTERCARD · COD · BPI</p>
        </div>
    </div>
</footer>

</body>
</html>
