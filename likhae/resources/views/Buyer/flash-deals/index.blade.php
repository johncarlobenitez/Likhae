<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Flash Deals — LIKHAE</title>

    @vite([
        'resources/css/buyer/flash-deals.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo Flash Deal Data
    |--------------------------------------------------------------------------
    | Replace with real flash_deals/products from your controller later.
    */
    $buyer = [
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'cart_count' => 2,
        'message_count' => 3,
        'notification_count' => 4,
    ];

    $categories = [
        ['key' => 'all', 'label' => 'All Deals'],
        ['key' => 'electronics', 'label' => 'Electronics'],
        ['key' => 'fashion', 'label' => 'Fashion'],
        ['key' => 'home', 'label' => 'Home & Living'],
        ['key' => 'beauty', 'label' => 'Beauty'],
        ['key' => 'local', 'label' => 'Local Finds'],
    ];

    $activeCategory = request('category', 'all');

    $deals = [
        [
            'id' => 1,
            'category' => 'electronics',
            'name' => 'Baseus Wireless Earbuds A3i Pro',
            'slug' => 'baseus-wireless-earbuds-a3i-pro',
            'seller' => 'TECHHUB PH',
            'seller_slug' => 'techhub-ph',
            'price' => 599,
            'old_price' => 999,
            'rating' => 4.8,
            'sold' => 82,
            'stock_total' => 100,
            'stock_left' => 18,
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 2,
            'category' => 'electronics',
            'name' => 'Mechanical Keyboard TKL RGB',
            'slug' => 'mechanical-keyboard-tkl-rgb',
            'seller' => 'TECHHUB PH',
            'seller_slug' => 'techhub-ph',
            'price' => 1599,
            'old_price' => 2399,
            'rating' => 4.9,
            'sold' => 63,
            'stock_total' => 80,
            'stock_left' => 17,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 3,
            'category' => 'fashion',
            'name' => 'Premium Cotton Polo Shirt',
            'slug' => 'premium-cotton-polo-shirt',
            'seller' => 'STYLE MANILA',
            'seller_slug' => 'style-manila',
            'price' => 249,
            'old_price' => 499,
            'rating' => 4.7,
            'sold' => 91,
            'stock_total' => 120,
            'stock_left' => 29,
            'image' => 'https://images.unsplash.com/photo-1603252109303-2751441dd157?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 4,
            'category' => 'local',
            'name' => 'Handwoven Rattan Tote Bag',
            'slug' => 'handwoven-rattan-tote-bag',
            'seller' => 'LIKHA ARTISANS',
            'seller_slug' => 'likha-artisans',
            'price' => 699,
            'old_price' => 1099,
            'rating' => 4.7,
            'sold' => 48,
            'stock_total' => 60,
            'stock_left' => 12,
            'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 5,
            'category' => 'home',
            'name' => 'Capiz Shell Pendant Lamp',
            'slug' => 'capiz-shell-pendant-lamp',
            'seller' => 'CEBU CRAFTS CO.',
            'seller_slug' => 'cebu-crafts-co',
            'price' => 1499,
            'old_price' => 2299,
            'rating' => 4.6,
            'sold' => 34,
            'stock_total' => 50,
            'stock_left' => 16,
            'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 6,
            'category' => 'beauty',
            'name' => 'Daily Hydrating Skin Care Set',
            'slug' => 'daily-hydrating-skin-care-set',
            'seller' => 'BEAUTY BAYA',
            'seller_slug' => 'beauty-baya',
            'price' => 449,
            'old_price' => 799,
            'rating' => 4.8,
            'sold' => 76,
            'stock_total' => 100,
            'stock_left' => 24,
            'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=90',
        ],
    ];

    if ($activeCategory !== 'all') {
        $deals = array_values(
            array_filter($deals, fn ($deal) => $deal['category'] === $activeCategory)
        );
    }
@endphp

<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container">
        <div class="flex h-16 items-center gap-4">
            <a href="{{ url('/buyer/home') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">L</span>
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

                <a href="{{ url('/buyer/wishlist') }}" class="buyer-header-action">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"></path>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Wishlist</span>
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
    <section class="flash-hero">
        <div class="likhae-container py-10 sm:py-14">
            <div class="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-end">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.26em] text-white/70">LIMITED-TIME OFFERS</p>
                    <h1 class="mt-3 text-4xl font-black tracking-[-0.05em] text-white sm:text-5xl">
                        FLASH DEALS
                    </h1>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-white/70">
                        Grab selected LIKHAE products at special prices before the timer runs out.
                    </p>
                </div>

                <div class="countdown-panel">
                    <p class="text-[9px] font-black uppercase tracking-[0.16em] text-white/60">
                        DEALS RESET IN
                    </p>

                    <div class="mt-3 flex items-center gap-2">
                        <div class="countdown-box">
                            <strong id="hours">00</strong>
                            <span>HRS</span>
                        </div>
                        <span class="countdown-separator">:</span>
                        <div class="countdown-box">
                            <strong id="minutes">00</strong>
                            <span>MIN</span>
                        </div>
                        <span class="countdown-separator">:</span>
                        <div class="countdown-box">
                            <strong id="seconds">00</strong>
                            <span>SEC</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container">
            <div class="flex overflow-x-auto hide-scrollbar">
                @foreach($categories as $category)
                    <a
                        href="{{ url('/buyer/flash-deals?category=' . $category['key']) }}"
                        class="deal-tab {{ $activeCategory === $category['key'] ? 'is-active' : '' }}"
                    >
                        {{ $category['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-8 lg:py-12">
        <div class="likhae-container">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="buyer-section-eyebrow">ENDING SOON</p>
                    <h2 class="mt-3 text-2xl font-black tracking-[-0.03em]">Today's Best Deals</h2>
                </div>

                <p class="text-xs text-[#918a82]">
                    {{ count($deals) }} deal{{ count($deals) === 1 ? '' : 's' }} available
                </p>
            </div>

            @if(count($deals) > 0)
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($deals as $deal)
                        @php
                            $discount = round((($deal['old_price'] - $deal['price']) / $deal['old_price']) * 100);
                            $soldPercent = min(100, round(($deal['sold'] / $deal['stock_total']) * 100));
                        @endphp

                        <article class="deal-card">
                            <div class="relative">
                                <a
                                    href="{{ url('/buyer/products/' . $deal['slug']) }}"
                                    class="block aspect-[4/3] overflow-hidden bg-[#eee8e0]"
                                >
                                    <img
                                        src="{{ $deal['image'] }}"
                                        alt="{{ $deal['name'] }}"
                                        class="h-full w-full object-cover transition duration-500 hover:scale-105"
                                    >
                                </a>

                                <span class="discount-badge">-{{ $discount }}%</span>

                                <form
                                    method="POST"
                                    action="{{ url('/buyer/wishlist') }}"
                                    class="absolute right-3 top-3"
                                >
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $deal['id'] }}">

                                    <button type="submit" class="wishlist-button" aria-label="Add to wishlist">
                                        ♡
                                    </button>
                                </form>
                            </div>

                            <div class="p-4">
                                <a
                                    href="{{ url('/buyer/seller/' . $deal['seller_slug']) }}"
                                    class="text-[9px] font-black uppercase tracking-[0.12em] text-[#8f8880] transition hover:text-[#d92d2f]"
                                >
                                    {{ $deal['seller'] }}
                                </a>

                                <a
                                    href="{{ url('/buyer/products/' . $deal['slug']) }}"
                                    class="mt-2 line-clamp-2 min-h-[44px] text-sm font-black leading-5 transition hover:text-[#d92d2f]"
                                >
                                    {{ $deal['name'] }}
                                </a>

                                <div class="mt-3 flex items-center gap-2 text-[10px]">
                                    <span class="text-[#f2a000]">★</span>
                                    <span class="font-bold">{{ number_format($deal['rating'], 1) }}</span>
                                    <span class="text-[#aaa39b]">· {{ $deal['sold'] }} sold</span>
                                </div>

                                <div class="mt-4 flex flex-wrap items-end gap-2">
                                    <span class="text-xl font-black text-[#d92d2f]">
                                        ₱{{ number_format($deal['price']) }}
                                    </span>

                                    <span class="pb-0.5 text-[10px] text-[#aaa39b] line-through">
                                        ₱{{ number_format($deal['old_price']) }}
                                    </span>
                                </div>

                                <div class="mt-4">
                                    <div class="flex items-center justify-between text-[9px] font-bold uppercase tracking-[0.08em]">
                                        <span class="text-[#d92d2f]">{{ $deal['stock_left'] }} left</span>
                                        <span class="text-[#9d968e]">{{ $soldPercent }}% sold</span>
                                    </div>

                                    <div class="stock-track mt-2">
                                        <span class="stock-fill" style="width: {{ $soldPercent }}%;"></span>
                                    </div>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-2">
                                    <form method="POST" action="{{ url('/buyer/cart') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $deal['id'] }}">
                                        <input type="hidden" name="quantity" value="1">

                                        <button type="submit" class="deal-action secondary w-full">
                                            Add to Cart
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ url('/buyer/checkout/buy-now') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $deal['id'] }}">
                                        <input type="hidden" name="quantity" value="1">

                                        <button type="submit" class="deal-action primary w-full">
                                            Buy Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="border border-[#dfd9d2] bg-white px-6 py-20 text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#fff1f1] text-xl font-black text-[#d92d2f]">
                        %
                    </div>

                    <h2 class="mt-5 text-xl font-black">No flash deals in this category</h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#918a82]">
                        Check another category or return later for new limited-time offers.
                    </p>

                    <a href="{{ url('/buyer/flash-deals') }}" class="deal-action primary mt-6">
                        View All Deals
                    </a>
                </div>
            @endif
        </div>
    </section>

    <section class="border-y border-[#ded8d0] bg-white">
        <div class="likhae-container py-10">
            <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                    <p class="buyer-section-eyebrow">LIKHAE DEAL REMINDER</p>
                    <h2 class="mt-3 text-2xl font-black tracking-[-0.03em]">
                        Don't miss the next price drop
                    </h2>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-[#8f8880]">
                        Save products to your wishlist so you can quickly return when a new deal becomes available.
                    </p>
                </div>

                <a href="{{ url('/buyer/wishlist') }}" class="deal-action secondary">
                    Open My Wishlist
                </a>
            </div>
        </div>
    </section>
</main>

<footer class="mt-12 bg-[#0a0a0a] text-white">
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hours = document.getElementById('hours');
        const minutes = document.getElementById('minutes');
        const seconds = document.getElementById('seconds');

        function updateCountdown() {
            const now = new Date();
            const tomorrow = new Date(now);
            tomorrow.setHours(24, 0, 0, 0);

            const distance = Math.max(0, tomorrow.getTime() - now.getTime());

            const totalSeconds = Math.floor(distance / 1000);
            const h = Math.floor(totalSeconds / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;

            hours.textContent = String(h).padStart(2, '0');
            minutes.textContent = String(m).padStart(2, '0');
            seconds.textContent = String(s).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
</script>

</body>
</html>