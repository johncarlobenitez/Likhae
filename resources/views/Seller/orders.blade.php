@extends('layouts.seller')

@php
    $isLogistics = ($pageMode ?? 'orders') === 'logistics';
    $currentMode = $mode ?? ($isLogistics ? request('view', 'couriers') : request('mode', 'index'));
    $currentStatus = $status ?? request('status', 'all');
    $orderId = $selectedOrder ?? request('order', '10001');
    $order = $sellerOrders->firstWhere('id', (string) $orderId) ?? $sellerOrders->first();
@endphp

@section('title', $isLogistics ? 'Logistics' : ($currentMode === 'show' ? 'Order #'.$order['id'] : 'Orders'))
@section('active', $isLogistics ? 'logistics' : 'orders')
@section('subtitle', $isLogistics ? 'Assign couriers, submit pickup requests, and monitor shipments.' : 'Review purchases and move each order through fulfillment.')

@section('content')
<div class="sl-page">
    @if ($isLogistics && $currentMode === 'couriers')
        <div class="sl-page-toolbar">
            <div><span class="sl-eyebrow">Logistics</span><h2>Assign Courier</h2><p>Choose the best available provider after the package is prepared.</p></div>
            <a href="{{ route('seller.orders', ['status' => 'ready-pickup']) }}" class="sl-btn sl-btn-ghost">Ready Pickup Orders</a>
        </div>

        <div class="sl-logistics-layout">
            <section class="sl-card sl-logistics-order">
                <header class="sl-card-head"><div><span class="sl-eyebrow">Prepared Package</span><h2>Order #{{ $order['id'] }}</h2><p>Confirm the parcel information before assigning logistics.</p></div><span class="sl-status is-info">Ready Pickup</span></header>
                <div class="sl-package-summary">
                    <div class="sl-order-thumb">{{ mb_strtoupper(mb_substr($order['product'], 0, 1)) }}</div>
                    <div><strong>{{ $order['product'] }}</strong><span>{{ $order['buyer'] }} · Qty {{ $order['quantity'] }}</span><small>Package: 68 × 15 × 45 cm · 3.2 kg</small></div>
                    <strong>₱{{ number_format($order['total'], 2) }}</strong>
                </div>
                <div class="sl-address-card"><svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0z"/><circle cx="12" cy="10" r="2"/></svg><div><span>Pickup address</span><strong>LIKHAE Studio, 24 Rizal Street</strong><small>Santa Cruz, Laguna 4009</small></div></div>
                <div class="sl-address-card"><svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0z"/><circle cx="12" cy="10" r="2"/></svg><div><span>Delivery area</span><strong>{{ $order['buyer'] }}</strong><small>Calamba City, Laguna 4027</small></div></div>
            </section>

            <form class="sl-card sl-courier-select" data-demo-form data-success="Courier confirmed for Order #{{ $order['id'] }}.">
                <header class="sl-card-head"><div><span class="sl-eyebrow">Available Logistics</span><h2>Select Logistics Provider</h2><p>Availability is based on your pickup area and package size.</p></div></header>
                <div class="sl-provider-list">
                    @foreach ([
                        ['J&T Express', 'Tomorrow, 10:00 AM', '1–2 days', '₱145.00', 'Recommended'],
                        ['Flash Express', 'Tomorrow, 2:00 PM', '1–3 days', '₱135.00', 'Lowest rate'],
                        ['LBC', 'Sep 06, 9:00 AM', '2–3 days', '₱180.00', ''],
                        ['Local Courier', 'Today, 4:30 PM', 'Same day', '₱220.00', 'Fastest'],
                    ] as $provider)
                        <label class="sl-provider-option">
                            <input type="radio" name="provider" value="{{ $provider[0] }}" {{ $loop->first ? 'checked' : '' }}>
                            <span class="sl-radio-mark"></span>
                            <span class="sl-provider-logo">{{ collect(explode(' ', $provider[0]))->map(fn ($word) => mb_substr($word, 0, 1))->join('') }}</span>
                            <span class="sl-provider-main"><strong>{{ $provider[0] }}</strong><small>Pickup: {{ $provider[1] }}</small></span>
                            <span class="sl-provider-meta"><small>{{ $provider[2] }}</small><strong>{{ $provider[3] }}</strong></span>
                            @if ($provider[4])<span class="sl-provider-tag">{{ $provider[4] }}</span>@endif
                        </label>
                    @endforeach
                </div>
                <div class="sl-form-grid">
                    <label class="sl-field"><span>Pickup date</span><input type="date" value="2026-09-05" required></label>
                    <label class="sl-field"><span>Pickup window</span><select required><option>10:00 AM – 12:00 PM</option><option>1:00 PM – 3:00 PM</option><option>3:00 PM – 5:00 PM</option></select></label>
                    <label class="sl-field sl-span-2"><span>Pickup note</span><textarea rows="3" placeholder="Optional instructions for the courier">Package is sealed and available at the store counter.</textarea></label>
                </div>
                <div class="sl-form-actions"><button type="button" class="sl-btn sl-btn-ghost" data-demo-action="Courier selection saved.">Confirm Courier</button><button type="submit" class="sl-btn sl-btn-primary">Submit Pickup Request</button></div>
            </form>
        </div>
    @elseif ($isLogistics && $currentMode === 'pickups')
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Logistics</span><h2>Pickup Requests</h2><p>Monitor scheduled courier pickups and handover readiness.</p></div><a href="{{ route('seller.logistics', ['view' => 'couriers']) }}" class="sl-btn sl-btn-primary">New Pickup Request</a></div>
        <section class="sl-mini-stats"><div><span>Scheduled Today</span><strong>4</strong></div><div><span>Awaiting Courier</span><strong>2</strong></div><div><span>Picked Up</span><strong>12</strong></div><div><span>Exceptions</span><strong>1</strong></div></section>
        <section class="sl-card">
            <div class="sl-table-toolbar"><div class="sl-search-input"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" placeholder="Search pickup or order"></div><select class="sl-select"><option>All schedules</option><option>Today</option><option>Tomorrow</option><option>Completed</option></select></div>
            <div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Request</th><th>Orders</th><th>Provider</th><th>Pickup Window</th><th>Courier</th><th>Status</th><th>Action</th></tr></thead><tbody>
                @foreach ([
                    ['PU-2091', '#10003', 'J&T Express', 'Today · 10AM–12PM', 'Juan Rider', 'Courier assigned', 'is-info'],
                    ['PU-2090', '#10007, #10008', 'Flash Express', 'Today · 1PM–3PM', 'Waiting assignment', 'Requested', 'is-warning'],
                    ['PU-2089', '#10009', 'LBC', 'Today · 3PM–5PM', 'Carlo Dela Cruz', 'Courier heading to store', 'is-info'],
                    ['PU-2088', '#10010', 'J&T Express', 'Yesterday · 2PM', 'Miguel Santos', 'Picked up', 'is-success'],
                ] as $pickup)
                    <tr><td><strong>{{ $pickup[0] }}</strong></td><td>{{ $pickup[1] }}</td><td>{{ $pickup[2] }}</td><td>{{ $pickup[3] }}</td><td>{{ $pickup[4] }}</td><td><span class="sl-status {{ $pickup[6] }}">{{ $pickup[5] }}</span></td><td><button type="button" class="sl-btn sl-btn-ghost sl-btn-sm" data-demo-action="Pickup {{ $pickup[0] }} opened.">Details</button></td></tr>
                @endforeach
            </tbody></table></div>
        </section>
    @elseif ($isLogistics && $currentMode === 'tracking')
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Logistics</span><h2>Shipment Tracking</h2><p>Monitor courier movement from pickup request to buyer delivery.</p></div><div class="sl-search-input sl-tracking-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input value="#{{ $order['id'] }}" aria-label="Tracking number"><button type="button" data-demo-action="Shipment tracking refreshed.">Track</button></div></div>
        <div class="sl-tracking-layout">
            <section class="sl-card sl-shipment-summary">
                <div class="sl-shipment-head"><div><span class="sl-eyebrow">Order #{{ $order['id'] }}</span><h2>{{ $order['product'] }}</h2><p>Tracking no. JT839201476PH</p></div><span class="sl-status is-info">In Transit</span></div>
                <dl class="sl-shipment-meta"><div><dt>Logistics</dt><dd>J&T Express</dd></div><div><dt>Courier</dt><dd>Juan Rider</dd></div><div><dt>Buyer</dt><dd>{{ $order['buyer'] }}</dd></div><div><dt>Estimated Delivery</dt><dd>September 5, 2026</dd></div></dl>
                <div class="sl-tracking-map"><div class="sl-map-grid"></div><span class="sl-map-route"></span><span class="sl-map-point is-start">S</span><span class="sl-map-point is-current">●</span><span class="sl-map-point is-end">B</span><div><strong>Parcel is moving to Calamba Hub</strong><small>Last updated 18 minutes ago</small></div></div>
            </section>
            <section class="sl-card">
                <header class="sl-card-head"><div><span class="sl-eyebrow">Live Progress</span><h2>Shipment Timeline</h2><p>Latest scan and delivery milestones.</p></div></header>
                <ol class="sl-timeline">
                    @foreach ([
                        ['Pickup Requested', 'Sep 03 · 9:05 AM', 'Pickup scheduled by seller', true],
                        ['Courier Assigned', 'Sep 03 · 9:32 AM', 'Juan Rider accepted the pickup', true],
                        ['Picked Up', 'Sep 03 · 11:48 AM', 'Parcel collected from LIKHAE Studio', true],
                        ['Sorting Center', 'Sep 03 · 4:20 PM', 'Scanned at Santa Cruz Sorting Center', true],
                        ['In Transit', 'Sep 04 · 7:10 AM', 'Departed for Calamba Hub', true],
                        ['Delivered', 'Estimated Sep 05', 'Waiting for buyer delivery confirmation', false],
                    ] as $event)
                        <li class="{{ $event[3] ? 'is-complete' : '' }}"><span>{{ $event[3] ? '✓' : '' }}</span><div><strong>{{ $event[0] }}</strong><small>{{ $event[1] }}</small><p>{{ $event[2] }}</p></div></li>
                    @endforeach
                </ol>
            </section>
        </div>
    @elseif (!$isLogistics && $currentMode === 'show')
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Order Details</span><h2>Order #{{ $order['id'] }}</h2><p>Placed {{ $order['date'] }}</p></div><a href="{{ route('seller.orders', ['status' => $order['status_key']]) }}" class="sl-btn sl-btn-ghost">Back to Orders</a></div>
        <div class="sl-order-detail-layout">
            <div class="sl-order-detail-main">
                <section class="sl-card">
                    <header class="sl-card-head"><div><h2>Ordered Product</h2><p>Items included in this purchase.</p></div><span class="sl-status is-warning">{{ $order['status'] }}</span></header>
                    <div class="sl-package-summary"><div class="sl-order-thumb">{{ mb_strtoupper(mb_substr($order['product'], 0, 1)) }}</div><div><strong>{{ $order['product'] }}</strong><span>Model: 27 inch Black · SKU MON-27-BLK</span><small>₱{{ number_format($order['total'] / $order['quantity'], 2) }} × {{ $order['quantity'] }}</small></div><strong>₱{{ number_format($order['total'], 2) }}</strong></div>
                </section>
                <section class="sl-card">
                    <header class="sl-card-head"><div><h2>Fulfillment Progress</h2><p>Complete each action in sequence.</p></div></header>
                    <div class="sl-order-progress"><div class="is-done"><span>✓</span><strong>Order received</strong></div><div><span>2</span><strong>Prepare package</strong></div><div><span>3</span><strong>Assign courier</strong></div><div><span>4</span><strong>Hand over parcel</strong></div></div>
                    <div class="sl-action-panel"><span class="sl-action-panel-icon"><svg viewBox="0 0 24 24"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5zM4 7.5l8 4.5 8-4.5M12 12v9"/></svg></span><div><strong>Prepare this order</strong><p>Verify the product, pack it securely, and print the shipping waybill.</p></div><button type="button" class="sl-btn sl-btn-ghost" data-demo-action="Waybill ready to print.">Print Waybill</button><button type="button" class="sl-btn sl-btn-primary" data-order-action="prepare">Mark as Prepared</button></div>
                </section>
            </div>
            <aside class="sl-order-detail-side">
                <section class="sl-card"><h3>Buyer Information</h3><dl class="sl-detail-list"><div><dt>Name</dt><dd>{{ $order['buyer'] }}</dd></div><div><dt>Contact</dt><dd>09•• ••• ••82</dd></div><div><dt>Delivery Address</dt><dd>Calamba City, Laguna 4027</dd></div></dl><a href="{{ route('seller.messages') }}" class="sl-btn sl-btn-soft sl-btn-block">Message Buyer</a></section>
                <section class="sl-card"><h3>Payment Summary</h3><dl class="sl-price-list"><div><dt>Item subtotal</dt><dd>₱{{ number_format($order['total'], 2) }}</dd></div><div><dt>Shipping fee</dt><dd>₱145.00</dd></div><div><dt>Platform voucher</dt><dd>−₱100.00</dd></div><div class="is-total"><dt>Buyer paid</dt><dd>₱{{ number_format($order['total'] + 45, 2) }}</dd></div></dl><span class="sl-payment-note">{{ $order['payment'] }} · Payment confirmed</span></section>
            </aside>
        </div>
    @else
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Order Management</span><h2>Manage Orders</h2><p>Process orders on time and keep buyers informed.</p></div><button type="button" class="sl-btn sl-btn-ghost" data-demo-action="Orders exported.">Export Orders</button></div>
        <section class="sl-order-tabs" aria-label="Order filters">
            @foreach (['all' => 'All Orders', 'to-process' => 'To Process', 'to-prepare' => 'To Prepare', 'ready-pickup' => 'Ready Pickup', 'shipping' => 'Shipping', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'returns' => 'Returns/Refunds'] as $key => $label)
                <a href="{{ route('seller.orders', ['status' => $key]) }}" class="{{ $currentStatus === $key ? 'is-active' : '' }}">{{ $label }} @if (in_array($key, ['to-process','to-prepare'], true))<span>{{ $key === 'to-process' ? 5 : 8 }}</span>@endif</a>
            @endforeach
        </section>
        <section class="sl-card sl-order-filters"><div class="sl-search-input"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" name="q" value="{{ request('q') }}" placeholder="Search order, buyer, or product" data-order-search></div><select class="sl-select"><option>All payment methods</option><option>Cash on Delivery</option><option>GCash</option><option>Maya</option><option>Credit Card</option></select><input class="sl-input" type="date" aria-label="Order date"><button type="button" class="sl-btn sl-btn-soft" data-demo-action="Order filters applied.">Apply</button></section>
        <div class="sl-order-list" data-order-list>
            @php $visibleOrders = $currentStatus === 'all' ? $sellerOrders : $sellerOrders->where('status_key', $currentStatus); @endphp
            @forelse ($visibleOrders as $item)
                <x-seller.order-card :order="$item" />
            @empty
                <section class="sl-card"><x-seller.empty-state title="No orders in this stage" message="Orders will appear here when they enter this fulfillment stage." action="View All Orders" :href="route('seller.orders')" /></section>
            @endforelse
        </div>
        <div class="sl-no-results sl-card" data-order-empty hidden>No orders match your search.</div>
    @endif
</div>
@endsection
