@php
    $id       = data_get($product, 'id');
    $slug     = data_get($product, 'slug', $id);
    $name     = (string) data_get($product, 'name', 'Product Name');
    $category = (string) (data_get($product, 'category.name') ?? data_get($product, 'category', 'Local Find'));
    $seller   = (string) (data_get($product, 'seller.store_name') ?? data_get($product, 'seller.name') ?? data_get($product, 'seller', 'LIKHAE Seller'));
    $image    = data_get($product, 'image_url') ?? data_get($product, 'image') ?? null;
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
    </div>

    <div class="lk-product-body">
        <span class="lk-product-category">{{ $category }}</span>
        <a href="{{ $detailsUrl }}" class="lk-product-title-link"><h3 class="lk-product-name">{{ $name }}</h3></a>
        <span class="lk-product-seller">{{ $seller }}</span>

        <div class="lk-card-actions" style="grid-template-columns: 1fr;">
            <a href="{{ $detailsUrl }}" class="lk-btn-view">View Details</a>
        </div>
    </div>
</article>
