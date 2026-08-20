<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Local Finds — LIKHAE</title>

    @vite([
        'resources/css/buyer/local-finds.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo Local Finds Data
    |--------------------------------------------------------------------------
    | Replace with real sellers/products/regions from your controller later.
    */
    $buyer = [
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'cart_count' => 2,
        'message_count' => 3,
        'notification_count' => 4,
    ];

    $filters = [
        ['key' => 'all', 'label' => 'All'],
        ['key' => 'luzon', 'label' => 'Luzon'],
        ['key' => 'visayas', 'label' => 'Visayas'],
        ['key' => 'mindanao', 'label' => 'Mindanao'],
        ['key' => 'handmade', 'label' => 'Handmade'],
        ['key' => 'food', 'label' => 'Food'],
        ['key' => 'fashion', 'label' => 'Fashion'],
        ['key' => 'home', 'label' => 'Home & Living'],
    ];

    $activeFilter = request('filter', 'all');

    $featuredSellers = [
        [
            'name' => 'LIKHA ARTISANS',
            'slug' => 'likha-artisans',
            'location' => 'Cebu City, Cebu',
            'specialty' => 'Handwoven bags & home pieces',
            'rating' => 4.9,
            'products' => 48,
            'initials' => 'LA',
        ],
        [
            'name' => 'CORDILLERA WEAVES',
            'slug' => 'cordillera-weaves',
            'location' => 'Baguio City, Benguet',
            'specialty' => 'Traditional woven textiles',
            'rating' => 4.8,
            'products' => 31,
            'initials' => 'CW',
        ],
        [
            'name' => 'MINDANAO CRAFT HOUSE',
            'slug' => 'mindanao-craft-house',
            'location' => 'Davao City, Davao del Sur',
            'specialty' => 'Artisan accessories & décor',
            'rating' => 4.9,
            'products' => 27,
            'initials' => 'MC',
        ],
    ];

    $products = [
        [
            'id' => 1,
            'filter' => ['visayas', 'handmade', 'fashion'],
            'name' => 'Handwoven Rattan Tote Bag',
            'slug' => 'handwoven-rattan-tote-bag',
            'seller' => 'LIKHA ARTISANS',
            'seller_slug' => 'likha-artisans',
            'location' => 'Cebu City, Cebu',
            'price' => 899,
            'old_price' => 1099,
            'rating' => 4.7,
            'sold' => 391,
            'stock' => 12,
            'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 2,
            'filter' => ['visayas', 'handmade', 'home'],
            'name' => 'Capiz Shell Pendant Lamp',
            'slug' => 'capiz-shell-pendant-lamp',
            'seller' => 'CEBU CRAFTS CO.',
            'seller_slug' => 'cebu-crafts-co',
            'location' => 'Lapu-Lapu City, Cebu',
            'price' => 1899,
            'old_price' => 2299,
            'rating' => 4.6,
            'sold' => 218,
            'stock' => 8,
            'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 3,
            'filter' => ['luzon', 'handmade', 'fashion'],
            'name' => 'Cordillera Woven Shoulder Bag',
            'slug' => 'cordillera-woven-shoulder-bag',
            'seller' => 'CORDILLERA WEAVES',
            'seller_slug' => 'cordillera-weaves',
            'location' => 'Baguio City, Benguet',
            'price' => 749,
            'old_price' => 899,
            'rating' => 4.8,
            'sold' => 286,
            'stock' => 14,
            'image' => 'https://images.unsplash.com/photo-1559563458-527698bf5295?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 4,
            'filter' => ['luzon', 'food'],
            'name' => 'Premium Philippine Tablea Pack',
            'slug' => 'premium-philippine-tablea-pack',
            'seller' => 'CACAO MABUHAY',
            'seller_slug' => 'cacao-mabuhay',
            'location' => 'Batangas City, Batangas',
            'price' => 329,
            'old_price' => 399,
            'rating' => 4.9,
            'sold' => 724,
            'stock' => 35,
            'image' => 'https://images.unsplash.com/photo-1575377427642-087cf684f29d?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 5,
            'filter' => ['mindanao', 'handmade', 'home'],
            'name' => 'Handcrafted Beaded Wall Décor',
            'slug' => 'handcrafted-beaded-wall-decor',
            'seller' => 'MINDANAO CRAFT HOUSE',
            'seller_slug' => 'mindanao-craft-house',
            'location' => 'Davao City, Davao del Sur',
            'price' => 1299,
            'old_price' => 1499,
            'rating' => 4.9,
            'sold' => 163,
            'stock' => 9,
            'image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 6,
            'filter' => ['mindanao', 'food'],
            'name' => 'Davao Dark Chocolate Gift Box',
            'slug' => 'davao-dark-chocolate-gift-box',
            'seller' => 'CACAO SOUTH',
            'seller_slug' => 'cacao-south',
            'location' => 'Davao City, Davao del Sur',
            'price' => 549,
            'old_price' => 649,
            'rating' => 4.8,
            'sold' => 541,
            'stock' => 22,
            'image' => 'https://images.unsplash.com/photo-1575377427642-087cf684f29d?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 7,
            'filter' => ['luzon', 'home', 'handmade'],
            'name' => 'Hand-carved Acacia Serving Board',
            'slug' => 'hand-carved-acacia-serving-board',
            'seller' => 'KAHOY STUDIO',
            'seller_slug' => 'kahoy-studio',
            'location' => 'Antipolo City, Rizal',
            'price' => 679,
            'old_price' => 799,
            'rating' => 4.7,
            'sold' => 204,
            'stock' => 16,
            'image' => 'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'id' => 8,
            'filter' => ['visayas', 'food'],
            'name' => 'Cebu Dried Mango Gift Pack',
            'slug' => 'cebu-dried-mango-gift-pack',
            'seller' => 'SUGBO TREATS',
            'seller_slug' => 'sugbo-treats',
            'location' => 'Cebu City, Cebu',
            'price' => 289,
            'old_price' => 349,
            'rating' => 4.8,
            'sold' => 913,
            'stock' => 40,
            'image' => 'https://images.unsplash.com/photo-1605027990121-cbae9e0642df?auto=format&fit=crop&w=900&q=90',
        ],
    ];

    if ($activeFilter !== 'all') {
        $products = array_values(array_filter(
            $products,
            fn ($product) => in_array($activeFilter, $product['filter'])
        ));
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
    {{-- =====================================================
         HERO
    ====================================================== --}}
    <section class="local-hero">
        <div class="likhae-container py-12 sm:py-16">
            <div class="max-w-3xl">
                <p class="text-[10px] font-black uppercase tracking-[0.26em] text-[#d92d2f]">
                    PROUDLY FILIPINO
                </p>

                <h1 class="mt-3 text-4xl font-black tracking-[-0.05em] sm:text-5xl">
                    LOCAL FINDS
                </h1>

                <p class="mt-4 max-w-2xl text-sm leading-6 text-[#746d65]">
                    Discover products made, grown, and crafted across the Philippines — from regional specialties to independent artisan goods.
                </p>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="#local-products" class="primary-action">
                        Explore Local Products
                    </a>
                    <a href="#featured-sellers" class="secondary-action">
                        Meet Local Sellers
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Filters --}}
    <section class="border-y border-[#ded8d0] bg-white">
        <div class="likhae-container">
            <div class="flex overflow-x-auto hide-scrollbar">
                @foreach($filters as $filter)
                    <a
                        href="{{ url('/buyer/local-finds?filter=' . $filter['key']) }}"
                        class="local-filter {{ $activeFilter === $filter['key'] ? 'is-active' : '' }}"
                    >
                        {{ $filter['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Regional Highlights --}}
    <section class="py-10">
        <div class="likhae-container">
            <div class="mb-6">
                <p class="buyer-section-eyebrow">SHOP BY REGION</p>
                <h2 class="mt-3 text-2xl font-black tracking-[-0.03em]">Find Something From Home</h2>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <a href="{{ url('/buyer/local-finds?filter=luzon') }}" class="region-card">
                    <span class="region-number">01</span>
                    <div>
                        <p class="region-eyebrow">NORTH & CENTRAL</p>
                        <h3>Luzon</h3>
                        <p>Weaves, woodcraft, cacao, home goods, and more.</p>
                    </div>
                    <span class="region-arrow">→</span>
                </a>

                <a href="{{ url('/buyer/local-finds?filter=visayas') }}" class="region-card">
                    <span class="region-number">02</span>
                    <div>
                        <p class="region-eyebrow">ISLAND CRAFT</p>
                        <h3>Visayas</h3>
                        <p>Rattan, capiz, delicacies, and artisan accessories.</p>
                    </div>
                    <span class="region-arrow">→</span>
                </a>

                <a href="{{ url('/buyer/local-finds?filter=mindanao') }}" class="region-card">
                    <span class="region-number">03</span>
                    <div>
                        <p class="region-eyebrow">SOUTHERN FINDS</p>
                        <h3>Mindanao</h3>
                        <p>Cacao, beadwork, textiles, and handcrafted décor.</p>
                    </div>
                    <span class="region-arrow">→</span>
                </a>
            </div>
        </div>
    </section>

    {{-- Featured Sellers --}}
    <section id="featured-sellers" class="border-y border-[#ded8d0] bg-white py-10">
        <div class="likhae-container">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="buyer-section-eyebrow">FEATURED LOCAL SELLERS</p>
                    <h2 class="mt-3 text-2xl font-black tracking-[-0.03em]">Meet the Makers</h2>
                </div>

                <p class="max-w-md text-xs leading-5 text-[#918a82]">
                    Independent Filipino sellers bringing regional craft, food, fashion, and home products to LIKHAE.
                </p>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                @foreach($featuredSellers as $seller)
                    <article class="seller-card">
                        <div class="flex items-start gap-4">
                            <div class="seller-avatar">{{ $seller['initials'] }}</div>

                            <div class="min-w-0 flex-1">
                                <p class="seller-location">{{ $seller['location'] }}</p>

                                <a
                                    href="{{ url('/buyer/seller/' . $seller['slug']) }}"
                                    class="mt-1 block text-sm font-black transition hover:text-[#d92d2f]"
                                >
                                    {{ $seller['name'] }}
                                </a>

                                <p class="mt-2 text-[10px] leading-5 text-[#918a82]">
                                    {{ $seller['specialty'] }}
                                </p>

                                <div class="mt-3 flex flex-wrap items-center gap-3 text-[10px]">
                                    <span><span class="text-[#f2a000]">★</span> {{ number_format($seller['rating'], 1) }}</span>
                                    <span class="text-[#aaa39b]">{{ $seller['products'] }} products</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ url('/buyer/seller/' . $seller['slug']) }}" class="secondary-action mt-5 w-full">
                            Visit Shop
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Local Products --}}
    <section id="local-products" class="py-10 lg:py-12">
        <div class="likhae-container">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="buyer-section-eyebrow">DISCOVER LOCAL</p>
                    <h2 class="mt-3 text-2xl font-black tracking-[-0.03em]">Filipino-made Picks</h2>
                </div>

                <p class="text-xs text-[#918a82]">
                    {{ count($products) }} product{{ count($products) === 1 ? '' : 's' }}
                </p>
            </div>

            @if(count($products) > 0)
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($products as $product)
                        @php
                            $discount = $product['old_price'] > $product['price']
                                ? round((($product['old_price'] - $product['price']) / $product['old_price']) * 100)
                                : 0;
                        @endphp

                        <article class="product-card">
                            <div class="relative">
                                <a
                                    href="{{ url('/buyer/products/' . $product['slug']) }}"
                                    class="block aspect-[4/3] overflow-hidden bg-[#eee8e0]"
                                >
                                    <img
                                        src="{{ $product['image'] }}"
                                        alt="{{ $product['name'] }}"
                                        class="h-full w-full object-cover transition duration-500 hover:scale-105"
                                    >
                                </a>

                                <span class="local-badge">LOCAL</span>

                                @if($discount > 0)
                                    <span class="discount-badge">-{{ $discount }}%</span>
                                @endif

                                <form method="POST" action="{{ url('/buyer/wishlist') }}" class="absolute right-3 top-3">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">

                                    <button type="submit" class="wishlist-button" aria-label="Add to wishlist">♡</button>
                                </form>
                            </div>

                            <div class="p-4">
                                <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#d92d2f]">
                                    {{ $product['location'] }}
                                </p>

                                <a
                                    href="{{ url('/buyer/seller/' . $product['seller_slug']) }}"
                                    class="mt-2 block text-[9px] font-black uppercase tracking-[0.12em] text-[#8f8880] transition hover:text-[#d92d2f]"
                                >
                                    {{ $product['seller'] }}
                                </a>

                                <a
                                    href="{{ url('/buyer/products/' . $product['slug']) }}"
                                    class="mt-2 line-clamp-2 min-h-[44px] text-sm font-black leading-5 transition hover:text-[#d92d2f]"
                                >
                                    {{ $product['name'] }}
                                </a>

                                <div class="mt-3 flex items-center gap-2 text-[10px]">
                                    <span class="text-[#f2a000]">★</span>
                                    <span class="font-bold">{{ number_format($product['rating'], 1) }}</span>
                                    <span class="text-[#aaa39b]">· {{ $product['sold'] }} sold</span>
                                </div>

                                <div class="mt-4 flex flex-wrap items-end gap-2">
                                    <span class="text-xl font-black text-[#d92d2f]">
                                        ₱{{ number_format($product['price']) }}
                                    </span>

                                    @if($product['old_price'] > $product['price'])
                                        <span class="pb-0.5 text-[10px] text-[#aaa39b] line-through">
                                            ₱{{ number_format($product['old_price']) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    <span class="stock-badge">
                                        {{ $product['stock'] }} IN STOCK
                                    </span>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-2">
                                    <form method="POST" action="{{ url('/buyer/cart') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                        <input type="hidden" name="quantity" value="1">

                                        <button type="submit" class="product-action secondary w-full">
                                            Add to Cart
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ url('/buyer/checkout/buy-now') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                        <input type="hidden" name="quantity" value="1">

                                        <button type="submit" class="product-action primary w-full">
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
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#f5f2ed] text-2xl text-[#d92d2f]">
                        L
                    </div>

                    <h2 class="mt-5 text-xl font-black">No local finds here yet</h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#918a82]">
                        Try another region or category to discover more Filipino-made products.
                    </p>

                    <a href="{{ url('/buyer/local-finds') }}" class="primary-action mt-6">
                        View All Local Finds
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- Support Local CTA --}}
    <section class="support-local">
        <div class="likhae-container py-12 sm:py-14">
            <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-white/60">
                        SUPPORT LOCAL
                    </p>

                    <h2 class="mt-3 max-w-2xl text-3xl font-black tracking-[-0.04em] text-white">
                        Every local purchase helps a Filipino seller grow.
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm leading-6 text-white/60">
                        Discover independent shops, regional specialties, and proudly Filipino products across LIKHAE.
                    </p>
                </div>

                <a href="{{ url('/buyer/products') }}" class="support-action">
                    Browse Marketplace
                </a>
            </div>
        </div>
    </section>
</main>

<footer class="bg-[#0a0a0a] text-white">
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