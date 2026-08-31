<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LIKHAE Seller Center')</title>
    @vite([
        'resources/css/seller/app.css',
        'resources/js/seller/app.js'
    ])
    @stack('styles')
</head>
<body>
    <div class="seller-shell">
        @php
            $sellerNavGroups = [
                ['label' => null, 'items' => [['label' => 'Overview', 'url' => '/seller/dashboard', 'icon' => 'home']]],
                ['label' => 'Orders', 'items' => [
                    ['label' => 'All Orders', 'url' => '/seller/orders', 'icon' => 'orders'],
                    ['label' => 'To Prepare', 'url' => '/seller/orders?status=to-prepare', 'icon' => 'box'],
                    ['label' => 'Ready for Pickup', 'url' => '/seller/orders?status=ready', 'icon' => 'truck'],
                    ['label' => 'Returns', 'url' => '/seller/orders/returns', 'icon' => 'return'],
                ]],
                ['label' => 'Products', 'items' => [
                    ['label' => 'Product List', 'url' => '/seller/products', 'icon' => 'products'],
                    ['label' => 'Add Product', 'url' => '/seller/products/create', 'icon' => 'plus'],
                    ['label' => 'Inventory', 'url' => '/seller/inventory', 'icon' => 'inventory'],
                ]],
                ['label' => 'Growth', 'items' => [
                    ['label' => 'Marketing', 'url' => '/seller/marketing', 'icon' => 'tag'],
                    ['label' => 'Flash Deals', 'url' => '/seller/marketing/flash-deals', 'icon' => 'bolt'],
                    ['label' => 'Reviews', 'url' => '/seller/reviews', 'icon' => 'star'],
                    ['label' => 'Messages', 'url' => '/seller/messages', 'icon' => 'message'],
                ]],
                ['label' => 'Operations', 'items' => [
                    ['label' => 'Shipping', 'url' => '/seller/shipping/pickups', 'icon' => 'truck'],
                    ['label' => 'Finance', 'url' => '/seller/finance', 'icon' => 'wallet'],
                    ['label' => 'Reports', 'url' => '/seller/reports', 'icon' => 'chart'],
                ]],
                ['label' => 'Settings', 'items' => [
                    ['label' => 'Store Profile', 'url' => '/seller/store/profile', 'icon' => 'store'],
                    ['label' => 'Verification', 'url' => '/seller/account/verification', 'icon' => 'shield'],
                    ['label' => 'Security', 'url' => '/seller/account/security', 'icon' => 'lock'],
                    ['label' => 'Help & Support', 'url' => '/seller/help', 'icon' => 'help'],
                ]],
            ];
            $sellerIcons = [
                'home' => '<path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/>',
                'orders' => '<rect x="4" y="4" width="16" height="16"/><path d="M8 8h8M8 12h8M8 16h5"/>',
                'box' => '<path d="m4 7 8-4 8 4-8 4-8-4Z"/><path d="M4 7v10l8 4 8-4V7"/>',
                'truck' => '<path d="M3 6h11v10H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
                'return' => '<path d="M9 7H5v-4"/><path d="M5 7c2-3 5-4 8-3 4 1 7 5 6 9s-5 7-9 6c-3-.5-5-2-6-5"/>',
                'products' => '<path d="M4 5h16v14H4z"/><path d="M8 9h8M8 13h5"/>',
                'plus' => '<path d="M12 5v14M5 12h14"/>',
                'inventory' => '<path d="M4 6h16v14H4z"/><path d="M8 6V4h8v2"/><path d="M8 11h8"/>',
                'tag' => '<path d="m3 12 9 9 9-9-9-9H3v9Z"/><circle cx="8" cy="8" r="1"/>',
                'bolt' => '<path d="m13 2-8 12h7l-1 8 8-12h-7l1-8Z"/>',
                'star' => '<path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"/>',
                'message' => '<path d="M4 5h16v12H8l-4 4V5Z"/>',
                'wallet' => '<path d="M4 7h16v12H4z"/><path d="M4 7V5h13v2"/><path d="M15 12h5"/>',
                'chart' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
                'store' => '<path d="M4 10v10h16V10"/><path d="m3 10 2-6h14l2 6"/><path d="M9 20v-6h6v6"/>',
                'shield' => '<path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"/><path d="m9 12 2 2 4-5"/>',
                'lock' => '<rect x="5" y="10" width="14" height="10"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
                'help' => '<circle cx="12" cy="12" r="9"/><path d="M9.7 9a2.5 2.5 0 0 1 4.8 1c0 2-2.5 2-2.5 4"/><path d="M12 18h.01"/>',
            ];
            $sellerCurrentPath = request()->path();
        @endphp

        <aside id="sellerSidebar" class="seller-sidebar">
            <div class="flex h-16 items-center justify-between border-b border-[#E5E0D9] px-5">
                <x-seller.logo :seller="true"/>
                <button id="sidebarClose" class="icon-button lg:hidden" aria-label="Close sidebar"><svg viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
            </div>
            <nav class="seller-nav">
                @foreach($sellerNavGroups as $group)
                    <div class="seller-nav-group">
                        @if($group['label'])<p class="seller-nav-label">{{ $group['label'] }}</p>@endif
                        @foreach($group['items'] as $item)
                            @php $path = trim(parse_url($item['url'], PHP_URL_PATH), '/'); $active = $sellerCurrentPath === $path || ($path !== 'seller/dashboard' && str_starts_with($sellerCurrentPath, $path)); @endphp
                            <a href="{{ url($item['url']) }}" class="seller-nav-item {{ $active ? 'is-active' : '' }}"><svg viewBox="0 0 24 24">{!! $sellerIcons[$item['icon']] !!}</svg><span>{{ $item['label'] }}</span></a>
                        @endforeach
                    </div>
                @endforeach
            </nav>
            <div class="border-t border-[#E5E0D9] p-4"><form method="POST" action="{{ route('logout') }}">@csrf<button class="seller-nav-item w-full" type="submit"><svg viewBox="0 0 24 24"><path d="M10 5H4v14h6"/><path d="m14 8 4 4-4 4"/><path d="M18 12H9"/></svg><span>Logout</span></button></form></div>
        </aside>
        <div id="sidebarOverlay" class="seller-sidebar-overlay"></div>

        <div class="seller-workspace">
            <header class="seller-header">
                <div class="flex min-w-0 items-center gap-3"><button id="sidebarOpen" class="icon-button lg:hidden" aria-label="Open navigation"><svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button><div class="hidden min-w-0 sm:block"><p class="truncate text-xs font-semibold text-[#6B6864]">Maria’s Local Finds</p><p class="truncate text-[10px] text-[#96918B]">Seller Center</p></div></div>
                <form action="{{ url('/seller/search') }}" method="GET" class="seller-global-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input name="q" type="search" placeholder="Search orders, products, customers..."></form>
                <div class="ml-auto flex items-center gap-1"><a href="{{ url('/seller/help') }}" class="icon-button" aria-label="Help"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M10 9a2.2 2.2 0 1 1 3.1 2c-.7.4-1.1 1-1.1 2"/><path d="M12 17h.01"/></svg></a><a href="{{ url('/seller/messages') }}" class="icon-button relative" aria-label="Messages"><svg viewBox="0 0 24 24"><path d="M4 5h16v12H8l-4 4V5Z"/></svg><span class="header-badge">4</span></a><a href="{{ url('/seller/notifications') }}" class="icon-button relative" aria-label="Notifications"><svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg><span class="header-badge">7</span></a><a href="{{ url('/seller/store/profile') }}" class="ml-2 flex items-center gap-2 border-l border-[#E5E0D9] pl-3"><span class="grid h-8 w-8 place-items-center rounded-full bg-[#171717] text-[11px] font-black text-white">MS</span><span class="hidden xl:block"><span class="block text-[11px] font-bold">Maria Santos</span><span class="block text-[9px] text-[#96918B]">Owner</span></span></a></div>
            </header>

            <main class="seller-content">
                @yield('content')
            </main>
        </div>
    </div>

    <nav class="seller-mobile-nav lg:hidden">
        @foreach([['Home','/seller/dashboard','M3 11l9-8 9 8M5 10v10h14V10'],['Orders','/seller/orders','M4 4h16v16H4zM8 8h8M8 12h8'],['Products','/seller/products','M4 5h16v14H4zM8 9h8'],['Messages','/seller/messages','M4 5h16v12H8l-4 4V5Z'],['Account','/seller/store/profile','M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4 21a8 8 0 0 1 16 0']] as [$label, $url, $icon])
            <a href="{{ url($url) }}" class="mobile-nav-item"><svg viewBox="0 0 24 24"><path d="{{ $icon }}"/></svg><span>{{ $label }}</span></a>
        @endforeach
    </nav>
    @stack('scripts')
</body>
</html>
