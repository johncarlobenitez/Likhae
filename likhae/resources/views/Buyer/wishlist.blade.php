@extends('layouts.buyer')

@section('title', 'Wishlist — LIKHAE')
@section('active', 'wishlist')

@section('content')
@php
    /*
    |--------------------------------------------------------------------------
    | Wishlist collection
    |--------------------------------------------------------------------------
    |
    | The backend should eventually provide $wishlistProducts.
    | During front-end development, the first four buyer products are used.
    |
    */

    if (isset($wishlistProducts)) {
        if (method_exists($wishlistProducts, 'items')) {
            $wishlist = collect(
                $wishlistProducts->items()
            );
        } else {
            $wishlist = collect($wishlistProducts);
        }
    } elseif (isset($buyerProducts)) {
        if (method_exists($buyerProducts, 'items')) {
            $wishlist = collect(
                $buyerProducts->items()
            )->take(4);
        } else {
            $wishlist = collect(
                $buyerProducts
            )->take(4);
        }
    } else {
        $wishlist = collect();
    }

    /*
    |--------------------------------------------------------------------------
    | Search and sorting
    |--------------------------------------------------------------------------
    */

    $search = trim(
        (string) request('search', '')
    );

    $allowedSorts = [
        'recent',
        'price-low',
        'price-high',
        'best-rated',
    ];

    $requestedSort = strtolower(
        (string) request('sort', 'recent')
    );

    $activeSort = in_array(
        $requestedSort,
        $allowedSorts,
        true
    ) ? $requestedSort : 'recent';

    $sortOptions = [
        'recent' => 'Recently Added',
        'price-low' => 'Price: Low to High',
        'price-high' => 'Price: High to Low',
        'best-rated' => 'Best Rated',
    ];

    $visibleWishlist = $wishlist->filter(
        function ($product) use ($search) {
            if ($search === '') {
                return true;
            }

            $categoryValue =
                data_get($product, 'category.name')
                ?? data_get($product, 'category')
                ?? '';

            $sellerValue =
                data_get($product, 'seller.store_name')
                ?? data_get($product, 'seller.shop_name')
                ?? data_get($product, 'seller.name')
                ?? data_get($product, 'seller')
                ?? '';

            $category = is_scalar($categoryValue)
                ? (string) $categoryValue
                : '';

            $seller = is_scalar($sellerValue)
                ? (string) $sellerValue
                : '';

            $searchableText = mb_strtolower(
                trim(
                    (string) data_get(
                        $product,
                        'name',
                        ''
                    ) . ' ' .
                    $category . ' ' .
                    $seller
                )
            );

            return str_contains(
                $searchableText,
                mb_strtolower($search)
            );
        }
    );

    $visibleWishlist = match ($activeSort) {
        'price-low' => $visibleWishlist->sortBy(
            fn ($product) =>
                (float) data_get($product, 'price', 0)
        ),

        'price-high' => $visibleWishlist->sortByDesc(
            fn ($product) =>
                (float) data_get($product, 'price', 0)
        ),

        'best-rated' => $visibleWishlist->sortByDesc(
            fn ($product) =>
                (float) data_get($product, 'rating', 0)
        ),

        default => $visibleWishlist->sortByDesc(
            fn ($product) =>
                data_get($product, 'wishlist_added_at')
                ?? data_get($product, 'created_at')
                ?? data_get($product, 'id', 0)
        ),
    };

    $visibleWishlist = $visibleWishlist->values();
@endphp

<div class="lk-page">
    {{-- Page heading --}}
    <div class="lk-page-title">
        <div>
            <span class="lk-kicker">
                Saved Products
            </span>

            <h1>Wishlist</h1>

            <p>
                Keep your favorite local products in one place and revisit them anytime.
            </p>
        </div>

        <a
            href="{{ route('buyer.products') }}"
            class="lk-btn lk-btn-light"
        >
            <svg
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path d="M6 8h12l1 13H5z"/>
                <path d="M9 10V6a3 3 0 0 1 6 0v4"/>
            </svg>

            Continue Shopping
        </a>
    </div>

    @if ($wishlist->isNotEmpty())
        {{-- Wishlist summary --}}
        <section class="lk-wishlist-summary">
            <div class="lk-wishlist-summary-icon">
                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z"/>
                </svg>
            </div>

            <div>
                <strong>
                    {{ $wishlist->count() }}
                    {{ $wishlist->count() === 1 ? 'saved product' : 'saved products' }}
                </strong>

                <span>
                    Product prices and availability may change while items are saved.
                </span>
            </div>

            <button
                type="button"
                class="lk-wishlist-clear"
                data-clear-wishlist
            >
                Clear Wishlist
            </button>
        </section>

        {{-- Wishlist toolbar --}}
        <section class="lk-wishlist-toolbar">
            <form
                method="GET"
                action="{{ route('buyer.wishlist') }}"
                class="lk-wishlist-search"
            >
                <input
                    type="hidden"
                    name="sort"
                    value="{{ $activeSort }}"
                >

                <div class="lk-wishlist-search-field">
                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search saved products..."
                    >

                    @if ($search !== '')
                        <a
                            href="{{ route('buyer.wishlist', ['sort' => $activeSort]) }}"
                            aria-label="Clear search"
                            class="lk-wishlist-search-clear"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="m6 6 12 12M18 6 6 18"/>
                            </svg>
                        </a>
                    @endif
                </div>

                <button
                    type="submit"
                    class="lk-btn lk-btn-dark"
                >
                    Search
                </button>
            </form>

            <form
                method="GET"
                action="{{ route('buyer.wishlist') }}"
                class="lk-wishlist-sort"
            >
                @if ($search !== '')
                    <input
                        type="hidden"
                        name="search"
                        value="{{ $search }}"
                    >
                @endif

                <label for="wishlist-sort">
                    Sort by
                </label>

                <select
                    id="wishlist-sort"
                    name="sort"
                >
                    @foreach ($sortOptions as $sortKey => $sortLabel)
                        <option
                            value="{{ $sortKey }}"
                            @selected($activeSort === $sortKey)
                        >
                            {{ $sortLabel }}
                        </option>
                    @endforeach
                </select>

                <button
                    type="submit"
                    class="lk-wishlist-sort-button"
                >
                    Apply
                </button>
            </form>
        </section>

        {{-- Result information --}}
        <div class="lk-wishlist-result">
            <span>
                Showing
                <strong>{{ $visibleWishlist->count() }}</strong>
                of
                <strong>{{ $wishlist->count() }}</strong>
                saved products
            </span>

            @if ($search !== '')
                <span>
                    Results for “{{ $search }}”
                </span>
            @endif
        </div>

        {{-- Wishlist products --}}
        @if ($visibleWishlist->isNotEmpty())
            <section
                class="lk-product-grid"
                aria-label="Saved products"
                data-wishlist-grid
            >
                @foreach ($visibleWishlist as $product)
                    <div
                        class="lk-wishlist-item"
                        data-wishlist-item
                        data-product-id="{{ data_get($product, 'id', data_get($product, 'slug', '')) }}"
                    >
                        <x-buyer.product-card
                            :product="$product"
                        />
                    </div>
                @endforeach
            </section>
        @else
            {{-- No search results --}}
            <section class="lk-card lk-wishlist-empty">
                <div class="lk-empty-icon">
                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                        <path d="M8.5 11h5"/>
                    </svg>
                </div>

                <h2>No matching saved products</h2>

                <p>
                    We could not find a wishlist product matching “{{ $search }}”.
                </p>

                <a
                    href="{{ route('buyer.wishlist') }}"
                    class="lk-btn lk-btn-light"
                >
                    Clear Search
                </a>
            </section>
        @endif
    @else
        {{-- Empty wishlist --}}
        <section class="lk-card lk-wishlist-empty">
            <div class="lk-empty-icon">
                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z"/>
                </svg>
            </div>

            <h2>Your wishlist is empty</h2>

            <p>
                Save products you love by selecting the heart icon on any product card.
            </p>

            <a
                href="{{ route('buyer.products') }}"
                class="lk-btn lk-btn-red"
            >
                Explore Products
            </a>
        </section>
    @endif
</div>
@endsection