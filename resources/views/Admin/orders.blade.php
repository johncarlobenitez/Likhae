@extends('layouts.admin')

@section('title', 'Orders & Delivery')
@section('subtitle', 'Monitor order progression and delivery exceptions without taking over logistics operations.')
@section('active', 'orders')

@section('content')
@php
    $view = request('view', 'orders');
    $status = request('status', 'all');
    $orders = collect([
        ['id' => '#LK-10482', 'buyer' => 'Angela Cruz', 'seller' => 'LIKHA Studio', 'items' => '27-inch Borderless Monitor × 1', 'total' => 12990, 'payment' => 'GCash', 'courier' => 'J&T Express', 'status' => 'To Process', 'date' => 'Today, 10:42 AM'],
        ['id' => '#LK-10481', 'buyer' => 'Marco Reyes', 'seller' => 'MNL Tech', 'items' => 'Mechanical Keyboard × 2', 'total' => 5580, 'payment' => 'COD', 'courier' => 'Flash Express', 'status' => 'To Prepare', 'date' => 'Today, 9:18 AM'],
        ['id' => '#LK-10480', 'buyer' => 'Sarah Lim', 'seller' => 'MNL Tech', 'items' => 'Wireless Gaming Mouse × 1', 'total' => 1490, 'payment' => 'Maya', 'courier' => 'LBC', 'status' => 'Ready Pickup', 'date' => 'Today, 8:32 AM'],
        ['id' => '#LK-10479', 'buyer' => 'Daniel Tan', 'seller' => 'Casa Local', 'items' => 'Wireless Headphones × 1', 'total' => 3490, 'payment' => 'Card', 'courier' => 'J&T Express', 'status' => 'Shipping', 'date' => 'Yesterday, 4:10 PM'],
        ['id' => '#LK-10478', 'buyer' => 'Patricia Go', 'seller' => 'Paper & Loom', 'items' => 'LED Desk Lamp × 1', 'total' => 1980, 'payment' => 'GCash', 'courier' => 'Local Courier', 'status' => 'Completed', 'date' => 'Yesterday, 2:25 PM'],
        ['id' => '#LK-10477', 'buyer' => 'Lara Ong', 'seller' => 'North & Pine', 'items' => 'Canvas Weekender × 1', 'total' => 2190, 'payment' => 'COD', 'courier' => 'Flash Express', 'status' => 'Cancelled', 'date' => 'Yesterday, 11:05 AM'],
    ]);
    $deliveries = [
        ['order' => '#LK-10479', 'buyer' => 'Daniel Tan', 'hub' => 'Pasig Sorting Center', 'provider' => 'J&T Express', 'rider' => 'Juan Rider', 'last' => 'Departed sorting center', 'eta' => 'Today, 5–7 PM', 'status' => 'In Transit'],
        ['order' => '#LK-10476', 'buyer' => 'Bea Ramos', 'hub' => 'QC Distribution Hub', 'provider' => 'Flash Express', 'rider' => 'Carlo Diaz', 'last' => 'Out for delivery', 'eta' => 'Today, 2–5 PM', 'status' => 'Out for Delivery'],
        ['order' => '#LK-10472', 'buyer' => 'Enzo Yu', 'hub' => 'Makati Local Hub', 'provider' => 'Local Courier', 'rider' => 'Mia Flores', 'last' => 'Buyer unavailable', 'eta' => 'Reschedule required', 'status' => 'Failed'],
        ['order' => '#LK-10466', 'buyer' => 'Ana Luna', 'hub' => 'Cebu Sorting Center', 'provider' => 'LBC', 'rider' => 'Leo Ramos', 'last' => 'Delivered to recipient', 'eta' => 'Completed 11:20 AM', 'status' => 'Delivered'],
    ];
@endphp

<div class="ad-page">
    <div class="ad-page-head"><div><span class="ad-overline">Marketplace fulfillment</span><h2>{{ $view === 'delivery' ? 'Delivery monitoring' : 'Order monitoring' }}</h2><p>Observe exceptions, payments, and handoffs while sellers and logistics teams perform fulfillment.</p></div><button class="ad-btn ad-btn-secondary" type="button" data-demo-action="Order data refreshed">Refresh status</button></div>

    <section class="ad-summary-grid">
        <div class="ad-mini-stat"><span>Orders today</span><strong>386</strong><small>₱642,810 GMV</small></div><div class="ad-mini-stat"><span>Processing</span><strong>162</strong><small>42% of today's orders</small></div><div class="ad-mini-stat"><span>Active deliveries</span><strong>1,296</strong><small>Across 22 logistics centers</small></div><div class="ad-mini-stat"><span>Delivery exceptions</span><strong>8</strong><small>3 need urgent review</small></div>
    </section>

    <div class="ad-tabs"><a class="ad-tab {{ $view === 'orders' ? 'is-active' : '' }}" href="{{ route('admin.orders', ['view' => 'orders']) }}">Orders</a><a class="ad-tab {{ $view === 'delivery' ? 'is-active' : '' }}" href="{{ route('admin.orders', ['view' => 'delivery']) }}">Delivery Monitoring</a></div>

    @if($view === 'delivery')
        <section class="ad-card" id="delivery-table">
            <div class="ad-filter-bar"><label class="ad-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" data-filter-input="#delivery-table" placeholder="Search order, hub, courier, or rider"></label><select class="ad-select"><option>All delivery statuses</option><option>In Transit</option><option>Out for Delivery</option><option>Failed</option><option>Delivered</option></select></div>
            <div class="ad-table-wrap"><table class="ad-table"><thead><tr><th>Order</th><th>Buyer</th><th>Logistics center</th><th>Provider / rider</th><th>Latest event</th><th>ETA</th><th>Status</th><th></th></tr></thead><tbody>@foreach($deliveries as $delivery)<tr data-filter-item data-search="{{ strtolower(implode(' ', $delivery)) }}"><td><strong>{{ $delivery['order'] }}</strong></td><td>{{ $delivery['buyer'] }}</td><td>{{ $delivery['hub'] }}</td><td><strong>{{ $delivery['provider'] }}</strong><small>{{ $delivery['rider'] }}</small></td><td>{{ $delivery['last'] }}</td><td>{{ $delivery['eta'] }}</td><td><span class="ad-status is-{{ strtolower(str_replace(' ', '-', $delivery['status'])) }}">{{ $delivery['status'] }}</span></td><td><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Opening shipment timeline for {{ $delivery['order'] }}">Timeline</button></td></tr>@endforeach</tbody></table></div>
        </section>
        <div class="ad-note"><strong>Operational boundary:</strong> Courier selection, pickup scheduling, sorting, and rider assignment belong to Seller and Logistics workflows. Admin monitors platform-wide exceptions and policy compliance.</div>
    @else
        <div class="ad-tabs" aria-label="Order status">
            @foreach(['all' => 'All', 'to-process' => 'To Process', 'to-prepare' => 'To Prepare', 'ready-pickup' => 'Ready Pickup', 'shipping' => 'Shipping', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'returns' => 'Returns / Refunds'] as $key => $label)<a class="ad-tab {{ $status === $key ? 'is-active' : '' }}" href="{{ route('admin.orders', ['view' => 'orders', 'status' => $key]) }}">{{ $label }}</a>@endforeach
        </div>
        <section class="ad-card" id="order-table"><div class="ad-filter-bar"><label class="ad-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" data-filter-input="#order-table" placeholder="Search order, buyer, seller, or product"></label><div class="ad-inline-actions"><select class="ad-select"><option>All payment methods</option><option>GCash</option><option>COD</option><option>Card</option></select><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Order list exported">Export</button></div></div><div class="ad-table-wrap"><table class="ad-table"><thead><tr><th>Order</th><th>Buyer / seller</th><th>Items</th><th>Total</th><th>Payment</th><th>Courier</th><th>Status</th><th></th></tr></thead><tbody>@foreach($orders as $order)@if($status === 'all' || strtolower(str_replace(' ', '-', $order['status'])) === $status)<tr data-filter-item data-search="{{ strtolower(implode(' ', $order)) }}"><td><strong>{{ $order['id'] }}</strong><small>{{ $order['date'] }}</small></td><td><strong>{{ $order['buyer'] }}</strong><small>{{ $order['seller'] }}</small></td><td>{{ $order['items'] }}</td><td><strong>₱{{ number_format($order['total'], 2) }}</strong></td><td>{{ $order['payment'] }}</td><td>{{ $order['courier'] }}</td><td><span class="ad-status is-{{ strtolower(str_replace(' ', '-', $order['status'])) }}">{{ $order['status'] }}</span></td><td><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Opening {{ $order['id'] }}">View</button></td></tr>@endif @endforeach</tbody></table></div></section>
    @endif
</div>
@endsection
