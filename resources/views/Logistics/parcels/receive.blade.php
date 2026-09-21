@extends('logistics.app')

@section('title', 'Receive Parcel - LIKHAE Logistics')

@php
    $parcel = $logisticsReceiveParcel ?? null;
    $items = collect(data_get($parcel, 'items', []));
    $total = $items->sum(fn ($item) => (float) data_get($item, 'price', 0) * (int) data_get($item, 'quantity', 1));
@endphp

@section('content')
<div class="flex w-full flex-col gap-6">
    <nav class="flex flex-wrap items-center gap-2 text-[10px] text-muted">
        <a href="{{ route('logistics.dashboard') }}" class="transition hover:text-primary">Dashboard</a>
        <span>/</span>
        <a href="{{ route('logistics.parcels') }}" class="transition hover:text-primary">Parcels</a>
        <span>/</span>
        <span class="font-semibold text-ink">Receive Parcel</span>
    </nav>

    <section class="rounded-xl border border-line bg-surface p-5">
        <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Parcel Intake</span>
        <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink sm:text-[38px]">Receive a parcel</h1>
        <p class="mt-2 max-w-[680px] text-[11px] leading-6 text-muted">
            Enter a real LIKHAE tracking number to load its delivery record before confirming arrival at the sorting center.
        </p>

    </section>

    <x-parcel-scanner :action="route('logistics.parcels.receive')" :tracking="$tracking" title="Scan Parcel For Receiving" button="Find Parcel" />

    @if($tracking && !$parcel)
        <section class="rounded-xl border border-warning/30 bg-warning-soft p-5">
            <h2 class="text-[13px] font-semibold text-warning">No parcel found</h2>
            <p class="mt-2 text-[10px] leading-6 text-warning/80">
                Tracking number {{ $tracking }} does not match an existing delivery record.
            </p>
        </section>
    @endif

    @if($parcel)
        <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_340px]">
            <div class="flex flex-col gap-5">
                <section class="overflow-hidden rounded-xl border border-line bg-surface">
                    <div class="border-b border-line px-5 py-4">
                        <h2 class="text-[13px] font-semibold text-ink">Parcel Information</h2>
                    </div>
                    <div class="grid gap-px bg-line sm:grid-cols-2 lg:grid-cols-3">
                        @foreach([
                            'Tracking Number' => $parcel['tracking'],
                            'Order Number' => $parcel['order'],
                            'Payment' => $parcel['payment'],
                            'Seller' => $parcel['seller'],
                            'Buyer' => $parcel['buyer'],
                            'Assigned Rider' => $delivery->rider?->user?->name ?: 'Unassigned',
                            'Current Status' => str($delivery->status)->headline(),
                            'Courier' => $delivery->provider?->name ?: 'Not recorded',
                            'Order Value' => 'PHP '.number_format($total ?: (float) data_get($parcel, 'value', 0), 2),
                        ] as $label => $value)
                            <div class="bg-surface p-4">
                                <span class="text-[8px] font-semibold uppercase tracking-[0.08em] text-muted">{{ $label }}</span>
                                <strong class="mt-2 block break-words text-[10px] font-semibold text-ink">{{ $value ?: 'Not recorded' }}</strong>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-line p-5">
                        <span class="text-[8px] font-semibold uppercase text-muted">Delivery Address</span>
                        <strong class="mt-2 block text-[10px] text-ink">{{ $parcel['destination'] }}</strong>
                        <p class="mt-1 text-[9px] leading-5 text-muted">{{ $parcel['address'] }}</p>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-line bg-surface">
                    <div class="border-b border-line px-5 py-4">
                        <h2 class="text-[13px] font-semibold text-ink">Order Items</h2>
                    </div>
                    @forelse($items as $item)
                        <article class="flex items-center justify-between gap-4 border-b border-line p-5 last:border-b-0">
                            <div>
                                <strong class="block text-[10px] font-semibold text-ink">{{ data_get($item, 'name', 'Order item') }}</strong>
                                <span class="mt-1 block text-[8px] text-muted">{{ data_get($item, 'variation', 'No variation') }}</span>
                            </div>
                            <div class="text-right">
                                <strong class="block text-[10px] font-semibold text-ink">PHP {{ number_format((float) data_get($item, 'price', 0), 2) }}</strong>
                                <span class="mt-1 block text-[8px] text-muted">Qty {{ data_get($item, 'quantity', 1) }}</span>
                            </div>
                        </article>
                    @empty
                        <div class="p-5 text-[10px] text-muted">No order items are attached to this delivery record.</div>
                    @endforelse
                </section>
            </div>

            <aside class="rounded-xl border border-line bg-surface p-5">
                <h2 class="text-[13px] font-semibold text-ink">Confirm Receiving</h2>
                <p class="mt-2 text-[10px] leading-6 text-muted">
                    This verifies the parcel record. Pickup status is changed only when the assigned rider confirms collection.
                </p>

                <form method="POST" action="{{ route('logistics.parcels.receive.confirm', $delivery) }}" class="mt-5">
                    @csrf
                    <input type="hidden" name="tracking" value="{{ $delivery->tracking_code }}">
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-primary px-4 text-[11px] font-semibold text-white">
                        Verify Parcel
                    </button>
                </form>
            </aside>
        </section>
    @endif
</div>
@endsection
