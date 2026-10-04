@php
    $pageTitle = html_entity_decode(
        trim($__env->yieldContent('title')) ?: 'Buyer',
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );
    $activePage = trim($__env->yieldContent('active')) ?: 'home';
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} &middot; LIKHAE Buyer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Buyer/buyer.css', 'resources/js/Buyer/buyer.js'])
    @stack('head')
    <script>
        (function(){var t=localStorage.getItem('likhae-theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}})();
    </script>
</head>
<body class="lk-buyer-body">
    <div class="lk-buyer-app" data-lk-buyer-app>
        <x-admin.sidebar role="buyer" :active="$activePage" />
        <div class="lk-shell" data-sidebar-content>
            <x-buyer.header :title="$pageTitle" />
            <main class="lk-main" id="main-content">@yield('content')</main>
            <x-buyer.footer />
        </div>
    </div>
    <div id="lkBuyerToast" class="lk-toast" aria-live="polite" aria-atomic="true"></div>
    @include('partials.darkmode')
    @stack('scripts')
</body>
</html>
