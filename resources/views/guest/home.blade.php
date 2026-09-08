@extends('layouts.guest')

@section('title', 'Welcome to LIKHAE')

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
    $heroProduct = $products->firstWhere('slug', 'wireless-headphones') ?? $products->first();

    $heroImage = data_get($heroProduct, 'image_url')
        ?? data_get($heroProduct, 'image');

    $heroName = data_get(
        $heroProduct,
        'name',
        'Discover meaningful local products'
    );

    $heroPrice = 0;

    $heroCategoryValue = data_get($heroProduct, 'category.name')
        ?? data_get($heroProduct, 'category')
        ?? 'Featured Product';

    $heroCategory = is_scalar($heroCategoryValue)
        ? (string) $heroCategoryValue
        : 'Featured Product';

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    $categories = [
        [
            'name' => 'Fashion',
            'slug' => 'fashion',
            'icon' => '<path d="M8 4h8l4 4-3 3-2-2v11H9V9l-2 2-3-3 4-4Z"/>',
        ],
        [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'icon' => '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 7h6M10 17h4"/>',
        ],
        [
            'name' => 'Home',
            'slug' => 'home',
            'icon' => '<path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10M9 20v-6h6v6"/>',
        ],
        [
            'name' => 'Books',
            'slug' => 'books',
            'icon' => '<path d="M4 5a3 3 0 0 1 3-2h5v17H7a3 3 0 0 0-3 2V5Z"/><path d="M20 5a3 3 0 0 0-3-2h-5v17h5a3 3 0 0 1 3 2V5Z"/>',
        ],
        [
            'name' => 'Beauty',
            'slug' => 'beauty',
            'icon' => '<path d="M9 3h6v5l3 4v9H6v-9l3-4V3Z"/><path d="M9 8h6M9 14h6"/>',
        ],
        [
            'name' => 'Sports',
            'slug' => 'sports',
            'icon' => '<circle cx="12" cy="12" r="9"/><path d="M5 7c4 2 10 2 14 0M5 17c4-2 10-2 14 0M12 3c-3 5-3 13 0 18"/>',
        ],
        [
            'name' => 'Toys',
            'slug' => 'toys',
            'icon' => '<path d="M8 9V6a4 4 0 0 1 8 0v3"/><rect x="4" y="9" width="16" height="11" rx="2"/><path d="M9 14h6M12 11v6"/>',
        ],
        [
            'name' => 'Automotive',
            'slug' => 'automotive',
            'icon' => '<path d="m5 11 2-5h10l2 5"/><rect x="3" y="11" width="18" height="7" rx="2"/><circle cx="7" cy="18" r="1.5"/><circle cx="17" cy="18" r="1.5"/>',
        ],
    ];
@endphp

<div class="lk-page">
    {{-- Hero --}}
    <x-buyer.marketplace-hero
        guest
        :products="$products"
        :hero-product="$heroProduct"
        :browse-url="route('products')"
        categories-url="#categories-heading"
    />
    {{--
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
                    href="{{ route('products') }}"
                    class="lk-btn lk-btn-red"
                >
                    Browse Products
                </a>

                <a
                    href="#categories-heading"
                    class="lk-btn lk-btn-light"
                >
                    View Categories
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
                        class="flex h-full min-h-72 w-full items-center justify-center rounded-2xl bg-red-50 text-red-800"
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
                            â‚±{{ number_format($heroPrice, 2) }}
                        </small>
                    @endif
                </div>
            @endif
        </div>
    </section>
    --}}

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
                href="{{ route('products') }}"
                class="lk-text-link"
            >
                View All
            </a>
        </div>

        <div class="lk-category-grid">
            @foreach ($categories as $category)
                <a
                    href="{{ route('products', ['category' => $category['slug']]) }}"
                    class="lk-category-card"
                >
                    <div class="lk-category-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">{!! $category['icon'] !!}</svg>
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
                href="{{ route('products', ['sort' => 'featured']) }}"
                class="lk-text-link"
            >
                See All
            </a>
        </div>

        @if ($featuredProducts->isNotEmpty())
            <div class="lk-product-grid">
                @foreach ($featuredProducts as $product)
                    @include('guest.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-red-800">
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
                href="{{ route('products', ['sort' => 'best-selling']) }}"
                class="lk-btn lk-btn-gold"
            >
                View Deals
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
                href="{{ route('products', ['sort' => 'best-rated']) }}"
                class="lk-text-link"
            >
                See All
            </a>
        </div>

        @if ($recommendedProducts->isNotEmpty())
            <div class="lk-product-grid">
                @foreach ($recommendedProducts as $product)
                    @include('guest.partials.product-card', ['product' => $product])
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
                    href="{{ route('products') }}"
                    class="mt-4 inline-flex items-center justify-center rounded-xl bg-red-900 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-red-950"
                >
                    Explore Products
                </a>
            </div>
        @endif
    </section>
</div>
@endsection
