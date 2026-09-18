@extends('logistics.app')

@section('title', 'Rider Assignment - LIKHAE Logistics')

@php
    $stats = $logisticsAssignmentStats ?? [
        ['label' => 'Waiting Assignment', 'value' => 0, 'class' => 'bg-primary-soft text-primary'],
        ['label' => 'Available Riders', 'value' => 0, 'class' => 'bg-info-soft text-info'],
        ['label' => 'Out for Delivery', 'value' => 0, 'class' => 'bg-warning-soft text-warning'],
        ['label' => 'Completed Today', 'value' => 0, 'class' => 'bg-success-soft text-success'],
    ];
    $parcels = $logisticsAssignmentParcels ?? [];
    $riders = $logisticsAssignmentRiders ?? [];
@endphp

@section('content')
<div class="flex w-full flex-col gap-6">
    @if(session('status'))<div class="border border-green-200 bg-green-50 p-4 text-[10px] font-semibold text-green-800">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="border border-red-200 bg-red-50 p-4 text-[10px] font-semibold text-red-700">{{ $errors->first() }}</div>@endif
    <nav class="flex flex-wrap items-center gap-2 text-[10px] text-muted">
        <a href="{{ route('logistics.dashboard') }}" class="transition hover:text-primary">Dashboard</a>
        <span>/</span>
        <span class="font-semibold text-ink">Rider Assignment</span>
    </nav>

    <section class="flex flex-col gap-5 rounded-xl border border-line bg-surface p-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Logistics</span>
            <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink sm:text-[38px]">Rider Assignment</h1>
            <p class="mt-2 max-w-[650px] text-[11px] leading-6 text-muted">
                Assign sorted parcels to available riders and keep delivery ownership in the database.
            </p>
        </div>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($stats as $stat)
            <article class="rounded-xl border border-line bg-surface p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="text-[9px] font-medium text-muted">{{ $stat['label'] }}</span>
                        <strong class="mt-2 block text-[24px] font-bold tracking-[-0.04em] text-ink">{{ $stat['value'] }}</strong>
                    </div>
                    <span class="mt-1 h-2 w-2 rounded-full {{ $stat['class'] }}"></span>
                </div>
            </article>
        @endforeach
    </section>

    <x-parcel-scanner :action="route('logistics.assignments')" :tracking="$tracking ?? ''" title="Scan Parcel For Rider Release" description="After final-rider assignment, verify the physical parcel before authorizing release." button="Verify Release" />

    @if(($tracking ?? '') !== '' && !$releaseDelivery)
        <div class="border border-red-200 bg-red-50 p-4 text-[10px] font-semibold text-red-700">No assigned final-delivery parcel matches that tracking number at this center.</div>
    @endif
    @if($releaseDelivery)
        <section class="border border-green-200 bg-green-50 p-5">
            <h2 class="text-[13px] font-semibold text-green-900">Parcel Verified For Release</h2>
            <p class="mt-2 text-[10px] text-green-800">{{ $releaseDelivery->tracking_number }} is assigned to {{ $releaseDelivery->rider?->name }} for {{ $releaseDelivery->delivery_area ?: 'the buyer destination' }}.</p>
            @if($releaseAuthorized)
                <div class="mt-4 border border-green-300 bg-white p-3 text-[10px] font-semibold text-green-800">Release already authorized. The assigned rider can now scan and collect the parcel.</div>
            @elseif($releaseAssignment?->status === 'accepted')
                <form method="POST" action="{{ route('logistics.assignments.release', $releaseDelivery) }}" class="mt-4">@csrf<input type="hidden" name="tracking" value="{{ $releaseDelivery->tracking_number }}"><button class="bg-primary px-4 py-2 text-[10px] font-semibold text-white">Confirm Release Authorization</button></form>
            @else
                <div class="mt-4 border border-amber-300 bg-amber-50 p-3 text-[10px] font-semibold text-amber-800">Waiting for {{ $releaseDelivery->rider?->name ?: 'the assigned rider' }} to accept the delivery assignment.</div>
            @endif
        </section>
    @endif

    <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section class="overflow-hidden rounded-xl border border-line bg-surface">
            <div class="border-b border-line px-5 py-4">
                <h2 class="text-[13px] font-semibold text-ink">Parcels Waiting for Rider</h2>
                <p class="mt-1 text-[9px] text-muted">Only sorted parcels from the deliveries table appear here.</p>
            </div>

            @forelse($parcels as $parcel)
                <article class="flex flex-col gap-4 border-b border-line p-5 last:border-b-0 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ $parcel['image'] }}" alt="" class="h-11 w-11 rounded-lg border border-line object-cover">
                        <div>
                            <strong class="block text-[11px] font-semibold text-ink">{{ $parcel['tracking'] }}</strong>
                            <p class="mt-1 text-[9px] text-muted">{{ $parcel['buyer'] }}</p>
                            <p class="mt-1 text-[9px] text-muted">{{ $parcel['destination'] }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <span class="w-fit rounded-full bg-primary-soft px-3 py-1 text-[8px] font-semibold text-primary">
                            {{ $parcel['area'] }}
                        </span>

                        <form method="POST" action="{{ route('logistics.assignments.assign-rider', $parcel['id']) }}" class="flex flex-wrap items-center gap-2">
                            @csrf
                            <select name="rider_id" required class="h-9 rounded-lg border border-line bg-surface px-2 text-[9px] text-ink">
                                <option value="">Select rider</option>
                                @foreach($riders as $rider)
                                    <option value="{{ $rider['id'] }}">{{ $rider['name'] }} - {{ $rider['area'] }}</option>
                                @endforeach
                            </select>

                            <button type="submit" class="h-9 rounded-lg bg-primary px-4 text-[9px] font-semibold text-white transition hover:bg-primary-hover">
                                Assign
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="p-5 text-[10px] text-muted">No sorted parcels are waiting for rider assignment.</div>
            @endforelse
        </section>

        <aside class="rounded-xl border border-line bg-surface p-5">
            <h2 class="text-[13px] font-semibold text-ink">Available Riders</h2>
            <p class="mt-1 text-[9px] text-muted">Active rider accounts from the users table.</p>

            <div class="mt-5 flex flex-col gap-3">
                @forelse($riders as $rider)
                    <a href="{{ route('logistics.riders.show', $rider['id']) }}" class="flex items-center justify-between rounded-xl border border-line bg-page-secondary p-4 text-left transition hover:border-primary">
                        <div>
                            <strong class="block text-[10px] font-semibold text-ink">{{ $rider['name'] }}</strong>
                            <span class="mt-1 block text-[8px] text-muted">{{ $rider['area'] }}</span>
                        </div>
                        <span class="text-[8px] font-semibold text-primary">{{ $rider['active'] }} active</span>
                    </a>
                @empty
                    <div class="rounded-xl border border-line bg-page-secondary p-4 text-[9px] text-muted">
                        No active riders are available.
                    </div>
                @endforelse
            </div>
        </aside>
    </section>
</div>
@endsection
