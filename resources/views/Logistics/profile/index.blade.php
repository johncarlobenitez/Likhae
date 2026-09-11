@extends('logistics.app')

@section('title', 'Profile & Settings — LIKHAE Logistics')

@section('content')

@php
    $account = [
        'first_name' => 'Logistics',
        'last_name' => 'Administrator',
        'email' => 'logistics@likhae.test',
        'contact' => '0917 000 0000',
        'role' => 'Logistics Administrator',
        'status' => 'Active Account',
        'initials' => 'LG',
    ];

    $notifications = [
        [
            'key' => 'parcel_updates',
            'title' => 'Parcel updates',
            'description' => 'Receive notifications whenever a parcel status changes.',
            'enabled' => true,
        ],
        [
            'key' => 'rider_applications',
            'title' => 'Rider applications',
            'description' => 'Get notified when a new courier registration is submitted.',
            'enabled' => true,
        ],
        [
            'key' => 'delivery_alerts',
            'title' => 'Delivery alerts',
            'description' => 'Receive failed, delayed, or exception delivery alerts.',
            'enabled' => true,
        ],
        [
            'key' => 'daily_summary',
            'title' => 'Daily summary',
            'description' => 'Receive a daily overview of logistics operations.',
            'enabled' => false,
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
            <path d="M4 7h3l2-3h6l2 3h3v12H4z"/>
            <circle cx="12" cy="13" r="4"/>
        ',
        'lock' => '
            <rect x="4" y="10" width="16" height="11" rx="2"/>
            <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
        ',
        'bell' => '
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
            <path d="M10 21h4"/>
        ',
        'sun' => '
            <circle cx="12" cy="12" r="4"/>
            <path d="M12 2v2"/>
            <path d="M12 20v2"/>
            <path d="m4.93 4.93 1.42 1.42"/>
            <path d="m17.66 17.66 1.41 1.41"/>
            <path d="M2 12h2"/>
            <path d="M20 12h2"/>
            <path d="m6.34 17.66-1.41 1.41"/>
            <path d="m19.07 4.93-1.41 1.42"/>
        ',
        'moon' => '
            <path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"/>
        ',
        'monitor' => '
            <rect x="3" y="4" width="18" height="13" rx="2"/>
            <path d="M8 21h8"/>
            <path d="M12 17v4"/>
        ',
        'info' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 11v5"/>
            <path d="M12 8h.01"/>
        ',
        'logout' => '
            <path d="M10 17l5-5-5-5"/>
            <path d="M15 12H3"/>
            <path d="M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5"/>
        ',
        'check' => '
            <path d="m5 12 4 4 10-10"/>
        ',
        'close' => '
            <path d="m6 6 12 12"/>
            <path d="m18 6-12 12"/>
        ',
        'save' => '
            <path d="M5 3h12l2 2v16H5z"/>
            <path d="M8 3v6h8V3"/>
            <path d="M8 15h8"/>
        ',
        'shield' => '
            <path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6z"/>
            <path d="m9 12 2 2 4-4"/>
        ',
    ];
@endphp

<style>
    :root {
        --ps-bg: #FBF7F2;
        --ps-soft: #F6EFE7;
        --ps-warm: #F3E4DE;
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

        --ps-shadow: 0 8px 24px rgba(86,28,23,.055);
        --ps-shadow-lg: 0 18px 44px rgba(86,28,23,.11);
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
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
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
        letter-spacing: .20em;
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
        font-size: clamp(42px,5vw,66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .ps-hero p {
        max-width: 680px;
        margin: 13px 0 0;
        color: var(--ps-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .ps-account-pill {
        display: inline-flex;
        min-height: 30px;
        align-items: center;
        gap: 7px;
        padding: 0 11px;
        border: 1px solid #CFE8DA;
        border-radius: 999px;
        background: var(--ps-success-soft);
        color: var(--ps-success);
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .ps-account-pill::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .ps-profile {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 16px;
        padding: 20px;
        border: 1px solid var(--ps-border);
        border-radius: 22px;
        background:
            radial-gradient(circle at 95% 4%, rgba(193,151,113,.12), transparent 26%),
            var(--ps-card);
        box-shadow: var(--ps-shadow);
    }

    .ps-avatar {
        position: relative;
        display: grid;
        width: 76px;
        height: 76px;
        place-items: center;
        overflow: hidden;
        border-radius: 999px;
        background:
            radial-gradient(circle at 30% 20%, rgba(255,255,255,.18), transparent 30%),
            var(--ps-maroon);
        color: #FFFFFF;
        box-shadow: 0 12px 26px rgba(86,28,23,.16);
    }

    .ps-avatar span {
        font-size: 17px;
        font-weight: 950;
    }

    .ps-profile-copy h2 {
        margin: 0;
        color: var(--ps-text);
        font-size: 16px;
        font-weight: 950;
    }

    .ps-profile-copy p {
        margin: 6px 0 0;
        color: var(--ps-muted);
        font-size: 9px;
        font-weight: 700;
    }

    .ps-profile-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 10px;
    }

    .ps-chip {
        display: inline-flex;
        min-height: 24px;
        align-items: center;
        gap: 6px;
        padding: 0 8px;
        border: 1px solid var(--ps-border);
        border-radius: 999px;
        background: var(--ps-soft);
        color: var(--ps-brown);
        font-size: 8px;
        font-weight: 850;
    }

    .ps-chip.is-success {
        border-color: #CFE8DA;
        background: var(--ps-success-soft);
        color: var(--ps-success);
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
        box-shadow: 0 10px 22px rgba(86,28,23,.14);
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
        background: var(--ps-warm);
        border-color: var(--ps-tan);
    }

    .ps-btn-danger {
        background: #FFFFFF;
        border-color: #E2B5B0;
        color: var(--ps-danger);
    }

    .ps-btn-danger:hover {
        background: var(--ps-danger-soft);
        border-color: #D99A93;
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
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ps-card-head h2 {
        margin: 0;
        color: var(--ps-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .ps-card-head p {
        margin: 7px 0 0;
        color: var(--ps-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .ps-card-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--ps-warm);
        color: var(--ps-maroon);
    }

    .ps-card-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ps-form {
        padding: 18px 20px;
    }

    .ps-form-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 13px;
    }

    .ps-field.is-wide {
        grid-column: 1 / -1;
    }

    .ps-field label {
        display: block;
        margin-bottom: 7px;
        color: var(--ps-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .ps-input {
        width: 100%;
        min-height: 41px;
        padding: 0 12px;
        border: 1px solid var(--ps-border);
        border-radius: 12px;
        background: var(--ps-soft);
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
        background: #F0E8DF;
    }

    .ps-form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 14px;
    }

    .ps-two-column {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 18px;
        align-items: start;
    }

    .ps-security {
        display: grid;
        gap: 13px;
        padding: 18px 20px;
    }

    .ps-password-field {
        position: relative;
    }

    .ps-password-field .ps-input {
        padding-right: 74px;
    }

    .ps-show-password {
        position: absolute;
        top: 50%;
        right: 8px;
        min-height: 28px;
        padding: 0 8px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: var(--ps-maroon);
        font-size: 8px;
        font-weight: 900;
        cursor: pointer;
        transform: translateY(-50%);
    }

    .ps-notifications {
        display: grid;
        gap: 10px;
        padding: 18px 20px;
    }

    .ps-notification {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 13px 14px;
        border: 1px solid var(--ps-border);
        border-radius: 14px;
        background: var(--ps-soft);
        cursor: pointer;
        transition: 150ms ease;
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
        width: 38px;
        height: 22px;
        flex: 0 0 38px;
    }

    .ps-switch input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .ps-switch-track {
        position: absolute;
        inset: 0;
        border: 1px solid var(--ps-border-strong);
        border-radius: 999px;
        background: #DDD1C6;
        transition: 180ms ease;
    }

    .ps-switch-track::after {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 14px;
        height: 14px;
        border-radius: 999px;
        background: #FFFFFF;
        box-shadow: 0 2px 5px rgba(0,0,0,.12);
        content: "";
        transition: 180ms ease;
    }

    .ps-switch input:checked + .ps-switch-track {
        border-color: var(--ps-maroon);
        background: var(--ps-maroon);
    }

    .ps-switch input:checked + .ps-switch-track::after {
        transform: translateX(16px);
    }

    .ps-appearance {
        display: grid;
        grid-template-columns: repeat(3,minmax(0,1fr));
        gap: 12px;
        padding: 18px 20px;
    }

    .ps-theme-option {
        position: relative;
        min-height: 132px;
        padding: 14px;
        border: 1px solid var(--ps-border);
        border-radius: 16px;
        background: var(--ps-soft);
        color: inherit;
        text-align: left;
        cursor: pointer;
        transition: 150ms ease;
    }

    .ps-theme-option:hover {
        transform: translateY(-1px);
        border-color: var(--ps-tan);
    }

    .ps-theme-option.is-active {
        border-color: var(--ps-maroon);
        background: var(--ps-warm);
        box-shadow: 0 0 0 3px rgba(86,28,23,.06);
    }

    .ps-theme-preview {
        display: grid;
        height: 58px;
        grid-template-columns: 20px 1fr;
        gap: 5px;
        padding: 6px;
        border: 1px solid var(--ps-border);
        border-radius: 10px;
        background: #FFFFFF;
    }

    .ps-theme-preview-sidebar {
        border-radius: 5px;
        background: #F2E7DC;
    }

    .ps-theme-preview-main {
        display: grid;
        gap: 4px;
    }

    .ps-theme-preview-main span {
        border-radius: 4px;
        background: #EBDDD0;
    }

    .ps-theme-preview.is-dark {
        border-color: #40322C;
        background: #171210;
    }

    .ps-theme-preview.is-dark .ps-theme-preview-sidebar {
        background: #2D1816;
    }

    .ps-theme-preview.is-dark .ps-theme-preview-main span {
        background: #30231F;
    }

    .ps-theme-preview.is-default {
        background:
            linear-gradient(90deg,#FFFFFF 0 50%,#171210 50% 100%);
    }

    .ps-theme-option strong {
        display: block;
        margin-top: 10px;
        color: var(--ps-text);
        font-size: 9px;
        font-weight: 950;
    }

    .ps-theme-option small {
        display: block;
        margin-top: 4px;
        color: var(--ps-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .ps-theme-check {
        position: absolute;
        top: 9px;
        right: 9px;
        display: none;
        width: 22px;
        height: 22px;
        place-items: center;
        border-radius: 999px;
        background: var(--ps-maroon);
        color: #FFFFFF;
    }

    .ps-theme-option.is-active .ps-theme-check {
        display: grid;
    }

    .ps-theme-check svg {
        width: 12px;
        height: 12px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
    }

    .ps-system-grid {
        display: grid;
        grid-template-columns: repeat(4,minmax(0,1fr));
        gap: 1px;
        background: var(--ps-border);
    }

    .ps-system-item {
        padding: 16px 18px;
        background: var(--ps-card);
    }

    .ps-system-item span {
        display: block;
        color: var(--ps-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .ps-system-item strong {
        display: block;
        margin-top: 7px;
        color: var(--ps-text);
        font-size: 10px;
        font-weight: 900;
    }

    .ps-danger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 18px 20px;
        border: 1px solid #EDC9C5;
        border-radius: 20px;
        background:
            radial-gradient(circle at 95% 10%, rgba(180,35,24,.05), transparent 30%),
            var(--ps-danger-soft);
    }

    .ps-danger-copy {
        display: flex;
        align-items: flex-start;
        gap: 11px;
    }

    .ps-danger-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border-radius: 12px;
        background: #FFFFFF;
        color: var(--ps-danger);
    }

    .ps-danger-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .ps-danger h2 {
        margin: 0;
        color: var(--ps-danger);
        font-size: 10px;
        font-weight: 950;
    }

    .ps-danger p {
        margin: 5px 0 0;
        color: #A65D56;
        font-size: 8px;
        line-height: 1.5;
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
        width: min(430px,100%);
        padding: 24px;
        border: 1px solid var(--ps-border);
        border-radius: 22px;
        background: var(--ps-card);
        box-shadow: var(--ps-shadow-lg);
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
    }

    .ps-modal-panel h2 {
        margin: 16px 0 0;
        color: var(--ps-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .ps-modal-panel p {
        margin: 10px 0 0;
        color: var(--ps-muted);
        font-size: 9px;
        line-height: 1.55;
    }

    @media (max-width: 1040px) {
        .ps-two-column {
            grid-template-columns: 1fr;
        }

        .ps-system-grid {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }
    }

    @media (max-width: 820px) {
        .ps-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .ps-profile {
            grid-template-columns: auto minmax(0,1fr);
        }

        .ps-profile .ps-btn {
            grid-column: 1 / -1;
            width: fit-content;
        }

        .ps-appearance {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 620px) {
        .ps-profile {
            grid-template-columns: 1fr;
        }

        .ps-profile .ps-btn {
            grid-column: auto;
            width: 100%;
        }

        .ps-form-grid,
        .ps-system-grid {
            grid-template-columns: 1fr;
        }

        .ps-field.is-wide {
            grid-column: auto;
        }

        .ps-form-actions .ps-btn {
            width: 100%;
        }

        .ps-danger {
            align-items: stretch;
            flex-direction: column;
        }

        .ps-danger form,
        .ps-danger .ps-btn {
            width: 100%;
        }
    }

    html.dark .ps-page {
        color: #F5EFE8;
    }

    html.dark .ps-hero,
    html.dark .ps-profile,
    html.dark .ps-card,
    html.dark .ps-modal-panel {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .ps-card-head {
        background: #1D1715 !important;
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
    html.dark .ps-theme-option small,
    html.dark .ps-system-item span,
    html.dark .ps-modal-panel p {
        color: #AFA19A !important;
    }

    html.dark .ps-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .ps-chip,
    html.dark .ps-input,
    html.dark .ps-notification,
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

    html.dark .ps-theme-option.is-active {
        background: #2D1816 !important;
        border-color: #A84538 !important;
    }

    html.dark .ps-card-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .ps-system-grid {
        background: #30231F !important;
    }

    html.dark .ps-system-item {
        background: #1A1412 !important;
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

    html.dark .ps-btn-danger {
        background: #1D1715 !important;
        border-color: #5B2925 !important;
        color: #F2A49A !important;
    }

    html.dark .ps-danger {
        background: #261615 !important;
        border-color: #5B2925 !important;
    }

    html.dark .ps-danger-icon {
        background: #1D1715 !important;
    }

    html.dark .ps-show-password {
        color: #EBA99D !important;
    }
</style>

<div class="ps-page">

    <section class="ps-hero">
        <div>
            <span class="ps-eyebrow">Account</span>

            <h1>Profile & Settings</h1>

            <p>
                Manage your logistics account information, security,
                notifications, appearance, and session preferences.
            </p>
        </div>

        <span class="ps-account-pill">
            Active Account
        </span>
    </section>

    <section class="ps-profile">
        <div id="profilePhotoPreview" class="ps-avatar">
            <span id="profilePhotoInitials">
                {{ $account['initials'] }}
            </span>
        </div>

        <div class="ps-profile-copy">
            <h2>
                {{ $account['role'] }}
            </h2>

            <p>
                {{ $account['email'] }}
            </p>

            <div class="ps-profile-meta">
                <span class="ps-chip is-success">
                    {{ $account['status'] }}
                </span>

                <span class="ps-chip">
                    Logistics Module
                </span>
            </div>
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
    </section>

    <section class="ps-card">
        <header class="ps-card-head">
            <div>
                <h2>Personal Information</h2>
                <p>Update the account details used by the logistics module.</p>
            </div>

            <span class="ps-card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    {!! $icons['user'] !!}
                </svg>
            </span>
        </header>

        <form id="profileForm" class="ps-form">
            <div class="ps-form-grid">
                <div class="ps-field">
                    <label for="firstName">First Name</label>

                    <input
                        id="firstName"
                        type="text"
                        value="{{ $account['first_name'] }}"
                        class="ps-input"
                    >
                </div>

                <div class="ps-field">
                    <label for="lastName">Last Name</label>

                    <input
                        id="lastName"
                        type="text"
                        value="{{ $account['last_name'] }}"
                        class="ps-input"
                    >
                </div>

                <div class="ps-field">
                    <label for="profileEmail">Email</label>

                    <input
                        id="profileEmail"
                        type="email"
                        value="{{ $account['email'] }}"
                        class="ps-input"
                    >
                </div>

                <div class="ps-field">
                    <label for="contactNumber">Contact Number</label>

                    <input
                        id="contactNumber"
                        type="text"
                        value="{{ $account['contact'] }}"
                        class="ps-input"
                    >
                </div>

                <div class="ps-field is-wide">
                    <label for="accountRole">Role</label>

                    <input
                        id="accountRole"
                        type="text"
                        value="{{ $account['role'] }}"
                        readonly
                        class="ps-input"
                    >
                </div>
            </div>

            <div class="ps-form-actions">
                <button type="submit" class="ps-btn ps-btn-primary">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        {!! $icons['save'] !!}
                    </svg>

                    Save Changes
                </button>
            </div>
        </form>
    </section>

    <section class="ps-two-column">

        <section class="ps-card">
            <header class="ps-card-head">
                <div>
                    <h2>Security</h2>
                    <p>Keep your logistics account protected with a strong password.</p>
                </div>

                <span class="ps-card-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        {!! $icons['lock'] !!}
                    </svg>
                </span>
            </header>

            <form id="passwordForm" class="ps-security">
                @foreach([
                    ['id' => 'currentPassword', 'label' => 'Current Password', 'placeholder' => 'Enter current password'],
                    ['id' => 'newPassword', 'label' => 'New Password', 'placeholder' => 'Enter new password'],
                    ['id' => 'confirmPassword', 'label' => 'Confirm Password', 'placeholder' => 'Confirm new password'],
                ] as $field)
                    <div class="ps-field">
                        <label for="{{ $field['id'] }}">
                            {{ $field['label'] }}
                        </label>

                        <div class="ps-password-field">
                            <input
                                id="{{ $field['id'] }}"
                                type="password"
                                placeholder="{{ $field['placeholder'] }}"
                                class="ps-input"
                            >

                            <button
                                type="button"
                                class="ps-show-password"
                                data-password-toggle="{{ $field['id'] }}"
                            >
                                Show
                            </button>
                        </div>
                    </div>
                @endforeach

                <div>
                    <button type="submit" class="ps-btn ps-btn-soft">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['shield'] !!}
                        </svg>

                        Update Password
                    </button>
                </div>
            </form>
        </section>

        <section class="ps-card">
            <header class="ps-card-head">
                <div>
                    <h2>Notifications</h2>
                    <p>Choose which logistics updates you want to receive.</p>
                </div>

                <span class="ps-card-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        {!! $icons['bell'] !!}
                    </svg>
                </span>
            </header>

            <div class="ps-notifications">
                @foreach($notifications as $notification)
                    <label class="ps-notification">
                        <span>
                            <strong>
                                {{ $notification['title'] }}
                            </strong>

                            <span>
                                {{ $notification['description'] }}
                            </span>
                        </span>

                        <span class="ps-switch">
                            <input
                                type="checkbox"
                                class="notification-toggle"
                                data-notification-key="{{ $notification['key'] }}"
                                {{ $notification['enabled'] ? 'checked' : '' }}
                            >

                            <span class="ps-switch-track"></span>
                        </span>
                    </label>
                @endforeach
            </div>
        </section>

    </section>

    <section class="ps-card">
        <header class="ps-card-head">
            <div>
                <h2>Appearance</h2>
                <p>Choose how the LIKHAE Logistics interface appears.</p>
            </div>

            <span class="ps-card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    {!! $icons['sun'] !!}
                </svg>
            </span>
        </header>

        <div class="ps-appearance">
            <button
                type="button"
                data-theme-option="light"
                class="ps-theme-option"
            >
                <span class="ps-theme-check">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        {!! $icons['check'] !!}
                    </svg>
                </span>

                <span class="ps-theme-preview">
                    <span class="ps-theme-preview-sidebar"></span>
                    <span class="ps-theme-preview-main">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </span>

                <strong>Light</strong>
                <small>Warm cream interface with LIKHAE maroon accents.</small>
            </button>

            <button
                type="button"
                data-theme-option="dark"
                class="ps-theme-option"
            >
                <span class="ps-theme-check">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        {!! $icons['check'] !!}
                    </svg>
                </span>

                <span class="ps-theme-preview is-dark">
                    <span class="ps-theme-preview-sidebar"></span>
                    <span class="ps-theme-preview-main">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </span>

                <strong>Dark</strong>
                <small>Near-black interface with warm maroon highlights.</small>
            </button>

            <button
                type="button"
                data-theme-option="default"
                class="ps-theme-option"
            >
                <span class="ps-theme-check">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        {!! $icons['check'] !!}
                    </svg>
                </span>

                <span class="ps-theme-preview is-default">
                    <span class="ps-theme-preview-sidebar"></span>
                    <span class="ps-theme-preview-main">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </span>

                <strong>Default</strong>
                <small>Use LIKHAE's default light appearance.</small>
            </button>
        </div>
    </section>

    <section class="ps-card">
        <header class="ps-card-head">
            <div>
                <h2>System Information</h2>
                <p>Current LIKHAE Logistics environment information.</p>
            </div>

            <span class="ps-card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    {!! $icons['info'] !!}
                </svg>
            </span>
        </header>

        <div class="ps-system-grid">
            @foreach($systemInfo as $item)
                <div class="ps-system-item">
                    <span>
                        {{ $item['label'] }}
                    </span>

                    <strong>
                        {{ $item['value'] }}
                    </strong>
                </div>
            @endforeach
        </div>
    </section>

    <section class="ps-danger">
        <div class="ps-danger-copy">
            <span class="ps-danger-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    {!! $icons['logout'] !!}
                </svg>
            </span>

            <div>
                <h2>Account Actions</h2>
                <p>Sign out of the current logistics session on this device.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="ps-btn ps-btn-danger">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['logout'] !!}
                </svg>

                Log Out
            </button>
        </form>
    </section>

</div>

<div id="settingsModal" class="ps-modal">
    <div class="ps-modal-panel">
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

        profilePhotoPreview.style.backgroundRepeat =
            'no-repeat';

        profilePhotoInitials?.setAttribute(
            'hidden',
            'hidden'
        );
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


    function setTheme(theme) {
        const normalizedTheme =
            theme === 'dark'
                ? 'dark'
                : theme === 'default'
                    ? 'default'
                    : 'light';

        localStorage.setItem(
            'likhae-theme',
            normalizedTheme
        );

        document.documentElement.classList.toggle(
            'dark',
            normalizedTheme === 'dark'
        );

        document
            .querySelectorAll('.ps-theme-option')
            .forEach(function (option) {
                option.classList.toggle(
                    'is-active',
                    option.dataset.themeOption === normalizedTheme
                );
            });
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
                    'Your profile information has been updated. Connect this form to your Laravel controller when the backend is ready.'
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
                    document
                        .getElementById('newPassword')
                        ?.value || '';

                const confirmPassword =
                    document
                        .getElementById('confirmPassword')
                        ?.value || '';

                if (
                    newPassword &&
                    newPassword !== confirmPassword
                ) {
                    openModal(
                        'Passwords Do Not Match',
                        'The new password and confirmation password must match.'
                    );

                    return;
                }

                openModal(
                    'Password Update',
                    'Your password form is ready. Connect it to Laravel authentication and validation when the backend is ready.'
                );
            }
        );


    document
        .querySelectorAll('[data-password-toggle]')
        .forEach(function (button) {
            button.addEventListener(
                'click',
                function () {
                    const targetId =
                        button.dataset.passwordToggle;

                    const input =
                        document.getElementById(targetId);

                    if (!input) {
                        return;
                    }

                    const show =
                        input.type === 'password';

                    input.type =
                        show
                            ? 'text'
                            : 'password';

                    button.textContent =
                        show
                            ? 'Hide'
                            : 'Show';
                }
            );
        });


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

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (
                !allowedTypes.includes(file.type)
                || file.size > 5 * 1024 * 1024
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

                    displayProfilePhoto(
                        photoUrl
                    );

                    localStorage.setItem(
                        'likhae-profile-photo',
                        photoUrl
                    );

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
        .querySelectorAll('.notification-toggle')
        .forEach(function (toggle) {
            const key =
                toggle.dataset.notificationKey;

            const storedValue =
                localStorage.getItem(
                    `likhae-notification-${key}`
                );

            if (storedValue !== null) {
                toggle.checked =
                    storedValue === 'true';
            }

            toggle.addEventListener(
                'change',
                function () {
                    localStorage.setItem(
                        `likhae-notification-${key}`,
                        String(toggle.checked)
                    );
                }
            );
        });


    document
        .querySelectorAll('.ps-theme-option')
        .forEach(function (button) {
            button.addEventListener(
                'click',
                function () {
                    setTheme(
                        button.dataset.themeOption
                            || 'light'
                    );
                }
            );
        });


    const savedTheme =
        localStorage.getItem('likhae-theme');

    setTheme(
        savedTheme === 'dark'
            ? 'dark'
            : savedTheme === 'default'
                ? 'default'
                : 'light'
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
});
</script>
@endpush
