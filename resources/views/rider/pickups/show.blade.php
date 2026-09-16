@extends('rider.app')

@section('title', 'Pickup Details - LIKHAE Rider')

@section('content')
<div class="flex flex-col gap-8">
    @if(session('status'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-700">{{ session('status') }}</div>
    @endif

    <section class="rounded-3xl border border-line bg-surface p-8">
        <span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Pickup Details</span>
        <h1 class="mt-3 text-2xl font-bold text-ink">{{ $pickup['tracking'] }}</h1>
        <p class="mt-3 text-sm text-muted">{{ $pickup['status_label'] }} - Seller: {{ $pickup['seller'] }}</p>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <article class="rounded-3xl border border-line bg-surface p-8">
            <h2 class="text-lg font-bold text-ink">Parcel Information</h2>
            <dl class="mt-5 grid gap-3 text-sm">
                <div><dt class="text-muted">Buyer</dt><dd class="font-semibold text-ink">{{ $pickup['buyer'] }}</dd></div>
                <div><dt class="text-muted">Delivery Address</dt><dd class="font-semibold text-ink whitespace-pre-line">{{ $pickup['address'] }}</dd></div>
                <div><dt class="text-muted">Items</dt><dd class="font-semibold text-ink">{{ $pickup['items'] }}</dd></div>
                <div><dt class="text-muted">Order Amount</dt><dd class="font-semibold text-ink">{{ $pickup['amount'] }}</dd></div>
            </dl>
        </article>

        <article class="rounded-3xl border border-line bg-surface p-8">
            <h2 class="text-lg font-bold text-ink">Pickup Actions</h2>
            <div class="mt-5 grid gap-3">
                @if($pickup['status'] === 'REQUESTED')
                    <form method="POST" action="{{ route('rider.pickups.accept', $pickup['id']) }}">@csrf<button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Accept Pickup</button></form>
                @endif
                @if($pickup['status'] === 'PICKUP_ACCEPTED')
                    <form method="POST" action="{{ route('rider.pickups.confirm', $pickup['id']) }}">@csrf<button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Scan / Confirm Parcel Pickup</button></form>
                @endif
                @if($pickup['status'] === 'PICKED_UP')
                    <form method="POST" action="{{ route('rider.pickups.sorting-center', $pickup['id']) }}">@csrf<button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Deliver To Sorting Center</button></form>
                @endif
                <a href="{{ route('rider.pickups') }}" class="rounded-xl border border-line px-5 py-3 text-center text-sm font-semibold text-ink">Back To Pickups</a>
            </div>
        </article>
    </section>
</div>
@endsection
