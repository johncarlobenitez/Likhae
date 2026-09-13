@extends('logistics.app')

@section('title', 'Profile & Settings — LIKHAE Logistics')

@php
    $user = auth()->user();

    $profileName = data_get($user, 'name', 'Logistics Administrator');
    $profileEmail = data_get($user, 'email', 'logistics@likhae.test');

    $nameParts = collect(preg_split('/\s+/', trim($profileName)))
        ->filter()
        ->values();

    $firstName = $nameParts->first() ?: 'Logistics';
    $lastName = $nameParts->count() > 1
        ? $nameParts->slice(1)->implode(' ')
        : 'Administrator';

    $initials = $nameParts
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'LG';

    $notifications = [
        [
            'title' => 'Parcel updates',
            'description' => 'Receive notifications when parcel status changes.',
            'checked' => true,
        ],
        [
            'title' => 'Rider applications',
            'description' => 'Get notified when a new rider applies.',
            'checked' => true,
        ],
        [
            'title' => 'Delivery alerts',
            'description' => 'Receive failed or delayed delivery alerts.',
            'checked' => true,
        ],
        [
            'title' => 'Daily summary',
            'description' => 'Receive the daily logistics summary.',
            'checked' => false,
        ],
    ];

    $systemInfo = [
        ['label' => 'Platform', 'value' => 'LIKHAE Marketplace'],
        ['label' => 'Module', 'value' => 'Logistics'],
        ['label' => 'Version', 'value' => 'v1.0.0'],
        ['label' => 'Status', 'value' => 'Operational'],
    ];

    $icons = [
        'user' => '
            <circle cx="12" cy="8" r="4"/>
            <path d="M4 21c0-5 3-8 8-8s8 3 8 8"/>
        ',
        'camera' => '
            <path d="M4 7h3l1.5-2h7L17 7h3v12H4z"/>
            <circle cx="12" cy="13" r="3.5"/>
        ',
        'shield' => '
            <path d="M12 3 5 6v5c0 4.5 2.8 8 7 10 4.2-2 7-5.5 7-10V6z"/>
            <path d="m9 12 2 2 4-4"/>
        ',
        'bell' => '
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"/>
            <path d="M10 19h4"/>
        ',
        'moon' => '
            <path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"/>
        ',
        'sun' => '
            <circle cx="12" cy="12" r="4"/>
            <path d="M12 2v2"/>
            <path d="M12 20v2"/>
            <path d="M4.93 4.93l1.41 1.41"/>
            <path d="M17.66 17.66l1.41 1.41"/>
            <path d="M2 12h2"/>
            <path d="M20 12h2"/>
            <path d="M6.34 17.66l-1.41 1.41"/>
            <path d="M19.07 4.93l-1.41 1.41"/>
        ',
        'monitor' => '
            <rect x="3" y="4" width="18" height="12" rx="2"/>
            <path d="M8 20h8"/>
            <path d="M12 16v4"/>
        ',
        'info' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 10v6"/>
            <path d="M12 7h.01"/>
        ',
        'logout' => '
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <path d="m16 17 5-5-5-5"/>
            <path d="M21 12H9"/>
        ',
        'check' => '
            <path d="m5 12 4 4 10-10"/>
        ',
        'lock' => '
            <rect x="5" y="10" width="14" height="11" rx="2"/>
            <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --ps-bg: #FBF7F2;
        --ps-bg-soft: #F6EFE7;
        --ps-bg-warm: #F3E4DE;
        --ps-card: #FFFDF9;

        --ps-border: #EADCCC;
        --ps-border-strong: #DBCEC1;

        --ps-maroon: #561C17;
        --ps-maroon-2: #642920;
        --ps-maroon-dark: #3E130F;

        --ps-text: #3B211B;
        --ps-text-dark: #1C160F;
        --ps-brown: #6C4936;
        --ps-muted: #987865;
        --ps-muted-light: #A99386;

        --ps-tan: #C19771;

        --ps-success: #256F4A;
        --ps-success-soft: #EAF7EF;

        --ps-danger: #B42318;
        --ps-danger-soft: #FCEBE9;

        --ps-shadow: 0 8px 24px rgba(86, 28, 23, 0.055);
        --ps-shadow-hover: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ps-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--ps-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .ps-page * {
        box-sizing: border-box;
    }

    .ps-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--ps-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193, 151, 113, 0.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86, 28, 23, 0.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--ps-shadow);
    }

    .ps-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--ps-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: 0.20em;
        text-transform: uppercase;
    }

    .ps-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .ps-hero h1 {
        margin: 10px 0 0;
        color: var(--ps-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: 0.94;
        letter-spacing: -0.055em;
    }

    .ps-hero p {
        max-width: 700px;
        margin: 13px 0 0;
        color: var(--ps-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .ps-card {
        overflow: hidden;
        border: 1px solid var(--ps-border);
        border-radius: 22px;
        background: var(--ps-card);
        box-shadow: var(--ps-shadow);
    }

    .ps-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--ps-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ps-card-head h2 {
        margin: 0;
        color: var(--ps-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .ps-card-head p {
        margin: 7px 0 0;
        color: var(--ps-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .ps-card-body {
        padding: 20px;
    }

    .ps-profile {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 16px;
        padding: 20px;
    }

    .ps-avatar {
        display: grid;
        width: 76px;
        height: 76px;
        flex: 0 0 76px;
        place-items: center;
        overflow: hidden;
        border: 1px solid #E6C7BE;
        border-radius: 999px;
        background:
            radial-gradient(circle at 30% 20%, rgba(255,255,255,.2), transparent 28%),
            var(--ps-maroon);
        color: #FFFFFF;
        box-shadow: 0 12px 26px rgba(86,28,23,.16);
        background-size: cover;
        background-position: center;
    }

    .ps-avatar span {
        font-size: 18px;
        font-weight: 950;
    }

    .ps-profile-copy {
        min-width: 0;
    }

    .ps-profile-copy h2 {
        margin: 0;
        color: var(--ps-text);
        font-size: 17px;
        font-weight: 950;
        line-height: 1.15;
    }

    .ps-profile-copy p {
        margin: 6px 0 0;
        color: var(--ps-muted);
        font-size: 9px;
        font-weight: 700;
    }

    .ps-active {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 0 9px;
        border: 1px solid #CFE8DA;
        border-radius: 999px;
        background: var(--ps-success-soft);
        color: var(--ps-success);
        font-size: 8px;
        font-weight: 900;
    }

    .ps-active::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .ps-btn {
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

    .ps-btn:hover {
        transform: translateY(-1px);
    }

    .ps-btn svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ps-btn-primary {
        background: var(--ps-maroon);
        border-color: var(--ps-maroon);
        color: #FFFFFF;
        box-shadow: 0 10px 22px rgba(86,28,23,0.14);
    }

    .ps-btn-primary:hover {
        background: var(--ps-maroon-dark);
        border-color: var(--ps-maroon-dark);
    }

    .ps-btn-soft {
        background: var(--ps-card);
        border-color: var(--ps-border);
        color: var(--ps-maroon);
    }

    .ps-btn-soft:hover {
        background: var(--ps-bg-warm);
        border-color: var(--ps-tan);
    }

    .ps-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 14px;
    }

    .ps-field label {
        display: block;
        margin-bottom: 7px;
        color: var(--ps-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .ps-input {
        width: 100%;
        min-height: 41px;
        padding: 0 12px;
        border: 1px solid var(--ps-border);
        border-radius: 12px;
        background: var(--ps-bg-soft);
        color: var(--ps-text);
        font-size: 10px;
        font-weight: 750;
        outline: none;
        transition: 150ms ease;
    }

    .ps-input::placeholder {
        color: var(--ps-muted-light);
    }

    .ps-input:focus {
        border-color: var(--ps-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86,28,23,.07);
    }

    .ps-input[readonly] {
        cursor: not-allowed;
        color: var(--ps-muted);
        background: #F1EAE2;
    }

    .ps-form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 16px;
    }

    .ps-main-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 18px;
        align-items: start;
    }

    .ps-section-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--ps-bg-warm);
        color: var(--ps-maroon);
    }

    .ps-section-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ps-password-stack {
        display: grid;
        gap: 12px;
    }

    .ps-notifications {
        display: grid;
        gap: 10px;
    }

    .ps-notification {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 13px;
        border: 1px solid var(--ps-border);
        border-radius: 14px;
        background: var(--ps-bg-soft);
        transition: 150ms ease;
        cursor: pointer;
    }

    .ps-notification:hover {
        border-color: var(--ps-tan);
        background: #FFF9F2;
    }

    .ps-notification strong {
        display: block;
        color: var(--ps-text);
        font-size: 9px;
        font-weight: 900;
    }

    .ps-notification span {
        display: block;
        margin-top: 4px;
        color: var(--ps-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .ps-switch {
        position: relative;
        display: inline-flex;
        width: 38px;
        height: 22px;
        flex: 0 0 38px;
    }

    .ps-switch input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
    }

    .ps-switch-track {
        position: absolute;
        inset: 0;
        border: 1px solid var(--ps-border);
        border-radius: 999px;
        background: #E7E4E1;
        transition: 160ms ease;
    }

    .ps-switch-track::after {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 14px;
        height: 14px;
        border-radius: 999px;
        background: #FFFFFF;
        box-shadow: 0 2px 6px rgba(0,0,0,.14);
        content: "";
        transition: 160ms ease;
    }

    .ps-switch input:checked + .ps-switch-track {
        border-color: var(--ps-maroon);
        background: var(--ps-maroon);
    }

    .ps-switch input:checked + .ps-switch-track::after {
        transform: translateX(16px);
    }

    .ps-theme-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0,1fr));
        gap: 12px;
    }

    .ps-theme-option {
        position: relative;
        display: grid;
        gap: 12px;
        min-height: 128px;
        padding: 15px;
        border: 1px solid var(--ps-border);
        border-radius: 16px;
        background: var(--ps-bg-soft);
        color: var(--ps-text);
        font: inherit;
        text-align: left;
        cursor: pointer;
        transition: 150ms ease;
    }

    .ps-theme-option:hover {
        transform: translateY(-1px);
        border-color: var(--ps-tan);
    }

    .ps-theme-option.is-selected {
        border-color: var(--ps-maroon);
        background: var(--ps-bg-warm);
        box-shadow: inset 0 0 0 1px var(--ps-maroon);
    }

    .ps-theme-preview {
        display: grid;
        height: 50px;
        place-items: center;
        border: 1px solid var(--ps-border);
        border-radius: 11px;
        background: #FFFDF9;
        color: var(--ps-maroon);
    }

    .ps-theme-option[data-theme-option="dark"] .ps-theme-preview {
        border-color: #3B2E27;
        background: #171210;
        color: #EBA99D;
    }

    .ps-theme-option[data-theme-option="system"] .ps-theme-preview {
        background:
            linear-gradient(
                90deg,
                #FFFDF9 0%,
                #FFFDF9 50%,
                #171210 50%,
                #171210 100%
            );
        color: var(--ps-maroon);
    }

    .ps-theme-preview svg {
        width: 20px;
        height: 20px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ps-theme-option strong {
        color: var(--ps-text);
        font-size: 10px;
        font-weight: 900;
    }

    .ps-theme-option span {
        color: var(--ps-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .ps-system-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0,1fr));
        gap: 10px;
    }

    .ps-system-item {
        min-width: 0;
        padding: 14px;
        border: 1px solid var(--ps-border);
        border-radius: 14px;
        background: var(--ps-bg-soft);
    }

    .ps-system-item span {
        display: block;
        color: var(--ps-muted);
        font-size: 8px;
        font-weight: 850;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .ps-system-item strong {
        display: block;
        margin-top: 7px;
        color: var(--ps-text);
        font-size: 9px;
        font-weight: 900;
    }

    .ps-danger-card {
        border-color: #EDC9C5;
        background:
            radial-gradient(circle at 96% 6%, rgba(180,35,24,.06), transparent 28%),
            #FFF9F7;
    }

    .ps-danger-card .ps-card-head {
        border-color: #EDC9C5;
        background: #FFF3F0;
    }

    .ps-danger-card .ps-card-head h2 {
        color: var(--ps-danger);
    }

    .ps-danger-card .ps-card-head p {
        color: #9C5C56;
    }

    .ps-btn-danger {
        border-color: #E8B9B3;
        background: #FFFFFF;
        color: var(--ps-danger);
    }

    .ps-btn-danger:hover {
        background: var(--ps-danger-soft);
        border-color: #DDA19A;
    }

    .ps-modal {
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

    .ps-modal.is-open {
        visibility: visible;
        opacity: 1;
    }

    .ps-modal-panel {
        width: min(430px, 100%);
        padding: 24px;
        border: 1px solid var(--ps-border);
        border-radius: 22px;
        background: var(--ps-card);
        box-shadow: var(--ps-shadow-hover);
        transform: scale(.96);
        transition: 180ms ease;
    }

    .ps-modal.is-open .ps-modal-panel {
        transform: scale(1);
    }

    .ps-modal-icon {
        display: grid;
        width: 54px;
        height: 54px;
        place-items: center;
        border-radius: 999px;
        background: var(--ps-success-soft);
        color: var(--ps-success);
    }

    .ps-modal-icon svg {
        width: 23px;
        height: 23px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ps-modal-panel h2 {
        margin: 16px 0 0;
        color: var(--ps-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .ps-modal-panel p {
        margin: 10px 0 0;
        color: var(--ps-muted);
        font-size: 10px;
        line-height: 1.55;
    }

    @media (max-width: 1020px) {
        .ps-main-grid {
            grid-template-columns: 1fr;
        }

        .ps-system-grid {
            grid-template-columns: repeat(2, minmax(0,1fr));
        }
    }

    @media (max-width: 760px) {
        .ps-hero {
            padding: 26px 22px;
        }

        .ps-profile {
            grid-template-columns: auto minmax(0,1fr);
        }

        .ps-profile > .ps-btn {
            grid-column: 1 / -1;
            width: 100%;
        }

        .ps-grid-2,
        .ps-theme-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 520px) {
        .ps-profile {
            grid-template-columns: 1fr;
            justify-items: start;
        }

        .ps-system-grid {
            grid-template-columns: 1fr;
        }

        .ps-form-actions .ps-btn {
            width: 100%;
        }
    }

    html.dark .ps-page {
        color: #F5EFE8;
    }

    html.dark .ps-hero,
    html.dark .ps-card,
    html.dark .ps-modal-panel {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,0.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .ps-card-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,0.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .ps-hero h1,
    html.dark .ps-profile-copy h2,
    html.dark .ps-card-head h2,
    html.dark .ps-notification strong,
    html.dark .ps-theme-option strong,
    html.dark .ps-system-item strong,
    html.dark .ps-modal-panel h2 {
        color: #F5EFE8 !important;
    }

    html.dark .ps-hero p,
    html.dark .ps-profile-copy p,
    html.dark .ps-card-head p,
    html.dark .ps-field label,
    html.dark .ps-notification span,
    html.dark .ps-theme-option span,
    html.dark .ps-system-item span,
    html.dark .ps-modal-panel p {
        color: #AFA19A !important;
    }

    html.dark .ps-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .ps-input,
    html.dark .ps-notification,
    html.dark .ps-system-item,
    html.dark .ps-theme-option {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ps-input:focus {
        background: #211B17 !important;
        border-color: #60463A !important;
    }

    html.dark .ps-input[readonly] {
        background: #171210 !important;
        color: #AFA19A !important;
    }

    html.dark .ps-notification:hover,
    html.dark .ps-theme-option:hover {
        background: #241817 !important;
        border-color: #60463A !important;
    }

    html.dark .ps-theme-option.is-selected {
        background: #2D1816 !important;
        border-color: #A84538 !important;
        box-shadow: inset 0 0 0 1px #A84538 !important;
    }

    html.dark .ps-theme-option[data-theme-option="light"] .ps-theme-preview {
        background: #FFFDF9 !important;
        border-color: #EADCCC !important;
        color: #561C17 !important;
    }

    html.dark .ps-theme-option[data-theme-option="dark"] .ps-theme-preview {
        background: #130F0E !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .ps-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .ps-btn-primary:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }

    html.dark .ps-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .ps-danger-card {
        background: #211312 !important;
        border-color: #5B2925 !important;
    }

    html.dark .ps-danger-card .ps-card-head {
        background: #2A1513 !important;
        border-color: #5B2925 !important;
    }

    html.dark .ps-danger-card .ps-card-head h2 {
        color: #F2A49A !important;
    }

    html.dark .ps-danger-card .ps-card-head p {
        color: #C99A94 !important;
    }

    html.dark .ps-btn-danger {
        background: #1D1715 !important;
        border-color: #5B2925 !important;
        color: #F2A49A !important;
    }
</style>

<div class="ps-page">

    <section class="ps-hero">
        <div>
            <span class="ps-eyebrow">Account</span>

            <h1>Profile & Settings</h1>

            <p>
                Manage your logistics account, security, notifications,
                and interface preferences from one place.
            </p>
        </div>
    </section>

    <section class="ps-card">
        <div class="ps-profile">
            <div id="profilePhotoPreview" class="ps-avatar">
                <span id="profilePhotoInitials">{{ $initials }}</span>
            </div>

            <div class="ps-profile-copy">
                <h2>{{ $profileName }}</h2>
                <p>{{ $profileEmail }}</p>

                <span class="ps-active">
                    Active Account
                </span>
            </div>

            <button
                type="button"
                id="changePhotoButton"
                class="ps-btn ps-btn-soft"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['camera'] !!}
                </svg>

                Change Photo
            </button>

            <input
                type="file"
                id="profilePhotoInput"
                accept="image/jpeg,image/png,image/webp"
                hidden
            >
        </div>
    </section>

    <section class="ps-card">
        <header class="ps-card-head">
            <div>
                <h2>Personal Information</h2>
                <p>Update the information associated with your logistics account.</p>
            </div>

            <span class="ps-section-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    {!! $icons['user'] !!}
                </svg>
            </span>
        </header>

        <div class="ps-card-body">
            <form id="profileForm">
                <div class="ps-grid-2">
                    <div class="ps-field">
                        <label for="profileFirstName">First Name</label>

                        <input
                            id="profileFirstName"
                            type="text"
                            value="{{ $firstName }}"
                            class="ps-input"
                        >
                    </div>

                    <div class="ps-field">
                        <label for="profileLastName">Last Name</label>

                        <input
                            id="profileLastName"
                            type="text"
                            value="{{ $lastName }}"
                            class="ps-input"
                        >
                    </div>

                    <div class="ps-field">
                        <label for="profileEmail">Email</label>

                        <input
                            id="profileEmail"
                            type="email"
                            value="{{ $profileEmail }}"
                            class="ps-input"
                        >
                    </div>

                    <div class="ps-field">
                        <label for="profileContact">Contact Number</label>

                        <input
                            id="profileContact"
                            type="text"
                            value="0917 000 0000"
                            class="ps-input"
                        >
                    </div>

                    <div class="ps-field" style="grid-column:1 / -1;">
                        <label for="profileRole">Role</label>

                        <input
                            id="profileRole"
                            type="text"
                            value="Logistics Administrator"
                            readonly
                            class="ps-input"
                        >
                    </div>
                </div>

                <div class="ps-form-actions">
                    <button type="submit" class="ps-btn ps-btn-primary">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="ps-main-grid">

        <section class="ps-card">
            <header class="ps-card-head">
                <div>
                    <h2>Security</h2>
                    <p>Keep your logistics account protected.</p>
                </div>

                <span class="ps-section-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        {!! $icons['shield'] !!}
                    </svg>
                </span>
            </header>

            <div class="ps-card-body">
                <form id="passwordForm" class="ps-password-stack">
                    <div class="ps-field">
                        <label for="currentPassword">Current Password</label>

                        <input
                            id="currentPassword"
                            type="password"
                            placeholder="Enter current password"
                            class="ps-input"
                        >
                    </div>

                    <div class="ps-field">
                        <label for="newPassword">New Password</label>

                        <input
                            id="newPassword"
                            type="password"
                            placeholder="Enter new password"
                            class="ps-input"
                        >
                    </div>

                    <div class="ps-field">
                        <label for="confirmPassword">Confirm Password</label>

                        <input
                            id="confirmPassword"
                            type="password"
                            placeholder="Confirm new password"
                            class="ps-input"
                        >
                    </div>

                    <div>
                        <button type="submit" class="ps-btn ps-btn-soft">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['lock'] !!}
                            </svg>

                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="ps-card">
            <header class="ps-card-head">
                <div>
                    <h2>Notifications</h2>
                    <p>Choose which logistics updates you want to receive.</p>
                </div>

                <span class="ps-section-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        {!! $icons['bell'] !!}
                    </svg>
                </span>
            </header>

            <div class="ps-card-body">
                <div class="ps-notifications">
                    @foreach($notifications as $notification)
                        <label class="ps-notification">
                            <span>
                                <strong>{{ $notification['title'] }}</strong>
                                <span>{{ $notification['description'] }}</span>
                            </span>

                            <span class="ps-switch">
                                <input
                                    type="checkbox"
                                    class="notification-toggle"
                                    {{ $notification['checked'] ? 'checked' : '' }}
                                >

                                <span class="ps-switch-track"></span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </section>

    </section>

    <section class="ps-card">
        <header class="ps-card-head">
            <div>
                <h2>Appearance</h2>
                <p>Choose how the LIKHAE Logistics interface appears.</p>
            </div>
        </header>

        <div class="ps-card-body">
            <div class="ps-theme-grid">
                <button
                    type="button"
                    data-theme-option="light"
                    class="ps-theme-option"
                >
                    <span class="ps-theme-preview">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['sun'] !!}
                        </svg>
                    </span>

                    <span>
                        <strong>Light</strong>
                        <span>Warm cream interface with LIKHAE maroon accents.</span>
                    </span>
                </button>

                <button
                    type="button"
                    data-theme-option="dark"
                    class="ps-theme-option"
                >
                    <span class="ps-theme-preview">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['moon'] !!}
                        </svg>
                    </span>

                    <span>
                        <strong>Dark</strong>
                        <span>Warm near-black interface for low-light use.</span>
                    </span>
                </button>

                <button
                    type="button"
                    data-theme-option="system"
                    class="ps-theme-option"
                >
                    <span class="ps-theme-preview">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['monitor'] !!}
                        </svg>
                    </span>

                    <span>
                        <strong>System</strong>
                        <span>Follow the current appearance preference of your device.</span>
                    </span>
                </button>
            </div>
        </div>
    </section>

    <section class="ps-card">
        <header class="ps-card-head">
            <div>
                <h2>System Information</h2>
                <p>Current LIKHAE Logistics environment.</p>
            </div>

            <span class="ps-section-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    {!! $icons['info'] !!}
                </svg>
            </span>
        </header>

        <div class="ps-card-body">
            <div class="ps-system-grid">
                @foreach($systemInfo as $item)
                    <div class="ps-system-item">
                        <span>{{ $item['label'] }}</span>
                        <strong>{{ $item['value'] }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="ps-card ps-danger-card">
        <header class="ps-card-head">
            <div>
                <h2>Account Actions</h2>
                <p>Sign out of the current logistics session.</p>
            </div>
        </header>

        <div class="ps-card-body">
            <button
                type="button"
                id="logoutButton"
                class="ps-btn ps-btn-danger"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['logout'] !!}
                </svg>

                Log Out
            </button>
        </div>
    </section>

</div>

<div id="settingsModal" class="ps-modal">
    <div id="settingsModalPanel" class="ps-modal-panel">
        <span class="ps-modal-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
                {!! $icons['check'] !!}
            </svg>
        </span>

        <h2 id="settingsModalTitle">
            Settings Saved
        </h2>

        <p id="settingsModalText">
            Your changes have been saved.
        </p>

        <button
            type="button"
            id="closeSettingsModal"
            class="ps-btn ps-btn-primary"
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
    const root =
        document.documentElement;

    const modal =
        document.getElementById('settingsModal');

    const modalTitle =
        document.getElementById('settingsModalTitle');

    const modalText =
        document.getElementById('settingsModalText');

    const changePhotoButton =
        document.getElementById('changePhotoButton');

    const profilePhotoInput =
        document.getElementById('profilePhotoInput');

    const profilePhotoPreview =
        document.getElementById('profilePhotoPreview');

    const profilePhotoInitials =
        document.getElementById('profilePhotoInitials');

    const savedPhoto =
        localStorage.getItem('likhae-profile-photo');


    function displayProfilePhoto(photoUrl) {
        if (!profilePhotoPreview) {
            return;
        }

        profilePhotoPreview.style.backgroundImage =
            `url("${photoUrl}")`;

        profilePhotoPreview.style.backgroundPosition =
            'center';

        profilePhotoPreview.style.backgroundSize =
            'cover';

        if (profilePhotoInitials) {
            profilePhotoInitials.style.display =
                'none';
        }
    }


    function openModal(title, text) {
        if (!modal) {
            return;
        }

        if (modalTitle) {
            modalTitle.textContent = title;
        }

        if (modalText) {
            modalText.textContent = text;
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


    function applyTheme(theme) {
        let useDark = false;

        if (theme === 'dark') {
            useDark = true;
        } else if (theme === 'system') {
            useDark =
                window.matchMedia(
                    '(prefers-color-scheme: dark)'
                ).matches;
        }

        root.classList.toggle(
            'dark',
            useDark
        );

        document
            .querySelectorAll('.ps-theme-option')
            .forEach(function (option) {
                option.classList.toggle(
                    'is-selected',
                    option.dataset.themeOption === theme
                );
            });

        const sidebarState =
            document.getElementById('logisticsThemeState');

        const sidebarOldState =
            document.getElementById('themeToggleTrack');

        if (sidebarState) {
            sidebarState.textContent =
                useDark ? 'ON' : 'OFF';
        }

        if (sidebarOldState) {
            sidebarOldState.textContent =
                useDark ? 'ON' : 'OFF';
        }
    }


    if (savedPhoto) {
        displayProfilePhoto(savedPhoto);
    }


    document
        .getElementById('profileForm')
        ?.addEventListener(
            'submit',
            function (event) {
                event.preventDefault();

                openModal(
                    'Profile Saved',
                    'Your profile information has been updated for this frontend prototype.'
                );
            }
        );


    document
        .getElementById('passwordForm')
        ?.addEventListener(
            'submit',
            function (event) {
                event.preventDefault();

                const newPassword =
                    document.getElementById('newPassword')?.value || '';

                const confirmPassword =
                    document.getElementById('confirmPassword')?.value || '';

                if (
                    newPassword &&
                    newPassword !== confirmPassword
                ) {
                    openModal(
                        'Passwords Do Not Match',
                        'Make sure the new password and confirmation password are identical.'
                    );

                    return;
                }

                openModal(
                    'Password Update',
                    'The password form is ready to connect to Laravel authentication.'
                );
            }
        );


    changePhotoButton?.addEventListener(
        'click',
        function () {
            profilePhotoInput?.click();
        }
    );


    profilePhotoInput?.addEventListener(
        'change',
        function () {
            const file =
                this.files?.[0];

            if (!file) {
                return;
            }

            if (
                !file.type.startsWith('image/') ||
                file.size > 5 * 1024 * 1024
            ) {
                this.value = '';

                openModal(
                    'Photo Not Added',
                    'Choose a JPG, PNG, or WEBP image smaller than 5 MB.'
                );

                return;
            }

            const reader =
                new FileReader();

            reader.addEventListener(
                'load',
                function () {
                    const photoUrl =
                        String(reader.result);

                    displayProfilePhoto(photoUrl);

                    try {
                        localStorage.setItem(
                            'likhae-profile-photo',
                            photoUrl
                        );
                    } catch (error) {
                        // Local storage can reject large images.
                    }

                    openModal(
                        'Profile Photo Updated',
                        'Your profile photo has been updated on this device.'
                    );
                }
            );

            reader.readAsDataURL(file);
        }
    );


    document
        .querySelectorAll('.ps-theme-option')
        .forEach(function (button) {
            button.addEventListener(
                'click',
                function () {
                    const theme =
                        button.dataset.themeOption || 'light';

                    localStorage.setItem(
                        'likhae-theme',
                        theme
                    );

                    applyTheme(theme);
                }
            );
        });


    const mediaQuery =
        window.matchMedia(
            '(prefers-color-scheme: dark)'
        );

    mediaQuery.addEventListener?.(
        'change',
        function () {
            const savedTheme =
                localStorage.getItem('likhae-theme');

            if (savedTheme === 'system') {
                applyTheme('system');
            }
        }
    );


    document
        .getElementById('logoutButton')
        ?.addEventListener(
            'click',
            function () {
                openModal(
                    'Log Out',
                    'Connect this action to your Laravel logout route when authentication handling is ready.'
                );
            }
        );


    document
        .getElementById('closeSettingsModal')
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


    const savedTheme =
        localStorage.getItem('likhae-theme') || 'light';

    applyTheme(
        ['light', 'dark', 'system'].includes(savedTheme)
            ? savedTheme
            : 'light'
    );
});
</script>
@endpush
