@extends('logistics.app')

@section('title', 'Riders - LIKHAE Logistics')

@php
    $stats = $logisticsRiderStats ?? [
        ['label' => 'Active Riders', 'value' => 0, 'tone' => 'success'],
        ['label' => 'Delivering', 'value' => 0, 'tone' => 'info'],
        ['label' => 'Inactive', 'value' => 0, 'tone' => 'warning'],
    ];
    $riders = $logisticsRiders ?? [];
@endphp

@section('content')
<div class="flex w-full flex-col gap-6">
    <section class="flex flex-col gap-5 rounded-xl border border-line bg-surface p-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Rider Management</span>
            <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink sm:text-[38px]">Riders</h1>
            <p class="mt-2 max-w-[650px] text-[11px] leading-6 text-muted">
                Active rider accounts and workload are loaded from real users and delivery assignments.
            </p>
        </div>

        <a href="{{ route('logistics.riders.applications') }}" class="inline-flex h-10 items-center justify-center rounded-lg bg-primary px-4 text-[10px] font-semibold text-white">
            Rider Applications
        </a>
    </section>

    <section class="grid gap-3 sm:grid-cols-3">
        @foreach($stats as $stat)
            <article class="rounded-xl border border-line bg-surface p-4">
                <span class="text-[9px] text-muted">{{ $stat['label'] }}</span>
                <strong class="mt-2 block text-[24px] font-bold text-ink">{{ $stat['value'] }}</strong>
            </article>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-xl border border-line bg-surface">
        <div class="border-b border-line px-5 py-4">
            <h2 class="text-[13px] font-semibold text-ink">Rider Accounts</h2>
        </div>

        @forelse($riders as $rider)
            <article class="flex flex-col gap-4 border-b border-line p-5 last:border-b-0 md:flex-row md:items-center md:justify-between">
                <div>
                    <strong class="block text-[11px] font-semibold text-ink">{{ $rider['name'] }}</strong>
                    <span class="mt-1 block text-[8px] text-muted">{{ $rider['code'] }} · {{ $rider['area'] }}</span>
                    <span class="mt-1 block text-[8px] text-muted">{{ $rider['parcels'] }} active deliveries</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="rounded-full bg-primary-soft px-3 py-1 text-[8px] font-semibold text-primary">{{ $rider['status'] }}</span>
                    <a href="{{ route('logistics.riders.show', $rider['id']) }}" class="rounded-lg border border-line px-3 py-2 text-[9px] font-semibold text-ink">View</a>
                </div>
            </article>
        @empty
            <div class="p-5 text-[10px] text-muted">No rider accounts are available.</div>
        @endforelse
    </section>
</div>
@endsection
