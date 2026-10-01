@props(['products', 'categories' => collect(), 'catalogMaxPrice' => null, 'catalogTotal' => null, 'paginator' => null, 'guest' => false, 'title' => 'Products', 'subtitle' => 'Explore quality products from trusted local sellers.'])

@php
    $collection = collect($products ?? []);
    $routeName = $guest ? 'home' : 'buyer.products';
    $activeCategory = strtolower((string) request('category', 'all'));
    $activeSort = strtolower((string) request('sort', 'latest'));
    $activeView = in_array(request('view'), ['grid', 'list'], true) ? request('view') : 'grid';
    $query = mb_strtolower(trim((string) request('q', '')));
    $maxCatalogPrice = max(10000, (int) ($catalogMaxPrice ?? ceil((float) $collection->max(fn($product) => data_get($product, 'min_price', 0)))));
    $requestedMaxPrice = (int) request('max_price', $maxCatalogPrice);
    $maxPrice = $requestedMaxPrice > 0 ? min($maxCatalogPrice, $requestedMaxPrice) : $maxCatalogPrice;
    $minimumRating = min(5, max(0, (float) request('rating', 0)));
    $productCategorySlug = fn ($product) => (string) (data_get($product, 'category.slug') ?: 'uncategorized');
    $categoryCollection = collect($categories ?? []);
    $childrenByParent = $categoryCollection->whereNotNull('parent_id')->groupBy('parent_id');
    $categoryRows = $categoryCollection->whereNull('parent_id')->map(function ($category) use ($childrenByParent, $activeCategory) {
        $children = $childrenByParent->get($category->id, collect())->map(fn ($child) => [
            'slug' => $child->slug,
            'label' => $child->name,
            'count' => (int) $child->products_count,
        ])->values()->all();
        $descendantCount = collect($children)->sum('count');

        return [
            'slug' => $category->slug,
            'label' => $category->name,
            'count' => (int) $category->products_count + $descendantCount,
            'children' => $children,
            'open' => $activeCategory === $category->slug || collect($children)->contains(fn ($child) => $child['slug'] === $activeCategory),
        ];
    })->sortBy('label')->values();
    $visibleProducts = $collection;
    $totalCatalogProducts = $catalogTotal ?? $collection->count();
    $resultCount = $paginator ? $paginator->total() : $collection->count();
    $queryParameters = request()->only(['q', 'category', 'sort', 'view', 'max_price', 'rating']);
    if (($queryParameters['max_price'] ?? null) === '0') unset($queryParameters['max_price']);
    $catalogUrl = fn (array $changes = []) => route($routeName, array_filter(array_merge($queryParameters, $changes), fn ($value) => $value !== '' && $value !== null));
@endphp

<div class="lk-page">
    <header class="lk-page-title">
        <div><span class="lk-kicker">Marketplace</span><h1>{{ $title }}</h1><p>{{ $subtitle }}</p></div>
        @if($guest)<div class="lk-note-chip">Browse freely &middot; Sign in only when you are ready to buy</div>@endif
    </header>

    <div class="lk-products-layout">
        <aside class="lk-products-sidebar" aria-label="Product filters">
            <form action="{{ route($routeName) }}" method="GET" class="grid gap-3">
                @foreach(request()->only(['q', 'category', 'sort', 'view']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <div class="lk-filter-card"><div class="lk-filter-heading"><h2>Categories</h2></div><nav class="lk-category-list"><a href="{{ $catalogUrl(['category' => 'all']) }}" class="lk-category-item {{ $activeCategory === 'all' ? 'is-active' : '' }}"><span>All Products</span><small>{{ $totalCatalogProducts }}</small></a>@foreach($categoryRows as $category)<details class="lk-category-group" @if($category['open']) open @endif><summary class="lk-category-item {{ $activeCategory === $category['slug'] ? 'is-active' : '' }}"><span>{{ $category['label'] }}</span><small>{{ $category['count'] }}</small></summary><div class="lk-category-sublist"><a href="{{ $catalogUrl(['category' => $category['slug']]) }}" class="lk-category-subitem {{ $activeCategory === $category['slug'] ? 'is-active' : '' }}">All {{ $category['label'] }}</a>@foreach($category['children'] as $child)<a href="{{ $catalogUrl(['category' => $child['slug']]) }}" class="lk-category-subitem {{ $activeCategory === $child['slug'] ? 'is-active' : '' }}"><span>{{ $child['label'] }}</span><small>{{ $child['count'] }}</small></a>@endforeach</div></details>@endforeach</nav></div>
                <div class="lk-filter-card"><div class="lk-filter-heading"><h2>Price Range</h2></div><input type="range" name="max_price" min="100" max="{{ $maxCatalogPrice }}" step="100" value="{{ max(100, $maxPrice) }}" class="lk-range-input" oninput="this.nextElementSibling.querySelector('b').textContent=new Intl.NumberFormat('en-PH').format(this.value)"><div class="lk-price-display"><span>&#8369;100</span><span>Up to &#8369;<b>{{ number_format(max(100, $maxPrice)) }}</b></span></div></div>
                <div class="lk-filter-card"><label class="lk-field-label" for="catalogRating">Minimum rating</label><select id="catalogRating" name="rating" class="lk-filter-select"><option value="0">Any rating</option>@foreach([4.5,4,3] as $rating)<option value="{{ $rating }}" @selected($minimumRating == $rating)>{{ number_format($rating,1) }}&#9733; and above</option>@endforeach</select><button class="lk-btn lk-btn-red lk-btn-full" type="submit">Apply Filters</button><a class="lk-btn lk-btn-light lk-btn-full" href="{{ route($routeName) }}">Clear Filters</a></div>
            </form>
        </aside>

        <main class="lk-products-main">
            <div class="lk-products-toolbar">
                <div class="lk-toolbar-info"><strong>{{ $resultCount }}</strong><span> products found</span>@if($query)<small> for "{{ request('q') }}"</small>@endif</div>
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
            @if($paginator && $paginator->hasPages())
                <div class="mt-5">{{ $paginator->links() }}</div>
            @endif
        </main>
    </div>
</div>
