@extends('logistics.app')

@section('title', 'All Parcels — LIKHAE Logistics')

@section('content')


<style>
    :root {
        --lp-bg: #FBF7F2;
        --lp-bg-soft: #F6EFE7;
        --lp-bg-warm: #F3E4DE;
        --lp-card: #FFFDF9;
        --lp-border: #EADCCC;
        --lp-border-strong: #DBCEC1;
        --lp-maroon: #561C17;
        --lp-maroon-2: #642920;
        --lp-maroon-dark: #3E130F;
        --lp-text: #3B211B;
        --lp-text-dark: #1C160F;
        --lp-brown: #6C4936;
        --lp-muted: #987865;
        --lp-muted-light: #A99386;
        --lp-tan: #C19771;
        --lp-success: #256F4A;
        --lp-success-soft: #EAF7EF;
        --lp-warning: #9A5B11;
        --lp-warning-soft: #FFF6DE;
        --lp-shadow: 0 8px 24px rgba(86, 28, 23, .055);
        --lp-shadow-hover: 0 18px 44px rgba(86, 28, 23, .10);
    }

    .lp-page {
        color: var(--lp-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .lp-page * { box-sizing: border-box; }

    .lp-breadcrumb {
        color: var(--lp-muted) !important;
        font-weight: 750;
    }

    .lp-breadcrumb a { color: var(--lp-muted) !important; }
    .lp-breadcrumb a:hover { color: var(--lp-maroon) !important; }
    .lp-breadcrumb span:last-child { color: var(--lp-text) !important; }

    .lp-hero {
        padding: 32px 36px;
        border: 1px solid var(--lp-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--lp-shadow);
    }

    .lp-hero > div > span:first-child {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--lp-maroon) !important;
        font-size: 10px !important;
        font-weight: 950 !important;
        letter-spacing: .20em !important;
    }

    .lp-hero > div > span:first-child::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .lp-hero h1 {
        color: var(--lp-text) !important;
        font-family: "Instrument Serif", Georgia, serif !important;
        font-size: clamp(42px, 5vw, 66px) !important;
        font-weight: 400 !important;
        line-height: .94 !important;
        letter-spacing: -.055em !important;
    }

    .lp-hero p { color: var(--lp-muted) !important; }

    .lp-hero > a {
        min-height: 42px !important;
        border-radius: 13px !important;
        background: var(--lp-maroon) !important;
        color: #fff !important;
        box-shadow: 0 10px 22px rgba(86,28,23,.16) !important;
    }

    .lp-hero > a:hover { background: var(--lp-maroon-dark) !important; }

    .lp-overview {
        grid-template-columns: repeat(5, minmax(0,1fr)) !important;
        gap: 14px !important;
    }

    .lp-stat-card {
        min-height: 126px;
        border-color: var(--lp-border) !important;
        border-radius: 20px !important;
        background:
            radial-gradient(circle at 94% 6%, rgba(193,151,113,.14), transparent 28%),
            linear-gradient(180deg,#FFFDF9 0%,#FFF9F2 100%) !important;
        box-shadow: var(--lp-shadow);
        transition: 160ms ease;
    }

    .lp-stat-card:hover {
        transform: translateY(-2px);
        border-color: var(--lp-tan) !important;
        box-shadow: var(--lp-shadow-hover);
    }

    .lp-stat-card span.text-muted { color: var(--lp-muted) !important; }
    .lp-stat-card strong { color: var(--lp-text-dark) !important; }

    .lp-stat-card > div > span.rounded-full {
        width: 38px !important;
        height: 38px !important;
        margin-top: 0 !important;
        border: 1px solid #E6C7BE !important;
        border-radius: 13px !important;
        background: var(--lp-bg-warm) !important;
        color: var(--lp-maroon) !important;
    }

    .lp-toolbar,
    .lp-directory {
        border-color: var(--lp-border) !important;
        border-radius: 22px !important;
        background: var(--lp-card) !important;
        box-shadow: var(--lp-shadow);
    }

    .lp-toolbar input,
    .lp-toolbar select {
        border-color: var(--lp-border) !important;
        border-radius: 12px !important;
        background: var(--lp-bg-soft) !important;
        color: var(--lp-text) !important;
    }

    .lp-toolbar input:focus,
    .lp-toolbar select:focus {
        border-color: var(--lp-tan) !important;
        background: #fff !important;
        box-shadow: 0 0 0 4px rgba(86,28,23,.07) !important;
    }

    .lp-toolbar button {
        border-color: var(--lp-border) !important;
        border-radius: 12px !important;
        background: var(--lp-card) !important;
        color: var(--lp-maroon) !important;
    }

    .lp-toolbar button:hover {
        border-color: var(--lp-tan) !important;
        background: var(--lp-bg-warm) !important;
    }

    .lp-directory > div:first-child {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.13), transparent 30%),
            linear-gradient(135deg,#FFFDF9 0%,#F8F0E8 100%) !important;
        border-color: var(--lp-border) !important;
    }

    .lp-directory h2 {
        color: var(--lp-text) !important;
        font-family: "Instrument Serif", Georgia, serif !important;
        font-size: 28px !important;
        font-weight: 400 !important;
        letter-spacing: -.04em !important;
    }

    .lp-directory p { color: var(--lp-muted) !important; }

    .lp-table thead tr { background: var(--lp-bg-soft) !important; }
    .lp-table th { color: var(--lp-muted) !important; border-color: var(--lp-border) !important; }
    .lp-table td { color: var(--lp-brown) !important; border-color: #EFE1D5 !important; }
    .lp-table tbody tr:hover td { background: var(--lp-bg-soft) !important; }

    .lp-table td a.text-ink,
    .lp-table td .text-ink { color: var(--lp-text) !important; }

    .lp-table td a.text-ink:hover { color: var(--lp-maroon) !important; }

    .lp-table .bg-primary-soft {
        background: var(--lp-bg-warm) !important;
        color: var(--lp-maroon) !important;
    }

    .lp-table .bg-info-soft {
        background: var(--lp-bg-warm) !important;
        color: var(--lp-maroon) !important;
    }

    .lp-table .bg-warning-soft {
        background: var(--lp-warning-soft) !important;
        color: var(--lp-warning) !important;
    }

    .lp-table .bg-success-soft {
        background: var(--lp-success-soft) !important;
        color: var(--lp-success) !important;
    }

    .lp-table a.bg-primary {
        background: var(--lp-maroon) !important;
        color: #fff !important;
    }

    .lp-table a.bg-primary:hover { background: var(--lp-maroon-dark) !important; }

    @media (max-width: 1250px) {
        .lp-overview { grid-template-columns: repeat(3,minmax(0,1fr)) !important; }
    }

    @media (max-width: 760px) {
        .lp-hero { padding: 26px 22px; }
        .lp-overview { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
    }

    @media (max-width: 520px) {
        .lp-overview { grid-template-columns: 1fr !important; }
    }

    html.dark .lp-hero,
    html.dark .lp-stat-card,
    html.dark .lp-toolbar,
    html.dark .lp-directory {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg,#211B17 0%,#1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .lp-hero h1,
    html.dark .lp-stat-card strong,
    html.dark .lp-directory h2,
    html.dark .lp-table td .text-ink,
    html.dark .lp-table td a.text-ink {
        color: #F5EFE8 !important;
    }

    html.dark .lp-hero p,
    html.dark .lp-breadcrumb,
    html.dark .lp-stat-card span.text-muted,
    html.dark .lp-directory p,
    html.dark .lp-table th {
        color: #AFA19A !important;
    }

    html.dark .lp-hero > div > span:first-child { color: #EBA99D !important; }

    html.dark .lp-toolbar input,
    html.dark .lp-toolbar select,
    html.dark .lp-toolbar button {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .lp-directory > div:first-child,
    html.dark .lp-table thead tr {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .lp-table td { border-color: #30231F !important; color: #D0C4BD !important; }
    html.dark .lp-table tbody tr:hover td { background: #241817 !important; }
    html.dark .lp-table .bg-primary-soft,
    html.dark .lp-table .bg-info-soft {
        background: #2D1816 !important;
        color: #EBA99D !important;
    }
    html.dark .lp-table a.bg-primary { background: #A84538 !important; }
</style>


@php

    /*
    |--------------------------------------------------------------------------
    | FRONTEND SAMPLE DATA
    |--------------------------------------------------------------------------
    | Temporary only. Replace with controller/database data later.
    */

    $parcels = [

        [
            'id' => 1001,
            'tracking' => 'LH-2026-1001',
            'order' => 'ORD-2026-1045',
            'buyer' => 'Juan Dela Cruz',
            'destination' => 'Santa Cruz, Laguna',
            'area' => 'Unassigned',
            'rider' => 'Not assigned',
            'status' => 'Waiting for Sorting',
            'status_key' => 'waiting_sorting',
            'received' => '10:32 AM',
        ],

        [
            'id' => 1002,
            'tracking' => 'LH-2026-1002',
            'order' => 'ORD-2026-1046',
            'buyer' => 'Maria Santos',
            'destination' => 'Pagsanjan, Laguna',
            'area' => 'Area B',
            'rider' => 'Not assigned',
            'status' => 'Awaiting Rider',
            'status_key' => 'awaiting_rider',
            'received' => '10:15 AM',
        ],

        [
            'id' => 1003,
            'tracking' => 'LH-2026-1003',
            'order' => 'ORD-2026-1047',
            'buyer' => 'Ana Reyes',
            'destination' => 'Los Baños, Laguna',
            'area' => 'Area C',
            'rider' => 'Rider 03',
            'status' => 'Rider Assigned',
            'status_key' => 'assigned',
            'received' => '9:48 AM',
        ],

        [
            'id' => 1004,
            'tracking' => 'LH-2026-1004',
            'order' => 'ORD-2026-1048',
            'buyer' => 'Carlo Mendoza',
            'destination' => 'Calamba, Laguna',
            'area' => 'Area D',
            'rider' => 'Rider 04',
            'status' => 'Out for Delivery',
            'status_key' => 'out_for_delivery',
            'received' => '8:20 AM',
        ],

        [
            'id' => 1005,
            'tracking' => 'LH-2026-1005',
            'order' => 'ORD-2026-1049',
            'buyer' => 'Sofia Garcia',
            'destination' => 'Santa Cruz, Laguna',
            'area' => 'Area A',
            'rider' => 'Rider 01',
            'status' => 'Delivered',
            'status_key' => 'delivered',
            'received' => '7:55 AM',
        ],

        [
            'id' => 1006,
            'tracking' => 'LH-2026-1006',
            'order' => 'ORD-2026-1050',
            'buyer' => 'Miguel Ramos',
            'destination' => 'Pagsanjan, Laguna',
            'area' => 'Unassigned',
            'rider' => 'Not assigned',
            'status' => 'Waiting for Sorting',
            'status_key' => 'waiting_sorting',
            'received' => '9:18 AM',
        ],

        [
            'id' => 1007,
            'tracking' => 'LH-2026-1007',
            'order' => 'ORD-2026-1051',
            'buyer' => 'Liza Bautista',
            'destination' => 'Los Baños, Laguna',
            'area' => 'Area C',
            'rider' => 'Not assigned',
            'status' => 'Awaiting Rider',
            'status_key' => 'awaiting_rider',
            'received' => '8:56 AM',
        ],

        [
            'id' => 1008,
            'tracking' => 'LH-2026-1008',
            'order' => 'ORD-2026-1052',
            'buyer' => 'Paolo Flores',
            'destination' => 'Santa Cruz, Laguna',
            'area' => 'Area A',
            'rider' => 'Rider 02',
            'status' => 'Rider Assigned',
            'status_key' => 'assigned',
            'received' => '8:31 AM',
        ],

    ];


    $overview = [
        [
            'label' => 'All Parcels',
            'value' => 284,
            'class' => 'bg-primary-soft text-primary',
        ],
        [
            'label' => 'Waiting Sorting',
            'value' => 34,
            'class' => 'bg-warning-soft text-warning',
        ],
        [
            'label' => 'Awaiting Rider',
            'value' => 18,
            'class' => 'bg-info-soft text-info',
        ],
        [
            'label' => 'On the Road',
            'value' => 76,
            'class' => 'bg-primary-soft text-primary',
        ],
        [
            'label' => 'Delivered',
            'value' => 156,
            'class' => 'bg-success-soft text-success',
        ],
    ];

@endphp


<div class="lp-page flex w-full flex-col gap-6">

    {{-- =====================================================
        BREADCRUMB
    ====================================================== --}}

    <nav
        class="lp-breadcrumb flex flex-wrap items-center gap-2 text-[10px] text-muted"
    >

        <a
            href="{{ route('logistics.dashboard') }}"
            class="transition hover:text-primary"
        >
            Dashboard
        </a>

        <span>/</span>

        <span class="font-semibold text-ink">
            Parcels
        </span>

    </nav>


    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}

    <section
        class="lp-hero flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between"
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
                Parcel Management
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
                Every parcel, in one place.
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
                Review parcels entering the sorting center, monitor their status,
                and continue the next logistics action.
            </p>

        </div>


        <a
            href="{{ route('logistics.parcels.receive') }}"
            class="
                inline-flex
                h-10
                shrink-0
                items-center
                justify-center
                gap-2
                rounded-lg
                bg-primary
                px-4
                text-[11px]
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

    </section>


    {{-- =====================================================
        OVERVIEW
    ====================================================== --}}

    <section
        class="lp-overview grid gap-3 sm:grid-cols-2 lg:grid-cols-5"
    >

        @foreach($overview as $item)

            <article
                class="lp-stat-card rounded-xl border border-line bg-surface p-4"
            >

                <div class="flex items-start justify-between gap-3">

                    <div>

                        <span
                            class="
                                text-[9px]
                                font-medium
                                text-muted
                            "
                        >
                            {{ $item['label'] }}
                        </span>


                        <strong
                            class="
                                mt-2
                                block
                                text-[24px]
                                font-bold
                                tracking-[-0.04em]
                                text-ink
                            "
                        >
                            {{ $item['value'] }}
                        </strong>

                    </div>


                    <span
                        class="
                            mt-1
                            h-2
                            w-2
                            rounded-full
                            {{ $item['class'] }}
                        "
                    ></span>

                </div>

            </article>

        @endforeach

    </section>


    {{-- =====================================================
        TOOLBAR
    ====================================================== --}}

    <section
        class="lp-toolbar rounded-xl border border-line bg-surface p-4"
    >

        <div
            class="
                grid
                gap-3
                md:grid-cols-[minmax(0,1fr)_190px_170px_auto]
            "
        >

            {{-- SEARCH --}}

            <div class="relative">

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
                        stroke-muted
                        stroke-[1.6]
                    "
                >
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5"></path>
                </svg>


                <input
                    type="text"
                    id="parcelSearch"
                    placeholder="Search tracking, order, buyer..."
                    class="
                        h-10
                        w-full
                        rounded-lg
                        border
                        border-line
                        bg-page-secondary
                        pl-10
                        pr-4
                        text-[10px]
                        text-ink
                        outline-none
                        transition
                        placeholder:text-muted-light
                        focus:border-primary
                        focus:ring-2
                        focus:ring-primary/10
                    "
                >

            </div>


            {{-- STATUS FILTER --}}

            <select
                id="statusFilter"
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
                <option value="all">
                    All Statuses
                </option>

                <option value="waiting_sorting">
                    Waiting for Sorting
                </option>

                <option value="awaiting_rider">
                    Awaiting Rider
                </option>

                <option value="assigned">
                    Rider Assigned
                </option>

                <option value="out_for_delivery">
                    Out for Delivery
                </option>

                <option value="delivered">
                    Delivered
                </option>
            </select>


            {{-- AREA FILTER --}}

            <select
                id="areaFilter"
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
                <option value="all">
                    All Areas
                </option>

                <option value="unassigned">
                    Unassigned
                </option>

                <option value="area a">
                    Area A
                </option>

                <option value="area b">
                    Area B
                </option>

                <option value="area c">
                    Area C
                </option>

                <option value="area d">
                    Area D
                </option>
            </select>


            {{-- RESET --}}

            <button
                type="button"
                id="resetFilters"
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
                    text-[10px]
                    font-semibold
                    text-muted
                    transition
                    hover:bg-surface-hover
                    hover:text-ink
                "
            >
                Reset
            </button>

        </div>

    </section>


    {{-- =====================================================
        PARCEL TABLE
    ====================================================== --}}

    <section
        class="lp-directory overflow-hidden rounded-xl border border-line bg-surface"
    >

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

                <h2
                    class="
                        text-[13px]
                        font-semibold
                        text-ink
                    "
                >
                    Parcel Directory
                </h2>


                <p
                    class="
                        mt-0.5
                        text-[9px]
                        text-muted
                    "
                >
                    Showing
                    <span
                        id="visibleParcelCount"
                        class="font-semibold text-ink"
                    >
                        {{ count($parcels) }}
                    </span>
                    parcel records
                </p>

            </div>


            <span
                class="
                    inline-flex
                    w-fit
                    items-center
                    gap-2
                    rounded-full
                    bg-success-soft
                    px-2.5
                    py-1
                    text-[8px]
                    font-semibold
                    text-success
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

                Operations Active
            </span>

        </div>


        <div class="overflow-x-auto">

            <table
                class="lp-table w-full min-w-[1050px]"
            >

                <thead>

                    <tr
                        class="
                            border-b
                            border-line
                            bg-page-secondary
                            text-left
                        "
                    >

                        <th class="px-5 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">
                            Parcel
                        </th>

                        <th class="px-4 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">
                            Buyer
                        </th>

                        <th class="px-4 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">
                            Destination
                        </th>

                        <th class="px-4 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">
                            Area
                        </th>

                        <th class="px-4 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">
                            Rider
                        </th>

                        <th class="px-4 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">
                            Status
                        </th>

                        <th class="px-4 py-3 text-[8px] font-bold uppercase tracking-[0.1em] text-muted">
                            Received
                        </th>

                        <th class="px-5 py-3 text-right text-[8px] font-bold uppercase tracking-[0.1em] text-muted">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody
                    id="parcelTableBody"
                    class="divide-y divide-line"
                >

                    @foreach($parcels as $parcel)

                        @php

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

                                    'waiting_sorting' =>
                                        [
                                            'label' => 'Sort',
                                            'route' => route(
                                                'logistics.sorting.index',
                                                ['tracking' => $parcel['tracking']]
                                            ),
                                        ],

                                    'awaiting_rider' =>
                                        [
                                            'label' => 'Assign',
                                            'route' => route(
                                                'logistics.assignments.assign',
                                                $parcel['id']
                                            ),
                                        ],

                                    'assigned',
                                    'out_for_delivery' =>
                                        [
                                            'label' => 'Track',
                                            'route' => route(
                                                'logistics.parcels.tracking',
                                                ['tracking' => $parcel['tracking']]
                                            ),
                                        ],

                                    default =>
                                        [
                                            'label' => 'View',
                                            'route' => route(
                                                'logistics.parcels.show',
                                                $parcel['id']
                                            ),
                                        ],
                                };

                        @endphp


                        <tr
                            class="
                                parcel-row
                                transition
                                hover:bg-surface-hover
                            "
                            data-search="{{ strtolower(
                                $parcel['tracking']
                                . ' '
                                . $parcel['order']
                                . ' '
                                . $parcel['buyer']
                                . ' '
                                . $parcel['destination']
                                . ' '
                                . $parcel['area']
                                . ' '
                                . $parcel['rider']
                            ) }}"
                            data-status="{{ $parcel['status_key'] }}"
                            data-area="{{ strtolower($parcel['area']) }}"
                        >

                            {{-- PARCEL --}}

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
                                            class="
                                                h-4
                                                w-4
                                                fill-none
                                                stroke-current
                                                stroke-[1.6]
                                            "
                                        >
                                            <path d="M21 8 12 3 3 8l9 5 9-5Z"></path>
                                            <path d="M3 8v8l9 5 9-5V8"></path>
                                        </svg>

                                    </span>


                                    <div>

                                        <a
                                            href="{{ route(
                                                'logistics.parcels.show',
                                                $parcel['id']
                                            ) }}"
                                            class="
                                                block
                                                text-[10px]
                                                font-semibold
                                                text-ink
                                                transition
                                                hover:text-primary
                                            "
                                        >
                                            {{ $parcel['tracking'] }}
                                        </a>


                                        <span
                                            class="
                                                mt-0.5
                                                block
                                                text-[8px]
                                                text-muted
                                            "
                                        >
                                            {{ $parcel['order'] }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            {{-- BUYER --}}

                            <td class="px-4 py-4">

                                <span
                                    class="
                                        text-[10px]
                                        font-medium
                                        text-ink
                                    "
                                >
                                    {{ $parcel['buyer'] }}
                                </span>

                            </td>


                            {{-- DESTINATION --}}

                            <td class="px-4 py-4">

                                <span
                                    class="
                                        text-[10px]
                                        text-ink
                                    "
                                >
                                    {{ $parcel['destination'] }}
                                </span>

                            </td>


                            {{-- AREA --}}

                            <td class="px-4 py-4">

                                <span
                                    class="
                                        inline-flex
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

                            </td>


                            {{-- RIDER --}}

                            <td class="px-4 py-4">

                                <span
                                    class="
                                        text-[9px]
                                        {{
                                            $parcel['rider'] === 'Not assigned'
                                                ? 'text-muted'
                                                : 'font-medium text-ink'
                                        }}
                                    "
                                >
                                    {{ $parcel['rider'] }}
                                </span>

                            </td>


                            {{-- STATUS --}}

                            <td class="px-4 py-4">

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

                            </td>


                            {{-- RECEIVED --}}

                            <td class="px-4 py-4">

                                <span
                                    class="
                                        text-[9px]
                                        text-muted
                                    "
                                >
                                    {{ $parcel['received'] }}
                                </span>

                            </td>


                            {{-- ACTIONS --}}

                            <td class="px-5 py-4">

                                <div
                                    class="
                                        flex
                                        items-center
                                        justify-end
                                        gap-2
                                    "
                                >

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
                                            text-[8px]
                                            font-semibold
                                            text-ink
                                            transition
                                            hover:bg-surface-hover
                                        "
                                    >
                                        View
                                    </a>


                                    <a
                                        href="{{ $nextAction['route'] }}"
                                        class="
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
                                            hover:bg-primary-hover
                                        "
                                    >
                                        {{ $nextAction['label'] }}
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- =================================================
            EMPTY FILTER STATE
        ================================================== --}}

        <div
            id="parcelEmptyState"
            class="
                hidden
                border-t
                border-line
                px-5
                py-12
                text-center
            "
        >

            <span
                class="
                    mx-auto
                    grid
                    h-12
                    w-12
                    place-items-center
                    rounded-full
                    bg-page-secondary
                    text-muted
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
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5"></path>
                </svg>
            </span>


            <h3
                class="
                    mt-4
                    text-[12px]
                    font-semibold
                    text-ink
                "
            >
                No parcels found
            </h3>


            <p
                class="
                    mt-1
                    text-[9px]
                    text-muted
                "
            >
                Try changing the search term or filters.
            </p>

        </div>

    </section>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('parcelSearch');

    const statusFilter =
        document.getElementById('statusFilter');

    const areaFilter =
        document.getElementById('areaFilter');

    const resetButton =
        document.getElementById('resetFilters');

    const rows =
        Array.from(
            document.querySelectorAll('.parcel-row')
        );

    const visibleCount =
        document.getElementById('visibleParcelCount');

    const emptyState =
        document.getElementById('parcelEmptyState');


    function applyFilters() {

        const search =
            (searchInput?.value || '')
                .trim()
                .toLowerCase();

        const status =
            statusFilter?.value || 'all';

        const area =
            areaFilter?.value || 'all';

        let visible = 0;


        rows.forEach(row => {

            const rowSearch =
                row.dataset.search || '';

            const rowStatus =
                row.dataset.status || '';

            const rowArea =
                row.dataset.area || '';


            const matchesSearch =
                !search
                || rowSearch.includes(search);


            const matchesStatus =
                status === 'all'
                || rowStatus === status;


            const matchesArea =
                area === 'all'
                || rowArea === area;


            const show =
                matchesSearch
                && matchesStatus
                && matchesArea;


            row.classList.toggle(
                'hidden',
                !show
            );


            if (show) {
                visible++;
            }

        });


        if (visibleCount) {
            visibleCount.textContent = visible;
        }


        emptyState?.classList.toggle(
            'hidden',
            visible !== 0
        );

    }


    searchInput?.addEventListener(
        'input',
        applyFilters
    );


    statusFilter?.addEventListener(
        'change',
        applyFilters
    );


    areaFilter?.addEventListener(
        'change',
        applyFilters
    );


    resetButton?.addEventListener(
        'click',
        function () {

            if (searchInput) {
                searchInput.value = '';
            }

            if (statusFilter) {
                statusFilter.value = 'all';
            }

            if (areaFilter) {
                areaFilter.value = 'all';
            }

            applyFilters();

        }
    );


    applyFilters();

});
</script>

@endpush
