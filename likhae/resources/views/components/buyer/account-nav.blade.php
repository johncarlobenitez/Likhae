@props(['current' => 'profile'])
<nav class="lk-account-nav" aria-label="Account navigation">
    <a href="{{ route('buyer.account.profile') }}" class="{{ $current === 'profile' ? 'is-active' : '' }}">Profile Details</a>
    <a href="{{ route('buyer.account.addresses') }}" class="{{ $current === 'addresses' ? 'is-active' : '' }}">Saved Addresses</a>
    <a href="{{ route('buyer.orders') }}" class="{{ $current === 'orders' ? 'is-active' : '' }}">Orders & Tracking</a>
    <a href="{{ route('buyer.wishlist') }}" class="{{ $current === 'wishlist' ? 'is-active' : '' }}">Saved Wishlist</a>
    <a href="{{ route('buyer.messages') }}" class="{{ $current === 'messages' ? 'is-active' : '' }}">Messages</a>
    <a href="{{ route('buyer.account.reviews') }}" class="{{ $current === 'reviews' ? 'is-active' : '' }}">My Reviews</a>
    <a href="{{ route('buyer.account.security') }}" class="{{ $current === 'security' ? 'is-active' : '' }}">Security & Logins</a>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:12px">
        @csrf
        <button type="submit" class="lk-text-link" style="color:var(--coral);padding:10px 0;font-weight:700">Sign Out</button>
    </form>
</nav>

