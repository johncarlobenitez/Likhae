@extends('layouts.guest')

@section('title', 'Products - LIKHAE Marketplace')

@push('head')
<style>
    @media (max-width: 900px) {
        .lk-guest-products-page .lk-guest-products-layout {
            display: block;
        }

        .lk-guest-products-page .lk-guest-products-sidebar {
            position: relative;
            z-index: 2;
            display: block;
            width: 100%;
            margin-bottom: 16px;
        }

        .lk-guest-products-page .lk-guest-category-card {
            overflow: visible;
            padding: 14px;
        }

        .lk-guest-products-page .lk-guest-category-list {
            display: flex !important;
            grid-template-columns: none !important;
            gap: 8px;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            padding: 2px 2px 10px;
            scroll-snap-type: x proximity;
            -webkit-overflow-scrolling: touch;
        }

        .lk-guest-products-page .lk-guest-category-item {
            flex: 0 0 auto;
            min-width: 138px;
            min-height: 44px;
            scroll-snap-align: start;
            white-space: nowrap;
        }
    }

    @media (max-width: 560px) {
        .lk-guest-products-page .lk-guest-category-card {
            padding: 12px;
        }

        .lk-guest-products-page .lk-guest-category-list {
            margin-inline: -2px;
        }

        .lk-guest-products-page .lk-guest-category-item {
            min-width: 128px;
            padding-inline: 12px;
        }

        .lk-guest-products-page .lk-toolbar-actions,
        .lk-guest-products-page .lk-catalog-sort,
        .lk-guest-products-page .lk-catalog-sort select {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $collection = collect($buyerProducts ?? []);
    $activeCategory = strtolower((string) request('category', 'all'));
    $activeSort = strtolower((string) request('sort', 'latest'));
    $activeView = in_array(request('view'), ['grid', 'list'], true) ? request('view') : 'grid';
    $query = mb_strtolower(trim((string) request('q', '')));
    $catalogRouteName = request()->routeIs('guest.home') ? 'guest.home' : 'products';
    $productCategorySlug = fn ($product) => (string) (data_get($product, 'category_slug') ?: \Illuminate\Support\Str::slug((string) data_get($product, 'category', 'uncategorized')));
    $productParentSlug = fn ($product) => (string) (data_get($product, 'parent_category_slug') ?: $productCategorySlug($product));
    $matchesCategory = fn ($product) => $activeCategory === 'all'
        || $productCategorySlug($product) === $activeCategory
        || $productParentSlug($product) === $activeCategory;
    $categoryRows = $collection
        ->groupBy(fn ($product) => $productParentSlug($product))
        ->map(function ($products, $parentSlug) use ($productCategorySlug, $activeCategory) {
            $first = $products->first();
            $parentLabel = data_get($first, 'parent_category') ?: data_get($first, 'category', 'Uncategorized');
            $children = $products
                ->groupBy(fn ($product) => $productCategorySlug($product))
                ->map(function ($childProducts, $childSlug) use ($parentSlug) {
                    $firstChild = $childProducts->first();

                    if ($childSlug === $parentSlug) {
                        return null;
                    }

                    return [
                        'slug' => $childSlug,
                        'label' => data_get($firstChild, 'category', 'Uncategorized'),
                        'count' => $childProducts->count(),
                    ];
                })
                ->filter()
                ->sortBy('label')
                ->values()
                ->all();

            return [
                'slug' => $parentSlug,
                'label' => $parentLabel,
                'count' => $products->count(),
                'children' => $children,
                'open' => $activeCategory === $parentSlug || collect($children)->contains(fn ($child) => $child['slug'] === $activeCategory),
            ];
        })
        ->sortBy('label')
        ->values();
    $visibleProducts = $collection->filter(function ($product) use ($matchesCategory, $query) {
        $haystack = mb_strtolower(implode(' ', [data_get($product,'name'), data_get($product,'category'), data_get($product,'seller'), data_get($product,'location')]));
        return $matchesCategory($product)
            && ($query === '' || str_contains($haystack, $query));
    });
    $visibleProducts = (match($activeSort) {
        default => $visibleProducts->sortByDesc(fn($product) => data_get($product, 'id', 0)),
    })->values();
    $catalogUrl = fn(array $changes = []) => route($catalogRouteName, array_filter(array_merge(request()->only(['q','category','sort','view']), $changes), fn($value) => $value !== '' && $value !== null));
@endphp

<div class="lk-page lk-guest-products-page">
    <header class="lk-page-title">
        <div><span class="lk-kicker">Marketplace</span><h1>Browse Products</h1><p>Find quality products from trusted local sellers.</p></div>
        <div class="lk-note-chip">Guest browsing only</div>
    </header>

    <div class="lk-products-layout lk-guest-products-layout">
        <aside class="lk-products-sidebar lk-guest-products-sidebar" aria-label="Product filters">
            <form action="{{ route($catalogRouteName) }}" method="GET" class="grid gap-3">
                <div class="lk-filter-card lk-guest-category-card"><div class="lk-filter-heading"><h2 id="categories-heading">Categories</h2></div><nav class="lk-category-list lk-guest-category-list"><a href="{{ $catalogUrl(['category' => 'all']) }}" class="lk-category-item lk-guest-category-item {{ $activeCategory === 'all' ? 'is-active' : '' }}"><span>All Products</span><small>{{ $collection->count() }}</small></a>@foreach($categoryRows as $category)<details class="lk-category-group" @if($category['open']) open @endif><summary class="lk-category-item lk-guest-category-item {{ $activeCategory === $category['slug'] ? 'is-active' : '' }}"><span>{{ $category['label'] }}</span><small>{{ $category['count'] }}</small></summary><div class="lk-category-sublist"><a href="{{ $catalogUrl(['category' => $category['slug']]) }}" class="lk-category-subitem {{ $activeCategory === $category['slug'] ? 'is-active' : '' }}">All {{ $category['label'] }}</a>@foreach($category['children'] as $child)<a href="{{ $catalogUrl(['category' => $child['slug']]) }}" class="lk-category-subitem {{ $activeCategory === $child['slug'] ? 'is-active' : '' }}"><span>{{ $child['label'] }}</span><small>{{ $child['count'] }}</small></a>@endforeach</div></details>@endforeach</nav></div>
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
