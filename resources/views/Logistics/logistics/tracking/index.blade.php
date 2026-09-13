@extends('logistics.app')

@section('title', 'Parcel Tracking — LIKHAE Logistics')

@php
    $trackingRecords = [
        'LH-2026-1003' => [
            'tracking' => 'LH-2026-1003',
            'buyer' => 'Ana Reyes',
            'rider' => 'Rider 03',
            'rider_contact' => '0917 555 1234',
            'destination' => 'Los Baños, Laguna',
            'current_location' => 'Los Baños, Laguna',
            'status' => 'Out for Delivery',
            'status_key' => 'out_for_delivery',
            'last_updated' => 'September 11, 2026 · 2:18 PM',
            'estimated_delivery' => 'Today · 4:30 PM – 6:00 PM',
            'area' => 'Area C',
            'timeline' => [
                [
                    'title' => 'Parcel Received',
                    'description' => 'Parcel checked in at the LIKHAE sorting center.',
                    'time' => '8:42 AM',
                    'done' => true,
                ],
                [
                    'title' => 'Sorted by Destination',
                    'description' => 'Parcel assigned to Area C for Los Baños.',
                    'time' => '9:10 AM',
                    'done' => true,
                ],
                [
                    'title' => 'Rider Assigned',
                    'description' => 'Rider 03 accepted the delivery assignment.',
                    'time' => '10:05 AM',
                    'done' => true,
                ],
                [
                    'title' => 'Out for Delivery',
                    'description' => 'Parcel is currently with the assigned rider.',
                    'time' => '11:26 AM',
                    'done' => true,
                    'current' => true,
                ],
                [
                    'title' => 'Delivered',
                    'description' => 'Waiting for successful delivery confirmation.',
                    'time' => null,
                    'done' => false,
                ],
            ],
        ],

        'LH-2026-1005' => [
            'tracking' => 'LH-2026-1005',
            'buyer' => 'Sofia Garcia',
            'rider' => 'Rider 01',
            'rider_contact' => '0917 555 9081',
            'destination' => 'Santa Cruz, Laguna',
            'current_location' => 'Santa Cruz, Laguna',
            'status' => 'Delivered',
            'status_key' => 'delivered',
            'last_updated' => 'September 11, 2026 · 12:32 PM',
            'estimated_delivery' => 'Delivered · 12:32 PM',
            'area' => 'Area A',
            'timeline' => [
                [
                    'title' => 'Parcel Received',
                    'description' => 'Parcel checked in at the LIKHAE sorting center.',
                    'time' => '7:20 AM',
                    'done' => true,
                ],
                [
                    'title' => 'Sorted by Destination',
                    'description' => 'Parcel assigned to Area A.',
                    'time' => '7:42 AM',
                    'done' => true,
                ],
                [
                    'title' => 'Rider Assigned',
                    'description' => 'Rider 01 accepted the delivery assignment.',
                    'time' => '8:05 AM',
                    'done' => true,
                ],
                [
                    'title' => 'Out for Delivery',
                    'description' => 'Parcel left the sorting center for delivery.',
                    'time' => '9:12 AM',
                    'done' => true,
                ],
                [
                    'title' => 'Delivered',
                    'description' => 'Parcel was successfully delivered to the recipient.',
                    'time' => '12:32 PM',
                    'done' => true,
                    'current' => true,
                ],
            ],
        ],
    ];

    $defaultTracking = request('tracking', 'LH-2026-1003');
    $parcel = $trackingRecords[$defaultTracking] ?? $trackingRecords['LH-2026-1003'];

    $icons = [
        'search' => '
            <circle cx="11" cy="11" r="7"/>
            <path d="m20 20-3.5-3.5"/>
        ',
        'package' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',
        'location' => '
            <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"/>
            <circle cx="12" cy="10" r="2"/>
        ',
        'rider' => '
            <circle cx="8" cy="7" r="3"/>
            <path d="M3 19c0-3 2-5 5-5"/>
            <path d="M14 7h7"/>
            <path d="M17.5 3.5v7"/>
        ',
        'check' => '
            <path d="m5 12 4 4 10-10"/>
        ',
        'clock' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7v5l3 2"/>
        ',
        'route' => '
            <path d="M5 19c3 0 3-5 6-5s3 5 6 5"/>
            <circle cx="5" cy="5" r="2"/>
            <circle cx="19" cy="5" r="2"/>
            <path d="M7 5h10"/>
        ',
        'phone' => '
            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1A19.5 19.5 0 0 1 5.2 12 19.8 19.8 0 0 1 2.1 3.3 2 2 0 0 1 4.1 1h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 9a16 16 0 0 0 7 7l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2Z"/>
        ',
        'image' => '
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <path d="m21 15-5-5L5 21"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --pt-bg: #FBF7F2;
        --pt-bg-soft: #F6EFE7;
        --pt-bg-warm: #F3E4DE;
        --pt-card: #FFFDF9;

        --pt-border: #EADCCC;
        --pt-border-strong: #DBCEC1;

        --pt-maroon: #561C17;
        --pt-maroon-2: #642920;
        --pt-maroon-dark: #3E130F;

        --pt-text: #3B211B;
        --pt-text-dark: #1C160F;
        --pt-brown: #6C4936;
        --pt-muted: #987865;
        --pt-muted-light: #A99386;

        --pt-tan: #C19771;

        --pt-success: #256F4A;
        --pt-success-soft: #EAF7EF;

        --pt-warning: #9A5B11;
        --pt-warning-soft: #FFF6DE;

        --pt-shadow: 0 8px 24px rgba(86, 28, 23, 0.055);
        --pt-shadow-hover: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .pt-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--pt-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .pt-page * {
        box-sizing: border-box;
    }

    .pt-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        color: var(--pt-muted);
        font-size: 10px;
        font-weight: 750;
    }

    .pt-breadcrumb a {
        color: var(--pt-muted);
        text-decoration: none;
        transition: 150ms ease;
    }

    .pt-breadcrumb a:hover {
        color: var(--pt-maroon);
    }

    .pt-breadcrumb strong {
        color: var(--pt-text);
        font-weight: 900;
    }

    .pt-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--pt-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--pt-shadow);
    }

    .pt-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--pt-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .pt-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .pt-hero h1 {
        margin: 10px 0 0;
        color: var(--pt-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .pt-hero p {
        max-width: 680px;
        margin: 13px 0 0;
        color: var(--pt-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .pt-search {
        display: grid;
        grid-template-columns: minmax(240px, 1fr) auto;
        width: min(460px, 100%);
        overflow: hidden;
        border: 1px solid var(--pt-border);
        border-radius: 13px;
        background: var(--pt-card);
        box-shadow: 0 8px 18px rgba(86,28,23,.08);
    }

    .pt-search-field {
        position: relative;
        min-width: 0;
    }

    .pt-search-field svg {
        position: absolute;
        top: 50%;
        left: 13px;
        width: 15px;
        height: 15px;
        color: var(--pt-muted);
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .pt-search input {
        width: 100%;
        min-height: 43px;
        padding: 0 12px 0 38px;
        border: 0;
        background: transparent;
        color: var(--pt-text);
        font-size: 10px;
        font-weight: 750;
        outline: none;
    }

    .pt-search input::placeholder {
        color: var(--pt-muted-light);
    }

    .pt-search button {
        min-width: 88px;
        border: 0;
        background: var(--pt-maroon);
        color: #FFFFFF;
        font-size: 9px;
        font-weight: 900;
        cursor: pointer;
        transition: 150ms ease;
    }

    .pt-search button:hover {
        background: var(--pt-maroon-dark);
    }

    .pt-main-grid {
        display: grid;
        grid-template-columns: minmax(0,1fr) 340px;
        gap: 18px;
        align-items: start;
    }

    .pt-stack {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .pt-card {
        overflow: hidden;
        border: 1px solid var(--pt-border);
        border-radius: 22px;
        background: var(--pt-card);
        box-shadow: var(--pt-shadow);
    }

    .pt-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--pt-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .pt-card-head h2 {
        margin: 0;
        color: var(--pt-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .pt-card-head p {
        margin: 7px 0 0;
        color: var(--pt-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .pt-card-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--pt-bg-warm);
        color: var(--pt-maroon);
    }

    .pt-card-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .pt-summary {
        padding: 20px;
    }

    .pt-summary-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .pt-label {
        display: block;
        color: var(--pt-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .pt-tracking {
        margin: 7px 0 0;
        color: var(--pt-text);
        font-size: 17px;
        font-weight: 950;
        letter-spacing: -.03em;
    }

    .pt-status {
        display: inline-flex;
        min-height: 27px;
        align-items: center;
        gap: 6px;
        padding: 0 10px;
        border: 1px solid #E6C7BE;
        border-radius: 999px;
        background: var(--pt-bg-warm);
        color: var(--pt-maroon);
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .pt-status::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .pt-status.is-delivered {
        border-color: #CFE8DA;
        background: var(--pt-success-soft);
        color: var(--pt-success);
    }

    .pt-summary-grid {
        display: grid;
        grid-template-columns: repeat(3,minmax(0,1fr));
        gap: 10px;
        margin-top: 17px;
    }

    .pt-summary-item {
        min-width: 0;
        padding: 13px;
        border: 1px solid var(--pt-border);
        border-radius: 14px;
        background: var(--pt-bg-soft);
    }

    .pt-summary-item strong {
        display: block;
        margin-top: 6px;
        color: var(--pt-text);
        font-size: 10px;
        font-weight: 900;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .pt-summary-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--pt-border);
    }

    .pt-summary-footer span {
        color: var(--pt-muted);
        font-size: 8px;
    }

    .pt-summary-footer strong {
        color: var(--pt-brown);
        font-size: 9px;
        font-weight: 850;
    }

    .pt-timeline {
        display: grid;
        padding: 18px 20px;
    }

    .pt-step {
        position: relative;
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        gap: 12px;
        padding-bottom: 20px;
    }

    .pt-step:last-child {
        padding-bottom: 0;
    }

    .pt-step:not(:last-child)::after {
        position: absolute;
        top: 28px;
        bottom: 0;
        left: 13px;
        width: 1px;
        background: var(--pt-border);
        content: "";
    }

    .pt-step.is-done:not(:last-child)::after {
        background: #C8A99B;
    }

    .pt-step-dot {
        position: relative;
        z-index: 1;
        display: grid;
        width: 27px;
        height: 27px;
        place-items: center;
        border: 1px solid var(--pt-border);
        border-radius: 999px;
        background: var(--pt-bg-soft);
        color: var(--pt-muted);
        font-size: 8px;
        font-weight: 950;
    }

    .pt-step.is-done .pt-step-dot {
        border-color: var(--pt-maroon);
        background: var(--pt-maroon);
        color: #FFFFFF;
    }

    .pt-step.is-current .pt-step-dot {
        box-shadow: 0 0 0 5px rgba(86,28,23,.08);
    }

    .pt-step-dot svg {
        width: 13px;
        height: 13px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .pt-step-copy {
        min-width: 0;
    }

    .pt-step-copy strong {
        display: block;
        padding-top: 2px;
        color: var(--pt-muted);
        font-size: 9px;
        font-weight: 850;
    }

    .pt-step.is-done .pt-step-copy strong {
        color: var(--pt-text);
    }

    .pt-step-copy p {
        margin: 4px 0 0;
        color: var(--pt-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .pt-step-time {
        padding-top: 2px;
        color: var(--pt-muted-light);
        font-size: 8px;
        font-weight: 750;
        white-space: nowrap;
    }

    .pt-map {
        padding: 18px 20px;
    }

    .pt-map-panel {
        position: relative;
        display: grid;
        min-height: 270px;
        place-items: center;
        overflow: hidden;
        border: 1px solid var(--pt-border);
        border-radius: 17px;
        background:
            linear-gradient(rgba(255,253,249,.80), rgba(255,253,249,.80)),
            repeating-linear-gradient(
                45deg,
                #F6EFE7,
                #F6EFE7 18px,
                #EFE5D9 18px,
                #EFE5D9 36px
            );
        text-align: center;
    }

    .pt-route-line {
        position: absolute;
        top: 50%;
        left: 20%;
        width: 60%;
        height: 3px;
        border-radius: 999px;
        background: var(--pt-maroon);
        transform: translateY(-50%) rotate(-8deg);
        opacity: .35;
    }

    .pt-map-marker {
        position: absolute;
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 4px solid #FFFDF9;
        border-radius: 999px;
        background: var(--pt-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86,28,23,.18);
    }

    .pt-map-marker svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
    }

    .pt-map-marker.is-start {
        left: 16%;
        bottom: 28%;
    }

    .pt-map-marker.is-current {
        top: 25%;
        right: 18%;
    }

    .pt-map-copy {
        position: relative;
        z-index: 2;
        max-width: 270px;
        padding: 16px;
        border: 1px solid rgba(234,220,204,.85);
        border-radius: 14px;
        background: rgba(255,253,249,.86);
        backdrop-filter: blur(8px);
    }

    .pt-map-copy svg {
        width: 22px;
        height: 22px;
        margin-inline: auto;
        fill: none;
        stroke: var(--pt-maroon);
        stroke-width: 1.8;
    }

    .pt-map-copy strong {
        display: block;
        margin-top: 9px;
        color: var(--pt-text);
        font-size: 10px;
        font-weight: 950;
    }

    .pt-map-copy span {
        display: block;
        margin-top: 5px;
        color: var(--pt-muted);
        font-size: 8px;
        line-height: 1.5;
    }

    .pt-location-card {
        overflow: hidden;
        border-radius: 20px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255,255,255,.12), transparent 30%),
            linear-gradient(135deg, var(--pt-maroon) 0%, var(--pt-maroon-2) 58%, var(--pt-maroon-dark) 100%);
        color: #FFFFFF;
        box-shadow: 0 18px 44px rgba(86,28,23,.18);
    }

    .pt-location-body {
        padding: 19px;
    }

    .pt-location-body small {
        color: rgba(255,255,255,.55);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .pt-location-body h2 {
        margin: 10px 0 0;
        color: #FFFFFF;
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 31px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .pt-location-body p {
        margin: 8px 0 0;
        color: rgba(255,255,255,.62);
        font-size: 9px;
        line-height: 1.55;
    }

    .pt-location-meta {
        display: grid;
        gap: 8px;
        margin-top: 16px;
    }

    .pt-location-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 12px;
        background: rgba(255,255,255,.07);
    }

    .pt-location-row span {
        color: rgba(255,255,255,.50);
        font-size: 8px;
    }

    .pt-location-row strong {
        color: #FFFFFF;
        font-size: 9px;
        font-weight: 900;
        text-align: right;
    }

    .pt-rider-body {
        padding: 18px 20px;
    }

    .pt-rider {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pt-rider-avatar {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;
        border-radius: 999px;
        background: var(--pt-maroon);
        color: #FFFFFF;
        font-size: 9px;
        font-weight: 950;
    }

    .pt-rider-copy strong {
        display: block;
        color: var(--pt-text);
        font-size: 10px;
        font-weight: 950;
    }

    .pt-rider-copy span {
        display: block;
        margin-top: 4px;
        color: var(--pt-muted);
        font-size: 8px;
    }

    .pt-call {
        display: inline-flex;
        min-height: 32px;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
        padding: 0 10px;
        border: 1px solid var(--pt-border);
        border-radius: 10px;
        background: var(--pt-card);
        color: var(--pt-maroon);
        font-size: 8px;
        font-weight: 900;
        text-decoration: none;
        transition: 150ms ease;
    }

    .pt-call:hover {
        background: var(--pt-bg-warm);
        border-color: var(--pt-tan);
    }

    .pt-call svg {
        width: 13px;
        height: 13px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .pt-proof {
        padding: 18px 20px;
    }

    .pt-proof-box {
        display: grid;
        min-height: 150px;
        place-items: center;
        border: 1px dashed var(--pt-border-strong);
        border-radius: 15px;
        background: var(--pt-bg-soft);
        color: var(--pt-muted);
        text-align: center;
    }

    .pt-proof-box svg {
        width: 24px;
        height: 24px;
        margin-inline: auto;
        fill: none;
        stroke: var(--pt-maroon);
        stroke-width: 1.7;
    }

    .pt-proof-box strong {
        display: block;
        margin-top: 8px;
        color: var(--pt-text);
        font-size: 9px;
        font-weight: 900;
    }

    .pt-proof-box span {
        display: block;
        margin-top: 4px;
        color: var(--pt-muted);
        font-size: 8px;
    }

    .pt-search-feedback {
        display: none;
        padding: 12px 14px;
        border: 1px solid #EAD39A;
        border-radius: 13px;
        background: var(--pt-warning-soft);
        color: var(--pt-warning);
        font-size: 9px;
        font-weight: 800;
    }

    .pt-search-feedback.is-visible {
        display: block;
    }

    @media (max-width: 1180px) {
        .pt-main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 820px) {
        .pt-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .pt-search {
            width: 100%;
        }

        .pt-summary-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .pt-search {
            grid-template-columns: 1fr;
        }

        .pt-search button {
            min-height: 40px;
        }

        .pt-summary-top,
        .pt-summary-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .pt-step {
            grid-template-columns: auto minmax(0,1fr);
        }

        .pt-step-time {
            grid-column: 2;
        }
    }

    html.dark .pt-page {
        color: #F5EFE8;
    }

    html.dark .pt-hero,
    html.dark .pt-card {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .pt-card-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .pt-hero h1,
    html.dark .pt-card-head h2,
    html.dark .pt-tracking,
    html.dark .pt-summary-item strong,
    html.dark .pt-step.is-done .pt-step-copy strong,
    html.dark .pt-rider-copy strong,
    html.dark .pt-proof-box strong,
    html.dark .pt-map-copy strong {
        color: #F5EFE8 !important;
    }

    html.dark .pt-hero p,
    html.dark .pt-breadcrumb,
    html.dark .pt-card-head p,
    html.dark .pt-label,
    html.dark .pt-step-copy p,
    html.dark .pt-step-time,
    html.dark .pt-rider-copy span,
    html.dark .pt-proof-box span,
    html.dark .pt-map-copy span {
        color: #AFA19A !important;
    }

    html.dark .pt-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .pt-search,
    html.dark .pt-summary-item,
    html.dark .pt-proof-box,
    html.dark .pt-map-copy {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .pt-search input {
        color: #F5EFE8 !important;
    }

    html.dark .pt-step-dot {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #AFA19A !important;
    }

    html.dark .pt-step.is-done .pt-step-dot {
        background: #A84538 !important;
        border-color: #A84538 !important;
        color: #FFFFFF !important;
    }

    html.dark .pt-map-panel {
        background:
            linear-gradient(rgba(23,18,16,.80), rgba(23,18,16,.80)),
            repeating-linear-gradient(
                45deg,
                #211B17,
                #211B17 18px,
                #1A1412 18px,
                #1A1412 36px
            ) !important;
        border-color: #3B2E27 !important;
    }

    html.dark .pt-map-marker {
        border-color: #1A1412 !important;
        background: #A84538 !important;
    }

    html.dark .pt-call {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .pt-status:not(.is-delivered) {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }
</style>

<div class="pt-page">

    <nav class="pt-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('logistics.dashboard') }}">
            Dashboard
        </a>

        <span>/</span>

        <strong>
            Parcel Tracking
        </strong>
    </nav>

    <section class="pt-hero">
        <div>
            <span class="pt-eyebrow">
                Delivery Monitoring
            </span>

            <h1>
                Parcel Tracking
            </h1>

            <p>
                Monitor parcel movement from the sorting center until successful delivery
                and review the latest rider and route updates.
            </p>
        </div>

        <form id="trackingSearchForm" class="pt-search">
            <div class="pt-search-field">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['search'] !!}
                </svg>

                <input
                    type="text"
                    id="trackingSearchInput"
                    value="{{ $parcel['tracking'] }}"
                    placeholder="Search tracking number..."
                    autocomplete="off"
                >
            </div>

            <button type="submit">
                Search
            </button>
        </form>
    </section>

    <div id="trackingSearchFeedback" class="pt-search-feedback">
        Tracking number not found in this frontend sample.
    </div>

    <section class="pt-main-grid">

        <div class="pt-stack">

            <section class="pt-card">
                <div class="pt-summary">
                    <div class="pt-summary-top">
                        <div>
                            <span class="pt-label">
                                Tracking Number
                            </span>

                            <h2 class="pt-tracking">
                                {{ $parcel['tracking'] }}
                            </h2>
                        </div>

                        <span class="pt-status {{ $parcel['status_key'] === 'delivered' ? 'is-delivered' : '' }}">
                            {{ $parcel['status'] }}
                        </span>
                    </div>

                    <div class="pt-summary-grid">
                        <div class="pt-summary-item">
                            <span class="pt-label">Buyer</span>
                            <strong>{{ $parcel['buyer'] }}</strong>
                        </div>

                        <div class="pt-summary-item">
                            <span class="pt-label">Rider</span>
                            <strong>{{ $parcel['rider'] }}</strong>
                        </div>

                        <div class="pt-summary-item">
                            <span class="pt-label">Destination</span>
                            <strong>{{ $parcel['destination'] }}</strong>
                        </div>
                    </div>

                    <div class="pt-summary-footer">
                        <span>
                            Last updated
                            <strong>{{ $parcel['last_updated'] }}</strong>
                        </span>

                        <span>
                            ETA
                            <strong>{{ $parcel['estimated_delivery'] }}</strong>
                        </span>
                    </div>
                </div>
            </section>

            <section class="pt-card">
                <header class="pt-card-head">
                    <div>
                        <h2>
                            Delivery Timeline
                        </h2>

                        <p>
                            Current delivery workflow and parcel movement.
                        </p>
                    </div>

                    <span class="pt-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['clock'] !!}
                        </svg>
                    </span>
                </header>

                <div class="pt-timeline">
                    @foreach($parcel['timeline'] as $step)
                        <div class="pt-step {{ $step['done'] ? 'is-done' : '' }} {{ !empty($step['current']) ? 'is-current' : '' }}">
                            <span class="pt-step-dot">
                                @if($step['done'])
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        {!! $icons['check'] !!}
                                    </svg>
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </span>

                            <div class="pt-step-copy">
                                <strong>
                                    {{ $step['title'] }}
                                </strong>

                                <p>
                                    {{ $step['description'] }}
                                </p>
                            </div>

                            <span class="pt-step-time">
                                {{ $step['time'] ?? 'Pending' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="pt-card">
                <header class="pt-card-head">
                    <div>
                        <h2>
                            Delivery Route
                        </h2>

                        <p>
                            Route view placeholder for your future maps integration.
                        </p>
                    </div>

                    <span class="pt-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['route'] !!}
                        </svg>
                    </span>
                </header>

                <div class="pt-map">
                    <div class="pt-map-panel">
                        <span class="pt-route-line"></span>

                        <span class="pt-map-marker is-start" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['package'] !!}
                            </svg>
                        </span>

                        <span class="pt-map-marker is-current" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['location'] !!}
                            </svg>
                        </span>

                        <div class="pt-map-copy">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['route'] !!}
                            </svg>

                            <strong>
                                {{ $parcel['current_location'] }}
                            </strong>

                            <span>
                                Connect this section to your preferred maps API
                                for live rider and parcel movement.
                            </span>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <aside class="pt-stack">

            <section class="pt-location-card">
                <div class="pt-location-body">
                    <small>
                        Current Location
                    </small>

                    <h2>
                        {{ $parcel['current_location'] }}
                    </h2>

                    <p>
                        {{
                            $parcel['status_key'] === 'delivered'
                                ? 'Parcel delivery has been completed.'
                                : 'The assigned rider is currently delivering this parcel.'
                        }}
                    </p>

                    <div class="pt-location-meta">
                        <div class="pt-location-row">
                            <span>Delivery Area</span>
                            <strong>{{ $parcel['area'] }}</strong>
                        </div>

                        <div class="pt-location-row">
                            <span>Status</span>
                            <strong>{{ $parcel['status'] }}</strong>
                        </div>

                        <div class="pt-location-row">
                            <span>Estimated Delivery</span>
                            <strong>{{ $parcel['estimated_delivery'] }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="pt-card">
                <header class="pt-card-head">
                    <div>
                        <h2>
                            Rider Information
                        </h2>

                        <p>
                            Courier currently assigned to this parcel.
                        </p>
                    </div>

                    <span class="pt-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['rider'] !!}
                        </svg>
                    </span>
                </header>

                <div class="pt-rider-body">
                    <div class="pt-rider">
                        <span class="pt-rider-avatar">
                            {{ substr($parcel['rider'], -2) }}
                        </span>

                        <div class="pt-rider-copy">
                            <strong>
                                {{ $parcel['rider'] }}
                            </strong>

                            <span>
                                {{ $parcel['rider_contact'] }}
                            </span>
                        </div>
                    </div>

                    <a
                        href="tel:{{ preg_replace('/\s+/', '', $parcel['rider_contact']) }}"
                        class="pt-call"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['phone'] !!}
                        </svg>

                        Contact Rider
                    </a>
                </div>
            </section>

            <section class="pt-card">
                <header class="pt-card-head">
                    <div>
                        <h2>
                            Delivery Proof
                        </h2>

                        <p>
                            Proof becomes available after successful delivery.
                        </p>
                    </div>

                    <span class="pt-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['image'] !!}
                        </svg>
                    </span>
                </header>

                <div class="pt-proof">
                    <div class="pt-proof-box">
                        <div>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['image'] !!}
                            </svg>

                            <strong>
                                {{
                                    $parcel['status_key'] === 'delivered'
                                        ? 'Proof of delivery available'
                                        : 'No delivery proof yet'
                                }}
                            </strong>

                            <span>
                                {{
                                    $parcel['status_key'] === 'delivered'
                                        ? 'Connect this preview to the uploaded rider proof.'
                                        : 'This will appear once the parcel is delivered.'
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

        </aside>

    </section>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form =
        document.getElementById('trackingSearchForm');

    const input =
        document.getElementById('trackingSearchInput');

    const feedback =
        document.getElementById('trackingSearchFeedback');

    const availableTracking =
        @json(array_keys($trackingRecords));


    form?.addEventListener(
        'submit',
        function (event) {
            event.preventDefault();

            const tracking =
                (input?.value || '')
                    .trim()
                    .toUpperCase();

            if (!tracking) {
                input?.focus();
                return;
            }

            const exists =
                availableTracking.includes(tracking);

            if (!exists) {
                feedback?.classList.add('is-visible');
                input?.focus();
                return;
            }

            feedback?.classList.remove('is-visible');

            const url =
                new URL(window.location.href);

            url.searchParams.set(
                'tracking',
                tracking
            );

            window.location.href =
                url.toString();
        }
    );


    input?.addEventListener(
        'input',
        function () {
            feedback?.classList.remove(
                'is-visible'
            );
        }
    );
});
</script>
@endpush
