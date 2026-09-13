@extends('layouts.seller')

@section('title', 'Dashboard')
@section('active', 'dashboard')
@section('subtitle', 'Your store performance and operational priorities at a glance.')

@php
    $seller = auth()->user();
    $sellerName = data_get($seller, 'name', 'Mariel');

    $dashboardOrders = isset($sellerOrders)
        ? collect($sellerOrders)
        : collect([
            ['id' => 10482, 'buyer' => 'Angela Cruz', 'product' => 'Handwoven Abaca Tote', 'total' => 12990, 'status' => 'To Process', 'status_key' => 'to-process'],
            ['id' => 10481, 'buyer' => 'Marco Reyes', 'product' => 'Mechanical Keyboard', 'total' => 5580, 'status' => 'To Prepare', 'status_key' => 'to-prepare'],
            ['id' => 10480, 'buyer' => 'Sarah Lim', 'product' => 'Artisan Soy Candle Set', 'total' => 1490, 'status' => 'Ready Pickup', 'status_key' => 'ready-pickup'],
            ['id' => 10479, 'buyer' => 'Daniel Tan', 'product' => 'Premium Linen Top', 'total' => 3490, 'status' => 'Shipping', 'status_key' => 'shipping'],
            ['id' => 10478, 'buyer' => 'Patricia Go', 'product' => 'Paper & Loom Notebook', 'total' => 1980, 'status' => 'Completed', 'status_key' => 'completed'],
        ]);

    $metrics = [
        ['label' => "Today's Sales", 'value' => '₱25,000', 'change' => '12.5%', 'direction' => 'up', 'icon' => 'sales'],
        ['label' => 'Total Orders', 'value' => '35', 'change' => '8.2%', 'direction' => 'up', 'icon' => 'orders'],
        ['label' => 'Revenue', 'value' => '₱184,650', 'change' => '6.4%', 'direction' => 'up', 'icon' => 'revenue'],
        ['label' => 'Products Sold', 'value' => '82', 'change' => '4.1%', 'direction' => 'up', 'icon' => 'products'],
        ['label' => 'Pending Shipment', 'value' => '8', 'change' => '2 due soon', 'direction' => 'down', 'icon' => 'shipping'],
    ];

    $metricIcons = [
        'sales' => '<path d="M3 7h18v13H3z"/><path d="M3 10h18"/><path d="M7 16h4"/>',
        'orders' => '<path d="M6 3h12l2 4v14H4V7z"/><path d="M4 7h16"/><path d="M9 12h6"/><path d="M9 16h4"/>',
        'revenue' => '<path d="M12 2v20"/><path d="M17 6H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
        'products' => '<path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/><path d="M4 7.5l8 4.5 8-4.5"/><path d="M12 12v9"/>',
        'shipping' => '<path d="M3 6h11v11H3z"/><path d="M14 10h4l3 3v4h-7z"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/>',
    ];

    $statusItems = [
        ['label' => 'To Process', 'count' => 5, 'status' => 'to-process'],
        ['label' => 'To Prepare', 'count' => 8, 'status' => 'to-prepare'],
        ['label' => 'Ready Pickup', 'count' => 8, 'status' => 'ready-pickup'],
        ['label' => 'In Transit', 'count' => 14, 'status' => 'shipping'],
    ];

    $inventoryAlerts = [
        ['tone' => 'critical', 'icon' => '!', 'label' => 'LOW STOCK', 'product' => 'Mechanical Keyboard', 'detail' => '3 remaining', 'action' => 'Update'],
        ['tone' => 'warning', 'icon' => '!', 'label' => 'LOW STOCK', 'product' => 'Gaming Mouse', 'detail' => '5 remaining', 'action' => 'Update'],
        ['tone' => 'neutral', 'icon' => '0', 'label' => 'OUT OF STOCK', 'product' => '7-in-1 USB-C Hub', 'detail' => 'Listing archived', 'action' => 'Review'],
    ];

    $workflowSteps = [
        ['1', 'New Order', 'Review details'],
        ['2', 'Prepare', 'Pack and print waybill'],
        ['3', 'Assign Courier', 'Choose logistics'],
        ['4', 'Pickup', 'Hand over parcel'],
        ['5', 'In Transit', 'Monitor shipment'],
        ['6', 'Delivered', 'Receive confirmation'],
    ];
@endphp

@section('content')
<style>
    :root {
        --seller-bg: #FBF7F2;
        --seller-bg-soft: #F6EFE7;
        --seller-bg-alt: #EFE7DE;
        --seller-card: #FFFDF9;

        --seller-border: #EADCCC;
        --seller-border-strong: #DBCEC1;

        --seller-maroon: #561C17;
        --seller-maroon-2: #642920;
        --seller-maroon-dark: #3E130F;

        --seller-text: #3B211B;
        --seller-text-dark: #1C160F;
        --seller-brown: #6C4936;
        --seller-muted: #987865;
        --seller-muted-2: #A99386;

        --seller-tan: #C19771;

        --seller-success: #256F4A;
        --seller-success-soft: #EAF7EF;

        --seller-warning: #9A5B11;
        --seller-warning-soft: #FFF6DE;

        --seller-danger: #B42318;
        --seller-danger-soft: #FCEBE9;

        --seller-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --seller-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .sl-seller-dashboard {
        color: var(--seller-text);

        --sl-blue: var(--seller-maroon);
        --sl-blue-dark: var(--seller-maroon-dark);
        --sl-blue-soft: #F3E4DE;
        --sl-indigo: var(--seller-tan);
    }

    .sl-seller-dashboard .sl-welcome-panel {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;

        padding: 34px 38px;

        border: 1px solid var(--seller-border);
        border-radius: 30px;

        background:
            radial-gradient(circle at 92% 12%, rgba(193, 151, 113, 0.28), transparent 30%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.08), transparent 32%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 56%, #EFE7DE 100%) !important;

        color: var(--seller-text);
        box-shadow: var(--seller-shadow-soft);
    }

    .sl-seller-dashboard .sl-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        color: var(--seller-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .sl-seller-dashboard .sl-eyebrow::before {
        width: 24px;
        height: 1px;
        background: var(--seller-maroon);
        content: "";
    }

    .sl-seller-dashboard .sl-card-head .sl-eyebrow::before {
        display: none;
    }

    .sl-seller-dashboard .sl-welcome-panel h2 {
        margin: 10px 0 0;

        color: var(--seller-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 70px);
        font-weight: 400;
        line-height: 0.92;
        letter-spacing: -0.055em;
    }

    .sl-seller-dashboard .sl-welcome-panel p {
        max-width: 650px;
        margin: 14px 0 0;

        color: var(--seller-muted) !important;

        font-size: 13px;
        line-height: 1.75;
    }

    .sl-seller-dashboard .sl-welcome-panel p strong {
        color: var(--seller-maroon);
        font-weight: 950;
    }

    .sl-seller-dashboard .sl-welcome-actions,
    .sl-seller-dashboard .sl-inline-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .sl-seller-dashboard .sl-btn {
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

        transition:
            transform 160ms ease,
            background 160ms ease,
            border-color 160ms ease,
            color 160ms ease,
            box-shadow 160ms ease;
    }

    .sl-seller-dashboard .sl-btn:hover {
        transform: translateY(-1px);
    }

    .sl-seller-dashboard .sl-btn svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sl-seller-dashboard .sl-btn-white {
        background: var(--seller-maroon) !important;
        border-color: var(--seller-maroon) !important;
        color: #FFFFFF !important;
        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .sl-seller-dashboard .sl-btn-white:hover {
        background: var(--seller-maroon-dark) !important;
        border-color: var(--seller-maroon-dark) !important;
    }

    .sl-seller-dashboard .sl-btn-blue-soft,
    .sl-seller-dashboard .sl-btn-ghost {
        background: var(--seller-card) !important;
        border-color: var(--seller-tan) !important;
        color: var(--seller-maroon) !important;
    }

    .sl-seller-dashboard .sl-btn-blue-soft:hover,
    .sl-seller-dashboard .sl-btn-ghost:hover {
        background: #F3E4DE !important;
        border-color: var(--seller-maroon) !important;
    }

    .sl-seller-dashboard .sl-btn-sm {
        min-height: 34px;
        padding-inline: 12px;
        border-radius: 11px;
        font-size: 11px;
    }

    /* FIXED DASHBOARD METRICS */
    .sl-seller-dashboard .sl-dashboard-metrics {
        display: grid !important;
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        gap: 16px !important;
        align-items: stretch !important;
        justify-items: stretch !important;

        width: 100% !important;
        margin: 20px 0 18px !important;
        padding: 0 !important;
    }

    .sl-seller-dashboard .sl-metric-card {
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

        border: 1px solid var(--seller-border) !important;
        border-radius: 22px !important;

        background:
            radial-gradient(circle at 94% 6%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%) !important;

        color: var(--seller-text) !important;

        box-shadow: var(--seller-shadow-soft) !important;

        transform: none !important;
        translate: none !important;
        transition: 160ms ease !important;
    }

    .sl-seller-dashboard .sl-metric-card:hover {
        transform: translateY(-2px) !important;
        border-color: var(--seller-tan) !important;
        box-shadow: var(--seller-shadow-card) !important;
    }

    .sl-seller-dashboard .sl-metric-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .sl-seller-dashboard .sl-metric-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;

        border: 1px solid #E6C7BE;
        border-radius: 13px;

        background: #F3E4DE;
        color: var(--seller-maroon);
    }

    .sl-seller-dashboard .sl-metric-icon svg {
        width: 18px;
        height: 18px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sl-seller-dashboard .sl-metric-change {
        display: inline-flex;
        min-height: 24px;
        align-items: center;

        padding: 0 8px;

        border: 1px solid #E6C7BE;
        border-radius: 999px;

        background: #F3E4DE;
        color: var(--seller-maroon);

        font-size: 10px;
        font-weight: 900;
        line-height: 1;
        white-space: nowrap;
    }

    .sl-seller-dashboard .sl-metric-change.is-down {
        border-color: #EAD39A;
        background: var(--seller-warning-soft);
        color: var(--seller-warning);
    }

    .sl-seller-dashboard .sl-metric-body {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .sl-seller-dashboard .sl-metric-label {
        margin: 0;

        color: var(--seller-muted);

        font-size: 10px;
        font-weight: 900;
        line-height: 1.2;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sl-seller-dashboard .sl-metric-value {
        display: block;
        margin: 0;

        color: var(--seller-text-dark);

        font-size: 26px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-seller-dashboard .sl-dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(330px, 0.65fr);
        gap: 18px;
        align-items: start;
    }

    .sl-seller-dashboard .sl-dashboard-grid-lower {
        grid-template-columns: minmax(0, 1.25fr) minmax(330px, 0.75fr);
    }

    .sl-seller-dashboard .sl-card {
        overflow: hidden;

        border: 1px solid var(--seller-border) !important;
        border-radius: 24px !important;

        background: var(--seller-card) !important;
        color: var(--seller-text) !important;

        box-shadow: var(--seller-shadow-soft) !important;
    }

    .sl-seller-dashboard .sl-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;

        padding: 20px 22px;

        border-bottom: 1px solid var(--seller-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .sl-seller-dashboard .sl-card-head h2,
    .sl-seller-dashboard .sl-card-head h3 {
        margin: 7px 0 0;

        color: var(--seller-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-seller-dashboard .sl-card-head p {
        margin: 8px 0 0;

        color: var(--seller-muted) !important;

        font-size: 12px;
        line-height: 1.65;
    }

    .sl-seller-dashboard .sl-select {
        min-height: 40px;
        padding: 0 12px;

        border: 1px solid var(--seller-border);
        border-radius: 13px;

        background: var(--seller-bg-soft);
        color: var(--seller-text);

        font-size: 11px;
        font-weight: 800;
        outline: none;
    }

    .sl-seller-dashboard .sl-select:focus {
        border-color: var(--seller-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .sl-seller-dashboard .sl-text-link {
        color: var(--seller-maroon) !important;
        font-size: 11px;
        font-weight: 900;
        text-decoration: none;
    }

    .sl-seller-dashboard .sl-text-link:hover {
        text-decoration: underline;
    }

    .sl-seller-dashboard .sl-chart-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;

        padding: 20px 22px 0;
    }

    .sl-seller-dashboard .sl-chart-summary span {
        color: var(--seller-muted);
        font-size: 11px;
        font-weight: 800;
    }

    .sl-seller-dashboard .sl-chart-summary strong {
        display: block;
        margin-top: 5px;

        color: var(--seller-maroon);

        font-size: 32px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.05em;
    }

    .sl-seller-dashboard .sl-positive {
        display: inline-flex;
        min-height: 28px;
        align-items: center;

        padding: 0 10px;

        border: 1px solid #E6C7BE;
        border-radius: 999px;

        background: #F3E4DE;
        color: var(--seller-maroon) !important;

        font-size: 10px !important;
        font-weight: 900 !important;
    }

    .sl-seller-dashboard .sl-bar-chart {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        align-items: end;
        gap: 12px;

        min-height: 235px;
        margin: 20px 22px 22px;
        padding: 18px 16px 10px;

        border: 1px solid var(--seller-border);
        border-radius: 18px;

        background:
            linear-gradient(180deg, rgba(255, 253, 249, 0.9), rgba(246, 239, 231, 0.75));
    }

    .sl-seller-dashboard .sl-bar-column {
        display: grid;
        height: 185px;
        align-items: end;
        gap: 9px;
    }

    .sl-seller-dashboard .sl-bar-column span {
        display: block;
        min-height: 22px;

        border-radius: 999px 999px 10px 10px;

        background:
            linear-gradient(180deg, var(--seller-maroon-2), var(--seller-maroon));

        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.14);
    }

    .sl-seller-dashboard .sl-bar-column small {
        color: var(--seller-muted);
        font-size: 10px;
        font-weight: 800;
        text-align: center;
    }

    .sl-seller-dashboard .sl-donut-wrap {
        display: grid;
        grid-template-columns: 170px minmax(0, 1fr);
        align-items: center;
        gap: 22px;

        padding: 22px;
    }

    .sl-seller-dashboard .sl-donut {
        display: grid;
        width: 170px;
        height: 170px;
        place-items: center;

        border-radius: 999px;

        background:
            conic-gradient(
                var(--seller-maroon) 0 28%,
                var(--seller-tan) 28% 50%,
                #D9B99D 50% 72%,
                #EFE1D5 72% 100%
            );
    }

    .sl-seller-dashboard .sl-donut > span {
        display: grid;
        width: 112px;
        height: 112px;
        place-items: center;

        border-radius: 999px;

        background: var(--seller-card);
        color: var(--seller-text);

        text-align: center;
    }

    .sl-seller-dashboard .sl-donut strong {
        display: block;
        color: var(--seller-text);
        font-size: 32px;
        font-weight: 950;
        line-height: 1;
    }

    .sl-seller-dashboard .sl-donut small {
        display: block;
        color: var(--seller-muted);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .sl-seller-dashboard .sl-chart-legend {
        display: grid;
        gap: 11px;
    }

    .sl-seller-dashboard .sl-chart-legend a {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px;

        padding: 10px;

        border: 1px solid var(--seller-border);
        border-radius: 13px;

        background: rgba(246, 239, 231, 0.55);
        color: var(--seller-brown);
        text-decoration: none;
    }

    .sl-seller-dashboard .sl-chart-legend a:hover {
        border-color: var(--seller-tan);
        background: var(--seller-bg-soft);
    }

    .sl-seller-dashboard .sl-chart-legend i {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: var(--seller-maroon);
    }

    .sl-seller-dashboard .sl-chart-legend i.is-indigo {
        background: var(--seller-tan);
    }

    .sl-seller-dashboard .sl-chart-legend i.is-slate {
        background: #D9B99D;
    }

    .sl-seller-dashboard .sl-chart-legend i.is-light {
        background: #EFE1D5;
    }

    .sl-seller-dashboard .sl-chart-legend span {
        color: var(--seller-muted);
        font-size: 11px;
        font-weight: 800;
    }

    .sl-seller-dashboard .sl-chart-legend strong {
        color: var(--seller-text);
        font-size: 12px;
        font-weight: 950;
    }

    .sl-seller-dashboard .sl-table-wrap {
        overflow-x: auto;
    }

    .sl-seller-dashboard .sl-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
    }

    .sl-seller-dashboard .sl-table thead {
        background: var(--seller-bg-soft);
    }

    .sl-seller-dashboard .sl-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--seller-border);

        color: var(--seller-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .sl-seller-dashboard .sl-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--seller-brown);

        font-size: 12px;
        vertical-align: middle;
    }

    .sl-seller-dashboard .sl-table tbody tr:hover td {
        background: var(--seller-bg-soft);
    }

    .sl-seller-dashboard .sl-table td strong {
        color: var(--seller-text);
        font-weight: 950;
    }

    .sl-seller-dashboard .sl-table-product {
        color: var(--seller-brown);
        font-weight: 800;
    }

    .sl-seller-dashboard .sl-status {
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

    .sl-seller-dashboard .sl-status.is-success {
        background: var(--seller-success-soft);
        border-color: #CFE8DA;
        color: var(--seller-success);
    }

    .sl-seller-dashboard .sl-status.is-info {
        background: #F3E4DE;
        border-color: #E6C7BE;
        color: var(--seller-maroon);
    }

    .sl-seller-dashboard .sl-status.is-warning {
        background: var(--seller-warning-soft);
        border-color: #EAD39A;
        color: var(--seller-warning);
    }

    .sl-seller-dashboard .sl-status.is-neutral {
        background: var(--seller-bg-soft);
        border-color: var(--seller-border-strong);
        color: var(--seller-brown);
    }

    .sl-seller-dashboard .sl-icon-link {
        display: grid;
        width: 30px;
        height: 30px;
        place-items: center;

        border: 1px solid var(--seller-border);
        border-radius: 999px;

        background: var(--seller-bg-soft);
        color: var(--seller-maroon);

        font-size: 15px;
        font-weight: 950;
        text-decoration: none;
    }

    .sl-seller-dashboard .sl-icon-link:hover {
        background: var(--seller-maroon);
        border-color: var(--seller-maroon);
        color: #FFFFFF;
    }

    .sl-seller-dashboard .sl-alert-list {
        display: grid;
        gap: 12px;
        padding: 20px;
    }

    .sl-seller-dashboard .sl-stock-alert {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;

        padding: 14px;

        border: 1px solid var(--seller-border);
        border-radius: 17px;

        background:
            radial-gradient(circle at 96% 4%, rgba(193, 151, 113, 0.12), transparent 28%),
            #FFFDF9;
    }

    .sl-seller-dashboard .sl-stock-alert.is-critical {
        border-color: #F0C9C4;
        background: var(--seller-danger-soft);
    }

    .sl-seller-dashboard .sl-stock-alert.is-warning {
        border-color: #EAD39A;
        background: var(--seller-warning-soft);
    }

    .sl-seller-dashboard .sl-stock-icon {
        display: grid;
        width: 40px;
        height: 40px;
        place-items: center;

        border-radius: 14px;

        background: #FFFFFF;
        color: var(--seller-maroon);

        font-size: 13px;
        font-weight: 950;
    }

    .sl-seller-dashboard .sl-stock-alert.is-critical .sl-stock-icon {
        color: var(--seller-danger);
    }

    .sl-seller-dashboard .sl-stock-alert.is-warning .sl-stock-icon {
        color: var(--seller-warning);
    }

    .sl-seller-dashboard .sl-stock-alert small {
        display: block;

        color: var(--seller-muted);

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.1em;
    }

    .sl-seller-dashboard .sl-stock-alert strong {
        display: block;
        margin-top: 4px;

        color: var(--seller-text);

        font-size: 12px;
        font-weight: 950;
    }

    .sl-seller-dashboard .sl-stock-alert div span {
        display: block;
        margin-top: 3px;

        color: var(--seller-muted);

        font-size: 11px;
    }

    .sl-seller-dashboard .sl-flow-card .sl-card-head {
        border-bottom: 0 !important;
    }

    .sl-seller-dashboard .sl-process-flow {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 12px;

        padding: 0 22px 22px;
    }

    .sl-seller-dashboard .sl-process-step {
        position: relative;

        display: grid;
        gap: 12px;

        min-height: 140px;
        padding: 16px;

        border: 1px solid var(--seller-border);
        border-radius: 18px;

        background:
            radial-gradient(circle at 90% 8%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
    }

    .sl-seller-dashboard .sl-process-step > span {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;

        border-radius: 12px;

        background: var(--seller-maroon);
        color: #FFFFFF;

        font-size: 12px;
        font-weight: 950;
    }

    .sl-seller-dashboard .sl-process-step strong {
        display: block;

        color: var(--seller-text);

        font-size: 12px;
        font-weight: 950;
        line-height: 1.25;
    }

    .sl-seller-dashboard .sl-process-step small {
        display: block;
        margin-top: 5px;

        color: var(--seller-muted);

        font-size: 10px;
        line-height: 1.45;
    }

    @media (max-width: 1280px) {
        .sl-seller-dashboard .sl-dashboard-metrics {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }

        .sl-seller-dashboard .sl-dashboard-grid,
        .sl-seller-dashboard .sl-dashboard-grid-lower {
            grid-template-columns: 1fr;
        }

        .sl-seller-dashboard .sl-process-flow {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .sl-seller-dashboard .sl-dashboard-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 820px) {
        .sl-seller-dashboard .sl-welcome-panel,
        .sl-seller-dashboard .sl-card-head,
        .sl-seller-dashboard .sl-chart-summary {
            align-items: flex-start;
            flex-direction: column;
        }

        .sl-seller-dashboard .sl-welcome-panel {
            padding: 28px 22px;
        }

        .sl-seller-dashboard .sl-process-flow {
            grid-template-columns: 1fr;
        }

        .sl-seller-dashboard .sl-welcome-actions,
        .sl-seller-dashboard .sl-welcome-actions .sl-btn,
        .sl-seller-dashboard .sl-card-head .sl-select {
            width: 100%;
        }

        .sl-seller-dashboard .sl-donut-wrap {
            grid-template-columns: 1fr;
            justify-items: center;
        }

        .sl-seller-dashboard .sl-stock-alert {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .sl-seller-dashboard .sl-stock-alert .sl-btn {
            grid-column: 1 / -1;
            width: 100%;
        }
    }

    @media (max-width: 620px) {
        .sl-seller-dashboard .sl-dashboard-metrics {
            grid-template-columns: 1fr !important;
        }
    }

    html.dark .sl-seller-dashboard .sl-welcome-panel,
    html.dark .sl-seller-dashboard .sl-metric-card,
    html.dark .sl-seller-dashboard .sl-card,
    html.dark .sl-seller-dashboard .sl-card-head,
    html.dark .sl-seller-dashboard .sl-bar-chart,
    html.dark .sl-seller-dashboard .sl-chart-legend a,
    html.dark .sl-seller-dashboard .sl-stock-alert,
    html.dark .sl-seller-dashboard .sl-process-step {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;

        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .sl-seller-dashboard .sl-welcome-panel h2,
    html.dark .sl-seller-dashboard .sl-metric-value,
    html.dark .sl-seller-dashboard .sl-card-head h2,
    html.dark .sl-seller-dashboard .sl-card-head h3,
    html.dark .sl-seller-dashboard .sl-chart-summary strong,
    html.dark .sl-seller-dashboard .sl-donut strong,
    html.dark .sl-seller-dashboard .sl-table td strong,
    html.dark .sl-seller-dashboard .sl-stock-alert strong,
    html.dark .sl-seller-dashboard .sl-process-step strong {
        color: #F5EFE8 !important;
    }

    html.dark .sl-seller-dashboard .sl-welcome-panel p,
    html.dark .sl-seller-dashboard .sl-metric-label,
    html.dark .sl-seller-dashboard .sl-card-head p,
    html.dark .sl-seller-dashboard .sl-chart-summary span,
    html.dark .sl-seller-dashboard .sl-donut small,
    html.dark .sl-seller-dashboard .sl-table td,
    html.dark .sl-seller-dashboard .sl-table-product,
    html.dark .sl-seller-dashboard .sl-stock-alert small,
    html.dark .sl-seller-dashboard .sl-stock-alert div span,
    html.dark .sl-seller-dashboard .sl-process-step small {
        color: #C8B7AD !important;
    }

    html.dark .sl-seller-dashboard .sl-eyebrow,
    html.dark .sl-seller-dashboard .sl-text-link {
        color: #EBA99D !important;
    }

    html.dark .sl-seller-dashboard .sl-eyebrow::before {
        background: #EBA99D;
    }

    html.dark .sl-seller-dashboard .sl-btn-white {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .sl-seller-dashboard .sl-btn-blue-soft,
    html.dark .sl-seller-dashboard .sl-btn-ghost,
    html.dark .sl-seller-dashboard .sl-select,
    html.dark .sl-seller-dashboard .sl-table thead {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-seller-dashboard .sl-metric-icon,
    html.dark .sl-seller-dashboard .sl-metric-change {
        background: #2D1414 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .sl-seller-dashboard .sl-table th,
    html.dark .sl-seller-dashboard .sl-table td {
        border-color: #3B2E27 !important;
    }

    html.dark .sl-seller-dashboard .sl-table tbody tr:hover td {
        background: #2D1414 !important;
    }

    html.dark .sl-seller-dashboard .sl-donut > span,
    html.dark .sl-seller-dashboard .sl-stock-icon {
        background: #1E1A17 !important;
        color: #EBA99D !important;
    }
</style>

<div class="sl-page sl-seller-dashboard">
    <section class="sl-welcome-panel">
        <div>
            <span class="sl-eyebrow">
                Friday, September 4
            </span>

            <h2>
                Good morning, {{ explode(' ', $sellerName)[0] ?? 'Seller' }}.
            </h2>

            <p>
                Your store is healthy. You have <strong>5 new orders</strong> and
                <strong>2 inventory alerts</strong> that need attention.
            </p>
        </div>

        <div class="sl-welcome-actions">
            <a
                href="{{ route('seller.products', ['mode' => 'add']) }}"
                class="sl-btn sl-btn-white"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 5v14M5 12h14"/>
                </svg>

                Add Product
            </a>

            <a
                href="{{ route('seller.orders', ['status' => 'to-process']) }}"
                class="sl-btn sl-btn-blue-soft"
            >
                Process Orders
            </a>
        </div>
    </section>

    <section class="sl-dashboard-metrics" aria-label="Store summary">
        @foreach ($metrics as $metric)
            <article class="sl-metric-card">
                <div class="sl-metric-top">
                    <span class="sl-metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $metricIcons[$metric['icon']] ?? $metricIcons['sales'] !!}
                        </svg>
                    </span>

                    <span class="sl-metric-change {{ $metric['direction'] === 'down' ? 'is-down' : '' }}">
                        {{ $metric['direction'] === 'down' ? '↓' : '↑' }} {{ $metric['change'] }}
                    </span>
                </div>

                <div class="sl-metric-body">
                    <p class="sl-metric-label">
                        {{ $metric['label'] }}
                    </p>

                    <strong class="sl-metric-value">
                        {{ $metric['value'] }}
                    </strong>
                </div>
            </article>
        @endforeach
    </section>

    <div class="sl-dashboard-grid">
        <section class="sl-card sl-sales-panel">
            <header class="sl-card-head">
                <div>
                    <span class="sl-eyebrow">
                        Performance
                    </span>

                    <h2>
                        Sales Overview
                    </h2>

                    <p>
                        Revenue collected over the last 7 days.
                    </p>
                </div>

                <select class="sl-select sl-select-sm" aria-label="Sales period">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                    <option>This year</option>
                </select>
            </header>

            <div class="sl-chart-summary">
                <div>
                    <span>Total revenue</span>

                    <strong>
                        ₱184,650
                    </strong>
                </div>

                <span class="sl-positive">
                    ↑ 6.4% vs previous week
                </span>
            </div>

            <div class="sl-bar-chart" aria-label="Sales chart from Saturday to Friday">
                @foreach ([42, 58, 48, 72, 64, 83, 76] as $height)
                    <div class="sl-bar-column">
                        <span style="height: {{ $height }}%"></span>

                        <small>
                            {{ ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'][$loop->index] }}
                        </small>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="sl-card sl-order-status-panel">
            <header class="sl-card-head">
                <div>
                    <span class="sl-eyebrow">
                        Fulfillment
                    </span>

                    <h2>
                        Order Status
                    </h2>

                    <p>
                        Current order distribution.
                    </p>
                </div>

                <a href="{{ route('seller.orders') }}" class="sl-text-link">
                    View all
                </a>
            </header>

            <div class="sl-donut-wrap">
                <div class="sl-donut" aria-label="35 active orders">
                    <span>
                        <strong>35</strong>
                        <small>Orders</small>
                    </span>
                </div>

                <div class="sl-chart-legend">
                    @foreach($statusItems as $item)
                        <a href="{{ route('seller.orders', ['status' => $item['status']]) }}">
                            <i class="{{ $loop->index === 1 ? 'is-indigo' : ($loop->index === 2 ? 'is-slate' : ($loop->index === 3 ? 'is-light' : 'is-blue')) }}"></i>

                            <span>
                                {{ $item['label'] }}
                            </span>

                            <strong>
                                {{ $item['count'] }}
                            </strong>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <div class="sl-dashboard-grid sl-dashboard-grid-lower">
        <section class="sl-card">
            <header class="sl-card-head">
                <div>
                    <span class="sl-eyebrow">
                        Operations
                    </span>

                    <h2>
                        Recent Orders
                    </h2>

                    <p>
                        Latest purchases waiting in your workflow.
                    </p>
                </div>

                <a href="{{ route('seller.orders') }}" class="sl-text-link">
                    Manage orders
                </a>
            </header>

            <div class="sl-table-wrap">
                <table class="sl-table sl-recent-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Buyer</th>
                            <th>Product</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($dashboardOrders->take(5) as $order)
                            @php
                                $tone = match (data_get($order, 'status_key')) {
                                    'completed' => 'is-success',
                                    'shipping', 'ready-pickup' => 'is-info',
                                    'to-prepare' => 'is-warning',
                                    default => 'is-neutral',
                                };
                            @endphp

                            <tr>
                                <td>
                                    <strong>
                                        #{{ data_get($order, 'id') }}
                                    </strong>
                                </td>

                                <td>
                                    {{ data_get($order, 'buyer') }}
                                </td>

                                <td>
                                    <span class="sl-table-product">
                                        {{ data_get($order, 'product') }}
                                    </span>
                                </td>

                                <td>
                                    <strong>
                                        ₱{{ number_format((float) data_get($order, 'total'), 2) }}
                                    </strong>
                                </td>

                                <td>
                                    <span class="sl-status {{ $tone }}">
                                        {{ data_get($order, 'status') }}
                                    </span>
                                </td>

                                <td>
                                    <a
                                        class="sl-icon-link"
                                        href="{{ route('seller.orders', ['mode' => 'show', 'order' => data_get($order, 'id')]) }}"
                                        aria-label="View order #{{ data_get($order, 'id') }}"
                                    >
                                        →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="sl-card">
            <header class="sl-card-head">
                <div>
                    <span class="sl-eyebrow">
                        Stock Control
                    </span>

                    <h2>
                        Inventory Alerts
                    </h2>

                    <p>
                        Products requiring attention.
                    </p>
                </div>

                <a
                    href="{{ route('seller.products', ['mode' => 'inventory']) }}"
                    class="sl-text-link"
                >
                    Inventory
                </a>
            </header>

            <div class="sl-alert-list">
                @foreach($inventoryAlerts as $alert)
                    <article class="sl-stock-alert is-{{ $alert['tone'] }}">
                        <span class="sl-stock-icon">
                            {{ $alert['icon'] }}
                        </span>

                        <div>
                            <small>
                                {{ $alert['label'] }}
                            </small>

                            <strong>
                                {{ $alert['product'] }}
                            </strong>

                            <span>
                                {{ $alert['detail'] }}
                            </span>
                        </div>

                        @if($alert['action'] === 'Update')
                            <button
                                type="button"
                                class="sl-btn sl-btn-ghost sl-btn-sm"
                                data-stock-update
                                data-product="{{ $alert['product'] }}"
                            >
                                Update
                            </button>
                        @else
                            <a
                                href="{{ route('seller.products', ['mode' => 'inventory']) }}"
                                class="sl-btn sl-btn-ghost sl-btn-sm"
                            >
                                Review
                            </a>
                        @endif
                    </article>
                @endforeach
            </div>
        </aside>
    </div>

    <section class="sl-card sl-flow-card">
        <header class="sl-card-head">
            <div>
                <span class="sl-eyebrow">
                    Fulfillment Workflow
                </span>

                <h2>
                    From Order to Delivery
                </h2>

                <p>
                    Use these stages to keep every shipment moving.
                </p>
            </div>
        </header>

        <div class="sl-process-flow">
            @foreach ($workflowSteps as $step)
                <div class="sl-process-step">
                    <span>
                        {{ $step[0] }}
                    </span>

                    <div>
                        <strong>
                            {{ $step[1] }}
                        </strong>

                        <small>
                            {{ $step[2] }}
                        </small>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection