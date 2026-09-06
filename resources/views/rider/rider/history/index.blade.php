@extends('rider.app')

@section('title', 'Delivery History — LIKHAE Rider')

@section('content')

@php

    $history = [

        [
            'tracking' => 'LH-2026-0901',
            'buyer' => 'Ana Reyes',
            'location' => 'Los Baños, Laguna',
            'date' => 'September 01, 2026',
            'status' => 'DELIVERED',
            'rating' => '5.0',
            'earnings' => '₱850',
        ],

        [
            'tracking' => 'LH-2026-0830',
            'buyer' => 'Carlo Mendoza',
            'location' => 'Calamba, Laguna',
            'date' => 'August 30, 2026',
            'status' => 'DELIVERED',
            'rating' => '4.9',
            'earnings' => '₱1,200',
        ],

        [
            'tracking' => 'LH-2026-0828',
            'buyer' => 'Maria Cruz',
            'location' => 'Santa Cruz, Laguna',
            'date' => 'August 28, 2026',
            'status' => 'DELIVERED',
            'rating' => '5.0',
            'earnings' => '₱560',
        ],

        [
            'tracking' => 'LH-2026-0825',
            'buyer' => 'Daniel Santos',
            'location' => 'Biñan, Laguna',
            'date' => 'August 25, 2026',
            'status' => 'DELIVERED',
            'rating' => '4.8',
            'earnings' => '₱780',
        ],

    ];

@endphp


<div class="mx-auto w-full max-w-[1280px] space-y-6">


    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <section
        class="
            flex
            flex-col
            gap-4
            border-b
            border-line
            pb-5
            lg:flex-row
            lg:items-center
            lg:justify-between
        "
    >

        <div>

            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-primary">
                Delivery Records
            </p>

            <h1 class="mt-1.5 text-[28px] font-bold leading-tight tracking-tight text-ink">
                Delivery History
            </h1>

            <p class="mt-1.5 text-sm text-muted">
                View your completed deliveries and performance records.
            </p>

        </div>


        {{-- Export Button --}}

        <button
            type="button"
            id="export-history-btn"
            class="
                inline-flex
                h-10
                w-fit
                items-center
                gap-2
                rounded-lg
                bg-primary
                px-4
                text-xs
                font-semibold
                text-white
                transition
                hover:opacity-90
            "
        >

            <svg
                viewBox="0 0 24 24"
                class="h-4 w-4 fill-none stroke-current"
            >
                <path
                    d="M12 3v12"
                    stroke-width="1.7"
                ></path>

                <path
                    d="m7 10 5 5 5-5"
                    stroke-width="1.7"
                ></path>

                <path
                    d="M5 21h14"
                    stroke-width="1.7"
                ></path>
            </svg>

            Export History

        </button>

    </section>



    {{-- =========================================================
        PERFORMANCE SUMMARY
    ========================================================== --}}

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">


        {{-- Completed --}}

        <div
            class="
                rounded-xl
                border
                border-line
                bg-surface
                px-5
                py-4
            "
        >

            <p class="text-xs font-medium text-muted">
                Completed Deliveries
            </p>

            <p class="mt-1.5 text-2xl font-bold tracking-tight text-ink">
                342
            </p>

            <p class="mt-1 text-[11px] text-success">
                Successfully completed
            </p>

        </div>



        {{-- Success Rate --}}

        <div
            class="
                rounded-xl
                border
                border-line
                bg-surface
                px-5
                py-4
            "
        >

            <p class="text-xs font-medium text-muted">
                Success Rate
            </p>

            <p class="mt-1.5 text-2xl font-bold tracking-tight text-ink">
                98%
            </p>

            <p class="mt-1 text-[11px] text-success">
                Excellent performance
            </p>

        </div>



        {{-- Rating --}}

        <div
            class="
                rounded-xl
                border
                border-line
                bg-surface
                px-5
                py-4
            "
        >

            <p class="text-xs font-medium text-muted">
                Customer Rating
            </p>

            <div class="mt-1.5 flex items-center gap-2">

                <p class="text-2xl font-bold tracking-tight text-ink">
                    4.9
                </p>

                <span class="text-sm text-warning">
                    ★
                </span>

            </div>

            <p class="mt-1 text-[11px] text-muted">
                Average customer rating
            </p>

        </div>



        {{-- Earnings --}}

        <div
            class="
                rounded-xl
                border
                border-line
                bg-surface
                px-5
                py-4
            "
        >

            <p class="text-xs font-medium text-muted">
                Total Earnings
            </p>

            <p class="mt-1.5 text-2xl font-bold tracking-tight text-ink">
                ₱28,450
            </p>

            <p class="mt-1 text-[11px] text-muted">
                From completed deliveries
            </p>

        </div>

    </section>



    {{-- =========================================================
        SEARCH / FILTER
    ========================================================== --}}

    <section
        class="
            rounded-xl
            border
            border-line
            bg-surface
            p-4
        "
    >

        <div
            class="
                flex
                flex-col
                gap-3
                lg:flex-row
                lg:items-end
            "
        >

            {{-- Search --}}

            <div class="flex-1">

                <label
                    for="history-search"
                    class="
                        mb-1.5
                        block
                        text-[10px]
                        font-bold
                        uppercase
                        tracking-wide
                        text-muted
                    "
                >
                    Search
                </label>

                <div class="relative">

                    <svg
                        viewBox="0 0 24 24"
                        class="
                            pointer-events-none
                            absolute
                            left-3
                            top-1/2
                            h-4
                            w-4
                            -translate-y-1/2
                            text-muted
                        "
                    >
                        <circle
                            cx="11"
                            cy="11"
                            r="7"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        ></circle>

                        <path
                            d="m16 16 5 5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        ></path>
                    </svg>

                    <input
                        id="history-search"
                        type="text"
                        placeholder="Search tracking number or buyer..."
                        class="
                            h-10
                            w-full
                            rounded-lg
                            border
                            border-line
                            bg-page
                            pl-9
                            pr-3
                            text-sm
                            text-ink
                            outline-none
                            transition
                            placeholder:text-muted
                            focus:border-primary
                        "
                    >

                </div>

            </div>



            {{-- Date --}}

            <div class="w-full lg:w-48">

                <label
                    for="history-date"
                    class="
                        mb-1.5
                        block
                        text-[10px]
                        font-bold
                        uppercase
                        tracking-wide
                        text-muted
                    "
                >
                    Date
                </label>

                <input
                    id="history-date"
                    type="date"
                    class="
                        h-10
                        w-full
                        rounded-lg
                        border
                        border-line
                        bg-page
                        px-3
                        text-sm
                        text-ink
                        outline-none
                        focus:border-primary
                    "
                >

            </div>



            {{-- Filter --}}

            <button
                type="button"
                id="filter-history-btn"
                class="
                    h-10
                    rounded-lg
                    bg-primary
                    px-5
                    text-xs
                    font-semibold
                    text-white
                    transition
                    hover:opacity-90
                "
            >
                Filter
            </button>

        </div>

    </section>



    {{-- =========================================================
        DELIVERY RECORDS
    ========================================================== --}}

    <section
        class="
            overflow-hidden
            rounded-2xl
            border
            border-line
            bg-surface
        "
    >


        {{-- Section Header --}}

        <div
            class="
                flex
                items-center
                justify-between
                border-b
                border-line
                px-5
                py-5
                sm:px-6
            "
        >

            <div>

                <h2 class="text-lg font-bold text-ink">
                    Completed Deliveries
                </h2>

                <p class="mt-1 text-xs text-muted">
                    Your previous successful deliveries.
                </p>

            </div>


            <span
                class="
                    hidden
                    rounded-full
                    bg-page-secondary
                    px-3
                    py-1.5
                    text-[10px]
                    font-semibold
                    text-muted
                    sm:inline-flex
                "
            >
                {{ count($history) }} Records
            </span>

        </div>



        {{-- =====================================================
            TABLE HEADER
        ====================================================== --}}

        <div
            class="
                hidden
                border-b
                border-line
                bg-page-secondary
                px-6
                py-3
                lg:grid
                lg:grid-cols-[1.3fr_1.1fr_1.2fr_110px_80px_100px_70px]
                lg:items-center
                lg:gap-4
            "
        >

            <span class="text-[10px] font-bold uppercase tracking-wide text-muted">
                Delivery
            </span>

            <span class="text-[10px] font-bold uppercase tracking-wide text-muted">
                Buyer
            </span>

            <span class="text-[10px] font-bold uppercase tracking-wide text-muted">
                Location
            </span>

            <span class="text-[10px] font-bold uppercase tracking-wide text-muted">
                Date
            </span>

            <span class="text-[10px] font-bold uppercase tracking-wide text-muted">
                Status
            </span>

            <span class="text-[10px] font-bold uppercase tracking-wide text-muted">
                Earnings
            </span>

            <span></span>

        </div>



        {{-- =====================================================
            RECORDS
        ====================================================== --}}

        <div
            id="history-list"
            class="divide-y divide-line"
        >


            @foreach($history as $record)

                <article
                    class="
                        history-record
                        px-5
                        py-5
                        transition
                        hover:bg-page-secondary
                        sm:px-6
                    "
                    data-tracking="{{ strtolower($record['tracking']) }}"
                    data-buyer="{{ strtolower($record['buyer']) }}"
                    data-date="{{ $record['date'] }}"
                >


                    {{-- DESKTOP --}}

                    <div
                        class="
                            hidden
                            lg:grid
                            lg:grid-cols-[1.3fr_1.1fr_1.2fr_110px_80px_100px_70px]
                            lg:items-center
                            lg:gap-4
                        "
                    >


                        {{-- Delivery --}}

                        <div>

                            <p class="text-sm font-bold text-ink">
                                {{ $record['tracking'] }}
                            </p>

                            <p class="mt-0.5 text-[11px] text-muted">
                                Delivery completed
                            </p>

                        </div>



                        {{-- Buyer --}}

                        <div class="min-w-0">

                            <p class="truncate text-sm font-semibold text-ink">
                                {{ $record['buyer'] }}
                            </p>

                            <p class="mt-0.5 text-[11px] text-muted">
                                Buyer
                            </p>

                        </div>



                        {{-- Location --}}

                        <div class="flex min-w-0 items-center gap-1.5">

                            <svg
                                viewBox="0 0 24 24"
                                class="h-3.5 w-3.5 shrink-0 text-muted"
                            >
                                <path
                                    d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                ></path>

                                <circle
                                    cx="12"
                                    cy="10"
                                    r="2"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                ></circle>
                            </svg>

                            <p class="truncate text-sm text-muted">
                                {{ $record['location'] }}
                            </p>

                        </div>



                        {{-- Date --}}

                        <p class="text-xs text-muted">
                            {{ $record['date'] }}
                        </p>



                        {{-- Status --}}

                        <span
                            class="
                                inline-flex
                                w-fit
                                items-center
                                gap-1.5
                                rounded-full
                                bg-success-soft
                                px-2.5
                                py-1
                                text-[10px]
                                font-semibold
                                text-success
                            "
                        >

                            <span class="h-1.5 w-1.5 rounded-full bg-success"></span>

                            Delivered

                        </span>



                        {{-- Earnings --}}

                        <p class="text-sm font-semibold text-ink">
                            {{ $record['earnings'] }}
                        </p>



                        {{-- View --}}

                        <a
                            href="{{ route('rider.deliveries.show', $record['tracking']) }}"
                            class="
                                inline-flex
                                h-8
                                items-center
                                justify-center
                                rounded-lg
                                border
                                border-line
                                px-3
                                text-[11px]
                                font-semibold
                                text-ink
                                transition
                                hover:bg-page-secondary
                            "
                        >
                            View
                        </a>

                    </div>



                    {{-- =================================================
                        MOBILE / TABLET
                    ================================================== --}}

                    <div class="lg:hidden">


                        <div class="flex items-start justify-between gap-3">

                            <div>

                                <p class="text-sm font-bold text-ink">
                                    {{ $record['tracking'] }}
                                </p>

                                <p class="mt-0.5 text-xs text-muted">
                                    {{ $record['date'] }}
                                </p>

                            </div>


                            <span
                                class="
                                    inline-flex
                                    items-center
                                    gap-1.5
                                    rounded-full
                                    bg-success-soft
                                    px-2.5
                                    py-1
                                    text-[10px]
                                    font-semibold
                                    text-success
                                "
                            >

                                <span class="h-1.5 w-1.5 rounded-full bg-success"></span>

                                Delivered

                            </span>

                        </div>



                        <div class="mt-4 grid gap-3 sm:grid-cols-3">


                            <div>

                                <p class="text-[10px] font-bold uppercase tracking-wide text-muted">
                                    Buyer
                                </p>

                                <p class="mt-1 text-sm font-semibold text-ink">
                                    {{ $record['buyer'] }}
                                </p>

                            </div>



                            <div>

                                <p class="text-[10px] font-bold uppercase tracking-wide text-muted">
                                    Location
                                </p>

                                <p class="mt-1 text-sm text-muted">
                                    {{ $record['location'] }}
                                </p>

                            </div>



                            <div>

                                <p class="text-[10px] font-bold uppercase tracking-wide text-muted">
                                    Earnings
                                </p>

                                <p class="mt-1 text-sm font-semibold text-ink">
                                    {{ $record['earnings'] }}
                                </p>

                            </div>

                        </div>



                        <a
                            href="{{ route('rider.deliveries.show', $record['tracking']) }}"
                            class="
                                mt-4
                                inline-flex
                                w-full
                                items-center
                                justify-center
                                rounded-lg
                                border
                                border-line
                                px-4
                                py-2.5
                                text-xs
                                font-semibold
                                text-ink
                            "
                        >
                            View Delivery
                        </a>

                    </div>

                </article>

            @endforeach


            {{-- Empty State --}}

            <div
                id="history-empty"
                class="hidden px-6 py-12 text-center"
            >

                <div
                    class="
                        mx-auto
                        grid
                        h-10
                        w-10
                        place-items-center
                        rounded-xl
                        bg-page-secondary
                        text-muted
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5 fill-none stroke-current"
                    >
                        <circle
                            cx="11"
                            cy="11"
                            r="7"
                            stroke-width="1.6"
                        ></circle>

                        <path
                            d="m16 16 5 5"
                            stroke-width="1.6"
                        ></path>
                    </svg>

                </div>

                <p class="mt-3 text-sm font-semibold text-ink">
                    No delivery records found
                </p>

                <p class="mt-1 text-xs text-muted">
                    Try another tracking number or date.
                </p>

            </div>

        </div>

    </section>



    {{-- =========================================================
        MONTHLY PERFORMANCE
    ========================================================== --}}

    <section
        class="
            rounded-2xl
            border
            border-line
            bg-surface
            p-5
            sm:p-6
        "
    >

        <div>

            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">
                Performance
            </p>

            <h2 class="mt-1 text-lg font-bold text-ink">
                Monthly Performance
            </h2>

        </div>


        <div class="mt-5 grid gap-3 md:grid-cols-3">


            <div class="rounded-xl bg-page-secondary p-4">

                <p class="text-[11px] text-muted">
                    Deliveries
                </p>

                <p class="mt-1 text-xl font-bold text-ink">
                    86
                </p>

                <p class="mt-1 text-[10px] text-muted">
                    This month
                </p>

            </div>



            <div class="rounded-xl bg-page-secondary p-4">

                <p class="text-[11px] text-muted">
                    Average Rating
                </p>

                <div class="mt-1 flex items-center gap-1.5">

                    <p class="text-xl font-bold text-ink">
                        4.9
                    </p>

                    <span class="text-xs text-warning">
                        ★
                    </span>

                </div>

                <p class="mt-1 text-[10px] text-muted">
                    Customer rating
                </p>

            </div>



            <div class="rounded-xl bg-page-secondary p-4">

                <p class="text-[11px] text-muted">
                    Income
                </p>

                <p class="mt-1 text-xl font-bold text-primary">
                    ₱7,850
                </p>

                <p class="mt-1 text-[10px] text-muted">
                    This month
                </p>

            </div>

        </div>

    </section>


</div>



{{-- =============================================================
    JAVASCRIPT
============================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    const searchInput =
        document.getElementById('history-search');


    const dateInput =
        document.getElementById('history-date');


    const records =
        document.querySelectorAll('.history-record');


    const emptyState =
        document.getElementById('history-empty');


    function filterHistory() {

        const search =
            searchInput.value
                .trim()
                .toLowerCase();


        const selectedDate =
            dateInput.value;


        let visibleCount = 0;


        records.forEach(function (record) {

            const tracking =
                record.dataset.tracking || '';


            const buyer =
                record.dataset.buyer || '';


            const date =
                record.dataset.date || '';


            const matchesSearch =
                !search ||
                tracking.includes(search) ||
                buyer.includes(search);


            const matchesDate =
                !selectedDate ||
                new Date(date).toISOString().slice(0, 10) === selectedDate;


            if (matchesSearch && matchesDate) {

                record.classList.remove('hidden');

                visibleCount++;

            } else {

                record.classList.add('hidden');

            }

        });


        if (visibleCount === 0) {

            emptyState.classList.remove('hidden');

        } else {

            emptyState.classList.add('hidden');

        }

    }


    searchInput.addEventListener(
        'input',
        filterHistory
    );


    document
        .getElementById('filter-history-btn')
        .addEventListener(
            'click',
            filterHistory
        );



    /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('export-history-btn')
        .addEventListener('click', function () {

            const rows = [
                [
                    'Tracking Number',
                    'Buyer',
                    'Location',
                    'Date',
                    'Status',
                    'Rating',
                    'Earnings'
                ]
            ];


            records.forEach(function (record) {

                if (record.classList.contains('hidden')) {
                    return;
                }


                const tracking =
                    record.dataset.tracking;


                const buyer =
                    record.dataset.buyer;


                const original =
                    Array.from(
                        @json($history)
                    ).find(function (item) {

                        return item.tracking.toLowerCase() === tracking;

                    });


                if (!original) {
                    return;
                }


                rows.push([

                    original.tracking,
                    original.buyer,
                    original.location,
                    original.date,
                    'Delivered',
                    original.rating,
                    original.earnings

                ]);

            });


            const csv =
                rows
                    .map(function (row) {

                        return row
                            .map(function (value) {

                                return '"' +
                                    String(value)
                                        .replace(/"/g, '""') +
                                    '"';

                            })
                            .join(',');

                    })
                    .join('\n');


            const blob =
                new Blob(
                    [csv],
                    {
                        type: 'text/csv;charset=utf-8;'
                    }
                );


            const url =
                URL.createObjectURL(blob);


            const link =
                document.createElement('a');


            link.href = url;

            link.download =
                'likhae-delivery-history.csv';


            document.body.appendChild(link);

            link.click();

            document.body.removeChild(link);

            URL.revokeObjectURL(url);

        });

});

</script>

@endpush

@endsection