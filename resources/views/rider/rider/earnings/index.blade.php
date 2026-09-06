@extends('rider.app')

@section('title', 'Earnings — LIKHAE Rider')

@section('content')

@php

    $weeklyEarnings = [
        ['day' => 'Mon', 'amount' => 650],
        ['day' => 'Tue', 'amount' => 900],
        ['day' => 'Wed', 'amount' => 750],
        ['day' => 'Thu', 'amount' => 1200],
        ['day' => 'Fri', 'amount' => 980],
        ['day' => 'Sat', 'amount' => 620],
        ['day' => 'Sun', 'amount' => 850],
    ];


    $earningsHistory = [
        [
            'date' => 'Sep 03, 2026',
            'deliveries' => '8 Deliveries',
            'amount' => '₱850',
            'status' => 'Paid',
        ],

        [
            'date' => 'Sep 02, 2026',
            'deliveries' => '10 Deliveries',
            'amount' => '₱1,200',
            'status' => 'Paid',
        ],

        [
            'date' => 'Sep 01, 2026',
            'deliveries' => '6 Deliveries',
            'amount' => '₱700',
            'status' => 'Pending',
        ],

        [
            'date' => 'Aug 31, 2026',
            'deliveries' => '9 Deliveries',
            'amount' => '₱980',
            'status' => 'Paid',
        ],
    ];


    $incentives = [
        [
            'title' => 'Daily Target',
            'amount' => '₱300 Bonus',
            'description' => 'Complete your daily delivery target.',
        ],

        [
            'title' => 'Perfect Rating',
            'amount' => '₱500 Bonus',
            'description' => 'Maintain a perfect customer rating.',
        ],

        [
            'title' => '100 Deliveries',
            'amount' => '₱1,000 Bonus',
            'description' => 'Complete 100 successful deliveries.',
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
                Income Management
            </p>

            <h1 class="mt-1.5 text-[28px] font-bold leading-tight tracking-tight text-ink">
                My Earnings
            </h1>

            <p class="mt-1.5 text-sm text-muted">
                Track your delivery income, incentives, and payout history.
            </p>

        </div>


        {{-- Request Payout --}}

        <button
            type="button"
            id="request-payout-btn"
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
                disabled:cursor-not-allowed
                disabled:opacity-60
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

            <span id="payout-button-text">
                Request Payout
            </span>

        </button>

    </section>



    {{-- =========================================================
        SUMMARY
    ========================================================== --}}

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">


        {{-- Today --}}

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
                Today
            </p>

            <p class="mt-1.5 text-2xl font-bold tracking-tight text-ink">
                ₱850
            </p>

            <p class="mt-1 text-[11px] text-success">
                +8% from yesterday
            </p>

        </div>



        {{-- Week --}}

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
                This Week
            </p>

            <p class="mt-1.5 text-2xl font-bold tracking-tight text-ink">
                ₱5,420
            </p>

            <p class="mt-1 text-[11px] text-success">
                +12% from last week
            </p>

        </div>



        {{-- Month --}}

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
                This Month
            </p>

            <p class="mt-1.5 text-2xl font-bold tracking-tight text-ink">
                ₱28,450
            </p>

            <p class="mt-1 text-[11px] text-success">
                +15% from last month
            </p>

        </div>



        {{-- Total --}}

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
                ₱142,800
            </p>

            <p class="mt-1 text-[11px] text-muted">
                Lifetime earnings
            </p>

        </div>

    </section>



    {{-- =========================================================
        WEEKLY EARNINGS + PAYOUT
    ========================================================== --}}

    <section class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_280px]">


        {{-- =====================================================
            WEEKLY EARNINGS
        ====================================================== --}}

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

            <div class="flex items-start justify-between gap-4">

                <div>

                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">
                        Weekly Overview
                    </p>

                    <h2 class="mt-1 text-lg font-bold text-ink">
                        Weekly Earnings
                    </h2>

                    <p class="mt-1 text-xs text-muted">
                        Your delivery income for this week.
                    </p>

                </div>


                <span
                    class="
                        inline-flex
                        shrink-0
                        items-center
                        rounded-full
                        bg-success-soft
                        px-2.5
                        py-1
                        text-[10px]
                        font-semibold
                        text-success
                    "
                >
                    +12%
                </span>

            </div>



            {{-- Chart --}}

            <div class="mt-6">

                <div
                    class="
                        flex
                        h-[220px]
                        items-end
                        gap-2
                        border-b
                        border-line
                        px-1
                        sm:gap-4
                    "
                >

                    @foreach($weeklyEarnings as $day)

                        @php
                            $height = max(
                                24,
                                ($day['amount'] / 1200) * 150
                            );
                        @endphp


                        <div
                            class="
                                flex
                                h-full
                                min-w-0
                                flex-1
                                flex-col
                                items-center
                                justify-end
                                gap-2
                            "
                        >

                            {{-- Amount --}}

                            <span
                                class="
                                    text-[10px]
                                    font-medium
                                    text-muted
                                "
                            >
                                ₱{{ number_format($day['amount']) }}
                            </span>


                            {{-- Bar --}}

                            <div
                                class="
                                    w-full
                                    max-w-[42px]
                                    rounded-t-lg
                                    bg-primary
                                    transition
                                    hover:opacity-80
                                "
                                style="height: {{ $height }}px"
                            ></div>


                            {{-- Day --}}

                            <span
                                class="
                                    pb-2
                                    text-[10px]
                                    font-medium
                                    text-muted
                                "
                            >
                                {{ $day['day'] }}
                            </span>

                        </div>

                    @endforeach

                </div>

            </div>

        </section>



        {{-- =====================================================
            PAYOUT STATUS
        ====================================================== --}}

        <aside
            class="
                rounded-2xl
                border
                border-line
                bg-surface
                p-5
            "
        >

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">
                        Payout
                    </p>

                    <h2 class="mt-1 text-lg font-bold text-ink">
                        Payout Status
                    </h2>

                </div>


                <div
                    class="
                        grid
                        h-9
                        w-9
                        place-items-center
                        rounded-lg
                        bg-success-soft
                        text-success
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-4 w-4 fill-none stroke-current"
                    >
                        <path
                            d="M12 3v18"
                            stroke-width="1.6"
                        ></path>

                        <path
                            d="M17 7H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"
                            stroke-width="1.6"
                        ></path>
                    </svg>

                </div>

            </div>



            {{-- Balance --}}

            <div
                class="
                    mt-5
                    rounded-xl
                    bg-success-soft
                    p-4
                "
            >

                <p class="text-[10px] font-medium uppercase tracking-wide text-success">
                    Available Balance
                </p>

                <p class="mt-1.5 text-2xl font-bold tracking-tight text-ink">
                    ₱8,450
                </p>

                <p class="mt-1 text-[10px] text-success">
                    Available for payout
                </p>

            </div>



            {{-- Payout Details --}}

            <div class="mt-5 space-y-3">


                <div class="flex items-center justify-between gap-4">

                    <span class="text-xs text-muted">
                        Last Payout
                    </span>

                    <span class="text-xs font-semibold text-ink">
                        ₱7,800
                    </span>

                </div>



                <div class="flex items-center justify-between gap-4">

                    <span class="text-xs text-muted">
                        Status
                    </span>

                    <span
                        class="
                            rounded-full
                            bg-success-soft
                            px-2.5
                            py-1
                            text-[10px]
                            font-semibold
                            text-success
                        "
                    >
                        Completed
                    </span>

                </div>



                <div class="flex items-center justify-between gap-4">

                    <span class="text-xs text-muted">
                        Schedule
                    </span>

                    <span class="text-xs font-semibold text-ink">
                        Every Friday
                    </span>

                </div>

            </div>



            <div class="mt-5 border-t border-line pt-4">

                <p class="text-[10px] leading-4 text-muted">
                    Payouts are processed every Friday for eligible balances.
                </p>

            </div>

        </aside>

    </section>



    {{-- =========================================================
        INCENTIVES
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
                Rewards
            </p>

            <h2 class="mt-1 text-lg font-bold text-ink">
                Delivery Incentives
            </h2>

            <p class="mt-1 text-xs text-muted">
                Additional rewards based on your delivery performance.
            </p>

        </div>



        <div class="mt-5 grid gap-3 md:grid-cols-3">


            @foreach($incentives as $bonus)

                <div
                    class="
                        rounded-xl
                        border
                        border-line
                        bg-page-secondary
                        p-4
                        transition
                        hover:border-primary/30
                    "
                >

                    <div class="flex items-start justify-between gap-3">

                        <div>

                            <h3 class="text-sm font-semibold text-ink">
                                {{ $bonus['title'] }}
                            </h3>

                            <p class="mt-1 text-[11px] leading-4 text-muted">
                                {{ $bonus['description'] }}
                            </p>

                        </div>


                        <span
                            class="
                                shrink-0
                                rounded-full
                                bg-primary-soft
                                px-2.5
                                py-1
                                text-[10px]
                                font-semibold
                                text-primary
                            "
                        >
                            Bonus
                        </span>

                    </div>


                    <p class="mt-4 text-base font-bold text-primary">
                        {{ $bonus['amount'] }}
                    </p>

                </div>

            @endforeach

        </div>

    </section>



    {{-- =========================================================
        EARNINGS HISTORY
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

        {{-- Header --}}

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
                    Earnings History
                </h2>

                <p class="mt-1 text-xs text-muted">
                    Recent delivery earnings and payout records.
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
                {{ count($earningsHistory) }} Records
            </span>

        </div>



        {{-- Desktop Header --}}

        <div
            class="
                hidden
                border-b
                border-line
                bg-page-secondary
                px-6
                py-3
                md:grid
                md:grid-cols-[1fr_1fr_120px_100px]
                md:items-center
                md:gap-4
            "
        >

            <span class="text-[10px] font-bold uppercase tracking-wide text-muted">
                Date
            </span>

            <span class="text-[10px] font-bold uppercase tracking-wide text-muted">
                Deliveries
            </span>

            <span class="text-[10px] font-bold uppercase tracking-wide text-muted">
                Earnings
            </span>

            <span class="text-[10px] font-bold uppercase tracking-wide text-muted">
                Status
            </span>

        </div>



        {{-- Records --}}

        <div class="divide-y divide-line">

            @foreach($earningsHistory as $earning)

                <article
                    class="
                        px-5
                        py-4
                        transition
                        hover:bg-page-secondary
                        sm:px-6
                    "
                >


                    {{-- Desktop --}}

                    <div
                        class="
                            hidden
                            md:grid
                            md:grid-cols-[1fr_1fr_120px_100px]
                            md:items-center
                            md:gap-4
                        "
                    >

                        <div>

                            <p class="text-sm font-semibold text-ink">
                                {{ $earning['date'] }}
                            </p>

                            <p class="mt-0.5 text-[11px] text-muted">
                                Delivery income
                            </p>

                        </div>


                        <p class="text-sm text-muted">
                            {{ $earning['deliveries'] }}
                        </p>


                        <p class="text-sm font-semibold text-ink">
                            {{ $earning['amount'] }}
                        </p>


                        <span
                            class="
                                w-fit
                                rounded-full
                                px-2.5
                                py-1
                                text-[10px]
                                font-semibold
                                {{ $earning['status'] === 'Paid'
                                    ? 'bg-success-soft text-success'
                                    : 'bg-warning-soft text-warning'
                                }}
                            "
                        >
                            {{ $earning['status'] }}
                        </span>

                    </div>



                    {{-- Mobile --}}

                    <div class="md:hidden">

                        <div class="flex items-start justify-between gap-3">

                            <div>

                                <p class="text-sm font-semibold text-ink">
                                    {{ $earning['date'] }}
                                </p>

                                <p class="mt-1 text-xs text-muted">
                                    {{ $earning['deliveries'] }}
                                </p>

                            </div>


                            <span
                                class="
                                    shrink-0
                                    rounded-full
                                    px-2.5
                                    py-1
                                    text-[10px]
                                    font-semibold
                                    {{ $earning['status'] === 'Paid'
                                        ? 'bg-success-soft text-success'
                                        : 'bg-warning-soft text-warning'
                                    }}
                                "
                            >
                                {{ $earning['status'] }}
                            </span>

                        </div>


                        <div class="mt-3 flex items-center justify-between">

                            <span class="text-xs text-muted">
                                Earnings
                            </span>

                            <span class="text-sm font-bold text-ink">
                                {{ $earning['amount'] }}
                            </span>

                        </div>

                    </div>


                </article>

            @endforeach

        </div>

    </section>



    {{-- =========================================================
        EARNINGS NOTE
    ========================================================== --}}

    <div
        class="
            flex
            items-start
            gap-3
            rounded-xl
            border
            border-line
            bg-page-secondary
            px-4
            py-3
        "
    >

        <div
            class="
                mt-0.5
                grid
                h-7
                w-7
                shrink-0
                place-items-center
                rounded-lg
                bg-primary-soft
                text-primary
            "
        >

            <svg
                viewBox="0 0 24 24"
                class="h-3.5 w-3.5 fill-none stroke-current"
            >
                <circle
                    cx="12"
                    cy="12"
                    r="9"
                    stroke-width="1.6"
                ></circle>

                <path
                    d="M12 11v5"
                    stroke-width="1.6"
                ></path>

                <circle
                    cx="12"
                    cy="8"
                    r=".8"
                    fill="currentColor"
                    stroke="none"
                ></circle>
            </svg>

        </div>


        <p class="text-xs leading-5 text-muted">
            Earnings are calculated from completed deliveries and eligible incentives.
            Payout amounts may vary depending on completed delivery records.
        </p>

    </div>


</div>



{{-- =============================================================
    JAVASCRIPT
============================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Request Payout
    |--------------------------------------------------------------------------
    */

    const payoutButton =
        document.getElementById('request-payout-btn');

    const payoutButtonText =
        document.getElementById('payout-button-text');


    if (payoutButton) {

        payoutButton.addEventListener('click', function () {

            if (this.disabled) {
                return;
            }


            this.disabled = true;

            payoutButtonText.textContent =
                'Processing...';


            setTimeout(function () {

                payoutButtonText.textContent =
                    'Payout Requested';


                payoutButton.classList.remove(
                    'bg-primary'
                );


                payoutButton.classList.add(
                    'bg-success'
                );


                setTimeout(function () {

                    payoutButtonText.textContent =
                        'Request Payout';

                    payoutButton.classList.remove(
                        'bg-success'
                    );

                    payoutButton.classList.add(
                        'bg-primary'
                    );

                    payoutButton.disabled = false;

                }, 1800);

            }, 700);

        });

    }

});

</script>

@endpush

@endsection