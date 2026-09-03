@props([
    'product',
    'compact' => false,
])

@php
    $id = data_get($product, 'id');
    $slug = data_get($product, 'slug');
    $name = (string) data_get($product, 'name', 'Product Name');

    $categoryValue = data_get($product, 'category.name')
        ?? data_get($product, 'category');

    $category = is_scalar($categoryValue)
        ? (string) $categoryValue
        : 'Local Find';

    $sellerValue = data_get($product, 'seller.store_name')
        ?? data_get($product, 'seller.shop_name')
        ?? data_get($product, 'seller.name')
        ?? data_get($product, 'seller');

    $seller = is_scalar($sellerValue)
        ? (string) $sellerValue
        : 'LIKHAE Seller';

    $price = max(0, (float) data_get($product, 'price', 0));
    $oldPrice = max(0, (float) data_get($product, 'old_price', 0));

    $rating = min(5, max(0, (float) data_get($product, 'rating', 4.8)));
    $reviews = max(0, (int) data_get($product, 'reviews', 0));
    $sold = max(0, (int) data_get($product, 'sold', 0));

    $image = data_get($product, 'image_url')
        ?? data_get($product, 'image')
        ?? asset('images/buyer/products/backpack.svg');
    $fallbackImage = asset('images/buyer/products/backpack.svg');

    $calculatedDiscount = $oldPrice > $price && $oldPrice > 0
        ? round((($oldPrice - $price) / $oldPrice) * 100)
        : 0;

    $providedDiscount = (float) data_get($product, 'discount', 0);

    $discount = (int) min(
        100,
        max(0, $calculatedDiscount ?: $providedDiscount)
    );

    $isWishlisted = (bool) data_get($product, 'is_wishlisted', false);

    $detailsUrl = filled($slug)
        ? route('buyer.product-details', ['slug' => $slug])
        : (filled($id)
            ? route('buyer.product-details', ['slug' => $id])
            : '#');

    $searchText = mb_strtolower(
        trim($name . ' ' . $category . ' ' . $seller)
    );
@endphp

<article
    class="lk-product-card {{ $compact ? 'is-compact' : '' }}"
    data-product-card
    data-product-id="{{ $id ?? $slug ?? '' }}"
    data-category="{{ mb_strtolower($category) }}"
    data-price="{{ $price }}"
    data-rating="{{ $rating }}"
    data-sold="{{ $sold }}"
    data-search="{{ $searchText }}"
>
    <div class="lk-product-media">
        <button type="button" class="lk-quick-view-btn" data-quick-view
                aria-label="View full details {{ $name }}">View Full Details</button>
        <a
            href="{{ $detailsUrl }}"
            class="lk-product-image-link"
            aria-label="View {{ $name }}"
        >
            <img
                src="{{ $image }}"
                alt="{{ $name }}"
                loading="lazy"
                decoding="async"
                onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
            >
        </a>

        @if ($discount > 0)
            <span class="lk-product-discount">
                -{{ $discount }}%
            </span>
        @endif

        <button
            type="button"
            class="lk-product-heart {{ $isWishlisted ? 'is-active' : '' }}"
            data-wishlist
            data-product-id="{{ $id ?? $slug ?? '' }}"
            aria-label="{{ $isWishlisted ? 'Remove' : 'Add' }} {{ $name }} {{ $isWishlisted ? 'from' : 'to' }} wishlist"
            aria-pressed="{{ $isWishlisted ? 'true' : 'false' }}"
        >
            <svg
                width="19"
                height="19"
                viewBox="0 0 24 24"
                fill="{{ $isWishlisted ? 'currentColor' : 'none' }}"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z"/>
            </svg>
        </button>
    </div>

    <div class="lk-product-body">
        <span class="lk-product-category">
            {{ $category }}
        </span>

        <a
            href="{{ $detailsUrl }}"
            class="lk-product-title-link"
        >
            <h3 class="lk-product-name">
                {{ $name }}
            </h3>
        </a>

        <p class="lk-product-seller">
            {{ $seller }}
        </p>

        <div class="lk-product-meta">
            <span class="lk-stars">
                <svg
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    stroke="currentColor"
                    stroke-width="1.5"
                    aria-hidden="true"
                >
                    <path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2Z"/>
                </svg>

                <strong>{{ number_format($rating, 1) }}</strong>

                <small>({{ number_format($reviews) }})</small>
            </span>

            <span class="lk-product-sold">
                {{ number_format($sold) }} sold
            </span>
        </div>

        <div class="lk-product-price-row">
            <strong>
                ₱{{ number_format($price, 2) }}
            </strong>

            @if ($oldPrice > $price)
                <del>
                    ₱{{ number_format($oldPrice, 2) }}
                </del>
            @endif
        </div>

        <button
            type="button"
            class="lk-add-cart"
            data-add-cart
            data-product-id="{{ $id ?? $slug ?? '' }}"
            aria-label="Add {{ $name }} to cart"
            @disabled(blank($id) && blank($slug))
        >
            <svg
                width="17"
                height="17"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.9"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M6 6h15l-2 8H8z"/>
                <path d="M6 6 5 3H2"/>
                <circle cx="9" cy="20" r="1"/>
                <circle cx="18" cy="20" r="1"/>
            </svg>

            <span>Add to Cart</span>
        </button>
    </div>
</article>