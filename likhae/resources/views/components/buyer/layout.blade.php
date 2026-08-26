@props(['title' => 'LIKHAE', 'buyer' => false, 'hideNav' => false])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — LIKHAE</title>
    @vite(['resources/css/Guest/home.css', 'resources/js/app.js'])
</head>
<body class="lk-body" data-user-role="{{ $buyer ? 'buyer' : 'guest' }}">
@if(!$hideNav)
    <x-buyer.header :buyer="$buyer" />
@endif
<main id="main-content">{{ $slot }}</main>
@if(!$hideNav)
    <x-buyer.footer />
@endif
<x-buyer.auth-gate />
<x-buyer.toast />
</body>
</html>

