@extends('Seller.layouts.app')
@section('title', 'Products — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/products.css') @endpush

@section('content')
<x-seller.page-header eyebrow="CATALOG" title="Products" description="Manage listings, prices, stock, and product status.">
    <x-slot:actions><a class="btn-primary" href="{{ url('/seller/products/create') }}">Add Product</a></x-slot:actions>
</x-seller.page-header>

<div class="filter-bar">
    <label class="search-field"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input placeholder="Search product or SKU"></label>
    <select class="filter-input"><option>Category: All</option><option>Local Products</option><option>Home & Living</option></select>
    <select class="filter-input"><option>Status: All</option><option>Active</option><option>Draft</option><option>Archived</option></select>
    <select class="filter-input"><option>Stock: All</option><option>Low Stock</option><option>Out of Stock</option></select>
</div>

<section class="panel mt-4">
    <div class="table-wrap product-table-desktop">
        <table class="data-table">
            <thead><tr><th><input type="checkbox"></th><th>Product</th><th>SKU</th><th>Category</th><th>Price</th><th>Sale</th><th>Stock</th><th>Sold</th><th>Status</th><th>Updated</th><th></th></tr></thead>
            <tbody>
                @foreach([
                    ['Handwoven Rattan Tote Bag','RAT-TOTE-NAT','Local Products','₱899','₱799','36','391','Active','Today'],
                    ['Capiz Shell Pendant Lamp','CAP-PEN-WHT','Home & Living','₱1,899','—','6','218','Low Stock','Yesterday'],
                    ['Premium Philippine Tablea','TAB-250-BX','Food & Beverage','₱329','₱299','54','724','Active','Aug 18'],
                    ['Cordillera Woven Bag','CRD-BAG-BLU','Fashion','₱749','—','0','286','Out of Stock','Aug 17'],
                ] as $p)
                <tr>
                    <td><input type="checkbox"></td>
                    <td><div class="flex items-center gap-3"><div class="product-thumb"></div><strong>{{ $p[0] }}</strong></div></td>
                    @foreach(array_slice($p,1,6) as $cell)<td>{{ $cell }}</td>@endforeach
                    <td><x-seller.status-badge :status="$p[7]"/></td>
                    <td>{{ $p[8] }}</td>
                    <td><button class="icon-button">•••</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mobile-product-list">
        @foreach([
            ['Handwoven Rattan Tote Bag','RAT-TOTE-NAT','₱899','36','Active'],
            ['Capiz Shell Pendant Lamp','CAP-PEN-WHT','₱1,899','6','Low Stock'],
            ['Premium Philippine Tablea','TAB-250-BX','₱329','54','Active'],
        ] as $p)
        <article class="mobile-product-card"><div class="product-thumb"></div><div class="min-w-0 flex-1"><strong>{{ $p[0] }}</strong><span>{{ $p[1] }}</span><div class="mt-2 flex items-center gap-3"><b>{{ $p[2] }}</b><span>Stock {{ $p[3] }}</span></div></div><x-seller.status-badge :status="$p[4]"/></article>
        @endforeach
    </div>
</section>
@endsection
