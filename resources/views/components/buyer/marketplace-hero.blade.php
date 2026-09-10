@props(['guest' => false, 'products' => collect(), 'heroProduct' => null, 'browseUrl', 'categoriesUrl', 'ordersUrl' => null])

@php
    $items = collect($products)->values();
    $heroProduct = $heroProduct ?: $items->first();
    $heroImage = data_get($heroProduct, 'image_url') ?? data_get($heroProduct, 'image');
    $heroName = data_get($heroProduct, 'name', 'Everyday marketplace find');
    $category = data_get($heroProduct, 'category.name') ?? data_get($heroProduct, 'category') ?? 'Featured Product';
    $supportProducts = $items->reject(fn ($product) => data_get($product, 'slug') === data_get($heroProduct, 'slug'))->take(3)->values();
@endphp

<section class="lk-hero lk-hero--marketplace {{ $guest ? 'is-guest' : 'is-buyer' }}">
    <div class="lk-hero-copy">
        <span class="lk-kicker">More than a marketplace</span>
        <h1>Discover Products<br>for Everyday Needs</h1>
        <p>LIKHAE connects buyers with trusted sellers, offering a convenient, secure, and community-centered shopping experience for everyday needs.</p>

        <div class="lk-hero-actions">
            <a href="{{ $guest ? $categoriesUrl : $browseUrl }}" class="lk-btn lk-btn-red">
                <span>{{ $guest ? 'Explore' : 'Shop Products' }}</span>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
            </a>
            @unless($guest)
                <a href="{{ $categoriesUrl }}" class="lk-btn lk-btn-light">Explore Categories</a>
            @endunless
        </div>

        @if($guest)
            <p class="lk-hero-line">&quot;Crafted for Everyday Needs&quot;</p>
        @else
            <div class="lk-hero-quick-links">
                <span>Buyer workspace</span>
                @if($ordersUrl)<a href="{{ $ordersUrl }}">My Orders</a>@endif
                <a href="{{ route('buyer.cart') }}">View Cart</a>
            </div>
        @endif
    </div>

    <div class="lk-hero-showcase" aria-label="Featured marketplace products">
        <p class="lk-hero-script">Every Need<br>Finds a<br>Clear Path</p>

        <div class="lk-hero-stage">
            <div class="lk-hero-bag" aria-hidden="true">
                <span class="lk-hero-bag-handle"></span>
                <span class="lk-hero-bag-logo">LIKHAE</span>
                <span class="lk-hero-bag-copy">Everyday<br>Looks Better<br>Here</span>
            </div>

            @if($heroImage)
                <a href="{{ $browseUrl }}" class="lk-hero-featured-product" aria-label="{{ $guest ? 'View' : 'Shop' }} {{ $heroName }}">
                    <img src="{{ $heroImage }}" alt="{{ $heroName }}" decoding="async">
                </a>
            @else
                <a href="{{ $browseUrl }}" class="lk-hero-featured-product lk-hero-featured-product--empty" aria-label="{{ $guest ? 'View' : 'Shop' }} featured products">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 8h12l1 13H5z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/></svg>
                </a>
            @endif

            @foreach($supportProducts as $index => $supportProduct)
                @php
                    $supportImage = data_get($supportProduct, 'image_url') ?? data_get($supportProduct, 'image');
                    $supportName = data_get($supportProduct, 'name', 'Marketplace product');
                @endphp

                @if($supportImage)
                    <span class="lk-hero-floating-product is-product-{{ $index + 1 }}">
                        <img src="{{ $supportImage }}" alt="{{ $supportName }}" decoding="async">
                    </span>
                @endif
            @endforeach

            <div class="lk-hero-plant" aria-hidden="true">
                <span></span><span></span><span></span><span></span>
            </div>
        </div>

        <div class="lk-hero-product-info">
            <span>{{ $category }}</span>
            <strong>{{ $heroName }}</strong>
            <small>Featured from the LIKHAE marketplace</small>
        </div>
    </div>
</section>
