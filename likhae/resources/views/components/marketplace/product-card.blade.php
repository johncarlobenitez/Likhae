@props([
    'product' => [],
])

@php
    $id = $product['id'] ?? 'product';
    $name = $product['name'] ?? 'Marketplace Product';
    $slug = $product['slug'] ?? $id;
    $image = $product['image'] ?? '/images/guest/products/headphones.svg';
    $seller = $product['seller'] ?? 'LIKHAE Seller';
    $category = $product['category'] ?? 'Others';
    $price = (float) ($product['price'] ?? 0);
    $oldPrice = isset($product['old_price']) ? (float) $product['old_price'] : null;
    $discount = $product['discount'] ?? null;
    $rating = $product['rating'] ?? 4.8;
    $reviews = $product['reviews'] ?? 0;
    $sold = $product['sold'] ?? 0;
@endphp

<article
    class="g-product-card"
    data-guest-product-card
    data-category="{{ $category }}"
    data-price="{{ $price }}"
    data-rating="{{ $rating }}"
    data-sold="{{ $sold }}"
    data-search="{{ strtolower($name . ' ' . $seller . ' ' . $category) }}"
>
    <a class="g-product-image" href="{{ url('/products/' . $slug) }}">
        <img src="{{ asset(ltrim($image, '/')) }}" alt="{{ $name }}" loading="lazy">
    </a>

    <div class="g-product-body">
        <a href="{{ url('/products/' . $slug) }}">
            <h3 class="g-product-name">{{ $name }}</h3>
        </a>

        <div class="g-product-seller">{{ $seller }}</div>

        <div class="g-price">
            <span class="g-price-current">₱{{ number_format($price, 2) }}</span>

            @if($oldPrice)
                <span class="g-price-old">₱{{ number_format($oldPrice, 2) }}</span>
            @endif

            @if($discount)
                <span class="g-discount">{{ $discount }}% OFF</span>
            @endif
        </div>

        <div class="g-product-meta">
            <span><span class="g-rating">★</span> {{ $rating }} ({{ $reviews }})</span>
            <span>{{ $sold }} sold</span>
        </div>
    </div>
</article>
