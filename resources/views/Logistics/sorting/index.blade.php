@extends('logistics.app')

@section('title', 'Parcel Sorting - LIKHAE Logistics')

@section('content')
@php
    $parcels = $logisticsParcels ?? [];
    $overview = $logisticsOverview ?? [];
@endphp

<div class="flex w-full flex-col gap-6">
    @if(session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-[11px] font-semibold text-green-700">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-[11px] font-semibold text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <nav class="flex flex-wrap items-center gap-2 text-[10px] text-muted">
        <a href="{{ route('logistics.dashboard') }}" class="transition hover:text-primary">Dashboard</a>
        <span>/</span>
        <span class="font-semibold text-ink">Parcel Sorting</span>
    </nav>

    <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Sorting Center</span>
            <h1 class="mt-2 font-display text-[26px] font-semibold tracking-[-0.04em] text-ink sm:text-[30px]">
                Sort parcels by destination.
            </h1>
            <p class="mt-2 max-w-[650px] text-[11px] leading-6 text-muted">
                Only parcels already received at the sorting center appear here. Sorting moves a parcel to rider assignment.
            </p>
        </div>

        <a href="{{ route('logistics.parcels.receive') }}" class="inline-flex h-10 shrink-0 items-center justify-center rounded-lg border border-line bg-surface px-4 text-[11px] font-semibold text-ink transition hover:bg-page-secondary">
            Receive Parcel
        </a>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($overview as $item)
            <article class="rounded-xl border border-line bg-surface p-4 shadow-sm">
                <span class="text-[9px] font-medium text-muted">{{ $item['label'] }}</span>
                <strong class="mt-2 block text-[24px] font-bold tracking-[-0.04em] text-ink">{{ $item['value'] }}</strong>
                <span class="mt-3 inline-flex rounded-full px-2.5 py-1 text-[8px] font-semibold {{ $item['class'] ?? 'bg-page-secondary text-muted' }}">
                    Live count
                </span>
            </article>
        @endforeach
    </section>

    <x-parcel-scanner :action="route('logistics.sorting')" :tracking="$tracking ?? ''" title="Scan Parcel For Sorting" description="Identify a parcel already received at this sorting center. Scanning does not sort it until you confirm." button="Verify Parcel" />

    @if(($tracking ?? '') !== '' && !$selectedDelivery)
        <div class="border border-red-200 bg-red-50 p-4 text-[10px] font-semibold text-red-700">No eligible parcel at this sorting center matches that tracking number.</div>
    @endif
    @if($selectedDelivery)
        <section class="border border-green-200 bg-green-50 p-5">
            <h2 class="text-[13px] font-semibold text-green-900">Parcel Verified</h2>
            <dl class="mt-3 grid gap-3 text-[10px] sm:grid-cols-3">
                <div><dt class="text-green-700">Tracking</dt><dd class="font-semibold">{{ $selectedDelivery->tracking_number }}</dd></div>
                <div><dt class="text-green-700">Buyer</dt><dd class="font-semibold">{{ $selectedDelivery->order?->buyer?->name }}</dd></div>
                <div><dt class="text-green-700">Destination</dt><dd class="font-semibold">{{ collect([$selectedDelivery->order?->buyer?->barangay, $selectedDelivery->order?->buyer?->municipality, $selectedDelivery->order?->buyer?->province])->filter()->implode(', ') ?: 'Address review required' }}</dd></div>
            </dl>
            <form method="POST" action="{{ route('logistics.sorting.sort', $selectedDelivery) }}" class="mt-4">@csrf<input type="hidden" name="tracking" value="{{ $selectedDelivery->tracking_number }}"><button class="bg-primary px-4 py-2 text-[10px] font-semibold text-white">Confirm Sorted</button></form>
        </section>
    @endif

    <section class="rounded-xl border border-line bg-surface p-4">
        <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_190px_auto]">
            <div class="relative">
                <input id="sortingSearch" type="text" placeholder="Search tracking, order, buyer, destination..." class="h-10 w-full rounded-lg border border-line bg-page-secondary px-4 text-[10px] text-ink outline-none transition placeholder:text-muted-light focus:border-primary focus:ring-2 focus:ring-primary/10">
            </div>

            <select id="areaFilter" class="h-10 w-full rounded-lg border border-line bg-page-secondary px-3 text-[10px] text-ink outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10">
                <option value="all">All Areas</option>
                <option value="unassigned">Unassigned</option>
                @foreach(collect($parcels)->pluck('area')->unique()->filter() as $area)
                    <option value="{{ strtolower($area) }}">{{ $area }}</option>
                @endforeach
            </select>

            <button id="resetFilters" type="button" class="inline-flex h-10 items-center justify-center rounded-lg border border-line bg-surface px-4 text-[10px] font-semibold text-muted transition hover:bg-page-secondary hover:text-ink">
                Reset
            </button>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-line bg-surface">
        <div class="flex flex-col gap-3 border-b border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-[13px] font-semibold text-ink">Sorting Queue</h2>
                <p class="mt-0.5 text-[9px] text-muted">
                    Showing <span id="visibleParcelCount" class="font-semibold text-ink">{{ count($parcels) }}</span> real parcel record(s)
                </p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-full bg-warning-soft px-2.5 py-1 text-[8px] font-semibold text-warning">
                Waiting sorting
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead>
                    <tr class="border-b border-line bg-page-secondary text-left">
                        <th class="px-5 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">Parcel</th>
                        <th class="px-4 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">Buyer</th>
                        <th class="px-4 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">Destination</th>
                        <th class="px-4 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">Area</th>
                        <th class="px-4 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">Status</th>
                        <th class="px-4 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">Received</th>
                        <th class="px-5 py-3 text-right text-[8px] font-bold uppercase tracking-[0.1em] text-muted">Action</th>
                    </tr>
                </thead>
                <tbody id="sortingTableBody" class="divide-y divide-line">
                    @forelse($parcels as $parcel)
                        <tr class="sorting-row transition hover:bg-page-secondary"
                            data-search="{{ strtolower($parcel['tracking'].' '.$parcel['order'].' '.$parcel['buyer'].' '.$parcel['destination'].' '.$parcel['area'].' '.$parcel['status']) }}"
                            data-area="{{ strtolower($parcel['area'] ?: 'unassigned') }}">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $parcel['image'] }}" alt="" class="h-10 w-10 rounded-lg border border-line object-cover">
                                    <div>
                                        <a href="{{ route('logistics.parcels.show', $parcel['id']) }}" class="block text-[10px] font-semibold text-ink transition hover:text-primary">{{ $parcel['tracking'] }}</a>
                                        <span class="mt-0.5 block text-[8px] text-muted">{{ $parcel['order'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-[10px] font-medium text-ink">{{ $parcel['buyer'] }}</td>
                            <td class="px-4 py-4 text-[10px] text-ink">{{ $parcel['destination'] }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full border border-line bg-page-secondary px-2.5 py-1 text-[8px] font-semibold text-muted">{{ $parcel['area'] }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full bg-warning-soft px-2.5 py-1 text-[8px] font-semibold text-warning">{{ $parcel['status'] }}</span>
                            </td>
                            <td class="px-4 py-4 text-[9px] text-muted">{{ $parcel['received'] }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('logistics.parcels.show', $parcel['id']) }}" class="inline-flex h-8 items-center justify-center rounded-lg border border-line bg-surface px-3 text-[8px] font-semibold text-ink transition hover:bg-page-secondary">View</a>
                                    <a href="{{ route('logistics.sorting', ['tracking' => $parcel['tracking']]) }}" class="inline-flex h-8 items-center justify-center rounded-lg bg-primary px-3 text-[8px] font-semibold text-white">Verify to Sort</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-[10px] text-muted">
                                No parcels are currently waiting for sorting. Receive a picked-up parcel first.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="sortingEmptyState" class="hidden border-t border-line px-5 py-12 text-center">
            <h3 class="text-[12px] font-semibold text-ink">No parcels found</h3>
            <p class="mt-1 text-[9px] text-muted">Try changing the search term or filters.</p>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('sortingSearch');
    const areaFilter = document.getElementById('areaFilter');
    const resetButton = document.getElementById('resetFilters');
    const rows = Array.from(document.querySelectorAll('.sorting-row'));
    const visibleCount = document.getElementById('visibleParcelCount');
    const emptyState = document.getElementById('sortingEmptyState');

    function applyFilters() {
        const search = (searchInput?.value || '').trim().toLowerCase();
        const area = areaFilter?.value || 'all';
        let visible = 0;

        rows.forEach(function (row) {
            const matchesSearch = !search || (row.dataset.search || '').includes(search);
            const matchesArea = area === 'all' || (row.dataset.area || '') === area;
            const show = matchesSearch && matchesArea;
            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        if (visibleCount) visibleCount.textContent = visible;
        emptyState?.classList.toggle('hidden', visible !== 0 || rows.length === 0);
    }

    searchInput?.addEventListener('input', applyFilters);
    areaFilter?.addEventListener('change', applyFilters);
    resetButton?.addEventListener('click', function () {
        if (searchInput) searchInput.value = '';
        if (areaFilter) areaFilter.value = 'all';
        applyFilters();
    });

    applyFilters();
});
</script>
@endpush
