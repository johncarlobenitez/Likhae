<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Order Placed — LIKHAE</title>

    @vite([
        'resources/css/buyer/order-success.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    $buyer = [
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'message_count' => 2,
        'notification_count' => 4,
    ];

    $order = [
        'order_number' => 'LH-20260818-0001',
        'status' => 'To Ship',
        'payment_method' => 'Cash on Delivery',
        'payment_status' => 'Pending',
        'subtotal' => 2896,
        'shipping' => 60,
        'discount' => 50,
        'total' => 2906,
        'placed_at' => now()->format('F d, Y • h:i A'),
        'recipient' => 'Juan Dela Cruz',
        'phone' => '0917 123 4567',
        'address' => '123 Rizal Street, Barangay San Antonio, Makati City, Metro Manila 1203',
    ];

    $steps = [
        ['label' => 'Order Placed', 'text' => 'Your order has been received by LIKHAE.', 'done' => true],
        ['label' => 'Seller Preparing Order', 'text' => 'The seller will prepare your items for shipment.', 'done' => false],
        ['label' => 'Handed to Courier', 'text' => 'Your parcel will be assigned to a courier.', 'done' => false],
        ['label' => 'In Transit', 'text' => 'Your parcel will travel to your delivery area.', 'done' => false],
        ['label' => 'Delivered', 'text' => 'Confirm receipt and leave a review.', 'done' => false],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container flex h-16 items-center gap-4">
        <a href="{{ url('/buyer/home') }}" class="flex items-center gap-2">
            <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">L</span>
            <span class="text-xl font-black tracking-tight">LIKHAE</span>
        </a>

        <div class="hidden flex-1 text-center text-[10px] font-bold uppercase tracking-[0.18em] text-[#9b948c] md:block">
            Order Confirmation
        </div>

        <nav class="ml-auto flex items-center gap-2">
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
</header>

<main>
    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container py-12 sm:py-16">
            <div class="mx-auto max-w-3xl text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#079b72] text-white shadow-[0_10px_30px_rgba(7,155,114,0.18)]">
                    <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m6.5 12.5 3.2 3.2L17.8 7.6"></path>
                    </svg>
                </div>

                <p class="mt-6 text-[10px] font-black uppercase tracking-[0.24em] text-[#079b72]">ORDER CONFIRMED</p>
                <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">Order placed successfully</h1>

                <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-[#847d75]">
                    Thank you, {{ $buyer['first_name'] }}. Your order has been received and is waiting for the seller to prepare it.
                </p>

                <div class="mt-7 flex flex-wrap justify-center gap-3">
                    <a href="{{ url('/buyer/orders/' . $order['order_number']) }}" class="primary-btn">View My Order</a>
                    <a href="{{ url('/buyer/products') }}" class="secondary-btn">Continue Shopping</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8 lg:py-12">
        <div class="likhae-container">
            <div class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-[1fr_320px]">

                <div class="space-y-5">
                    <section class="success-card">
                        <div class="success-card-header">
                            <div>
                                <p class="success-label">ORDER NUMBER</p>
                                <h2 class="mt-2 text-xl font-black">{{ $order['order_number'] }}</h2>
                            </div>
                            <span class="order-status-badge">{{ strtoupper($order['status']) }}</span>
                        </div>

                        <div class="grid gap-5 border-t border-[#eee8e1] p-5 sm:grid-cols-2 sm:p-6">
                            <div>
                                <p class="success-label">ORDER DATE</p>
                                <p class="mt-2 text-sm font-semibold text-[#4f4a45]">{{ $order['placed_at'] }}</p>
                            </div>

                            <div>
                                <p class="success-label">PAYMENT METHOD</p>
                                <p class="mt-2 text-sm font-semibold text-[#4f4a45]">{{ $order['payment_method'] }}</p>
                                <p class="mt-1 text-[10px] text-[#9d968e]">Payment status: {{ $order['payment_status'] }}</p>
                            </div>
                        </div>
                    </section>

                    <section class="success-card">
                        <div class="p-5 sm:p-6">
                            <p class="success-label">WHAT HAPPENS NEXT</p>
                            <h2 class="mt-2 text-lg font-black">Order Progress</h2>
                            <p class="mt-2 text-xs leading-5 text-[#928b83]">
                                Track these steps from My Orders as your parcel moves through delivery.
                            </p>
                        </div>

                        <div class="border-t border-[#eee8e1] p-5 sm:p-6">
                            @foreach($steps as $step)
                                <div class="timeline-step">
                                    <div class="timeline-icon {{ $step['done'] ? 'is-done' : '' }}">
                                        @if($step['done'])
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="m6.5 12.5 3.2 3.2L17.8 7.6"></path>
                                            </svg>
                                        @else
                                            <span class="h-2 w-2 rounded-full bg-current"></span>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="text-sm font-bold">{{ $step['label'] }}</p>
                                        <p class="mt-1 text-[11px] leading-5 text-[#9a938b]">{{ $step['text'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="success-card p-5 sm:p-6">
                        <p class="success-label">DELIVERY ADDRESS</p>

                        <div class="mt-4 flex items-start gap-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center border border-[#ddd6ce] bg-[#faf8f5] text-[#d92d2f]">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"></path>
                                    <circle cx="12" cy="10" r="2"></circle>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-black">{{ $order['recipient'] }}</p>
                                <p class="mt-1 text-xs text-[#706a63]">{{ $order['phone'] }}</p>
                                <p class="mt-3 max-w-xl text-sm leading-6 text-[#5e5953]">{{ $order['address'] }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <aside>
                    <div class="sticky top-[88px] space-y-4">
                        <section class="success-card p-5">
                            <p class="success-label">ORDER SUMMARY</p>

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
                                    <strong class="text-[#079b72]">-₱{{ number_format($order['discount']) }}</strong>
                                </div>
                            </div>

                            <div class="mt-5 border-t border-[#e9e3dc] pt-5">
                                <div class="flex items-end justify-between gap-4">
                                    <div>
                                        <p class="success-label">TOTAL</p>
                                        <p class="mt-1 text-[9px] text-[#aaa39b]">Final order amount</p>
                                    </div>
                                    <strong class="text-2xl font-black text-[#d92d2f]">₱{{ number_format($order['total']) }}</strong>
                                </div>
                            </div>
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
                                    <p class="mt-1 text-[10px] leading-5 text-[#9b948c]">
                                        Your order is protected throughout fulfillment and delivery.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <a href="{{ url('/buyer/orders') }}" class="secondary-btn flex w-full">View All Orders</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>

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