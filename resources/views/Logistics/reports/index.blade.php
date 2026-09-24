@extends('Logistics.app')

@section('title', 'Logistics Reports - LIKHAE Logistics')

@section('content')
<div class="flex flex-col gap-6">
    <section>
        <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Analytics</span>
        <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink">Logistics Reports</h1>
        <p class="mt-2 max-w-[680px] text-[11px] leading-6 text-muted">Report values are calculated from real delivery and order records.</p>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($summaryCards as $stat)
            <article class="rounded-xl border border-line bg-surface p-4">
                <span class="text-[9px] font-medium text-muted">{{ $stat['label'] }}</span>
                <strong class="mt-2 block text-[24px] font-bold tracking-[-0.04em] text-ink">{{ $stat['value'] }}</strong>
                <span class="mt-2 block text-[8px] font-semibold text-primary">{{ $stat['change'] }}</span>
            </article>
        @endforeach
    </section>

    <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_320px]">
        <section class="overflow-hidden rounded-xl border border-line bg-surface">
            <div class="border-b border-line px-5 py-4">
                <h2 class="text-[13px] font-semibold text-ink">Parcel Summary</h2>
                <p class="mt-1 text-[9px] text-muted">Grouped by the latest parcel update date.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="bg-page-secondary text-[8px] uppercase tracking-[0.12em] text-muted">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Received</th>
                            <th class="px-4 py-3">Sorted</th>
                            <th class="px-4 py-3">Out for Delivery</th>
                            <th class="px-4 py-3">Delivered</th>
                            <th class="px-4 py-3">Failed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line text-[10px]">
                        @forelse($parcelSummary as $row)
                            <tr>
                                @foreach($row as $cell)
                                    <td class="px-4 py-3">{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-muted">No delivery records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="rounded-xl border border-line bg-surface p-5">
            <h2 class="text-[13px] font-semibold text-ink">Delivery Rate</h2>
            <strong class="mt-5 block text-[34px] font-bold text-primary">{{ $successRate }}%</strong>
            <p class="mt-2 text-[10px] text-muted">{{ $pendingCount }} parcel(s) still active or pending.</p>
            <p class="mt-4 text-[9px] text-muted">COD total from delivery orders: PHP {{ number_format($codTotal, 2) }}</p>
        </aside>
    </section>

    <section class="grid gap-5 xl:grid-cols-2">
        <section class="overflow-hidden rounded-xl border border-line bg-surface">
            <div class="border-b border-line px-5 py-4">
                <h2 class="text-[13px] font-semibold text-ink">Rider Workload</h2>
            </div>
            @forelse($riders as $rider)
                <article class="flex items-center justify-between border-b border-line p-5">
                    <div>
                        <strong class="block text-[10px] font-semibold text-ink">{{ $rider[0] }}</strong>
                        <span class="mt-1 block text-[8px] text-muted">{{ $rider[1] }} - {{ $rider[2] }} assigned - {{ $rider[3] }} delivered</span>
                    </div>
                    <span class="text-[8px] font-semibold text-muted">{{ $rider[4] }}</span>
                </article>
            @empty
                <div class="p-5 text-[10px] text-muted">No active riders found.</div>
            @endforelse
        </section>

        <section class="overflow-hidden rounded-xl border border-line bg-surface">
            <div class="border-b border-line px-5 py-4">
                <h2 class="text-[13px] font-semibold text-ink">Area Coverage</h2>
            </div>
            @forelse($areas as $area)
                <article class="flex items-center justify-between border-b border-line p-5">
                    <div>
                        <strong class="block text-[10px] font-semibold text-ink">{{ $area['area'] }}</strong>
                        <span class="mt-1 block text-[8px] text-muted">{{ $area['municipality'] }}</span>
                    </div>
                    <span class="text-[8px] font-semibold text-primary">{{ $area['available'] }} rider(s)</span>
                </article>
            @empty
                <div class="p-5 text-[10px] text-muted">No area data found.</div>
            @endforelse
        </section>
    </section>
</div>
@endsection
