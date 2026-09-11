@extends('rider.app')

@section('title', 'Delivery History — LIKHAE Rider')

@php
    $summary = [
        [
            'label' => 'Completed Deliveries',
            'value' => '342',
            'change' => '↑ 12.5%',
            'tone' => 'primary',
            'icon' => 'completed',
        ],
        [
            'label' => 'Successful Rate',
            'value' => '98%',
            'change' => 'Excellent',
            'tone' => 'success',
            'icon' => 'trend',
        ],
        [
            'label' => 'Customer Rating',
            'value' => '4.9 ★',
            'change' => 'Top rated',
            'tone' => 'primary',
            'icon' => 'star',
        ],
        [
            'label' => 'Total Earnings',
            'value' => '₱28,450',
            'change' => '↑ 8.2%',
            'tone' => 'success',
            'icon' => 'wallet',
        ],
        [
            'label' => 'This Month',
            'value' => '86',
            'change' => 'Deliveries',
            'tone' => 'warning',
            'icon' => 'calendar',
        ],
    ];

    $deliveries = [
        [
            'tracking' => 'LH-2026-0901',
            'buyer' => 'Ana Reyes',
            'location' => 'Los Baños, Laguna',
            'date' => 'September 01, 2026',
            'payment' => '₱850',
            'rating' => '5.0',
        ],
        [
            'tracking' => 'LH-2026-0830',
            'buyer' => 'Carlo Mendoza',
            'location' => 'Calamba, Laguna',
            'date' => 'August 30, 2026',
            'payment' => '₱1,200',
            'rating' => '4.9',
        ],
        [
            'tracking' => 'LH-2026-0828',
            'buyer' => 'Maria Cruz',
            'location' => 'Santa Cruz, Laguna',
            'date' => 'August 28, 2026',
            'payment' => '₱560',
            'rating' => '5.0',
        ],
    ];

    $monthly = [
        ['label' => 'Deliveries', 'value' => '86'],
        ['label' => 'Average Rating', 'value' => '4.9'],
        ['label' => 'Income', 'value' => '₱7,850'],
    ];

    $icons = [
        'completed' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="m8 12 2.5 2.5L16 9"/>
        ',
        'trend' => '
            <path d="m3 17 6-6 4 4 7-8"/>
            <path d="M14 7h6v6"/>
        ',
        'star' => '
            <path d="m12 3 2.6 5.3 5.9.9-4.3 4.1 1 5.8-5.2-2.7-5.2 2.7 1-5.8-4.3-4.1 5.9-.9z"/>
        ',
        'wallet' => '
            <path d="M4 6h15a2 2 0 0 1 2 2v10H4a2 2 0 0 1-2-2V7a3 3 0 0 1 3-3h12"/>
            <path d="M16 11h5v4h-5a2 2 0 0 1 0-4Z"/>
        ',
        'calendar' => '
            <rect x="3" y="5" width="18" height="16" rx="2"/>
            <path d="M16 3v4"/>
            <path d="M8 3v4"/>
            <path d="M3 10h18"/>
        ',
        'search' => '
            <circle cx="11" cy="11" r="7"/>
            <path d="m20 20-3.5-3.5"/>
        ',
        'location' => '
            <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"/>
            <circle cx="12" cy="10" r="2"/>
        ',
        'eye' => '
            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
            <circle cx="12" cy="12" r="2.5"/>
        ',
        'export' => '
            <path d="M12 3v12"/>
            <path d="m7 10 5 5 5-5"/>
            <path d="M5 21h14"/>
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
        --dh-page: #FBF7F2;
        --dh-surface: #FFFDF9;
        --dh-soft: #F7F0E8;
        --dh-warm: #F1E2DA;
        --dh-line: #E8D8C8;
        --dh-line-strong: #DCC7B7;

        --dh-primary: #661F18;
        --dh-primary-dark: #4D1712;
        --dh-primary-soft: #F1E1DB;

        --dh-text: #3A211B;
        --dh-text-strong: #21110D;
        --dh-muted: #987865;
        --dh-muted-light: #B09A8A;

        --dh-success: #256F4A;
        --dh-success-soft: #EAF7EF;
        --dh-warning: #9A5B11;
        --dh-warning-soft: #FFF4D9;

        --dh-shadow: 0 12px 34px rgba(74,35,27,.06);
        --dh-shadow-lg: 0 24px 60px rgba(74,35,27,.10);
    }

    .dh-page {
        display: grid;
        gap: 24px;
        width: 100%;
        color: var(--dh-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .dh-page * {
        box-sizing: border-box;
    }

    .dh-page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
    }

    .dh-page-head small {
        display: block;
        color: var(--dh-primary);
        font-size: 9px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .dh-page-head h1 {
        margin: 7px 0 0;
        color: var(--dh-text);
        font-size: 22px;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .dh-page-head p {
        margin: 6px 0 0;
        color: var(--dh-muted);
        font-size: 9px;
    }

    .dh-btn {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 14px;
        border: 1px solid transparent;
        border-radius: 12px;
        font-size: 9px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        transition: 160ms ease;
    }

    .dh-btn:hover {
        transform: translateY(-1px);
    }

    .dh-btn svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .dh-btn-primary {
        border-color: var(--dh-primary);
        background: var(--dh-primary);
        color: #FFF;
        box-shadow: 0 12px 22px rgba(102,31,24,.14);
    }

    .dh-btn-primary:hover {
        border-color: var(--dh-primary-dark);
        background: var(--dh-primary-dark);
    }

    .dh-btn-soft {
        border-color: var(--dh-line-strong);
        background: var(--dh-surface);
        color: var(--dh-primary);
    }

    .dh-btn-soft:hover {
        background: var(--dh-warm);
    }

    .dh-hero {
        display: flex;
        min-height: 190px;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 28px 30px;
        border: 1px solid var(--dh-line);
        border-radius: 28px;
        background:
            radial-gradient(circle at 90% 18%, rgba(194,151,113,.24), transparent 28%),
            linear-gradient(120deg, #FBF5EE 0%, #F4EBE3 55%, #EAD9C8 100%);
        box-shadow: var(--dh-shadow);
    }

    .dh-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--dh-primary);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .dh-hero-kicker::before {
        width: 20px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .dh-hero h2 {
        margin: 10px 0 0;
        color: var(--dh-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px,5vw,66px);
        font-weight: 400;
        line-height: .96;
        letter-spacing: -.055em;
    }

    .dh-hero p {
        max-width: 690px;
        margin: 12px 0 0;
        color: var(--dh-muted);
        font-size: 10px;
        line-height: 1.65;
    }

    .dh-hero strong {
        color: var(--dh-primary);
    }

    .dh-stats {
        display: grid;
        grid-template-columns: repeat(5,minmax(0,1fr));
        gap: 14px;
    }

    .dh-stat {
        min-height: 126px;
        padding: 15px;
        border: 1px solid var(--dh-line);
        border-radius: 18px;
        background: rgba(255,253,249,.95);
        box-shadow: var(--dh-shadow);
        transition: 160ms ease;
    }

    .dh-stat:hover {
        transform: translateY(-2px);
        box-shadow: var(--dh-shadow-lg);
    }

    .dh-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
    }

    .dh-stat-icon {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid #E4C8BF;
        border-radius: 11px;
        background: var(--dh-primary-soft);
        color: var(--dh-primary);
    }

    .dh-stat-icon svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .dh-stat-badge {
        display: inline-flex;
        min-height: 23px;
        align-items: center;
        padding: 0 7px;
        border: 1px solid #E7C6BC;
        border-radius: 999px;
        background: #F8E9E4;
        color: var(--dh-primary);
        font-size: 7px;
        font-weight: 900;
        white-space: nowrap;
    }

    .dh-stat-badge.is-success {
        border-color: #CFE8DA;
        background: var(--dh-success-soft);
        color: var(--dh-success);
    }

    .dh-stat-badge.is-warning {
        border-color: #EACF8C;
        background: var(--dh-warning-soft);
        color: var(--dh-warning);
    }

    .dh-stat label {
        display: block;
        margin-top: 14px;
        color: var(--dh-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dh-stat strong {
        display: block;
        margin-top: 5px;
        color: var(--dh-text-strong);
        font-size: 22px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .dh-card {
        overflow: hidden;
        border: 1px solid var(--dh-line);
        border-radius: 22px;
        background: var(--dh-surface);
        box-shadow: var(--dh-shadow);
    }

    .dh-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 20px 16px;
        border-bottom: 1px solid var(--dh-line);
        background: linear-gradient(180deg,#FFFDF9 0%,#FBF7F2 100%);
    }

    .dh-card-head small {
        display: block;
        color: var(--dh-primary);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .dh-card-head h3 {
        margin: 7px 0 0;
        color: var(--dh-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .dh-card-head p {
        margin: 7px 0 0;
        color: var(--dh-muted);
        font-size: 9px;
        line-height: 1.45;
    }

    .dh-chip {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        padding: 0 9px;
        border: 1px solid #E7C6BC;
        border-radius: 999px;
        background: #F8E9E4;
        color: var(--dh-primary);
        font-size: 7px;
        font-weight: 900;
        white-space: nowrap;
    }

    .dh-filter {
        display: grid;
        grid-template-columns: minmax(0,1fr) 220px auto;
        gap: 12px;
        padding: 18px 20px;
    }

    .dh-field label {
        display: block;
        margin-bottom: 7px;
        color: var(--dh-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dh-search {
        position: relative;
    }

    .dh-search svg {
        position: absolute;
        top: 50%;
        left: 12px;
        width: 15px;
        height: 15px;
        color: var(--dh-muted);
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .dh-input {
        width: 100%;
        min-height: 40px;
        padding: 0 12px;
        border: 1px solid var(--dh-line);
        border-radius: 11px;
        background: var(--dh-soft);
        color: var(--dh-text);
        font-size: 9px;
        font-weight: 750;
        outline: none;
        transition: 150ms ease;
    }

    .dh-search .dh-input {
        padding-left: 38px;
    }

    .dh-input:focus {
        border-color: var(--dh-line-strong);
        background: #FFF;
        box-shadow: 0 0 0 4px rgba(102,31,24,.06);
    }

    .dh-history {
        display: grid;
    }

    .dh-history-row {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--dh-line);
        transition: 150ms ease;
    }

    .dh-history-row:last-child {
        border-bottom: 0;
    }

    .dh-history-row:hover {
        background: var(--dh-soft);
    }

    .dh-history-icon {
        display: grid;
        width: 44px;
        height: 44px;
        place-items: center;
        border: 1px solid #E4C8BF;
        border-radius: 14px;
        background: var(--dh-primary-soft);
        color: var(--dh-primary);
    }

    .dh-history-icon svg {
        width: 19px;
        height: 19px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
    }

    .dh-history-main {
        min-width: 0;
    }

    .dh-history-main h4 {
        margin: 0;
        color: var(--dh-text);
        font-size: 10px;
        font-weight: 950;
    }

    .dh-history-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px 12px;
        margin-top: 6px;
        color: var(--dh-muted);
        font-size: 8px;
    }

    .dh-history-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .dh-history-meta svg {
        width: 12px;
        height: 12px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .dh-history-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 7px;
    }

    .dh-status {
        display: inline-flex;
        min-height: 24px;
        align-items: center;
        gap: 6px;
        padding: 0 8px;
        border: 1px solid #CFE8DA;
        border-radius: 999px;
        background: var(--dh-success-soft);
        color: var(--dh-success);
        font-size: 7px;
        font-weight: 900;
    }

    .dh-status::before {
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .dh-rating {
        display: inline-flex;
        min-height: 24px;
        align-items: center;
        padding: 0 8px;
        border: 1px solid #E7C6BC;
        border-radius: 999px;
        background: #F8E9E4;
        color: var(--dh-primary);
        font-size: 7px;
        font-weight: 900;
    }

    .dh-payment {
        color: var(--dh-text-strong);
        font-size: 9px;
        font-weight: 950;
        white-space: nowrap;
    }

    .dh-performance-grid {
        display: grid;
        grid-template-columns: repeat(3,minmax(0,1fr));
        gap: 12px;
        padding: 18px 20px 20px;
    }

    .dh-performance-card {
        padding: 16px;
        border: 1px solid var(--dh-line);
        border-radius: 14px;
        background: var(--dh-soft);
    }

    .dh-performance-card span {
        color: var(--dh-muted);
        font-size: 8px;
        font-weight: 850;
    }

    .dh-performance-card strong {
        display: block;
        margin-top: 7px;
        color: var(--dh-text);
        font-size: 20px;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .dh-performance-card:last-child strong {
        color: var(--dh-primary);
    }

    .dh-modal {
        visibility: hidden;
        position: fixed;
        inset: 0;
        z-index: 100;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgba(0,0,0,.48);
        opacity: 0;
        backdrop-filter: blur(4px);
        transition: 180ms ease;
    }

    .dh-modal.is-open {
        visibility: visible;
        opacity: 1;
    }

    .dh-modal-panel {
        width: min(420px,100%);
        padding: 24px;
        border: 1px solid var(--dh-line);
        border-radius: 22px;
        background: var(--dh-surface);
        box-shadow: var(--dh-shadow-lg);
        transform: scale(.96);
        transition: 180ms ease;
    }

    .dh-modal.is-open .dh-modal-panel {
        transform: scale(1);
    }

    .dh-modal h3 {
        margin: 0;
        color: var(--dh-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        letter-spacing: -.04em;
    }

    .dh-modal p {
        margin: 8px 0 0;
        color: var(--dh-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .dh-modal button {
        width: 100%;
        min-height: 40px;
        margin-top: 18px;
        border: 0;
        border-radius: 11px;
        background: var(--dh-primary);
        color: #FFF;
        font-size: 9px;
        font-weight: 900;
        cursor: pointer;
    }

    @media (max-width: 1260px) {
        .dh-stats {
            grid-template-columns: repeat(3,minmax(0,1fr));
        }
    }

    @media (max-width: 860px) {
        .dh-page-head,
        .dh-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .dh-hero {
            padding: 26px 22px;
        }

        .dh-stats {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }

        .dh-filter {
            grid-template-columns: 1fr;
        }

        .dh-history-row {
            grid-template-columns: auto minmax(0,1fr);
        }

        .dh-history-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
            padding-left: 58px;
        }
    }

    @media (max-width: 560px) {
        .dh-stats,
        .dh-performance-grid {
            grid-template-columns: 1fr;
        }

        .dh-page-head .dh-btn {
            width: 100%;
        }

        .dh-history-row {
            grid-template-columns: 1fr;
        }

        .dh-history-actions {
            grid-column: auto;
            padding-left: 0;
        }
    }

    html.dark .dh-page {
        color: #F5EFE8;
    }

    html.dark .dh-hero,
    html.dark .dh-stat,
    html.dark .dh-card,
    html.dark .dh-modal-panel {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg,#211B17 0%,#1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .dh-card-head {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .dh-page-head h1,
    html.dark .dh-hero h2,
    html.dark .dh-stat strong,
    html.dark .dh-card-head h3,
    html.dark .dh-history-main h4,
    html.dark .dh-payment,
    html.dark .dh-performance-card strong,
    html.dark .dh-modal h3 {
        color: #F5EFE8 !important;
    }

    html.dark .dh-page-head p,
    html.dark .dh-hero p,
    html.dark .dh-stat label,
    html.dark .dh-card-head p,
    html.dark .dh-history-meta,
    html.dark .dh-performance-card span,
    html.dark .dh-modal p {
        color: #AFA19A !important;
    }

    html.dark .dh-input,
    html.dark .dh-performance-card,
    html.dark .dh-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .dh-history-row {
        border-color: #30231F !important;
    }

    html.dark .dh-history-row:hover {
        background: #241817 !important;
    }

    html.dark .dh-history-icon,
    html.dark .dh-stat-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .dh-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }
</style>

<div class="dh-page">

    <section class="dh-page-head">
        <div>
            <small>Delivery Records</small>
            <h1>Delivery History</h1>
            <p>Review completed deliveries, ratings, earnings, and monthly performance.</p>
        </div>

        <button type="button" id="exportHistoryButton" class="dh-btn dh-btn-primary">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['export'] !!}
            </svg>

            Export History
        </button>
    </section>

    <section class="dh-hero">
        <div>
            <span class="dh-hero-kicker">September 2026</span>

            <h2>342 deliveries completed.</h2>

            <p>
                You are maintaining a <strong>98% success rate</strong> with an
                average customer rating of <strong>4.9 stars</strong>.
            </p>
        </div>
    </section>

    <section class="dh-stats">
        @foreach($summary as $stat)
            <article class="dh-stat">
                <div class="dh-stat-top">
                    <span class="dh-stat-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons[$stat['icon']] !!}
                        </svg>
                    </span>

                    <span class="dh-stat-badge
                        {{ $stat['tone'] === 'success' ? 'is-success' : '' }}
                        {{ $stat['tone'] === 'warning' ? 'is-warning' : '' }}
                    ">
                        {{ $stat['change'] }}
                    </span>
                </div>

                <label>{{ $stat['label'] }}</label>
                <strong>{{ $stat['value'] }}</strong>
            </article>
        @endforeach
    </section>

    <section class="dh-card">
        <header class="dh-card-head">
            <div>
                <small>Filters</small>
                <h3>Find a Delivery</h3>
                <p>Search completed deliveries by tracking number or date.</p>
            </div>

            <span class="dh-chip">History search</span>
        </header>

        <div class="dh-filter">
            <div class="dh-field">
                <label for="historySearch">Search</label>

                <div class="dh-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        {!! $icons['search'] !!}
                    </svg>

                    <input
                        id="historySearch"
                        type="text"
                        class="dh-input"
                        placeholder="Search tracking number..."
                        autocomplete="off"
                    >
                </div>
            </div>

            <div class="dh-field">
                <label for="historyDate">Date</label>

                <input
                    id="historyDate"
                    type="date"
                    class="dh-input"
                >
            </div>

            <button
                type="button"
                id="historyFilterButton"
                class="dh-btn dh-btn-primary"
                style="align-self:end;"
            >
                Filter
            </button>
        </div>
    </section>

    <section class="dh-card">
        <header class="dh-card-head">
            <div>
                <small>Completed</small>
                <h3>Completed Deliveries</h3>
                <p>Your previous successful parcel deliveries.</p>
            </div>

            <span class="dh-chip">
                <span id="visibleHistoryCount">{{ count($deliveries) }}</span>&nbsp;shown
            </span>
        </header>

        <div class="dh-history" id="deliveryHistoryList">
            @foreach($deliveries as $delivery)
                <article
                    class="dh-history-row"
                    data-search="{{ strtolower($delivery['tracking'] . ' ' . $delivery['buyer'] . ' ' . $delivery['location']) }}"
                    data-date="{{ \Illuminate\Support\Carbon::parse($delivery['date'])->format('Y-m-d') }}"
                >
                    <span class="dh-history-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['completed'] !!}
                        </svg>
                    </span>

                    <div class="dh-history-main">
                        <h4>{{ $delivery['tracking'] }}</h4>

                        <div class="dh-history-meta">
                            <span>Buyer: {{ $delivery['buyer'] }}</span>

                            <span>
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    {!! $icons['location'] !!}
                                </svg>

                                {{ $delivery['location'] }}
                            </span>

                            <span>{{ $delivery['date'] }}</span>
                        </div>
                    </div>

                    <div class="dh-history-actions">
                        <span class="dh-status">Delivered</span>

                        <span class="dh-rating">
                            {{ $delivery['rating'] }} ★
                        </span>

                        <strong class="dh-payment">
                            {{ $delivery['payment'] }}
                        </strong>

                        <button
                            type="button"
                            class="dh-btn dh-btn-soft history-view-button"
                            data-tracking="{{ $delivery['tracking'] }}"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['eye'] !!}
                            </svg>

                            View
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="dh-card">
        <header class="dh-card-head">
            <div>
                <small>Performance</small>
                <h3>Monthly Performance</h3>
                <p>Your current month delivery totals and earnings.</p>
            </div>
        </header>

        <div class="dh-performance-grid">
            @foreach($monthly as $item)
                <article class="dh-performance-card">
                    <span>{{ $item['label'] }}</span>
                    <strong>{{ $item['value'] }}</strong>
                </article>
            @endforeach
        </div>
    </section>

</div>

<div id="historyModal" class="dh-modal">
    <div class="dh-modal-panel">
        <h3 id="historyModalTitle">Delivery Details</h3>

        <p id="historyModalText">
            Delivery record selected.
        </p>

        <button type="button" id="closeHistoryModal">
            Done
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput =
        document.getElementById('historySearch');

    const dateInput =
        document.getElementById('historyDate');

    const filterButton =
        document.getElementById('historyFilterButton');

    const rows =
        Array.from(
            document.querySelectorAll('.dh-history-row')
        );

    const visibleCount =
        document.getElementById('visibleHistoryCount');

    const exportButton =
        document.getElementById('exportHistoryButton');

    const modal =
        document.getElementById('historyModal');

    const modalTitle =
        document.getElementById('historyModalTitle');

    const modalText =
        document.getElementById('historyModalText');

    const closeModalButton =
        document.getElementById('closeHistoryModal');


    function filterHistory() {
        const search =
            (searchInput?.value || '')
                .trim()
                .toLowerCase();

        const date =
            dateInput?.value || '';

        let visible = 0;

        rows.forEach(function (row) {
            const matchesSearch =
                !search
                || (row.dataset.search || '')
                    .includes(search);

            const matchesDate =
                !date
                || row.dataset.date === date;

            const show =
                matchesSearch && matchesDate;

            row.hidden = !show;

            if (show) {
                visible++;
            }
        });

        if (visibleCount) {
            visibleCount.textContent =
                String(visible);
        }
    }


    function openModal(title, text) {
        if (!modal) {
            return;
        }

        if (modalTitle) {
            modalTitle.textContent = title;
        }

        if (modalText) {
            modalText.textContent = text;
        }

        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }


    function closeModal() {
        modal?.classList.remove('is-open');
        document.body.style.overflow = '';
    }


    searchInput?.addEventListener(
        'input',
        filterHistory
    );


    filterButton?.addEventListener(
        'click',
        filterHistory
    );


    dateInput?.addEventListener(
        'change',
        filterHistory
    );


    document
        .querySelectorAll('.history-view-button')
        .forEach(function (button) {
            button.addEventListener(
                'click',
                function () {
                    openModal(
                        button.dataset.tracking || 'Delivery Details',
                        'This delivery record is ready to connect to your rider delivery details page.'
                    );
                }
            );
        });


    exportButton?.addEventListener(
        'click',
        function () {
            openModal(
                'Export History',
                'The delivery history is ready for export. Connect this action to your Laravel export endpoint when the backend is ready.'
            );
        }
    );


    closeModalButton?.addEventListener(
        'click',
        closeModal
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


    filterHistory();
});
</script>
@endpush
