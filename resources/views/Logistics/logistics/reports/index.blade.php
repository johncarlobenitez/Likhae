@extends('logistics.app')

@section('title', 'Logistics Reports — LIKHAE Logistics')

@section('content')

<div class="flex w-full flex-col gap-6">

    {{-- PAGE HEADER --}}
    <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">

        <div>
            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">
                Analytics
            </span>

            <h1 class="mt-2 font-display text-[30px] font-semibold tracking-[-0.04em] text-ink sm:text-[34px]">
                Logistics Reports
            </h1>

            <p class="mt-2 max-w-[680px] text-[10px] leading-5 text-muted">
                Review parcel activity, rider performance, delivery areas, and payment summaries.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <button id="exportPdfButton"
                class="h-10 rounded-lg border border-line bg-surface px-4 text-[9px] font-semibold text-ink hover:bg-page-secondary">
                Export PDF
            </button>

            <button id="exportExcelButton"
                class="h-10 rounded-lg bg-primary px-4 text-[9px] font-semibold text-white hover:bg-primary-hover">
                Export Excel
            </button>
        </div>

    </section>


    {{-- FILTERS --}}
    <section class="rounded-xl border border-line bg-surface p-5">

        <div class="grid gap-3 lg:grid-cols-[1fr_180px_180px_auto]">

            <div>
                <label class="mb-1.5 block text-[8px] font-semibold uppercase tracking-[0.1em] text-muted">
                    Report
                </label>

                <select id="reportType"
                    class="h-10 w-full rounded-lg border border-line bg-surface px-3 text-[9px] text-ink outline-none focus:border-primary">
                    <option>Parcel Summary</option>
                    <option>Rider Performance</option>
                    <option>Area Performance</option>
                    <option>COD / Payment Summary</option>
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-[8px] font-semibold uppercase tracking-[0.1em] text-muted">
                    From
                </label>

                <input id="dateFrom" type="date" value="2026-09-01"
                    class="h-10 w-full rounded-lg border border-line bg-surface px-3 text-[9px] text-ink outline-none focus:border-primary">
            </div>

            <div>
                <label class="mb-1.5 block text-[8px] font-semibold uppercase tracking-[0.1em] text-muted">
                    To
                </label>

                <input id="dateTo" type="date" value="2026-09-03"
                    class="h-10 w-full rounded-lg border border-line bg-surface px-3 text-[9px] text-ink outline-none focus:border-primary">
            </div>

            <button id="applyFilterButton"
                class="h-10 self-end rounded-lg bg-primary px-5 text-[9px] font-semibold text-white hover:bg-primary-hover">
                Apply
            </button>

        </div>

    </section>


    {{-- SUMMARY CARDS --}}
    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

        @foreach([
            ['Parcels Received','1,248','+12.8%'],
            ['Delivered','1,106','+9.4%'],
            ['Out for Delivery','86','Active today'],
            ['Failed Delivery','18','-3.2%']
        ] as $stat)

        <article class="rounded-xl border border-line bg-surface p-5">

            <span class="text-[9px] text-muted">
                {{ $stat[0] }}
            </span>

            <div class="mt-2 flex items-end justify-between gap-3">

                <strong class="text-[26px] font-bold tracking-[-0.04em] text-ink">
                    {{ $stat[1] }}
                </strong>

                <span class="rounded-full bg-success-soft px-2 py-1 text-[7px] font-semibold text-success">
                    {{ $stat[2] }}
                </span>

            </div>

        </article>

        @endforeach

    </section>


    {{-- OVERVIEW --}}
    <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_320px]">

        <section class="rounded-xl border border-line bg-surface p-5">

            <div class="flex items-start justify-between gap-4">

                <div>
                    <h2 class="text-[13px] font-semibold text-ink">
                        Delivery Overview
                    </h2>

                    <p class="mt-1 text-[9px] text-muted">
                        Parcel volume for the selected reporting period.
                    </p>
                </div>

                <span class="rounded-full bg-primary-soft px-3 py-1 text-[8px] font-semibold text-primary">
                    Current Period
                </span>

            </div>


            <div class="mt-6 flex h-[230px] items-end gap-3 border-b border-line">

                @foreach([
                    ['Sep 01',87],
                    ['Sep 02',91],
                    ['Sep 03',84],
                    ['Sep 04',76],
                    ['Sep 05',93],
                    ['Sep 06',81],
                    ['Sep 07',96]
                ] as $day)

                <div class="flex min-w-0 flex-1 flex-col items-center justify-end gap-2">

                    <span class="text-[7px] font-semibold text-muted">
                        {{ $day[1] }}
                    </span>

                    <div
                        class="w-full max-w-[42px] rounded-t-lg bg-primary/80"
                        style="height: {{ max(35, round($day[1] * 1.55)) }}px"
                    ></div>

                    <span class="pb-2 text-[7px] text-muted">
                        {{ $day[0] }}
                    </span>

                </div>

                @endforeach

            </div>

        </section>


        <section class="rounded-xl border border-line bg-surface p-5">

            <h2 class="text-[13px] font-semibold text-ink">
                Delivery Rate
            </h2>

            <p class="mt-1 text-[9px] text-muted">
                Overall delivery completion.
            </p>

            <div class="mt-7 flex justify-center">

                <div class="grid h-40 w-40 place-items-center rounded-full border-[14px] border-primary">

                    <div class="text-center">
                        <strong class="block text-[28px] font-bold text-ink">
                            91.8%
                        </strong>

                        <span class="text-[8px] text-muted">
                            Success rate
                        </span>
                    </div>

                </div>

            </div>

            <div class="mt-6 grid grid-cols-2 gap-2">

                <div class="rounded-lg bg-page-secondary p-3 text-center">
                    <strong class="block text-[13px] font-bold text-ink">1,106</strong>
                    <span class="text-[7px] text-muted">Delivered</span>
                </div>

                <div class="rounded-lg bg-page-secondary p-3 text-center">
                    <strong class="block text-[13px] font-bold text-ink">124</strong>
                    <span class="text-[7px] text-muted">Pending</span>
                </div>

            </div>

        </section>

    </section>


    {{-- PARCEL REPORT --}}
    <section class="overflow-hidden rounded-xl border border-line bg-surface">

        <div class="flex flex-col gap-3 border-b border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-[13px] font-semibold text-ink">
                    Parcel Summary
                </h2>

                <p class="mt-1 text-[9px] text-muted">
                    Daily parcel volume and delivery status.
                </p>
            </div>

            <span class="text-[8px] text-muted">
                September 2026
            </span>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full min-w-[760px] text-left">

                <thead class="bg-page-secondary">

                    <tr class="border-b border-line">

                        @foreach(['Date','Received','Sorted','Out for Delivery','Delivered','Failed'] as $heading)

                        <th class="px-5 py-3 text-[7px] font-bold uppercase tracking-[0.1em] text-muted">
                            {{ $heading }}
                        </th>

                        @endforeach

                    </tr>

                </thead>


                <tbody class="divide-y divide-line">

                    @foreach([
                        ['Sep 03, 2026',84,79,72,68,2],
                        ['Sep 02, 2026',91,88,81,76,3],
                        ['Sep 01, 2026',87,84,78,74,1],
                        ['Aug 31, 2026',79,76,70,66,2],
                        ['Aug 30, 2026',82,80,74,70,1]
                    ] as $row)

                    <tr class="transition hover:bg-page-secondary">

                        <td class="px-5 py-4 text-[9px] font-semibold text-ink">
                            {{ $row[0] }}
                        </td>

                        <td class="px-5 py-4 text-[9px] text-muted">{{ $row[1] }}</td>
                        <td class="px-5 py-4 text-[9px] text-muted">{{ $row[2] }}</td>
                        <td class="px-5 py-4 text-[9px] text-muted">{{ $row[3] }}</td>

                        <td class="px-5 py-4 text-[9px] font-semibold text-success">
                            {{ $row[4] }}
                        </td>

                        <td class="px-5 py-4 text-[9px] font-semibold text-warning">
                            {{ $row[5] }}
                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </section>


    {{-- RIDER PERFORMANCE --}}
    <section class="grid gap-5 xl:grid-cols-2">

        <section class="overflow-hidden rounded-xl border border-line bg-surface">

            <div class="border-b border-line px-5 py-4">

                <h2 class="text-[13px] font-semibold text-ink">
                    Rider Performance
                </h2>

                <p class="mt-1 text-[9px] text-muted">
                    Rider delivery completion and ratings.
                </p>

            </div>


            <div class="divide-y divide-line">

                @foreach([
                    ['Rider 03','Area C','48','45','93.8%','4.9'],
                    ['Rider 01','Area A','43','41','95.3%','4.8'],
                    ['Rider 04','Area D','39','37','94.9%','4.8'],
                    ['Rider 02','Area B','36','33','91.7%','4.7']
                ] as $rider)

                <div class="flex items-center gap-4 px-5 py-4">

                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-primary-soft text-[8px] font-bold text-primary">
                        {{ substr($rider[0], -2) }}
                    </div>

                    <div class="min-w-0 flex-1">

                        <strong class="block text-[9px] font-semibold text-ink">
                            {{ $rider[0] }}
                        </strong>

                        <span class="mt-1 block text-[7px] text-muted">
                            {{ $rider[1] }} · {{ $rider[2] }} assigned
                        </span>

                    </div>

                    <div class="text-right">

                        <strong class="block text-[9px] font-semibold text-success">
                            {{ $rider[4] }}
                        </strong>

                        <span class="mt-1 block text-[7px] text-muted">
                            {{ $rider[5] }} ★
                        </span>

                    </div>

                </div>

                @endforeach

            </div>

        </section>


        {{-- AREA PERFORMANCE --}}
        <section class="overflow-hidden rounded-xl border border-line bg-surface">

            <div class="border-b border-line px-5 py-4">

                <h2 class="text-[13px] font-semibold text-ink">
                    Area Performance
                </h2>

                <p class="mt-1 text-[9px] text-muted">
                    Parcel distribution by delivery area.
                </p>

            </div>


            <div class="divide-y divide-line">

                @foreach([
                    ['Area A','Santa Cruz','312','286','91.7'],
                    ['Area B','Pagsanjan','245','223','91.0'],
                    ['Area C','Los Baños','381','349','91.6'],
                    ['Area D','Calamba','310','281','90.6']
                ] as $area)

                <div class="px-5 py-4">

                    <div class="flex items-center justify-between gap-3">

                        <div>

                            <strong class="block text-[9px] font-semibold text-ink">
                                {{ $area[0] }}
                            </strong>

                            <span class="mt-1 block text-[7px] text-muted">
                                {{ $area[1] }}
                            </span>

                        </div>

                        <strong class="text-[9px] font-semibold text-success">
                            {{ $area[4] }}%
                        </strong>

                    </div>


                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-page-secondary">

                        <div
                            class="h-full rounded-full bg-primary"
                            style="width: {{ $area[4] }}%"
                        ></div>

                    </div>


                    <div class="mt-2 flex justify-between">

                        <span class="text-[7px] text-muted">
                            {{ $area[2] }} parcels
                        </span>

                        <span class="text-[7px] text-muted">
                            {{ $area[3] }} delivered
                        </span>

                    </div>

                </div>

                @endforeach

            </div>

        </section>

    </section>


    {{-- PAYMENT --}}
    <section class="rounded-xl border border-line bg-surface p-5">

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-[13px] font-semibold text-ink">
                    COD / Payment Summary
                </h2>

                <p class="mt-1 text-[9px] text-muted">
                    Payment collection overview for completed deliveries.
                </p>
            </div>

            <span class="rounded-full bg-success-soft px-3 py-1 text-[8px] font-semibold text-success">
                Reconciled
            </span>

        </div>


        <div class="mt-5 grid gap-3 sm:grid-cols-3">

            <div class="rounded-lg bg-page-secondary p-4">
                <span class="text-[8px] text-muted">COD Collected</span>
                <strong class="mt-2 block text-[18px] font-bold text-ink">
                    ₱184,520
                </strong>
            </div>

            <div class="rounded-lg bg-page-secondary p-4">
                <span class="text-[8px] text-muted">GCash Payments</span>
                <strong class="mt-2 block text-[18px] font-bold text-ink">
                    ₱96,840
                </strong>
            </div>

            <div class="rounded-lg bg-page-secondary p-4">
                <span class="text-[8px] text-muted">Total Collected</span>
                <strong class="mt-2 block text-[18px] font-bold text-primary">
                    ₱281,360
                </strong>
            </div>

        </div>

    </section>

</div>


{{-- SIMPLE FEEDBACK MODAL --}}
<div id="reportModal"
    class="invisible fixed inset-0 z-[100] grid place-items-center bg-black/50 p-5 opacity-0 backdrop-blur-sm transition">

    <div id="reportModalPanel"
        class="w-full max-w-[420px] scale-95 rounded-2xl border border-line bg-surface p-6 shadow-likhae-lg transition">

        <div class="grid h-12 w-12 place-items-center rounded-full bg-success-soft text-success">
            ✓
        </div>

        <h2 id="reportModalTitle" class="mt-4 text-[18px] font-semibold text-ink">
            Export Ready
        </h2>

        <p id="reportModalText" class="mt-2 text-[9px] leading-5 text-muted">
            Your report is ready.
        </p>

        <button id="closeReportModal"
            class="mt-5 h-10 w-full rounded-lg bg-primary text-[9px] font-semibold text-white">
            Done
        </button>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('reportModal');
    const panel = document.getElementById('reportModalPanel');
    const title = document.getElementById('reportModalTitle');
    const text = document.getElementById('reportModalText');


    function openModal(modalTitle, modalText) {

        title.textContent = modalTitle;
        text.textContent = modalText;

        modal.classList.remove('invisible', 'opacity-0');
        modal.classList.add('visible', 'opacity-100');

        panel.classList.remove('scale-95');
        panel.classList.add('scale-100');

        document.body.classList.add('overflow-hidden');
    }


    function closeModal() {

        modal.classList.remove('visible', 'opacity-100');
        modal.classList.add('invisible', 'opacity-0');

        panel.classList.remove('scale-100');
        panel.classList.add('scale-95');

        document.body.classList.remove('overflow-hidden');
    }


    document.getElementById('exportPdfButton')?.addEventListener('click', function () {

        openModal(
            'PDF Export',
            'The selected report is prepared for PDF export. Connect Laravel PDF generation when the backend is ready.'
        );

    });


    document.getElementById('exportExcelButton')?.addEventListener('click', function () {

        openModal(
            'Excel Export',
            'The selected report is prepared for Excel export. Connect Laravel Excel when the backend is ready.'
        );

    });


    document.getElementById('applyFilterButton')?.addEventListener('click', function () {

        const from = document.getElementById('dateFrom').value;
        const to = document.getElementById('dateTo').value;


        if (!from || !to) {

            openModal(
                'Date Required',
                'Please select both a start date and an end date.'
            );

            return;
        }


        if (from > to) {

            openModal(
                'Invalid Date Range',
                'The start date cannot be later than the end date.'
            );

            return;
        }


        openModal(
            'Filters Applied',
            `The report period is ${from} to ${to}. Connect these filters to your Laravel controller for live data.`
        );

    });


    document.getElementById('closeReportModal')?.addEventListener(
        'click',
        closeModal
    );


    modal?.addEventListener('click', function (event) {

        if (event.target === modal) {
            closeModal();
        }

    });


    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            closeModal();
        }

    });

});
</script>

@endpush
