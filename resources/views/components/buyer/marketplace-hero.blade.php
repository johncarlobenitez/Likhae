@props([
    'guest' => false,
    'products' => collect(),
    'heroProduct' => null,
    'browseUrl',
    'categoriesUrl',
    'ordersUrl' => null,
])

@php
    $productsCollection = collect($products);

    $heroImage = data_get($heroProduct, 'image_url')
        ?? data_get($heroProduct, 'image');

    $heroName = data_get(
        $heroProduct,
        'name',
        'A local find'
    );

    $categoryValue = data_get($heroProduct, 'category.name')
        ?? data_get($heroProduct, 'category')
        ?? 'Local find';

    $category = is_scalar($categoryValue)
        ? (string) $categoryValue
        : 'Local find';

    $supportProduct = $productsCollection
        ->first(function ($product) {
            $categoryValue = data_get($product, 'category.name')
                ?? data_get($product, 'category');

            return is_scalar($categoryValue)
                && str_contains(strtolower((string) $categoryValue), 'beauty');
        })
        ?? $productsCollection->skip(1)->first();

    $supportImage = data_get($supportProduct, 'image_url')
        ?? data_get($supportProduct, 'image');

    $supportCategoryValue = data_get($supportProduct, 'category.name')
        ?? data_get($supportProduct, 'category')
        ?? 'Handpicked';

    $supportCategory = is_scalar($supportCategoryValue)
        ? (string) $supportCategoryValue
        : 'Handpicked';

    $loginUrl = Route::has('login')
        ? route('login')
        : url('/login');

    $hasBuyerCartRoute = Route::has('buyer.cart');

    $cartUrl = $hasBuyerCartRoute
        ? route('buyer.cart')
        : '#';
@endphp

@once
<style>
    :root {
        --lk-bg: #FBF7F2;
        --lk-bg-soft: #F6EFE7;
        --lk-bg-alt: #EFE7DE;
        --lk-card: #FFFDF9;

        --lk-border: #EADCCC;
        --lk-border-strong: #DBCEC1;

        --lk-maroon: #561C17;
        --lk-maroon-2: #642920;
        --lk-maroon-light: #7A2A22;
        --lk-maroon-dark: #3E130F;

        --lk-text: #3B211B;
        --lk-brown: #6C4936;
        --lk-muted: #987865;
        --lk-muted-2: #A99386;

        --lk-tan: #C19771;
        --lk-gold: #C88418;

        --lk-shadow-soft: 0 10px 28px rgba(86, 28, 23, 0.06);
        --lk-shadow-hero: 0 24px 60px rgba(86, 28, 23, 0.12);
    }

    .lk-hero.lk-hero--marketplace.lk-hero--inspo {
        position: relative !important;

        display: grid !important;
        grid-template-columns: minmax(0, 1.05fr) minmax(360px, 0.95fr) !important;
        align-items: center !important;
        gap: 34px !important;

        min-height: 540px !important;
        overflow: hidden !important;

        padding: clamp(28px, 4vw, 54px) !important;

        border: 1px solid var(--lk-border) !important;
        border-radius: 28px !important;

        background:
            radial-gradient(circle at 8% 18%, rgba(193, 151, 113, 0.20), transparent 28%),
            radial-gradient(circle at 92% 10%, rgba(86, 28, 23, 0.08), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%) !important;

        color: var(--lk-text) !important;

        box-shadow: var(--lk-shadow-hero) !important;
    }

    .lk-hero.lk-hero--marketplace.lk-hero--inspo::before {
        position: absolute !important;
        right: -90px !important;
        top: -110px !important;

        width: 320px !important;
        height: 320px !important;

        border-radius: 999px !important;
        background: rgba(193, 151, 113, 0.18) !important;

        content: "" !important;
        pointer-events: none !important;
    }

    .lk-hero.lk-hero--marketplace.lk-hero--inspo::after {
        position: absolute !important;
        left: 46% !important;
        bottom: -140px !important;

        width: 360px !important;
        height: 360px !important;

        border: 1px solid rgba(86, 28, 23, 0.10) !important;
        border-radius: 999px !important;

        content: "" !important;
        pointer-events: none !important;
    }

    .lk-hero--inspo .lk-hero-copy {
        position: relative !important;
        z-index: 2 !important;

        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        justify-content: center !important;

        padding: 0 !important;
    }

    .lk-hero--inspo .lk-kicker {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;

        margin: 0 0 16px !important;

        color: var(--lk-maroon) !important;

        font-size: 10px !important;
        font-weight: 900 !important;
        letter-spacing: 0.22em !important;
        text-transform: uppercase !important;
    }

    .lk-hero--inspo .lk-kicker::before {
        width: 28px !important;
        height: 1px !important;

        background: var(--lk-maroon) !important;

        content: "" !important;
    }

    .lk-hero--inspo h1 {
        max-width: 650px !important;
        margin: 0 !important;

        color: var(--lk-text) !important;

        font-family: "Instrument Serif", Georgia, serif !important;
        font-size: clamp(48px, 5.8vw, 86px) !important;
        font-weight: 400 !important;
        line-height: 0.88 !important;
        letter-spacing: -0.055em !important;
    }

    .lk-hero--inspo h1 span {
        color: var(--lk-maroon) !important;
        font-family: inherit !important;
        font-style: italic !important;
        font-weight: 400 !important;
    }

    .lk-hero--inspo .lk-hero-copy > p {
        max-width: 560px !important;
        margin: 22px 0 0 !important;

        color: var(--lk-muted) !important;

        font-size: 14px !important;
        line-height: 1.8 !important;
    }

    .lk-hero--inspo .lk-hero-actions {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 12px !important;

        margin-top: 28px !important;
    }

    .lk-hero--inspo .lk-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;

        min-height: 46px !important;
        padding: 0 20px !important;

        border-radius: 13px !important;

        font-size: 12px !important;
        font-weight: 900 !important;
        text-decoration: none !important;

        transition:
            transform 160ms ease,
            background 160ms ease,
            border-color 160ms ease,
            color 160ms ease !important;
    }

    .lk-hero--inspo .lk-btn:hover {
        transform: translateY(-1px) !important;
    }

    .lk-hero--inspo .lk-btn-red {
        border: 1px solid var(--lk-maroon) !important;
        background: var(--lk-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 24px rgba(86, 28, 23, 0.18) !important;
    }

    .lk-hero--inspo .lk-btn-red:hover {
        border-color: var(--lk-maroon-dark) !important;
        background: var(--lk-maroon-dark) !important;
    }

    .lk-hero--inspo .lk-btn-light {
        border: 1px solid var(--lk-tan) !important;
        background: rgba(255, 253, 249, 0.78) !important;
        color: var(--lk-maroon) !important;
    }

    .lk-hero--inspo .lk-btn-light:hover {
        border-color: var(--lk-maroon) !important;
        background: #F5ECEA !important;
        color: var(--lk-maroon-dark) !important;
    }

    .lk-hero--inspo .lk-hero-account-link {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;

        margin-top: 18px !important;

        color: var(--lk-maroon) !important;

        font-size: 12px !important;
        font-weight: 800 !important;
        text-decoration: none !important;
    }

    .lk-hero--inspo .lk-hero-account-link:hover {
        color: var(--lk-maroon-dark) !important;
        text-decoration: underline !important;
        text-underline-offset: 3px !important;
    }

    .lk-hero--inspo .lk-hero-quick-links {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 10px !important;

        margin-top: 18px !important;

        color: var(--lk-muted) !important;

        font-size: 11px !important;
        font-weight: 700 !important;
    }

    .lk-hero--inspo .lk-hero-quick-links span {
        color: var(--lk-muted) !important;
    }

    .lk-hero--inspo .lk-hero-quick-links a {
        color: var(--lk-maroon) !important;
        text-decoration: none !important;
    }

    .lk-hero--inspo .lk-hero-quick-links a:hover {
        color: var(--lk-maroon-dark) !important;
        text-decoration: underline !important;
        text-underline-offset: 3px !important;
    }

    .lk-hero--inspo .lk-hero-quick-links a + a::before {
        margin-right: 10px !important;
        color: var(--lk-border-strong) !important;
        content: "•" !important;
    }

    .lk-hero--inspo .lk-hero-trust {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 10px !important;

        margin-top: 30px !important;

        color: var(--lk-brown) !important;

        font-size: 10px !important;
        font-weight: 800 !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
    }

    .lk-hero--inspo .lk-hero-trust i {
        width: 5px !important;
        height: 5px !important;

        border-radius: 999px !important;
        background: var(--lk-tan) !important;
    }

    .lk-hero--inspo .lk-hero-showcase {
        position: relative !important;
        z-index: 2 !important;

        display: flex !important;
        min-height: 430px !important;
        align-items: center !important;
        justify-content: center !important;

        padding: 0 !important;
    }

    .lk-hero--inspo .lk-hero-showcase::before {
        position: absolute !important;
        width: min(88%, 440px) !important;
        height: min(88%, 440px) !important;

        border-radius: 46% 54% 52% 48% / 50% 42% 58% 50% !important;
        background: linear-gradient(135deg, #EADCCC, #F6EFE7) !important;

        content: "" !important;
        transform: rotate(-9deg) !important;
        pointer-events: none !important;
    }

    .lk-hero--inspo .lk-hero-tag {
        position: absolute !important;
        top: 26px !important;
        right: 26px !important;
        z-index: 5 !important;

        display: inline-flex !important;
        align-items: center !important;

        min-height: 34px !important;
        padding: 0 14px !important;

        border: 1px solid rgba(255, 253, 249, 0.64) !important;
        border-radius: 999px !important;

        background: var(--lk-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 12px 26px rgba(86, 28, 23, 0.18) !important;

        font-size: 10px !important;
        font-weight: 900 !important;
        letter-spacing: 0.08em !important;
        text-transform: uppercase !important;
    }

    .lk-hero--inspo .lk-hero-product {
        position: relative !important;
        z-index: 3 !important;

        width: min(100%, 390px) !important;
        aspect-ratio: 1 / 1.02 !important;
        overflow: hidden !important;

        padding: 10px !important;

        border: 1px solid rgba(234, 220, 204, 0.9) !important;
        border-radius: 28px !important;

        background: rgba(255, 253, 249, 0.86) !important;

        box-shadow: 0 24px 52px rgba(86, 28, 23, 0.16) !important;
        backdrop-filter: blur(10px) !important;
    }

    .lk-hero--inspo .lk-hero-product img {
        width: 100% !important;
        height: 100% !important;

        border-radius: 20px !important;

        object-fit: cover !important;
    }

    .lk-hero--inspo .lk-hero-product-placeholder {
        display: grid !important;
        width: 100% !important;
        height: 100% !important;
        place-items: center !important;

        border-radius: 20px !important;

        background:
            radial-gradient(circle at 50% 24%, rgba(255, 255, 255, 0.82), transparent 32%),
            linear-gradient(135deg, #F3ECE4, #EADCCC) !important;

        color: var(--lk-maroon) !important;
    }

    .lk-hero--inspo .lk-hero-product-placeholder svg {
        width: 96px !important;
        height: 96px !important;

        fill: none !important;
        stroke: currentColor !important;
        stroke-width: 1.2 !important;
        stroke-linecap: round !important;
        stroke-linejoin: round !important;
    }

    .lk-hero--inspo .lk-hero-product-info {
        position: absolute !important;
        right: 0 !important;
        bottom: 34px !important;
        z-index: 6 !important;

        display: grid !important;
        width: min(250px, 70%) !important;
        gap: 4px !important;

        padding: 15px 16px !important;

        border: 1px solid var(--lk-border) !important;
        border-radius: 17px !important;

        background: rgba(255, 253, 249, 0.94) !important;

        box-shadow: var(--lk-shadow-soft) !important;
        backdrop-filter: blur(12px) !important;
    }

    .lk-hero--inspo .lk-hero-product-info span {
        color: var(--lk-maroon) !important;

        font-size: 9px !important;
        font-weight: 900 !important;
        letter-spacing: 0.12em !important;
        text-transform: uppercase !important;
    }

    .lk-hero--inspo .lk-hero-product-info strong {
        overflow: hidden !important;

        color: var(--lk-text) !important;

        font-size: 13px !important;
        font-weight: 900 !important;
        line-height: 1.35 !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .lk-hero--inspo .lk-hero-product-info small {
        color: var(--lk-muted) !important;
        font-size: 10px !important;
        line-height: 1.45 !important;
    }

    .lk-hero--inspo .lk-hero-support {
        position: absolute !important;
        left: 8px !important;
        bottom: 50px !important;
        z-index: 6 !important;

        display: grid !important;
        grid-template-columns: 56px minmax(0, auto) !important;
        align-items: center !important;
        gap: 10px !important;

        max-width: 230px !important;
        padding: 8px 12px 8px 8px !important;

        border: 1px solid var(--lk-border) !important;
        border-radius: 16px !important;

        background: rgba(255, 253, 249, 0.94) !important;

        box-shadow: var(--lk-shadow-soft) !important;
        backdrop-filter: blur(12px) !important;
    }

    .lk-hero--inspo .lk-hero-support img {
        width: 56px !important;
        height: 56px !important;

        border-radius: 12px !important;

        object-fit: cover !important;
    }

    .lk-hero--inspo .lk-hero-support span {
        overflow: hidden !important;

        color: var(--lk-text) !important;

        font-size: 11px !important;
        font-weight: 900 !important;
        line-height: 1.3 !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .lk-hero--inspo .lk-hero-script {
        position: absolute !important;
        right: 20px !important;
        bottom: 4px !important;
        z-index: 2 !important;

        color: rgba(86, 28, 23, 0.30) !important;

        font-family: "Caveat", cursive !important;
        font-size: clamp(28px, 3vw, 44px) !important;
        line-height: 1 !important;

        transform: rotate(-7deg) !important;
        pointer-events: none !important;
    }

    @media (max-width: 1100px) {
        .lk-hero.lk-hero--marketplace.lk-hero--inspo {
            grid-template-columns: 1fr !important;
        }

        .lk-hero--inspo .lk-hero-showcase {
            min-height: 420px !important;
        }

        .lk-hero--inspo .lk-hero-product-info {
            right: 28px !important;
        }

        .lk-hero--inspo .lk-hero-support {
            left: 28px !important;
        }
    }

    @media (max-width: 640px) {
        .lk-hero.lk-hero--marketplace.lk-hero--inspo {
            padding: 24px 18px !important;
            border-radius: 22px !important;
        }

        .lk-hero--inspo h1 {
            font-size: clamp(42px, 13vw, 56px) !important;
        }

        .lk-hero--inspo .lk-hero-copy > p {
            font-size: 13px !important;
        }

        .lk-hero--inspo .lk-hero-actions {
            width: 100% !important;
        }

        .lk-hero--inspo .lk-hero-actions .lk-btn {
            width: 100% !important;
        }

        .lk-hero--inspo .lk-hero-showcase {
            min-height: 350px !important;
        }

        .lk-hero--inspo .lk-hero-product {
            width: min(100%, 300px) !important;
        }

        .lk-hero--inspo .lk-hero-tag {
            top: 12px !important;
            right: 12px !important;
        }

        .lk-hero--inspo .lk-hero-product-info {
            right: 0 !important;
            bottom: 12px !important;
            width: min(230px, 76%) !important;
        }

        .lk-hero--inspo .lk-hero-support {
            left: 0 !important;
            bottom: 76px !important;
            grid-template-columns: 44px minmax(0, auto) !important;
        }

        .lk-hero--inspo .lk-hero-support img {
            width: 44px !important;
            height: 44px !important;
        }

        .lk-hero--inspo .lk-hero-script {
            display: none !important;
        }
    }

    html.dark .lk-hero.lk-hero--marketplace.lk-hero--inspo {
        background:
            radial-gradient(circle at 8% 18%, rgba(193, 151, 113, 0.08), transparent 28%),
            radial-gradient(circle at 92% 10%, rgba(168, 93, 80, 0.14), transparent 30%),
            linear-gradient(135deg, #241A17 0%, #1E1714 58%, #171210 100%) !important;

        border-color: #49342B !important;
        color: #FFF8F2 !important;
    }

    html.dark .lk-hero--inspo h1,
    html.dark .lk-hero--inspo .lk-hero-product-info strong,
    html.dark .lk-hero--inspo .lk-hero-support span {
        color: #FFF8F2 !important;
    }

    html.dark .lk-hero--inspo h1 span,
    html.dark .lk-hero--inspo .lk-kicker,
    html.dark .lk-hero--inspo .lk-hero-product-info span,
    html.dark .lk-hero--inspo .lk-hero-account-link {
        color: #EBA99D !important;
    }

    html.dark .lk-hero--inspo .lk-hero-copy > p,
    html.dark .lk-hero--inspo .lk-hero-trust,
    html.dark .lk-hero--inspo .lk-hero-product-info small {
        color: #C8B7AD !important;
    }

    html.dark .lk-hero--inspo .lk-hero-showcase::before {
        background: linear-gradient(135deg, #382820, #261B17) !important;
    }

    html.dark .lk-hero--inspo .lk-hero-product,
    html.dark .lk-hero--inspo .lk-hero-product-info,
    html.dark .lk-hero--inspo .lk-hero-support {
        background: rgba(36, 26, 23, 0.94) !important;
        border-color: #49342B !important;
    }

    html.dark .lk-hero--inspo .lk-btn-light {
        background: #2A1E1A !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .lk-hero--inspo .lk-btn-light:hover {
        background: #361B17 !important;
        border-color: #A85D50 !important;
        color: #FFFFFF !important;
    }
</style>
@endonce

<section class="lk-hero lk-hero--marketplace lk-hero--inspo">
    <div class="lk-hero-copy">
        <span class="lk-kicker">
            Welcome to LIKHAE
        </span>

        <h1>
            Made for what<br>
            you <span>need.</span>
        </h1>

        <p>
            Discover products across categories, compare choices from trusted sellers,
            and find the right fit for everyday needs.
        </p>

        <div class="lk-hero-actions">
            <a
                href="{{ $browseUrl }}"
                class="lk-btn lk-btn-red"
            >
                Browse Products
            </a>

            <a
                href="{{ $categoriesUrl }}"
                class="lk-btn lk-btn-light"
            >
                Explore Categories
            </a>
        </div>

        @if($guest)
            <a
                class="lk-hero-account-link"
                href="{{ $loginUrl }}"
            >
                Sign in to unlock full marketplace features
                <span aria-hidden="true">→</span>
            </a>
        @else
            <div class="lk-hero-quick-links">
                <span>Buyer workspace</span>

                @if($ordersUrl)
                    <a href="{{ $ordersUrl }}">
                        My Orders
                    </a>
                @endif

                <a
                    href="{{ $cartUrl }}"
                    @if(!$hasBuyerCartRoute) onclick="return false;" @endif
                >
                    View Cart
                </a>
            </div>
        @endif

        <div
            class="lk-hero-trust"
            aria-label="Marketplace values"
        >
            <span>Multiple Categories</span>
            <i aria-hidden="true"></i>
            <span>Trusted Sellers</span>
            <i aria-hidden="true"></i>
            <span>Secure Marketplace</span>
        </div>
    </div>

    <div
        class="lk-hero-showcase"
        aria-label="Featured marketplace categories"
    >
        <div class="lk-hero-tag">
            Explore more categories
        </div>

        <div class="lk-hero-product">
            @if($heroImage)
                <img
                    src="{{ $heroImage }}"
                    alt="{{ $heroName }}"
                    decoding="async"
                >
            @else
                <div
                    class="lk-hero-product-placeholder"
                    aria-label="Featured local product"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 8h12l1 13H5z"/>
                        <path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                    </svg>
                </div>
            @endif
        </div>

        <div class="lk-hero-product-info">
            <span>{{ $category }}</span>
            <strong>{{ $heroName }}</strong>
            <small>Featured from the LIKHAE marketplace</small>
        </div>

        @if($supportImage)
            <div class="lk-hero-support">
                <img
                    src="{{ $supportImage }}"
                    alt="{{ $supportCategory }} marketplace product"
                    decoding="async"
                >

                <span>{{ $supportCategory }}</span>
            </div>
        @endif

        <div class="lk-hero-script" aria-hidden="true">
            Curated everyday finds
        </div>
    </div>
</section>