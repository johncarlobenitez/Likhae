@include('guest.product-data')

@php
    $currentSlug = $slug ?? request()->route('slug') ?? 'wireless-headphones';

    $product = collect($guestProducts)->firstWhere('slug', $currentSlug)
        ?? $guestProducts[0];

    $gallery = [
        $product['image'],
        '/images/guest/products/camera.svg',
        '/images/guest/products/watch.svg',
        '/images/guest/products/backpack.svg',
    ];
@endphp

<x-marketplace.layout :title="$product['name']">
    <div
        class="g-page"
        data-guest-product-detail
        data-stock="{{ $product['stock'] }}"
    >
        <div class="g-container">
            <nav class="g-breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <span>›</span>
                <a href="{{ url('/products') }}">Products</a>
                <span>›</span>
                <a href="{{ url('/products') }}?category={{ urlencode($product['category']) }}">
                    {{ $product['category'] }}
                </a>
                <span>›</span>
                <span>{{ $product['name'] }}</span>
            </nav>

            <section class="g-product-layout">
                <div class="g-gallery">
                    <div class="g-thumbnails">
                        @foreach($gallery as $index => $image)
                            <button
                                class="g-thumbnail {{ $index === 0 ? 'is-active' : '' }}"
                                type="button"
                                data-guest-thumbnail
                                data-image="{{ asset(ltrim($image, '/')) }}"
                                data-alt="{{ $product['name'] }} product view {{ $index + 1 }}"
                                aria-label="Show product image {{ $index + 1 }}"
                            >
                                <img
                                    src="{{ asset(ltrim($image, '/')) }}"
                                    alt=""
                                >
                            </button>
                        @endforeach
                    </div>

                    <div class="g-main-image">
                        <img
                            src="{{ asset(ltrim($product['image'], '/')) }}"
                            alt="{{ $product['name'] }}"
                            data-guest-main-image
                        >
                    </div>
                </div>

                <aside class="g-card g-product-panel">
                    <span class="g-product-kicker">{{ $product['category'] }}</span>

                    <h1>{{ $product['name'] }}</h1>

                    <div class="g-product-stats">
                        <span><span class="g-rating">★</span> {{ $product['rating'] }}</span>
                        <span>{{ $product['reviews'] }} reviews</span>
                        <span>{{ $product['sold'] }} sold</span>
                    </div>

                    <div class="g-price">
                        <span class="g-price-current">₱{{ number_format($product['price'], 2) }}</span>
                        <span class="g-price-old">₱{{ number_format($product['old_price'], 2) }}</span>
                        <span class="g-discount">{{ $product['discount'] }}% OFF</span>
                    </div>

                    <div class="g-stock">
                        In stock · {{ $product['stock'] }} available
                    </div>

                    <div class="g-option-group" data-option-group>
                        <strong>Color</strong>

                        <div class="g-options">
                            <button class="g-option is-selected" type="button" data-option>Black</button>
                            <button class="g-option" type="button" data-option>White</button>
                            <button class="g-option" type="button" data-option>Blue</button>
                            <button class="g-option" type="button" data-option disabled>Beige</button>
                        </div>
                    </div>

                    <div class="g-option-group" data-option-group>
                        <strong>Size / Type</strong>

                        <div class="g-options">
                            <button class="g-option is-selected" type="button" data-option>Standard</button>
                            <button class="g-option" type="button" data-option>Premium</button>
                        </div>
                    </div>

                    <div class="g-option-group">
                        <strong>Quantity</strong>

                        <div class="g-qty">
                            <button type="button" data-quantity-minus aria-label="Decrease quantity">−</button>
                            <input
                                type="text"
                                inputmode="numeric"
                                value="1"
                                readonly
                                aria-label="Quantity"
                                data-product-quantity
                            >
                            <button type="button" data-quantity-plus aria-label="Increase quantity">+</button>
                        </div>
                    </div>

                    <div class="g-product-actions">
                        <button class="g-btn g-btn-secondary" type="button" data-guest-add-cart>
                            Add to Cart
                        </button>

                        <button class="g-btn g-btn-primary" type="button" data-guest-buy-now>
                            Buy Now
                        </button>
                    </div>

                    <div class="g-secondary-actions">
                        <button class="g-btn g-btn-ghost" type="button" data-guest-wishlist>
                            ♡ Add to Wishlist
                        </button>

                        <button class="g-btn g-btn-ghost" type="button" data-guest-chat>
                            Message Seller
                        </button>
                    </div>
                </aside>
            </section>

            <section class="g-product-content">
                <div class="g-tabs" role="tablist">
                    <button class="g-tab is-active" type="button" data-product-tab="description">Description</button>
                    <button class="g-tab" type="button" data-product-tab="specifications">Specifications</button>
                    <button class="g-tab" type="button" data-product-tab="reviews">Reviews</button>
                    <button class="g-tab" type="button" data-product-tab="shipping">Shipping & Returns</button>
                </div>

                <div class="g-tab-panel is-active" data-product-panel="description">
                    <h2 class="g-section-title" style="font-size:24px">Made for everyday use</h2>
                    <p class="g-muted" style="max-width:850px;line-height:1.75">
                        {{ $product['name'] }} combines practical everyday performance with a clean,
                        modern design. The product listing includes clear pricing, variation selection,
                        seller information, stock availability, and buyer-focused purchase details.
                    </p>
                </div>

                <div class="g-tab-panel" data-product-panel="specifications">
                    <div class="g-spec-table">
                        @foreach([
                            'Brand' => 'LIKHAE Select',
                            'Category' => $product['category'],
                            'Color' => 'Multiple options',
                            'Warranty' => 'Seller warranty where applicable',
                            'Seller Location' => $product['location'],
                            'Condition' => 'New',
                        ] as $label => $value)
                            <div class="g-spec-row">
                                <span>{{ $label }}</span>
                                <strong>{{ $value }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="g-tab-panel" data-product-panel="reviews">
                    <h2 class="g-section-title" style="font-size:24px">
                        {{ $product['rating'] }} out of 5
                    </h2>

                    <p class="g-muted">
                        Based on {{ $product['reviews'] }} buyer reviews.
                    </p>

                    <div class="g-card" style="padding:17px;margin-top:14px">
                        <strong>Verified Buyer</strong>
                        <div class="g-rating" style="margin:5px 0">★★★★★</div>
                        <p class="g-muted" style="margin-bottom:0;line-height:1.6">
                            Product arrived as described and was packed properly. The listing was easy
                            to understand and the variation matched the order.
                        </p>
                    </div>
                </div>

                <div class="g-tab-panel" data-product-panel="shipping">
                    <h2 class="g-section-title" style="font-size:24px">Shipping Information</h2>
                    <p class="g-muted" style="line-height:1.7">
                        Shipping options, estimated delivery dates, and fees will be shown during
                        checkout after you sign in. Eligible purchases are covered by LIKHAE buyer
                        protection.
                    </p>
                </div>
            </section>

            <section class="g-card g-seller-card">
                <div class="g-seller-info">
                    <div class="g-seller-avatar">MF</div>

                    <div>
                        <strong>{{ $product['seller'] }}</strong>
                        <small>
                            ★ 4.9 seller rating · 96% response rate · {{ $product['location'] }}
                        </small>
                    </div>
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <a class="g-btn g-btn-secondary" href="{{ route('login') }}">Chat Seller</a>
                    <a class="g-btn g-btn-primary" href="{{ url('/products') }}">Browse Products</a>
                </div>
            </section>
        </div>
    </div>
</x-marketplace.layout>
