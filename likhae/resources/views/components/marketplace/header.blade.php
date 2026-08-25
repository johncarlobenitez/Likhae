@props(['buyer' => false])
<header class="lk-header" data-header>
    <div class="lk-header__inner">
        <a class="lk-logo" href="{{ $buyer ? route('buyer.home') : url('/') }}" aria-label="LIKHAE home">LIKHAE</a>
        <nav class="lk-nav" aria-label="Primary navigation">
            <a href="{{ $buyer ? route('buyer.products') : url('/products') }}">Discover</a>
            <a href="{{ $buyer ? route('buyer.local-finds') : url('/products?filter=local') }}">Local</a>
            <a href="{{ $buyer ? route('buyer.products') . '?sort=newest' : url('/products?sort=newest') }}">New</a>
            <a href="{{ $buyer ? route('buyer.home') . '#stories' : url('/#stories') }}">Stories</a>
        </nav>
        <form class="lk-search" action="{{ $buyer ? route('buyer.products') : url('/products') }}" method="get" role="search" data-search-form>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21 21-4.4-4.4m2.4-5.1a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
            <input name="q" autocomplete="off" placeholder="Search makers, products, places..." aria-label="Search LIKHAE" data-search-input>
            <div class="lk-search__suggestions" data-search-suggestions hidden></div>
        </form>
        <div class="lk-actions">
            @if($buyer)
                <a class="lk-icon-link" href="{{ route('buyer.wishlist') }}" aria-label="Saved pieces">♡</a>
                <a class="lk-icon-link lk-bag-link" href="{{ route('buyer.cart') }}" aria-label="Shopping bag">Bag <span class="lk-count" data-cart-count>0</span></a>
                <a class="lk-icon-link" href="{{ route('buyer.messages') }}" aria-label="Messages">Messages</a>
                <a class="lk-icon-link lk-notif-link" href="{{ route('buyer.notifications') }}" aria-label="Notifications">
                    <span class="lk-notif-dot"></span>●
                </a>
                <a class="lk-account-link" href="{{ route('buyer.account') }}">Account</a>
            @else
                <span class="lk-guest-chip">Guest</span>
                <a class="lk-account-link" href="{{ route('login') }}">Sign In</a>
            @endif
        </div>
        <button class="lk-menu-button" type="button" aria-label="Open menu" data-mobile-menu-button>☰</button>
    </div>
    <div class="lk-mobile-menu" data-mobile-menu hidden>
        <a href="{{ $buyer ? route('buyer.products') : url('/products') }}">Discover</a>
        <a href="{{ $buyer ? route('buyer.local-finds') : url('/products?filter=local') }}">Local finds</a>
        <a href="{{ $buyer ? route('buyer.products') . '?sort=newest' : url('/products?sort=newest') }}">New & Noteworthy</a>
        <a href="{{ $buyer ? route('buyer.flash-deals') : url('/products?filter=limited') }}">Limited Drops</a>
        @if($buyer)
            <a href="{{ route('buyer.wishlist') }}">Wishlist</a>
            <a href="{{ route('buyer.cart') }}">Shopping Bag (<span data-cart-count>0</span>)</a>
            <a href="{{ route('buyer.messages') }}">Messages</a>
            <a href="{{ route('buyer.notifications') }}">Notifications</a>
            <a href="{{ route('buyer.account') }}">Account Profile</a>
            <form action="{{ route('logout') }}" method="POST" style="margin-top:8px">
                @csrf
                <button type="submit" class="lk-text-link" style="color:var(--coral)">Sign Out</button>
            </form>
        @else
            <a href="{{ route('login') }}">Sign In</a>
            <a href="{{ route('register') }}">Create Account</a>
        @endif
    </div>
</header>

