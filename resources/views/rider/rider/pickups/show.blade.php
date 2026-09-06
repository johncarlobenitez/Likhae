@extends('rider.app')

@section('title', 'Pickup Details — LIKHAE Rider')

@section('content')

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
            lg:items-end
            lg:justify-between
        "
    >

        <div>

            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-primary">
                Pickup Assignment
            </p>

            <h1 class="mt-1.5 text-[28px] font-bold leading-tight tracking-tight text-ink">
                Pickup Details
            </h1>

            <p class="mt-1.5 text-sm text-muted">
                Collect this parcel from the seller and bring it to the sorting center.
            </p>

        </div>


        {{-- Current Status --}}

        <span
            id="headerStatus"
            class="
                inline-flex
                w-fit
                items-center
                gap-2
                rounded-full
                bg-warning-soft
                px-3.5
                py-2
                text-[10px]
                font-semibold
                text-warning
            "
        >

            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

            READY_FOR_PICKUP

        </span>

    </section>



    {{-- =========================================================
        MAIN LAYOUT
    ========================================================== --}}

    <section
        class="
            grid
            gap-4
            xl:grid-cols-[minmax(0,1fr)_290px]
        "
    >


        {{-- =====================================================
            LEFT CONTENT
        ====================================================== --}}

        <div class="space-y-4">


            {{-- =================================================
                PARCEL INFORMATION
            ================================================== --}}

            <section
                class="
                    rounded-2xl
                    border
                    border-line
                    bg-surface
                    p-5
                "
            >

                {{-- Tracking Header --}}

                <div class="flex items-center gap-3.5">

                    <div
                        class="
                            grid
                            h-12
                            w-12
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
                                stroke-width="1.5"
                            ></path>

                            <path
                                d="M3 8v8l9 5 9-5V8"
                                stroke-width="1.5"
                            ></path>

                            <path
                                d="M12 13v8"
                                stroke-width="1.5"
                            ></path>
                        </svg>

                    </div>


                    <div>

                        <p class="text-[10px] text-muted">
                            Tracking Number
                        </p>

                        <h2 class="mt-0.5 text-base font-bold text-ink">
                            LH-2026-1001
                        </h2>

                    </div>

                </div>


                {{-- Parcel Details --}}

                <div class="mt-5 grid gap-3 sm:grid-cols-2">


                    {{-- Status --}}

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            px-4
                            py-3.5
                        "
                    >

                        <p class="text-[10px] text-muted">
                            Parcel Status
                        </p>

                        <p
                            id="parcelStatus"
                            class="mt-1 text-xs font-bold text-warning"
                        >
                            READY_FOR_PICKUP
                        </p>

                    </div>


                    {{-- Items --}}

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            px-4
                            py-3.5
                        "
                    >

                        <p class="text-[10px] text-muted">
                            Items
                        </p>

                        <p class="mt-1 text-xs font-semibold text-ink">
                            3 Items
                        </p>

                    </div>


                    {{-- Payment --}}

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            px-4
                            py-3.5
                        "
                    >

                        <p class="text-[10px] text-muted">
                            Payment Type
                        </p>

                        <p class="mt-1 text-xs font-semibold text-ink">
                            Cash on Delivery
                        </p>

                    </div>


                    {{-- COD --}}

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            px-4
                            py-3.5
                        "
                    >

                        <p class="text-[10px] text-muted">
                            COD Amount
                        </p>

                        <p class="mt-1 text-xs font-semibold text-ink">
                            ₱850
                        </p>

                    </div>

                </div>

            </section>



            {{-- =================================================
                SELLER INFORMATION
            ================================================== --}}

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
                        Seller
                    </p>

                    <h2 class="mt-1 text-base font-bold text-ink">
                        Seller Information
                    </h2>

                </div>


                <div class="mt-4 space-y-2.5">


                    {{-- Store --}}

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            px-4
                            py-3.5
                        "
                    >

                        <p class="text-[10px] text-muted">
                            Store Name
                        </p>

                        <p class="mt-1 text-xs font-semibold text-ink">
                            ABC Handmade Store
                        </p>

                    </div>


                    {{-- Address --}}

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            px-4
                            py-3.5
                        "
                    >

                        <p class="text-[10px] text-muted">
                            Pickup Address
                        </p>

                        <p class="mt-1 text-xs font-semibold leading-relaxed text-ink">
                            123 Rizal Street, Calamba, Laguna
                        </p>

                    </div>


                    {{-- Contact --}}

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            px-4
                            py-3.5
                        "
                    >

                        <p class="text-[10px] text-muted">
                            Seller Contact
                        </p>

                        <p class="mt-1 text-xs font-semibold text-ink">
                            0917 555 8888
                        </p>

                    </div>

                </div>

            </section>



            {{-- =================================================
                PICKUP PROCESS
            ================================================== --}}

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
                        Workflow
                    </p>

                    <h2 class="mt-1 text-base font-bold text-ink">
                        Pickup Process
                    </h2>

                    <p class="mt-1 text-xs text-muted">
                        Follow these steps to complete the pickup.
                    </p>

                </div>


                <div
                    class="
                        mt-5
                        grid
                        gap-2.5
                        sm:grid-cols-2
                        xl:grid-cols-4
                    "
                >

                    @foreach([

                        [
                            'number' => '1',
                            'title' => 'Accept Pickup',
                            'description' => 'Accept the assigned pickup task.',
                            'active' => true
                        ],

                        [
                            'number' => '2',
                            'title' => 'Travel To Seller',
                            'description' => 'Go to the seller pickup location.',
                            'active' => false
                        ],

                        [
                            'number' => '3',
                            'title' => 'Confirm Parcel',
                            'description' => 'Scan and verify the parcel.',
                            'active' => false
                        ],

                        [
                            'number' => '4',
                            'title' => 'Send To Sorting',
                            'description' => 'Bring the parcel to the sorting center.',
                            'active' => false
                        ]

                    ] as $step)

                        <div
                            class="
                                rounded-xl
                                bg-page-secondary
                                p-4
                            "
                        >

                            <div
                                class="
                                    grid
                                    h-8
                                    w-8
                                    place-items-center
                                    rounded-lg
                                    {{ $step['active']
                                        ? 'bg-primary text-white'
                                        : 'bg-surface text-muted border border-line'
                                    }}
                                    text-[11px]
                                    font-bold
                                "
                            >
                                {{ $step['number'] }}
                            </div>


                            <h3 class="mt-3 text-xs font-semibold text-ink">
                                {{ $step['title'] }}
                            </h3>


                            <p class="mt-1.5 text-[10px] leading-relaxed text-muted">
                                {{ $step['description'] }}
                            </p>

                        </div>

                    @endforeach

                </div>

            </section>

        </div>



        {{-- =====================================================
            RIGHT ACTION PANEL
        ====================================================== --}}

        <aside class="space-y-4">


            {{-- =================================================
                ACTIONS
            ================================================== --}}

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
                    Actions
                </p>

                <h2 class="mt-1 text-base font-bold text-ink">
                    Pickup Actions
                </h2>


                <div class="mt-4 space-y-2.5">


                    {{-- Accept Pickup --}}

                    <button
                        id="acceptPickupBtn"
                        type="button"
                        class="
                            flex
                            w-full
                            items-center
                            justify-center
                            gap-2
                            rounded-lg
                            bg-primary
                            px-4
                            py-3
                            text-xs
                            font-semibold
                            text-white
                            transition
                            hover:opacity-90
                            disabled:cursor-not-allowed
                        "
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-4 w-4 fill-none stroke-current"
                        >
                            <path
                                d="m5 12 4 4L19 6"
                                stroke-width="2"
                            ></path>
                        </svg>

                        Accept Pickup

                    </button>


                    {{-- Navigation --}}

                    <button
                        id="openNavBtn"
                        type="button"
                        class="
                            flex
                            w-full
                            items-center
                            justify-center
                            gap-2
                            rounded-lg
                            border
                            border-line
                            bg-surface
                            px-4
                            py-3
                            text-xs
                            font-semibold
                            text-ink
                            transition
                            hover:bg-page-secondary
                        "
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-4 w-4 fill-none stroke-current"
                        >
                            <path
                                d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"
                                stroke-width="1.5"
                            ></path>

                            <circle
                                cx="12"
                                cy="10"
                                r="2"
                                stroke-width="1.5"
                            ></circle>
                        </svg>

                        Open Navigation

                    </button>


                    {{-- Scan --}}

                    <a
                        href="{{ route('rider.pickups.scan') }}"
                        class="
                            flex
                            w-full
                            items-center
                            justify-center
                            gap-2
                            rounded-lg
                            border
                            border-line
                            bg-surface
                            px-4
                            py-3
                            text-xs
                            font-semibold
                            text-ink
                            transition
                            hover:bg-page-secondary
                        "
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-4 w-4 fill-none stroke-current"
                        >
                            <rect
                                x="3"
                                y="3"
                                width="18"
                                height="18"
                                rx="2"
                                stroke-width="1.5"
                            ></rect>

                            <path
                                d="M7 7h.01M7 12h.01M7 17h.01"
                                stroke-width="2"
                            ></path>

                            <path
                                d="M11 7h6M11 12h6M11 17h6"
                                stroke-width="1.5"
                            ></path>
                        </svg>

                        Scan Parcel

                    </a>

                </div>

            </section>



            {{-- =================================================
                NEXT STEP
            ================================================== --}}

            <section
                class="
                    rounded-2xl
                    bg-primary
                    p-5
                    text-white
                "
            >

                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/60">
                    Next Step
                </p>


                <h2 class="mt-2 text-[18px] font-bold leading-snug">
                    Bring Parcel To Sorting Center
                </h2>


                <p class="mt-2 text-xs leading-relaxed text-white/75">
                    After successful pickup, the parcel status changes to
                    <strong class="font-semibold text-white">
                        PICKED_UP
                    </strong>.
                </p>

            </section>



            {{-- =================================================
                QUICK INFO
            ================================================== --}}

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
                    Quick Info
                </p>


                <div class="mt-4 space-y-3">


                    <div class="flex items-center justify-between">

                        <span class="text-[10px] text-muted">
                            Items
                        </span>

                        <span class="text-[10px] font-semibold text-ink">
                            3
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-[10px] text-muted">
                            Payment
                        </span>

                        <span class="text-[10px] font-semibold text-ink">
                            COD
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-[10px] text-muted">
                            COD Amount
                        </span>

                        <span class="text-[10px] font-semibold text-ink">
                            ₱850
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-[10px] text-muted">
                            Destination
                        </span>

                        <span class="text-[10px] font-semibold text-ink">
                            Sorting Center
                        </span>

                    </div>

                </div>

            </section>

        </aside>

    </section>

</div>



{{-- =============================================================
    JAVASCRIPT
============================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const acceptPickupBtn =
        document.getElementById('acceptPickupBtn');

    const openNavBtn =
        document.getElementById('openNavBtn');

    const parcelStatus =
        document.getElementById('parcelStatus');

    const headerStatus =
        document.getElementById('headerStatus');


    /*
    |--------------------------------------------------------------------------
    | Accept Pickup
    |--------------------------------------------------------------------------
    */

    acceptPickupBtn?.addEventListener('click', function () {

        acceptPickupBtn.disabled = true;

        acceptPickupBtn.innerHTML = `
            <svg
                viewBox="0 0 24 24"
                class="h-4 w-4 fill-none stroke-current"
            >
                <path
                    d="m5 12 4 4L19 6"
                    stroke-width="2"
                ></path>
            </svg>

            Pickup Accepted
        `;

        acceptPickupBtn.classList.remove(
            'bg-primary'
        );

        acceptPickupBtn.classList.add(
            'bg-success'
        );


        /*
        |--------------------------------------------------------------------------
        | Update Parcel Status
        |--------------------------------------------------------------------------
        */

        if (parcelStatus) {

            parcelStatus.textContent =
                'PICKED_UP';

            parcelStatus.classList.remove(
                'text-warning'
            );

            parcelStatus.classList.add(
                'text-success'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Update Header Status
        |--------------------------------------------------------------------------
        */

        if (headerStatus) {

            headerStatus.classList.remove(
                'bg-warning-soft',
                'text-warning'
            );

            headerStatus.classList.add(
                'bg-success-soft',
                'text-success'
            );

            headerStatus.innerHTML = `
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                PICKED_UP
            `;

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Open Navigation
    |--------------------------------------------------------------------------
    */

    openNavBtn?.addEventListener('click', function () {

        const address =
            '123 Rizal Street, Calamba, Laguna';

        const mapsUrl =
            'https://www.google.com/maps/search/?api=1&query='
            + encodeURIComponent(address);

        window.open(
            mapsUrl,
            '_blank',
            'noopener,noreferrer'
        );

    });

});

</script>

@endpush

@endsection