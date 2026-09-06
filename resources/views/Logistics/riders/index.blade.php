@extends('logistics.app')

@section('title', 'Rider Management — LIKHAE Logistics')

@section('content')

@php
    $stats = [
        ['label' => 'Total Riders', 'value' => 48, 'tone' => 'primary'],
        ['label' => 'Available', 'value' => 32, 'tone' => 'success'],
        ['label' => 'Delivering', 'value' => 12, 'tone' => 'info'],
        ['label' => 'Inactive', 'value' => 4, 'tone' => 'warning'],
    ];

    $riders = [
        ['id' => 1, 'code' => 'Rider 01', 'name' => 'Juan Dela Cruz', 'area' => 'Area A', 'status' => 'Available', 'parcels' => 3],
        ['id' => 2, 'code' => 'Rider 02', 'name' => 'Mark Santos', 'area' => 'Area B', 'status' => 'Delivering', 'parcels' => 5],
        ['id' => 3, 'code' => 'Rider 03', 'name' => 'Pedro Reyes', 'area' => 'Area C', 'status' => 'Available', 'parcels' => 2],
        ['id' => 4, 'code' => 'Rider 04', 'name' => 'Carlo Mendoza', 'area' => 'Area D', 'status' => 'Inactive', 'parcels' => 0],
    ];
@endphp

<div class="flex w-full flex-col gap-6">

    <nav class="flex flex-wrap items-center gap-2 text-[10px] text-muted">
        <a href="{{ route('logistics.dashboard') }}" class="transition hover:text-primary">
            Dashboard
        </a>

        <span>/</span>

        <span class="font-semibold text-ink">
            Riders
        </span>
    </nav>

    <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">
                Delivery Management
            </span>

            <h1 class="mt-2 font-display text-[28px] font-semibold tracking-[-0.04em] text-ink sm:text-[32px]">
                Rider Management
            </h1>

            <p class="mt-2 text-[10px] text-muted">
                Manage riders, availability, assigned parcels, and account status.
            </p>
        </div>

        <button type="button" class="inline-flex h-10 items-center justify-center rounded-lg bg-primary px-5 text-[9px] font-semibold text-white transition hover:bg-primary/90">
            + Add Rider
        </button>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($stats as $stat)
            <article class="rounded-xl border border-line bg-surface p-5">
                <span class="text-[9px] text-muted">{{ $stat['label'] }}</span>
                <strong class="mt-2 block text-[26px] font-bold text-ink">{{ $stat['value'] }}</strong>
            </article>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-xl border border-line bg-surface">
        <div class="border-b border-line px-5 py-4">
            <h2 class="text-[13px] font-semibold text-ink">Rider List</h2>
            <p class="mt-1 text-[9px] text-muted">View rider information and delivery workload.</p>
        </div>

        <div class="divide-y divide-line">
            @foreach($riders as $rider)
                <article class="flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="grid h-10 w-10 place-items-center rounded-full bg-primary-soft text-[10px] font-bold text-primary">
                            {{ substr($rider['code'], -2) }}
                        </div>

                        <div>
                            <strong class="block text-[11px] font-semibold text-ink">
                                {{ $rider['name'] }}
                            </strong>
                            <span class="mt-1 block text-[8px] text-muted">
                                {{ $rider['code'] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full bg-page-secondary px-3 py-1 text-[8px] font-semibold text-muted">
                            {{ $rider['area'] }}
                        </span>

                        <span class="rounded-full px-3 py-1 text-[8px] font-semibold
                            @if($rider['status'] === 'Available')
                                bg-success-soft text-success
                            @elseif($rider['status'] === 'Delivering')
                                bg-primary-soft text-primary
                            @else
                                bg-warning-soft text-warning
                            @endif
                        ">
                            {{ $rider['status'] }}
                        </span>

                        <span class="text-[9px] text-muted">
                            {{ $rider['parcels'] }} parcels
                        </span>

                        <a href="{{ route('logistics.riders.show', $rider['id']) }}" class="inline-flex items-center justify-center rounded-lg border border-line px-3 py-2 text-[8px] font-semibold text-ink transition hover:border-primary hover:text-primary">
                            View
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

</div>

@endsection
