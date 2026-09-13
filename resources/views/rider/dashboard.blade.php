@extends('rider.app')

@section('title', 'Delivery Tracking — LIKHAE Rider')

@php
    $delivery = [
        'tracking' => 'LH-2026-1007',
        'status' => 'OUT_FOR_DELIVERY',
        'customer' => 'Juan Dela Cruz',
        'contact' => '0917 555 1234',
        'address' => '123 Main Street, Los Baños, Laguna',
        'cod' => '₱1,200',
    ];

    $timeline = [
        [
            'title' => 'Assigned To Rider',
            'status' => 'Completed',
            'description' => 'Parcel assignment confirmed for this rider.',
        ],
        [
            'title' => 'Picked Up From Sorting Center',
            'status' => 'Completed',
            'description' => 'Parcel was collected from the sorting center.',
        ],
        [
            'title' => 'Out For Delivery',
            'status' => 'Current',
            'description' => 'Parcel is currently heading to the customer.',
        ],
        [
            'title' => 'Customer Received Parcel',
            'status' => 'Pending',
            'description' => 'Waiting for successful customer handoff.',
        ],
        [
            'title' => 'Completed',
            'status' => 'Pending',
            'description' => 'Delivery closes after final confirmation.',
        ],
    ];

    $summary = [
        [
            'label' => 'Current Status',
            'value' => 'On Route',
            'badge' => 'Live',
            'icon' => 'truck',
        ],
        [
            'label' => 'Tracking',
            'value' => $delivery['tracking'],
            'badge' => 'Active',
            'icon' => 'parcel',
        ],
        [
            'label' => 'Customer',
            'value' => $delivery['customer'],
            'badge' => 'Recipient',
            'icon' => 'user',
        ],
        [
            'label' => 'COD Amount',
            'value' => $delivery['cod'],
            'badge' => 'Collect',
            'icon' => 'wallet',
        ],
        [
            'label' => 'Progress',
            'value' => '3 / 5',
            'badge' => '60%',
            'icon' => 'check',
        ],
    ];

    $icons = [
        'truck' => '
            <path d="M3 6h11v11H3z"/>
            <path d="M14 10h4l3 3v4h-7z"/>
            <circle cx="7" cy="19" r="2"/>
            <circle cx="18" cy="19" r="2"/>
        ',
        'location' => '
            <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"/>
            <circle cx="12" cy="10" r="2"/>
        ',
        'navigation' => '
            <path d="m3 11 18-8-8 18-2-8z"/>
        ',
        'user' => '
            <circle cx="12" cy="8" r="4"/>
            <path d="M4 21c0-5 3-8 8-8s8 3 8 8"/>
        ',
        'phone' => '
            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1A19.5 19.5 0 0 1 5.2 12 19.8 19.8 0 0 1 2.1 3.3 2 2 0 0 1 4.1 1h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 9a16 16 0 0 0 7 7l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2Z"/>
        ',
        'check' => '
            <path d="m5 12 4 4 10-10"/>
        ',
        'x' => '
            <path d="m7 7 10 10"/>
            <path d="m17 7-10 10"/>
        ',
        'parcel' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',
        'wallet' => '
            <path d="M4 6h15a2 2 0 0 1 2 2v10H4a2 2 0 0 1-2-2V7a3 3 0 0 1 3-3h12"/>
            <path d="M16 11h5v4h-5a2 2 0 0 1 0-4Z"/>
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
        --dt2-page: #FBF7F2;
        --dt2-surface: #FFFDF9;
        --dt2-soft: #F7F0E8;
        --dt2-soft-2: #F3E7DD;
        --dt2-line: #E8D8C8;
        --dt2-line-strong: #DCC7B7;

        --dt2-primary: #661F18;
        --dt2-primary-dark: #4D1712;
        --dt2-primary-soft: #F1E1DB;

        --dt2-text: #3A211B;
        --dt2-text-strong: #21110D;
        --dt2-muted: #987865;
        --dt2-muted-light: #B09A8A;

        --dt2-success: #256F4A;
        --dt2-success-soft: #EAF7EF;
        --dt2-warning: #9A5B11;
        --dt2-warning-soft: #FFF4D9;
        --dt2-danger: #B42318;
        --dt2-danger-soft: #FCEBE9;

        --dt2-shadow: 0 12px 34px rgba(74, 35, 27, .06);
        --dt2-shadow-lg: 0 24px 60px rgba(74, 35, 27, .10);
    }

    .dt2-page {
        display: grid;
        gap: 24px;
        width: 100%;
        color: var(--dt2-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .dt2-page * {
        box-sizing: border-box;
    }

    .dt2-page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
    }

    .dt2-page-head small {
        display: block;
        color: var(--dt2-primary);
        font-size: 9px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .dt2-page-head h1 {
        margin: 7px 0 0;
        color: var(--dt2-text);
        font-size: 22px;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .dt2-page-head p {
        margin: 6px 0 0;
        color: var(--dt2-muted);
        font-size: 9px;
    }

    .dt2-live-pill {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        gap: 8px;
        padding: 0 13px;
        border: 1px solid #E2C5BA;
        border-radius: 13px;
        background: var(--dt2-surface);
        color: var(--dt2-primary);
        font-size: 8px;
        font-weight: 950;
        box-shadow: var(--dt2-shadow);
    }

    .dt2-live-pill svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .dt2-hero {
        display: flex;
        min-height: 190px;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 28px 30px;
        border: 1px solid var(--dt2-line);
        border-radius: 28px;
        background:
            radial-gradient(circle at 90% 18%, rgba(194,151,113,.24), transparent 28%),
            linear-gradient(120deg, #FBF5EE 0%, #F4EBE3 55%, #EAD9C8 100%);
        box-shadow: var(--dt2-shadow);
    }

    .dt2-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--dt2-primary);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .dt2-hero-kicker::before {
        width: 20px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .dt2-hero h2 {
        margin: 10px 0 0;
        color: var(--dt2-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px,5vw,66px);
        font-weight: 400;
        line-height: .96;
        letter-spacing: -.055em;
    }

    .dt2-hero p {
        max-width: 690px;
        margin: 12px 0 0;
        color: var(--dt2-muted);
        font-size: 10px;
        line-height: 1.65;
    }

    .dt2-hero strong {
        color: var(--dt2-primary);
    }

    .dt2-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .dt2-btn {
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

    .dt2-btn:hover {
        transform: translateY(-1px);
    }

    .dt2-btn svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .dt2-btn-primary {
        border-color: var(--dt2-primary);
        background: var(--dt2-primary);
        color: #FFF;
        box-shadow: 0 12px 22px rgba(102,31,24,.14);
    }

    .dt2-btn-primary:hover {
        border-color: var(--dt2-primary-dark);
        background: var(--dt2-primary-dark);
    }

    .dt2-btn-soft {
        border-color: var(--dt2-line-strong);
        background: rgba(255,253,249,.82);
        color: var(--dt2-primary);
    }

    .dt2-btn-soft:hover {
        background: var(--dt2-surface);
    }

    .dt2-btn-success {
        border-color: var(--dt2-success);
        background: var(--dt2-success);
        color: #FFF;
    }

    .dt2-btn-danger {
        border-color: #D9A29C;
        background: var(--dt2-surface);
        color: var(--dt2-danger);
    }

    .dt2-btn.is-state-success {
        border-color: #CFE8DA !important;
        background: var(--dt2-success-soft) !important;
        color: var(--dt2-success) !important;
    }

    .dt2-btn.is-state-warning {
        border-color: #EACF8C !important;
        background: var(--dt2-warning-soft) !important;
        color: var(--dt2-warning) !important;
    }

    .dt2-btn[disabled] {
        cursor: default;
        transform: none;
    }

    .dt2-stats {
        display: grid;
        grid-template-columns: repeat(5,minmax(0,1fr));
        gap: 14px;
    }

    .dt2-stat {
        min-height: 126px;
        padding: 15px;
        border: 1px solid var(--dt2-line);
        border-radius: 18px;
        background: rgba(255,253,249,.95);
        box-shadow: var(--dt2-shadow);
    }

    .dt2-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
    }

    .dt2-stat-icon {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid #E4C8BF;
        border-radius: 11px;
        background: var(--dt2-primary-soft);
        color: var(--dt2-primary);
    }

    .dt2-stat-icon svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .dt2-stat-badge {
        display: inline-flex;
        min-height: 23px;
        align-items: center;
        padding: 0 7px;
        border: 1px solid #E7C6BC;
        border-radius: 999px;
        background: #F8E9E4;
        color: var(--dt2-primary);
        font-size: 7px;
        font-weight: 900;
        white-space: nowrap;
    }

    .dt2-stat label {
        display: block;
        margin-top: 14px;
        color: var(--dt2-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dt2-stat strong {
        display: block;
        margin-top: 5px;
        overflow: hidden;
        color: var(--dt2-text-strong);
        font-size: 20px;
        font-weight: 950;
        line-height: 1.12;
        letter-spacing: -.04em;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dt2-main {
        display: grid;
        grid-template-columns: minmax(0,1.6fr) minmax(310px,.75fr);
        gap: 18px;
        align-items: start;
    }

    .dt2-stack {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .dt2-card {
        overflow: hidden;
        border: 1px solid var(--dt2-line);
        border-radius: 22px;
        background: var(--dt2-surface);
        box-shadow: var(--dt2-shadow);
    }

    .dt2-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 20px 16px;
        border-bottom: 1px solid var(--dt2-line);
        background: linear-gradient(180deg,#FFFDF9 0%,#FBF7F2 100%);
    }

    .dt2-card-head small {
        display: block;
        color: var(--dt2-primary);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .dt2-card-head h3 {
        margin: 7px 0 0;
        color: var(--dt2-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .dt2-card-head p {
        margin: 7px 0 0;
        color: var(--dt2-muted);
        font-size: 9px;
        line-height: 1.45;
    }

    .dt2-card-chip {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        padding: 0 9px;
        border: 1px solid #E7C6BC;
        border-radius: 999px;
        background: #F8E9E4;
        color: var(--dt2-primary);
        font-size: 7px;
        font-weight: 900;
    }

    .dt2-map-wrap {
        padding: 18px 20px 20px;
    }

    .dt2-map {
        position: relative;
        min-height: 360px;
        overflow: hidden;
        border: 1px solid var(--dt2-line);
        border-radius: 16px;
        background:
            linear-gradient(rgba(102,31,24,.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(102,31,24,.035) 1px, transparent 1px),
            radial-gradient(circle at 24% 34%, rgba(196,154,119,.18), transparent 25%),
            linear-gradient(135deg,#FCF8F4 0%,#F3E9DF 100%);
        background-size: 32px 32px, 32px 32px, auto, auto;
    }

    .dt2-map-route {
        position: absolute;
        top: 47%;
        left: 13%;
        width: 72%;
        height: 100px;
        border-top: 3px dashed rgba(102,31,24,.38);
        border-radius: 50%;
        transform: rotate(-10deg);
    }

    .dt2-map-point {
        position: absolute;
        display: grid;
        width: 48px;
        height: 48px;
        place-items: center;
        border-radius: 999px;
        box-shadow: 0 12px 28px rgba(74,35,27,.15);
    }

    .dt2-map-point svg {
        width: 20px;
        height: 20px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .dt2-map-point.is-origin {
        bottom: 21%;
        left: 12%;
        border: 1px solid var(--dt2-line);
        background: var(--dt2-surface);
        color: var(--dt2-primary);
    }

    .dt2-map-point.is-destination {
        top: 19%;
        right: 12%;
        background: var(--dt2-primary);
        color: #FFF;
    }

    .dt2-map-info {
        position: absolute;
        top: 50%;
        left: 50%;
        width: min(335px,calc(100% - 38px));
        padding: 15px 16px;
        border: 1px solid rgba(255,255,255,.88);
        border-radius: 14px;
        background: rgba(255,253,249,.93);
        text-align: center;
        box-shadow: var(--dt2-shadow-lg);
        backdrop-filter: blur(12px);
        transform: translate(-50%,-50%);
    }

    .dt2-map-info small {
        display: block;
        color: var(--dt2-primary);
        font-size: 7px;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .dt2-map-info strong {
        display: block;
        margin-top: 6px;
        color: var(--dt2-text);
        font-size: 11px;
        font-weight: 950;
    }

    .dt2-map-info span {
        display: block;
        margin-top: 5px;
        color: var(--dt2-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .dt2-map-actions {
        margin-top: 14px;
    }

    .dt2-map-actions .dt2-btn {
        width: 100%;
    }

    .dt2-customer {
        padding: 18px 20px 20px;
    }

    .dt2-customer-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        border: 1px solid var(--dt2-line);
        border-radius: 14px;
        background: var(--dt2-soft);
    }

    .dt2-avatar {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;
        border-radius: 12px;
        background: var(--dt2-primary);
        color: #FFF;
        font-size: 10px;
        font-weight: 950;
    }

    .dt2-customer-profile strong {
        display: block;
        color: var(--dt2-text);
        font-size: 10px;
        font-weight: 950;
    }

    .dt2-customer-profile span {
        display: block;
        margin-top: 4px;
        color: var(--dt2-muted);
        font-size: 8px;
    }

    .dt2-customer-meta {
        display: grid;
        gap: 9px;
        margin-top: 12px;
    }

    .dt2-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid var(--dt2-line);
    }

    .dt2-meta-row:last-child {
        border-bottom: 0;
    }

    .dt2-meta-row span {
        color: var(--dt2-muted);
        font-size: 8px;
    }

    .dt2-meta-row strong {
        color: var(--dt2-text);
        font-size: 9px;
        font-weight: 900;
        text-align: right;
    }

    .dt2-actions {
        display: grid;
        gap: 9px;
        padding: 18px 20px 20px;
    }

    .dt2-progress {
        display: grid;
        grid-template-columns: repeat(5,minmax(0,1fr));
        gap: 12px;
        padding: 18px 20px 20px;
    }

    .dt2-step {
        position: relative;
        min-height: 145px;
        padding: 14px;
        border: 1px solid var(--dt2-line);
        border-radius: 14px;
        background: var(--dt2-soft);
    }

    .dt2-step:not(:last-child)::after {
        position: absolute;
        top: 31px;
        right: -13px;
        width: 14px;
        height: 1px;
        background: var(--dt2-line-strong);
        content: "";
    }

    .dt2-step-dot {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid var(--dt2-line);
        border-radius: 11px;
        background: var(--dt2-surface);
        color: var(--dt2-muted);
        font-size: 9px;
        font-weight: 950;
    }

    .dt2-step.is-completed .dt2-step-dot {
        border-color: var(--dt2-success);
        background: var(--dt2-success);
        color: #FFF;
    }

    .dt2-step.is-current .dt2-step-dot {
        border-color: var(--dt2-primary);
        background: var(--dt2-primary);
        color: #FFF;
        box-shadow: 0 8px 18px rgba(102,31,24,.14);
    }

    .dt2-step-dot svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
    }

    .dt2-step h4 {
        margin: 12px 0 0;
        color: var(--dt2-text);
        font-size: 9px;
        font-weight: 950;
        line-height: 1.35;
    }

    .dt2-step p {
        margin: 6px 0 0;
        color: var(--dt2-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .dt2-step small {
        display: inline-flex;
        margin-top: 9px;
        color: var(--dt2-muted-light);
        font-size: 7px;
        font-weight: 850;
    }

    .dt2-summary {
        overflow: hidden;
        border-radius: 22px;
        background:
            radial-gradient(circle at 90% 15%, rgba(255,255,255,.12), transparent 28%),
            linear-gradient(135deg,var(--dt2-primary) 0%,#74271E 58%,var(--dt2-primary-dark) 100%);
        color: #FFF;
        box-shadow: 0 18px 44px rgba(102,31,24,.17);
    }

    .dt2-summary-body {
        padding: 20px;
    }

    .dt2-summary small {
        color: rgba(255,255,255,.58);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .dt2-summary h3 {
        margin: 10px 0 0;
        color: #FFF;
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        letter-spacing: -.04em;
    }

    .dt2-summary p {
        margin: 8px 0 0;
        color: rgba(255,255,255,.66);
        font-size: 9px;
        line-height: 1.5;
    }

    .dt2-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 15px;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 12px;
        background: rgba(255,255,255,.07);
    }

    .dt2-summary-row span {
        color: rgba(255,255,255,.52);
        font-size: 8px;
    }

    .dt2-summary-row strong {
        color: #FFF;
        font-size: 10px;
        font-weight: 900;
    }

    @media (max-width: 1260px) {
        .dt2-stats {
            grid-template-columns: repeat(3,minmax(0,1fr));
        }

        .dt2-main {
            grid-template-columns: 1fr;
        }

        .dt2-progress {
            grid-template-columns: repeat(3,minmax(0,1fr));
        }

        .dt2-step::after {
            display: none;
        }
    }

    @media (max-width: 860px) {
        .dt2-page-head,
        .dt2-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .dt2-hero {
            padding: 26px 22px;
        }

        .dt2-hero-actions {
            width: 100%;
        }

        .dt2-hero-actions .dt2-btn {
            flex: 1;
        }

        .dt2-stats {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }

        .dt2-progress {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }
    }

    @media (max-width: 560px) {
        .dt2-stats,
        .dt2-progress {
            grid-template-columns: 1fr;
        }

        .dt2-hero-actions {
            flex-direction: column;
        }

        .dt2-hero-actions .dt2-btn {
            width: 100%;
        }
    }

    html.dark .dt2-page {
        color: #F5EFE8;
    }

    html.dark .dt2-hero,
    html.dark .dt2-stat,
    html.dark .dt2-card {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg,#211B17 0%,#1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .dt2-card-head {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .dt2-page-head h1,
    html.dark .dt2-hero h2,
    html.dark .dt2-stat strong,
    html.dark .dt2-card-head h3,
    html.dark .dt2-customer-profile strong,
    html.dark .dt2-meta-row strong,
    html.dark .dt2-step h4,
    html.dark .dt2-map-info strong {
        color: #F5EFE8 !important;
    }

    html.dark .dt2-page-head p,
    html.dark .dt2-hero p,
    html.dark .dt2-stat label,
    html.dark .dt2-card-head p,
    html.dark .dt2-customer-profile span,
    html.dark .dt2-meta-row span,
    html.dark .dt2-step p,
    html.dark .dt2-step small,
    html.dark .dt2-map-info span {
        color: #AFA19A !important;
    }

    html.dark .dt2-live-pill,
    html.dark .dt2-btn-soft,
    html.dark .dt2-customer-profile,
    html.dark .dt2-step,
    html.dark .dt2-map-info {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .dt2-map {
        border-color: #3B2E27 !important;
        background:
            linear-gradient(rgba(235,169,157,.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(235,169,157,.035) 1px, transparent 1px),
            radial-gradient(circle at 24% 34%, rgba(193,151,113,.08), transparent 25%),
            #171210 !important;
        background-size: 32px 32px, 32px 32px, auto, auto !important;
    }

    html.dark .dt2-map-point.is-origin {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .dt2-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .dt2-btn-primary:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }

    html.dark .dt2-step.is-current .dt2-step-dot {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }
</style>

<div class="dt2-page">

    <section class="dt2-page-head">
        <div>
            <small>Live Delivery</small>
            <h1>Delivery Tracking</h1>
            <p>Monitor this parcel from the sorting center to the customer.</p>
        </div>

        <span class="dt2-live-pill">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['truck'] !!}
            </svg>

            {{ $delivery['status'] }}
        </span>
    </section>

    <section class="dt2-hero">
        <div>
            <span class="dt2-hero-kicker">Friday, September 11</span>

            <h2>Delivery in progress.</h2>

            <p>
                You are currently delivering <strong>{{ $delivery['tracking'] }}</strong>
                to <strong>{{ $delivery['customer'] }}</strong> in Los Baños, Laguna.
            </p>
        </div>

        <div class="dt2-hero-actions">
            <button
                type="button"
                id="openRouteBtn"
                class="dt2-btn dt2-btn-primary"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['navigation'] !!}
                </svg>

                Open Navigation
            </button>

            <button
                type="button"
                id="contactCustomerBtn"
                class="dt2-btn dt2-btn-soft"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['phone'] !!}
                </svg>

                Contact Customer
            </button>
        </div>
    </section>

    <section class="dt2-stats">
        @foreach($summary as $item)
            <article class="dt2-stat">
                <div class="dt2-stat-top">
                    <span class="dt2-stat-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons[$item['icon']] !!}
                        </svg>
                    </span>

                    <span class="dt2-stat-badge">
                        {{ $item['badge'] }}
                    </span>
                </div>

                <label>{{ $item['label'] }}</label>
                <strong title="{{ $item['value'] }}">{{ $item['value'] }}</strong>
            </article>
        @endforeach
    </section>

    <section class="dt2-main">

        <div class="dt2-stack">

            <section class="dt2-card">
                <header class="dt2-card-head">
                    <div>
                        <small>Route</small>
                        <h3>Delivery Route</h3>
                        <p>Current route from the LIKHAE sorting center to the customer.</p>
                    </div>

                    <span class="dt2-card-chip">Live route</span>
                </header>

                <div class="dt2-map-wrap">
                    <div class="dt2-map">
                        <div class="dt2-map-route"></div>

                        <span class="dt2-map-point is-origin" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['parcel'] !!}
                            </svg>
                        </span>

                        <span class="dt2-map-point is-destination" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['location'] !!}
                            </svg>
                        </span>

                        <div class="dt2-map-info">
                            <small>Customer Location</small>
                            <strong>{{ $delivery['customer'] }}</strong>
                            <span>{{ $delivery['address'] }}</span>
                        </div>
                    </div>

                    <div class="dt2-map-actions">
                        <button
                            type="button"
                            id="openRouteBtnSecondary"
                            class="dt2-btn dt2-btn-primary"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['navigation'] !!}
                            </svg>

                            Open Full Navigation
                        </button>
                    </div>
                </div>
            </section>

        </div>

        <aside class="dt2-stack">

            <section class="dt2-card">
                <header class="dt2-card-head">
                    <div>
                        <small>Recipient</small>
                        <h3>Customer</h3>
                        <p>Delivery recipient and contact information.</p>
                    </div>
                </header>

                <div class="dt2-customer">
                    <div class="dt2-customer-profile">
                        <span class="dt2-avatar">JD</span>

                        <div>
                            <strong>{{ $delivery['customer'] }}</strong>
                            <span>Delivery Recipient</span>
                        </div>
                    </div>

                    <div class="dt2-customer-meta">
                        <div class="dt2-meta-row">
                            <span>Contact</span>
                            <strong>{{ $delivery['contact'] }}</strong>
                        </div>

                        <div class="dt2-meta-row">
                            <span>COD Amount</span>
                            <strong>{{ $delivery['cod'] }}</strong>
                        </div>

                        <div class="dt2-meta-row">
                            <span>Status</span>
                            <strong>{{ $delivery['status'] }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="dt2-card">
                <header class="dt2-card-head">
                    <div>
                        <small>Actions</small>
                        <h3>Delivery Actions</h3>
                        <p>Update the current delivery outcome.</p>
                    </div>
                </header>

                <div class="dt2-actions">
                    <button
                        type="button"
                        id="arrivedCustomerBtn"
                        class="dt2-btn dt2-btn-soft"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['location'] !!}
                        </svg>

                        Arrived At Customer
                    </button>

                    <button
                        type="button"
                        id="markDeliveredBtn"
                        class="dt2-btn dt2-btn-success"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['check'] !!}
                        </svg>

                        Mark As Delivered
                    </button>

                    <button
                        type="button"
                        id="failedDeliveryBtn"
                        class="dt2-btn dt2-btn-danger"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['x'] !!}
                        </svg>

                        Delivery Failed
                    </button>
                </div>
            </section>

            <section class="dt2-summary">
                <div class="dt2-summary-body">
                    <small>Tracking Number</small>

                    <h3>{{ $delivery['tracking'] }}</h3>

                    <p>
                        Active parcel currently assigned for final-mile delivery.
                    </p>

                    <div class="dt2-summary-row">
                        <span>COD Amount</span>
                        <strong>{{ $delivery['cod'] }}</strong>
                    </div>
                </div>
            </section>

        </aside>

    </section>

    <section class="dt2-card">
        <header class="dt2-card-head">
            <div>
                <small>Progress</small>
                <h3>Delivery Status</h3>
                <p>Current delivery workflow from rider assignment to completion.</p>
            </div>

            <span class="dt2-card-chip">3 of 5 steps</span>
        </header>

        <div class="dt2-progress">
            @foreach($timeline as $index => $step)
                @php
                    $stepClass = match($step['status']) {
                        'Completed' => 'is-completed',
                        'Current' => 'is-current',
                        default => '',
                    };
                @endphp

                <article class="dt2-step {{ $stepClass }}">
                    <span class="dt2-step-dot">
                        @if($step['status'] === 'Completed')
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['check'] !!}
                            </svg>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </span>

                    <h4>{{ $step['title'] }}</h4>
                    <p>{{ $step['description'] }}</p>
                    <small>{{ $step['status'] }}</small>
                </article>
            @endforeach
        </div>
    </section>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const routeButtons = [
        document.getElementById('openRouteBtn'),
        document.getElementById('openRouteBtnSecondary')
    ].filter(Boolean);

    const contactButton =
        document.getElementById('contactCustomerBtn');

    const arrivedButton =
        document.getElementById('arrivedCustomerBtn');

    const deliveredButton =
        document.getElementById('markDeliveredBtn');

    const failedButton =
        document.getElementById('failedDeliveryBtn');


    function openRoute() {
        window.open(
            'https://www.google.com/maps/search/?api=1&query=123+Main+Street+Los+Ba%C3%B1os+Laguna',
            '_blank'
        );
    }


    routeButtons.forEach(function (button) {
        button.addEventListener(
            'click',
            openRoute
        );
    });


    contactButton?.addEventListener(
        'click',
        function () {
            window.location.href =
                'tel:+639175551234';
        }
    );


    arrivedButton?.addEventListener(
        'click',
        function () {
            arrivedButton.textContent =
                'Customer Reached';

            arrivedButton.disabled =
                true;

            arrivedButton.classList.add(
                'is-state-success'
            );
        }
    );


    deliveredButton?.addEventListener(
        'click',
        function () {
            deliveredButton.textContent =
                'Delivered ✓';

            deliveredButton.disabled =
                true;

            deliveredButton.classList.add(
                'is-state-success'
            );
        }
    );


    failedButton?.addEventListener(
        'click',
        function () {
            failedButton.textContent =
                'Marked Failed';

            failedButton.disabled =
                true;

            failedButton.classList.add(
                'is-state-warning'
            );
        }
    );
});
</script>
@endpush
