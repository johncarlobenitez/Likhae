@extends('logistics.app')

@section('title', 'Parcel Details — LIKHAE Logistics')

@php
    /*
    |--------------------------------------------------------------------------
    | FRONTEND SAMPLE DATA
    |--------------------------------------------------------------------------
    | Temporary sample records.
    | Replace with controller/database data later.
    */

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
            'route' => route('logistics.sorting'),
        ],

        'awaiting_rider' => [
            'label' => 'Assign Rider',
            'route' => route('logistics.assignments'),
        ],

        'assigned',
        'out_for_delivery' => [
            'label' => 'Track Parcel',
            'route' => route('logistics.parcels.tracking'),
        ],

        default => [
            'label' => 'View Tracking',
            'route' => route('logistics.parcels.tracking'),
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
            'description' =>
                $parcel['area'] === 'Unassigned'
                    ? 'Waiting for destination-area sorting.'
                    : 'Parcel assigned to ' . $parcel['area'] . '.',
            'time' =>
                $parcel['area'] === 'Unassigned'
                    ? null
                    : 'September 1, 2026 · 10:02 AM',
            'done' =>
                $parcel['status_key'] !== 'waiting_sorting',
        ],
        [
            'title' => 'Rider Assigned',
            'description' =>
                $parcel['rider'] === 'Not assigned'
                    ? 'Waiting for an available rider.'
                    : $parcel['rider'] . ' received the delivery assignment.',
            'time' =>
                $parcel['rider'] === 'Not assigned'
                    ? null
                    : 'September 1, 2026 · 10:14 AM',
            'done' =>
                in_array(
                    $parcel['status_key'],
                    ['assigned', 'out_for_delivery', 'delivered'],
                    true
                ),
        ],
        [
            'title' => 'Out for Delivery',
            'description' => 'Parcel has left the sorting center for delivery.',
            'time' =>
                in_array(
                    $parcel['status_key'],
                    ['out_for_delivery', 'delivered'],
                    true
                )
                    ? 'September 1, 2026 · 11:10 AM'
                    : null,
            'done' =>
                in_array(
                    $parcel['status_key'],
                    ['out_for_delivery', 'delivered'],
                    true
                ),
        ],
        [
            'title' => 'Delivered',
            'description' => 'Parcel successfully delivered to the recipient.',
            'time' =>
                $parcel['status_key'] === 'delivered'
                    ? 'September 1, 2026 · 12:18 PM'
                    : null,
            'done' =>
                $parcel['status_key'] === 'delivered',
        ],
    ];

    $icons = [
        'parcel' => '
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
        'note' => '
            <path d="M5 3h14v18H5z"/>
            <path d="M8 8h8"/>
            <path d="M8 12h8"/>
            <path d="M8 16h5"/>
        ',
        'copy' => '
            <rect x="8" y="8" width="11" height="11" rx="2"/>
            <path d="M16 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h3"/>
        ',
        'arrow' => '
            <path d="M5 12h14"/>
            <path d="m14 7 5 5-5 5"/>
        ',
        'waybill' => '
            <path d="M6 3h9l4 4v14H6z"/>
            <path d="M14 3v5h5"/>
            <path d="M9 13h6"/>
            <path d="M9 17h4"/>
        ',
        'phone' => '
            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1A19.5 19.5 0 0 1 5.2 12 19.8 19.8 0 0 1 2.1 3.3 2 2 0 0 1 4.1 1h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 9a16 16 0 0 0 7 7l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2Z"/>
        ',
    ];
@endphp

<style>
    :root {
        --pd-bg: #FBF7F2;
        --pd-soft: #F6EFE7;
        --pd-warm: #F3E4DE;
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
        --pd-shadow: 0 8px 24px rgba(86,28,23,.055);
        --pd-shadow-lg: 0 18px 44px rgba(86,28,23,.11);
    }

    .pd-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--pd-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .pd-page * { box-sizing: border-box; }

    .pd-breadcrumb {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
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

    .pd-breadcrumb a:hover { color: var(--pd-maroon); }

    .pd-breadcrumb strong {
        color: var(--pd-text);
        font-weight: 900;
    }

    .pd-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--pd-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
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
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .pd-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .pd-hero-title-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-top: 10px;
    }

    .pd-hero h1 {
        margin: 0;
        color: var(--pd-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .pd-hero p {
        margin: 12px 0 0;
        color: var(--pd-muted);
        font-size: 10px;
        font-weight: 750;
    }

    .pd-status {
        display: inline-flex;
        min-height: 29px;
        align-items: center;
        gap: 6px;
        padding: 0 10px;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 950;
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

    .pd-status.is-maroon {
        border-color: #E6C7BE;
        background: var(--pd-warm);
        color: var(--pd-maroon);
    }

    .pd-status.is-success {
        border-color: #CFE8DA;
        background: var(--pd-success-soft);
        color: var(--pd-success);
    }

    .pd-status.is-neutral {
        border-color: var(--pd-border);
        background: var(--pd-soft);
        color: var(--pd-muted);
    }

    .pd-actions {
        display: flex;
        flex-wrap: wrap;
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

    .pd-btn:hover { transform: translateY(-1px); }

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
        box-shadow: 0 10px 22px rgba(86,28,23,.14);
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
        background: var(--pd-warm);
        border-color: var(--pd-tan);
    }

    .pd-main {
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
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .pd-card-head h2 {
        margin: 0;
        color: var(--pd-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .pd-card-head p {
        margin: 7px 0 0;
        color: var(--pd-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .pd-card-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 12px;
        background: var(--pd-warm);
        color: var(--pd-maroon);
    }

    .pd-card-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .pd-overview-grid {
        display: grid;
        grid-template-columns: repeat(3,minmax(0,1fr));
        gap: 1px;
        background: var(--pd-border);
    }

    .pd-overview-item {
        min-width: 0;
        padding: 17px 20px;
        background: var(--pd-card);
    }

    .pd-label {
        display: block;
        color: var(--pd-muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .pd-value {
        display: block;
        margin-top: 7px;
        color: var(--pd-text);
        font-size: 10px;
        font-weight: 900;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .pd-delivery {
        padding: 18px 20px;
    }

    .pd-delivery-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 12px;
    }

    .pd-delivery-box {
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        gap: 12px;
        padding: 14px;
        border: 1px solid var(--pd-border);
        border-radius: 15px;
        background: var(--pd-soft);
    }

    .pd-delivery-box strong {
        display: block;
        color: var(--pd-text);
        font-size: 10px;
        font-weight: 950;
    }

    .pd-delivery-box p {
        margin: 5px 0 0;
        color: var(--pd-muted);
        font-size: 8px;
        line-height: 1.5;
    }

    .pd-mini-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 10px;
        margin-top: 12px;
    }

    .pd-mini {
        padding: 12px;
        border: 1px solid var(--pd-border);
        border-radius: 12px;
        background: var(--pd-card);
    }

    .pd-items { display: grid; }

    .pd-item {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
        border-bottom: 1px solid var(--pd-border);
    }

    .pd-item:last-child { border-bottom: 0; }

    .pd-item-icon {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 13px;
        background: var(--pd-warm);
        color: var(--pd-maroon);
    }

    .pd-item-icon svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
    }

    .pd-item-copy strong {
        display: block;
        color: var(--pd-text);
        font-size: 10px;
        font-weight: 950;
    }

    .pd-item-copy span,
    .pd-item-price span {
        display: block;
        margin-top: 4px;
        color: var(--pd-muted);
        font-size: 8px;
    }

    .pd-item-price { text-align: right; }

    .pd-item-price strong {
        color: var(--pd-text);
        font-size: 10px;
        font-weight: 950;
    }

    .pd-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 20px;
        border-top: 1px solid var(--pd-border);
        background: var(--pd-soft);
    }

    .pd-total span {
        color: var(--pd-muted);
        font-size: 9px;
        font-weight: 800;
    }

    .pd-total strong {
        color: var(--pd-maroon);
        font-size: 14px;
        font-weight: 950;
    }

    .pd-notes {
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        gap: 12px;
        padding: 18px 20px;
    }

    .pd-notes p {
        margin: 0;
        padding: 13px 14px;
        border: 1px solid var(--pd-border);
        border-radius: 13px;
        background: var(--pd-soft);
        color: var(--pd-muted);
        font-size: 9px;
        line-height: 1.6;
    }

    .pd-status-card {
        overflow: hidden;
        border-radius: 20px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255,255,255,.12), transparent 30%),
            linear-gradient(135deg, var(--pd-maroon) 0%, var(--pd-maroon-2) 58%, var(--pd-maroon-dark) 100%);
        color: #FFFFFF;
        box-shadow: 0 18px 44px rgba(86,28,23,.18);
    }

    .pd-status-body { padding: 19px; }

    .pd-status-body small {
        color: rgba(255,255,255,.55);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .pd-status-body h2 {
        margin: 10px 0 0;
        color: #FFFFFF;
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 31px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .pd-status-body p {
        margin: 8px 0 0;
        color: rgba(255,255,255,.62);
        font-size: 9px;
        line-height: 1.55;
    }

    .pd-status-meta {
        display: grid;
        gap: 8px;
        margin-top: 16px;
    }

    .pd-status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 12px;
        background: rgba(255,255,255,.07);
    }

    .pd-status-row span {
        color: rgba(255,255,255,.48);
        font-size: 8px;
    }

    .pd-status-row strong {
        color: #FFFFFF;
        font-size: 9px;
        font-weight: 900;
        text-align: right;
    }

    .pd-timeline {
        display: grid;
        padding: 18px 20px;
    }

    .pd-step {
        position: relative;
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        gap: 11px;
        padding-bottom: 19px;
    }

    .pd-step:last-child { padding-bottom: 0; }

    .pd-step:not(:last-child)::after {
        position: absolute;
        top: 28px;
        bottom: 0;
        left: 13px;
        width: 1px;
        background: var(--pd-border);
        content: "";
    }

    .pd-step.is-done:not(:last-child)::after {
        background: #C8A99B;
    }

    .pd-step-dot {
        position: relative;
        z-index: 1;
        display: grid;
        width: 27px;
        height: 27px;
        place-items: center;
        border: 1px solid var(--pd-border);
        border-radius: 999px;
        background: var(--pd-soft);
        color: var(--pd-muted);
        font-size: 8px;
        font-weight: 950;
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
    }

    .pd-step strong {
        display: block;
        padding-top: 2px;
        color: var(--pd-muted);
        font-size: 9px;
        font-weight: 850;
    }

    .pd-step.is-done strong { color: var(--pd-text); }

    .pd-step p {
        margin: 4px 0 0;
        color: var(--pd-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    .pd-step time {
        display: block;
        margin-top: 5px;
        color: var(--pd-muted-light);
        font-size: 7px;
        font-style: normal;
    }

    .pd-quick {
        display: grid;
        gap: 8px;
        padding: 18px 20px;
    }

    .pd-quick .pd-btn {
        width: 100%;
        justify-content: space-between;
    }

    .pd-copy-success {
        border-color: #CFE8DA !important;
        background: var(--pd-success-soft) !important;
        color: var(--pd-success) !important;
    }

    @media (max-width: 1180px) {
        .pd-main { grid-template-columns: 1fr; }
    }

    @media (max-width: 860px) {
        .pd-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .pd-actions { width: 100%; }

        .pd-actions .pd-btn { flex: 1; }

        .pd-overview-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
    }

    @media (max-width: 620px) {
        .pd-overview-grid,
        .pd-delivery-grid,
        .pd-mini-grid {
            grid-template-columns: 1fr;
        }

        .pd-actions { flex-direction: column; }

        .pd-actions .pd-btn { width: 100%; }

        .pd-item {
            grid-template-columns: auto minmax(0,1fr);
        }

        .pd-item-price {
            grid-column: 2;
            text-align: left;
        }
    }

    html.dark .pd-page { color: #F5EFE8; }

    html.dark .pd-hero,
    html.dark .pd-card {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .pd-card-head {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .pd-hero h1,
    html.dark .pd-card-head h2,
    html.dark .pd-value,
    html.dark .pd-delivery-box strong,
    html.dark .pd-item-copy strong,
    html.dark .pd-item-price strong,
    html.dark .pd-step.is-done strong {
        color: #F5EFE8 !important;
    }

    html.dark .pd-hero p,
    html.dark .pd-breadcrumb,
    html.dark .pd-card-head p,
    html.dark .pd-label,
    html.dark .pd-delivery-box p,
    html.dark .pd-item-copy span,
    html.dark .pd-item-price span,
    html.dark .pd-step p,
    html.dark .pd-step time {
        color: #AFA19A !important;
    }

    html.dark .pd-eyebrow { color: #EBA99D !important; }

    html.dark .pd-overview-grid {
        background: #30231F !important;
    }

    html.dark .pd-overview-item {
        background: #1A1412 !important;
    }

    html.dark .pd-delivery-box,
    html.dark .pd-mini,
    html.dark .pd-notes p {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .pd-card-icon,
    html.dark .pd-item-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .pd-total {
        background: #1D1715 !important;
        border-color: #30231F !important;
    }

    html.dark .pd-total strong { color: #EBA99D !important; }

    html.dark .pd-status.is-maroon {
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
        <a href="{{ route('logistics.parcels') }}">Parcels</a>
        <span>/</span>
        <strong>{{ $parcel['tracking'] }}</strong>
    </nav>

    <section class="pd-hero">
        <div>
            <span class="pd-eyebrow">Parcel Details</span>

            <div class="pd-hero-title-row">
                <h1>{{ $parcel['tracking'] }}</h1>

                <span class="pd-status is-{{ $statusTone }}">
                    {{ $parcel['status'] }}
                </span>
            </div>

            <p>
                Order {{ $parcel['order'] }} · Received {{ $parcel['received_at'] }}
            </p>
        </div>

        <div class="pd-actions">
            <a href="{{ route('logistics.parcels') }}" class="pd-btn pd-btn-soft">
                Back
            </a>

            <a
                href="{{ route('logistics.waybills.show', ['tracking' => $parcel['tracking']]) }}"
                class="pd-btn pd-btn-soft"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['waybill'] !!}
                </svg>

                Waybill
            </a>

            <a href="{{ $nextAction['route'] }}" class="pd-btn pd-btn-primary">
                {{ $nextAction['label'] }}

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    {!! $icons['arrow'] !!}
                </svg>
            </a>
        </div>
    </section>

    <section class="pd-main">

        <div class="pd-stack">

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <h2>Parcel Overview</h2>
                        <p>Core parcel, order, payment, and condition information.</p>
                    </div>

                    <span class="pd-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['parcel'] !!}
                        </svg>
                    </span>
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
                        <p>Destination, assigned rider, and recipient contact details.</p>
                    </div>

                    <span class="pd-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['location'] !!}
                        </svg>
                    </span>
                </header>

                <div class="pd-delivery">
                    <div class="pd-delivery-grid">
                        <div class="pd-delivery-box">
                            <span class="pd-card-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    {!! $icons['location'] !!}
                                </svg>
                            </span>

                            <div>
                                <span class="pd-label">Destination</span>
                                <strong>{{ $parcel['destination'] }}</strong>
                                <p>{{ $parcel['address'] }}</p>
                            </div>
                        </div>

                        <div class="pd-delivery-box">
                            <span class="pd-card-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    {!! $icons['rider'] !!}
                                </svg>
                            </span>

                            <div>
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

                    <div class="pd-mini-grid">
                        <div class="pd-mini">
                            <span class="pd-label">Delivery Area</span>
                            <strong class="pd-value">{{ $parcel['area'] }}</strong>
                        </div>

                        <div class="pd-mini">
                            <span class="pd-label">Received From</span>
                            <strong class="pd-value">{{ $parcel['received_from'] }}</strong>
                        </div>

                        <div class="pd-mini">
                            <span class="pd-label">Customer Contact</span>
                            <strong class="pd-value">{{ $parcel['contact'] }}</strong>
                        </div>

                        <div class="pd-mini">
                            <span class="pd-label">Received At</span>
                            <strong class="pd-value">{{ $parcel['received_at'] }}</strong>
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

                    <span class="pd-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['parcel'] !!}
                        </svg>
                    </span>
                </header>

                <div class="pd-items">
                    @foreach($parcel['items'] as $item)
                        <article class="pd-item">
                            <span class="pd-item-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    {!! $icons['parcel'] !!}
                                </svg>
                            </span>

                            <div class="pd-item-copy">
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
                        <p>Operational notes attached to this parcel.</p>
                    </div>

                    <span class="pd-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['note'] !!}
                        </svg>
                    </span>
                </header>

                <div class="pd-notes">
                    <span class="pd-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['note'] !!}
                        </svg>
                    </span>

                    <p>{{ $parcel['notes'] }}</p>
                </div>
            </section>

        </div>

        <aside class="pd-stack">

            <section class="pd-status-card">
                <div class="pd-status-body">
                    <small>Current Status</small>

                    <h2>{{ $parcel['status'] }}</h2>

                    <p>
                        Current parcel workflow state for {{ $parcel['tracking'] }}.
                    </p>

                    <div class="pd-status-meta">
                        <div class="pd-status-row">
                            <span>Delivery Area</span>
                            <strong>{{ $parcel['area'] }}</strong>
                        </div>

                        <div class="pd-status-row">
                            <span>Assigned Rider</span>
                            <strong>{{ $parcel['rider'] }}</strong>
                        </div>

                        <div class="pd-status-row">
                            <span>Payment</span>
                            <strong>{{ $parcel['payment'] }}</strong>
                        </div>
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

                            <div>
                                <strong>{{ $step['title'] }}</strong>
                                <p>{{ $step['description'] }}</p>
                                <time>{{ $step['time'] ?? 'Pending' }}</time>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="pd-card">
                <header class="pd-card-head">
                    <div>
                        <h2>Quick Actions</h2>
                        <p>Continue parcel operations or copy the tracking ID.</p>
                    </div>
                </header>

                <div class="pd-quick">
                    <a href="{{ $nextAction['route'] }}" class="pd-btn pd-btn-primary">
                        {{ $nextAction['label'] }}

                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['arrow'] !!}
                        </svg>
                    </a>

                    <a
                        href="{{ route('logistics.parcels.tracking') }}"
                        class="pd-btn pd-btn-soft"
                    >
                        Open Tracking

                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['arrow'] !!}
                        </svg>
                    </a>

                    <a
                        href="tel:{{ preg_replace('/\s+/', '', $parcel['contact']) }}"
                        class="pd-btn pd-btn-soft"
                    >
                        Contact Buyer

                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['phone'] !!}
                        </svg>
                    </a>

                    <button
                        type="button"
                        id="copyTrackingButton"
                        data-tracking="{{ $parcel['tracking'] }}"
                        class="pd-btn pd-btn-soft"
                    >
                        <span>Copy Tracking ID</span>

                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['copy'] !!}
                        </svg>
                    </button>
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

                const originalHtml =
                    copyButton.innerHTML;

                copyButton.classList.add(
                    'pd-copy-success'
                );

                copyButton.innerHTML =
                    '<span>Copied!</span><span>✓</span>';

                setTimeout(
                    function () {
                        copyButton.innerHTML =
                            originalHtml;

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
