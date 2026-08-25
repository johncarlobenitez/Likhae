@props(['product', 'buyer' => false])
<article class="lk-product-card" data-product-card data-product-id="{{ $product['id'] }}">
    <a class="lk-product-card__media" href="{{ $buyer ? route('buyer.product-details', $product['slug']) : url('/products/'.$product['slug']) }}">
        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
        @if(!empty($product['badge']))
            <span class="lk-badge {{ strtolower(str_replace(' ', '-', $product['badge'])) }}">{{ $product['badge'] }}</span>
        @endif
    </a>
    <div class="lk-product-card__meta">
        <div>
            <p class="lk-eyebrow">{{ $product['maker'] }} · {{ $product['location'] }}</p>
            <h3><a href="{{ $buyer ? route('buyer.product-details', $product['slug']) : url('/products/'.$product['slug']) }}">{{ $product['name'] }}</a></h3>
            <div class="lk-price-row">
                <strong>₱{{ number_format($product['price']) }}</strong>
                @if(!empty($product['compareAt']))
                    <del>₱{{ number_format($product['compareAt']) }}</del>
                @endif
            </div>
            <p class="lk-rating">★ {{ $product['rating'] }} <span>({{ $product['reviews'] }})</span></p>
        </div>
        <button class="lk-heart" type="button" aria-label="Save {{ $product['name'] }}" data-wishlist-toggle data-product-id="{{ $product['id'] }}" data-requires-buyer="{{ $buyer ? 'false' : 'true' }}">♡</button>
    </div>
</article>

