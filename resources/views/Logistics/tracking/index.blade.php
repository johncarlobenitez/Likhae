@extends('Logistics.app')

@section('title', 'Parcel Tracking - LIKHAE Logistics')

@section('content')
<div class="flex flex-col gap-8">
    <section class="rounded-3xl border border-line bg-surface p-8">
        <span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Parcel Tracking</span>
        <h1 class="mt-3 text-2xl font-bold text-ink">Track Parcel</h1>
        <form method="GET" action="{{ route('logistics.parcels.tracking') }}" class="mt-6 flex flex-col gap-3 sm:flex-row">
            <input name="tracking" value="{{ $tracking }}" class="min-h-12 flex-1 rounded-xl border border-line bg-white px-4 text-sm" placeholder="Tracking number or order number">
            <button class="rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white">Search</button>
        </form>
    </section>

    @if($tracking !== '' && ! $delivery)
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">No parcel found for {{ $tracking }}.</div>
    @endif

    @if($delivery)
        <section class="rounded-3xl border border-line bg-surface p-8">
            <h2 class="text-lg font-bold text-ink">{{ $parcel['tracking'] }}</h2>
            <p class="mt-2 text-sm text-muted">Order: {{ $parcel['order'] }} - Buyer: {{ $parcel['buyer'] }}</p>
            <p class="mt-2 whitespace-pre-line text-sm text-muted">{{ $parcel['destination'] }}</p>
            <span class="mt-5 inline-flex rounded-full bg-primary-soft px-4 py-2 text-xs font-semibold text-primary">{{ $parcel['status'] }}</span>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('logistics.parcels.show', $delivery) }}" class="rounded-xl border border-line px-5 py-3 text-sm font-semibold text-ink">View Parcel</a>
                <a href="{{ route('logistics.waybills.show', $parcel['tracking']) }}" class="rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Waybill</a>
            </div>
        </section>
    @endif
</div>
@endsection
