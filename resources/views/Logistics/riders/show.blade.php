@extends('logistics.app')

@section('title', 'Rider Profile - LIKHAE Logistics')

@php
    $data = $rider ?? [];
    $name = data_get($data, 'name', 'Rider Account');
    $initials = collect(explode(' ', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'R';

    $details = [
        ['Full Name', $name],
        ['Email', data_get($data, 'email', 'Not recorded')],
        ['Contact', data_get($data, 'contact', 'Not recorded')],
        ['Delivery Area', data_get($data, 'area', 'Unassigned')],
        ['Status', data_get($data, 'status', 'Not recorded')],
        ['Active Deliveries', data_get($data, 'parcels', 0)],
    ];
@endphp

@section('content')
<div class="flex w-full flex-col gap-6">
    <nav class="flex flex-wrap items-center gap-2 text-[10px] text-muted">
        <a href="{{ route('logistics.dashboard') }}" class="transition hover:text-primary">Dashboard</a>
        <span>/</span>
        <a href="{{ route('logistics.riders') }}" class="transition hover:text-primary">Riders</a>
        <span>/</span>
        <span class="font-semibold text-ink">{{ $name }}</span>
    </nav>

    <section class="rounded-xl border border-line bg-surface p-5">
        <div class="flex flex-col gap-5 md:flex-row md:items-center">
            <div class="grid h-20 w-20 place-items-center rounded-full bg-primary-soft text-primary">
                <span class="text-[20px] font-bold">{{ $initials }}</span>
            </div>

            <div>
                <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Rider Management</span>
                <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink">{{ $name }}</h1>
                <p class="mt-1 text-[10px] text-muted">Rider ID: RID-{{ str_pad((string) data_get($data, 'id', 0), 4, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-line bg-surface p-5">
        <h2 class="text-[13px] font-semibold text-ink">Rider Information</h2>
        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($details as $item)
                <div class="rounded-lg bg-page-secondary p-4">
                    <span class="text-[8px] uppercase text-muted">{{ $item[0] }}</span>
                    <strong class="mt-2 block break-words text-[10px] font-semibold text-ink">{{ $item[1] }}</strong>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-xl border border-line bg-surface p-5">
        <h2 class="text-[13px] font-semibold text-ink">Operational Note</h2>
        <p class="mt-2 text-[10px] leading-6 text-muted">
            This page intentionally displays only fields currently available from LIKHAE rider/user records. Uploaded document previews and vehicle verification can be added once those storage-backed fields exist.
        </p>
    </section>
</div>
@endsection
