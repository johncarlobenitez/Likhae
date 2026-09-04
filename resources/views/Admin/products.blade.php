@extends('layouts.admin')

@section('title', 'Marketplace Management')
@section('subtitle', 'Oversee listings, categories, and human-reviewed prohibited-item risk signals.')
@section('active', 'products')

@section('content')
@php
    $view = request('view', 'products');
    $products = [
        ['id' => 'PRD-8451', 'name' => 'Handwoven Abaca Tote', 'seller' => 'LIKHA Studio', 'category' => 'Fashion', 'price' => 1590, 'stock' => 34, 'sold' => 126, 'status' => 'Active'],
        ['id' => 'PRD-8450', 'name' => 'Mechanical Keyboard 87 Keys', 'seller' => 'MNL Tech', 'category' => 'Electronics', 'price' => 2790, 'stock' => 3, 'sold' => 98, 'status' => 'Active'],
        ['id' => 'PRD-8449', 'name' => 'Artisan Soy Candle Set', 'seller' => 'Casa Local', 'category' => 'Home', 'price' => 890, 'stock' => 18, 'sold' => 72, 'status' => 'Active'],
        ['id' => 'PRD-8448', 'name' => 'Unverified Tactical Blade', 'seller' => 'RapidCart PH', 'category' => 'Other', 'price' => 1290, 'stock' => 11, 'sold' => 0, 'status' => 'Flagged'],
    ];
    $categories = [
        ['name' => 'Fashion', 'products' => 2814, 'active' => 2760, 'updated' => 'Sep 4, 2026'],
        ['name' => 'Electronics', 'products' => 1942, 'active' => 1889, 'updated' => 'Sep 3, 2026'],
        ['name' => 'Home & Living', 'products' => 1640, 'active' => 1602, 'updated' => 'Sep 2, 2026'],
        ['name' => 'Books', 'products' => 721, 'active' => 709, 'updated' => 'Aug 30, 2026'],
        ['name' => 'Beauty', 'products' => 983, 'active' => 951, 'updated' => 'Aug 28, 2026'],
    ];
    $flags = [
        ['id' => 'FLG-3088', 'product' => 'Unverified Tactical Blade', 'seller' => 'RapidCart PH', 'signal' => 'Possible weapon', 'confidence' => 94, 'source' => 'Automated scan', 'status' => 'Open'],
        ['id' => 'FLG-3087', 'product' => 'Herbal Relief Capsules', 'seller' => 'Wellness Corner', 'signal' => 'Possible controlled claim', 'confidence' => 82, 'source' => 'Buyer report', 'status' => 'Under Review'],
        ['id' => 'FLG-3086', 'product' => 'Industrial Solvent 1L', 'seller' => 'BuildRight', 'signal' => 'Restricted chemical', 'confidence' => 76, 'source' => 'Keyword + image scan', 'status' => 'Open'],
        ['id' => 'FLG-3085', 'product' => 'Replica Collector Pistol', 'seller' => 'Hobby House', 'signal' => 'Weapon-like item', 'confidence' => 71, 'source' => 'Seller report', 'status' => 'Under Review'],
    ];
@endphp

<div class="ad-page">
    <div class="ad-page-head">
        <div><span class="ad-overline">Marketplace governance</span><h2>{{ $view === 'categories' ? 'Category management' : ($view === 'monitor' ? 'Prohibited-item monitor' : 'Product catalog') }}</h2><p>{{ $view === 'monitor' ? 'Risk signals support review; they never replace an evidence-based administrator decision.' : 'Keep the marketplace organized, accurate, and policy-compliant.' }}</p></div>
        @if($view === 'categories')<button class="ad-btn ad-btn-primary" type="button" data-demo-action="New category form opened">Add category</button>@else<a class="ad-btn ad-btn-secondary" href="{{ route('admin.products', ['view' => 'monitor']) }}">Review flags</a>@endif
    </div>

    <div class="ad-tabs">
        <a class="ad-tab {{ $view === 'products' ? 'is-active' : '' }}" href="{{ route('admin.products', ['view' => 'products']) }}">Products</a>
        <a class="ad-tab {{ $view === 'categories' ? 'is-active' : '' }}" href="{{ route('admin.products', ['view' => 'categories']) }}">Categories</a>
        <a class="ad-tab {{ $view === 'monitor' ? 'is-active' : '' }}" href="{{ route('admin.products', ['view' => 'monitor']) }}">Prohibited Item Monitor <b>17</b></a>
    </div>

    @if($view === 'monitor')
        <section class="ad-summary-grid">
            <div class="ad-mini-stat"><span>Open signals</span><strong>17</strong><small>5 high-confidence</small></div><div class="ad-mini-stat"><span>Reviewed today</span><strong>31</strong><small>Median: 18 minutes</small></div><div class="ad-mini-stat"><span>Listings removed</span><strong>6</strong><small>Evidence retained</small></div><div class="ad-mini-stat"><span>False positives</span><strong>9</strong><small>Signals cleared by Admin</small></div>
        </section>
        <div class="ad-note is-warning"><strong>Human review required:</strong> Image, keyword, seller-history, and community-report signals may be inaccurate. Open the listing and evidence before taking any enforcement action.</div>
        <section class="ad-risk-layout">
            <div class="ad-card" id="flag-table">
                <div class="ad-filter-bar"><label class="ad-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" data-filter-input="#flag-table" placeholder="Search flag, product, or seller"></label><select class="ad-select"><option>Risk: all</option><option>High confidence</option><option>Medium confidence</option></select></div>
                <div class="ad-table-wrap"><table class="ad-table"><thead><tr><th>Flag / product</th><th>Seller</th><th>Risk signal</th><th>Confidence</th><th>Source</th><th>Status</th><th></th></tr></thead><tbody>
                    @foreach($flags as $flag)<tr data-filter-item data-search="{{ strtolower(implode(' ', $flag)) }}"><td><strong>{{ $flag['product'] }}</strong><small>{{ $flag['id'] }}</small></td><td>{{ $flag['seller'] }}</td><td>{{ $flag['signal'] }}</td><td><span class="ad-risk-score"><i style="--risk:{{ $flag['confidence'] }}%"></i>{{ $flag['confidence'] }}%</span></td><td>{{ $flag['source'] }}</td><td><span class="ad-status is-{{ strtolower(str_replace(' ', '-', $flag['status'])) }}">{{ $flag['status'] }}</span></td><td><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Opening evidence for {{ $flag['id'] }}">Review</button></td></tr>@endforeach
                </tbody></table></div>
            </div>
            <aside class="ad-card"><header class="ad-card-head"><div><span class="ad-overline">Signal categories</span><h3>Risk breakdown</h3><p>Open items by detected concern.</p></div></header><div class="ad-risk-summary"><div class="ad-risk-item"><span class="ad-risk-number">5</span><div><strong>Weapons or weapon-like items</strong><p>Highest priority; manually inspect media and description.</p></div></div><div class="ad-risk-item"><span class="ad-risk-number">4</span><div><strong>Controlled or illegal substances</strong><p>Check ingredients, claims, permits, and seller history.</p></div></div><div class="ad-risk-item is-warning"><span class="ad-risk-number">8</span><div><strong>Restricted claims or chemicals</strong><p>Validate against current marketplace policy before action.</p></div></div></div></aside>
        </section>
    @elseif($view === 'categories')
        <section class="ad-card" id="category-table"><div class="ad-filter-bar"><label class="ad-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" data-filter-input="#category-table" placeholder="Search categories"></label></div><div class="ad-table-wrap"><table class="ad-table"><thead><tr><th>Category</th><th>Total products</th><th>Active listings</th><th>Last updated</th><th style="text-align:right">Actions</th></tr></thead><tbody>@foreach($categories as $category)<tr data-filter-item data-search="{{ strtolower($category['name']) }}"><td><strong>{{ $category['name'] }}</strong></td><td>{{ number_format($category['products']) }}</td><td>{{ number_format($category['active']) }}</td><td>{{ $category['updated'] }}</td><td><div class="ad-row-actions"><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Editing {{ $category['name'] }}">Edit</button><button class="ad-btn ad-btn-danger-soft ad-btn-sm" data-confirm-action data-confirm-title="Archive category?" data-confirm-message="Existing listings will need a replacement category." data-success-message="Category archived">Archive</button></div></td></tr>@endforeach</tbody></table></div></section>
    @else
        <section class="ad-card" id="product-table"><div class="ad-filter-bar"><label class="ad-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" data-filter-input="#product-table" placeholder="Search product, seller, or ID"></label><div class="ad-inline-actions"><select class="ad-select"><option>All categories</option><option>Fashion</option><option>Electronics</option><option>Home</option></select><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Catalog exported">Export</button></div></div><div class="ad-table-wrap"><table class="ad-table"><thead><tr><th>Product</th><th>Seller</th><th>Category</th><th>Price</th><th>Stock</th><th>Sold</th><th>Status</th><th></th></tr></thead><tbody>@foreach($products as $product)<tr data-filter-item data-search="{{ strtolower(implode(' ', $product)) }}"><td><div class="ad-cell-product"><span class="ad-product-thumb">{{ mb_strtoupper(mb_substr($product['name'], 0, 1)) }}</span><span><strong>{{ $product['name'] }}</strong><small>{{ $product['id'] }}</small></span></div></td><td>{{ $product['seller'] }}</td><td>{{ $product['category'] }}</td><td><strong>₱{{ number_format($product['price'], 2) }}</strong></td><td>{{ $product['stock'] }}</td><td>{{ $product['sold'] }}</td><td><span class="ad-status is-{{ strtolower($product['status']) }}">{{ $product['status'] }}</span></td><td><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Opening {{ $product['name'] }}">Review</button></td></tr>@endforeach</tbody></table></div></section>
    @endif
</div>
@endsection
