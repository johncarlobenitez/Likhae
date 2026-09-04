@extends('layouts.buyer')

@section('title', 'Rewards & Vouchers')
@section('active', 'rewards')
@section('subtitle', 'Manage vouchers, points, and cashback in one place.')

@section('content')
@php
    $tab = in_array(request('tab'), ['vouchers','points','cashback'], true) ? request('tab') : 'vouchers';
    $tabs = ['vouchers' => 'My Vouchers', 'points' => 'Reward Points', 'cashback' => 'Cashback'];
@endphp

<div class="lk-page">
    <div class="lk-page-title"><div><span class="lk-kicker">Buyer Benefits</span><h1>Rewards & Vouchers</h1><p>Use earned benefits before they expire.</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.products') }}">Browse Products</a></div>

    <nav class="flex gap-1 overflow-x-auto rounded-xl border border-stone-200 bg-white p-1 shadow-sm" aria-label="Reward sections">
        @foreach($tabs as $key => $label)<a class="whitespace-nowrap rounded-lg px-4 py-2.5 text-xs font-semibold transition {{ $tab === $key ? 'bg-red-900 text-white' : 'text-stone-600 hover:bg-stone-50 hover:text-red-900' }}" href="{{ route('buyer.rewards', ['tab' => $key]) }}">{{ $label }}</a>@endforeach
    </nav>

    @if($tab === 'vouchers')
        <section class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach([
                ['LIKHAE100','₱100 OFF','Minimum spend ₱1,000','Sep 30, 2026','Available'],
                ['LOCAL15','15% OFF','Selected local sellers · cap ₱250','Sep 18, 2026','Available'],
                ['SHIPFREE','FREE SHIPPING','Minimum spend ₱499','Sep 12, 2026','Available'],
            ] as [$code,$value,$condition,$expires,$status])
                <article class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                    <div class="flex items-center gap-4 border-b border-dashed border-stone-200 p-4"><div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-red-50 text-sm font-black text-red-900">{{ str_contains($value, '%') ? '%' : '₱' }}</div><div><span class="text-[10px] font-bold uppercase tracking-widest text-red-800">{{ $status }}</span><h2 class="text-lg font-bold text-stone-900">{{ $value }}</h2><p class="text-xs text-stone-500">{{ $condition }}</p></div></div>
                    <div class="flex items-center justify-between gap-3 p-4"><div><code class="rounded bg-stone-100 px-2 py-1 text-xs font-bold text-stone-700">{{ $code }}</code><small class="mt-1 block text-[10px] text-stone-400">Expires {{ $expires }}</small></div><button type="button" class="lk-btn lk-btn-red" data-demo-action="Voucher {{ $code }} copied.">Use Now</button></div>
                </article>
            @endforeach
        </section>
        <section class="mt-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Voucher history</h2><div class="mt-3 overflow-x-auto"><table class="w-full min-w-[560px] text-left text-xs"><thead class="border-y border-stone-100 bg-stone-50 text-[10px] uppercase tracking-wide text-stone-500"><tr><th class="px-4 py-3">Voucher</th><th class="px-4 py-3">Benefit</th><th class="px-4 py-3">Order</th><th class="px-4 py-3">Status</th></tr></thead><tbody class="divide-y divide-stone-100"><tr><td class="px-4 py-3 font-semibold">WELCOME50</td><td class="px-4 py-3">₱50 discount</td><td class="px-4 py-3">#LK-2048</td><td class="px-4 py-3 text-stone-500">Used</td></tr><tr><td class="px-4 py-3 font-semibold">SHIPSEP</td><td class="px-4 py-3">Free shipping</td><td class="px-4 py-3">—</td><td class="px-4 py-3 text-stone-500">Expired</td></tr></tbody></table></div></section>
    @elseif($tab === 'points')
        <section class="mt-5 grid gap-4 lg:grid-cols-[340px_1fr]">
            <article class="rounded-2xl bg-red-950 p-6 text-white shadow-sm"><span class="text-xs text-red-200">Available balance</span><strong class="mt-2 block text-4xl">2,450</strong><span class="text-xs text-red-200">LIKHAE Reward Points</span><div class="mt-6 border-t border-red-800 pt-4 text-xs text-red-100">100 points = ₱1 checkout discount</div></article>
            <article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Earn more points</h2><div class="mt-4 grid gap-3 sm:grid-cols-3">@foreach([['Complete an order','+50'],['Review a product','+20'],['Shop local picks','2×']] as [$label,$value])<div class="rounded-xl border border-stone-200 p-4"><strong class="text-lg text-red-900">{{ $value }}</strong><p class="mt-1 text-xs text-stone-600">{{ $label }}</p></div>@endforeach</div></article>
        </section>
        <section class="mt-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Points activity</h2><div class="mt-3 divide-y divide-stone-100">@foreach([['Order #LK-2071 completed','+50 points','Sep 3, 2026'],['Product review submitted','+20 points','Aug 31, 2026'],['Checkout discount','−500 points','Aug 28, 2026']] as [$label,$amount,$date])<div class="flex items-center justify-between gap-4 py-3"><div><strong class="block text-xs text-stone-800">{{ $label }}</strong><span class="text-[10px] text-stone-400">{{ $date }}</span></div><span class="text-xs font-bold {{ str_starts_with($amount, '+') ? 'text-red-800' : 'text-stone-500' }}">{{ $amount }}</span></div>@endforeach</div></section>
    @else
        <section class="mt-5 grid gap-4 sm:grid-cols-3"><article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:col-span-1"><span class="text-xs text-stone-500">Available cashback</span><strong class="mt-2 block text-3xl text-red-900">₱368.00</strong><button type="button" class="lk-btn lk-btn-red mt-5" data-demo-action="Cashback will be applied at checkout.">Use at Checkout</button></article><article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:col-span-2"><h2 class="text-base font-bold text-stone-900">How cashback works</h2><p class="mt-2 text-sm leading-6 text-stone-600">Eligible purchases earn cashback after the order is completed. Use the balance on a future checkout before its expiration date.</p><div class="mt-4 rounded-xl bg-amber-50 p-4 text-xs text-amber-900">₱125 pending · available when current orders are completed.</div></article></section>
        <section class="mt-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Cashback activity</h2><div class="mt-3 divide-y divide-stone-100">@foreach([['Order #LK-2071','Earned','₱85.00','Sep 3, 2026'],['Order #LK-2064','Used','−₱120.00','Aug 28, 2026'],['Order #LK-2050','Earned','₱45.00','Aug 21, 2026']] as [$order,$type,$amount,$date])<div class="grid grid-cols-[1fr_auto] gap-4 py-3 text-xs sm:grid-cols-4"><strong>{{ $order }}</strong><span class="text-stone-500">{{ $type }}</span><span class="font-semibold text-stone-800">{{ $amount }}</span><time class="text-right text-stone-400">{{ $date }}</time></div>@endforeach</div></section>
    @endif
</div>
@endsection
