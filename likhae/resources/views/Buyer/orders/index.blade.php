<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>My Orders — LIKHAE</title>

    @vite([
        'resources/css/buyer/orders.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo Buyer / Order Data
    |--------------------------------------------------------------------------
    | Replace with data from Buyer\OrderController@index later.
    */
    $buyer = [
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'cart_count' => 2,
        'message_count' => 3,
        'notification_count' => 4,
    ];

    $tabs = [
        ['key' => 'all', 'label' => 'All', 'count' => 8],
        ['key' => 'to-pay', 'label' => 'To Pay', 'count' => 1],
        ['key' => 'to-ship', 'label' => 'To Ship', 'count' => 2],
        ['key' => 'in-transit', 'label' => 'In Transit', 'count' => 1],
        ['key' => 'out-for-delivery', 'label' => 'Out for Delivery', 'count' => 1],
        ['key' => 'completed', 'label' => 'Completed', 'count' => 2],
        ['key' => 'cancelled', 'label' => 'Cancelled', 'count' => 1],
    ];

    $activeTab = request('status', 'all');

    $orders = [
        [
            'number' => 'LH-20260818-0001',
            'seller' => 'TECHHUB PH',
            'seller_slug' => 'techhub-ph',
            'status_key' => 'to-ship',
            'status' => 'To Ship',
            'status_text' => 'Seller is preparing your order.',
            'placed_at' => 'Aug 18, 2026',
            'total' => 2398,
            'payment' => 'Cash on Delivery',
            'courier' => null,
            'eta' => 'Aug 20–23',
            'items' => [
                [
                    'name' => 'Baseus Wireless Earbuds A3i Pro',
                    'slug' => 'baseus-wireless-earbuds-a3i-pro',
                    'variation' => 'Black / Standard',
                    'quantity' => 1,
                    'price' => 599,
                    'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=700&q=90',
                ],
                [
                    'name' => 'Mechanical Keyboard TKL RGB',
                    'slug' => 'mechanical-keyboard-tkl-rgb',
                    'variation' => 'Black / Red Switch',
                    'quantity' => 1,
                    'price' => 1799,
                    'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=700&q=90',
                ],
            ],
        ],
        [
            'number' => 'LH-20260816-0008',
            'seller' => 'STYLE MANILA',
            'seller_slug' => 'style-manila',
            'status_key' => 'in-transit',
            'status' => 'In Transit',
            'status_text' => 'Your parcel is moving to your delivery area.',
            'placed_at' => 'Aug 16, 2026',
            'total' => 558,
            'payment' => 'GCash',
            'courier' => 'Miguel R. Santos',
            'eta' => 'Aug 19–20',
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
        ],
        [
            'number' => 'LH-20260815-0004',
            'seller' => 'CEBU CRAFTS CO.',
            'seller_slug' => 'cebu-crafts-co',
            'status_key' => 'out-for-delivery',
            'status' => 'Out for Delivery',
            'status_text' => 'Your courier is delivering your parcel today.',
            'placed_at' => 'Aug 15, 2026',
            'total' => 1959,
            'payment' => 'Maya',
            'courier' => 'Ana P. Reyes',
            'eta' => 'Today',
            'items' => [
                [
                    'name' => 'Capiz Shell Pendant Lamp',
                    'slug' => 'capiz-shell-pendant-lamp',
                    'variation' => 'Natural / Medium',
                    'quantity' => 1,
                    'price' => 1899,
                    'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=700&q=90',
                ],
            ],
        ],
        [
            'number' => 'LH-20260810-0012',
            'seller' => 'LIKHA ARTISANS',
            'seller_slug' => 'likha-artisans',
            'status_key' => 'completed',
            'status' => 'Completed',
            'status_text' => 'Delivered successfully on Aug 13, 2026.',
            'placed_at' => 'Aug 10, 2026',
            'total' => 959,
            'payment' => 'Cash on Delivery',
            'courier' => 'Carlo M. Dela Cruz',
            'eta' => 'Delivered',
            'items' => [
                [
                    'name' => 'Handwoven Rattan Tote Bag',
                    'slug' => 'handwoven-rattan-tote-bag',
                    'variation' => 'Natural / Standard',
                    'quantity' => 1,
                    'price' => 899,
                    'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=700&q=90',
                ],
            ],
        ],
        [
            'number' => 'LH-20260808-0006',
            'seller' => 'TECH REPUBLIC PH',
            'seller_slug' => 'tech-republic-ph',
            'status_key' => 'cancelled',
            'status' => 'Cancelled',
            'status_text' => 'This order was cancelled.',
            'placed_at' => 'Aug 8, 2026',
            'total' => 34999,
            'payment' => 'Card',
            'courier' => null,
            'eta' => null,
            'items' => [
                [
                    'name' => 'Lenovo IdeaPad Slim 5i 14" Laptop',
                    'slug' => 'lenovo-ideapad-slim-5i-14-laptop',
                    'variation' => 'Gray / 16GB + 512GB',
                    'quantity' => 1,
                    'price' => 34999,
                    'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=700&q=90',
                ],
            ],
        ],
    ];

    if ($activeTab !== 'all') {
        $orders = array_values(array_filter($orders, fn ($order) => $order['status_key'] === $activeTab));
    }
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
                <span class="text-[#4d4944]">My Orders</span>
            </div>

            <div class="mt-5">
                <p class="buyer-section-eyebrow">PURCHASE HISTORY</p>
                <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">My Orders</h1>
                <p class="mt-2 text-sm text-[#8b847c]">
                    Track active deliveries, review completed orders, and manage your purchases.
                </p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         ORDER TABS
    ====================================================== --}}
    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container">
            <div class="flex overflow-x-auto hide-scrollbar">
                @foreach($tabs as $tab)
                    <a
                        href="{{ url('/buyer/orders?status=' . $tab['key']) }}"
                        class="order-tab {{ $activeTab === $tab['key'] ? 'is-active' : '' }}"
                    >
                        <span>{{ $tab['label'] }}</span>

                        @if($tab['count'] > 0)
                            <span class="order-tab-count">{{ $tab['count'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         ORDER LIST
    ====================================================== --}}
    <section class="py-8 lg:py-12">
        <div class="likhae-container">

            {{-- Search / Filter --}}
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form action="{{ url('/buyer/orders') }}" method="GET" class="flex w-full max-w-lg">
                    <input type="hidden" name="status" value="{{ $activeTab }}">

                    <input
                        type="search"
                        name="search"
                        placeholder="Search order number or product..."
                        class="h-11 min-w-0 flex-1 border border-r-0 border-[#dcd5cd] bg-white px-4 text-sm outline-none focus:border-[#d92d2f]"
                    >

                    <button
                        type="submit"
                        class="h-11 bg-[#111] px-5 text-xs font-bold text-white transition hover:bg-[#d92d2f]"
                    >
                        Search
                    </button>
                </form>

                <p class="text-xs text-[#9b948c]">
                    Showing {{ count($orders) }} order{{ count($orders) === 1 ? '' : 's' }}
                </p>
            </div>

            @if(count($orders) > 0)
                <div class="space-y-5">
                    @foreach($orders as $order)
                        <article class="order-card">
                            {{-- Order Header --}}
                            <div class="order-card-header">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <a
                                            href="{{ url('/buyer/seller/' . $order['seller_slug']) }}"
                                            class="text-xs font-black uppercase tracking-[0.08em] transition hover:text-[#d92d2f]"
                                        >
                                            {{ $order['seller'] }}
                                        </a>

                                        <span class="hidden h-4 w-px bg-[#ddd6ce] sm:block"></span>

                                        <p class="text-[10px] text-[#928b83]">
                                            Order #{{ $order['number'] }}
                                        </p>
                                    </div>

                                    <p class="mt-2 text-[10px] text-[#aaa39b]">
                                        Placed {{ $order['placed_at'] }}
                                    </p>
                                </div>

                                <div class="text-left sm:text-right">
                                    <span class="status-badge status-{{ $order['status_key'] }}">
                                        {{ strtoupper($order['status']) }}
                                    </span>

                                    <p class="mt-2 max-w-[260px] text-[10px] leading-4 text-[#918a82]">
                                        {{ $order['status_text'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- Items --}}
                            <div class="divide-y divide-[#eee8e1]">
                                @foreach($order['items'] as $item)
                                    <div class="grid gap-4 p-5 sm:grid-cols-[86px_minmax(0,1fr)_120px] sm:items-center">
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

                                            <p class="mt-2 text-[10px] text-[#908981]">
                                                {{ $item['variation'] }}
                                            </p>

                                            <p class="mt-1 text-[10px] text-[#908981]">
                                                Qty: {{ $item['quantity'] }}
                                            </p>
                                        </div>

                                        <div class="sm:text-right">
                                            <p class="text-[9px] uppercase tracking-[0.14em] text-[#9b948c]">
                                                Item Price
                                            </p>

                                            <p class="mt-1 text-base font-black text-[#d92d2f]">
                                                ₱{{ number_format($item['price'] * $item['quantity']) }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Delivery / Payment --}}
                            <div class="grid gap-4 border-t border-[#e8e2da] bg-[#faf8f5] px-5 py-4 sm:grid-cols-3">
                                <div>
                                    <p class="order-meta-label">Payment</p>
                                    <p class="mt-1 text-xs font-semibold text-[#57514b]">
                                        {{ $order['payment'] }}
                                    </p>
                                </div>

                                <div>
                                    <p class="order-meta-label">Courier</p>
                                    <p class="mt-1 text-xs font-semibold text-[#57514b]">
                                        {{ $order['courier'] ?? 'Not assigned yet' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="order-meta-label">Delivery</p>
                                    <p class="mt-1 text-xs font-semibold text-[#57514b]">
                                        {{ $order['eta'] ?? '—' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Footer / Actions --}}
                            <div class="flex flex-col gap-4 border-t border-[#e8e2da] px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-[0.14em] text-[#918a82]">
                                        Order Total
                                    </p>

                                    <p class="mt-1 text-xl font-black text-[#d92d2f]">
                                        ₱{{ number_format($order['total']) }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @if($order['status_key'] === 'to-ship')
                                        <a
                                            href="{{ url('/buyer/messages/new?seller=' . $order['seller_slug']) }}"
                                            class="order-action secondary"
                                        >
                                            Chat Seller
                                        </a>

                                        <a
                                            href="{{ url('/buyer/orders/' . $order['number']) }}"
                                            class="order-action primary"
                                        >
                                            View Order
                                        </a>

                                    @elseif($order['status_key'] === 'in-transit' || $order['status_key'] === 'out-for-delivery')
                                        @if($order['courier'])
                                            <a
                                                href="{{ url('/buyer/messages/new?order=' . $order['number'] . '&courier=1') }}"
                                                class="order-action secondary"
                                            >
                                                Message Courier
                                            </a>
                                        @endif

                                        <a
                                            href="{{ url('/buyer/orders/' . $order['number'] . '#tracking') }}"
                                            class="order-action primary"
                                        >
                                            Track Order
                                        </a>

                                    @elseif($order['status_key'] === 'completed')
                                        <a
                                            href="{{ url('/buyer/products/' . $order['items'][0]['slug']) }}"
                                            class="order-action secondary"
                                        >
                                            Buy Again
                                        </a>

                                        <a
                                            href="{{ url('/buyer/orders/' . $order['number'] . '/review') }}"
                                            class="order-action primary"
                                        >
                                            Rate & Review
                                        </a>

                                    @elseif($order['status_key'] === 'cancelled')
                                        <a
                                            href="{{ url('/buyer/products/' . $order['items'][0]['slug']) }}"
                                            class="order-action secondary"
                                        >
                                            Buy Again
                                        </a>

                                        <a
                                            href="{{ url('/buyer/orders/' . $order['number']) }}"
                                            class="order-action primary"
                                        >
                                            View Details
                                        </a>

                                    @else
                                        <a
                                            href="{{ url('/buyer/orders/' . $order['number']) }}"
                                            class="order-action primary"
                                        >
                                            View Order
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination placeholder --}}
                <div class="mt-8 flex items-center justify-center gap-2">
                    <button class="pagination-btn" disabled>←</button>
                    <button class="pagination-btn is-active">1</button>
                    <button class="pagination-btn">2</button>
                    <button class="pagination-btn">3</button>
                    <button class="pagination-btn">→</button>
                </div>
            @else
                <div class="border border-[#dfd9d2] bg-white px-6 py-16 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-[#f5f2ed] text-[#aaa39b]">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="m4 7 8-4 8 4-8 4-8-4Z"></path>
                            <path d="M4 7v10l8 4 8-4V7"></path>
                            <path d="M12 11v10"></path>
                        </svg>
                    </div>

                    <h2 class="mt-5 text-lg font-black">No orders found</h2>

                    <p class="mt-2 text-sm text-[#938c84]">
                        There are no orders under this status yet.
                    </p>

                    <a href="{{ url('/buyer/products') }}" class="order-action primary mt-6">
                        Start Shopping
                    </a>
                </div>
            @endif
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
                    <a href="#">Returns</a>
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