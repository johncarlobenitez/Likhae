@extends('logistics.app')

@section('title', 'Parcel Details — LIKHAE Logistics')

@php
    $parcelRecords = [
        1001 => [
            'id' => 1001,
            'tracking' => 'LH-2026-1001',
            'order' => 'ORD-2026-1045',
            'seller' => 'Habing Lokal',
            'buyer' => 'Juan Dela Cruz',
            'contact' => '0917 123 4567',
            'address' => '21 Rizal Street, Brgy. Bubukal, Santa Cruz, Laguna',
            'destination' => 'Santa Cruz, Laguna',
            'area' => 'Unassigned',
            'rider' => 'Not assigned',
            'payment' => 'Cash on Delivery',
            'value' => 1700,
            'status' => 'Waiting for Sorting',
            'status_key' => 'waiting_sorting',
            'condition' => 'Good',
            'received_from' => 'Seller / Drop-off',
            'received_at' => 'September 1, 2026 · 10:32 AM',
            'notes' => 'Parcel received in good condition. Ready for sorting.',
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
        ],

        1002 => [
            'id' => 1002,
            'tracking' => 'LH-2026-1002',
            'order' => 'ORD-2026-1046',
            'seller' => 'Gawang Laguna',
            'buyer' => 'Maria Santos',
            'contact' => '0918 456 7712',
            'address' => '18 Riverside Street, Brgy. Sampaloc, Pagsanjan, Laguna',
            'destination' => 'Pagsanjan, Laguna',
            'area' => 'Area B',
            'rider' => 'Not assigned',
            'payment' => 'GCash',
            'value' => 980,
            'status' => 'Awaiting Rider',
            'status_key' => 'awaiting_rider',
            'condition' => 'Good',
            'received_from' => 'Seller / Drop-off',
            'received_at' => 'September 1, 2026 · 10:15 AM',
            'notes' => 'Sorted under Area B. Waiting for available rider.',
            'items' => [
                [
                    'name' => 'Handmade Woven Basket',
                    'variation' => 'Brown / Small',
                    'quantity' => 1,
                    'price' => 980,
                ],
            ],
        ],

        1003 => [
            'id' => 1003,
            'tracking' => 'LH-2026-1003',
            'order' => 'ORD-2026-1047',
            'seller' => 'Lokal Finds',
            'buyer' => 'Ana Reyes',
            'contact' => '0919 845 2201',
            'address' => '15 Lopez Avenue, Brgy. Batong Malake, Los Baños, Laguna',
            'destination' => 'Los Baños, Laguna',
            'area' => 'Area C',
            'rider' => 'Rider 03',
            'payment' => 'GCash',
            'value' => 2150,
            'status' => 'Rider Assigned',
            'status_key' => 'assigned',
            'condition' => 'Good',
            'received_from' => 'Courier Transfer',
            'received_at' => 'September 1, 2026 · 9:48 AM',
            'notes' => 'Assigned to Rider 03 for Area C.',
            'items' => [
                [
                    'name' => 'Artisan Shoulder Bag',
                    'variation' => 'Maroon / Large',
                    'quantity' => 1,
                    'price' => 2150,
                ],
            ],
        ],

        1004 => [
            'id' => 1004,
            'tracking' => 'LH-2026-1004',
            'order' => 'ORD-2026-1048',
            'seller' => 'LIKHAE Home',
            'buyer' => 'Carlo Mendoza',
            'contact' => '0920 334 8821',
            'address' => '88 National Highway, Brgy. Real, Calamba, Laguna',
            'destination' => 'Calamba, Laguna',
            'area' => 'Area D',
            'rider' => 'Rider 04',
            'payment' => 'Cash on Delivery',
            'value' => 980,
            'status' => 'Out for Delivery',
            'status_key' => 'out_for_delivery',
            'condition' => 'Good',
            'received_from' => 'Courier Transfer',
            'received_at' => 'September 1, 2026 · 8:20 AM',
            'notes' => 'Parcel released to Rider 04 and currently out for delivery.',
            'items' => [
                [
                    'name' => 'Decorative Native Tray',
                    'variation' => 'Natural / Medium',
                    'quantity' => 1,
                    'price' => 980,
                ],
            ],
        ],

        1005 => [
            'id' => 1005,
            'tracking' => 'LH-2026-1005',
            'order' => 'ORD-2026-1049',
            'seller' => 'Gawang Laguna',
            'buyer' => 'Sofia Garcia',
            'contact' => '0921 772 0019',
            'address' => '42 Provincial Road, Brgy. Pagsawitan, Santa Cruz, Laguna',
            'destination' => 'Santa Cruz, Laguna',
            'area' => 'Area A',
            'rider' => 'Rider 01',
            'payment' => 'GCash',
            'value' => 1320,
            'status' => 'Delivered',
            'status_key' => 'delivered',
            'condition' => 'Good',
            'received_from' => 'Seller / Drop-off',
            'received_at' => 'September 1, 2026 · 7:55 AM',
            'notes' => 'Successfully delivered to recipient.',
            'items' => [
                [
                    'name' => 'Handcrafted Table Runner',
                    'variation' => 'Cream / 180cm',
                    'quantity' => 1,
                    'price' => 1320,
                ],
            ],
        ],
    ];

    $parcelId = isset($id) ? (int) $id : 1001;
    $parcel = $parcelRecords[$parcelId] ?? $parcelRecords[1001];

    $statusTone = match($parcel['status_key']) {
        'waiting_sorting' => 'warning',
        'awaiting_rider' => 'maroon',
        'assigned' => 'maroon',
        'out_for_delivery' => 'maroon',
        'delivered' => 'success',
        default => 'neutral',
    };

    $nextAction = match($parcel['status_key']) {
        'waiting_sorting' => [
            'label' => 'Continue to Sorting',
            'route' => route(
                'logistics.sorting.index',
                ['tracking' => $parcel['tracking']]
            ),
        ],

        'awaiting_rider' => [
            'label' => 'Assign Rider',
            'route' => route(
                'logistics.assignments.assign',
                $parcel['id']
            ),
        ],

        'assigned',
        'out_for_delivery' => [
            'label' => 'Track Parcel',
            'route' => route(
                'logistics.parcels.tracking',
                ['tracking' => $parcel['tracking']]
            ),
        ],

        default => [
            'label' => 'View Tracking',
            'route' => route(
                'logistics.parcels.tracking',
                ['tracking' => $parcel['tracking']]
            ),
        ],
    };

    $timeline = [
        [
            'title' => 'Parcel Received',
            'description' => 'Parcel checked into the LIKHAE sorting center.',
            'time' => $parcel['received_at'],
            'done' => true,
        ],
        [
            'title' => 'Sorted by Destination',
            'description' => $parcel['area'] === 'Unassigned'
                ? 'Waiting for destination-area sorting.'
                : 'Parcel assigned to ' . $parcel['area'] . '.',
            'time' => $parcel['area'] === 'Unassigned'
                ? null
                : 'September 1, 2026 · 10:02 AM',
            'done' => $parcel['status_key'] !== 'waiting_sorting',
        ],
        [
            'title' => 'Rider Assigned',
            'description' => $parcel['rider'] === 'Not assigned'
                ? 'Waiting for an available rider.'
                : $parcel['rider'] . ' received the delivery assignment.',
            'time' => $parcel['rider'] === 'Not assigned'
                ? null
                : 'September 1, 2026 · 10:14 AM',
            'done' => in_array(
                $parcel['status_key'],
                ['assigned', 'out_for_delivery', 'delivered'],
                true
            ),
        ],
        [
            'title' => 'Out for Delivery',
            'description' => 'Parcel has left the sorting center for delivery.',
            'time' => in_array(
                $parcel['status_key'],
                ['out_for_delivery', 'delivered'],
                true
            )
                ? 'September 1, 2026 · 11:10 AM'
                : null,
            'done' => in_array(
                $parcel['status_key'],
                ['out_for_delivery', 'delivered'],
                true
            ),
        ],
        [
            'title' => 'Delivered',
            'description' => 'Parcel successfully delivered to the recipient.',
            'time' => $parcel['status_key'] === 'delivered'
                ? 'September 1, 2026 · 12:18 PM'
                : null,
            'done' => $parcel['status_key'] === 'delivered',
        ],
    ];

    $icons = [
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
        'arrow' => '
            <path d="M5 12h14"/>
            <path d="m14 7 5 5-5 5"/>
        ',
        'copy' => '
            <rect x="9" y="9" width="10" height="10" rx="2"/>
            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --pd-bg: #FBF7F2;
        --pd-bg-soft: #F6EFE7;
        --pd-bg-warm: #F3E4DE;
        --pd-card: #FFFDF9;

        --pd-border: #EADCCC;
        --pd-border-strong: #DBCEC1;

        --pd-maroon: #561C17;
        --pd-maroon-2: #642920;
        --pd-maroon-dark: #3E130F;

        --pd-text: #3B211B;
        --pd-text-dark: #1C160F;
        --pd-brown: #6C4936;
        --pd-muted: #987865;
        --pd-muted-light: #A99386;

        --pd-tan: #C19771;

        --pd-success: #256F4A;
        --pd-success-soft: #EAF7EF;

        --pd-warning: #9A5B11;
        --pd-warning-soft: #FFF6DE;

        --pd-danger: #B42318;
        --pd-danger-soft: #FCEBE9;

        --pd-shadow: 0 8px 24px rgba(86, 28, 23, 0.055);
        --pd-shadow-hover: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .pd-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--pd-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .pd-page * {
        box-sizing: border-box;
    }

    .pd-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        color: var(--pd-muted);
        font-size: 10px;
        font-weight: 750;
    }

    .pd-breadcrumb a {
        color: var(--pd-muted);
        text-decoration: none;
        transition: 150ms ease;
    }

    .pd-breadcrumb a:hover {
        color: var(--pd-maroon);
    }

    .pd-breadcrumb strong {
        color: var(--pd-text);
        font-weight: 900;
    }

    .pd-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 30px 34px;
        border: 1px solid var(--pd-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,0.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,0.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--pd-shadow);
    }

    .pd-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--pd-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: 0.20em;
        text-transform: uppercase;
    }

    .pd-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .pd-hero-title {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 10px;
    }

    .pd-hero h1 {
        margin: 0;
        color: var(--pd-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 64px);
        font-weight: 400;
        line-height: 0.94;
        letter-spacing: -0.055em;
    }

    .pd-hero p {
        margin: 12px 0 0;
        color: var(--pd-muted);
        font-size: 11px;
        line-height: 1.6;
    }

    .pd-status {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        gap: 6px;
        padding: 0 10px;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .pd-status::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .pd-status.is-warning {
        border-color: #EAD39A;
        background: var(--pd-warning-soft);
        color: var(--pd-warning);
    }

    .pd-status.is-maroon,
    .pd-status.is-neutral {
        border-color: #E6C7BE;
        background: var(--pd-bg-warm);
        color: var(--pd-maroon);
    }

    .pd-status.is-success {
        border-color: #CFE8DA;
        background: var(--pd-success-soft);
        color: var(--pd-success);
    }

    .pd-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .pd-btn {
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

    .pd-btn:hover {
        transform: translateY(-1px);
    }

    .pd-btn svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .pd-btn-primary {
        background: var(--pd-maroon);
        border-color: var(--pd-maroon);
        color: #FFFFFF;
        box-shadow: 0 10px 22px rgba(86,28,23,0.16);
    }

    .pd-btn-primary:hover {
        background: var(--pd-maroon-dark);
        border-color: var(--pd-maroon-dark);
    }

    .pd-btn-soft {
        background: var(--pd-card);
        border-color: var(--pd-border);
        color: var(--pd-maroon);
    }

    .pd-btn-soft:hover {
        background: var(--pd-bg-warm);
        border-color: var(--pd-tan);
    }

    .pd-main-grid {
        display: grid;
        grid-template-columns: minmax(0,1fr) 330px;
        gap: 18px;
        align-items: start;
    }

    .pd-stack {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .pd-card {
        overflow: hidden;
        border: 1px solid var(--pd-border);
        border-radius: 22px;
        background: var(--pd-card);
        box-shadow: var(--pd-shadow);
    }

    .pd-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--pd-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,0.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .pd-card-head h2 {
        margin: 0;
        color: var(--pd-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .pd-card-head p {
        margin: 7px 0 0;
        color: var(--pd-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .pd-overview-grid {
        display: grid;
        grid-template-columns: repeat(3,minmax(0,1fr));
        gap: 1px;
        background: var(--pd-border);
    }

    .pd-overview-item {
        min-width: 0;
        padding: 18px;
        background: var(--pd-card);
    }

    .pd-label {
        display: block;
        color: var(--pd-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.09em;
        text-transform: uppercase;
    }

    .pd-value {
        display: block;
        margin-top: 7px;
        color: var(--pd-text);
        font-size: 10px;
        font-weight: 900;
        line-height: 1.4;
    }

    .pd-body {
        padding: 18px 20px;
    }

    .pd-delivery-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 12px;
    }

    .pd-info-box {
        min-width: 0;
        padding: 14px;
        border: 1px solid var(--pd-border);
        border-radius: 15px;
        background: var(--pd-bg-soft);
    }

    .pd-info-row {
        display: flex;
        align-items: flex-start;
        gap: 11px;
    }

    .pd-info-icon,
    .pd-item-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--pd-bg-warm);
        color: var(--pd-maroon);
    }

    .pd-info-icon svg,
    .pd-item-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .pd-info-copy {
        min-width: 0;
    }

    .pd-info-copy strong {
        display: block;
        margin-top: 4px;
        color: var(--pd-text);
        font-size: 10px;
        font-weight: 900;
    }

    .pd-info-copy p {
        margin: 5px 0 0;
        color: var(--pd-muted);
        font-size: 8px;
        line-height: 1.5;
    }

    .pd-contact-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 10px;
        margin-top: 12px;
    }

    .pd-contact-box {
        padding: 12px;
        border: 1px solid var(--pd-border);
        border-radius: 13px;
        background: var(--pd-card);
    }

    .pd-contact-box strong {
        display: block;
        margin-top: 5px;
        color: var(--pd-text);
        font-size: 9px;
        font-weight: 900;
    }

    .pd-items {
        display: grid;
    }

    .pd-item {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
        border-bottom: 1px solid var(--pd-border);
    }

    .pd-item:last-child {
        border-bottom: 0;
    }

    .pd-item-main {
        min-width: 0;
    }

    .pd-item-main strong {
        display: block;
        color: var(--pd-text);
        font-size: 10px;
        font-weight: 900;
    }

    .pd-item-main span,
    .pd-item-price span {
        display: block;
        margin-top: 4px;
        color: var(--pd-muted);
        font-size: 8px;
        font-weight: 700;
    }

    .pd-item-price {
        text-align: right;
    }

    .pd-item-price strong {
        color: var(--pd-text);
        font-size: 10px;
        font-weight: 900;
    }

    .pd-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 20px;
        border-top: 1px solid var(--pd-border);
        background: var(--pd-bg-soft);
    }

    .pd-total span {
        color: var(--pd-muted);
        font-size: 10px;
        font-weight: 800;
    }

    .pd-total strong {
        color: var(--pd-maroon);
        font-size: 14px;
        font-weight: 950;
    }

    .pd-notes {
        padding: 14px;
        border: 1px solid var(--pd-border);
        border-radius: 14px;
        background: var(--pd-bg-soft);
        color: var(--pd-muted);
        font-size: 9px;
        line-height: 1.6;
    }

    .pd-status-card {
        overflow: hidden;
        border-radius: 20px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255,255,255,0.12), transparent 30%),
            linear-gradient(135deg, var(--pd-maroon) 0%, var(--pd-maroon-2) 58%, var(--pd-maroon-dark) 100%);
        color: #FFFFFF;
        box-shadow: 0 18px 44px rgba(86,28,23,0.18);
    }

    .pd-status-card-body {
        padding: 18px;
    }

    .pd-status-card small {
        color: rgba(255,255,255,0.55);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .pd-status-card h2 {
        margin: 10px 0 0;
        color: #FFFFFF;
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .pd-status-card p {
        margin: 9px 0 0;
        color: rgba(255,255,255,0.62);
        font-size: 9px;
        line-height: 1.5;
    }

    .pd-area-box {
        margin-top: 16px;
        padding: 13px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 13px;
        background: rgba(255,255,255,0.07);
    }

    .pd-area-box span {
        display: block;
        color: rgba(255,255,255,0.45);
        font-size: 8px;
        font-weight: 850;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .pd-area-box strong {
        display: block;
        margin-top: 5px;
        color: #FFFFFF;
        font-size: 11px;
        font-weight: 900;
    }

    .pd-timeline {
        display: grid;
        gap: 0;
        margin-top: 16px;
    }

    .pd-step {
        position: relative;
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        gap: 10px;
        padding-bottom: 18px;
    }

    .pd-step:last-child {
        padding-bottom: 0;
    }

    .pd-step:not(:last-child)::after {
        position: absolute;
        top: 26px;
        bottom: 0;
        left: 12px;
        width: 1px;
        background: var(--pd-border);
        content: "";
    }

    .pd-step.is-done:not(:last-child)::after {
        background: var(--pd-maroon);
    }

    .pd-step-dot {
        position: relative;
        z-index: 1;
        display: grid;
        width: 25px;
        height: 25px;
        place-items: center;
        border: 1px solid var(--pd-border);
        border-radius: 999px;
        background: var(--pd-bg-soft);
        color: var(--pd-muted);
        font-size: 8px;
        font-weight: 900;
    }

    .pd-step.is-done .pd-step-dot {
        border-color: var(--pd-maroon);
        background: var(--pd-maroon);
        color: #FFFFFF;
    }

    .pd-step-dot svg {
        width: 13px;
        height: 13px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .pd-step-copy strong {
        display: block;
        padding-top: 2px;
        color: var(--pd-muted);
        font-size: 9px;
        font-weight: 850;
    }

    .pd-step.is-done .pd-step-copy strong {
        color: var(--pd-text);
    }

    .pd-step-copy p {
        margin: 4px 0 0;
        color: var(--pd-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .pd-step-copy time {
        display: block;
        margin-top: 4px;
        color: var(--pd-muted-light);
        font-size: 7px;
        font-weight: 700;
    }

    .pd-quick-actions {
        display: grid;
        gap: 8px;
    }

    .pd-copy-success {
        background: var(--pd-success-soft) !important;
        border-color: #CFE8DA !important;
        color: var(--pd-success) !important;
    }

    @media (max-width: 1180px) {
        .pd-main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 860px) {
        .pd-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .pd-overview-grid,
        .pd-delivery-grid,
        .pd-contact-grid {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }

        .pd-actions {
            width: 100%;
        }

        .pd-actions .pd-btn {
            flex: 1;
        }
    }

    @media (max-width: 560px) {
        .pd-overview-grid,
        .pd-delivery-grid,
        .pd-contact-grid {
            grid-template-columns: 1fr;
        }

        .pd-item {
            grid-template-columns: auto minmax(0,1fr);
        }

        .pd-item-price {
            grid-column: 2;
            text-align: left;
        }
    }

    html.dark .pd-page {
        color: #F5EFE8;
    }

    html.dark .pd-hero,
    html.dark .pd-card {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,0.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .pd-card-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,0.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .pd-hero h1,
    html.dark .pd-card-head h2,
    html.dark .pd-value,
    html.dark .pd-info-copy strong,
    html.dark .pd-contact-box strong,
    html.dark .pd-item-main strong,
    html.dark .pd-item-price strong,
    html.dark .pd-step.is-done .pd-step-copy strong {
        color: #F5EFE8 !important;
    }

    html.dark .pd-hero p,
    html.dark .pd-breadcrumb,
    html.dark .pd-label,
    html.dark .pd-card-head p,
    html.dark .pd-info-copy p,
    html.dark .pd-item-main span,
    html.dark .pd-item-price span,
    html.dark .pd-notes,
    html.dark .pd-step-copy p,
    html.dark .pd-step-copy time {
        color: #AFA19A !important;
    }

    html.dark .pd-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .pd-overview-grid {
        background: #30231F !important;
    }

    html.dark .pd-overview-item {
        background: #1A1412 !important;
    }

    html.dark .pd-info-box,
    html.dark .pd-contact-box,
    html.dark .pd-notes {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .pd-info-icon,
    html.dark .pd-item-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .pd-total {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .pd-total strong {
        color: #EBA99D !important;
    }

    html.dark .pd-status.is-maroon,
    html.dark .pd-status.is-neutral {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .pd-step-dot {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #AFA19A !important;
    }

    html.dark .pd-step.is-done .pd-step-dot {
        background: #A84538 !important;
        border-color: #A84538 !important;
        color: #FFFFFF !important;
    }

    html.dark .pd-step.is-done:not(:last-child)::after {
        background: #A84538 !important;
    }

    html.dark .pd-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .pd-btn-primary:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }

    html.dark .pd-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }
</style>

<div class="pd-page">

    <nav class="pd-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('logistics.dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('logistics.parcels.index') }}">Parcels</a>
        <span>/</span>
        <strong>{{ $parcel['tracking'] }}</strong>
    </nav>

    <section class="pd-hero">
        <div>
            <span class="pd-eyebrow">Parcel Details</span>

            <div class="pd-hero-title">
                <h1>{{ $parcel['tracking'] }}</h1>

                <span class="pd-status is-{{ $statusTone }}">
                    {{ $parcel['status'] }}
                </span>
            </div>

            <p>
                Order {{ $parcel['order'] }} · {{ $parcel['seller'] }}
            </p>
        </div>

        <div class="pd-actions">
            <a href="{{ route('logistics.parcels.index') }}" class="pd-btn pd-btn-soft">
                ← Back
            </a>

            <a href="{{ $nextAction['route'] }}" class="pd-btn pd-btn-primary">
                {{ $nextAction['label'] }}

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['arrow'] !!}
                </svg>
            </a>
        </div>
    </section>

    <section class="pd-main-grid">

        <div class="pd-stack">

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <h2>Parcel Overview</h2>
                        <p>Core parcel and order information.</p>
                    </div>
                </header>

                <div class="pd-overview-grid">
                    @foreach([
                        ['label' => 'Order Number', 'value' => $parcel['order']],
                        ['label' => 'Seller', 'value' => $parcel['seller']],
                        ['label' => 'Buyer', 'value' => $parcel['buyer']],
                        ['label' => 'Payment', 'value' => $parcel['payment']],
                        ['label' => 'Parcel Value', 'value' => '₱' . number_format($parcel['value'], 2)],
                        ['label' => 'Condition', 'value' => $parcel['condition']],
                    ] as $detail)
                        <div class="pd-overview-item">
                            <span class="pd-label">{{ $detail['label'] }}</span>
                            <strong class="pd-value">{{ $detail['value'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <h2>Delivery Information</h2>
                        <p>Destination, delivery area, and rider assignment.</p>
                    </div>

                    <span class="pd-status is-neutral">
                        {{ $parcel['area'] }}
                    </span>
                </header>

                <div class="pd-body">
                    <div class="pd-delivery-grid">

                        <div class="pd-info-box">
                            <div class="pd-info-row">
                                <span class="pd-info-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        {!! $icons['location'] !!}
                                    </svg>
                                </span>

                                <div class="pd-info-copy">
                                    <span class="pd-label">Destination</span>
                                    <strong>{{ $parcel['destination'] }}</strong>
                                    <p>{{ $parcel['address'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="pd-info-box">
                            <div class="pd-info-row">
                                <span class="pd-info-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        {!! $icons['rider'] !!}
                                    </svg>
                                </span>

                                <div class="pd-info-copy">
                                    <span class="pd-label">Assigned Rider</span>
                                    <strong>{{ $parcel['rider'] }}</strong>

                                    <p>
                                        {{
                                            $parcel['rider'] === 'Not assigned'
                                                ? 'No rider has been assigned to this parcel yet.'
                                                : 'Rider assigned based on the parcel delivery area.'
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="pd-contact-grid">
                        <div class="pd-contact-box">
                            <span class="pd-label">Customer Contact</span>
                            <strong>{{ $parcel['contact'] }}</strong>
                        </div>

                        <div class="pd-contact-box">
                            <span class="pd-label">Received From</span>
                            <strong>{{ $parcel['received_from'] }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <h2>Parcel Items</h2>
                        <p>Items included in this order.</p>
                    </div>
                </header>

                <div class="pd-items">
                    @foreach($parcel['items'] as $item)
                        <article class="pd-item">
                            <span class="pd-item-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    {!! $icons['package'] !!}
                                </svg>
                            </span>

                            <div class="pd-item-main">
                                <strong>{{ $item['name'] }}</strong>
                                <span>{{ $item['variation'] }}</span>
                            </div>

                            <div class="pd-item-price">
                                <strong>₱{{ number_format($item['price'], 2) }}</strong>
                                <span>Qty {{ $item['quantity'] }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pd-total">
                    <span>Total</span>
                    <strong>₱{{ number_format($parcel['value'], 2) }}</strong>
                </div>
            </section>

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <h2>Logistics Notes</h2>
                        <p>Latest logistics remarks for this parcel.</p>
                    </div>
                </header>

                <div class="pd-body">
                    <div class="pd-notes">
                        {{ $parcel['notes'] }}
                    </div>
                </div>
            </section>

        </div>

        <aside class="pd-stack">

            <section class="pd-status-card">
                <div class="pd-status-card-body">
                    <small>Current Status</small>

                    <h2>{{ $parcel['status'] }}</h2>

                    <p>
                        Tracking {{ $parcel['tracking'] }}
                    </p>

                    <div class="pd-area-box">
                        <span>Delivery Area</span>
                        <strong>{{ $parcel['area'] }}</strong>
                    </div>
                </div>
            </section>

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <h2>Parcel Timeline</h2>
                        <p>Delivery workflow and progress.</p>
                    </div>
                </header>

                <div class="pd-body">
                    <div class="pd-timeline">
                        @foreach($timeline as $step)
                            <div class="pd-step {{ $step['done'] ? 'is-done' : '' }}">
                                <span class="pd-step-dot">
                                    @if($step['done'])
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            {!! $icons['check'] !!}
                                        </svg>
                                    @else
                                        {{ $loop->iteration }}
                                    @endif
                                </span>

                                <div class="pd-step-copy">
                                    <strong>{{ $step['title'] }}</strong>
                                    <p>{{ $step['description'] }}</p>
                                    <time>{{ $step['time'] ?? 'Pending' }}</time>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <h2>Quick Actions</h2>
                        <p>Common actions for this parcel.</p>
                    </div>
                </header>

                <div class="pd-body">
                    <div class="pd-quick-actions">
                        <a href="{{ $nextAction['route'] }}" class="pd-btn pd-btn-primary">
                            {{ $nextAction['label'] }}

                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['arrow'] !!}
                            </svg>
                        </a>

                        <a
                            href="{{ route(
                                'logistics.parcels.tracking',
                                ['tracking' => $parcel['tracking']]
                            ) }}"
                            class="pd-btn pd-btn-soft"
                        >
                            Open Tracking

                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['arrow'] !!}
                            </svg>
                        </a>

                        <button
                            type="button"
                            id="copyTrackingButton"
                            data-tracking="{{ $parcel['tracking'] }}"
                            class="pd-btn pd-btn-soft"
                        >
                            <span id="copyTrackingLabel">Copy Tracking ID</span>

                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['copy'] !!}
                            </svg>
                        </button>
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
    const copyButton =
        document.getElementById('copyTrackingButton');

    const copyLabel =
        document.getElementById('copyTrackingLabel');

    copyButton?.addEventListener(
        'click',
        async function () {
            const tracking =
                copyButton.dataset.tracking;

            if (!tracking) {
                return;
            }

            try {
                await navigator.clipboard.writeText(
                    tracking
                );

                if (copyLabel) {
                    copyLabel.textContent = 'Copied!';
                }

                copyButton.classList.add(
                    'pd-copy-success'
                );

                window.setTimeout(
                    function () {
                        if (copyLabel) {
                            copyLabel.textContent =
                                'Copy Tracking ID';
                        }

                        copyButton.classList.remove(
                            'pd-copy-success'
                        );
                    },
                    1500
                );
            } catch (error) {
                window.prompt(
                    'Copy tracking number:',
                    tracking
                );
            }
        }
    );
});
</script>
@endpush
