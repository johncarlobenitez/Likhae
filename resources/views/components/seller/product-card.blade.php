@props([
    'product' => [],
])

@php
    $id = data_get($product, 'id', 'PRD-001');
    $name = data_get($product, 'name', 'Product');
    $sku = data_get($product, 'sku', $id);
    $category = data_get($product, 'category', 'General');
    $image = data_get($product, 'image');

    $price = (float) data_get($product, 'price', 0);
    $stock = (int) data_get($product, 'stock', 0);
    $sold = (int) data_get($product, 'sold', 0);
    $rating = (float) data_get($product, 'rating', 0);

    $status = data_get($product, 'status', 'Active');
    $statusKey = mb_strtolower((string) $status);

    $statusTone = match ($statusKey) {
        'active' => 'is-success',
        'draft', 'pending' => 'is-warning',
        'archived', 'inactive' => 'is-neutral',
        default => 'is-neutral',
    };

    $stockTone = match (true) {
        $stock <= 0 => 'is-danger',
        $stock <= 5 => 'is-warning',
        default => 'is-success',
    };

    $stockLabel = match (true) {
        $stock <= 0 => 'Out of stock',
        $stock <= 5 => 'Low stock',
        default => 'Healthy',
    };

    $placeholder = mb_strtoupper(mb_substr($name, 0, 1));
@endphp

<article
    class="sl-mobile-product"
    data-product-row
    data-product-name="{{ mb_strtolower($name . ' ' . $sku . ' ' . $category) }}"
    data-product-status="{{ $statusKey }}"
>
    <div class="sl-mobile-product-head">
        <div class="sl-product-cell">
            @if ($image)
                <img
                    src="{{ $image }}"
                    alt="{{ $name }}"
                    loading="lazy"
                >
            @else
                <span class="sl-product-placeholder">
                    {{ $placeholder }}
                </span>
            @endif

            <div>
                <strong>
                    {{ $name }}
                </strong>

                <small>
                    {{ $sku }}
                </small>
            </div>
        </div>

        <span class="sl-status {{ $statusTone }}">
            {{ $status }}
        </span>
    </div>

    <div class="sl-mobile-product-category">
        <span>
            {{ $category }}
        </span>

        <strong class="{{ $stockTone }}">
            {{ $stockLabel }}
        </strong>
    </div>

    <dl class="sl-mobile-metrics">
        <div>
            <dt>Price</dt>
            <dd>₱{{ number_format($price, 2) }}</dd>
        </div>

        <div>
            <dt>Stock</dt>
            <dd class="{{ $stockTone }}">
                {{ number_format($stock) }}
            </dd>
        </div>

        <div>
            <dt>Sold</dt>
            <dd>{{ number_format($sold) }}</dd>
        </div>

        <div>
            <dt>Rating</dt>
            <dd class="is-rating">
                ★ {{ number_format($rating, 1) }}
            </dd>
        </div>
    </dl>

    <div class="sl-row-actions">
        <a
            href="{{ route('seller.products', ['mode' => 'edit', 'product' => $id]) }}"
            class="sl-btn sl-btn-primary sl-btn-sm"
        >
            Edit
        </a>

        <button
            type="button"
            class="sl-btn sl-btn-soft sl-btn-sm"
            data-stock-update
            data-product="{{ $name }}"
        >
            Update Stock
        </button>

        <button
            type="button"
            class="sl-btn sl-btn-ghost sl-btn-sm"
            data-demo-action="{{ $statusKey === 'archived' ? 'Product restored.' : 'Product archived.' }}"
        >
            {{ $statusKey === 'archived' ? 'Restore' : 'Archive' }}
        </button>
    </div>
</article>