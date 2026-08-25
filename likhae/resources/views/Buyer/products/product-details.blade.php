<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Baseus Wireless Earbuds A3i Pro — LIKHAE</title>

    @vite([
        'resources/css/Buyer/product-details.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo product data
    |--------------------------------------------------------------------------
    | Replace this with a $product model from ProductController later.
    */
    $product = [
        'name' => 'Baseus Wireless Earbuds A3i Pro',
        'category' => 'Electronics',
        'seller' => 'TechHub PH',
        'seller_rating' => 4.8,
        'seller_location' => 'Makati City, NCR',
        'price' => 599,
        'old_price' => 1299,
        'discount' => 54,
        'saved' => 700,
        'rating' => 4.7,
        'reviews_count' => 2341,
        'sold' => 8921,
        'stock' => 45,
        'sku' => 'BSA3I-PRO-BLK',
        'brand' => 'Baseus',
        'condition' => 'Brand New',
        'warranty' => '12 Months Seller Warranty',
        'shipping' => 'Nationwide Delivery',
        'shipping_eta' => '2–7 business days',
        'description' => 'Premium wireless earbuds with active noise cancellation, 30-hour total battery life, and Bluetooth 5.3. Includes charging case. IPX5 water-resistant. Clear calls with 4-mic setup.',
        'main_image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1300&q=95',
        'images' => [
            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1300&q=95',
            'https://images.unsplash.com/photo-1484704849700-f032a568e944?auto=format&fit=crop&w=1300&q=95',
            'https://images.unsplash.com/photo-1545127398-14699f92334b?auto=format&fit=crop&w=1300&q=95',
        ],
    ];

    $categories = [
        'Electronics',
        'Fashion',
        'Home & Living',
        'Beauty & Care',
        'Food & Grocery',
        'Sports',
        'Books',
        'Toys & Games',
        'Health & Wellness',
        'Automotive',
    ];

    $similarProducts = [
        [
            'name' => 'Lenovo IdeaPad Slim 5i 14" Laptop',
            'seller' => 'TECH REPUBLIC PH',
            'price' => 34999,
            'old_price' => 42999,
            'discount' => 19,
            'rating' => 4.7,
            'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Samsung Galaxy A35 5G 256GB',
            'seller' => 'SAMSUNG OFFICIAL PH',
            'price' => 16999,
            'old_price' => 19999,
            'discount' => 15,
            'rating' => 4.6,
            'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Mechanical Keyboard TKL RGB',
            'seller' => 'PC WORLD MNL',
            'price' => 1799,
            'old_price' => 3499,
            'discount' => 49,
            'rating' => 4.9,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=900&q=90',
        ],
        [
            'name' => 'Premium Cotton Polo Shirt',
            'seller' => 'STYLE MANILA',
            'price' => 249,
            'old_price' => 599,
            'discount' => 58,
            'rating' => 4.5,
            'image' => 'https://images.unsplash.com/photo-1603252109303-2751441dd157?auto=format&fit=crop&w=900&q=90',
        ],
    ];
@endphp

{{-- =========================================================
     HEADER
========================================================= --}}
<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container">
        <div class="flex h-16 items-center gap-4">

            <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">L</span>
                <span class="text-xl font-black tracking-tight">LIKHAE</span>
            </a>

            <form action="{{ url('/buyer/products') }}" method="GET" class="hidden min-w-0 flex-1 md:flex">
                <div class="flex h-11 w-full overflow-hidden border border-[#dedad3] bg-white">
                    <select
                        name="category"
                        class="w-[100px] border-r border-[#dedad3] bg-white px-3 text-xs text-[#5d5a55] outline-none"
                    >
                        <option value="">All</option>
                        @foreach($categories as $category)
                            <option value="{{ \Illuminate\Support\Str::slug($category) }}">
                                {{ $category }}
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
                <a href="{{ url('/notifications') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                            <path d="M10 21h4"></path>
                        </svg>
                        <span class="absolute -right-1 -top-1 h-2 w-2 rounded-full bg-[#d92d2f]"></span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Alerts</span>
                </a>

                <a href="{{ url('/buyer/account') }}" class="buyer-header-action">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <circle cx="12" cy="8" r="3.2"></circle>
                        <path d="M5.5 20c.7-4.2 3-6.3 6.5-6.3s5.8 2.1 6.5 6.3"></path>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Account</span>
                </a>

                <a href="{{ url('/buyer/wishlist') }}" class="buyer-header-action">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 5.9l-1.3-1.3a5.3 5.3 0 1 0-7.5 7.5L12 21l8.8-8.9a5.3 5.3 0 0 0 0-7.5Z"></path>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Wishlist</span>
                </a>

                <a href="{{ url('/buyer/cart') }}" class="buyer-header-action">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6"></path>
                        <circle cx="9" cy="20" r="1"></circle>
                        <circle cx="17" cy="20" r="1"></circle>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Cart</span>
                </a>
            </nav>
        </div>

        <form action="{{ url('/buyer/products') }}" method="GET" class="pb-3 md:hidden">
            <div class="flex h-10 overflow-hidden border border-[#dedad3] bg-white">
                <input
                    type="search"
                    name="q"
                    placeholder="Search LIKHAE..."
                    class="min-w-0 flex-1 px-3 text-sm outline-none"
                >
                <button type="submit" class="w-12 bg-[#d92d2f] text-white" aria-label="Search">
                    <svg class="mx-auto h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <div class="border-t border-[#ece8e1]">
        <div class="likhae-container flex h-10 items-center gap-7 overflow-x-auto whitespace-nowrap text-xs text-[#4e4a45] hide-scrollbar">
            @foreach($categories as $category)
                <a
                    href="{{ url('/buyer/products?category=' . \Illuminate\Support\Str::slug($category)) }}"
                    class="transition hover:text-[#d92d2f]"
                >
                    {{ $category }}
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
         BREADCRUMB
    ====================================================== --}}
    <section class="border-b border-[#ddd8d1] bg-white">
        <div class="likhae-container flex min-h-[52px] items-center gap-2 overflow-x-auto whitespace-nowrap py-3 text-xs hide-scrollbar">
            <a href="{{ url('/buyer/home') }}" class="text-[#8f8982] transition hover:text-[#d92d2f]">Home</a>
            <span class="text-[#b9b2aa]">›</span>
            <a href="{{ url('/buyer/products?category=' . \Illuminate\Support\Str::slug($product['category'])) }}" class="text-[#8f8982] transition hover:text-[#d92d2f]">
                {{ $product['category'] }}
            </a>
            <span class="text-[#b9b2aa]">›</span>
            <span class="truncate font-semibold text-[#111]">{{ $product['name'] }}</span>
        </div>
    </section>

    {{-- =====================================================
         PRODUCT HERO
    ====================================================== --}}
    <section class="py-8 lg:py-9">
        <div class="likhae-container">
            <div class="grid gap-8 lg:grid-cols-[1fr_1fr]">

                {{-- Gallery --}}
                <div class="min-w-0">
                    <div class="aspect-square overflow-hidden bg-[#eee9e1]">
                        <img
                            id="mainProductImage"
                            src="{{ $product['main_image'] }}"
                            alt="{{ $product['name'] }}"
                            class="h-full w-full object-cover"
                        >
                    </div>

                    <div class="mt-3 flex gap-2 overflow-x-auto pb-1 hide-scrollbar">
                        @foreach($product['images'] as $index => $image)
                            <button
                                type="button"
                                class="product-thumbnail {{ $index === 0 ? 'is-active' : '' }}"
                                data-image="{{ $image }}"
                                aria-label="View product image {{ $index + 1 }}"
                            >
                                <img
                                    src="{{ $image }}"
                                    alt="{{ $product['name'] }} image {{ $index + 1 }}"
                                    class="h-full w-full object-cover"
                                >
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Product information --}}
                <div class="min-w-0 lg:pt-1">
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-[#d92d2f]">
                        {{ $product['category'] }}
                    </p>

                    <h1 class="mt-3 text-2xl font-black leading-tight tracking-[-0.035em] sm:text-3xl">
                        {{ $product['name'] }}
                    </h1>

                    <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-[#8a837c]">
                        <span>
                            <span class="text-[#f2a000]">★★★★★</span>
                            <strong class="ml-1 font-semibold text-[#625d57]">
                                {{ number_format($product['rating'], 1) }}
                            </strong>
                            <span class="text-[#aaa39b]">({{ number_format($product['reviews_count']) }})</span>
                        </span>

                        <span>{{ number_format($product['sold']) }} sold</span>

                        <span class="font-semibold text-[#079b72]">
                            ♧ Free Shipping
                        </span>
                    </div>

                    {{-- Price box --}}
                    <div class="mt-6 border border-[#f4cfd0] bg-[#fff2f2] px-5 py-5">
                        <div class="flex flex-wrap items-end gap-3">
                            <span class="text-4xl font-black leading-none text-[#d92d2f]">
                                ₱{{ number_format($product['price']) }}
                            </span>

                            <span class="pb-1 text-lg text-[#b8b0a9] line-through">
                                ₱{{ number_format($product['old_price']) }}
                            </span>

                            <span class="mb-1 bg-[#d92d2f] px-2 py-1 text-[10px] font-black text-white">
                                {{ $product['discount'] }}% OFF
                            </span>
                        </div>

                        <p class="mt-3 text-xs font-semibold text-[#d92d2f]">
                            You save ₱{{ number_format($product['saved']) }}
                        </p>
                    </div>

                    {{-- Stock --}}
                    <div class="mt-5 flex items-center gap-2 text-xs">
                        <span class="h-2 w-2 rounded-full bg-[#079b72]"></span>
                        <span class="font-semibold">
                            In Stock ({{ $product['stock'] }} available)
                        </span>
                    </div>

                    {{-- Quantity --}}
                    <div class="mt-6 flex flex-wrap items-center gap-4">
                        <span class="w-16 text-[10px] font-bold uppercase tracking-[0.16em] text-[#8d867e]">
                            Qty
                        </span>

                        <div class="flex h-10 border border-[#ddd6ce] bg-white">
                            <button
                                id="decreaseQty"
                                type="button"
                                class="grid w-10 place-items-center text-[#6e6861] transition hover:bg-[#f3efe9]"
                            >
                                −
                            </button>

                            <input
                                id="quantityInput"
                                name="quantity"
                                type="number"
                                value="1"
                                min="1"
                                max="{{ $product['stock'] }}"
                                class="w-12 border-x border-[#e7e1da] bg-white text-center text-sm font-semibold outline-none"
                            >

                            <button
                                id="increaseQty"
                                type="button"
                                class="grid w-10 place-items-center text-[#6e6861] transition hover:bg-[#f3efe9]"
                            >
                                +
                            </button>
                        </div>

                        <span class="text-xs text-[#aaa39b]">
                            {{ $product['stock'] }} available
                        </span>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-6 grid grid-cols-[1fr_1fr_auto] gap-2">
                        <button
                            type="button"
                            class="flex h-13 items-center justify-center gap-2 bg-[#101010] px-4 text-sm font-bold text-white transition hover:bg-[#272727]"
                        >
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6"></path>
                                <circle cx="9" cy="20" r="1"></circle>
                                <circle cx="17" cy="20" r="1"></circle>
                            </svg>
                            <span class="hidden sm:inline">Add to Cart</span>
                            <span class="sm:hidden">Cart</span>
                        </button>

                        <a
                            href="{{ url('/buyer/checkout') }}"
                            class="flex h-13 items-center justify-center bg-[#d92d2f] px-4 text-sm font-bold text-white transition hover:bg-[#bd2024]"
                        >
                            Buy Now
                        </a>

                        <button
                            type="button"
                            class="grid h-13 w-13 place-items-center border border-[#ddd6ce] bg-white text-[#a39c94] transition hover:border-[#d92d2f] hover:text-[#d92d2f]"
                            aria-label="Add to wishlist"
                        >
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 5.9l-1.3-1.3a5.3 5.3 0 1 0-7.5 7.5L12 21l8.8-8.9a5.3 5.3 0 0 0 0-7.5Z"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Seller --}}
                    <div class="mt-6 border border-[#ddd7d0] bg-white p-4">
                        <div class="flex items-center gap-4">
                            <div class="grid h-12 w-12 shrink-0 place-items-center border border-[#ddd7d0] bg-[#faf8f5] text-[#8d867e]">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M4 10h16v10H4z"></path>
                                    <path d="m5 10 1-5h12l1 5"></path>
                                    <path d="M9 14h6"></path>
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold">{{ $product['seller'] }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-[10px] text-[#9c958d]">
                                    <span>
                                        <span class="text-[#f2a000]">★★★★★</span>
                                        <strong class="font-semibold text-[#6d6760]">
                                            {{ number_format($product['seller_rating'], 1) }}
                                        </strong>
                                    </span>
                                    <span>{{ $product['seller_location'] }}</span>
                                </div>
                            </div>

                            <a
                                href="{{ url('/seller/techhub-ph') }}"
                                class="shrink-0 border border-[#ddd7d0] px-4 py-2.5 text-[11px] font-semibold transition hover:border-[#d92d2f] hover:text-[#d92d2f]"
                            >
                                Visit Shop
                            </a>
                        </div>
                    </div>

                    {{-- Delivery & protection --}}
                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        <div class="border border-[#ddd7d0] bg-[#f9f7f3] p-4">
                            <div class="flex gap-3">
                                <span class="mt-0.5 text-[#079b72]">♧</span>
                                <div>
                                    <p class="text-[11px] font-bold">{{ $product['shipping'] }}</p>
                                    <p class="mt-1 text-[10px] text-[#aaa39b]">{{ $product['shipping_eta'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="border border-[#ddd7d0] bg-[#f9f7f3] p-4">
                            <div class="flex gap-3">
                                <span class="mt-0.5 text-[#079b72]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"></path>
                                    </svg>
                                </span>

                                <div>
                                    <p class="text-[11px] font-bold">Buyer Protection</p>
                                    <p class="mt-1 text-[10px] text-[#aaa39b]">100% money-back guarantee</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================================================
                 PRODUCT TABS
            ================================================== --}}
            <div class="mt-12 border border-[#ddd7d0] bg-white">
                <div class="flex overflow-x-auto border-b border-[#ddd7d0] hide-scrollbar">
                    <button type="button" class="product-tab is-active" data-tab="description">
                        Description
                    </button>
                    <button type="button" class="product-tab" data-tab="specifications">
                        Specifications
                    </button>
                    <button type="button" class="product-tab" data-tab="reviews">
                        Reviews
                    </button>
                </div>

                <div class="p-6 sm:p-7">
                    <div id="tab-description" class="tab-panel">
                        <p class="text-sm leading-7 text-[#514c46]">
                            {{ $product['description'] }}
                        </p>
                    </div>

                    <div id="tab-specifications" class="tab-panel hidden">
                        <div class="grid gap-x-12 gap-y-4 text-sm sm:grid-cols-2">
                            <div class="spec-row">
                                <span>Brand</span>
                                <strong>{{ $product['brand'] }}</strong>
                            </div>
                            <div class="spec-row">
                                <span>SKU</span>
                                <strong>{{ $product['sku'] }}</strong>
                            </div>
                            <div class="spec-row">
                                <span>Condition</span>
                                <strong>{{ $product['condition'] }}</strong>
                            </div>
                            <div class="spec-row">
                                <span>Warranty</span>
                                <strong>{{ $product['warranty'] }}</strong>
                            </div>
                            <div class="spec-row">
                                <span>Category</span>
                                <strong>{{ $product['category'] }}</strong>
                            </div>
                            <div class="spec-row">
                                <span>Stock</span>
                                <strong>{{ $product['stock'] }} available</strong>
                            </div>
                        </div>
                    </div>

                    <div id="tab-reviews" class="tab-panel hidden">
                        <div class="grid gap-6 md:grid-cols-[170px_1fr]">
                            <div>
                                <div class="text-4xl font-black">{{ number_format($product['rating'], 1) }}</div>
                                <div class="mt-2 text-[#f2a000]">★★★★★</div>
                                <p class="mt-2 text-xs text-[#9c958d]">
                                    {{ number_format($product['reviews_count']) }} verified reviews
                                </p>
                            </div>

                            <div class="border-l-0 border-[#e6e0d9] md:border-l md:pl-6">
                                <p class="text-sm font-bold">Excellent product quality</p>
                                <p class="mt-2 text-sm leading-6 text-[#716b64]">
                                    Buyers consistently praise the sound quality, battery life, fit, and overall value for money.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================================================
                 SIMILAR PRODUCTS
            ================================================== --}}
            <section class="pb-4 pt-12">
                <p class="section-eyebrow">YOU MAY ALSO LIKE</p>
                <h2 class="mt-3 text-3xl font-black tracking-[-0.035em]">Similar Products</h2>

                <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
                    @foreach($similarProducts as $item)
                        <article class="group overflow-hidden bg-white transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_18px_45px_rgba(0,0,0,0.08)]">
                            <a
                                href="{{ url('/buyer/products/' . \Illuminate\Support\Str::slug($item['name'])) }}"
                                class="relative block aspect-square overflow-hidden bg-[#ebe7e1]"
                            >
                                <img
                                    src="{{ $item['image'] }}"
                                    alt="{{ $item['name'] }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                >

                                <span class="absolute left-3 top-3 bg-[#d92d2f] px-2 py-1 text-[9px] font-black text-white">
                                    -{{ $item['discount'] }}%
                                </span>
                            </a>

                            <div class="p-4">
                                <p class="truncate text-[9px] font-bold uppercase tracking-[0.15em] text-[#a59d93] sm:text-[10px]">
                                    {{ $item['seller'] }}
                                </p>

                                <a
                                    href="{{ url('/buyer/products/' . \Illuminate\Support\Str::slug($item['name'])) }}"
                                    class="mt-2 block min-h-[40px] text-[13px] font-medium leading-5 transition hover:text-[#d92d2f] sm:text-sm"
                                >
                                    {{ $item['name'] }}
                                </a>

                                <div class="mt-4 border-t border-[#ebe5de] pt-4">
                                    <div class="flex items-end justify-between gap-3">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-base font-black text-[#d92d2f] sm:text-lg">
                                                    ₱{{ number_format($item['price']) }}
                                                </span>
                                                <span class="text-[10px] text-[#aaa39b] line-through">
                                                    ₱{{ number_format($item['old_price']) }}
                                                </span>
                                            </div>

                                            <p class="mt-2 text-[9px] font-bold text-[#079b72] sm:text-[10px]">
                                                ♧ Free Shipping
                                            </p>
                                        </div>

                                        <span class="pb-1 text-[10px] text-[#6d6760]">
                                            <span class="text-[#f2a000]">★</span>
                                            {{ number_format($item['rating'], 1) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </section>
</main>

{{-- =========================================================
     FOOTER
========================================================= --}}
<footer class="mt-8 bg-[#0a0a0a] text-white">
    <div class="likhae-container py-14">
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
                        <a
                            href="#"
                            class="grid h-9 w-9 place-items-center border border-white/10 text-[10px] text-white/45 transition hover:border-white/30 hover:text-white"
                        >
                            {{ $social }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="footer-title">SHOP</h3>
                <div class="footer-links">
                    <a href="{{ url('/buyer/products?category=electronics') }}">Electronics</a>
                    <a href="{{ url('/buyer/products?category=fashion') }}">Fashion</a>
                    <a href="{{ url('/buyer/products?category=home-living') }}">Home & Living</a>
                    <a href="{{ url('/buyer/products?category=beauty-care') }}">Beauty & Care</a>
                    <a href="{{ url('/local-finds') }}">Local Finds</a>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mainImage = document.getElementById('mainProductImage');
        const thumbnails = document.querySelectorAll('.product-thumbnail');

        thumbnails.forEach((thumbnail) => {
            thumbnail.addEventListener('click', () => {
                mainImage.src = thumbnail.dataset.image;

                thumbnails.forEach((item) => item.classList.remove('is-active'));
                thumbnail.classList.add('is-active');
            });
        });

        const quantityInput = document.getElementById('quantityInput');
        const decreaseQty = document.getElementById('decreaseQty');
        const increaseQty = document.getElementById('increaseQty');

        const minQty = parseInt(quantityInput.min || '1', 10);
        const maxQty = parseInt(quantityInput.max || '999', 10);

        decreaseQty.addEventListener('click', () => {
            const value = parseInt(quantityInput.value || '1', 10);
            quantityInput.value = Math.max(minQty, value - 1);
        });

        increaseQty.addEventListener('click', () => {
            const value = parseInt(quantityInput.value || '1', 10);
            quantityInput.value = Math.min(maxQty, value + 1);
        });

        quantityInput.addEventListener('change', () => {
            let value = parseInt(quantityInput.value || '1', 10);

            if (Number.isNaN(value)) {
                value = minQty;
            }

            quantityInput.value = Math.max(minQty, Math.min(maxQty, value));
        });

        const tabs = document.querySelectorAll('.product-tab');
        const panels = document.querySelectorAll('.tab-panel');

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;

                tabs.forEach((item) => item.classList.remove('is-active'));
                panels.forEach((panel) => panel.classList.add('hidden'));

                tab.classList.add('is-active');
                document.getElementById(`tab-${target}`).classList.remove('hidden');
            });
        });
    });
</script>

</body>
</html>