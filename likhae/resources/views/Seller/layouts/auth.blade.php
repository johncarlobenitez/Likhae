<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LIKHAE Seller')</title>
    @vite([
        'resources/css/seller/app.css',
        'resources/css/seller/auth.css'
    ])
</head>
<body class="auth-body">
    <header class="auth-header">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6">
            <a class="inline-flex items-center gap-2" href="{{ url('/') }}">
                <span aria-hidden="true"><svg viewBox="0 0 36 36" class="h-9 w-9"><path d="M8.5 11.5h19l-1.4 18H9.9L8.5 11.5Z" fill="#D92D2F"/><path d="M13 13V9.8C13 6.6 15.2 4.5 18 4.5s5 2.1 5 5.3V13" stroke="#171717" stroke-width="2.2" stroke-linecap="round"/><path d="M14.8 15.5v8.8h6.8" stroke="white" stroke-width="2.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <span><span class="block text-[18px] font-black tracking-[-.04em] text-[#171717]">LIKHAE</span><span class="block text-[8px] font-bold uppercase tracking-[.18em] text-[#96918B]">Seller Center</span></span>
            </a>
            <a href="{{ url('/') }}" class="text-xs font-bold text-[#6B6864] hover:text-[#D92D2F]">Back to LIKHAE</a>
        </div>
    </header>
    <main>@yield('content')</main>
</body>
</html>
