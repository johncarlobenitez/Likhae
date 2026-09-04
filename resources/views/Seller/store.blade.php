@extends('layouts.seller')

@php $currentTab = $tab ?? request('tab', 'profile'); @endphp

@section('title', 'Store')
@section('active', 'store')
@section('subtitle', 'Manage your public shop identity, information, and operating settings.')

@section('content')
<div class="sl-page">
    <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Store Management</span><h2>Your Shop</h2><p>Keep your storefront accurate, trustworthy, and easy to contact.</p></div><button type="button" class="sl-btn sl-btn-ghost" data-demo-action="Store preview opened.">Preview Store</button></div>
    <nav class="sl-tabs"><a href="{{ route('seller.store', ['tab' => 'profile']) }}" class="{{ $currentTab === 'profile' ? 'is-active' : '' }}">Shop Profile</a><a href="{{ route('seller.store', ['tab' => 'settings']) }}" class="{{ $currentTab === 'settings' ? 'is-active' : '' }}">Store Settings</a></nav>
    @if ($currentTab === 'settings')
        <form class="sl-settings-layout" data-demo-form data-success="Store settings saved.">
            <div class="sl-settings-nav sl-card"><strong>Settings</strong><a href="#operations" class="is-active">Operations</a><a href="#orders">Order Preferences</a><a href="#service">Customer Service</a><a href="#visibility">Store Visibility</a></div>
            <div class="sl-settings-main">
                <section class="sl-card sl-form-section" id="operations"><header class="sl-card-head"><div><h2>Store Operations</h2><p>Set regular hours and handling capacity.</p></div></header><div class="sl-form-grid"><label class="sl-field"><span>Opening time</span><input type="time" value="09:00"></label><label class="sl-field"><span>Closing time</span><input type="time" value="18:00"></label><label class="sl-field"><span>Operating days</span><select><option>Monday to Saturday</option><option>Monday to Friday</option><option>Every day</option></select></label><label class="sl-field"><span>Daily order capacity</span><input type="number" min="1" value="80"></label></div></section>
                <section class="sl-card sl-form-section" id="orders"><header class="sl-card-head"><div><h2>Order Preferences</h2><p>Configure default preparation and cancellation rules.</p></div></header><div class="sl-setting-switch"><div><strong>Automatic order acceptance</strong><p>Accept paid orders automatically when stock is available.</p></div><label class="sl-switch"><input type="checkbox"><span></span></label></div><div class="sl-setting-switch"><div><strong>Low stock protection</strong><p>Pause a listing automatically when its stock reaches zero.</p></div><label class="sl-switch"><input type="checkbox" checked><span></span></label></div><label class="sl-field"><span>Default preparation time</span><select><option>1 business day</option><option>2 business days</option><option>3 business days</option></select></label></section>
                <section class="sl-card sl-form-section" id="service"><header class="sl-card-head"><div><h2>Customer Service</h2><p>Set expectations for buyer communication.</p></div></header><div class="sl-setting-switch"><div><strong>Show response time</strong><p>Display your average chat response time on the shop.</p></div><label class="sl-switch"><input type="checkbox" checked><span></span></label></div><label class="sl-field"><span>Automatic welcome message</span><textarea rows="4">Hello! Thank you for messaging LIKHAE Studio. How can we help with your order today?</textarea></label></section>
                <section class="sl-card sl-form-section" id="visibility"><header class="sl-card-head"><div><h2>Store Visibility</h2><p>Control whether buyers can discover and order from your shop.</p></div></header><div class="sl-setting-switch"><div><strong>Store is visible</strong><p>Buyers can visit the shop and purchase active listings.</p></div><label class="sl-switch"><input type="checkbox" checked><span></span></label></div><div class="sl-setting-switch"><div><strong>Vacation mode</strong><p>Temporarily stop new orders while keeping the store page visible.</p></div><label class="sl-switch"><input type="checkbox"><span></span></label></div></section>
                <div class="sl-sticky-actions"><button type="button" class="sl-btn sl-btn-ghost">Discard</button><button type="submit" class="sl-btn sl-btn-primary">Save Settings</button></div>
            </div>
        </form>
    @else
        <form class="sl-store-layout" data-demo-form data-success="Shop profile updated.">
            <aside class="sl-card sl-store-preview">
                <div class="sl-shop-cover"><span>Shop cover</span><button type="button" data-demo-action="Cover image picker opened.">Change</button></div>
                <div class="sl-shop-logo">LS<button type="button" data-demo-action="Shop logo picker opened." aria-label="Change shop logo">✎</button></div>
                <h2>LIKHAE Studio</h2><p>Verified technology and workspace essentials from Laguna.</p>
                <div class="sl-store-rating"><strong>4.8</strong><span>★★★★★</span><small>1,248 reviews</small></div>
                <dl><div><dt>Products</dt><dd>126</dd></div><div><dt>Followers</dt><dd>8.4k</dd></div><div><dt>Response</dt><dd>98%</dd></div></dl>
                <span class="sl-verified-store">✓ Verified Seller</span>
            </aside>
            <div class="sl-store-form">
                <section class="sl-card sl-form-section"><header class="sl-card-head"><div><h2>Shop Information</h2><p>This information is visible to buyers.</p></div></header><div class="sl-form-grid"><label class="sl-field sl-span-2"><span>Shop name <b>*</b></span><input value="LIKHAE Studio" required><small>Shop names can be changed once every 30 days.</small></label><label class="sl-field sl-span-2"><span>Description</span><textarea rows="5">Verified technology and workspace essentials from Laguna. We provide carefully selected products, secure packaging, and responsive after-sales support.</textarea></label><label class="sl-field"><span>Store category</span><select><option>Electronics & Accessories</option><option>Home & Living</option><option>Fashion</option></select></label><label class="sl-field"><span>Public contact</span><input value="support@likhaestudio.ph" type="email"></label></div></section>
                <section class="sl-card sl-form-section"><header class="sl-card-head"><div><h2>Location & Business Hours</h2><p>Help buyers understand where and when you operate.</p></div></header><div class="sl-form-grid"><label class="sl-field sl-span-2"><span>Store location</span><input value="24 Rizal Street, Santa Cruz, Laguna 4009"></label><label class="sl-field"><span>Business days</span><select><option>Monday to Saturday</option><option>Monday to Friday</option></select></label><label class="sl-field"><span>Business hours</span><input value="9:00 AM – 6:00 PM"></label></div></section>
                <div class="sl-sticky-actions"><button type="button" class="sl-btn sl-btn-ghost">Cancel</button><button type="submit" class="sl-btn sl-btn-primary">Save Shop Profile</button></div>
            </div>
        </form>
    @endif
</div>
@endsection
