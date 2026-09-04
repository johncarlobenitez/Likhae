@props(['guest' => false])

<footer class="lk-footer {{ $guest ? 'is-guest' : '' }}">
    <span>© {{ date('Y') }} LIKHAE Marketplace. Buyer-first shopping experience.</span>
    <nav aria-label="Footer navigation">
        @if($guest)
            <a href="{{ route('home') }}">Marketplace</a>
            <a href="{{ Route::has('login') ? route('login') : url('/login') }}">Sign In</a>
            <a href="{{ Route::has('register') ? route('register') : url('/register') }}">Create Account</a>
        @else
            <a href="{{ route('buyer.orders') }}">Track Orders</a><a href="{{ route('buyer.messages') }}">Support</a><a href="{{ route('buyer.account') }}">Account</a>
        @endif
    </nav>
</footer>
