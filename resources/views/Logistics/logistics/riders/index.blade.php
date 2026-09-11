@extends('logistics.app')

@section('title', 'Rider Management — LIKHAE Logistics')

@php
    $stats = [
        [
            'label' => 'Total Riders',
            'value' => 48,
            'description' => 'Registered courier accounts',
            'tone' => 'primary',
            'icon' => 'riders',
        ],
        [
            'label' => 'Available',
            'value' => 32,
            'description' => 'Ready for assignment',
            'tone' => 'success',
            'icon' => 'available',
        ],
        [
            'label' => 'Delivering',
            'value' => 12,
            'description' => 'Currently on the road',
            'tone' => 'primary',
            'icon' => 'delivery',
        ],
        [
            'label' => 'Inactive',
            'value' => 4,
            'description' => 'Not accepting assignments',
            'tone' => 'warning',
            'icon' => 'inactive',
        ],
    ];

    $riders = [
        [
            'id' => 1,
            'code' => 'Rider 01',
            'name' => 'Juan Dela Cruz',
            'area' => 'Area A',
            'status' => 'Available',
            'parcels' => 3,
            'rating' => '4.9',
            'vehicle' => 'Motorcycle',
        ],
        [
            'id' => 2,
            'code' => 'Rider 02',
            'name' => 'Mark Santos',
            'area' => 'Area B',
            'status' => 'Delivering',
            'parcels' => 5,
            'rating' => '4.8',
            'vehicle' => 'Motorcycle',
        ],
        [
            'id' => 3,
            'code' => 'Rider 03',
            'name' => 'Pedro Reyes',
            'area' => 'Area C',
            'status' => 'Available',
            'parcels' => 2,
            'rating' => '4.9',
            'vehicle' => 'Motorcycle',
        ],
        [
            'id' => 4,
            'code' => 'Rider 04',
            'name' => 'Carlo Mendoza',
            'area' => 'Area D',
            'status' => 'Inactive',
            'parcels' => 0,
            'rating' => '4.7',
            'vehicle' => 'Van',
        ],
    ];

    $icons = [
        'riders' => '
            <circle cx="8" cy="7" r="3"/>
            <path d="M3 19c0-3 2-5 5-5"/>
            <circle cx="17" cy="8" r="2.5"/>
            <path d="M14 19c.2-2.4 1.7-4 4-4 1.2 0 2.2.4 3 1.1"/>
        ',
        'available' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="m8 12 2.7 2.7L16.5 9"/>
        ',
        'delivery' => '
            <path d="M3 6h11v11H3z"/>
            <path d="M14 10h4l3 3v4h-7z"/>
            <circle cx="7" cy="19" r="2"/>
            <circle cx="18" cy="19" r="2"/>
        ',
        'inactive' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="M8 12h8"/>
        ',
        'search' => '
            <circle cx="11" cy="11" r="7"/>
            <path d="m20 20-3.5-3.5"/>
        ',
        'plus' => '
            <path d="M12 5v14"/>
            <path d="M5 12h14"/>
        ',
        'vehicle' => '
            <path d="M5 16h10"/>
            <path d="m9 16 2-5h5l3 5"/>
            <circle cx="7" cy="18" r="2"/>
            <circle cx="18" cy="18" r="2"/>
        ',
        'eye' => '
            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
            <circle cx="12" cy="12" r="2.5"/>
        ',
        'star' => '
            <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.8 1-6.1-4.4-4.3 6.1-.9z"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --rm-bg: #FBF7F2;
        --rm-bg-soft: #F6EFE7;
        --rm-bg-warm: #F3E4DE;
        --rm-card: #FFFDF9;

        --rm-border: #EADCCC;
        --rm-border-strong: #DBCEC1;

        --rm-maroon: #561C17;
        --rm-maroon-2: #642920;
        --rm-maroon-dark: #3E130F;

        --rm-text: #3B211B;
        --rm-text-dark: #1C160F;
        --rm-brown: #6C4936;
        --rm-muted: #987865;
        --rm-muted-light: #A99386;

        --rm-tan: #C19771;

        --rm-success: #256F4A;
        --rm-success-soft: #EAF7EF;

        --rm-warning: #9A5B11;
        --rm-warning-soft: #FFF6DE;

        --rm-danger: #B42318;
        --rm-danger-soft: #FCEBE9;

        --rm-shadow: 0 8px 24px rgba(86, 28, 23, 0.055);
        --rm-shadow-hover: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .rm-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--rm-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .rm-page * {
        box-sizing: border-box;
    }

    .rm-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        color: var(--rm-muted);
        font-size: 10px;
        font-weight: 750;
    }

    .rm-breadcrumb a {
        color: var(--rm-muted);
        text-decoration: none;
        transition: 150ms ease;
    }

    .rm-breadcrumb a:hover {
        color: var(--rm-maroon);
    }

    .rm-breadcrumb strong {
        color: var(--rm-text);
        font-weight: 900;
    }

    .rm-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--rm-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--rm-shadow);
    }

    .rm-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--rm-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .rm-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .rm-hero h1 {
        margin: 10px 0 0;
        color: var(--rm-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .rm-hero p {
        max-width: 680px;
        margin: 13px 0 0;
        color: var(--rm-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .rm-btn {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 15px;
        border: 1px solid transparent;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        transition: 160ms ease;
    }

    .rm-btn:hover {
        transform: translateY(-1px);
    }

    .rm-btn svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rm-btn-primary {
        background: var(--rm-maroon);
        border-color: var(--rm-maroon);
        color: #FFFFFF;
        box-shadow: 0 10px 22px rgba(86,28,23,.14);
    }

    .rm-btn-primary:hover {
        background: var(--rm-maroon-dark);
        border-color: var(--rm-maroon-dark);
    }

    .rm-btn-soft {
        background: var(--rm-card);
        border-color: var(--rm-border);
        color: var(--rm-maroon);
    }

    .rm-btn-soft:hover {
        background: var(--rm-bg-warm);
        border-color: var(--rm-tan);
    }

    .rm-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0,1fr));
        gap: 14px;
    }

    .rm-stat {
        display: flex;
        min-height: 128px;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        padding: 16px;
        border: 1px solid var(--rm-border);
        border-radius: 20px;
        background:
            radial-gradient(circle at 94% 6%, rgba(193,151,113,.14), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
        box-shadow: var(--rm-shadow);
        transition: 160ms ease;
    }

    .rm-stat:hover {
        transform: translateY(-2px);
        border-color: var(--rm-tan);
        box-shadow: var(--rm-shadow-hover);
    }

    .rm-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .rm-stat-icon {
        display: grid;
        width: 38px;
        height: 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 13px;
        background: var(--rm-bg-warm);
        color: var(--rm-maroon);
    }

    .rm-stat-icon.is-success {
        border-color: #CFE8DA;
        background: var(--rm-success-soft);
        color: var(--rm-success);
    }

    .rm-stat-icon.is-warning {
        border-color: #EAD39A;
        background: var(--rm-warning-soft);
        color: var(--rm-warning);
    }

    .rm-stat-icon svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.75;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rm-stat-label {
        display: block;
        color: var(--rm-muted);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .rm-stat-value {
        display: block;
        margin-top: 6px;
        color: var(--rm-text-dark);
        font-size: 28px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.045em;
    }

    .rm-stat-description {
        display: block;
        margin-top: 6px;
        color: var(--rm-muted);
        font-size: 9px;
        font-weight: 700;
    }

    .rm-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 15px;
        border: 1px solid var(--rm-border);
        border-radius: 20px;
        background: var(--rm-card);
        box-shadow: var(--rm-shadow);
    }

    .rm-search {
        position: relative;
        width: min(380px, 100%);
    }

    .rm-search svg {
        position: absolute;
        top: 50%;
        left: 13px;
        width: 16px;
        height: 16px;
        color: var(--rm-muted);
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .rm-search input {
        width: 100%;
        min-height: 40px;
        padding: 0 14px 0 40px;
        border: 1px solid var(--rm-border);
        border-radius: 12px;
        background: var(--rm-bg-soft);
        color: var(--rm-text);
        font-size: 10px;
        font-weight: 750;
        outline: none;
        transition: 150ms ease;
    }

    .rm-search input::placeholder {
        color: var(--rm-muted-light);
    }

    .rm-search input:focus {
        border-color: var(--rm-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86,28,23,.07);
    }

    .rm-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .rm-filter {
        min-height: 36px;
        padding: 0 13px;
        border: 1px solid var(--rm-border);
        border-radius: 10px;
        background: var(--rm-card);
        color: var(--rm-brown);
        font-size: 9px;
        font-weight: 900;
        cursor: pointer;
        transition: 150ms ease;
    }

    .rm-filter:hover {
        border-color: var(--rm-tan);
        background: var(--rm-bg-soft);
        color: var(--rm-maroon);
    }

    .rm-filter.is-active {
        border-color: var(--rm-maroon);
        background: var(--rm-maroon);
        color: #FFFFFF;
    }

    .rm-list-card {
        overflow: hidden;
        border: 1px solid var(--rm-border);
        border-radius: 22px;
        background: var(--rm-card);
        box-shadow: var(--rm-shadow);
    }

    .rm-list-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--rm-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .rm-list-head h2 {
        margin: 0;
        color: var(--rm-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .rm-list-head p {
        margin: 7px 0 0;
        color: var(--rm-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .rm-count {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        padding: 0 9px;
        border: 1px solid #E6C7BE;
        border-radius: 999px;
        background: var(--rm-bg-warm);
        color: var(--rm-maroon);
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .rm-list {
        display: grid;
    }

    .rm-rider {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 14px;
        padding: 17px 20px;
        border-bottom: 1px solid var(--rm-border);
        transition: 150ms ease;
    }

    .rm-rider:last-child {
        border-bottom: 0;
    }

    .rm-rider:hover {
        background: var(--rm-bg-soft);
    }

    .rm-avatar {
        display: grid;
        width: 46px;
        height: 46px;
        place-items: center;
        border-radius: 999px;
        background: var(--rm-maroon);
        color: #FFFFFF;
        font-size: 9px;
        font-weight: 950;
        box-shadow: 0 8px 18px rgba(86,28,23,.12);
    }

    .rm-rider-main {
        min-width: 0;
    }

    .rm-rider-top {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .rm-rider-main h3 {
        margin: 0;
        color: var(--rm-text);
        font-size: 11px;
        font-weight: 950;
    }

    .rm-code {
        color: var(--rm-muted);
        font-size: 8px;
        font-weight: 800;
    }

    .rm-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 12px;
        margin-top: 7px;
        color: var(--rm-brown);
        font-size: 8px;
        font-weight: 750;
    }

    .rm-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .rm-meta svg {
        width: 12px;
        height: 12px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rm-status {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        gap: 6px;
        padding: 0 9px;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .rm-status::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .rm-status.is-available {
        border-color: #CFE8DA;
        background: var(--rm-success-soft);
        color: var(--rm-success);
    }

    .rm-status.is-delivering {
        border-color: #E6C7BE;
        background: var(--rm-bg-warm);
        color: var(--rm-maroon);
    }

    .rm-status.is-inactive {
        border-color: #EAD39A;
        background: var(--rm-warning-soft);
        color: var(--rm-warning);
    }

    .rm-rider-side {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .rm-area,
    .rm-load,
    .rm-rating {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        gap: 5px;
        padding: 0 9px;
        border: 1px solid var(--rm-border);
        border-radius: 999px;
        background: var(--rm-bg-soft);
        color: var(--rm-brown);
        font-size: 8px;
        font-weight: 850;
        white-space: nowrap;
    }

    .rm-rating {
        color: #9A5B11;
    }

    .rm-rating svg {
        width: 11px;
        height: 11px;
        fill: currentColor;
        stroke: currentColor;
        stroke-width: 1.4;
    }

    .rm-empty {
        display: none;
        padding: 42px 20px;
        text-align: center;
        color: var(--rm-muted);
        font-size: 10px;
    }

    .rm-empty.is-visible {
        display: block;
    }

    @media (max-width: 1180px) {
        .rm-stats {
            grid-template-columns: repeat(2, minmax(0,1fr));
        }

        .rm-rider {
            grid-template-columns: auto minmax(0,1fr);
        }

        .rm-rider-side {
            grid-column: 1 / -1;
            justify-content: flex-start;
            padding-left: 60px;
        }
    }

    @media (max-width: 820px) {
        .rm-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .rm-hero .rm-btn {
            width: 100%;
        }

        .rm-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .rm-search {
            width: 100%;
        }
    }

    @media (max-width: 560px) {
        .rm-stats {
            grid-template-columns: 1fr;
        }

        .rm-rider {
            grid-template-columns: 1fr;
        }

        .rm-rider-side {
            grid-column: auto;
            padding-left: 0;
        }

        .rm-list-head {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    html.dark .rm-page {
        color: #F5EFE8;
    }

    html.dark .rm-hero,
    html.dark .rm-stat,
    html.dark .rm-toolbar,
    html.dark .rm-list-card {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .rm-list-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .rm-hero h1,
    html.dark .rm-stat-value,
    html.dark .rm-list-head h2,
    html.dark .rm-rider-main h3 {
        color: #F5EFE8 !important;
    }

    html.dark .rm-hero p,
    html.dark .rm-breadcrumb,
    html.dark .rm-stat-label,
    html.dark .rm-stat-description,
    html.dark .rm-list-head p,
    html.dark .rm-code,
    html.dark .rm-meta {
        color: #AFA19A !important;
    }

    html.dark .rm-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .rm-search input {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .rm-search input:focus {
        background: #211B17 !important;
        border-color: #60463A !important;
    }

    html.dark .rm-filter {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #C8B7AD !important;
    }

    html.dark .rm-filter:hover {
        background: #241817 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .rm-filter.is-active {
        background: #A84538 !important;
        border-color: #A84538 !important;
        color: #FFFFFF !important;
    }

    html.dark .rm-rider {
        border-color: #30231F !important;
    }

    html.dark .rm-rider:hover {
        background: #241817 !important;
    }

    html.dark .rm-stat-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .rm-avatar {
        background: #A84538 !important;
    }

    html.dark .rm-status.is-delivering {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .rm-area,
    html.dark .rm-load,
    html.dark .rm-rating {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #C8B7AD !important;
    }

    html.dark .rm-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .rm-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .rm-btn-primary:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }
</style>

<div class="rm-page">

    <nav class="rm-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('logistics.dashboard') }}">
            Dashboard
        </a>

        <span>/</span>

        <strong>
            Riders
        </strong>
    </nav>

    <section class="rm-hero">
        <div>
            <span class="rm-eyebrow">
                Delivery Management
            </span>

            <h1>
                Rider Management
            </h1>

            <p>
                Manage rider availability, current workload, delivery areas,
                and courier account status from one place.
            </p>
        </div>

        <button
            type="button"
            class="rm-btn rm-btn-primary"
            id="addRiderButton"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['plus'] !!}
            </svg>

            Add Rider
        </button>
    </section>

    <section class="rm-stats" aria-label="Rider summary">
        @foreach($stats as $stat)
            @php
                $toneClass = match($stat['tone']) {
                    'success' => 'is-success',
                    'warning' => 'is-warning',
                    default => '',
                };
            @endphp

            <article class="rm-stat">
                <div class="rm-stat-top">
                    <span class="rm-stat-icon {{ $toneClass }}" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons[$stat['icon']] !!}
                        </svg>
                    </span>
                </div>

                <div>
                    <span class="rm-stat-label">
                        {{ $stat['label'] }}
                    </span>

                    <strong class="rm-stat-value">
                        {{ $stat['value'] }}
                    </strong>

                    <span class="rm-stat-description">
                        {{ $stat['description'] }}
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="rm-toolbar">
        <div class="rm-search">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['search'] !!}
            </svg>

            <input
                type="search"
                id="riderSearch"
                placeholder="Search rider, area, vehicle..."
                autocomplete="off"
            >
        </div>

        <div class="rm-filters" aria-label="Rider status filters">
            @foreach([
                'all' => 'All',
                'available' => 'Available',
                'delivering' => 'Delivering',
                'inactive' => 'Inactive',
            ] as $key => $label)
                <button
                    type="button"
                    class="rm-filter {{ $key === 'all' ? 'is-active' : '' }}"
                    data-filter="{{ $key }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </section>

    <section class="rm-list-card">
        <header class="rm-list-head">
            <div>
                <h2>
                    Rider Directory
                </h2>

                <p>
                    View rider information, current availability, and active delivery workload.
                </p>
            </div>

            <span class="rm-count">
                <span id="visibleRiderCount">{{ count($riders) }}</span>&nbsp;shown
            </span>
        </header>

        <div class="rm-list" id="riderList">
            @foreach($riders as $rider)
                @php
                    $statusKey = mb_strtolower($rider['status']);

                    $statusClass = match($rider['status']) {
                        'Available' => 'is-available',
                        'Delivering' => 'is-delivering',
                        default => 'is-inactive',
                    };
                @endphp

                <article
                    class="rm-rider"
                    data-status="{{ $statusKey }}"
                    data-search="{{ mb_strtolower(
                        $rider['code']
                        . ' '
                        . $rider['name']
                        . ' '
                        . $rider['area']
                        . ' '
                        . $rider['vehicle']
                    ) }}"
                >
                    <span class="rm-avatar">
                        {{ substr($rider['code'], -2) }}
                    </span>

                    <div class="rm-rider-main">
                        <div class="rm-rider-top">
                            <h3>
                                {{ $rider['name'] }}
                            </h3>

                            <span class="rm-code">
                                {{ $rider['code'] }}
                            </span>

                            <span class="rm-status {{ $statusClass }}">
                                {{ $rider['status'] }}
                            </span>
                        </div>

                        <div class="rm-meta">
                            <span>
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    {!! $icons['vehicle'] !!}
                                </svg>

                                {{ $rider['vehicle'] }}
                            </span>

                            <span>
                                {{ $rider['area'] }}
                            </span>
                        </div>
                    </div>

                    <div class="rm-rider-side">
                        <span class="rm-area">
                            {{ $rider['area'] }}
                        </span>

                        <span class="rm-load">
                            {{ $rider['parcels'] }}
                            {{ $rider['parcels'] === 1 ? 'parcel' : 'parcels' }}
                        </span>

                        <span class="rm-rating">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['star'] !!}
                            </svg>

                            {{ $rider['rating'] }}
                        </span>

                        <a
                            href="{{ route('logistics.riders.show', $rider['id']) }}"
                            class="rm-btn rm-btn-soft"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['eye'] !!}
                            </svg>

                            View
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div id="riderEmptyState" class="rm-empty">
            No riders match the selected search or filter.
        </div>
    </section>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput =
        document.getElementById('riderSearch');

    const filterButtons =
        Array.from(
            document.querySelectorAll('.rm-filter')
        );

    const riders =
        Array.from(
            document.querySelectorAll('.rm-rider')
        );

    const visibleCount =
        document.getElementById('visibleRiderCount');

    const emptyState =
        document.getElementById('riderEmptyState');

    const addRiderButton =
        document.getElementById('addRiderButton');

    let activeFilter = 'all';


    function applyFilters() {
        const search =
            (searchInput?.value || '')
                .trim()
                .toLowerCase();

        let visible = 0;

        riders.forEach(function (rider) {
            const matchesStatus =
                activeFilter === 'all'
                || rider.dataset.status === activeFilter;

            const matchesSearch =
                !search
                || (rider.dataset.search || '')
                    .includes(search);

            const show =
                matchesStatus && matchesSearch;

            rider.hidden = !show;

            if (show) {
                visible++;
            }
        });

        if (visibleCount) {
            visibleCount.textContent = visible;
        }

        if (emptyState) {
            emptyState.classList.toggle(
                'is-visible',
                visible === 0
            );
        }
    }


    filterButtons.forEach(function (button) {
        button.addEventListener(
            'click',
            function () {
                activeFilter =
                    button.dataset.filter || 'all';

                filterButtons.forEach(function (item) {
                    item.classList.toggle(
                        'is-active',
                        item === button
                    );
                });

                applyFilters();
            }
        );
    });


    searchInput?.addEventListener(
        'input',
        applyFilters
    );


    addRiderButton?.addEventListener(
        'click',
        function () {
            window.alert(
                'Connect this button to your Add Rider form or route.'
            );
        }
    );


    applyFilters();
});
</script>
@endpush
