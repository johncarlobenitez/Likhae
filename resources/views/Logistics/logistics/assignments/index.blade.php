@extends('logistics.app')

@section('title', 'Rider Assignment — LIKHAE Logistics')

@php
    $stats = [
        [
            'label' => 'Waiting Assignment',
            'value' => 18,
            'description' => 'Parcels ready for rider',
            'tone' => 'primary',
            'icon' => 'package',
        ],
        [
            'label' => 'Available Riders',
            'value' => 12,
            'description' => 'Currently online',
            'tone' => 'neutral',
            'icon' => 'rider',
        ],
        [
            'label' => 'Out for Delivery',
            'value' => 24,
            'description' => 'Active deliveries',
            'tone' => 'warning',
            'icon' => 'delivery',
        ],
        [
            'label' => 'Completed Today',
            'value' => 57,
            'description' => 'Successful deliveries',
            'tone' => 'success',
            'icon' => 'check',
        ],
    ];

    $parcels = [
        [
            'tracking' => 'LH-2026-1002',
            'buyer' => 'Maria Santos',
            'area' => 'Area B',
            'destination' => 'Pagsanjan, Laguna',
        ],
        [
            'tracking' => 'LH-2026-1010',
            'buyer' => 'Carlo Mendoza',
            'area' => 'Area D',
            'destination' => 'Calamba, Laguna',
        ],
        [
            'tracking' => 'LH-2026-1012',
            'buyer' => 'Ana Reyes',
            'area' => 'Area C',
            'destination' => 'Los Baños, Laguna',
        ],
        [
            'tracking' => 'LH-2026-1024',
            'buyer' => 'Rene Dizon',
            'area' => 'Area A',
            'destination' => 'San Pablo, Laguna',
        ],
    ];

    $riders = [
        [
            'name' => 'Rider 01',
            'area' => 'Area A',
            'active' => 3,
        ],
        [
            'name' => 'Rider 02',
            'area' => 'Area B',
            'active' => 2,
        ],
        [
            'name' => 'Rider 03',
            'area' => 'Area C',
            'active' => 4,
        ],
        [
            'name' => 'Rider 04',
            'area' => 'Area D',
            'active' => 1,
        ],
    ];

    $icons = [
        'package' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',

        'rider' => '
            <circle cx="9" cy="7" r="3"/>
            <path d="M3 20c0-4 2.5-6 6-6s6 2 6 6"/>
            <path d="M17 11h4"/>
            <path d="M19 9v4"/>
        ',

        'delivery' => '
            <path d="M3 6h11v11H3z"/>
            <path d="M14 10h4l3 3v4h-7z"/>
            <circle cx="7" cy="19" r="2"/>
            <circle cx="18" cy="19" r="2"/>
        ',

        'check' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="m8 12 2.7 2.7L16.5 9"/>
        ',

        'plus' => '
            <path d="M12 5v14"/>
            <path d="M5 12h14"/>
        ',

        'location' => '
            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0z"/>
            <circle cx="12" cy="10" r="2"/>
        ',

        'arrow' => '
            <path d="M5 12h14"/>
            <path d="m14 7 5 5-5 5"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --ra-bg: #FBF7F2;
        --ra-bg-soft: #F6EFE7;
        --ra-bg-warm: #F3E4DE;
        --ra-card: #FFFDF9;

        --ra-border: #EADCCC;
        --ra-border-strong: #DBCEC1;

        --ra-maroon: #561C17;
        --ra-maroon-2: #642920;
        --ra-maroon-dark: #3E130F;

        --ra-text: #3B211B;
        --ra-text-dark: #1C160F;
        --ra-brown: #6C4936;
        --ra-muted: #987865;
        --ra-muted-light: #A99386;

        --ra-tan: #C19771;

        --ra-success: #256F4A;
        --ra-success-soft: #EAF7EF;

        --ra-warning: #9A5B11;
        --ra-warning-soft: #FFF6DE;

        --ra-danger: #B42318;
        --ra-danger-soft: #FCEBE9;

        --ra-shadow:
            0 8px 24px rgba(86, 28, 23, 0.055);

        --ra-shadow-hover:
            0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ra-page {
        display: grid;
        gap: 18px;

        width: 100%;

        color: var(--ra-text);

        font-family:
            "DM Sans",
            Poppins,
            system-ui,
            sans-serif;
    }

    .ra-page * {
        box-sizing: border-box;
    }

    /* =====================================================
       BREADCRUMB
    ====================================================== */

    .ra-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;

        color: var(--ra-muted);

        font-size: 10px;
        font-weight: 750;
    }

    .ra-breadcrumb a {
        color: var(--ra-muted);
        text-decoration: none;

        transition: 150ms ease;
    }

    .ra-breadcrumb a:hover {
        color: var(--ra-maroon);
    }

    .ra-breadcrumb strong {
        color: var(--ra-text);
        font-weight: 900;
    }

    /* =====================================================
       HERO
    ====================================================== */

    .ra-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;

        padding: 32px 36px;

        border: 1px solid var(--ra-border);
        border-radius: 28px;

        background:
            radial-gradient(
                circle at 94% 10%,
                rgba(193, 151, 113, 0.24),
                transparent 29%
            ),
            radial-gradient(
                circle at 8% 16%,
                rgba(86, 28, 23, 0.055),
                transparent 30%
            ),
            linear-gradient(
                135deg,
                #FFFDF9 0%,
                #F6EFE7 58%,
                #EFE7DE 100%
            );

        box-shadow: var(--ra-shadow);
    }

    .ra-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        color: var(--ra-maroon);

        font-size: 10px;
        font-weight: 950;
        letter-spacing: 0.20em;
        text-transform: uppercase;
    }

    .ra-eyebrow::before {
        width: 24px;
        height: 1px;

        background: currentColor;

        content: "";
    }

    .ra-hero h1 {
        margin: 10px 0 0;

        color: var(--ra-text);

        font-family:
            "Instrument Serif",
            Georgia,
            serif;

        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: 0.94;
        letter-spacing: -0.055em;
    }

    .ra-hero p {
        max-width: 680px;
        margin: 13px 0 0;

        color: var(--ra-muted);

        font-size: 12px;
        line-height: 1.7;
    }

    /* =====================================================
       BUTTONS
    ====================================================== */

    .ra-btn {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 8px;

        padding: 0 16px;

        border: 1px solid transparent;
        border-radius: 13px;

        font-size: 11px;
        font-weight: 900;
        line-height: 1;

        text-decoration: none;

        cursor: pointer;
        transition: 160ms ease;
    }

    .ra-btn:hover {
        transform: translateY(-1px);
    }

    .ra-btn svg {
        width: 16px;
        height: 16px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ra-btn-primary {
        background: var(--ra-maroon);
        border-color: var(--ra-maroon);
        color: #FFFFFF;

        box-shadow:
            0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ra-btn-primary:hover {
        background: var(--ra-maroon-dark);
        border-color: var(--ra-maroon-dark);
    }

    .ra-btn-soft {
        background: var(--ra-card);
        border-color: var(--ra-border);
        color: var(--ra-maroon);
    }

    .ra-btn-soft:hover {
        background: var(--ra-bg-warm);
        border-color: var(--ra-tan);
    }

    .ra-btn-sm {
        min-height: 34px;
        padding: 0 13px;

        border-radius: 10px;

        font-size: 10px;
    }

    /* =====================================================
       STATS
    ====================================================== */

    .ra-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .ra-stat-card {
        display: flex;
        min-width: 0;
        min-height: 132px;
        flex-direction: column;
        justify-content: space-between;
        gap: 16px;

        padding: 17px;

        border: 1px solid var(--ra-border);
        border-radius: 20px;

        background:
            radial-gradient(
                circle at 94% 6%,
                rgba(193, 151, 113, 0.14),
                transparent 28%
            ),
            linear-gradient(
                180deg,
                #FFFDF9 0%,
                #FFF9F2 100%
            );

        box-shadow: var(--ra-shadow);

        transition: 160ms ease;
    }

    .ra-stat-card:hover {
        transform: translateY(-2px);

        border-color: var(--ra-tan);

        box-shadow: var(--ra-shadow-hover);
    }

    .ra-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .ra-stat-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;

        border: 1px solid #E6C7BE;
        border-radius: 13px;

        background: var(--ra-bg-warm);
        color: var(--ra-maroon);
    }

    .ra-stat-icon svg {
        width: 18px;
        height: 18px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.75;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ra-stat-icon.is-warning {
        border-color: #EAD39A;
        background: var(--ra-warning-soft);
        color: var(--ra-warning);
    }

    .ra-stat-icon.is-success {
        border-color: #CFE8DA;
        background: var(--ra-success-soft);
        color: var(--ra-success);
    }

    .ra-stat-status {
        display: inline-flex;
        min-height: 23px;
        align-items: center;

        padding: 0 8px;

        border-radius: 999px;

        background: var(--ra-bg-soft);
        color: var(--ra-muted);

        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .ra-stat-card small {
        display: block;

        color: var(--ra-muted);

        font-size: 9px;
        font-weight: 850;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .ra-stat-card strong {
        display: block;
        margin-top: 6px;

        color: var(--ra-text-dark);

        font-size: 28px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ra-stat-description {
        display: block;
        margin-top: 6px;

        color: var(--ra-muted);

        font-size: 10px;
        font-weight: 700;
    }

    /* =====================================================
       MAIN GRID
    ====================================================== */

    .ra-main-grid {
        display: grid;
        grid-template-columns:
            minmax(0, 1fr)
            minmax(300px, 360px);

        gap: 18px;

        align-items: start;
    }

    .ra-card {
        overflow: hidden;

        border: 1px solid var(--ra-border);
        border-radius: 22px;

        background: var(--ra-card);

        box-shadow: var(--ra-shadow);
    }

    .ra-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding: 19px 20px;

        border-bottom: 1px solid var(--ra-border);

        background:
            radial-gradient(
                circle at 96% 6%,
                rgba(193, 151, 113, 0.13),
                transparent 30%
            ),
            linear-gradient(
                135deg,
                #FFFDF9 0%,
                #F8F0E8 100%
            );
    }

    .ra-card-kicker {
        display: block;

        color: var(--ra-maroon);

        font-size: 8px;
        font-weight: 950;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .ra-card-head h2 {
        margin: 7px 0 0;

        color: var(--ra-text);

        font-family:
            "Instrument Serif",
            Georgia,
            serif;

        font-size: 30px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .ra-card-head p {
        margin: 7px 0 0;

        color: var(--ra-muted);

        font-size: 10px;
        line-height: 1.55;
    }

    /* =====================================================
       PARCEL LIST
    ====================================================== */

    .ra-parcel-list {
        display: grid;
    }

    .ra-parcel-row {
        display: grid;
        grid-template-columns:
            auto
            minmax(0, 1fr)
            auto;

        align-items: center;
        gap: 14px;

        padding: 16px 20px;

        border-bottom: 1px solid var(--ra-border);

        transition: 150ms ease;
    }

    .ra-parcel-row:last-child {
        border-bottom: 0;
    }

    .ra-parcel-row:hover {
        background: var(--ra-bg-soft);
    }

    .ra-parcel-icon {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;

        border: 1px solid var(--ra-border);
        border-radius: 14px;

        background: var(--ra-bg-soft);
        color: var(--ra-maroon);
    }

    .ra-parcel-icon svg {
        width: 18px;
        height: 18px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ra-parcel-main {
        min-width: 0;
    }

    .ra-parcel-main strong {
        display: block;

        color: var(--ra-text);

        font-size: 11px;
        font-weight: 950;
    }

    .ra-parcel-buyer {
        display: block;
        margin-top: 4px;

        color: var(--ra-brown);

        font-size: 10px;
        font-weight: 750;
    }

    .ra-destination {
        display: flex;
        align-items: center;
        gap: 5px;

        margin-top: 4px;

        color: var(--ra-muted);

        font-size: 9px;
        font-weight: 700;
    }

    .ra-destination svg {
        width: 12px;
        height: 12px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .ra-parcel-actions {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .ra-area {
        display: inline-flex;
        min-height: 25px;
        align-items: center;

        padding: 0 10px;

        border: 1px solid #E6C7BE;
        border-radius: 999px;

        background: var(--ra-bg-warm);
        color: var(--ra-maroon);

        font-size: 9px;
        font-weight: 900;
        white-space: nowrap;
    }

    /* =====================================================
       RIDERS
    ====================================================== */

    .ra-riders-body {
        display: grid;
        gap: 10px;

        padding: 16px;
    }

    .ra-rider {
        display: grid;
        grid-template-columns:
            auto
            minmax(0, 1fr)
            auto;

        align-items: center;
        gap: 11px;

        width: 100%;
        padding: 12px;

        border: 1px solid var(--ra-border);
        border-radius: 15px;

        background:
            linear-gradient(
                180deg,
                #FFFDF9 0%,
                #FBF7F2 100%
            );

        color: var(--ra-text);

        font: inherit;
        text-align: left;

        cursor: pointer;

        transition: 150ms ease;
    }

    .ra-rider:hover {
        transform: translateY(-1px);

        border-color: var(--ra-tan);
        background: var(--ra-bg-soft);
    }

    .ra-rider-avatar {
        display: grid;
        width: 36px;
        height: 36px;
        place-items: center;

        border-radius: 999px;

        background: var(--ra-maroon);
        color: #FFFFFF;

        font-size: 9px;
        font-weight: 950;
    }

    .ra-rider-info {
        min-width: 0;
    }

    .ra-rider-info strong {
        display: block;

        color: var(--ra-text);

        font-size: 10px;
        font-weight: 950;
    }

    .ra-rider-info span {
        display: block;
        margin-top: 4px;

        color: var(--ra-muted);

        font-size: 8px;
        font-weight: 750;
    }

    .ra-rider-load {
        display: grid;
        justify-items: end;
        gap: 4px;
    }

    .ra-rider-load strong {
        color: var(--ra-maroon);

        font-size: 10px;
        font-weight: 950;
    }

    .ra-rider-load small {
        color: var(--ra-muted);

        font-size: 8px;
        font-weight: 700;
    }

    .ra-online {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        gap: 6px;

        padding: 0 9px;

        border: 1px solid #CFE8DA;
        border-radius: 999px;

        background: var(--ra-success-soft);
        color: var(--ra-success);

        font-size: 9px;
        font-weight: 900;
        white-space: nowrap;
    }

    .ra-online::before {
        width: 6px;
        height: 6px;

        border-radius: 999px;

        background: currentColor;

        content: "";
    }

    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 1180px) {
        .ra-stats {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .ra-main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .ra-hero {
            align-items: flex-start;
            flex-direction: column;

            padding: 26px 22px;
        }

        .ra-hero .ra-btn {
            width: 100%;
        }

        .ra-stats {
            grid-template-columns: 1fr;
        }

        .ra-parcel-row {
            grid-template-columns:
                auto
                minmax(0, 1fr);
        }

        .ra-parcel-actions {
            grid-column: 1 / -1;

            width: 100%;
        }

        .ra-parcel-actions .ra-btn {
            flex: 1;
        }
    }

    /* =====================================================
       DARK MODE
    ====================================================== */

    html.dark .ra-page {
        color: #F5EFE8;
    }

    html.dark .ra-hero,
    html.dark .ra-card,
    html.dark .ra-stat-card {
        background:
            radial-gradient(
                circle at 94% 8%,
                rgba(193, 151, 113, 0.08),
                transparent 28%
            ),
            linear-gradient(
                180deg,
                #211B17 0%,
                #1A1412 100%
            ) !important;

        border-color: #3B2E27 !important;

        box-shadow: none !important;
    }

    html.dark .ra-card-head {
        background:
            radial-gradient(
                circle at 96% 6%,
                rgba(193, 151, 113, 0.08),
                transparent 30%
            ),
            #1D1715 !important;

        border-color: #3B2E27 !important;
    }

    html.dark .ra-hero h1,
    html.dark .ra-stat-card strong,
    html.dark .ra-card-head h2,
    html.dark .ra-parcel-main strong,
    html.dark .ra-rider-info strong {
        color: #F5EFE8 !important;
    }

    html.dark .ra-hero p,
    html.dark .ra-breadcrumb,
    html.dark .ra-stat-card small,
    html.dark .ra-stat-description,
    html.dark .ra-card-head p,
    html.dark .ra-destination,
    html.dark .ra-rider-info span,
    html.dark .ra-rider-load small {
        color: #AFA19A !important;
    }

    html.dark .ra-eyebrow,
    html.dark .ra-card-kicker {
        color: #EBA99D !important;
    }

    html.dark .ra-parcel-row {
        border-color: #30231F !important;
    }

    html.dark .ra-parcel-row:hover {
        background: #241817 !important;
    }

    html.dark .ra-parcel-icon,
    html.dark .ra-stat-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .ra-area {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .ra-rider {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ra-rider:hover {
        background: #241817 !important;
        border-color: #60463A !important;
    }

    html.dark .ra-rider-avatar {
        background: #A84538 !important;
    }

    html.dark .ra-rider-load strong {
        color: #EBA99D !important;
    }

    html.dark .ra-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .ra-btn-primary:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }

    html.dark .ra-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }
</style>


<div class="ra-page">

    {{-- =====================================================
        BREADCRUMB
    ====================================================== --}}

    <nav class="ra-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('logistics.dashboard') }}">
            Dashboard
        </a>

        <span>/</span>

        <strong>
            Rider Assignment
        </strong>
    </nav>


    {{-- =====================================================
        HERO
    ====================================================== --}}

    <section class="ra-hero">
        <div>
            <span class="ra-eyebrow">
                Logistics
            </span>

            <h1>
                Rider Assignment
            </h1>

            <p>
                Assign sorted parcels to available riders and keep delivery
                workloads balanced across each logistics area.
            </p>
        </div>

        <button
            type="button"
            class="ra-btn ra-btn-primary"
            data-assign-rider
        >
            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['plus'] !!}
            </svg>

            Assign Rider
        </button>
    </section>


    {{-- =====================================================
        STAT CARDS
    ====================================================== --}}

    <section class="ra-stats" aria-label="Rider assignment summary">
        @foreach($stats as $stat)
            @php
                $toneClass = match($stat['tone']) {
                    'success' => 'is-success',
                    'warning' => 'is-warning',
                    'neutral' => 'is-neutral',
                    default => '',
                };
            @endphp

            <article class="ra-stat-card">
                <div class="ra-stat-top">
                    <span class="ra-stat-icon {{ $toneClass }}" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons[$stat['icon']] !!}
                        </svg>
                    </span>

                    <span class="ra-stat-status">
                        Live
                    </span>
                </div>

                <div>
                    <small>
                        {{ $stat['label'] }}
                    </small>

                    <strong>
                        {{ $stat['value'] }}
                    </strong>

                    <span class="ra-stat-description">
                        {{ $stat['description'] }}
                    </span>
                </div>
            </article>
        @endforeach
    </section>


    {{-- =====================================================
        MAIN CONTENT
    ====================================================== --}}

    <section class="ra-main-grid">

        {{-- PARCELS --}}

        <section class="ra-card">
            <header class="ra-card-head">
                <div>
                    <span class="ra-card-kicker">
                        Assignment Queue
                    </span>

                    <h2>
                        Parcels Waiting for Rider
                    </h2>

                    <p>
                        Sorted parcels that are ready to be assigned for delivery.
                    </p>
                </div>
            </header>

            <div class="ra-parcel-list">
                @foreach($parcels as $parcel)
                    <article class="ra-parcel-row">

                        <span class="ra-parcel-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['package'] !!}
                            </svg>
                        </span>

                        <div class="ra-parcel-main">
                            <strong>
                                {{ $parcel['tracking'] }}
                            </strong>

                            <span class="ra-parcel-buyer">
                                {{ $parcel['buyer'] }}
                            </span>

                            <span class="ra-destination">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    {!! $icons['location'] !!}
                                </svg>

                                {{ $parcel['destination'] }}
                            </span>
                        </div>

                        <div class="ra-parcel-actions">
                            <span class="ra-area">
                                {{ $parcel['area'] }}
                            </span>

                            <button
                                type="button"
                                class="ra-btn ra-btn-primary ra-btn-sm"
                                data-parcel="{{ $parcel['tracking'] }}"
                            >
                                Assign

                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    {!! $icons['arrow'] !!}
                                </svg>
                            </button>
                        </div>

                    </article>
                @endforeach
            </div>
        </section>


        {{-- AVAILABLE RIDERS --}}

        <aside class="ra-card">
            <header class="ra-card-head">
                <div>
                    <span class="ra-card-kicker">
                        Rider Availability
                    </span>

                    <h2>
                        Available Riders
                    </h2>

                    <p>
                        Select a rider based on area and current delivery load.
                    </p>
                </div>

                <span class="ra-online">
                    12 Online
                </span>
            </header>

            <div class="ra-riders-body">
                @foreach($riders as $rider)
                    @php
                        $initials = collect(explode(' ', $rider['name']))
                            ->filter()
                            ->take(2)
                            ->map(fn($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                            ->implode('');
                    @endphp

                    <button
                        type="button"
                        class="ra-rider"
                        data-rider="{{ $rider['name'] }}"
                    >
                        <span class="ra-rider-avatar">
                            {{ $initials ?: 'R' }}
                        </span>

                        <span class="ra-rider-info">
                            <strong>
                                {{ $rider['name'] }}
                            </strong>

                            <span>
                                {{ $rider['area'] }}
                            </span>
                        </span>

                        <span class="ra-rider-load">
                            <strong>
                                {{ $rider['active'] }}
                            </strong>

                            <small>
                                active
                            </small>
                        </span>
                    </button>
                @endforeach
            </div>
        </aside>

    </section>

</div>

@endsection