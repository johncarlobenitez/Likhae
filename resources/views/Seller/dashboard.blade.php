@extends('layouts.seller')

@section('title', 'Dashboard')
@section('active', 'dashboard')
@section('subtitle', 'Your store performance and operational priorities at a glance.')

@section('content')
<div class="sl-page">
    <section class="sl-welcome-panel">
        <div>
            <span class="sl-eyebrow">Friday, September 4</span>
            <h2>Good morning, Mariel.</h2>
            <p>Your store is healthy. You have <strong>5 new orders</strong> and <strong>2 inventory alerts</strong> that need attention.</p>
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
        <x-seller.stat-card label="Today's Sales" value="₱25,000" change="12.5%" icon="sales" />
        <x-seller.stat-card label="Total Orders" value="35" change="8.2%" icon="orders" />
        <x-seller.stat-card label="Revenue" value="₱184,650" change="6.4%" icon="revenue" />
        <x-seller.stat-card label="Products Sold" value="82" change="4.1%" icon="products" />
        <x-seller.stat-card label="Pending Shipment" value="8" change="2 due soon" direction="down" icon="shipping" />
    </section>

    <div class="sl-dashboard-grid">
        <section class="sl-card sl-sales-panel">
            <header class="sl-card-head">
                <div><span class="sl-eyebrow">Performance</span><h2>Sales Overview</h2><p>Revenue collected over the last 7 days.</p></div>
                <select class="sl-select sl-select-sm" aria-label="Sales period">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                    <option>This year</option>
                </select>
            </header>
            <div class="sl-chart-summary">
                <div><span>Total revenue</span><strong>₱184,650</strong></div>
                <span class="sl-positive">↑ 6.4% vs previous week</span>
            </div>
            <div class="sl-bar-chart" aria-label="Sales chart from Saturday to Friday">
                @foreach ([42, 58, 48, 72, 64, 83, 76] as $height)
                    <div class="sl-bar-column"><span style="height: {{ $height }}%"></span><small>{{ ['Sat','Sun','Mon','Tue','Wed','Thu','Fri'][$loop->index] }}</small></div>
                @endforeach
            </div>
        </section>

        <section class="sl-card sl-order-status-panel">
            <header class="sl-card-head">
                <div><span class="sl-eyebrow">Fulfillment</span><h2>Order Status</h2><p>Current order distribution.</p></div>
                <a href="{{ route('seller.orders') }}" class="sl-text-link">View all</a>
            </header>
            <div class="sl-donut-wrap">
                <div class="sl-donut" aria-label="35 active orders"><span><strong>35</strong><small>Orders</small></span></div>
                <div class="sl-chart-legend">
                    <a href="{{ route('seller.orders', ['status' => 'to-process']) }}"><i class="is-blue"></i><span>To Process</span><strong>5</strong></a>
                    <a href="{{ route('seller.orders', ['status' => 'to-prepare']) }}"><i class="is-indigo"></i><span>To Prepare</span><strong>8</strong></a>
                    <a href="{{ route('seller.orders', ['status' => 'ready-pickup']) }}"><i class="is-slate"></i><span>Ready Pickup</span><strong>8</strong></a>
                    <a href="{{ route('seller.orders', ['status' => 'shipping']) }}"><i class="is-light"></i><span>In Transit</span><strong>14</strong></a>
                </div>
            </div>
        </section>
    </div>

    <div class="sl-dashboard-grid sl-dashboard-grid-lower">
        <section class="sl-card">
            <header class="sl-card-head">
                <div><span class="sl-eyebrow">Operations</span><h2>Recent Orders</h2><p>Latest purchases waiting in your workflow.</p></div>
                <a href="{{ route('seller.orders') }}" class="sl-text-link">Manage orders</a>
            </header>
            <div class="sl-table-wrap">
                <table class="sl-table sl-recent-table">
                    <thead><tr><th>Order</th><th>Buyer</th><th>Product</th><th>Total</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($sellerOrders->take(5) as $order)
                            @php
                                $tone = match (data_get($order, 'status_key')) {
                                    'completed' => 'is-success',
                                    'shipping', 'ready-pickup' => 'is-info',
                                    'to-prepare' => 'is-warning',
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
                        @endforeach
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
                <article class="sl-stock-alert is-critical">
                    <span class="sl-stock-icon">!</span>
                    <div><small>LOW STOCK</small><strong>Mechanical Keyboard</strong><span>3 remaining</span></div>
                    <button type="button" class="sl-btn sl-btn-ghost sl-btn-sm" data-stock-update data-product="Mechanical Keyboard">Update</button>
                </article>
                <article class="sl-stock-alert is-warning">
                    <span class="sl-stock-icon">!</span>
                    <div><small>LOW STOCK</small><strong>Gaming Mouse</strong><span>5 remaining</span></div>
                    <button type="button" class="sl-btn sl-btn-ghost sl-btn-sm" data-stock-update data-product="Gaming Mouse">Update</button>
                </article>
                <article class="sl-stock-alert is-neutral">
                    <span class="sl-stock-icon">0</span>
                    <div><small>OUT OF STOCK</small><strong>7-in-1 USB-C Hub</strong><span>Listing archived</span></div>
                    <a href="{{ route('seller.products', ['mode' => 'inventory']) }}" class="sl-btn sl-btn-ghost sl-btn-sm">Review</a>
                </article>
            </div>
        </aside>
    </div>

    <section class="sl-card sl-flow-card">
        <header class="sl-card-head">
            <div><span class="sl-eyebrow">Fulfillment Workflow</span><h2>From Order to Delivery</h2><p>Use these stages to keep every shipment moving.</p></div>
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
