@php
    $pageTitle = trim($__env->yieldContent('title')) ?: 'Buyer';
    $activePage = trim($__env->yieldContent('active')) ?: 'home';
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} Â· LIKHAE Buyer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Buyer/buyer.css', 'resources/js/buyer/buyer.js'])
    @stack('head')
</head>
<body class="lk-buyer-body">
    <div class="lk-buyer-app" data-lk-buyer-app>
        <x-buyer.sidebar :active="$activePage" />
        <button class="lk-overlay" type="button" data-lk-overlay aria-label="Close navigation"></button>
        <div class="lk-shell">
            <x-buyer.header :title="$pageTitle" />
            <main class="lk-main" id="main-content">@yield('content')</main>
            <x-buyer.footer />
        </div>
    </div>
    <div id="lkBuyerToast" class="lk-toast" aria-live="polite" aria-atomic="true"></div>
    @stack('scripts')
</body>
</html>

