@php
    $pageTitle = trim($__env->yieldContent('title')) ?: 'Marketplace';
    $isLandingPage = trim($__env->yieldContent('guestPageMode')) === 'landing';
    $homeUrl = route('home');
    $productsUrl = route('products');
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} &middot; LIKHAE Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Buyer/buyer.css', 'resources/js/Buyer/buyer.js'])
    @stack('head')
    <script>
        (function () { var theme = localStorage.getItem('likhae-theme'); if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark'); })();
    </script>
</head>
<body class="lk-buyer-body lk-guest-body {{ $isLandingPage ? 'lk-landing-body' : 'lk-guest-marketplace-body' }}">
    <div class="lk-guest-shell">
        <header class="lk-header is-guest">
            <a href="{{ $homeUrl }}" class="lk-guest-brand" aria-label="LIKHAE Marketplace home">
                <x-likhae-logo context="Marketplace" class="likhae-logo--guest" />
            </a>

            <nav class="lk-guest-nav" aria-label="{{ $isLandingPage ? 'Landing page navigation' : 'Guest marketplace navigation' }}">
                @if($isLandingPage)
                    <a href="#home">Home</a>
                    <a href="#about">About</a>
                    <a href="#process">Process</a>
                @else
                    <a href="{{ $homeUrl }}">Home</a>
                    <a href="{{ $homeUrl }}#categories-heading">Categories</a>
                    <a href="{{ $productsUrl }}">Products</a>
                @endif
            </nav>

            @unless($isLandingPage)
                <form action="{{ $productsUrl }}" method="GET" class="lk-search" role="search">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search for products, brands, or more..." aria-label="Search marketplace">
                    <button type="submit" aria-label="Search"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
                </form>
            @endunless

            <nav class="lk-guest-actions" aria-label="Guest actions">
                @unless($isLandingPage)
                    <button type="button" class="lk-icon-btn lk-guest-theme-toggle" id="themeToggle" aria-label="Toggle dark mode" title="Toggle dark mode">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"/></svg>
                    </button>
                @endunless
                <a class="lk-btn lk-btn-light lk-guest-signin" href="{{ Route::has('login') ? route('login') : url('/login') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Login</span>
                </a>
                @unless($isLandingPage)
                    <a class="lk-btn lk-btn-red lk-guest-register" href="{{ Route::has('register') ? route('register') : url('/register') }}">
                        <span>Register</span>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    </a>
                @endunless
            </nav>
        </header>

        <main class="lk-main lk-guest-main {{ $isLandingPage ? 'lk-landing-main' : '' }}" id="main-content">@yield('content')</main>
        <x-buyer.footer :guest="true" :landing="$isLandingPage" />
    </div>

    <dialog class="lk-auth-dialog" data-auth-dialog>
        <form method="dialog" class="lk-auth-dialog-card">
            <button class="lk-auth-dialog-close" value="cancel" aria-label="Close">&times;</button>
            <span class="lk-auth-dialog-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg></span>
            <h2>Sign in to continue</h2>
            <p data-auth-message>Create a LIKHAE Buyer account or sign in to use this marketplace action.</p>
            <div class="lk-auth-dialog-actions">
                <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="lk-btn lk-btn-light">Login</a>
                <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="lk-btn lk-btn-red">Register</a>
            </div>
        </form>
    </dialog>
    <div id="lkBuyerToast" class="lk-toast" aria-live="polite" aria-atomic="true"></div>
    @include('partials.darkmode')
    @stack('scripts')
</body>
</html>
