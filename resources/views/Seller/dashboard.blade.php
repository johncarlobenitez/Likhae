@extends('layouts.seller')

@section('title', 'Dashboard')
@section('active', 'dashboard')
@section('subtitle', 'Your store performance and operational priorities at a glance.')

@section('content')
@php
    $firstName = data_get($seller, 'first_name') ?: str($seller->name)->before(' ');
    $stats = $dashboardStats;
    $activeOrders = collect($orderStatusCounts)->sum();
@endphp
<div class="sl-page">
    <section class="sl-welcome-panel">
        <div>
            <span class="sl-eyebrow">{{ now()->format('l, F j') }}</span>
            <h2>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ $firstName }}.</h2>
            <p>Your live store data shows <strong>{{ $orderStatusCounts['to-process'] }} new orders</strong> and <strong>{{ $stats['inventory_alerts'] }} inventory alerts</strong> that may need attention.</p>
        </div>
        <div class="sl-welcome-actions">
            <a href="{{ route('seller.products', ['mode' => 'add']) }}" class="sl-btn sl-btn-white">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Add Product
            </a>
            <a href="{{ route('seller.orders', ['status' => 'to-process']) }}" class="sl-btn sl-btn-blue-soft">Process Orders</a>
        </div>
    </section>

    <section class="sl-stat-grid" aria-label="Store summary">
        <x-seller.stat-card label="Today's Sales" :value="'₱'.number_format($stats['today_sales'], 2)" change="Live" icon="sales" />
        <x-seller.stat-card label="Total Orders" :value="number_format($stats['orders'])" change="Database" icon="orders" />
        <x-seller.stat-card label="Revenue" :value="'₱'.number_format($stats['revenue'], 2)" change="Current" icon="revenue" />
        <x-seller.stat-card label="Products Sold" :value="number_format($stats['products_sold'])" change="Units" icon="products" />
        <x-seller.stat-card label="Pending Shipment" :value="number_format($stats['pending_shipment'])" change="Needs action" direction="down" icon="shipping" />
    </section>

    <div class="sl-dashboard-grid">
        <section class="sl-card sl-sales-panel">
            <header class="sl-card-head">
                <div><span class="sl-eyebrow">Performance</span><h2>Sales Overview</h2><p>Revenue collected over the last 7 days.</p></div>
                <a href="{{ route('seller.finance', ['tab' => 'sales']) }}" class="sl-text-link">Finance details</a>
            </header>
            <div class="sl-chart-summary">
                <div><span>Total revenue</span><strong>₱{{ number_format($stats['revenue'], 2) }}</strong></div>
                <span class="sl-positive">Live order data</span>
            </div>
            <div class="sl-bar-chart" aria-label="Sales chart for the last seven days">
                @foreach ($salesChart as $point)
                    <div class="sl-bar-column" title="{{ $point['label'] }}: ₱{{ number_format($point['value'], 2) }}">
                        <span style="height: {{ $point['height'] }}%"></span>
                        <small>{{ $point['label'] }}</small>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="sl-card sl-order-status-panel">
            <header class="sl-card-head">
                <div><span class="sl-eyebrow">Fulfillment</span><h2>Order Status</h2><p>Current order distribution.</p></div>
                <a href="{{ route('seller.orders') }}" class="sl-text-link">View all</a>
            </header>
            <div class="sl-donut-wrap">
                <div class="sl-donut" aria-label="{{ $activeOrders }} orders"><span><strong>{{ $activeOrders }}</strong><small>Orders</small></span></div>
                <div class="sl-chart-legend">
                    <a href="{{ route('seller.orders', ['status' => 'to-process']) }}"><i class="is-blue"></i><span>To Process</span><strong>{{ $orderStatusCounts['to-process'] }}</strong></a>
                    <a href="{{ route('seller.orders', ['status' => 'to-prepare']) }}"><i class="is-indigo"></i><span>To Prepare</span><strong>{{ $orderStatusCounts['to-prepare'] }}</strong></a>
                    <a href="{{ route('seller.orders', ['status' => 'ready-pickup']) }}"><i class="is-slate"></i><span>Ready Pickup</span><strong>{{ $orderStatusCounts['ready-pickup'] }}</strong></a>
                    <a href="{{ route('seller.orders', ['status' => 'shipping']) }}"><i class="is-light"></i><span>In Transit</span><strong>{{ $orderStatusCounts['shipping'] }}</strong></a>
                </div>
            </div>
        </section>
    </div>

    <div class="sl-dashboard-grid sl-dashboard-grid-lower">
        <section class="sl-card">
            <header class="sl-card-head">
                <div><span class="sl-eyebrow">Operations</span><h2>Recent Orders</h2><p>Latest purchases in your workflow.</p></div>
                <a href="{{ route('seller.orders') }}" class="sl-text-link">Manage orders</a>
            </header>
            <div class="sl-table-wrap">
                <table class="sl-table sl-recent-table">
                    <thead><tr><th>Order</th><th>Buyer</th><th>Product</th><th>Total</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($sellerOrders as $order)
                            @php
                                $tone = match (data_get($order, 'status_key')) {
                                    'completed' => 'is-success',
                                    'shipping', 'ready-pickup' => 'is-info',
                                    'to-prepare' => 'is-warning',
                                    'returns', 'cancelled' => 'is-danger',
                                    default => 'is-neutral',
                                };
                            @endphp
                            <tr>
                                <td><strong>#{{ data_get($order, 'id') }}</strong></td>
                                <td>{{ data_get($order, 'buyer') }}</td>
                                <td><span class="sl-table-product">{{ data_get($order, 'product') }}</span></td>
                                <td><strong>₱{{ number_format((float) data_get($order, 'total'), 2) }}</strong></td>
                                <td><span class="sl-status {{ $tone }}">{{ data_get($order, 'status') }}</span></td>
                                <td><a class="sl-icon-link" href="{{ route('seller.orders', ['mode' => 'show', 'order' => data_get($order, 'id')]) }}" aria-label="View order #{{ data_get($order, 'id') }}">→</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No seller orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="sl-card">
            <header class="sl-card-head">
                <div><span class="sl-eyebrow">Stock Control</span><h2>Inventory Alerts</h2><p>Products requiring attention.</p></div>
                <a href="{{ route('seller.products', ['mode' => 'inventory']) }}" class="sl-text-link">Inventory</a>
            </header>
            <div class="sl-alert-list">
                @forelse ($inventoryAlerts as $product)
                    <article class="sl-stock-alert {{ $product['stock'] === 0 ? 'is-critical' : ($product['stock'] <= 5 ? 'is-warning' : 'is-neutral') }}">
                        <span class="sl-stock-icon">{{ $product['stock'] }}</span>
                        <div><small>{{ $product['stock'] === 0 ? 'OUT OF STOCK' : 'LOW STOCK' }}</small><strong>{{ $product['name'] }}</strong><span>{{ $product['stock'] }} remaining</span></div>
                        <a href="{{ route('seller.products', ['mode' => 'inventory']) }}" class="sl-btn sl-btn-ghost sl-btn-sm">Update</a>
                    </article>
                @empty
                    <div class="sl-empty-state"><h3>Inventory is healthy</h3><p>No low-stock products right now.</p></div>
                @endforelse
            </div>
        </aside>
    </div>

    <section class="sl-card sl-flow-card">
        <header class="sl-card-head">
            <div><span class="sl-eyebrow">Fulfillment Workflow</span><h2>From Order to Delivery</h2><p>Every stage below is linked to Seller Center operations.</p></div>
        </header>
        <div class="sl-process-flow">
            @foreach ([
                ['1', 'New Order', 'Review details'],
                ['2', 'Prepare', 'Pack and print waybill'],
                ['3', 'Assign Courier', 'Choose logistics'],
                ['4', 'Pickup', 'Hand over parcel'],
                ['5', 'In Transit', 'Monitor shipment'],
                ['6', 'Delivered', 'Receive confirmation'],
            ] as $step)
                <div class="sl-process-step"><span>{{ $step[0] }}</span><div><strong>{{ $step[1] }}</strong><small>{{ $step[2] }}</small></div></div>
            @endforeach
        </div>
    </section>
</div>
@endsection
