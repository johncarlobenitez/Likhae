@extends('logistics.app')

@section('title', 'Receive Parcel — LIKHAE Logistics')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | FRONTEND SAMPLE DATA
    |--------------------------------------------------------------------------
    | Temporary data only.
    | Replace with controller/database data later.
    */

    $parcel = [
        'id' => 1001,
        'tracking' => 'LH-2026-1001',
        'order' => 'ORD-2026-1045',
        'seller' => 'Habing Lokal',
        'buyer' => 'Juan Dela Cruz',
        'address' => '21 Rizal Street, Brgy. Bubukal, Santa Cruz, Laguna',
        'destination' => 'Santa Cruz, Laguna',
        'area' => 'Unassigned',
        'payment' => 'Cash on Delivery',
        'status' => 'Incoming Parcel',
        'received_from' => 'Seller / Drop-off',
        'condition' => 'Good',
        'items' => [
            [
                'name' => 'Handwoven Abaca Bag',
                'variation' => 'Natural / Medium',
                'quantity' => 1,
                'price' => 1250,
            ],
            [
                'name' => 'Embroidered Pouch',
                'variation' => 'Burgundy / Standard',
                'quantity' => 1,
                'price' => 450,
            ],
        ],
    ];


    $total = collect($parcel['items'])->sum(
        fn ($item) => $item['price'] * $item['quantity']
    );

@endphp


<div class="flex w-full flex-col gap-6">

    {{-- =====================================================
        BREADCRUMB
    ====================================================== --}}

    <nav
        class="
            flex
            flex-wrap
            items-center
            gap-2
            text-[10px]
            text-muted
        "
    >

        <a
            href="{{ route('logistics.dashboard') }}"
            class="transition hover:text-primary"
        >
            Dashboard
        </a>

        <span>/</span>

        <a
            href="{{ route('logistics.parcels') }}"
            class="transition hover:text-primary"
        >
            Parcels
        </a>

        <span>/</span>

        <span class="font-semibold text-ink">
            Receive Parcel
        </span>

    </nav>


    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}

    <section
        class="
            flex
            flex-col
            gap-5
            lg:flex-row
            lg:items-end
            lg:justify-between
        "
    >

        <div>

            <span
                class="
                    text-[10px]
                    font-bold
                    uppercase
                    tracking-[0.18em]
                    text-primary
                "
            >
                Parcel Intake
            </span>


            <h1
                class="
                    mt-2
                    font-display
                    text-[30px]
                    font-semibold
                    tracking-[-0.04em]
                    text-ink
                    sm:text-[34px]
                "
            >
                Receive a parcel.
            </h1>


            <p
                class="
                    mt-2
                    max-w-[650px]
                    text-[11px]
                    leading-6
                    text-muted
                "
            >
                Scan an incoming parcel, verify its information, inspect the package,
                and confirm that it has arrived at the LIKHAE sorting center.
            </p>

        </div>


        <a
            href="{{ route('logistics.parcels') }}"
            class="
                inline-flex
                h-10
                shrink-0
                items-center
                justify-center
                gap-2
                rounded-lg
                border
                border-line
                bg-surface
                px-4
                text-[10px]
                font-semibold
                text-ink
                transition
                hover:bg-surface-hover
            "
        >
            ← Back to Parcels
        </a>

    </section>


    {{-- =====================================================
        QUICK SCANNER
    ====================================================== --}}

    <section
        class="
            overflow-hidden
            rounded-xl
            border
            border-primary/20
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
                lg:flex-row
                lg:items-center
                lg:justify-between
            "
        >

            <div class="max-w-[520px]">

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
                            tracking-[0.14em]
                            text-white/65
                        "
                    >
                        Quick Parcel Scanner
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
                    Scan or enter the tracking number
                </h2>


                <p
                    class="
                        mt-1
                        text-[10px]
                        leading-5
                        text-white/60
                    "
                >
                    Use a barcode scanner or manually enter the parcel tracking ID.
                </p>

            </div>


            <div
                class="
                    flex
                    w-full
                    flex-col
                    gap-2
                    sm:flex-row
                    lg:max-w-[520px]
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
                        id="receiveTrackingNumber"
                        value="{{ $parcel['tracking'] }}"
                        placeholder="LH-2026-1001"
                        class="
                            h-11
                            w-full
                            rounded-lg
                            border-0
                            bg-white
                            pl-10
                            pr-4
                            text-[11px]
                            font-medium
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
                    id="loadParcelButton"
                    class="
                        inline-flex
                        h-11
                        items-center
                        justify-center
                        gap-2
                        rounded-lg
                        bg-white
                        px-5
                        text-[10px]
                        font-semibold
                        text-[#74181c]
                        transition
                        hover:bg-[#f7f3ec]
                    "
                >
                    Load Parcel
                    <span>→</span>
                </button>

            </div>

        </div>

    </section>


    {{-- =====================================================
        MAIN GRID
    ====================================================== --}}

    <section
        class="
            grid
            gap-4
            xl:grid-cols-[minmax(0,1fr)_320px]
        "
    >

        {{-- =================================================
            LEFT COLUMN
        ================================================== --}}

        <div class="flex min-w-0 flex-col gap-4">

            {{-- PARCEL INFORMATION --}}

            <section
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
                        gap-4
                        border-b
                        border-line
                        px-5
                        py-4
                    "
                >

                    <div>

                        <h2
                            class="
                                text-[13px]
                                font-semibold
                                text-ink
                            "
                        >
                            Parcel Information
                        </h2>

                        <p
                            class="
                                mt-0.5
                                text-[9px]
                                text-muted
                            "
                        >
                            Verify the parcel against the order record.
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
                        {{ $parcel['status'] }}
                    </span>

                </div>


                <div
                    class="
                        grid
                        gap-px
                        bg-line
                        sm:grid-cols-2
                        lg:grid-cols-3
                    "
                >

                    <div class="bg-surface p-5">

                        <span
                            class="
                                text-[8px]
                                font-semibold
                                uppercase
                                tracking-[0.1em]
                                text-muted
                            "
                        >
                            Tracking Number
                        </span>

                        <strong
                            class="
                                mt-2
                                block
                                text-[11px]
                                font-semibold
                                text-ink
                            "
                        >
                            {{ $parcel['tracking'] }}
                        </strong>

                    </div>


                    <div class="bg-surface p-5">

                        <span
                            class="
                                text-[8px]
                                font-semibold
                                uppercase
                                tracking-[0.1em]
                                text-muted
                            "
                        >
                            Order Number
                        </span>

                        <strong
                            class="
                                mt-2
                                block
                                text-[11px]
                                font-semibold
                                text-ink
                            "
                        >
                            {{ $parcel['order'] }}
                        </strong>

                    </div>


                    <div class="bg-surface p-5">

                        <span
                            class="
                                text-[8px]
                                font-semibold
                                uppercase
                                tracking-[0.1em]
                                text-muted
                            "
                        >
                            Payment
                        </span>

                        <strong
                            class="
                                mt-2
                                block
                                text-[11px]
                                font-semibold
                                text-ink
                            "
                        >
                            {{ $parcel['payment'] }}
                        </strong>

                    </div>


                    <div class="bg-surface p-5">

                        <span
                            class="
                                text-[8px]
                                font-semibold
                                uppercase
                                tracking-[0.1em]
                                text-muted
                            "
                        >
                            Seller
                        </span>

                        <strong
                            class="
                                mt-2
                                block
                                text-[11px]
                                font-semibold
                                text-ink
                            "
                        >
                            {{ $parcel['seller'] }}
                        </strong>

                    </div>


                    <div class="bg-surface p-5">

                        <span
                            class="
                                text-[8px]
                                font-semibold
                                uppercase
                                tracking-[0.1em]
                                text-muted
                            "
                        >
                            Buyer
                        </span>

                        <strong
                            class="
                                mt-2
                                block
                                text-[11px]
                                font-semibold
                                text-ink
                            "
                        >
                            {{ $parcel['buyer'] }}
                        </strong>

                    </div>


                    <div class="bg-surface p-5">

                        <span
                            class="
                                text-[8px]
                                font-semibold
                                uppercase
                                tracking-[0.1em]
                                text-muted
                            "
                        >
                            Order Value
                        </span>

                        <strong
                            class="
                                mt-2
                                block
                                text-[11px]
                                font-semibold
                                text-ink
                            "
                        >
                            ₱{{ number_format($total, 2) }}
                        </strong>

                    </div>

                </div>


                <div
                    class="
                        border-t
                        border-line
                        p-5
                    "
                >

                    <span
                        class="
                            text-[8px]
                            font-semibold
                            uppercase
                            tracking-[0.1em]
                            text-muted
                        "
                    >
                        Delivery Address
                    </span>


                    <div
                        class="
                            mt-3
                            flex
                            items-start
                            gap-3
                            rounded-lg
                            bg-page-secondary
                            p-4
                        "
                    >

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
                                class="
                                    h-4
                                    w-4
                                    fill-none
                                    stroke-current
                                    stroke-[1.6]
                                "
                            >
                                <path
                                    d="
                                        M12 21
                                        s7-5 7-11
                                        a7 7 0 1 0-14 0
                                        c0 6 7 11 7 11Z
                                    "
                                ></path>

                                <circle cx="12" cy="10" r="2"></circle>
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
                                {{ $parcel['destination'] }}
                            </strong>

                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    leading-5
                                    text-muted
                                "
                            >
                                {{ $parcel['address'] }}
                            </p>

                        </div>

                    </div>

                </div>

            </section>


            {{-- ORDER ITEMS --}}

            <section
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

                    <h2
                        class="
                            text-[13px]
                            font-semibold
                            text-ink
                        "
                    >
                        Order Items
                    </h2>

                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            text-muted
                        "
                    >
                        Items expected inside the parcel.
                    </p>

                </div>


                <div class="divide-y divide-line">

                    @foreach($parcel['items'] as $item)

                        <article
                            class="
                                flex
                                items-center
                                gap-4
                                px-5
                                py-4
                            "
                        >

                            <span
                                class="
                                    grid
                                    h-11
                                    w-11
                                    shrink-0
                                    place-items-center
                                    rounded-lg
                                    bg-primary-soft
                                    text-primary
                                "
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="
                                        h-5
                                        w-5
                                        fill-none
                                        stroke-current
                                        stroke-[1.6]
                                    "
                                >
                                    <path d="M21 8 12 3 3 8l9 5 9-5Z"></path>
                                    <path d="M3 8v8l9 5 9-5V8"></path>
                                </svg>
                            </span>


                            <div class="min-w-0 flex-1">

                                <strong
                                    class="
                                        block
                                        text-[10px]
                                        font-semibold
                                        text-ink
                                    "
                                >
                                    {{ $item['name'] }}
                                </strong>

                                <span
                                    class="
                                        mt-1
                                        block
                                        text-[8px]
                                        text-muted
                                    "
                                >
                                    {{ $item['variation'] }}
                                </span>

                            </div>


                            <div class="text-right">

                                <strong
                                    class="
                                        block
                                        text-[10px]
                                        font-semibold
                                        text-ink
                                    "
                                >
                                    ₱{{ number_format($item['price'], 2) }}
                                </strong>

                                <span
                                    class="
                                        mt-1
                                        block
                                        text-[8px]
                                        text-muted
                                    "
                                >
                                    Qty {{ $item['quantity'] }}
                                </span>

                            </div>

                        </article>

                    @endforeach

                </div>


                <div
                    class="
                        flex
                        items-center
                        justify-between
                        border-t
                        border-line
                        bg-page-secondary
                        px-5
                        py-4
                    "
                >

                    <span
                        class="
                            text-[10px]
                            font-semibold
                            text-muted
                        "
                    >
                        Parcel Total
                    </span>


                    <strong
                        class="
                            text-[13px]
                            font-bold
                            text-ink
                        "
                    >
                        ₱{{ number_format($total, 2) }}
                    </strong>

                </div>

            </section>


            {{-- RECEIVING CHECKLIST --}}

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

                    <h2
                        class="
                            text-[13px]
                            font-semibold
                            text-ink
                        "
                    >
                        Receiving Checklist
                    </h2>

                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            text-muted
                        "
                    >
                        Complete all checks before confirming the parcel.
                    </p>

                </div>


                <div class="mt-5 grid gap-3">

                    <label
                        class="
                            flex
                            cursor-pointer
                            items-start
                            gap-3
                            rounded-lg
                            border
                            border-line
                            bg-page-secondary
                            p-4
                            transition
                            hover:border-primary/30
                        "
                    >

                        <input
                            type="checkbox"
                            class="
                                receive-check
                                mt-0.5
                                h-4
                                w-4
                                rounded
                                border-line
                                accent-[#74181c]
                            "
                            checked
                        >


                        <span>

                            <strong
                                class="
                                    block
                                    text-[10px]
                                    font-semibold
                                    text-ink
                                "
                            >
                                Tracking number verified
                            </strong>

                            <span
                                class="
                                    mt-1
                                    block
                                    text-[8px]
                                    leading-4
                                    text-muted
                                "
                            >
                                The physical label matches the parcel record.
                            </span>

                        </span>

                    </label>


                    <label
                        class="
                            flex
                            cursor-pointer
                            items-start
                            gap-3
                            rounded-lg
                            border
                            border-line
                            bg-page-secondary
                            p-4
                            transition
                            hover:border-primary/30
                        "
                    >

                        <input
                            type="checkbox"
                            class="
                                receive-check
                                mt-0.5
                                h-4
                                w-4
                                rounded
                                border-line
                                accent-[#74181c]
                            "
                            checked
                        >


                        <span>

                            <strong
                                class="
                                    block
                                    text-[10px]
                                    font-semibold
                                    text-ink
                                "
                            >
                                Parcel physically received
                            </strong>

                            <span
                                class="
                                    mt-1
                                    block
                                    text-[8px]
                                    leading-4
                                    text-muted
                                "
                            >
                                The parcel is currently inside the sorting center.
                            </span>

                        </span>

                    </label>


                    <label
                        class="
                            flex
                            cursor-pointer
                            items-start
                            gap-3
                            rounded-lg
                            border
                            border-line
                            bg-page-secondary
                            p-4
                            transition
                            hover:border-primary/30
                        "
                    >

                        <input
                            type="checkbox"
                            class="
                                receive-check
                                mt-0.5
                                h-4
                                w-4
                                rounded
                                border-line
                                accent-[#74181c]
                            "
                        >


                        <span>

                            <strong
                                class="
                                    block
                                    text-[10px]
                                    font-semibold
                                    text-ink
                                "
                            >
                                Package condition inspected
                            </strong>

                            <span
                                class="
                                    mt-1
                                    block
                                    text-[8px]
                                    leading-4
                                    text-muted
                                "
                            >
                                Check the outer packaging for visible damage or tampering.
                            </span>

                        </span>

                    </label>

                </div>

            </section>


            {{-- RECEIVING DETAILS --}}

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

                    <h2
                        class="
                            text-[13px]
                            font-semibold
                            text-ink
                        "
                    >
                        Receiving Details
                    </h2>

                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            text-muted
                        "
                    >
                        Record how the parcel was received.
                    </p>

                </div>


                <div
                    class="
                        mt-5
                        grid
                        gap-4
                        md:grid-cols-2
                    "
                >

                    <div>

                        <label
                            for="packageCondition"
                            class="
                                mb-2
                                block
                                text-[9px]
                                font-semibold
                                text-ink
                            "
                        >
                            Package Condition
                        </label>


                        <select
                            id="packageCondition"
                            class="
                                h-10
                                w-full
                                rounded-lg
                                border
                                border-line
                                bg-page-secondary
                                px-3
                                text-[10px]
                                text-ink
                                outline-none
                                transition
                                focus:border-primary
                                focus:ring-2
                                focus:ring-primary/10
                            "
                        >
                            <option value="Good" selected>
                                Good
                            </option>

                            <option value="Minor Damage">
                                Minor Damage
                            </option>

                            <option value="Damaged">
                                Damaged
                            </option>

                            <option value="Tampered">
                                Tampered
                            </option>
                        </select>

                    </div>


                    <div>

                        <label
                            for="receivedFrom"
                            class="
                                mb-2
                                block
                                text-[9px]
                                font-semibold
                                text-ink
                            "
                        >
                            Received From
                        </label>


                        <select
                            id="receivedFrom"
                            class="
                                h-10
                                w-full
                                rounded-lg
                                border
                                border-line
                                bg-page-secondary
                                px-3
                                text-[10px]
                                text-ink
                                outline-none
                                transition
                                focus:border-primary
                                focus:ring-2
                                focus:ring-primary/10
                            "
                        >
                            <option selected>
                                Seller / Drop-off
                            </option>

                            <option>
                                Courier Transfer
                            </option>

                            <option>
                                Seller Representative
                            </option>

                            <option>
                                Other
                            </option>
                        </select>

                    </div>

                </div>


                <div class="mt-4">

                    <label
                        for="receivingNotes"
                        class="
                            mb-2
                            block
                            text-[9px]
                            font-semibold
                            text-ink
                        "
                    >
                        Receiving Notes
                    </label>


                    <textarea
                        id="receivingNotes"
                        rows="4"
                        placeholder="Add package observations or receiving notes..."
                        class="
                            w-full
                            resize-none
                            rounded-lg
                            border
                            border-line
                            bg-page-secondary
                            px-3
                            py-3
                            text-[10px]
                            leading-5
                            text-ink
                            outline-none
                            transition
                            placeholder:text-muted-light
                            focus:border-primary
                            focus:ring-2
                            focus:ring-primary/10
                        "
                    ></textarea>

                </div>

            </section>

        </div>


        {{-- =================================================
            RIGHT COLUMN
        ================================================== --}}

        <aside class="flex flex-col gap-4">

            {{-- CURRENT STATUS --}}

            <section
                class="
                    overflow-hidden
                    rounded-xl
                    bg-primary
                    p-5
                    text-white
                    shadow-likhae
                "
            >

                <span
                    class="
                        text-[8px]
                        font-semibold
                        uppercase
                        tracking-[0.14em]
                        text-white/55
                    "
                >
                    Current Status
                </span>


                <h2
                    class="
                        mt-3
                        font-display
                        text-[24px]
                        font-semibold
                        tracking-[-0.035em]
                        text-white
                    "
                >
                    Incoming Parcel
                </h2>


                <p
                    class="
                        mt-2
                        text-[9px]
                        leading-5
                        text-white/60
                    "
                >
                    Waiting for receiving confirmation at the sorting center.
                </p>


                <div
                    class="
                        mt-5
                        rounded-lg
                        border
                        border-white/10
                        bg-white/[0.08]
                        p-4
                    "
                >

                    <span
                        class="
                            text-[8px]
                            uppercase
                            tracking-[0.1em]
                            text-white/45
                        "
                    >
                        Tracking
                    </span>

                    <strong
                        class="
                            mt-1.5
                            block
                            text-[12px]
                            font-semibold
                            text-white
                        "
                    >
                        {{ $parcel['tracking'] }}
                    </strong>

                </div>

            </section>


            {{-- DESTINATION --}}

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
                        items-center
                        gap-3
                    "
                >

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
                            class="
                                h-4
                                w-4
                                fill-none
                                stroke-current
                                stroke-[1.6]
                            "
                        >
                            <path
                                d="
                                    M12 21
                                    s7-5 7-11
                                    a7 7 0 1 0-14 0
                                    c0 6 7 11 7 11Z
                                "
                            ></path>

                            <circle cx="12" cy="10" r="2"></circle>
                        </svg>
                    </span>


                    <div>

                        <span
                            class="
                                text-[8px]
                                font-semibold
                                uppercase
                                tracking-[0.1em]
                                text-muted
                            "
                        >
                            Destination
                        </span>

                        <strong
                            class="
                                mt-1
                                block
                                text-[11px]
                                font-semibold
                                text-ink
                            "
                        >
                            {{ $parcel['destination'] }}
                        </strong>

                    </div>

                </div>


                <div
                    class="
                        mt-4
                        rounded-lg
                        bg-page-secondary
                        p-4
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            gap-3
                        "
                    >

                        <span
                            class="
                                text-[9px]
                                text-muted
                            "
                        >
                            Delivery Area
                        </span>

                        <strong
                            class="
                                text-[9px]
                                font-semibold
                                text-warning
                            "
                        >
                            To be determined
                        </strong>

                    </div>

                </div>

            </section>


            {{-- JOURNEY --}}

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

                    <h2
                        class="
                            text-[13px]
                            font-semibold
                            text-ink
                        "
                    >
                        Parcel Journey
                    </h2>

                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            text-muted
                        "
                    >
                        Expected logistics workflow.
                    </p>

                </div>


                <div class="mt-5">

                    @php

                        $journey = [
                            [
                                'title' => 'Receive Parcel',
                                'state' => 'current',
                            ],
                            [
                                'title' => 'Sort by Destination',
                                'state' => 'pending',
                            ],
                            [
                                'title' => 'Assign Rider',
                                'state' => 'pending',
                            ],
                            [
                                'title' => 'Out for Delivery',
                                'state' => 'pending',
                            ],
                            [
                                'title' => 'Delivered',
                                'state' => 'pending',
                            ],
                        ];

                    @endphp


                    @foreach($journey as $step)

                        <div
                            class="
                                relative
                                flex
                                gap-3
                                pb-5
                                last:pb-0
                            "
                        >

                            @if(!$loop->last)

                                <span
                                    class="
                                        absolute
                                        left-[13px]
                                        top-7
                                        h-[calc(100%-12px)]
                                        w-px
                                        bg-line
                                    "
                                ></span>

                            @endif


                            <span
                                class="
                                    relative
                                    z-10
                                    grid
                                    h-7
                                    w-7
                                    shrink-0
                                    place-items-center
                                    rounded-full
                                    border
                                    text-[8px]
                                    font-bold
                                    {{
                                        $step['state'] === 'current'
                                            ? 'border-primary bg-primary text-white ring-4 ring-primary/10'
                                            : 'border-line bg-page-secondary text-muted'
                                    }}
                                "
                            >
                                {{ $loop->iteration }}
                            </span>


                            <div class="pt-1">

                                <strong
                                    class="
                                        block
                                        text-[9px]
                                        font-semibold
                                        {{
                                            $step['state'] === 'current'
                                                ? 'text-primary'
                                                : 'text-muted'
                                        }}
                                    "
                                >
                                    {{ $step['title'] }}
                                </strong>


                                <span
                                    class="
                                        mt-1
                                        block
                                        text-[8px]
                                        text-muted-light
                                    "
                                >
                                    {{
                                        $step['state'] === 'current'
                                            ? 'Current step'
                                            : 'Pending'
                                    }}
                                </span>

                            </div>

                        </div>

                    @endforeach

                </div>

            </section>

        </aside>

    </section>


    {{-- =====================================================
        ACTION BAR
    ====================================================== --}}

    <section
        class="
            sticky
            bottom-4
            z-20
            rounded-xl
            border
            border-line
            bg-surface/95
            p-4
            shadow-likhae-lg
            backdrop-blur-xl
        "
    >

        <div
            class="
                flex
                flex-col
                gap-4
                sm:flex-row
                sm:items-center
                sm:justify-between
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
                    Ready to confirm this parcel?
                </strong>


                <span
                    id="checklistStatusText"
                    class="
                        mt-1
                        block
                        text-[9px]
                        text-warning
                    "
                >
                    2 of 3 receiving checks completed.
                </span>

            </div>


            <button
                type="button"
                id="confirmReceiveButton"
                class="
                    inline-flex
                    h-10
                    items-center
                    justify-center
                    gap-2
                    rounded-lg
                    bg-primary
                    px-5
                    text-[10px]
                    font-semibold
                    text-white
                    transition
                    hover:bg-primary-hover
                "
            >
                Confirm Parcel Received
                <span>→</span>
            </button>

        </div>

    </section>

</div>


{{-- =========================================================
    SUCCESS MODAL
========================================================= --}}

<div
    id="receiveSuccessModal"
    class="
        invisible
        fixed
        inset-0
        z-[100]
        grid
        place-items-center
        bg-black/50
        p-5
        opacity-0
        backdrop-blur-[2px]
        transition-all
        duration-200
    "
>

    <div
        class="
            w-full
            max-w-[430px]
            scale-95
            rounded-2xl
            border
            border-line
            bg-surface
            p-6
            shadow-likhae-lg
            transition-transform
            duration-200
        "
        id="receiveSuccessPanel"
    >

        <span
            class="
                mx-auto
                grid
                h-14
                w-14
                place-items-center
                rounded-full
                bg-success-soft
                text-success
            "
        >
            <svg
                viewBox="0 0 24 24"
                class="
                    h-6
                    w-6
                    fill-none
                    stroke-current
                    stroke-[2]
                "
            >
                <path d="m5 12 4 4 10-10"></path>
            </svg>
        </span>


        <div class="mt-5 text-center">

            <span
                class="
                    text-[9px]
                    font-bold
                    uppercase
                    tracking-[0.15em]
                    text-success
                "
            >
                Parcel Received
            </span>


            <h2
                class="
                    mt-2
                    font-display
                    text-[26px]
                    font-semibold
                    tracking-[-0.04em]
                    text-ink
                "
            >
                Ready for sorting.
            </h2>


            <p
                class="
                    mx-auto
                    mt-2
                    max-w-[330px]
                    text-[10px]
                    leading-5
                    text-muted
                "
            >
                {{ $parcel['tracking'] }} has been recorded as received
                and can now proceed to destination sorting.
            </p>

        </div>


        <div
            class="
                mt-6
                grid
                gap-2
                sm:grid-cols-2
            "
        >

            <a
                href="{{ route('logistics.parcels') }}"
                class="
                    inline-flex
                    h-10
                    items-center
                    justify-center
                    rounded-lg
                    border
                    border-line
                    bg-surface
                    px-4
                    text-[9px]
                    font-semibold
                    text-ink
                    transition
                    hover:bg-surface-hover
                "
            >
                Back to Parcels
            </a>


            <a
                href="{{ route('logistics.sorting') }}"
                class="
                    inline-flex
                    h-10
                    items-center
                    justify-center
                    rounded-lg
                    bg-primary
                    px-4
                    text-[9px]
                    font-semibold
                    text-white
                    transition
                    hover:bg-primary-hover
                "
            >
                Continue to Sorting
            </a>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const scannerInput =
        document.getElementById('receiveTrackingNumber');

    const loadButton =
        document.getElementById('loadParcelButton');

    const checkboxes =
        Array.from(
            document.querySelectorAll('.receive-check')
        );

    const checklistStatus =
        document.getElementById('checklistStatusText');

    const confirmButton =
        document.getElementById('confirmReceiveButton');

    const modal =
        document.getElementById('receiveSuccessModal');

    const modalPanel =
        document.getElementById('receiveSuccessPanel');


    /*
    |--------------------------------------------------------------------------
    | LOAD / SCAN PARCEL
    |--------------------------------------------------------------------------
    */

    function loadParcel() {

        const tracking =
            scannerInput?.value?.trim();

        if (!tracking) {

            scannerInput?.focus();

            return;
        }


        /*
        | Frontend prototype:
        | Later this should request parcel data from Laravel.
        */

        if (
            tracking.toUpperCase()
            !== @json($parcel['tracking'])
        ) {

            window.alert(
                'Demo mode: try tracking number {{ $parcel['tracking'] }}.'
            );

            return;
        }


        window.alert(
            'Parcel {{ $parcel['tracking'] }} loaded successfully.'
        );

    }


    loadButton?.addEventListener(
        'click',
        loadParcel
    );


    scannerInput?.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Enter') {

                event.preventDefault();

                loadParcel();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CHECKLIST
    |--------------------------------------------------------------------------
    */

    function updateChecklistStatus() {

        const completed =
            checkboxes.filter(
                checkbox => checkbox.checked
            ).length;


        const total =
            checkboxes.length;


        if (!checklistStatus) {
            return;
        }


        checklistStatus.textContent =
            `${completed} of ${total} receiving checks completed.`;


        checklistStatus.classList.remove(
            'text-warning',
            'text-success'
        );


        checklistStatus.classList.add(
            completed === total
                ? 'text-success'
                : 'text-warning'
        );

    }


    checkboxes.forEach(
        checkbox => {

            checkbox.addEventListener(
                'change',
                updateChecklistStatus
            );

        }
    );


    updateChecklistStatus();


    /*
    |--------------------------------------------------------------------------
    | SUCCESS MODAL
    |--------------------------------------------------------------------------
    */

    function openModal() {

        if (!modal || !modalPanel) {
            return;
        }


        modal.classList.remove(
            'invisible',
            'opacity-0'
        );


        modal.classList.add(
            'visible',
            'opacity-100'
        );


        modalPanel.classList.remove(
            'scale-95'
        );


        modalPanel.classList.add(
            'scale-100'
        );


        document.body.classList.add(
            'overflow-hidden'
        );

    }


    function closeModal() {

        if (!modal || !modalPanel) {
            return;
        }


        modal.classList.remove(
            'visible',
            'opacity-100'
        );


        modal.classList.add(
            'invisible',
            'opacity-0'
        );


        modalPanel.classList.remove(
            'scale-100'
        );


        modalPanel.classList.add(
            'scale-95'
        );


        document.body.classList.remove(
            'overflow-hidden'
        );

    }


    confirmButton?.addEventListener(
        'click',
        function () {

            const allComplete =
                checkboxes.every(
                    checkbox => checkbox.checked
                );


            if (!allComplete) {

                window.alert(
                    'Please complete all receiving checklist items before confirming the parcel.'
                );

                return;
            }


            openModal();

        }
    );


    modal?.addEventListener(
        'click',
        function (event) {

            if (event.target === modal) {
                closeModal();
            }

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {
                closeModal();
            }

        }
    );

});
</script>

@endpush
