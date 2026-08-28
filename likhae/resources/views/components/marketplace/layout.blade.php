@props([
    'title' => 'LIKHAE',
    'showHeader' => true,
    'showFooter' => true,
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} | LIKHAE</title>

    @vite([
        'resources/css/Guest/app.css',
        'resources/js/guest/app.js',
    ])
</head>
<body>
    @if($showHeader)
        <x-marketplace.header />
    @endif

    <main>
        {{ $slot }}
    </main>

    @if($showFooter)
        <x-marketplace.footer />
    @endif

    <x-marketplace.toast />
</body>
</html>
