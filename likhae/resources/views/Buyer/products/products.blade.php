<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Shop Products — LIKHAE</title>

    @vite([
        'resources/css/Buyer/products.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo data
    |--------------------------------------------------------------------------
    | Replace these arrays with controller/database data later.
    |
    | Example:
    | return view('Guest.products', compact('products', 'categories'));
    */

    $categories = [
        ['name' => 'Electronics', 'count' => 342],
        ['name' => 'Fashion', 'count' => 286],
        ['name' => 'Home & Living', 'count' => 194],
        ['name' => 'Beauty & Care', 'count' => 151],
        ['name' => 'Food & Grocery', 'count' => 122],
        ['name' => 'Sports', 'count' => 88],
        ['name' => 'Books', 'count' => 74],
        ['name' => 'Toys & Games', 'count' => 69],
    ];

    $products = [
        [
            'name' => 'Baseus Wireless Earbuds A3i Pro',
            'seller' => 'TECHHUB PH',
            'price' => 599,
            'old_price' => 1299,
            'discount' => 54,
            'rating' => 4.7,
            'sold' => '2.1K',
            'location' => 'Metro Manila',
            'free_shipping' => true,
            'local' => false,
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Lenovo IdeaPad Slim 5i 14" Laptop',
            'seller' => 'TECH REPUBLIC PH',
            'price' => 34999,
            'old_price' => 42999,
            'discount' => 19,
            'rating' => 4.7,
            'sold' => '436',
            'location' => 'Quezon City',
            'free_shipping' => true,
            'local' => false,
            'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Samsung Galaxy A35 5G 256GB',
            'seller' => 'SAMSUNG OFFICIAL PH',
            'price' => 16999,
            'old_price' => 19999,
            'discount' => 15,
            'rating' => 4.6,
            'sold' => '1.8K',
            'location' => 'Taguig City',
            'free_shipping' => true,
            'local' => false,
            'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Premium Cotton Polo Shirt',
            'seller' => 'STYLE MANILA',
            'price' => 249,
            'old_price' => 599,
            'discount' => 58,
            'rating' => 4.5,
            'sold' => '5.6K',
            'location' => 'Manila',
            'free_shipping' => false,
            'local' => true,
            'image' => 'https://images.unsplash.com/photo-1603252109303-2751441dd157?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Floral Midi Sundress Summer',
            'seller' => 'STYLE MANILA',
            'price' => 449,
            'old_price' => 899,
            'discount' => 50,
            'rating' => 4.5,
            'sold' => '3.2K',
            'location' => 'Manila',
            'free_shipping' => true,
            'local' => true,
            'image' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Running Shoes Ultraboost Lite',
            'seller' => 'SPORTZONE PH',
            'price' => 1299,
            'old_price' => 2499,
            'discount' => 48,
            'rating' => 4.4,
            'sold' => '948',
            'location' => 'Pasig City',
            'free_shipping' => true,
            'local' => false,
            'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Handwoven Rattan Tote Bag',
            'seller' => 'LIKHA ARTISANS',
            'price' => 899,
            'old_price' => null,
            'discount' => null,
            'rating' => 4.8,
            'sold' => '621',
            'location' => 'Cebu City',
            'free_shipping' => true,
            'local' => true,
            'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Cosmos Digital Rice Cooker 1.8L',
            'seller' => 'HOME ESSENTIALS PH',
            'price' => 899,
            'old_price' => 1899,
            'discount' => 53,
            'rating' => 4.8,
            'sold' => '1.2K',
            'location' => 'Makati City',
            'free_shipping' => true,
            'local' => false,
            'image' => 'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Air Purifier HEPA H13 True Filter',
            'seller' => 'HOME ESSENTIALS PH',
            'price' => 1849,
            'old_price' => 2499,
            'discount' => 26,
            'rating' => 4.8,
            'sold' => '502',
            'location' => 'Makati City',
            'free_shipping' => true,
            'local' => false,
            'image' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Vitamin C Brightening Serum Set',
            'seller' => 'GLOW SKINCARE PH',
            'price' => 799,
            'old_price' => 1799,
            'discount' => 56,
            'rating' => 4.7,
            'sold' => '4.4K',
            'location' => 'Quezon City',
            'free_shipping' => true,
            'local' => false,
            'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Capiz Shell Pendant Lamp',
            'seller' => 'CEBU CRAFTS CO.',
            'price' => 1899,
            'old_price' => 2499,
            'discount' => 24,
            'rating' => 4.9,
            'sold' => '387',
            'location' => 'Cebu',
            'free_shipping' => false,
            'local' => true,
            'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Pineapple Fiber Barong Tagalog',
            'seller' => 'BARONG REPUBLIC',
            'price' => 2899,
            'old_price' => null,
            'discount' => null,
            'rating' => 4.9,
            'sold' => '713',
            'location' => 'Metro Manila',
            'free_shipping' => true,
            'local' => true,
            'image' => 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?auto=format&fit=crop&w=900&q=90',
        ],
    ];
@endphp

{{-- =========================================================
     HEADER
========================================================= --}}
<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="mx-auto w-full max-w-[1280px] px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center gap-4">

            {{-- Brand --}}
            <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">
                    L
                </span>
                <span class="text-xl font-black tracking-tight">LIKHAE</span>
            </a>

            {{-- Search --}}
            <form
                action="{{ url('/buyer/products') }}"
                method="GET"
                class="hidden min-w-0 flex-1 md:flex"
            >
                <div class="flex h-11 w-full overflow-hidden border border-[#dedad3] bg-white">
                    <select
                        name="category"
                        class="w-[105px] border-r border-[#dedad3] bg-white px-3 text-xs text-[#5d5a55] outline-none"
                    >
                        <option value="">All</option>

                        @foreach($categories as $category)
                            <option value="{{ \Illuminate\Support\Str::slug($category['name']) }}">
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>

                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search products, brands, Filipino finds..."
                        class="min-w-0 flex-1 bg-transparent px-4 text-sm outline-none placeholder:text-[#b9b4ad]"
                    >

                    <button
                        type="submit"
                        class="flex w-[108px] items-center justify-center gap-2 bg-[#d92d2f] px-4 text-sm font-bold text-white transition hover:bg-[#bd2024]"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                        >
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>

                        Search
                    </button>
                </div>
            </form>

            {{-- Actions --}}
            <nav class="ml-auto flex items-center gap-1 sm:gap-3">
                <a href="{{ url('/buyer/account') }}" class="flex min-w-10 flex-col items-center gap-1 px-1 text-[#57534e] transition hover:text-[#d92d2f]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <circle cx="12" cy="8" r="3.2"></circle>
                        <path d="M5.5 20c.7-4.2 3-6.3 6.5-6.3s5.8 2.1 6.5 6.3"></path>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Account</span>
                </a>

                <a href="{{ url('/buyer/wishlist') }}" class="flex min-w-10 flex-col items-center gap-1 px-1 text-[#57534e] transition hover:text-[#d92d2f]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 5.9l-1.3-1.3a5.3 5.3 0 1 0-7.5 7.5L12 21l8.8-8.9a5.3 5.3 0 0 0 0-7.5Z"></path>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Wishlist</span>
                </a>

                <a href="{{ url('/buyer/cart') }}" class="flex min-w-10 flex-col items-center gap-1 px-1 text-[#57534e] transition hover:text-[#d92d2f]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6"></path>
                        <circle cx="9" cy="20" r="1"></circle>
                        <circle cx="17" cy="20" r="1"></circle>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Cart</span>
                </a>
            </nav>
        </div>

        {{-- Mobile Search --}}
        <form action="{{ url('/buyer/products') }}" method="GET" class="pb-3 md:hidden">
            <div class="flex h-10 overflow-hidden border border-[#dedad3] bg-white">
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search LIKHAE..."
                    class="min-w-0 flex-1 px-3 text-sm outline-none"
                >
                <button
                    type="submit"
                    class="w-12 bg-[#d92d2f] text-white"
                    aria-label="Search"
                >
                    <svg class="mx-auto h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    {{-- Category Navigation --}}
    <div class="border-t border-[#ece8e1]">
        <div class="mx-auto flex h-10 w-full max-w-[1280px] items-center gap-7 overflow-x-auto whitespace-nowrap px-4 text-xs text-[#4e4a45] sm:px-6 lg:px-8">
            @foreach($categories as $category)
                <a
                    href="{{ url('/buyer/products?category=' . \Illuminate\Support\Str::slug($category['name'])) }}"
                    class="transition hover:text-[#d92d2f]"
                >
                    {{ $category['name'] }}
                </a>
            @endforeach

            <a
                href="{{ url('/flash-deals') }}"
                class="ml-auto border-b border-[#d92d2f] px-4 py-[13px] font-bold text-[#d92d2f]"
            >
                ⚡ Flash Deals
            </a>
        </div>
    </div>
</header>

<main>
    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}
    <section class="border-b border-black/5 bg-white">
        <div class="mx-auto w-full max-w-[1280px] px-4 py-8 sm:px-6 lg:px-8">
            <div class="text-[11px] text-[#9b958d]">
                <a href="{{ url('/buyer/home') }}" class="transition hover:text-[#d92d2f]">Home</a>
                <span class="mx-2">/</span>
                <span class="text-[#4d4944]">All Products</span>
            </div>

            <div class="mt-5 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.24em] text-[#716b64]">
                        <span class="block h-4 w-[2px] bg-[#d92d2f]"></span>
                        DISCOVER
                    </p>

                    <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">
                        All Products
                    </h1>

                    <p class="mt-2 max-w-xl text-sm leading-6 text-[#77716a]">
                        Discover everyday essentials, trending finds, and proudly Filipino-made products from sellers nationwide.
                    </p>
                </div>

                <div class="text-sm text-[#77716a]">
                    <span class="font-bold text-[#111]">1,248</span>
                    products found
                </div>
            </div>
        </div>
    </section>

    {{-- =====================================================
         MOBILE FILTER BAR
    ====================================================== --}}
    <div class="border-b border-[#dfd9d2] bg-[#f5f2ed] lg:hidden">
        <div class="mx-auto flex w-full max-w-[1280px] gap-2 px-4 py-3 sm:px-6">
            <button
                type="button"
                onclick="document.getElementById('mobileFilters').classList.toggle('hidden')"
                class="flex flex-1 items-center justify-center gap-2 border border-[#d8d2ca] bg-white px-4 py-3 text-xs font-bold"
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path d="M4 6h16M7 12h10M10 18h4"></path>
                </svg>
                Filters
            </button>

            <select
                name="sort_mobile"
                class="flex-1 border border-[#d8d2ca] bg-white px-4 py-3 text-xs font-bold outline-none"
            >
                <option>Most Popular</option>
                <option>Newest</option>
                <option>Price: Low to High</option>
                <option>Price: High to Low</option>
                <option>Top Rated</option>
            </select>
        </div>
    </div>

    {{-- =====================================================
         MOBILE FILTER PANEL
    ====================================================== --}}
    <div id="mobileFilters" class="hidden border-b border-[#ded8d0] bg-white lg:hidden">
        <form action="{{ url('/buyer/products') }}" method="GET" class="mx-auto w-full max-w-[1280px] px-4 py-6 sm:px-6">
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-[0.16em]">Category</h3>

                    <div class="mt-4 space-y-3">
                        @foreach(array_slice($categories, 0, 5) as $category)
                            <label class="flex items-center justify-between gap-3 text-sm text-[#5e5953]">
                                <span class="flex items-center gap-3">
                                    <input
                                        type="checkbox"
                                        name="categories[]"
                                        value="{{ \Illuminate\Support\Str::slug($category['name']) }}"
                                        class="h-4 w-4 accent-[#d92d2f]"
                                    >
                                    {{ $category['name'] }}
                                </span>

                                <span class="text-xs text-[#aaa39b]">{{ $category['count'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-black uppercase tracking-[0.16em]">Price Range</h3>

                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <input
                            type="number"
                            name="min_price"
                            placeholder="Min ₱"
                            class="w-full border border-[#ddd6ce] bg-[#faf9f7] px-3 py-3 text-sm outline-none focus:border-[#d92d2f]"
                        >
                        <input
                            type="number"
                            name="max_price"
                            placeholder="Max ₱"
                            class="w-full border border-[#ddd6ce] bg-[#faf9f7] px-3 py-3 text-sm outline-none focus:border-[#d92d2f]"
                        >
                    </div>

                    <button
                        type="submit"
                        class="mt-4 w-full bg-[#d92d2f] px-4 py-3 text-xs font-bold text-white"
                    >
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- =====================================================
         PRODUCTS AREA
    ====================================================== --}}
    <section class="py-8 lg:py-12">
        <div class="mx-auto w-full max-w-[1280px] px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[235px_minmax(0,1fr)]">

                {{-- =================================================
                     FILTER SIDEBAR
                ================================================== --}}
                <aside class="hidden lg:block">
                    <form
                        action="{{ url('/buyer/products') }}"
                        method="GET"
                        class="sticky top-[130px] border border-[#e1dbd4] bg-white"
                    >
                        <div class="flex items-center justify-between border-b border-[#ebe6df] px-5 py-4">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 6h16M7 12h10M10 18h4"></path>
                                </svg>

                                <h2 class="text-xs font-black uppercase tracking-[0.16em]">
                                    Filters
                                </h2>
                            </div>

                            <a
                                href="{{ url('/buyer/products') }}"
                                class="text-[10px] font-bold text-[#d92d2f] hover:underline"
                            >
                                Clear
                            </a>
                        </div>

                        {{-- Categories --}}
                        <div class="border-b border-[#ebe6df] p-5">
                            <h3 class="text-[11px] font-black uppercase tracking-[0.16em]">
                                Category
                            </h3>

                            <div class="mt-4 space-y-3">
                                @foreach($categories as $category)
                                    <label class="flex cursor-pointer items-center justify-between gap-3 text-sm text-[#5e5953]">
                                        <span class="flex items-center gap-3">
                                            <input
                                                type="checkbox"
                                                name="categories[]"
                                                value="{{ \Illuminate\Support\Str::slug($category['name']) }}"
                                                class="h-4 w-4 accent-[#d92d2f]"
                                            >
                                            <span>{{ $category['name'] }}</span>
                                        </span>

                                        <span class="text-[10px] text-[#b2aaa1]">
                                            {{ $category['count'] }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Price --}}
                        <div class="border-b border-[#ebe6df] p-5">
                            <h3 class="text-[11px] font-black uppercase tracking-[0.16em]">
                                Price Range
                            </h3>

                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <input
                                    type="number"
                                    name="min_price"
                                    placeholder="Min ₱"
                                    class="w-full min-w-0 border border-[#ddd6ce] bg-[#faf9f7] px-3 py-2.5 text-xs outline-none focus:border-[#d92d2f]"
                                >

                                <input
                                    type="number"
                                    name="max_price"
                                    placeholder="Max ₱"
                                    class="w-full min-w-0 border border-[#ddd6ce] bg-[#faf9f7] px-3 py-2.5 text-xs outline-none focus:border-[#d92d2f]"
                                >
                            </div>

                            <button
                                type="submit"
                                class="mt-3 w-full border border-[#d92d2f] px-3 py-2.5 text-xs font-bold text-[#d92d2f] transition hover:bg-[#d92d2f] hover:text-white"
                            >
                                Apply Price
                            </button>
                        </div>

                        {{-- Rating --}}
                        <div class="border-b border-[#ebe6df] p-5">
                            <h3 class="text-[11px] font-black uppercase tracking-[0.16em]">
                                Rating
                            </h3>

                            <div class="mt-4 space-y-3">
                                @foreach([4, 3, 2] as $rating)
                                    <label class="flex cursor-pointer items-center gap-3 text-sm text-[#5e5953]">
                                        <input
                                            type="radio"
                                            name="rating"
                                            value="{{ $rating }}"
                                            class="h-4 w-4 accent-[#d92d2f]"
                                        >
                                        <span class="text-[#f2a000]">
                                            @for($i = 1; $i <= 5; $i++)
                                                {{ $i <= $rating ? '★' : '☆' }}
                                            @endfor
                                        </span>
                                        <span class="text-xs">& up</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Shipping & Local --}}
                        <div class="p-5">
                            <h3 class="text-[11px] font-black uppercase tracking-[0.16em]">
                                More
                            </h3>

                            <div class="mt-4 space-y-3">
                                <label class="flex cursor-pointer items-center gap-3 text-sm text-[#5e5953]">
                                    <input
                                        type="checkbox"
                                        name="free_shipping"
                                        value="1"
                                        class="h-4 w-4 accent-[#d92d2f]"
                                    >
                                    Free Shipping
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 text-sm text-[#5e5953]">
                                    <input
                                        type="checkbox"
                                        name="local"
                                        value="1"
                                        class="h-4 w-4 accent-[#d92d2f]"
                                    >
                                    Filipino Local Finds
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 text-sm text-[#5e5953]">
                                    <input
                                        type="checkbox"
                                        name="flash_deal"
                                        value="1"
                                        class="h-4 w-4 accent-[#d92d2f]"
                                    >
                                    Flash Deals
                                </label>
                            </div>

                            <button
                                type="submit"
                                class="mt-5 w-full bg-[#111] px-4 py-3 text-xs font-bold text-white transition hover:bg-[#d92d2f]"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </aside>

                {{-- =================================================
                     PRODUCT LIST
                ================================================== --}}
                <div class="min-w-0">
                    {{-- Toolbar --}}
                    <div class="mb-5 flex flex-col gap-3 border border-[#e1dbd4] bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-2 text-xs text-[#77716a]">
                            <span class="hidden sm:inline">Showing</span>
                            <strong class="text-[#111]">1–12</strong>
                            <span>of</span>
                            <strong class="text-[#111]">1,248</strong>
                            <span>products</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="hidden text-xs text-[#77716a] sm:inline">Sort by</span>

                            <select
                                name="sort"
                                class="min-w-[165px] border border-[#ddd6ce] bg-white px-3 py-2.5 text-xs font-semibold outline-none"
                            >
                                <option value="popular">Most Popular</option>
                                <option value="newest">Newest</option>
                                <option value="price_asc">Price: Low to High</option>
                                <option value="price_desc">Price: High to Low</option>
                                <option value="rating">Top Rated</option>
                                <option value="discount">Biggest Discount</option>
                            </select>
                        </div>
                    </div>

                    {{-- Active filter sample --}}
                    <div class="mb-5 flex flex-wrap items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#8d867e]">
                            Popular:
                        </span>

                        @foreach(['Free Shipping', 'Local Finds', 'Under ₱1,000'] as $filter)
                            <a
                                href="#"
                                class="border border-[#ddd6ce] bg-white px-3 py-2 text-[10px] font-semibold text-[#5e5953] transition hover:border-[#d92d2f] hover:text-[#d92d2f]"
                            >
                                {{ $filter }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Product Grid --}}
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 xl:grid-cols-4">
                        @foreach($products as $product)
                            <article class="group overflow-hidden border border-[#ebe5de] bg-white transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_18px_45px_rgba(0,0,0,0.08)]">
                                <a href="{{ url('/buyer/products/' . \Illuminate\Support\Str::slug($product['name'])) }}" class="relative block aspect-[4/4.4] overflow-hidden bg-[#ebe7e1]">
                                    <img
                                        src="{{ $product['image'] }}"
                                        alt="{{ $product['name'] }}"
                                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                    >

                                    {{-- Top badges --}}
                                    <div class="absolute left-3 top-3 flex flex-col items-start gap-1.5">
                                        @if($product['discount'])
                                            <span class="bg-[#e3262b] px-2 py-1 text-[9px] font-black text-white sm:text-[10px]">
                                                -{{ $product['discount'] }}%
                                            </span>
                                        @endif

                                        @if($product['local'])
                                            <span class="bg-[#079b72] px-2 py-1 text-[9px] font-black text-white sm:text-[10px]">
                                                LOCAL
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Wishlist --}}
                                    <button
                                        type="button"
                                        onclick="event.preventDefault();"
                                        class="absolute right-3 top-3 grid h-8 w-8 place-items-center rounded-full bg-white/90 text-[#5e5953] shadow-sm transition hover:bg-[#d92d2f] hover:text-white"
                                        aria-label="Add to wishlist"
                                    >
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 5.9l-1.3-1.3a5.3 5.3 0 1 0-7.5 7.5L12 21l8.8-8.9a5.3 5.3 0 0 0 0-7.5Z"></path>
                                        </svg>
                                    </button>
                                </a>

                                <div class="p-3 sm:p-4">
                                    <p class="truncate text-[9px] font-bold uppercase tracking-[0.14em] text-[#a59d93] sm:text-[10px]">
                                        {{ $product['seller'] }}
                                    </p>

                                    <a
                                        href="{{ url('/buyer/products/' . \Illuminate\Support\Str::slug($product['name'])) }}"
                                        class="mt-2 block min-h-[40px] text-[13px] font-medium leading-5 transition hover:text-[#d92d2f] sm:text-sm"
                                    >
                                        {{ $product['name'] }}
                                    </a>

                                    <div class="mt-3 flex flex-wrap items-baseline gap-2">
                                        <span class="text-base font-black text-[#d92d2f] sm:text-lg">
                                            ₱{{ number_format($product['price']) }}
                                        </span>

                                        @if($product['old_price'])
                                            <span class="text-[10px] text-[#b0aaa3] line-through sm:text-[11px]">
                                                ₱{{ number_format($product['old_price']) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex items-center justify-between gap-2 text-[10px] text-[#817a72]">
                                        <span>
                                            <span class="text-[#f2a000]">★</span>
                                            {{ number_format($product['rating'], 1) }}
                                            <span class="text-[#bbb4ac]">·</span>
                                            {{ $product['sold'] }} sold
                                        </span>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-[#eee8e1] pt-3">
                                        @if($product['free_shipping'])
                                            <span class="text-[9px] font-bold text-[#079b72] sm:text-[10px]">
                                                FREE SHIPPING
                                            </span>
                                        @endif

                                        <span class="ml-auto truncate text-[9px] text-[#aaa39b] sm:text-[10px]">
                                            {{ $product['location'] }}
                                        </span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- =================================================
                         PAGINATION
                    ================================================== --}}
                    <nav class="mt-10 flex items-center justify-center gap-1" aria-label="Pagination">
                        <a
                            href="#"
                            class="grid h-10 w-10 place-items-center border border-[#ddd6ce] bg-white text-sm text-[#77716a] transition hover:border-[#111] hover:text-[#111]"
                            aria-label="Previous page"
                        >
                            ←
                        </a>

                        <a href="#" class="grid h-10 min-w-10 place-items-center bg-[#d92d2f] px-3 text-xs font-bold text-white">
                            1
                        </a>

                        @foreach([2, 3, 4, 5] as $page)
                            <a
                                href="#"
                                class="grid h-10 min-w-10 place-items-center border border-[#ddd6ce] bg-white px-3 text-xs font-semibold text-[#5e5953] transition hover:border-[#d92d2f] hover:text-[#d92d2f]"
                            >
                                {{ $page }}
                            </a>
                        @endforeach

                        <span class="px-2 text-[#aaa39b]">...</span>

                        <a
                            href="#"
                            class="hidden h-10 min-w-10 place-items-center border border-[#ddd6ce] bg-white px-3 text-xs font-semibold text-[#5e5953] transition hover:border-[#d92d2f] hover:text-[#d92d2f] sm:grid"
                        >
                            104
                        </a>

                        <a
                            href="#"
                            class="grid h-10 w-10 place-items-center border border-[#ddd6ce] bg-white text-sm text-[#77716a] transition hover:border-[#111] hover:text-[#111]"
                            aria-label="Next page"
                        >
                            →
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    {{-- =====================================================
         SELLER / LOCAL CTA
    ====================================================== --}}
    <section class="bg-[#d92d2f] text-white">
        <div class="mx-auto grid w-full max-w-[1280px] gap-8 px-4 py-12 sm:px-6 md:grid-cols-[1fr_auto] md:items-center lg:px-8">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-white/70">
                    MADE IN THE PHILIPPINES
                </p>

                <h2 class="mt-2 text-2xl font-black tracking-tight sm:text-3xl">
                    Discover proudly local Filipino finds.
                </h2>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-white/70">
                    Support artisans, independent sellers, and homegrown brands from across the country.
                </p>
            </div>

            <a
                href="{{ url('/local-finds') }}"
                class="inline-flex h-12 items-center justify-center border border-white/35 px-6 text-sm font-bold transition hover:bg-white hover:text-[#d92d2f]"
            >
                Shop Local →
            </a>
        </div>
    </section>
</main>

{{-- =========================================================
     FOOTER
========================================================= --}}
<footer class="bg-[#0a0a0a] text-white">
    <div class="mx-auto w-full max-w-[1280px] px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.5fr_repeat(4,1fr)]">
            <div>
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black">
                        L
                    </span>
                    <span class="text-xl font-black">LIKHAE</span>
                </a>

                <p class="mt-4 max-w-[240px] text-sm leading-6 text-white/35">
                    Shop More. Discover More. Live More. — Your Philippine marketplace.
                </p>
            </div>

            <div>
                <h3 class="text-[11px] font-black tracking-[0.2em]">SHOP</h3>
                <div class="mt-5 flex flex-col gap-4 text-xs text-white/30">
                    <a href="{{ url('/buyer/products') }}" class="hover:text-white">All Products</a>
                    <a href="{{ url('/flash-deals') }}" class="hover:text-white">Flash Deals</a>
                    <a href="{{ url('/local-finds') }}" class="hover:text-white">Local Finds</a>
                    <a href="{{ url('/categories') }}" class="hover:text-white">Categories</a>
                </div>
            </div>

            <div>
                <h3 class="text-[11px] font-black tracking-[0.2em]">SELL</h3>
                <div class="mt-5 flex flex-col gap-4 text-xs text-white/30">
                    <a href="{{ url('/seller/register') }}" class="hover:text-white">Start Selling</a>
                    <a href="#" class="hover:text-white">Seller Center</a>
                    <a href="#" class="hover:text-white">Advertising</a>
                    <a href="#" class="hover:text-white">LIKHAE Academy</a>
                </div>
            </div>

            <div>
                <h3 class="text-[11px] font-black tracking-[0.2em]">SUPPORT</h3>
                <div class="mt-5 flex flex-col gap-4 text-xs text-white/30">
                    <a href="#" class="hover:text-white">Help Center</a>
                    <a href="#" class="hover:text-white">Track Order</a>
                    <a href="#" class="hover:text-white">Returns</a>
                    <a href="#" class="hover:text-white">Buyer Protection</a>
                </div>
            </div>

            <div>
                <h3 class="text-[11px] font-black tracking-[0.2em]">COMPANY</h3>
                <div class="mt-5 flex flex-col gap-4 text-xs text-white/30">
                    <a href="#" class="hover:text-white">About LIKHAE</a>
                    <a href="#" class="hover:text-white">Careers</a>
                    <a href="#" class="hover:text-white">Privacy Policy</a>
                    <a href="#" class="hover:text-white">Terms</a>
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-4 border-t border-white/10 pt-6 text-[10px] text-white/25 md:flex-row md:items-center md:justify-between">
            <p>© {{ date('Y') }} LIKHAE, Inc. — Made with pride in the Philippines.</p>
            <p>GCASH · MAYA · VISA · MASTERCARD · COD · BPI</p>
        </div>
    </div>
</footer>

</body>
</html>
