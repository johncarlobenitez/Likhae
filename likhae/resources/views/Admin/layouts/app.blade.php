<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LIKHAE Admin')</title>
    @vite(['resources/css/admin/app.css', 'resources/js/seller/app.js'])
</head>
<body>
<div class="admin-shell">

    @php
        $navGroups = [
            ['label' => null, 'items' => [
                ['label' => 'Dashboard',        'url' => '/admin/dashboard',         'icon' => 'home'],
            ]],
            ['label' => 'Users', 'items' => [
                ['label' => 'User Management',  'url' => '/admin/users',             'icon' => 'users'],
                ['label' => 'Seller Approvals', 'url' => '/admin/sellers/approvals', 'icon' => 'shield'],
                ['label' => 'Rider Management', 'url' => '/admin/riders',            'icon' => 'truck'],
            ]],
            ['label' => 'Catalog', 'items' => [
                ['label' => 'Products',         'url' => '/admin/products',          'icon' => 'products'],
                ['label' => 'Categories',       'url' => '/admin/categories',        'icon' => 'tag'],
            ]],
            ['label' => 'Operations', 'items' => [
                ['label' => 'Orders',           'url' => '/admin/orders',            'icon' => 'orders'],
                ['label' => 'Delivery',         'url' => '/admin/delivery',          'icon' => 'delivery'],
                ['label' => 'Payments',         'url' => '/admin/payments',          'icon' => 'wallet'],
                ['label' => 'Refunds',          'url' => '/admin/refunds',           'icon' => 'return'],
            ]],
            ['label' => 'Insights', 'items' => [
                ['label' => 'Reports',          'url' => '/admin/reports',           'icon' => 'chart'],
            ]],
            ['label' => 'System', 'items' => [
                ['label' => 'Settings',         'url' => '/admin/settings',          'icon' => 'settings'],
            ]],
        ];

        $icons = [
            'home'     => '<path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/>',
            'users'    => '<circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.87"/>',
            'shield'   => '<path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"/><path d="m9 12 2 2 4-5"/>',
            'truck'    => '<path d="M3 6h11v10H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
            'products' => '<path d="M4 5h16v14H4z"/><path d="M8 9h8M8 13h5"/>',
            'tag'      => '<path d="m3 12 9 9 9-9-9-9H3v9Z"/><circle cx="8" cy="8" r="1"/>',
            'orders'   => '<rect x="4" y="4" width="16" height="16"/><path d="M8 8h8M8 12h8M8 16h5"/>',
            'delivery' => '<path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect x="9" y="11" width="14" height="10"/><circle cx="12" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>',
            'wallet'   => '<path d="M4 7h16v12H4z"/><path d="M4 7V5h13v2"/><path d="M15 12h5"/>',
            'return'   => '<path d="M9 7H5v-4"/><path d="M5 7c2-3 5-4 8-3 4 1 7 5 6 9s-5 7-9 6c-3-.5-5-2-6-5"/>',
            'chart'    => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/>',
        ];

        $currentPath = request()->path();
    @endphp

    <aside id="sellerSidebar" class="admin-sidebar">
        <div class="flex h-16 items-center justify-between border-b border-[#E5E0D9] px-5">
            <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center gap-2">
                <span class="grid h-7 w-7 place-items-center rounded-md bg-[#D92D2F] text-xs font-black text-white">L</span>
                <span class="text-sm font-black tracking-tight">LIKHAE <span class="text-[#D92D2F]">Admin</span></span>
            </a>
            <button id="sidebarClose" class="icon-button lg:hidden" aria-label="Close sidebar">
                <svg viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </div>

        <nav class="seller-nav">
            @foreach($navGroups as $group)
                <div class="seller-nav-group">
                    @if($group['label'])<p class="seller-nav-label">{{ $group['label'] }}</p>@endif
                    @foreach($group['items'] as $item)
                        @php
                            $path = trim(parse_url($item['url'], PHP_URL_PATH), '/');
                            $active = $currentPath === $path || ($path !== 'admin/dashboard' && str_starts_with($currentPath, $path));
                        @endphp
                        <a href="{{ url($item['url']) }}" class="seller-nav-item {{ $active ? 'is-active' : '' }}">
                            <svg viewBox="0 0 24 24">{!! $icons[$item['icon']] !!}</svg>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>

        <div class="border-t border-[#E5E0D9] p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="seller-nav-item w-full" type="submit">
                    <svg viewBox="0 0 24 24"><path d="M10 5H4v14h6"/><path d="m14 8 4 4-4 4"/><path d="M18 12H9"/></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div id="sidebarOverlay" class="seller-sidebar-overlay"></div>

    <div class="seller-workspace">
        <header class="seller-header">
            <div class="flex min-w-0 items-center gap-3">
                <button id="sidebarOpen" class="icon-button lg:hidden" aria-label="Open navigation">
                    <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
                <div class="hidden min-w-0 sm:block">
                    <p class="truncate text-xs font-semibold text-[#6B6864]">LIKHAE Marketplace</p>
                    <p class="truncate text-[10px] text-[#96918B]">Admin Panel</p>
                </div>
            </div>

            <form action="{{ url('/admin/search') }}" method="GET" class="seller-global-search">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input name="q" type="search" placeholder="Search users, orders, products...">
            </form>

            <div class="ml-auto flex items-center gap-1">
                <a href="{{ url('/admin/settings') }}" class="icon-button" aria-label="Settings">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>
                </a>
                <div class="ml-2 flex items-center gap-2 border-l border-[#E5E0D9] pl-3">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-[#D92D2F] text-[11px] font-black text-white">A</span>
                    <span class="hidden xl:block">
                        <span class="block text-[11px] font-bold">Admin</span>
                        <span class="block text-[9px] text-[#96918B]">Administrator</span>
                    </span>
                </div>
            </div>
        </header>

        <main class="seller-content">
            @yield('content')
        </main>
    </div>
</div>

<nav class="seller-mobile-nav lg:hidden">
    @foreach([
        ['Dashboard', '/admin/dashboard',         'M3 11l9-8 9 8M5 10v10h14V10'],
        ['Users',     '/admin/users',              'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z'],
        ['Orders',    '/admin/orders',             'M4 4h16v16H4zM8 8h8M8 12h8'],
        ['Payments',  '/admin/payments',           'M4 7h16v12H4zM4 7V5h13v2'],
        ['Settings',  '/admin/settings',           'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z'],
    ] as [$label, $url, $icon])
        <a href="{{ url($url) }}" class="mobile-nav-item">
            <svg viewBox="0 0 24 24"><path d="{{ $icon }}"/></svg>
            <span>{{ $label }}</span>
        </a>
    @endforeach
</nav>

@stack('scripts')
</body>
</html>
