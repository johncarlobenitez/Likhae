@extends('layouts.seller')

@section('title', 'Finance')
@section('active', 'finance')
@section('subtitle', 'Monitor revenue, platform fees, balances, and payouts.')

@php
    $tabs = [
        'sales' => 'Sales Overview',
        'transactions' => 'Transactions',
        'payouts' => 'Payouts',
    ];

    $requestedTab = $tab ?? request('tab', 'sales');

    $currentTab = array_key_exists($requestedTab, $tabs)
        ? $requestedTab
        : 'sales';

    $metrics = [
        ['label' => 'Gross Sales', 'value' => '₱184,650', 'change' => '6.4%', 'direction' => 'up', 'icon' => 'sales'],
        ['label' => 'Commission', 'value' => '₱12,926', 'change' => '7.0% rate', 'direction' => 'down', 'icon' => 'finance'],
        ['label' => 'Net Earnings', 'value' => '₱171,724', 'change' => '5.8%', 'direction' => 'up', 'icon' => 'revenue'],
        ['label' => 'Pending Balance', 'value' => '₱38,450', 'change' => 'Next payout Sep 8', 'direction' => 'up', 'icon' => 'finance'],
    ];

    $metricIcons = [
        'sales' => '<path d="M3 7h18v13H3z"/><path d="M3 10h18"/><path d="M7 16h4"/>',
        'finance' => '<path d="M4 6h16v12H4z"/><path d="M7 10h4"/><path d="M7 14h2"/><path d="M15 10h2"/><path d="M15 14h2"/>',
        'revenue' => '<path d="M12 2v20"/><path d="M17 6H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
    ];

    $fees = [
        ['label' => 'Marketplace commission', 'amount' => '₱12,926', 'tone' => 'maroon'],
        ['label' => 'Payment processing', 'amount' => '₱3,124', 'tone' => 'tan'],
        ['label' => 'Seller-funded vouchers', 'amount' => '₱2,850', 'tone' => 'soft'],
        ['label' => 'Adjustments', 'amount' => '₱420', 'tone' => 'light'],
    ];

    $payouts = [
        ['id' => 'PAY-0918', 'period' => 'Aug 25–31', 'bank' => 'BDO •••• 4721', 'amount' => '₱42,180', 'date' => 'Sep 01, 2026', 'status' => 'Paid'],
        ['id' => 'PAY-0917', 'period' => 'Aug 18–24', 'bank' => 'BDO •••• 4721', 'amount' => '₱36,520', 'date' => 'Aug 25, 2026', 'status' => 'Paid'],
        ['id' => 'PAY-0916', 'period' => 'Aug 11–17', 'bank' => 'BDO •••• 4721', 'amount' => '₱31,875', 'date' => 'Aug 18, 2026', 'status' => 'Paid'],
    ];

    $transactions = [
        ['order' => '#10005', 'amount' => 1980, 'commission' => 138.60, 'net' => 1841.40, 'date' => 'Sep 04, 2026', 'status' => 'Completed'],
        ['order' => '#10004', 'amount' => 3490, 'commission' => 244.30, 'net' => 3245.70, 'date' => 'Sep 03, 2026', 'status' => 'Pending'],
        ['order' => '#10003', 'amount' => 1490, 'commission' => 104.30, 'net' => 1385.70, 'date' => 'Sep 03, 2026', 'status' => 'Pending'],
        ['order' => '#10002', 'amount' => 5580, 'commission' => 390.60, 'net' => 5189.40, 'date' => 'Sep 02, 2026', 'status' => 'Completed'],
        ['order' => '#10001', 'amount' => 12990, 'commission' => 909.30, 'net' => 12080.70, 'date' => 'Sep 01, 2026', 'status' => 'Completed'],
    ];
@endphp

@section('content')
<style>
    :root {
        --fin-bg: #FBF7F2;
        --fin-bg-soft: #F6EFE7;
        --fin-bg-alt: #EFE7DE;
        --fin-card: #FFFDF9;

        --fin-border: #EADCCC;
        --fin-border-strong: #DBCEC1;

        --fin-maroon: #561C17;
        --fin-maroon-2: #642920;
        --fin-maroon-dark: #3E130F;

        --fin-text: #3B211B;
        --fin-text-dark: #1C160F;
        --fin-brown: #6C4936;
        --fin-muted: #987865;
        --fin-muted-2: #A99386;

        --fin-tan: #C19771;

        --fin-success: #256F4A;
        --fin-success-soft: #EAF7EF;

        --fin-warning: #9A5B11;
        --fin-warning-soft: #FFF6DE;

        --fin-danger: #B42318;
        --fin-danger-soft: #FCEBE9;

        --fin-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --fin-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .sl-finance-page {
        color: var(--fin-text);

        --sl-blue: var(--fin-maroon);
        --sl-blue-dark: var(--fin-maroon-dark);
        --sl-blue-soft: #F3E4DE;
        --sl-indigo: var(--fin-tan);
    }

    .sl-finance-page .sl-page-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;

        padding: 32px 36px;

        border: 1px solid var(--fin-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--fin-shadow-soft);
    }

    .sl-finance-page .sl-eyebrow {
        color: var(--fin-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .sl-finance-page .sl-page-toolbar h2 {
        margin-top: 10px;

        color: var(--fin-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .sl-finance-page .sl-page-toolbar p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--fin-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .sl-finance-page .sl-toolbar-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .sl-finance-page .sl-btn {
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

    .sl-finance-page .sl-btn:hover {
        transform: translateY(-1px);
    }

    .sl-finance-page .sl-btn-primary {
        background: var(--fin-maroon) !important;
        border-color: var(--fin-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .sl-finance-page .sl-btn-primary:hover {
        background: var(--fin-maroon-dark) !important;
        border-color: var(--fin-maroon-dark) !important;
    }

    .sl-finance-page .sl-btn-ghost,
    .sl-finance-page .sl-btn-soft {
        background: var(--fin-card) !important;
        border-color: var(--fin-tan) !important;
        color: var(--fin-maroon) !important;
    }

    .sl-finance-page .sl-btn-ghost:hover,
    .sl-finance-page .sl-btn-soft:hover {
        background: #F3E4DE !important;
        border-color: var(--fin-maroon) !important;
    }

    .sl-finance-page .sl-btn-sm {
        min-height: 34px;
        padding-inline: 12px;
        border-radius: 11px;

        font-size: 11px;
    }

    .sl-finance-page .sl-finance-metrics {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 16px !important;
        align-items: stretch !important;
        justify-items: stretch !important;

        width: 100% !important;
        margin: 20px 0 18px !important;
        padding: 0 !important;
    }

    .sl-finance-page .sl-finance-metric-card {
        display: flex !important;
        min-height: 138px !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        gap: 16px !important;

        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;

        margin: 0 !important;
        padding: 18px !important;

        border: 1px solid var(--fin-border) !important;
        border-radius: 22px !important;

        background:
            radial-gradient(circle at 94% 6%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%) !important;

        color: var(--fin-text) !important;
        box-shadow: var(--fin-shadow-soft) !important;

        transform: none !important;
        translate: none !important;
        transition: 160ms ease !important;
    }

    .sl-finance-page .sl-finance-metric-card:hover {
        transform: translateY(-2px) !important;
        border-color: var(--fin-tan) !important;
        box-shadow: var(--fin-shadow-card) !important;
    }

    .sl-finance-page .sl-finance-metric-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .sl-finance-page .sl-finance-metric-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;

        border: 1px solid #E6C7BE;
        border-radius: 13px;

        background: #F3E4DE;
        color: var(--fin-maroon);
    }

    .sl-finance-page .sl-finance-metric-icon svg {
        width: 18px;
        height: 18px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sl-finance-page .sl-finance-metric-change {
        display: inline-flex;
        min-height: 24px;
        align-items: center;
        justify-content: center;

        max-width: 130px;
        padding: 0 8px;

        border: 1px solid #E6C7BE;
        border-radius: 999px;

        background: #F3E4DE;
        color: var(--fin-maroon);

        font-size: 10px;
        font-weight: 900;
        line-height: 1;
        white-space: nowrap;
    }

    .sl-finance-page .sl-finance-metric-change.is-down {
        border-color: #EAD39A;
        background: var(--fin-warning-soft);
        color: var(--fin-warning);
    }

    .sl-finance-page .sl-finance-metric-body {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .sl-finance-page .sl-finance-metric-label {
        margin: 0;

        color: var(--fin-muted);

        font-size: 10px;
        font-weight: 900;
        line-height: 1.2;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sl-finance-page .sl-finance-metric-value {
        display: block;
        margin: 0;

        color: var(--fin-text-dark);

        font-size: 26px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-finance-page .sl-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 6px;

        border: 1px solid var(--fin-border);
        border-radius: 16px;

        background: var(--fin-card);
        box-shadow: var(--fin-shadow-soft);
    }

    .sl-finance-page .sl-tabs a {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;

        padding: 0 16px;

        border-radius: 12px;

        color: var(--fin-brown);
        text-decoration: none;

        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;

        transition: 160ms ease;
    }

    .sl-finance-page .sl-tabs a:hover {
        background: var(--fin-bg-soft);
        color: var(--fin-maroon);
    }

    .sl-finance-page .sl-tabs a.is-active {
        background: var(--fin-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .sl-finance-page .sl-dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(340px, 0.65fr);
        gap: 18px;
        align-items: start;
    }

    .sl-finance-page .sl-card {
        overflow: hidden;

        border: 1px solid var(--fin-border) !important;
        border-radius: 24px !important;

        background: var(--fin-card) !important;
        color: var(--fin-text) !important;

        box-shadow: var(--fin-shadow-soft) !important;
    }

    .sl-finance-page .sl-card-head,
    .sl-finance-page .sl-table-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding: 20px 22px;

        border-bottom: 1px solid var(--fin-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .sl-finance-page .sl-card-head h2,
    .sl-finance-page .sl-table-toolbar h3 {
        margin: 7px 0 0;

        color: var(--fin-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 32px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-finance-page .sl-card-head p,
    .sl-finance-page .sl-table-toolbar p {
        margin: 8px 0 0;

        color: var(--fin-muted) !important;

        font-size: 12px;
        line-height: 1.65;
    }

    .sl-finance-page .sl-select,
    .sl-finance-page .sl-input {
        min-height: 42px;
        padding: 0 13px;

        border: 1px solid var(--fin-border);
        border-radius: 14px;

        background: var(--fin-bg-soft);
        color: var(--fin-text);

        font-size: 12px;
        font-weight: 800;
        outline: none;

        transition: 160ms ease;
    }

    .sl-finance-page .sl-select:focus,
    .sl-finance-page .sl-input:focus {
        border-color: var(--fin-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .sl-finance-page .sl-chart-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;

        padding: 20px 22px 0;
    }

    .sl-finance-page .sl-chart-summary span {
        color: var(--fin-muted);
        font-size: 11px;
        font-weight: 800;
    }

    .sl-finance-page .sl-chart-summary strong {
        display: block;
        margin-top: 5px;

        color: var(--fin-maroon);

        font-size: 34px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.05em;
    }

    .sl-finance-page .sl-positive {
        display: inline-flex;
        min-height: 28px;
        align-items: center;

        padding: 0 10px;

        border: 1px solid #E6C7BE;
        border-radius: 999px;

        background: #F3E4DE;
        color: var(--fin-maroon) !important;

        font-size: 10px !important;
        font-weight: 900 !important;
    }

    .sl-finance-page .sl-line-chart {
        margin: 20px 22px 22px;
        padding: 18px;

        border: 1px solid var(--fin-border);
        border-radius: 20px;

        background:
            linear-gradient(180deg, rgba(255, 253, 249, 0.92), rgba(246, 239, 231, 0.74));
    }

    .sl-finance-page .sl-line-chart svg {
        display: block;
        width: 100%;
        height: 240px;
    }

    .sl-finance-page .sl-grid-line {
        fill: none;
        stroke: rgba(86, 28, 23, 0.10);
        stroke-width: 1;
    }

    .sl-finance-page .sl-area {
        fill: rgba(86, 28, 23, 0.13);
    }

    .sl-finance-page .sl-line {
        fill: none;
        stroke: var(--fin-maroon);
        stroke-width: 5;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sl-finance-page .sl-line-chart-labels {
        display: flex;
        justify-content: space-between;
        gap: 10px;

        margin-top: 10px;

        color: var(--fin-muted);

        font-size: 10px;
        font-weight: 800;
    }

    .sl-finance-page .sl-fee-list {
        display: grid;
        gap: 11px;

        padding: 20px 22px;
    }

    .sl-finance-page .sl-fee-list div {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;

        padding: 13px;

        border: 1px solid var(--fin-border);
        border-radius: 15px;

        background: var(--fin-bg-soft);
    }

    .sl-finance-page .sl-fee-list span {
        display: inline-flex;
        align-items: center;
        gap: 9px;

        color: var(--fin-brown);

        font-size: 11px;
        font-weight: 850;
    }

    .sl-finance-page .sl-fee-list i {
        width: 11px;
        height: 11px;

        border-radius: 999px;

        background: var(--fin-maroon);
    }

    .sl-finance-page .sl-fee-list i.is-tan {
        background: var(--fin-tan);
    }

    .sl-finance-page .sl-fee-list i.is-soft {
        background: #D9B99D;
    }

    .sl-finance-page .sl-fee-list i.is-light {
        background: #EFE1D5;
    }

    .sl-finance-page .sl-fee-list strong {
        color: var(--fin-text);
        font-size: 12px;
        font-weight: 950;
    }

    .sl-finance-page .sl-fee-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;

        margin: 0 22px 22px;
        padding: 17px;

        border: 1px solid #E6C7BE;
        border-radius: 18px;

        background: #F3E4DE;
    }

    .sl-finance-page .sl-fee-total span {
        color: var(--fin-maroon);

        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .sl-finance-page .sl-fee-total strong {
        color: var(--fin-maroon);

        font-size: 20px;
        font-weight: 950;
        letter-spacing: -0.04em;
    }

    .sl-finance-page .sl-payout-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;

        padding: 30px 34px;

        background:
            radial-gradient(circle at 92% 12%, rgba(193, 151, 113, 0.24), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 100%) !important;
    }

    .sl-finance-page .sl-payout-hero h2 {
        margin-top: 8px;

        color: var(--fin-maroon) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(46px, 5vw, 72px);
        font-weight: 400;
        line-height: 0.92;
        letter-spacing: -0.055em;
    }

    .sl-finance-page .sl-payout-hero p {
        margin-top: 12px;

        color: var(--fin-muted) !important;

        font-size: 13px;
        line-height: 1.6;
    }

    .sl-finance-page .sl-status {
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

    .sl-finance-page .sl-status.is-success {
        background: var(--fin-success-soft);
        border-color: #CFE8DA;
        color: var(--fin-success);
    }

    .sl-finance-page .sl-status.is-warning {
        background: var(--fin-warning-soft);
        border-color: #EAD39A;
        color: var(--fin-warning);
    }

    .sl-finance-page .sl-status.is-info {
        background: #F3E4DE;
        border-color: #E6C7BE;
        color: var(--fin-maroon);
    }

    .sl-finance-page .sl-table-wrap {
        overflow-x: auto;
    }

    .sl-finance-page .sl-table {
        width: 100%;
        min-width: 840px;
        border-collapse: collapse;
    }

    .sl-finance-page .sl-table thead {
        background: var(--fin-bg-soft);
    }

    .sl-finance-page .sl-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--fin-border);

        color: var(--fin-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .sl-finance-page .sl-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--fin-brown);

        font-size: 12px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .sl-finance-page .sl-table tbody tr:hover td {
        background: var(--fin-bg-soft);
    }

    .sl-finance-page .sl-table td strong {
        color: var(--fin-text);
        font-weight: 950;
    }

    .sl-finance-page .sl-search-input {
        position: relative;
        flex: 1;
        max-width: 500px;
    }

    .sl-finance-page .sl-search-input svg {
        position: absolute;
        top: 50%;
        left: 14px;

        width: 16px;
        height: 16px;

        color: var(--fin-muted-2);

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;

        transform: translateY(-50%);
    }

    .sl-finance-page .sl-search-input input {
        width: 100%;
        min-height: 42px;
        padding: 0 14px 0 42px;

        border: 1px solid var(--fin-border);
        border-radius: 14px;

        background: var(--fin-bg-soft);
        color: var(--fin-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;
    }

    .sl-finance-page .sl-search-input input:focus {
        border-color: var(--fin-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    @media (max-width: 1180px) {
        .sl-finance-page .sl-finance-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .sl-finance-page .sl-dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 820px) {
        .sl-finance-page .sl-page-toolbar,
        .sl-finance-page .sl-card-head,
        .sl-finance-page .sl-table-toolbar,
        .sl-finance-page .sl-chart-summary,
        .sl-finance-page .sl-payout-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .sl-finance-page .sl-page-toolbar {
            padding: 26px 22px;
        }

        .sl-finance-page .sl-finance-metrics {
            grid-template-columns: 1fr !important;
        }

        .sl-finance-page .sl-toolbar-group,
        .sl-finance-page .sl-btn,
        .sl-finance-page .sl-search-input,
        .sl-finance-page .sl-select,
        .sl-finance-page .sl-input {
            width: 100%;
            max-width: none;
        }

        .sl-finance-page .sl-line-chart-labels {
            font-size: 9px;
        }
    }

    html.dark .sl-finance-page .sl-page-toolbar,
    html.dark .sl-finance-page .sl-finance-metric-card,
    html.dark .sl-finance-page .sl-tabs,
    html.dark .sl-finance-page .sl-card,
    html.dark .sl-finance-page .sl-card-head,
    html.dark .sl-finance-page .sl-table-toolbar,
    html.dark .sl-finance-page .sl-line-chart,
    html.dark .sl-finance-page .sl-fee-list div,
    html.dark .sl-finance-page .sl-fee-total,
    html.dark .sl-finance-page .sl-payout-hero {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;

        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .sl-finance-page .sl-page-toolbar h2,
    html.dark .sl-finance-page .sl-finance-metric-value,
    html.dark .sl-finance-page .sl-card-head h2,
    html.dark .sl-finance-page .sl-table-toolbar h3,
    html.dark .sl-finance-page .sl-chart-summary strong,
    html.dark .sl-finance-page .sl-fee-list strong,
    html.dark .sl-finance-page .sl-fee-total strong,
    html.dark .sl-finance-page .sl-payout-hero h2,
    html.dark .sl-finance-page .sl-table td strong {
        color: #F5EFE8 !important;
    }

    html.dark .sl-finance-page .sl-page-toolbar p,
    html.dark .sl-finance-page .sl-finance-metric-label,
    html.dark .sl-finance-page .sl-card-head p,
    html.dark .sl-finance-page .sl-table-toolbar p,
    html.dark .sl-finance-page .sl-chart-summary span,
    html.dark .sl-finance-page .sl-fee-list span,
    html.dark .sl-finance-page .sl-payout-hero p,
    html.dark .sl-finance-page .sl-table td {
        color: #C8B7AD !important;
    }

    html.dark .sl-finance-page .sl-eyebrow,
    html.dark .sl-finance-page .sl-positive,
    html.dark .sl-finance-page .sl-fee-total span {
        color: #EBA99D !important;
    }

    html.dark .sl-finance-page .sl-finance-metric-icon,
    html.dark .sl-finance-page .sl-finance-metric-change {
        background: #2D1414 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .sl-finance-page .sl-tabs a {
        color: #C8B7AD !important;
    }

    html.dark .sl-finance-page .sl-tabs a:hover,
    html.dark .sl-finance-page .sl-tabs a.is-active {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-finance-page .sl-btn-primary {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .sl-finance-page .sl-btn-ghost,
    html.dark .sl-finance-page .sl-btn-soft,
    html.dark .sl-finance-page .sl-select,
    html.dark .sl-finance-page .sl-input,
    html.dark .sl-finance-page .sl-search-input input,
    html.dark .sl-finance-page .sl-table thead {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-finance-page .sl-table th,
    html.dark .sl-finance-page .sl-table td {
        border-color: #3B2E27 !important;
    }

    html.dark .sl-finance-page .sl-table tbody tr:hover td {
        background: #2D1414 !important;
    }
</style>

<div class="sl-page sl-finance-page">
    <div class="sl-page-toolbar">
        <div>
            <span class="sl-eyebrow">
                Financial Management
            </span>

            <h2>
                Finance Overview
            </h2>

            <p>
                Transparent earnings, platform fees, balances, and payout monitoring for your store.
            </p>
        </div>

        <div class="sl-toolbar-group">
            <button
                type="button"
                class="sl-btn sl-btn-ghost"
                data-demo-action="Finance statement downloaded."
            >
                Download Statement
            </button>

            <a href="{{ route('seller.reports') }}" class="sl-btn sl-btn-primary">
                Generate Report
            </a>
        </div>
    </div>

    <section class="sl-finance-metrics" aria-label="Finance summary">
        @foreach ($metrics as $metric)
            <article class="sl-finance-metric-card">
                <div class="sl-finance-metric-top">
                    <span class="sl-finance-metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $metricIcons[$metric['icon']] ?? $metricIcons['sales'] !!}
                        </svg>
                    </span>

                    <span class="sl-finance-metric-change {{ $metric['direction'] === 'down' ? 'is-down' : '' }}">
                        {{ $metric['direction'] === 'down' ? '↓' : '↑' }} {{ $metric['change'] }}
                    </span>
                </div>

                <div class="sl-finance-metric-body">
                    <p class="sl-finance-metric-label">
                        {{ $metric['label'] }}
                    </p>

                    <strong class="sl-finance-metric-value">
                        {{ $metric['value'] }}
                    </strong>
                </div>
            </article>
        @endforeach
    </section>

    <nav class="sl-tabs" aria-label="Finance sections">
        @foreach($tabs as $key => $label)
            <a
                href="{{ route('seller.finance', ['tab' => $key]) }}"
                class="{{ $currentTab === $key ? 'is-active' : '' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </nav>

    @if ($currentTab === 'sales')
        <div class="sl-dashboard-grid">
            <section class="sl-card">
                <header class="sl-card-head">
                    <div>
                        <span class="sl-eyebrow">
                            Net Sales
                        </span>

                        <h2>
                            Earnings Trend
                        </h2>

                        <p>
                            Gross sales minus marketplace commission and approved adjustments.
                        </p>
                    </div>

                    <select class="sl-select sl-select-sm" aria-label="Sales period">
                        <option>Last 30 days</option>
                        <option>This quarter</option>
                    </select>
                </header>

                <div class="sl-chart-summary">
                    <div>
                        <span>Net earnings</span>
                        <strong>₱171,724</strong>
                    </div>

                    <span class="sl-positive">
                        ↑ 5.8% vs last period
                    </span>
                </div>

                <div class="sl-line-chart">
                    <svg viewBox="0 0 700 220" preserveAspectRatio="none" aria-label="Earnings trend chart">
                        <path class="sl-grid-line" d="M0 35H700M0 90H700M0 145H700M0 200H700"/>
                        <path class="sl-area" d="M0 178 C70 162 105 172 150 135 S235 92 290 120 S390 72 450 82 S535 42 590 55 S650 30 700 38 L700 220 L0 220Z"/>
                        <path class="sl-line" d="M0 178 C70 162 105 172 150 135 S235 92 290 120 S390 72 450 82 S535 42 590 55 S650 30 700 38"/>
                    </svg>

                    <div class="sl-line-chart-labels">
                        <span>Aug 06</span>
                        <span>Aug 13</span>
                        <span>Aug 20</span>
                        <span>Aug 27</span>
                        <span>Sep 04</span>
                    </div>
                </div>
            </section>

            <section class="sl-card">
                <header class="sl-card-head">
                    <div>
                        <span class="sl-eyebrow">
                            Deductions
                        </span>

                        <h2>
                            Fee Breakdown
                        </h2>

                        <p>
                            Costs deducted from gross sales before payout.
                        </p>
                    </div>
                </header>

                <div class="sl-fee-list">
                    @foreach($fees as $fee)
                        <div>
                            <span>
                                <i class="{{ $fee['tone'] === 'tan' ? 'is-tan' : ($fee['tone'] === 'soft' ? 'is-soft' : ($fee['tone'] === 'light' ? 'is-light' : '')) }}"></i>
                                {{ $fee['label'] }}
                            </span>

                            <strong>
                                {{ $fee['amount'] }}
                            </strong>
                        </div>
                    @endforeach
                </div>

                <div class="sl-fee-total">
                    <span>Total deductions</span>
                    <strong>₱19,320</strong>
                </div>
            </section>
        </div>
    @elseif ($currentTab === 'payouts')
        <section class="sl-card sl-payout-hero">
            <div>
                <span class="sl-eyebrow">
                    Next Payout
                </span>

                <h2>
                    ₱38,450.00
                </h2>

                <p>
                    Scheduled for September 8, 2026 to BDO •••• 4721.
                </p>
            </div>

            <span class="sl-status is-info">
                Processing
            </span>
        </section>

        <section class="sl-card">
            <div class="sl-table-toolbar">
                <div>
                    <h3>
                        Payout History
                    </h3>

                    <p>
                        Completed and scheduled store settlements.
                    </p>
                </div>
            </div>

            <div class="sl-table-wrap">
                <table class="sl-table">
                    <thead>
                        <tr>
                            <th>Payout ID</th>
                            <th>Period</th>
                            <th>Bank Account</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($payouts as $payout)
                            <tr>
                                <td>
                                    <strong>{{ $payout['id'] }}</strong>
                                </td>

                                <td>{{ $payout['period'] }}</td>
                                <td>{{ $payout['bank'] }}</td>

                                <td>
                                    <strong>{{ $payout['amount'] }}</strong>
                                </td>

                                <td>{{ $payout['date'] }}</td>

                                <td>
                                    <span class="sl-status is-success">
                                        {{ $payout['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @else
        <section class="sl-card">
            <div class="sl-table-toolbar">
                <div class="sl-search-input">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-4-4"/>
                    </svg>

                    <input
                        type="search"
                        placeholder="Search order or transaction"
                    >
                </div>

                <div class="sl-toolbar-group">
                    <input type="date" class="sl-input">

                    <select class="sl-select">
                        <option>All status</option>
                        <option>Completed</option>
                        <option>Pending</option>
                        <option>Refunded</option>
                    </select>
                </div>
            </div>

            <div class="sl-table-wrap">
                <table class="sl-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Commission</th>
                            <th>Net Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>
                                    <strong>{{ $transaction['order'] }}</strong>
                                </td>

                                <td>
                                    ₱{{ number_format($transaction['amount'], 2) }}
                                </td>

                                <td>
                                    −₱{{ number_format($transaction['commission'], 2) }}
                                </td>

                                <td>
                                    <strong>
                                        ₱{{ number_format($transaction['net'], 2) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $transaction['date'] }}
                                </td>

                                <td>
                                    <span class="sl-status {{ $transaction['status'] === 'Completed' ? 'is-success' : 'is-warning' }}">
                                        {{ $transaction['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
@endsection
