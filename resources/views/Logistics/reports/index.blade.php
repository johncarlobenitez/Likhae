@extends('logistics.app')

@section('title', 'Logistics Reports — LIKHAE Logistics')

@php
    $summaryCards = [
        [
            'label' => 'Parcels Received',
            'value' => '1,248',
            'change' => '+12.8%',
            'tone' => 'primary',
            'icon' => 'package',
        ],
        [
            'label' => 'Delivered',
            'value' => '1,106',
            'change' => '+9.4%',
            'tone' => 'success',
            'icon' => 'check',
        ],
        [
            'label' => 'Out for Delivery',
            'value' => '86',
            'change' => 'Active today',
            'tone' => 'primary',
            'icon' => 'truck',
        ],
        [
            'label' => 'Failed Delivery',
            'value' => '18',
            'change' => '-3.2%',
            'tone' => 'warning',
            'icon' => 'alert',
        ],
    ];

    $deliveryOverview = [
        ['label' => 'Sep 01', 'value' => 87],
        ['label' => 'Sep 02', 'value' => 91],
        ['label' => 'Sep 03', 'value' => 84],
        ['label' => 'Sep 04', 'value' => 76],
        ['label' => 'Sep 05', 'value' => 93],
        ['label' => 'Sep 06', 'value' => 81],
        ['label' => 'Sep 07', 'value' => 96],
    ];

    $parcelSummary = [
        ['Sep 03, 2026', 84, 79, 72, 68, 2],
        ['Sep 02, 2026', 91, 88, 81, 76, 3],
        ['Sep 01, 2026', 87, 84, 78, 74, 1],
        ['Aug 31, 2026', 79, 76, 70, 66, 2],
        ['Aug 30, 2026', 82, 80, 74, 70, 1],
    ];

    $riders = [
        ['Rider 03', 'Area C', '48', '45', '93.8%', '4.9'],
        ['Rider 01', 'Area A', '43', '41', '95.3%', '4.8'],
        ['Rider 04', 'Area D', '39', '37', '94.9%', '4.8'],
        ['Rider 02', 'Area B', '36', '33', '91.7%', '4.7'],
    ];

    $areas = [
        ['Area A', 'Santa Cruz', '312', '286', '91.7'],
        ['Area B', 'Pagsanjan', '245', '223', '91.0'],
        ['Area C', 'Los Baños', '381', '349', '91.6'],
        ['Area D', 'Calamba', '310', '281', '90.6'],
    ];

    $icons = [
        'package' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',
        'check' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="m8 12 2.7 2.7L16.5 9"/>
        ',
        'truck' => '
            <path d="M3 6h11v11H3z"/>
            <path d="M14 10h4l3 3v4h-7z"/>
            <circle cx="7" cy="19" r="2"/>
            <circle cx="18" cy="19" r="2"/>
        ',
        'alert' => '
            <path d="M12 3 2.5 20h19z"/>
            <path d="M12 9v4"/>
            <path d="M12 17h.01"/>
        ',
        'pdf' => '
            <path d="M6 3h9l4 4v14H6z"/>
            <path d="M14 3v5h5"/>
            <path d="M9 14h6"/>
            <path d="M9 17h4"/>
        ',
        'excel' => '
            <rect x="4" y="3" width="16" height="18" rx="2"/>
            <path d="m8 8 4 8"/>
            <path d="m12 8-4 8"/>
        ',
        'filter' => '
            <path d="M4 6h16"/>
            <path d="M7 12h10"/>
            <path d="M10 18h4"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --lr-bg: #FBF7F2;
        --lr-bg-soft: #F6EFE7;
        --lr-bg-warm: #F3E4DE;
        --lr-card: #FFFDF9;

        --lr-border: #EADCCC;
        --lr-border-strong: #DBCEC1;

        --lr-maroon: #561C17;
        --lr-maroon-2: #642920;
        --lr-maroon-dark: #3E130F;

        --lr-text: #3B211B;
        --lr-text-dark: #1C160F;
        --lr-brown: #6C4936;
        --lr-muted: #987865;
        --lr-muted-light: #A99386;

        --lr-tan: #C19771;

        --lr-success: #256F4A;
        --lr-success-soft: #EAF7EF;

        --lr-warning: #9A5B11;
        --lr-warning-soft: #FFF6DE;

        --lr-danger: #B42318;
        --lr-danger-soft: #FCEBE9;

        --lr-shadow: 0 8px 24px rgba(86, 28, 23, 0.055);
        --lr-shadow-hover: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .lr-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--lr-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .lr-page * {
        box-sizing: border-box;
    }

    .lr-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--lr-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--lr-shadow);
    }

    .lr-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--lr-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .lr-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .lr-hero h1 {
        margin: 10px 0 0;
        color: var(--lr-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .lr-hero p {
        max-width: 700px;
        margin: 13px 0 0;
        color: var(--lr-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .lr-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .lr-btn {
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
        cursor: pointer;
        transition: 160ms ease;
    }

    .lr-btn:hover {
        transform: translateY(-1px);
    }

    .lr-btn svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .lr-btn-primary {
        background: var(--lr-maroon);
        border-color: var(--lr-maroon);
        color: #FFFFFF;
        box-shadow: 0 10px 22px rgba(86,28,23,.14);
    }

    .lr-btn-primary:hover {
        background: var(--lr-maroon-dark);
        border-color: var(--lr-maroon-dark);
    }

    .lr-btn-soft {
        background: var(--lr-card);
        border-color: var(--lr-border);
        color: var(--lr-maroon);
    }

    .lr-btn-soft:hover {
        background: var(--lr-bg-warm);
        border-color: var(--lr-tan);
    }

    .lr-card {
        overflow: hidden;
        border: 1px solid var(--lr-border);
        border-radius: 22px;
        background: var(--lr-card);
        box-shadow: var(--lr-shadow);
    }

    .lr-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--lr-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .lr-card-head h2 {
        margin: 0;
        color: var(--lr-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .lr-card-head p {
        margin: 7px 0 0;
        color: var(--lr-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .lr-card-body {
        padding: 18px 20px;
    }

    .lr-filter-card {
        padding: 16px;
    }

    .lr-filter-grid {
        display: grid;
        grid-template-columns: minmax(0,1fr) 180px 180px auto;
        gap: 10px;
        align-items: end;
    }

    .lr-field label {
        display: block;
        margin-bottom: 7px;
        color: var(--lr-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .lr-input {
        width: 100%;
        min-height: 41px;
        padding: 0 12px;
        border: 1px solid var(--lr-border);
        border-radius: 12px;
        background: var(--lr-bg-soft);
        color: var(--lr-text);
        font-size: 10px;
        font-weight: 750;
        outline: none;
        transition: 150ms ease;
    }

    .lr-input:focus {
        border-color: var(--lr-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86,28,23,.07);
    }

    .lr-stats {
        display: grid;
        grid-template-columns: repeat(4,minmax(0,1fr));
        gap: 14px;
    }

    .lr-stat-card {
        display: flex;
        min-height: 128px;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        padding: 16px;
        border: 1px solid var(--lr-border);
        border-radius: 20px;
        background:
            radial-gradient(circle at 94% 6%, rgba(193,151,113,.14), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
        box-shadow: var(--lr-shadow);
        transition: 160ms ease;
    }

    .lr-stat-card:hover {
        transform: translateY(-2px);
        border-color: var(--lr-tan);
        box-shadow: var(--lr-shadow-hover);
    }

    .lr-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .lr-stat-icon {
        display: grid;
        width: 38px;
        height: 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 13px;
        background: var(--lr-bg-warm);
        color: var(--lr-maroon);
    }

    .lr-stat-icon.is-success {
        border-color: #CFE8DA;
        background: var(--lr-success-soft);
        color: var(--lr-success);
    }

    .lr-stat-icon.is-warning {
        border-color: #EAD39A;
        background: var(--lr-warning-soft);
        color: var(--lr-warning);
    }

    .lr-stat-icon svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.75;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .lr-stat-change {
        display: inline-flex;
        min-height: 24px;
        align-items: center;
        padding: 0 8px;
        border-radius: 999px;
        background: var(--lr-success-soft);
        color: var(--lr-success);
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .lr-stat-change.is-warning {
        background: var(--lr-warning-soft);
        color: var(--lr-warning);
    }

    .lr-stat-label {
        display: block;
        color: var(--lr-muted);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .lr-stat-value {
        display: block;
        margin-top: 6px;
        color: var(--lr-text-dark);
        font-size: 28px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.045em;
    }

    .lr-overview-grid {
        display: grid;
        grid-template-columns: minmax(0,1fr) 330px;
        gap: 18px;
        align-items: stretch;
    }

    .lr-chart-wrap {
        padding: 20px;
    }

    .lr-chart {
        display: flex;
        height: 230px;
        align-items: flex-end;
        gap: 12px;
        padding-top: 14px;
        border-bottom: 1px solid var(--lr-border);
    }

    .lr-bar-group {
        display: flex;
        min-width: 0;
        flex: 1;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        height: 100%;
    }

    .lr-bar-value {
        color: var(--lr-muted);
        font-size: 8px;
        font-weight: 850;
    }

    .lr-bar {
        width: min(42px, 100%);
        border-radius: 10px 10px 0 0;
        background:
            linear-gradient(
                180deg,
                var(--lr-maroon-2) 0%,
                var(--lr-maroon) 100%
            );
        box-shadow: 0 10px 20px rgba(86,28,23,.10);
    }

    .lr-bar-label {
        padding-bottom: 9px;
        color: var(--lr-muted);
        font-size: 8px;
        font-weight: 700;
        white-space: nowrap;
    }

    .lr-period {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        padding: 0 9px;
        border: 1px solid #E6C7BE;
        border-radius: 999px;
        background: var(--lr-bg-warm);
        color: var(--lr-maroon);
        font-size: 8px;
        font-weight: 900;
    }

    .lr-rate-body {
        display: grid;
        justify-items: center;
        gap: 20px;
        padding: 22px;
    }

    .lr-rate-ring {
        position: relative;
        display: grid;
        width: 160px;
        height: 160px;
        place-items: center;
        border-radius: 999px;
        background:
            conic-gradient(
                var(--lr-maroon) 0deg 330deg,
                var(--lr-bg-soft) 330deg 360deg
            );
    }

    .lr-rate-ring::after {
        position: absolute;
        inset: 15px;
        border-radius: inherit;
        background: var(--lr-card);
        content: "";
    }

    .lr-rate-copy {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .lr-rate-copy strong {
        display: block;
        color: var(--lr-text);
        font-size: 28px;
        font-weight: 950;
        line-height: 1;
    }

    .lr-rate-copy span {
        display: block;
        margin-top: 5px;
        color: var(--lr-muted);
        font-size: 8px;
    }

    .lr-rate-mini {
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 9px;
        width: 100%;
    }

    .lr-mini-stat {
        padding: 12px;
        border: 1px solid var(--lr-border);
        border-radius: 13px;
        background: var(--lr-bg-soft);
        text-align: center;
    }

    .lr-mini-stat strong {
        display: block;
        color: var(--lr-text);
        font-size: 13px;
        font-weight: 950;
    }

    .lr-mini-stat span {
        display: block;
        margin-top: 4px;
        color: var(--lr-muted);
        font-size: 8px;
    }

    .lr-table-wrap {
        overflow-x: auto;
    }

    .lr-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
    }

    .lr-table thead {
        background: var(--lr-bg-soft);
    }

    .lr-table th {
        padding: 12px 20px;
        border-bottom: 1px solid var(--lr-border);
        color: var(--lr-muted);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .09em;
        text-align: left;
        text-transform: uppercase;
    }

    .lr-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #EFE1D5;
        color: var(--lr-muted);
        font-size: 9px;
    }

    .lr-table tbody tr {
        transition: 150ms ease;
    }

    .lr-table tbody tr:hover td {
        background: var(--lr-bg-soft);
    }

    .lr-table td:first-child {
        color: var(--lr-text);
        font-weight: 900;
    }

    .lr-success-text {
        color: var(--lr-success) !important;
        font-weight: 900 !important;
    }

    .lr-warning-text {
        color: var(--lr-warning) !important;
        font-weight: 900 !important;
    }

    .lr-half-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 18px;
    }

    .lr-list {
        display: grid;
    }

    .lr-rider-row,
    .lr-area-row {
        padding: 14px 20px;
        border-bottom: 1px solid var(--lr-border);
    }

    .lr-rider-row:last-child,
    .lr-area-row:last-child {
        border-bottom: 0;
    }

    .lr-rider-row {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 12px;
    }

    .lr-rider-avatar {
        display: grid;
        width: 38px;
        height: 38px;
        place-items: center;
        border-radius: 999px;
        background: var(--lr-maroon);
        color: #FFFFFF;
        font-size: 8px;
        font-weight: 950;
    }

    .lr-rider-copy strong,
    .lr-area-row strong {
        display: block;
        color: var(--lr-text);
        font-size: 9px;
        font-weight: 900;
    }

    .lr-rider-copy span,
    .lr-area-row span {
        display: block;
        margin-top: 4px;
        color: var(--lr-muted);
        font-size: 8px;
    }

    .lr-rider-score {
        text-align: right;
    }

    .lr-rider-score strong {
        display: block;
        color: var(--lr-success);
        font-size: 9px;
        font-weight: 950;
    }

    .lr-rider-score span {
        display: block;
        margin-top: 4px;
        color: var(--lr-muted);
        font-size: 8px;
    }

    .lr-area-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .lr-area-rate {
        color: var(--lr-success) !important;
        font-weight: 950 !important;
    }

    .lr-progress {
        height: 7px;
        margin-top: 10px;
        overflow: hidden;
        border-radius: 999px;
        background: var(--lr-bg-soft);
    }

    .lr-progress > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: var(--lr-maroon);
    }

    .lr-area-meta {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 8px;
    }

    .lr-payment-grid {
        display: grid;
        grid-template-columns: repeat(3,minmax(0,1fr));
        gap: 10px;
    }

    .lr-payment-card {
        padding: 15px;
        border: 1px solid var(--lr-border);
        border-radius: 14px;
        background: var(--lr-bg-soft);
    }

    .lr-payment-card span {
        display: block;
        color: var(--lr-muted);
        font-size: 8px;
        font-weight: 800;
    }

    .lr-payment-card strong {
        display: block;
        margin-top: 8px;
        color: var(--lr-text);
        font-size: 18px;
        font-weight: 950;
        letter-spacing: -.03em;
    }

    .lr-payment-card.is-total strong {
        color: var(--lr-maroon);
    }

    .lr-reconciled {
        display: inline-flex;
        min-height: 27px;
        align-items: center;
        gap: 6px;
        padding: 0 10px;
        border: 1px solid #CFE8DA;
        border-radius: 999px;
        background: var(--lr-success-soft);
        color: var(--lr-success);
        font-size: 8px;
        font-weight: 900;
    }

    .lr-reconciled::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .lr-modal {
        visibility: hidden;
        position: fixed;
        inset: 0;
        z-index: 100;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgba(0,0,0,.52);
        opacity: 0;
        backdrop-filter: blur(3px);
        transition: 180ms ease;
    }

    .lr-modal.is-open {
        visibility: visible;
        opacity: 1;
    }

    .lr-modal-panel {
        width: min(430px, 100%);
        padding: 24px;
        border: 1px solid var(--lr-border);
        border-radius: 22px;
        background: var(--lr-card);
        box-shadow: var(--lr-shadow-hover);
        transform: scale(.96);
        transition: 180ms ease;
    }

    .lr-modal.is-open .lr-modal-panel {
        transform: scale(1);
    }

    .lr-modal-icon {
        display: grid;
        width: 54px;
        height: 54px;
        place-items: center;
        border-radius: 999px;
        background: var(--lr-success-soft);
        color: var(--lr-success);
    }

    .lr-modal-icon svg {
        width: 23px;
        height: 23px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .lr-modal-panel h2 {
        margin: 16px 0 0;
        color: var(--lr-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .lr-modal-panel p {
        margin: 10px 0 0;
        color: var(--lr-muted);
        font-size: 10px;
        line-height: 1.55;
    }

    @media (max-width: 1180px) {
        .lr-stats {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }

        .lr-overview-grid,
        .lr-half-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 900px) {
        .lr-filter-grid {
            grid-template-columns: 1fr 1fr;
        }

        .lr-filter-grid > :first-child {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 760px) {
        .lr-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .lr-actions {
            width: 100%;
        }

        .lr-actions .lr-btn {
            flex: 1;
        }

        .lr-payment-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .lr-filter-grid,
        .lr-stats {
            grid-template-columns: 1fr;
        }

        .lr-filter-grid > :first-child {
            grid-column: auto;
        }
    }

    html.dark .lr-page {
        color: #F5EFE8;
    }

    html.dark .lr-hero,
    html.dark .lr-card,
    html.dark .lr-stat-card,
    html.dark .lr-modal-panel {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .lr-card-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .lr-hero h1,
    html.dark .lr-stat-value,
    html.dark .lr-card-head h2,
    html.dark .lr-rate-copy strong,
    html.dark .lr-mini-stat strong,
    html.dark .lr-rider-copy strong,
    html.dark .lr-area-row strong,
    html.dark .lr-payment-card strong,
    html.dark .lr-modal-panel h2 {
        color: #F5EFE8 !important;
    }

    html.dark .lr-hero p,
    html.dark .lr-stat-label,
    html.dark .lr-card-head p,
    html.dark .lr-bar-value,
    html.dark .lr-bar-label,
    html.dark .lr-rate-copy span,
    html.dark .lr-mini-stat span,
    html.dark .lr-rider-copy span,
    html.dark .lr-rider-score span,
    html.dark .lr-area-row span,
    html.dark .lr-payment-card span,
    html.dark .lr-modal-panel p {
        color: #AFA19A !important;
    }

    html.dark .lr-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .lr-input {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .lr-input:focus {
        background: #211B17 !important;
        border-color: #60463A !important;
    }

    html.dark .lr-table thead,
    html.dark .lr-mini-stat,
    html.dark .lr-payment-card {
        background: #1D1715 !important;
    }

    html.dark .lr-table th,
    html.dark .lr-table td,
    html.dark .lr-mini-stat,
    html.dark .lr-payment-card {
        border-color: #30231F !important;
    }

    html.dark .lr-table th,
    html.dark .lr-table td {
        color: #AFA19A !important;
    }

    html.dark .lr-table td:first-child {
        color: #F5EFE8 !important;
    }

    html.dark .lr-table tbody tr:hover td {
        background: #241817 !important;
    }

    html.dark .lr-rate-ring {
        background:
            conic-gradient(
                #A84538 0deg 330deg,
                #241817 330deg 360deg
            ) !important;
    }

    html.dark .lr-rate-ring::after {
        background: #1A1412 !important;
    }

    html.dark .lr-rider-avatar {
        background: #A84538 !important;
    }

    html.dark .lr-progress {
        background: #241817 !important;
    }

    html.dark .lr-progress > span {
        background: #A84538 !important;
    }

    html.dark .lr-payment-card.is-total strong {
        color: #EBA99D !important;
    }

    html.dark .lr-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .lr-btn-primary:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }

    html.dark .lr-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }
</style>

<div class="lr-page">

    <section class="lr-hero">
        <div>
            <span class="lr-eyebrow">Analytics</span>

            <h1>Logistics Reports</h1>

            <p>
                Review parcel activity, rider performance, delivery areas,
                and payment summaries across LIKHAE Logistics.
            </p>
        </div>

        <div class="lr-actions">
            <button type="button" id="exportPdfButton" class="lr-btn lr-btn-soft">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['pdf'] !!}
                </svg>

                Export PDF
            </button>

            <button type="button" id="exportExcelButton" class="lr-btn lr-btn-primary">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['excel'] !!}
                </svg>

                Export Excel
            </button>
        </div>
    </section>

    <section class="lr-card lr-filter-card">
        <div class="lr-filter-grid">
            <div class="lr-field">
                <label for="reportType">Report</label>

                <select id="reportType" class="lr-input">
                    <option>Parcel Summary</option>
                    <option>Rider Performance</option>
                    <option>Area Performance</option>
                    <option>COD / Payment Summary</option>
                </select>
            </div>

            <div class="lr-field">
                <label for="dateFrom">From</label>

                <input
                    id="dateFrom"
                    type="date"
                    value="2026-09-01"
                    class="lr-input"
                >
            </div>

            <div class="lr-field">
                <label for="dateTo">To</label>

                <input
                    id="dateTo"
                    type="date"
                    value="2026-09-03"
                    class="lr-input"
                >
            </div>

            <button type="button" id="applyFilterButton" class="lr-btn lr-btn-primary">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['filter'] !!}
                </svg>

                Apply
            </button>
        </div>
    </section>

    <section class="lr-stats">
        @foreach($summaryCards as $stat)
            @php
                $toneClass = match($stat['tone']) {
                    'success' => 'is-success',
                    'warning' => 'is-warning',
                    default => '',
                };

                $changeClass = $stat['tone'] === 'warning'
                    ? 'is-warning'
                    : '';
            @endphp

            <article class="lr-stat-card">
                <div class="lr-stat-top">
                    <span class="lr-stat-icon {{ $toneClass }}" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons[$stat['icon']] !!}
                        </svg>
                    </span>

                    <span class="lr-stat-change {{ $changeClass }}">
                        {{ $stat['change'] }}
                    </span>
                </div>

                <div>
                    <span class="lr-stat-label">
                        {{ $stat['label'] }}
                    </span>

                    <strong class="lr-stat-value">
                        {{ $stat['value'] }}
                    </strong>
                </div>
            </article>
        @endforeach
    </section>

    <section class="lr-overview-grid">

        <section class="lr-card">
            <header class="lr-card-head">
                <div>
                    <h2>Delivery Overview</h2>
                    <p>Parcel volume for the selected reporting period.</p>
                </div>

                <span class="lr-period">Current Period</span>
            </header>

            <div class="lr-chart-wrap">
                <div class="lr-chart">
                    @foreach($deliveryOverview as $day)
                        <div class="lr-bar-group">
                            <span class="lr-bar-value">{{ $day['value'] }}</span>

                            <span
                                class="lr-bar"
                                style="height: {{ max(38, round($day['value'] * 1.55)) }}px"
                            ></span>

                            <span class="lr-bar-label">
                                {{ $day['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="lr-card">
            <header class="lr-card-head">
                <div>
                    <h2>Delivery Rate</h2>
                    <p>Overall delivery completion.</p>
                </div>
            </header>

            <div class="lr-rate-body">
                <div class="lr-rate-ring">
                    <div class="lr-rate-copy">
                        <strong>91.8%</strong>
                        <span>Success rate</span>
                    </div>
                </div>

                <div class="lr-rate-mini">
                    <div class="lr-mini-stat">
                        <strong>1,106</strong>
                        <span>Delivered</span>
                    </div>

                    <div class="lr-mini-stat">
                        <strong>124</strong>
                        <span>Pending</span>
                    </div>
                </div>
            </div>
        </section>

    </section>

    <section class="lr-card">
        <header class="lr-card-head">
            <div>
                <h2>Parcel Summary</h2>
                <p>Daily parcel volume and delivery status.</p>
            </div>

            <span class="lr-period">September 2026</span>
        </header>

        <div class="lr-table-wrap">
            <table class="lr-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Received</th>
                        <th>Sorted</th>
                        <th>Out for Delivery</th>
                        <th>Delivered</th>
                        <th>Failed</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($parcelSummary as $row)
                        <tr>
                            <td>{{ $row[0] }}</td>
                            <td>{{ $row[1] }}</td>
                            <td>{{ $row[2] }}</td>
                            <td>{{ $row[3] }}</td>
                            <td class="lr-success-text">{{ $row[4] }}</td>
                            <td class="lr-warning-text">{{ $row[5] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="lr-half-grid">

        <section class="lr-card">
            <header class="lr-card-head">
                <div>
                    <h2>Rider Performance</h2>
                    <p>Rider delivery completion and ratings.</p>
                </div>
            </header>

            <div class="lr-list">
                @foreach($riders as $rider)
                    <article class="lr-rider-row">
                        <span class="lr-rider-avatar">
                            {{ substr($rider[0], -2) }}
                        </span>

                        <div class="lr-rider-copy">
                            <strong>{{ $rider[0] }}</strong>

                            <span>
                                {{ $rider[1] }} · {{ $rider[2] }} assigned · {{ $rider[3] }} completed
                            </span>
                        </div>

                        <div class="lr-rider-score">
                            <strong>{{ $rider[4] }}</strong>
                            <span>{{ $rider[5] }} ★</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="lr-card">
            <header class="lr-card-head">
                <div>
                    <h2>Area Performance</h2>
                    <p>Parcel distribution by delivery area.</p>
                </div>
            </header>

            <div class="lr-list">
                @foreach($areas as $area)
                    <article class="lr-area-row">
                        <div class="lr-area-head">
                            <div>
                                <strong>{{ $area[0] }}</strong>
                                <span>{{ $area[1] }}</span>
                            </div>

                            <strong class="lr-area-rate">
                                {{ $area[4] }}%
                            </strong>
                        </div>

                        <div class="lr-progress">
                            <span style="width: {{ $area[4] }}%"></span>
                        </div>

                        <div class="lr-area-meta">
                            <span>{{ $area[2] }} parcels</span>
                            <span>{{ $area[3] }} delivered</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

    </section>

    <section class="lr-card">
        <header class="lr-card-head">
            <div>
                <h2>COD / Payment Summary</h2>
                <p>Payment collection overview for completed deliveries.</p>
            </div>

            <span class="lr-reconciled">
                Reconciled
            </span>
        </header>

        <div class="lr-card-body">
            <div class="lr-payment-grid">
                <div class="lr-payment-card">
                    <span>COD Collected</span>
                    <strong>₱184,520</strong>
                </div>

                <div class="lr-payment-card">
                    <span>GCash Payments</span>
                    <strong>₱96,840</strong>
                </div>

                <div class="lr-payment-card is-total">
                    <span>Total Collected</span>
                    <strong>₱281,360</strong>
                </div>
            </div>
        </div>
    </section>

</div>

<div id="reportModal" class="lr-modal">
    <div class="lr-modal-panel">
        <span class="lr-modal-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
                {!! $icons['check'] !!}
            </svg>
        </span>

        <h2 id="reportModalTitle">Export Ready</h2>

        <p id="reportModalText">
            Your report is ready.
        </p>

        <button
            type="button"
            id="closeReportModal"
            class="lr-btn lr-btn-primary"
            style="width:100%;margin-top:18px;"
        >
            Done
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal =
        document.getElementById('reportModal');

    const title =
        document.getElementById('reportModalTitle');

    const text =
        document.getElementById('reportModalText');


    function openModal(modalTitle, modalText) {
        if (!modal) {
            return;
        }

        if (title) {
            title.textContent = modalTitle;
        }

        if (text) {
            text.textContent = modalText;
        }

        modal.classList.add('is-open');

        document.body.style.overflow =
            'hidden';
    }


    function closeModal() {
        if (!modal) {
            return;
        }

        modal.classList.remove('is-open');

        document.body.style.overflow =
            '';
    }


    document
        .getElementById('exportPdfButton')
        ?.addEventListener(
            'click',
            function () {
                openModal(
                    'PDF Export',
                    'The selected report is prepared for PDF export. Connect this action to your Laravel PDF generator when the backend is ready.'
                );
            }
        );


    document
        .getElementById('exportExcelButton')
        ?.addEventListener(
            'click',
            function () {
                openModal(
                    'Excel Export',
                    'The selected report is prepared for Excel export. Connect this action to Laravel Excel when the backend is ready.'
                );
            }
        );


    document
        .getElementById('applyFilterButton')
        ?.addEventListener(
            'click',
            function () {
                const reportType =
                    document.getElementById('reportType')?.value || 'Parcel Summary';

                const from =
                    document.getElementById('dateFrom')?.value || '';

                const to =
                    document.getElementById('dateTo')?.value || '';

                if (!from || !to) {
                    openModal(
                        'Date Required',
                        'Please select both a start date and an end date.'
                    );

                    return;
                }

                if (from > to) {
                    openModal(
                        'Invalid Date Range',
                        'The start date cannot be later than the end date.'
                    );

                    return;
                }

                openModal(
                    'Filters Applied',
                    `${reportType} is set to ${from} through ${to}. Connect these filters to your Laravel controller for live report data.`
                );
            }
        );


    document
        .getElementById('closeReportModal')
        ?.addEventListener(
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
