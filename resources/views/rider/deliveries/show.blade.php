@extends('rider.app')

@section('title', 'Delivery Details — LIKHAE Rider')

@php
    $delivery = [
        'tracking' => 'LH-2026-1007',
        'status' => 'ASSIGNED_TO_RIDER',
        'payment_method' => 'Cash On Delivery',
        'cod_amount' => '₱1,200',
        'parcel_type' => 'Regular Package',
        'customer' => 'Juan Dela Cruz',
        'address' => '123 Main Street, Los Baños, Laguna',
        'contact' => '0917 555 1234',
    ];

    $timeline = [
        [
            'title' => 'Receive Assignment',
            'status' => 'Completed',
            'description' => 'Delivery assignment received from the sorting center.',
        ],
        [
            'title' => 'Pickup From Sorting Center',
            'status' => 'Current',
            'description' => 'Collect the parcel and verify the tracking label.',
        ],
        [
            'title' => 'Out For Delivery',
            'status' => 'Pending',
            'description' => 'Start the trip to the customer delivery address.',
        ],
        [
            'title' => 'Delivered',
            'status' => 'Pending',
            'description' => 'Complete the parcel handoff to the customer.',
        ],
        [
            'title' => 'Completed',
            'status' => 'Pending',
            'description' => 'Close the delivery after buyer confirmation.',
        ],
    ];

    $icons = [
        'truck' => '
            <path d="M3 6h11v11H3z"/>
            <path d="M14 10h4l3 3v4h-7z"/>
            <circle cx="7" cy="19" r="2"/>
            <circle cx="18" cy="19" r="2"/>
        ',
        'parcel' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',
        'user' => '
            <circle cx="12" cy="8" r="4"/>
            <path d="M4 21c0-5 3-8 8-8s8 3 8 8"/>
        ',
        'location' => '
            <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"/>
            <circle cx="12" cy="10" r="2"/>
        ',
        'phone' => '
            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1A19.5 19.5 0 0 1 5.2 12 19.8 19.8 0 0 1 2.1 3.3 2 2 0 0 1 4.1 1h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 9a16 16 0 0 0 7 7l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2Z"/>
        ',
        'money' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="M15.5 8.5c-.8-.7-2-1-3.2-1-1.8 0-3.3.8-3.3 2.3 0 3.8 7 1.7 7 5 0 1.5-1.5 2.5-3.6 2.5-1.5 0-2.8-.4-3.8-1.2"/>
            <path d="M12 5.5v13"/>
        ',
        'check' => '
            <path d="m5 12 4 4 10-10"/>
        ',
        'arrow' => '
            <path d="M5 12h14"/>
            <path d="m14 7 5 5-5 5"/>
        ',
        'navigation' => '
            <path d="m3 11 18-8-8 18-2-8z"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --dd-bg: #FBF7F2;
        --dd-soft: #F6EFE7;
        --dd-warm: #F3E4DE;
        --dd-card: #FFFDF9;

        --dd-border: #EADCCC;
        --dd-border-strong: #DBCEC1;

        --dd-maroon: #561C17;
        --dd-maroon-2: #642920;
        --dd-maroon-dark: #3E130F;

        --dd-text: #3B211B;
        --dd-text-dark: #1C160F;
        --dd-brown: #6C4936;
        --dd-muted: #987865;
        --dd-muted-light: #A99386;

        --dd-tan: #C19771;

        --dd-success: #256F4A;
        --dd-success-soft: #EAF7EF;

        --dd-warning: #9A5B11;
        --dd-warning-soft: #FFF6DE;

        --dd-danger: #B42318;
        --dd-danger-soft: #FCEBE9;

        --dd-shadow: 0 8px 24px rgba(86,28,23,.055);
        --dd-shadow-hover: 0 18px 44px rgba(86,28,23,.10);
    }

    .dd-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--dd-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .dd-page * {
        box-sizing: border-box;
    }

    .dd-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--dd-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--dd-shadow);
    }

    .dd-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--dd-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .dd-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .dd-hero h1 {
        margin: 10px 0 0;
        color: var(--dd-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px,5vw,66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .dd-hero p {
        max-width: 680px;
        margin: 13px 0 0;
        color: var(--dd-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .dd-status {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        gap: 7px;
        padding: 0 12px;
        border: 1px solid #E6C7BE;
        border-radius: 999px;
        background: var(--dd-warm);
        color: var(--dd-maroon);
        font-size: 9px;
        font-weight: 950;
        white-space: nowrap;
    }

    .dd-status::before {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .dd-main {
        display: grid;
        grid-template-columns: minmax(0,1fr) 350px;
        gap: 18px;
        align-items: start;
    }

    .dd-stack {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .dd-card {
        overflow: hidden;
        border: 1px solid var(--dd-border);
        border-radius: 22px;
        background: var(--dd-card);
        box-shadow: var(--dd-shadow);
    }

    .dd-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--dd-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .dd-card-head h2 {
        margin: 0;
        color: var(--dd-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .dd-card-head p {
        margin: 7px 0 0;
        color: var(--dd-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .dd-card-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--dd-warm);
        color: var(--dd-maroon);
    }

    .dd-card-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .dd-parcel-header {
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        align-items: center;
        gap: 14px;
        padding: 18px 20px;
    }

    .dd-parcel-icon {
        display: grid;
        width: 54px;
        height: 54px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 17px;
        background: var(--dd-warm);
        color: var(--dd-maroon);
    }

    .dd-parcel-icon svg {
        width: 23px;
        height: 23px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
    }

    .dd-parcel-header span {
        display: block;
        color: var(--dd-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dd-parcel-header strong {
        display: block;
        margin-top: 5px;
        color: var(--dd-text);
        font-size: 14px;
        font-weight: 950;
    }

    .dd-info-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 1px;
        background: var(--dd-border);
        border-top: 1px solid var(--dd-border);
    }

    .dd-info {
        padding: 16px 20px;
        background: var(--dd-card);
    }

    .dd-label {
        display: block;
        color: var(--dd-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dd-value {
        display: block;
        margin-top: 7px;
        color: var(--dd-text);
        font-size: 10px;
        font-weight: 900;
        line-height: 1.45;
    }

    .dd-value.is-maroon {
        color: var(--dd-maroon);
    }

    .dd-customer {
        display: grid;
        gap: 10px;
        padding: 18px 20px;
    }

    .dd-customer-row {
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        gap: 11px;
        padding: 13px;
        border: 1px solid var(--dd-border);
        border-radius: 14px;
        background: var(--dd-soft);
    }

    .dd-customer-icon {
        display: grid;
        width: 38px;
        height: 38px;
        place-items: center;
        border-radius: 11px;
        background: var(--dd-warm);
        color: var(--dd-maroon);
    }

    .dd-customer-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .dd-customer-row strong {
        display: block;
        color: var(--dd-text);
        font-size: 10px;
        font-weight: 900;
    }

    .dd-customer-row p {
        margin: 4px 0 0;
        color: var(--dd-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .dd-timeline {
        display: grid;
        padding: 18px 20px;
    }

    .dd-step {
        position: relative;
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        gap: 11px;
        padding-bottom: 20px;
    }

    .dd-step:last-child {
        padding-bottom: 0;
    }

    .dd-step:not(:last-child)::after {
        position: absolute;
        top: 31px;
        bottom: 0;
        left: 14px;
        width: 1px;
        background: var(--dd-border);
        content: "";
    }

    .dd-step.is-completed:not(:last-child)::after {
        background: #9FC9B3;
    }

    .dd-step-dot {
        position: relative;
        z-index: 1;
        display: grid;
        width: 29px;
        height: 29px;
        place-items: center;
        border: 1px solid var(--dd-border);
        border-radius: 999px;
        background: var(--dd-soft);
        color: var(--dd-muted);
        font-size: 8px;
        font-weight: 950;
    }

    .dd-step.is-completed .dd-step-dot {
        border-color: var(--dd-success);
        background: var(--dd-success);
        color: #FFFFFF;
    }

    .dd-step.is-current .dd-step-dot {
        border-color: var(--dd-maroon);
        background: var(--dd-maroon);
        color: #FFFFFF;
        box-shadow: 0 0 0 5px rgba(86,28,23,.08);
    }

    .dd-step-dot svg {
        width: 13px;
        height: 13px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
    }

    .dd-step strong {
        display: block;
        padding-top: 2px;
        color: var(--dd-muted);
        font-size: 9px;
        font-weight: 900;
    }

    .dd-step.is-current strong,
    .dd-step.is-completed strong {
        color: var(--dd-text);
    }

    .dd-step p {
        margin: 4px 0 0;
        color: var(--dd-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .dd-step small {
        display: block;
        margin-top: 5px;
        color: var(--dd-muted-light);
        font-size: 7px;
        font-weight: 800;
    }

    .dd-actions-card {
        padding: 18px 20px;
    }

    .dd-action-list {
        display: grid;
        gap: 9px;
        margin-top: 14px;
    }

    .dd-btn {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 14px;
        border: 1px solid transparent;
        border-radius: 12px;
        font-size: 9px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        transition: 150ms ease;
    }

    .dd-btn:hover {
        transform: translateY(-1px);
    }

    .dd-btn svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .dd-btn-primary {
        border-color: var(--dd-maroon);
        background: var(--dd-maroon);
        color: #FFFFFF;
        box-shadow: 0 10px 20px rgba(86,28,23,.13);
    }

    .dd-btn-primary:hover {
        border-color: var(--dd-maroon-dark);
        background: var(--dd-maroon-dark);
    }

    .dd-btn-soft {
        border-color: var(--dd-border);
        background: var(--dd-card);
        color: var(--dd-maroon);
    }

    .dd-btn-soft:hover {
        border-color: var(--dd-tan);
        background: var(--dd-warm);
    }

    .dd-btn-success {
        border-color: var(--dd-success) !important;
        background: var(--dd-success) !important;
        color: #FFFFFF !important;
    }

    .dd-btn[disabled] {
        cursor: default;
        opacity: .9;
        transform: none;
    }

    .dd-next {
        overflow: hidden;
        border-radius: 20px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255,255,255,.12), transparent 30%),
            linear-gradient(135deg, var(--dd-maroon) 0%, var(--dd-maroon-2) 58%, var(--dd-maroon-dark) 100%);
        color: #FFFFFF;
        box-shadow: 0 18px 44px rgba(86,28,23,.18);
    }

    .dd-next-body {
        padding: 19px;
    }

    .dd-next small {
        color: rgba(255,255,255,.55);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .dd-next h2 {
        margin: 10px 0 0;
        color: #FFFFFF;
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .dd-next p {
        margin: 8px 0 0;
        color: rgba(255,255,255,.62);
        font-size: 9px;
        line-height: 1.55;
    }

    .dd-next-flow {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 16px;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 12px;
        background: rgba(255,255,255,.07);
        color: rgba(255,255,255,.82);
        font-size: 8px;
        font-weight: 800;
    }

    .dd-next-flow svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    @media (max-width: 1120px) {
        .dd-main {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 820px) {
        .dd-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }
    }

    @media (max-width: 600px) {
        .dd-info-grid {
            grid-template-columns: 1fr;
        }
    }

    html.dark .dd-page {
        color: #F5EFE8;
    }

    html.dark .dd-hero,
    html.dark .dd-card {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .dd-card-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .dd-hero h1,
    html.dark .dd-card-head h2,
    html.dark .dd-parcel-header strong,
    html.dark .dd-value,
    html.dark .dd-customer-row strong,
    html.dark .dd-step.is-current strong,
    html.dark .dd-step.is-completed strong {
        color: #F5EFE8 !important;
    }

    html.dark .dd-hero p,
    html.dark .dd-card-head p,
    html.dark .dd-label,
    html.dark .dd-customer-row p,
    html.dark .dd-step p,
    html.dark .dd-step small {
        color: #AFA19A !important;
    }

    html.dark .dd-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .dd-info-grid {
        background: #30231F !important;
    }

    html.dark .dd-info {
        background: #1A1412 !important;
    }

    html.dark .dd-parcel-icon,
    html.dark .dd-card-icon,
    html.dark .dd-customer-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .dd-customer-row {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .dd-status {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .dd-step-dot {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #AFA19A !important;
    }

    html.dark .dd-step.is-current .dd-step-dot {
        background: #A84538 !important;
        border-color: #A84538 !important;
        color: #FFFFFF !important;
    }

    html.dark .dd-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .dd-btn-primary:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }

    html.dark .dd-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }
</style>

<div class="dd-page">

    <section class="dd-hero">
        <div>
            <span class="dd-eyebrow">
                Delivery Assignment
            </span>

            <h1>
                Delivery Details
            </h1>

            <p>
                Review parcel and customer information, accept the assignment,
                then continue the delivery from the sorting center to the buyer.
            </p>
        </div>

        <span class="dd-status" id="deliveryStatusBadge">
            {{ $delivery['status'] }}
        </span>
    </section>

    <section class="dd-main">

        <div class="dd-stack">

            <section class="dd-card">
                <header class="dd-card-head">
                    <div>
                        <h2>
                            Parcel Details
                        </h2>

                        <p>
                            Verify the parcel before leaving the sorting center.
                        </p>
                    </div>

                    <span class="dd-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['parcel'] !!}
                        </svg>
                    </span>
                </header>

                <div class="dd-parcel-header">
                    <span class="dd-parcel-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['truck'] !!}
                        </svg>
                    </span>

                    <div>
                        <span>
                            Tracking Number
                        </span>

                        <strong>
                            {{ $delivery['tracking'] }}
                        </strong>
                    </div>
                </div>

                <div class="dd-info-grid">
                    <div class="dd-info">
                        <span class="dd-label">
                            Current Status
                        </span>

                        <strong class="dd-value is-maroon" id="deliveryCurrentStatus">
                            {{ $delivery['status'] }}
                        </strong>
                    </div>

                    <div class="dd-info">
                        <span class="dd-label">
                            Payment Method
                        </span>

                        <strong class="dd-value">
                            {{ $delivery['payment_method'] }}
                        </strong>
                    </div>

                    <div class="dd-info">
                        <span class="dd-label">
                            COD Amount
                        </span>

                        <strong class="dd-value">
                            {{ $delivery['cod_amount'] }}
                        </strong>
                    </div>

                    <div class="dd-info">
                        <span class="dd-label">
                            Parcel Type
                        </span>

                        <strong class="dd-value">
                            {{ $delivery['parcel_type'] }}
                        </strong>
                    </div>
                </div>
            </section>

            <section class="dd-card">
                <header class="dd-card-head">
                    <div>
                        <h2>
                            Customer Information
                        </h2>

                        <p>
                            Customer contact and delivery destination.
                        </p>
                    </div>

                    <span class="dd-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['user'] !!}
                        </svg>
                    </span>
                </header>

                <div class="dd-customer">
                    <div class="dd-customer-row">
                        <span class="dd-customer-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['user'] !!}
                            </svg>
                        </span>

                        <div>
                            <strong>
                                {{ $delivery['customer'] }}
                            </strong>

                            <p>
                                Customer Name
                            </p>
                        </div>
                    </div>

                    <div class="dd-customer-row">
                        <span class="dd-customer-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['location'] !!}
                            </svg>
                        </span>

                        <div>
                            <strong>
                                {{ $delivery['address'] }}
                            </strong>

                            <p>
                                Delivery Address
                            </p>
                        </div>
                    </div>

                    <div class="dd-customer-row">
                        <span class="dd-customer-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $icons['phone'] !!}
                            </svg>
                        </span>

                        <div>
                            <strong>
                                {{ $delivery['contact'] }}
                            </strong>

                            <p>
                                Contact Number
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="dd-card">
                <header class="dd-card-head">
                    <div>
                        <h2>
                            Delivery Progress
                        </h2>

                        <p>
                            Follow the parcel workflow from assignment to completion.
                        </p>
                    </div>
                </header>

                <div class="dd-timeline">
                    @foreach($timeline as $index => $step)
                        @php
                            $stepClass = match($step['status']) {
                                'Completed' => 'is-completed',
                                'Current' => 'is-current',
                                default => '',
                            };
                        @endphp

                        <div class="dd-step {{ $stepClass }}">
                            <span class="dd-step-dot">
                                @if($step['status'] === 'Completed')
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        {!! $icons['check'] !!}
                                    </svg>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </span>

                            <div>
                                <strong>
                                    {{ $step['title'] }}
                                </strong>

                                <p>
                                    {{ $step['description'] }}
                                </p>

                                <small>
                                    {{ $step['status'] }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>

        <aside class="dd-stack">

            <section class="dd-card">
                <header class="dd-card-head">
                    <div>
                        <h2>
                            Delivery Actions
                        </h2>

                        <p>
                            Continue the current rider workflow.
                        </p>
                    </div>
                </header>

                <div class="dd-actions-card">
                    <div class="dd-action-list">
                        <button
                            type="button"
                            id="acceptDeliveryBtn"
                            class="dd-btn dd-btn-primary"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['check'] !!}
                            </svg>

                            Accept Delivery
                        </button>

                        <button
                            type="button"
                            id="navigateSortingBtn"
                            class="dd-btn dd-btn-soft"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['navigation'] !!}
                            </svg>

                            Navigate To Sorting Center
                        </button>

                        <button
                            type="button"
                            id="contactCustomerBtn"
                            class="dd-btn dd-btn-soft"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['phone'] !!}
                            </svg>

                            Contact Customer
                        </button>
                    </div>
                </div>
            </section>

            <section class="dd-next">
                <div class="dd-next-body">
                    <small>
                        Next Status
                    </small>

                    <h2>
                        OUT_FOR_DELIVERY
                    </h2>

                    <p>
                        After collecting the parcel from the sorting center,
                        start the final-mile delivery to the customer.
                    </p>

                    <div class="dd-next-flow">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['arrow'] !!}
                        </svg>

                        Pickup complete → start delivery
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
    const acceptButton =
        document.getElementById('acceptDeliveryBtn');

    const navigateButton =
        document.getElementById('navigateSortingBtn');

    const contactButton =
        document.getElementById('contactCustomerBtn');

    const statusBadge =
        document.getElementById('deliveryStatusBadge');

    const currentStatus =
        document.getElementById('deliveryCurrentStatus');


    acceptButton?.addEventListener(
        'click',
        function () {
            acceptButton.textContent =
                'Delivery Accepted';

            acceptButton.disabled =
                true;

            acceptButton.classList.remove(
                'dd-btn-primary'
            );

            acceptButton.classList.add(
                'dd-btn-success'
            );

            if (statusBadge) {
                statusBadge.textContent =
                    'OUT_FOR_DELIVERY';

                statusBadge.style.borderColor =
                    'var(--dd-success)';

                statusBadge.style.background =
                    'var(--dd-success-soft)';

                statusBadge.style.color =
                    'var(--dd-success)';
            }

            if (currentStatus) {
                currentStatus.textContent =
                    'OUT_FOR_DELIVERY';

                currentStatus.style.color =
                    'var(--dd-success)';
            }

            window.setTimeout(
                function () {
                    window.location.href =
                        '{{ route('rider.deliveries.tracking', $id) }}';
                },
                400
            );
        }
    );


    navigateButton?.addEventListener(
        'click',
        function () {
            window.open(
                'https://www.google.com/maps/search/?api=1&query=Sorting+Center+Laguna',
                '_blank'
            );
        }
    );


    contactButton?.addEventListener(
        'click',
        function () {
            window.location.href =
                'tel:+639175551234';
        }
    );
});
</script>
@endpush
