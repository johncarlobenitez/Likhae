@props(['products', 'guest' => false, 'title' => 'Products', 'subtitle' => 'Explore quality products from trusted local sellers.'])

@php
    $collection = collect($products ?? []);
    $routeName = $guest ? 'home' : 'buyer.products';
    $activeCategory = strtolower((string) request('category', 'all'));
    $activeSort = strtolower((string) request('sort', 'latest'));
    $activeView = in_array(request('view'), ['grid', 'list'], true) ? request('view') : 'grid';
    $query = mb_strtolower(trim((string) request('q', '')));
    $maxCatalogPrice = max(10000, (int) ceil((float) $collection->max(fn($product) => data_get($product, 'price', 0))));
    $maxPrice = min($maxCatalogPrice, max(0, (int) request('max_price', $maxCatalogPrice)));
    $minimumRating = min(5, max(0, (float) request('rating', 0)));
    $categories = ['all' => 'All Products', 'fashion' => 'Fashion', 'electronics' => 'Electronics', 'home' => 'Home & Living', 'books' => 'Books', 'beauty' => 'Beauty', 'sports' => 'Sports', 'toys' => 'Toys', 'automotive' => 'Automotive'];
    $categoryKey = function ($product) {
        $slug = \Illuminate\Support\Str::slug((string) data_get($product, 'category', ''));
        foreach (['fashion','electronics','home','books','beauty','sports','toys','automotive'] as $key) if (str_contains($slug, $key)) return $key;
        return in_array($slug, ['bags','accessories'], true) ? 'fashion' : $slug;
    };
    $visibleProducts = $collection->filter(function ($product) use ($activeCategory, $query, $maxPrice, $minimumRating, $categoryKey) {
        $haystack = mb_strtolower(implode(' ', [data_get($product,'name'), data_get($product,'category'), data_get($product,'seller'), data_get($product,'location')]));
        return ($activeCategory === 'all' || $categoryKey($product) === $activeCategory)
            && ($query === '' || str_contains($haystack, $query))
            && (float) data_get($product, 'price', 0) <= $maxPrice
            && (float) data_get($product, 'rating', 0) >= $minimumRating;
    });
    $visibleProducts = (match($activeSort) {
        'price-low' => $visibleProducts->sortBy(fn($product) => data_get($product, 'price', 0)),
        'price-high' => $visibleProducts->sortByDesc(fn($product) => data_get($product, 'price', 0)),
        'best-rated' => $visibleProducts->sortByDesc(fn($product) => data_get($product, 'rating', 0)),
        'best-selling' => $visibleProducts->sortByDesc(fn($product) => data_get($product, 'sold', 0)),
        default => $visibleProducts->sortByDesc(fn($product) => data_get($product, 'id', 0)),
    })->values();
    $catalogUrl = fn(array $changes = []) => route($routeName, array_filter(array_merge(request()->only(['q','category','sort','view','max_price','rating']), $changes), fn($value) => $value !== '' && $value !== null));
@endphp

<div class="lk-page">
    <header class="lk-page-title">
        <div><span class="lk-kicker">Marketplace</span><h1>{{ $title }}</h1><p>{{ $subtitle }}</p></div>
        @if($guest)<div class="lk-note-chip">Browse freely &middot; Sign in only when you are ready to buy</div>@endif
    </header>

    <div class="lk-products-layout">
        <aside class="lk-products-sidebar" aria-label="Product filters">
            <form action="{{ route($routeName) }}" method="GET" class="grid gap-3">
                <div class="lk-filter-card"><div class="lk-filter-heading"><h2>Categories</h2></div><nav class="lk-category-list">@foreach($categories as $key => $label)<a href="{{ $catalogUrl(['category' => $key]) }}" class="lk-category-item {{ $activeCategory === $key ? 'is-active' : '' }}"><span>{{ $label }}</span><small>{{ $key === 'all' ? $collection->count() : $collection->filter(fn($product) => $categoryKey($product) === $key)->count() }}</small></a>@endforeach</nav></div>
                <div class="lk-filter-card"><div class="lk-filter-heading"><h2>Price Range</h2></div><input type="range" name="max_price" min="0" max="{{ $maxCatalogPrice }}" step="100" value="{{ $maxPrice }}" class="lk-range-input" oninput="this.nextElementSibling.querySelector('b').textContent=new Intl.NumberFormat('en-PH').format(this.value)"><div class="lk-price-display"><span>&#8369;0</span><span>Up to &#8369;<b>{{ number_format($maxPrice) }}</b></span></div></div>
                <div class="lk-filter-card"><label class="lk-field-label" for="catalogRating">Minimum rating</label><select id="catalogRating" name="rating" class="lk-filter-select"><option value="0">Any rating</option>@foreach([4.5,4,3] as $rating)<option value="{{ $rating }}" @selected($minimumRating == $rating)>{{ number_format($rating,1) }}&#9733; and above</option>@endforeach</select><button class="lk-btn lk-btn-red lk-btn-full" type="submit">Apply Filters</button><a class="lk-btn lk-btn-light lk-btn-full" href="{{ route($routeName) }}">Clear Filters</a></div>
            </form>
        </aside>

        <main class="lk-products-main">
            <div class="lk-products-toolbar">
                <div class="lk-toolbar-info"><strong>{{ $visibleProducts->count() }}</strong><span> products found</span>@if($query)<small> for "{{ request('q') }}"</small>@endif</div>
                <div class="lk-toolbar-actions">
                    <form action="{{ route($routeName) }}" method="GET" class="lk-catalog-sort">@foreach(request()->except('sort') as $key => $value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach<select name="sort" onchange="this.form.submit()"><option value="latest" @selected($activeSort === 'latest')>Latest</option><option value="price-low" @selected($activeSort === 'price-low')>Price: Low to High</option><option value="price-high" @selected($activeSort === 'price-high')>Price: High to Low</option><option value="best-rated" @selected($activeSort === 'best-rated')>Best Rated</option><option value="best-selling" @selected($activeSort === 'best-selling')>Best Selling</option></select></form>
                    <a href="{{ $catalogUrl(['view' => 'grid']) }}" class="lk-view-toggle {{ $activeView === 'grid' ? 'active' : '' }}" aria-label="Grid view"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></a><a href="{{ $catalogUrl(['view' => 'list']) }}" class="lk-view-toggle {{ $activeView === 'list' ? 'active' : '' }}" aria-label="List view"><svg viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg></a>
                </div>
            </div>
            @if($visibleProducts->isNotEmpty())
                <div class="lk-product-grid {{ $activeView === 'list' ? 'is-list-view' : '' }}">@foreach($visibleProducts as $product)<x-buyer.product-card :product="$product" :guest="$guest" />@endforeach</div>
            @else
                <x-buyer.empty-state title="No products found" message="Try another category, search term, price, or rating filter." />
            @endif
        </main>
    </div>
</div>
