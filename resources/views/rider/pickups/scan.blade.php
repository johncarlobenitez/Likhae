@extends('rider.app')

@section('title', 'Scan Parcel — LIKHAE Rider')

@php
    $parcel = [
        'tracking' => 'LH-2026-1001',
        'seller' => 'ABC Handmade Store',
        'status' => 'READY_FOR_PICKUP',
        'next_status' => 'PICKED_UP',
        'final_status' => 'AT_SORTING_CENTER',
        'location' => 'Calamba, Laguna',
        'items' => '3 Items',
    ];

    $summary = [
        [
            'label' => 'Tracking',
            'value' => $parcel['tracking'],
            'badge' => 'Active',
            'icon' => 'parcel',
        ],
        [
            'label' => 'Seller',
            'value' => $parcel['seller'],
            'badge' => 'Verified',
            'icon' => 'store',
        ],
        [
            'label' => 'Pickup Status',
            'value' => 'Ready',
            'badge' => 'Pending',
            'icon' => 'clock',
        ],
        [
            'label' => 'Parcel Items',
            'value' => $parcel['items'],
            'badge' => 'Expected',
            'icon' => 'items',
        ],
        [
            'label' => 'Destination',
            'value' => 'Sorting Center',
            'badge' => 'Next',
            'icon' => 'location',
        ],
    ];

    $icons = [
        'scan' => '
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <path d="M7 7h.01"/>
            <path d="M7 12h.01"/>
            <path d="M7 17h.01"/>
            <path d="M11 7h6"/>
            <path d="M11 12h6"/>
            <path d="M11 17h6"/>
        ',
        'parcel' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',
        'store' => '
            <path d="M4 10v10h16V10"/>
            <path d="M3 7h18l-2-4H5z"/>
            <path d="M8 14h3v6"/>
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
        'location' => '
            <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"/>
            <circle cx="12" cy="10" r="2"/>
        ',
        'camera' => '
            <path d="M4 7h3l2-3h6l2 3h3v12H4z"/>
            <circle cx="12" cy="13" r="4"/>
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
        --sp-page: #FBF7F2;
        --sp-surface: #FFFDF9;
        --sp-soft: #F7F0E8;
        --sp-warm: #F1E2DA;
        --sp-line: #E8D8C8;
        --sp-line-strong: #DCC7B7;

        --sp-primary: #661F18;
        --sp-primary-dark: #4D1712;
        --sp-primary-soft: #F1E1DB;

        --sp-text: #3A211B;
        --sp-text-strong: #21110D;
        --sp-muted: #987865;
        --sp-muted-light: #B09A8A;

        --sp-success: #256F4A;
        --sp-success-soft: #EAF7EF;
        --sp-warning: #9A5B11;
        --sp-warning-soft: #FFF4D9;

        --sp-shadow: 0 12px 34px rgba(74,35,27,.06);
        --sp-shadow-lg: 0 24px 60px rgba(74,35,27,.10);
    }

    .sp-page {
        display: grid;
        gap: 24px;
        width: 100%;
        color: var(--sp-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .sp-page * {
        box-sizing: border-box;
    }

    .sp-page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
    }

    .sp-page-head small {
        display: block;
        color: var(--sp-primary);
        font-size: 9px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .sp-page-head h1 {
        margin: 7px 0 0;
        color: var(--sp-text);
        font-size: 22px;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .sp-page-head p {
        margin: 6px 0 0;
        color: var(--sp-muted);
        font-size: 9px;
    }

    .sp-status-pill {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        gap: 8px;
        padding: 0 13px;
        border: 1px solid #EACF8C;
        border-radius: 13px;
        background: var(--sp-warning-soft);
        color: var(--sp-warning);
        font-size: 8px;
        font-weight: 950;
        box-shadow: var(--sp-shadow);
    }

    .sp-status-pill::before {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .sp-hero {
        display: flex;
        min-height: 190px;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 28px 30px;
        border: 1px solid var(--sp-line);
        border-radius: 28px;
        background:
            radial-gradient(circle at 90% 18%, rgba(194,151,113,.24), transparent 28%),
            linear-gradient(120deg, #FBF5EE 0%, #F4EBE3 55%, #EAD9C8 100%);
        box-shadow: var(--sp-shadow);
    }

    .sp-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--sp-primary);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .sp-hero-kicker::before {
        width: 20px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .sp-hero h2 {
        margin: 10px 0 0;
        color: var(--sp-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px,5vw,66px);
        font-weight: 400;
        line-height: .96;
        letter-spacing: -.055em;
    }

    .sp-hero p {
        max-width: 700px;
        margin: 12px 0 0;
        color: var(--sp-muted);
        font-size: 10px;
        line-height: 1.65;
    }

    .sp-hero strong {
        color: var(--sp-primary);
    }

    .sp-stats {
        display: grid;
        grid-template-columns: repeat(5,minmax(0,1fr));
        gap: 14px;
    }

    .sp-stat {
        min-height: 126px;
        padding: 15px;
        border: 1px solid var(--sp-line);
        border-radius: 18px;
        background: rgba(255,253,249,.95);
        box-shadow: var(--sp-shadow);
    }

    .sp-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
    }

    .sp-stat-icon {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid #E4C8BF;
        border-radius: 11px;
        background: var(--sp-primary-soft);
        color: var(--sp-primary);
    }

    .sp-stat-icon svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sp-stat-badge {
        display: inline-flex;
        min-height: 23px;
        align-items: center;
        padding: 0 7px;
        border: 1px solid #E7C6BC;
        border-radius: 999px;
        background: #F8E9E4;
        color: var(--sp-primary);
        font-size: 7px;
        font-weight: 900;
        white-space: nowrap;
    }

    .sp-stat label {
        display: block;
        margin-top: 14px;
        color: var(--sp-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sp-stat strong {
        display: block;
        margin-top: 5px;
        overflow: hidden;
        color: var(--sp-text-strong);
        font-size: 20px;
        font-weight: 950;
        line-height: 1.1;
        letter-spacing: -.04em;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sp-main {
        display: grid;
        grid-template-columns: minmax(0,1.6fr) minmax(310px,.75fr);
        gap: 18px;
        align-items: start;
    }

    .sp-stack {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .sp-card {
        overflow: hidden;
        border: 1px solid var(--sp-line);
        border-radius: 22px;
        background: var(--sp-surface);
        box-shadow: var(--sp-shadow);
    }

    .sp-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 20px 16px;
        border-bottom: 1px solid var(--sp-line);
        background: linear-gradient(180deg,#FFFDF9 0%,#FBF7F2 100%);
    }

    .sp-card-head small {
        display: block;
        color: var(--sp-primary);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .sp-card-head h3 {
        margin: 7px 0 0;
        color: var(--sp-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .sp-card-head p {
        margin: 7px 0 0;
        color: var(--sp-muted);
        font-size: 9px;
        line-height: 1.45;
    }

    .sp-chip {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        padding: 0 9px;
        border: 1px solid #E7C6BC;
        border-radius: 999px;
        background: #F8E9E4;
        color: var(--sp-primary);
        font-size: 7px;
        font-weight: 900;
        white-space: nowrap;
    }

    .sp-scanner-wrap {
        padding: 18px 20px 20px;
    }

    .sp-scanner {
        position: relative;
        display: grid;
        min-height: 360px;
        place-items: center;
        overflow: hidden;
        border: 1px dashed var(--sp-line-strong);
        border-radius: 18px;
        background:
            linear-gradient(rgba(102,31,24,.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(102,31,24,.03) 1px, transparent 1px),
            radial-gradient(circle at 50% 50%, rgba(102,31,24,.06), transparent 30%),
            linear-gradient(135deg,#FCF8F4 0%,#F3E9DF 100%);
        background-size: 28px 28px, 28px 28px, auto, auto;
    }

    .sp-scan-frame {
        position: relative;
        width: min(240px,72%);
        aspect-ratio: 1;
    }

    .sp-scan-corner {
        position: absolute;
        width: 46px;
        height: 46px;
        border-color: var(--sp-primary);
    }

    .sp-scan-corner.is-tl {
        top: 0;
        left: 0;
        border-top: 3px solid;
        border-left: 3px solid;
        border-radius: 13px 0 0 0;
    }

    .sp-scan-corner.is-tr {
        top: 0;
        right: 0;
        border-top: 3px solid;
        border-right: 3px solid;
        border-radius: 0 13px 0 0;
    }

    .sp-scan-corner.is-bl {
        bottom: 0;
        left: 0;
        border-bottom: 3px solid;
        border-left: 3px solid;
        border-radius: 0 0 0 13px;
    }

    .sp-scan-corner.is-br {
        right: 0;
        bottom: 0;
        border-right: 3px solid;
        border-bottom: 3px solid;
        border-radius: 0 0 13px 0;
    }

    .sp-scan-line {
        position: absolute;
        top: 18%;
        right: 12px;
        left: 12px;
        height: 2px;
        border-radius: 999px;
        background: var(--sp-primary);
        box-shadow: 0 0 18px rgba(102,31,24,.30);
        opacity: .75;
    }

    .sp-scanner.is-active .sp-scan-line {
        animation: spScan 1.4s ease-in-out infinite alternate;
    }

    @keyframes spScan {
        from { top: 18%; }
        to { top: 78%; }
    }

    .sp-scanner-center {
        position: absolute;
        top: 50%;
        left: 50%;
        width: min(300px,calc(100% - 42px));
        padding: 14px;
        border: 1px solid rgba(255,255,255,.88);
        border-radius: 15px;
        background: rgba(255,253,249,.93);
        text-align: center;
        box-shadow: var(--sp-shadow-lg);
        backdrop-filter: blur(12px);
        transform: translate(-50%,-50%);
    }

    .sp-scanner-center svg {
        width: 27px;
        height: 27px;
        fill: none;
        stroke: var(--sp-primary);
        stroke-width: 1.6;
    }

    .sp-scanner-center strong {
        display: block;
        margin-top: 9px;
        color: var(--sp-text);
        font-size: 10px;
        font-weight: 950;
    }

    .sp-scanner-center span {
        display: block;
        margin-top: 5px;
        color: var(--sp-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .sp-actions {
        margin-top: 14px;
    }

    .sp-btn {
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

    .sp-btn:hover {
        transform: translateY(-1px);
    }

    .sp-btn svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sp-btn-primary {
        border-color: var(--sp-primary);
        background: var(--sp-primary);
        color: #FFF;
        box-shadow: 0 12px 22px rgba(102,31,24,.14);
    }

    .sp-btn-primary:hover {
        border-color: var(--sp-primary-dark);
        background: var(--sp-primary-dark);
    }

    .sp-btn-soft {
        border-color: var(--sp-line-strong);
        background: var(--sp-surface);
        color: var(--sp-primary);
    }

    .sp-btn-soft:hover {
        background: var(--sp-warm);
    }

    .sp-btn-success {
        border-color: var(--sp-success) !important;
        background: var(--sp-success) !important;
        color: #FFF !important;
    }

    .sp-btn[disabled] {
        cursor: default;
        transform: none;
    }

    .sp-actions .sp-btn {
        width: 100%;
    }

    .sp-info {
        display: grid;
        gap: 9px;
        padding: 18px 20px 20px;
    }

    .sp-info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 12px 13px;
        border: 1px solid var(--sp-line);
        border-radius: 13px;
        background: var(--sp-soft);
    }

    .sp-info-row span {
        color: var(--sp-muted);
        font-size: 8px;
    }

    .sp-info-row strong {
        color: var(--sp-text);
        font-size: 9px;
        font-weight: 900;
        text-align: right;
    }

    .sp-info-row.is-warning {
        border-color: #EACF8C;
        background: var(--sp-warning-soft);
    }

    .sp-info-row.is-warning span,
    .sp-info-row.is-warning strong {
        color: var(--sp-warning);
    }

    .sp-after-scan {
        overflow: hidden;
        border-radius: 22px;
        background:
            radial-gradient(circle at 90% 15%, rgba(255,255,255,.12), transparent 28%),
            linear-gradient(135deg,var(--sp-primary) 0%,#74271E 58%,var(--sp-primary-dark) 100%);
        color: #FFF;
        box-shadow: 0 18px 44px rgba(102,31,24,.17);
    }

    .sp-after-scan-body {
        padding: 20px;
    }

    .sp-after-scan small {
        color: rgba(255,255,255,.58);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .sp-after-scan h3 {
        margin: 10px 0 0;
        color: #FFF;
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        letter-spacing: -.04em;
    }

    .sp-after-scan p {
        margin: 8px 0 0;
        color: rgba(255,255,255,.66);
        font-size: 9px;
        line-height: 1.5;
    }

    .sp-status-flow {
        display: grid;
        gap: 8px;
        margin-top: 15px;
    }

    .sp-status-flow-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 12px;
        background: rgba(255,255,255,.07);
        color: rgba(255,255,255,.88);
        font-size: 8px;
        font-weight: 850;
    }

    .sp-status-flow-row svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
    }

    .sp-confirm {
        display: grid;
        grid-template-columns: minmax(0,1fr) auto;
        gap: 18px;
        align-items: center;
        padding: 18px 20px;
    }

    .sp-confirm h3 {
        margin: 0;
        color: var(--sp-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 27px;
        font-weight: 400;
        letter-spacing: -.04em;
    }

    .sp-confirm p {
        margin: 7px 0 0;
        color: var(--sp-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .sp-confirm-actions {
        display: flex;
        gap: 8px;
    }

    @media (max-width: 1260px) {
        .sp-stats {
            grid-template-columns: repeat(3,minmax(0,1fr));
        }

        .sp-main {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 860px) {
        .sp-page-head,
        .sp-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .sp-hero {
            padding: 26px 22px;
        }

        .sp-stats {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }

        .sp-confirm {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .sp-stats {
            grid-template-columns: 1fr;
        }

        .sp-confirm-actions {
            flex-direction: column;
        }

        .sp-confirm-actions .sp-btn {
            width: 100%;
        }
    }

    html.dark .sp-page {
        color: #F5EFE8;
    }

    html.dark .sp-hero,
    html.dark .sp-stat,
    html.dark .sp-card {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg,#211B17 0%,#1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .sp-card-head {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .sp-page-head h1,
    html.dark .sp-hero h2,
    html.dark .sp-stat strong,
    html.dark .sp-card-head h3,
    html.dark .sp-scanner-center strong,
    html.dark .sp-info-row strong,
    html.dark .sp-confirm h3 {
        color: #F5EFE8 !important;
    }

    html.dark .sp-page-head p,
    html.dark .sp-hero p,
    html.dark .sp-stat label,
    html.dark .sp-card-head p,
    html.dark .sp-scanner-center span,
    html.dark .sp-info-row span,
    html.dark .sp-confirm p {
        color: #AFA19A !important;
    }

    html.dark .sp-status-pill {
        background: #332817 !important;
        border-color: #5F4B25 !important;
    }

    html.dark .sp-scanner {
        border-color: #3B2E27 !important;
        background:
            linear-gradient(rgba(235,169,157,.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(235,169,157,.035) 1px, transparent 1px),
            radial-gradient(circle at 50% 50%, rgba(235,169,157,.04), transparent 30%),
            #171210 !important;
        background-size: 28px 28px, 28px 28px, auto, auto !important;
    }

    html.dark .sp-scanner-center,
    html.dark .sp-info-row,
    html.dark .sp-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .sp-btn-soft {
        color: #EBA99D !important;
    }

    html.dark .sp-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }
</style>

<div class="sp-page">

    <section class="sp-page-head">
        <div>
            <small>Parcel Verification</small>
            <h1>Scan Parcel</h1>
            <p>Verify the parcel before confirming the seller handoff.</p>
        </div>

        <span class="sp-status-pill">
            {{ $parcel['status'] }}
        </span>
    </section>

    <section class="sp-hero">
        <div>
            <span class="sp-hero-kicker">Pickup Verification</span>

            <h2>Scan before pickup.</h2>

            <p>
                Confirm that <strong>{{ $parcel['tracking'] }}</strong> matches the parcel
                handed over by <strong>{{ $parcel['seller'] }}</strong>.
            </p>
        </div>
    </section>

    <section class="sp-stats">
        @foreach($summary as $item)
            <article class="sp-stat">
                <div class="sp-stat-top">
                    <span class="sp-stat-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons[$item['icon']] !!}
                        </svg>
                    </span>

                    <span class="sp-stat-badge">
                        {{ $item['badge'] }}
                    </span>
                </div>

                <label>{{ $item['label'] }}</label>
                <strong title="{{ $item['value'] }}">{{ $item['value'] }}</strong>
            </article>
        @endforeach
    </section>

    <section class="sp-main">

        <div class="sp-stack">

            <section class="sp-card">
                <header class="sp-card-head">
                    <div>
                        <small>Scanner</small>
                        <h3>Barcode Scanner</h3>
                        <p>Scan the parcel QR code or barcode before confirming pickup.</p>
                    </div>

                    <span class="sp-chip" id="scannerStateChip">
                        Waiting
                    </span>
                </header>

                <div class="sp-scanner-wrap">
                    <div class="sp-scanner" id="scannerArea">
                        <div class="sp-scan-frame" aria-hidden="true">
                            <span class="sp-scan-corner is-tl"></span>
                            <span class="sp-scan-corner is-tr"></span>
                            <span class="sp-scan-corner is-bl"></span>
                            <span class="sp-scan-corner is-br"></span>
                            <span class="sp-scan-line"></span>
                        </div>

                        <div class="sp-scanner-center">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['camera'] !!}
                            </svg>

                            <strong id="scannerTitle">
                                Camera Scanner
                            </strong>

                            <span id="scannerText">
                                Point your camera at the parcel barcode.
                            </span>
                        </div>
                    </div>

                    <div class="sp-actions">
                        <button
                            type="button"
                            id="startScannerBtn"
                            class="sp-btn sp-btn-primary"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['scan'] !!}
                            </svg>

                            Start Scanner
                        </button>
                    </div>
                </div>
            </section>

        </div>

        <aside class="sp-stack">

            <section class="sp-card">
                <header class="sp-card-head">
                    <div>
                        <small>Parcel</small>
                        <h3>Parcel Information</h3>
                        <p>Reference details for this pickup.</p>
                    </div>
                </header>

                <div class="sp-info">
                    <div class="sp-info-row">
                        <span>Tracking Number</span>
                        <strong>{{ $parcel['tracking'] }}</strong>
                    </div>

                    <div class="sp-info-row">
                        <span>Seller</span>
                        <strong>{{ $parcel['seller'] }}</strong>
                    </div>

                    <div class="sp-info-row">
                        <span>Location</span>
                        <strong>{{ $parcel['location'] }}</strong>
                    </div>

                    <div class="sp-info-row">
                        <span>Parcel Items</span>
                        <strong>{{ $parcel['items'] }}</strong>
                    </div>

                    <div class="sp-info-row is-warning">
                        <span>Current Status</span>
                        <strong>{{ $parcel['status'] }}</strong>
                    </div>
                </div>
            </section>

            <section class="sp-after-scan">
                <div class="sp-after-scan-body">
                    <small>After Scan</small>

                    <h3>Status flow</h3>

                    <p>
                        The parcel will move through the next pickup statuses after verification.
                    </p>

                    <div class="sp-status-flow">
                        <div class="sp-status-flow-row">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['check'] !!}
                            </svg>

                            {{ $parcel['next_status'] }}
                        </div>

                        <div class="sp-status-flow-row">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['check'] !!}
                            </svg>

                            {{ $parcel['final_status'] }}
                        </div>
                    </div>
                </div>
            </section>

        </aside>

    </section>

    <section class="sp-card">
        <div class="sp-confirm">
            <div>
                <h3>Confirm Pickup</h3>

                <p>
                    Verify that the seller successfully handed over the correct parcel.
                </p>
            </div>

            <div class="sp-confirm-actions">
                <a
                    href="{{ route('rider.pickups') }}"
                    class="sp-btn sp-btn-soft"
                >
                    Cancel
                </a>

                <button
                    type="button"
                    id="confirmPickupBtn"
                    class="sp-btn sp-btn-success"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        {!! $icons['check'] !!}
                    </svg>

                    Confirm Parcel Pickup
                </button>
            </div>
        </div>
    </section>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const scannerButton =
        document.getElementById('startScannerBtn');

    const scannerArea =
        document.getElementById('scannerArea');

    const scannerChip =
        document.getElementById('scannerStateChip');

    const scannerTitle =
        document.getElementById('scannerTitle');

    const scannerText =
        document.getElementById('scannerText');

    const confirmButton =
        document.getElementById('confirmPickupBtn');


    scannerButton?.addEventListener(
        'click',
        function () {
            scannerButton.textContent =
                'Scanner Ready';

            scannerButton.disabled =
                true;

            scannerButton.classList.remove(
                'sp-btn-primary'
            );

            scannerButton.classList.add(
                'sp-btn-success'
            );

            scannerArea?.classList.add(
                'is-active'
            );

            if (scannerChip) {
                scannerChip.textContent =
                    'Scanner Ready';
            }

            if (scannerTitle) {
                scannerTitle.textContent =
                    'Scanner Active';
            }

            if (scannerText) {
                scannerText.textContent =
                    'Hold the parcel barcode inside the scan frame.';
            }
        }
    );


    confirmButton?.addEventListener(
        'click',
        function () {
            confirmButton.textContent =
                'Pickup Confirmed ✓';

            confirmButton.disabled =
                true;

            window.setTimeout(
                function () {
                    window.location.href =
                        '{{ route('rider.pickups') }}';
                },
                500
            );
        }
    );
});
</script>
@endpush
