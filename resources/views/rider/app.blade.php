<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap"
        rel="stylesheet"
    >

    <title>@yield('title', 'LIKHAE Rider Panel')</title>

    <script>
        (function () {
            const savedTheme =
                localStorage.getItem('likhae-theme') || 'light';

            document.documentElement.classList.toggle(
                'dark',
                savedTheme === 'dark'
            );
        })();
    </script>

    @vite([
        'resources/css/logistic/app.css'
    ])

    <style>
        :root {
            --ra-page: #FBF7F2;
            --ra-page-soft: #F6EFE7;
            --ra-card: #FFFDF9;
            --ra-card-soft: #FFF9F3;

            --ra-line: #EADCCC;
            --ra-line-strong: #DBCEC1;

            --ra-maroon: #561C17;
            --ra-maroon-hover: #642920;
            --ra-maroon-dark: #3E130F;
            --ra-active: #F3E4DE;

            --ra-text: #3B211B;
            --ra-text-deep: #1C160F;
            --ra-muted: #987865;
            --ra-muted-light: #A99386;
            --ra-brown: #6C4936;

            --ra-success: #256F4A;
            --ra-success-soft: #EAF7EF;

            --ra-shadow: 0 14px 38px rgba(74, 35, 27, .06);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body.rider-shell {
            margin: 0;
            background: var(--ra-page);
            color: var(--ra-text);
            font-family: "DM Sans", system-ui, sans-serif;
        }

        .rider-layout {
            min-height: 100vh;
            background:
                radial-gradient(circle at 80% -10%, rgba(193, 151, 113, .08), transparent 27%),
                var(--ra-page);
        }

        .rider-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 60;
            display: flex;
            width: 248px;
            flex-direction: column;
            overflow-y: auto;
            border-right: 1px solid var(--ra-line);
            background: rgba(255, 253, 249, .97);
            box-shadow: 8px 0 26px rgba(74, 35, 27, .035);
            backdrop-filter: blur(18px);
            transition: transform .25s ease;
        }

        .rider-brand {
            display: flex;
            min-height: 68px;
            align-items: center;
            gap: 11px;
            padding: 0 18px;
            border-bottom: 1px solid var(--ra-line);
        }

        .rider-brand-mark {
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border-radius: 10px;
            background: var(--ra-maroon);
            color: #FFFFFF;
            font-family: "Instrument Serif", Georgia, serif;
            font-size: 20px;
            line-height: 1;
            box-shadow: 0 8px 18px rgba(86, 28, 23, .16);
        }

        .rider-brand-copy strong {
            display: block;
            color: var(--ra-text-deep);
            font-size: 16px;
            font-weight: 900;
            letter-spacing: -.04em;
        }

        .rider-brand-copy span {
            display: block;
            margin-top: 1px;
            color: var(--ra-muted);
            font-size: 7px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .rider-profile-card {
            margin: 14px 12px 6px;
            padding: 12px;
            border: 1px solid var(--ra-line);
            border-radius: 15px;
            background:
                radial-gradient(circle at 92% 10%, rgba(193,151,113,.13), transparent 32%),
                var(--ra-page-soft);
        }

        .rider-profile-top {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rider-avatar {
            display: grid;
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            place-items: center;
            border-radius: 999px;
            background: var(--ra-maroon);
            color: #FFFFFF;
            font-size: 10px;
            font-weight: 900;
            box-shadow: 0 8px 18px rgba(86, 28, 23, .13);
        }

        .rider-profile-copy {
            min-width: 0;
        }

        .rider-profile-copy strong {
            display: block;
            overflow: hidden;
            color: var(--ra-text);
            font-size: 10px;
            font-weight: 900;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .rider-profile-copy span {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 3px;
            color: var(--ra-success);
            font-size: 7px;
            font-weight: 850;
        }

        .rider-profile-copy span::before {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: var(--ra-success);
            content: "";
        }

        .rider-nav {
            flex: 1;
            padding: 12px 10px 18px;
        }

        .rider-nav-label {
            display: block;
            padding: 5px 10px 8px;
            color: var(--ra-muted-light);
            font-size: 7px;
            font-weight: 900;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .rider-nav-list {
            display: grid;
            gap: 3px;
        }

        .rider-nav-link {
            position: relative;
            display: flex;
            min-height: 42px;
            align-items: center;
            gap: 10px;
            padding: 0 11px;
            border-radius: 11px;
            color: var(--ra-brown);
            font-size: 9px;
            font-weight: 800;
            text-decoration: none;
            transition: .14s ease;
        }

        .rider-nav-link:hover {
            background: var(--ra-page-soft);
            color: var(--ra-maroon);
        }

        .rider-nav-link.is-active {
            background: var(--ra-active);
            color: var(--ra-maroon);
        }

        .rider-nav-link.is-active::before {
            position: absolute;
            top: 9px;
            bottom: 9px;
            left: 0;
            width: 3px;
            border-radius: 0 5px 5px 0;
            background: var(--ra-maroon);
            content: "";
        }

        .rider-nav-icon {
            display: grid;
            width: 26px;
            height: 26px;
            flex: 0 0 26px;
            place-items: center;
            color: currentColor;
        }

        .rider-nav-icon svg {
            width: 17px;
            height: 17px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.7;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .rider-sidebar-footer {
            display: grid;
            gap: 7px;
            padding: 11px 10px 13px;
            border-top: 1px solid var(--ra-line);
        }

        .rider-footer-btn {
            display: flex;
            min-height: 42px;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 0 11px;
            border: 1px solid transparent;
            border-radius: 11px;
            background: transparent;
            color: var(--ra-brown);
            font: inherit;
            cursor: pointer;
            transition: .14s ease;
        }

        .rider-footer-btn:hover {
            border-color: var(--ra-line);
            background: var(--ra-page-soft);
            color: var(--ra-maroon);
        }

        .rider-footer-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rider-footer-left svg {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.6;
        }

        .rider-appearance-text {
            display: grid;
            text-align: left;
        }

        .rider-appearance-text strong {
            color: inherit;
            font-size: 10.5px;
            font-weight: 500;
            line-height: 1.1;
        }

        .rider-appearance-text small {
            display: block;
            margin-top: 2px;
            color: currentColor;
            opacity: 0.5;
            font-size: 8px;
            line-height: 1.1;
            font-weight: inherit;
        }

        .rider-theme-badge {
            display: inline-flex;
            min-height: 23px;
            align-items: center;
            padding: 0 7px;
            border: 1px solid var(--ra-line);
            border-radius: 999px;
            background: var(--ra-card);
            color: var(--ra-muted);
            font-size: 7px;
            font-weight: 900;
        }

        .rider-logout-form {
            margin: 0;
        }

        .rider-logout-btn {
            justify-content: flex-start;
            color: var(--ra-muted);
        }

        .rider-main {
            min-height: 100vh;
            margin-left: 248px;
        }

        .rider-topbar {
            position: sticky;
            top: 0;
            z-index: 40;
            display: flex;
            min-height: 68px;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 0 26px;
            border-bottom: 1px solid var(--ra-line);
            background: rgba(251, 247, 242, .88);
            backdrop-filter: blur(18px);
        }

        .rider-topbar-copy strong {
            display: block;
            color: var(--ra-text-deep);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: -.025em;
        }

        .rider-topbar-copy span {
            display: block;
            margin-top: 2px;
            color: var(--ra-muted);
            font-size: 8px;
        }

        .rider-topbar-status {
            display: inline-flex;
            min-height: 31px;
            align-items: center;
            gap: 7px;
            padding: 0 10px;
            border: 1px solid #CFE8DA;
            border-radius: 999px;
            background: var(--ra-success-soft);
            color: var(--ra-success);
            font-size: 7px;
            font-weight: 900;
        }

        .rider-topbar-status::before {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: currentColor;
            content: "";
        }

        .rider-content {
            width: 100%;
            padding: 24px 26px 34px;
        }

        .rider-mobile-bar {
            display: none;
        }

        .rider-mobile-menu-btn {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border: 1px solid var(--ra-line);
            border-radius: 11px;
            background: var(--ra-card);
            color: var(--ra-maroon);
            cursor: pointer;
        }

        .rider-mobile-menu-btn svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
        }

        .rider-sidebar-overlay {
            visibility: hidden;
            position: fixed;
            inset: 0;
            z-index: 55;
            background: rgba(28, 22, 15, .42);
            opacity: 0;
            backdrop-filter: blur(3px);
            transition: opacity .2s ease, visibility .2s ease;
        }

        .rider-sidebar-overlay.is-visible {
            visibility: visible;
            opacity: 1;
        }

        @media (max-width: 1023px) {
            .rider-sidebar {
                transform: translateX(-100%);
            }

            .rider-sidebar.is-open {
                transform: translateX(0);
            }

            .rider-main {
                margin-left: 0;
            }

            .rider-topbar {
                display: none;
            }

            .rider-mobile-bar {
                position: sticky;
                top: 0;
                z-index: 45;
                display: flex;
                min-height: 64px;
                align-items: center;
                justify-content: space-between;
                padding: 0 16px;
                border-bottom: 1px solid var(--ra-line);
                background: rgba(251, 247, 242, .92);
                backdrop-filter: blur(18px);
            }

            .rider-mobile-brand {
                display: flex;
                align-items: center;
                gap: 9px;
            }

            .rider-mobile-brand .rider-brand-mark {
                width: 31px;
                height: 31px;
                border-radius: 9px;
                font-size: 18px;
            }

            .rider-mobile-brand strong {
                color: var(--ra-text-deep);
                font-size: 13px;
                font-weight: 900;
                letter-spacing: -.04em;
            }

            .rider-content {
                padding: 20px 16px 28px;
            }
        }

        html.dark {
            color-scheme: dark;
        }

        html.dark body.rider-shell,
        html.dark .rider-layout {
            background: #130F0E;
            color: #F5EFE8;
        }

        html.dark .rider-sidebar {
            border-color: #30231F;
            background: rgba(19, 15, 14, .98);
            box-shadow: none;
        }

        html.dark .rider-brand,
        html.dark .rider-sidebar-footer,
        html.dark .rider-topbar,
        html.dark .rider-mobile-bar {
            border-color: #30231F;
        }

        html.dark .rider-brand-copy strong,
        html.dark .rider-topbar-copy strong,
        html.dark .rider-mobile-brand strong {
            color: #F5EFE8;
        }

        html.dark .rider-brand-copy span,
        html.dark .rider-topbar-copy span,
        html.dark .rider-nav-label {
            color: #8F817A;
        }

        html.dark .rider-profile-card {
            border-color: #30231F;
            background:
                radial-gradient(circle at 92% 10%, rgba(168,69,56,.10), transparent 32%),
                #1B1513;
        }

        html.dark .rider-profile-copy strong {
            color: #F5EFE8;
        }

        html.dark .rider-nav-link,
        html.dark .rider-footer-btn {
            color: #B8AAA1;
        }

        html.dark .rider-footer-btn .rider-appearance-text small {
            color: currentColor;
            opacity: 0.5;
        }

        html.dark .rider-nav-link:hover,
        html.dark .rider-footer-btn:hover {
            border-color: #3B2E27;
            background: #211B17;
            color: #F3C6BA;
        }

        html.dark .rider-nav-link.is-active {
            background: #3A1111;
            color: #F3C6BA;
        }

        html.dark .rider-nav-link.is-active::before {
            background: #A84538;
        }

        html.dark .rider-theme-badge,
        html.dark .rider-mobile-menu-btn {
            border-color: #3B2E27;
            background: #211B17;
            color: #EBA99D;
        }

        html.dark .rider-topbar,
        html.dark .rider-mobile-bar {
            background: rgba(19, 15, 14, .88);
        }

        html.dark .rider-topbar-status {
            border-color: #28543D;
            background: #16291F;
            color: #79C79D;
        }
    </style>
</head>

<body class="rider-shell">
    <div class="rider-layout">

        <div
            id="riderSidebarOverlay"
            class="rider-sidebar-overlay"
        ></div>

        <aside
            id="riderSidebar"
            class="rider-sidebar"
        >
            <div class="rider-brand">
                <span class="rider-brand-mark">
                    L
                </span>

                <div class="rider-brand-copy">
                    <strong>LIKHAE</strong>
                    <span>Rider Panel</span>
                </div>
            </div>

            <section class="rider-profile-card">
                <div class="rider-profile-top">
                    <span class="rider-avatar">
                        JD
                    </span>

                    <div class="rider-profile-copy">
                        <strong>Juan Dela Cruz</strong>
                        <span>Verified Rider</span>
                    </div>
                </div>
            </section>

            <nav class="rider-nav">
                <span class="rider-nav-label">
                    Operations
                </span>

                <div class="rider-nav-list">
                    <a
                        href="{{ route('rider.dashboard') }}"
                        class="rider-nav-link {{ request()->routeIs('rider.dashboard') ? 'is-active' : '' }}"
                    >
                        <span class="rider-nav-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M3 12L12 3l9 9"></path>
                                <path d="M9 21V12h6v9"></path>
                                <path d="M5 10v11h14V10"></path>
                            </svg>
                        </span>

                        Dashboard
                    </a>

                    <a
                        href="{{ route('rider.pickups') }}"
                        class="rider-nav-link {{ request()->routeIs('rider.pickups', 'rider.pickups.*') ? 'is-active' : '' }}"
                    >
                        <span class="rider-nav-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M21 8 12 3 3 8l9 5 9-5Z"></path>
                                <path d="M3 8v8l9 5 9-5V8"></path>
                                <path d="M12 13v8"></path>
                            </svg>
                        </span>

                        Pickup Assignments
                    </a>

                    <a
                        href="{{ route('rider.deliveries') }}"
                        class="rider-nav-link {{ request()->routeIs('rider.deliveries', 'rider.deliveries.*') ? 'is-active' : '' }}"
                    >
                        <span class="rider-nav-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M3 6h11v11H3z"></path>
                                <path d="M14 10h4l3 3v4h-7z"></path>
                                <circle cx="7" cy="19" r="2"></circle>
                                <circle cx="18" cy="19" r="2"></circle>
                            </svg>
                        </span>

                        Delivery Assignments
                    </a>

                    <a
                        href="{{ route('rider.history') }}"
                        class="rider-nav-link {{ request()->routeIs('rider.history') ? 'is-active' : '' }}"
                    >
                        <span class="rider-nav-icon">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 7v5l3 3"></path>
                            </svg>
                        </span>

                        Delivery History
                    </a>

                    <a
                        href="{{ route('rider.earnings') }}"
                        class="rider-nav-link {{ request()->routeIs('rider.earnings') ? 'is-active' : '' }}"
                    >
                        <span class="rider-nav-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M6 3h8a4 4 0 0 1 0 8H6z"></path>
                                <path d="M6 11h8a4 4 0 0 1 0 8H6z"></path>
                                <path d="M4 7h4"></path>
                                <path d="M4 15h4"></path>
                                <path d="M6 3v18"></path>
                            </svg>
                        </span>

                        Earnings
                    </a>
                </div>

                <span class="rider-nav-label" style="margin-top:12px;">
                    Account
                </span>

                <div class="rider-nav-list">
                    <a
                        href="{{ route('rider.account') }}"
                        class="rider-nav-link {{ request()->routeIs('rider.account') ? 'is-active' : '' }}"
                    >
                        <span class="rider-nav-icon">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="8" r="4"></circle>
                                <path d="M4 21c0-5 3-8 8-8s8 3 8 8"></path>
                            </svg>
                        </span>

                        Profile
                    </a>
                </div>
            </nav>

            <div class="rider-sidebar-footer">
                <button
                    id="themeToggle"
                    type="button"
                    class="rider-footer-btn"
                >
                    <span class="rider-footer-left">
                        <svg viewBox="0 0 24 24">
                            <path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"></path>
                        </svg>

                        <span class="rider-appearance-text">
                            <strong>Appearance</strong>
                            <small>Light / Dark Mode</small>
                        </span>
                    </span>

                    <span
                        id="themeToggleBadge"
                        class="rider-theme-badge"
                    >
                        Light
                    </span>
                </button>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="rider-logout-form"
                >
                    @csrf

                    <button
                        type="submit"
                        class="rider-footer-btn rider-logout-btn"
                    >
                        <span class="rider-footer-left">
                            <svg viewBox="0 0 24 24">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <path d="m16 17 5-5-5-5"></path>
                                <path d="M21 12H9"></path>
                            </svg>

                            Logout
                        </span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="rider-main">

            <div class="rider-mobile-bar">
                <div class="rider-mobile-brand">
                    <span class="rider-brand-mark">
                        L
                    </span>

                    <strong>
                        LIKHAE Rider
                    </strong>
                </div>

                <button
                    id="riderMobileSidebarToggle"
                    type="button"
                    class="rider-mobile-menu-btn"
                    aria-label="Open rider navigation"
                >
                    <svg viewBox="0 0 24 24">
                        <path d="M4 7h16"></path>
                        <path d="M4 12h16"></path>
                        <path d="M4 17h16"></path>
                    </svg>
                </button>
            </div>

            <header class="rider-topbar">
                <div class="rider-topbar-copy">
                    <strong>
                        LIKHAE Rider
                    </strong>

                    <span>
                        Operations workspace
                    </span>
                </div>

                <span class="rider-topbar-status">
                    Rider Online
                </span>
            </header>

            <div class="rider-content">
                @yield('content')
            </div>

        </main>

    </div>

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {
                const root =
                    document.documentElement;

                const sidebar =
                    document.getElementById('riderSidebar');

                const sidebarToggle =
                    document.getElementById('riderMobileSidebarToggle');

                const sidebarOverlay =
                    document.getElementById('riderSidebarOverlay');

                const themeToggle =
                    document.getElementById('themeToggle');

                const themeBadge =
                    document.getElementById('themeToggleBadge');


                function openSidebar() {
                    sidebar?.classList.add('is-open');
                    sidebarOverlay?.classList.add('is-visible');
                    document.body.style.overflow = 'hidden';
                }


                function closeSidebar() {
                    sidebar?.classList.remove('is-open');
                    sidebarOverlay?.classList.remove('is-visible');
                    document.body.style.overflow = '';
                }


                function syncTheme() {
                    const isDark =
                        root.classList.contains('dark');

                    if (themeBadge) {
                        themeBadge.textContent =
                            isDark ? 'Dark' : 'Light';
                    }
                }


                sidebarToggle?.addEventListener(
                    'click',
                    openSidebar
                );


                sidebarOverlay?.addEventListener(
                    'click',
                    closeSidebar
                );


                sidebar
                    ?.querySelectorAll('a')
                    .forEach(function (link) {
                        link.addEventListener(
                            'click',
                            closeSidebar
                        );
                    });


                document.addEventListener(
                    'keydown',
                    function (event) {
                        if (event.key === 'Escape') {
                            closeSidebar();
                        }
                    }
                );


                themeToggle?.addEventListener(
                    'click',
                    function () {
                        const nextDark =
                            !root.classList.contains('dark');

                        root.classList.toggle(
                            'dark',
                            nextDark
                        );

                        localStorage.setItem(
                            'likhae-theme',
                            nextDark ? 'dark' : 'light'
                        );

                        syncTheme();
                    }
                );


                syncTheme();
            }
        );
    </script>

    @stack('scripts')
</body>

</html>
