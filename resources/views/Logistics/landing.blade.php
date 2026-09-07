<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logistics Portal | LIKHAE</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-stone-50 text-stone-900">
    <main class="mx-auto flex min-h-screen max-w-6xl flex-col px-5 py-6 sm:px-8">
        <header class="flex items-center justify-between gap-4"><a href="{{ route('home') }}" class="text-lg font-extrabold text-red-900">LIKHAE</a><a href="{{ route('home') }}" class="text-sm font-semibold text-stone-600 hover:text-red-900">Return to Marketplace</a></header>
        <section class="my-auto py-14"><span class="text-xs font-bold uppercase tracking-widest text-red-800">LIKHAE Operations</span><h1 class="mt-3 max-w-3xl text-4xl font-extrabold leading-tight sm:text-5xl">Logistics and Rider Portal</h1><p class="mt-5 max-w-2xl text-base leading-7 text-stone-600">Manage parcel intake, sorting, rider assignments, pickups, and deliveries from the dedicated operations workspace.</p><div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('logistics.login') }}" class="rounded-xl bg-red-900 px-5 py-3 text-sm font-bold text-white hover:bg-red-800">Sign In to Logistics Portal</a><a href="{{ route('register.role', ['role' => 'logistics']) }}" class="rounded-xl border border-stone-300 bg-white px-5 py-3 text-sm font-bold text-stone-700 hover:border-red-800 hover:text-red-900">Register</a></div></section>
        <section class="grid gap-4 pb-8 md:grid-cols-2"><article class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm"><h2 class="text-lg font-bold">Logistics Center</h2><p class="mt-3 text-sm leading-6 text-stone-600">Receive and sort parcels, manage riders, assign pickup and delivery work, and monitor operations.</p></article><article class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm"><h2 class="text-lg font-bold">Rider / Courier</h2><p class="mt-3 text-sm leading-6 text-stone-600">Accept assigned pickups and deliveries, update parcel progress, and view your own history and earnings.</p></article></section>
    </main>
</body>
</html>
