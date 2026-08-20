@extends('Seller.layouts.app')
@section('title', 'Store Profile — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/store.css') @endpush

@section('content')
<x-seller.page-header eyebrow="STORE" title="Store Profile" description="Manage how customers see and understand your shop.">
    <x-slot:actions><button class="btn-secondary">Preview Store</button><button class="btn-primary">Save Changes</button></x-slot:actions>
</x-seller.page-header>

<section class="store-banner-card"><div class="store-banner-placeholder"><button class="btn-secondary">Change Banner</button></div><div class="store-profile-row"><div class="store-logo-placeholder">ML</div><div><h2>Maria’s Local Finds</h2><p>Cebu City, Cebu · Local Products</p></div><x-seller.status-badge status="Active" class="ml-auto"/></div></section>

<div class="mt-5 grid gap-5 xl:grid-cols-[1fr_350px]">
    <section class="panel p-5 sm:p-6">
        <p class="panel-eyebrow">STORE INFORMATION</p>
        <div class="form-grid mt-5">
            <div><label class="form-label">Store Name</label><input class="form-input" value="Maria’s Local Finds"></div>
            <div><label class="form-label">Business Name</label><input class="form-input" value="Maria Santos Trading"></div>
            <div><label class="form-label">Category</label><select class="form-input"><option>Local Products</option></select></div>
            <div><label class="form-label">Location</label><input class="form-input" value="Cebu City, Cebu"></div>
            <div class="sm:col-span-2"><label class="form-label">Store Description</label><textarea class="form-input min-h-32">Thoughtfully selected Filipino-made goods from local makers and small businesses.</textarea></div>
            <div><label class="form-label">Contact Number</label><input class="form-input" value="+63 917 123 4567"></div>
            <div><label class="form-label">Business Email</label><input class="form-input" value="hello@mariaslocalfinds.ph"></div>
        </div>
    </section>

    <aside class="panel p-5">
        <p class="panel-eyebrow">STORE PERFORMANCE</p>
        <div class="store-stats mt-4"><div><span>Rating</span><strong>4.8</strong></div><div><span>Followers</span><strong>3,284</strong></div><div><span>Products</span><strong>128</strong></div><div><span>Response Rate</span><strong>96%</strong></div><div><span>Fulfillment Rate</span><strong>98.7%</strong></div></div>
    </aside>
</div>
@endsection
