@extends('logistics.app')

@section('title', 'Rider Assignment — LIKHAE Logistics')

@section('content')

@php

    $stats = [
        [
            'label' => 'Waiting Assignment',
            'value' => 18,
            'class' => 'bg-primary-soft text-primary',
        ],
        [
            'label' => 'Available Riders',
            'value' => 12,
            'class' => 'bg-info-soft text-info',
        ],
        [
            'label' => 'Out for Delivery',
            'value' => 24,
            'class' => 'bg-warning-soft text-warning',
        ],
        [
            'label' => 'Completed Today',
            'value' => 57,
            'class' => 'bg-success-soft text-success',
        ],
    ];

    $parcels = [
        [
            'tracking' => 'LH-2026-1002',
            'buyer' => 'Maria Santos',
            'area' => 'Area B',
            'destination' => 'Pagsanjan, Laguna',
        ],
        [
            'tracking' => 'LH-2026-1010',
            'buyer' => 'Carlo Mendoza',
            'area' => 'Area D',
            'destination' => 'Calamba, Laguna',
        ],
        [
            'tracking' => 'LH-2026-1012',
            'buyer' => 'Ana Reyes',
            'area' => 'Area C',
            'destination' => 'Los Baños, Laguna',
        ],
        [
            'tracking' => 'LH-2026-1024',
            'buyer' => 'Rene Dizon',
            'area' => 'Area A',
            'destination' => 'San Pablo, Laguna',
        ],
    ];

    $riders = [
        [
            'name' => 'Rider 01',
            'area' => 'Area A',
            'active' => 3,
        ],
        [
            'name' => 'Rider 02',
            'area' => 'Area B',
            'active' => 2,
        ],
        [
            'name' => 'Rider 03',
            'area' => 'Area C',
            'active' => 4,
        ],
        [
            'name' => 'Rider 04',
            'area' => 'Area D',
            'active' => 1,
        ],
    ];

@endphp

<div class="flex w-full flex-col gap-6">

    <nav class="flex flex-wrap items-center gap-2 text-[10px] text-muted">
        <a href="{{ route('logistics.dashboard') }}" class="transition hover:text-primary">
            Dashboard
        </a>

        <span>/</span>

        <span class="font-semibold text-ink">
            Rider Assignment
        </span>
    </nav>

    <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">
                Logistics
            </span>

            <h1 class="mt-2 font-display text-[30px] font-semibold tracking-[-0.04em] text-ink sm:text-[34px]">
                Rider Assignment
            </h1>

            <p class="mt-2 max-w-[650px] text-[11px] leading-6 text-muted">
                Assign sorted parcels to available riders and balance delivery loads by area.
            </p>
        </div>

        <button type="button" class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-lg bg-primary px-4 text-[11px] font-semibold text-white shadow-sm transition hover:bg-primary-hover">
            <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.8]">
                <path d="M12 5v14"></path>
                <path d="M5 12h14"></path>
            </svg>
            Assign Rider
        </button>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($stats as $stat)
            <article class="rounded-xl border border-line bg-surface p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="text-[9px] font-medium text-muted">
                            {{ $stat['label'] }}
                        </span>

                        <strong class="mt-2 block text-[24px] font-bold tracking-[-0.04em] text-ink">
                            {{ $stat['value'] }}
                        </strong>
                    </div>

                    <span class="mt-1 h-2 w-2 rounded-full {{ $stat['class'] }}"></span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section class="overflow-hidden rounded-xl border border-line bg-surface">
            <div class="border-b border-line px-5 py-4">
                <h2 class="text-[13px] font-semibold text-ink">
                    Parcels Waiting for Rider
                </h2>

                <p class="mt-1 text-[9px] text-muted">
                    Sorted parcels ready for assignment.
                </p>
            </div>

            @foreach($parcels as $parcel)
                <article class="flex flex-col gap-4 border-b border-line p-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <strong class="block text-[11px] font-semibold text-ink">
                            {{ $parcel['tracking'] }}
                        </strong>

                        <p class="mt-1 text-[9px] text-muted">
                            {{ $parcel['buyer'] }}
                        </p>

                        <p class="mt-1 text-[9px] text-muted">
                            {{ $parcel['destination'] }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-primary-soft px-3 py-1 text-[8px] font-semibold text-primary">
                            {{ $parcel['area'] }}
                        </span>

                        <button type="button" class="rounded-lg bg-primary px-4 py-2 text-[8px] font-semibold text-white transition hover:bg-primary-hover">
                            Assign
                        </button>
                    </div>
                </article>
            @endforeach
        </section>

        <aside class="rounded-xl border border-line bg-surface p-5">
            <h2 class="text-[13px] font-semibold text-ink">
                Available Riders
            </h2>

            <p class="mt-1 text-[9px] text-muted">
                Choose rider based on delivery area.
            </p>

            <div class="mt-5 flex flex-col gap-3">
                @foreach($riders as $rider)
                    <button type="button" class="flex items-center justify-between rounded-xl border border-line bg-page-secondary p-4 text-left transition hover:border-primary">
                        <div>
                            <strong class="block text-[10px] font-semibold text-ink">
                                {{ $rider['name'] }}
                            </strong>

                            <span class="mt-1 block text-[8px] text-muted">
                                {{ $rider['area'] }}
                            </span>
                        </div>

                        <span class="text-[8px] font-semibold text-primary">
                            {{ $rider['active'] }} active
                        </span>
                    </button>
                @endforeach
            </div>
        </aside>
    </section>

</div>

@endsection
