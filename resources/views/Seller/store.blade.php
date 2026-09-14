@extends('layouts.seller')

@php $currentTab = $tab ?? request('tab','profile'); @endphp

@section('title','Store')
@section('active','store')
@section('subtitle','Manage your public shop identity, information, and operating settings.')

@section('content')
<div class="sl-page">
    <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Store Management</span><h2>Your Shop</h2><p>Store profile and settings now persist in seller_profiles.</p></div><a href="{{ route('seller.products') }}" class="sl-btn sl-btn-ghost">Preview Catalog</a></div>
    <nav class="sl-tabs"><a href="{{ route('seller.store',['tab'=>'profile']) }}" class="{{ $currentTab==='profile'?'is-active':'' }}">Shop Profile</a><a href="{{ route('seller.store',['tab'=>'settings']) }}" class="{{ $currentTab==='settings'?'is-active':'' }}">Store Settings</a></nav>

    @if($currentTab === 'settings')
        <form class="sl-settings-layout" method="POST" action="{{ route('seller.store.update') }}">@csrf @method('PUT')
            <input type="hidden" name="shop_name" value="{{ $storeProfile->shop_name }}">
            <div class="sl-settings-nav sl-card"><strong>Settings</strong><a href="#operations" class="is-active">Operations</a><a href="#orders">Order Preferences</a><a href="#visibility">Store Visibility</a></div>
            <div class="sl-settings-main">
                <section class="sl-card sl-form-section" id="operations"><header class="sl-card-head"><div><h2>Store Operations</h2><p>Set handling time and operating schedule.</p></div></header><div class="sl-form-grid"><label class="sl-field"><span>Business days</span><input name="business_days" value="{{ old('business_days',$storeProfile->business_days) }}"></label><label class="sl-field"><span>Business hours</span><input name="business_hours" value="{{ old('business_hours',$storeProfile->business_hours) }}"></label><label class="sl-field"><span>Default preparation days</span><input name="processing_days" type="number" min="1" max="30" value="{{ old('processing_days',$storeProfile->processing_days) }}"></label><label class="sl-field"><span>Order cutoff</span><input name="order_cutoff" value="{{ old('order_cutoff',$storeProfile->order_cutoff) }}" placeholder="e.g. 2:00 PM"></label></div></section>
                <section class="sl-card sl-form-section" id="orders"><header class="sl-card-head"><div><h2>Order Preferences</h2><p>Control automatic acceptance behavior.</p></div></header><div class="sl-setting-switch"><div><strong>Automatic order acceptance</strong><p>Enable automatic acceptance when stock is available.</p></div><label class="sl-switch"><input type="hidden" name="auto_accept_orders" value="0"><input name="auto_accept_orders" value="1" type="checkbox" @checked($storeProfile->auto_accept_orders)><span></span></label></div></section>
                <section class="sl-card sl-form-section" id="visibility"><header class="sl-card-head"><div><h2>Store Visibility</h2><p>Control buyer discovery and vacation state.</p></div></header><div class="sl-setting-switch"><div><strong>Store is visible</strong><p>Buyers can discover active listings.</p></div><label class="sl-switch"><input type="hidden" name="store_visibility" value="0"><input name="store_visibility" value="1" type="checkbox" @checked($storeProfile->store_visibility)><span></span></label></div><div class="sl-setting-switch"><div><strong>Vacation mode</strong><p>Temporarily pause new orders.</p></div><label class="sl-switch"><input type="hidden" name="vacation_mode" value="0"><input name="vacation_mode" value="1" type="checkbox" @checked($storeProfile->vacation_mode)><span></span></label></div></section>
                <div class="sl-sticky-actions"><a href="{{ route('seller.store',['tab'=>'settings']) }}" class="sl-btn sl-btn-ghost">Discard</a><button type="submit" class="sl-btn sl-btn-primary">Save Settings</button></div>
            </div>
        </form>
    @else
        <form class="sl-store-layout" method="POST" action="{{ route('seller.store.update') }}">@csrf @method('PUT')
            <aside class="sl-card sl-store-preview"><div class="sl-shop-logo">{{ collect(explode(' ',$storeProfile->shop_name ?: 'LIKHAE Studio'))->filter()->take(2)->map(fn($w)=>mb_strtoupper(mb_substr($w,0,1)))->implode('') }}</div><h2>{{ $storeProfile->shop_name ?: 'LIKHAE Studio' }}</h2><p>{{ $storeProfile->description ?: 'Complete your shop description.' }}</p><span class="sl-verified-store">✓ Verified Seller</span></aside>
            <div class="sl-store-form">
                <section class="sl-card sl-form-section"><header class="sl-card-head"><div><h2>Shop Information</h2><p>This information is visible to buyers.</p></div></header><div class="sl-form-grid"><label class="sl-field sl-span-2"><span>Shop name <b>*</b></span><input name="shop_name" value="{{ old('shop_name',$storeProfile->shop_name) }}" required></label><label class="sl-field sl-span-2"><span>Tagline</span><input name="tagline" value="{{ old('tagline',$storeProfile->tagline) }}"></label><label class="sl-field sl-span-2"><span>Description</span><textarea name="description" rows="5">{{ old('description',$storeProfile->description) }}</textarea></label></div></section>
                <section class="sl-card sl-form-section"><header class="sl-card-head"><div><h2>Location & Business Hours</h2><p>Help buyers understand where and when you operate.</p></div></header><div class="sl-form-grid"><label class="sl-field sl-span-2"><span>Store location</span><input name="location" value="{{ old('location',$storeProfile->location) }}"></label><label class="sl-field"><span>Business days</span><input name="business_days" value="{{ old('business_days',$storeProfile->business_days) }}"></label><label class="sl-field"><span>Business hours</span><input name="business_hours" value="{{ old('business_hours',$storeProfile->business_hours) }}"></label></div></section>
                <div class="sl-sticky-actions"><a href="{{ route('seller.store') }}" class="sl-btn sl-btn-ghost">Cancel</a><button type="submit" class="sl-btn sl-btn-primary">Save Shop Profile</button></div>
            </div>
        </form>
    @endif
</div>
@endsection
