@extends('layouts.seller')

@php
    $currentMode = $mode ?? request('mode', 'list');
    $editing = $currentMode === 'edit';
    $product = $editing ? $sellerProducts->firstWhere('id', (string) ($selectedProduct ?? request('product'))) : null;
    $pageTitle = match ($currentMode) { 'add' => 'Add Product', 'edit' => 'Edit Product', 'inventory' => 'Inventory', default => 'Products' };
    $oldVariationNames = old('variation_name');
    $variationRows = collect($oldVariationNames !== null
        ? collect($oldVariationNames)->map(fn ($name, $index) => [
            'name' => $name,
            'id' => old("variation_id.$index"),
            'value' => old("variation_value.$index"),
            'sku' => old("variation_sku.$index"),
            'price' => old("variation_price.$index"),
            'stock' => old("variation_stock.$index"),
            'weight_grams' => old("variation_weight_grams.$index"),
        ])->all()
        : data_get($product, 'variation_rows', []));
    if ($variationRows->isEmpty()) {
        $variationRows = collect([['name' => '', 'value' => '', 'sku' => '', 'price' => '', 'stock' => '']]);
    }
    $specificationsText = old('specifications_text', collect(data_get($product, 'specification_rows', []))
        ->map(fn ($row) => data_get($row, 'name').': '.data_get($row, 'value'))
        ->filter(fn ($line) => trim($line) !== ':')
        ->implode("\n"));
@endphp

@section('title', $pageTitle)
@section('active', 'products')
@section('subtitle', $currentMode === 'inventory' ? 'Monitor stock levels and prevent missed sales.' : ($editing ? 'Update listing information, pricing, and inventory.' : ($currentMode === 'add' ? 'Create a complete and buyer-ready product listing.' : 'Manage product listings, pricing, stock, and visibility.')))

@section('content')
<div class="sl-page">
    @if(session('status'))<div class="sl-alert sl-alert-success" role="status">{{ session('status') }}</div>@endif
    @if (in_array($currentMode, ['add', 'edit'], true))
        <div class="sl-page-toolbar">
            <div><span class="sl-eyebrow">Product Management</span><h2>{{ $editing ? 'Edit '.data_get($product, 'name') : 'Create New Product' }}</h2><p>Save directly to the seller catalog database.</p></div>
            <a href="{{ route('seller.products') }}" class="sl-btn sl-btn-ghost">Cancel</a>
        </div>

        <form class="sl-editor-layout" method="POST" enctype="multipart/form-data" action="{{ $editing ? route('seller.products.update', data_get($product, 'db_id')) : route('seller.products.store') }}">
            @csrf
            @if($editing) @method('PUT') @endif
            <div class="sl-editor-main">
                <section class="sl-card sl-form-section">
                    <header class="sl-form-section-head"><span>1</span><div><h3>Product Information</h3><p>These values are saved to the products table.</p></div></header>
                    <div class="sl-form-grid">
                        <label class="sl-field sl-span-2"><span>Product name <b>*</b></span><input name="name" type="text" value="{{ old('name', data_get($product, 'name')) }}" required></label>
                        <label class="sl-field"><span>Line of Business</span><input value="{{ $lineOfBusinessCategory?->name ?? 'Not assigned' }}" readonly></label>
                        <label class="sl-field"><span>Subcategory</span><select name="category_id"><option value="">Select subcategory</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', data_get($product, 'category_id')) === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
                        <label class="sl-field"><span>SKU</span><input value="{{ data_get($product, 'sku', 'Generated after save') }}" disabled><small>Generated automatically by LIKHAE.</small></label>
                        <label class="sl-field sl-span-2"><span>Description <b>*</b></span><textarea name="description" rows="6" maxlength="3000" required>{{ old('description', data_get($product, 'description')) }}</textarea><small><span data-character-count>0</span>/3000 characters</small></label>
                        <label class="sl-field sl-span-2"><span>Product Specifications</span><textarea name="specifications_text" rows="5" maxlength="2000" placeholder="e.g. Material: Cotton canvas&#10;Dimensions: 30 x 20 x 12 cm&#10;Care: Wipe clean with a dry cloth">{{ $specificationsText }}</textarea><small>Optional details such as material, dimensions, care, capacity, or inclusions.</small></label>
                        <div class="sl-field sl-span-2"><span>Primary image</span><label class="sl-upload-box"><input name="image" type="file" accept="image/*" data-image-input><svg viewBox="0 0 24 24"><path d="M12 16V4M7 9l5-5 5 5M4 15v5h16v-5"/></svg><strong>Upload product image</strong><small>JPG, PNG, WebP · maximum 5 MB</small></label><div class="sl-image-preview" data-image-preview>@if(data_get($product,'image'))<img src="{{ data_get($product,'image') }}" alt="">@endif</div></div>
                        <div class="sl-field sl-span-2"><span>Additional images</span><label class="sl-upload-box"><input name="images[]" type="file" accept="image/*" multiple data-images-input><svg viewBox="0 0 24 24"><path d="M12 16V4M7 9l5-5 5 5M4 15v5h16v-5"/></svg><strong>Upload multiple product images</strong><small>JPG, PNG, WebP - maximum 5 MB each</small></label><div class="sl-image-preview" data-images-preview></div>
                            @error('images')<small class="sl-error">{{ $message }}</small>@enderror
                            @if($editing && data_get($product, 'image'))<small>Uploaded images are stored with this product. The primary image is shown in the catalog.</small>@endif
                        </div>
                    </div>
                </section>

                <section class="sl-card sl-form-section">
                    <header class="sl-form-section-head"><span>2</span><div><h3>Pricing & Inventory</h3><p>Price, stock, and listing state update immediately after save.</p></div></header>
                    <div class="sl-form-grid sl-form-grid-4">
                        <label class="sl-field"><span>Price <b>*</b></span><div class="sl-input-prefix"><i>₱</i><input name="price" type="number" min="0" step="0.01" value="{{ old('price', data_get($product, 'price')) }}" required></div></label>
                        <label class="sl-field"><span>Stock <b>*</b></span><input name="stock" type="number" min="0" value="{{ old('stock', data_get($product, 'stock', 0)) }}" required></label>
                        <label class="sl-field"><span>Weight (grams) <b>*</b></span><input name="weight_grams" type="number" min="1" value="{{ old('weight_grams', data_get($product, 'weight_grams', 1)) }}" required><small>Enter the shipping weight of one unit.</small></label>
                        <label class="sl-field"><span>Listing Status</span><select name="listing_status"><option value="active" @selected(old('listing_status', strtolower(data_get($product,'status','active'))) === 'active')>Active</option><option value="draft" @selected(old('listing_status', strtolower(data_get($product,'status',''))) === 'draft')>Draft</option><option value="archived" @selected(old('listing_status', strtolower(data_get($product,'status',''))) === 'archived')>Archived</option></select><small>Admin/compliance status is managed separately.</small></label>
                    </div>
                    <div class="sl-variation-head">
                        <div><strong>Product Variations</strong><small>Optional size, color, SKU, or stock options saved as product variation rows.</small></div>
                        <button type="button" class="sl-btn sl-btn-soft sl-btn-sm" data-add-variation>Add Variation</button>
                    </div>
                    <div class="sl-variation-table" data-variation-list>
                        <div class="sl-variation-row sl-variation-labels" aria-hidden="true"><span>Name</span><span>Option</span><span>SKU</span><span>Price</span><span>Stock</span><span>Weight (g)</span><span></span></div>
                        @foreach($variationRows as $row)
                            <div class="sl-variation-row">
                                <input name="variation_id[]" type="hidden" value="{{ data_get($row, 'id') }}">
                                <input name="variation_name[]" value="{{ data_get($row, 'name') }}" placeholder="e.g. Color" aria-label="Variation name">
                                <input name="variation_value[]" value="{{ data_get($row, 'value') }}" placeholder="e.g. Navy Blue" aria-label="Variation option">
                                <input name="variation_sku[]" value="{{ data_get($row, 'sku') }}" placeholder="SKU" aria-label="Variation SKU">
                                <input name="variation_price[]" type="number" min="0" step="0.01" value="{{ data_get($row, 'price') }}" placeholder="Price" aria-label="Variation price">
                                <input name="variation_stock[]" type="number" min="0" value="{{ data_get($row, 'stock') }}" aria-label="Variation stock">
                                <input name="variation_weight_grams[]" type="number" min="1" value="{{ data_get($row, 'weight_grams', data_get($product, 'weight_grams')) }}" placeholder="Weight (g)" aria-label="Weight of one unit in grams" @if($errors->has('variation_weight_grams.'.$loop->index)) aria-invalid="true" @endif>
                                <button type="button" class="sl-icon-btn" data-remove-row aria-label="Remove variation">&times;</button>
                            </div>
                            @if($errors->has('variation_weight_grams.'.$loop->index))<p class="sl-variation-error">Variation {{ $loop->iteration }} weight: {{ $errors->first('variation_weight_grams.'.$loop->index) }}</p>@endif
                        @endforeach
                    </div>
                </section>
            </div>
            @if($errors->any())<div class="sl-form-errors" role="alert"><strong>Product could not be published.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <aside class="sl-editor-side">
                <section class="sl-card sl-publish-card"><h3>Publish Listing</h3><div class="sl-quality-list"><p><span>✓</span> Product details</p><p><span>✓</span> Pricing and stock</p><p><span>✓</span> Database-backed listing</p></div><button type="submit" class="sl-btn sl-btn-primary sl-btn-block">{{ $editing ? 'Save Changes' : 'Publish Product' }}</button></section>
            </aside>
        </form>

    @elseif ($currentMode === 'inventory')
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Stock Control</span><h2>Inventory Overview</h2><p>Each update button now writes to the database.</p></div><a href="{{ route('seller.products.export') }}" class="sl-btn sl-btn-ghost">Export CSV</a></div>
        <section class="sl-stat-grid sl-stat-grid-4">
            <x-seller.stat-card label="Total SKUs" :value="$sellerProducts->count()" icon="products" />
            <x-seller.stat-card label="In Stock" :value="$sellerProducts->where('stock','>',0)->count()" icon="products" />
            <x-seller.stat-card label="Low Stock" :value="$sellerProducts->where('stock','>',0)->where('stock','<=',5)->count()" direction="down" change="Needs review" icon="inventory" />
            <x-seller.stat-card label="Out of Stock" :value="$sellerProducts->where('stock',0)->count()" direction="down" change="Action needed" icon="inventory" />
        </section>
        <section class="sl-card">
            <div class="sl-table-toolbar"><div class="sl-search-input"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" placeholder="Search product or SKU" data-product-search></div><select class="sl-select" data-product-status-filter><option value="all">All stock levels</option><option value="low">Low stock</option><option value="out">Out of stock</option><option value="healthy">Healthy</option></select></div>
            <div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Product</th><th>SKU</th><th>Available</th><th>Status</th><th>Update</th></tr></thead><tbody>
                @forelse(($inventoryProducts ?? $sellerProducts) as $item)
                    @php $level = $item['stock'] === 0 ? 'out' : ($item['stock'] <= 5 ? 'low' : 'healthy'); @endphp
                    <tr data-product-row data-product-name="{{ mb_strtolower($item['name'].' '.$item['sku']) }}" data-product-status="{{ $level }}"><td><div class="sl-product-cell">@if($item['image'])<img src="{{ $item['image'] }}" alt="">@endif<strong>{{ $item['name'] }}</strong></div></td><td>{{ $item['sku'] }}</td><td><strong>{{ $item['stock'] }}</strong></td><td><span class="sl-status {{ $level === 'out' ? 'is-danger' : ($level === 'low' ? 'is-warning' : 'is-success') }}">{{ ucfirst($level) }}</span></td><td><form method="POST" action="{{ route('seller.products.stock', $item['db_id']) }}" class="sl-inline-stock">@csrf @method('PATCH')<input name="stock" type="number" min="0" value="{{ $item['stock'] }}"><button class="sl-btn sl-btn-soft sl-btn-sm" type="submit">Update</button></form></td></tr>
                @empty
                    <tr><td colspan="5"><div class="sl-empty-state"><h3>No inventory records yet</h3><p>Published products will appear here after they are saved.</p></div></td></tr>
                @endforelse
            </tbody></table></div><div class="sl-no-results" data-product-empty hidden>No products match your filters.</div>
        </section>
    @else
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Catalog</span><h2>Product Management</h2><p>Create, edit, archive, restore, filter, and export listings.</p></div><a href="{{ route('seller.products', ['mode' => 'add']) }}" class="sl-btn sl-btn-primary"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Add Product</a></div>
        <section class="sl-mini-stats"><div><span>All Products</span><strong>{{ $sellerProducts->count() }}</strong></div><div><span>Active</span><strong>{{ $sellerProducts->where('status','Active')->count() }}</strong></div><div><span>Low Stock</span><strong>{{ $sellerProducts->where('stock','>',0)->where('stock','<=',5)->count() }}</strong></div><div><span>Archived</span><strong>{{ $sellerProducts->where('status','Archived')->count() }}</strong></div></section>
        <section class="sl-card">
            <div class="sl-table-toolbar"><div class="sl-search-input"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" placeholder="Search product or SKU" data-product-search></div><div class="sl-toolbar-group"><select class="sl-select" data-product-status-filter><option value="all">All status</option><option value="active">Active</option><option value="archived">Archived</option><option value="draft">Draft</option></select><a href="{{ route('seller.products.export') }}" class="sl-btn sl-btn-ghost">Export CSV</a></div></div>
            <div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Sold</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead><tbody>
                @foreach($sellerProducts as $item)
                    <tr data-product-row data-product-name="{{ mb_strtolower($item['name'].' '.$item['sku']) }}" data-product-status="{{ mb_strtolower($item['status']) }}"><td><div class="sl-product-cell">@if($item['image'])<img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">@endif<div><strong>{{ $item['name'] }}</strong><small>{{ $item['sku'] }}</small></div></div></td><td>{{ $item['category'] }}</td><td><strong>₱{{ number_format($item['price'],2) }}</strong></td><td>{{ $item['stock'] }}</td><td>{{ $item['sold'] }}</td><td>★ {{ number_format($item['rating'],1) }}</td><td><span class="sl-status {{ $item['status']==='Active'?'is-success':'is-neutral' }}">{{ $item['status'] }}</span></td><td><div class="sl-table-actions"><a href="{{ route('seller.products',['mode'=>'edit','product'=>$item['id']]) }}" class="sl-icon-btn" title="Edit">✎</a><form method="POST" action="{{ route('seller.products.toggle',$item['db_id']) }}">@csrf @method('PATCH')<button class="sl-icon-btn" type="submit" title="{{ $item['status']==='Archived'?'Restore':'Archive' }}">{{ $item['status']==='Archived'?'↺':'□' }}</button></form></div></td></tr>
                @endforeach
            </tbody></table></div><div class="sl-no-results" data-product-empty hidden>No products match your filters.</div><footer class="sl-table-footer"><span>Showing {{ $sellerProducts->count() }} products</span></footer>
        </section>
    @endif
</div>
@endsection
