@use('Illuminate\Support\Str')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LIKHAE — Filipino Marketplace</title>

    @vite(['resources/css/Guest/home.css', 'resources/js/app.js'])
</head>

<body class="bg-[#f5f2ed] text-[#111111] antialiased">

@php
    $categories = [
        ['name' => 'Electronics', 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=500&q=85'],
        ['name' => 'Fashion', 'image' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?auto=format&fit=crop&w=500&q=85'],
        ['name' => 'Home & Living', 'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=500&q=85'],
        ['name' => 'Beauty & Care', 'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=500&q=85'],
        ['name' => 'Food & Grocery', 'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=500&q=85'],
        ['name' => 'Sports', 'image' => 'https://images.unsplash.com/photo-1538805060514-97d9cc17730c?auto=format&fit=crop&w=500&q=85'],
        ['name' => 'Books', 'image' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?auto=format&fit=crop&w=500&q=85'],
        ['name' => 'Toys & Games', 'image' => 'https://images.unsplash.com/photo-1594736797933-d0501ba2fe65?auto=format&fit=crop&w=500&q=85'],
        ['name' => 'Health & Wellness', 'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=500&q=85'],
        ['name' => 'Automotive', 'image' => 'https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=500&q=85'],
    ];

    $flashDeals = [
        [
            'name' => 'Baseus Wireless Earbuds A3i Pro',
            'seller' => 'TECHHUB PH',
            'price' => 599,
            'old_price' => 1299,
            'discount' => 54,
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Samsung Galaxy A35 5G 256GB',
            'seller' => 'SAMSUNG OFFICIAL PH',
            'price' => 16999,
            'old_price' => 19999,
            'discount' => 15,
            'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Mechanical Keyboard TKL RGB',
            'seller' => 'KEYWORKS PH',
            'price' => 1799,
            'old_price' => 3499,
            'discount' => 49,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Premium Cotton Polo Shirt',
            'seller' => 'STYLE MANILA',
            'price' => 249,
            'old_price' => 599,
            'discount' => 58,
            'image' => 'https://images.unsplash.com/photo-1603252109303-2751441dd157?auto=format&fit=crop&w=900&q=90',
        ],
    ];

    $products = [
        [
            'name' => 'Baseus Wireless Earbuds A3i Pro',
            'seller' => 'TECHHUB PH',
            'price' => 599,
            'old_price' => 1299,
            'discount' => 54,
            'rating' => 4.7,
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=90',
            'badge' => null,
        ],
        [
            'name' => 'Lenovo IdeaPad Slim 5i 14" Laptop',
            'seller' => 'TECH REPUBLIC PH',
            'price' => 34999,
            'old_price' => 42999,
            'discount' => 19,
            'rating' => 4.7,
            'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=90',
            'badge' => null,
        ],
        [
            'name' => 'Samsung Galaxy A35 5G 256GB',
            'seller' => 'SAMSUNG OFFICIAL PH',
            'price' => 16999,
            'old_price' => 19999,
            'discount' => 15,
            'rating' => 4.6,
            'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=90',
            'badge' => null,
        ],
        [
            'name' => 'Premium Cotton Polo Shirt',
            'seller' => 'STYLE MANILA',
            'price' => 249,
            'old_price' => 599,
            'discount' => 58,
            'rating' => 4.5,
            'image' => 'https://images.unsplash.com/photo-1603252109303-2751441dd157?auto=format&fit=crop&w=900&q=90',
            'badge' => null,
        ],
        [
            'name' => 'Floral Midi Sundress Summer',
            'seller' => 'STYLE MANILA',
            'price' => 449,
            'old_price' => 899,
            'discount' => 50,
            'rating' => 4.5,
            'image' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=900&q=90',
            'badge' => null,
        ],
        [
            'name' => 'Running Shoes Ultraboost Lite',
            'seller' => 'SPORTZONE PH',
            'price' => 1299,
            'old_price' => 2499,
            'discount' => 48,
            'rating' => 4.4,
            'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=90',
            'badge' => null,
        ],
        [
            'name' => 'Handwoven Rattan Tote Bag',
            'seller' => 'LIKHA ARTISANS',
            'price' => 899,
            'old_price' => null,
            'discount' => null,
            'rating' => 4.8,
            'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=90',
            'badge' => 'LOCAL',
        ],
        [
            'name' => 'Cosmos Digital Rice Cooker 1.8L',
            'seller' => 'HOME ESSENTIALS PH',
            'price' => 899,
            'old_price' => 1899,
            'discount' => 53,
            'rating' => 4.8,
            'image' => 'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=900&q=90',
            'badge' => null,
        ],
        [
            'name' => 'Air Purifier HEPA H13 True Filter',
            'seller' => 'HOME ESSENTIALS PH',
            'price' => 1849,
            'old_price' => 2499,
            'discount' => 26,
            'rating' => 4.8,
            'image' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=900&q=90',
            'badge' => null,
        ],
        [
            'name' => 'Vitamin C Brightening Serum Set',
            'seller' => 'GLOW SKINCARE PH',
            'price' => 799,
            'old_price' => 1799,
            'discount' => 56,
            'rating' => 4.7,
            'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=90',
            'badge' => null,
        ],
        [
            'name' => 'Capiz Shell Pendant Lamp',
            'seller' => 'CEBU CRAFTS CO.',
            'price' => 1899,
            'old_price' => 2499,
            'discount' => 24,
            'rating' => 4.9,
            'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=90',
            'badge' => 'LOCAL',
        ],
        [
            'name' => 'Pineapple Fiber Barong Tagalog',
            'seller' => 'BARONG REPUBLIC',
            'price' => 2899,
            'old_price' => null,
            'discount' => null,
            'rating' => 4.9,
            'image' => 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?auto=format&fit=crop&w=900&q=90',
            'badge' => 'LOCAL',
        ],
    ];

    $localFinds = [
        [
            'name' => 'Handwoven Rattan Tote Bag',
            'location' => 'Cebu City, Cebu',
            'price' => 899,
            'old_price' => null,
            'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Burnay Handmade Clay Pot Set',
            'location' => 'Vigan City, Ilocos Sur',
            'price' => 1299,
            'old_price' => null,
            'image' => 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Capiz Shell Pendant Lamp',
            'location' => 'Cebu',
            'price' => 1899,
            'old_price' => 2499,
            'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Pineapple Fiber Barong Tagalog',
            'location' => 'Metro Manila',
            'price' => 2899,
            'old_price' => null,
            'image' => 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Abstract Canvas Wall Art Print',
            'location' => 'Angono, Rizal',
            'price' => 399,
            'old_price' => null,
            'image' => 'https://images.unsplash.com/photo-1549490349-8643362247b5?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Artisan Filipino Food Basket',
            'location' => 'Bacolod City',
            'price' => 749,
            'old_price' => 999,
            'image' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=900&q=90',
        ],
    ];

    $testimonials = [
        [
            'initial' => 'M',
            'name' => 'Maria Santos',
            'location' => 'Quezon City',
            'quote' => '"I love how easy it is to find local Filipino products. Bought a burnay clay pot from Vigan and it arrived perfectly packed. Sulit na sulit!"',
        ],
        [
            'initial' => 'J',
            'name' => 'Joven Reyes',
            'location' => 'Cebu City',
            'quote' => '"Flash deals are insane. Got wireless earbuds for ₱599 — same quality as ₱3,000 ones I see at the mall. LIKHAE has changed how I shop."',
        ],
        [
            'initial' => 'T',
            'name' => 'Tricia Lim',
            'location' => 'BGC, Taguig',
            'quote' => '"Seller communication is so good. My barong arrived tailored perfectly. The craftsmanship of Filipino artisans here is truly world-class."',
        ],
    ];
@endphp

{{-- =========================================================
     HEADER
========================================================= --}}
<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container">
        <div class="flex h-[64px] items-center gap-4">
            <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white shadow-sm">L</span>
                <span class="text-xl font-black tracking-tight">LIKHAE</span>
            </a>

            <form action="{{ url('/search') }}" method="GET" class="hidden min-w-0 flex-1 md:flex">
                <div class="flex h-11 w-full overflow-hidden border border-[#dedad3] bg-white">
                    <select
                        name="category"
                        class="w-[86px] border-r border-[#dedad3] bg-white px-3 text-sm text-[#5d5a55] outline-none"
                    >
                        <option value="">All</option>
                        @foreach($categories as $category)
                            <option value="{{ Str::slug($category['name']) }}">
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>

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

            <nav class="ml-auto flex items-center gap-1 sm:gap-3">
                <a href="{{ url('/notifications') }}" class="header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                            <path d="M10 21h4"></path>
                        </svg>
                        <span class="absolute -right-1 -top-1 h-2 w-2 rounded-full bg-[#d92d2f]"></span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Alerts</span>
                </a>

                <a href="{{ url('/login') }}" class="header-action">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <circle cx="12" cy="8" r="3.2"></circle>
                        <path d="M5.5 20c.7-4.2 3-6.3 6.5-6.3s5.8 2.1 6.5 6.3"></path>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Account</span>
                </a>

                <a href="{{ url('/wishlist') }}" class="header-action">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 5.9l-1.3-1.3a5.3 5.3 0 1 0-7.5 7.5L12 21l8.8-8.9a5.3 5.3 0 0 0 0-7.5Z"></path>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Wishlist</span>
                </a>

                <a href="{{ url('/cart') }}" class="header-action">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6"></path>
                        <circle cx="9" cy="20" r="1"></circle>
                        <circle cx="17" cy="20" r="1"></circle>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Cart</span>
                </a>
            </nav>
        </div>

        <form action="{{ url('/search') }}" method="GET" class="pb-3 md:hidden">
            <div class="flex h-10 overflow-hidden border border-[#dedad3] bg-white">
                <input
                    type="search"
                    name="q"
                    placeholder="Search LIKHAE..."
                    class="min-w-0 flex-1 px-3 text-sm outline-none"
                >
                <button class="w-12 bg-[#d92d2f] text-white" aria-label="Search">
                    <svg class="mx-auto h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <div class="border-t border-[#ece8e1]">
        <div class="likhae-container flex h-[40px] items-center gap-7 overflow-x-auto whitespace-nowrap text-xs text-[#4e4a45] hide-scrollbar">
            @foreach($categories as $category)
                <a
                    href="{{ url('/category/' . Str::slug($category['name'])) }}"
                    class="transition hover:text-[#d92d2f]"
                >
                    {{ $category['name'] }}
                </a>
            @endforeach

            <a
                href="#flash-deals"
                class="ml-auto border-b border-[#d92d2f] px-4 py-[13px] font-bold text-[#d92d2f]"
            >
                ⚡ Flash Deals
            </a>
        </div>
    </div>
</header>

<main>
    {{-- =====================================================
         HERO
    ====================================================== --}}
    <section class="relative overflow-hidden bg-[#0b0b0b] text-white">
        <div class="hero-grid-pattern absolute inset-0 opacity-40"></div>
        <div class="likhae-container relative py-12 lg:py-16">
            <div class="grid items-center gap-8 lg:grid-cols-[1.2fr_.8fr]">
                <div class="max-w-2xl">
                    <p class="section-eyebrow text-white/65 before:bg-[#d92d2f]">YOUR FILIPINO MARKETPLACE</p>

                    <h1 class="mt-4 text-4xl font-black leading-[1.02] tracking-[-0.045em] sm:text-5xl lg:text-6xl">
                        Shop More. Discover More.
                        <span class="block text-[#e43a3c]">Live More.</span>
                    </h1>

                    <p class="mt-5 max-w-xl text-sm leading-7 text-white/55 sm:text-base">
                        From everyday essentials to proudly local Filipino finds —
                        discover products you love and support sellers from all over the Philippines.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="#featured" class="btn-primary">
                            Shop Now
                            <span aria-hidden="true">→</span>
                        </a>
                        <a href="#local-finds" class="btn-dark-outline">
                            Local Finds
                        </a>
                    </div>

                    <div class="mt-10 grid max-w-2xl grid-cols-2 border-t border-white/10 pt-7 sm:grid-cols-4">
                        <div class="stat-item">
                            <strong>50K+</strong>
                            <span>Sellers</span>
                        </div>
                        <div class="stat-item">
                            <strong>2M+</strong>
                            <span>Products</span>
                        </div>
                        <div class="stat-item">
                            <strong>1.5M</strong>
                            <span>Buyers</span>
                        </div>
                        <div class="stat-item border-r-0">
                            <strong>81</strong>
                            <span>Provinces</span>
                        </div>
                    </div>
                </div>

                <div class="hidden lg:grid lg:grid-cols-2 lg:gap-3">
                    <a href="#" class="hero-product-card row-span-2 min-h-[340px]">
                        <img
                            src="{{ $flashDeals[0]['image'] }}"
                            alt="{{ $flashDeals[0]['name'] }}"
                            class="absolute inset-0 h-full w-full object-cover"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/15 to-transparent"></div>
                        <div class="relative mt-auto p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-white/60">
                                {{ $flashDeals[0]['seller'] }}
                            </p>
                            <h3 class="mt-1 text-lg font-bold">{{ $flashDeals[0]['name'] }}</h3>
                            <p class="mt-2 text-xl font-black text-[#ff4245]">
                                ₱{{ number_format($flashDeals[0]['price']) }}
                            </p>
                        </div>
                    </a>

                    <a href="#" class="hero-product-card min-h-[164px]">
                        <img
                            src="{{ $flashDeals[1]['image'] }}"
                            alt="{{ $flashDeals[1]['name'] }}"
                            class="absolute inset-0 h-full w-full object-cover"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-black/10"></div>
                        <div class="relative mt-auto p-4">
                            <h3 class="line-clamp-1 text-sm font-bold">{{ $flashDeals[1]['name'] }}</h3>
                            <p class="mt-1 font-black text-[#ff4245]">₱{{ number_format($flashDeals[1]['price']) }}</p>
                        </div>
                    </a>

                    <a href="#" class="hero-product-card min-h-[164px]">
                        <img
                            src="{{ $flashDeals[3]['image'] }}"
                            alt="{{ $flashDeals[3]['name'] }}"
                            class="absolute inset-0 h-full w-full object-cover"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-black/10"></div>
                        <div class="relative mt-auto p-4">
                            <h3 class="line-clamp-1 text-sm font-bold">{{ $flashDeals[3]['name'] }}</h3>
                            <p class="mt-1 font-black text-[#ff4245]">₱{{ number_format($flashDeals[3]['price']) }}</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Flash ticker --}}
    <div class="overflow-hidden bg-[#d92d2f] text-white">
        <div class="likhae-ticker flex h-[52px] items-center gap-9 whitespace-nowrap text-xs font-bold">
            @foreach(array_merge($flashDeals, $flashDeals) as $deal)
                <span class="flex items-center gap-4">
                    <span>⚡</span>
                    <span>{{ $deal['name'] }}</span>
                    <span class="text-white/80">₱{{ number_format($deal['price']) }}</span>
                    <span class="text-white/30">•</span>
                </span>
            @endforeach
        </div>
    </div>

    {{-- =====================================================
         CATEGORIES
    ====================================================== --}}
    <section class="section-space bg-[#f5f2ed]">
        <div class="likhae-container">
            <div class="section-heading">
                <div>
                    <p class="section-eyebrow">BROWSE</p>
                    <h2>Shop by Category</h2>
                </div>
                <a href="{{ url('/categories') }}" class="section-link">All Categories →</a>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-10">
                @foreach($categories as $category)
                    <a
                        href="{{ url('/category/' . Str::slug($category['name'])) }}"
                        class="group text-center"
                    >
                        <div class="aspect-square overflow-hidden bg-white">
                            <img
                                src="{{ $category['image'] }}"
                                alt="{{ $category['name'] }}"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            >
                        </div>
                        <p class="mt-3 text-xs font-medium text-[#2c2926] group-hover:text-[#d92d2f]">
                            {{ $category['name'] }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         FLASH DEALS
    ====================================================== --}}
    <section id="flash-deals" class="section-space bg-white">
        <div class="likhae-container">
            <div class="section-heading">
                <div>
                    <p class="section-eyebrow">LIMITED TIME</p>
                    <div class="mt-2 flex flex-wrap items-end gap-5">
                        <h2 class="!mt-0">⚡ Flash Deals</h2>

                        <div class="flex items-end gap-2 pb-1">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-[#77716a]">Ends in</span>
                            <div class="flex gap-1">
                                <span class="countdown-box">04</span>
                                <span class="font-bold">:</span>
                                <span class="countdown-box">20</span>
                                <span class="font-bold">:</span>
                                <span class="countdown-box">48</span>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ url('/flash-deals') }}" class="section-link">See all →</a>
            </div>

            <div class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($flashDeals as $deal)
                    <a href="#" class="flash-card group">
                        <img
                            src="{{ $deal['image'] }}"
                            alt="{{ $deal['name'] }}"
                            class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                        >

                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/25 to-black/5"></div>

                        <div class="absolute left-4 top-4 bg-[#e3262b] px-3 py-2 text-[11px] font-black text-white">
                            -{{ $deal['discount'] }}% OFF
                        </div>

                        <div class="relative mt-auto p-5 text-white">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-white/55">
                                {{ $deal['seller'] }}
                            </p>
                            <h3 class="mt-1 line-clamp-2 text-sm font-bold sm:text-base">
                                {{ $deal['name'] }}
                            </h3>

                            <div class="mt-3 flex items-end gap-2">
                                <span class="text-2xl font-black">
                                    ₱{{ number_format($deal['price']) }}
                                </span>
                                <span class="pb-1 text-xs text-white/40 line-through">
                                    ₱{{ number_format($deal['old_price']) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         FEATURED PRODUCTS
    ====================================================== --}}
    <section id="featured" class="section-space bg-[#f5f2ed]">
        <div class="likhae-container">
            <div class="section-heading">
                <div>
                    <p class="section-eyebrow">HANDPICKED</p>
                    <h2>Featured Products</h2>
                </div>

                <a href="{{ url('/products') }}" class="section-link">View all →</a>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                @foreach($products as $product)
                    <article class="product-card group">
                        <a href="#" class="relative block aspect-[4/4.4] overflow-hidden bg-[#ebe7e1]">
                            <img
                                src="{{ $product['image'] }}"
                                alt="{{ $product['name'] }}"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            >

                            @if($product['discount'])
                                <span class="absolute left-3 top-3 bg-[#e3262b] px-2 py-1 text-[10px] font-black text-white">
                                    -{{ $product['discount'] }}%
                                </span>
                            @endif

                            @if($product['badge'])
                                <span class="absolute left-3 top-3 bg-[#00a875] px-2 py-1 text-[10px] font-black text-white">
                                    {{ $product['badge'] }}
                                </span>
                            @endif
                        </a>

                        <div class="p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#a59d93]">
                                {{ $product['seller'] }}
                            </p>

                            <a href="#" class="mt-2 block min-h-[40px] text-sm font-medium leading-5 hover:text-[#d92d2f]">
                                {{ $product['name'] }}
                            </a>

                            <div class="mt-4 border-t border-[#e9e2da] pt-4">
                                <div class="flex items-end justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-lg font-black text-[#d92d2f]">
                                                ₱{{ number_format($product['price']) }}
                                            </span>

                                            @if($product['old_price'])
                                                <span class="text-[11px] text-[#b0aaa3] line-through">
                                                    ₱{{ number_format($product['old_price']) }}
                                                </span>
                                            @endif
                                        </div>

                                        <p class="mt-2 text-[10px] font-semibold text-[#009d71]">
                                            ♧ Free Shipping
                                        </p>
                                    </div>

                                    <div class="shrink-0 pb-1 text-[11px] text-[#6e6962]">
                                        <span class="text-[#f4a100]">★</span>
                                        {{ number_format($product['rating'], 1) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         LOCAL FINDS
    ====================================================== --}}
    <section id="local-finds" class="section-space bg-[#d92d2f] text-white">
        <div class="likhae-container">
            <div class="section-heading text-white">
                <div>
                    <p class="section-eyebrow text-white/80 before:bg-white">PROUDLY PHILIPPINE-MADE</p>
                    <h2 class="text-white">Local Filipino Finds</h2>
                    <p class="mt-1 text-sm text-white/80">
                        From Ilocos to Cebu — support your kababayans
                    </p>
                </div>

                <a href="{{ url('/local-finds') }}" class="border border-white/30 px-5 py-3 text-xs font-bold text-white transition hover:bg-white hover:text-[#d92d2f]">
                    Explore →
                </a>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                @foreach($localFinds as $item)
                    <article class="group overflow-hidden bg-white text-[#111]">
                        <a href="#" class="block aspect-square overflow-hidden">
                            <img
                                src="{{ $item['image'] }}"
                                alt="{{ $item['name'] }}"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            >
                        </a>

                        <div class="p-4">
                            <p class="text-[10px] text-[#a59187]">⌖ {{ $item['location'] }}</p>
                            <a href="#" class="mt-2 block min-h-[40px] text-sm font-medium leading-5 hover:text-[#d92d2f]">
                                {{ $item['name'] }}
                            </a>

                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-lg font-black text-[#d92d2f]">
                                    ₱{{ number_format($item['price']) }}
                                </span>

                                @if($item['old_price'])
                                    <span class="text-[11px] text-[#aaa39a] line-through">
                                        ₱{{ number_format($item['old_price']) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         TESTIMONIALS
    ====================================================== --}}
    <section class="section-space bg-white">
        <div class="likhae-container">
            <p class="section-eyebrow">WHAT THEY SAY</p>
            <h2 class="mt-3 text-3xl font-black tracking-tight">Loved by Filipinos</h2>

            <div class="mt-9 grid gap-4 md:grid-cols-3">
                @foreach($testimonials as $testimonial)
                    <article class="bg-[#f5f2ed] p-7">
                        <div class="text-[#f4a100]">★★★★★</div>

                        <p class="mt-4 text-sm leading-7 text-[#514d47]">
                            {{ $testimonial['quote'] }}
                        </p>

                        <div class="mt-5 flex items-center gap-3 border-t border-[#ddd7cf] pt-4">
                            <div class="grid h-10 w-10 shrink-0 place-items-center bg-[#d92d2f] font-black text-white">
                                {{ $testimonial['initial'] }}
                            </div>
                            <div>
                                <p class="text-sm font-bold">{{ $testimonial['name'] }}</p>
                                <p class="text-[11px] text-[#9c958d]">{{ $testimonial['location'] }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         PROMO STRIP
    ====================================================== --}}
    <section class="bg-[#f5f2ed] py-20">
        <div class="likhae-container grid gap-4 md:grid-cols-3">
            <div class="promo-card bg-[#0d0d0d] text-white">
                <div>
                    <p class="font-bold">Download the App</p>
                    <p class="mt-1 text-xs leading-5 text-white/60">App-only coupons & push-deal alerts</p>
                </div>
                <a href="#" class="promo-button border-white/20">Get it Free</a>
            </div>

            <div class="promo-card bg-[#d92d2f] text-white">
                <div>
                    <p class="font-bold">Sell on LIKHAE</p>
                    <p class="mt-1 text-xs leading-5 text-white/70">Reach 1.5 million buyers nationwide</p>
                </div>
                <a href="{{ url('/seller/register') }}" class="promo-button border-white/25">Start Selling</a>
            </div>

            <div class="promo-card bg-[#079b72] text-white">
                <div>
                    <p class="font-bold">LIKHAE Plus</p>
                    <p class="mt-1 text-xs leading-5 text-white/70">Free shipping on every single order</p>
                </div>
                <a href="#" class="promo-button border-white/25">₱99/month</a>
            </div>
        </div>
    </section>
</main>

{{-- =========================================================
     FOOTER
========================================================= --}}
<footer class="bg-[#0a0a0a] text-white">
    <div class="likhae-container py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.5fr_repeat(4,1fr)]">
            <div>
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black">L</span>
                    <span class="text-xl font-black">LIKHAE</span>
                </a>

                <p class="mt-4 max-w-[230px] text-sm leading-6 text-white/35">
                    Shop More. Discover More. Live More. — Your Philippine marketplace.
                </p>

                <div class="mt-6 flex gap-2">
                    @foreach(['F', 'IG', 'X', 'YT'] as $social)
                        <a href="#" class="grid h-9 w-9 place-items-center border border-white/10 text-[10px] text-white/45 hover:border-white/30 hover:text-white">
                            {{ $social }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="footer-title">SHOP</h3>
                <div class="footer-links">
                    <a href="#">Electronics</a>
                    <a href="#">Fashion</a>
                    <a href="#">Home & Living</a>
                    <a href="#">Beauty & Care</a>
                    <a href="#local-finds">Local Finds</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">SELL</h3>
                <div class="footer-links">
                    <a href="{{ url('/seller/register') }}">Start Selling</a>
                    <a href="#">Seller Center</a>
                    <a href="#">Advertising</a>
                    <a href="#">LIKHAE Academy</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">SUPPORT</h3>
                <div class="footer-links">
                    <a href="#">Help Center</a>
                    <a href="#">Track Order</a>
                    <a href="#">Returns</a>
                    <a href="#">Buyer Protection</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">COMPANY</h3>
                <div class="footer-links">
                    <a href="#">About LIKHAE</a>
                    <a href="#">Careers</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms</a>
                </div>
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-4 border-t border-white/10 pt-7 text-[10px] text-white/25 md:flex-row md:items-center md:justify-between">
            <p>© {{ date('Y') }} LIKHAE, Inc. — Made with pride in the Philippines.</p>
            <p>Accepted: GCASH · MAYA · VISA · MASTERCARD · COD · BPI</p>
        </div>
    </div>
</footer>

<a
    href="#"
    class="fixed bottom-5 right-5 z-40 grid h-10 w-10 place-items-center rounded-full border border-white/15 bg-[#171717] text-sm font-bold text-white shadow-xl"
    aria-label="Help"
>
    ?
</a>

</body>
</html>