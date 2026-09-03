@extends('layouts.buyer')

@section('title', 'Home — LIKHAE')
@section('active', 'home')

@section('content')
@php
    /*
    |--------------------------------------------------------------------------
    | Product collection
    |--------------------------------------------------------------------------
    */

    if (isset($buyerProducts) && method_exists($buyerProducts, 'items')) {
        $products = collect($buyerProducts->items());
    } else {
        $products = collect($buyerProducts ?? []);
    }

    $featuredProducts = $products->take(4);
    $recommendedProducts = $products->skip(4)->take(4);
    $heroProduct = $products->first();

    $heroImage = data_get($heroProduct, 'image_url')
        ?? data_get($heroProduct, 'image');

    $heroName = data_get(
        $heroProduct,
        'name',
        'Discover meaningful local products'
    );

    $heroPrice = max(
        0,
        (float) data_get($heroProduct, 'price', 0)
    );

    $heroCategoryValue = data_get($heroProduct, 'category.name')
        ?? data_get($heroProduct, 'category')
        ?? 'Featured Product';

    $heroCategory = is_scalar($heroCategoryValue)
        ? (string) $heroCategoryValue
        : 'Featured Product';

    /*
    |--------------------------------------------------------------------------
    | Front-end-safe optional routes
    |--------------------------------------------------------------------------
    */

    $hasWishlistRoute =
        \Illuminate\Support\Facades\Route::has('buyer.wishlist');

    $wishlistUrl = $hasWishlistRoute
        ? route('buyer.wishlist')
        : '#';

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    $categories = [
        [
            'name' => 'Fashion',
            'slug' => 'fashion',
            'image' => asset('images/buyer/products/shoes.svg'),
        ],
        [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'image' => asset('images/buyer/products/headphones.svg'),
        ],
        [
            'name' => 'Home',
            'slug' => 'home',
            'image' => asset('images/buyer/products/lamp.svg'),
        ],
        [
            'name' => 'Books',
            'slug' => 'books',
            'image' => asset('images/buyer/products/book.svg'),
        ],
        [
            'name' => 'Beauty',
            'slug' => 'beauty',
            'image' => asset('images/buyer/products/skincare.svg'),
        ],
        [
            'name' => 'Sports',
            'slug' => 'sports',
            'image' => asset('images/buyer/products/basketball.svg'),
        ],
        [
            'name' => 'Bags',
            'slug' => 'bags',
            'image' => asset('images/buyer/products/backpack.svg'),
        ],
        [
            'name' => 'Cameras',
            'slug' => 'cameras',
            'image' => asset('images/buyer/products/camera.svg'),
        ],
    ];
@endphp

<div class="lk-page">
    {{-- Hero --}}
    <section class="lk-hero">
        <div class="lk-hero-copy">
            <span class="lk-kicker">
                Welcome to LIKHAE
            </span>

            <h1>
                Discover products with
                <span>heart and story.</span>
            </h1>

            <p>
                Explore meaningful local finds from trusted sellers,
                manage your orders, and connect directly with shops.
            </p>

            <div class="lk-hero-actions">
                <a
                    href="{{ route('buyer.products') }}"
                    class="lk-btn lk-btn-red"
                >
                    Shop Now
                </a>

                <a
                    href="{{ route('buyer.orders') }}"
                    class="lk-btn lk-btn-light"
                >
                    Track Orders
                </a>
            </div>

            <div class="lk-hero-stats">
                <div>
                    <strong>30+</strong>
                    <span>Trusted Sellers</span>
                </div>

                <div>
                    <strong>10k+</strong>
                    <span>Buyer Visits</span>
                </div>

                <div>
                    <strong>4.8</strong>
                    <span>Average Rating</span>
                </div>
            </div>
        </div>

        <div class="lk-hero-showcase">
            <div class="lk-hero-product">
                @if ($heroImage)
                    <img
                        src="{{ $heroImage }}"
                        alt="{{ $heroName }}"
                        decoding="async"
                    >
                @else
                    <div
                        class="flex h-full min-h-72 w-full items-center justify-center rounded-2xl bg-amber-50 text-amber-700"
                        aria-label="Featured product placeholder"
                    >
                        <svg
                            width="110"
                            height="110"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M6 8h12l1 13H5z"/>
                            <path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                            <path d="M9 14h6"/>
                        </svg>
                    </div>
                @endif
            </div>

            @if ($heroProduct)
                <div class="lk-hero-product-info">
                    <span>{{ $heroCategory }}</span>

                    <strong>{{ $heroName }}</strong>

                    @if ($heroPrice > 0)
                        <small>
                            ₱{{ number_format($heroPrice, 2) }}
                        </small>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- Quick actions --}}
    <section class="lk-section" aria-labelledby="quick-actions-heading">
        <div class="sr-only">
            <h2 id="quick-actions-heading">Quick Actions</h2>
        </div>

        <div class="lk-quick-grid">
            <a
                href="{{ route('buyer.orders') }}"
                class="lk-quick-card"
            >
                <div class="lk-quick-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M6 2h12l2 4v16H4V6z"/>
                        <path d="M6 6h12"/>
                        <path d="M8 11h8"/>
                        <path d="M8 15h6"/>
                    </svg>
                </div>

                <div>
                    <strong>My Orders</strong>
                    <small>Track and review</small>
                </div>
            </a>

            <a
                href="{{ $wishlistUrl }}"
                class="lk-quick-card"
                @if (!$hasWishlistRoute)
                    data-frontend-placeholder
                    onclick="return false;"
                @endif
            >
                <div class="lk-quick-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z"/>
                    </svg>
                </div>

                <div>
                    <strong>Wishlist</strong>
                    <small>Saved products</small>
                </div>
            </a>

            <a
                href="{{ route('buyer.messages') }}"
                class="lk-quick-card"
            >
                <div class="lk-quick-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M4 5h16v11H8l-4 4z"/>
                        <path d="M8 9h8"/>
                        <path d="M8 13h5"/>
                    </svg>
                </div>

                <div>
                    <strong>Messages</strong>
                    <small>Chat with sellers</small>
                </div>
            </a>

            <a
                href="#flash-picks"
                class="lk-quick-card"
            >
                <div class="lk-quick-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7"/>
                        <path d="M2 7h20v5H2z"/>
                        <path d="M12 7v14"/>
                        <path d="M12 7H7.5A2.5 2.5 0 1 1 10 4.5z"/>
                        <path d="M12 7h4.5A2.5 2.5 0 1 0 14 4.5z"/>
                    </svg>
                </div>

                <div>
                    <strong>Vouchers</strong>
                    <small>Deals and rewards</small>
                </div>
            </a>
        </div>
    </section>

    {{-- Categories --}}
    <section
        class="lk-section"
        aria-labelledby="categories-heading"
    >
        <div class="lk-section-head">
            <div>
                <span class="lk-kicker">Browse</span>

                <h2 id="categories-heading">
                    Shop by Category
                </h2>

                <p>
                    Explore products from different collections.
                </p>
            </div>

            <a
                href="{{ route('buyer.products') }}"
                class="lk-text-link"
            >
                View All
            </a>
        </div>

        <div class="lk-category-grid">
            @foreach ($categories as $category)
                <a
                    href="{{ route('buyer.products', ['category' => $category['slug']]) }}"
                    class="lk-category-card"
                >
                    <div class="lk-category-icon">
                        <img
                            src="{{ $category['image'] }}"
                            alt=""
                            loading="lazy"
                            decoding="async"
                        >
                    </div>

                    <strong>{{ $category['name'] }}</strong>
                    <small>Explore</small>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured products --}}
    <section
        class="lk-section"
        aria-labelledby="featured-products-heading"
    >
        <div class="lk-section-head">
            <div>
                <span class="lk-kicker">Curated</span>

                <h2 id="featured-products-heading">
                    Featured Products
                </h2>

                <p>
                    Top picks from trusted sellers.
                </p>
            </div>

            <a
                href="{{ route('buyer.products', ['sort' => 'featured']) }}"
                class="lk-text-link"
            >
                See All
            </a>
        </div>

        @if ($featuredProducts->isNotEmpty())
            <div class="lk-product-grid">
                @foreach ($featuredProducts as $product)
                    <x-buyer.product-card :product="$product"/>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-50 text-amber-700">
                    <svg
                        width="27"
                        height="27"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M6 8h12l1 13H5z"/>
                        <path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                    </svg>
                </div>

                <h3 class="mt-4 text-sm font-semibold text-stone-900">
                    Featured products are coming soon
                </h3>

                <p class="mt-1 text-xs text-stone-500">
                    Products from trusted local sellers will appear here.
                </p>
            </div>
        @endif
    </section>

    {{-- Flash picks --}}
    <section
        id="flash-picks"
        class="lk-section scroll-mt-24"
        aria-labelledby="flash-picks-heading"
    >
        <div class="lk-deal-banner">
            <div>
                <span class="lk-kicker">
                    Limited Offers
                </span>

                <h2 id="flash-picks-heading">
                    Flash Picks
                </h2>

                <p>
                    Premium local finds with limited-time prices.
                </p>
            </div>

            <div
                class="lk-countdown"
                aria-label="Six hours, twenty-four minutes, and eighteen seconds remaining"
                data-flash-countdown
            >
                <div>
                    <b data-countdown-hours>06</b>
                    <small>Hours</small>
                </div>

                <span aria-hidden="true">:</span>

                <div>
                    <b data-countdown-minutes>24</b>
                    <small>Minutes</small>
                </div>

                <span aria-hidden="true">:</span>

                <div>
                    <b data-countdown-seconds>18</b>
                    <small>Seconds</small>
                </div>
            </div>

            <a
                href="{{ route('buyer.products', ['sort' => 'flash']) }}"
                class="lk-btn lk-btn-gold"
            >
                Shop Deals
            </a>
        </div>
    </section>

    {{-- Recommended products --}}
    <section
        class="lk-section"
        aria-labelledby="recommended-products-heading"
    >
        <div class="lk-section-head">
            <div>
                <span class="lk-kicker">For You</span>

                <h2 id="recommended-products-heading">
                    Recommended For You
                </h2>

                <p>
                    Products based on popular buyer choices.
                </p>
            </div>

            <a
                href="{{ route('buyer.products', ['sort' => 'recommended']) }}"
                class="lk-text-link"
            >
                See All
            </a>
        </div>

        @if ($recommendedProducts->isNotEmpty())
            <div class="lk-product-grid">
                @foreach ($recommendedProducts as $product)
                    <x-buyer.product-card :product="$product"/>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-10 text-center">
                <h3 class="text-sm font-semibold text-stone-900">
                    More recommendations will appear here
                </h3>

                <p class="mt-1 text-xs text-stone-500">
                    Explore products to help us prepare recommendations for you.
                </p>

                <a
                    href="{{ route('buyer.products') }}"
                    class="mt-4 inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                >
                    Explore Products
                </a>
            </div>
        @endif
    </section>
</div>
@endsection