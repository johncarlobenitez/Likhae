@extends('rider.app')

@section('title', 'Pickup Assignments — LIKHAE Rider')

@section('content')

@php

    $pickups = [

        [
            'tracking' => 'LH-2026-1001',
            'seller' => 'ABC Handmade Store',
            'location' => 'Calamba, Laguna',
            'status' => 'READY_FOR_PICKUP',
            'items' => '3 Items',
        ],

        [
            'tracking' => 'LH-2026-1005',
            'seller' => 'Creative Crafts PH',
            'location' => 'Santa Rosa, Laguna',
            'status' => 'READY_FOR_PICKUP',
            'items' => '2 Items',
        ],

        [
            'tracking' => 'LH-2026-1009',
            'seller' => 'Local Artisan Shop',
            'location' => 'Biñan, Laguna',
            'status' => 'READY_FOR_PICKUP',
            'items' => '5 Items',
        ],

        [
            'tracking' => 'LH-2026-1012',
            'seller' => 'Luna Craft Studio',
            'location' => 'Los Baños, Laguna',
            'status' => 'READY_FOR_PICKUP',
            'items' => '4 Items',
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
                Pickup Management
            </p>

            <h1 class="mt-1.5 text-[28px] font-bold leading-tight tracking-tight text-ink">
                Pickup Assignments
            </h1>

            <p class="mt-1.5 text-sm text-muted">
                Collect parcels from sellers and bring them to the sorting center.
            </p>

        </div>


        {{-- Pending Pickup --}}

        <div
            class="
                flex
                w-fit
                items-center
                gap-2.5
                rounded-xl
                border
                border-line
                bg-warning-soft
                px-4
                py-2.5
            "
        >

            <div
                class="
                    grid
                    h-8
                    w-8
                    place-items-center
                    rounded-lg
                    bg-white/70
                    text-warning
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-4 w-4 fill-none stroke-current"
                >
                    <path
                        d="M21 8 12 3 3 8l9 5 9-5Z"
                        stroke-width="1.7"
                    ></path>

                    <path
                        d="M3 8v8l9 5 9-5V8"
                        stroke-width="1.7"
                    ></path>

                    <path
                        d="M12 13v8"
                        stroke-width="1.7"
                    ></path>
                </svg>

            </div>


            <div>

                <p class="text-[11px] font-medium text-warning">
                    Pending Pickup
                </p>

                <p class="text-base font-bold leading-5 text-warning">
                    5
                </p>

            </div>

        </div>

    </section>



    {{-- =========================================================
        SUMMARY CARDS
    ========================================================== --}}

    <section class="grid gap-4 md:grid-cols-3">


        {{-- Ready --}}

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

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-medium text-muted">
                        Ready for Pickup
                    </p>

                    <p class="mt-1 text-2xl font-bold leading-none text-ink">
                        5
                    </p>

                </div>


                <span
                    class="
                        rounded-full
                        bg-warning-soft
                        px-2.5
                        py-1
                        text-[10px]
                        font-semibold
                        text-warning
                    "
                >
                    Waiting
                </span>

            </div>


            <p class="mt-2 text-[11px] text-muted">
                Parcels waiting at seller locations.
            </p>

        </div>



        {{-- Picked Up --}}

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

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-medium text-muted">
                        Picked Up Today
                    </p>

                    <p class="mt-1 text-2xl font-bold leading-none text-ink">
                        8
                    </p>

                </div>


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


            <p class="mt-2 text-[11px] text-muted">
                Parcels collected from sellers today.
            </p>

        </div>



        {{-- Sorting --}}

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

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-medium text-muted">
                        At Sorting Center
                    </p>

                    <p class="mt-1 text-2xl font-bold leading-none text-ink">
                        6
                    </p>

                </div>


                <span
                    class="
                        rounded-full
                        bg-primary-soft
                        px-2.5
                        py-1
                        text-[10px]
                        font-semibold
                        text-primary
                    "
                >
                    Received
                </span>

            </div>


            <p class="mt-2 text-[11px] text-muted">
                Parcels delivered to sorting.
            </p>

        </div>

    </section>



    {{-- =========================================================
        PICKUP TASKS
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
                    Pickup Tasks
                </h2>

                <p class="mt-1 text-xs text-muted">
                    Assigned parcels waiting for seller pickup.
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
                {{ count($pickups) }} Current Tasks
            </span>

        </div>



        {{-- =====================================================
            PICKUP LIST
        ====================================================== --}}

        <div class="divide-y divide-line">


            @foreach($pickups as $pickup)

                @php

                    $statusMap = [

                        'READY_FOR_PICKUP' => [
                            'label' => 'Ready for Pickup',
                            'class' => 'bg-warning-soft text-warning',
                            'dot' => 'bg-warning',
                        ],

                        'PICKUP_ACCEPTED' => [
                            'label' => 'Pickup Accepted',
                            'class' => 'bg-success-soft text-success',
                            'dot' => 'bg-success',
                        ],

                        'PICKED_UP' => [
                            'label' => 'Picked Up',
                            'class' => 'bg-success-soft text-success',
                            'dot' => 'bg-success',
                        ],

                        'AT_SORTING_CENTER' => [
                            'label' => 'At Sorting Center',
                            'class' => 'bg-primary-soft text-primary',
                            'dot' => 'bg-primary',
                        ],

                    ];

                    $status = $statusMap[$pickup['status']]
                        ?? [
                            'label' => 'Unknown',
                            'class' => 'bg-page-secondary text-muted',
                            'dot' => 'bg-muted',
                        ];

                @endphp



                <article
                    id="pickup-{{ $pickup['tracking'] }}"
                    class="
                        pickup-row
                        px-5
                        py-5
                        transition
                        hover:bg-page-secondary
                        sm:px-6
                    "
                >

                    {{-- Desktop Layout --}}

                    <div
                        class="
                            hidden
                            lg:grid
                            lg:grid-cols-[48px_minmax(260px,1.2fr)_minmax(180px,1fr)_minmax(160px,1fr)_auto]
                            lg:items-center
                            lg:gap-5
                        "
                    >


                        {{-- ICON --}}

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

                            <svg
                                viewBox="0 0 24 24"
                                class="h-5 w-5 fill-none stroke-current"
                            >
                                <path
                                    d="M21 8 12 3 3 8l9 5 9-5Z"
                                    stroke-width="1.6"
                                ></path>

                                <path
                                    d="M3 8v8l9 5 9-5V8"
                                    stroke-width="1.6"
                                ></path>

                                <path
                                    d="M12 13v8"
                                    stroke-width="1.6"
                                ></path>
                            </svg>

                        </div>



                        {{-- PARCEL --}}

                        <div class="min-w-0">

                            <div class="flex items-center gap-2">

                                <h3 class="truncate text-sm font-bold text-ink">
                                    {{ $pickup['tracking'] }}
                                </h3>


                                <span
                                    class="
                                        pickup-status
                                        inline-flex
                                        shrink-0
                                        items-center
                                        gap-1.5
                                        rounded-full
                                        px-2.5
                                        py-1
                                        text-[10px]
                                        font-semibold
                                        {{ $status['class'] }}
                                    "
                                >

                                    <span
                                        class="
                                            status-dot
                                            h-1.5
                                            w-1.5
                                            rounded-full
                                            {{ $status['dot'] }}
                                        "
                                    ></span>

                                    <span class="status-label">
                                        {{ $status['label'] }}
                                    </span>

                                </span>

                            </div>

                            <p class="mt-1 text-[11px] text-muted">
                                {{ $pickup['items'] }}
                            </p>

                        </div>



                        {{-- SELLER --}}

                        <div class="min-w-0">

                            <p class="text-[10px] font-medium uppercase tracking-wide text-muted">
                                Seller
                            </p>

                            <p class="mt-1 truncate text-sm font-semibold text-ink">
                                {{ $pickup['seller'] }}
                            </p>

                        </div>



                        {{-- LOCATION --}}

                        <div class="min-w-0">

                            <p class="text-[10px] font-medium uppercase tracking-wide text-muted">
                                Pickup Location
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
                                    {{ $pickup['location'] }}
                                </p>

                            </div>

                        </div>



                        {{-- ACTIONS --}}

                        <div class="flex items-center justify-end gap-2">

                            <a
                                href="{{ route('rider.pickups.show', $pickup['tracking']) }}"
                                class="
                                    inline-flex
                                    h-9
                                    items-center
                                    justify-center
                                    rounded-lg
                                    border
                                    border-line
                                    px-3.5
                                    text-xs
                                    font-semibold
                                    text-ink
                                    transition
                                    hover:bg-page-secondary
                                "
                            >
                                View
                            </a>


                            @if($pickup['status'] === 'READY_FOR_PICKUP')

                                <button
                                    type="button"
                                    class="
                                        accept-pickup-btn
                                        inline-flex
                                        h-9
                                        items-center
                                        justify-center
                                        rounded-lg
                                        bg-primary
                                        px-3.5
                                        text-xs
                                        font-semibold
                                        text-white
                                        transition
                                        hover:opacity-90
                                        disabled:cursor-not-allowed
                                        disabled:opacity-60
                                    "
                                    data-tracking="{{ $pickup['tracking'] }}"
                                >
                                    Accept Pickup
                                </button>

                            @else

                                <a
                                    href="{{ route('rider.pickups.show', $pickup['tracking']) }}"
                                    class="
                                        inline-flex
                                        h-9
                                        items-center
                                        justify-center
                                        rounded-lg
                                        bg-success
                                        px-3.5
                                        text-xs
                                        font-semibold
                                        text-white
                                    "
                                >
                                    Continue
                                </a>

                            @endif

                        </div>

                    </div>



                    {{-- =================================================
                        MOBILE / TABLET
                    ================================================== --}}

                    <div class="lg:hidden">


                        <div class="flex items-start justify-between gap-3">

                            <div class="flex min-w-0 items-center gap-3">

                                <div
                                    class="
                                        grid
                                        h-11
                                        w-11
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
                                        ></path>

                                        <path
                                            d="M3 8v8l9 5 9-5V8"
                                            stroke-width="1.6"
                                        ></path>

                                        <path
                                            d="M12 13v8"
                                            stroke-width="1.6"
                                        ></path>
                                    </svg>

                                </div>


                                <div class="min-w-0">

                                    <p class="truncate text-sm font-bold text-ink">
                                        {{ $pickup['tracking'] }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-muted">
                                        {{ $pickup['items'] }}
                                    </p>

                                </div>

                            </div>


                            <span
                                class="
                                    pickup-status
                                    inline-flex
                                    shrink-0
                                    items-center
                                    gap-1.5
                                    rounded-full
                                    px-2.5
                                    py-1
                                    text-[10px]
                                    font-semibold
                                    {{ $status['class'] }}
                                "
                            >

                                <span
                                    class="
                                        status-dot
                                        h-1.5
                                        w-1.5
                                        rounded-full
                                        {{ $status['dot'] }}
                                    "
                                ></span>

                                <span class="status-label">
                                    {{ $status['label'] }}
                                </span>

                            </span>

                        </div>



                        <div class="mt-4 grid gap-3 sm:grid-cols-2">


                            <div>

                                <p class="text-[10px] font-medium uppercase tracking-wide text-muted">
                                    Seller
                                </p>

                                <p class="mt-1 text-sm font-semibold text-ink">
                                    {{ $pickup['seller'] }}
                                </p>

                            </div>


                            <div>

                                <p class="text-[10px] font-medium uppercase tracking-wide text-muted">
                                    Pickup Location
                                </p>

                                <p class="mt-1 text-sm text-muted">
                                    {{ $pickup['location'] }}
                                </p>

                            </div>

                        </div>



                        <div class="mt-4 flex gap-2">

                            <a
                                href="{{ route('rider.pickups.show', $pickup['tracking']) }}"
                                class="
                                    flex-1
                                    rounded-lg
                                    border
                                    border-line
                                    px-4
                                    py-2.5
                                    text-center
                                    text-xs
                                    font-semibold
                                    text-ink
                                "
                            >
                                View Details
                            </a>


                            @if($pickup['status'] === 'READY_FOR_PICKUP')

                                <button
                                    type="button"
                                    class="
                                        accept-pickup-btn
                                        flex-1
                                        rounded-lg
                                        bg-primary
                                        px-4
                                        py-2.5
                                        text-xs
                                        font-semibold
                                        text-white
                                    "
                                    data-tracking="{{ $pickup['tracking'] }}"
                                >
                                    Accept Pickup
                                </button>

                            @else

                                <a
                                    href="{{ route('rider.pickups.show', $pickup['tracking']) }}"
                                    class="
                                        flex-1
                                        rounded-lg
                                        bg-success
                                        px-4
                                        py-2.5
                                        text-center
                                        text-xs
                                        font-semibold
                                        text-white
                                    "
                                >
                                    Continue
                                </a>

                            @endif

                        </div>

                    </div>

                </article>


            @endforeach


        </div>

    </section>



    {{-- =========================================================
        PICKUP PROCESS
    ========================================================== --}}

    <section
        class="
            rounded-2xl
            border
            border-line
            bg-surface
            px-5
            py-5
            sm:px-6
        "
    >

        <div class="flex items-center justify-between">

            <div>

                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">
                    Pickup Workflow
                </p>

                <h2 class="mt-1 text-lg font-bold text-ink">
                    Pickup Process
                </h2>

            </div>

        </div>



        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">


            @foreach([

                [
                    'number' => '01',
                    'title' => 'Accept Pickup',
                    'description' => 'Accept the assigned pickup.'
                ],

                [
                    'number' => '02',
                    'title' => 'Go to Seller',
                    'description' => 'Travel to the seller location.'
                ],

                [
                    'number' => '03',
                    'title' => 'Scan Parcel',
                    'description' => 'Verify and confirm the parcel.'
                ],

                [
                    'number' => '04',
                    'title' => 'Send to Sorting',
                    'description' => 'Bring the parcel to the sorting center.'
                ]

            ] as $step)

                <div
                    class="
                        flex
                        items-start
                        gap-3
                        rounded-xl
                        border
                        border-line
                        bg-page-secondary
                        p-4
                    "
                >

                    <span
                        class="
                            grid
                            h-8
                            w-8
                            shrink-0
                            place-items-center
                            rounded-lg
                            bg-primary
                            text-[10px]
                            font-bold
                            text-white
                        "
                    >
                        {{ $step['number'] }}
                    </span>


                    <div>

                        <h3 class="text-sm font-semibold text-ink">
                            {{ $step['title'] }}
                        </h3>

                        <p class="mt-1 text-[11px] leading-4 text-muted">
                            {{ $step['description'] }}
                        </p>

                    </div>

                </div>

            @endforeach

        </div>

    </section>


</div>



{{-- =============================================================
    JAVASCRIPT
============================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const buttons = document.querySelectorAll(
        '.accept-pickup-btn'
    );


    buttons.forEach(function (button) {

        button.addEventListener('click', function () {

            const tracking =
                this.dataset.tracking;


            const pickup =
                document.getElementById(
                    'pickup-' + tracking
                );


            if (!pickup) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Disable button
            |--------------------------------------------------------------------------
            */

            this.disabled = true;

            this.textContent =
                'Accepting...';



            /*
            |--------------------------------------------------------------------------
            | Update all status badges
            |--------------------------------------------------------------------------
            */

            const badges =
                pickup.querySelectorAll(
                    '.pickup-status'
                );


            badges.forEach(function (badge) {

                badge.classList.remove(
                    'bg-warning-soft',
                    'text-warning'
                );


                badge.classList.add(
                    'bg-success-soft',
                    'text-success'
                );


                const dot =
                    badge.querySelector(
                        '.status-dot'
                    );


                if (dot) {

                    dot.classList.remove(
                        'bg-warning'
                    );

                    dot.classList.add(
                        'bg-success'
                    );

                }


                const label =
                    badge.querySelector(
                        '.status-label'
                    );


                if (label) {

                    label.textContent =
                        'Pickup Accepted';

                }

            });



            /*
            |--------------------------------------------------------------------------
            | Update button
            |--------------------------------------------------------------------------
            */

            this.textContent =
                'Pickup Accepted';


            this.classList.remove(
                'bg-primary'
            );


            this.classList.add(
                'bg-success'
            );



            /*
            |--------------------------------------------------------------------------
            | Redirect to pickup details
            |--------------------------------------------------------------------------
            */

            setTimeout(function () {

                const url =
                    "{{ route('rider.pickups.show', ':tracking') }}"
                    .replace(
                        ':tracking',
                        encodeURIComponent(tracking)
                    );


                window.location.href = url;

            }, 500);

        });

    });

});

</script>

@endpush

@endsection