<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Notifications — LIKHAE</title>

    @vite([
        'resources/css/buyer/notifications.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo Notification Data
    |--------------------------------------------------------------------------
    | Replace with real notification records from your controller.
    */
    $buyer = [
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'cart_count' => 2,
        'message_count' => 3,
        'notification_count' => 6,
    ];

    $filters = [
        ['key' => 'all', 'label' => 'All'],
        ['key' => 'orders', 'label' => 'Orders'],
        ['key' => 'payments', 'label' => 'Payments'],
        ['key' => 'messages', 'label' => 'Messages'],
        ['key' => 'promos', 'label' => 'Promos'],
        ['key' => 'account', 'label' => 'Account'],
    ];

    $activeFilter = request('type', 'all');

    $notifications = [
        [
            'id' => 1,
            'type' => 'orders',
            'title' => 'Your order is now in transit',
            'message' => 'Order LH-20260816-0008 has been picked up and is on the way to your delivery area.',
            'time' => '12 minutes ago',
            'unread' => true,
            'icon' => 'truck',
            'link' => url('/buyer/orders/LH-20260816-0008#tracking'),
        ],
        [
            'id' => 2,
            'type' => 'messages',
            'title' => 'New message from TECHHUB PH',
            'message' => '“Yes, the black color is still available.”',
            'time' => '31 minutes ago',
            'unread' => true,
            'icon' => 'message',
            'link' => url('/buyer/messages'),
        ],
        [
            'id' => 3,
            'type' => 'payments',
            'title' => 'Payment confirmed',
            'message' => 'Your GCash payment for order LH-20260816-0008 was successfully received.',
            'time' => '2 hours ago',
            'unread' => true,
            'icon' => 'payment',
            'link' => url('/buyer/orders/LH-20260816-0008'),
        ],
        [
            'id' => 4,
            'type' => 'orders',
            'title' => 'Seller is preparing your order',
            'message' => 'TECHHUB PH is preparing order LH-20260818-0001 for shipment.',
            'time' => '5 hours ago',
            'unread' => true,
            'icon' => 'box',
            'link' => url('/buyer/orders/LH-20260818-0001'),
        ],
        [
            'id' => 5,
            'type' => 'promos',
            'title' => 'Flash Deals are live',
            'message' => 'Save on selected electronics, home finds, and local products today.',
            'time' => 'Yesterday',
            'unread' => false,
            'icon' => 'promo',
            'link' => url('/buyer/flash-deals'),
        ],
        [
            'id' => 6,
            'type' => 'account',
            'title' => 'Your buyer account is approved',
            'message' => 'Your LIKHAE buyer account verification has been completed successfully.',
            'time' => 'Aug 18, 2026',
            'unread' => false,
            'icon' => 'shield',
            'link' => url('/buyer/account'),
        ],
        [
            'id' => 7,
            'type' => 'orders',
            'title' => 'Order delivered successfully',
            'message' => 'Order LH-20260810-0012 was delivered. You can now rate your purchase.',
            'time' => 'Aug 13, 2026',
            'unread' => false,
            'icon' => 'check',
            'link' => url('/buyer/orders/LH-20260810-0012/review'),
        ],
    ];

    if ($activeFilter !== 'all') {
        $notifications = array_values(
            array_filter($notifications, fn ($item) => $item['type'] === $activeFilter)
        );
    }

    $unreadCount = count(array_filter($notifications, fn ($item) => $item['unread']));
@endphp

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
                <a href="{{ url('/buyer/notifications') }}" class="buyer-header-action text-[#d92d2f]">
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
    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container py-8">
            <div class="text-[11px] text-[#9b958d]">
                <a href="{{ url('/buyer/home') }}" class="transition hover:text-[#d92d2f]">Home</a>
                <span class="mx-2">/</span>
                <span class="text-[#4d4944]">Notifications</span>
            </div>

            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="buyer-section-eyebrow">ACCOUNT & ORDER UPDATES</p>
                    <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">
                        Notifications
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#8b847c]">
                        Stay updated on your orders, payments, messages, promos, and account activity.
                    </p>
                </div>

                @if($unreadCount > 0)
                    <form method="POST" action="{{ url('/buyer/notifications/read-all') }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="secondary-action">
                            Mark All as Read
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container">
            <div class="flex overflow-x-auto hide-scrollbar">
                @foreach($filters as $filter)
                    <a
                        href="{{ url('/buyer/notifications?type=' . $filter['key']) }}"
                        class="notification-tab {{ $activeFilter === $filter['key'] ? 'is-active' : '' }}"
                    >
                        {{ $filter['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-8 lg:py-12">
        <div class="likhae-container">
            <div class="mx-auto max-w-4xl">

                <div class="mb-5 flex items-center justify-between">
                    <p class="text-xs text-[#8f8880]">
                        {{ count($notifications) }} notification{{ count($notifications) === 1 ? '' : 's' }}
                    </p>

                    @if($unreadCount > 0)
                        <p class="text-[10px] font-black uppercase tracking-[0.12em] text-[#d92d2f]">
                            {{ $unreadCount }} unread
                        </p>
                    @endif
                </div>

                @forelse($notifications as $notification)
                    <article class="notification-card {{ $notification['unread'] ? 'is-unread' : '' }}">
                        <div class="notification-icon icon-{{ $notification['type'] }}">
                            @if($notification['icon'] === 'truck')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M3 6h11v10H3z"></path>
                                    <path d="M14 10h4l3 3v3h-7z"></path>
                                    <circle cx="7" cy="18" r="2"></circle>
                                    <circle cx="17" cy="18" r="2"></circle>
                                </svg>
                            @elseif($notification['icon'] === 'message')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M4 5h16v11H8l-4 4V5Z"></path>
                                </svg>
                            @elseif($notification['icon'] === 'payment')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <rect x="3" y="5" width="18" height="14" rx="1"></rect>
                                    <path d="M3 9h18"></path>
                                </svg>
                            @elseif($notification['icon'] === 'box')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="m4 7 8-4 8 4-8 4-8-4Z"></path>
                                    <path d="M4 7v10l8 4 8-4V7"></path>
                                </svg>
                            @elseif($notification['icon'] === 'promo')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M4 12v-2l11-5v14L4 14v-2Z"></path>
                                    <path d="M15 9h3a3 3 0 0 1 0 6h-3"></path>
                                </svg>
                            @elseif($notification['icon'] === 'shield')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"></path>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="m8 12 2.5 2.5L16 9"></path>
                                </svg>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-sm font-black">
                                            {{ $notification['title'] }}
                                        </h2>

                                        @if($notification['unread'])
                                            <span class="unread-dot"></span>
                                        @endif
                                    </div>

                                    <p class="mt-2 max-w-2xl text-xs leading-5 text-[#7f7871]">
                                        {{ $notification['message'] }}
                                    </p>

                                    <p class="mt-3 text-[9px] uppercase tracking-[0.1em] text-[#aaa39b]">
                                        {{ $notification['time'] }}
                                    </p>
                                </div>

                                <div class="flex shrink-0 flex-wrap gap-2">
                                    <a href="{{ $notification['link'] }}" class="notification-action primary">
                                        View
                                    </a>

                                    @if($notification['unread'])
                                        <form method="POST" action="{{ url('/buyer/notifications/' . $notification['id'] . '/read') }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="notification-action secondary">
                                                Mark as Read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="border border-[#dfd9d2] bg-white px-6 py-20 text-center">
                        <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#f5f2ed] text-[#aaa39b]">
                            <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                <path d="M10 21h4"></path>
                            </svg>
                        </div>

                        <h2 class="mt-5 text-xl font-black">No notifications here</h2>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#918a82]">
                            New order, payment, delivery, and account updates will appear here.
                        </p>

                        <a href="{{ url('/buyer/home') }}" class="notification-action primary mt-6">
                            Back to Home
                        </a>
                    </div>
                @endforelse
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