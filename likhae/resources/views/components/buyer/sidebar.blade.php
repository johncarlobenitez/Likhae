@props(['active' => 'home'])
@php
    $user     = auth()->user();
    $name     = $user?->name ?? 'Buyer';
    $email    = $user?->email ?? '';
    $initials = collect(explode(' ', trim($name)))
                  ->filter()->map(fn($p) => strtoupper(substr($p,0,1)))->take(2)->implode('') ?: 'B';

    $icon = fn(string $k): string => match($k) {
        'home'     => '<svg viewBox="0 0 24 24"><path d="M3 10.8 12 3l9 7.8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/></svg>',
        'products' => '<svg viewBox="0 0 24 24"><path d="M4 4h7v7H4z"/><path d="M13 4h7v7h-7z"/><path d="M4 13h7v7H4z"/><path d="M13 13h7v7h-7z"/></svg>',
        'cart'     => '<svg viewBox="0 0 24 24"><path d="M6 6h15l-2 8H8z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>',
        'orders'   => '<svg viewBox="0 0 24 24"><path d="M6 2h12l2 4v16H4V6z"/><path d="M6 6h12"/><path d="M8 11h8"/><path d="M8 15h6"/></svg>',
        'messages' => '<svg viewBox="0 0 24 24"><path d="M4 5h16v11H8l-4 4z"/><path d="M8 9h8"/><path d="M8 13h5"/></svg>',
        'rewards'  => '<svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
        'account'  => '<svg viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>',
        'logout'   => '<svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
        default    => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/></svg>',
    };
@endphp

<aside class="lk-sidebar" aria-label="Buyer navigation">

    {{-- Brand / collapse toggle --}}
    <div class="lk-sidebar-head">
        <button type="button" class="lk-side-toggle" data-lk-sidebar-toggle aria-label="Collapse sidebar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <a href="{{ route('buyer.home') }}" class="lk-brand" aria-label="LIKHAE home">
            <span class="lk-brand-mark">L</span>
            <span class="lk-brand-text">
                <strong>LIKHAE</strong>
                <small>Buyer Center</small>
            </span>
        </a>
    </div>

    {{-- Nav scroll area --}}
    <nav class="lk-sidebar-scroll" aria-label="Main navigation">

        {{-- SHOP --}}
        <div class="lk-nav-group">
            <a class="lk-nav-link {{ $active==='home'     ? 'is-active':'' }}" href="{{ route('buyer.home') }}"     data-title="Home">
                <span class="lk-nav-icon">{!! $icon('home') !!}</span>
                <span class="lk-nav-text">Home</span>
            </a>
            <a class="lk-nav-link {{ $active==='products' ? 'is-active':'' }}" href="{{ route('buyer.products') }}" data-title="Categories">
                <span class="lk-nav-icon">{!! $icon('products') !!}</span>
                <span class="lk-nav-text">Categories</span>
            </a>
            <a class="lk-nav-link {{ $active==='cart'     ? 'is-active':'' }}" href="{{ route('buyer.cart') }}"     data-title="Cart">
                <span class="lk-nav-icon">{!! $icon('cart') !!}</span>
                <span class="lk-nav-text">Cart</span>
                <span class="lk-nav-count" data-cart-badge>0</span>
            </a>
        </div>

        {{-- MESSAGES --}}
        <div class="lk-nav-group">
            <a class="lk-nav-link {{ $active==='messages' ? 'is-active':'' }}" href="{{ route('buyer.messages') }}" data-title="Messages">
                <span class="lk-nav-icon">{!! $icon('messages') !!}</span>
                <span class="lk-nav-text">Messages</span>
                <span class="lk-nav-count">2</span>
            </a>
        </div>

        {{-- MY ORDERS --}}
        <div class="lk-nav-group">
            <button class="lk-nav-toggle"
                    data-nav-toggle aria-label="My Orders" aria-expanded="false">
                <span class="lk-nav-icon">{!! $icon('orders') !!}</span>
                <span class="lk-nav-text">My Orders</span>
                <span class="lk-nav-arrow">▼</span>
            </button>
            <div class="lk-nav-submenu" data-nav-submenu>
                <a class="{{ $active==='orders' ? 'is-active':'' }}" href="{{ route('buyer.orders') }}">All Orders</a>
                <a href="{{ route('buyer.orders',['status'=>'to-pay']) }}">To Pay</a>
                <a href="{{ route('buyer.orders',['status'=>'to-ship']) }}">To Ship</a>
                <a href="{{ route('buyer.orders',['status'=>'to-receive']) }}">To Receive</a>
                <a href="{{ route('buyer.orders',['status'=>'completed']) }}">Completed</a>
                <a href="{{ route('buyer.orders',['status'=>'cancelled']) }}">Cancelled</a>
                <a href="{{ route('buyer.orders',['status'=>'returns']) }}">Returns / Refunds</a>
            </div>
        </div>

        {{-- REWARDS --}}
        <div class="lk-nav-group">
            <button class="lk-nav-toggle" data-nav-toggle aria-label="Rewards" aria-expanded="false">
                <span class="lk-nav-icon">{!! $icon('rewards') !!}</span>
                <span class="lk-nav-text">Rewards &amp; Vouchers</span>
                <span class="lk-nav-arrow">▼</span>
            </button>
            <div class="lk-nav-submenu" data-nav-submenu>
                <a href="#">My Vouchers</a>
                <a href="#">Reward Points</a>
                <a href="#">Cashback</a>
            </div>
        </div>

        {{-- ACCOUNT MANAGEMENT --}}
        @php $tab = request('tab', 'profile'); @endphp
        <div class="lk-nav-group">
            <button class="lk-nav-toggle"
                    data-nav-toggle aria-label="Account" aria-expanded="false">
                <span class="lk-nav-icon">{!! $icon('account') !!}</span>
                <span class="lk-nav-text">Account Management</span>
                <span class="lk-nav-arrow">▼</span>
            </button>
            <div class="lk-nav-submenu" data-nav-submenu>
                <a class="{{ $active==='account' && $tab==='profile'       ? 'is-active':'' }}" href="{{ route('buyer.account',['tab'=>'profile']) }}">Profile</a>
                <a class="{{ $active==='account' && $tab==='addresses'     ? 'is-active':'' }}" href="{{ route('buyer.account',['tab'=>'addresses']) }}">Addresses</a>
                <a class="{{ $active==='account' && $tab==='password'      ? 'is-active':'' }}" href="{{ route('buyer.account',['tab'=>'password']) }}">Change Password</a>
                <a class="{{ $active==='account' && $tab==='privacy'       ? 'is-active':'' }}" href="{{ route('buyer.account',['tab'=>'privacy']) }}">Privacy Settings</a>
                <a class="{{ $active==='account' && $tab==='deletion'      ? 'is-active':'' }}" href="{{ route('buyer.account',['tab'=>'deletion']) }}">Account Deletion</a>
                <a class="{{ $active==='account' && $tab==='notifications' ? 'is-active':'' }}" href="{{ route('buyer.account',['tab'=>'notifications']) }}">Notification Settings</a>
            </div>
        </div>

    </nav>

    {{-- Logout --}}
    <div class="lk-sidebar-foot">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="lk-nav-link lk-logout" data-title="Logout" style="width:100%">
                <span class="lk-nav-icon">{!! $icon('logout') !!}</span>
                <span class="lk-nav-text">Logout</span>
            </button>
        </form>
    </div>

</aside>
