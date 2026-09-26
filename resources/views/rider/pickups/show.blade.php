@extends('Rider.app')

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
                @if($pickup['status'] === 'PICKUP_ASSIGNED')
                    @if($verified)
                        <div class="border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800">Parcel Verified: {{ $delivery->tracking_code }}</div>
                        <form method="POST" action="{{ route('rider.shipments.transition', $delivery) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="picked_up"><button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Confirm Picked Up</button></form>
                    @else
                        <p class="text-sm text-muted">Scan and verify the seller's waybill below before confirming collection.</p>
                    @endif
                @endif
                @if($pickup['status'] === 'PICKED_UP')
                    <form method="POST" action="{{ route('rider.shipments.transition', $delivery) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="in_transit"><button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Mark In Transit</button></form>
                @endif
                <a href="{{ route('rider.pickups') }}" class="rounded-xl border border-line px-5 py-3 text-center text-sm font-semibold text-ink">Back To Pickups</a>
            </div>
        </article>
    </section>

    @if($pickup['status'] === 'PICKUP_ASSIGNED')
        <x-parcel-scanner :action="route('rider.pickups.show', $delivery)" :tracking="request('tracking', '')" title="Scan Parcel At Seller" description="The tracking code must match this pickup assignment. Scanning alone does not mark the parcel picked up." button="Verify Parcel" />
        @if(request()->filled('tracking') && !$verified)<div class="border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">The scanned tracking number does not match this pickup assignment.</div>@endif
    @endif
</div>
@endsection
