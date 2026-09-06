@extends('logistics.app')

@section('title', 'Dashboard — LIKHAE Logistics')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | FRONTEND SAMPLE DATA
    |--------------------------------------------------------------------------
    | Temporary only.
    | Later these values will come from controllers / database.
    */

    $stats = [
        [
            'label' => 'Received Today',
            'value' => 128,
            'description' => 'Incoming parcels',
            'change' => '+12%',
            'type' => 'primary',
        ],
        [
            'label' => 'Sorting Queue',
            'value' => 34,
            'description' => 'Waiting for sorting',
            'change' => '8 urgent',
            'type' => 'warning',
        ],
        [
            'label' => 'Awaiting Rider',
            'value' => 18,
            'description' => 'Ready for assignment',
            'change' => '14 riders online',
            'type' => 'info',
        ],
        [
            'label' => 'On the Road',
            'value' => 76,
            'description' => 'Out for delivery',
            'change' => '91% on time',
            'type' => 'success',
        ],
    ];


    $recentParcels = [
        [
            'id' => 1001,
            'tracking' => 'LH-2026-1001',
            'buyer' => 'Juan Dela Cruz',
            'destination' => 'Santa Cruz',
            'area' => 'Unassigned',
            'status' => 'Waiting for Sorting',
            'status_type' => 'warning',
            'time' => '10:32 AM',
        ],
        [
            'id' => 1002,
            'tracking' => 'LH-2026-1002',
            'buyer' => 'Maria Santos',
            'destination' => 'Pagsanjan',
            'area' => 'Area B',
            'status' => 'Awaiting Rider',
            'status_type' => 'info',
            'time' => '10:15 AM',
        ],
        [
            'id' => 1003,
            'tracking' => 'LH-2026-1003',
            'buyer' => 'Ana Reyes',
            'destination' => 'Los Baños',
            'area' => 'Area C',
            'status' => 'Rider Assigned',
            'status_type' => 'primary',
            'time' => '9:48 AM',
        ],
        [
            'id' => 1004,
            'tracking' => 'LH-2026-1004',
            'buyer' => 'Carlo Mendoza',
            'destination' => 'Calamba',
            'area' => 'Area D',
            'status' => 'Out for Delivery',
            'status_type' => 'success',
            'time' => '8:20 AM',
        ],
        [
            'id' => 1005,
            'tracking' => 'LH-2026-1005',
            'buyer' => 'Sofia Garcia',
            'destination' => 'Santa Cruz',
            'area' => 'Area A',
            'status' => 'Delivered',
            'status_type' => 'success',
            'time' => '7:55 AM',
        ],
    ];


    $areas = [
        [
            'code' => 'A',
            'area' => 'Area A',
            'municipality' => 'Santa Cruz',
            'available' => 3,
            'total' => 4,
        ],
        [
            'code' => 'B',
            'area' => 'Area B',
            'municipality' => 'Pagsanjan',
            'available' => 2,
            'total' => 3,
        ],
        [
            'code' => 'C',
            'area' => 'Area C',
            'municipality' => 'Los Baños',
            'available' => 5,
            'total' => 6,
        ],
        [
            'code' => 'D',
            'area' => 'Area D',
            'municipality' => 'Calamba',
            'available' => 4,
            'total' => 5,
        ],
    ];


    $activity = [
        [
            'title' => 'Parcel received',
            'description' => 'LH-2026-1018 entered the sorting center.',
            'time' => '2 min ago',
            'type' => 'primary',
        ],
        [
            'title' => 'Rider assigned',
            'description' => 'Rider 03 assigned to LH-2026-1003.',
            'time' => '8 min ago',
            'type' => 'info',
        ],
        [
            'title' => 'Parcel dispatched',
            'description' => 'LH-2026-1004 is now out for delivery.',
            'time' => '15 min ago',
            'type' => 'success',
        ],
        [
            'title' => 'Sorting completed',
            'description' => 'LH-2026-1002 sorted under Area B.',
            'time' => '24 min ago',
            'type' => 'warning',
        ],
    ];

@endphp


<div class="flex w-full flex-col gap-6">

    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <section
        class="
            flex
            flex-col
            gap-5

            lg:flex-row
            lg:items-center
            lg:justify-between
        "
    >

        <div>

            <div class="mb-1 flex items-center gap-2">

                <span
                    class="
                        text-[11px]
                        font-semibold
                        uppercase
                        tracking-[0.16em]
                        text-primary
                    "
                >
                    Dashboard
                </span>

                <span class="h-1 w-1 rounded-full bg-line-strong"></span>

                <span class="text-[11px] text-muted">
                    Logistics Center
                </span>

            </div>


            <h1
                class="
                    font-display
                    text-[28px]
                    font-semibold
                    tracking-[-0.035em]
                    text-ink

                    sm:text-[32px]
                "
            >
                Logistics Overview
            </h1>


            <p
                class="
                    mt-1
                    text-[13px]
                    leading-6
                    text-muted
                "
            >
                Welcome back. Here's what's happening across the sorting center today.
            </p>

        </div>


        <div
            class="
                flex
                flex-col
                gap-2

                sm:flex-row
            "
        >

            <a
                href="{{ route('logistics.parcels.tracking') }}"
                class="
                    inline-flex
                    h-10
                    items-center
                    justify-center
                    gap-2

                    rounded-lg

                    border
                    border-line

                    bg-surface

                    px-4

                    text-[12px]
                    font-semibold
                    text-ink

                    shadow-sm

                    transition

                    hover:bg-surface-hover
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.7]
                    "
                >
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5"></path>
                </svg>

                Track Parcel

            </a>


            <a
                href="{{ route('logistics.parcels.receive') }}"
                class="
                    inline-flex
                    h-10
                    items-center
                    justify-center
                    gap-2

                    rounded-lg

                    bg-primary

                    px-4

                    text-[12px]
                    font-semibold
                    text-white

                    shadow-sm

                    transition

                    hover:bg-primary-hover
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.8]
                    "
                >
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>

                Receive Parcel

            </a>

        </div>

    </section>


    {{-- =====================================================
        STAT CARDS
    ====================================================== --}}

    <section
        class="
            grid
            gap-3

            sm:grid-cols-2
            xl:grid-cols-4
        "
    >

        @foreach ($stats as $stat)

            @php

                $iconClasses = match($stat['type']) {
                    'success' => 'bg-success-soft text-success',
                    'warning' => 'bg-warning-soft text-warning',
                    'info' => 'bg-info-soft text-info',
                    default => 'bg-primary-soft text-primary',
                };

                $changeClasses = match($stat['type']) {
                    'success' => 'text-success',
                    'warning' => 'text-warning',
                    'info' => 'text-info',
                    default => 'text-primary',
                };

            @endphp


            <article
                class="
                    rounded-xl

                    border
                    border-line

                    bg-surface

                    p-5

                    shadow-sm

                    transition

                    hover:-translate-y-0.5
                    hover:shadow-likhae
                "
            >

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <span
                            class="
                                text-[11px]
                                font-medium
                                text-muted
                            "
                        >
                            {{ $stat['label'] }}
                        </span>


                        <strong
                            class="
                                mt-2
                                block

                                text-[28px]
                                font-bold
                                leading-none
                                tracking-[-0.04em]
                                text-ink
                            "
                        >
                            {{ $stat['value'] }}
                        </strong>

                    </div>


                    <span
                        class="
                            grid
                            h-10
                            w-10
                            shrink-0
                            place-items-center

                            rounded-lg

                            {{ $iconClasses }}
                        "
                    >

                        @if($stat['type'] === 'primary')

                            <svg
                                viewBox="0 0 24 24"
                                class="
                                    h-[18px]
                                    w-[18px]

                                    fill-none
                                    stroke-current
                                    stroke-[1.7]
                                "
                            >
                                <path d="M21 8 12 3 3 8l9 5 9-5Z"></path>
                                <path d="M3 8v8l9 5 9-5V8"></path>
                            </svg>

                        @elseif($stat['type'] === 'warning')

                            <svg
                                viewBox="0 0 24 24"
                                class="
                                    h-[18px]
                                    w-[18px]

                                    fill-none
                                    stroke-current
                                    stroke-[1.7]
                                "
                            >
                                <path d="M8 3v18"></path>
                                <path d="m4 7 4-4 4 4"></path>
                                <path d="M16 21V3"></path>
                                <path d="m12 17 4 4 4-4"></path>
                            </svg>

                        @elseif($stat['type'] === 'info')

                            <svg
                                viewBox="0 0 24 24"
                                class="
                                    h-[18px]
                                    w-[18px]

                                    fill-none
                                    stroke-current
                                    stroke-[1.7]
                                "
                            >
                                <circle cx="8" cy="7" r="3"></circle>
                                <path d="M3 19c0-3 2-5 5-5"></path>
                                <path d="M13 13h8"></path>
                                <path d="m18 9 4 4-4 4"></path>
                            </svg>

                        @else

                            <svg
                                viewBox="0 0 24 24"
                                class="
                                    h-[18px]
                                    w-[18px]

                                    fill-none
                                    stroke-current
                                    stroke-[1.7]
                                "
                            >
                                <path d="M4 17h3"></path>
                                <path d="M17 17h3"></path>
                                <path d="M6 17 8 9h8l2 8"></path>
                                <circle cx="8" cy="17" r="2"></circle>
                                <circle cx="16" cy="17" r="2"></circle>
                            </svg>

                        @endif

                    </span>

                </div>


                <div
                    class="
                        mt-5

                        flex
                        items-center
                        justify-between
                        gap-3

                        border-t
                        border-line

                        pt-3
                    "
                >

                    <span class="text-[10px] text-muted">
                        {{ $stat['description'] }}
                    </span>


                    <span
                        class="
                            text-[10px]
                            font-semibold

                            {{ $changeClasses }}
                        "
                    >
                        {{ $stat['change'] }}
                    </span>

                </div>

            </article>

        @endforeach

    </section>


    {{-- =====================================================
        QUICK OPERATIONS
    ====================================================== --}}

    <section
        class="
            rounded-xl

            border
            border-line

            bg-surface

            p-5
        "
    >

        <div
            class="
                mb-4

                flex
                items-center
                justify-between
                gap-4
            "
        >

            <div>

                <h2
                    class="
                        text-[14px]
                        font-semibold
                        text-ink
                    "
                >
                    Quick Operations
                </h2>


                <p class="mt-0.5 text-[11px] text-muted">
                    Common logistics tasks.
                </p>

            </div>

        </div>


        <div
            class="
                grid
                gap-3

                sm:grid-cols-2
                xl:grid-cols-4
            "
        >

            {{-- RECEIVE --}}

            <a
                href="{{ route('logistics.parcels.receive') }}"
                class="
                    group

                    flex
                    items-center
                    gap-3

                    rounded-lg

                    border
                    border-line

                    bg-page-secondary

                    p-4

                    transition

                    hover:border-primary/30
                    hover:bg-primary-soft
                "
            >

                <span
                    class="
                        grid
                        h-10
                        w-10
                        shrink-0
                        place-items-center

                        rounded-lg

                        bg-primary-soft
                        text-primary

                        transition

                        group-hover:bg-primary
                        group-hover:text-white
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[18px] w-[18px] fill-none stroke-current stroke-[1.7]"
                    >
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>
                </span>


                <div class="min-w-0">

                    <strong class="block text-[12px] font-semibold text-ink">
                        Receive Parcel
                    </strong>

                    <span class="mt-0.5 block text-[10px] text-muted">
                        Scan incoming package
                    </span>

                </div>


                <span
                    class="
                        ml-auto
                        text-muted

                        transition

                        group-hover:translate-x-0.5
                        group-hover:text-primary
                    "
                >
                    →
                </span>

            </a>


            {{-- SORTING --}}

            <a
                href="{{ route('logistics.sorting') }}"
                class="
                    group

                    flex
                    items-center
                    gap-3

                    rounded-lg

                    border
                    border-line

                    bg-page-secondary

                    p-4

                    transition

                    hover:border-primary/30
                    hover:bg-primary-soft
                "
            >

                <span
                    class="
                        grid
                        h-10
                        w-10
                        shrink-0
                        place-items-center

                        rounded-lg

                        bg-warning-soft
                        text-warning
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[18px] w-[18px] fill-none stroke-current stroke-[1.7]"
                    >
                        <path d="M8 3v18"></path>
                        <path d="m4 7 4-4 4 4"></path>
                        <path d="M16 21V3"></path>
                        <path d="m12 17 4 4 4-4"></path>
                    </svg>
                </span>


                <div class="min-w-0">

                    <strong class="block text-[12px] font-semibold text-ink">
                        Sort Parcels
                    </strong>

                    <span class="mt-0.5 block text-[10px] text-muted">
                        34 parcels waiting
                    </span>

                </div>


                <span class="ml-auto text-muted transition group-hover:translate-x-0.5 group-hover:text-primary">
                    →
                </span>

            </a>


            {{-- ASSIGNMENT --}}

            <a
                href="{{ route('logistics.assignments') }}"
                class="
                    group

                    flex
                    items-center
                    gap-3

                    rounded-lg

                    border
                    border-line

                    bg-page-secondary

                    p-4

                    transition

                    hover:border-primary/30
                    hover:bg-primary-soft
                "
            >

                <span
                    class="
                        grid
                        h-10
                        w-10
                        shrink-0
                        place-items-center

                        rounded-lg

                        bg-info-soft
                        text-info
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[18px] w-[18px] fill-none stroke-current stroke-[1.7]"
                    >
                        <circle cx="8" cy="7" r="3"></circle>
                        <path d="M3 19c0-3 2-5 5-5"></path>
                        <path d="M13 13h8"></path>
                        <path d="m18 9 4 4-4 4"></path>
                    </svg>
                </span>


                <div class="min-w-0">

                    <strong class="block text-[12px] font-semibold text-ink">
                        Assign Riders
                    </strong>

                    <span class="mt-0.5 block text-[10px] text-muted">
                        18 awaiting assignment
                    </span>

                </div>


                <span class="ml-auto text-muted transition group-hover:translate-x-0.5 group-hover:text-primary">
                    →
                </span>

            </a>


            {{-- TRACKING --}}

            <a
                href="{{ route('logistics.parcels.tracking') }}"
                class="
                    group

                    flex
                    items-center
                    gap-3

                    rounded-lg

                    border
                    border-line

                    bg-page-secondary

                    p-4

                    transition

                    hover:border-primary/30
                    hover:bg-primary-soft
                "
            >

                <span
                    class="
                        grid
                        h-10
                        w-10
                        shrink-0
                        place-items-center

                        rounded-lg

                        bg-success-soft
                        text-success
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[18px] w-[18px] fill-none stroke-current stroke-[1.7]"
                    >
                        <circle cx="12" cy="12" r="8"></circle>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </span>


                <div class="min-w-0">

                    <strong class="block text-[12px] font-semibold text-ink">
                        Track Deliveries
                    </strong>

                    <span class="mt-0.5 block text-[10px] text-muted">
                        76 parcels on road
                    </span>

                </div>


                <span class="ml-auto text-muted transition group-hover:translate-x-0.5 group-hover:text-primary">
                    →
                </span>

            </a>

        </div>

    </section>


    {{-- =====================================================
        MAIN GRID
    ====================================================== --}}

    <section
        class="
            grid
            gap-4

            xl:grid-cols-[minmax(0,1.5fr)_minmax(300px,0.65fr)]
        "
    >

        {{-- =================================================
            RECENT PARCELS
        ================================================== --}}

        <div
            class="
                min-w-0
                overflow-hidden

                rounded-xl

                border
                border-line

                bg-surface
            "
        >

            <div
                class="
                    flex
                    items-center
                    justify-between
                    gap-4

                    border-b
                    border-line

                    px-5
                    py-4
                "
            >

                <div>

                    <h2 class="text-[14px] font-semibold text-ink">
                        Recent Parcels
                    </h2>

                    <p class="mt-0.5 text-[10px] text-muted">
                        Latest parcels processed by the sorting center.
                    </p>

                </div>


                <a
                    href="{{ route('logistics.parcels') }}"
                    class="
                        shrink-0

                        text-[11px]
                        font-semibold
                        text-primary

                        hover:text-primary-hover
                    "
                >
                    View All
                </a>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[760px]">

                    <thead>

                        <tr
                            class="
                                border-b
                                border-line

                                bg-page-secondary

                                text-left
                            "
                        >

                            <th
                                class="
                                    px-5
                                    py-3

                                    text-[9px]
                                    font-semibold
                                    uppercase
                                    tracking-[0.08em]
                                    text-muted
                                "
                            >
                                Parcel
                            </th>


                            <th
                                class="
                                    px-4
                                    py-3

                                    text-[9px]
                                    font-semibold
                                    uppercase
                                    tracking-[0.08em]
                                    text-muted
                                "
                            >
                                Customer
                            </th>


                            <th
                                class="
                                    px-4
                                    py-3

                                    text-[9px]
                                    font-semibold
                                    uppercase
                                    tracking-[0.08em]
                                    text-muted
                                "
                            >
                                Destination
                            </th>


                            <th
                                class="
                                    px-4
                                    py-3

                                    text-[9px]
                                    font-semibold
                                    uppercase
                                    tracking-[0.08em]
                                    text-muted
                                "
                            >
                                Status
                            </th>


                            <th
                                class="
                                    px-4
                                    py-3

                                    text-[9px]
                                    font-semibold
                                    uppercase
                                    tracking-[0.08em]
                                    text-muted
                                "
                            >
                                Time
                            </th>


                            <th class="px-5 py-3"></th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-line">

                        @foreach ($recentParcels as $parcel)

                            @php

                                $statusClass = match($parcel['status_type']) {
                                    'success' => 'bg-success-soft text-success',
                                    'warning' => 'bg-warning-soft text-warning',
                                    'info' => 'bg-info-soft text-info',
                                    default => 'bg-primary-soft text-primary',
                                };

                            @endphp


                            <tr
                                class="
                                    transition

                                    hover:bg-surface-hover
                                "
                            >

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <span
                                            class="
                                                grid
                                                h-9
                                                w-9
                                                shrink-0
                                                place-items-center

                                                rounded-lg

                                                bg-primary-soft

                                                text-primary
                                            "
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                class="h-4 w-4 fill-none stroke-current stroke-[1.6]"
                                            >
                                                <path d="M21 8 12 3 3 8l9 5 9-5Z"></path>
                                                <path d="M3 8v8l9 5 9-5V8"></path>
                                            </svg>
                                        </span>


                                        <div>

                                            <strong
                                                class="
                                                    block
                                                    text-[11px]
                                                    font-semibold
                                                    text-ink
                                                "
                                            >
                                                {{ $parcel['tracking'] }}
                                            </strong>


                                            <span class="mt-0.5 block text-[9px] text-muted">
                                                Parcel #{{ $parcel['id'] }}
                                            </span>

                                        </div>

                                    </div>

                                </td>


                                <td class="px-4 py-4">

                                    <span class="text-[11px] font-medium text-ink">
                                        {{ $parcel['buyer'] }}
                                    </span>

                                </td>


                                <td class="px-4 py-4">

                                    <strong class="block text-[11px] font-medium text-ink">
                                        {{ $parcel['destination'] }}
                                    </strong>

                                    <span class="mt-0.5 block text-[9px] text-muted">
                                        {{ $parcel['area'] }}
                                    </span>

                                </td>


                                <td class="px-4 py-4">

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-1.5

                                            rounded-full

                                            px-2.5
                                            py-1

                                            text-[9px]
                                            font-semibold

                                            {{ $statusClass }}
                                        "
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                        {{ $parcel['status'] }}

                                    </span>

                                </td>


                                <td class="px-4 py-4">

                                    <span class="text-[10px] text-muted">
                                        {{ $parcel['time'] }}
                                    </span>

                                </td>


                                <td class="px-5 py-4 text-right">

                                    <a
                                        href="{{ route(
                                            'logistics.parcels.show',
                                            $parcel['id']
                                        ) }}"
                                        class="
                                            inline-flex
                                            h-8
                                            items-center
                                            justify-center

                                            rounded-lg

                                            border
                                            border-line

                                            bg-surface

                                            px-3

                                            text-[9px]
                                            font-semibold
                                            text-ink

                                            transition

                                            hover:bg-primary
                                            hover:text-white
                                        "
                                    >
                                        View
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =================================================
            RIDER AVAILABILITY
        ================================================== --}}

        <aside
            class="
                overflow-hidden

                rounded-xl

                border
                border-line

                bg-surface
            "
        >

            <div
                class="
                    flex
                    items-center
                    justify-between
                    gap-3

                    border-b
                    border-line

                    px-5
                    py-4
                "
            >

                <div>

                    <h2 class="text-[14px] font-semibold text-ink">
                        Rider Availability
                    </h2>

                    <p class="mt-0.5 text-[10px] text-muted">
                        By delivery area
                    </p>

                </div>


                <span
                    class="
                        rounded-full

                        bg-success-soft

                        px-2.5
                        py-1

                        text-[9px]
                        font-semibold
                        text-success
                    "
                >
                    14 Online
                </span>

            </div>


            <div class="divide-y divide-line">

                @foreach ($areas as $area)

                    @php

                        $percentage =
                            ($area['available'] / $area['total']) * 100;

                    @endphp


                    <article class="px-5 py-4">

                        <div class="flex items-center gap-3">

                            <span
                                class="
                                    grid
                                    h-9
                                    w-9
                                    shrink-0
                                    place-items-center

                                    rounded-full

                                    bg-primary-soft

                                    text-[11px]
                                    font-bold
                                    text-primary
                                "
                            >
                                {{ $area['code'] }}
                            </span>


                            <div class="min-w-0 flex-1">

                                <div
                                    class="
                                        flex
                                        items-center
                                        justify-between
                                        gap-3
                                    "
                                >

                                    <div>

                                        <strong
                                            class="
                                                block
                                                text-[11px]
                                                font-semibold
                                                text-ink
                                            "
                                        >
                                            {{ $area['area'] }}
                                        </strong>


                                        <span
                                            class="
                                                mt-0.5
                                                block
                                                text-[9px]
                                                text-muted
                                            "
                                        >
                                            {{ $area['municipality'] }}
                                        </span>

                                    </div>


                                    <div class="text-right">

                                        <strong
                                            class="
                                                block
                                                text-[12px]
                                                font-bold
                                                text-ink
                                            "
                                        >
                                            {{ $area['available'] }}/{{ $area['total'] }}
                                        </strong>


                                        <span
                                            class="
                                                text-[8px]
                                                text-success
                                            "
                                        >
                                            available
                                        </span>

                                    </div>

                                </div>


                                <div
                                    class="
                                        mt-3
                                        h-1.5
                                        overflow-hidden
                                        rounded-full
                                        bg-page-secondary
                                    "
                                >

                                    <div
                                        class="
                                            h-full
                                            rounded-full
                                            bg-success
                                        "
                                        style="width: {{ $percentage }}%"
                                    ></div>

                                </div>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>


            <div
                class="
                    border-t
                    border-line

                    bg-page-secondary

                    px-5
                    py-3
                "
            >

                <a
                    href="{{ route('logistics.riders') }}"
                    class="
                        flex
                        items-center
                        justify-between

                        text-[10px]
                        font-semibold
                        text-primary
                    "
                >
                    Manage Riders

                    <span>→</span>
                </a>

            </div>

        </aside>

    </section>


    {{-- =====================================================
        LOWER GRID
    ====================================================== --}}

    <section
        class="
            grid
            gap-4

            lg:grid-cols-[minmax(0,1fr)_340px]
        "
    >

        {{-- PARCEL SCANNER --}}

        <div
            class="
                rounded-xl

                bg-primary

                p-5

                text-white

                shadow-likhae
            "
        >

            <div
                class="
                    flex
                    flex-col
                    gap-5

                    md:flex-row
                    md:items-center
                    md:justify-between
                "
            >

                <div>

                    <div class="flex items-center gap-2">

                        <span
                            class="
                                grid
                                h-8
                                w-8
                                place-items-center

                                rounded-lg

                                bg-white/10
                            "
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="
                                    h-4
                                    w-4

                                    fill-none
                                    stroke-current
                                    stroke-[1.6]
                                "
                            >
                                <path d="M3 5h4"></path>
                                <path d="M17 5h4"></path>
                                <path d="M3 19h4"></path>
                                <path d="M17 19h4"></path>
                                <path d="M7 3v18"></path>
                                <path d="M17 3v18"></path>
                                <path d="M11 3v18"></path>
                                <path d="M14 3v18"></path>
                            </svg>
                        </span>


                        <span
                            class="
                                text-[9px]
                                font-semibold
                                uppercase
                                tracking-[0.12em]
                                text-white/60
                            "
                        >
                            Quick Scanner
                        </span>

                    </div>


                    <h2
                        class="
                            mt-3
                            text-[18px]
                            font-semibold
                            tracking-[-0.02em]
                            text-white
                        "
                    >
                        Scan a parcel instantly
                    </h2>


                    <p
                        class="
                            mt-1
                            max-w-[420px]

                            text-[10px]
                            leading-5
                            text-white/60
                        "
                    >
                        Enter or scan a tracking number to quickly locate and process a parcel.
                    </p>

                </div>


                <div
                    class="
                        flex
                        w-full
                        flex-col
                        gap-2

                        sm:flex-row
                        md:max-w-[500px]
                    "
                >

                    <div class="relative flex-1">

                        <svg
                            viewBox="0 0 24 24"
                            class="
                                pointer-events-none

                                absolute
                                left-3.5
                                top-1/2

                                h-4
                                w-4

                                -translate-y-1/2

                                fill-none
                                stroke-[#8b817d]
                                stroke-[1.6]
                            "
                        >
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>


                        <input
                            type="text"
                            id="trackingNumber"
                            placeholder="LH-2026-1001"
                            value="LH-2026-1001"
                            class="
                                h-11
                                w-full

                                rounded-lg

                                border-0

                                bg-white

                                pl-10
                                pr-4

                                text-[11px]
                                text-[#211a19]

                                outline-none

                                placeholder:text-[#9f9691]

                                focus:ring-2
                                focus:ring-white/30
                            "
                        >

                    </div>


                    <button
                        type="button"
                        id="scanParcelBtn"
                        class="
                            inline-flex
                            h-11
                            items-center
                            justify-center
                            gap-2

                            rounded-lg

                            bg-white

                            px-4

                            text-[10px]
                            font-semibold
                            text-[#74181c]

                            transition

                            hover:bg-[#f7f3ec]
                        "
                    >
                        Scan Parcel

                        <span>→</span>
                    </button>

                </div>

            </div>

        </div>


        {{-- ACTIVITY --}}

        <aside
            class="
                overflow-hidden

                rounded-xl

                border
                border-line

                bg-surface
            "
        >

            <div
                class="
                    border-b
                    border-line

                    px-5
                    py-4
                "
            >
                <h2 class="text-[14px] font-semibold text-ink">
                    Recent Activity
                </h2>

                <p class="mt-0.5 text-[10px] text-muted">
                    Latest logistics updates
                </p>
            </div>


            <div class="px-5">

                @foreach ($activity as $item)

                    @php

                        $dotClass = match($item['type']) {
                            'success' => 'bg-success',
                            'warning' => 'bg-warning',
                            'info' => 'bg-info',
                            default => 'bg-primary',
                        };

                    @endphp


                    <article
                        class="
                            relative

                            flex
                            gap-3

                            border-b
                            border-line

                            py-4

                            last:border-b-0
                        "
                    >

                        <span
                            class="
                                mt-1.5

                                h-2
                                w-2
                                shrink-0

                                rounded-full

                                {{ $dotClass }}
                            "
                        ></span>


                        <div class="min-w-0">

                            <strong
                                class="
                                    block
                                    text-[10px]
                                    font-semibold
                                    text-ink
                                "
                            >
                                {{ $item['title'] }}
                            </strong>


                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    leading-4
                                    text-muted
                                "
                            >
                                {{ $item['description'] }}
                            </p>


                            <span
                                class="
                                    mt-1.5
                                    block
                                    text-[8px]
                                    text-muted-light
                                "
                            >
                                {{ $item['time'] }}
                            </span>

                        </div>

                    </article>

                @endforeach

            </div>

        </aside>

    </section>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const scanButton =
        document.getElementById('scanParcelBtn');

    const trackingInput =
        document.getElementById('trackingNumber');


    function scanParcel() {

        if (!trackingInput) {
            return;
        }


        const value =
            trackingInput.value.trim();


        if (!value) {

            trackingInput.focus();

            return;
        }


        window.location.href =
            "{{ route('logistics.parcels.tracking') }}"
            + '?tracking='
            + encodeURIComponent(value);

    }


    scanButton?.addEventListener(
        'click',
        scanParcel
    );


    trackingInput?.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Enter') {

                event.preventDefault();

                scanParcel();

            }

        }
    );

});
</script>

@endpush