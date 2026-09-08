@props(['title' => 'Buyer', 'guest' => false])

@php
    $user = auth()->user();
    $demoUser = session('demo_user');
    $buyerName = data_get($user, 'name') 
        ?? data_get($demoUser, 'name') 
        ?? (data_get($demoUser, 'role') === 'buyer' || !$guest ? 'Buyer Account' : 'Guest Shopper');
    $initials = collect(explode(' ', trim($buyerName)))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') ?: 'BA';
    $searchRoute = $guest ? route('home') : route('buyer.products');
    $cartCount = session('cart_count', 2);
    $notificationCount = session('notification_count', 3);
@endphp

<header class="lk-header {{ $guest ? 'is-guest' : '' }}">
    @unless($guest)
        <button type="button" class="lk-mobile-menu" data-lk-mobile-menu aria-label="Open sidebar" aria-controls="buyer-sidebar" aria-expanded="false">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    @endunless
    @if($guest)
        <a href="{{ route('home') }}" class="lk-guest-brand" aria-label="LIKHAE Marketplace home">
            <x-likhae-logo context="Marketplace" class="likhae-logo--guest" />
        </a>
    @else
        <div class="lk-header-title"><strong>{{ preg_replace('/\s+—\s+LIKHAE$/', '', $title) }}</strong><span>Shop smart with LIKHAE</span></div>
    @endif
    <form action="{{ $searchRoute }}" method="GET" class="lk-search" role="search">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products, categories, local sellers..." aria-label="Search marketplace">
        <button type="submit" aria-label="Search"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
    </form>
    @if($guest)
        <nav class="lk-guest-actions" aria-label="Guest actions">
            <a class="lk-btn lk-btn-light" href="{{ Route::has('login') ? route('login') : url('/login') }}">Sign In</a>
            <a class="lk-btn lk-btn-red" href="{{ Route::has('register') ? route('register') : url('/register') }}">Create Account</a>
        </nav>
    @else
        <div class="lk-header-actions">
            <a href="{{ route('buyer.notifications') }}" class="lk-icon-btn" title="Notifications" aria-label="Notifications"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>@if($notificationCount)<span class="lk-badge">{{ $notificationCount }}</span>@endif</a>
            <a href="{{ route('buyer.cart') }}" class="lk-icon-btn" title="Cart" aria-label="Cart"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6h15l-2 8H8z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>@if($cartCount)<span class="lk-badge">{{ $cartCount }}</span>@endif</a>
        </div>
        <a class="lk-profile-container" href="{{ route('buyer.account') }}">
            <span class="lk-profile-avatar">{{ $initials }}</span><span class="lk-profile-info"><strong>{{ $buyerName }}</strong><small>Buyer Default</small></span>
        </a>
    @endif
</header>
