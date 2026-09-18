@extends('logistics.app')

@section('title', 'Delivery Areas - LIKHAE Logistics')

@section('content')
<div class="flex flex-col gap-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Logistics Management</span>
            <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink">Delivery Areas</h1>
            <p class="mt-2 max-w-[680px] text-[11px] leading-6 text-muted">
                Coverage is generated from actual buyer delivery addresses and active rider municipalities.
            </p>
        </div>
        <a href="{{ route('logistics.riders') }}" class="inline-flex h-10 items-center justify-center rounded-lg bg-primary px-4 text-[11px] font-semibold text-white">Manage Riders</a>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['label' => 'Total Areas', 'value' => $areaStats['total']],
            ['label' => 'Active Areas', 'value' => $areaStats['active']],
            ['label' => 'Active Riders', 'value' => $areaStats['riders']],
            ['label' => 'Needs Address Review', 'value' => $areaStats['unassigned']],
        ] as $stat)
            <article class="rounded-xl border border-line bg-surface p-4">
                <span class="text-[9px] font-medium text-muted">{{ $stat['label'] }}</span>
                <strong class="mt-2 block text-[24px] font-bold tracking-[-0.04em] text-ink">{{ $stat['value'] }}</strong>
            </article>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-xl border border-line bg-surface">
        <div class="border-b border-line px-5 py-4">
            <h2 class="text-[13px] font-semibold text-ink">Coverage Areas</h2>
            <p class="mt-1 text-[9px] text-muted">Only areas found in real parcel and rider records are shown.</p>
        </div>

        @forelse($areas as $area)
            <article class="flex flex-col gap-4 border-b border-line p-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-[12px] font-semibold text-ink">{{ $area['municipality'] }}, {{ $area['province'] }}</h3>
                    <p class="mt-1 text-[9px] text-muted">Barangay: {{ $area['barangay'] }}</p>
                    <p class="mt-1 text-[9px] text-muted">{{ $area['parcels'] }} parcel(s) routed here</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-primary-soft px-3 py-1 text-[8px] font-semibold text-primary">{{ $area['riders'] }} active rider(s)</span>
                    <span class="rounded-full bg-page-secondary px-3 py-1 text-[8px] font-semibold text-ink">{{ $area['status'] }}</span>
                </div>
            </article>
        @empty
            <div class="p-6 text-[10px] text-muted">No delivery areas can be built yet because there are no parcel records.</div>
        @endforelse
    </section>
</div>
@endsection
