@props(['active' => 'dashboard'])

@php
    $admin = auth()->user();
    $adminName = data_get($admin, 'name', 'Admin User');
    $initials = collect(explode(' ', $adminName))->filter()->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') ?: 'A';

    $icons = [
        'dashboard' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
        'registrations' => '<path d="M12 3 3 7l9 4 9-4-9-4Z"/><path d="m7 9.2-2 1V16l7 4 7-4v-5.8l-2-1"/><path d="M21 7v6"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'products' => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/>',
        'orders' => '<path d="M6 3h12l2 4v14H4V7z"/><path d="M4 7h16M9 11h6"/>',
        'compliance' => '<path d="m12 3 8 3v5c0 5-3.4 8.3-8 10-4.6-1.7-8-5-8-10V6l8-3Z"/><path d="m9 12 2 2 4-5"/>',
        'complaints' => '<path d="M4 5h16v13H7l-3 3V5Z"/><path d="M12 8v4m0 3h.01"/>',
        'finance' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/>',
        'reports' => '<path d="M4 20V10m6 10V4m6 16v-7m4 7H2"/>',
        'messages' => '<path d="M4 5h16v12H8l-4 3V5Z"/><path d="M8 9h8M8 13h5"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3A1.7 1.7 0 0 0 10 3V2.8h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z"/>',
        'account' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5m5 5H9"/>',
    ];

    $groups = [
        ['label' => 'Registration Management', 'key' => 'registrations', 'icon' => 'registrations', 'route' => 'admin.registrations', 'items' => [
            ['label' => 'Buyer Applications', 'params' => ['type' => 'buyers']],
            ['label' => 'Seller Applications', 'params' => ['type' => 'sellers']],
            ['label' => 'Logistics Applications', 'params' => ['type' => 'logistics']],
        ]],
        ['label' => 'User Management', 'key' => 'users', 'icon' => 'users', 'route' => 'admin.users', 'items' => [
            ['label' => 'All Users', 'params' => ['role' => 'all']],
            ['label' => 'Buyers', 'params' => ['role' => 'buyers']],
            ['label' => 'Sellers', 'params' => ['role' => 'sellers']],
            ['label' => 'Logistics Centers', 'params' => ['role' => 'logistics']],
            ['label' => 'Riders / Couriers', 'params' => ['role' => 'riders']],
        ]],
        ['label' => 'Marketplace Management', 'key' => 'products', 'icon' => 'products', 'route' => 'admin.products', 'items' => [
            ['label' => 'Products', 'params' => ['view' => 'products']],
            ['label' => 'Categories', 'params' => ['view' => 'categories']],
            ['label' => 'Prohibited Item Monitor', 'params' => ['view' => 'monitor']],
        ]],
        ['label' => 'Compliance & Disputes', 'key' => 'compliance', 'icon' => 'compliance', 'route' => 'admin.compliance', 'aliases' => ['complaints'], 'items' => [
            ['label' => 'Seller Compliance', 'route' => 'admin.compliance', 'params' => ['tab' => 'sellers']],
            ['label' => 'Product Violations', 'route' => 'admin.compliance', 'params' => ['tab' => 'violations']],
            ['label' => 'Complaints / Disputes', 'route' => 'admin.complaints', 'params' => ['tab' => 'complaints']],
            ['label' => 'Returns / Refunds', 'route' => 'admin.complaints', 'params' => ['tab' => 'returns']],
        ]],
        ['label' => 'Finance', 'key' => 'finance', 'icon' => 'finance', 'route' => 'admin.finance', 'aliases' => ['reports'], 'items' => [
            ['label' => 'Commission Management', 'route' => 'admin.finance', 'params' => ['tab' => 'commission']],
            ['label' => 'Transactions', 'route' => 'admin.finance', 'params' => ['tab' => 'transactions']],
            ['label' => 'Payments', 'route' => 'admin.finance', 'params' => ['tab' => 'payments']],
            ['label' => 'Financial Reports', 'route' => 'admin.reports'],
        ]],
        ['label' => 'Communication', 'key' => 'messages', 'icon' => 'messages', 'route' => 'admin.messages', 'items' => [
            ['label' => 'Messages', 'params' => ['tab' => 'messages']],
            ['label' => 'Announcements', 'params' => ['tab' => 'announcements']],
        ]],
        ['label' => 'System Management', 'key' => 'settings', 'icon' => 'settings', 'route' => 'admin.settings', 'items' => [
            ['label' => 'Platform Policies', 'params' => ['tab' => 'policies']],
            ['label' => 'Platform Settings', 'params' => ['tab' => 'platform']],
            ['label' => 'Audit Logs', 'params' => ['tab' => 'audit']],
        ]],
        ['label' => 'Account', 'key' => 'account', 'icon' => 'account', 'route' => 'admin.account', 'aliases' => ['notifications'], 'items' => [
            ['label' => 'Profile', 'params' => ['tab' => 'profile']],
            ['label' => 'Security', 'params' => ['tab' => 'security']],
            ['label' => 'Notification Settings', 'params' => ['tab' => 'notifications']],
        ]],
    ];
@endphp

<aside class="ad-sidebar" id="admin-sidebar" data-admin-sidebar aria-label="Admin navigation">
    <div class="ad-sidebar-head">
        <button type="button" class="ad-side-toggle" data-admin-sidebar-toggle aria-label="Collapse navigation" aria-expanded="true">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="{{ route('admin.dashboard') }}" class="ad-brand" data-title="LIKHAE Admin Center">
            <span class="ad-brand-mark">L</span>
            <span class="ad-brand-copy"><strong>LIKHAE</strong><small>Admin Center</small></span>
        </a>
    </div>

    <div class="ad-sidebar-profile">
        <span class="ad-avatar">{{ $initials }}</span>
        <span class="ad-profile-copy"><strong>{{ $adminName }}</strong><small>Platform Administrator</small></span>
        <span class="ad-verified" title="Verified administrator">✓</span>
    </div>

    <nav class="ad-sidebar-scroll">
        <a class="ad-nav-link {{ $active === 'dashboard' ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}" data-title="Dashboard">
            <span class="ad-nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons['dashboard'] !!}</svg></span>
            <span class="ad-nav-text">Dashboard</span>
        </a>

        @foreach($groups as $group)
            @php
                $groupActive = $active === $group['key'] || in_array($active, $group['aliases'] ?? [], true);
                $currentQuery = request()->query();
            @endphp
            <div class="ad-nav-group {{ $groupActive ? 'is-current' : '' }}">
                <span class="ad-nav-heading">{{ $group['label'] }}</span>
                <button class="ad-nav-toggle {{ $groupActive ? 'is-active is-open' : '' }}" type="button" data-admin-nav-toggle data-title="{{ $group['label'] }}" aria-expanded="{{ $groupActive ? 'true' : 'false' }}">
                    <span class="ad-nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons[$group['icon']] !!}</svg></span>
                    <span class="ad-nav-text">{{ $group['label'] }}</span>
                    <svg class="ad-nav-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 10 3 3 3-3"/></svg>
                </button>
                <div class="ad-nav-submenu" data-admin-submenu @if(!$groupActive) hidden @endif>
                    @foreach($group['items'] as $item)
                        @php
                            $itemRoute = $item['route'] ?? $group['route'];
                            $params = $item['params'] ?? [];
                            $queryMatches = $groupActive && collect($params)->every(fn($value, $key) => request($key) === $value);
                        @endphp
                        <a href="{{ route($itemRoute, $params) }}" class="{{ $queryMatches ? 'is-active' : '' }}" data-title="{{ $item['label'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    <div class="ad-sidebar-foot">
        {{-- DARK MODE TOGGLE --}}
        <button
            id="themeToggle"
            type="button"
            class="ad-nav-link"
            data-title="Appearance"
            style="width:100%;display:flex;align-items:center;justify-content:space-between;gap:8px;cursor:pointer;background:none;border:none;"
        >
            <span style="display:flex;align-items:center;gap:8px;">
                <span class="ad-nav-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true" style="width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.6;"><path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"/></svg>
                </span>
                <span class="ad-nav-text" style="flex-direction:column;align-items:flex-start;">
                    <span style="display:block;font-weight:600;font-size:.8125rem;">Appearance</span>
                    <span style="display:block;font-size:.7rem;opacity:.6;">Light / Dark Mode</span>
                </span>
            </span>
            <span id="themeToggleBadge" style="border-radius:9999px;padding:2px 10px;font-size:.7rem;font-weight:700;background:#c92d2f;color:#fff;">ON</span>
        </button>
        @if(Route::has('logout'))
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="ad-nav-link ad-logout" type="submit" data-title="Logout">
                    <span class="ad-nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons['logout'] !!}</svg></span>
                    <span class="ad-nav-text">Logout</span>
                </button>
            </form>
        @else
            <button class="ad-nav-link ad-logout" type="button" data-demo-action="Connect this button to your logout route" data-title="Logout">
                <span class="ad-nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons['logout'] !!}</svg></span>
                <span class="ad-nav-text">Logout</span>
            </button>
        @endif
    </div>
</aside>
