@extends('logistics.app')

@section('title', 'Rider Application Review — LIKHAE Logistics')

@php
    $status = data_get($rider, 'status', 'Pending Approval');

    $statusKey = match($status) {
        'Approved' => 'approved',
        'Rejected' => 'rejected',
        default => 'pending',
    };

    $fullName = data_get($rider, 'name', 'Juan Dela Cruz');

    $nameParts = collect(preg_split('/\s+/', trim($fullName)))
        ->filter()
        ->values();

    $firstName = data_get($rider, 'first_name')
        ?: ($nameParts->first() ?: 'Juan');

    $lastName = data_get($rider, 'last_name')
        ?: ($nameParts->count() > 1
            ? $nameParts->slice(1)->implode(' ')
            : 'Dela Cruz');

    $middleInitial = data_get($rider, 'middle_initial', 'M.');

    $initials = $nameParts
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'JR';

    $personalDetails = [
        ['label' => 'Last Name', 'value' => $lastName],
        ['label' => 'First Name', 'value' => $firstName],
        ['label' => 'Middle Initial', 'value' => $middleInitial],
        ['label' => 'Sex', 'value' => data_get($rider, 'sex', 'Male')],
        ['label' => 'Birthday', 'value' => data_get($rider, 'birthday', 'January 15, 2000')],
        ['label' => 'Age', 'value' => data_get($rider, 'age', '26 Years Old')],
    ];

    $contactDetails = [
        ['label' => 'Email', 'value' => data_get($rider, 'email', 'juan@email.com')],
        ['label' => 'Contact Number', 'value' => data_get($rider, 'contact', '0917 123 4567')],
        [
            'label' => 'Complete Address',
            'value' => data_get($rider, 'address', 'Barangay Real, Calamba City, Laguna'),
            'wide' => true,
        ],
    ];

    $vehicleDetails = [
        ['label' => 'Vehicle Type', 'value' => data_get($rider, 'vehicle', 'Motorcycle')],
        ['label' => 'Plate Number', 'value' => data_get($rider, 'plate', 'ABC-1234')],
        ['label' => 'Application Type', 'value' => 'Courier Rider'],
    ];

    $documents = [
        [
            'key' => 'orcr',
            'title' => 'OR / CR',
            'description' => 'Vehicle registration proof',
            'status' => 'Submitted',
        ],
        [
            'key' => 'valid-id',
            'title' => 'Driver License / Valid ID',
            'description' => 'Identity verification',
            'status' => 'Submitted',
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
        'mail' => '
            <rect x="3" y="5" width="18" height="14" rx="2"/>
            <path d="m3 7 9 6 9-6"/>
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
        'check' => '
            <path d="m5 12 4 4 10-10"/>
        ',
        'x' => '
            <path d="m7 7 10 10"/>
            <path d="m17 7-10 10"/>
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
        --rr-bg: #FBF7F2;
        --rr-bg-soft: #F6EFE7;
        --rr-bg-warm: #F3E4DE;
        --rr-card: #FFFDF9;

        --rr-border: #EADCCC;
        --rr-border-strong: #DBCEC1;

        --rr-maroon: #561C17;
        --rr-maroon-2: #642920;
        --rr-maroon-dark: #3E130F;

        --rr-text: #3B211B;
        --rr-text-dark: #1C160F;
        --rr-brown: #6C4936;
        --rr-muted: #987865;
        --rr-muted-light: #A99386;

        --rr-tan: #C19771;

        --rr-success: #256F4A;
        --rr-success-soft: #EAF7EF;

        --rr-warning: #9A5B11;
        --rr-warning-soft: #FFF6DE;

        --rr-danger: #B42318;
        --rr-danger-soft: #FCEBE9;

        --rr-shadow: 0 8px 24px rgba(86, 28, 23, 0.055);
        --rr-shadow-hover: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .rr-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--rr-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .rr-page * {
        box-sizing: border-box;
    }

    .rr-success-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 13px 15px;
        border: 1px solid #CFE8DA;
        border-radius: 14px;
        background: var(--rr-success-soft);
        color: var(--rr-success);
        font-size: 10px;
        font-weight: 850;
    }

    .rr-success-alert svg {
        width: 16px;
        height: 16px;
        flex: 0 0 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rr-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--rr-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--rr-shadow);
    }

    .rr-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--rr-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .rr-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .rr-hero h1 {
        margin: 10px 0 0;
        color: var(--rr-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .rr-hero p {
        max-width: 680px;
        margin: 13px 0 0;
        color: var(--rr-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .rr-status {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        gap: 7px;
        padding: 0 12px;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 950;
        letter-spacing: .05em;
        white-space: nowrap;
    }

    .rr-status::before {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .rr-status.is-pending {
        border-color: #EAD39A;
        background: var(--rr-warning-soft);
        color: var(--rr-warning);
    }

    .rr-status.is-approved {
        border-color: #CFE8DA;
        background: var(--rr-success-soft);
        color: var(--rr-success);
    }

    .rr-status.is-rejected {
        border-color: #EDC9C5;
        background: var(--rr-danger-soft);
        color: var(--rr-danger);
    }

    .rr-profile {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 16px;
        padding: 20px;
        border: 1px solid var(--rr-border);
        border-radius: 22px;
        background: var(--rr-card);
        box-shadow: var(--rr-shadow);
    }

    .rr-avatar {
        display: grid;
        width: 72px;
        height: 72px;
        place-items: center;
        border-radius: 999px;
        background:
            radial-gradient(circle at 30% 20%, rgba(255,255,255,.18), transparent 28%),
            var(--rr-maroon);
        color: #FFFFFF;
        font-size: 16px;
        font-weight: 950;
        box-shadow: 0 12px 26px rgba(86,28,23,.16);
    }

    .rr-profile-copy {
        min-width: 0;
    }

    .rr-profile-copy h2 {
        margin: 0;
        color: var(--rr-text);
        font-size: 16px;
        font-weight: 950;
        line-height: 1.15;
    }

    .rr-profile-copy p {
        margin: 6px 0 0;
        color: var(--rr-muted);
        font-size: 9px;
        font-weight: 700;
    }

    .rr-profile-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 10px;
    }

    .rr-profile-chip {
        display: inline-flex;
        min-height: 24px;
        align-items: center;
        padding: 0 8px;
        border: 1px solid var(--rr-border);
        border-radius: 999px;
        background: var(--rr-bg-soft);
        color: var(--rr-brown);
        font-size: 8px;
        font-weight: 800;
    }

    .rr-card {
        overflow: hidden;
        border: 1px solid var(--rr-border);
        border-radius: 22px;
        background: var(--rr-card);
        box-shadow: var(--rr-shadow);
    }

    .rr-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--rr-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .rr-card-head h2 {
        margin: 0;
        color: var(--rr-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .rr-card-head p {
        margin: 7px 0 0;
        color: var(--rr-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .rr-card-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--rr-bg-warm);
        color: var(--rr-maroon);
    }

    .rr-card-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rr-detail-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 1px;
        background: var(--rr-border);
    }

    .rr-detail-grid.is-three {
        grid-template-columns: repeat(3,minmax(0,1fr));
    }

    .rr-detail {
        min-width: 0;
        padding: 17px 20px;
        background: var(--rr-card);
    }

    .rr-detail.is-wide {
        grid-column: 1 / -1;
    }

    .rr-label {
        display: block;
        color: var(--rr-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .rr-value {
        display: block;
        margin-top: 7px;
        color: var(--rr-text);
        font-size: 10px;
        font-weight: 900;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .rr-documents {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 12px;
        padding: 18px 20px;
    }

    .rr-document {
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        gap: 12px;
        padding: 14px;
        border: 1px solid var(--rr-border);
        border-radius: 16px;
        background: var(--rr-bg-soft);
        transition: 150ms ease;
    }

    .rr-document:hover {
        border-color: var(--rr-tan);
        background: #FFF9F2;
    }

    .rr-document-icon {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 13px;
        background: var(--rr-bg-warm);
        color: var(--rr-maroon);
    }

    .rr-document-icon svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rr-document-copy h3 {
        margin: 0;
        color: var(--rr-text);
        font-size: 10px;
        font-weight: 950;
    }

    .rr-document-copy p {
        margin: 5px 0 0;
        color: var(--rr-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .rr-doc-status {
        display: inline-flex;
        min-height: 23px;
        align-items: center;
        gap: 5px;
        margin-top: 9px;
        padding: 0 8px;
        border: 1px solid #CFE8DA;
        border-radius: 999px;
        background: var(--rr-success-soft);
        color: var(--rr-success);
        font-size: 8px;
        font-weight: 900;
    }

    .rr-doc-status::before {
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .rr-btn {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 12px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: 9px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        transition: 150ms ease;
    }

    .rr-btn:hover {
        transform: translateY(-1px);
    }

    .rr-btn svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rr-btn-preview {
        margin-top: 11px;
        border-color: var(--rr-border);
        background: var(--rr-card);
        color: var(--rr-maroon);
    }

    .rr-btn-preview:hover {
        border-color: var(--rr-tan);
        background: var(--rr-bg-warm);
    }

    .rr-actions {
        position: sticky;
        bottom: 14px;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 14px 16px;
        border: 1px solid var(--rr-border);
        border-radius: 18px;
        background: rgba(255,253,249,.94);
        box-shadow: var(--rr-shadow-hover);
        backdrop-filter: blur(16px);
    }

    .rr-actions-copy strong {
        display: block;
        color: var(--rr-text);
        font-size: 10px;
        font-weight: 900;
    }

    .rr-actions-copy span {
        display: block;
        margin-top: 4px;
        color: var(--rr-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .rr-action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .rr-btn-approve {
        min-height: 42px;
        padding: 0 16px;
        border-color: var(--rr-success);
        background: var(--rr-success);
        color: #FFFFFF;
        box-shadow: 0 10px 20px rgba(37,111,74,.13);
    }

    .rr-btn-approve:hover {
        background: #1D5A3C;
        border-color: #1D5A3C;
    }

    .rr-btn-reject {
        min-height: 42px;
        padding: 0 16px;
        border-color: #D8AAA5;
        background: #FFFFFF;
        color: var(--rr-danger);
    }

    .rr-btn-reject:hover {
        background: var(--rr-danger-soft);
        border-color: #D99992;
    }

    .rr-modal {
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

    .rr-modal.is-open {
        visibility: visible;
        opacity: 1;
    }

    .rr-modal-panel {
        width: min(620px,100%);
        overflow: hidden;
        border: 1px solid var(--rr-border);
        border-radius: 22px;
        background: var(--rr-card);
        box-shadow: var(--rr-shadow-hover);
        transform: scale(.96);
        transition: 180ms ease;
    }

    .rr-modal.is-open .rr-modal-panel {
        transform: scale(1);
    }

    .rr-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 18px;
        border-bottom: 1px solid var(--rr-border);
        background: var(--rr-bg-soft);
    }

    .rr-modal-head h2 {
        margin: 0;
        color: var(--rr-text);
        font-size: 12px;
        font-weight: 950;
    }

    .rr-modal-close {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid var(--rr-border);
        border-radius: 10px;
        background: var(--rr-card);
        color: var(--rr-maroon);
        cursor: pointer;
    }

    .rr-modal-close svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
    }

    .rr-modal-body {
        padding: 20px;
    }

    .rr-document-preview {
        display: grid;
        min-height: 320px;
        place-items: center;
        border: 1px dashed var(--rr-border-strong);
        border-radius: 16px;
        background: linear-gradient(135deg,#FFFDF9 0%,#F6EFE7 100%);
        color: var(--rr-muted);
        text-align: center;
    }

    .rr-document-preview svg {
        width: 34px;
        height: 34px;
        margin-inline: auto;
        fill: none;
        stroke: var(--rr-maroon);
        stroke-width: 1.6;
    }

    .rr-document-preview strong {
        display: block;
        margin-top: 12px;
        color: var(--rr-text);
        font-size: 11px;
        font-weight: 950;
    }

    .rr-document-preview span {
        display: block;
        margin-top: 5px;
        color: var(--rr-muted);
        font-size: 9px;
    }

    @media (max-width: 860px) {
        .rr-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .rr-profile {
            grid-template-columns: auto minmax(0,1fr);
        }

        .rr-detail-grid.is-three {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }

        .rr-documents {
            grid-template-columns: 1fr;
        }

        .rr-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .rr-action-buttons {
            width: 100%;
        }

        .rr-action-buttons form {
            flex: 1;
        }

        .rr-action-buttons .rr-btn {
            width: 100%;
        }
    }

    @media (max-width: 560px) {
        .rr-profile,
        .rr-detail-grid,
        .rr-detail-grid.is-three {
            grid-template-columns: 1fr;
        }

        .rr-detail.is-wide {
            grid-column: auto;
        }

        .rr-action-buttons {
            flex-direction: column;
        }

        .rr-action-buttons form {
            width: 100%;
        }
    }

    html.dark .rr-page {
        color: #F5EFE8;
    }

    html.dark .rr-hero,
    html.dark .rr-profile,
    html.dark .rr-card,
    html.dark .rr-modal-panel {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .rr-card-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .rr-hero h1,
    html.dark .rr-profile-copy h2,
    html.dark .rr-card-head h2,
    html.dark .rr-value,
    html.dark .rr-document-copy h3,
    html.dark .rr-actions-copy strong,
    html.dark .rr-modal-head h2,
    html.dark .rr-document-preview strong {
        color: #F5EFE8 !important;
    }

    html.dark .rr-hero p,
    html.dark .rr-profile-copy p,
    html.dark .rr-card-head p,
    html.dark .rr-label,
    html.dark .rr-document-copy p,
    html.dark .rr-actions-copy span,
    html.dark .rr-document-preview span {
        color: #AFA19A !important;
    }

    html.dark .rr-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .rr-detail-grid {
        background: #30231F !important;
    }

    html.dark .rr-detail {
        background: #1A1412 !important;
    }

    html.dark .rr-profile-chip,
    html.dark .rr-document {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #C8B7AD !important;
    }

    html.dark .rr-document:hover {
        background: #241817 !important;
        border-color: #60463A !important;
    }

    html.dark .rr-document-icon,
    html.dark .rr-card-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .rr-btn-preview,
    html.dark .rr-modal-close {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .rr-actions {
        background: rgba(26,20,18,.94) !important;
        border-color: #3B2E27 !important;
    }

    html.dark .rr-btn-reject {
        background: #1D1715 !important;
        border-color: #5B2925 !important;
        color: #F2A49A !important;
    }

    html.dark .rr-modal-head {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .rr-document-preview {
        background: #171210 !important;
        border-color: #3B2E27 !important;
    }
</style>

<div class="rr-page">

    @if(session('success'))
        <div class="rr-success-alert">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['check'] !!}
            </svg>

            {{ session('success') }}
        </div>
    @endif

    <section class="rr-hero">
        <div>
            <span class="rr-eyebrow">
                Courier Registration
            </span>

            <h1>
                Rider Application
            </h1>

            <p>
                Review the applicant profile, verify submitted requirements,
                and make an approval decision for this courier account.
            </p>
        </div>

        <span class="rr-status is-{{ $statusKey }}">
            {{
                $statusKey === 'approved'
                    ? 'APPROVED'
                    : ($statusKey === 'rejected'
                        ? 'REJECTED'
                        : 'PENDING REVIEW')
            }}
        </span>
    </section>

    <section class="rr-profile">
        <span class="rr-avatar">
            {{ $initials }}
        </span>

        <div class="rr-profile-copy">
            <h2>
                {{ $fullName }}
            </h2>

            <p>
                Courier Applicant
            </p>

            <div class="rr-profile-meta">
                <span class="rr-profile-chip">
                    {{ data_get($rider, 'vehicle', 'Motorcycle') }}
                </span>

                <span class="rr-profile-chip">
                    {{ data_get($rider, 'plate', 'ABC-1234') }}
                </span>
            </div>
        </div>

        <span class="rr-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
                {!! $icons['rider'] !!}
            </svg>
        </span>
    </section>

    <section class="rr-card">
        <header class="rr-card-head">
            <div>
                <h2>
                    Personal Information
                </h2>

                <p>
                    Applicant identity and personal details.
                </p>
            </div>

            <span class="rr-card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    {!! $icons['user'] !!}
                </svg>
            </span>
        </header>

        <div class="rr-detail-grid">
            @foreach($personalDetails as $detail)
                <div class="rr-detail">
                    <span class="rr-label">
                        {{ $detail['label'] }}
                    </span>

                    <strong class="rr-value">
                        {{ $detail['value'] }}
                    </strong>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rr-card">
        <header class="rr-card-head">
            <div>
                <h2>
                    Contact Details
                </h2>

                <p>
                    Contact information submitted during registration.
                </p>
            </div>

            <span class="rr-card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    {!! $icons['mail'] !!}
                </svg>
            </span>
        </header>

        <div class="rr-detail-grid">
            @foreach($contactDetails as $detail)
                <div class="rr-detail {{ !empty($detail['wide']) ? 'is-wide' : '' }}">
                    <span class="rr-label">
                        {{ $detail['label'] }}
                    </span>

                    <strong class="rr-value">
                        {{ $detail['value'] }}
                    </strong>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rr-card">
        <header class="rr-card-head">
            <div>
                <h2>
                    Vehicle Information
                </h2>

                <p>
                    Vehicle details submitted for courier operations.
                </p>
            </div>

            <span class="rr-card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    {!! $icons['vehicle'] !!}
                </svg>
            </span>
        </header>

        <div class="rr-detail-grid is-three">
            @foreach($vehicleDetails as $detail)
                <div class="rr-detail">
                    <span class="rr-label">
                        {{ $detail['label'] }}
                    </span>

                    <strong class="rr-value">
                        {{ $detail['value'] }}
                    </strong>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rr-card">
        <header class="rr-card-head">
            <div>
                <h2>
                    Submitted Documents
                </h2>

                <p>
                    Review registration documents before deciding on the application.
                </p>
            </div>

            <span class="rr-card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    {!! $icons['document'] !!}
                </svg>
            </span>
        </header>

        <div class="rr-documents">
            @foreach($documents as $document)
                <article class="rr-document">
                    <span class="rr-document-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['document'] !!}
                        </svg>
                    </span>

                    <div class="rr-document-copy">
                        <h3>
                            {{ $document['title'] }}
                        </h3>

                        <p>
                            {{ $document['description'] }}
                        </p>

                        <span class="rr-doc-status">
                            {{ $document['status'] }}
                        </span>

                        <div>
                            <button
                                type="button"
                                class="rr-btn rr-btn-preview"
                                data-document-title="{{ $document['title'] }}"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    {!! $icons['eye'] !!}
                                </svg>

                                Preview
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    @if($status === 'Pending Approval')
        <section class="rr-actions">
            <div class="rr-actions-copy">
                <strong>
                    Application decision
                </strong>

                <span>
                    Confirm that the submitted information and documents have been reviewed.
                </span>
            </div>

            <div class="rr-action-buttons">
                <form
                    method="POST"
                    action="{{ route('logistics.rider-applications.approve', $rider['id']) }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="rr-btn rr-btn-approve"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['check'] !!}
                        </svg>

                        Approve Application
                    </button>
                </form>

                <form
                    method="POST"
                    action="{{ route('logistics.rider-applications.reject', $rider['id']) }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="rr-btn rr-btn-reject"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['x'] !!}
                        </svg>

                        Reject Application
                    </button>
                </form>
            </div>
        </section>
    @endif

</div>

<div id="documentPreviewModal" class="rr-modal">
    <div class="rr-modal-panel">
        <header class="rr-modal-head">
            <h2 id="documentPreviewTitle">
                Document Preview
            </h2>

            <button
                type="button"
                id="closeDocumentPreview"
                class="rr-modal-close"
                aria-label="Close document preview"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['close'] !!}
                </svg>
            </button>
        </header>

        <div class="rr-modal-body">
            <div class="rr-document-preview">
                <div>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        {!! $icons['document'] !!}
                    </svg>

                    <strong id="documentPreviewName">
                        Submitted Document
                    </strong>

                    <span>
                        Connect this preview to the uploaded rider document from storage.
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
        document.getElementById('documentPreviewModal');

    const modalTitle =
        document.getElementById('documentPreviewTitle');

    const modalName =
        document.getElementById('documentPreviewName');

    const closeButton =
        document.getElementById('closeDocumentPreview');


    function openDocumentPreview(title) {
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


    function closeDocumentPreview() {
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
                    openDocumentPreview(
                        button.dataset.documentTitle
                            || 'Document'
                    );
                }
            );
        });


    closeButton?.addEventListener(
        'click',
        closeDocumentPreview
    );


    modal?.addEventListener(
        'click',
        function (event) {
            if (event.target === modal) {
                closeDocumentPreview();
            }
        }
    );


    document.addEventListener(
        'keydown',
        function (event) {
            if (event.key === 'Escape') {
                closeDocumentPreview();
            }
        }
    );
});
</script>
@endpush
