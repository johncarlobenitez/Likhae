@extends('rider.app')

@section('title', 'Delivery Tracking — LIKHAE Rider')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | Delivery Data
    |--------------------------------------------------------------------------
    | Replace these values later with database/controller data.
    */

    $trackingNumber = $id ?? 'LH-2026-1007';

    $customerName = 'Juan Dela Cruz';

    $customerPhone = '0917 555 1234';

    $customerAddress = '123 Main Street, Los Baños, Laguna';

    $codAmount = '₱1,200';

    $currentStatus = 'OUT_FOR_DELIVERY';


    /*
    |--------------------------------------------------------------------------
    | Delivery Steps
    |--------------------------------------------------------------------------
    */

    $steps = [

        [
            'title' => 'Assigned To Rider',
            'description' => 'Delivery assignment received.',
            'status' => 'Completed',
        ],

        [
            'title' => 'Picked Up From Sorting Center',
            'description' => 'Parcel collected from the sorting center.',
            'status' => 'Completed',
        ],

        [
            'title' => 'Out For Delivery',
            'description' => 'Parcel is currently being delivered.',
            'status' => 'Current',
        ],

        [
            'title' => 'Customer Received Parcel',
            'description' => 'Waiting for customer to receive the parcel.',
            'status' => 'Pending',
        ],

        [
            'title' => 'Completed',
            'description' => 'Delivery will be completed after confirmation.',
            'status' => 'Pending',
        ],

    ];

@endphp


<div class="mx-auto w-full max-w-[1270px] space-y-6">


    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <section
        class="
            border-b
            border-line
            pb-5
        "
    >

        <div
            class="
                flex
                flex-col
                gap-4
                sm:flex-row
                sm:items-start
                sm:justify-between
            "
        >

            <div>

                <p
                    class="
                        text-[10px]
                        font-bold
                        uppercase
                        tracking-[0.18em]
                        text-primary
                    "
                >
                    Live Delivery
                </p>


                <h1
                    class="
                        mt-2
                        text-[28px]
                        font-bold
                        tracking-tight
                        text-ink
                        sm:text-[30px]
                    "
                >
                    Delivery Tracking
                </h1>


                <p class="mt-1 text-xs text-muted">
                    Track your delivery progress until the customer receives the parcel.
                </p>

            </div>


            {{-- CURRENT STATUS --}}

            <div
                class="
                    inline-flex
                    w-fit
                    items-center
                    gap-2
                    rounded-full
                    bg-primary-soft
                    px-4
                    py-2.5
                    text-[9px]
                    font-semibold
                    text-primary
                "
            >

                <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>

                OUT_FOR_DELIVERY

            </div>

        </div>

    </section>



    {{-- =========================================================
        MAIN GRID
    ========================================================== --}}

    <section
        class="
            grid
            gap-5
            xl:grid-cols-[minmax(0,1fr)_300px]
        "
    >


        {{-- =====================================================
            LEFT COLUMN
        ====================================================== --}}

        <div class="flex min-w-0 flex-col gap-5">


            {{-- =================================================
                DELIVERY ROUTE
            ================================================== --}}

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
                        flex
                        flex-col
                        gap-1
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                    "
                >

                    <div>

                        <h2 class="text-[15px] font-bold text-ink">
                            Delivery Route
                        </h2>

                        <p class="mt-1 text-[9px] text-muted">
                            Navigate to the customer's delivery location.
                        </p>

                    </div>


                    <span class="text-[9px] text-muted">
                        {{ $trackingNumber }}
                    </span>

                </div>



                {{-- MAP PLACEHOLDER --}}

                <div
                    class="
                        relative
                        mt-5
                        h-[280px]
                        overflow-hidden
                        rounded-xl
                        border
                        border-line
                        bg-page-secondary
                    "
                >

                    {{-- MAP GRID --}}

                    <div
                        class="
                            absolute
                            inset-0
                            opacity-30
                        "
                        style="
                            background-image:
                            linear-gradient(to right, currentColor 1px, transparent 1px),
                            linear-gradient(to bottom, currentColor 1px, transparent 1px);
                            background-size: 45px 45px;
                        "
                    ></div>


                    {{-- ROUTE LINE --}}

                    <div
                        class="
                            absolute
                            left-[20%]
                            top-[65%]
                            h-[2px]
                            w-[60%]
                            rotate-[-12deg]
                            bg-primary
                        "
                    ></div>


                    {{-- RIDER LOCATION --}}

                    <div
                        class="
                            absolute
                            left-[20%]
                            top-[65%]
                            grid
                            h-10
                            w-10
                            -translate-x-1/2
                            -translate-y-1/2
                            place-items-center
                            rounded-full
                            border-4
                            border-surface
                            bg-primary
                            text-white
                            shadow-sm
                        "
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-4 w-4 fill-none stroke-current"
                        >

                            <path
                                d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"
                                stroke-width="1.7"
                            />

                            <rect
                                x="9"
                                y="11"
                                width="14"
                                height="10"
                                rx="2"
                                stroke-width="1.7"
                            />

                            <circle
                                cx="12"
                                cy="21"
                                r="1"
                                stroke-width="1.7"
                            />

                            <circle
                                cx="20"
                                cy="21"
                                r="1"
                                stroke-width="1.7"
                            />

                        </svg>

                    </div>


                    {{-- CUSTOMER LOCATION --}}

                    <div
                        class="
                            absolute
                            right-[20%]
                            top-[35%]
                            grid
                            h-10
                            w-10
                            translate-x-1/2
                            -translate-y-1/2
                            place-items-center
                            rounded-full
                            border-4
                            border-surface
                            bg-success
                            text-white
                            shadow-sm
                        "
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-4 w-4 fill-none stroke-current"
                        >

                            <path
                                d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"
                                stroke-width="1.7"
                            />

                            <circle
                                cx="12"
                                cy="10"
                                r="2"
                                stroke-width="1.7"
                            />

                        </svg>

                    </div>


                    {{-- MAP LABEL --}}

                    <div
                        class="
                            absolute
                            bottom-4
                            left-4
                            rounded-lg
                            border
                            border-line
                            bg-surface
                            px-3
                            py-2
                            shadow-sm
                        "
                    >

                        <p class="text-[8px] uppercase tracking-wide text-muted">
                            Destination
                        </p>

                        <p class="mt-0.5 text-[9px] font-semibold text-ink">
                            {{ $customerAddress }}
                        </p>

                    </div>


                    {{-- LIVE INDICATOR --}}

                    <div
                        class="
                            absolute
                            right-4
                            top-4
                            inline-flex
                            items-center
                            gap-1.5
                            rounded-full
                            bg-surface
                            px-3
                            py-1.5
                            text-[8px]
                            font-semibold
                            text-success
                            shadow-sm
                        "
                    >

                        <span class="h-1.5 w-1.5 rounded-full bg-success"></span>

                        Live Route

                    </div>

                </div>


                {{-- NAVIGATION --}}

                <button
                    id="openRouteBtn"
                    type="button"
                    class="
                        mt-4
                        flex
                        w-full
                        items-center
                        justify-center
                        gap-2
                        rounded-lg
                        bg-primary
                        py-3
                        text-[9px]
                        font-semibold
                        text-white
                        transition
                        hover:opacity-90
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-3.5 w-3.5 fill-none stroke-current"
                    >

                        <path
                            d="M12 19V5"
                            stroke-width="1.8"
                        />

                        <path
                            d="m6 11 6-6 6 6"
                            stroke-width="1.8"
                        />

                    </svg>

                    Open Navigation

                </button>

            </section>



            {{-- =================================================
                DELIVERY STATUS
            ================================================== --}}

            <section
                class="
                    rounded-xl
                    border
                    border-line
                    bg-surface
                    p-5
                "
            >

                <div>

                    <h2 class="text-[15px] font-bold text-ink">
                        Delivery Status
                    </h2>

                    <p class="mt-1 text-[9px] text-muted">
                        Follow the parcel's progress.
                    </p>

                </div>



                <div class="relative mt-6">


                    {{-- CONNECTING LINE --}}

                    <div
                        class="
                            absolute
                            left-[15px]
                            top-4
                            bottom-4
                            w-px
                            bg-line
                        "
                    ></div>


                    <div class="relative space-y-5">

                        @foreach($steps as $index => $step)

                            @php

                                if ($step['status'] === 'Completed') {

                                    $circleClass = 'bg-success text-white';

                                } elseif ($step['status'] === 'Current') {

                                    $circleClass = 'bg-primary text-white ring-4 ring-primary-soft';

                                } else {

                                    $circleClass = 'bg-page-secondary text-muted border border-line';

                                }

                            @endphp


                            <div class="flex gap-4">


                                {{-- STEP NUMBER --}}

                                <div
                                    class="
                                        relative
                                        z-10
                                        grid
                                        h-8
                                        w-8
                                        shrink-0
                                        place-items-center
                                        rounded-full
                                        text-[8px]
                                        font-bold
                                        {{ $circleClass }}
                                    "
                                >

                                    @if($step['status'] === 'Completed')

                                        <svg
                                            viewBox="0 0 24 24"
                                            class="h-3.5 w-3.5 fill-none stroke-current"
                                        >

                                            <path
                                                d="m5 12 4 4 10-10"
                                                stroke-width="2"
                                            />

                                        </svg>

                                    @else

                                        {{ $index + 1 }}

                                    @endif

                                </div>



                                {{-- STEP CONTENT --}}

                                <div class="min-w-0 flex-1 pb-1">

                                    <div
                                        class="
                                            flex
                                            flex-col
                                            gap-1
                                            sm:flex-row
                                            sm:items-center
                                            sm:justify-between
                                        "
                                    >

                                        <h3 class="text-[10px] font-semibold text-ink">
                                            {{ $step['title'] }}
                                        </h3>


                                        @if($step['status'] === 'Current')

                                            <span
                                                class="
                                                    w-fit
                                                    rounded-full
                                                    bg-primary-soft
                                                    px-2.5
                                                    py-1
                                                    text-[7px]
                                                    font-semibold
                                                    text-primary
                                                "
                                            >
                                                Current
                                            </span>

                                        @elseif($step['status'] === 'Completed')

                                            <span
                                                class="
                                                    w-fit
                                                    rounded-full
                                                    bg-success-soft
                                                    px-2.5
                                                    py-1
                                                    text-[7px]
                                                    font-semibold
                                                    text-success
                                                "
                                            >
                                                Completed
                                            </span>

                                        @else

                                            <span
                                                class="
                                                    w-fit
                                                    rounded-full
                                                    bg-page-secondary
                                                    px-2.5
                                                    py-1
                                                    text-[7px]
                                                    font-semibold
                                                    text-muted
                                                "
                                            >
                                                Pending
                                            </span>

                                        @endif

                                    </div>


                                    <p class="mt-1 text-[8px] text-muted">
                                        {{ $step['description'] }}
                                    </p>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </section>


        </div>



        {{-- =====================================================
            RIGHT COLUMN
        ====================================================== --}}

        <aside class="flex flex-col gap-5">


            {{-- CUSTOMER --}}

            <section
                class="
                    rounded-xl
                    border
                    border-line
                    bg-surface
                    p-5
                "
            >

                <div class="flex items-center justify-between">

                    <h2 class="text-[15px] font-bold text-ink">
                        Customer
                    </h2>

                    <span
                        class="
                            grid
                            h-8
                            w-8
                            place-items-center
                            rounded-lg
                            bg-primary-soft
                            text-[9px]
                            font-bold
                            text-primary
                        "
                    >
                        JD
                    </span>

                </div>


                <div class="mt-4 space-y-3">


                    <div class="rounded-lg bg-page-secondary p-3.5">

                        <p class="text-[8px] uppercase tracking-wide text-muted">
                            Name
                        </p>

                        <p class="mt-1 text-[10px] font-semibold text-ink">
                            {{ $customerName }}
                        </p>

                    </div>


                    <div class="rounded-lg bg-page-secondary p-3.5">

                        <p class="text-[8px] uppercase tracking-wide text-muted">
                            Contact
                        </p>

                        <p class="mt-1 text-[10px] font-semibold text-ink">
                            {{ $customerPhone }}
                        </p>

                    </div>


                    <div class="rounded-lg bg-page-secondary p-3.5">

                        <p class="text-[8px] uppercase tracking-wide text-muted">
                            Delivery Address
                        </p>

                        <p class="mt-1 text-[10px] font-semibold leading-4 text-ink">
                            {{ $customerAddress }}
                        </p>

                    </div>

                </div>


                {{-- CALL CUSTOMER --}}

                <button
                    id="contactCustomerBtn"
                    type="button"
                    class="
                        mt-4
                        flex
                        w-full
                        items-center
                        justify-center
                        gap-2
                        rounded-lg
                        border
                        border-line
                        py-2.5
                        text-[9px]
                        font-semibold
                        text-ink
                        transition
                        hover:bg-page-secondary
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-3.5 w-3.5 fill-none stroke-current text-primary"
                    >

                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92Z"
                            stroke-width="1.6"
                        />

                    </svg>

                    Contact Customer

                </button>

            </section>



            {{-- ACTIONS --}}

            <section
                class="
                    rounded-xl
                    border
                    border-line
                    bg-surface
                    p-5
                "
            >

                <h2 class="text-[15px] font-bold text-ink">
                    Delivery Actions
                </h2>


                <div class="mt-4 flex flex-col gap-2.5">


                    {{-- ARRIVED --}}

                    <button
                        id="arrivedCustomerBtn"
                        type="button"
                        class="
                            w-full
                            rounded-lg
                            border
                            border-line
                            px-4
                            py-3
                            text-[9px]
                            font-semibold
                            text-ink
                            transition
                            hover:bg-page-secondary
                        "
                    >
                        Arrived At Customer
                    </button>


                    {{-- DELIVERED --}}

                    <button
                        id="markDeliveredBtn"
                        type="button"
                        class="
                            w-full
                            rounded-lg
                            bg-success
                            px-4
                            py-3
                            text-[9px]
                            font-semibold
                            text-white
                            transition
                            hover:opacity-90
                        "
                    >
                        Mark As Delivered
                    </button>


                    {{-- FAILED --}}

                    <button
                        id="failedDeliveryBtn"
                        type="button"
                        class="
                            w-full
                            rounded-lg
                            bg-danger
                            px-4
                            py-3
                            text-[9px]
                            font-semibold
                            text-white
                            transition
                            hover:opacity-90
                        "
                    >
                        Delivery Failed
                    </button>

                </div>

            </section>



            {{-- =================================================
                PARCEL SUMMARY
            ================================================== --}}

            <section
                class="
                    rounded-xl
                    bg-primary
                    p-5
                    text-white
                "
            >

                <p
                    class="
                        text-[8px]
                        font-bold
                        uppercase
                        tracking-[0.15em]
                        text-white/70
                    "
                >
                    Tracking Number
                </p>


                <h2 class="mt-2 text-[18px] font-bold">
                    {{ $trackingNumber }}
                </h2>


                <div class="mt-4 border-t border-white/15 pt-3">

                    <div class="flex items-center justify-between">

                        <span class="text-[8px] text-white/70">
                            Payment
                        </span>

                        <span class="text-[9px] font-semibold">
                            Cash On Delivery
                        </span>

                    </div>


                    <div class="mt-2 flex items-center justify-between">

                        <span class="text-[8px] text-white/70">
                            COD Amount
                        </span>

                        <span class="text-[11px] font-bold">
                            {{ $codAmount }}
                        </span>

                    </div>

                </div>

            </section>



            {{-- NEXT STATUS --}}

            <section
                class="
                    rounded-xl
                    border
                    border-primary/20
                    bg-primary-soft
                    p-5
                "
            >

                <p
                    class="
                        text-[8px]
                        font-bold
                        uppercase
                        tracking-[0.15em]
                        text-primary
                    "
                >
                    Next Step
                </p>


                <h2 class="mt-2 text-[13px] font-bold text-ink">
                    Customer Receives Parcel
                </h2>


                <p class="mt-1 text-[8px] leading-4 text-muted">
                    Confirm delivery after successfully handing the parcel to the customer.
                </p>

            </section>


        </aside>

    </section>


</div>


@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    const openRouteBtn = document.getElementById('openRouteBtn');

    openRouteBtn?.addEventListener('click', function () {

        const address = @json($customerAddress);

        const mapsUrl =
            'https://www.google.com/maps/search/?api=1&query=' +
            encodeURIComponent(address);

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

    const contactCustomerBtn =
        document.getElementById('contactCustomerBtn');

    contactCustomerBtn?.addEventListener('click', function () {

        window.location.href =
            'tel:' + @json($customerPhone);

    });



    /*
    |--------------------------------------------------------------------------
    | Arrived At Customer
    |--------------------------------------------------------------------------
    */

    const arrivedCustomerBtn =
        document.getElementById('arrivedCustomerBtn');

    arrivedCustomerBtn?.addEventListener('click', function () {

        this.textContent = 'Customer Reached ✓';

        this.disabled = true;

        this.classList.remove(
            'border-line',
            'text-ink'
        );

        this.classList.add(
            'border-success',
            'bg-success-soft',
            'text-success'
        );

    });



    /*
    |--------------------------------------------------------------------------
    | Mark Delivered
    |--------------------------------------------------------------------------
    */

    const markDeliveredBtn =
        document.getElementById('markDeliveredBtn');

    markDeliveredBtn?.addEventListener('click', function () {

        const button = this;

        button.textContent = 'Delivered ✓';

        button.disabled = true;

        button.classList.remove(
            'bg-success'
        );

        button.classList.add(
            'bg-success-soft',
            'text-success'
        );


        /*
        |--------------------------------------------------------------------------
        | Update status badge
        |--------------------------------------------------------------------------
        */

        const statusBadge =
            document.querySelector(
                'section:first-of-type .bg-primary-soft'
            );

        if (statusBadge) {

            statusBadge.classList.remove(
                'bg-primary-soft',
                'text-primary'
            );

            statusBadge.classList.add(
                'bg-success-soft',
                'text-success'
            );

            statusBadge.innerHTML =
                '<span class="h-1.5 w-1.5 rounded-full bg-success"></span>' +
                ' DELIVERED';

        }

    });



    /*
    |--------------------------------------------------------------------------
    | Delivery Failed
    |--------------------------------------------------------------------------
    */

    const failedDeliveryBtn =
        document.getElementById('failedDeliveryBtn');

    failedDeliveryBtn?.addEventListener('click', function () {

        const button = this;

        button.textContent = 'Delivery Marked Failed';

        button.disabled = true;

        button.classList.remove(
            'bg-danger'
        );

        button.classList.add(
            'bg-danger-soft',
            'text-danger'
        );

    });

});

</script>

@endpush