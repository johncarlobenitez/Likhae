@php
    $pageTitle = trim($__env->yieldContent('title')) ?: 'Seller Center';
    $activePage = trim($__env->yieldContent('active')) ?: 'dashboard';
    $pageSubtitle = trim($__env->yieldContent('subtitle')) ?: 'Manage your store operations and performance.';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} · LIKHAE Seller Center</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/seller/seller.css', 'resources/js/seller/seller.js'])
    <script>
        (function(){var t=localStorage.getItem('likhae-theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}})();
    </script></head>
<body class="sl-body">
    @include('components.seller.sidebar', ['active' => $activePage])

    <div class="sl-shell">
        @include('components.seller.header', [
            'title' => $pageTitle,
            'subtitle' => $pageSubtitle,
        ])

        <main class="sl-main" id="mainContent">
            @yield('content')
        </main>

        @include('components.seller.footer')
    </div>

    <div class="sl-overlay" data-sl-overlay></div>
    <div class="sl-toast" data-sl-toast role="status" aria-live="polite"></div>

    @include('partials.darkmode')
    @stack('scripts')
</body>
</html>


