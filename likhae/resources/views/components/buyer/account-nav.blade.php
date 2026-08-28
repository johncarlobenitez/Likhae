<nav class="b-card b-account-nav">
<a href="{{ route('buyer.account') }}" class="{{ request()->routeIs('buyer.account')?'is-active':'' }}">Dashboard</a>
<a href="{{ route('buyer.orders') }}" class="{{ request()->routeIs('buyer.orders*')?'is-active':'' }}">My Orders</a>
<a href="{{ route('buyer.messages') }}">Messages</a><a href="{{ route('buyer.wishlist') }}">Wishlist</a><a href="{{ route('buyer.notifications') }}">Notifications</a>
<a href="{{ route('buyer.account.reviews') }}" class="{{ request()->routeIs('buyer.account.reviews')?'is-active':'' }}">My Reviews</a>
<a href="{{ route('buyer.account.profile') }}" class="{{ request()->routeIs('buyer.account.profile')?'is-active':'' }}">Profile</a>
<a href="{{ route('buyer.account.addresses') }}" class="{{ request()->routeIs('buyer.account.addresses')?'is-active':'' }}">Addresses</a>
<a href="{{ route('buyer.account.security') }}" class="{{ request()->routeIs('buyer.account.security')?'is-active':'' }}">Security</a>
<form action="{{ route('logout') }}" method="POST" style="margin-top:8px">@csrf<button type="submit" style="color:var(--l)">Logout</button></form>
</nav>
