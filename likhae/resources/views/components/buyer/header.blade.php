<header class="b-header">
<div class="b-container">
<div class="b-header-row">
<div class="b-header-left">
<button class="b-icon-button b-mobile-toggle" type="button" aria-label="Open navigation" data-buyer-mobile-toggle>☰</button>
<a class="b-brand" href="{{ route('buyer.home') }}">LIKHAE</a>
<button class="b-category-btn" type="button" data-buyer-category-toggle>Categories ▾</button>
<nav class="b-category-menu" data-buyer-category-menu>
@foreach(['Fashion','Electronics','Home & Living','Beauty & Health','Sports & Outdoors','Toys & Games','Automotive','Books & Stationery','Groceries','Pet Supplies','Shoes & Accessories','Others'] as $category)
<a href="{{ route('buyer.products',['category'=>$category]) }}">{{ $category }}</a>
@endforeach
</nav>
</div>
<form class="b-search" action="{{ route('buyer.products') }}" method="GET" role="search">
<button class="b-search-button" aria-label="Search">⌕</button>
<input class="b-input" name="q" value="{{ request('q') }}" placeholder="Search for products, brands and more..." autocomplete="off" data-buyer-search>
<div class="b-search-suggestions" data-buyer-search-suggestions></div>
</form>
<nav class="b-header-actions">
<a class="b-icon-link desktop-only" href="{{ route('buyer.wishlist') }}" aria-label="Wishlist">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="22" height="22"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
    <span class="b-count-badge" data-buyer-wishlist-count>0</span>
</a>
<a class="b-icon-link" href="{{ route('buyer.cart') }}" aria-label="Cart">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="22" height="22"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
    <span class="b-count-badge" data-buyer-cart-count>0</span>
</a>
<a class="b-icon-link desktop-only" href="{{ route('buyer.messages') }}" aria-label="Messages">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="22" height="22"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
</a>
<a class="b-icon-link desktop-only" href="{{ route('buyer.notifications') }}" aria-label="Notifications">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="22" height="22"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
</a>
<a class="b-icon-link" href="{{ route('buyer.account') }}" aria-label="Account">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="22" height="22"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
</a>
</nav>
</div>
<nav class="b-mobile-nav" data-buyer-mobile-nav>
<a href="{{ route('buyer.home') }}">Home</a>
<a href="{{ route('buyer.products') }}">Products</a>
<a href="{{ route('buyer.cart') }}">Cart</a>
<a href="{{ route('buyer.orders') }}">Orders</a>
<a href="{{ route('buyer.messages') }}">Chat</a>
<a href="{{ route('buyer.notifications') }}">Notifications</a>
<a href="{{ route('buyer.wishlist') }}">Wishlist</a>
<a href="{{ route('buyer.account') }}">Account</a>
<form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Logout</button></form>
</nav>
</div>
</header>
