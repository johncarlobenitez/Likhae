@extends('rider.app')

@section('title', 'Earnings — LIKHAE Rider')

@php
    $summary = [
        [
            'label' => "Today's Earnings",
            'value' => '₱850',
            'change' => '↑ 12.5%',
            'tone' => 'primary',
            'icon' => 'wallet',
        ],
        [
            'label' => 'This Week',
            'value' => '₱5,420',
            'change' => '↑ 8.2%',
            'tone' => 'primary',
            'icon' => 'calendar',
        ],
        [
            'label' => 'This Month',
            'value' => '₱28,450',
            'change' => '↑ 6.4%',
            'tone' => 'primary',
            'icon' => 'coins',
        ],
        [
            'label' => 'Completed Deliveries',
            'value' => '126',
            'change' => '↑ 4.1%',
            'tone' => 'primary',
            'icon' => 'parcel',
        ],
        [
            'label' => 'Pending Payout',
            'value' => '₱8,450',
            'change' => '1 due soon',
            'tone' => 'warning',
            'icon' => 'truck',
        ],
    ];

    $weekly = [
        ['day' => 'Mon', 'value' => 650],
        ['day' => 'Tue', 'value' => 900],
        ['day' => 'Wed', 'value' => 750],
        ['day' => 'Thu', 'value' => 1200],
        ['day' => 'Fri', 'value' => 980],
        ['day' => 'Sat', 'value' => 620],
        ['day' => 'Sun', 'value' => 850],
    ];

    $incentives = [
        [
            'title' => 'Daily Target',
            'reward' => '₱300 Bonus',
            'description' => 'Complete your daily delivery goal.',
            'progress' => 75,
        ],
        [
            'title' => 'Perfect Rating',
            'reward' => '₱500 Bonus',
            'description' => 'Maintain excellent buyer feedback.',
            'progress' => 92,
        ],
        [
            'title' => '100 Deliveries',
            'reward' => '₱1,000 Bonus',
            'description' => 'Reach the delivery milestone.',
            'progress' => 68,
        ],
    ];

    $history = [
        ['Sep 03, 2026', '8 Deliveries', '₱850', 'Paid'],
        ['Sep 02, 2026', '10 Deliveries', '₱1,200', 'Paid'],
        ['Sep 01, 2026', '6 Deliveries', '₱700', 'Pending'],
        ['Aug 31, 2026', '7 Deliveries', '₱780', 'Paid'],
    ];

    $icons = [
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
        'coins' => '
            <ellipse cx="8" cy="8" rx="5" ry="3"/>
            <path d="M3 8v4c0 1.7 2.2 3 5 3s5-1.3 5-3V8"/>
            <path d="M11 15c.8 1.2 2.6 2 4.7 2 2.9 0 5.3-1.4 5.3-3.2 0-1.5-1.7-2.7-4.1-3.1"/>
            <path d="M11 18c.9 1.2 2.7 2 4.7 2 2.9 0 5.3-1.4 5.3-3.2v-3"/>
        ',
        'parcel' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',
        'truck' => '
            <path d="M3 6h11v11H3z"/>
            <path d="M14 10h4l3 3v4h-7z"/>
            <circle cx="7" cy="19" r="2"/>
            <circle cx="18" cy="19" r="2"/>
        ',
        'arrow' => '
            <path d="M5 12h14"/>
            <path d="m14 7 5 5-5 5"/>
        ',
        'gift' => '
            <path d="M3 10h18v11H3z"/>
            <path d="M12 10v11"/>
            <path d="M2 7h20v3H2z"/>
            <path d="M12 7H7.5a2.5 2.5 0 1 1 2.2-3.7L12 7Z"/>
            <path d="M12 7h4.5a2.5 2.5 0 1 0-2.2-3.7L12 7Z"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --er-page: #FBF7F2;
        --er-surface: #FFFDF9;
        --er-soft: #F6EFE7;
        --er-warm: #F1E2DA;
        --er-line: #E8D8C8;
        --er-line-strong: #D9C6B7;

        --er-primary: #661F18;
        --er-primary-hover: #4F1712;
        --er-primary-soft: #F2E3DE;

        --er-text: #3A211B;
        --er-text-strong: #20110E;
        --er-muted: #987865;
        --er-muted-2: #B39A8A;

        --er-success: #256F4A;
        --er-success-soft: #EAF7EF;
        --er-warning: #9A5B11;
        --er-warning-soft: #FFF4D9;

        --er-shadow: 0 12px 32px rgba(79, 36, 27, .06);
        --er-shadow-lg: 0 22px 60px rgba(79, 36, 27, .10);
    }

    .er-page {
        width: 100%;
        display: grid;
        gap: 24px;
        color: var(--er-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .er-page * {
        box-sizing: border-box;
    }

    .er-topbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 22px;
    }

    .er-title-wrap small {
        display: block;
        color: var(--er-primary);
        font-size: 9px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .er-title-wrap h1 {
        margin: 8px 0 0;
        color: var(--er-text);
        font-size: 22px;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .er-title-wrap p {
        margin: 6px 0 0;
        color: var(--er-muted);
        font-size: 9px;
    }

    .er-request-btn {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        gap: 8px;
        padding: 0 16px;
        border: 1px solid var(--er-primary);
        border-radius: 12px;
        background: var(--er-primary);
        color: #fff;
        font-size: 9px;
        font-weight: 900;
        cursor: pointer;
        box-shadow: 0 12px 24px rgba(102,31,24,.14);
        transition: 160ms ease;
    }

    .er-request-btn:hover {
        transform: translateY(-1px);
        background: var(--er-primary-hover);
        border-color: var(--er-primary-hover);
    }

    .er-request-btn svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .er-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        min-height: 190px;
        padding: 28px 30px;
        border: 1px solid var(--er-line);
        border-radius: 28px;
        background:
            radial-gradient(circle at 88% 18%, rgba(196,154,119,.22), transparent 28%),
            linear-gradient(120deg, #FBF5EE 0%, #F5EDE5 55%, #EEDFCE 100%);
        box-shadow: var(--er-shadow);
    }

    .er-hero small {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--er-primary);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .er-hero small::before {
        width: 20px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .er-hero h2 {
        margin: 10px 0 0;
        color: var(--er-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5.3vw, 68px);
        font-weight: 400;
        line-height: .96;
        letter-spacing: -.055em;
    }

    .er-hero p {
        margin: 12px 0 0;
        color: var(--er-muted);
        font-size: 10px;
        line-height: 1.65;
    }

    .er-hero strong {
        color: var(--er-primary);
    }

    .er-stats {
        display: grid;
        grid-template-columns: repeat(5,minmax(0,1fr));
        gap: 14px;
    }

    .er-stat {
        min-height: 126px;
        padding: 15px;
        border: 1px solid var(--er-line);
        border-radius: 18px;
        background: rgba(255,253,249,.95);
        box-shadow: var(--er-shadow);
    }

    .er-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
    }

    .er-stat-icon {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid #E5C8BF;
        border-radius: 11px;
        background: var(--er-primary-soft);
        color: var(--er-primary);
    }

    .er-stat-icon svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .er-change {
        display: inline-flex;
        min-height: 23px;
        align-items: center;
        padding: 0 7px;
        border: 1px solid #E7C6BC;
        border-radius: 999px;
        background: #F8E9E4;
        color: var(--er-primary);
        font-size: 7px;
        font-weight: 900;
        white-space: nowrap;
    }

    .er-change.is-warning {
        border-color: #ECD293;
        background: var(--er-warning-soft);
        color: var(--er-warning);
    }

    .er-stat label {
        display: block;
        margin-top: 14px;
        color: var(--er-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .er-stat strong {
        display: block;
        margin-top: 5px;
        color: var(--er-text-strong);
        font-size: 24px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.045em;
    }

    .er-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(300px,.75fr);
        gap: 18px;
        align-items: start;
    }

    .er-card {
        overflow: hidden;
        border: 1px solid var(--er-line);
        border-radius: 22px;
        background: var(--er-surface);
        box-shadow: var(--er-shadow);
    }

    .er-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 20px 16px;
        border-bottom: 1px solid var(--er-line);
        background: linear-gradient(180deg,#FFFDF9 0%,#FBF7F2 100%);
    }

    .er-card-head small {
        display: block;
        color: var(--er-primary);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .er-card-head h3 {
        margin: 7px 0 0;
        color: var(--er-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .er-card-head p {
        margin: 7px 0 0;
        color: var(--er-muted);
        font-size: 9px;
    }

    .er-range {
        min-height: 34px;
        padding: 0 11px;
        border: 1px solid var(--er-line);
        border-radius: 10px;
        background: var(--er-soft);
        color: var(--er-text);
        font-size: 8px;
        font-weight: 850;
        outline: none;
    }

    .er-chart-body {
        padding: 18px 20px 22px;
    }

    .er-chart-total {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 14px;
    }

    .er-chart-total span {
        display: block;
        color: var(--er-muted);
        font-size: 8px;
        font-weight: 800;
    }

    .er-chart-total strong {
        display: block;
        margin-top: 4px;
        color: var(--er-primary);
        font-size: 28px;
        font-weight: 950;
        letter-spacing: -.045em;
    }

    .er-growth-pill {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        padding: 0 9px;
        border: 1px solid #E7C6BC;
        border-radius: 999px;
        background: #F8E9E4;
        color: var(--er-primary);
        font-size: 7px;
        font-weight: 900;
    }

    .er-chart {
        display: flex;
        height: 240px;
        align-items: end;
        gap: 18px;
        margin-top: 18px;
        padding: 24px 18px 12px;
        border: 1px solid var(--er-line);
        border-radius: 16px;
        background:
            linear-gradient(rgba(102,31,24,.035) 1px, transparent 1px),
            linear-gradient(180deg,#FFFDF9 0%,#FCF8F4 100%);
        background-size: 100% 42px, auto;
    }

    .er-bar-wrap {
        display: flex;
        min-width: 0;
        flex: 1;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .er-bar-wrap > span:first-child {
        color: var(--er-muted);
        font-size: 7px;
        font-weight: 800;
    }

    .er-bar {
        width: 18px;
        min-height: 26px;
        border-radius: 8px 8px 3px 3px;
        background: linear-gradient(180deg,#823126 0%,#651F18 100%);
        box-shadow: 0 8px 16px rgba(102,31,24,.15);
    }

    .er-bar-wrap > span:last-child {
        color: var(--er-muted);
        font-size: 7px;
        font-weight: 750;
    }

    .er-payout {
        padding: 18px 20px 20px;
    }

    .er-balance {
        padding: 18px;
        border: 1px solid #CFE8DA;
        border-radius: 16px;
        background: var(--er-success-soft);
    }

    .er-balance span {
        color: var(--er-success);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .er-balance strong {
        display: block;
        margin-top: 7px;
        color: var(--er-text-strong);
        font-size: 28px;
        font-weight: 950;
        letter-spacing: -.04em;
    }

    .er-payout-list {
        display: grid;
        gap: 12px;
        margin-top: 16px;
    }

    .er-payout-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 11px;
        border-bottom: 1px solid var(--er-line);
    }

    .er-payout-row:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .er-payout-row span {
        color: var(--er-muted);
        font-size: 8px;
    }

    .er-payout-row strong {
        color: var(--er-text);
        font-size: 9px;
        font-weight: 900;
    }

    .er-payout-row strong.is-success {
        color: var(--er-success);
    }

    .er-section {
        overflow: hidden;
        border: 1px solid var(--er-line);
        border-radius: 22px;
        background: var(--er-surface);
        box-shadow: var(--er-shadow);
    }

    .er-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--er-line);
        background: linear-gradient(180deg,#FFFDF9 0%,#FBF7F2 100%);
    }

    .er-section-head h3 {
        margin: 0;
        color: var(--er-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 26px;
        font-weight: 400;
        letter-spacing: -.035em;
    }

    .er-section-head p {
        margin: 6px 0 0;
        color: var(--er-muted);
        font-size: 8px;
    }

    .er-incentives {
        display: grid;
        grid-template-columns: repeat(3,minmax(0,1fr));
        gap: 12px;
        padding: 18px 20px 20px;
    }

    .er-incentive {
        padding: 15px;
        border: 1px solid var(--er-line);
        border-radius: 14px;
        background: var(--er-soft);
    }

    .er-incentive-icon {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border-radius: 11px;
        background: var(--er-primary-soft);
        color: var(--er-primary);
    }

    .er-incentive-icon svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
    }

    .er-incentive h4 {
        margin: 12px 0 0;
        color: var(--er-text);
        font-size: 9px;
        font-weight: 950;
    }

    .er-incentive strong {
        display: block;
        margin-top: 4px;
        color: var(--er-primary);
        font-size: 10px;
        font-weight: 950;
    }

    .er-incentive p {
        margin: 6px 0 0;
        color: var(--er-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .er-progress {
        height: 6px;
        overflow: hidden;
        margin-top: 12px;
        border-radius: 999px;
        background: #E8DDD3;
    }

    .er-progress > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: var(--er-primary);
    }

    .er-history {
        display: grid;
    }

    .er-history-row {
        display: grid;
        grid-template-columns: minmax(0,1fr) auto auto;
        align-items: center;
        gap: 18px;
        padding: 15px 20px;
        border-bottom: 1px solid var(--er-line);
    }

    .er-history-row:last-child {
        border-bottom: 0;
    }

    .er-history-row:hover {
        background: var(--er-soft);
    }

    .er-history-row strong {
        display: block;
        color: var(--er-text);
        font-size: 9px;
        font-weight: 950;
    }

    .er-history-row span {
        display: block;
        margin-top: 4px;
        color: var(--er-muted);
        font-size: 8px;
    }

    .er-history-amount {
        color: var(--er-text-strong) !important;
        font-size: 10px !important;
        font-weight: 950 !important;
        white-space: nowrap;
    }

    .er-status-pill {
        display: inline-flex !important;
        min-height: 24px;
        align-items: center;
        padding: 0 8px;
        margin: 0 !important;
        border-radius: 999px;
        font-size: 7px !important;
        font-weight: 900 !important;
        white-space: nowrap;
    }

    .er-status-pill.is-paid {
        background: var(--er-success-soft);
        color: var(--er-success);
    }

    .er-status-pill.is-pending {
        background: var(--er-warning-soft);
        color: var(--er-warning);
    }

    .er-modal {
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

    .er-modal.is-open {
        visibility: visible;
        opacity: 1;
    }

    .er-modal-panel {
        width: min(420px,100%);
        padding: 24px;
        border: 1px solid var(--er-line);
        border-radius: 22px;
        background: var(--er-surface);
        box-shadow: var(--er-shadow-lg);
        transform: scale(.96);
        transition: 180ms ease;
    }

    .er-modal.is-open .er-modal-panel {
        transform: scale(1);
    }

    .er-modal-icon {
        display: grid;
        width: 48px;
        height: 48px;
        place-items: center;
        border-radius: 999px;
        background: var(--er-primary-soft);
        color: var(--er-primary);
    }

    .er-modal-icon svg {
        width: 21px;
        height: 21px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .er-modal h3 {
        margin: 15px 0 0;
        color: var(--er-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        letter-spacing: -.04em;
    }

    .er-modal p {
        margin: 8px 0 0;
        color: var(--er-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .er-modal button {
        width: 100%;
        min-height: 40px;
        margin-top: 18px;
        border: 0;
        border-radius: 11px;
        background: var(--er-primary);
        color: #fff;
        font-size: 9px;
        font-weight: 900;
        cursor: pointer;
    }

    @media (max-width: 1260px) {
        .er-stats {
            grid-template-columns: repeat(3,minmax(0,1fr));
        }

        .er-main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 860px) {
        .er-topbar,
        .er-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .er-hero {
            padding: 26px 22px;
        }

        .er-request-btn {
            width: 100%;
            justify-content: center;
        }

        .er-stats {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }

        .er-incentives {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .er-stats {
            grid-template-columns: 1fr;
        }

        .er-history-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .er-chart {
            gap: 8px;
            padding-inline: 10px;
        }

        .er-bar {
            width: 14px;
        }
    }

    html.dark .er-page {
        color: #F5EFE8;
    }

    html.dark .er-hero,
    html.dark .er-stat,
    html.dark .er-card,
    html.dark .er-section,
    html.dark .er-modal-panel {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg,#211B17 0%,#1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .er-card-head,
    html.dark .er-section-head {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .er-title-wrap h1,
    html.dark .er-hero h2,
    html.dark .er-stat strong,
    html.dark .er-card-head h3,
    html.dark .er-chart-total strong,
    html.dark .er-balance strong,
    html.dark .er-section-head h3,
    html.dark .er-incentive h4,
    html.dark .er-history-row strong,
    html.dark .er-modal h3 {
        color: #F5EFE8 !important;
    }

    html.dark .er-title-wrap p,
    html.dark .er-hero p,
    html.dark .er-stat label,
    html.dark .er-card-head p,
    html.dark .er-section-head p,
    html.dark .er-incentive p,
    html.dark .er-history-row span,
    html.dark .er-modal p {
        color: #AFA19A !important;
    }

    html.dark .er-chart {
        background:
            linear-gradient(rgba(235,169,157,.035) 1px, transparent 1px),
            #171210 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .er-incentive {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .er-history-row {
        border-color: #30231F !important;
    }

    html.dark .er-history-row:hover {
        background: #241817 !important;
    }

    html.dark .er-range {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .er-request-btn {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }
</style>

<div class="er-page">

    <section class="er-topbar">
        <div class="er-title-wrap">
            <small>Income Management</small>

            <h1>My Earnings</h1>

            <p>
                Track delivery income, incentives, and payout activity.
            </p>
        </div>

        <button type="button" id="requestPayoutButton" class="er-request-btn">
            Request Payout

            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['arrow'] !!}
            </svg>
        </button>
    </section>

    <section class="er-hero">
        <div>
            <small>Friday, September 11</small>

            <h2>Good evening, Rider.</h2>

            <p>
                You earned <strong>₱850 today</strong> and currently have
                <strong>₱8,450 available for payout</strong>.
            </p>
        </div>
    </section>

    <section class="er-stats">
        @foreach($summary as $item)
            <article class="er-stat">
                <div class="er-stat-top">
                    <span class="er-stat-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons[$item['icon']] !!}
                        </svg>
                    </span>

                    <span class="er-change {{ $item['tone'] === 'warning' ? 'is-warning' : '' }}">
                        {{ $item['change'] }}
                    </span>
                </div>

                <label>{{ $item['label'] }}</label>
                <strong>{{ $item['value'] }}</strong>
            </article>
        @endforeach
    </section>

    <section class="er-main-grid">
        <section class="er-card">
            <header class="er-card-head">
                <div>
                    <small>Performance</small>
                    <h3>Weekly Earnings</h3>
                    <p>Delivery income collected over the last 7 days.</p>
                </div>

                <select class="er-range" aria-label="Earnings range">
                    <option>Last 7 days</option>
                    <option>This month</option>
                    <option>Last 30 days</option>
                </select>
            </header>

            <div class="er-chart-body">
                <div class="er-chart-total">
                    <div>
                        <span>Total earnings</span>
                        <strong>₱5,950</strong>
                    </div>

                    <span class="er-growth-pill">
                        ↑ 12% vs previous week
                    </span>
                </div>

                <div class="er-chart">
                    @foreach($weekly as $day)
                        <div class="er-bar-wrap">
                            <span>₱{{ number_format($day['value']) }}</span>

                            <div
                                class="er-bar"
                                style="height: {{ max(32, round(($day['value'] / 1200) * 145)) }}px"
                            ></div>

                            <span>{{ $day['day'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <aside class="er-card">
            <header class="er-card-head">
                <div>
                    <small>Payout</small>
                    <h3>Payout Status</h3>
                    <p>Current balance and recent payout details.</p>
                </div>
            </header>

            <div class="er-payout">
                <div class="er-balance">
                    <span>Available Balance</span>
                    <strong>₱8,450</strong>
                </div>

                <div class="er-payout-list">
                    <div class="er-payout-row">
                        <span>Last Payout</span>
                        <strong>₱7,800</strong>
                    </div>

                    <div class="er-payout-row">
                        <span>Status</span>
                        <strong class="is-success">Completed</strong>
                    </div>

                    <div class="er-payout-row">
                        <span>Schedule</span>
                        <strong>Every Friday</strong>
                    </div>
                </div>
            </div>
        </aside>
    </section>

    <section class="er-section">
        <header class="er-section-head">
            <div>
                <h3>Delivery Incentives</h3>
                <p>Additional rider rewards based on performance.</p>
            </div>
        </header>

        <div class="er-incentives">
            @foreach($incentives as $bonus)
                <article class="er-incentive">
                    <span class="er-incentive-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['gift'] !!}
                        </svg>
                    </span>

                    <h4>{{ $bonus['title'] }}</h4>
                    <strong>{{ $bonus['reward'] }}</strong>

                    <p>{{ $bonus['description'] }}</p>

                    <div class="er-progress">
                        <span style="width: {{ $bonus['progress'] }}%"></span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="er-section">
        <header class="er-section-head">
            <div>
                <h3>Earnings History</h3>
                <p>Recent delivery income and payout status.</p>
            </div>
        </header>

        <div class="er-history">
            @foreach($history as $earning)
                <article class="er-history-row">
                    <div>
                        <strong>{{ $earning[0] }}</strong>
                        <span>{{ $earning[1] }}</span>
                    </div>

                    <strong class="er-history-amount">
                        {{ $earning[2] }}
                    </strong>

                    <span class="er-status-pill {{ $earning[3] === 'Paid' ? 'is-paid' : 'is-pending' }}">
                        {{ $earning[3] }}
                    </span>
                </article>
            @endforeach
        </div>
    </section>

</div>

<div id="payoutModal" class="er-modal">
    <div class="er-modal-panel">
        <span class="er-modal-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['wallet'] !!}
            </svg>
        </span>

        <h3>Payout request</h3>

        <p>
            Your available balance is ₱8,450. Connect this action to your Laravel
            payout endpoint when the backend flow is ready.
        </p>

        <button type="button" id="closePayoutModal">
            Done
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const requestButton =
        document.getElementById('requestPayoutButton');

    const modal =
        document.getElementById('payoutModal');

    const closeButton =
        document.getElementById('closePayoutModal');


    function openModal() {
        modal?.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }


    function closeModal() {
        modal?.classList.remove('is-open');
        document.body.style.overflow = '';
    }


    requestButton?.addEventListener(
        'click',
        openModal
    );


    closeButton?.addEventListener(
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
});
</script>
@endpush
