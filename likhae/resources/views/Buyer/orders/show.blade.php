<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Order Details — LIKHAE</title>

    @vite([
        'resources/css/buyer/order-details.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo Buyer / Order Data
    |--------------------------------------------------------------------------
    | Replace these arrays with a real $order model later.
    */
    $buyer = [
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'cart_count' => 2,
        'message_count' => 3,
        'notification_count' => 4,
    ];

    $order = [
        'number' => 'LH-20260816-0008',
        'status_key' => 'in-transit',
        'status' => 'In Transit',
        'status_text' => 'Your parcel is currently moving to your delivery area.',
        'placed_at' => 'August 16, 2026 • 10:42 AM',
        'payment_method' => 'GCash',
        'payment_status' => 'Paid',
        'subtotal' => 498,
        'shipping' => 60,
        'discount' => 0,
        'total' => 558,
        'seller' => 'STYLE MANILA',
        'seller_slug' => 'style-manila',
        'seller_rating' => 4.8,
        'seller_location' => 'Quezon City, NCR',
        'courier' => 'Miguel R. Santos',
        'courier_phone' => '0917 555 2188',
        'tracking_number' => 'CR-982341',
        'eta' => 'August 19–20, 2026',
        'recipient' => 'Juan Dela Cruz',
        'phone' => '0917 123 4567',
        'address' => '123 Rizal Street, Barangay San Antonio, Makati City, Metro Manila 1203',
        'items' => [
            [
                'name' => 'Premium Cotton Polo Shirt',
                'slug' => 'premium-cotton-polo-shirt',
                'variation' => 'White / Large',
                'quantity' => 2,
                'price' => 249,
                'image' => 'https://images.unsplash.com/photo-1603252109303-2751441dd157?auto=format&fit=crop&w=700&q=90',
            ],
        ],
    ];

    $timeline = [
        [
            'label' => 'Order Placed',
            'description' => 'Your order was successfully placed.',
            'time' => 'Aug 16, 2026 • 10:42 AM',
            'state' => 'done',
        ],
        [
            'label' => 'Seller Preparing Order',
            'description' => 'The seller packed and prepared your item.',
            'time' => 'Aug 16, 2026 • 2:15 PM',
            'state' => 'done',
        ],
        [
            'label' => 'Picked Up by Courier',
            'description' => 'The parcel was collected by the assigned courier.',
            'time' => 'Aug 17, 2026 • 8:30 AM',
            'state' => 'done',
        ],
        [
            'label' => 'In Transit',
            'description' => 'Your parcel is moving to your delivery area.',
            'time' => 'Aug 18, 2026 • 9:20 AM',
            'state' => 'current',
        ],
        [
            'label' => 'Out for Delivery',
            'description' => 'Your parcel will be delivered to your address.',
            'time' => null,
            'state' => 'upcoming',
        ],
        [
            'label' => 'Delivered',
            'description' => 'Confirm receipt and leave a review.',
            'time' => null,
            'state' => 'upcoming',
        ],
    ];
@endphp

{{-- =========================================================
     HEADER
========================================================= --}}
<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container">
        <div class="flex h-16 items-center gap-4">
            <a href="{{ url('/buyer/home') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">
                    L
                </span>
                <span class="text-xl font-black tracking-tight">LIKHAE</span>
            </a>

            <form action="{{ url('/buyer/products') }}" method="GET" class="hidden min-w-0 flex-1 md:flex">
                <div class="flex h-11 w-full overflow-hidden border border-[#dedad3] bg-white">
                    <input
                        type="search"
                        name="q"
                        placeholder="Search products, brands, Filipino finds..."
                        class="min-w-0 flex-1 bg-transparent px-4 text-sm outline-none placeholder:text-[#b9b4ad]"
                    >

                    <button
                        type="submit"
                        class="flex w-[108px] items-center justify-center gap-2 bg-[#d92d2f] px-4 text-sm font-bold text-white transition hover:bg-[#bd2024]"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>
                        Search
                    </button>
                </div>
            </form>

            <nav class="ml-auto flex items-center gap-1 sm:gap-2">
                <a href="{{ url('/buyer/notifications') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                            <path d="M10 21h4"></path>
                        </svg>
                        <span class="header-count">{{ $buyer['notification_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Alerts</span>
                </a>

                <a href="{{ url('/buyer/messages') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M4 5h16v11H8l-4 4V5Z"></path>
                        </svg>
                        <span class="header-count">{{ $buyer['message_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Messages</span>
                </a>

                <a href="{{ url('/buyer/cart') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6"></path>
                            <circle cx="9" cy="20" r="1"></circle>
                            <circle cx="17" cy="20" r="1"></circle>
                        </svg>
                        <span class="header-count">{{ $buyer['cart_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Cart</span>
                </a>

                <a href="{{ url('/buyer/account') }}" class="ml-1 flex items-center gap-2 border-l border-[#ece7e0] pl-3">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-[#111] text-xs font-black text-white">
                        {{ strtoupper(substr($buyer['first_name'], 0, 1)) }}
                    </span>
                    <span class="hidden xl:block">
                        <span class="block text-[11px] font-bold">{{ $buyer['first_name'] }}</span>
                        <span class="block text-[9px] text-[#a39c94]">Buyer</span>
                    </span>
                </a>
            </nav>
        </div>
    </div>
</header>

<main>
    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}
    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container py-8">
            <div class="text-[11px] text-[#9b958d]">
                <a href="{{ url('/buyer/home') }}" class="transition hover:text-[#d92d2f]">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ url('/buyer/orders') }}" class="transition hover:text-[#d92d2f]">My Orders</a>
                <span class="mx-2">/</span>
                <span class="text-[#4d4944]">{{ $order['number'] }}</span>
            </div>

            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="buyer-section-eyebrow">ORDER DETAILS</p>

                    <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">
                        {{ $order['number'] }}
                    </h1>

                    <p class="mt-2 text-sm text-[#8b847c]">
                        Placed {{ $order['placed_at'] }}
                    </p>
                </div>

                <div class="sm:text-right">
                    <span class="status-badge status-{{ $order['status_key'] }}">
                        {{ strtoupper($order['status']) }}
                    </span>

                    <p class="mt-2 max-w-sm text-xs leading-5 text-[#8d867e]">
                        {{ $order['status_text'] }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- =====================================================
         ORDER DETAIL CONTENT
    ====================================================== --}}
    <section class="py-8 lg:py-12">
        <div class="likhae-container">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_340px]">

                {{-- LEFT --}}
                <div class="min-w-0 space-y-5">

                    {{-- Tracking Timeline --}}
                    <section id="tracking" class="detail-card">
                        <div class="detail-card-header">
                            <div>
                                <p class="detail-card-eyebrow">TRACKING</p>
                                <h2>Order Progress</h2>
                            </div>

                            <div class="text-left sm:text-right">
                                <p class="detail-card-eyebrow">ESTIMATED DELIVERY</p>
                                <p class="mt-1 text-sm font-black text-[#d92d2f]">
                                    {{ $order['eta'] }}
                                </p>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="relative">
                                @foreach($timeline as $step)
                                    <div class="tracking-step {{ $step['state'] }}">
                                        <div class="tracking-line"></div>

                                        <div class="tracking-marker">
                                            @if($step['state'] === 'done')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="m6.5 12.5 3.2 3.2L17.8 7.6"></path>
                                                </svg>
                                            @elseif($step['state'] === 'current')
                                                <span class="h-2 w-2 rounded-full bg-white"></span>
                                            @else
                                                <span class="h-2 w-2 rounded-full bg-current"></span>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                                <p class="text-sm font-black">
                                                    {{ $step['label'] }}
                                                </p>

                                                @if($step['time'])
                                                    <p class="text-[10px] text-[#9c958d]">
                                                        {{ $step['time'] }}
                                                    </p>
                                                @endif
                                            </div>

                                            <p class="mt-1 text-xs leading-5 text-[#908981]">
                                                {{ $step['description'] }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    {{-- Courier --}}
                    <section class="detail-card">
                        <div class="detail-card-header">
                            <div>
                                <p class="detail-card-eyebrow">DELIVERY PARTNER</p>
                                <h2>Courier Information</h2>
                            </div>

                            <span class="text-[10px] font-bold text-[#079b72]">
                                Assigned
                            </span>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-start gap-4">
                                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-[#111] text-sm font-black text-white">
                                        MR
                                    </div>

                                    <div>
                                        <p class="text-sm font-black">
                                            {{ $order['courier'] }}
                                        </p>

                                        <p class="mt-1 text-xs text-[#817a72]">
                                            {{ $order['courier_phone'] }}
                                        </p>

                                        <div class="mt-2 text-[10px] text-[#9c958d]">
                                            Tracking No.
                                            <strong class="ml-1 font-semibold text-[#5e5953]">
                                                {{ $order['tracking_number'] }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <a
                                        href="{{ url('/buyer/messages/new?order=' . $order['number'] . '&courier=1') }}"
                                        class="action-btn secondary"
                                    >
                                        Message Courier
                                    </a>

                                    <a
                                        href="tel:{{ preg_replace('/\s+/', '', $order['courier_phone']) }}"
                                        class="action-btn secondary"
                                    >
                                        Call Courier
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Seller --}}
                    <section class="detail-card">
                        <div class="detail-card-header">
                            <div>
                                <p class="detail-card-eyebrow">SELLER</p>
                                <h2>{{ $order['seller'] }}</h2>
                            </div>

                            <div class="text-[10px] text-[#8f8880]">
                                <span class="text-[#f2a000]">★★★★★</span>
                                {{ number_format($order['seller_rating'], 1) }}
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-xs text-[#807970]">
                                        {{ $order['seller_location'] }}
                                    </p>
                                    <p class="mt-2 text-[10px] leading-5 text-[#a09991]">
                                        Contact the seller if you have questions about the product or order preparation.
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <a
                                        href="{{ url('/buyer/messages/new?seller=' . $order['seller_slug']) }}"
                                        class="action-btn secondary"
                                    >
                                        Chat Seller
                                    </a>

                                    <a
                                        href="{{ url('/buyer/seller/' . $order['seller_slug']) }}"
                                        class="action-btn primary"
                                    >
                                        Visit Shop
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Delivery Address --}}
                    <section class="detail-card">
                        <div class="detail-card-header">
                            <div>
                                <p class="detail-card-eyebrow">DELIVERY ADDRESS</p>
                                <h2>Recipient Details</h2>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="flex items-start gap-4">
                                <div class="grid h-11 w-11 shrink-0 place-items-center border border-[#ddd6ce] bg-[#faf8f5] text-[#d92d2f]">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                                        <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"></path>
                                        <circle cx="12" cy="10" r="2"></circle>
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-black">
                                        {{ $order['recipient'] }}
                                    </p>

                                    <p class="mt-1 text-xs text-[#706a63]">
                                        {{ $order['phone'] }}
                                    </p>

                                    <p class="mt-3 max-w-2xl text-sm leading-6 text-[#5e5953]">
                                        {{ $order['address'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Ordered Items --}}
                    <section class="detail-card">
                        <div class="detail-card-header">
                            <div>
                                <p class="detail-card-eyebrow">ORDER ITEMS</p>
                                <h2>{{ count($order['items']) }} Product{{ count($order['items']) === 1 ? '' : 's' }}</h2>
                            </div>
                        </div>

                        <div class="divide-y divide-[#eee8e1]">
                            @foreach($order['items'] as $item)
                                <article class="grid gap-4 p-5 sm:grid-cols-[90px_minmax(0,1fr)_120px] sm:items-center">
                                    <a
                                        href="{{ url('/buyer/products/' . $item['slug']) }}"
                                        class="block aspect-square overflow-hidden bg-[#eee8e0]"
                                    >
                                        <img
                                            src="{{ $item['image'] }}"
                                            alt="{{ $item['name'] }}"
                                            class="h-full w-full object-cover transition duration-500 hover:scale-105"
                                        >
                                    </a>

                                    <div class="min-w-0">
                                        <a
                                            href="{{ url('/buyer/products/' . $item['slug']) }}"
                                            class="block text-sm font-bold leading-6 transition hover:text-[#d92d2f]"
                                        >
                                            {{ $item['name'] }}
                                        </a>

                                        <p class="mt-2 text-[10px] text-[#918a82]">
                                            {{ $item['variation'] }}
                                        </p>

                                        <p class="mt-1 text-[10px] text-[#918a82]">
                                            Quantity: {{ $item['quantity'] }}
                                        </p>
                                    </div>

                                    <div class="sm:text-right">
                                        <p class="detail-card-eyebrow">
                                            SUBTOTAL
                                        </p>

                                        <p class="mt-1 text-base font-black text-[#d92d2f]">
                                            ₱{{ number_format($item['price'] * $item['quantity']) }}
                                        </p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                </div>

                {{-- RIGHT --}}
                <aside>
                    <div class="sticky top-[88px] space-y-4">

                        {{-- Order Summary --}}
                        <section class="detail-card p-5">
                            <p class="detail-card-eyebrow">ORDER SUMMARY</p>

                            <div class="mt-5 space-y-4 text-sm">
                                <div class="summary-row">
                                    <span>Merchandise</span>
                                    <strong>₱{{ number_format($order['subtotal']) }}</strong>
                                </div>

                                <div class="summary-row">
                                    <span>Shipping</span>
                                    <strong>₱{{ number_format($order['shipping']) }}</strong>
                                </div>

                                <div class="summary-row">
                                    <span>Discount</span>
                                    <strong class="text-[#079b72]">
                                        -₱{{ number_format($order['discount']) }}
                                    </strong>
                                </div>
                            </div>

                            <div class="mt-5 border-t border-[#e9e3dc] pt-5">
                                <div class="flex items-end justify-between gap-4">
                                    <div>
                                        <p class="detail-card-eyebrow">TOTAL</p>
                                        <p class="mt-1 text-[9px] text-[#aaa39b]">Final order amount</p>
                                    </div>

                                    <strong class="text-2xl font-black text-[#d92d2f]">
                                        ₱{{ number_format($order['total']) }}
                                    </strong>
                                </div>
                            </div>
                        </section>

                        {{-- Payment --}}
                        <section class="detail-card p-5">
                            <p class="detail-card-eyebrow">PAYMENT</p>

                            <div class="mt-4">
                                <p class="text-sm font-black">
                                    {{ $order['payment_method'] }}
                                </p>

                                <div class="mt-2 flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-[#079b72]"></span>
                                    <span class="text-[10px] font-semibold text-[#079b72]">
                                        {{ strtoupper($order['payment_status']) }}
                                    </span>
                                </div>
                            </div>
                        </section>

                        {{-- Status Actions --}}
                        <section class="detail-card p-5">
                            <p class="detail-card-eyebrow">ORDER ACTIONS</p>

                            <div class="mt-4 space-y-2">
                                @if($order['status_key'] === 'to-ship')
                                    <a
                                        href="{{ url('/buyer/messages/new?seller=' . $order['seller_slug']) }}"
                                        class="action-btn secondary w-full"
                                    >
                                        Chat Seller
                                    </a>

                                    <button
                                        type="button"
                                        class="action-btn danger w-full"
                                    >
                                        Cancel Order
                                    </button>

                                @elseif($order['status_key'] === 'in-transit' || $order['status_key'] === 'out-for-delivery')
                                    <a
                                        href="#tracking"
                                        class="action-btn primary w-full"
                                    >
                                        Track Order
                                    </a>

                                    <a
                                        href="{{ url('/buyer/messages/new?order=' . $order['number'] . '&courier=1') }}"
                                        class="action-btn secondary w-full"
                                    >
                                        Message Courier
                                    </a>

                                @elseif($order['status_key'] === 'completed')
                                    <a
                                        href="{{ url('/buyer/orders/' . $order['number'] . '/review') }}"
                                        class="action-btn primary w-full"
                                    >
                                        Rate & Review
                                    </a>

                                    <a
                                        href="{{ url('/buyer/products/' . $order['items'][0]['slug']) }}"
                                        class="action-btn secondary w-full"
                                    >
                                        Buy Again
                                    </a>

                                @elseif($order['status_key'] === 'cancelled')
                                    <a
                                        href="{{ url('/buyer/products/' . $order['items'][0]['slug']) }}"
                                        class="action-btn primary w-full"
                                    >
                                        Buy Again
                                    </a>
                                @endif

                                <a
                                    href="{{ url('/buyer/orders') }}"
                                    class="action-btn secondary w-full"
                                >
                                    Back to My Orders
                                </a>
                            </div>
                        </section>

                        {{-- Protection --}}
                        <section class="border border-[#dfd9d2] bg-[#faf8f5] p-4">
                            <div class="flex gap-3">
                                <span class="mt-0.5 text-[#079b72]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"></path>
                                    </svg>
                                </span>

                                <div>
                                    <p class="text-[11px] font-bold">
                                        LIKHAE Buyer Protection
                                    </p>

                                    <p class="mt-1 text-[10px] leading-5 text-[#9b948c]">
                                        Your order remains protected throughout fulfillment and delivery.
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>

{{-- =========================================================
     FOOTER
========================================================= --}}
<footer class="mt-4 bg-[#0a0a0a] text-white">
    <div class="likhae-container py-12">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.5fr_repeat(4,1fr)]">
            <div>
                <a href="{{ url('/buyer/home') }}" class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black">L</span>
                    <span class="text-xl font-black">LIKHAE</span>
                </a>

                <p class="mt-4 max-w-[230px] text-sm leading-6 text-white/35">
                    Shop More. Discover More. Live More. — Your Philippine marketplace.
                </p>
            </div>

            <div>
                <h3 class="footer-title">SHOP</h3>
                <div class="footer-links">
                    <a href="{{ url('/buyer/products') }}">All Products</a>
                    <a href="{{ url('/buyer/flash-deals') }}">Flash Deals</a>
                    <a href="{{ url('/buyer/local-finds') }}">Local Finds</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">MY ACCOUNT</h3>
                <div class="footer-links">
                    <a href="{{ url('/buyer/orders') }}">My Orders</a>
                    <a href="{{ url('/buyer/wishlist') }}">Wishlist</a>
                    <a href="{{ url('/buyer/messages') }}">Messages</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">SUPPORT</h3>
                <div class="footer-links">
                    <a href="#">Help Center</a>
                    <a href="{{ url('/buyer/orders') }}">Track Order</a>
                    <a href="#">Buyer Protection</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">COMPANY</h3>
                <div class="footer-links">
                    <a href="#">About LIKHAE</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms</a>
                </div>
            </div>
        </div>

        <div class="mt-12 border-t border-white/10 pt-6 text-[10px] text-white/25">
            © {{ date('Y') }} LIKHAE, Inc. — Made with pride in the Philippines.
        </div>
    </div>
</footer>

</body>
</html>