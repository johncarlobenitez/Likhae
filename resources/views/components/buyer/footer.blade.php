@props(['guest' => false, 'landing' => false])

@php
    $guestBrowseUrl = \Illuminate\Support\Facades\Route::has('guest.home') ? route('guest.home') : route('products');
@endphp

@if($guest)
    <footer class="lk-footer is-guest {{ $landing ? 'is-landing' : '' }}">
        <div class="lk-guest-footer-main">
            <div class="lk-guest-footer-brand">
                <x-likhae-logo context="Marketplace" class="likhae-logo--guest-footer" />
                <p>{{ $landing ? 'Real people. Real products. A brighter everyday.' : 'Crafted for Everyday Needs' }}</p>
            </div>

            <nav class="lk-guest-footer-links" aria-label="Quick links">
                <strong>Quick Links</strong>
                @if($landing)
                    <a href="#home">Home</a>
                    <a href="#about">About</a>
                    <a href="#process">Process</a>
                @else
                    <a href="{{ $guestBrowseUrl }}">Home</a>
                    <a href="{{ $guestBrowseUrl }}#categories-heading">Categories</a>
                    <a href="{{ route('products') }}">Products</a>
                @endif
            </nav>

            <nav class="lk-guest-footer-links" aria-label="Customer service">
                <strong>Customer Service</strong>
                <a href="{{ Route::has('login') ? route('login') : url('/login') }}">Help Center</a>
                <a href="{{ route('products') }}">Track Your Order</a>
                <a href="{{ Route::has('register') ? route('register') : url('/register') }}">Returns & Refunds</a>
                <a href="{{ Route::has('login') ? route('login') : url('/login') }}">Contact Us</a>
            </nav>

            <div class="lk-guest-footer-social">
                <strong>Follow Us</strong>
                <div>
                    <a href="{{ route('home') }}" aria-label="Facebook">f</a>
                    <a href="{{ route('home') }}" aria-label="Instagram">ig</a>
                    <a href="{{ route('home') }}" aria-label="TikTok">tk</a>
                    <a href="{{ route('home') }}" aria-label="YouTube">yt</a>
                </div>
                <p>More Good Days Together</p>
            </div>
        </div>

        <div class="lk-guest-footer-bottom">
            <span>&copy; {{ date('Y') }} LIKHAE. All rights reserved.</span>
            <span>A more connected everyday. A brighter tomorrow.</span>
        </div>
    </footer>
@else
    <footer class="lk-footer">
        <span>&copy; {{ date('Y') }} LIKHAE Marketplace. Buyer-first shopping experience.</span>
        <nav aria-label="Footer navigation">
            <a href="{{ route('buyer.orders') }}">Track Orders</a>
            <a href="{{ route('buyer.messages') }}">Support</a>
            <a href="{{ route('buyer.account') }}">Account</a>
        </nav>
    </footer>
@endif
