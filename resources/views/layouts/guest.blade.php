@php $pageTitle = trim($__env->yieldContent('title')) ?: 'Marketplace'; @endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} Â· LIKHAE Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Buyer/buyer.css', 'resources/js/Buyer/buyer.js'])
    @stack('head')
    <script>
        (function () { var theme = localStorage.getItem('likhae-theme'); if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark'); })();
    </script>
</head>
<body class="lk-buyer-body lk-guest-body">
    <div class="lk-guest-shell">
        <header class="lk-header is-guest">
            <a href="{{ route('home') }}" class="lk-guest-brand" aria-label="LIKHAE Marketplace home">
                <x-likhae-logo context="Marketplace" class="likhae-logo--guest" />
            </a>
            <form action="{{ route('products') }}" method="GET" class="lk-search" role="search">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products, categories, local sellers..." aria-label="Search marketplace">
                <button type="submit" aria-label="Search"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
            </form>
            <nav class="lk-guest-actions" aria-label="Guest actions">
                <button type="button" class="lk-icon-btn lk-guest-theme-toggle" id="themeToggle" aria-label="Toggle dark mode" title="Toggle dark mode">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"/></svg>
                </button>
                <a class="lk-btn lk-btn-light" href="{{ Route::has('login') ? route('login') : url('/login') }}">Sign In</a>
                <a class="lk-btn lk-btn-red" href="{{ Route::has('register') ? route('register') : url('/register') }}">Create Account</a>
            </nav>
        </header>
        <main class="lk-main lk-guest-main" id="main-content">@yield('content')</main>
        <x-buyer.footer :guest="true" />
    </div>
    <dialog class="lk-auth-dialog" data-auth-dialog>
        <form method="dialog" class="lk-auth-dialog-card">
            <button class="lk-auth-dialog-close" value="cancel" aria-label="Close">Ã—</button>
            <span class="lk-auth-dialog-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg></span>
            <h2>Sign in to continue</h2>
            <p data-auth-message>Create a LIKHAE Buyer account or sign in to use this marketplace action.</p>
            <div class="lk-auth-dialog-actions">
                <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="lk-btn lk-btn-light">Sign In</a>
                <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="lk-btn lk-btn-red">Create Account</a>
            </div>
        </form>
    </dialog>
    <div id="lkBuyerToast" class="lk-toast" aria-live="polite" aria-atomic="true"></div>
    @include('partials.darkmode')
    @stack('scripts')
</body>
</html>
