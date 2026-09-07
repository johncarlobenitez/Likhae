@extends('logistics.app')

@section('title', 'Parcel Details — LIKHAE Logistics')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | FRONTEND SAMPLE DATA
    |--------------------------------------------------------------------------
    | Temporary sample records.
    | Replace with controller/database data later.
    */

    $parcelRecords = [

        1001 => [
            'id' => 1001,
            'tracking' => 'LH-2026-1001',
            'order' => 'ORD-2026-1045',
            'seller' => 'Habing Lokal',
            'buyer' => 'Juan Dela Cruz',
            'contact' => '0917 123 4567',
            'address' => '21 Rizal Street, Brgy. Bubukal, Santa Cruz, Laguna',
            'destination' => 'Santa Cruz, Laguna',
            'area' => 'Unassigned',
            'rider' => 'Not assigned',
            'payment' => 'Cash on Delivery',
            'value' => 1700,
            'status' => 'Waiting for Sorting',
            'status_key' => 'waiting_sorting',
            'condition' => 'Good',
            'received_from' => 'Seller / Drop-off',
            'received_at' => 'September 1, 2026 · 10:32 AM',
            'notes' => 'Parcel received in good condition. Ready for sorting.',
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
        ],

        1002 => [
            'id' => 1002,
            'tracking' => 'LH-2026-1002',
            'order' => 'ORD-2026-1046',
            'seller' => 'Gawang Laguna',
            'buyer' => 'Maria Santos',
            'contact' => '0918 456 7712',
            'address' => '18 Riverside Street, Brgy. Sampaloc, Pagsanjan, Laguna',
            'destination' => 'Pagsanjan, Laguna',
            'area' => 'Area B',
            'rider' => 'Not assigned',
            'payment' => 'GCash',
            'value' => 980,
            'status' => 'Awaiting Rider',
            'status_key' => 'awaiting_rider',
            'condition' => 'Good',
            'received_from' => 'Seller / Drop-off',
            'received_at' => 'September 1, 2026 · 10:15 AM',
            'notes' => 'Sorted under Area B. Waiting for available rider.',
            'items' => [
                [
                    'name' => 'Handmade Woven Basket',
                    'variation' => 'Brown / Small',
                    'quantity' => 1,
                    'price' => 980,
                ],
            ],
        ],

        1003 => [
            'id' => 1003,
            'tracking' => 'LH-2026-1003',
            'order' => 'ORD-2026-1047',
            'seller' => 'Lokal Finds',
            'buyer' => 'Ana Reyes',
            'contact' => '0919 845 2201',
            'address' => '15 Lopez Avenue, Brgy. Batong Malake, Los Baños, Laguna',
            'destination' => 'Los Baños, Laguna',
            'area' => 'Area C',
            'rider' => 'Rider 03',
            'payment' => 'GCash',
            'value' => 2150,
            'status' => 'Rider Assigned',
            'status_key' => 'assigned',
            'condition' => 'Good',
            'received_from' => 'Courier Transfer',
            'received_at' => 'September 1, 2026 · 9:48 AM',
            'notes' => 'Assigned to Rider 03 for Area C.',
            'items' => [
                [
                    'name' => 'Artisan Shoulder Bag',
                    'variation' => 'Maroon / Large',
                    'quantity' => 1,
                    'price' => 2150,
                ],
            ],
        ],

        1004 => [
            'id' => 1004,
            'tracking' => 'LH-2026-1004',
            'order' => 'ORD-2026-1048',
            'seller' => 'LIKHAE Home',
            'buyer' => 'Carlo Mendoza',
            'contact' => '0920 334 8821',
            'address' => '88 National Highway, Brgy. Real, Calamba, Laguna',
            'destination' => 'Calamba, Laguna',
            'area' => 'Area D',
            'rider' => 'Rider 04',
            'payment' => 'Cash on Delivery',
            'value' => 980,
            'status' => 'Out for Delivery',
            'status_key' => 'out_for_delivery',
            'condition' => 'Good',
            'received_from' => 'Courier Transfer',
            'received_at' => 'September 1, 2026 · 8:20 AM',
            'notes' => 'Parcel released to Rider 04 and currently out for delivery.',
            'items' => [
                [
                    'name' => 'Decorative Native Tray',
                    'variation' => 'Natural / Medium',
                    'quantity' => 1,
                    'price' => 980,
                ],
            ],
        ],

        1005 => [
            'id' => 1005,
            'tracking' => 'LH-2026-1005',
            'order' => 'ORD-2026-1049',
            'seller' => 'Gawang Laguna',
            'buyer' => 'Sofia Garcia',
            'contact' => '0921 772 0019',
            'address' => '42 Provincial Road, Brgy. Pagsawitan, Santa Cruz, Laguna',
            'destination' => 'Santa Cruz, Laguna',
            'area' => 'Area A',
            'rider' => 'Rider 01',
            'payment' => 'GCash',
            'value' => 1320,
            'status' => 'Delivered',
            'status_key' => 'delivered',
            'condition' => 'Good',
            'received_from' => 'Seller / Drop-off',
            'received_at' => 'September 1, 2026 · 7:55 AM',
            'notes' => 'Successfully delivered to recipient.',
            'items' => [
                [
                    'name' => 'Handcrafted Table Runner',
                    'variation' => 'Cream / 180cm',
                    'quantity' => 1,
                    'price' => 1320,
                ],
            ],
        ],

    ];


    $parcelId =
        isset($id)
            ? (int) $id
            : 1001;


    $parcel =
        $parcelRecords[$parcelId]
            ?? $parcelRecords[1001];


    $statusClass =
        match($parcel['status_key']) {

            'waiting_sorting' =>
                'bg-warning-soft text-warning',

            'awaiting_rider' =>
                'bg-info-soft text-info',

            'assigned' =>
                'bg-primary-soft text-primary',

            'out_for_delivery' =>
                'bg-primary-soft text-primary',

            'delivered' =>
                'bg-success-soft text-success',

            default =>
                'bg-page-secondary text-muted',
        };


    $nextAction =
        match($parcel['status_key']) {

            'waiting_sorting' => [
                'label' => 'Continue to Sorting',
                'route' => route('logistics.sorting'),
            ],

            'awaiting_rider' => [
                'label' => 'Assign Rider',
                'route' => route('logistics.assignments'),
            ],

            'assigned',
            'out_for_delivery' => [
                'label' => 'Track Parcel',
                'route' => route('logistics.parcels.tracking'),
            ],

            default => [
                'label' => 'View Tracking',
                'route' => route('logistics.parcels.tracking'),
            ],
        };


    $timeline = [

        [
            'title' => 'Parcel Received',
            'description' => 'Parcel checked into the LIKHAE sorting center.',
            'time' => $parcel['received_at'],
            'done' => true,
        ],

        [
            'title' => 'Sorted by Destination',
            'description' =>
                $parcel['area'] === 'Unassigned'
                    ? 'Waiting for destination-area sorting.'
                    : 'Parcel assigned to ' . $parcel['area'] . '.',
            'time' =>
                $parcel['area'] === 'Unassigned'
                    ? null
                    : 'September 1, 2026 · 10:02 AM',
            'done' =>
                $parcel['status_key'] !== 'waiting_sorting',
        ],

        [
            'title' => 'Rider Assigned',
            'description' =>
                $parcel['rider'] === 'Not assigned'
                    ? 'Waiting for an available rider.'
                    : $parcel['rider'] . ' received the delivery assignment.',
            'time' =>
                $parcel['rider'] === 'Not assigned'
                    ? null
                    : 'September 1, 2026 · 10:14 AM',
            'done' =>
                in_array(
                    $parcel['status_key'],
                    [
                        'assigned',
                        'out_for_delivery',
                        'delivered',
                    ],
                    true
                ),
        ],

        [
            'title' => 'Out for Delivery',
            'description' =>
                'Parcel has left the sorting center for delivery.',
            'time' =>
                in_array(
                    $parcel['status_key'],
                    [
                        'out_for_delivery',
                        'delivered',
                    ],
                    true
                )
                    ? 'September 1, 2026 · 11:10 AM'
                    : null,
            'done' =>
                in_array(
                    $parcel['status_key'],
                    [
                        'out_for_delivery',
                        'delivered',
                    ],
                    true
                ),
        ],

        [
            'title' => 'Delivered',
            'description' =>
                'Parcel successfully delivered to the recipient.',
            'time' =>
                $parcel['status_key'] === 'delivered'
                    ? 'September 1, 2026 · 12:18 PM'
                    : null,
            'done' =>
                $parcel['status_key'] === 'delivered',
        ],

    ];

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
            {{ $parcel['tracking'] }}
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
                Parcel Details
            </span>


            <div
                class="
                    mt-2
                    flex
                    flex-wrap
                    items-center
                    gap-3
                "
            >

                <h1
                    class="
                        font-display
                        text-[30px]
                        font-semibold
                        tracking-[-0.04em]
                        text-ink
                        sm:text-[34px]
                    "
                >
                    {{ $parcel['tracking'] }}
                </h1>


                <span
                    class="
                        inline-flex
                        items-center
                        gap-1.5
                        rounded-full
                        px-2.5
                        py-1
                        text-[8px]
                        font-semibold
                        {{ $statusClass }}
                    "
                >

                    <span
                        class="
                            h-1.5
                            w-1.5
                            rounded-full
                            bg-current
                        "
                    ></span>

                    {{ $parcel['status'] }}

                </span>

            </div>


            <p
                class="
                    mt-2
                    text-[10px]
                    text-muted
                "
            >
                Order {{ $parcel['order'] }}
            </p>

        </div>


        <div
            class="
                flex
                flex-wrap
                items-center
                gap-2
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
                ← Back
            </a>


            <a
                href="{{ route('logistics.waybills.show', ['tracking' => $parcel['tracking']]) }}"
                class="inline-flex h-10 items-center justify-center rounded-lg border border-line bg-surface px-4 text-[9px] font-semibold text-ink transition hover:bg-surface-hover"
            >
                Waybill
            </a>

            <a
                href="{{ $nextAction['route'] }}"
                class="
                    inline-flex
                    h-10
                    items-center
                    justify-center
                    gap-2
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
                {{ $nextAction['label'] }}
                <span>→</span>
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
            xl:grid-cols-[minmax(0,1fr)_330px]
        "
    >

        {{-- LEFT COLUMN --}}

        <div class="flex min-w-0 flex-col gap-4">


            {{-- =============================================
                PARCEL OVERVIEW
            ============================================== --}}

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
                        Parcel Overview
                    </h2>


                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            text-muted
                        "
                    >
                        Core parcel and order information.
                    </p>

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

                    @foreach([
                        ['label' => 'Order Number', 'value' => $parcel['order']],
                        ['label' => 'Seller', 'value' => $parcel['seller']],
                        ['label' => 'Buyer', 'value' => $parcel['buyer']],
                        ['label' => 'Payment', 'value' => $parcel['payment']],
                        ['label' => 'Parcel Value', 'value' => '₱' . number_format($parcel['value'], 2)],
                        ['label' => 'Condition', 'value' => $parcel['condition']],
                    ] as $detail)

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
                                {{ $detail['label'] }}
                            </span>


                            <strong
                                class="
                                    mt-2
                                    block
                                    text-[10px]
                                    font-semibold
                                    text-ink
                                "
                            >
                                {{ $detail['value'] }}
                            </strong>

                        </div>

                    @endforeach

                </div>

            </section>


            {{-- =============================================
                DELIVERY INFORMATION
            ============================================== --}}

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
                        gap-3
                        border-b
                        border-line
                        pb-4
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
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
                            Delivery Information
                        </h2>


                        <p
                            class="
                                mt-0.5
                                text-[9px]
                                text-muted
                            "
                        >
                            Destination, delivery area, and rider assignment.
                        </p>

                    </div>


                    <span
                        class="
                            inline-flex
                            w-fit
                            rounded-full
                            border
                            border-line
                            bg-page-secondary
                            px-2.5
                            py-1
                            text-[8px]
                            font-semibold
                            text-muted
                        "
                    >
                        {{ $parcel['area'] }}
                    </span>

                </div>


                <div
                    class="
                        mt-5
                        grid
                        gap-4
                        md:grid-cols-2
                    "
                >

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            p-4
                        "
                    >

                        <div class="flex items-start gap-3">

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
                                        text-[10px]
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


                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            p-4
                        "
                    >

                        <div class="flex items-start gap-3">

                            <span
                                class="
                                    grid
                                    h-9
                                    w-9
                                    shrink-0
                                    place-items-center
                                    rounded-lg
                                    bg-info-soft
                                    text-info
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
                                    <circle cx="8" cy="7" r="3"></circle>
                                    <path d="M3 19c0-3 2-5 5-5"></path>
                                    <path d="M14 7h7"></path>
                                    <path d="M17.5 3.5V10.5"></path>
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
                                    Assigned Rider
                                </span>


                                <strong
                                    class="
                                        mt-1
                                        block
                                        text-[10px]
                                        font-semibold
                                        text-ink
                                    "
                                >
                                    {{ $parcel['rider'] }}
                                </strong>


                                <p
                                    class="
                                        mt-1
                                        text-[9px]
                                        leading-5
                                        text-muted
                                    "
                                >
                                    {{
                                        $parcel['rider'] === 'Not assigned'
                                            ? 'No rider has been assigned to this parcel yet.'
                                            : 'Rider assigned based on the parcel delivery area.'
                                    }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <div
                    class="
                        mt-4
                        grid
                        gap-4
                        sm:grid-cols-2
                    "
                >

                    <div
                        class="
                            rounded-lg
                            border
                            border-line
                            p-4
                        "
                    >
                        <span
                            class="
                                text-[8px]
                                uppercase
                                tracking-[0.1em]
                                text-muted
                            "
                        >
                            Customer Contact
                        </span>

                        <strong
                            class="
                                mt-1
                                block
                                text-[10px]
                                font-semibold
                                text-ink
                            "
                        >
                            {{ $parcel['contact'] }}
                        </strong>
                    </div>


                    <div
                        class="
                            rounded-lg
                            border
                            border-line
                            p-4
                        "
                    >
                        <span
                            class="
                                text-[8px]
                                uppercase
                                tracking-[0.1em]
                                text-muted
                            "
                        >
                            Received From
                        </span>

                        <strong
                            class="
                                mt-1
                                block
                                text-[10px]
                                font-semibold
                                text-ink
                            "
                        >
                            {{ $parcel['received_from'] }}
                        </strong>
                    </div>

                </div>

            </section>


            {{-- =============================================
                ITEMS
            ============================================== --}}

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
                        Parcel Items
                    </h2>


                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            text-muted
                        "
                    >
                        Items included in this order.
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
                        Total
                    </span>


                    <strong
                        class="
                            text-[13px]
                            font-bold
                            text-ink
                        "
                    >
                        ₱{{ number_format($parcel['value'], 2) }}
                    </strong>

                </div>

            </section>


            {{-- =============================================
                NOTES
            ============================================== --}}

            <section
                class="
                    rounded-xl
                    border
                    border-line
                    bg-surface
                    p-5
                "
            >

                <h2
                    class="
                        text-[13px]
                        font-semibold
                        text-ink
                    "
                >
                    Logistics Notes
                </h2>


                <p
                    class="
                        mt-3
                        rounded-lg
                        bg-page-secondary
                        p-4
                        text-[9px]
                        leading-5
                        text-muted
                    "
                >
                    {{ $parcel['notes'] }}
                </p>

            </section>

        </div>


        {{-- =================================================
            RIGHT COLUMN
        ================================================== --}}

        <aside class="flex flex-col gap-4">


            {{-- STATUS CARD --}}

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
                    {{ $parcel['status'] }}
                </h2>


                <p
                    class="
                        mt-2
                        text-[9px]
                        leading-5
                        text-white/60
                    "
                >
                    Tracking {{ $parcel['tracking'] }}
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
                        Delivery Area
                    </span>


                    <strong
                        class="
                            mt-1.5
                            block
                            text-[11px]
                            font-semibold
                            text-white
                        "
                    >
                        {{ $parcel['area'] }}
                    </strong>

                </div>

            </section>


            {{-- TIMELINE --}}

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
                        Parcel Timeline
                    </h2>


                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            text-muted
                        "
                    >
                        Delivery workflow and progress.
                    </p>

                </div>


                <div class="mt-5">

                    @foreach($timeline as $step)

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
                                        {{
                                            $step['done']
                                                ? 'bg-primary'
                                                : 'bg-line'
                                        }}
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
                                        $step['done']
                                            ? 'border-primary bg-primary text-white'
                                            : 'border-line bg-page-secondary text-muted'
                                    }}
                                "
                            >

                                @if($step['done'])

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="
                                            h-3.5
                                            w-3.5
                                            fill-none
                                            stroke-current
                                            stroke-[2]
                                        "
                                    >
                                        <path d="m5 12 4 4 10-10"></path>
                                    </svg>

                                @else

                                    {{ $loop->iteration }}

                                @endif

                            </span>


                            <div class="min-w-0 pt-1">

                                <strong
                                    class="
                                        block
                                        text-[9px]
                                        font-semibold
                                        {{
                                            $step['done']
                                                ? 'text-ink'
                                                : 'text-muted'
                                        }}
                                    "
                                >
                                    {{ $step['title'] }}
                                </strong>


                                <p
                                    class="
                                        mt-1
                                        text-[8px]
                                        leading-4
                                        text-muted
                                    "
                                >
                                    {{ $step['description'] }}
                                </p>


                                <span
                                    class="
                                        mt-1.5
                                        block
                                        text-[7px]
                                        text-muted-light
                                    "
                                >
                                    {{ $step['time'] ?? 'Pending' }}
                                </span>

                            </div>

                        </div>

                    @endforeach

                </div>

            </section>


            {{-- QUICK ACTIONS --}}

            <section
                class="
                    rounded-xl
                    border
                    border-line
                    bg-surface
                    p-5
                "
            >

                <h2
                    class="
                        text-[13px]
                        font-semibold
                        text-ink
                    "
                >
                    Quick Actions
                </h2>


                <div class="mt-4 grid gap-2">

                    <a
                        href="{{ $nextAction['route'] }}"
                        class="
                            inline-flex
                            h-10
                            items-center
                            justify-between
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
                        {{ $nextAction['label'] }}
                        <span>→</span>
                    </a>


                    <a
                        href="{{ route('logistics.parcels.tracking') }}"
                        class="
                            inline-flex
                            h-10
                            items-center
                            justify-between
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
                        Open Tracking
                        <span>→</span>
                    </a>


                    <button
                        type="button"
                        id="copyTrackingButton"
                        data-tracking="{{ $parcel['tracking'] }}"
                        class="
                            inline-flex
                            h-10
                            items-center
                            justify-between
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
                        Copy Tracking ID
                        <span>⧉</span>
                    </button>

                </div>

            </section>

        </aside>

    </section>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const copyButton =
        document.getElementById(
            'copyTrackingButton'
        );


    copyButton?.addEventListener(
        'click',
        async function () {

            const tracking =
                copyButton.dataset.tracking;


            if (!tracking) {
                return;
            }


            try {

                await navigator.clipboard.writeText(
                    tracking
                );


                const originalText =
                    copyButton.innerHTML;


                copyButton.innerHTML =
                    '<span>Copied!</span><span>✓</span>';


                setTimeout(
                    function () {

                        copyButton.innerHTML =
                            originalText;

                    },
                    1500
                );

            } catch (error) {

                window.prompt(
                    'Copy tracking number:',
                    tracking
                );

            }

        }
    );

});
</script>

@endpush
