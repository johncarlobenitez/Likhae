@props(['active' => 'home', 'cartCount' => null, 'messageCount' => null])

@php
    $buyer = auth()->user();
    $demoUser = session('demo_user');
    $cartCount = $cartCount ?? session('cart_count', 2);
    $messageCount = $messageCount ?? session('message_count', 2);
    $buyerName = data_get($buyer, 'name') 
        ?? data_get($demoUser, 'name') 
        ?? (data_get($demoUser, 'role') === 'buyer' ? 'Buyer Account' : 'Buyer Account');
    $buyerInitials = collect(explode(' ', trim($buyerName)))->filter()->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') ?: 'BA';
    $avatar = data_get($buyer, 'profile_photo_url') ?? data_get($buyer, 'avatar_url');
    $orderStatus = request('status', 'all');
    $rewardTab = request('tab', 'vouchers');
    $accountTab = request('tab', 'profile');
    $icons = [
        'home' => '<path d="M3 10.8 12 3l9 7.8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/>',
        'products' => '<rect x="4" y="4" width="7" height="7" rx="1"/><rect x="13" y="4" width="7" height="7" rx="1"/><rect x="4" y="13" width="7" height="7" rx="1"/><rect x="13" y="13" width="7" height="7" rx="1"/>',
        'cart' => '<path d="M6 6h15l-2 8H8z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>',
        'messages' => '<path d="M4 5h16v11H8l-4 4z"/><path d="M8 9h8M8 13h5"/>',
        'orders' => '<path d="M6 3h12l2 4v14H4V7z"/><path d="M4 7h16M8 11h8M8 15h6"/>',
        'rewards' => '<path d="M20 12v9H4v-9M2 7h20v5H2zM12 7v14M12 7H7.5A2.5 2.5 0 1 1 10 4.5L12 7Zm0 0h4.5A2.5 2.5 0 1 0 14 4.5L12 7Z"/>',
        'account' => '<circle cx="12" cy="7" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/>',
    ];
    $topLinks = [
        ['key' => 'home', 'label' => 'Home', 'route' => 'buyer.home', 'icon' => 'home'],
        ['key' => 'products', 'label' => 'Categories', 'route' => 'buyer.products', 'icon' => 'products'],
        ['key' => 'cart', 'label' => 'Cart', 'route' => 'buyer.cart', 'icon' => 'cart', 'count' => $cartCount],
        ['key' => 'messages', 'label' => 'Messages', 'route' => 'buyer.messages', 'icon' => 'messages', 'count' => $messageCount],
    ];
    $orderLinks = ['all' => 'All', 'to-pay' => 'To Pay', 'to-ship' => 'To Ship', 'to-receive' => 'To Receive', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'returns' => 'Returns / Refunds'];
    $rewardLinks = ['vouchers' => 'My Vouchers', 'points' => 'Reward Points', 'cashback' => 'Cashback'];
    $accountLinks = ['profile' => 'Profile', 'addresses' => 'Addresses', 'password' => 'Change Password', 'privacy' => 'Privacy Settings', 'deletion' => 'Account Deletion', 'notifications' => 'Notification Settings'];
@endphp

<aside class="lk-sidebar" id="buyer-sidebar" data-lk-sidebar aria-label="Buyer navigation">
    <div class="lk-sidebar-head">
        <button type="button" class="lk-side-toggle" data-lk-sidebar-toggle aria-label="Collapse sidebar" aria-expanded="true">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="{{ route('buyer.home') }}" class="lk-brand" aria-label="LIKHAE Buyer Home">
            <span class="lk-brand-mark">L</span><span class="lk-brand-text"><strong>LIKHAE</strong><small>Buyer Center</small></span>
        </a>
    </div>

    <div class="lk-sidebar-profile">
        <span class="lk-sidebar-avatar">@if($avatar)<img src="{{ $avatar }}" alt="{{ $buyerName }}">@else{{ $buyerInitials }}@endif</span>
        <span class="lk-sidebar-profile-copy"><strong>{{ $buyerName }}</strong><span>Buyer Default</span></span>
        <span class="lk-sidebar-verified" title="Buyer account">✓</span>
    </div>

    <nav class="lk-sidebar-scroll">
        <div class="lk-nav-group">
            @foreach($topLinks as $link)
                <a href="{{ route($link['route']) }}" class="lk-nav-link {{ $active === $link['key'] ? 'is-active' : '' }}" data-title="{{ $link['label'] }}" @if($active === $link['key']) aria-current="page" @endif>
                    <span class="lk-nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons[$link['icon']] !!}</svg></span>
                    <span class="lk-nav-text">{{ $link['label'] }}</span>
                    @if(($link['count'] ?? 0) > 0)<span class="lk-nav-count">{{ $link['count'] > 99 ? '99+' : $link['count'] }}</span>@endif
                </a>
            @endforeach
        </div>

        <div class="lk-nav-group">
            <button type="button" class="lk-nav-toggle {{ $active === 'orders' ? 'is-active is-open' : '' }}" data-nav-toggle data-title="My Orders" aria-expanded="{{ $active === 'orders' ? 'true' : 'false' }}">
                <span class="lk-nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons['orders'] !!}</svg></span><span class="lk-nav-text">My Orders</span><span class="lk-nav-arrow"><svg viewBox="0 0 24 24"><path d="m7 10 5 5 5-5"/></svg></span>
            </button>
            <div class="lk-nav-submenu {{ $active === 'orders' ? 'expanded' : '' }}" data-nav-submenu @if($active !== 'orders') hidden @endif>
                @foreach($orderLinks as $key => $label)<a href="{{ route('buyer.orders', ['status' => $key]) }}" class="{{ $active === 'orders' && $orderStatus === $key ? 'is-active' : '' }}">{{ $label }}</a>@endforeach
            </div>
        </div>

        <div class="lk-nav-group">
            <button type="button" class="lk-nav-toggle {{ $active === 'rewards' ? 'is-active is-open' : '' }}" data-nav-toggle data-title="Rewards & Vouchers" aria-expanded="{{ $active === 'rewards' ? 'true' : 'false' }}">
                <span class="lk-nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons['rewards'] !!}</svg></span><span class="lk-nav-text">Rewards &amp; Vouchers</span><span class="lk-nav-arrow"><svg viewBox="0 0 24 24"><path d="m7 10 5 5 5-5"/></svg></span>
            </button>
            <div class="lk-nav-submenu {{ $active === 'rewards' ? 'expanded' : '' }}" data-nav-submenu @if($active !== 'rewards') hidden @endif>
                @foreach($rewardLinks as $key => $label)<a href="{{ route('buyer.rewards', ['tab' => $key]) }}" class="{{ $active === 'rewards' && $rewardTab === $key ? 'is-active' : '' }}">{{ $label }}</a>@endforeach
            </div>
        </div>

        <div class="lk-nav-group">
            <button type="button" class="lk-nav-toggle {{ $active === 'account' ? 'is-active is-open' : '' }}" data-nav-toggle data-title="Account Management" aria-expanded="{{ $active === 'account' ? 'true' : 'false' }}">
                <span class="lk-nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons['account'] !!}</svg></span><span class="lk-nav-text">Account Management</span><span class="lk-nav-arrow"><svg viewBox="0 0 24 24"><path d="m7 10 5 5 5-5"/></svg></span>
            </button>
            <div class="lk-nav-submenu {{ $active === 'account' ? 'expanded' : '' }}" data-nav-submenu @if($active !== 'account') hidden @endif>
                @foreach($accountLinks as $key => $label)<a href="{{ route('buyer.account', ['tab' => $key]) }}" class="{{ $active === 'account' && $accountTab === $key ? 'is-active' : '' }}">{{ $label }}</a>@endforeach
            </div>
        </div>
    </nav>

    <div class="lk-sidebar-foot">
        {{-- DARK MODE TOGGLE --}}
        <button
            id="themeToggle"
            type="button"
            class="lk-nav-link"
            data-title="Appearance"
            style="width:100%;display:flex;align-items:center;justify-content:space-between;gap:8px;cursor:pointer;background:none;border:none;"
        >
            <span style="display:flex;align-items:center;gap:8px;">
                <span class="lk-nav-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true" style="width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.6;"><path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"/></svg>
                </span>
                <span class="lk-nav-text" style="flex-direction:column;align-items:flex-start;">
                    <span style="display:block;font-weight:600;font-size:.8125rem;">Appearance</span>
                    <span style="display:block;font-size:.7rem;opacity:.6;">Light / Dark Mode</span>
                </span>
            </span>
            <span id="themeToggleBadge" style="border-radius:9999px;padding:2px 10px;font-size:.7rem;font-weight:700;background:#c92d2f;color:#fff;">ON</span>
        </button>
        @if(Route::has('logout'))
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="lk-nav-link lk-logout" data-title="Logout"><span class="lk-nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons['logout'] !!}</svg></span><span class="lk-nav-text">Logout</span></button></form>
        @else
            <button type="button" class="lk-nav-link lk-logout" data-demo-action="Connect this button to your logout route" data-title="Logout"><span class="lk-nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons['logout'] !!}</svg></span><span class="lk-nav-text">Logout</span></button>
        @endif
    </div>
</aside>

