@props(['title'=>'LIKHAE','showHeader'=>true,'showFooter'=>true])
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title }} | LIKHAE</title>
@vite(['resources/css/Buyer/apps.css','resources/js/buyer/app.js'])
</head>
<body>
@if($showHeader)<x-buyer.header />@endif
<main>{{ $slot }}</main>
@if($showFooter)<x-buyer.footer />@endif
<x-buyer.toast />
</body>
</html>
