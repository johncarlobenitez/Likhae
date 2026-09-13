@extends('logistics.app')

@section('title', 'Receive Parcel — LIKHAE Logistics')

@php
    $parcel = [
        'id' => 1001,
        'tracking' => 'LH-2026-1001',
        'order' => 'ORD-2026-1045',
        'seller' => 'Habing Lokal',
        'buyer' => 'Juan Dela Cruz',
        'address' => '21 Rizal Street, Brgy. Bubukal, Santa Cruz, Laguna',
        'destination' => 'Santa Cruz, Laguna',
        'area' => 'Unassigned',
        'payment' => 'Cash on Delivery',
        'status' => 'Incoming Parcel',
        'received_from' => 'Seller / Drop-off',
        'condition' => 'Good',
        'items' => [
            [
                'name' => 'Handwoven Abaca Bag',
                'variation' => 'Natural / Medium',
                'quantity' => 1,
                'price' => 1250,
            ],
            [
                'name' => 'Embroidered Pouch',
                'variation' => 'Burgundy / Standard',
                'quantity' => 1,
                'price' => 450,
            ],
        ],
    ];

    $total = collect($parcel['items'])->sum(
        fn ($item) => $item['price'] * $item['quantity']
    );

    $journey = [
        ['title' => 'Receive Parcel', 'state' => 'current'],
        ['title' => 'Sort by Destination', 'state' => 'pending'],
        ['title' => 'Assign Rider', 'state' => 'pending'],
        ['title' => 'Out for Delivery', 'state' => 'pending'],
        ['title' => 'Delivered', 'state' => 'pending'],
    ];

    $icons = [
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
        'search' => '
            <circle cx="11" cy="11" r="7"/>
            <path d="m20 20-3.5-3.5"/>
        ',
        'location' => '
            <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"/>
            <circle cx="12" cy="10" r="2"/>
        ',
        'package' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',
        'check' => '
            <path d="m5 12 4 4 10-10"/>
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
        --rp-bg: #FBF7F2;
        --rp-bg-soft: #F6EFE7;
        --rp-bg-warm: #F3E4DE;
        --rp-card: #FFFDF9;

        --rp-border: #EADCCC;
        --rp-border-strong: #DBCEC1;

        --rp-maroon: #561C17;
        --rp-maroon-2: #642920;
        --rp-maroon-dark: #3E130F;

        --rp-text: #3B211B;
        --rp-text-dark: #1C160F;
        --rp-brown: #6C4936;
        --rp-muted: #987865;
        --rp-muted-light: #A99386;

        --rp-tan: #C19771;

        --rp-success: #256F4A;
        --rp-success-soft: #EAF7EF;

        --rp-warning: #9A5B11;
        --rp-warning-soft: #FFF6DE;

        --rp-danger: #B42318;
        --rp-danger-soft: #FCEBE9;

        --rp-shadow: 0 8px 24px rgba(86, 28, 23, 0.055);
        --rp-shadow-hover: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .rp-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--rp-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .rp-page * {
        box-sizing: border-box;
    }

    .rp-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        color: var(--rp-muted);
        font-size: 10px;
        font-weight: 750;
    }

    .rp-breadcrumb a {
        color: var(--rp-muted);
        text-decoration: none;
        transition: 150ms ease;
    }

    .rp-breadcrumb a:hover {
        color: var(--rp-maroon);
    }

    .rp-breadcrumb strong {
        color: var(--rp-text);
        font-weight: 900;
    }

    .rp-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--rp-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193, 151, 113, 0.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86, 28, 23, 0.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--rp-shadow);
    }

    .rp-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--rp-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: 0.20em;
        text-transform: uppercase;
    }

    .rp-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .rp-hero h1 {
        margin: 10px 0 0;
        color: var(--rp-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: 0.94;
        letter-spacing: -0.055em;
    }

    .rp-hero p {
        max-width: 700px;
        margin: 13px 0 0;
        color: var(--rp-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .rp-btn {
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

    .rp-btn:hover {
        transform: translateY(-1px);
    }

    .rp-btn svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rp-btn-primary {
        background: var(--rp-maroon);
        border-color: var(--rp-maroon);
        color: #FFFFFF;
        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .rp-btn-primary:hover {
        background: var(--rp-maroon-dark);
        border-color: var(--rp-maroon-dark);
    }

    .rp-btn-soft {
        background: var(--rp-card);
        border-color: var(--rp-border);
        color: var(--rp-maroon);
    }

    .rp-btn-soft:hover {
        background: var(--rp-bg-warm);
        border-color: var(--rp-tan);
    }

    .rp-scanner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 24px 26px;
        border-radius: 24px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255,255,255,0.14), transparent 30%),
            linear-gradient(135deg, var(--rp-maroon) 0%, var(--rp-maroon-2) 58%, var(--rp-maroon-dark) 100%);
        color: #FFFFFF;
        box-shadow: 0 18px 44px rgba(86, 28, 23, 0.18);
    }

    .rp-scanner-copy {
        max-width: 520px;
    }

    .rp-scanner-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #F3D8CC;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .rp-scanner-icon {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid rgba(255,255,255,0.14);
        border-radius: 11px;
        background: rgba(255,255,255,0.08);
        color: #FFFFFF;
    }

    .rp-scanner-icon svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rp-scanner h2 {
        margin: 11px 0 0;
        color: #FFFFFF;
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 31px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .rp-scanner p {
        margin: 9px 0 0;
        color: rgba(255,255,255,0.68);
        font-size: 10px;
        line-height: 1.6;
    }

    .rp-scanner-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 9px;
        width: min(520px, 100%);
    }

    .rp-scanner-input {
        position: relative;
        min-width: 0;
    }

    .rp-scanner-input svg {
        position: absolute;
        top: 50%;
        left: 13px;
        width: 16px;
        height: 16px;
        color: var(--rp-muted);
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        transform: translateY(-50%);
    }

    .rp-scanner-input input {
        width: 100%;
        min-height: 44px;
        padding: 0 14px 0 40px;
        border: 1px solid rgba(255,255,255,0.18);
        border-radius: 13px;
        background: #FFFDF9;
        color: var(--rp-text);
        font-size: 10px;
        font-weight: 800;
        outline: none;
    }

    .rp-scanner-form button {
        min-height: 44px;
        padding: 0 16px;
        border: 1px solid #FFFFFF;
        border-radius: 13px;
        background: #FFFFFF;
        color: var(--rp-maroon);
        font-size: 10px;
        font-weight: 900;
        cursor: pointer;
        transition: 150ms ease;
    }

    .rp-scanner-form button:hover {
        transform: translateY(-1px);
        background: #F6EFE7;
    }

    .rp-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 18px;
        align-items: start;
    }

    .rp-stack {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .rp-card {
        overflow: hidden;
        border: 1px solid var(--rp-border);
        border-radius: 22px;
        background: var(--rp-card);
        box-shadow: var(--rp-shadow);
    }

    .rp-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--rp-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,0.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .rp-card-head h2 {
        margin: 0;
        color: var(--rp-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .rp-card-head p {
        margin: 7px 0 0;
        color: var(--rp-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .rp-status {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        gap: 6px;
        padding: 0 9px;
        border: 1px solid #EAD39A;
        border-radius: 999px;
        background: var(--rp-warning-soft);
        color: var(--rp-warning);
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .rp-status::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .rp-info-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0,1fr));
        gap: 1px;
        background: var(--rp-border);
    }

    .rp-info-item {
        min-width: 0;
        padding: 18px;
        background: var(--rp-card);
    }

    .rp-label {
        display: block;
        color: var(--rp-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.09em;
        text-transform: uppercase;
    }

    .rp-value {
        display: block;
        margin-top: 7px;
        color: var(--rp-text);
        font-size: 10px;
        font-weight: 900;
        line-height: 1.35;
    }

    .rp-address-wrap {
        padding: 18px 20px;
        border-top: 1px solid var(--rp-border);
    }

    .rp-address {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-top: 10px;
        padding: 14px;
        border: 1px solid var(--rp-border);
        border-radius: 14px;
        background: var(--rp-bg-soft);
    }

    .rp-address-icon,
    .rp-item-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--rp-bg-warm);
        color: var(--rp-maroon);
    }

    .rp-address-icon svg,
    .rp-item-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rp-address strong {
        display: block;
        color: var(--rp-text);
        font-size: 10px;
        font-weight: 900;
    }

    .rp-address p {
        margin: 5px 0 0;
        color: var(--rp-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .rp-items {
        display: grid;
    }

    .rp-item {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
        border-bottom: 1px solid var(--rp-border);
    }

    .rp-item:last-child {
        border-bottom: 0;
    }

    .rp-item-main {
        min-width: 0;
    }

    .rp-item-main strong {
        display: block;
        color: var(--rp-text);
        font-size: 10px;
        font-weight: 900;
    }

    .rp-item-main span,
    .rp-item-price span {
        display: block;
        margin-top: 4px;
        color: var(--rp-muted);
        font-size: 8px;
        font-weight: 700;
    }

    .rp-item-price {
        text-align: right;
    }

    .rp-item-price strong {
        color: var(--rp-text);
        font-size: 10px;
        font-weight: 900;
    }

    .rp-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 20px;
        border-top: 1px solid var(--rp-border);
        background: var(--rp-bg-soft);
    }

    .rp-total span {
        color: var(--rp-muted);
        font-size: 10px;
        font-weight: 800;
    }

    .rp-total strong {
        color: var(--rp-maroon);
        font-size: 14px;
        font-weight: 950;
    }

    .rp-card-body {
        padding: 18px 20px;
    }

    .rp-checklist {
        display: grid;
        gap: 10px;
    }

    .rp-check {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 13px;
        border: 1px solid var(--rp-border);
        border-radius: 14px;
        background: var(--rp-bg-soft);
        cursor: pointer;
        transition: 150ms ease;
    }

    .rp-check:hover {
        border-color: var(--rp-tan);
        background: #FFF9F2;
    }

    .rp-check input {
        width: 16px;
        height: 16px;
        margin-top: 2px;
        accent-color: var(--rp-maroon);
    }

    .rp-check strong {
        display: block;
        color: var(--rp-text);
        font-size: 10px;
        font-weight: 900;
    }

    .rp-check span span {
        display: block;
        margin-top: 4px;
        color: var(--rp-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .rp-form-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 12px;
    }

    .rp-form-field label {
        display: block;
        margin-bottom: 7px;
        color: var(--rp-text);
        font-size: 9px;
        font-weight: 850;
    }

    .rp-input,
    .rp-select,
    .rp-textarea {
        width: 100%;
        border: 1px solid var(--rp-border);
        border-radius: 12px;
        background: var(--rp-bg-soft);
        color: var(--rp-text);
        font-size: 10px;
        font-weight: 700;
        outline: none;
        transition: 150ms ease;
    }

    .rp-input,
    .rp-select {
        min-height: 40px;
        padding: 0 12px;
    }

    .rp-textarea {
        min-height: 100px;
        padding: 12px;
        resize: vertical;
        line-height: 1.5;
    }

    .rp-input:focus,
    .rp-select:focus,
    .rp-textarea:focus {
        border-color: var(--rp-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86,28,23,0.07);
    }

    .rp-side-card {
        border: 1px solid var(--rp-border);
        border-radius: 20px;
        background: var(--rp-card);
        box-shadow: var(--rp-shadow);
    }

    .rp-side-body {
        padding: 18px;
    }

    .rp-status-card {
        overflow: hidden;
        border: 0;
        background:
            radial-gradient(circle at 92% 12%, rgba(255,255,255,0.12), transparent 30%),
            linear-gradient(135deg, var(--rp-maroon) 0%, var(--rp-maroon-2) 58%, var(--rp-maroon-dark) 100%);
        color: #FFFFFF;
        box-shadow: 0 18px 44px rgba(86,28,23,0.18);
    }

    .rp-status-card small {
        color: rgba(255,255,255,0.55);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .rp-status-card h2 {
        margin: 10px 0 0;
        color: #FFFFFF;
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .rp-status-card p {
        margin: 9px 0 0;
        color: rgba(255,255,255,0.64);
        font-size: 9px;
        line-height: 1.6;
    }

    .rp-tracking-box {
        margin-top: 16px;
        padding: 13px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 13px;
        background: rgba(255,255,255,0.07);
    }

    .rp-tracking-box span {
        display: block;
        color: rgba(255,255,255,0.45);
        font-size: 8px;
        font-weight: 850;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .rp-tracking-box strong {
        display: block;
        margin-top: 5px;
        color: #FFFFFF;
        font-size: 11px;
        font-weight: 900;
    }

    .rp-destination {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .rp-destination strong {
        display: block;
        color: var(--rp-text);
        font-size: 10px;
        font-weight: 900;
    }

    .rp-destination small {
        display: block;
        margin-top: 4px;
        color: var(--rp-muted);
        font-size: 8px;
        font-weight: 700;
    }

    .rp-area-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 14px;
        padding: 12px;
        border: 1px solid var(--rp-border);
        border-radius: 13px;
        background: var(--rp-bg-soft);
    }

    .rp-area-box span {
        color: var(--rp-muted);
        font-size: 9px;
    }

    .rp-area-box strong {
        color: var(--rp-warning);
        font-size: 9px;
        font-weight: 900;
    }

    .rp-journey {
        display: grid;
        gap: 0;
        margin-top: 15px;
    }

    .rp-step {
        position: relative;
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        gap: 10px;
        padding-bottom: 17px;
    }

    .rp-step:last-child {
        padding-bottom: 0;
    }

    .rp-step:not(:last-child)::after {
        position: absolute;
        top: 26px;
        bottom: 0;
        left: 12px;
        width: 1px;
        background: var(--rp-border);
        content: "";
    }

    .rp-step-number {
        position: relative;
        z-index: 1;
        display: grid;
        width: 25px;
        height: 25px;
        place-items: center;
        border: 1px solid var(--rp-border);
        border-radius: 999px;
        background: var(--rp-bg-soft);
        color: var(--rp-muted);
        font-size: 8px;
        font-weight: 900;
    }

    .rp-step.is-current .rp-step-number {
        border-color: var(--rp-maroon);
        background: var(--rp-maroon);
        color: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86,28,23,0.08);
    }

    .rp-step strong {
        display: block;
        padding-top: 2px;
        color: var(--rp-muted);
        font-size: 9px;
        font-weight: 850;
    }

    .rp-step.is-current strong {
        color: var(--rp-maroon);
    }

    .rp-step small {
        display: block;
        margin-top: 3px;
        color: var(--rp-muted-light);
        font-size: 8px;
    }

    .rp-actionbar {
        position: sticky;
        bottom: 14px;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 14px 16px;
        border: 1px solid var(--rp-border);
        border-radius: 18px;
        background: color-mix(in srgb, var(--rp-card) 94%, transparent);
        box-shadow: var(--rp-shadow-hover);
        backdrop-filter: blur(16px);
    }

    .rp-actionbar strong {
        display: block;
        color: var(--rp-text);
        font-size: 10px;
        font-weight: 900;
    }

    .rp-actionbar span {
        display: block;
        margin-top: 4px;
        color: var(--rp-warning);
        font-size: 9px;
        font-weight: 700;
    }

    .rp-modal {
        visibility: hidden;
        position: fixed;
        inset: 0;
        z-index: 100;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgba(0,0,0,0.52);
        opacity: 0;
        backdrop-filter: blur(3px);
        transition: 180ms ease;
    }

    .rp-modal.is-open {
        visibility: visible;
        opacity: 1;
    }

    .rp-modal-panel {
        width: min(430px,100%);
        padding: 24px;
        border: 1px solid var(--rp-border);
        border-radius: 22px;
        background: var(--rp-card);
        box-shadow: var(--rp-shadow-hover);
        transform: scale(.96);
        transition: 180ms ease;
    }

    .rp-modal.is-open .rp-modal-panel {
        transform: scale(1);
    }

    .rp-modal-check {
        display: grid;
        width: 56px;
        height: 56px;
        place-items: center;
        margin-inline: auto;
        border-radius: 999px;
        background: var(--rp-success-soft);
        color: var(--rp-success);
    }

    .rp-modal-check svg {
        width: 24px;
        height: 24px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rp-modal-copy {
        margin-top: 16px;
        text-align: center;
    }

    .rp-modal-copy small {
        color: var(--rp-success);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .rp-modal-copy h2 {
        margin: 7px 0 0;
        color: var(--rp-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .rp-modal-copy p {
        margin: 9px auto 0;
        max-width: 330px;
        color: var(--rp-muted);
        font-size: 10px;
        line-height: 1.55;
    }

    .rp-modal-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 9px;
        margin-top: 20px;
    }

    @media (max-width: 1180px) {
        .rp-main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 860px) {
        .rp-hero,
        .rp-scanner {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .rp-hero .rp-btn,
        .rp-scanner-form {
            width: 100%;
        }

        .rp-info-grid {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }
    }

    @media (max-width: 640px) {
        .rp-scanner-form,
        .rp-form-grid,
        .rp-modal-actions {
            grid-template-columns: 1fr;
        }

        .rp-info-grid {
            grid-template-columns: 1fr;
        }

        .rp-item {
            grid-template-columns: auto minmax(0,1fr);
        }

        .rp-item-price {
            grid-column: 2;
            text-align: left;
        }

        .rp-actionbar {
            align-items: stretch;
            flex-direction: column;
        }

        .rp-actionbar .rp-btn {
            width: 100%;
        }
    }

    html.dark .rp-page {
        color: #F5EFE8;
    }

    html.dark .rp-hero,
    html.dark .rp-card,
    html.dark .rp-side-card,
    html.dark .rp-modal-panel {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,0.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .rp-card-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,0.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .rp-hero h1,
    html.dark .rp-card-head h2,
    html.dark .rp-value,
    html.dark .rp-address strong,
    html.dark .rp-item-main strong,
    html.dark .rp-item-price strong,
    html.dark .rp-check strong,
    html.dark .rp-form-field label,
    html.dark .rp-destination strong,
    html.dark .rp-actionbar strong,
    html.dark .rp-modal-copy h2 {
        color: #F5EFE8 !important;
    }

    html.dark .rp-hero p,
    html.dark .rp-breadcrumb,
    html.dark .rp-label,
    html.dark .rp-card-head p,
    html.dark .rp-address p,
    html.dark .rp-item-main span,
    html.dark .rp-item-price span,
    html.dark .rp-check span span,
    html.dark .rp-destination small,
    html.dark .rp-modal-copy p {
        color: #AFA19A !important;
    }

    html.dark .rp-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .rp-info-item {
        background: #1A1412 !important;
    }

    html.dark .rp-info-grid {
        background: #30231F !important;
    }

    html.dark .rp-address,
    html.dark .rp-check,
    html.dark .rp-area-box {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .rp-address-icon,
    html.dark .rp-item-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .rp-input,
    html.dark .rp-select,
    html.dark .rp-textarea {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .rp-input:focus,
    html.dark .rp-select:focus,
    html.dark .rp-textarea:focus {
        background: #211B17 !important;
        border-color: #60463A !important;
    }

    html.dark .rp-total {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .rp-total strong,
    html.dark .rp-step.is-current strong {
        color: #EBA99D !important;
    }

    html.dark .rp-step-number {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #AFA19A !important;
    }

    html.dark .rp-step.is-current .rp-step-number {
        background: #A84538 !important;
        border-color: #A84538 !important;
        color: #FFFFFF !important;
    }

    html.dark .rp-actionbar {
        background: rgba(26,20,18,.94) !important;
        border-color: #3B2E27 !important;
    }

    html.dark .rp-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .rp-btn-primary:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }

    html.dark .rp-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }
</style>

<div class="rp-page">

    <nav class="rp-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('logistics.dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('logistics.parcels.index') }}">Parcels</a>
        <span>/</span>
        <strong>Receive Parcel</strong>
    </nav>

    <section class="rp-hero">
        <div>
            <span class="rp-eyebrow">Parcel Intake</span>

            <h1>Receive a parcel.</h1>

            <p>
                Scan an incoming parcel, verify its information, inspect the package,
                and confirm that it has arrived at the LIKHAE sorting center.
            </p>
        </div>

        <a href="{{ route('logistics.parcels.index') }}" class="rp-btn rp-btn-soft">
            ← Back to Parcels
        </a>
    </section>

    <section class="rp-scanner">
        <div class="rp-scanner-copy">
            <span class="rp-scanner-kicker">
                <span class="rp-scanner-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        {!! $icons['scan'] !!}
                    </svg>
                </span>

                Quick Parcel Scanner
            </span>

            <h2>Scan or enter the tracking number</h2>

            <p>
                Use a barcode scanner or manually enter the parcel tracking ID.
            </p>
        </div>

        <div class="rp-scanner-form">
            <div class="rp-scanner-input">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['search'] !!}
                </svg>

                <input
                    type="text"
                    id="receiveTrackingNumber"
                    value="{{ $parcel['tracking'] }}"
                    placeholder="LH-2026-1001"
                >
            </div>

            <button type="button" id="loadParcelButton">
                Load Parcel →
            </button>
        </div>
    </section>

    <section class="rp-main-grid">

        <div class="rp-stack">

            <section class="rp-card">
                <header class="rp-card-head">
                    <div>
                        <h2>Parcel Information</h2>
                        <p>Verify the parcel against the order record.</p>
                    </div>

                    <span class="rp-status">
                        {{ $parcel['status'] }}
                    </span>
                </header>

                <div class="rp-info-grid">
                    <div class="rp-info-item">
                        <span class="rp-label">Tracking Number</span>
                        <strong class="rp-value">{{ $parcel['tracking'] }}</strong>
                    </div>

                    <div class="rp-info-item">
                        <span class="rp-label">Order Number</span>
                        <strong class="rp-value">{{ $parcel['order'] }}</strong>
                    </div>

                    <div class="rp-info-item">
                        <span class="rp-label">Payment</span>
                        <strong class="rp-value">{{ $parcel['payment'] }}</strong>
                    </div>

                    <div class="rp-info-item">
                        <span class="rp-label">Seller</span>
                        <strong class="rp-value">{{ $parcel['seller'] }}</strong>
                    </div>

                    <div class="rp-info-item">
                        <span class="rp-label">Buyer</span>
                        <strong class="rp-value">{{ $parcel['buyer'] }}</strong>
                    </div>

                    <div class="rp-info-item">
                        <span class="rp-label">Order Value</span>
                        <strong class="rp-value">₱{{ number_format($total, 2) }}</strong>
                    </div>
                </div>

                <div class="rp-address-wrap">
                    <span class="rp-label">Delivery Address</span>

                    <div class="rp-address">
                        <span class="rp-address-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['location'] !!}
                            </svg>
                        </span>

                        <div>
                            <strong>{{ $parcel['destination'] }}</strong>
                            <p>{{ $parcel['address'] }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rp-card">
                <header class="rp-card-head">
                    <div>
                        <h2>Order Items</h2>
                        <p>Items expected inside the parcel.</p>
                    </div>
                </header>

                <div class="rp-items">
                    @foreach($parcel['items'] as $item)
                        <article class="rp-item">
                            <span class="rp-item-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    {!! $icons['package'] !!}
                                </svg>
                            </span>

                            <div class="rp-item-main">
                                <strong>{{ $item['name'] }}</strong>
                                <span>{{ $item['variation'] }}</span>
                            </div>

                            <div class="rp-item-price">
                                <strong>₱{{ number_format($item['price'], 2) }}</strong>
                                <span>Qty {{ $item['quantity'] }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="rp-total">
                    <span>Parcel Total</span>
                    <strong>₱{{ number_format($total, 2) }}</strong>
                </div>
            </section>

            <section class="rp-card">
                <header class="rp-card-head">
                    <div>
                        <h2>Receiving Checklist</h2>
                        <p>Complete all checks before confirming the parcel.</p>
                    </div>
                </header>

                <div class="rp-card-body">
                    <div class="rp-checklist">
                        <label class="rp-check">
                            <input
                                type="checkbox"
                                class="receive-check"
                                checked
                            >

                            <span>
                                <strong>Tracking number verified</strong>
                                <span>The physical label matches the parcel record.</span>
                            </span>
                        </label>

                        <label class="rp-check">
                            <input
                                type="checkbox"
                                class="receive-check"
                                checked
                            >

                            <span>
                                <strong>Parcel physically received</strong>
                                <span>The parcel is currently inside the sorting center.</span>
                            </span>
                        </label>

                        <label class="rp-check">
                            <input
                                type="checkbox"
                                class="receive-check"
                            >

                            <span>
                                <strong>Package condition inspected</strong>
                                <span>Check the outer packaging for visible damage or tampering.</span>
                            </span>
                        </label>
                    </div>
                </div>
            </section>

            <section class="rp-card">
                <header class="rp-card-head">
                    <div>
                        <h2>Receiving Details</h2>
                        <p>Record how the parcel was received.</p>
                    </div>
                </header>

                <div class="rp-card-body">
                    <div class="rp-form-grid">
                        <div class="rp-form-field">
                            <label for="packageCondition">Package Condition</label>

                            <select id="packageCondition" class="rp-select">
                                <option value="Good" selected>Good</option>
                                <option value="Minor Damage">Minor Damage</option>
                                <option value="Damaged">Damaged</option>
                                <option value="Tampered">Tampered</option>
                            </select>
                        </div>

                        <div class="rp-form-field">
                            <label for="receivedFrom">Received From</label>

                            <select id="receivedFrom" class="rp-select">
                                <option selected>Seller / Drop-off</option>
                                <option>Courier Transfer</option>
                                <option>Seller Representative</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="rp-form-field" style="margin-top:12px;">
                        <label for="receivingNotes">Receiving Notes</label>

                        <textarea
                            id="receivingNotes"
                            class="rp-textarea"
                            placeholder="Add package observations or receiving notes..."
                        ></textarea>
                    </div>
                </div>
            </section>

        </div>

        <aside class="rp-stack">

            <section class="rp-side-card rp-status-card">
                <div class="rp-side-body">
                    <small>Current Status</small>

                    <h2>Incoming Parcel</h2>

                    <p>
                        Waiting for receiving confirmation at the sorting center.
                    </p>

                    <div class="rp-tracking-box">
                        <span>Tracking</span>
                        <strong>{{ $parcel['tracking'] }}</strong>
                    </div>
                </div>
            </section>

            <section class="rp-side-card">
                <div class="rp-side-body">
                    <div class="rp-destination">
                        <span class="rp-address-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['location'] !!}
                            </svg>
                        </span>

                        <div>
                            <small>Destination</small>
                            <strong>{{ $parcel['destination'] }}</strong>
                        </div>
                    </div>

                    <div class="rp-area-box">
                        <span>Delivery Area</span>
                        <strong>To be determined</strong>
                    </div>
                </div>
            </section>

            <section class="rp-side-card">
                <div class="rp-side-body">
                    <span class="rp-label">Workflow</span>

                    <h2
                        style="
                            margin:7px 0 0;
                            color:var(--rp-text);
                            font-family:'Instrument Serif',Georgia,serif;
                            font-size:27px;
                            font-weight:400;
                            line-height:1;
                            letter-spacing:-.04em;
                        "
                    >
                        Parcel Journey
                    </h2>

                    <div class="rp-journey">
                        @foreach($journey as $step)
                            <div class="rp-step {{ $step['state'] === 'current' ? 'is-current' : '' }}">
                                <span class="rp-step-number">
                                    {{ $loop->iteration }}
                                </span>

                                <div>
                                    <strong>{{ $step['title'] }}</strong>

                                    <small>
                                        {{ $step['state'] === 'current' ? 'Current step' : 'Pending' }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

        </aside>

    </section>

    <section class="rp-actionbar">
        <div>
            <strong>Ready to confirm this parcel?</strong>
            <span id="checklistStatusText">2 of 3 receiving checks completed.</span>
        </div>

        <button type="button" id="confirmReceiveButton" class="rp-btn rp-btn-primary">
            Confirm Parcel Received

            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['arrow'] !!}
            </svg>
        </button>
    </section>

</div>

<div id="receiveSuccessModal" class="rp-modal">
    <div class="rp-modal-panel" id="receiveSuccessPanel">
        <span class="rp-modal-check" aria-hidden="true">
            <svg viewBox="0 0 24 24">
                {!! $icons['check'] !!}
            </svg>
        </span>

        <div class="rp-modal-copy">
            <small>Parcel Received</small>

            <h2>Ready for sorting.</h2>

            <p>
                {{ $parcel['tracking'] }} has been recorded as received
                and can now proceed to destination sorting.
            </p>
        </div>

        <div class="rp-modal-actions">
            <a href="{{ route('logistics.parcels.index') }}" class="rp-btn rp-btn-soft">
                Back to Parcels
            </a>

            <a
                href="{{ route(
                    'logistics.sorting.index',
                    ['tracking' => $parcel['tracking']]
                ) }}"
                class="rp-btn rp-btn-primary"
            >
                Continue to Sorting
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const scannerInput =
        document.getElementById('receiveTrackingNumber');

    const loadButton =
        document.getElementById('loadParcelButton');

    const checkboxes =
        Array.from(
            document.querySelectorAll('.receive-check')
        );

    const checklistStatus =
        document.getElementById('checklistStatusText');

    const confirmButton =
        document.getElementById('confirmReceiveButton');

    const modal =
        document.getElementById('receiveSuccessModal');

    const expectedTracking =
        @json($parcel['tracking']);


    function loadParcel() {
        const tracking =
            scannerInput?.value?.trim();

        if (!tracking) {
            scannerInput?.focus();
            return;
        }

        if (
            tracking.toUpperCase() !==
            expectedTracking.toUpperCase()
        ) {
            window.alert(
                `Demo mode: try tracking number ${expectedTracking}.`
            );

            return;
        }

        window.alert(
            `Parcel ${expectedTracking} loaded successfully.`
        );
    }


    function updateChecklistStatus() {
        const completed =
            checkboxes.filter(
                checkbox => checkbox.checked
            ).length;

        const total =
            checkboxes.length;

        if (!checklistStatus) {
            return;
        }

        checklistStatus.textContent =
            `${completed} of ${total} receiving checks completed.`;

        checklistStatus.style.color =
            completed === total
                ? '#256F4A'
                : '#9A5B11';
    }


    function openModal() {
        if (!modal) {
            return;
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


    loadButton?.addEventListener(
        'click',
        loadParcel
    );


    scannerInput?.addEventListener(
        'keydown',
        function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                loadParcel();
            }
        }
    );


    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener(
            'change',
            updateChecklistStatus
        );
    });


    confirmButton?.addEventListener(
        'click',
        function () {
            const allComplete =
                checkboxes.every(
                    checkbox => checkbox.checked
                );

            if (!allComplete) {
                window.alert(
                    'Please complete all receiving checklist items before confirming the parcel.'
                );

                return;
            }

            openModal();
        }
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


    updateChecklistStatus();
});
</script>
@endpush
