@extends('layouts.seller')

@php
    $currentMode = $mode ?? request('mode', 'list');
    $editing = $currentMode === 'edit';
    $product = $editing
        ? $sellerProducts->firstWhere('id', $selectedProduct ?? request('product')) ?? $sellerProducts->first()
        : null;
    $pageTitle = match ($currentMode) {
        'add' => 'Add Product',
        'edit' => 'Edit Product',
        'inventory' => 'Inventory',
        default => 'Products',
    };
@endphp

@section('title', $pageTitle)
@section('active', 'products')
@section('subtitle', $currentMode === 'inventory' ? 'Monitor stock levels and prevent missed sales.' : ($editing ? 'Update listing information, pricing, and inventory.' : ($currentMode === 'add' ? 'Create a complete and buyer-ready product listing.' : 'Manage product listings, pricing, stock, and visibility.')))

@section('content')
<div class="sl-page">
    @if (in_array($currentMode, ['add', 'edit'], true))
        <div class="sl-page-toolbar">
            <div>
                <span class="sl-eyebrow">Product Management</span>
                <h2>{{ $editing ? 'Edit '.$product['name'] : 'Create New Product' }}</h2>
                <p>Complete the essential product, pricing, inventory, and shipping information.</p>
            </div>
            <a href="{{ route('seller.products') }}" class="sl-btn sl-btn-ghost">Cancel</a>
        </div>

        <form class="sl-editor-layout" data-demo-form data-success="{{ $editing ? 'Product changes saved.' : 'Product created successfully.' }}">
            <div class="sl-editor-main">
                <section class="sl-card sl-form-section">
                    <header class="sl-form-section-head"><span>1</span><div><h3>Product Information</h3><p>Describe what buyers will see on the product page.</p></div></header>
                    <div class="sl-form-grid">
                        <label class="sl-field sl-span-2"><span>Product name <b>*</b></span><input type="text" value="{{ data_get($product, 'name') }}" placeholder="e.g. 27-inch Borderless Monitor" required><small>Use a clear title with the product type and key feature.</small></label>
                        <label class="sl-field"><span>Category <b>*</b></span><select required><option value="">Select category</option><option selected>Electronics</option><option>Home Office</option><option>Audio</option><option>Accessories</option></select></label>
                        <label class="sl-field"><span>Brand</span><input type="text" value="{{ $editing ? 'NovaView' : '' }}" placeholder="Brand name"></label>
                        <label class="sl-field sl-span-2"><span>Description <b>*</b></span><textarea rows="6" placeholder="Describe the product, its benefits, included items, and care instructions." required>{{ $editing ? 'Crisp Full HD monitor with slim bezels, adjustable viewing modes, and multiple input ports for productive workspaces.' : '' }}</textarea><small><span data-character-count>0</span>/3000 characters</small></label>
                        <div class="sl-field sl-span-2"><span>Product images <b>*</b></span><label class="sl-upload-box"><input type="file" multiple accept="image/*" data-image-input><svg viewBox="0 0 24 24"><path d="M12 16V4M7 9l5-5 5 5M4 15v5h16v-5"/></svg><strong>Upload product images</strong><small>JPG, PNG, or WebP · up to 5 images · maximum 5 MB each</small></label><div class="sl-image-preview" data-image-preview></div></div>
                    </div>
                </section>

                <section class="sl-card sl-form-section">
                    <header class="sl-form-section-head"><span>2</span><div><h3>Pricing</h3><p>Set a competitive selling price and optional promotion.</p></div></header>
                    <div class="sl-form-grid sl-form-grid-4">
                        <label class="sl-field"><span>Regular price <b>*</b></span><div class="sl-input-prefix"><i>₱</i><input type="number" min="0" step="0.01" value="{{ data_get($product, 'price') }}" placeholder="0.00" required></div></label>
                        <label class="sl-field"><span>Discount</span><div class="sl-input-suffix"><input type="number" min="0" max="100" value="10"><i>%</i></div></label>
                        <label class="sl-field"><span>Sale price</span><div class="sl-input-prefix"><i>₱</i><input type="number" min="0" step="0.01" value="{{ $editing ? 11691 : '' }}" placeholder="0.00"></div></label>
                        <label class="sl-field"><span>Voucher</span><select><option>No voucher</option><option>New buyer voucher</option><option>September campaign</option></select></label>
                    </div>
                </section>

                <section class="sl-card sl-form-section">
                    <header class="sl-form-section-head"><span>3</span><div><h3>Inventory & Variations</h3><p>Track stock using a unique SKU for each variation.</p></div></header>
                    <div class="sl-form-grid">
                        <label class="sl-field"><span>Stock quantity <b>*</b></span><input type="number" min="0" value="{{ data_get($product, 'stock', 20) }}" required></label>
                        <label class="sl-field"><span>SKU <b>*</b></span><input type="text" value="{{ data_get($product, 'sku') }}" placeholder="e.g. MON-27-BLK" required></label>
                    </div>
                    <div class="sl-variation-head"><div><strong>Variations</strong><small>Add options such as color, size, or model.</small></div><button type="button" class="sl-btn sl-btn-soft sl-btn-sm" data-add-variation>+ Add Variation</button></div>
                    <div class="sl-variation-table" data-variation-list>
                        <div class="sl-variation-row sl-variation-labels"><span>Variation</span><span>Option</span><span>SKU</span><span>Stock</span><span></span></div>
                        <div class="sl-variation-row"><input value="Model" aria-label="Variation name"><input value="27 inch Black" aria-label="Variation option"><input value="MON-27-BLK" aria-label="Variation SKU"><input type="number" value="20" min="0" aria-label="Variation stock"><button type="button" class="sl-icon-btn" data-remove-row aria-label="Remove variation">×</button></div>
                    </div>
                </section>

                <section class="sl-card sl-form-section">
                    <header class="sl-form-section-head"><span>4</span><div><h3>Shipping</h3><p>Provide accurate package dimensions for logistics rates.</p></div></header>
                    <div class="sl-form-grid sl-form-grid-4">
                        <label class="sl-field"><span>Weight <b>*</b></span><div class="sl-input-suffix"><input type="number" min="0" step="0.01" value="3.2" required><i>kg</i></div></label>
                        <label class="sl-field"><span>Length</span><div class="sl-input-suffix"><input type="number" min="0" value="68"><i>cm</i></div></label>
                        <label class="sl-field"><span>Width</span><div class="sl-input-suffix"><input type="number" min="0" value="15"><i>cm</i></div></label>
                        <label class="sl-field"><span>Height</span><div class="sl-input-suffix"><input type="number" min="0" value="45"><i>cm</i></div></label>
                        <div class="sl-field sl-span-2"><span>Shipping methods <b>*</b></span><div class="sl-check-grid"><label><input type="checkbox" checked> J&T Express</label><label><input type="checkbox" checked> Flash Express</label><label><input type="checkbox"> LBC</label><label><input type="checkbox"> Local Courier</label></div></div>
                    </div>
                </section>
            </div>

            <aside class="sl-editor-side">
                <section class="sl-card sl-publish-card">
                    <h3>Listing Status</h3>
                    <label class="sl-field"><span>Visibility</span><select><option>Active</option><option>Draft</option><option>Archived</option></select></label>
                    <div class="sl-quality-list"><strong>Listing quality</strong><p><span>✓</span> Clear product title</p><p><span>✓</span> Complete pricing</p><p><span>✓</span> Stock and SKU added</p><p><span>○</span> Add at least 3 images</p></div>
                    <button type="submit" class="sl-btn sl-btn-primary sl-btn-block">{{ $editing ? 'Save Changes' : 'Publish Product' }}</button>
                    <button type="button" class="sl-btn sl-btn-ghost sl-btn-block" data-demo-action="Draft saved.">Save as Draft</button>
                </section>
                <section class="sl-help-card"><span>i</span><div><strong>Seller tip</strong><p>Listings with complete specifications and 3–5 clear images receive more buyer views.</p></div></section>
            </aside>
        </form>
    @elseif ($currentMode === 'inventory')
        <div class="sl-page-toolbar">
            <div><span class="sl-eyebrow">Stock Control</span><h2>Inventory Overview</h2><p>Update stock before products run out and affect fulfillment.</p></div>
            <button type="button" class="sl-btn sl-btn-primary" data-demo-action="Inventory updates saved.">Save Updates</button>
        </div>

        <section class="sl-stat-grid sl-stat-grid-4">
            <x-seller.stat-card label="Total SKUs" value="126" icon="products" />
            <x-seller.stat-card label="In Stock" value="118" change="93.6%" icon="products" />
            <x-seller.stat-card label="Low Stock" value="7" direction="down" change="Needs review" icon="inventory" />
            <x-seller.stat-card label="Out of Stock" value="1" direction="down" change="Action needed" icon="inventory" />
        </section>

        <section class="sl-card">
            <div class="sl-table-toolbar">
                <div class="sl-search-input"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" placeholder="Search product or SKU" data-product-search></div>
                <select class="sl-select" data-product-status-filter><option value="all">All stock levels</option><option value="low">Low stock</option><option value="out">Out of stock</option><option value="healthy">Healthy</option></select>
            </div>
            <div class="sl-table-wrap">
                <table class="sl-table">
                    <thead><tr><th>Product</th><th>SKU</th><th>Available</th><th>Reserved</th><th>Reorder Level</th><th>Stock Status</th><th>Update</th></tr></thead>
                    <tbody>
                        @foreach ($sellerProducts as $item)
                            @php $level = $item['stock'] === 0 ? 'out' : ($item['stock'] <= 5 ? 'low' : 'healthy'); @endphp
                            <tr data-product-row data-product-name="{{ mb_strtolower($item['name'].' '.$item['sku']) }}" data-product-status="{{ $level }}">
                                <td><div class="sl-product-cell"><img src="{{ $item['image'] }}" alt=""><strong>{{ $item['name'] }}</strong></div></td>
                                <td>{{ $item['sku'] }}</td><td><strong>{{ $item['stock'] }}</strong></td><td>{{ min(3, $item['stock']) }}</td><td>8</td>
                                <td><span class="sl-status {{ $level === 'out' ? 'is-danger' : ($level === 'low' ? 'is-warning' : 'is-success') }}">{{ $level === 'out' ? 'Out of stock' : ($level === 'low' ? 'Low stock' : 'Healthy') }}</span></td>
                                <td><div class="sl-inline-stock"><input type="number" min="0" value="{{ $item['stock'] }}" aria-label="Stock for {{ $item['name'] }}"><button type="button" class="sl-btn sl-btn-soft sl-btn-sm" data-demo-action="Stock updated for {{ $item['name'] }}.">Update</button></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @else
        <div class="sl-page-toolbar">
            <div><span class="sl-eyebrow">Catalog</span><h2>Product Management</h2><p>Manage active, draft, and archived product listings.</p></div>
            <a href="{{ route('seller.products', ['mode' => 'add']) }}" class="sl-btn sl-btn-primary"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Add Product</a>
        </div>

        <section class="sl-mini-stats">
            <div><span>All Products</span><strong>{{ $sellerProducts->count() }}</strong></div><div><span>Active</span><strong>{{ $sellerProducts->where('status', 'Active')->count() }}</strong></div><div><span>Low Stock</span><strong>{{ $sellerProducts->where('stock', '<=', 5)->where('stock', '>', 0)->count() }}</strong></div><div><span>Archived</span><strong>{{ $sellerProducts->where('status', 'Archived')->count() }}</strong></div>
        </section>

        <section class="sl-card">
            <div class="sl-table-toolbar">
                <div class="sl-search-input"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" placeholder="Search product or SKU" data-product-search></div>
                <div class="sl-toolbar-group"><select class="sl-select" data-product-status-filter><option value="all">All status</option><option value="active">Active</option><option value="archived">Archived</option></select><button type="button" class="sl-btn sl-btn-ghost" data-demo-action="Product list exported.">Export</button></div>
            </div>
            <div class="sl-table-wrap sl-desktop-product-table">
                <table class="sl-table">
                    <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Sold</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        @foreach ($sellerProducts as $item)
                            <tr data-product-row data-product-name="{{ mb_strtolower($item['name'].' '.$item['sku']) }}" data-product-status="{{ mb_strtolower($item['status']) }}">
                                <td><div class="sl-product-cell"><img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"><div><strong>{{ $item['name'] }}</strong><small>{{ $item['sku'] }}</small></div></div></td>
                                <td>{{ $item['category'] }}</td><td><strong>₱{{ number_format($item['price'], 2) }}</strong></td><td><span class="{{ $item['stock'] <= 5 ? 'sl-low-stock' : '' }}">{{ $item['stock'] }}</span></td><td>{{ $item['sold'] }}</td><td><span class="sl-rating">★ {{ number_format($item['rating'], 1) }}</span></td><td><span class="sl-status {{ $item['status'] === 'Active' ? 'is-success' : 'is-neutral' }}">{{ $item['status'] }}</span></td>
                                <td><div class="sl-table-actions"><a href="{{ route('seller.products', ['mode' => 'edit', 'product' => $item['id']]) }}" class="sl-icon-btn" title="Edit"><svg viewBox="0 0 24 24"><path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10zM14 7l3 3"/></svg></a><button type="button" class="sl-icon-btn" title="Update stock" data-stock-update data-product="{{ $item['name'] }}"><svg viewBox="0 0 24 24"><path d="M4 7h16v13H4zM4 11h16M9 15h6"/></svg></button><button type="button" class="sl-icon-btn" title="{{ $item['status'] === 'Archived' ? 'Restore' : 'Archive' }}" data-demo-action="{{ $item['status'] === 'Archived' ? 'Product restored.' : 'Product archived.' }}"><svg viewBox="0 0 24 24"><path d="M4 7h16v13H4zM3 4h18v3H3zM9 12h6"/></svg></button></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="sl-mobile-product-list">
                @foreach ($sellerProducts as $item)<x-seller.product-card :product="$item" />@endforeach
            </div>
            <div class="sl-no-results" data-product-empty hidden>No products match your filters.</div>
            <footer class="sl-table-footer"><span>Showing {{ $sellerProducts->count() }} products</span><div><button disabled>←</button><button class="is-current">1</button><button disabled>→</button></div></footer>
        </section>
    @endif
</div>
@endsection
