@extends('layouts.seller')

@php
    $currentMode = $mode ?? request('mode', 'list');
    $editing = $currentMode === 'edit';
    $product = $editing ? $selectedProduct : null;
    $productImageFallback = asset('images/product-placeholder.svg');
    $pageTitle = match ($currentMode) { 'add' => 'Add Product', 'edit' => 'Edit Product', 'inventory' => 'Inventory', default => 'Products' };
    $variationRows = collect(old('variants', data_get($product, 'variation_rows', [])));
    if ($variationRows->isEmpty()) {
        $variationRows = collect([['id' => null, 'values' => '', 'sku' => '', 'price' => '', 'stock' => '']]);
    }
    $optionRows = collect(old('options', data_get($product, 'option_rows', [])));
    if ($optionRows->isEmpty()) {
        $optionRows = collect([['name' => '', 'values' => '']]);
    }
    $storedHasVariations = collect(data_get($product, 'option_rows', []))->isNotEmpty() || collect(data_get($product, 'variation_rows', []))->count() > 1;
    $productType = old('product_type', $storedHasVariations ? 'variations' : 'single');
    $singleVariant = collect(data_get($product, 'variation_rows', []))->first();
    $listingStatus = old('listing_status', strtolower(data_get($product, 'status', 'draft')));
@endphp

@section('title', $pageTitle)
@section('active', 'products')
@section('subtitle', $currentMode === 'inventory' ? 'Monitor stock levels and prevent missed sales.' : ($editing ? 'Update listing information, pricing, and inventory.' : ($currentMode === 'add' ? 'Create a complete and buyer-ready product listing.' : 'Manage product listings, pricing, stock, and visibility.')))

@section('content')
<div class="sl-page">
    @if(session('product_saved'))
        @php
            $savedProduct = session('product_saved');
            $wasPublished = data_get($savedProduct, 'status') === 'ACTIVE';
        @endphp
        <dialog class="sl-product-save-dialog" data-product-save-dialog aria-labelledby="product-save-title" aria-describedby="product-save-message">
            <span class="sl-product-save-dialog__icon" aria-hidden="true">✓</span>
            <h2 id="product-save-title">{{ $wasPublished ? 'Product published' : 'Draft saved' }}</h2>
            <p id="product-save-message"><strong>{{ data_get($savedProduct, 'name') }}</strong> {{ $wasPublished ? 'is now published and available to buyers.' : 'has been saved as a draft and is not visible to buyers.' }}</p>
            <form method="dialog"><button class="sl-btn sl-btn-primary" autofocus>Continue editing</button></form>
        </dialog>
        <style>
            .sl-product-save-dialog{width:min(420px,calc(100% - 32px));padding:30px;border:1px solid #eadccc;border-radius:20px;background:#fffdf9;color:#3b211b;text-align:center;box-shadow:0 24px 80px rgba(35,20,17,.25)}
            .sl-product-save-dialog::backdrop{background:rgba(35,20,17,.58);backdrop-filter:blur(3px)}
            .sl-product-save-dialog__icon{display:grid;width:52px;height:52px;margin:0 auto 14px;place-items:center;border-radius:50%;background:#eaf7ef;color:#256f4a;font-size:28px;font-weight:800}
            .sl-product-save-dialog h2{margin:0;font-size:21px;font-weight:800}
            .sl-product-save-dialog p{margin:10px 0 22px;color:#705c54;font-size:14px;line-height:1.6}
            .sl-product-save-dialog form{display:flex;justify-content:center}
            html.dark .sl-product-save-dialog{border-color:#49342b;background:#241a17;color:#fff8f2}
            html.dark .sl-product-save-dialog p{color:#c8b7ad}
        </style>
        <script>
            document.querySelector('[data-product-save-dialog]')?.showModal();
        </script>
    @elseif(session('status'))
        <div class="sl-alert sl-alert-success" role="status">{{ session('status') }}</div>
    @endif
    @if (in_array($currentMode, ['add', 'edit'], true))
        <div class="sl-page-toolbar">
            <div><a href="{{ route('seller.products') }}" class="sl-editor-back">&larr; Back to Products</a><h2>{{ $editing ? 'Edit '.data_get($product, 'name') : 'Create New Product' }}</h2><p>Add product information, pricing, images, and availability.</p></div>
        </div>

        <form class="sl-editor-layout" method="POST" enctype="multipart/form-data" action="{{ $editing ? route('seller.products.update', data_get($product, 'db_id')) : route('seller.products.store') }}">
            @csrf
            @if($editing) @method('PUT') @endif
            <input type="hidden" name="listing_status" value="draft" data-listing-status-field>
            <div class="sl-editor-main">
                <section class="sl-card sl-form-section">
                    <header class="sl-form-section-head"><span>1</span><div><h3>Product Details</h3><p>Core information stored on the product record.</p></div></header>
                    <div class="sl-form-grid">
                        <label class="sl-field sl-span-2"><span>Product name <b>*</b></span><input name="name" type="text" value="{{ old('name', data_get($product, 'name')) }}" required></label>
                        <label class="sl-field"><span>Line of Business</span><input value="{{ $lineOfBusinessCategory?->name ?? 'Not assigned' }}" readonly></label>
                        <label class="sl-field"><span>Subcategory <b>*</b></span><select name="category_id" required><option value="">Select subcategory</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', data_get($product, 'category_id')) === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
                        <label class="sl-field sl-span-2"><span>Description</span><textarea name="description" rows="7" maxlength="10000" placeholder="Describe the product, materials, dimensions, care instructions, and inclusions.">{{ old('description', data_get($product, 'description')) }}</textarea><small><span data-character-count>0</span>/10000 characters</small></label>
                        <div class="sl-field sl-span-2"><span>Primary image</span><label class="sl-upload-box"><input name="image" type="file" accept="image/*" data-image-input><svg viewBox="0 0 24 24"><path d="M12 16V4M7 9l5-5 5 5M4 15v5h16v-5"/></svg><strong>Upload product image</strong><small>JPG, PNG, WebP · maximum 5 MB</small></label><div class="sl-image-preview" data-image-preview>@if(data_get($product,'image'))<img src="{{ data_get($product,'image') }}" alt="">@endif</div></div>
                        <div class="sl-field sl-span-2"><span>Additional images</span><label class="sl-upload-box"><input name="images[]" type="file" accept="image/*" multiple data-images-input><svg viewBox="0 0 24 24"><path d="M12 16V4M7 9l5-5 5 5M4 15v5h16v-5"/></svg><strong>Upload multiple product images</strong><small>JPG, PNG, WebP · maximum 5 MB each, 10 photos per product including the primary image</small></label>
                            @if($editing && collect(data_get($product, 'images', []))->isNotEmpty())
                                <div class="sl-image-preview" aria-label="Saved product images">
                                    @foreach(data_get($product, 'images', []) as $savedImage)
                                        <img src="{{ data_get($savedImage, 'url') }}" alt="{{ data_get($savedImage, 'alt', data_get($product, 'name')) }}" loading="lazy">
                                    @endforeach
                                </div>
                            @endif
                            <div class="sl-image-preview" data-images-preview></div>
                            @error('images')<small class="sl-error">{{ $message }}</small>@enderror
                            @if($editing && data_get($product, 'image'))<small>Uploaded images are stored with this product. The primary image is shown in the catalog.</small>@endif
                        </div>
                    </div>
                </section>

                <section class="sl-card sl-form-section">
                    <header class="sl-form-section-head"><span>2</span><div><h3>Pricing &amp; Variations</h3><p>Choose a single product or database-backed variants.</p></div></header>
                    <div class="sl-product-type-grid" data-product-type-editor><label class="sl-product-type-card"><input type="radio" name="product_type" value="single" @checked($productType === 'single')><strong>Single Product</strong><small>One price, stock, and SKU.</small></label><label class="sl-product-type-card"><input type="radio" name="product_type" value="variations" @checked($productType === 'variations')><strong>Has Variations</strong><small>Different sizes, colors, prices, or stock.</small></label></div>
                    <div data-single-product-fields>
                    <input type="hidden" name="single_variant_id" value="{{ data_get($singleVariant, 'id') }}">
                    <div class="sl-form-grid sl-form-grid-4">
                        <label class="sl-field"><span>Price <b>*</b></span><div class="sl-input-prefix"><i>₱</i><input name="price" type="number" min="0" step="0.01" value="{{ old('price', data_get($product, 'price')) }}" required></div></label>
                        <label class="sl-field"><span>Stock <b>*</b></span><input name="stock" type="number" min="0" value="{{ old('stock', data_get($product, 'stock', 0)) }}" required></label>
                        <label class="sl-field sl-span-2"><span>SKU</span><input name="sku" maxlength="100" value="{{ old('sku', data_get($singleVariant, 'sku')) }}" placeholder="Generated automatically when blank"><small>Stored on product_variants.</small></label>
                    </div>
                    </div>
                    <div data-variation-product-fields>
                    <div class="sl-option-list" data-option-list>@foreach($optionRows as $index => $option)<div class="sl-option-row"><label class="sl-field"><span>Variation type</span><input name="options[{{ $index }}][name]" value="{{ data_get($option, 'name') }}" placeholder="e.g. Size"></label><label class="sl-field"><span>Options</span><input name="options[{{ $index }}][values]" value="{{ data_get($option, 'values') }}" placeholder="Small, Medium, Large"></label><button type="button" class="sl-icon-btn" data-remove-option>&times;</button></div>@endforeach</div>
                    <div class="sl-option-actions"><button type="button" class="sl-btn sl-btn-soft sl-btn-sm" data-add-option>Add option type</button><button type="button" class="sl-btn sl-btn-ghost sl-btn-sm" data-generate-variants>Generate variation cards</button></div>
                    <div class="sl-variation-head">
                        <div><strong>Variation Details</strong><small>Each row maps directly to product_variants.</small></div>
                    </div>
                    <div class="sl-variation-table" data-variation-list>
                        <div class="sl-variation-row sl-variation-labels" aria-hidden="true"><span>Option values</span><span>SKU</span><span>Price</span><span>Stock</span><span></span></div>
                        @foreach($variationRows as $index => $row)
                            <div class="sl-variation-row">
                                <input name="variants[{{ $index }}][id]" type="hidden" value="{{ data_get($row, 'id') }}">
                                <input name="variants[{{ $index }}][values]" value="{{ data_get($row, 'values') }}" placeholder="e.g. Small, Red" aria-label="Option values">
                                <input name="variants[{{ $index }}][sku]" value="{{ data_get($row, 'sku') }}" placeholder="Auto-generated SKU" aria-label="Variant SKU">
                                <input name="variants[{{ $index }}][price]" type="number" min="0" step="0.01" value="{{ data_get($row, 'price') }}" placeholder="Price" aria-label="Variant price">
                                <input name="variants[{{ $index }}][stock]" type="number" min="0" value="{{ data_get($row, 'stock') }}" aria-label="Variant stock">
                                <input name="variants[{{ $index }}][is_active]" type="hidden" value="1">
                                <button type="button" class="sl-icon-btn" data-remove-variant aria-label="Remove variation">&times;</button>
                            </div>
                        @endforeach
                    </div>
                    </div>
                </section>
            </div>
            @if($errors->any())<div class="sl-form-errors" role="alert"><strong>Product could not be saved.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <aside class="sl-editor-side">
                <section class="sl-card sl-publish-card sl-publish-card-current" data-product-status-card><h3>Product Status</h3><p class="sl-sidebar-help">Complete the required information before publishing.</p><div class="sl-completion-list"><p data-check="name"><span></span> Product information</p><p data-check="category"><span></span> Subcategory</p><p data-check="image"><span></span> Primary image</p><p data-check="pricing"><span></span> Pricing</p><p data-check="inventory"><span></span> Inventory</p><p data-check="variations"><span></span> Variations</p></div><button type="submit" data-listing-status="draft" onclick="this.form.elements.listing_status.value='draft'" class="sl-btn sl-btn-ghost sl-btn-block">Save as Draft</button><button type="submit" data-listing-status="active" onclick="this.form.elements.listing_status.value='active'" class="sl-btn sl-btn-primary sl-btn-block" data-publish-product>{{ $editing && $listingStatus === 'active' ? 'Save Changes' : 'Publish Product' }}</button></section>
                <section class="sl-card sl-buyer-preview" data-seller-buyer-preview><header><h3>Buyer Preview</h3><p>This is how your product may appear to customers.</p><small>Preview only - your product is not published yet.</small></header><img src="{{ data_get($product, 'image', $productImageFallback) }}" alt="Product preview" data-preview-image><div><span data-preview-category>Subcategory</span><h4 data-preview-name>{{ old('name', data_get($product, 'name', 'Your product name')) ?: 'Your product name' }}</h4><strong data-preview-price>&#8369;0.00</strong><p data-preview-stock>0 available</p><div data-preview-options></div><button type="button" disabled>Add to Cart</button><button type="button" disabled>Buy Now</button></div></section>
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
                    <tr data-product-row data-product-name="{{ mb_strtolower($item['name'].' '.$item['sku']) }}" data-product-status="{{ $level }}"><td><div class="sl-product-cell"><img src="{{ $item['image'] ?: $productImageFallback }}" alt="{{ $item['name'] }}" loading="lazy" onerror="this.onerror=null;this.src='{{ $productImageFallback }}'"><strong>{{ $item['name'] }}</strong></div></td><td>{{ $item['sku'] }}</td><td><strong>{{ $item['stock'] }}</strong></td><td><span class="sl-status {{ $level === 'out' ? 'is-danger' : ($level === 'low' ? 'is-warning' : 'is-success') }}">{{ ucfirst($level) }}</span></td><td><form method="POST" action="{{ route('seller.products.stock', $item['db_id']) }}" class="sl-inline-stock">@csrf @method('PATCH')<input name="stock" type="number" min="0" value="{{ $item['stock'] }}"><button class="sl-btn sl-btn-soft sl-btn-sm" type="submit">Update</button></form></td></tr>
                @empty
                    <tr><td colspan="5"><div class="sl-empty-state"><h3>No inventory records yet</h3><p>Published products will appear here after they are saved.</p></div></td></tr>
                @endforelse
            </tbody></table></div><div class="sl-no-results" data-product-empty hidden>No products match your filters.</div>
        </section>
    @else
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Catalog</span><h2>Product Management</h2><p>Create, edit, archive, restore, filter, and export listings.</p></div><a href="{{ route('seller.products', ['mode' => 'add']) }}" class="sl-btn sl-btn-primary"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Add Product</a></div>
        <section class="sl-mini-stats"><div><span>All Products</span><strong>{{ $sellerProducts->count() }}</strong></div><div><span>Active</span><strong>{{ $sellerProducts->where('status','Active')->count() }}</strong></div><div><span>Low Stock</span><strong>{{ $sellerProducts->where('stock','>',0)->where('stock','<=',5)->count() }}</strong></div><div><span>Archived</span><strong>{{ $sellerProducts->where('status','Archived')->count() }}</strong></div></section>
        <section class="sl-card">
            <div class="sl-table-toolbar"><div class="sl-search-input"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" placeholder="Search product or SKU" data-product-search></div><div class="sl-toolbar-group"><select class="sl-select" data-product-status-filter><option value="all">All status</option><option value="active">Active</option><option value="archived">Archived</option><option value="draft">Draft</option></select><a href="{{ route('seller.products.trash') }}" class="sl-btn sl-btn-ghost">Recently Deleted</a><a href="{{ route('seller.products.export') }}" class="sl-btn sl-btn-ghost">Export CSV</a></div></div>
            <div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Sold</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead><tbody>
                @foreach($sellerProducts as $item)
                    <tr data-product-row data-product-name="{{ mb_strtolower($item['name'].' '.$item['sku']) }}" data-product-status="{{ mb_strtolower($item['status']) }}"><td><div class="sl-product-cell">@if($item['image'])<img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">@endif<div><strong>{{ $item['name'] }}</strong><small>{{ $item['sku'] }}</small></div></div></td><td>{{ $item['category'] }}</td><td><strong>₱{{ number_format($item['price'],2) }}</strong></td><td>{{ $item['stock'] }}</td><td>{{ $item['sold'] }}</td><td>★ {{ number_format($item['rating'],1) }}</td><td><span class="sl-status {{ $item['status']==='Active'?'is-success':'is-neutral' }}">{{ $item['status'] }}</span></td><td><div class="sl-table-actions"><a href="{{ route('seller.products',['mode'=>'edit','product'=>$item['id']]) }}" class="sl-icon-btn" title="Edit">✎</a><form method="POST" action="{{ route('seller.products.toggle',$item['db_id']) }}">@csrf @method('PATCH')<button class="sl-icon-btn" type="submit" title="{{ $item['status']==='Archived'?'Restore':'Archive' }}">{{ $item['status']==='Archived'?'↺':'□' }}</button></form><form method="POST" action="{{ route('seller.products.destroy',$item['db_id']) }}" onsubmit="return confirm('Move this product to Recently Deleted? It can be restored for 30 days.');">@csrf @method('DELETE')<button class="sl-icon-btn" type="submit" title="Move to Recently Deleted">×</button></form></div></td></tr>
                @endforeach
            </tbody></table></div><div class="sl-no-results" data-product-empty hidden>No products match your filters.</div><footer class="sl-table-footer"><span>Showing {{ $sellerProducts->count() }} products</span></footer>
        </section>
    @endif
</div>
@endsection
