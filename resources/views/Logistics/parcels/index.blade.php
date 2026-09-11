@extends('logistics.app')

@section('title', 'All Parcels — LIKHAE Logistics')

@php
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
            'description' => 'Total parcel records',
            'tone' => 'primary',
            'icon' => 'parcel',
        ],
        [
            'label' => 'Waiting Sorting',
            'value' => 34,
            'description' => 'Pending sorting',
            'tone' => 'warning',
            'icon' => 'sorting',
        ],
        [
            'label' => 'Awaiting Rider',
            'value' => 18,
            'description' => 'Ready for assignment',
            'tone' => 'primary',
            'icon' => 'rider',
        ],
        [
            'label' => 'On the Road',
            'value' => 76,
            'description' => 'Active deliveries',
            'tone' => 'primary',
            'icon' => 'truck',
        ],
        [
            'label' => 'Delivered',
            'value' => 156,
            'description' => 'Successfully delivered',
            'tone' => 'success',
            'icon' => 'check',
        ],
    ];

    $icons = [
        'parcel' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',
        'sorting' => '
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
        'check' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="m8 12 2.7 2.7L16.5 9"/>
        ',
        'plus' => '
            <path d="M12 5v14"/>
            <path d="M5 12h14"/>
        ',
        'search' => '
            <circle cx="11" cy="11" r="7"/>
            <path d="m20 20-3.5-3.5"/>
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
        --ap-bg: #FBF7F2;
        --ap-bg-soft: #F6EFE7;
        --ap-bg-warm: #F3E4DE;
        --ap-card: #FFFDF9;

        --ap-border: #EADCCC;
        --ap-border-strong: #DBCEC1;

        --ap-maroon: #561C17;
        --ap-maroon-2: #642920;
        --ap-maroon-dark: #3E130F;

        --ap-text: #3B211B;
        --ap-text-dark: #1C160F;
        --ap-brown: #6C4936;
        --ap-muted: #987865;
        --ap-muted-light: #A99386;

        --ap-tan: #C19771;

        --ap-success: #256F4A;
        --ap-success-soft: #EAF7EF;

        --ap-warning: #9A5B11;
        --ap-warning-soft: #FFF6DE;

        --ap-shadow: 0 8px 24px rgba(86, 28, 23, 0.055);
        --ap-shadow-hover: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ap-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--ap-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .ap-page * {
        box-sizing: border-box;
    }

    .ap-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        color: var(--ap-muted);
        font-size: 10px;
        font-weight: 750;
    }

    .ap-breadcrumb a {
        color: var(--ap-muted);
        text-decoration: none;
        transition: 150ms ease;
    }

    .ap-breadcrumb a:hover {
        color: var(--ap-maroon);
    }

    .ap-breadcrumb strong {
        color: var(--ap-text);
        font-weight: 900;
    }

    .ap-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--ap-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193, 151, 113, 0.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86, 28, 23, 0.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--ap-shadow);
    }

    .ap-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--ap-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .ap-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .ap-hero h1 {
        margin: 10px 0 0;
        color: var(--ap-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .ap-hero p {
        max-width: 700px;
        margin: 13px 0 0;
        color: var(--ap-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .ap-btn {
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

    .ap-btn:hover {
        transform: translateY(-1px);
    }

    .ap-btn svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ap-btn-primary {
        background: var(--ap-maroon);
        border-color: var(--ap-maroon);
        color: #FFFFFF;
        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.14);
    }

    .ap-btn-primary:hover {
        background: var(--ap-maroon-dark);
        border-color: var(--ap-maroon-dark);
    }

    .ap-btn-soft {
        background: var(--ap-card);
        border-color: var(--ap-border);
        color: var(--ap-maroon);
    }

    .ap-btn-soft:hover {
        background: var(--ap-bg-warm);
        border-color: var(--ap-tan);
    }

    .ap-btn-sm {
        min-height: 32px;
        padding: 0 11px;
        border-radius: 10px;
        font-size: 9px;
    }

    .ap-overview {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
    }

    .ap-stat {
        display: flex;
        min-width: 0;
        min-height: 128px;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        padding: 16px;
        border: 1px solid var(--ap-border);
        border-radius: 20px;
        background:
            radial-gradient(circle at 94% 6%, rgba(193, 151, 113, 0.14), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
        box-shadow: var(--ap-shadow);
        transition: 160ms ease;
    }

    .ap-stat:hover {
        transform: translateY(-2px);
        border-color: var(--ap-tan);
        box-shadow: var(--ap-shadow-hover);
    }

    .ap-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .ap-stat-icon {
        display: grid;
        width: 38px;
        height: 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 13px;
        background: var(--ap-bg-warm);
        color: var(--ap-maroon);
    }

    .ap-stat-icon.is-warning {
        border-color: #EAD39A;
        background: var(--ap-warning-soft);
        color: var(--ap-warning);
    }

    .ap-stat-icon.is-success {
        border-color: #CFE8DA;
        background: var(--ap-success-soft);
        color: var(--ap-success);
    }

    .ap-stat-icon svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.75;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ap-stat-label {
        display: block;
        color: var(--ap-muted);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .ap-stat-value {
        display: block;
        margin-top: 6px;
        color: var(--ap-text-dark);
        font-size: 28px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.045em;
    }

    .ap-stat-description {
        display: block;
        margin-top: 6px;
        color: var(--ap-muted);
        font-size: 9px;
        font-weight: 700;
    }

    .ap-card {
        overflow: hidden;
        border: 1px solid var(--ap-border);
        border-radius: 22px;
        background: var(--ap-card);
        box-shadow: var(--ap-shadow);
    }

    .ap-toolbar {
        padding: 15px;
    }

    .ap-toolbar-grid {
        display: grid;
        grid-template-columns: minmax(250px, 1fr) 190px 170px auto;
        gap: 10px;
    }

    .ap-field {
        position: relative;
        min-width: 0;
    }

    .ap-field-icon {
        position: absolute;
        top: 50%;
        left: 13px;
        width: 16px;
        height: 16px;
        color: var(--ap-muted);
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .ap-input,
    .ap-select {
        width: 100%;
        min-height: 40px;
        border: 1px solid var(--ap-border);
        border-radius: 12px;
        background: var(--ap-bg-soft);
        color: var(--ap-text);
        font-size: 10px;
        font-weight: 750;
        outline: none;
        transition: 150ms ease;
    }

    .ap-input {
        padding: 0 14px 0 40px;
    }

    .ap-select {
        padding: 0 12px;
    }

    .ap-input::placeholder {
        color: var(--ap-muted-light);
    }

    .ap-input:focus,
    .ap-select:focus {
        border-color: var(--ap-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.07);
    }

    .ap-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--ap-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ap-card-head h2 {
        margin: 0;
        color: var(--ap-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .ap-card-head p {
        margin: 7px 0 0;
        color: var(--ap-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .ap-live {
        display: inline-flex;
        min-height: 27px;
        align-items: center;
        gap: 6px;
        padding: 0 10px;
        border: 1px solid #CFE8DA;
        border-radius: 999px;
        background: var(--ap-success-soft);
        color: var(--ap-success);
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .ap-live::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .ap-table-wrap {
        overflow-x: auto;
    }

    .ap-table {
        width: 100%;
        min-width: 1080px;
        border-collapse: collapse;
    }

    .ap-table thead {
        background: var(--ap-bg-soft);
    }

    .ap-table th {
        padding: 12px 14px;
        border-bottom: 1px solid var(--ap-border);
        color: var(--ap-muted);
        font-size: 8px;
        font-weight: 950;
        letter-spacing: .10em;
        text-align: left;
        text-transform: uppercase;
    }

    .ap-table th:first-child,
    .ap-table td:first-child {
        padding-left: 20px;
    }

    .ap-table th:last-child,
    .ap-table td:last-child {
        padding-right: 20px;
    }

    .ap-table td {
        padding: 14px;
        border-bottom: 1px solid #EFE1D5;
        color: var(--ap-brown);
        font-size: 10px;
        vertical-align: middle;
    }

    .ap-table tbody tr {
        transition: 150ms ease;
    }

    .ap-table tbody tr:hover td {
        background: var(--ap-bg-soft);
    }

    .ap-parcel {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .ap-parcel-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--ap-bg-warm);
        color: var(--ap-maroon);
    }

    .ap-parcel-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ap-tracking {
        display: block;
        color: var(--ap-text);
        font-size: 10px;
        font-weight: 950;
        text-decoration: none;
    }

    .ap-tracking:hover {
        color: var(--ap-maroon);
    }

    .ap-order {
        display: block;
        margin-top: 4px;
        color: var(--ap-muted);
        font-size: 8px;
        font-weight: 700;
    }

    .ap-person {
        color: var(--ap-text);
        font-weight: 850;
    }

    .ap-destination {
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--ap-brown);
    }

    .ap-destination svg {
        width: 12px;
        height: 12px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .ap-area {
        display: inline-flex;
        min-height: 24px;
        align-items: center;
        padding: 0 9px;
        border: 1px solid var(--ap-border);
        border-radius: 999px;
        background: var(--ap-bg-soft);
        color: var(--ap-brown);
        font-size: 8px;
        font-weight: 850;
        white-space: nowrap;
    }

    .ap-rider {
        color: var(--ap-text);
        font-size: 9px;
        font-weight: 800;
    }

    .ap-rider.is-unassigned {
        color: var(--ap-muted);
        font-weight: 700;
    }

    .ap-status {
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

    .ap-status::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .ap-status.is-warning {
        border-color: #EAD39A;
        background: var(--ap-warning-soft);
        color: var(--ap-warning);
    }

    .ap-status.is-maroon {
        border-color: #E6C7BE;
        background: var(--ap-bg-warm);
        color: var(--ap-maroon);
    }

    .ap-status.is-success {
        border-color: #CFE8DA;
        background: var(--ap-success-soft);
        color: var(--ap-success);
    }

    .ap-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 7px;
    }

    .ap-empty {
        display: none;
        padding: 44px 20px;
        border-top: 1px solid var(--ap-border);
        text-align: center;
    }

    .ap-empty.is-visible {
        display: block;
    }

    .ap-empty-icon {
        display: grid;
        width: 52px;
        height: 52px;
        place-items: center;
        margin-inline: auto;
        border-radius: 16px;
        background: var(--ap-bg-soft);
        color: var(--ap-maroon);
    }

    .ap-empty-icon svg {
        width: 20px;
        height: 20px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
    }

    .ap-empty h3 {
        margin: 13px 0 0;
        color: var(--ap-text);
        font-size: 11px;
        font-weight: 950;
    }

    .ap-empty p {
        margin: 5px 0 0;
        color: var(--ap-muted);
        font-size: 9px;
    }

    @media (max-width: 1280px) {
        .ap-overview {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .ap-toolbar-grid {
            grid-template-columns: minmax(220px,1fr) 180px 160px;
        }

        .ap-toolbar-grid .ap-reset {
            grid-column: 1 / -1;
            width: fit-content;
        }
    }

    @media (max-width: 860px) {
        .ap-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .ap-hero .ap-btn {
            width: 100%;
        }

        .ap-overview {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ap-toolbar-grid {
            grid-template-columns: 1fr 1fr;
        }

        .ap-toolbar-grid .ap-field:first-child {
            grid-column: 1 / -1;
        }

        .ap-toolbar-grid .ap-reset {
            width: 100%;
        }

        .ap-card-head {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (max-width: 560px) {
        .ap-overview,
        .ap-toolbar-grid {
            grid-template-columns: 1fr;
        }

        .ap-toolbar-grid .ap-field:first-child,
        .ap-toolbar-grid .ap-reset {
            grid-column: auto;
        }
    }

    html.dark .ap-page {
        color: #F5EFE8;
    }

    html.dark .ap-hero,
    html.dark .ap-stat,
    html.dark .ap-card {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .ap-card-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .ap-hero h1,
    html.dark .ap-stat-value,
    html.dark .ap-card-head h2,
    html.dark .ap-tracking,
    html.dark .ap-person,
    html.dark .ap-rider,
    html.dark .ap-empty h3 {
        color: #F5EFE8 !important;
    }

    html.dark .ap-hero p,
    html.dark .ap-breadcrumb,
    html.dark .ap-stat-label,
    html.dark .ap-stat-description,
    html.dark .ap-card-head p,
    html.dark .ap-order,
    html.dark .ap-rider.is-unassigned,
    html.dark .ap-empty p {
        color: #AFA19A !important;
    }

    html.dark .ap-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .ap-input,
    html.dark .ap-select {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ap-input:focus,
    html.dark .ap-select:focus {
        background: #211B17 !important;
        border-color: #60463A !important;
    }

    html.dark .ap-table thead {
        background: #1D1715 !important;
    }

    html.dark .ap-table th,
    html.dark .ap-table td {
        border-color: #30231F !important;
    }

    html.dark .ap-table th {
        color: #AFA19A !important;
    }

    html.dark .ap-table td {
        color: #D0C4BD !important;
    }

    html.dark .ap-table tbody tr:hover td {
        background: #241817 !important;
    }

    html.dark .ap-parcel-icon,
    html.dark .ap-stat-icon,
    html.dark .ap-area {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .ap-status.is-maroon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .ap-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .ap-btn-primary:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }

    html.dark .ap-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }
</style>

<div class="ap-page">

    <nav class="ap-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('logistics.dashboard') }}">Dashboard</a>
        <span>/</span>
        <strong>Parcels</strong>
    </nav>

    <section class="ap-hero">
        <div>
            <span class="ap-eyebrow">Parcel Management</span>

            <h1>Every parcel, in one place.</h1>

            <p>
                Review parcels entering the sorting center, monitor their current
                delivery status, and continue the next logistics action.
            </p>
        </div>

        <a
            href="{{ route('logistics.parcels.receive') }}"
            class="ap-btn ap-btn-primary"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['plus'] !!}
            </svg>

            Receive Parcel
        </a>
    </section>

    <section class="ap-overview" aria-label="Parcel overview">
        @foreach($overview as $item)
            @php
                $toneClass = match($item['tone']) {
                    'warning' => 'is-warning',
                    'success' => 'is-success',
                    default => '',
                };
            @endphp

            <article class="ap-stat">
                <div class="ap-stat-top">
                    <span class="ap-stat-icon {{ $toneClass }}" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons[$item['icon']] !!}
                        </svg>
                    </span>
                </div>

                <div>
                    <span class="ap-stat-label">{{ $item['label'] }}</span>
                    <strong class="ap-stat-value">{{ $item['value'] }}</strong>
                    <span class="ap-stat-description">{{ $item['description'] }}</span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="ap-card ap-toolbar">
        <div class="ap-toolbar-grid">
            <div class="ap-field">
                <svg viewBox="0 0 24 24" class="ap-field-icon" aria-hidden="true">
                    {!! $icons['search'] !!}
                </svg>

                <input
                    type="text"
                    id="parcelSearch"
                    class="ap-input"
                    placeholder="Search tracking, order, buyer..."
                    autocomplete="off"
                >
            </div>

            <div class="ap-field">
                <select id="statusFilter" class="ap-select" aria-label="Filter by status">
                    <option value="all">All Statuses</option>
                    <option value="waiting_sorting">Waiting for Sorting</option>
                    <option value="awaiting_rider">Awaiting Rider</option>
                    <option value="assigned">Rider Assigned</option>
                    <option value="out_for_delivery">Out for Delivery</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>

            <div class="ap-field">
                <select id="areaFilter" class="ap-select" aria-label="Filter by area">
                    <option value="all">All Areas</option>
                    <option value="unassigned">Unassigned</option>
                    <option value="area a">Area A</option>
                    <option value="area b">Area B</option>
                    <option value="area c">Area C</option>
                    <option value="area d">Area D</option>
                </select>
            </div>

            <button
                type="button"
                id="resetFilters"
                class="ap-btn ap-btn-soft ap-reset"
            >
                Reset Filters
            </button>
        </div>
    </section>

    <section class="ap-card">
        <header class="ap-card-head">
            <div>
                <h2>Parcel Directory</h2>

                <p>
                    Showing
                    <strong id="visibleParcelCount">{{ count($parcels) }}</strong>
                    parcel records.
                </p>
            </div>

            <span class="ap-live">Operations Active</span>
        </header>

        <div class="ap-table-wrap">
            <table class="ap-table">
                <thead>
                    <tr>
                        <th>Parcel</th>
                        <th>Buyer</th>
                        <th>Destination</th>
                        <th>Area</th>
                        <th>Rider</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>

                <tbody id="parcelTableBody">
                    @foreach($parcels as $parcel)
                        @php
                            $statusClass = match($parcel['status_key']) {
                                'waiting_sorting' => 'is-warning',
                                'awaiting_rider' => 'is-maroon',
                                'assigned' => 'is-maroon',
                                'out_for_delivery' => 'is-maroon',
                                'delivered' => 'is-success',
                                default => 'is-maroon',
                            };

                            $nextAction = match($parcel['status_key']) {
                                'waiting_sorting' => [
                                    'label' => 'Sort',
                                    'route' => route(
                                        'logistics.sorting'
                                    ),
                                ],

                                'awaiting_rider' => [
                                    'label' => 'Assign',
                                    'route' => route(
                                        'logistics.assignments'
                                    ),
                                ],

                                'assigned',
                                'out_for_delivery' => [
                                    'label' => 'Track',
                                    'route' => route(
                                        'logistics.parcels.tracking'
                                    ),
                                ],

                                default => [
                                    'label' => 'View',
                                    'route' => route(
                                        'logistics.parcels.show',
                                        $parcel['id']
                                    ),
                                ],
                            };
                        @endphp

                        <tr
                            class="parcel-row"
                            data-search="{{ mb_strtolower(
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
                            data-area="{{ mb_strtolower($parcel['area']) }}"
                        >
                            <td>
                                <div class="ap-parcel">
                                    <span class="ap-parcel-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24">
                                            {!! $icons['parcel'] !!}
                                        </svg>
                                    </span>

                                    <div>
                                        <a
                                            href="{{ route('logistics.parcels.show', $parcel['id']) }}"
                                            class="ap-tracking"
                                        >
                                            {{ $parcel['tracking'] }}
                                        </a>

                                        <span class="ap-order">
                                            {{ $parcel['order'] }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="ap-person">
                                    {{ $parcel['buyer'] }}
                                </span>
                            </td>

                            <td>
                                <span class="ap-destination">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        {!! $icons['location'] !!}
                                    </svg>

                                    {{ $parcel['destination'] }}
                                </span>
                            </td>

                            <td>
                                <span class="ap-area">
                                    {{ $parcel['area'] }}
                                </span>
                            </td>

                            <td>
                                <span class="ap-rider {{ $parcel['rider'] === 'Not assigned' ? 'is-unassigned' : '' }}">
                                    {{ $parcel['rider'] }}
                                </span>
                            </td>

                            <td>
                                <span class="ap-status {{ $statusClass }}">
                                    {{ $parcel['status'] }}
                                </span>
                            </td>

                            <td>
                                {{ $parcel['received'] }}
                            </td>

                            <td>
                                <div class="ap-actions">
                                    <a
                                        href="{{ route('logistics.parcels.show', $parcel['id']) }}"
                                        class="ap-btn ap-btn-soft ap-btn-sm"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="{{ $nextAction['route'] }}"
                                        class="ap-btn ap-btn-primary ap-btn-sm"
                                    >
                                        {{ $nextAction['label'] }}

                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            {!! $icons['arrow'] !!}
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div id="parcelEmptyState" class="ap-empty">
            <span class="ap-empty-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['search'] !!}
                </svg>
            </span>

            <h3>No parcels found</h3>

            <p>
                Try changing the search term, status, or delivery area.
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

    const visibleCount =
        document.getElementById('visibleParcelCount');

    const emptyState =
        document.getElementById('parcelEmptyState');

    const rows =
        Array.from(
            document.querySelectorAll('.parcel-row')
        );


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


        rows.forEach(function (row) {
            const rowSearch =
                row.dataset.search || '';

            const rowStatus =
                row.dataset.status || '';

            const rowArea =
                row.dataset.area || '';

            const matchesSearch =
                !search ||
                rowSearch.includes(search);

            const matchesStatus =
                status === 'all' ||
                rowStatus === status;

            const matchesArea =
                area === 'all' ||
                rowArea === area;

            const show =
                matchesSearch &&
                matchesStatus &&
                matchesArea;

            row.hidden = !show;

            if (show) {
                visible++;
            }
        });


        if (visibleCount) {
            visibleCount.textContent =
                String(visible);
        }


        if (emptyState) {
            emptyState.classList.toggle(
                'is-visible',
                visible === 0
            );
        }
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

            searchInput?.focus();
        }
    );


    applyFilters();
});
</script>
@endpush
