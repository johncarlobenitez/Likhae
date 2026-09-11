@props([
    'active' => null,
    'notificationCount' => null,
])

@php
    $admin = auth()->user();
    $demoUser = session('demo_user');

    $adminName = data_get($admin, 'name')
        ?? data_get($demoUser, 'name')
        ?? 'Admin Account';

    $adminInitials = collect(explode(' ', trim($adminName)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('') ?: 'AD';

    $avatar = data_get($admin, 'profile_photo_url')
        ?? data_get($admin, 'avatar_url');

    $notificationCount = $notificationCount
        ?? session('admin_notification_count', 0);

    $icons = [
        'dashboard' => '
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
        ',

        'registrations' => '
            <path d="M6 3h9l4 4v14H6z"></path>
            <path d="M14 3v5h5"></path>
            <path d="M9 13h6"></path>
            <path d="M9 17h4"></path>
        ',

        'users' => '
            <circle cx="9" cy="8" r="3"></circle>
            <path d="M3 20c0-4 2.5-6 6-6s6 2 6 6"></path>
            <circle cx="17" cy="9" r="2.5"></circle>
            <path d="M15 15c3.7 0 6 1.7 6 5"></path>
        ',

        'products' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"></path>
            <path d="m4 7.5 8 4.5 8-4.5"></path>
            <path d="M12 12v9"></path>
        ',

        'compliance' => '
            <path d="M12 3l7 3v5c0 5-3 8.5-7 10-4-1.5-7-5-7-10V6z"></path>
            <path d="m9 12 2 2 4-4"></path>
        ',

        'complaints' => '
            <path d="M4 5h16v11H8l-4 4z"></path>
            <path d="M12 8v4"></path>
            <path d="M12 15h.01"></path>
        ',

        'finance' => '
            <path d="M4 7h16v12H4z"></path>
            <path d="M4 10h16"></path>
            <path d="M8 15h4"></path>
            <path d="M8 4h8"></path>
        ',

        'reports' => '
            <path d="M5 20V10"></path>
            <path d="M12 20V4"></path>
            <path d="M19 20v-7"></path>
            <path d="M3 20h18"></path>
        ',

        'messages' => '
            <path d="M4 5h16v11H8l-4 4z"></path>
            <path d="M8 9h8"></path>
            <path d="M8 13h5"></path>
        ',

        'notifications' => '
            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
            <path d="M10 21h4"></path>
        ',

        'settings' => '
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21h-4v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3v-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1L7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V3h4v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1v4H21a1.7 1.7 0 0 0-1.6 1Z"></path>
        ',

        'account' => '
            <circle cx="12" cy="8" r="4"></circle>
            <path d="M4 21a8 8 0 0 1 16 0"></path>
        ',

        'moon' => '
            <path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"></path>
        ',

        'logout' => '
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <path d="m16 17 5-5-5-5"></path>
            <path d="M21 12H9"></path>
        ',

        'menu' => '
            <path d="M4 6h16"></path>
            <path d="M4 12h16"></path>
            <path d="M4 18h16"></path>
        ',
    ];

    $groups = [
        [
            'label' => 'User Management',
            'items' => [
                [
                    'key' => 'registrations',
                    'label' => 'Registrations',
                    'route' => 'admin.registrations',
                    'icon' => 'registrations',
                    'active' => [
                        'admin.registrations',
                        'admin.registrations.*',
                        'admin.sellers.approvals',
                    ],
                ],
                [
                    'key' => 'users',
                    'label' => 'User Accounts',
                    'route' => 'admin.users',
                    'icon' => 'users',
                    'active' => [
                        'admin.users',
                        'admin.riders',
                    ],
                ],
            ],
        ],

        [
            'label' => 'Marketplace',
            'items' => [
                [
                    'key' => 'products',
                    'label' => 'Products',
                    'route' => 'admin.products',
                    'icon' => 'products',
                    'active' => [
                        'admin.products',
                        'admin.categories',
                    ],
                ],
                [
                    'key' => 'compliance',
                    'label' => 'Seller Compliance',
                    'route' => 'admin.compliance',
                    'icon' => 'compliance',
                    'active' => [
                        'admin.compliance',
                    ],
                ],
                [
                    'key' => 'complaints',
                    'label' => 'Complaints & Disputes',
                    'route' => 'admin.complaints',
                    'icon' => 'complaints',
                    'active' => [
                        'admin.complaints',
                        'admin.refunds',
                    ],
                ],
            ],
        ],

        [
            'label' => 'Finance & Analytics',
            'items' => [
                [
                    'key' => 'finance',
                    'label' => 'Finance',
                    'route' => 'admin.finance',
                    'icon' => 'finance',
                    'active' => [
                        'admin.finance',
                        'admin.payments',
                    ],
                ],
                [
                    'key' => 'reports',
                    'label' => 'Reports',
                    'route' => 'admin.reports',
                    'icon' => 'reports',
                    'active' => [
                        'admin.reports',
                    ],
                ],
            ],
        ],

        [
            'label' => 'Communication',
            'items' => [
                [
                    'key' => 'messages',
                    'label' => 'Messages',
                    'route' => 'admin.messages',
                    'icon' => 'messages',
                    'active' => [
                        'admin.messages',
                    ],
                ],
                [
                    'key' => 'notifications',
                    'label' => 'Notifications',
                    'route' => 'admin.notifications',
                    'icon' => 'notifications',
                    'active' => [
                        'admin.notifications',
                    ],
                    'count' => $notificationCount,
                ],
            ],
        ],

        [
            'label' => 'System',
            'items' => [
                [
                    'key' => 'settings',
                    'label' => 'Platform Settings',
                    'route' => 'admin.settings',
                    'icon' => 'settings',
                    'active' => [
                        'admin.settings',
                    ],
                ],
                [
                    'key' => 'account',
                    'label' => 'My Account',
                    'route' => 'admin.account',
                    'icon' => 'account',
                    'active' => [
                        'admin.account',
                    ],
                ],
            ],
        ],
    ];

    $routeActive = function (array $routes) {
        return collect($routes)
            ->contains(fn ($route) => request()->routeIs($route));
    };
@endphp

<style>
    :root {
        --admin-side-width: 252px;
        --admin-side-collapsed: 76px;

        --admin-bg: #FFFDF9;
        --admin-page: #FBF7F2;
        --admin-soft: #FAF3EC;
        --admin-active: #F3E4DE;

        --admin-line: #E8D8C8;
        --admin-line-strong: #DCC8B8;

        --admin-primary: #641F19;
        --admin-primary-dark: #4D1712;

        --admin-text: #694737;
        --admin-text-strong: #311E18;
        --admin-muted: #A27F6C;
        --admin-muted-light: #A88B7B;
    }

    #adminSidebar,
    #adminSidebar * {
        box-sizing: border-box;
    }

    #adminSidebar {
        position: fixed;
        inset: 0 auto 0 0;
        z-index: 60;

        display: flex;
        width: var(--admin-side-width);
        height: 100vh;
        flex-direction: column;

        overflow: hidden;

        border-right: 1px solid var(--admin-line);
        background: var(--admin-bg);
        color: var(--admin-text);

        font-family:
            "DM Sans",
            system-ui,
            -apple-system,
            BlinkMacSystemFont,
            "Segoe UI",
            sans-serif;

        transition:
            width .22s ease,
            transform .22s ease;
    }

    /* HEADER */

    .ad-side-head {
        display: flex;
        min-height: 62px;
        align-items: center;
        gap: 11px;

        padding: 0 11px;

        border-bottom: 1px solid var(--admin-line);
    }

    .ad-side-menu {
        display: grid;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        place-items: center;

        border: 1px solid var(--admin-line);
        border-radius: 10px;

        background: var(--admin-soft);
        color: #6C4936;

        cursor: pointer;

        transition: .15s ease;
    }

    .ad-side-menu:hover {
        border-color: var(--admin-line-strong);
        background: var(--admin-active);
        color: var(--admin-primary);
    }

    .ad-side-menu svg {
        width: 18px;
        height: 18px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ad-side-brand {
        display: flex;
        min-width: 0;
        align-items: center;

        color: inherit;
        text-decoration: none;
    }

    .ad-side-brand .likhae-logo--sidebar {
        width: auto;
        max-width: 151px;
        max-height: 40px;
    }

    /* PROFILE */

    .ad-side-profile {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        min-height: 61px;
        align-items: center;
        gap: 10px;

        margin: 13px 10px 8px;
        padding: 10px;

        border: 1px solid var(--admin-line);
        border-radius: 13px;

        background:
            radial-gradient(
                circle at 94% 9%,
                rgba(193,151,113,.12),
                transparent 34%
            ),
            var(--admin-soft);
    }

    .ad-side-avatar {
        display: grid;
        width: 39px;
        height: 39px;
        flex: 0 0 39px;
        place-items: center;
        overflow: hidden;

        border-radius: 10px;

        background: var(--admin-primary);
        color: #FFFFFF;

        font-size: 10px;
        font-weight: 900;
    }

    .ad-side-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ad-side-profile-copy {
        min-width: 0;
    }

    .ad-side-profile-copy strong {
        display: block;
        overflow: hidden;

        color: var(--admin-text-strong);

        font-size: 10px;
        font-weight: 800;
        line-height: 1.2;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ad-side-profile-copy small {
        display: block;
        margin-top: 3px;

        color: var(--admin-muted);

        font-size: 7px;
        font-weight: 500;
        line-height: 1.15;
    }

    .ad-side-verified {
        display: grid;
        width: 19px;
        height: 19px;
        flex: 0 0 19px;
        place-items: center;

        border-radius: 999px;

        background: var(--admin-primary);
        color: #FFFFFF;

        font-size: 9px;
        font-weight: 900;
    }

    /* SCROLL */

    .ad-side-scroll {
        min-height: 0;
        flex: 1;

        overflow-x: hidden;
        overflow-y: auto;

        padding: 4px 8px 14px;

        scrollbar-width: thin;
        scrollbar-color: #D8C2B1 transparent;
    }

    .ad-side-scroll::-webkit-scrollbar {
        width: 5px;
    }

    .ad-side-scroll::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: #D8C2B1;
    }

    /* NAV */

    .ad-side-group {
        margin-top: 5px;
    }

    .ad-side-heading {
        display: block;

        margin: 0;
        padding: 14px 11px 6px;

        color: var(--admin-muted-light);

        font-size: 7px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .ad-side-list {
        display: grid;
        gap: 2px;
    }

    .ad-side-link {
        position: relative;

        display: flex;
        width: 100%;
        min-height: 41px;
        align-items: center;
        gap: 10px;

        padding: 0 11px;

        border: 0;
        border-radius: 9px;

        background: transparent;
        color: #72503F;

        font-size: 10.5px;
        font-weight: 500;
        line-height: 1;

        text-decoration: none;

        transition: .14s ease;
    }

    .ad-side-link:hover {
        background: var(--admin-soft);
        color: var(--admin-primary);
    }

    .ad-side-link.is-active {
        background: var(--admin-active);
        color: var(--admin-primary);
        font-weight: 700;
    }

    .ad-side-link.is-active::before {
        position: absolute;
        top: 8px;
        bottom: 8px;
        left: 0;

        width: 3px;

        border-radius: 0 6px 6px 0;

        background: var(--admin-primary);

        content: "";
    }

    .ad-side-icon {
        display: grid;
        width: 26px;
        height: 26px;
        flex: 0 0 26px;
        place-items: center;

        color: currentColor;
    }

    .ad-side-icon svg {
        width: 18px;
        height: 18px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.6;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ad-side-label {
        min-width: 0;
        flex: 1;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ad-side-badge {
        display: inline-flex;
        min-width: 20px;
        height: 20px;
        align-items: center;
        justify-content: center;

        margin-left: auto;

        padding: 0 6px;

        border-radius: 999px;

        background: #9E7D68;
        color: #FFFFFF;

        font-size: 8px;
        font-weight: 800;
        line-height: 1;
    }

    /* FOOTER */

    .ad-side-bottom {
        display: grid;
        gap: 2px;

        padding: 7px 8px 9px;

        border-top: 1px solid var(--admin-line);

        background: var(--admin-bg);
    }

    .ad-side-bottom form {
        margin: 0;
    }

    .ad-side-theme,
    .ad-side-logout {
        display: flex;
        width: 100%;
        min-height: 43px;
        align-items: center;
        gap: 10px;

        padding: 0 11px;

        border: 0;
        border-radius: 9px;

        background: transparent;
        color: #72503F;

        font-family: inherit;
        font-size: 10.5px;
        font-weight: 500;
        text-align: left;

        cursor: pointer;

        transition: .14s ease;
    }

    .ad-side-theme {
        justify-content: space-between;
    }

    .ad-side-theme:hover,
    .ad-side-logout:hover {
        background: var(--admin-soft);
        color: var(--admin-primary);
    }

    .ad-side-theme-copy {
        display: grid;
        min-width: 0;
        flex: 1;
        gap: 2px;
    }

    .ad-side-theme-copy strong {
        color: inherit;

        font-size: 10.5px;
        font-weight: 500;
        line-height: 1.1;
    }

    .ad-side-theme-copy small {
        color: var(--admin-muted);

        font-size: 8px;
        font-weight: 500;
        line-height: 1.1;
    }

    .ad-side-theme-state {
        display: inline-flex;
        min-width: 40px;
        min-height: 21px;
        align-items: center;
        justify-content: center;

        margin-left: auto;
        padding: 0 9px;

        border-radius: 999px;

        background: #E3E3E2;
        color: #494644;

        font-size: 7px;
        font-weight: 900;
        line-height: 1;
    }

    .ad-side-logout {
        justify-content: flex-start;
    }

    /* COLLAPSED */

    #adminSidebar.is-collapsed {
        width: var(--admin-side-collapsed);
    }

    #adminSidebar.is-collapsed .ad-side-brand,
    #adminSidebar.is-collapsed .ad-side-profile-copy,
    #adminSidebar.is-collapsed .ad-side-verified,
    #adminSidebar.is-collapsed .ad-side-heading,
    #adminSidebar.is-collapsed .ad-side-label,
    #adminSidebar.is-collapsed .ad-side-badge,
    #adminSidebar.is-collapsed .ad-side-theme-copy,
    #adminSidebar.is-collapsed .ad-side-theme-state {
        display: none !important;
    }

    #adminSidebar.is-collapsed .ad-side-head {
        justify-content: center;
        padding-inline: 0;
    }

    #adminSidebar.is-collapsed .ad-side-profile {
        display: flex;
        min-height: auto;
        justify-content: center;

        margin-inline: 8px;
        padding: 8px 0;
    }

    #adminSidebar.is-collapsed .ad-side-link,
    #adminSidebar.is-collapsed .ad-side-theme,
    #adminSidebar.is-collapsed .ad-side-logout {
        justify-content: center;
        padding-inline: 0;
    }

    /* DARK */

    html.dark #adminSidebar,
    html.dark .ad-side-bottom {
        border-color: #30231F;
        background: #130F0E;
        color: #F5EFE8;
    }

    html.dark .ad-side-head,
    html.dark .ad-side-bottom {
        border-color: #30231F;
    }

    html.dark .ad-side-menu {
        border-color: #3B2E27;
        background: #211B17;
        color: #EBA99D;
    }

    html.dark .ad-side-profile {
        border-color: #30231F;
        background: #1B1513;
    }

    html.dark .ad-side-profile-copy strong {
        color: #F5EFE8;
    }

    html.dark .ad-side-profile-copy small,
    html.dark .ad-side-heading,
    html.dark .ad-side-theme-copy small {
        color: #8F817A;
    }

    html.dark .ad-side-link,
    html.dark .ad-side-theme,
    html.dark .ad-side-logout {
        color: #B8AAA1;
    }

    html.dark .ad-side-link:hover,
    html.dark .ad-side-theme:hover,
    html.dark .ad-side-logout:hover {
        background: #211B17;
        color: #F3C6BA;
    }

    html.dark .ad-side-link.is-active {
        background: #3A1111;
        color: #F3C6BA;
    }

    html.dark .ad-side-link.is-active::before {
        background: #A84538;
    }

    html.dark .ad-side-badge {
        background: #8E7363;
        color: #FFFFFF;
    }

    html.dark .ad-side-theme-state {
        background: #A84538;
        color: #FFFFFF;
    }

    html.dark .ad-side-avatar,
    html.dark .ad-side-verified {
        background: #A84538;
    }

    /* MOBILE */

    #adminSidebarOverlay {
        visibility: hidden;
        position: fixed;
        inset: 0;
        z-index: 55;

        background: rgba(28,22,15,.42);

        opacity: 0;
        backdrop-filter: blur(3px);

        transition:
            opacity .18s ease,
            visibility .18s ease;
    }

    #adminSidebarOverlay.is-visible {
        visibility: visible;
        opacity: 1;
    }

    @media (max-width: 1023px) {
        #adminSidebar,
        #adminSidebar.is-collapsed {
            width: var(--admin-side-width);
            transform: translateX(-100%);
        }

        #adminSidebar.is-open {
            transform: translateX(0);
        }

        #adminSidebar.is-collapsed .ad-side-brand,
        #adminSidebar.is-collapsed .ad-side-profile-copy,
        #adminSidebar.is-collapsed .ad-side-verified,
        #adminSidebar.is-collapsed .ad-side-heading,
        #adminSidebar.is-collapsed .ad-side-label,
        #adminSidebar.is-collapsed .ad-side-badge,
        #adminSidebar.is-collapsed .ad-side-theme-copy,
        #adminSidebar.is-collapsed .ad-side-theme-state {
            display: initial !important;
        }

        #adminSidebar.is-collapsed .ad-side-head {
            justify-content: flex-start;
            padding-inline: 11px;
        }

        #adminSidebar.is-collapsed .ad-side-profile {
            display: grid;
            grid-template-columns: auto minmax(0,1fr) auto;
            min-height: 61px;
            justify-content: initial;

            margin: 13px 10px 8px;
            padding: 10px;
        }

        #adminSidebar.is-collapsed .ad-side-link,
        #adminSidebar.is-collapsed .ad-side-theme,
        #adminSidebar.is-collapsed .ad-side-logout {
            justify-content: initial;
            padding-inline: 11px;
        }
    }
</style>

<div
    id="adminSidebarOverlay"
    aria-hidden="true"
></div>

<aside
    id="adminSidebar"
    aria-label="Admin navigation"
>
    <div class="ad-side-head">
        <button
            type="button"
            id="adminSidebarToggle"
            class="ad-side-menu"
            aria-label="Toggle admin sidebar"
            aria-expanded="true"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['menu'] !!}
            </svg>
        </button>

        <a
            href="{{ route('admin.dashboard') }}"
            class="ad-side-brand"
            aria-label="LIKHAE Admin Dashboard"
        >
            <x-likhae-logo
                context="Admin Center"
                class="likhae-logo--sidebar"
            />
        </a>
    </div>

    <section class="ad-side-profile">
        <span class="ad-side-avatar">
            @if($avatar)
                <img
                    src="{{ $avatar }}"
                    alt="{{ $adminName }}"
                >
            @else
                {{ $adminInitials }}
            @endif
        </span>

        <span class="ad-side-profile-copy">
            <strong>
                {{ $adminName }}
            </strong>

            <small>
                LIKHAE Administrator
            </small>
        </span>

        <span
            class="ad-side-verified"
            title="Administrator account"
        >
            ✓
        </span>
    </section>

    <nav class="ad-side-scroll">

        <a
            href="{{ route('admin.dashboard') }}"
            class="ad-side-link
                {{ request()->routeIs('admin.dashboard') || $active === 'dashboard' ? 'is-active' : '' }}"
            @if(request()->routeIs('admin.dashboard') || $active === 'dashboard')
                aria-current="page"
            @endif
        >
            <span class="ad-side-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['dashboard'] !!}
                </svg>
            </span>

            <span class="ad-side-label">
                Dashboard
            </span>
        </a>

        @foreach($groups as $group)
            <section class="ad-side-group">
                <p class="ad-side-heading">
                    {{ $group['label'] }}
                </p>

                <div class="ad-side-list">
                    @foreach($group['items'] as $item)
                        @php
                            $isActive = $active === $item['key']
                                || $routeActive($item['active']);
                        @endphp

                        <a
                            href="{{ route($item['route']) }}"
                            class="ad-side-link {{ $isActive ? 'is-active' : '' }}"
                            @if($isActive) aria-current="page" @endif
                        >
                            <span class="ad-side-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    {!! $icons[$item['icon']] !!}
                                </svg>
                            </span>

                            <span class="ad-side-label">
                                {{ $item['label'] }}
                            </span>

                            @if(($item['count'] ?? 0) > 0)
                                <span class="ad-side-badge">
                                    {{ $item['count'] > 99 ? '99+' : $item['count'] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach

    </nav>

    <div class="ad-side-bottom">

        <button
            type="button"
            id="adminThemeToggle"
            class="ad-side-theme"
            aria-label="Toggle light and dark mode"
            aria-pressed="false"
        >
            <span class="ad-side-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['moon'] !!}
                </svg>
            </span>

            <span class="ad-side-theme-copy">
                <strong>
                    Appearance
                </strong>

                <small>
                    Light / Dark Mode
                </small>
            </span>

            <span
                id="adminThemeState"
                class="ad-side-theme-state"
            >
                OFF
            </span>
        </button>

        @if(Route::has('logout'))
            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="ad-side-logout"
                >
                    <span class="ad-side-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['logout'] !!}
                        </svg>
                    </span>

                    <span class="ad-side-label">
                        Logout
                    </span>
                </button>
            </form>
        @endif

    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const root =
        document.documentElement;

    const body =
        document.body;

    const sidebar =
        document.getElementById('adminSidebar');

    const overlay =
        document.getElementById('adminSidebarOverlay');

    const sidebarToggle =
        document.getElementById('adminSidebarToggle');

    const themeToggle =
        document.getElementById('adminThemeToggle');

    const themeState =
        document.getElementById('adminThemeState');


    function isMobile() {
        return window.innerWidth < 1024;
    }


    function syncTheme() {
        const dark =
            root.classList.contains('dark');

        if (themeState) {
            themeState.textContent =
                dark ? 'ON' : 'OFF';
        }

        themeToggle?.setAttribute(
            'aria-pressed',
            dark ? 'true' : 'false'
        );
    }


    function restoreTheme() {
        const savedTheme =
            localStorage.getItem('likhae-theme') || 'light';

        root.classList.toggle(
            'dark',
            savedTheme === 'dark'
        );

        syncTheme();
    }


    function openMobileSidebar() {
        sidebar?.classList.add('is-open');
        overlay?.classList.add('is-visible');

        if (overlay) {
            overlay.setAttribute(
                'aria-hidden',
                'false'
            );
        }

        body.style.overflow = 'hidden';
    }


    function closeMobileSidebar() {
        sidebar?.classList.remove('is-open');
        overlay?.classList.remove('is-visible');

        if (overlay) {
            overlay.setAttribute(
                'aria-hidden',
                'true'
            );
        }

        body.style.overflow = '';
    }


    function restoreSidebarState() {
        if (!sidebar) {
            return;
        }

        if (isMobile()) {
            sidebar.classList.remove(
                'is-collapsed'
            );

            body.classList.remove(
                'admin-sidebar-collapsed'
            );

            return;
        }

        const collapsed =
            localStorage.getItem(
                'likhae-admin-sidebar'
            ) === 'collapsed';

        sidebar.classList.toggle(
            'is-collapsed',
            collapsed
        );

        body.classList.toggle(
            'admin-sidebar-collapsed',
            collapsed
        );

        sidebarToggle?.setAttribute(
            'aria-expanded',
            collapsed ? 'false' : 'true'
        );
    }


    function toggleSidebar() {
        if (!sidebar) {
            return;
        }

        if (isMobile()) {
            if (sidebar.classList.contains('is-open')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }

            return;
        }

        const collapsed =
            !sidebar.classList.contains(
                'is-collapsed'
            );

        sidebar.classList.toggle(
            'is-collapsed',
            collapsed
        );

        body.classList.toggle(
            'admin-sidebar-collapsed',
            collapsed
        );

        sidebarToggle?.setAttribute(
            'aria-expanded',
            collapsed ? 'false' : 'true'
        );

        localStorage.setItem(
            'likhae-admin-sidebar',
            collapsed
                ? 'collapsed'
                : 'expanded'
        );

        window.dispatchEvent(
            new CustomEvent(
                'likhae:admin-sidebar',
                {
                    detail: {
                        collapsed: collapsed,
                        width: collapsed
                            ? 76
                            : 252
                    }
                }
            )
        );
    }


    sidebarToggle?.addEventListener(
        'click',
        toggleSidebar
    );


    overlay?.addEventListener(
        'click',
        closeMobileSidebar
    );


    document
        .querySelectorAll('[data-admin-sidebar-open]')
        .forEach(function (button) {
            button.addEventListener(
                'click',
                openMobileSidebar
            );
        });


    window.addEventListener(
        'likhae:admin-sidebar-open',
        openMobileSidebar
    );


    themeToggle?.addEventListener(
        'click',
        function () {
            const dark =
                !root.classList.contains('dark');

            root.classList.toggle(
                'dark',
                dark
            );

            localStorage.setItem(
                'likhae-theme',
                dark
                    ? 'dark'
                    : 'light'
            );

            syncTheme();
        }
    );


    window.addEventListener(
        'resize',
        function () {
            closeMobileSidebar();
            restoreSidebarState();
        }
    );


    restoreTheme();
    restoreSidebarState();
});
</script>
