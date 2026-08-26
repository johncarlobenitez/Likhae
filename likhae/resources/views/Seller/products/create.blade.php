@extends('Seller.layouts.app')
@section('title', 'Add Product — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/products.css') @endpush

@section('content')
<x-seller.page-header eyebrow="CATALOG" title="Add Product" description="Create a complete, customer-ready listing.">
    <x-slot:actions><button class="btn-secondary">Save Draft</button><button class="btn-secondary">Preview</button><button class="btn-primary">Publish Product</button></x-slot:actions>
</x-seller.page-header>

<form class="product-form-layout">
    <nav class="form-section-nav">
        @foreach(['Basic Information','Media','Variations','Pricing','Inventory','Shipping','Status'] as $i=>$s)
        <a href="#section-{{ $i+1 }}" class="{{ $i===0?'is-active':'' }}"><span>{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span>{{ $s }}</a>
        @endforeach
    </nav>

    <div class="space-y-5">
        <section id="section-1" class="panel p-5 sm:p-6">
            <div class="form-section-title"><span>01</span><div><h2>Basic Information</h2><p>Make your product easy to understand and discover.</p></div></div>
            <div class="form-grid mt-5">
                <div class="sm:col-span-2"><label class="form-label">Product Name *</label><input class="form-input" placeholder="e.g. Handwoven Rattan Tote Bag"></div>
                <div><label class="form-label">Category *</label><select class="form-input"><option>Local Products</option></select></div>
                <div><label class="form-label">Subcategory</label><select class="form-input"><option>Bags & Accessories</option></select></div>
                <div><label class="form-label">Brand</label><input class="form-input" placeholder="Optional"></div>
                <div><label class="form-label">Condition</label><select class="form-input"><option>New</option></select></div>
                <div class="sm:col-span-2"><label class="form-label">Description *</label><textarea class="form-input min-h-40" placeholder="Describe materials, dimensions, care instructions, and what makes this product special."></textarea></div>
            </div>
        </section>

        <section id="section-2" class="panel p-5 sm:p-6">
            <div class="form-section-title"><span>02</span><div><h2>Product Media</h2><p>Upload clear images. The first image becomes the primary listing image.</p></div></div>
            <div class="media-grid mt-5"><label class="media-upload"><span>+</span><strong>Add Images</strong><small>JPG / PNG · Up to 8 images</small><input type="file" multiple hidden></label>@for($i=0;$i<3;$i++)<div class="media-placeholder"><span>{{ $i===0?'PRIMARY':'IMAGE '.($i+1) }}</span></div>@endfor</div>
        </section>

        <section id="section-3" class="panel p-5 sm:p-6">
            <div class="form-section-title"><span>03</span><div><h2>Variations</h2><p>Create combinations such as Color × Size.</p></div></div>
            <div class="variation-builder mt-5"><div><label class="form-label">Variation Name</label><input class="form-input" value="Color"></div><div><label class="form-label">Options</label><input class="form-input" value="Natural, Brown, Black"></div><button type="button" class="btn-secondary">Add Variation</button></div>
        </section>

        <section id="section-4" class="panel p-5 sm:p-6">
            <div class="form-section-title"><span>04</span><div><h2>Pricing</h2><p>Cost price is private and only used for your own profit reporting.</p></div></div>
            <div class="form-grid mt-5"><div><label class="form-label">Regular Price *</label><input class="form-input" value="899"></div><div><label class="form-label">Sale Price</label><input class="form-input" value="799"></div><div><label class="form-label">Cost Price</label><input class="form-input" value="450"></div></div>
        </section>

        <section id="section-5" class="panel p-5 sm:p-6">
            <div class="form-section-title"><span>05</span><div><h2>Inventory</h2><p>Set SKU, available units, and low-stock warning.</p></div></div>
            <div class="form-grid mt-5"><div><label class="form-label">SKU</label><input class="form-input" value="RAT-TOTE-NAT"></div><div><label class="form-label">Stock *</label><input class="form-input" value="36"></div><div><label class="form-label">Low Stock Alert</label><input class="form-input" value="8"></div></div>
        </section>

        <section id="section-6" class="panel p-5 sm:p-6">
            <div class="form-section-title"><span>06</span><div><h2>Shipping</h2><p>Accurate dimensions help calculate delivery requirements.</p></div></div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4"><div><label class="form-label">Weight (kg)</label><input class="form-input" value=".45"></div><div><label class="form-label">Length (cm)</label><input class="form-input" value="35"></div><div><label class="form-label">Width (cm)</label><input class="form-input" value="12"></div><div><label class="form-label">Height (cm)</label><input class="form-input" value="28"></div></div>
        </section>

        <section id="section-7" class="panel p-5 sm:p-6">
            <div class="form-section-title"><span>07</span><div><h2>Product Status</h2><p>Publish now or save the listing as a draft.</p></div></div>
            <div class="mt-5 flex flex-wrap gap-3"><label class="status-choice"><input type="radio" name="status" checked><span><strong>Publish</strong><small>Visible to customers immediately</small></span></label><label class="status-choice"><input type="radio" name="status"><span><strong>Draft</strong><small>Keep private until ready</small></span></label></div>
        </section>
    </div>
</form>
@endsection
