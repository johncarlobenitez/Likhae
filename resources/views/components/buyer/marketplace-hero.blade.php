@props(['guest' => false, 'products' => collect(), 'heroProduct' => null, 'browseUrl', 'categoriesUrl', 'ordersUrl' => null])

@php
    $heroImage = data_get($heroProduct, 'image_url') ?? data_get($heroProduct, 'image');
    $heroName = data_get($heroProduct, 'name', 'A local find');
    $category = data_get($heroProduct, 'category.name') ?? data_get($heroProduct, 'category') ?? 'Local find';
    $supportProduct = collect($products)->firstWhere('category', 'Beauty & Health') ?? collect($products)->skip(1)->first();
    $supportImage = data_get($supportProduct, 'image_url') ?? data_get($supportProduct, 'image');
    $supportCategory = data_get($supportProduct, 'category.name') ?? data_get($supportProduct, 'category') ?? 'Handpicked';
@endphp

<section class="lk-hero lk-hero--marketplace">
    <div class="lk-hero-copy">
        <span class="lk-kicker">Welcome to LIKHAE</span>
        <h1>Made for what<br>you <span>need.</span></h1>
        <p>Discover products across categories, compare choices from trusted sellers, and find the right fit for everyday needs.</p>

        <div class="lk-hero-actions">
            <a href="{{ $browseUrl }}" class="lk-btn lk-btn-red">Browse Products</a>
            <a href="{{ $categoriesUrl }}" class="lk-btn lk-btn-light">Explore Categories</a>
        </div>

        @if($guest)
            <a class="lk-hero-account-link" href="{{ Route::has('login') ? route('login') : url('/login') }}">Sign in to unlock full marketplace features <span aria-hidden="true">&rarr;</span></a>
        @else
            <div class="lk-hero-quick-links">
                <span>Buyer workspace</span>
                @if($ordersUrl)<a href="{{ $ordersUrl }}">My Orders</a>@endif
                <a href="{{ route('buyer.cart') }}">View Cart</a>
            </div>
        @endif

        <div class="lk-hero-trust" aria-label="Marketplace values">
            <span>Multiple Categories</span><i aria-hidden="true"></i><span>Trusted Sellers</span><i aria-hidden="true"></i><span>Secure Marketplace</span>
        </div>
    </div>

    <div class="lk-hero-showcase" aria-label="Featured marketplace categories">
        <div class="lk-hero-tag">Explore more categories</div>
        <div class="lk-hero-product">
            @if($heroImage)
                <img src="{{ $heroImage }}" alt="{{ $heroName }}" decoding="async">
            @else
                <div class="lk-hero-product-placeholder" aria-label="Featured local product"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 8h12l1 13H5z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/></svg></div>
            @endif
        </div>
        <div class="lk-hero-product-info"><span>{{ $category }}</span><strong>{{ $heroName }}</strong><small>Featured from the LIKHAE marketplace</small></div>
        @if($supportImage)
            <div class="lk-hero-support"><img src="{{ $supportImage }}" alt="{{ $supportCategory }} marketplace product" decoding="async"><span>{{ $supportCategory }}</span></div>
        @endif
    </div>
</section>
