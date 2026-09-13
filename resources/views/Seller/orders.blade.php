@extends('layouts.seller')

@php
    $ordersCollection = isset($sellerOrders)
        ? collect($sellerOrders)
        : collect([
            [
                'id' => '10001',
                'product' => 'Handwoven Abaca Tote',
                'buyer' => 'Angela Cruz',
                'quantity' => 1,
                'total' => 1590,
                'date' => 'Sep 4, 2026',
                'status' => 'To Process',
                'status_key' => 'to-process',
                'payment' => 'GCash',
                'variant' => 'Natural Brown',
            ],
            [
                'id' => '10002',
                'product' => 'Mechanical Keyboard',
                'buyer' => 'Marco Reyes',
                'quantity' => 2,
                'total' => 5580,
                'date' => 'Sep 4, 2026',
                'status' => 'To Prepare',
                'status_key' => 'to-prepare',
                'payment' => 'COD',
                'variant' => '87 Keys · Black',
            ],
            [
                'id' => '10003',
                'product' => 'Artisan Soy Candle Set',
                'buyer' => 'Noel Garcia',
                'quantity' => 1,
                'total' => 890,
                'date' => 'Sep 3, 2026',
                'status' => 'Ready Pickup',
                'status_key' => 'ready-pickup',
                'payment' => 'Maya',
                'variant' => 'Set of 3',
            ],
        ]);

    $isLogistics = ($pageMode ?? 'orders') === 'logistics';

    $currentMode = $mode ?? (
        $isLogistics
            ? request('view', 'couriers')
            : request('mode', 'index')
    );

    $currentStatus = $status ?? request('status', 'all');

    $orderId = $selectedOrder ?? request('order', '10001');

    $order = $ordersCollection->firstWhere('id', (string) $orderId)
        ?? $ordersCollection->first()
        ?? [
            'id' => '10001',
            'product' => 'Sample Product',
            'buyer' => 'Buyer Name',
            'quantity' => 1,
            'total' => 0,
            'date' => 'Today',
            'status' => 'To Process',
            'status_key' => 'to-process',
            'payment' => 'GCash',
            'variant' => 'Standard',
        ];

    $pageTitle = $isLogistics
        ? 'Logistics'
        : ($currentMode === 'show' ? 'Order #' . data_get($order, 'id') : 'Orders');

    $pageSubtitle = $isLogistics
        ? 'Assign couriers, submit pickup requests, and monitor shipments.'
        : 'Review purchases and move each order through fulfillment.';

    $orderTabs = [
        'all' => 'All Orders',
        'to-process' => 'To Process',
        'to-prepare' => 'To Prepare',
        'ready-pickup' => 'Ready Pickup',
        'shipping' => 'Shipping',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'returns' => 'Returns/Refunds',
    ];

    $providers = [
        ['name' => 'J&T Express', 'pickup' => 'Tomorrow, 10:00 AM', 'eta' => '1–2 days', 'rate' => '₱145.00', 'tag' => 'Recommended'],
        ['name' => 'Flash Express', 'pickup' => 'Tomorrow, 2:00 PM', 'eta' => '1–3 days', 'rate' => '₱135.00', 'tag' => 'Lowest rate'],
        ['name' => 'LBC', 'pickup' => 'Sep 06, 9:00 AM', 'eta' => '2–3 days', 'rate' => '₱180.00', 'tag' => ''],
        ['name' => 'Local Courier', 'pickup' => 'Today, 4:30 PM', 'eta' => 'Same day', 'rate' => '₱220.00', 'tag' => 'Fastest'],
    ];

    $pickups = [
        ['id' => 'PU-2091', 'orders' => '#10003', 'provider' => 'J&T Express', 'window' => 'Today · 10AM–12PM', 'courier' => 'Juan Rider', 'status' => 'Courier assigned', 'tone' => 'is-info'],
        ['id' => 'PU-2090', 'orders' => '#10007, #10008', 'provider' => 'Flash Express', 'window' => 'Today · 1PM–3PM', 'courier' => 'Waiting assignment', 'status' => 'Requested', 'tone' => 'is-warning'],
        ['id' => 'PU-2089', 'orders' => '#10009', 'provider' => 'LBC', 'window' => 'Today · 3PM–5PM', 'courier' => 'Carlo Dela Cruz', 'status' => 'Courier heading to store', 'tone' => 'is-info'],
        ['id' => 'PU-2088', 'orders' => '#10010', 'provider' => 'J&T Express', 'window' => 'Yesterday · 2PM', 'courier' => 'Miguel Santos', 'status' => 'Picked up', 'tone' => 'is-success'],
    ];

    $trackingEvents = [
        ['title' => 'Pickup Requested', 'time' => 'Sep 03 · 9:05 AM', 'description' => 'Pickup scheduled by seller.', 'done' => true],
        ['title' => 'Courier Assigned', 'time' => 'Sep 03 · 9:32 AM', 'description' => 'Juan Rider accepted the pickup.', 'done' => true],
        ['title' => 'Picked Up', 'time' => 'Sep 03 · 11:48 AM', 'description' => 'Parcel collected from LIKHAE Studio.', 'done' => true],
        ['title' => 'Sorting Center', 'time' => 'Sep 03 · 4:20 PM', 'description' => 'Scanned at Santa Cruz Sorting Center.', 'done' => true],
        ['title' => 'In Transit', 'time' => 'Sep 04 · 7:10 AM', 'description' => 'Departed for Calamba Hub.', 'done' => true],
        ['title' => 'Delivered', 'time' => 'Estimated Sep 05', 'description' => 'Waiting for buyer delivery confirmation.', 'done' => false],
    ];

    $visibleOrders = $currentStatus === 'all'
        ? $ordersCollection
        : $ordersCollection->where('status_key', $currentStatus);
@endphp

@section('title', $pageTitle)
@section('active', $isLogistics ? 'logistics' : 'orders')
@section('subtitle', $pageSubtitle)

@section('content')
<style>
    :root {
        --ord-bg: #FBF7F2;
        --ord-bg-soft: #F6EFE7;
        --ord-bg-alt: #EFE7DE;
        --ord-card: #FFFDF9;

        --ord-border: #EADCCC;
        --ord-border-strong: #DBCEC1;

        --ord-maroon: #561C17;
        --ord-maroon-2: #642920;
        --ord-maroon-dark: #3E130F;

        --ord-text: #3B211B;
        --ord-brown: #6C4936;
        --ord-muted: #987865;
        --ord-muted-2: #A99386;

        --ord-tan: #C19771;

        --ord-success: #256F4A;
        --ord-success-soft: #EAF7EF;

        --ord-warning: #9A5B11;
        --ord-warning-soft: #FFF6DE;

        --ord-danger: #B42318;
        --ord-danger-soft: #FCEBE9;

        --ord-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --ord-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .sl-orders-page {
        color: var(--ord-text);

        --sl-blue: var(--ord-maroon);
        --sl-blue-dark: var(--ord-maroon-dark);
        --sl-blue-soft: #F3E4DE;
        --sl-indigo: var(--ord-tan);
    }

    .sl-orders-page .sl-page-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;

        padding: 32px 36px;

        border: 1px solid var(--ord-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--ord-shadow-soft);
    }

    .sl-orders-page .sl-eyebrow {
        color: var(--ord-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .sl-orders-page .sl-page-toolbar h2 {
        margin-top: 10px;

        color: var(--ord-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .sl-orders-page .sl-page-toolbar p {
        max-width: 700px;
        margin-top: 13px;

        color: var(--ord-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .sl-orders-page .sl-btn {
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

    .sl-orders-page .sl-btn:hover {
        transform: translateY(-1px);
    }

    .sl-orders-page .sl-btn svg {
        width: 16px;
        height: 16px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sl-orders-page .sl-btn-primary {
        background: var(--ord-maroon) !important;
        border-color: var(--ord-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .sl-orders-page .sl-btn-primary:hover {
        background: var(--ord-maroon-dark) !important;
        border-color: var(--ord-maroon-dark) !important;
    }

    .sl-orders-page .sl-btn-ghost,
    .sl-orders-page .sl-btn-soft {
        background: var(--ord-card) !important;
        border-color: var(--ord-tan) !important;
        color: var(--ord-maroon) !important;
    }

    .sl-orders-page .sl-btn-ghost:hover,
    .sl-orders-page .sl-btn-soft:hover {
        background: #F3E4DE !important;
        border-color: var(--ord-maroon) !important;
    }

    .sl-orders-page .sl-btn-sm {
        min-height: 34px;
        padding-inline: 12px;
        border-radius: 11px;

        font-size: 11px;
    }

    .sl-orders-page .sl-btn-block {
        width: 100%;
    }

    .sl-orders-page .sl-card {
        overflow: hidden;

        border: 1px solid var(--ord-border) !important;
        border-radius: 24px !important;

        background: var(--ord-card) !important;
        color: var(--ord-text) !important;

        box-shadow: var(--ord-shadow-soft) !important;
    }

    .sl-orders-page .sl-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding: 20px 22px;

        border-bottom: 1px solid var(--ord-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .sl-orders-page .sl-card-head h2,
    .sl-orders-page .sl-card h3 {
        margin: 0;

        color: var(--ord-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 32px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-orders-page .sl-card-head h2 {
        margin-top: 7px;
    }

    .sl-orders-page .sl-card-head p {
        margin-top: 8px;

        color: var(--ord-muted) !important;

        font-size: 12px;
        line-height: 1.65;
    }

    .sl-orders-page .sl-status {
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

    .sl-orders-page .sl-status.is-success,
    .sl-orders-page .sl-status.is-completed {
        background: var(--ord-success-soft);
        border-color: #CFE8DA;
        color: var(--ord-success);
    }

    .sl-orders-page .sl-status.is-warning {
        background: var(--ord-warning-soft);
        border-color: #EAD39A;
        color: var(--ord-warning);
    }

    .sl-orders-page .sl-status.is-info {
        background: #F3E4DE;
        border-color: #E6C7BE;
        color: var(--ord-maroon);
    }

    .sl-orders-page .sl-status.is-danger {
        background: var(--ord-danger-soft);
        border-color: #F0C9C4;
        color: var(--ord-danger);
    }

    .sl-orders-page .sl-status.is-neutral {
        background: var(--ord-bg-soft);
        border-color: var(--ord-border-strong);
        color: var(--ord-brown);
    }

    .sl-orders-page .sl-mini-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .sl-orders-page .sl-mini-stats div {
        padding: 18px;

        border: 1px solid var(--ord-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        box-shadow: var(--ord-shadow-soft);
    }

    .sl-orders-page .sl-mini-stats span {
        display: block;

        color: var(--ord-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .sl-orders-page .sl-mini-stats strong {
        display: block;
        margin-top: 8px;

        color: var(--ord-maroon);

        font-size: 32px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-orders-page .sl-logistics-layout,
    .sl-orders-page .sl-tracking-layout,
    .sl-orders-page .sl-order-detail-layout {
        display: grid;
        grid-template-columns: minmax(0, 0.92fr) minmax(0, 1.08fr);
        gap: 18px;
        align-items: start;
    }

    .sl-orders-page .sl-order-detail-layout {
        grid-template-columns: minmax(0, 1fr) 340px;
    }

    .sl-orders-page .sl-order-detail-main,
    .sl-orders-page .sl-order-detail-side {
        display: grid;
        gap: 18px;
    }

    .sl-orders-page .sl-package-summary {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 14px;

        padding: 18px 22px;

        border-bottom: 1px solid var(--ord-border);

        background: #FFFDF9;
    }

    .sl-orders-page .sl-order-thumb {
        display: grid;
        width: 54px;
        height: 54px;
        place-items: center;

        border-radius: 17px;

        background: #F1E4D7;
        color: var(--ord-maroon);

        font-size: 18px;
        font-weight: 950;
    }

    .sl-orders-page .sl-package-summary strong {
        color: var(--ord-text);

        font-size: 13px;
        font-weight: 950;
    }

    .sl-orders-page .sl-package-summary span,
    .sl-orders-page .sl-package-summary small {
        display: block;
        margin-top: 4px;

        color: var(--ord-muted);

        font-size: 11px;
        line-height: 1.5;
    }

    .sl-orders-page .sl-address-card {
        display: flex;
        gap: 12px;

        margin: 14px 22px;
        padding: 15px;

        border: 1px solid var(--ord-border);
        border-radius: 18px;

        background: var(--ord-bg-soft);
    }

    .sl-orders-page .sl-address-card svg {
        width: 21px;
        height: 21px;
        flex: 0 0 21px;

        color: var(--ord-maroon);

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sl-orders-page .sl-address-card span,
    .sl-orders-page .sl-address-card small {
        display: block;

        color: var(--ord-muted);

        font-size: 10px;
        font-weight: 800;
    }

    .sl-orders-page .sl-address-card strong {
        display: block;
        margin-top: 3px;

        color: var(--ord-text);

        font-size: 12px;
        font-weight: 950;
    }

    .sl-orders-page .sl-provider-list {
        display: grid;
        gap: 12px;

        padding: 20px;
    }

    .sl-orders-page .sl-provider-option {
        position: relative;

        display: grid;
        grid-template-columns: auto auto minmax(0, 1fr) auto auto;
        align-items: center;
        gap: 12px;

        padding: 14px;

        border: 1px solid var(--ord-border);
        border-radius: 18px;

        background:
            radial-gradient(circle at 96% 8%, rgba(193, 151, 113, 0.10), transparent 28%),
            #FFFDF9;

        cursor: pointer;
        transition: 160ms ease;
    }

    .sl-orders-page .sl-provider-option:hover,
    .sl-orders-page .sl-provider-option:has(input:checked) {
        border-color: var(--ord-tan);
        background: #FFF8F0;
        box-shadow: var(--ord-shadow-soft);
    }

    .sl-orders-page .sl-provider-option input {
        position: absolute;
        opacity: 0;
    }

    .sl-orders-page .sl-radio-mark {
        display: grid;
        width: 18px;
        height: 18px;
        place-items: center;

        border: 2px solid var(--ord-border-strong);
        border-radius: 999px;
    }

    .sl-orders-page .sl-provider-option input:checked + .sl-radio-mark {
        border-color: var(--ord-maroon);
    }

    .sl-orders-page .sl-provider-option input:checked + .sl-radio-mark::after {
        width: 8px;
        height: 8px;

        border-radius: 999px;

        background: var(--ord-maroon);

        content: "";
    }

    .sl-orders-page .sl-provider-logo {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;

        border-radius: 14px;

        background: #F1E4D7;
        color: var(--ord-maroon);

        font-size: 12px;
        font-weight: 950;
    }

    .sl-orders-page .sl-provider-main strong,
    .sl-orders-page .sl-provider-meta strong {
        display: block;

        color: var(--ord-text);

        font-size: 12px;
        font-weight: 950;
    }

    .sl-orders-page .sl-provider-main small,
    .sl-orders-page .sl-provider-meta small {
        display: block;
        margin-top: 4px;

        color: var(--ord-muted);

        font-size: 10px;
        line-height: 1.45;
    }

    .sl-orders-page .sl-provider-meta {
        text-align: right;
    }

    .sl-orders-page .sl-provider-tag {
        display: inline-flex;
        min-height: 24px;
        align-items: center;

        padding: 0 9px;

        border-radius: 999px;

        background: #F3E4DE;
        color: var(--ord-maroon);

        font-size: 9px;
        font-weight: 900;
        white-space: nowrap;
    }

    .sl-orders-page .sl-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;

        padding: 0 20px 20px;
    }

    .sl-orders-page .sl-span-2 {
        grid-column: 1 / -1;
    }

    .sl-orders-page .sl-field {
        display: grid;
        gap: 7px;
    }

    .sl-orders-page .sl-field span {
        color: var(--ord-muted) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sl-orders-page .sl-field input,
    .sl-orders-page .sl-field select,
    .sl-orders-page .sl-field textarea,
    .sl-orders-page .sl-select,
    .sl-orders-page .sl-input {
        width: 100%;
        min-height: 42px;
        padding: 0 13px;

        border: 1px solid var(--ord-border);
        border-radius: 14px;

        background: var(--ord-bg-soft);
        color: var(--ord-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;

        transition: 160ms ease;
    }

    .sl-orders-page .sl-field textarea {
        min-height: 92px;
        padding-block: 11px;
        line-height: 1.6;
        resize: vertical;
    }

    .sl-orders-page .sl-field input:focus,
    .sl-orders-page .sl-field select:focus,
    .sl-orders-page .sl-field textarea:focus,
    .sl-orders-page .sl-select:focus,
    .sl-orders-page .sl-input:focus {
        border-color: var(--ord-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .sl-orders-page .sl-form-actions {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;

        padding: 0 20px 20px;
    }

    .sl-orders-page .sl-table-toolbar,
    .sl-orders-page .sl-order-filters {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;

        padding: 15px;

        border-bottom: 1px solid var(--ord-border);

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.12), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .sl-orders-page .sl-order-filters {
        border-radius: 22px;
        border: 1px solid var(--ord-border);
        box-shadow: var(--ord-shadow-soft);
    }

    .sl-orders-page .sl-search-input {
        position: relative;
        flex: 1;
        max-width: 520px;
    }

    .sl-orders-page .sl-search-input svg {
        position: absolute;
        top: 50%;
        left: 14px;

        width: 16px;
        height: 16px;

        color: var(--ord-muted-2);

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;

        transform: translateY(-50%);
    }

    .sl-orders-page .sl-search-input input {
        width: 100%;
        min-height: 42px;
        padding: 0 14px 0 42px;

        border: 1px solid var(--ord-border);
        border-radius: 14px;

        background: var(--ord-bg-soft);
        color: var(--ord-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;
    }

    .sl-orders-page .sl-tracking-search {
        display: flex;
        max-width: 420px;
        overflow: hidden;

        border: 1px solid var(--ord-border);
        border-radius: 15px;

        background: var(--ord-card);
    }

    .sl-orders-page .sl-tracking-search input {
        border: 0;
        border-radius: 0;
        background: transparent;
    }

    .sl-orders-page .sl-tracking-search button {
        min-width: 86px;

        border: 0;

        background: var(--ord-maroon);
        color: #FFFFFF;

        font-size: 11px;
        font-weight: 900;
        cursor: pointer;
    }

    .sl-orders-page .sl-table-wrap {
        overflow-x: auto;
    }

    .sl-orders-page .sl-table {
        width: 100%;
        min-width: 920px;

        border-collapse: collapse;
    }

    .sl-orders-page .sl-table thead {
        background: var(--ord-bg-soft);
    }

    .sl-orders-page .sl-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--ord-border);

        color: var(--ord-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .sl-orders-page .sl-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--ord-brown);

        font-size: 12px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .sl-orders-page .sl-table tbody tr:hover td {
        background: var(--ord-bg-soft);
    }

    .sl-orders-page .sl-table td strong {
        color: var(--ord-text);
        font-weight: 950;
    }

    .sl-orders-page .sl-order-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 6px;

        border: 1px solid var(--ord-border);
        border-radius: 16px;

        background: var(--ord-card);
        box-shadow: var(--ord-shadow-soft);
    }

    .sl-orders-page .sl-order-tabs a {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 8px;

        padding: 0 15px;

        border-radius: 12px;

        color: var(--ord-brown);
        text-decoration: none;

        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;

        transition: 160ms ease;
    }

    .sl-orders-page .sl-order-tabs a:hover {
        background: var(--ord-bg-soft);
        color: var(--ord-maroon);
    }

    .sl-orders-page .sl-order-tabs a.is-active {
        background: var(--ord-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .sl-orders-page .sl-order-tabs a span {
        display: grid;
        min-width: 22px;
        height: 22px;
        place-items: center;

        padding: 0 7px;

        border-radius: 999px;

        background: rgba(255, 255, 255, 0.18);
        color: inherit;

        font-size: 10px;
        font-weight: 950;
    }

    .sl-orders-page .sl-order-list {
        display: grid;
        gap: 14px;
    }

    .sl-orders-page .sl-shipment-summary {
        padding: 0;
    }

    .sl-orders-page .sl-shipment-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding: 22px;

        border-bottom: 1px solid var(--ord-border);

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .sl-orders-page .sl-shipment-head h2 {
        margin-top: 7px;

        color: var(--ord-text);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 36px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-orders-page .sl-shipment-head p {
        margin-top: 8px;

        color: var(--ord-muted);

        font-size: 12px;
    }

    .sl-orders-page .sl-shipment-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;

        margin: 0;
        padding: 20px 22px;
    }

    .sl-orders-page .sl-shipment-meta div,
    .sl-orders-page .sl-detail-list div,
    .sl-orders-page .sl-price-list div {
        padding: 13px;

        border: 1px solid var(--ord-border);
        border-radius: 14px;

        background: var(--ord-bg-soft);
    }

    .sl-orders-page .sl-shipment-meta dt,
    .sl-orders-page .sl-detail-list dt,
    .sl-orders-page .sl-price-list dt {
        color: var(--ord-muted);

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sl-orders-page .sl-shipment-meta dd,
    .sl-orders-page .sl-detail-list dd,
    .sl-orders-page .sl-price-list dd {
        margin: 5px 0 0;

        color: var(--ord-text);

        font-size: 12px;
        font-weight: 900;
    }

    .sl-orders-page .sl-tracking-map {
        position: relative;

        min-height: 250px;
        margin: 0 22px 22px;
        overflow: hidden;

        border: 1px solid var(--ord-border);
        border-radius: 22px;

        background:
            linear-gradient(rgba(86, 28, 23, 0.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(86, 28, 23, 0.035) 1px, transparent 1px),
            var(--ord-bg-soft);
        background-size: 28px 28px;
    }

    .sl-orders-page .sl-map-route {
        position: absolute;
        left: 55px;
        right: 55px;
        top: 118px;

        height: 4px;

        border-radius: 999px;

        background: linear-gradient(90deg, var(--ord-maroon), var(--ord-tan));
    }

    .sl-orders-page .sl-map-point {
        position: absolute;
        top: 102px;

        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;

        border-radius: 999px;

        background: var(--ord-maroon);
        color: #FFFFFF;

        font-size: 11px;
        font-weight: 950;
    }

    .sl-orders-page .sl-map-point.is-start {
        left: 38px;
    }

    .sl-orders-page .sl-map-point.is-current {
        left: 48%;
        background: var(--ord-tan);
        color: var(--ord-maroon);
    }

    .sl-orders-page .sl-map-point.is-end {
        right: 38px;
    }

    .sl-orders-page .sl-tracking-map > div:last-child {
        position: absolute;
        left: 22px;
        right: 22px;
        bottom: 20px;

        padding: 14px;

        border: 1px solid var(--ord-border);
        border-radius: 16px;

        background: rgba(255, 253, 249, 0.88);
        backdrop-filter: blur(10px);
    }

    .sl-orders-page .sl-tracking-map strong {
        display: block;

        color: var(--ord-text);

        font-size: 13px;
        font-weight: 950;
    }

    .sl-orders-page .sl-tracking-map small {
        display: block;
        margin-top: 4px;

        color: var(--ord-muted);

        font-size: 10px;
        font-weight: 700;
    }

    .sl-orders-page .sl-timeline {
        display: grid;
        gap: 0;

        margin: 0;
        padding: 18px 22px 22px;

        list-style: none;
    }

    .sl-orders-page .sl-timeline li {
        position: relative;

        display: grid;
        grid-template-columns: 34px minmax(0, 1fr);
        gap: 12px;

        padding-bottom: 18px;
    }

    .sl-orders-page .sl-timeline li::before {
        position: absolute;
        left: 16px;
        top: 34px;
        bottom: 0;

        width: 2px;

        background: var(--ord-border);

        content: "";
    }

    .sl-orders-page .sl-timeline li:last-child {
        padding-bottom: 0;
    }

    .sl-orders-page .sl-timeline li:last-child::before {
        display: none;
    }

    .sl-orders-page .sl-timeline li > span {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;

        border: 1px solid var(--ord-border);
        border-radius: 999px;

        background: var(--ord-bg-soft);
        color: var(--ord-muted);

        font-size: 11px;
        font-weight: 950;
    }

    .sl-orders-page .sl-timeline li.is-complete > span {
        background: var(--ord-maroon);
        border-color: var(--ord-maroon);
        color: #FFFFFF;
    }

    .sl-orders-page .sl-timeline strong {
        color: var(--ord-text);

        font-size: 12px;
        font-weight: 950;
    }

    .sl-orders-page .sl-timeline small {
        display: block;
        margin-top: 3px;

        color: var(--ord-muted);

        font-size: 10px;
        font-weight: 800;
    }

    .sl-orders-page .sl-timeline p {
        margin: 5px 0 0;

        color: var(--ord-muted);

        font-size: 11px;
        line-height: 1.55;
    }

    .sl-orders-page .sl-order-progress {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;

        padding: 20px 22px;
    }

    .sl-orders-page .sl-order-progress div {
        display: grid;
        gap: 10px;

        min-height: 118px;
        padding: 14px;

        border: 1px solid var(--ord-border);
        border-radius: 17px;

        background: var(--ord-bg-soft);
    }

    .sl-orders-page .sl-order-progress div.is-done {
        background: #F3E4DE;
        border-color: #E6C7BE;
    }

    .sl-orders-page .sl-order-progress span {
        display: grid;
        width: 32px;
        height: 32px;
        place-items: center;

        border-radius: 12px;

        background: var(--ord-maroon);
        color: #FFFFFF;

        font-size: 11px;
        font-weight: 950;
    }

    .sl-orders-page .sl-order-progress strong {
        color: var(--ord-text);

        font-size: 11px;
        font-weight: 950;
        line-height: 1.35;
    }

    .sl-orders-page .sl-action-panel {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto auto;
        align-items: center;
        gap: 13px;

        margin: 0 22px 22px;
        padding: 16px;

        border: 1px solid var(--ord-border);
        border-radius: 18px;

        background:
            radial-gradient(circle at 96% 8%, rgba(193, 151, 113, 0.13), transparent 30%),
            var(--ord-bg-soft);
    }

    .sl-orders-page .sl-action-panel-icon {
        display: grid;
        width: 46px;
        height: 46px;
        place-items: center;

        border-radius: 15px;

        background: var(--ord-maroon);
        color: #FFFFFF;
    }

    .sl-orders-page .sl-action-panel-icon svg {
        width: 22px;
        height: 22px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sl-orders-page .sl-action-panel strong {
        color: var(--ord-text);

        font-size: 13px;
        font-weight: 950;
    }

    .sl-orders-page .sl-action-panel p {
        margin: 4px 0 0;

        color: var(--ord-muted);

        font-size: 11px;
        line-height: 1.55;
    }

    .sl-orders-page .sl-order-detail-side .sl-card {
        display: grid;
        gap: 16px;

        padding: 20px;
    }

    .sl-orders-page .sl-detail-list,
    .sl-orders-page .sl-price-list {
        display: grid;
        gap: 10px;

        margin: 0;
    }

    .sl-orders-page .sl-price-list .is-total {
        background: #F3E4DE;
        border-color: #E6C7BE;
    }

    .sl-orders-page .sl-price-list .is-total dd {
        color: var(--ord-maroon);
        font-size: 15px;
    }

    .sl-orders-page .sl-payment-note {
        display: block;

        padding: 12px;

        border: 1px solid #CFE8DA;
        border-radius: 14px;

        background: var(--ord-success-soft);
        color: var(--ord-success);

        font-size: 11px;
        font-weight: 900;
    }

    .sl-orders-page .sl-no-results {
        padding: 34px;

        color: var(--ord-muted);

        font-size: 13px;
        font-weight: 800;
        text-align: center;
    }

    @media (max-width: 1180px) {
        .sl-orders-page .sl-logistics-layout,
        .sl-orders-page .sl-tracking-layout,
        .sl-orders-page .sl-order-detail-layout {
            grid-template-columns: 1fr;
        }

        .sl-orders-page .sl-order-detail-side {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 820px) {
        .sl-orders-page .sl-page-toolbar,
        .sl-orders-page .sl-table-toolbar,
        .sl-orders-page .sl-order-filters {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .sl-orders-page .sl-search-input,
        .sl-orders-page .sl-select,
        .sl-orders-page .sl-input,
        .sl-orders-page .sl-btn,
        .sl-orders-page .sl-tracking-search {
            width: 100%;
            max-width: none;
        }

        .sl-orders-page .sl-mini-stats,
        .sl-orders-page .sl-form-grid,
        .sl-orders-page .sl-shipment-meta,
        .sl-orders-page .sl-order-progress,
        .sl-orders-page .sl-order-detail-side {
            grid-template-columns: 1fr;
        }

        .sl-orders-page .sl-span-2 {
            grid-column: auto;
        }

        .sl-orders-page .sl-package-summary,
        .sl-orders-page .sl-provider-option,
        .sl-orders-page .sl-action-panel {
            grid-template-columns: 1fr;
            align-items: flex-start;
        }

        .sl-orders-page .sl-provider-meta {
            text-align: left;
        }

        .sl-orders-page .sl-form-actions {
            flex-direction: column;
        }
    }

    html.dark .sl-orders-page .sl-page-toolbar,
    html.dark .sl-orders-page .sl-card,
    html.dark .sl-orders-page .sl-card-head,
    html.dark .sl-orders-page .sl-package-summary,
    html.dark .sl-orders-page .sl-address-card,
    html.dark .sl-orders-page .sl-provider-option,
    html.dark .sl-orders-page .sl-mini-stats div,
    html.dark .sl-orders-page .sl-table-toolbar,
    html.dark .sl-orders-page .sl-order-filters,
    html.dark .sl-orders-page .sl-order-tabs,
    html.dark .sl-orders-page .sl-shipment-head,
    html.dark .sl-orders-page .sl-shipment-meta div,
    html.dark .sl-orders-page .sl-detail-list div,
    html.dark .sl-orders-page .sl-price-list div,
    html.dark .sl-orders-page .sl-tracking-map,
    html.dark .sl-orders-page .sl-order-progress div,
    html.dark .sl-orders-page .sl-action-panel {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;

        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .sl-orders-page .sl-page-toolbar h2,
    html.dark .sl-orders-page .sl-card-head h2,
    html.dark .sl-orders-page .sl-card h3,
    html.dark .sl-orders-page .sl-package-summary strong,
    html.dark .sl-orders-page .sl-address-card strong,
    html.dark .sl-orders-page .sl-provider-main strong,
    html.dark .sl-orders-page .sl-provider-meta strong,
    html.dark .sl-orders-page .sl-table td strong,
    html.dark .sl-orders-page .sl-shipment-head h2,
    html.dark .sl-orders-page .sl-shipment-meta dd,
    html.dark .sl-orders-page .sl-detail-list dd,
    html.dark .sl-orders-page .sl-price-list dd,
    html.dark .sl-orders-page .sl-timeline strong,
    html.dark .sl-orders-page .sl-order-progress strong,
    html.dark .sl-orders-page .sl-action-panel strong {
        color: #F5EFE8 !important;
    }

    html.dark .sl-orders-page .sl-page-toolbar p,
    html.dark .sl-orders-page .sl-card-head p,
    html.dark .sl-orders-page .sl-package-summary span,
    html.dark .sl-orders-page .sl-package-summary small,
    html.dark .sl-orders-page .sl-address-card span,
    html.dark .sl-orders-page .sl-address-card small,
    html.dark .sl-orders-page .sl-provider-main small,
    html.dark .sl-orders-page .sl-provider-meta small,
    html.dark .sl-orders-page .sl-table td,
    html.dark .sl-orders-page .sl-shipment-head p,
    html.dark .sl-orders-page .sl-timeline small,
    html.dark .sl-orders-page .sl-timeline p,
    html.dark .sl-orders-page .sl-action-panel p {
        color: #C8B7AD !important;
    }

    html.dark .sl-orders-page .sl-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .sl-orders-page .sl-btn-primary,
    html.dark .sl-orders-page .sl-tracking-search button {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .sl-orders-page .sl-btn-ghost,
    html.dark .sl-orders-page .sl-btn-soft,
    html.dark .sl-orders-page .sl-select,
    html.dark .sl-orders-page .sl-input,
    html.dark .sl-orders-page .sl-search-input input,
    html.dark .sl-orders-page .sl-field input,
    html.dark .sl-orders-page .sl-field select,
    html.dark .sl-orders-page .sl-field textarea,
    html.dark .sl-orders-page .sl-table thead {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-orders-page .sl-table th,
    html.dark .sl-orders-page .sl-table td {
        border-color: #3B2E27 !important;
    }

    html.dark .sl-orders-page .sl-table tbody tr:hover td {
        background: #2D1414 !important;
    }

    html.dark .sl-orders-page .sl-order-tabs a {
        color: #C8B7AD !important;
    }

    html.dark .sl-orders-page .sl-order-tabs a:hover,
    html.dark .sl-orders-page .sl-order-tabs a.is-active {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-orders-page .sl-order-thumb,
    html.dark .sl-orders-page .sl-provider-logo {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }


    /* =========================================================
       ORDER FILTER BAR — FINAL ALIGNMENT FIX
       Fixes search icon overlapping the payment methods select.
    ========================================================= */

    .sl-orders-page .sl-order-filters {
        display: grid !important;
        grid-template-columns: minmax(280px, 1fr) minmax(210px, 240px) minmax(160px, 180px) auto !important;
        align-items: center !important;
        gap: 12px !important;

        width: 100% !important;
        padding: 14px !important;

        border: 1px solid var(--ord-border) !important;
        border-radius: 22px !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.12), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;

        box-shadow: var(--ord-shadow-soft) !important;
    }

    .sl-orders-page .sl-order-filters > * {
        position: relative !important;
        z-index: 1 !important;
        min-width: 0 !important;
        margin: 0 !important;
    }

    .sl-orders-page .sl-order-filters > .sl-search-input {
        display: block !important;
        width: 100% !important;
        max-width: none !important;
        height: 46px !important;
        overflow: visible !important;
    }

    .sl-orders-page .sl-order-filters > .sl-search-input svg {
        position: absolute !important;
        top: 50% !important;
        left: 15px !important;
        z-index: 2 !important;

        width: 16px !important;
        height: 16px !important;

        margin: 0 !important;

        color: var(--ord-muted-2) !important;

        fill: none !important;
        stroke: currentColor !important;
        stroke-width: 1.8 !important;
        stroke-linecap: round !important;
        stroke-linejoin: round !important;

        pointer-events: none !important;
        transform: translateY(-50%) !important;
    }

    .sl-orders-page .sl-order-filters > .sl-search-input input,
    .sl-orders-page .sl-order-filters > .sl-select,
    .sl-orders-page .sl-order-filters > .sl-input,
    .sl-orders-page .sl-order-filters > .sl-btn {
        display: flex !important;
        width: 100% !important;
        max-width: none !important;
        min-width: 0 !important;
        height: 46px !important;
        min-height: 46px !important;
        margin: 0 !important;

        border: 1px solid var(--ord-border) !important;
        border-radius: 15px !important;

        background: var(--ord-bg-soft) !important;
        color: var(--ord-text) !important;

        font-size: 12px !important;
        font-weight: 800 !important;
        line-height: 1 !important;

        box-shadow: none !important;
        outline: none !important;
    }

    .sl-orders-page .sl-order-filters > .sl-search-input input {
        padding: 0 14px 0 44px !important;
    }

    .sl-orders-page .sl-order-filters > .sl-select,
    .sl-orders-page .sl-order-filters > .sl-input {
        padding: 0 13px !important;
    }

    .sl-orders-page .sl-order-filters > select.sl-select {
        appearance: auto !important;
        -webkit-appearance: menulist !important;
        cursor: pointer !important;
    }

    .sl-orders-page .sl-order-filters > .sl-btn {
        width: auto !important;
        min-width: 92px !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 18px !important;
    }

    .sl-orders-page .sl-order-filters > .sl-search-input input:focus,
    .sl-orders-page .sl-order-filters > .sl-select:focus,
    .sl-orders-page .sl-order-filters > .sl-input:focus {
        border-color: var(--ord-tan) !important;
        background: #FFFFFF !important;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08) !important;
    }

    @media (max-width: 980px) {
        .sl-orders-page .sl-order-filters {
            grid-template-columns: minmax(0, 1fr) minmax(180px, 220px) !important;
        }

        .sl-orders-page .sl-order-filters > .sl-btn {
            width: 100% !important;
        }
    }

    @media (max-width: 680px) {
        .sl-orders-page .sl-order-filters {
            grid-template-columns: 1fr !important;
            align-items: stretch !important;
            padding: 14px !important;
        }

        .sl-orders-page .sl-order-filters > .sl-btn {
            width: 100% !important;
        }
    }

    html.dark .sl-orders-page .sl-order-filters,
    html.dark .sl-orders-page .sl-order-filters > .sl-search-input input,
    html.dark .sl-orders-page .sl-order-filters > .sl-select,
    html.dark .sl-orders-page .sl-order-filters > .sl-input {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

</style>

<div class="sl-page sl-orders-page">
    @if ($isLogistics && $currentMode === 'couriers')
        <div class="sl-page-toolbar">
            <div>
                <span class="sl-eyebrow">Logistics</span>
                <h2>Request Pickup</h2>
                <p>Choose a logistics provider after the package is prepared. Logistics assigns riders.</p>
            </div>

            <a href="{{ route('seller.orders', ['status' => 'ready-pickup']) }}" class="sl-btn sl-btn-ghost">
                Ready Pickup Orders
            </a>
        </div>

        <div class="sl-logistics-layout">
            <section class="sl-card sl-logistics-order">
                <header class="sl-card-head">
                    <div>
                        <span class="sl-eyebrow">Prepared Package</span>
                        <h2>Order #{{ data_get($order, 'id') }}</h2>
                        <p>Confirm the parcel information before assigning logistics.</p>
                    </div>

                    <span class="sl-status is-info">Ready Pickup</span>
                </header>

                <div class="sl-package-summary">
                    <div class="sl-order-thumb">
                        {{ mb_strtoupper(mb_substr(data_get($order, 'product', 'P'), 0, 1)) }}
                    </div>

                    <div>
                        <strong>{{ data_get($order, 'product') }}</strong>
                        <span>{{ data_get($order, 'buyer') }} · Qty {{ data_get($order, 'quantity', 1) }}</span>
                        <small>Package: 68 × 15 × 45 cm · 3.2 kg</small>
                    </div>

                    <strong>₱{{ number_format((float) data_get($order, 'total', 0), 2) }}</strong>
                </div>

                <div class="sl-address-card">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0z"/>
                        <circle cx="12" cy="10" r="2"/>
                    </svg>

                    <div>
                        <span>Pickup address</span>
                        <strong>LIKHAE Studio, 24 Rizal Street</strong>
                        <small>Santa Cruz, Laguna 4009</small>
                    </div>
                </div>

                <div class="sl-address-card">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0z"/>
                        <circle cx="12" cy="10" r="2"/>
                    </svg>

                    <div>
                        <span>Delivery area</span>
                        <strong>{{ data_get($order, 'buyer') }}</strong>
                        <small>Calamba City, Laguna 4027</small>
                    </div>
                </div>
            </section>

            <form
                class="sl-card sl-courier-select"
                data-demo-form
                data-success="Courier confirmed for Order #{{ data_get($order, 'id') }}."
            >
                <header class="sl-card-head">
                    <div>
                        <span class="sl-eyebrow">Available Logistics</span>
                        <h2>Select Logistics Provider</h2>
                        <p>Availability is based on your pickup area and package size.</p>
                    </div>
                </header>

                <div class="sl-provider-list">
                    @foreach ($providers as $provider)
                        @php
                            $logo = collect(explode(' ', $provider['name']))
                                ->filter()
                                ->map(fn ($word) => mb_substr($word, 0, 1))
                                ->join('');
                        @endphp

                        <label class="sl-provider-option">
                            <input
                                type="radio"
                                name="provider"
                                value="{{ $provider['name'] }}"
                                @checked($loop->first)
                            >

                            <span class="sl-radio-mark"></span>

                            <span class="sl-provider-logo">
                                {{ $logo }}
                            </span>

                            <span class="sl-provider-main">
                                <strong>{{ $provider['name'] }}</strong>
                                <small>Pickup: {{ $provider['pickup'] }}</small>
                            </span>

                            <span class="sl-provider-meta">
                                <small>{{ $provider['eta'] }}</small>
                                <strong>{{ $provider['rate'] }}</strong>
                            </span>

                            @if ($provider['tag'])
                                <span class="sl-provider-tag">
                                    {{ $provider['tag'] }}
                                </span>
                            @endif
                        </label>
                    @endforeach
                </div>

                <div class="sl-form-grid">
                    <label class="sl-field">
                        <span>Pickup date</span>
                        <input type="date" value="2026-09-05" required>
                    </label>

                    <label class="sl-field">
                        <span>Pickup window</span>

                        <select required>
                            <option>10:00 AM – 12:00 PM</option>
                            <option>1:00 PM – 3:00 PM</option>
                            <option>3:00 PM – 5:00 PM</option>
                        </select>
                    </label>

                    <label class="sl-field sl-span-2">
                        <span>Pickup note</span>

                        <textarea rows="3" placeholder="Optional instructions for the courier">Package is sealed and available at the store counter.</textarea>
                    </label>
                </div>

                <div class="sl-form-actions">
                    <button
                        type="button"
                        class="sl-btn sl-btn-ghost"
                        data-demo-action="Courier selection saved."
                    >
                        Confirm Courier
                    </button>

                    <button type="submit" class="sl-btn sl-btn-primary">
                        Submit Pickup Request
                    </button>
                </div>
            </form>
        </div>
    @elseif ($isLogistics && $currentMode === 'pickups')
        <div class="sl-page-toolbar">
            <div>
                <span class="sl-eyebrow">Logistics</span>
                <h2>Pickup Requests</h2>
                <p>Monitor scheduled courier pickups and handover readiness.</p>
            </div>

            <a href="{{ route('seller.logistics', ['view' => 'couriers']) }}" class="sl-btn sl-btn-primary">
                New Pickup Request
            </a>
        </div>

        <section class="sl-mini-stats" aria-label="Pickup summary">
            <div><span>Scheduled Today</span><strong>4</strong></div>
            <div><span>Awaiting Courier</span><strong>2</strong></div>
            <div><span>Picked Up</span><strong>12</strong></div>
            <div><span>Exceptions</span><strong>1</strong></div>
        </section>

        <section class="sl-card">
            <div class="sl-table-toolbar">
                <div class="sl-search-input">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-4-4"/>
                    </svg>

                    <input type="search" placeholder="Search pickup or order">
                </div>

                <select class="sl-select">
                    <option>All schedules</option>
                    <option>Today</option>
                    <option>Tomorrow</option>
                    <option>Completed</option>
                </select>
            </div>

            <div class="sl-table-wrap">
                <table class="sl-table">
                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Orders</th>
                            <th>Provider</th>
                            <th>Pickup Window</th>
                            <th>Courier</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pickups as $pickup)
                            <tr>
                                <td><strong>{{ $pickup['id'] }}</strong></td>
                                <td>{{ $pickup['orders'] }}</td>
                                <td>{{ $pickup['provider'] }}</td>
                                <td>{{ $pickup['window'] }}</td>
                                <td>{{ $pickup['courier'] }}</td>

                                <td>
                                    <span class="sl-status {{ $pickup['tone'] }}">
                                        {{ $pickup['status'] }}
                                    </span>
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="sl-btn sl-btn-ghost sl-btn-sm"
                                        data-demo-action="Pickup {{ $pickup['id'] }} opened."
                                    >
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @elseif ($isLogistics && $currentMode === 'tracking')
        <div class="sl-page-toolbar">
            <div>
                <span class="sl-eyebrow">Logistics</span>
                <h2>Shipment Tracking</h2>
                <p>Monitor courier movement from pickup request to buyer delivery.</p>
            </div>

            <div class="sl-search-input sl-tracking-search">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-4-4"/>
                </svg>

                <input value="#{{ data_get($order, 'id') }}" aria-label="Tracking number">

                <button type="button" data-demo-action="Shipment tracking refreshed.">
                    Track
                </button>
            </div>
        </div>

        <div class="sl-tracking-layout">
            <section class="sl-card sl-shipment-summary">
                <div class="sl-shipment-head">
                    <div>
                        <span class="sl-eyebrow">Order #{{ data_get($order, 'id') }}</span>
                        <h2>{{ data_get($order, 'product') }}</h2>
                        <p>Tracking no. JT839201476PH</p>
                    </div>

                    <span class="sl-status is-info">In Transit</span>
                </div>

                <dl class="sl-shipment-meta">
                    <div><dt>Logistics</dt><dd>J&T Express</dd></div>
                    <div><dt>Courier</dt><dd>Juan Rider</dd></div>
                    <div><dt>Buyer</dt><dd>{{ data_get($order, 'buyer') }}</dd></div>
                    <div><dt>Estimated Delivery</dt><dd>September 5, 2026</dd></div>
                </dl>

                <div class="sl-tracking-map">
                    <span class="sl-map-route"></span>
                    <span class="sl-map-point is-start">S</span>
                    <span class="sl-map-point is-current">●</span>
                    <span class="sl-map-point is-end">B</span>

                    <div>
                        <strong>Parcel is moving to Calamba Hub</strong>
                        <small>Last updated 18 minutes ago</small>
                    </div>
                </div>
            </section>

            <section class="sl-card">
                <header class="sl-card-head">
                    <div>
                        <span class="sl-eyebrow">Live Progress</span>
                        <h2>Shipment Timeline</h2>
                        <p>Latest scan and delivery milestones.</p>
                    </div>
                </header>

                <ol class="sl-timeline">
                    @foreach ($trackingEvents as $event)
                        <li class="{{ $event['done'] ? 'is-complete' : '' }}">
                            <span>{{ $event['done'] ? '✓' : '' }}</span>

                            <div>
                                <strong>{{ $event['title'] }}</strong>
                                <small>{{ $event['time'] }}</small>
                                <p>{{ $event['description'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>
    @elseif (!$isLogistics && $currentMode === 'show')
        <div class="sl-page-toolbar">
            <div>
                <span class="sl-eyebrow">Order Details</span>
                <h2>Order #{{ data_get($order, 'id') }}</h2>
                <p>Placed {{ data_get($order, 'date') }}</p>
            </div>

            <a
                href="{{ route('seller.orders', ['status' => data_get($order, 'status_key', 'all')]) }}"
                class="sl-btn sl-btn-ghost"
            >
                Back to Orders
            </a>
        </div>

        <div class="sl-order-detail-layout">
            <div class="sl-order-detail-main">
                <section class="sl-card">
                    <header class="sl-card-head">
                        <div>
                            <h2>Ordered Product</h2>
                            <p>Items included in this purchase.</p>
                        </div>

                        <span class="sl-status is-warning">
                            {{ data_get($order, 'status') }}
                        </span>
                    </header>

                    <div class="sl-package-summary">
                        <div class="sl-order-thumb">
                            {{ mb_strtoupper(mb_substr(data_get($order, 'product', 'P'), 0, 1)) }}
                        </div>

                        <div>
                            <strong>{{ data_get($order, 'product') }}</strong>
                            <span>Variation: {{ data_get($order, 'variant', 'Standard') }}</span>
                            <small>
                                ₱{{ number_format((float) data_get($order, 'total', 0) / max((int) data_get($order, 'quantity', 1), 1), 2) }}
                                × {{ data_get($order, 'quantity', 1) }}
                            </small>
                        </div>

                        <strong>
                            ₱{{ number_format((float) data_get($order, 'total', 0), 2) }}
                        </strong>
                    </div>
                </section>

                <section class="sl-card">
                    <header class="sl-card-head">
                        <div>
                            <h2>Fulfillment Progress</h2>
                            <p>Complete each action in sequence.</p>
                        </div>
                    </header>

                    <div class="sl-order-progress">
                        <div class="is-done">
                            <span>✓</span>
                            <strong>Order received</strong>
                        </div>

                        <div>
                            <span>2</span>
                            <strong>Prepare package</strong>
                        </div>

                        <div>
                            <span>3</span>
                            <strong>Assign courier</strong>
                        </div>

                        <div>
                            <span>4</span>
                            <strong>Hand over parcel</strong>
                        </div>
                    </div>

                    <div class="sl-action-panel">
                        <span class="sl-action-panel-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5zM4 7.5l8 4.5 8-4.5M12 12v9"/>
                            </svg>
                        </span>

                        <div>
                            <strong>Prepare this order</strong>
                            <p>Verify the product, pack it securely, and print the shipping waybill.</p>
                        </div>

                        <button
                            type="button"
                            class="sl-btn sl-btn-ghost"
                            data-demo-action="Waybill ready to print."
                        >
                            Print Waybill
                        </button>

                        <button
                            type="button"
                            class="sl-btn sl-btn-primary"
                            data-order-action="prepare"
                        >
                            Mark as Prepared
                        </button>
                    </div>
                </section>
            </div>

            <aside class="sl-order-detail-side">
                <section class="sl-card">
                    <h3>Buyer Information</h3>

                    <dl class="sl-detail-list">
                        <div><dt>Name</dt><dd>{{ data_get($order, 'buyer') }}</dd></div>
                        <div><dt>Contact</dt><dd>09•• ••• ••82</dd></div>
                        <div><dt>Delivery Address</dt><dd>Calamba City, Laguna 4027</dd></div>
                    </dl>

                    <a href="{{ route('seller.messages') }}" class="sl-btn sl-btn-soft sl-btn-block">
                        Message Buyer
                    </a>
                </section>

                <section class="sl-card">
                    <h3>Payment Summary</h3>

                    <dl class="sl-price-list">
                        <div>
                            <dt>Item subtotal</dt>
                            <dd>₱{{ number_format((float) data_get($order, 'total', 0), 2) }}</dd>
                        </div>

                        <div>
                            <dt>Shipping fee</dt>
                            <dd>₱145.00</dd>
                        </div>

                        <div>
                            <dt>Platform voucher</dt>
                            <dd>−₱100.00</dd>
                        </div>

                        <div class="is-total">
                            <dt>Buyer paid</dt>
                            <dd>₱{{ number_format((float) data_get($order, 'total', 0) + 45, 2) }}</dd>
                        </div>
                    </dl>

                    <span class="sl-payment-note">
                        {{ data_get($order, 'payment', 'GCash') }} · Payment confirmed
                    </span>
                </section>
            </aside>
        </div>
    @else
        <div class="sl-page-toolbar">
            <div>
                <span class="sl-eyebrow">Order Management</span>
                <h2>Manage Orders</h2>
                <p>Process orders on time and keep buyers informed.</p>
            </div>

            <button
                type="button"
                class="sl-btn sl-btn-ghost"
                data-demo-action="Orders exported."
            >
                Export Orders
            </button>
        </div>

        <section class="sl-order-tabs" aria-label="Order filters">
            @foreach ($orderTabs as $key => $label)
                <a
                    href="{{ route('seller.orders', ['status' => $key]) }}"
                    class="{{ $currentStatus === $key ? 'is-active' : '' }}"
                >
                    {{ $label }}

                    @if (in_array($key, ['to-process', 'to-prepare'], true))
                        <span>{{ $key === 'to-process' ? 5 : 8 }}</span>
                    @endif
                </a>
            @endforeach
        </section>

        <section class="sl-card sl-order-filters">
            <div class="sl-search-input">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-4-4"/>
                </svg>

                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search order, buyer, or product"
                    data-order-search
                >
            </div>

            <select class="sl-select">
                <option>All payment methods</option>
                <option>Cash on Delivery</option>
                <option>GCash</option>
                <option>Maya</option>
                <option>Credit Card</option>
            </select>

            <input class="sl-input" type="date" aria-label="Order date">

            <button
                type="button"
                class="sl-btn sl-btn-soft"
                data-demo-action="Order filters applied."
            >
                Apply
            </button>
        </section>

        <div class="sl-order-list" data-order-list>
            @forelse ($visibleOrders as $item)
                <x-seller.order-card :order="$item" />
            @empty
                <section class="sl-card">
                    <x-seller.empty-state
                        title="No orders in this stage"
                        message="Orders will appear here when they enter this fulfillment stage."
                        action="View All Orders"
                        :href="route('seller.orders')"
                    />
                </section>
            @endforelse
        </div>

        <div class="sl-no-results sl-card" data-order-empty hidden>
            No orders match your search.
        </div>
    @endif
</div>
@endsection