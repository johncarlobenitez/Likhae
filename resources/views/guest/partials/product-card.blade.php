@php
    $id       = data_get($product, 'id');
    $slug     = data_get($product, 'slug', $id);
    $name     = (string) data_get($product, 'name', 'Product Name');
    $category = (string) (data_get($product, 'category.name') ?? data_get($product, 'category', 'Local Find'));
    $seller   = (string) (data_get($product, 'seller.store_name') ?? data_get($product, 'seller.name') ?? data_get($product, 'seller', 'LIKHAE Seller'));
    $image    = data_get($product, 'image_url') ?? data_get($product, 'image') ?? null;
    $price    = (float) (data_get($product, 'price') ?? data_get($product, 'base_price') ?? 0);
    $oldPrice = (float) (data_get($product, 'original_price') ?? data_get($product, 'compare_price') ?? 0);
    $rating   = (float) (data_get($product, 'rating') ?? data_get($product, 'average_rating') ?? 0);
    $sold     = (int)   (data_get($product, 'sold_count') ?? data_get($product, 'total_sold') ?? 0);
    $discount = ($oldPrice > $price && $price > 0) ? round((1 - $price / $oldPrice) * 100) : 0;
    $detailsUrl = route('products.show', ['slug' => $slug]);
@endphp

<article class="lk-product-card is-guest" data-product-card data-product-id="{{ $id ?? $slug }}" data-category="{{ \Illuminate\Support\Str::slug($category) }}" data-search="{{ mb_strtolower($name.' '.$category.' '.$seller) }}">
    <div class="lk-product-media">
        <a href="{{ $detailsUrl }}" class="lk-product-image-link" aria-label="View {{ $name }}">
            @if($image)
                <img src="{{ $image }}" alt="{{ $name }}" loading="lazy" decoding="async">
            @else
                <div class="lk-product-placeholder">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 8h12l1 13H5z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/></svg>
                </div>
            @endif
        </a>
        @if($discount > 0)
            <span class="lk-product-discount">-{{ $discount }}%</span>
        @endif
    </div>

    <div class="lk-product-body">
        <span class="lk-product-category">{{ $category }}</span>
        <a href="{{ $detailsUrl }}" class="lk-product-title-link"><h3 class="lk-product-name">{{ $name }}</h3></a>
        <span class="lk-product-seller">{{ $seller }}</span>

        @if($rating > 0 || $sold > 0)
            <div class="lk-product-meta">
                @if($rating > 0)
                    <span class="lk-stars">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        {{ number_format($rating, 1) }}
                    </span>
                @endif
                @if($sold > 0)
                    <span class="lk-product-sold">{{ $sold >= 1000 ? number_format($sold/1000,1).'k' : $sold }} sold</span>
                @endif
            </div>
        @endif

        <div class="lk-product-price-row">
            @if($price > 0)
                <strong>₱{{ number_format($price, 2) }}</strong>
                @if($oldPrice > $price)
                    <del>₱{{ number_format($oldPrice, 2) }}</del>
                @endif
            @endif
        </div>

        <div class="lk-card-actions" style="grid-template-columns: 1fr;">
            <a href="{{ $detailsUrl }}" class="lk-btn-view">View Details</a>
        </div>
    </div>
</article>
