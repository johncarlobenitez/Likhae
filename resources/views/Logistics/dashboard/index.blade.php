@extends('logistics.app')

@section('title', 'Dashboard — LIKHAE Logistics')

@php
    $stats = [
        [
            'label' => 'Received Today',
            'value' => 128,
            'description' => 'Incoming parcels',
            'change' => '+12%',
            'type' => 'primary',
            'icon' => 'box',
        ],
        [
            'label' => 'Sorting Queue',
            'value' => 34,
            'description' => 'Waiting for sorting',
            'change' => '8 urgent',
            'type' => 'warning',
            'icon' => 'sort',
        ],
        [
            'label' => 'Awaiting Rider',
            'value' => 18,
            'description' => 'Ready for assignment',
            'change' => '14 riders online',
            'type' => 'info',
            'icon' => 'rider',
        ],
        [
            'label' => 'On the Road',
            'value' => 76,
            'description' => 'Out for delivery',
            'change' => '91% on time',
            'type' => 'success',
            'icon' => 'truck',
        ],
    ];

    $recentParcels = [
        [
            'id' => 1001,
            'tracking' => 'LH-2026-1001',
            'buyer' => 'Juan Dela Cruz',
            'destination' => 'Santa Cruz',
            'area' => 'Unassigned',
            'status' => 'Waiting for Sorting',
            'status_type' => 'warning',
            'time' => '10:32 AM',
        ],
        [
            'id' => 1002,
            'tracking' => 'LH-2026-1002',
            'buyer' => 'Maria Santos',
            'destination' => 'Pagsanjan',
            'area' => 'Area B',
            'status' => 'Awaiting Rider',
            'status_type' => 'info',
            'time' => '10:15 AM',
        ],
        [
            'id' => 1003,
            'tracking' => 'LH-2026-1003',
            'buyer' => 'Ana Reyes',
            'destination' => 'Los Baños',
            'area' => 'Area C',
            'status' => 'Rider Assigned',
            'status_type' => 'primary',
            'time' => '9:48 AM',
        ],
        [
            'id' => 1004,
            'tracking' => 'LH-2026-1004',
            'buyer' => 'Carlo Mendoza',
            'destination' => 'Calamba',
            'area' => 'Area D',
            'status' => 'Out for Delivery',
            'status_type' => 'success',
            'time' => '8:20 AM',
        ],
        [
            'id' => 1005,
            'tracking' => 'LH-2026-1005',
            'buyer' => 'Sofia Garcia',
            'destination' => 'Santa Cruz',
            'area' => 'Area A',
            'status' => 'Delivered',
            'status_type' => 'success',
            'time' => '7:55 AM',
        ],
    ];

    $areas = [
        [
            'code' => 'A',
            'area' => 'Area A',
            'municipality' => 'Santa Cruz',
            'available' => 3,
            'total' => 4,
        ],
        [
            'code' => 'B',
            'area' => 'Area B',
            'municipality' => 'Pagsanjan',
            'available' => 2,
            'total' => 3,
        ],
        [
            'code' => 'C',
            'area' => 'Area C',
            'municipality' => 'Los Baños',
            'available' => 5,
            'total' => 6,
        ],
        [
            'code' => 'D',
            'area' => 'Area D',
            'municipality' => 'Calamba',
            'available' => 4,
            'total' => 5,
        ],
    ];

    $activity = [
        [
            'title' => 'Parcel received',
            'description' => 'LH-2026-1018 entered the sorting center.',
            'time' => '2 min ago',
            'type' => 'primary',
        ],
        [
            'title' => 'Rider assigned',
            'description' => 'Rider 03 assigned to LH-2026-1003.',
            'time' => '8 min ago',
            'type' => 'info',
        ],
        [
            'title' => 'Parcel dispatched',
            'description' => 'LH-2026-1004 is now out for delivery.',
            'time' => '15 min ago',
            'type' => 'success',
        ],
        [
            'title' => 'Sorting completed',
            'description' => 'LH-2026-1002 sorted under Area B.',
            'time' => '24 min ago',
            'type' => 'warning',
        ],
    ];

    $operations = [
        [
            'label' => 'Receive Parcel',
            'description' => 'Scan incoming package',
            'href' => route('logistics.parcels.receive'),
            'type' => 'primary',
            'icon' => 'plus',
        ],
        [
            'label' => 'Sort Parcels',
            'description' => '34 parcels waiting',
            'href' => route('logistics.sorting'),
            'type' => 'warning',
            'icon' => 'sort',
        ],
        [
            'label' => 'Assign Riders',
            'description' => '18 awaiting assignment',
            'href' => route('logistics.assignments'),
            'type' => 'info',
            'icon' => 'rider',
        ],
        [
            'label' => 'Track Deliveries',
            'description' => '76 parcels on road',
            'href' => route('logistics.parcels.tracking'),
            'type' => 'success',
            'icon' => 'target',
        ],
    ];

    $iconPaths = [
        'box' => '
            <path d="M21 8 12 3 3 8l9 5 9-5Z"/>
            <path d="M3 8v8l9 5 9-5V8"/>
        ',
        'sort' => '
            <path d="M8 3v18"/>
            <path d="m4 7 4-4 4 4"/>
            <path d="M16 21V3"/>
            <path d="m12 17 4 4 4-4"/>
        ',
        'rider' => '
            <circle cx="8" cy="7" r="3"/>
            <path d="M3 19c0-3 2-5 5-5"/>
            <path d="M13 13h8"/>
            <path d="m18 9 4 4-4 4"/>
        ',
        'truck' => '
            <path d="M3 6h11v11H3z"/>
            <path d="M14 10h4l3 3v4h-7z"/>
            <circle cx="7" cy="19" r="2"/>
            <circle cx="18" cy="19" r="2"/>
        ',
        'plus' => '
            <path d="M12 5v14"/>
            <path d="M5 12h14"/>
        ',
        'target' => '
            <circle cx="12" cy="12" r="8"/>
            <circle cx="12" cy="12" r="3"/>
        ',
        'search' => '
            <circle cx="11" cy="11" r="7"/>
            <path d="m20 20-3.5-3.5"/>
        ',
        'scan' => '
            <path d="M3 5h4"/>
            <path d="M17 5h4"/>
            <path d="M3 19h4"/>
            <path d="M17 19h4"/>
            <path d="M7 3v18"/>
            <path d="M17 3v18"/>
            <path d="M11 3v18"/>
            <path d="M14 3v18"/>
        ',
    ];
@endphp

@section('content')
<style>
    :root {
        --lgx-bg: #FBF7F2;
        --lgx-bg-soft: #F6EFE7;
        --lgx-bg-alt: #EFE7DE;
        --lgx-card: #FFFDF9;

        --lgx-border: #EADCCC;
        --lgx-border-strong: #DBCEC1;

        --lgx-maroon: #561C17;
        --lgx-maroon-2: #642920;
        --lgx-maroon-dark: #3E130F;

        --lgx-text: #3B211B;
        --lgx-text-dark: #1C160F;
        --lgx-brown: #6C4936;
        --lgx-muted: #987865;
        --lgx-muted-2: #A99386;

        --lgx-tan: #C19771;

        --lgx-success: #256F4A;
        --lgx-success-soft: #EAF7EF;

        --lgx-warning: #9A5B11;
        --lgx-warning-soft: #FFF6DE;

        --lgx-danger: #B42318;
        --lgx-danger-soft: #FCEBE9;

        --lgx-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --lgx-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .lgx-dashboard {
        display: grid;
        gap: 18px;

        width: 100%;

        color: var(--lgx-text);
    }

    .lgx-dashboard * {
        box-sizing: border-box;
    }

    .lgx-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;

        padding: 34px 38px;

        border: 1px solid var(--lgx-border);
        border-radius: 30px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.28), transparent 30%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.08), transparent 32%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 56%, #EFE7DE 100%);

        box-shadow: var(--lgx-shadow-soft);
    }

    .lgx-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;

        margin-bottom: 16px;

        color: var(--lgx-muted);

        font-size: 10px;
        font-weight: 800;
    }

    .lgx-breadcrumb a {
        color: var(--lgx-maroon);
        text-decoration: none;
    }

    .lgx-breadcrumb a:hover {
        text-decoration: underline;
    }

    .lgx-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        color: var(--lgx-maroon);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .lgx-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .lgx-card-head .lgx-eyebrow::before,
    .lgx-scanner .lgx-eyebrow::before {
        display: none;
    }

    .lgx-hero h1 {
        margin: 10px 0 0;

        color: var(--lgx-text);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 72px);
        font-weight: 400;
        line-height: 0.92;
        letter-spacing: -0.055em;
    }

    .lgx-hero p {
        max-width: 680px;
        margin: 14px 0 0;

        color: var(--lgx-muted);

        font-size: 13px;
        line-height: 1.75;
    }

    .lgx-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
    }

    .lgx-btn {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 8px;

        padding: 0 16px;

        border: 1px solid transparent;
        border-radius: 14px;

        font-size: 12px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;

        cursor: pointer;
        transition: 160ms ease;
    }

    .lgx-btn:hover {
        transform: translateY(-1px);
    }

    .lgx-btn svg {
        width: 16px;
        height: 16px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .lgx-btn-primary {
        background: var(--lgx-maroon);
        border-color: var(--lgx-maroon);
        color: #FFFFFF;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .lgx-btn-primary:hover {
        background: var(--lgx-maroon-dark);
        border-color: var(--lgx-maroon-dark);
    }

    .lgx-btn-soft {
        background: var(--lgx-card);
        border-color: var(--lgx-tan);
        color: var(--lgx-maroon);
    }

    .lgx-btn-soft:hover {
        background: #F3E4DE;
        border-color: var(--lgx-maroon);
    }

    .lgx-btn-sm {
        min-height: 34px;
        padding-inline: 12px;
        border-radius: 11px;

        font-size: 11px;
    }

    .lgx-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .lgx-metric-card {
        display: flex;
        min-height: 138px;
        flex-direction: column;
        justify-content: space-between;
        gap: 16px;

        padding: 18px;

        border: 1px solid var(--lgx-border);
        border-radius: 22px;

        background:
            radial-gradient(circle at 94% 6%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        color: var(--lgx-text);

        box-shadow: var(--lgx-shadow-soft);
        transition: 160ms ease;
    }

    .lgx-metric-card:hover {
        transform: translateY(-2px);
        border-color: var(--lgx-tan);
        box-shadow: var(--lgx-shadow-card);
    }

    .lgx-metric-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .lgx-icon {
        display: grid;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        place-items: center;

        border: 1px solid #E6C7BE;
        border-radius: 14px;

        background: #F3E4DE;
        color: var(--lgx-maroon);
    }

    .lgx-icon svg {
        width: 18px;
        height: 18px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .lgx-icon.is-success {
        border-color: #CFE8DA;
        background: var(--lgx-success-soft);
        color: var(--lgx-success);
    }

    .lgx-icon.is-warning {
        border-color: #EAD39A;
        background: var(--lgx-warning-soft);
        color: var(--lgx-warning);
    }

    .lgx-icon.is-info {
        border-color: #E6C7BE;
        background: #F3E4DE;
        color: var(--lgx-maroon);
    }

    .lgx-change {
        display: inline-flex;
        min-height: 24px;
        align-items: center;

        padding: 0 8px;

        border: 1px solid #E6C7BE;
        border-radius: 999px;

        background: #F3E4DE;
        color: var(--lgx-maroon);

        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .lgx-change.is-success {
        border-color: #CFE8DA;
        background: var(--lgx-success-soft);
        color: var(--lgx-success);
    }

    .lgx-change.is-warning {
        border-color: #EAD39A;
        background: var(--lgx-warning-soft);
        color: var(--lgx-warning);
    }

    .lgx-metric-label {
        margin: 0;

        color: var(--lgx-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .lgx-metric-value {
        display: block;
        margin-top: 7px;

        color: var(--lgx-text-dark);

        font-size: 30px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .lgx-metric-desc {
        display: block;
        margin-top: 6px;

        color: var(--lgx-muted);

        font-size: 11px;
        font-weight: 800;
    }

    .lgx-card {
        overflow: hidden;

        border: 1px solid var(--lgx-border);
        border-radius: 24px;

        background: var(--lgx-card);
        color: var(--lgx-text);

        box-shadow: var(--lgx-shadow-soft);
    }

    .lgx-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding: 20px 22px;

        border-bottom: 1px solid var(--lgx-border);

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .lgx-card-head h2 {
        margin: 7px 0 0;

        color: var(--lgx-text);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 32px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .lgx-card-head p {
        margin: 8px 0 0;

        color: var(--lgx-muted);

        font-size: 12px;
        line-height: 1.65;
    }

    .lgx-operations-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;

        padding: 18px;
    }

    .lgx-operation-link {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;

        padding: 14px;

        border: 1px solid var(--lgx-border);
        border-radius: 18px;

        background:
            radial-gradient(circle at 96% 8%, rgba(193, 151, 113, 0.12), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        color: var(--lgx-text);
        text-decoration: none;

        transition: 160ms ease;
    }

    .lgx-operation-link:hover {
        transform: translateY(-2px);
        border-color: var(--lgx-tan);
        box-shadow: var(--lgx-shadow-card);
    }

    .lgx-operation-link strong {
        display: block;

        color: var(--lgx-text);

        font-size: 12px;
        font-weight: 950;
    }

    .lgx-operation-link small {
        display: block;
        margin-top: 4px;

        color: var(--lgx-muted);

        font-size: 10px;
        font-weight: 800;
    }

    .lgx-operation-arrow {
        color: var(--lgx-maroon);

        font-size: 15px;
        font-weight: 950;

        transition: 160ms ease;
    }

    .lgx-operation-link:hover .lgx-operation-arrow {
        transform: translateX(2px);
    }

    .lgx-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(320px, 0.7fr);
        gap: 18px;
        align-items: start;
    }

    .lgx-lower-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 18px;
        align-items: stretch;
    }

    .lgx-table-wrap {
        overflow-x: auto;
    }

    .lgx-table {
        width: 100%;
        min-width: 780px;

        border-collapse: collapse;
    }

    .lgx-table thead {
        background: var(--lgx-bg-soft);
    }

    .lgx-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--lgx-border);

        color: var(--lgx-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .lgx-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--lgx-brown);

        font-size: 12px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .lgx-table tbody tr:hover td {
        background: var(--lgx-bg-soft);
    }

    .lgx-parcel-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .lgx-parcel-cell strong,
    .lgx-table td strong {
        color: var(--lgx-text);
        font-weight: 950;
    }

    .lgx-parcel-cell span,
    .lgx-destination span {
        display: block;
        margin-top: 4px;

        color: var(--lgx-muted);

        font-size: 10px;
        font-weight: 800;
    }

    .lgx-status {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        gap: 7px;

        padding: 0 10px;

        border: 1px solid transparent;
        border-radius: 999px;

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .lgx-status::before {
        width: 7px;
        height: 7px;

        border-radius: 999px;

        background: currentColor;

        content: "";
    }

    .lgx-status.is-primary,
    .lgx-status.is-info {
        background: #F3E4DE;
        border-color: #E6C7BE;
        color: var(--lgx-maroon);
    }

    .lgx-status.is-success {
        background: var(--lgx-success-soft);
        border-color: #CFE8DA;
        color: var(--lgx-success);
    }

    .lgx-status.is-warning {
        background: var(--lgx-warning-soft);
        border-color: #EAD39A;
        color: var(--lgx-warning);
    }

    .lgx-rider-list {
        display: grid;
    }

    .lgx-area-card {
        padding: 16px 20px;

        border-bottom: 1px solid var(--lgx-border);
    }

    .lgx-area-card:last-child {
        border-bottom: 0;
    }

    .lgx-area-row {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;
    }

    .lgx-area-code {
        display: grid;
        width: 40px;
        height: 40px;
        place-items: center;

        border-radius: 14px;

        background: #F3E4DE;
        color: var(--lgx-maroon);

        font-size: 12px;
        font-weight: 950;
    }

    .lgx-area-card strong {
        display: block;

        color: var(--lgx-text);

        font-size: 12px;
        font-weight: 950;
    }

    .lgx-area-card small {
        display: block;
        margin-top: 4px;

        color: var(--lgx-muted);

        font-size: 10px;
        font-weight: 800;
    }

    .lgx-area-count {
        text-align: right;
    }

    .lgx-area-count strong {
        font-size: 14px;
    }

    .lgx-area-count span {
        color: var(--lgx-success);

        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .lgx-progress {
        display: block;
        height: 8px;
        margin-top: 12px;
        overflow: hidden;

        border-radius: 999px;

        background: #E7D9CB;
    }

    .lgx-progress i {
        display: block;
        height: 100%;

        border-radius: inherit;

        background: linear-gradient(90deg, var(--lgx-maroon), var(--lgx-tan));
    }

    .lgx-card-foot {
        padding: 14px 20px;

        border-top: 1px solid var(--lgx-border);

        background: var(--lgx-bg-soft);
    }

    .lgx-card-foot a {
        display: flex;
        align-items: center;
        justify-content: space-between;

        color: var(--lgx-maroon);

        font-size: 11px;
        font-weight: 950;
        text-decoration: none;
    }

    .lgx-card-foot a:hover {
        text-decoration: underline;
    }

    .lgx-online-pill {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        gap: 7px;

        padding: 0 10px;

        border: 1px solid #CFE8DA;
        border-radius: 999px;

        background: var(--lgx-success-soft);
        color: var(--lgx-success);

        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .lgx-online-pill::before {
        width: 7px;
        height: 7px;

        border-radius: 999px;

        background: currentColor;

        content: "";
    }

    .lgx-scanner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 22px;

        min-height: 220px;
        padding: 28px;

        border-radius: 26px;

        background:
            radial-gradient(circle at 92% 12%, rgba(255, 255, 255, 0.18), transparent 30%),
            linear-gradient(135deg, var(--lgx-maroon) 0%, var(--lgx-maroon-2) 58%, var(--lgx-maroon-dark) 100%);

        color: #FFFFFF;
        box-shadow: 0 18px 44px rgba(86, 28, 23, 0.18);
    }

    .lgx-scanner .lgx-eyebrow {
        color: #F3D8CC;
    }

    .lgx-scanner h2 {
        margin: 14px 0 0;

        color: #FFFFFF;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(36px, 4vw, 56px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .lgx-scanner p {
        max-width: 470px;
        margin: 12px 0 0;

        color: rgba(255, 255, 255, 0.72);

        font-size: 12px;
        line-height: 1.7;
    }

    .lgx-scanner-icon {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;

        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 14px;

        background: rgba(255, 255, 255, 0.10);
        color: #FFFFFF;
    }

    .lgx-scanner-icon svg {
        width: 19px;
        height: 19px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .lgx-scan-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px;

        width: min(500px, 100%);
    }

    .lgx-scan-input {
        position: relative;
        min-width: 0;
    }

    .lgx-scan-input svg {
        position: absolute;
        top: 50%;
        left: 14px;

        width: 16px;
        height: 16px;

        color: var(--lgx-muted);

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;

        transform: translateY(-50%);
    }

    .lgx-scan-input input {
        width: 100%;
        min-height: 46px;
        padding: 0 14px 0 42px;

        border: 1px solid rgba(255, 255, 255, 0.24);
        border-radius: 15px;

        background: #FFFDF9;
        color: var(--lgx-text);

        font-size: 12px;
        font-weight: 850;
        outline: none;
    }

    .lgx-scan-input input:focus {
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.16);
    }

    .lgx-scan-form button {
        min-height: 46px;
        padding: 0 16px;

        border: 1px solid #FFFFFF;
        border-radius: 15px;

        background: #FFFFFF;
        color: var(--lgx-maroon);

        font-size: 12px;
        font-weight: 950;

        cursor: pointer;
        transition: 160ms ease;
    }

    .lgx-scan-form button:hover {
        transform: translateY(-1px);
        background: #F6EFE7;
    }

    .lgx-activity-list {
        padding: 0 20px;
    }

    .lgx-activity-item {
        position: relative;

        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 12px;

        padding: 16px 0;

        border-bottom: 1px solid var(--lgx-border);
    }

    .lgx-activity-item:last-child {
        border-bottom: 0;
    }

    .lgx-activity-dot {
        width: 10px;
        height: 10px;
        margin-top: 5px;

        border-radius: 999px;

        background: var(--lgx-maroon);
        box-shadow: 0 0 0 5px rgba(86, 28, 23, 0.10);
    }

    .lgx-activity-dot.is-success {
        background: var(--lgx-success);
        box-shadow: 0 0 0 5px rgba(37, 111, 74, 0.10);
    }

    .lgx-activity-dot.is-warning {
        background: var(--lgx-warning);
        box-shadow: 0 0 0 5px rgba(154, 91, 17, 0.10);
    }

    .lgx-activity-dot.is-info {
        background: var(--lgx-maroon);
    }

    .lgx-activity-item strong {
        display: block;

        color: var(--lgx-text);

        font-size: 12px;
        font-weight: 950;
    }

    .lgx-activity-item p {
        margin: 5px 0 0;

        color: var(--lgx-muted);

        font-size: 11px;
        line-height: 1.55;
    }

    .lgx-activity-item small {
        display: block;
        margin-top: 6px;

        color: var(--lgx-muted-2);

        font-size: 10px;
        font-weight: 800;
    }

    @media (max-width: 1280px) {
        .lgx-metrics,
        .lgx-operations-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .lgx-main-grid,
        .lgx-lower-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 820px) {
        .lgx-hero,
        .lgx-card-head,
        .lgx-scanner {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .lgx-actions,
        .lgx-actions .lgx-btn,
        .lgx-scan-form {
            width: 100%;
        }

        .lgx-scan-form {
            grid-template-columns: 1fr;
        }

        .lgx-metrics,
        .lgx-operations-grid {
            grid-template-columns: 1fr;
        }
    }

    html.dark .lgx-hero,
    html.dark .lgx-metric-card,
    html.dark .lgx-card,
    html.dark .lgx-card-head,
    html.dark .lgx-operation-link,
    html.dark .lgx-card-foot {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;

        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .lgx-hero h1,
    html.dark .lgx-metric-value,
    html.dark .lgx-card-head h2,
    html.dark .lgx-operation-link strong,
    html.dark .lgx-table td strong,
    html.dark .lgx-parcel-cell strong,
    html.dark .lgx-area-card strong,
    html.dark .lgx-activity-item strong {
        color: #F5EFE8 !important;
    }

    html.dark .lgx-hero p,
    html.dark .lgx-breadcrumb,
    html.dark .lgx-metric-label,
    html.dark .lgx-metric-desc,
    html.dark .lgx-card-head p,
    html.dark .lgx-operation-link small,
    html.dark .lgx-table td,
    html.dark .lgx-parcel-cell span,
    html.dark .lgx-destination span,
    html.dark .lgx-area-card small,
    html.dark .lgx-activity-item p,
    html.dark .lgx-activity-item small {
        color: #C8B7AD !important;
    }

    html.dark .lgx-eyebrow,
    html.dark .lgx-breadcrumb a,
    html.dark .lgx-card-foot a {
        color: #EBA99D !important;
    }

    html.dark .lgx-eyebrow::before {
        background: #EBA99D !important;
    }

    html.dark .lgx-btn-primary {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .lgx-btn-soft,
    html.dark .lgx-icon,
    html.dark .lgx-area-code,
    html.dark .lgx-table thead,
    html.dark .lgx-scan-input input {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .lgx-table th,
    html.dark .lgx-table td,
    html.dark .lgx-area-card,
    html.dark .lgx-activity-item {
        border-color: #3B2E27 !important;
    }

    html.dark .lgx-table tbody tr:hover td {
        background: #2D1414 !important;
    }

    html.dark .lgx-progress {
        background: #2D2520 !important;
    }
</style>

<div class="lgx-dashboard">
    <section class="lgx-hero">
        <div>
            <nav class="lgx-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('logistics.dashboard') }}">
                    Dashboard
                </a>

                <span>/</span>

                <span>
                    Logistics Center
                </span>
            </nav>

            <span class="lgx-eyebrow">
                Logistics Dashboard
            </span>

            <h1>
                Logistics Overview
            </h1>

            <p>
                Welcome back. Monitor receiving, sorting, rider assignment, and delivery movement across the sorting center.
            </p>
        </div>

        <div class="lgx-actions">
            <a href="{{ route('logistics.parcels.tracking') }}" class="lgx-btn lgx-btn-soft">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $iconPaths['search'] !!}
                </svg>

                Track Parcel
            </a>

            <a href="{{ route('logistics.parcels.receive') }}" class="lgx-btn lgx-btn-primary">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $iconPaths['plus'] !!}
                </svg>

                Receive Parcel
            </a>
        </div>
    </section>

    <section class="lgx-metrics" aria-label="Logistics summary">
        @foreach ($stats as $stat)
            @php
                $toneClass = match ($stat['type']) {
                    'success' => 'is-success',
                    'warning' => 'is-warning',
                    'info' => 'is-info',
                    default => 'is-primary',
                };
            @endphp

            <article class="lgx-metric-card">
                <div class="lgx-metric-top">
                    <span class="lgx-icon {{ $toneClass }}" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $iconPaths[$stat['icon']] ?? $iconPaths['box'] !!}
                        </svg>
                    </span>

                    <span class="lgx-change {{ $toneClass }}">
                        {{ $stat['change'] }}
                    </span>
                </div>

                <div>
                    <p class="lgx-metric-label">
                        {{ $stat['label'] }}
                    </p>

                    <strong class="lgx-metric-value">
                        {{ $stat['value'] }}
                    </strong>

                    <span class="lgx-metric-desc">
                        {{ $stat['description'] }}
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="lgx-card">
        <header class="lgx-card-head">
            <div>
                <span class="lgx-eyebrow">
                    Quick Operations
                </span>

                <h2>
                    Common logistics tasks
                </h2>

                <p>
                    Jump directly to the most used sorting center workflows.
                </p>
            </div>
        </header>

        <div class="lgx-operations-grid">
            @foreach ($operations as $operation)
                @php
                    $toneClass = match ($operation['type']) {
                        'success' => 'is-success',
                        'warning' => 'is-warning',
                        'info' => 'is-info',
                        default => 'is-primary',
                    };
                @endphp

                <a href="{{ $operation['href'] }}" class="lgx-operation-link">
                    <span class="lgx-icon {{ $toneClass }}" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $iconPaths[$operation['icon']] ?? $iconPaths['box'] !!}
                        </svg>
                    </span>

                    <span>
                        <strong>
                            {{ $operation['label'] }}
                        </strong>

                        <small>
                            {{ $operation['description'] }}
                        </small>
                    </span>

                    <span class="lgx-operation-arrow">
                        →
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="lgx-main-grid">
        <section class="lgx-card">
            <header class="lgx-card-head">
                <div>
                    <span class="lgx-eyebrow">
                        Parcel Movement
                    </span>

                    <h2>
                        Recent Parcels
                    </h2>

                    <p>
                        Latest parcels processed by the sorting center.
                    </p>
                </div>

                <a href="{{ route('logistics.parcels') }}" class="lgx-btn lgx-btn-soft lgx-btn-sm">
                    View All
                </a>
            </header>

            <div class="lgx-table-wrap">
                <table class="lgx-table">
                    <thead>
                        <tr>
                            <th>Parcel</th>
                            <th>Customer</th>
                            <th>Destination</th>
                            <th>Status</th>
                            <th>Time</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($recentParcels as $parcel)
                            @php
                                $statusClass = match ($parcel['status_type']) {
                                    'success' => 'is-success',
                                    'warning' => 'is-warning',
                                    'info' => 'is-info',
                                    default => 'is-primary',
                                };
                            @endphp

                            <tr>
                                <td>
                                    <div class="lgx-parcel-cell">
                                        <span class="lgx-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24">
                                                {!! $iconPaths['box'] !!}
                                            </svg>
                                        </span>

                                        <div>
                                            <strong>
                                                {{ $parcel['tracking'] }}
                                            </strong>

                                            <span>
                                                Parcel #{{ $parcel['id'] }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <strong>
                                        {{ $parcel['buyer'] }}
                                    </strong>
                                </td>

                                <td>
                                    <div class="lgx-destination">
                                        <strong>
                                            {{ $parcel['destination'] }}
                                        </strong>

                                        <span>
                                            {{ $parcel['area'] }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <span class="lgx-status {{ $statusClass }}">
                                        {{ $parcel['status'] }}
                                    </span>
                                </td>

                                <td>
                                    {{ $parcel['time'] }}
                                </td>

                                <td style="text-align: right;">
                                    <a
                                        href="{{ route('logistics.parcels.show', $parcel['id']) }}"
                                        class="lgx-btn lgx-btn-soft lgx-btn-sm"
                                    >
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="lgx-card">
            <header class="lgx-card-head">
                <div>
                    <span class="lgx-eyebrow">
                        Rider Availability
                    </span>

                    <h2>
                        Delivery Areas
                    </h2>

                    <p>
                        Available riders by assigned area.
                    </p>
                </div>

                <span class="lgx-online-pill">
                    14 Online
                </span>
            </header>

            <div class="lgx-rider-list">
                @foreach ($areas as $area)
                    @php
                        $percentage = ($area['available'] / max($area['total'], 1)) * 100;
                    @endphp

                    <article class="lgx-area-card">
                        <div class="lgx-area-row">
                            <span class="lgx-area-code">
                                {{ $area['code'] }}
                            </span>

                            <div>
                                <strong>
                                    {{ $area['area'] }}
                                </strong>

                                <small>
                                    {{ $area['municipality'] }}
                                </small>
                            </div>

                            <div class="lgx-area-count">
                                <strong>
                                    {{ $area['available'] }}/{{ $area['total'] }}
                                </strong>

                                <span>
                                    available
                                </span>
                            </div>
                        </div>

                        <span class="lgx-progress" aria-hidden="true">
                            <i style="width: {{ $percentage }}%"></i>
                        </span>
                    </article>
                @endforeach
            </div>

            <footer class="lgx-card-foot">
                <a href="{{ route('logistics.riders') }}">
                    Manage Riders
                    <span>→</span>
                </a>
            </footer>
        </aside>
    </section>

    <section class="lgx-lower-grid">
        <section class="lgx-scanner">
            <div>
                <span class="lgx-scanner-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        {!! $iconPaths['scan'] !!}
                    </svg>
                </span>

                <span class="lgx-eyebrow">
                    Quick Scanner
                </span>

                <h2>
                    Scan a parcel instantly
                </h2>

                <p>
                    Enter or scan a tracking number to quickly locate and process a parcel in the logistics center.
                </p>
            </div>

            <div class="lgx-scan-form">
                <div class="lgx-scan-input">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        {!! $iconPaths['search'] !!}
                    </svg>

                    <input
                        type="text"
                        id="trackingNumber"
                        placeholder="LH-2026-1001"
                        value="LH-2026-1001"
                    >
                </div>

                <button type="button" id="scanParcelBtn">
                    Scan Parcel →
                </button>
            </div>
        </section>

        <aside class="lgx-card">
            <header class="lgx-card-head">
                <div>
                    <span class="lgx-eyebrow">
                        Live Updates
                    </span>

                    <h2>
                        Recent Activity
                    </h2>

                    <p>
                        Latest logistics updates.
                    </p>
                </div>
            </header>

            <div class="lgx-activity-list">
                @foreach ($activity as $item)
                    @php
                        $dotClass = match ($item['type']) {
                            'success' => 'is-success',
                            'warning' => 'is-warning',
                            'info' => 'is-info',
                            default => 'is-primary',
                        };
                    @endphp

                    <article class="lgx-activity-item">
                        <span class="lgx-activity-dot {{ $dotClass }}" aria-hidden="true"></span>

                        <div>
                            <strong>
                                {{ $item['title'] }}
                            </strong>

                            <p>
                                {{ $item['description'] }}
                            </p>

                            <small>
                                {{ $item['time'] }}
                            </small>
                        </div>
                    </article>
                @endforeach
            </div>
        </aside>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const scanButton = document.getElementById('scanParcelBtn');
    const trackingInput = document.getElementById('trackingNumber');

    function scanParcel() {
        if (!trackingInput) {
            return;
        }

        const value = trackingInput.value.trim();

        if (!value) {
            trackingInput.focus();
            return;
        }

        window.location.href =
            "{{ route('logistics.parcels.tracking') }}"
            + '?tracking='
            + encodeURIComponent(value);
    }

    scanButton?.addEventListener('click', scanParcel);

    trackingInput?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            scanParcel();
        }
    });
});
</script>
@endpush