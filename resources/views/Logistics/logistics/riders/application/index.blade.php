@extends('logistics.app')

@section('title', 'Rider Applications — LIKHAE Logistics')

@php
    $riders = [
        [
            'id' => 1,
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@email.com',
            'vehicle' => 'Motorcycle',
            'plate' => 'ABC-1234',
            'status' => 'Pending Approval',
            'area' => 'Area B',
            'submitted' => 'Sep 11, 2026',
        ],
        [
            'id' => 2,
            'name' => 'Mark Santos',
            'email' => 'mark@email.com',
            'vehicle' => 'Motorcycle',
            'plate' => 'XYZ-5678',
            'status' => 'Approved',
            'area' => 'Area A',
            'submitted' => 'Sep 10, 2026',
        ],
        [
            'id' => 3,
            'name' => 'Carlo Reyes',
            'email' => 'carlo@email.com',
            'vehicle' => 'Van',
            'plate' => 'VAN-9090',
            'status' => 'Rejected',
            'area' => 'Area D',
            'submitted' => 'Sep 09, 2026',
        ],
    ];

    $summary = [
        [
            'label' => 'Total Applications',
            'value' => 35,
            'description' => 'All rider registrations',
            'tone' => 'primary',
            'icon' => 'applications',
        ],
        [
            'label' => 'Pending',
            'value' => 12,
            'description' => 'Awaiting verification',
            'tone' => 'warning',
            'icon' => 'clock',
        ],
        [
            'label' => 'Approved',
            'value' => 20,
            'description' => 'Verified rider accounts',
            'tone' => 'success',
            'icon' => 'check',
        ],
        [
            'label' => 'Rejected',
            'value' => 3,
            'description' => 'Applications declined',
            'tone' => 'danger',
            'icon' => 'x',
        ],
    ];

    $icons = [
        'applications' => '
            <path d="M6 3h9l4 4v14H6z"/>
            <path d="M14 3v5h5"/>
            <path d="M9 13h6"/>
            <path d="M9 17h4"/>
        ',
        'clock' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7v5l3 2"/>
        ',
        'check' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="m8 12 2.7 2.7L16.5 9"/>
        ',
        'x' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="m9 9 6 6"/>
            <path d="m15 9-6 6"/>
        ',
        'rider' => '
            <circle cx="8" cy="7" r="3"/>
            <path d="M3 19c0-3 2-5 5-5"/>
            <path d="M14 7h7"/>
            <path d="M17.5 3.5v7"/>
        ',
        'vehicle' => '
            <path d="M5 16h10"/>
            <path d="m9 16 2-5h5l3 5"/>
            <circle cx="7" cy="18" r="2"/>
            <circle cx="18" cy="18" r="2"/>
        ',
        'search' => '
            <circle cx="11" cy="11" r="7"/>
            <path d="m20 20-3.5-3.5"/>
        ',
        'eye' => '
            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
            <circle cx="12" cy="12" r="2.5"/>
        ',
        'approve' => '
            <path d="m5 12 4 4 10-10"/>
        ',
        'reject' => '
            <path d="m7 7 10 10"/>
            <path d="m17 7-10 10"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --ra-bg: #FBF7F2;
        --ra-bg-soft: #F6EFE7;
        --ra-bg-warm: #F3E4DE;
        --ra-card: #FFFDF9;

        --ra-border: #EADCCC;
        --ra-border-strong: #DBCEC1;

        --ra-maroon: #561C17;
        --ra-maroon-2: #642920;
        --ra-maroon-dark: #3E130F;

        --ra-text: #3B211B;
        --ra-text-dark: #1C160F;
        --ra-brown: #6C4936;
        --ra-muted: #987865;
        --ra-muted-light: #A99386;

        --ra-tan: #C19771;

        --ra-success: #256F4A;
        --ra-success-soft: #EAF7EF;

        --ra-warning: #9A5B11;
        --ra-warning-soft: #FFF6DE;

        --ra-danger: #B42318;
        --ra-danger-soft: #FCEBE9;

        --ra-shadow: 0 8px 24px rgba(86, 28, 23, 0.055);
        --ra-shadow-hover: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ra-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--ra-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .ra-page * {
        box-sizing: border-box;
    }

    .ra-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--ra-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--ra-shadow);
    }

    .ra-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--ra-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .ra-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .ra-hero h1 {
        margin: 10px 0 0;
        color: var(--ra-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .ra-hero p {
        max-width: 680px;
        margin: 13px 0 0;
        color: var(--ra-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .ra-pending-pill {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        gap: 7px;
        padding: 0 12px;
        border: 1px solid #EAD39A;
        border-radius: 999px;
        background: var(--ra-warning-soft);
        color: var(--ra-warning);
        font-size: 9px;
        font-weight: 900;
        white-space: nowrap;
    }

    .ra-pending-pill::before {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .ra-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0,1fr));
        gap: 14px;
    }

    .ra-stat {
        display: flex;
        min-height: 128px;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        padding: 16px;
        border: 1px solid var(--ra-border);
        border-radius: 20px;
        background:
            radial-gradient(circle at 94% 6%, rgba(193,151,113,.14), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
        box-shadow: var(--ra-shadow);
        transition: 160ms ease;
    }

    .ra-stat:hover {
        transform: translateY(-2px);
        border-color: var(--ra-tan);
        box-shadow: var(--ra-shadow-hover);
    }

    .ra-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .ra-stat-icon {
        display: grid;
        width: 38px;
        height: 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 13px;
        background: var(--ra-bg-warm);
        color: var(--ra-maroon);
    }

    .ra-stat-icon.is-warning {
        border-color: #EAD39A;
        background: var(--ra-warning-soft);
        color: var(--ra-warning);
    }

    .ra-stat-icon.is-success {
        border-color: #CFE8DA;
        background: var(--ra-success-soft);
        color: var(--ra-success);
    }

    .ra-stat-icon.is-danger {
        border-color: #EDC9C5;
        background: var(--ra-danger-soft);
        color: var(--ra-danger);
    }

    .ra-stat-icon svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.75;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ra-stat-label {
        display: block;
        color: var(--ra-muted);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .ra-stat-value {
        display: block;
        margin-top: 6px;
        color: var(--ra-text-dark);
        font-size: 28px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.045em;
    }

    .ra-stat-description {
        display: block;
        margin-top: 6px;
        color: var(--ra-muted);
        font-size: 9px;
        font-weight: 700;
    }

    .ra-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 15px;
        border: 1px solid var(--ra-border);
        border-radius: 20px;
        background: var(--ra-card);
        box-shadow: var(--ra-shadow);
    }

    .ra-search {
        position: relative;
        width: min(380px, 100%);
    }

    .ra-search svg {
        position: absolute;
        top: 50%;
        left: 13px;
        width: 16px;
        height: 16px;
        color: var(--ra-muted);
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .ra-search input {
        width: 100%;
        min-height: 40px;
        padding: 0 14px 0 40px;
        border: 1px solid var(--ra-border);
        border-radius: 12px;
        background: var(--ra-bg-soft);
        color: var(--ra-text);
        font-size: 10px;
        font-weight: 750;
        outline: none;
        transition: 150ms ease;
    }

    .ra-search input::placeholder {
        color: var(--ra-muted-light);
    }

    .ra-search input:focus {
        border-color: var(--ra-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86,28,23,.07);
    }

    .ra-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .ra-filter {
        min-height: 36px;
        padding: 0 13px;
        border: 1px solid var(--ra-border);
        border-radius: 10px;
        background: var(--ra-card);
        color: var(--ra-brown);
        font-size: 9px;
        font-weight: 900;
        cursor: pointer;
        transition: 150ms ease;
    }

    .ra-filter:hover {
        border-color: var(--ra-tan);
        background: var(--ra-bg-soft);
        color: var(--ra-maroon);
    }

    .ra-filter.is-active {
        border-color: var(--ra-maroon);
        background: var(--ra-maroon);
        color: #FFFFFF;
    }

    .ra-list-card {
        overflow: hidden;
        border: 1px solid var(--ra-border);
        border-radius: 22px;
        background: var(--ra-card);
        box-shadow: var(--ra-shadow);
    }

    .ra-list-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--ra-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ra-list-head h2 {
        margin: 0;
        color: var(--ra-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .ra-list-head p {
        margin: 7px 0 0;
        color: var(--ra-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .ra-result-count {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        padding: 0 9px;
        border: 1px solid #E6C7BE;
        border-radius: 999px;
        background: var(--ra-bg-warm);
        color: var(--ra-maroon);
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .ra-list {
        display: grid;
    }

    .ra-applicant {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 14px;
        padding: 17px 20px;
        border-bottom: 1px solid var(--ra-border);
        transition: 150ms ease;
    }

    .ra-applicant:last-child {
        border-bottom: 0;
    }

    .ra-applicant:hover {
        background: var(--ra-bg-soft);
    }

    .ra-avatar {
        display: grid;
        width: 46px;
        height: 46px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 15px;
        background: var(--ra-bg-warm);
        color: var(--ra-maroon);
    }

    .ra-avatar svg {
        width: 20px;
        height: 20px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ra-applicant-main {
        min-width: 0;
    }

    .ra-applicant-top {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ra-applicant-main h3 {
        margin: 0;
        color: var(--ra-text);
        font-size: 11px;
        font-weight: 950;
    }

    .ra-applicant-main p {
        margin: 5px 0 0;
        color: var(--ra-muted);
        font-size: 9px;
    }

    .ra-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px 12px;
        margin-top: 8px;
        color: var(--ra-brown);
        font-size: 8px;
        font-weight: 750;
    }

    .ra-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .ra-meta svg {
        width: 12px;
        height: 12px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .ra-status {
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

    .ra-status::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .ra-status.is-pending {
        border-color: #EAD39A;
        background: var(--ra-warning-soft);
        color: var(--ra-warning);
    }

    .ra-status.is-approved {
        border-color: #CFE8DA;
        background: var(--ra-success-soft);
        color: var(--ra-success);
    }

    .ra-status.is-rejected {
        border-color: #EDC9C5;
        background: var(--ra-danger-soft);
        color: var(--ra-danger);
    }

    .ra-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 7px;
    }

    .ra-btn {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 11px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: 9px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        transition: 150ms ease;
    }

    .ra-btn:hover {
        transform: translateY(-1px);
    }

    .ra-btn svg {
        width: 13px;
        height: 13px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ra-btn-soft {
        border-color: var(--ra-border);
        background: var(--ra-card);
        color: var(--ra-maroon);
    }

    .ra-btn-soft:hover {
        border-color: var(--ra-tan);
        background: var(--ra-bg-warm);
    }

    .ra-btn-approve {
        border-color: var(--ra-success);
        background: var(--ra-success);
        color: #FFFFFF;
    }

    .ra-btn-approve:hover {
        background: #1D5A3C;
        border-color: #1D5A3C;
    }

    .ra-btn-reject {
        border-color: #D8AAA5;
        background: #FFFFFF;
        color: var(--ra-danger);
    }

    .ra-btn-reject:hover {
        background: var(--ra-danger-soft);
        border-color: #D99992;
    }

    .ra-empty {
        display: none;
        padding: 42px 20px;
        text-align: center;
        color: var(--ra-muted);
        font-size: 10px;
    }

    .ra-empty.is-visible {
        display: block;
    }

    @media (max-width: 1180px) {
        .ra-stats {
            grid-template-columns: repeat(2, minmax(0,1fr));
        }

        .ra-applicant {
            grid-template-columns: auto minmax(0,1fr);
        }

        .ra-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
            padding-left: 60px;
        }
    }

    @media (max-width: 820px) {
        .ra-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .ra-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .ra-search {
            width: 100%;
        }
    }

    @media (max-width: 560px) {
        .ra-stats {
            grid-template-columns: 1fr;
        }

        .ra-applicant {
            grid-template-columns: 1fr;
        }

        .ra-actions {
            grid-column: auto;
            justify-content: flex-start;
            padding-left: 0;
        }

        .ra-avatar {
            width: 42px;
            height: 42px;
        }

        .ra-list-head {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    html.dark .ra-page {
        color: #F5EFE8;
    }

    html.dark .ra-hero,
    html.dark .ra-stat,
    html.dark .ra-toolbar,
    html.dark .ra-list-card {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .ra-list-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .ra-hero h1,
    html.dark .ra-stat-value,
    html.dark .ra-list-head h2,
    html.dark .ra-applicant-main h3 {
        color: #F5EFE8 !important;
    }

    html.dark .ra-hero p,
    html.dark .ra-stat-label,
    html.dark .ra-stat-description,
    html.dark .ra-list-head p,
    html.dark .ra-applicant-main p,
    html.dark .ra-meta {
        color: #AFA19A !important;
    }

    html.dark .ra-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .ra-search input {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ra-search input:focus {
        background: #211B17 !important;
        border-color: #60463A !important;
    }

    html.dark .ra-filter {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #C8B7AD !important;
    }

    html.dark .ra-filter:hover {
        background: #241817 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .ra-filter.is-active {
        background: #A84538 !important;
        border-color: #A84538 !important;
        color: #FFFFFF !important;
    }

    html.dark .ra-applicant {
        border-color: #30231F !important;
    }

    html.dark .ra-applicant:hover {
        background: #241817 !important;
    }

    html.dark .ra-avatar,
    html.dark .ra-stat-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .ra-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .ra-btn-reject {
        background: #1D1715 !important;
        border-color: #5B2925 !important;
        color: #F2A49A !important;
    }
</style>

<div class="ra-page">

    <section class="ra-hero">
        <div>
            <span class="ra-eyebrow">Rider Management</span>

            <h1>Rider Applications</h1>

            <p>
                Review courier registration requests, verify submitted details,
                and approve or reject applicants before they can accept deliveries.
            </p>
        </div>

        <span class="ra-pending-pill">
            12 Pending Reviews
        </span>
    </section>

    <section class="ra-stats" aria-label="Rider application summary">
        @foreach($summary as $item)
            @php
                $toneClass = match($item['tone']) {
                    'warning' => 'is-warning',
                    'success' => 'is-success',
                    'danger' => 'is-danger',
                    default => '',
                };
            @endphp

            <article class="ra-stat">
                <div class="ra-stat-top">
                    <span class="ra-stat-icon {{ $toneClass }}" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons[$item['icon']] !!}
                        </svg>
                    </span>
                </div>

                <div>
                    <span class="ra-stat-label">
                        {{ $item['label'] }}
                    </span>

                    <strong class="ra-stat-value">
                        {{ $item['value'] }}
                    </strong>

                    <span class="ra-stat-description">
                        {{ $item['description'] }}
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="ra-toolbar">
        <div class="ra-search">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['search'] !!}
            </svg>

            <input
                type="search"
                id="riderApplicationSearch"
                placeholder="Search applicant, email, vehicle, plate..."
                autocomplete="off"
            >
        </div>

        <div class="ra-filters" aria-label="Application filters">
            @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
                <button
                    type="button"
                    class="ra-filter {{ $key === 'all' ? 'is-active' : '' }}"
                    data-filter="{{ $key }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </section>

    <section class="ra-list-card">
        <header class="ra-list-head">
            <div>
                <h2>Courier Applicants</h2>
                <p>Verify applicant information and registration requirements.</p>
            </div>

            <span class="ra-result-count">
                <span id="visibleApplicationCount">{{ count($riders) }}</span>&nbsp;shown
            </span>
        </header>

        <div class="ra-list" id="riderApplicationList">
            @foreach($riders as $rider)
                @php
                    $statusKey = match($rider['status']) {
                        'Approved' => 'approved',
                        'Rejected' => 'rejected',
                        default => 'pending',
                    };

                    $statusClass = match($statusKey) {
                        'approved' => 'is-approved',
                        'rejected' => 'is-rejected',
                        default => 'is-pending',
                    };
                @endphp

                <article
                    class="ra-applicant"
                    data-status="{{ $statusKey }}"
                    data-search="{{ mb_strtolower(
                        $rider['name']
                        . ' '
                        . $rider['email']
                        . ' '
                        . $rider['vehicle']
                        . ' '
                        . $rider['plate']
                        . ' '
                        . $rider['area']
                    ) }}"
                >
                    <span class="ra-avatar" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['rider'] !!}
                        </svg>
                    </span>

                    <div class="ra-applicant-main">
                        <div class="ra-applicant-top">
                            <h3>{{ $rider['name'] }}</h3>

                            <span class="ra-status {{ $statusClass }}">
                                {{ $rider['status'] }}
                            </span>
                        </div>

                        <p>{{ $rider['email'] }}</p>

                        <div class="ra-meta">
                            <span>
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    {!! $icons['vehicle'] !!}
                                </svg>

                                {{ $rider['vehicle'] }} · {{ $rider['plate'] }}
                            </span>

                            <span>{{ $rider['area'] }}</span>

                            <span>Submitted {{ $rider['submitted'] }}</span>
                        </div>
                    </div>

                    <div class="ra-actions">
                        <a
                            href="{{ route('logistics.riders.show', $rider['id']) }}"
                            class="ra-btn ra-btn-soft"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['eye'] !!}
                            </svg>

                            View
                        </a>

                        @if($rider['status'] === 'Pending Approval')
                            <form
                                method="POST"
                                action="{{ route('logistics.riders.approve', $rider['id']) }}"
                            >
                                @csrf

                                <button type="submit" class="ra-btn ra-btn-approve">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        {!! $icons['approve'] !!}
                                    </svg>

                                    Approve
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('logistics.riders.reject', $rider['id']) }}"
                            >
                                @csrf

                                <button type="submit" class="ra-btn ra-btn-reject">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        {!! $icons['reject'] !!}
                                    </svg>

                                    Reject
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div id="riderApplicationEmpty" class="ra-empty">
            No rider applications match the selected filter.
        </div>
    </section>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput =
        document.getElementById('riderApplicationSearch');

    const filterButtons =
        Array.from(
            document.querySelectorAll('.ra-filter')
        );

    const applications =
        Array.from(
            document.querySelectorAll('.ra-applicant')
        );

    const resultCount =
        document.getElementById('visibleApplicationCount');

    const emptyState =
        document.getElementById('riderApplicationEmpty');

    let activeFilter = 'all';


    function applyFilters() {
        const search =
            (searchInput?.value || '')
                .trim()
                .toLowerCase();

        let visible = 0;

        applications.forEach(function (application) {
            const matchesFilter =
                activeFilter === 'all' ||
                application.dataset.status === activeFilter;

            const matchesSearch =
                !search ||
                (application.dataset.search || '')
                    .includes(search);

            const show =
                matchesFilter && matchesSearch;

            application.hidden = !show;

            if (show) {
                visible++;
            }
        });

        if (resultCount) {
            resultCount.textContent = visible;
        }

        if (emptyState) {
            emptyState.classList.toggle(
                'is-visible',
                visible === 0
            );
        }
    }


    filterButtons.forEach(function (button) {
        button.addEventListener(
            'click',
            function () {
                activeFilter =
                    button.dataset.filter || 'all';

                filterButtons.forEach(function (item) {
                    item.classList.toggle(
                        'is-active',
                        item === button
                    );
                });

                applyFilters();
            }
        );
    });


    searchInput?.addEventListener(
        'input',
        applyFilters
    );


    applyFilters();
});
</script>
@endpush
