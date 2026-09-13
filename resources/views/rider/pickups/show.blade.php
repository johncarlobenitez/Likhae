@extends('rider.app')

@section('title', 'Pickup Details — LIKHAE Rider')

@php
    $pickup = [
        'tracking' => 'LH-2026-1001',
        'status' => 'READY_FOR_PICKUP',
        'items' => '3 Items',
        'payment_type' => 'Cash on Delivery',
        'cod_amount' => '₱850',
        'seller' => 'ABC Handmade Store',
        'address' => '123 Rizal Street, Calamba Laguna',
        'contact' => '0917 555 8888',
    ];

    $summary = [
        [
            'label' => 'Tracking',
            'value' => $pickup['tracking'],
            'badge' => 'Active',
            'icon' => 'parcel',
        ],
        [
            'label' => 'Pickup Status',
            'value' => 'Ready',
            'badge' => 'Pending',
            'icon' => 'clock',
        ],
        [
            'label' => 'Parcel Items',
            'value' => $pickup['items'],
            'badge' => 'Expected',
            'icon' => 'items',
        ],
        [
            'label' => 'COD Amount',
            'value' => $pickup['cod_amount'],
            'badge' => 'Collect',
            'icon' => 'wallet',
        ],
        [
            'label' => 'Seller',
            'value' => $pickup['seller'],
            'badge' => 'Verified',
            'icon' => 'store',
        ],
    ];

    $process = [
        [
            'title' => 'Accept Pickup',
            'status' => 'Waiting',
            'description' => 'Confirm that you are taking this pickup assignment.',
        ],
        [
            'title' => 'Travel To Seller',
            'status' => 'Pending',
            'description' => 'Navigate to the seller pickup location.',
        ],
        [
            'title' => 'Confirm Parcel',
            'status' => 'Pending',
            'description' => 'Scan and verify the parcel tracking code.',
        ],
        [
            'title' => 'Bring To Sorting Center',
            'status' => 'Pending',
            'description' => 'Deliver the collected parcel to the sorting center.',
        ],
    ];

    $icons = [
        'parcel' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',
        'pickup' => '
            <path d="M21 8 12 3 3 8l9 5 9-5Z"/>
            <path d="M3 8v8l9 5 9-5V8"/>
            <path d="M12 13v8"/>
        ',
        'clock' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7v5l3 2"/>
        ',
        'items' => '
            <path d="M5 5h14v14H5z"/>
            <path d="M9 9h6"/>
            <path d="M9 13h6"/>
            <path d="M9 17h4"/>
        ',
        'wallet' => '
            <path d="M4 6h15a2 2 0 0 1 2 2v10H4a2 2 0 0 1-2-2V7a3 3 0 0 1 3-3h12"/>
            <path d="M16 11h5v4h-5a2 2 0 0 1 0-4Z"/>
        ',
        'store' => '
            <path d="M4 10v10h16V10"/>
            <path d="M3 7h18l-2-4H5z"/>
            <path d="M8 14h3v6"/>
        ',
        'location' => '
            <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"/>
            <circle cx="12" cy="10" r="2"/>
        ',
        'phone' => '
            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1A19.5 19.5 0 0 1 5.2 12 19.8 19.8 0 0 1 2.1 3.3 2 2 0 0 1 4.1 1h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 9a16 16 0 0 0 7 7l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2Z"/>
        ',
        'navigation' => '
            <path d="m3 11 18-8-8 18-2-8z"/>
        ',
        'scan' => '
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <path d="M7 7h.01"/>
            <path d="M7 12h.01"/>
            <path d="M7 17h.01"/>
            <path d="M11 7h6"/>
            <path d="M11 12h6"/>
            <path d="M11 17h6"/>
        ',
        'check' => '
            <path d="m5 12 4 4 10-10"/>
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
        --pd-page: #FBF7F2;
        --pd-surface: #FFFDF9;
        --pd-soft: #F7F0E8;
        --pd-warm: #F1E2DA;
        --pd-line: #E8D8C8;
        --pd-line-strong: #DCC7B7;

        --pd-primary: #661F18;
        --pd-primary-dark: #4D1712;
        --pd-primary-soft: #F1E1DB;

        --pd-text: #3A211B;
        --pd-text-strong: #21110D;
        --pd-muted: #987865;
        --pd-muted-light: #B09A8A;

        --pd-success: #256F4A;
        --pd-success-soft: #EAF7EF;
        --pd-warning: #9A5B11;
        --pd-warning-soft: #FFF4D9;

        --pd-shadow: 0 12px 34px rgba(74,35,27,.06);
        --pd-shadow-lg: 0 24px 60px rgba(74,35,27,.10);
    }

    .pd-page {
        display: grid;
        gap: 24px;
        width: 100%;
        color: var(--pd-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .pd-page * {
        box-sizing: border-box;
    }

    .pd-page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
    }

    .pd-page-head small {
        display: block;
        color: var(--pd-primary);
        font-size: 9px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .pd-page-head h1 {
        margin: 7px 0 0;
        color: var(--pd-text);
        font-size: 22px;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .pd-page-head p {
        margin: 6px 0 0;
        color: var(--pd-muted);
        font-size: 9px;
    }

    .pd-status-pill {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        gap: 8px;
        padding: 0 13px;
        border: 1px solid #EACF8C;
        border-radius: 13px;
        background: var(--pd-warning-soft);
        color: var(--pd-warning);
        font-size: 8px;
        font-weight: 950;
        box-shadow: var(--pd-shadow);
    }

    .pd-status-pill::before {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .pd-hero {
        display: flex;
        min-height: 190px;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 28px 30px;
        border: 1px solid var(--pd-line);
        border-radius: 28px;
        background:
            radial-gradient(circle at 90% 18%, rgba(194,151,113,.24), transparent 28%),
            linear-gradient(120deg, #FBF5EE 0%, #F4EBE3 55%, #EAD9C8 100%);
        box-shadow: var(--pd-shadow);
    }

    .pd-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--pd-primary);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .pd-hero-kicker::before {
        width: 20px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .pd-hero h2 {
        margin: 10px 0 0;
        color: var(--pd-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px,5vw,66px);
        font-weight: 400;
        line-height: .96;
        letter-spacing: -.055em;
    }

    .pd-hero p {
        max-width: 690px;
        margin: 12px 0 0;
        color: var(--pd-muted);
        font-size: 10px;
        line-height: 1.65;
    }

    .pd-hero strong {
        color: var(--pd-primary);
    }

    .pd-stats {
        display: grid;
        grid-template-columns: repeat(5,minmax(0,1fr));
        gap: 14px;
    }

    .pd-stat {
        min-height: 126px;
        padding: 15px;
        border: 1px solid var(--pd-line);
        border-radius: 18px;
        background: rgba(255,253,249,.95);
        box-shadow: var(--pd-shadow);
    }

    .pd-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
    }

    .pd-stat-icon {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid #E4C8BF;
        border-radius: 11px;
        background: var(--pd-primary-soft);
        color: var(--pd-primary);
    }

    .pd-stat-icon svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .pd-stat-badge {
        display: inline-flex;
        min-height: 23px;
        align-items: center;
        padding: 0 7px;
        border: 1px solid #E7C6BC;
        border-radius: 999px;
        background: #F8E9E4;
        color: var(--pd-primary);
        font-size: 7px;
        font-weight: 900;
        white-space: nowrap;
    }

    .pd-stat label {
        display: block;
        margin-top: 14px;
        color: var(--pd-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .pd-stat strong {
        display: block;
        margin-top: 5px;
        overflow: hidden;
        color: var(--pd-text-strong);
        font-size: 20px;
        font-weight: 950;
        line-height: 1.1;
        letter-spacing: -.04em;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .pd-main {
        display: grid;
        grid-template-columns: minmax(0,1.6fr) minmax(310px,.75fr);
        gap: 18px;
        align-items: start;
    }

    .pd-stack {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .pd-card {
        overflow: hidden;
        border: 1px solid var(--pd-line);
        border-radius: 22px;
        background: var(--pd-surface);
        box-shadow: var(--pd-shadow);
    }

    .pd-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 20px 16px;
        border-bottom: 1px solid var(--pd-line);
        background: linear-gradient(180deg,#FFFDF9 0%,#FBF7F2 100%);
    }

    .pd-card-head small {
        display: block;
        color: var(--pd-primary);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .pd-card-head h3 {
        margin: 7px 0 0;
        color: var(--pd-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .pd-card-head p {
        margin: 7px 0 0;
        color: var(--pd-muted);
        font-size: 9px;
        line-height: 1.45;
    }

    .pd-chip {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        padding: 0 9px;
        border: 1px solid #E7C6BC;
        border-radius: 999px;
        background: #F8E9E4;
        color: var(--pd-primary);
        font-size: 7px;
        font-weight: 900;
        white-space: nowrap;
    }

    .pd-detail-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 1px;
        background: var(--pd-line);
    }

    .pd-detail {
        min-width: 0;
        padding: 17px 20px;
        background: var(--pd-surface);
    }

    .pd-label {
        display: block;
        color: var(--pd-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .pd-value {
        display: block;
        margin-top: 7px;
        color: var(--pd-text);
        font-size: 10px;
        font-weight: 900;
        line-height: 1.45;
    }

    .pd-value.is-warning {
        color: var(--pd-warning);
    }

    .pd-seller {
        display: grid;
        gap: 10px;
        padding: 18px 20px 20px;
    }

    .pd-seller-row {
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        gap: 11px;
        padding: 13px;
        border: 1px solid var(--pd-line);
        border-radius: 14px;
        background: var(--pd-soft);
    }

    .pd-seller-icon {
        display: grid;
        width: 38px;
        height: 38px;
        place-items: center;
        border-radius: 11px;
        background: var(--pd-primary-soft);
        color: var(--pd-primary);
    }

    .pd-seller-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .pd-seller-row strong {
        display: block;
        color: var(--pd-text);
        font-size: 10px;
        font-weight: 900;
    }

    .pd-seller-row span {
        display: block;
        margin-top: 4px;
        color: var(--pd-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .pd-process {
        display: grid;
        grid-template-columns: repeat(4,minmax(0,1fr));
        gap: 12px;
        padding: 18px 20px 20px;
    }

    .pd-step {
        position: relative;
        min-height: 150px;
        padding: 14px;
        border: 1px solid var(--pd-line);
        border-radius: 14px;
        background: var(--pd-soft);
    }

    .pd-step:not(:last-child)::after {
        position: absolute;
        top: 31px;
        right: -13px;
        width: 14px;
        height: 1px;
        background: var(--pd-line-strong);
        content: "";
    }

    .pd-step-number {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid var(--pd-line);
        border-radius: 11px;
        background: var(--pd-surface);
        color: var(--pd-muted);
        font-size: 9px;
        font-weight: 950;
    }

    .pd-step.is-current .pd-step-number {
        border-color: var(--pd-primary);
        background: var(--pd-primary);
        color: #FFF;
        box-shadow: 0 8px 18px rgba(102,31,24,.14);
    }

    .pd-step h4 {
        margin: 12px 0 0;
        color: var(--pd-text);
        font-size: 9px;
        font-weight: 950;
        line-height: 1.35;
    }

    .pd-step p {
        margin: 6px 0 0;
        color: var(--pd-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .pd-step small {
        display: inline-flex;
        margin-top: 9px;
        color: var(--pd-muted-light);
        font-size: 7px;
        font-weight: 850;
    }

    .pd-actions {
        display: grid;
        gap: 9px;
        padding: 18px 20px 20px;
    }

    .pd-btn {
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

    .pd-btn:hover {
        transform: translateY(-1px);
    }

    .pd-btn svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .pd-btn-primary {
        border-color: var(--pd-primary);
        background: var(--pd-primary);
        color: #FFF;
        box-shadow: 0 12px 22px rgba(102,31,24,.14);
    }

    .pd-btn-primary:hover {
        border-color: var(--pd-primary-dark);
        background: var(--pd-primary-dark);
    }

    .pd-btn-soft {
        border-color: var(--pd-line-strong);
        background: var(--pd-surface);
        color: var(--pd-primary);
    }

    .pd-btn-soft:hover {
        background: var(--pd-warm);
    }

    .pd-btn-success {
        border-color: var(--pd-success) !important;
        background: var(--pd-success) !important;
        color: #FFF !important;
    }

    .pd-btn[disabled] {
        cursor: default;
        transform: none;
    }

    .pd-next {
        overflow: hidden;
        border-radius: 22px;
        background:
            radial-gradient(circle at 90% 15%, rgba(255,255,255,.12), transparent 28%),
            linear-gradient(135deg,var(--pd-primary) 0%,#74271E 58%,var(--pd-primary-dark) 100%);
        color: #FFF;
        box-shadow: 0 18px 44px rgba(102,31,24,.17);
    }

    .pd-next-body {
        padding: 20px;
    }

    .pd-next small {
        color: rgba(255,255,255,.58);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .pd-next h3 {
        margin: 10px 0 0;
        color: #FFF;
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        line-height: 1.02;
        letter-spacing: -.04em;
    }

    .pd-next p {
        margin: 8px 0 0;
        color: rgba(255,255,255,.66);
        font-size: 9px;
        line-height: 1.5;
    }

    .pd-next-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 15px;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 12px;
        background: rgba(255,255,255,.07);
        color: rgba(255,255,255,.88);
        font-size: 8px;
        font-weight: 850;
    }

    .pd-next-row svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    @media (max-width: 1260px) {
        .pd-stats {
            grid-template-columns: repeat(3,minmax(0,1fr));
        }

        .pd-main {
            grid-template-columns: 1fr;
        }

        .pd-process {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }

        .pd-step::after {
            display: none;
        }
    }

    @media (max-width: 860px) {
        .pd-page-head,
        .pd-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .pd-hero {
            padding: 26px 22px;
        }

        .pd-stats {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }
    }

    @media (max-width: 560px) {
        .pd-stats,
        .pd-detail-grid,
        .pd-process {
            grid-template-columns: 1fr;
        }
    }

    html.dark .pd-page {
        color: #F5EFE8;
    }

    html.dark .pd-hero,
    html.dark .pd-stat,
    html.dark .pd-card {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg,#211B17 0%,#1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .pd-card-head {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .pd-page-head h1,
    html.dark .pd-hero h2,
    html.dark .pd-stat strong,
    html.dark .pd-card-head h3,
    html.dark .pd-value,
    html.dark .pd-seller-row strong,
    html.dark .pd-step h4 {
        color: #F5EFE8 !important;
    }

    html.dark .pd-page-head p,
    html.dark .pd-hero p,
    html.dark .pd-stat label,
    html.dark .pd-card-head p,
    html.dark .pd-label,
    html.dark .pd-seller-row span,
    html.dark .pd-step p,
    html.dark .pd-step small {
        color: #AFA19A !important;
    }

    html.dark .pd-status-pill {
        background: #332817 !important;
        border-color: #5F4B25 !important;
    }

    html.dark .pd-detail-grid {
        background: #30231F !important;
    }

    html.dark .pd-detail {
        background: #1A1412 !important;
    }

    html.dark .pd-seller-row,
    html.dark .pd-step,
    html.dark .pd-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .pd-btn-soft {
        color: #EBA99D !important;
    }

    html.dark .pd-stat-icon,
    html.dark .pd-seller-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .pd-step.is-current .pd-step-number,
    html.dark .pd-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }
</style>

<div class="pd-page">

    <section class="pd-page-head">
        <div>
            <small>Pickup Assignment</small>
            <h1>Pickup Details</h1>
            <p>Review seller and parcel details before collecting the package.</p>
        </div>

        <span class="pd-status-pill" id="pickupStatusBadge">
            {{ $pickup['status'] }}
        </span>
    </section>

    <section class="pd-hero">
        <div>
            <span class="pd-hero-kicker">Pickup Verification</span>

            <h2>Ready for pickup.</h2>

            <p>
                Collect <strong>{{ $pickup['tracking'] }}</strong> from
                <strong>{{ $pickup['seller'] }}</strong> and bring it to the
                LIKHAE sorting center.
            </p>
        </div>
    </section>

    <section class="pd-stats">
        @foreach($summary as $item)
            <article class="pd-stat">
                <div class="pd-stat-top">
                    <span class="pd-stat-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons[$item['icon']] !!}
                        </svg>
                    </span>

                    <span class="pd-stat-badge">
                        {{ $item['badge'] }}
                    </span>
                </div>

                <label>{{ $item['label'] }}</label>
                <strong title="{{ $item['value'] }}">{{ $item['value'] }}</strong>
            </article>
        @endforeach
    </section>

    <section class="pd-main">

        <div class="pd-stack">

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <small>Parcel</small>
                        <h3>Parcel Information</h3>
                        <p>Verify the parcel before accepting the pickup task.</p>
                    </div>

                    <span class="pd-chip">Pickup parcel</span>
                </header>

                <div class="pd-detail-grid">
                    <div class="pd-detail">
                        <span class="pd-label">Tracking Number</span>
                        <strong class="pd-value">{{ $pickup['tracking'] }}</strong>
                    </div>

                    <div class="pd-detail">
                        <span class="pd-label">Parcel Status</span>
                        <strong class="pd-value is-warning" id="parcelStatusText">
                            {{ $pickup['status'] }}
                        </strong>
                    </div>

                    <div class="pd-detail">
                        <span class="pd-label">Items</span>
                        <strong class="pd-value">{{ $pickup['items'] }}</strong>
                    </div>

                    <div class="pd-detail">
                        <span class="pd-label">Payment Type</span>
                        <strong class="pd-value">{{ $pickup['payment_type'] }}</strong>
                    </div>

                    <div class="pd-detail">
                        <span class="pd-label">COD Amount</span>
                        <strong class="pd-value">{{ $pickup['cod_amount'] }}</strong>
                    </div>

                    <div class="pd-detail">
                        <span class="pd-label">Assignment Type</span>
                        <strong class="pd-value">Seller Pickup</strong>
                    </div>
                </div>
            </section>

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <small>Seller</small>
                        <h3>Seller Information</h3>
                        <p>Pickup location and seller contact information.</p>
                    </div>
                </header>

                <div class="pd-seller">
                    <div class="pd-seller-row">
                        <span class="pd-seller-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['store'] !!}
                            </svg>
                        </span>

                        <div>
                            <strong>{{ $pickup['seller'] }}</strong>
                            <span>Store Name</span>
                        </div>
                    </div>

                    <div class="pd-seller-row">
                        <span class="pd-seller-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['location'] !!}
                            </svg>
                        </span>

                        <div>
                            <strong>{{ $pickup['address'] }}</strong>
                            <span>Pickup Address</span>
                        </div>
                    </div>

                    <div class="pd-seller-row">
                        <span class="pd-seller-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['phone'] !!}
                            </svg>
                        </span>

                        <div>
                            <strong>{{ $pickup['contact'] }}</strong>
                            <span>Seller Contact</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <small>Workflow</small>
                        <h3>Pickup Process</h3>
                        <p>Follow the pickup flow from assignment acceptance to sorting center handoff.</p>
                    </div>

                    <span class="pd-chip">Step 1 of 4</span>
                </header>

                <div class="pd-process">
                    @foreach($process as $index => $step)
                        <article class="pd-step {{ $index === 0 ? 'is-current' : '' }}">
                            <span class="pd-step-number">
                                {{ $index + 1 }}
                            </span>

                            <h4>{{ $step['title'] }}</h4>
                            <p>{{ $step['description'] }}</p>
                            <small>{{ $step['status'] }}</small>
                        </article>
                    @endforeach
                </div>
            </section>

        </div>

        <aside class="pd-stack">

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <small>Actions</small>
                        <h3>Pickup Actions</h3>
                        <p>Continue the current pickup assignment.</p>
                    </div>
                </header>

                <div class="pd-actions">
                    <button
                        id="acceptPickupBtn"
                        type="button"
                        class="pd-btn pd-btn-primary"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['check'] !!}
                        </svg>

                        Accept Pickup
                    </button>

                    <button
                        id="openNavBtn"
                        type="button"
                        class="pd-btn pd-btn-soft"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['navigation'] !!}
                        </svg>

                        Open Navigation
                    </button>

                    <a
                        href="{{ route('rider.pickups.scan') }}"
                        class="pd-btn pd-btn-soft"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['scan'] !!}
                        </svg>

                        Scan Parcel
                    </a>
                </div>
            </section>

            <section class="pd-next">
                <div class="pd-next-body">
                    <small>Next Step</small>

                    <h3>Bring Parcel To Sorting Center</h3>

                    <p>
                        After successful pickup, the parcel status changes to PICKED_UP.
                    </p>

                    <div class="pd-next-row">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['arrow'] !!}
                        </svg>

                        Pickup confirmed → sorting center
                    </div>
                </div>
            </section>

        </aside>

    </section>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const acceptButton =
        document.getElementById('acceptPickupBtn');

    const navigationButton =
        document.getElementById('openNavBtn');

    const statusBadge =
        document.getElementById('pickupStatusBadge');

    const parcelStatus =
        document.getElementById('parcelStatusText');


    acceptButton?.addEventListener(
        'click',
        function () {
            acceptButton.textContent =
                'Pickup Accepted!';

            acceptButton.disabled =
                true;

            acceptButton.classList.remove(
                'pd-btn-primary'
            );

            acceptButton.classList.add(
                'pd-btn-success'
            );

            if (statusBadge) {
                statusBadge.textContent =
                    'ACCEPTED';

                statusBadge.style.borderColor =
                    'var(--pd-success)';

                statusBadge.style.background =
                    'var(--pd-success-soft)';

                statusBadge.style.color =
                    'var(--pd-success)';
            }

            if (parcelStatus) {
                parcelStatus.textContent =
                    'ACCEPTED';

                parcelStatus.style.color =
                    'var(--pd-success)';
            }
        }
    );


    navigationButton?.addEventListener(
        'click',
        function () {
            window.open(
                'https://www.google.com/maps/search/?api=1&query=123+Rizal+Street+Calamba+Laguna',
                '_blank'
            );
        }
    );
});
</script>
@endpush
