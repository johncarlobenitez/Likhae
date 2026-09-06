@extends('rider.app')

@section('title', 'Delivery Details — LIKHAE Rider')

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
                Delivery Assignment
            </p>

            <h1 class="mt-1.5 text-[28px] font-bold leading-tight tracking-tight text-ink">
                Delivery Details
            </h1>

            <p class="mt-1.5 text-sm text-muted">
                Deliver this parcel from the sorting center to the customer.
            </p>

        </div>


        {{-- CURRENT STATUS --}}

        <span
            id="headerStatus"
            class="
                inline-flex
                w-fit
                items-center
                gap-2
                rounded-full
                bg-primary-soft
                px-3.5
                py-2
                text-[10px]
                font-semibold
                text-primary
            "
        >

            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

            ASSIGNED_TO_RIDER

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

                {{-- Tracking --}}

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
                                d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"
                                stroke-width="1.5"
                            ></path>

                            <rect
                                x="9"
                                y="11"
                                width="14"
                                height="10"
                                rx="2"
                                stroke-width="1.5"
                            ></rect>

                            <circle
                                cx="12"
                                cy="21"
                                r="1"
                                stroke-width="1.5"
                            ></circle>

                            <circle
                                cx="20"
                                cy="21"
                                r="1"
                                stroke-width="1.5"
                            ></circle>
                        </svg>

                    </div>


                    <div>

                        <p class="text-[10px] text-muted">
                            Tracking Number
                        </p>

                        <h2 class="mt-0.5 text-base font-bold text-ink">
                            LH-2026-1007
                        </h2>

                    </div>

                </div>


                {{-- Details --}}

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
                            Current Status
                        </p>

                        <p
                            id="parcelStatus"
                            class="mt-1 text-xs font-bold text-primary"
                        >
                            ASSIGNED_TO_RIDER
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
                            Payment Method
                        </p>

                        <p class="mt-1 text-xs font-semibold text-ink">
                            Cash On Delivery
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
                            ₱1,200
                        </p>

                    </div>


                    {{-- Parcel Type --}}

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            px-4
                            py-3.5
                        "
                    >

                        <p class="text-[10px] text-muted">
                            Parcel Type
                        </p>

                        <p class="mt-1 text-xs font-semibold text-ink">
                            Regular Package
                        </p>

                    </div>

                </div>

            </section>



            {{-- =================================================
                CUSTOMER INFORMATION
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
                    Customer
                </p>

                <h2 class="mt-1 text-base font-bold text-ink">
                    Customer Information
                </h2>


                <div class="mt-4 space-y-2.5">


                    {{-- Customer Name --}}

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            px-4
                            py-3.5
                        "
                    >

                        <p class="text-[10px] text-muted">
                            Customer Name
                        </p>

                        <p class="mt-1 text-xs font-semibold text-ink">
                            Juan Dela Cruz
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
                            Delivery Address
                        </p>

                        <p class="mt-1 text-xs font-semibold leading-relaxed text-ink">
                            123 Main Street, Los Baños, Laguna
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
                            Contact Number
                        </p>

                        <p class="mt-1 text-xs font-semibold text-ink">
                            0917 555 1234
                        </p>

                    </div>

                </div>

            </section>



            {{-- =================================================
                DELIVERY PROGRESS
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
                    Workflow
                </p>

                <h2 class="mt-1 text-base font-bold text-ink">
                    Delivery Progress
                </h2>

                <p class="mt-1 text-xs text-muted">
                    Track the parcel through each delivery stage.
                </p>


                <div
                    class="
                        mt-5
                        grid
                        gap-2.5
                        sm:grid-cols-2
                        xl:grid-cols-5
                    "
                >

                    @foreach([

                        [
                            'number' => '1',
                            'title' => 'Receive Assignment',
                            'status' => 'Completed',
                            'type' => 'completed'
                        ],

                        [
                            'number' => '2',
                            'title' => 'Pickup From Sorting',
                            'status' => 'Current',
                            'type' => 'current'
                        ],

                        [
                            'number' => '3',
                            'title' => 'Out For Delivery',
                            'status' => 'Pending',
                            'type' => 'pending'
                        ],

                        [
                            'number' => '4',
                            'title' => 'Delivered',
                            'status' => 'Pending',
                            'type' => 'pending'
                        ],

                        [
                            'number' => '5',
                            'title' => 'Completed',
                            'status' => 'Pending',
                            'type' => 'pending'
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
                                    text-[11px]
                                    font-bold

                                    @if($step['type'] === 'completed')
                                        bg-success
                                        text-white
                                    @elseif($step['type'] === 'current')
                                        bg-primary
                                        text-white
                                    @else
                                        border
                                        border-line
                                        bg-surface
                                        text-muted
                                    @endif
                                "
                            >
                                {{ $step['number'] }}
                            </div>


                            <h3 class="mt-3 text-xs font-semibold leading-snug text-ink">
                                {{ $step['title'] }}
                            </h3>


                            <p
                                class="
                                    mt-1.5
                                    text-[10px]
                                    font-medium

                                    @if($step['type'] === 'completed')
                                        text-success
                                    @elseif($step['type'] === 'current')
                                        text-primary
                                    @else
                                        text-muted
                                    @endif
                                "
                            >
                                {{ $step['status'] }}
                            </p>

                        </div>

                    @endforeach

                </div>

            </section>

        </div>



        {{-- =====================================================
            RIGHT SIDEBAR
        ====================================================== --}}

        <aside class="space-y-4">


            {{-- =================================================
                DELIVERY ACTIONS
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
                    Delivery Actions
                </h2>


                <div class="mt-4 space-y-2.5">


                    {{-- Accept Delivery --}}

                    <button
                        id="acceptDeliveryBtn"
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

                        Accept Delivery

                    </button>


                    {{-- Navigate --}}

                    <button
                        id="navigateSortingBtn"
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

                        Navigate To Sorting Center

                    </button>


                    {{-- Contact Customer --}}

                    <button
                        id="contactCustomerBtn"
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
                                d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"
                                stroke-width="1.5"
                            ></path>
                        </svg>

                        Contact Customer

                    </button>

                </div>

            </section>



            {{-- =================================================
                NEXT STATUS
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
                    Next Status
                </p>


                <h2
                    id="nextStatus"
                    class="mt-2 text-[18px] font-bold leading-snug"
                >
                    OUT_FOR_DELIVERY
                </h2>


                <p class="mt-2 text-xs leading-relaxed text-white/75">
                    After collecting the parcel from the sorting center,
                    start delivery to the customer.
                </p>

            </section>



            {{-- =================================================
                DELIVERY SUMMARY
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
                            Parcel Type
                        </span>

                        <span class="text-[10px] font-semibold text-ink">
                            Regular
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
                            ₱1,200
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-[10px] text-muted">
                            Destination
                        </span>

                        <span class="text-[10px] font-semibold text-ink">
                            Los Baños
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

    const acceptDeliveryBtn =
        document.getElementById('acceptDeliveryBtn');

    const navigateSortingBtn =
        document.getElementById('navigateSortingBtn');

    const contactCustomerBtn =
        document.getElementById('contactCustomerBtn');

    const parcelStatus =
        document.getElementById('parcelStatus');

    const headerStatus =
        document.getElementById('headerStatus');

    const nextStatus =
        document.getElementById('nextStatus');


    /*
    |--------------------------------------------------------------------------
    | Accept Delivery
    |--------------------------------------------------------------------------
    */

    acceptDeliveryBtn?.addEventListener('click', function () {

        acceptDeliveryBtn.disabled = true;

        acceptDeliveryBtn.innerHTML = `
            <svg
                viewBox="0 0 24 24"
                class="h-4 w-4 fill-none stroke-current"
            >
                <path
                    d="m5 12 4 4L19 6"
                    stroke-width="2"
                ></path>
            </svg>

            Delivery Accepted
        `;

        acceptDeliveryBtn.classList.remove(
            'bg-primary'
        );

        acceptDeliveryBtn.classList.add(
            'bg-success'
        );


        /*
        |--------------------------------------------------------------------------
        | Update Current Status
        |--------------------------------------------------------------------------
        */

        if (parcelStatus) {

            parcelStatus.textContent =
                'OUT_FOR_DELIVERY';

            parcelStatus.classList.remove(
                'text-primary'
            );

            parcelStatus.classList.add(
                'text-success'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Update Header Badge
        |--------------------------------------------------------------------------
        */

        if (headerStatus) {

            headerStatus.classList.remove(
                'bg-primary-soft',
                'text-primary'
            );

            headerStatus.classList.add(
                'bg-success-soft',
                'text-success'
            );

            headerStatus.innerHTML = `
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                OUT_FOR_DELIVERY
            `;

        }


        /*
        |--------------------------------------------------------------------------
        | Update Next Status
        |--------------------------------------------------------------------------
        */

        if (nextStatus) {

            nextStatus.textContent =
                'DELIVERY IN PROGRESS';

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Navigate To Sorting Center
    |--------------------------------------------------------------------------
    */

    navigateSortingBtn?.addEventListener('click', function () {

        const sortingCenter =
            'Sorting Center Laguna';

        const mapsUrl =
            'https://www.google.com/maps/search/?api=1&query='
            + encodeURIComponent(sortingCenter);

        window.open(
            mapsUrl,
            '_blank',
            'noopener,noreferrer'
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Contact Customer
    |--------------------------------------------------------------------------
    */

    contactCustomerBtn?.addEventListener('click', function () {

        window.location.href =
            'tel:+639175551234';

    });

});

</script>

@endpush

@endsection