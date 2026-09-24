@extends('Logistics.app')

@section('title', 'Parcels - LIKHAE Logistics')

@php
    $parcels = $logisticsParcels ?? [];
    $overview = $logisticsOverview ?? [
        ['label' => 'Total Parcels', 'value' => 0, 'tone' => 'primary'],
        ['label' => 'Waiting Sorting', 'value' => 0, 'tone' => 'warning'],
        ['label' => 'Awaiting Rider', 'value' => 0, 'tone' => 'info'],
        ['label' => 'Delivered', 'value' => 0, 'tone' => 'success'],
    ];
@endphp

@section('content')
<div class="flex w-full flex-col gap-6">
    <section class="flex flex-col gap-5 rounded-xl border border-line bg-surface p-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Parcel Management</span>
            <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink sm:text-[38px]">All Parcels</h1>
            <p class="mt-2 max-w-[650px] text-[11px] leading-6 text-muted">
                Parcel records shown here come from the deliveries table and connected orders.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('logistics.parcels.receive') }}" class="inline-flex h-10 items-center justify-center rounded-lg bg-primary px-4 text-[10px] font-semibold text-white">Receive Parcel</a>
            <a href="{{ route('logistics.parcels.tracking') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-line bg-surface px-4 text-[10px] font-semibold text-ink">Track Parcel</a>
        </div>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($overview as $item)
            <article class="rounded-xl border border-line bg-surface p-4">
                <span class="text-[9px] font-semibold uppercase tracking-[0.08em] text-muted">{{ $item['label'] }}</span>
                <strong class="mt-2 block text-[24px] font-bold text-ink">{{ $item['value'] }}</strong>
            </article>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-xl border border-line bg-surface">
        <div class="border-b border-line px-5 py-4">
            <h2 class="text-[13px] font-semibold text-ink">Parcel Records</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px] text-left">
                <thead class="bg-page-secondary text-[8px] uppercase tracking-[0.08em] text-muted">
                    <tr>
                        <th class="px-5 py-3">Tracking</th>
                        <th class="px-5 py-3">Buyer</th>
                        <th class="px-5 py-3">Destination</th>
                        <th class="px-5 py-3">Area</th>
                        <th class="px-5 py-3">Rider</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($parcels as $parcel)
                        <tr>
                            <td class="px-5 py-4">
                                <strong class="block text-[10px] font-semibold text-ink">{{ $parcel['tracking'] }}</strong>
                                <span class="text-[8px] text-muted">{{ $parcel['order'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-[10px] text-ink">{{ $parcel['buyer'] }}</td>
                            <td class="px-5 py-4 text-[10px] text-ink">{{ $parcel['destination'] }}</td>
                            <td class="px-5 py-4 text-[10px] text-muted">{{ $parcel['area'] }}</td>
                            <td class="px-5 py-4 text-[10px] text-muted">{{ $parcel['rider'] }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-primary-soft px-3 py-1 text-[8px] font-semibold text-primary">{{ $parcel['status'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('logistics.parcels.show', $parcel['id']) }}" class="rounded-lg border border-line px-3 py-2 text-[9px] font-semibold text-ink">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-6 text-[10px] text-muted">No parcel records are available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
