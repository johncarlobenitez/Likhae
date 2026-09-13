@extends('layouts.guest')

@section('title', 'Products - LIKHAE Marketplace')

@section('content')
@php
    $collection = collect($buyerProducts ?? []);
    $activeCategory = strtolower((string) request('category', 'all'));
    $activeSort = strtolower((string) request('sort', 'latest'));
    $activeView = in_array(request('view'), ['grid', 'list'], true) ? request('view') : 'grid';
    $query = mb_strtolower(trim((string) request('q', '')));
    $categories = ['all' => 'All Products', 'fashion' => 'Fashion', 'electronics' => 'Electronics', 'home' => 'Home & Living', 'books' => 'Books', 'beauty' => 'Beauty', 'sports' => 'Sports', 'toys' => 'Toys', 'automotive' => 'Automotive'];
    $catalogRouteName = request()->routeIs('guest.home') ? 'guest.home' : 'products';
    $categoryKey = function ($product) {
        $slug = \Illuminate\Support\Str::slug((string) data_get($product, 'category', ''));
        foreach (['fashion','electronics','home','books','beauty','sports','toys','automotive'] as $key) {
            if (str_contains($slug, $key)) return $key;
        }
        return in_array($slug, ['bags','accessories'], true) ? 'fashion' : $slug;
    };
    $visibleProducts = $collection->filter(function ($product) use ($activeCategory, $query, $categoryKey) {
        $haystack = mb_strtolower(implode(' ', [data_get($product,'name'), data_get($product,'category'), data_get($product,'seller'), data_get($product,'location')]));
        return ($activeCategory === 'all' || $categoryKey($product) === $activeCategory)
            && ($query === '' || str_contains($haystack, $query));
    });
    $visibleProducts = (match($activeSort) {
        default => $visibleProducts->sortByDesc(fn($product) => data_get($product, 'id', 0)),
    })->values();
    $catalogUrl = fn(array $changes = []) => route($catalogRouteName, array_filter(array_merge(request()->only(['q','category','sort','view']), $changes), fn($value) => $value !== '' && $value !== null));
@endphp

<div class="lk-page">
    <header class="lk-page-title">
        <div><span class="lk-kicker">Marketplace</span><h1>Browse Products</h1><p>Find quality products from trusted local sellers.</p></div>
        <div class="lk-note-chip">Guest browsing only</div>
    </header>

    <div class="lk-products-layout">
        <aside class="lk-products-sidebar" aria-label="Product filters">
            <form action="{{ route($catalogRouteName) }}" method="GET" class="grid gap-3">
                <div class="lk-filter-card"><div class="lk-filter-heading"><h2 id="categories-heading">Categories</h2></div><nav class="lk-category-list">@foreach($categories as $key => $label)<a href="{{ $catalogUrl(['category' => $key]) }}" class="lk-category-item {{ $activeCategory === $key ? 'is-active' : '' }}"><span>{{ $label }}</span><small>{{ $key === 'all' ? $collection->count() : $collection->filter(fn($product) => $categoryKey($product) === $key)->count() }}</small></a>@endforeach</nav></div>
            </form>
        </aside>

        <main class="lk-products-main">
            <div class="lk-products-toolbar">
                <div class="lk-toolbar-info"><strong>{{ $visibleProducts->count() }}</strong><span> products found</span>@if($query)<small> for "{{ request('q') }}"</small>@endif</div>
                <div class="lk-toolbar-actions">
                    <form action="{{ route($catalogRouteName) }}" method="GET" class="lk-catalog-sort">@foreach(request()->except('sort') as $key => $value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach<select name="sort" onchange="this.form.submit()"><option value="latest" @selected($activeSort === 'latest')>Latest</option><option value="name" @selected($activeSort === 'name')>Name</option></select></form>
                    <a href="{{ $catalogUrl(['view' => 'grid']) }}" class="lk-view-toggle {{ $activeView === 'grid' ? 'active' : '' }}" aria-label="Grid view"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></a><a href="{{ $catalogUrl(['view' => 'list']) }}" class="lk-view-toggle {{ $activeView === 'list' ? 'active' : '' }}" aria-label="List view"><svg viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg></a>
                </div>
            </div>
            @if($visibleProducts->isNotEmpty())
                <div class="lk-product-grid {{ $activeView === 'list' ? 'is-list-view' : '' }}">@foreach($visibleProducts as $product) @include('guest.partials.product-card', ['product' => $product]) @endforeach</div>
            @else
                <x-buyer.empty-state title="No products found" message="Try another category or search term." />
            @endif
        </main>
    </div>
</div>
@endsection
