@props(['buyer' => false, 'slug' => 'linen-lounge-set'])
@php
$allProducts = $likhaeProducts ?? \App\Support\MarketplaceData::all();
$product = collect($allProducts)->firstWhere('slug', $slug) ?? $allProducts[0];
$gallery = $product['gallery'] ?? [$product['image']];
@endphp
<section class="lk-product-page lk-container" data-product-detail data-product-id="{{ $product['id'] }}" data-product-slug="{{ $product['slug'] }}" data-product-name="{{ $product['name'] }}" data-product-price="{{ $product['price'] }}" data-product-image="{{ $product['image'] }}" data-product-maker="{{ $product['maker'] }}" data-product-location="{{ $product['location'] }}">
    <div class="lk-gallery">
        <div class="lk-gallery__thumbs">
            @foreach($gallery as $i => $img)
                <button type="button" class="{{ $i === 0 ? 'is-active' : '' }}" data-gallery-thumb="{{ $i }}" aria-label="View photo {{ $i + 1 }}">
                    <img src="{{ $img }}" alt="{{ $product['name'] }} thumbnail {{ $i + 1 }}">
                </button>
            @endforeach
        </div>
        <button class="lk-gallery__main" type="button" data-gallery-open aria-label="Click to zoom image">
            <img src="{{ $gallery[0] }}" alt="{{ $product['name'] }}" data-gallery-main>
            <span class="lk-gallery__count" data-gallery-count>1 / {{ count($gallery) }}</span>
        </button>
    </div>
    <div class="lk-product-info">
        <p class="lk-eyebrow">{{ strtoupper($product['maker']) }} · {{ strtoupper($product['location']) }}</p>
        <h1>{{ $product['name'] }}</h1>
        <div class="lk-product-info__rating">
            <span>★★★★★</span>
            <a href="#reviews">{{ $product['rating'] }} · {{ $product['reviews'] }} reviews</a>
            <span>{{ $product['sold'] }} sold</span>
        </div>
        <div class="lk-product-info__price">
            <strong>₱{{ number_format($product['price']) }}</strong>
            @if(!empty($product['compareAt']))
                <del>₱{{ number_format($product['compareAt']) }}</del>
                <span>Save {{ round((1 - ($product['price'] / $product['compareAt'])) * 100) }}%</span>
            @endif
        </div>
        <p class="lk-stock" data-stock-copy>
            @if($product['stock'] <= 3)
                Only {{ $product['stock'] }} left in this variation
            @else
                In stock · {{ $product['stock'] }} available
            @endif
        </p>
        <p class="lk-lead">{{ $product['description'] }}</p>

        @if(!empty($product['hasColors']) && !empty($product['colors']))
            <div class="lk-option">
                <div class="lk-option__label">
                    <strong>Color</strong>
                    <span data-color-label>{{ $product['colors'][0]['name'] ?? 'Standard' }}</span>
                </div>
                <div class="lk-swatches">
                    @foreach($product['colors'] as $c)
                        <button type="button" 
                            class="{{ !empty($c['active']) ? 'is-active' : '' }}" 
                            aria-label="{{ $c['name'] }}" 
                            data-color="{{ $c['name'] }}" 
                            data-stock="{{ $c['stock'] }}" 
                            {{ !empty($c['disabled']) ? 'disabled' : '' }} 
                            style="--swatch:{{ $c['hex'] }}">
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!empty($product['hasSizes']) && !empty($product['sizes']))
            <div class="lk-option">
                <div class="lk-option__label">
                    <strong>Size</strong>
                    <button class="lk-text-link" type="button" data-size-guide>Size guide</button>
                </div>
                <div class="lk-sizes">
                    @foreach($product['sizes'] as $size)
                        @php $isDisabled = in_array($size, $product['disabledSizes'] ?? []); @endphp
                        <button type="button" 
                            data-size="{{ $size }}" 
                            {{ $isDisabled ? 'disabled' : '' }} 
                            title="{{ $isDisabled ? 'Sold out in '.$size : 'Select '.$size }}">
                            {{ $size }}
                        </button>
                    @endforeach
                </div>
                <p class="lk-field-error" data-variation-error></p>
            </div>
        @endif

        <div class="lk-option">
            <div class="lk-option__label">
                <strong>Quantity</strong>
                <span data-available-copy>{{ $product['stock'] }} pieces available</span>
            </div>
            <div class="lk-quantity">
                <button type="button" data-qty-minus aria-label="Decrease quantity">−</button>
                <input value="1" inputmode="numeric" aria-label="Quantity" data-qty readonly>
                <button type="button" data-qty-plus aria-label="Increase quantity">+</button>
            </div>
        </div>

        <div class="lk-purchase-actions">
            <button class="lk-btn lk-btn--primary lk-btn--wide" type="button" data-add-cart data-requires-buyer="{{ $buyer ? 'false' : 'true' }}">
                {{ $buyer ? 'Add to Bag' : 'Sign in to Add to Bag' }}
            </button>
            <button class="lk-btn lk-btn--secondary lk-btn--wide" type="button" data-buy-now data-requires-buyer="{{ $buyer ? 'false' : 'true' }}">
                Buy Now
            </button>
            <button class="lk-save-link" type="button" data-wishlist-toggle data-product-id="{{ $product['id'] }}" data-requires-buyer="{{ $buyer ? 'false' : 'true' }}">
                ♡ Add to Wishlist
            </button>
        </div>

        <div class="lk-accordions">
            <details open>
                <summary>Description & Story</summary>
                <p>{{ $product['description'] }} Handcrafted by independent artisans using time-tested methods rooted in local heritage.</p>
            </details>
            <details>
                <summary>Specifications</summary>
                <dl>
                    <div><dt>Material</dt><dd>{{ $product['material'] ?? 'Local botanical & natural textiles' }}</dd></div>
                    <div><dt>Fit / Scale</dt><dd>{{ $product['fit'] ?? 'Standard' }}</dd></div>
                    <div><dt>Origin</dt><dd>{{ $product['origin'] ?? $product['location'].', Philippines' }}</dd></div>
                    <div><dt>Care</dt><dd>{{ $product['care'] ?? 'Handle with care; follow garment label instructions' }}</dd></div>
                    <div><dt>Maker Studio</dt><dd>{{ $product['maker'] }}</dd></div>
                </dl>
            </details>
            <details>
                <summary>Shipping & Returns</summary>
                <p>Ships directly from {{ $product['location'] }} in 1–3 business days. Metro Manila delivery takes 3–5 days; provincial delivery takes 4–7 days. 7-day return policy for unused items in original packaging.</p>
            </details>
            <details>
                <summary>Seller Information</summary>
                <p><strong>{{ $product['maker'] }}</strong> · Local Maker · {{ $product['location'] }} · ★ 4.9 · 99% positive feedback</p>
                <button class="lk-text-link" type="button" data-requires-buyer="{{ $buyer ? 'false' : 'true' }}">Message Maker →</button>
            </details>
        </div>
    </div>
</section>

<section class="lk-section lk-section--tint" id="reviews">
    <div class="lk-container">
        <div class="lk-reviews-head">
            <div>
                <span class="lk-kicker">VERIFIED REVIEWS</span>
                <h2>{{ $product['rating'] }} <span>/ 5</span></h2>
                <p>★★★★★ · {{ $product['reviews'] }} verified customer reviews</p>
            </div>
            <div class="lk-rating-bars">
                <div><span>5 ★</span><i><b style="width:82%"></b></i><small>82%</small></div>
                <div><span>4 ★</span><i><b style="width:14%"></b></i><small>14%</small></div>
                <div><span>3 ★</span><i><b style="width:3%"></b></i><small>3%</small></div>
                <div><span>2 ★</span><i><b style="width:1%"></b></i><small>1%</small></div>
                <div><span>1 ★</span><i><b style="width:0%"></b></i><small>0%</small></div>
            </div>
        </div>
        <div class="lk-review-filters">
            <button type="button" class="is-active">All</button>
            <button type="button">5 stars</button>
            <button type="button">4 stars</button>
            <button type="button">With photos</button>
            <button type="button">Most helpful</button>
            <button type="button">Newest</button>
        </div>
        <div class="lk-review-list">
            <article>
                <div class="lk-avatar">ML</div>
                <div>
                    <strong>Maria L.</strong>
                    <p class="lk-stars">★★★★★ <span>Verified Purchase</span></p>
                    <small>{{ $product['maker'] }} · Aug 18, 2026</small>
                    <p>The materials feel remarkably premium and the craftsmanship is evident down to the smallest seams. Arrived thoughtfully packaged in recycled paper.</p>
                    <button class="lk-text-link" type="button">Helpful (14)</button>
                </div>
            </article>
            <article>
                <div class="lk-avatar">AC</div>
                <div>
                    <strong>Ana C.</strong>
                    <p class="lk-stars">★★★★★ <span>Verified Purchase</span></p>
                    <small>{{ $product['location'] }} order · Aug 11, 2026</small>
                    <p>Everything about this piece feels intentional. Supporting Filipino makers while getting such high quality is why I keep coming back to LIKHAE.</p>
                    <button class="lk-text-link" type="button">Helpful (9)</button>
                </div>
            </article>
        </div>
    </div>
</section>

{{-- Lightbox Zoom Modal --}}
<div class="lk-modal" data-gallery-modal hidden>
    <div class="lk-modal__backdrop" data-close-gallery></div>
    <div class="lk-lightbox">
        <button type="button" data-close-gallery aria-label="Close lightbox">×</button>
        <img src="{{ $gallery[0] }}" alt="Expanded product view" data-lightbox-image>
    </div>
</div>

{{-- Size Guide Modal --}}
<div class="lk-modal" data-size-modal hidden>
    <div class="lk-modal__backdrop" data-close-size></div>
    <section class="lk-modal__panel" role="dialog" aria-modal="true" aria-label="Size Guide">
        <button class="lk-modal__close" data-close-size aria-label="Close size guide">×</button>
        <span class="lk-kicker">FIT & SIZING NOTES</span>
        <h2>Size Guide</h2>
        <p style="color:var(--slate);font-size:0.85rem;margin-bottom:18px">All measurements are in inches. Designed with relaxed ease for Philippine climate.</p>
        <table class="lk-size-table">
            <thead>
                <tr><th>Size</th><th>Bust</th><th>Waist</th><th>Hip</th></tr>
            </thead>
            <tbody>
                <tr><td>XS</td><td>31–32</td><td>24–25</td><td>34–35</td></tr>
                <tr><td>S</td><td>33–34</td><td>26–27</td><td>36–37</td></tr>
                <tr><td>M</td><td>35–36</td><td>28–29</td><td>38–39</td></tr>
                <tr><td>L</td><td>37–39</td><td>30–32</td><td>40–42</td></tr>
                <tr><td>XL</td><td>40–42</td><td>33–35</td><td>43–45</td></tr>
            </tbody>
        </table>
    </section>
</div>
