@extends('rider.app')

@section('title', 'Delivery Assignments — LIKHAE Rider')

@section('content')

@php

    $deliveries = [

        [
            'tracking' => 'LH-2026-1007',
            'buyer' => 'Juan Dela Cruz',
            'address' => '123 Main Street, Los Baños, Laguna',
            'status' => 'ASSIGNED_TO_RIDER',
            'payment' => '₱1,200',
        ],

        [
            'tracking' => 'LH-2026-1015',
            'buyer' => 'Maria Santos',
            'address' => 'Brgy. Real, Calamba, Laguna',
            'status' => 'SORTED',
            'payment' => '₱650',
        ],

        [
            'tracking' => 'LH-2026-1020',
            'buyer' => 'Carlo Reyes',
            'address' => 'Santa Rosa City, Laguna',
            'status' => 'OUT_FOR_DELIVERY',
            'payment' => '₱900',
        ],

        [
            'tracking' => 'LH-2026-1027',
            'buyer' => 'Angela Cruz',
            'address' => 'Biñan City, Laguna',
            'status' => 'ASSIGNED_TO_RIDER',
            'payment' => '₱780',
        ],

    ];


    $statusMap = [

        'ASSIGNED_TO_RIDER' => [
            'label' => 'Assigned',
            'class' => 'bg-primary-soft text-primary',
            'dot' => 'bg-primary',
        ],

        'SORTED' => [
            'label' => 'Ready for Delivery',
            'class' => 'bg-warning-soft text-warning',
            'dot' => 'bg-warning',
        ],

        'OUT_FOR_DELIVERY' => [
            'label' => 'Out for Delivery',
            'class' => 'bg-success-soft text-success',
            'dot' => 'bg-success',
        ],

        'DELIVERED' => [
            'label' => 'Delivered',
            'class' => 'bg-success-soft text-success',
            'dot' => 'bg-success',
        ],

        'DELIVERY_FAILED' => [
            'label' => 'Delivery Failed',
            'class' => 'bg-danger-soft text-danger',
            'dot' => 'bg-danger',
        ],

    ];

@endphp


<div class="mx-auto w-full max-w-[1270px] space-y-6">


    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <section class="border-b border-line pb-5">

        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

            <div>

                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">
                    Delivery Management
                </p>

                <h1 class="mt-2 text-[28px] font-bold tracking-tight text-ink sm:text-[30px]">
                    Delivery Assignments
                </h1>

                <p class="mt-1 text-xs text-muted">
                    Manage your assigned deliveries and customer drop-offs.
                </p>

            </div>


            {{-- RIDER STATUS --}}

            <div
                class="
                    inline-flex
                    w-fit
                    items-center
                    gap-2
                    rounded-full
                    bg-success-soft
                    px-4
                    py-2.5
                    text-[9px]
                    font-semibold
                    text-success
                "
            >

                <span class="relative flex h-1.5 w-1.5">

                    <span
                        class="
                            absolute
                            inline-flex
                            h-full
                            w-full
                            animate-ping
                            rounded-full
                            bg-success
                            opacity-40
                        "
                    ></span>

                    <span
                        class="
                            relative
                            inline-flex
                            h-1.5
                            w-1.5
                            rounded-full
                            bg-success
                        "
                    ></span>

                </span>

                Rider Online

            </div>

        </div>

    </section>



    {{-- =========================================================
        SUMMARY
    ========================================================== --}}

    <section class="grid gap-3 lg:grid-cols-3">


        {{-- READY FOR DELIVERY --}}

        <div class="rounded-xl border border-line bg-surface p-4">

            <div class="flex items-start justify-between gap-4">

                <div>

                    <p class="text-[9px] text-muted">
                        Ready for Delivery
                    </p>

                    <p class="mt-1 text-[22px] font-bold leading-none text-ink">
                        5
                    </p>

                    <p class="mt-2 text-[9px] text-muted">
                        Parcels waiting at sorting center.
                    </p>

                </div>


                <span
                    class="
                        rounded-full
                        bg-warning-soft
                        px-2.5
                        py-1
                        text-[8px]
                        font-semibold
                        text-warning
                    "
                >
                    Waiting
                </span>

            </div>

        </div>



        {{-- OUT FOR DELIVERY --}}

        <div class="rounded-xl border border-line bg-surface p-4">

            <div class="flex items-start justify-between gap-4">

                <div>

                    <p class="text-[9px] text-muted">
                        Out for Delivery
                    </p>

                    <p class="mt-1 text-[22px] font-bold leading-none text-ink">
                        3
                    </p>

                    <p class="mt-2 text-[9px] text-muted">
                        Parcels currently with you.
                    </p>

                </div>


                <span
                    class="
                        rounded-full
                        bg-success-soft
                        px-2.5
                        py-1
                        text-[8px]
                        font-semibold
                        text-success
                    "
                >
                    Active
                </span>

            </div>

        </div>



        {{-- DELIVERED --}}

        <div class="rounded-xl border border-line bg-surface p-4">

            <div class="flex items-start justify-between gap-4">

                <div>

                    <p class="text-[9px] text-muted">
                        Delivered Today
                    </p>

                    <p class="mt-1 text-[22px] font-bold leading-none text-ink">
                        12
                    </p>

                    <p class="mt-2 text-[9px] text-muted">
                        Successfully completed.
                    </p>

                </div>


                <span
                    class="
                        rounded-full
                        bg-success-soft
                        px-2.5
                        py-1
                        text-[8px]
                        font-semibold
                        text-success
                    "
                >
                    Completed
                </span>

            </div>

        </div>

    </section>



    {{-- =========================================================
        DELIVERY TASKS
    ========================================================== --}}

    <section class="overflow-hidden rounded-xl border border-line bg-surface">


        {{-- HEADER --}}

        <div
            class="
                flex
                flex-col
                gap-3
                border-b
                border-line
                px-5
                py-4
                sm:flex-row
                sm:items-center
                sm:justify-between
            "
        >

            <div>

                <h2 class="text-[15px] font-bold text-ink">
                    Today's Deliveries
                </h2>

                <p class="mt-1 text-[9px] text-muted">
                    Parcels currently assigned to you.
                </p>

            </div>


            <span class="rounded-full bg-page-secondary px-3 py-1.5 text-[8px] font-semibold text-muted">
                {{ count($deliveries) }} Current Tasks
            </span>

        </div>



        {{-- =====================================================
            DESKTOP COLUMN HEADER
        ====================================================== --}}

        <div
            class="
                hidden
                border-b
                border-line
                bg-page-secondary
                px-5
                py-3
                lg:grid
                lg:grid-cols-[1.5fr_1fr_1.25fr_190px]
                lg:items-center
                lg:gap-5
            "
        >

            <span class="text-[8px] font-bold uppercase tracking-[0.12em] text-muted">
                Delivery
            </span>

            <span class="text-[8px] font-bold uppercase tracking-[0.12em] text-muted">
                Customer
            </span>

            <span class="text-[8px] font-bold uppercase tracking-[0.12em] text-muted">
                Delivery Location
            </span>

            <span class="text-right text-[8px] font-bold uppercase tracking-[0.12em] text-muted">
                Action
            </span>

        </div>



        {{-- =====================================================
            DELIVERY ROWS
        ====================================================== --}}

        <div id="delivery-list" class="divide-y divide-line">

            @foreach($deliveries as $delivery)

                @php

                    $status = $statusMap[$delivery['status']]
                        ?? [
                            'label' => 'Unknown',
                            'class' => 'bg-page-secondary text-muted',
                            'dot' => 'bg-muted',
                        ];

                @endphp


                <div
                    id="delivery-{{ $delivery['tracking'] }}"
                    class="
                        delivery-row
                        px-5
                        py-4
                        transition
                        hover:bg-page-secondary
                    "
                    data-status="{{ $delivery['status'] }}"
                >


                    {{-- =================================================
                        DESKTOP
                    ================================================== --}}

                    <div
                        class="
                            hidden
                            lg:grid
                            lg:grid-cols-[1.5fr_1fr_1.25fr_190px]
                            lg:items-center
                            lg:gap-5
                        "
                    >


                        {{-- DELIVERY --}}

                        <div class="flex min-w-0 items-center gap-3">

                            <div
                                class="
                                    grid
                                    h-10
                                    w-10
                                    shrink-0
                                    place-items-center
                                    rounded-xl
                                    bg-primary-soft
                                    text-primary
                                "
                            >

                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-5 w-5 fill-none stroke-current"
                                >

                                    <path
                                        d="M21 8 12 3 3 8l9 5 9-5Z"
                                        stroke-width="1.6"
                                    />

                                    <path
                                        d="M3 8v8l9 5 9-5V8M12 13v8"
                                        stroke-width="1.6"
                                    />

                                </svg>

                            </div>


                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <p class="text-[11px] font-bold text-ink">
                                        {{ $delivery['tracking'] }}
                                    </p>


                                    <span
                                        class="
                                            inline-flex
                                            shrink-0
                                            items-center
                                            gap-1.5
                                            whitespace-nowrap
                                            rounded-full
                                            px-2.5
                                            py-1
                                            text-[8px]
                                            font-semibold
                                            leading-none
                                            {{ $status['class'] }}
                                        "
                                    >

                                        <span
                                            class="
                                                h-1.5
                                                w-1.5
                                                shrink-0
                                                rounded-full
                                                {{ $status['dot'] }}
                                            "
                                        ></span>

                                        {{ $status['label'] }}

                                    </span>

                                </div>


                                <p class="mt-1 text-[9px] text-muted">
                                    COD {{ $delivery['payment'] }}
                                </p>

                            </div>

                        </div>



                        {{-- CUSTOMER --}}

                        <div class="min-w-0">

                            <p class="text-[8px] uppercase tracking-wide text-muted">
                                Customer
                            </p>

                            <p class="mt-1 truncate text-[10px] font-semibold text-ink">
                                {{ $delivery['buyer'] }}
                            </p>

                        </div>



                        {{-- LOCATION --}}

                        <div class="min-w-0">

                            <p class="text-[8px] uppercase tracking-wide text-muted">
                                Delivery Location
                            </p>

                            <div class="mt-1 flex items-center gap-1.5">

                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-3.5 w-3.5 shrink-0 text-muted"
                                >

                                    <path
                                        d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                    />

                                    <circle
                                        cx="12"
                                        cy="10"
                                        r="2"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                    />

                                </svg>

                                <p class="truncate text-[9px] text-muted">
                                    {{ $delivery['address'] }}
                                </p>

                            </div>

                        </div>



                        {{-- ACTIONS --}}

                        <div class="flex items-center justify-end gap-2">


                            {{-- VIEW --}}

                            <a
                                href="{{ route('rider.deliveries.show', $delivery['tracking']) }}"
                                class="
                                    inline-flex
                                    h-8
                                    items-center
                                    justify-center
                                    rounded-lg
                                    border
                                    border-line
                                    px-3
                                    text-[8px]
                                    font-semibold
                                    text-ink
                                    transition
                                    hover:bg-page-secondary
                                "
                            >
                                View
                            </a>



                            {{-- TRACK IF ACTIVE --}}

                            @if($delivery['status'] === 'OUT_FOR_DELIVERY')

                                <a
                                    href="{{ route('rider.deliveries.tracking', $delivery['tracking']) }}"
                                    class="
                                        inline-flex
                                        h-8
                                        items-center
                                        justify-center
                                        rounded-lg
                                        bg-success
                                        px-3
                                        text-[8px]
                                        font-semibold
                                        text-white
                                        transition
                                        hover:opacity-90
                                    "
                                >
                                    Track
                                </a>


                            {{-- ACCEPT OTHERWISE --}}

                            @elseif(
                                $delivery['status'] === 'ASSIGNED_TO_RIDER' ||
                                $delivery['status'] === 'SORTED'
                            )

                                <button
                                    type="button"
                                    class="
                                        accept-delivery-btn
                                        inline-flex
                                        h-8
                                        items-center
                                        justify-center
                                        rounded-lg
                                        bg-primary
                                        px-3
                                        text-[8px]
                                        font-semibold
                                        text-white
                                        transition
                                        hover:opacity-90
                                        disabled:cursor-not-allowed
                                        disabled:opacity-60
                                    "
                                    data-tracking="{{ $delivery['tracking'] }}"
                                >
                                    Accept
                                </button>

                            @else

                                <span class="text-[8px] text-muted">
                                    —
                                </span>

                            @endif


                        </div>


                    </div>



                    {{-- =================================================
                        MOBILE
                    ================================================== --}}

                    <div class="lg:hidden">


                        <div class="flex items-start justify-between gap-3">


                            <div class="flex min-w-0 items-center gap-3">

                                <div
                                    class="
                                        grid
                                        h-10
                                        w-10
                                        shrink-0
                                        place-items-center
                                        rounded-xl
                                        bg-primary-soft
                                        text-primary
                                    "
                                >

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-5 w-5 fill-none stroke-current"
                                    >

                                        <path
                                            d="M21 8 12 3 3 8l9 5 9-5Z"
                                            stroke-width="1.6"
                                        />

                                        <path
                                            d="M3 8v8l9 5 9-5V8M12 13v8"
                                            stroke-width="1.6"
                                        />

                                    </svg>

                                </div>


                                <div class="min-w-0">

                                    <p class="truncate text-[11px] font-bold text-ink">
                                        {{ $delivery['tracking'] }}
                                    </p>

                                    <p class="mt-1 text-[9px] text-muted">
                                        COD {{ $delivery['payment'] }}
                                    </p>

                                </div>

                            </div>


                            <span
                                class="
                                    inline-flex
                                    shrink-0
                                    items-center
                                    gap-1.5
                                    whitespace-nowrap
                                    rounded-full
                                    px-2.5
                                    py-1.5
                                    text-[8px]
                                    font-semibold
                                    leading-none
                                    {{ $status['class'] }}
                                "
                            >

                                <span
                                    class="
                                        h-1.5
                                        w-1.5
                                        shrink-0
                                        rounded-full
                                        {{ $status['dot'] }}
                                    "
                                ></span>

                                {{ $status['label'] }}

                            </span>

                        </div>



                        <div class="mt-4 space-y-3">

                            <div>

                                <p class="text-[8px] uppercase tracking-wide text-muted">
                                    Customer
                                </p>

                                <p class="mt-1 text-[10px] font-semibold text-ink">
                                    {{ $delivery['buyer'] }}
                                </p>

                            </div>


                            <div>

                                <p class="text-[8px] uppercase tracking-wide text-muted">
                                    Delivery Location
                                </p>

                                <p class="mt-1 text-[9px] text-ink">
                                    {{ $delivery['address'] }}
                                </p>

                            </div>

                        </div>



                        <div class="mt-4 flex gap-2">


                            <a
                                href="{{ route('rider.deliveries.show', $delivery['tracking']) }}"
                                class="
                                    flex-1
                                    rounded-lg
                                    border
                                    border-line
                                    px-3
                                    py-2.5
                                    text-center
                                    text-[8px]
                                    font-semibold
                                    text-ink
                                "
                            >
                                View Details
                            </a>


                            @if($delivery['status'] === 'OUT_FOR_DELIVERY')

                                <a
                                    href="{{ route('rider.deliveries.tracking', $delivery['tracking']) }}"
                                    class="
                                        flex-1
                                        rounded-lg
                                        bg-success
                                        px-3
                                        py-2.5
                                        text-center
                                        text-[8px]
                                        font-semibold
                                        text-white
                                    "
                                >
                                    Track Delivery
                                </a>

                            @elseif(
                                $delivery['status'] === 'ASSIGNED_TO_RIDER' ||
                                $delivery['status'] === 'SORTED'
                            )

                                <button
                                    type="button"
                                    class="
                                        accept-delivery-btn
                                        flex-1
                                        rounded-lg
                                        bg-primary
                                        px-3
                                        py-2.5
                                        text-[8px]
                                        font-semibold
                                        text-white
                                        disabled:opacity-60
                                    "
                                    data-tracking="{{ $delivery['tracking'] }}"
                                >
                                    Accept Delivery
                                </button>

                            @endif

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </section>



    {{-- =========================================================
        DELIVERY WORKFLOW
    ========================================================== --}}

    <section class="rounded-xl border border-line bg-surface p-5">


        <p class="text-[9px] font-bold uppercase tracking-[0.16em] text-primary">
            Delivery Workflow
        </p>


        <h2 class="mt-1 text-[15px] font-bold text-ink">
            Delivery Process
        </h2>


        <p class="mt-1 text-[9px] text-muted">
            Follow each step to complete a successful delivery.
        </p>


        <div class="mt-4 grid gap-2.5 sm:grid-cols-2 xl:grid-cols-5">


            @foreach([

                [
                    'number' => '01',
                    'title' => 'Accept Delivery',
                    'text' => 'Accept the assigned parcel.'
                ],

                [
                    'number' => '02',
                    'title' => 'Pickup',
                    'text' => 'Collect the parcel from sorting.'
                ],

                [
                    'number' => '03',
                    'title' => 'Out for Delivery',
                    'text' => 'Travel to the customer.'
                ],

                [
                    'number' => '04',
                    'title' => 'Deliver',
                    'text' => 'Hand over the parcel.'
                ],

                [
                    'number' => '05',
                    'title' => 'Completed',
                    'text' => 'Confirm successful delivery.'
                ]

            ] as $step)

                <div class="rounded-lg border border-line bg-page-secondary p-3.5">

                    <div class="flex items-center gap-2.5">

                        <span
                            class="
                                grid
                                h-8
                                w-8
                                shrink-0
                                place-items-center
                                rounded-lg
                                bg-primary
                                text-[8px]
                                font-bold
                                text-white
                            "
                        >
                            {{ $step['number'] }}
                        </span>


                        <p class="text-[9px] font-semibold text-ink">
                            {{ $step['title'] }}
                        </p>

                    </div>


                    <p class="mt-2.5 text-[8px] leading-4 text-muted">
                        {{ $step['text'] }}
                    </p>

                </div>

            @endforeach

        </div>

    </section>


</div>

@endsection