@extends('logistics.app')

@section('title', 'Rider Profile — LIKHAE Logistics')

@php
    $profile = [
        'id' => data_get($rider ?? null, 'id', 1),
        'code' => data_get($rider ?? null, 'code', 'RID-0001'),
        'name' => data_get($rider ?? null, 'name', 'Juan Dela Cruz'),
        'email' => data_get($rider ?? null, 'email', 'juan@email.com'),
        'contact' => data_get($rider ?? null, 'contact', '0917 555 1234'),
        'address' => data_get($rider ?? null, 'address', 'Santa Cruz, Laguna'),
        'birthday' => data_get($rider ?? null, 'birthday', 'January 10, 1998'),
        'sex' => data_get($rider ?? null, 'sex', 'Male'),
        'area' => data_get($rider ?? null, 'area', 'Area A'),
        'status' => data_get($rider ?? null, 'status', 'Active'),
        'vehicle' => data_get($rider ?? null, 'vehicle', 'Motorcycle'),
        'plate' => data_get($rider ?? null, 'plate', 'ABC-1234'),
        'orcr_status' => data_get($rider ?? null, 'orcr_status', 'Verified'),
        'license_status' => data_get($rider ?? null, 'license_status', 'Verified'),
        'completed' => data_get($rider ?? null, 'completed_deliveries', 342),
        'rating' => data_get($rider ?? null, 'rating', '4.9'),
        'success_rate' => data_get($rider ?? null, 'success_rate', '98%'),
    ];

    $initials = collect(preg_split('/\s+/', trim($profile['name'])))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'RD';

    $personalInfo = [
        ['label' => 'Full Name', 'value' => $profile['name']],
        ['label' => 'Email', 'value' => $profile['email']],
        ['label' => 'Contact', 'value' => $profile['contact']],
        ['label' => 'Address', 'value' => $profile['address']],
        ['label' => 'Birthday', 'value' => $profile['birthday']],
        ['label' => 'Sex', 'value' => $profile['sex']],
    ];

    $vehicleInfo = [
        ['label' => 'Vehicle Type', 'value' => $profile['vehicle']],
        ['label' => 'Plate Number', 'value' => $profile['plate']],
        ['label' => 'OR / CR Status', 'value' => $profile['orcr_status']],
        ['label' => 'Driver License', 'value' => $profile['license_status']],
    ];

    $documents = [
        [
            'key' => 'orcr',
            'title' => 'OR / CR',
            'description' => 'Vehicle registration and ownership record.',
            'status' => $profile['orcr_status'],
        ],
        [
            'key' => 'license',
            'title' => 'Driver License / ID',
            'description' => 'Identity and driving eligibility document.',
            'status' => $profile['license_status'],
        ],
    ];

    $performance = [
        [
            'label' => 'Completed Deliveries',
            'value' => $profile['completed'],
            'description' => 'Successful completed orders',
            'icon' => 'package',
        ],
        [
            'label' => 'Rating',
            'value' => $profile['rating'],
            'description' => 'Average delivery rating',
            'icon' => 'star',
        ],
        [
            'label' => 'Success Rate',
            'value' => $profile['success_rate'],
            'description' => 'Successful delivery attempts',
            'icon' => 'check',
        ],
    ];

    $assignedParcels = [
        [
            'tracking' => 'LH-2026-1003',
            'status' => 'Out for Delivery',
            'destination' => 'Santa Cruz, Laguna',
        ],
        [
            'tracking' => 'LH-2026-1015',
            'status' => 'Delivered',
            'destination' => 'Pagsanjan, Laguna',
        ],
        [
            'tracking' => 'LH-2026-1018',
            'status' => 'Pending Pickup',
            'destination' => 'Los Baños, Laguna',
        ],
    ];

    $icons = [
        'rider' => '
            <circle cx="8" cy="7" r="3"/>
            <path d="M3 19c0-3 2-5 5-5"/>
            <path d="M14 7h7"/>
            <path d="M17.5 3.5v7"/>
        ',
        'user' => '
            <circle cx="12" cy="8" r="4"/>
            <path d="M4 21c0-5 3-8 8-8s8 3 8 8"/>
        ',
        'vehicle' => '
            <path d="M5 16h10"/>
            <path d="m9 16 2-5h5l3 5"/>
            <circle cx="7" cy="18" r="2"/>
            <circle cx="18" cy="18" r="2"/>
        ',
        'document' => '
            <path d="M6 3h9l4 4v14H6z"/>
            <path d="M14 3v5h5"/>
            <path d="M9 13h6"/>
            <path d="M9 17h4"/>
        ',
        'eye' => '
            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
            <circle cx="12" cy="12" r="2.5"/>
        ',
        'package' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',
        'star' => '
            <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.8 1-6.1-4.4-4.3 6.1-.9z"/>
        ',
        'check' => '
            <path d="m5 12 4 4 10-10"/>
        ',
        'pause' => '
            <rect x="5" y="4" width="5" height="16" rx="1"/>
            <rect x="14" y="4" width="5" height="16" rx="1"/>
        ',
        'power' => '
            <path d="M12 3v8"/>
            <path d="M6.3 5.8a8 8 0 1 0 11.4 0"/>
        ',
        'location' => '
            <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"/>
            <circle cx="12" cy="10" r="2"/>
        ',
        'close' => '
            <path d="m6 6 12 12"/>
            <path d="m18 6-12 12"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --rf-bg: #FBF7F2;
        --rf-bg-soft: #F6EFE7;
        --rf-bg-warm: #F3E4DE;
        --rf-card: #FFFDF9;

        --rf-border: #EADCCC;
        --rf-border-strong: #DBCEC1;

        --rf-maroon: #561C17;
        --rf-maroon-2: #642920;
        --rf-maroon-dark: #3E130F;

        --rf-text: #3B211B;
        --rf-text-dark: #1C160F;
        --rf-brown: #6C4936;
        --rf-muted: #987865;
        --rf-muted-light: #A99386;

        --rf-tan: #C19771;

        --rf-success: #256F4A;
        --rf-success-soft: #EAF7EF;

        --rf-warning: #9A5B11;
        --rf-warning-soft: #FFF6DE;

        --rf-danger: #B42318;
        --rf-danger-soft: #FCEBE9;

        --rf-shadow: 0 8px 24px rgba(86, 28, 23, 0.055);
        --rf-shadow-hover: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .rf-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--rf-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .rf-page * {
        box-sizing: border-box;
    }

    .rf-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        color: var(--rf-muted);
        font-size: 10px;
        font-weight: 750;
    }

    .rf-breadcrumb a {
        color: var(--rf-muted);
        text-decoration: none;
        transition: 150ms ease;
    }

    .rf-breadcrumb a:hover {
        color: var(--rf-maroon);
    }

    .rf-breadcrumb strong {
        color: var(--rf-text);
        font-weight: 900;
    }

    .rf-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--rf-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--rf-shadow);
    }

    .rf-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--rf-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .rf-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .rf-hero h1 {
        margin: 10px 0 0;
        color: var(--rf-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .rf-hero p {
        max-width: 690px;
        margin: 13px 0 0;
        color: var(--rf-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .rf-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .rf-btn {
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

    .rf-btn:hover {
        transform: translateY(-1px);
    }

    .rf-btn svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rf-btn-primary {
        background: var(--rf-maroon);
        border-color: var(--rf-maroon);
        color: #FFFFFF;
        box-shadow: 0 10px 22px rgba(86,28,23,.14);
    }

    .rf-btn-primary:hover {
        background: var(--rf-maroon-dark);
        border-color: var(--rf-maroon-dark);
    }

    .rf-btn-soft {
        background: var(--rf-card);
        border-color: var(--rf-border);
        color: var(--rf-maroon);
    }

    .rf-btn-soft:hover {
        background: var(--rf-bg-warm);
        border-color: var(--rf-tan);
    }

    .rf-btn-danger {
        background: #FFFFFF;
        border-color: #E2B5B0;
        color: var(--rf-danger);
    }

    .rf-btn-danger:hover {
        background: var(--rf-danger-soft);
        border-color: #D99A93;
    }

    .rf-profile {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 17px;
        padding: 20px;
        border: 1px solid var(--rf-border);
        border-radius: 22px;
        background:
            radial-gradient(circle at 95% 5%, rgba(193,151,113,.11), transparent 28%),
            var(--rf-card);
        box-shadow: var(--rf-shadow);
    }

    .rf-avatar {
        display: grid;
        width: 76px;
        height: 76px;
        place-items: center;
        border-radius: 999px;
        background:
            radial-gradient(circle at 30% 20%, rgba(255,255,255,.18), transparent 28%),
            var(--rf-maroon);
        color: #FFFFFF;
        font-size: 17px;
        font-weight: 950;
        box-shadow: 0 12px 26px rgba(86,28,23,.16);
    }

    .rf-profile-copy {
        min-width: 0;
    }

    .rf-profile-copy h2 {
        margin: 0;
        color: var(--rf-text);
        font-size: 17px;
        font-weight: 950;
        line-height: 1.15;
    }

    .rf-profile-copy p {
        margin: 6px 0 0;
        color: var(--rf-muted);
        font-size: 9px;
        font-weight: 700;
    }

    .rf-profile-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 10px;
    }

    .rf-status,
    .rf-area {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        gap: 6px;
        padding: 0 9px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .rf-status {
        border: 1px solid #CFE8DA;
        background: var(--rf-success-soft);
        color: var(--rf-success);
    }

    .rf-status::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .rf-area {
        border: 1px solid #E6C7BE;
        background: var(--rf-bg-warm);
        color: var(--rf-maroon);
    }

    .rf-profile-side {
        display: grid;
        justify-items: end;
        gap: 5px;
        color: var(--rf-muted);
        font-size: 8px;
        text-align: right;
    }

    .rf-profile-side strong {
        color: var(--rf-maroon);
        font-size: 10px;
        font-weight: 950;
    }

    .rf-main-grid {
        display: grid;
        grid-template-columns: minmax(0,1fr) 340px;
        gap: 18px;
        align-items: start;
    }

    .rf-stack {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .rf-card {
        overflow: hidden;
        border: 1px solid var(--rf-border);
        border-radius: 22px;
        background: var(--rf-card);
        box-shadow: var(--rf-shadow);
    }

    .rf-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--rf-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .rf-card-head h2 {
        margin: 0;
        color: var(--rf-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .rf-card-head p {
        margin: 7px 0 0;
        color: var(--rf-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .rf-card-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--rf-bg-warm);
        color: var(--rf-maroon);
    }

    .rf-card-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rf-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 1px;
        background: var(--rf-border);
    }

    .rf-info-grid.is-one {
        grid-template-columns: 1fr;
    }

    .rf-info-item {
        min-width: 0;
        padding: 17px 20px;
        background: var(--rf-card);
    }

    .rf-label {
        display: block;
        color: var(--rf-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .rf-value {
        display: block;
        margin-top: 7px;
        color: var(--rf-text);
        font-size: 10px;
        font-weight: 900;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .rf-documents {
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 12px;
        padding: 18px 20px;
    }

    .rf-document {
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        gap: 12px;
        padding: 14px;
        border: 1px solid var(--rf-border);
        border-radius: 16px;
        background: var(--rf-bg-soft);
        transition: 150ms ease;
    }

    .rf-document:hover {
        border-color: var(--rf-tan);
        background: #FFF9F2;
    }

    .rf-document-icon {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 13px;
        background: var(--rf-bg-warm);
        color: var(--rf-maroon);
    }

    .rf-document-icon svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rf-document-copy h3 {
        margin: 0;
        color: var(--rf-text);
        font-size: 10px;
        font-weight: 950;
    }

    .rf-document-copy p {
        margin: 5px 0 0;
        color: var(--rf-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .rf-verified {
        display: inline-flex;
        min-height: 23px;
        align-items: center;
        gap: 5px;
        margin-top: 9px;
        padding: 0 8px;
        border: 1px solid #CFE8DA;
        border-radius: 999px;
        background: var(--rf-success-soft);
        color: var(--rf-success);
        font-size: 8px;
        font-weight: 900;
    }

    .rf-verified::before {
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .rf-doc-btn {
        margin-top: 10px;
        min-height: 33px;
        padding: 0 10px;
        font-size: 8px;
    }

    .rf-performance {
        display: grid;
        grid-template-columns: repeat(3,minmax(0,1fr));
        gap: 12px;
    }

    .rf-performance-card {
        display: flex;
        min-height: 126px;
        flex-direction: column;
        justify-content: space-between;
        gap: 12px;
        padding: 16px;
        border: 1px solid var(--rf-border);
        border-radius: 19px;
        background:
            radial-gradient(circle at 94% 6%, rgba(193,151,113,.13), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
        box-shadow: var(--rf-shadow);
    }

    .rf-performance-icon {
        display: grid;
        width: 37px;
        height: 37px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--rf-bg-warm);
        color: var(--rf-maroon);
    }

    .rf-performance-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rf-performance-card strong {
        display: block;
        color: var(--rf-text-dark);
        font-size: 26px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .rf-performance-card span {
        display: block;
        margin-top: 6px;
        color: var(--rf-muted);
        font-size: 8px;
        font-weight: 800;
    }

    .rf-performance-card small {
        display: block;
        margin-top: 4px;
        color: var(--rf-muted-light);
        font-size: 8px;
    }

    .rf-parcel-list {
        display: grid;
    }

    .rf-parcel {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
        border-bottom: 1px solid var(--rf-border);
        transition: 150ms ease;
    }

    .rf-parcel:last-child {
        border-bottom: 0;
    }

    .rf-parcel:hover {
        background: var(--rf-bg-soft);
    }

    .rf-parcel-icon {
        display: grid;
        width: 38px;
        height: 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--rf-bg-warm);
        color: var(--rf-maroon);
    }

    .rf-parcel-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
    }

    .rf-parcel-copy strong {
        display: block;
        color: var(--rf-text);
        font-size: 10px;
        font-weight: 950;
    }

    .rf-parcel-copy span {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 5px;
        color: var(--rf-muted);
        font-size: 8px;
        line-height: 1.4;
    }

    .rf-parcel-copy svg {
        width: 11px;
        height: 11px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .rf-parcel-status {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        gap: 6px;
        padding: 0 9px;
        border: 1px solid #E6C7BE;
        border-radius: 999px;
        background: var(--rf-bg-warm);
        color: var(--rf-maroon);
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .rf-parcel-status.is-delivered {
        border-color: #CFE8DA;
        background: var(--rf-success-soft);
        color: var(--rf-success);
    }

    .rf-parcel-status.is-pending {
        border-color: #EAD39A;
        background: var(--rf-warning-soft);
        color: var(--rf-warning);
    }

    .rf-summary-card {
        overflow: hidden;
        border-radius: 20px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255,255,255,.12), transparent 30%),
            linear-gradient(135deg, var(--rf-maroon) 0%, var(--rf-maroon-2) 58%, var(--rf-maroon-dark) 100%);
        color: #FFFFFF;
        box-shadow: 0 18px 44px rgba(86,28,23,.18);
    }

    .rf-summary-body {
        padding: 19px;
    }

    .rf-summary-body small {
        color: rgba(255,255,255,.55);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .rf-summary-body h2 {
        margin: 10px 0 0;
        color: #FFFFFF;
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 31px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .rf-summary-body p {
        margin: 8px 0 0;
        color: rgba(255,255,255,.62);
        font-size: 9px;
        line-height: 1.55;
    }

    .rf-summary-meta {
        display: grid;
        gap: 8px;
        margin-top: 16px;
    }

    .rf-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 12px;
        background: rgba(255,255,255,.07);
    }

    .rf-summary-row span {
        color: rgba(255,255,255,.5);
        font-size: 8px;
    }

    .rf-summary-row strong {
        color: #FFFFFF;
        font-size: 9px;
        font-weight: 900;
    }

    .rf-modal {
        visibility: hidden;
        position: fixed;
        inset: 0;
        z-index: 100;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgba(0,0,0,.54);
        opacity: 0;
        backdrop-filter: blur(3px);
        transition: 180ms ease;
    }

    .rf-modal.is-open {
        visibility: visible;
        opacity: 1;
    }

    .rf-modal-panel {
        width: min(620px, 100%);
        overflow: hidden;
        border: 1px solid var(--rf-border);
        border-radius: 22px;
        background: var(--rf-card);
        box-shadow: var(--rf-shadow-hover);
        transform: scale(.96);
        transition: 180ms ease;
    }

    .rf-modal.is-open .rf-modal-panel {
        transform: scale(1);
    }

    .rf-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 18px;
        border-bottom: 1px solid var(--rf-border);
        background: var(--rf-bg-soft);
    }

    .rf-modal-head h2 {
        margin: 0;
        color: var(--rf-text);
        font-size: 12px;
        font-weight: 950;
    }

    .rf-modal-close {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid var(--rf-border);
        border-radius: 10px;
        background: var(--rf-card);
        color: var(--rf-maroon);
        cursor: pointer;
    }

    .rf-modal-close svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
    }

    .rf-modal-body {
        padding: 20px;
    }

    .rf-preview {
        display: grid;
        min-height: 320px;
        place-items: center;
        border: 1px dashed var(--rf-border-strong);
        border-radius: 16px;
        background: linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 100%);
        color: var(--rf-muted);
        text-align: center;
    }

    .rf-preview svg {
        width: 34px;
        height: 34px;
        margin-inline: auto;
        fill: none;
        stroke: var(--rf-maroon);
        stroke-width: 1.6;
    }

    .rf-preview strong {
        display: block;
        margin-top: 12px;
        color: var(--rf-text);
        font-size: 11px;
        font-weight: 950;
    }

    .rf-preview span {
        display: block;
        margin-top: 5px;
        color: var(--rf-muted);
        font-size: 9px;
    }

    @media (max-width: 1180px) {
        .rf-main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 820px) {
        .rf-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .rf-actions {
            width: 100%;
        }

        .rf-actions .rf-btn {
            flex: 1;
        }

        .rf-profile {
            grid-template-columns: auto minmax(0,1fr);
        }

        .rf-profile-side {
            grid-column: 1 / -1;
            justify-items: start;
            text-align: left;
        }

        .rf-performance {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 620px) {
        .rf-profile {
            grid-template-columns: 1fr;
        }

        .rf-info-grid,
        .rf-documents {
            grid-template-columns: 1fr;
        }

        .rf-parcel {
            grid-template-columns: auto minmax(0,1fr);
        }

        .rf-parcel-status {
            grid-column: 2;
            justify-self: start;
        }
    }

    html.dark .rf-page {
        color: #F5EFE8;
    }

    html.dark .rf-hero,
    html.dark .rf-profile,
    html.dark .rf-card,
    html.dark .rf-performance-card,
    html.dark .rf-modal-panel {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .rf-card-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .rf-hero h1,
    html.dark .rf-profile-copy h2,
    html.dark .rf-card-head h2,
    html.dark .rf-value,
    html.dark .rf-document-copy h3,
    html.dark .rf-performance-card strong,
    html.dark .rf-parcel-copy strong,
    html.dark .rf-modal-head h2,
    html.dark .rf-preview strong {
        color: #F5EFE8 !important;
    }

    html.dark .rf-hero p,
    html.dark .rf-breadcrumb,
    html.dark .rf-profile-copy p,
    html.dark .rf-card-head p,
    html.dark .rf-label,
    html.dark .rf-document-copy p,
    html.dark .rf-performance-card span,
    html.dark .rf-performance-card small,
    html.dark .rf-parcel-copy span,
    html.dark .rf-preview span {
        color: #AFA19A !important;
    }

    html.dark .rf-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .rf-info-grid {
        background: #30231F !important;
    }

    html.dark .rf-info-item {
        background: #1A1412 !important;
    }

    html.dark .rf-area,
    html.dark .rf-document {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #C8B7AD !important;
    }

    html.dark .rf-document:hover,
    html.dark .rf-parcel:hover {
        background: #241817 !important;
    }

    html.dark .rf-document-icon,
    html.dark .rf-card-icon,
    html.dark .rf-performance-icon,
    html.dark .rf-parcel-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .rf-parcel {
        border-color: #30231F !important;
    }

    html.dark .rf-parcel-status {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .rf-btn-soft,
    html.dark .rf-doc-btn,
    html.dark .rf-modal-close {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .rf-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .rf-btn-primary:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }

    html.dark .rf-btn-danger {
        background: #1D1715 !important;
        border-color: #5B2925 !important;
        color: #F2A49A !important;
    }

    html.dark .rf-modal-head {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .rf-preview {
        background: #171210 !important;
        border-color: #3B2E27 !important;
    }
</style>

<div class="rf-page">

    <nav class="rf-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('logistics.dashboard') }}">Dashboard</a>
        <span>/</span>

        @if(\Illuminate\Support\Facades\Route::has('logistics.riders'))
            <a href="{{ route('logistics.riders') }}">Riders</a>
            <span>/</span>
        @endif

        <strong>{{ $profile['name'] }}</strong>
    </nav>

    <section class="rf-hero">
        <div>
            <span class="rf-eyebrow">
                Rider Management
            </span>

            <h1>
                Rider Profile
            </h1>

            <p>
                Review rider information, verification documents, vehicle details,
                active parcels, and delivery performance.
            </p>
        </div>

        <div class="rf-actions">
            <button
                type="button"
                id="suspendRiderButton"
                class="rf-btn rf-btn-danger"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['pause'] !!}
                </svg>

                Suspend
            </button>

            <button
                type="button"
                id="activateRiderButton"
                class="rf-btn rf-btn-primary"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['power'] !!}
                </svg>

                Activate
            </button>
        </div>
    </section>

    <section class="rf-profile">
        <span class="rf-avatar">
            {{ $initials }}
        </span>

        <div class="rf-profile-copy">
            <h2>
                {{ $profile['name'] }}
            </h2>

            <p>
                Rider ID: {{ $profile['code'] }}
            </p>

            <div class="rf-profile-meta">
                <span class="rf-status">
                    {{ $profile['status'] }}
                </span>

                <span class="rf-area">
                    {{ $profile['area'] }}
                </span>
            </div>
        </div>

        <div class="rf-profile-side">
            <span>Assigned Area</span>
            <strong>{{ $profile['area'] }}</strong>
            <span>{{ $profile['vehicle'] }} · {{ $profile['plate'] }}</span>
        </div>
    </section>

    <section class="rf-main-grid">

        <div class="rf-stack">

            <section class="rf-card">
                <header class="rf-card-head">
                    <div>
                        <h2>Personal Information</h2>
                        <p>Primary rider identity and contact details.</p>
                    </div>

                    <span class="rf-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['user'] !!}
                        </svg>
                    </span>
                </header>

                <div class="rf-info-grid">
                    @foreach($personalInfo as $item)
                        <div class="rf-info-item">
                            <span class="rf-label">
                                {{ $item['label'] }}
                            </span>

                            <strong class="rf-value">
                                {{ $item['value'] }}
                            </strong>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rf-card">
                <header class="rf-card-head">
                    <div>
                        <h2>Verification Documents</h2>
                        <p>Validated rider registration and driving documents.</p>
                    </div>

                    <span class="rf-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['document'] !!}
                        </svg>
                    </span>
                </header>

                <div class="rf-documents">
                    @foreach($documents as $document)
                        <article class="rf-document">
                            <span class="rf-document-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    {!! $icons['document'] !!}
                                </svg>
                            </span>

                            <div class="rf-document-copy">
                                <h3>
                                    {{ $document['title'] }}
                                </h3>

                                <p>
                                    {{ $document['description'] }}
                                </p>

                                <span class="rf-verified">
                                    {{ $document['status'] }}
                                </span>

                                <div>
                                    <button
                                        type="button"
                                        class="rf-btn rf-btn-soft rf-doc-btn"
                                        data-document-title="{{ $document['title'] }}"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            {!! $icons['eye'] !!}
                                        </svg>

                                        View File
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="rf-card">
                <header class="rf-card-head">
                    <div>
                        <h2>Assigned Parcels</h2>
                        <p>Current and recent delivery assignments.</p>
                    </div>

                    <span class="rf-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['package'] !!}
                        </svg>
                    </span>
                </header>

                <div class="rf-parcel-list">
                    @foreach($assignedParcels as $parcel)
                        @php
                            $parcelStatusClass = match($parcel['status']) {
                                'Delivered' => 'is-delivered',
                                'Pending Pickup' => 'is-pending',
                                default => '',
                            };
                        @endphp

                        <article class="rf-parcel">
                            <span class="rf-parcel-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    {!! $icons['package'] !!}
                                </svg>
                            </span>

                            <div class="rf-parcel-copy">
                                <strong>
                                    {{ $parcel['tracking'] }}
                                </strong>

                                <span>
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        {!! $icons['location'] !!}
                                    </svg>

                                    {{ $parcel['destination'] }}
                                </span>
                            </div>

                            <span class="rf-parcel-status {{ $parcelStatusClass }}">
                                {{ $parcel['status'] }}
                            </span>
                        </article>
                    @endforeach
                </div>
            </section>

        </div>

        <aside class="rf-stack">

            <section class="rf-summary-card">
                <div class="rf-summary-body">
                    <small>Rider Overview</small>

                    <h2>{{ $profile['name'] }}</h2>

                    <p>
                        Current rider account and delivery assignment summary.
                    </p>

                    <div class="rf-summary-meta">
                        <div class="rf-summary-row">
                            <span>Account Status</span>
                            <strong>{{ $profile['status'] }}</strong>
                        </div>

                        <div class="rf-summary-row">
                            <span>Delivery Area</span>
                            <strong>{{ $profile['area'] }}</strong>
                        </div>

                        <div class="rf-summary-row">
                            <span>Vehicle</span>
                            <strong>{{ $profile['vehicle'] }}</strong>
                        </div>

                        <div class="rf-summary-row">
                            <span>Plate Number</span>
                            <strong>{{ $profile['plate'] }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rf-card">
                <header class="rf-card-head">
                    <div>
                        <h2>Vehicle Information</h2>
                        <p>Registered courier vehicle and verification status.</p>
                    </div>

                    <span class="rf-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['vehicle'] !!}
                        </svg>
                    </span>
                </header>

                <div class="rf-info-grid is-one">
                    @foreach($vehicleInfo as $item)
                        <div class="rf-info-item">
                            <span class="rf-label">
                                {{ $item['label'] }}
                            </span>

                            <strong class="rf-value">
                                {{ $item['value'] }}
                            </strong>
                        </div>
                    @endforeach
                </div>
            </section>

        </aside>

    </section>

    <section class="rf-performance" aria-label="Rider performance">
        @foreach($performance as $stat)
            <article class="rf-performance-card">
                <span class="rf-performance-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        {!! $icons[$stat['icon']] !!}
                    </svg>
                </span>

                <div>
                    <strong>{{ $stat['value'] }}</strong>
                    <span>{{ $stat['label'] }}</span>
                    <small>{{ $stat['description'] }}</small>
                </div>
            </article>
        @endforeach
    </section>

</div>

<div id="riderDocumentModal" class="rf-modal">
    <div class="rf-modal-panel">
        <header class="rf-modal-head">
            <h2 id="riderDocumentTitle">
                Document Preview
            </h2>

            <button
                type="button"
                id="closeRiderDocumentModal"
                class="rf-modal-close"
                aria-label="Close document preview"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['close'] !!}
                </svg>
            </button>
        </header>

        <div class="rf-modal-body">
            <div class="rf-preview">
                <div>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        {!! $icons['document'] !!}
                    </svg>

                    <strong id="riderDocumentName">
                        Verification Document
                    </strong>

                    <span>
                        Connect this preview to the rider document stored in Laravel Storage.
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal =
        document.getElementById('riderDocumentModal');

    const modalTitle =
        document.getElementById('riderDocumentTitle');

    const modalName =
        document.getElementById('riderDocumentName');

    const closeButton =
        document.getElementById('closeRiderDocumentModal');


    function openDocumentModal(title) {
        if (!modal) {
            return;
        }

        if (modalTitle) {
            modalTitle.textContent =
                title + ' Preview';
        }

        if (modalName) {
            modalName.textContent =
                title;
        }

        modal.classList.add('is-open');

        document.body.style.overflow =
            'hidden';
    }


    function closeDocumentModal() {
        if (!modal) {
            return;
        }

        modal.classList.remove('is-open');

        document.body.style.overflow =
            '';
    }


    document
        .querySelectorAll('[data-document-title]')
        .forEach(function (button) {
            button.addEventListener(
                'click',
                function () {
                    openDocumentModal(
                        button.dataset.documentTitle
                            || 'Document'
                    );
                }
            );
        });


    document
        .getElementById('suspendRiderButton')
        ?.addEventListener(
            'click',
            function () {
                window.alert(
                    'Connect this button to your rider suspension route or controller action.'
                );
            }
        );


    document
        .getElementById('activateRiderButton')
        ?.addEventListener(
            'click',
            function () {
                window.alert(
                    'Connect this button to your rider activation route or controller action.'
                );
            }
        );


    closeButton?.addEventListener(
        'click',
        closeDocumentModal
    );


    modal?.addEventListener(
        'click',
        function (event) {
            if (event.target === modal) {
                closeDocumentModal();
            }
        }
    );


    document.addEventListener(
        'keydown',
        function (event) {
            if (event.key === 'Escape') {
                closeDocumentModal();
            }
        }
    );
});
</script>
@endpush
