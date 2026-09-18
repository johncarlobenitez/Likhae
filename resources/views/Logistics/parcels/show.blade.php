@extends('logistics.app')

@section('title', 'Parcel Details - LIKHAE Logistics')

@php
    $parcel = $logisticsParcel ?? null;
    $items = collect(data_get($parcel, 'items', []));
    $statusKey = data_get($parcel, 'status_key', 'unknown');
    $statusTone = match ($statusKey) {
        'waiting_sorting' => 'bg-warning-soft text-warning',
        'awaiting_rider', 'assigned' => 'bg-primary-soft text-primary',
        'out_for_delivery' => 'bg-info-soft text-info',
        'delivered' => 'bg-success-soft text-success',
        default => 'bg-page-secondary text-muted',
    };
@endphp

@section('content')
<div class="flex w-full flex-col gap-6">
    <nav class="flex flex-wrap items-center gap-2 text-[10px] text-muted">
        <a href="{{ route('logistics.dashboard') }}" class="transition hover:text-primary">Dashboard</a>
        <span>/</span>
        <a href="{{ route('logistics.parcels') }}" class="transition hover:text-primary">Parcels</a>
        <span>/</span>
        <span class="font-semibold text-ink">{{ data_get($parcel, 'tracking', 'Parcel Details') }}</span>
    </nav>

    @if(!$parcel)
        <section class="rounded-xl border border-line bg-surface p-8 text-center">
            <h1 class="font-display text-[30px] font-semibold tracking-[-0.04em] text-ink">Parcel not found</h1>
            <p class="mx-auto mt-2 max-w-[520px] text-[11px] leading-6 text-muted">
                No database parcel was loaded for this page. Go back to the parcel list and open an existing delivery record.
            </p>
            <a href="{{ route('logistics.parcels') }}" class="mt-5 inline-flex h-10 items-center justify-center rounded-lg bg-primary px-4 text-[11px] font-semibold text-white">
                Back to Parcels
            </a>
        </section>
    @else
        <section class="flex flex-col gap-5 rounded-xl border border-line bg-surface p-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Parcel Details</span>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <h1 class="font-display text-[32px] font-semibold tracking-[-0.04em] text-ink sm:text-[38px]">
                        {{ $parcel['tracking'] }}
                    </h1>
                    <span class="rounded-full px-3 py-1 text-[8px] font-semibold {{ $statusTone }}">
                        {{ $parcel['status'] }}
                    </span>
                </div>
                <p class="mt-2 text-[10px] text-muted">
                    Order {{ $parcel['order'] }} · Received {{ $parcel['received_at'] }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('logistics.parcels') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-line bg-surface px-4 text-[10px] font-semibold text-ink">
                    Back
                </a>
                <a href="{{ route('logistics.waybills.show', ['tracking' => $parcel['tracking']]) }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-line bg-surface px-4 text-[10px] font-semibold text-ink">
                    Waybill
                </a>
                <a href="{{ route('logistics.parcels.tracking', ['tracking' => $parcel['tracking']]) }}" class="inline-flex h-10 items-center justify-center rounded-lg bg-primary px-4 text-[10px] font-semibold text-white">
                    Track Parcel
                </a>
            </div>
        </section>

        <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
            <div class="flex flex-col gap-5">
                <section class="overflow-hidden rounded-xl border border-line bg-surface">
                    <div class="border-b border-line px-5 py-4">
                        <h2 class="text-[13px] font-semibold text-ink">Parcel Overview</h2>
                    </div>
                    <div class="grid gap-px bg-line sm:grid-cols-2 lg:grid-cols-3">
                        @foreach([
                            'Order Number' => $parcel['order'],
                            'Seller' => $parcel['seller'],
                            'Buyer' => $parcel['buyer'],
                            'Payment' => $parcel['payment'],
                            'Parcel Value' => 'PHP '.number_format((float) $parcel['value'], 2),
                            'Condition' => $parcel['condition'],
                        ] as $label => $value)
                            <div class="bg-surface p-4">
                                <span class="text-[8px] font-semibold uppercase tracking-[0.08em] text-muted">{{ $label }}</span>
                                <strong class="mt-2 block break-words text-[10px] font-semibold text-ink">{{ $value ?: 'Not recorded' }}</strong>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-xl border border-line bg-surface p-5">
                    <h2 class="text-[13px] font-semibold text-ink">Delivery Information</h2>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div class="rounded-lg bg-page-secondary p-4">
                            <span class="text-[8px] font-semibold uppercase text-muted">Destination</span>
                            <strong class="mt-2 block text-[10px] text-ink">{{ $parcel['destination'] }}</strong>
                            <p class="mt-1 text-[9px] leading-5 text-muted">{{ $parcel['address'] }}</p>
                        </div>
                        <div class="rounded-lg bg-page-secondary p-4">
                            <span class="text-[8px] font-semibold uppercase text-muted">Assigned Rider</span>
                            <strong class="mt-2 block text-[10px] text-ink">{{ $parcel['rider'] }}</strong>
                            <p class="mt-1 text-[9px] leading-5 text-muted">Delivery area: {{ $parcel['area'] }}</p>
                        </div>
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

            <aside class="flex flex-col gap-5">
                <section class="rounded-xl border border-line bg-surface p-5">
                    <h2 class="text-[13px] font-semibold text-ink">Current Status</h2>
                    <p class="mt-2 text-[11px] font-semibold text-primary">{{ $parcel['status'] }}</p>
                    <div class="mt-4 grid gap-3">
                        <div class="rounded-lg bg-page-secondary p-4">
                            <span class="text-[8px] uppercase text-muted">Customer Contact</span>
                            <strong class="mt-2 block text-[10px] text-ink">{{ $parcel['contact'] ?: 'Not recorded' }}</strong>
                        </div>
                        <div class="rounded-lg bg-page-secondary p-4">
                            <span class="text-[8px] uppercase text-muted">Received From</span>
                            <strong class="mt-2 block text-[10px] text-ink">{{ $parcel['received_from'] }}</strong>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-line bg-surface p-5">
                    <h2 class="text-[13px] font-semibold text-ink">Logistics Notes</h2>
                    <p class="mt-3 text-[10px] leading-6 text-muted">{{ $parcel['notes'] ?: 'No logistics notes recorded.' }}</p>
                </section>
            </aside>
        </section>
    @endif
</div>
@endsection
