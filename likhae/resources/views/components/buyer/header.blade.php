@props(['title' => 'Buyer'])
@php
    $user     = auth()->user();
    $name     = $user?->name ?? 'Buyer';
    $initials = collect(explode(' ', trim($name)))
                  ->filter()->map(fn($p) => strtoupper(substr($p,0,1)))->take(2)->implode('') ?: 'B';
    $photo    = $user?->profile_photo ?? $user?->profile_picture ?? null;
    $photoUrl = $photo ? asset('storage/'.ltrim($photo,'/')) : null;
@endphp

<header class="lk-header">

    {{-- Mobile hamburger --}}
    <button type="button" class="lk-mobile-menu" data-lk-mobile-menu aria-label="Open navigation">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <path d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Search --}}
    <form action="{{ route('buyer.products') }}" method="GET" class="lk-search" role="search">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="11" cy="11" r="7"/>
            <path d="m21 21-4.3-4.3"/>
        </svg>
        <input type="search" name="q" value="{{ request('q') }}"
               placeholder="Search products, categories, sellers…" aria-label="Search">
        <button type="submit" aria-label="Search">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <path d="m9 18 6-6-6-6"/>
            </svg>
        </button>
    </form>

    {{-- Actions --}}
    <div class="lk-header-actions">

        {{-- Notifications --}}
        <a href="{{ route('buyer.notifications') }}" class="lk-icon-btn" title="Notifications" aria-label="Notifications">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                <path d="M10 21h4"/>
            </svg>
            <span class="lk-badge">3</span>
        </a>

        {{-- Cart --}}
        <a href="{{ route('buyer.cart') }}" class="lk-icon-btn" title="Cart" aria-label="Cart">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M6 6h15l-2 8H8z"/>
                <path d="M6 6 5 3H2"/>
                <circle cx="9" cy="20" r="1"/>
                <circle cx="18" cy="20" r="1"/>
            </svg>
            <span class="lk-badge" data-cart-badge>0</span>
        </a>

        {{-- Profile --}}
        <a href="{{ route('buyer.account') }}" class="lk-profile-container" aria-label="Account">
            <div class="lk-profile-avatar">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $name }}">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div class="lk-profile-info">
                <strong>{{ $name }}</strong>
                <span>Buyer</span>
            </div>
        </a>

    </div>
</header>
