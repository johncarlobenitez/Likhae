@extends('layouts.seller')

@section('title', 'Marketing')
@section('active', 'marketing')
@section('subtitle', 'Create controlled promotions that grow sales without hurting margins.')

@php
    $tabs = [
        'discounts' => 'Discounts',
        'vouchers' => 'Vouchers',
        'promotions' => 'Promotions',
    ];

    $requestedTab = $tab ?? request('tab', 'discounts');

    $currentTab = array_key_exists($requestedTab, $tabs)
        ? $requestedTab
        : 'discounts';

    $vouchers = [
        ['code' => 'WELCOME100', 'benefit' => '₱100 off', 'minimum' => '₱1,000', 'usage' => '245 / 500', 'validity' => 'Sep 01–30', 'status' => 'Active'],
        ['code' => 'TECH10', 'benefit' => '10% off', 'minimum' => '₱2,500', 'usage' => '302 / 400', 'validity' => 'Sep 01–15', 'status' => 'Active'],
        ['code' => 'SHIPFREE', 'benefit' => 'Free shipping', 'minimum' => '₱799', 'usage' => '101 / 250', 'validity' => 'Sep 04–10', 'status' => 'Scheduled'],
    ];

    $marketplaceCampaigns = [
        ['title' => 'Payday Tech Deals', 'date' => 'Sep 14–16', 'description' => 'Electronics and accessories', 'eligibility' => '12 products eligible'],
        ['title' => 'Home Office Week', 'date' => 'Sep 20–25', 'description' => 'Workspace essentials', 'eligibility' => '8 products eligible'],
        ['title' => 'Month-End Sale', 'date' => 'Sep 27–30', 'description' => 'Storewide campaign', 'eligibility' => 'All active products'],
    ];

    $discounts = [
        ['title' => 'Weekend Tech Sale', 'benefit' => '10% off', 'products' => '8 products', 'date' => 'Sep 04–06', 'status' => 'Active', 'progress' => 72],
        ['title' => 'Monitor Launch Offer', 'benefit' => '₱1,299 off', 'products' => '1 product', 'date' => 'Sep 01–15', 'status' => 'Active', 'progress' => 54],
        ['title' => 'Accessories Bundle', 'benefit' => '15% off', 'products' => '9 products', 'date' => 'Sep 10–20', 'status' => 'Scheduled', 'progress' => 0],
    ];
@endphp

@section('content')
<style>
    :root {
        --mkt-bg: #FBF7F2;
        --mkt-bg-soft: #F6EFE7;
        --mkt-bg-alt: #EFE7DE;
        --mkt-card: #FFFDF9;

        --mkt-border: #EADCCC;
        --mkt-border-strong: #DBCEC1;

        --mkt-maroon: #561C17;
        --mkt-maroon-2: #642920;
        --mkt-maroon-dark: #3E130F;

        --mkt-text: #3B211B;
        --mkt-brown: #6C4936;
        --mkt-muted: #987865;
        --mkt-muted-2: #A99386;

        --mkt-tan: #C19771;

        --mkt-success: #256F4A;
        --mkt-success-soft: #EAF7EF;

        --mkt-warning: #9A5B11;
        --mkt-warning-soft: #FFF6DE;

        --mkt-danger: #B42318;
        --mkt-danger-soft: #FCEBE9;

        --mkt-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --mkt-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .sl-marketing-page {
        color: var(--mkt-text);

        --sl-blue: var(--mkt-maroon);
        --sl-blue-dark: var(--mkt-maroon-dark);
        --sl-blue-soft: #F3E4DE;
        --sl-indigo: var(--mkt-tan);
    }

    .sl-marketing-page .sl-page-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;

        padding: 32px 36px;

        border: 1px solid var(--mkt-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--mkt-shadow-soft);
    }

    .sl-marketing-page .sl-eyebrow {
        color: var(--mkt-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .sl-marketing-page .sl-page-toolbar h2 {
        margin-top: 10px;

        color: var(--mkt-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .sl-marketing-page .sl-page-toolbar p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--mkt-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .sl-marketing-page .sl-btn {
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

    .sl-marketing-page .sl-btn:hover,
    .sl-marketing-modal .sl-btn:hover {
        transform: translateY(-1px);
    }

    .sl-marketing-page .sl-btn svg {
        width: 16px;
        height: 16px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sl-marketing-page .sl-btn-primary,
    .sl-marketing-modal .sl-btn-primary {
        background: var(--mkt-maroon) !important;
        border-color: var(--mkt-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .sl-marketing-page .sl-btn-primary:hover,
    .sl-marketing-modal .sl-btn-primary:hover {
        background: var(--mkt-maroon-dark) !important;
        border-color: var(--mkt-maroon-dark) !important;
    }

    .sl-marketing-page .sl-btn-white,
    .sl-marketing-page .sl-btn-ghost,
    .sl-marketing-page .sl-btn-soft,
    .sl-marketing-modal .sl-btn-ghost {
        background: var(--mkt-card) !important;
        border-color: var(--mkt-tan) !important;
        color: var(--mkt-maroon) !important;
    }

    .sl-marketing-page .sl-btn-white:hover,
    .sl-marketing-page .sl-btn-ghost:hover,
    .sl-marketing-page .sl-btn-soft:hover,
    .sl-marketing-modal .sl-btn-ghost:hover {
        background: #F3E4DE !important;
        border-color: var(--mkt-maroon) !important;
    }

    .sl-marketing-page .sl-btn-sm,
    .sl-marketing-modal .sl-btn-sm {
        min-height: 34px;
        padding-inline: 12px;
        border-radius: 11px;

        font-size: 11px;
    }

    .sl-marketing-page .sl-btn-block {
        width: 100%;
    }

    .sl-marketing-page .sl-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 6px;

        border: 1px solid var(--mkt-border);
        border-radius: 16px;

        background: var(--mkt-card);
        box-shadow: var(--mkt-shadow-soft);
    }

    .sl-marketing-page .sl-tabs a {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;

        padding: 0 16px;

        border-radius: 12px;

        color: var(--mkt-brown);
        text-decoration: none;

        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;

        transition: 160ms ease;
    }

    .sl-marketing-page .sl-tabs a:hover {
        background: var(--mkt-bg-soft);
        color: var(--mkt-maroon);
    }

    .sl-marketing-page .sl-tabs a.is-active {
        background: var(--mkt-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .sl-marketing-page .sl-mini-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .sl-marketing-page .sl-mini-stats div {
        padding: 18px;

        border: 1px solid var(--mkt-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        box-shadow: var(--mkt-shadow-soft);
    }

    .sl-marketing-page .sl-mini-stats span {
        display: block;

        color: var(--mkt-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .sl-marketing-page .sl-mini-stats strong {
        display: block;
        margin-top: 8px;

        color: var(--mkt-maroon);

        font-size: 32px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-marketing-page .sl-card {
        overflow: hidden;

        border: 1px solid var(--mkt-border) !important;
        border-radius: 24px !important;

        background: var(--mkt-card) !important;
        color: var(--mkt-text) !important;

        box-shadow: var(--mkt-shadow-soft) !important;
    }

    .sl-marketing-page .sl-table-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding: 20px 22px;

        border-bottom: 1px solid var(--mkt-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .sl-marketing-page .sl-table-toolbar h3 {
        margin: 0;

        color: var(--mkt-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 32px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-marketing-page .sl-table-toolbar p {
        margin: 8px 0 0;

        color: var(--mkt-muted) !important;

        font-size: 12px;
        line-height: 1.65;
    }

    .sl-marketing-page .sl-table-wrap {
        overflow-x: auto;
    }

    .sl-marketing-page .sl-table {
        width: 100%;
        min-width: 860px;
        border-collapse: collapse;
    }

    .sl-marketing-page .sl-table thead {
        background: var(--mkt-bg-soft);
    }

    .sl-marketing-page .sl-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--mkt-border);

        color: var(--mkt-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .sl-marketing-page .sl-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--mkt-brown);

        font-size: 12px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .sl-marketing-page .sl-table tbody tr:hover td {
        background: var(--mkt-bg-soft);
    }

    .sl-marketing-page .sl-table td strong {
        color: var(--mkt-text);
        font-weight: 950;
    }

    .sl-marketing-page .sl-code {
        display: inline-flex;
        min-height: 28px;
        align-items: center;

        padding: 0 10px;

        border: 1px dashed var(--mkt-tan);
        border-radius: 10px;

        background: var(--mkt-bg-soft);
        color: var(--mkt-maroon) !important;

        font-size: 11px;
        font-weight: 950;
        letter-spacing: 0.08em;
    }

    .sl-marketing-page .sl-status {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        justify-content: center;

        padding: 0 10px;

        border: 1px solid transparent;
        border-radius: 999px;

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .sl-marketing-page .sl-status.is-success {
        background: var(--mkt-success-soft);
        border-color: #CFE8DA;
        color: var(--mkt-success);
    }

    .sl-marketing-page .sl-status.is-neutral {
        background: var(--mkt-bg-soft);
        border-color: var(--mkt-border-strong);
        color: var(--mkt-brown);
    }

    .sl-marketing-page .sl-status.is-warning,
    .sl-marketing-page .sl-status.is-scheduled {
        background: var(--mkt-warning-soft);
        border-color: #EAD39A;
        color: var(--mkt-warning);
    }

    .sl-marketing-page .sl-promo-list {
        display: grid;
        gap: 12px;

        padding: 18px;
    }

    .sl-marketing-page .sl-promo-row {
        display: grid;
        grid-template-columns: auto minmax(0, 1.3fr) minmax(130px, 0.5fr) minmax(160px, 0.75fr) auto auto;
        align-items: center;
        gap: 14px;

        padding: 16px;

        border: 1px solid var(--mkt-border);
        border-radius: 18px;

        background:
            radial-gradient(circle at 96% 8%, rgba(193, 151, 113, 0.12), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        transition: 160ms ease;
    }

    .sl-marketing-page .sl-promo-row:hover {
        transform: translateY(-2px);
        border-color: var(--mkt-tan);
        box-shadow: var(--mkt-shadow-card);
    }

    .sl-marketing-page .sl-promo-icon {
        display: grid;
        width: 46px;
        height: 46px;
        place-items: center;

        border-radius: 16px;

        background: var(--mkt-maroon);
        color: #FFFFFF;

        font-size: 16px;
        font-weight: 950;
    }

    .sl-marketing-page .sl-promo-main strong {
        display: block;

        color: var(--mkt-text);

        font-size: 13px;
        font-weight: 950;
        letter-spacing: -0.025em;
    }

    .sl-marketing-page .sl-promo-main span {
        display: block;
        margin-top: 4px;

        color: var(--mkt-muted);

        font-size: 11px;
        line-height: 1.5;
    }

    .sl-marketing-page .sl-promo-row small {
        display: block;

        color: var(--mkt-muted);

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sl-marketing-page .sl-promo-row div > strong {
        display: block;
        margin-top: 4px;

        color: var(--mkt-maroon);

        font-size: 13px;
        font-weight: 950;
    }

    .sl-marketing-page .sl-progress {
        display: block;
        width: 100%;
        height: 9px;
        margin-top: 7px;
        overflow: hidden;

        border-radius: 999px;

        background: #E7D9CB;
    }

    .sl-marketing-page .sl-progress i {
        display: block;
        height: 100%;

        border-radius: inherit;

        background: linear-gradient(90deg, var(--mkt-maroon), var(--mkt-tan));
    }

    .sl-marketing-page .sl-icon-btn {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;

        border: 1px solid var(--mkt-border);
        border-radius: 12px;

        background: var(--mkt-card);
        color: var(--mkt-maroon);

        font-size: 12px;
        font-weight: 950;
        cursor: pointer;

        transition: 160ms ease;
    }

    .sl-marketing-page .sl-icon-btn:hover,
    .sl-marketing-modal .sl-icon-btn:hover {
        background: var(--mkt-maroon);
        border-color: var(--mkt-maroon);
        color: #FFFFFF;
    }

    .sl-marketing-page .sl-campaign-banner {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 22px;

        padding: 34px 38px;

        background:
            radial-gradient(circle at 92% 14%, rgba(255, 253, 249, 0.18), transparent 30%),
            linear-gradient(135deg, var(--mkt-maroon) 0%, var(--mkt-maroon-2) 58%, var(--mkt-maroon-dark) 100%) !important;

        color: #FFFFFF !important;
    }

    .sl-marketing-page .sl-campaign-banner .sl-eyebrow {
        color: #F3D8CC !important;
    }

    .sl-marketing-page .sl-campaign-banner h2 {
        margin-top: 10px;

        color: #FFFFFF !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 72px);
        font-weight: 400;
        line-height: 0.92;
        letter-spacing: -0.055em;
    }

    .sl-marketing-page .sl-campaign-banner p {
        max-width: 620px;
        margin-top: 13px;

        color: rgba(255, 255, 255, 0.78) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .sl-marketing-page .sl-campaign-banner div div {
        display: inline-flex;
        align-items: center;
        gap: 10px;

        margin-top: 18px;
        padding: 10px 12px;

        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 999px;

        background: rgba(255, 255, 255, 0.09);
    }

    .sl-marketing-page .sl-campaign-banner div div span {
        color: rgba(255, 255, 255, 0.64);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sl-marketing-page .sl-campaign-banner div div strong {
        color: #FFFFFF;

        font-size: 11px;
        font-weight: 950;
    }

    .sl-marketing-page .sl-card-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .sl-marketing-page .sl-campaign-card {
        display: grid;
        gap: 13px;

        padding: 20px;

        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.16), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%) !important;

        transition: 160ms ease;
    }

    .sl-marketing-page .sl-campaign-card:hover {
        transform: translateY(-3px);
        border-color: var(--mkt-tan) !important;
        box-shadow: var(--mkt-shadow-card) !important;
    }

    .sl-marketing-page .sl-campaign-date {
        display: inline-flex;
        width: max-content;
        min-height: 28px;
        align-items: center;

        padding: 0 10px;

        border-radius: 999px;

        background: #F3E4DE;
        color: var(--mkt-maroon);

        font-size: 10px;
        font-weight: 900;
    }

    .sl-marketing-page .sl-campaign-card h3 {
        margin: 0;

        color: var(--mkt-text);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 31px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-marketing-page .sl-campaign-card p {
        margin: 0;

        color: var(--mkt-muted);

        font-size: 12px;
        line-height: 1.6;
    }

    .sl-marketing-page .sl-campaign-card small {
        display: block;

        color: var(--mkt-brown);

        font-size: 11px;
        font-weight: 900;
    }

    .sl-marketing-modal .sl-modal-dialog {
        overflow: hidden;

        border: 1px solid var(--mkt-border) !important;
        border-radius: 24px !important;

        background: var(--mkt-card) !important;
        color: var(--mkt-text) !important;

        box-shadow: 0 24px 70px rgba(86, 28, 23, 0.22);
    }

    .sl-marketing-modal .sl-modal-dialog > header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding: 20px 22px;

        border-bottom: 1px solid var(--mkt-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .sl-marketing-modal .sl-modal-dialog h2 {
        margin-top: 7px;

        color: var(--mkt-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 34px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-marketing-modal .sl-icon-btn {
        display: grid;
        width: 36px;
        height: 36px;
        place-items: center;

        border: 1px solid var(--mkt-border);
        border-radius: 12px;

        background: var(--mkt-bg-soft);
        color: var(--mkt-maroon);

        font-size: 18px;
        font-weight: 900;
        cursor: pointer;
    }

    .sl-marketing-modal .sl-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;

        padding: 22px;
    }

    .sl-marketing-modal .sl-span-2 {
        grid-column: 1 / -1;
    }

    .sl-marketing-modal .sl-field {
        display: grid;
        gap: 7px;
    }

    .sl-marketing-modal .sl-field > span {
        color: var(--mkt-muted) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sl-marketing-modal .sl-field input,
    .sl-marketing-modal .sl-field select {
        width: 100%;
        min-height: 43px;
        padding: 0 13px;

        border: 1px solid var(--mkt-border);
        border-radius: 14px;

        background: var(--mkt-bg-soft);
        color: var(--mkt-text);

        font-size: 12px;
        font-weight: 700;
        outline: 0;

        transition: 160ms ease;
    }

    .sl-marketing-modal .sl-field input:focus,
    .sl-marketing-modal .sl-field select:focus {
        border-color: var(--mkt-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .sl-marketing-modal .sl-input-suffix {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 42px;
        align-items: center;

        overflow: hidden;

        border: 1px solid var(--mkt-border);
        border-radius: 14px;

        background: var(--mkt-bg-soft);
    }

    .sl-marketing-modal .sl-input-suffix input {
        border: 0;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
    }

    .sl-marketing-modal .sl-input-suffix i {
        display: grid;
        height: 100%;
        min-height: 43px;
        place-items: center;

        color: var(--mkt-maroon);

        font-size: 12px;
        font-style: normal;
        font-weight: 950;
    }

    .sl-marketing-modal footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;

        padding: 0 22px 22px;

        border-top: 0 !important;
        background: transparent !important;
    }

    @media (max-width: 1180px) {
        .sl-marketing-page .sl-card-grid-3 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sl-marketing-page .sl-promo-row {
            grid-template-columns: auto minmax(0, 1fr) auto;
        }

        .sl-marketing-page .sl-promo-row > div:not(.sl-promo-main) {
            grid-column: 2 / -1;
        }
    }

    @media (max-width: 820px) {
        .sl-marketing-page .sl-page-toolbar,
        .sl-marketing-page .sl-table-toolbar,
        .sl-marketing-page .sl-campaign-banner {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .sl-marketing-page .sl-mini-stats,
        .sl-marketing-page .sl-card-grid-3,
        .sl-marketing-modal .sl-form-grid {
            grid-template-columns: 1fr;
        }

        .sl-marketing-modal .sl-span-2 {
            grid-column: auto;
        }

        .sl-marketing-page .sl-btn,
        .sl-marketing-modal .sl-btn {
            width: 100%;
        }

        .sl-marketing-modal footer {
            flex-direction: column;
        }
    }

    @media (max-width: 620px) {
        .sl-marketing-page .sl-promo-row {
            grid-template-columns: 1fr;
        }

        .sl-marketing-page .sl-promo-row > div:not(.sl-promo-main) {
            grid-column: auto;
        }

        .sl-marketing-page .sl-icon-btn {
            width: 100%;
        }
    }

    html.dark .sl-marketing-page .sl-page-toolbar,
    html.dark .sl-marketing-page .sl-tabs,
    html.dark .sl-marketing-page .sl-mini-stats div,
    html.dark .sl-marketing-page .sl-card,
    html.dark .sl-marketing-page .sl-table-toolbar,
    html.dark .sl-marketing-page .sl-promo-row,
    html.dark .sl-marketing-page .sl-campaign-card,
    html.dark .sl-marketing-modal .sl-modal-dialog,
    html.dark .sl-marketing-modal .sl-modal-dialog > header {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;

        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .sl-marketing-page .sl-page-toolbar h2,
    html.dark .sl-marketing-page .sl-mini-stats strong,
    html.dark .sl-marketing-page .sl-table-toolbar h3,
    html.dark .sl-marketing-page .sl-table td strong,
    html.dark .sl-marketing-page .sl-promo-main strong,
    html.dark .sl-marketing-page .sl-campaign-card h3,
    html.dark .sl-marketing-modal .sl-modal-dialog h2 {
        color: #F5EFE8 !important;
    }

    html.dark .sl-marketing-page .sl-page-toolbar p,
    html.dark .sl-marketing-page .sl-mini-stats span,
    html.dark .sl-marketing-page .sl-table-toolbar p,
    html.dark .sl-marketing-page .sl-table td,
    html.dark .sl-marketing-page .sl-promo-main span,
    html.dark .sl-marketing-page .sl-promo-row small,
    html.dark .sl-marketing-page .sl-campaign-card p,
    html.dark .sl-marketing-page .sl-campaign-card small {
        color: #C8B7AD !important;
    }

    html.dark .sl-marketing-page .sl-eyebrow,
    html.dark .sl-marketing-modal .sl-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .sl-marketing-page .sl-tabs a {
        color: #C8B7AD !important;
    }

    html.dark .sl-marketing-page .sl-tabs a:hover,
    html.dark .sl-marketing-page .sl-tabs a.is-active {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-marketing-page .sl-btn-primary,
    html.dark .sl-marketing-modal .sl-btn-primary {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .sl-marketing-page .sl-btn-white,
    html.dark .sl-marketing-page .sl-btn-ghost,
    html.dark .sl-marketing-page .sl-btn-soft,
    html.dark .sl-marketing-page .sl-icon-btn,
    html.dark .sl-marketing-modal .sl-btn-ghost,
    html.dark .sl-marketing-modal .sl-icon-btn,
    html.dark .sl-marketing-modal .sl-field input,
    html.dark .sl-marketing-modal .sl-field select,
    html.dark .sl-marketing-modal .sl-input-suffix {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-marketing-page .sl-table thead,
    html.dark .sl-marketing-page .sl-code,
    html.dark .sl-marketing-page .sl-progress {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-marketing-page .sl-table th,
    html.dark .sl-marketing-page .sl-table td {
        border-color: #3B2E27 !important;
    }

    html.dark .sl-marketing-page .sl-table tbody tr:hover td {
        background: #2D1414 !important;
    }
</style>

<div class="sl-page sl-marketing-page">
    <div class="sl-page-toolbar">
        <div>
            <span class="sl-eyebrow">
                Growth Tools
            </span>

            <h2>
                Marketing Center
            </h2>

            <p>
                Manage discounts, vouchers, and campaign participation without hurting your store margins.
            </p>
        </div>

        <button
            type="button"
            class="sl-btn sl-btn-primary"
            data-modal-open="campaignModal"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 5v14M5 12h14"/>
            </svg>

            Create Campaign
        </button>
    </div>

    <nav class="sl-tabs" aria-label="Marketing tabs">
        @foreach($tabs as $key => $label)
            <a
                href="{{ route('seller.marketing', ['tab' => $key]) }}"
                class="{{ $currentTab === $key ? 'is-active' : '' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </nav>

    @if ($currentTab === 'vouchers')
        <section class="sl-mini-stats" aria-label="Voucher summary">
            <div>
                <span>Active Vouchers</span>
                <strong>4</strong>
            </div>

            <div>
                <span>Claims</span>
                <strong>1,284</strong>
            </div>

            <div>
                <span>Uses</span>
                <strong>648</strong>
            </div>

            <div>
                <span>Voucher Sales</span>
                <strong>₱92,450</strong>
            </div>
        </section>

        <section class="sl-card">
            <div class="sl-table-toolbar">
                <div>
                    <h3>
                        Store Vouchers
                    </h3>

                    <p>
                        Create buyer incentives with controlled usage limits and date ranges.
                    </p>
                </div>

                <button
                    type="button"
                    class="sl-btn sl-btn-primary sl-btn-sm"
                    data-modal-open="campaignModal"
                >
                    Create Voucher
                </button>
            </div>

            <div class="sl-table-wrap">
                <table class="sl-table">
                    <thead>
                        <tr>
                            <th>Voucher</th>
                            <th>Benefit</th>
                            <th>Minimum Spend</th>
                            <th>Usage</th>
                            <th>Validity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($vouchers as $voucher)
                            <tr>
                                <td>
                                    <strong class="sl-code">
                                        {{ $voucher['code'] }}
                                    </strong>
                                </td>

                                <td>{{ $voucher['benefit'] }}</td>
                                <td>{{ $voucher['minimum'] }}</td>
                                <td>{{ $voucher['usage'] }}</td>
                                <td>{{ $voucher['validity'] }}</td>

                                <td>
                                    <span class="sl-status {{ $voucher['status'] === 'Active' ? 'is-success' : 'is-neutral' }}">
                                        {{ $voucher['status'] }}
                                    </span>
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="sl-btn sl-btn-ghost sl-btn-sm"
                                        data-demo-action="Voucher editor opened."
                                    >
                                        Manage
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @elseif ($currentTab === 'promotions')
        <section class="sl-card sl-campaign-banner">
            <div>
                <span class="sl-eyebrow">
                    Marketplace Campaign
                </span>

                <h2>
                    9.9 Local Finds Festival
                </h2>

                <p>
                    Feature eligible products in LIKHAE’s September discovery campaign and increase store visibility.
                </p>

                <div>
                    <span>Registration closes</span>
                    <strong>September 6, 2026</strong>
                </div>
            </div>

            <button
                type="button"
                class="sl-btn sl-btn-white"
                data-demo-action="Campaign registration opened."
            >
                Join Campaign
            </button>
        </section>

        <div class="sl-card-grid-3">
            @foreach ($marketplaceCampaigns as $campaign)
                <article class="sl-card sl-campaign-card">
                    <span class="sl-campaign-date">
                        {{ $campaign['date'] }}
                    </span>

                    <h3>
                        {{ $campaign['title'] }}
                    </h3>

                    <p>
                        {{ $campaign['description'] }}
                    </p>

                    <small>
                        {{ $campaign['eligibility'] }}
                    </small>

                    <button
                        type="button"
                        class="sl-btn sl-btn-soft sl-btn-block"
                        data-demo-action="Campaign details opened."
                    >
                        View Requirements
                    </button>
                </article>
            @endforeach
        </div>
    @else
        <section class="sl-mini-stats" aria-label="Discount summary">
            <div>
                <span>Active Discounts</span>
                <strong>3</strong>
            </div>

            <div>
                <span>Discounted Products</span>
                <strong>18</strong>
            </div>

            <div>
                <span>Sales Generated</span>
                <strong>₱68,240</strong>
            </div>

            <div>
                <span>Avg. Conversion</span>
                <strong>8.4%</strong>
            </div>
        </section>

        <section class="sl-card">
            <div class="sl-table-toolbar">
                <div>
                    <h3>
                        Product Discounts
                    </h3>

                    <p>
                        Scheduled and active price promotions for selected listings.
                    </p>
                </div>

                <button
                    type="button"
                    class="sl-btn sl-btn-primary sl-btn-sm"
                    data-modal-open="campaignModal"
                >
                    Create Discount
                </button>
            </div>

            <div class="sl-promo-list">
                @foreach ($discounts as $discount)
                    <article class="sl-promo-row">
                        <span class="sl-promo-icon">
                            %
                        </span>

                        <div class="sl-promo-main">
                            <strong>
                                {{ $discount['title'] }}
                            </strong>

                            <span>
                                {{ $discount['products'] }} · {{ $discount['date'] }}
                            </span>
                        </div>

                        <div>
                            <small>Benefit</small>

                            <strong>
                                {{ $discount['benefit'] }}
                            </strong>
                        </div>

                        <div>
                            <small>Performance</small>

                            <span class="sl-progress">
                                <i style="width: {{ $discount['progress'] }}%"></i>
                            </span>
                        </div>

                        <span class="sl-status {{ $discount['status'] === 'Active' ? 'is-success' : 'is-neutral' }}">
                            {{ $discount['status'] }}
                        </span>

                        <button
                            type="button"
                            class="sl-icon-btn"
                            data-demo-action="Discount options opened."
                            aria-label="Discount actions"
                        >
                            •••
                        </button>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>

<div class="sl-modal sl-marketing-modal" data-modal="campaignModal" hidden>
    <div class="sl-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="campaignTitle">
        <header>
            <div>
                <span class="sl-eyebrow">
                    Marketing Tool
                </span>

                <h2 id="campaignTitle">
                    Create Campaign
                </h2>
            </div>

            <button
                type="button"
                class="sl-icon-btn"
                data-modal-close
                aria-label="Close"
            >
                ×
            </button>
        </header>

        <form data-demo-form data-success="Marketing campaign created.">
            <div class="sl-form-grid">
                <label class="sl-field sl-span-2">
                    <span>Campaign name</span>

                    <input
                        type="text"
                        placeholder="e.g. September Product Sale"
                        required
                    >
                </label>

                <label class="sl-field">
                    <span>Campaign type</span>

                    <select>
                        <option>Product discount</option>
                        <option>Store voucher</option>
                        <option>Marketplace promotion</option>
                    </select>
                </label>

                <label class="sl-field">
                    <span>Discount value</span>

                    <div class="sl-input-suffix">
                        <input
                            type="number"
                            min="1"
                            max="100"
                            value="10"
                        >

                        <i>%</i>
                    </div>
                </label>

                <label class="sl-field">
                    <span>Start date</span>

                    <input type="date" required>
                </label>

                <label class="sl-field">
                    <span>End date</span>

                    <input type="date" required>
                </label>

                <label class="sl-field sl-span-2">
                    <span>Products</span>

                    <select>
                        <option>All active products</option>
                        <option>Select specific products</option>
                    </select>
                </label>
            </div>

            <footer>
                <button
                    type="button"
                    class="sl-btn sl-btn-ghost"
                    data-modal-close
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="sl-btn sl-btn-primary"
                >
                    Create Campaign
                </button>
            </footer>
        </form>
    </div>
</div>
@endsection