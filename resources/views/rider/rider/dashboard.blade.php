@extends('rider.app')

@section('title', 'Rider Dashboard — LIKHAE')

@section('content')

@php

    $summaryCards = [
        [
            'title' => 'Pickup Assignments',
            'value' => '12',
            'description' => 'Parcels waiting for pickup',
            'type' => 'pickup',
        ],
        [
            'title' => 'Delivery Assignments',
            'value' => '8',
            'description' => 'Parcels to deliver',
            'type' => 'delivery',
        ],
        [
            'title' => 'Completed Today',
            'value' => '6',
            'description' => 'Successful deliveries',
            'type' => 'completed',
        ],
        [
            'title' => 'Today Earnings',
            'value' => '₱850',
            'description' => 'Delivery income',
            'type' => 'earnings',
        ],
    ];


    $tasks = [
        [
            'id' => 'LH-2026-1001',
            'type' => 'Pickup',
            'location' => 'ABC Crafts Store — Calamba',
            'status' => 'READY_FOR_PICKUP',
            'status_type' => 'pickup',
            'route' => 'rider.pickups.show',
        ],
        [
            'id' => 'LH-2026-1007',
            'type' => 'Delivery',
            'location' => 'Juan Dela Cruz — Los Baños',
            'status' => 'OUT_FOR_DELIVERY',
            'status_type' => 'delivery',
            'route' => 'rider.deliveries.show',
        ],
        [
            'id' => 'LH-2026-1011',
            'type' => 'Pickup',
            'location' => 'Maria Shop — Santa Cruz',
            'status' => 'READY_FOR_PICKUP',
            'status_type' => 'pickup',
            'route' => 'rider.pickups.show',
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
            sm:flex-row
            sm:items-end
            sm:justify-between
        "
    >

        <div>

            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-primary">
                Rider Dashboard
            </p>

            <h1 class="mt-1.5 text-[28px] font-bold leading-tight tracking-tight text-ink">
                Good morning, Juan
            </h1>

            <p class="mt-1.5 text-sm text-muted">
                Manage your pickup and delivery assignments for today.
            </p>

        </div>


        {{-- Online Status --}}

        <div
            class="
                inline-flex
                w-fit
                items-center
                gap-2
                rounded-full
                bg-success-soft
                px-3.5
                py-2
                text-xs
                font-semibold
                text-success
            "
        >

            <span class="h-2 w-2 rounded-full bg-success"></span>

            Rider Online

        </div>

    </section>



    {{-- =========================================================
        SUMMARY CARDS
    ========================================================== --}}

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

        @foreach($summaryCards as $card)

            <div
                class="
                    rounded-2xl
                    border
                    border-line
                    bg-surface
                    p-5
                    transition
                    duration-200
                    hover:-translate-y-0.5
                    hover:shadow-sm
                "
            >

                <div class="flex items-center justify-between">

                    <div
                        class="
                            grid
                            h-11
                            w-11
                            place-items-center
                            rounded-xl
                            bg-primary-soft
                            text-primary
                        "
                    >

                        @if($card['type'] === 'pickup')

                            <svg
                                viewBox="0 0 24 24"
                                class="h-5 w-5 fill-none stroke-current"
                            >
                                <path
                                    d="M21 8 12 3 3 8l9 5 9-5Z"
                                    stroke-width="1.5"
                                ></path>

                                <path
                                    d="M3 8v8l9 5 9-5V8M12 13v8"
                                    stroke-width="1.5"
                                ></path>
                            </svg>

                        @elseif($card['type'] === 'delivery')

                            <svg
                                viewBox="0 0 24 24"
                                class="h-5 w-5 fill-none stroke-current"
                            >
                                <path
                                    d="M4 17h11V5H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2Z"
                                    stroke-width="1.5"
                                ></path>

                                <path
                                    d="M15 9h4l3 3v5h-7V9Z"
                                    stroke-width="1.5"
                                ></path>

                                <circle
                                    cx="6"
                                    cy="19"
                                    r="2"
                                    stroke-width="1.5"
                                ></circle>

                                <circle
                                    cx="18"
                                    cy="19"
                                    r="2"
                                    stroke-width="1.5"
                                ></circle>
                            </svg>

                        @elseif($card['type'] === 'completed')

                            <svg
                                viewBox="0 0 24 24"
                                class="h-5 w-5 fill-none stroke-current"
                            >
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                    stroke-width="1.5"
                                ></circle>

                                <path
                                    d="m8.5 12 2.3 2.3 4.7-5"
                                    stroke-width="1.5"
                                ></path>
                            </svg>

                        @else

                            <svg
                                viewBox="0 0 24 24"
                                class="h-5 w-5 fill-none stroke-current"
                            >
                                <path
                                    d="M6 3h8a4 4 0 0 1 0 8H6z"
                                    stroke-width="1.5"
                                ></path>

                                <path
                                    d="M6 11h8a4 4 0 0 1 0 8H6z"
                                    stroke-width="1.5"
                                ></path>

                                <path
                                    d="M6 3v18M4 7h4M4 15h4"
                                    stroke-width="1.5"
                                ></path>
                            </svg>

                        @endif

                    </div>

                </div>


                <p class="mt-4 text-xs text-muted">
                    {{ $card['title'] }}
                </p>


                <p
                    class="
                        mt-1
                        text-[25px]
                        font-bold
                        leading-none
                        tracking-tight
                        text-ink
                    "
                >
                    {{ $card['value'] }}
                </p>


                <p class="mt-2 text-[11px] text-muted">
                    {{ $card['description'] }}
                </p>

            </div>

        @endforeach

    </section>



    {{-- =========================================================
        MAIN DASHBOARD
    ========================================================== --}}

    <section class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_300px]">


        {{-- =====================================================
            TODAY'S ASSIGNMENTS
        ====================================================== --}}

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
                    gap-4
                    border-b
                    border-line
                    px-5
                    py-4
                    sm:px-6
                "
            >

                <div>

                    <h2 class="text-base font-bold text-ink">
                        Today's Assignments
                    </h2>

                    <p class="mt-1 text-xs text-muted">
                        Your active courier tasks.
                    </p>

                </div>


                <a
                    href="{{ route('rider.pickups.index') }}"
                    class="
                        shrink-0
                        rounded-lg
                        bg-primary
                        px-3.5
                        py-2
                        text-xs
                        font-semibold
                        text-white
                        transition
                        hover:opacity-90
                    "
                >
                    View All
                </a>

            </div>


            {{-- Assignment List --}}

            <div class="divide-y divide-line">

                @foreach($tasks as $task)

                    <div
                        class="
                            flex
                            flex-col
                            gap-4
                            px-5
                            py-5
                            sm:px-6
                            lg:flex-row
                            lg:items-center
                            lg:justify-between
                        "
                    >

                        {{-- Task Details --}}

                        <div class="min-w-0">

                            <div class="flex flex-wrap items-center gap-2">

                                <h3 class="text-sm font-bold text-ink">
                                    {{ $task['id'] }}
                                </h3>


                                <span
                                    class="
                                        rounded-full
                                        px-2.5
                                        py-1
                                        text-[9px]
                                        font-semibold
                                        {{ $task['status_type'] === 'pickup'
                                            ? 'bg-warning-soft text-warning'
                                            : 'bg-primary-soft text-primary'
                                        }}
                                    "
                                >
                                    {{ $task['status'] }}
                                </span>

                            </div>


                            <p class="mt-2 text-xs font-medium text-muted">
                                {{ $task['type'] }}
                            </p>


                            <p class="mt-1 text-sm text-muted">
                                {{ $task['location'] }}
                            </p>

                        </div>


                        {{-- Action --}}

                        <a
                            href="{{ route($task['route'], $task['id']) }}"
                            class="
                                inline-flex
                                w-fit
                                shrink-0
                                items-center
                                justify-center
                                rounded-lg
                                bg-primary
                                px-4
                                py-2.5
                                text-xs
                                font-semibold
                                text-white
                                transition
                                hover:opacity-90
                            "
                        >
                            View Details
                        </a>

                    </div>

                @endforeach

            </div>

        </section>



        {{-- =====================================================
            RIGHT SIDEBAR
        ====================================================== --}}

        <aside class="space-y-4">


            {{-- Today's Progress --}}

            <section
                class="
                    rounded-2xl
                    bg-primary
                    p-5
                    text-white
                "
            >

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/70">
                            Today's Progress
                        </p>

                        <p class="mt-2 text-2xl font-bold">
                            6<span class="text-white/60">/12</span>
                        </p>

                    </div>


                    <div
                        class="
                            grid
                            h-9
                            w-9
                            place-items-center
                            rounded-lg
                            bg-white/10
                        "
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-4 w-4 fill-none stroke-current"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="8"
                                stroke-width="1.5"
                            ></circle>

                            <path
                                d="M8 12h8"
                                stroke-width="1.5"
                            ></path>

                        </svg>

                    </div>

                </div>


                <p class="mt-2 text-xs text-white/70">
                    Completed tasks today
                </p>


                <div class="mt-4">

                    <div
                        class="
                            h-2
                            overflow-hidden
                            rounded-full
                            bg-white/20
                        "
                    >

                        <div
                            class="
                                h-full
                                w-1/2
                                rounded-full
                                bg-white
                            "
                        ></div>

                    </div>

                </div>


                <div class="mt-2 flex justify-between text-[10px] text-white/60">

                    <span>
                        50% complete
                    </span>

                    <span>
                        6 remaining
                    </span>

                </div>

            </section>



            {{-- Quick Actions --}}

            <section
                class="
                    rounded-2xl
                    border
                    border-line
                    bg-surface
                    p-5
                "
            >

                <div>

                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-primary">
                        Shortcuts
                    </p>

                    <h2 class="mt-1 text-base font-bold text-ink">
                        Quick Actions
                    </h2>

                </div>


                <div class="mt-4 space-y-2.5">


                    {{-- Pickup --}}

                    <a
                        href="{{ route('rider.pickups.index') }}"
                        class="
                            group
                            flex
                            items-center
                            gap-3
                            rounded-xl
                            border
                            border-line
                            bg-page-secondary
                            p-3.5
                            transition
                            hover:border-primary
                        "
                    >

                        <div
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
                                class="h-4 w-4 fill-none stroke-current"
                            >
                                <path
                                    d="M21 8 12 3 3 8l9 5 9-5Z"
                                    stroke-width="1.5"
                                ></path>

                                <path
                                    d="M3 8v8l9 5 9-5V8M12 13v8"
                                    stroke-width="1.5"
                                ></path>
                            </svg>

                        </div>


                        <div class="min-w-0">

                            <p class="text-xs font-semibold text-ink">
                                Pickup Assignments
                            </p>

                            <p class="mt-0.5 text-[10px] text-muted">
                                Collect parcels from sellers
                            </p>

                        </div>

                    </a>



                    {{-- Delivery --}}

                    <a
                        href="{{ route('rider.deliveries.index') }}"
                        class="
                            group
                            flex
                            items-center
                            gap-3
                            rounded-xl
                            border
                            border-line
                            bg-page-secondary
                            p-3.5
                            transition
                            hover:border-primary
                        "
                    >

                        <div
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
                                class="h-4 w-4 fill-none stroke-current"
                            >
                                <path
                                    d="M4 17h11V5H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2Z"
                                    stroke-width="1.5"
                                ></path>

                                <path
                                    d="M15 9h4l3 3v5h-7V9Z"
                                    stroke-width="1.5"
                                ></path>

                                <circle
                                    cx="6"
                                    cy="19"
                                    r="2"
                                    stroke-width="1.5"
                                ></circle>

                                <circle
                                    cx="18"
                                    cy="19"
                                    r="2"
                                    stroke-width="1.5"
                                ></circle>
                            </svg>

                        </div>


                        <div class="min-w-0">

                            <p class="text-xs font-semibold text-ink">
                                Delivery Assignments
                            </p>

                            <p class="mt-0.5 text-[10px] text-muted">
                                Deliver parcels to buyers
                            </p>

                        </div>

                    </a>



                    {{-- History --}}

                    <a
                        href="{{ route('rider.history.index') }}"
                        class="
                            group
                            flex
                            items-center
                            gap-3
                            rounded-xl
                            border
                            border-line
                            bg-page-secondary
                            p-3.5
                            transition
                            hover:border-primary
                        "
                    >

                        <div
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
                                class="h-4 w-4 fill-none stroke-current"
                            >
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                    stroke-width="1.5"
                                ></circle>

                                <path
                                    d="M12 7v5l3 2"
                                    stroke-width="1.5"
                                ></path>
                            </svg>

                        </div>


                        <div class="min-w-0">

                            <p class="text-xs font-semibold text-ink">
                                Delivery History
                            </p>

                            <p class="mt-0.5 text-[10px] text-muted">
                                View completed deliveries
                            </p>

                        </div>

                    </a>

                </div>

            </section>



            {{-- Performance --}}

            <section
                class="
                    rounded-2xl
                    border
                    border-line
                    bg-surface
                    p-5
                "
            >

                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-primary">
                    Performance
                </p>

                <h2 class="mt-1 text-base font-bold text-ink">
                    Rider Performance
                </h2>


                <div class="mt-4 space-y-3">

                    <div class="flex items-center justify-between">

                        <span class="text-xs text-muted">
                            Rating
                        </span>

                        <span class="text-xs font-semibold text-ink">
                            4.9 / 5.0
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-xs text-muted">
                            Success Rate
                        </span>

                        <span class="text-xs font-semibold text-success">
                            98%
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-xs text-muted">
                            Completed Deliveries
                        </span>

                        <span class="text-xs font-semibold text-ink">
                            342
                        </span>

                    </div>

                </div>

            </section>


        </aside>

    </section>



    {{-- =========================================================
        TODAY'S ACTIVITY
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

        <div
            class="
                flex
                flex-col
                gap-2
                sm:flex-row
                sm:items-center
                sm:justify-between
            "
        >

            <div>

                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-primary">
                    Activity
                </p>

                <h2 class="mt-1 text-base font-bold text-ink">
                    Today's Activity
                </h2>

            </div>


            <span class="text-xs text-muted">
                Updated just now
            </span>

        </div>


        <div class="mt-5 grid gap-3 sm:grid-cols-3">


            <div
                class="
                    rounded-xl
                    border
                    border-line
                    bg-page-secondary
                    p-4
                "
            >

                <p class="text-[10px] uppercase tracking-wide text-muted">
                    Pickups
                </p>

                <p class="mt-1 text-lg font-bold text-ink">
                    8
                </p>

                <p class="mt-0.5 text-[10px] text-muted">
                    Completed today
                </p>

            </div>


            <div
                class="
                    rounded-xl
                    border
                    border-line
                    bg-page-secondary
                    p-4
                "
            >

                <p class="text-[10px] uppercase tracking-wide text-muted">
                    Deliveries
                </p>

                <p class="mt-1 text-lg font-bold text-ink">
                    6
                </p>

                <p class="mt-0.5 text-[10px] text-muted">
                    Successfully delivered
                </p>

            </div>


            <div
                class="
                    rounded-xl
                    border
                    border-line
                    bg-page-secondary
                    p-4
                "
            >

                <p class="text-[10px] uppercase tracking-wide text-muted">
                    Earnings
                </p>

                <p class="mt-1 text-lg font-bold text-ink">
                    ₱850
                </p>

                <p class="mt-0.5 text-[10px] text-muted">
                    Earned today
                </p>

            </div>


        </div>

    </section>


</div>

@endsection