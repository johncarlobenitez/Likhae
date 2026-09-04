@props(['product', 'compact' => false, 'guest' => false])

@php
    $id = data_get($product, 'id');
    $slug = data_get($product, 'slug', $id);
    $name = (string) data_get($product, 'name', 'Product Name');
    $categoryValue = data_get($product, 'category.name') ?? data_get($product, 'category', 'Local Find');
    $category = is_scalar($categoryValue) ? (string) $categoryValue : 'Local Find';
    $sellerValue = data_get($product, 'seller.store_name') ?? data_get($product, 'seller.name') ?? data_get($product, 'seller', 'LIKHAE Seller');
    $seller = is_scalar($sellerValue) ? (string) $sellerValue : 'LIKHAE Seller';
    $sellerSlug = data_get($product, 'seller_slug', \Illuminate\Support\Str::slug($seller));
    $price = max(0, (float) data_get($product, 'price', 0));
    $oldPrice = max(0, (float) data_get($product, 'old_price', 0));
    $rating = min(5, max(0, (float) data_get($product, 'rating', 4.8)));
    $reviews = max(0, (int) data_get($product, 'reviews', 0));
    $sold = max(0, (int) data_get($product, 'sold', 0));
    $image = data_get($product, 'image_url') ?? data_get($product, 'image') ?? 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80';
    $discount = $oldPrice > $price && $oldPrice > 0 ? (int) round((($oldPrice - $price) / $oldPrice) * 100) : (int) data_get($product, 'discount', 0);
    $detailsUrl = $guest ? route('products.show', ['slug' => $slug]) : route('buyer.product-details', ['slug' => $slug]);
    $storeUrl = $guest ? route('products.show', ['slug' => $slug]) : route('buyer.shop', ['seller' => $sellerSlug]);
@endphp

<article class="lk-product-card {{ $compact ? 'is-compact' : '' }} {{ $guest ? 'is-guest' : '' }}" data-product-card data-product-id="{{ $id ?? $slug }}" data-category="{{ \Illuminate\Support\Str::slug($category) }}" data-price="{{ $price }}" data-rating="{{ $rating }}" data-sold="{{ $sold }}" data-search="{{ mb_strtolower($name.' '.$category.' '.$seller) }}">
    <div class="lk-product-media">
        <a href="{{ $detailsUrl }}" class="lk-product-image-link" aria-label="View {{ $name }}"><img src="{{ $image }}" alt="{{ $name }}" loading="lazy" decoding="async"></a>
        @if($discount > 0)<span class="lk-product-discount">-{{ min(100, $discount) }}%</span>@endif
        <button type="button" class="lk-product-heart" @if($guest) data-auth-required data-auth-message="Sign in to save {{ $name }} to your wishlist." @else data-wishlist data-product-id="{{ $id ?? $slug }}" aria-pressed="false" @endif aria-label="Save {{ $name }} to wishlist">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z"/></svg>
        </button>
    </div>
    <div class="lk-product-body">
        <span class="lk-product-category">{{ $category }}</span>
        <a href="{{ $detailsUrl }}" class="lk-product-title-link"><h3 class="lk-product-name">{{ $name }}</h3></a>
        <a href="{{ $storeUrl }}" class="lk-product-seller">{{ $seller }}</a>
        <div class="lk-product-meta"><span class="lk-stars"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2Z"/></svg><strong>{{ number_format($rating, 1) }}</strong><small>({{ number_format($reviews) }})</small></span><span class="lk-product-sold">{{ number_format($sold) }} sold</span></div>
        <div class="lk-product-price-row"><strong>₱{{ number_format($price, 2) }}</strong>@if($oldPrice > $price)<del>₱{{ number_format($oldPrice, 2) }}</del>@endif</div>
        <div class="lk-card-actions">
            <a href="{{ $detailsUrl }}" class="lk-btn-view">View</a>
            @if($guest)
                <button type="button" class="lk-add-cart" data-auth-required data-auth-message="Sign in or create a Buyer account to add {{ $name }} to your cart."><svg viewBox="0 0 24 24"><path d="M6 6h15l-2 8H8zM6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg><span>Add to Cart</span></button>
            @else
                <a href="{{ route('buyer.cart', ['add' => $id ?? $slug]) }}" class="lk-add-cart"><svg viewBox="0 0 24 24"><path d="M6 6h15l-2 8H8zM6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg><span>Add to Cart</span></a>
            @endif
        </div>
    </div>
</article>
