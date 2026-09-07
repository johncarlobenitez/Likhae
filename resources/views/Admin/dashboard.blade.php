@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Platform health, risk signals, and operational priorities at a glance.')
@section('active', 'dashboard')

@section('content')
@php
    $recentOrders = [
        ['id' => '#LK-10482', 'buyer' => 'Angela Cruz', 'seller' => 'LIKHA Studio', 'total' => 12990, 'delivery' => 'J&T Express', 'status' => 'Processing'],
        ['id' => '#LK-10481', 'buyer' => 'Marco Reyes', 'seller' => 'North & Pine', 'total' => 5580, 'delivery' => 'Flash Express', 'status' => 'Shipping'],
        ['id' => '#LK-10480', 'buyer' => 'Sarah Lim', 'seller' => 'MNL Tech', 'total' => 1490, 'delivery' => 'LBC', 'status' => 'Delivered'],
        ['id' => '#LK-10479', 'buyer' => 'Daniel Tan', 'seller' => 'Casa Local', 'total' => 3490, 'delivery' => 'Local Courier', 'status' => 'Completed'],
        ['id' => '#LK-10478', 'buyer' => 'Patricia Go', 'seller' => 'Paper & Loom', 'total' => 1980, 'delivery' => 'J&T Express', 'status' => 'Under Review'],
    ];
@endphp

<div class="ad-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">Control center</span>
            <h2>Marketplace overview</h2>
            <p>Prioritized for review speed, platform safety, and financial visibility.</p>
        </div>
        <div class="ad-inline-actions">
            <a href="{{ route('admin.reports') }}" class="ad-btn ad-btn-secondary">Export report</a>
            <a href="{{ route('admin.messages', ['tab' => 'announcements']) }}" class="ad-btn ad-btn-primary">New announcement</a>
        </div>
    </div>

    <section class="ad-stat-grid" aria-label="Platform summary">
        <x-admin.stat-card label="Pending applications" value="24" trend="+6" detail="since yesterday" icon="users" tone="warning" :href="route('admin.registrations')" />
        <x-admin.stat-card label="Active users" value="12,480" trend="+4.2%" detail="this month" icon="users" :href="route('admin.users')" />
        <x-admin.stat-card label="Platform reports" value="14" trend="+2" detail="generated this week" icon="reports" :href="route('admin.reports')" />
        <x-admin.stat-card label="Flagged products" value="17" trend="5 high risk" detail="human review needed" icon="flag" tone="danger" :href="route('admin.products', ['view' => 'monitor'])" />
        <x-admin.stat-card label="Open disputes" value="11" trend="3 urgent" detail="awaiting decision" icon="case" tone="warning" :href="route('admin.complaints')" />
        <x-admin.stat-card label="Platform revenue" value="₱420K" trend="10%" detail="commission rate" icon="money" :href="route('admin.finance')" />
    </section>

    <section class="ad-dashboard-grid">
        <article class="ad-card ad-chart-card">
            <header class="ad-card-head">
                <div><span class="ad-overline">Performance</span><h2>Marketplace revenue</h2><p>Gross merchandise value for the last seven days.</p></div>
                <select class="ad-select" aria-label="Revenue period"><option>Last 7 days</option><option>Last 30 days</option><option>This quarter</option></select>
            </header>
            <div class="ad-card-body">
                <div class="ad-chart-summary">
                    <div><small>Gross merchandise value</small><strong>₱4,206,500</strong><small><span style="color:#2563eb;font-weight:600">↑ 6.4%</span> vs previous week</small></div>
                    <div class="ad-chart-legend"><span><i></i> Gross sales</span><span><i class="is-muted"></i> Prior period</span></div>
                </div>
                <div class="ad-bar-chart" aria-label="Revenue bar chart">
                    @foreach([45, 62, 51, 74, 66, 86, 78] as $height)
                        <div class="ad-bar"><i style="height: {{ $height }}%"></i><span>{{ ['Sat','Sun','Mon','Tue','Wed','Thu','Fri'][$loop->index] }}</span></div>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="ad-card">
            <header class="ad-card-head">
                <div><span class="ad-overline">Order health</span><h2>Fulfillment status</h2><p>386 orders placed today.</p></div>
                <span class="ad-overline">Read-only summary</span>
            </header>
            <div class="ad-card-body ad-donut-wrap">
                <div class="ad-donut"><strong>386<small>orders</small></strong></div>
                <div class="ad-donut-list">
                    <div><i></i><span>Processing</span><b>162</b></div>
                    <div><i></i><span>Shipping</span><b>93</b></div>
                    <div><i></i><span>Delivered</span><b>69</b></div>
                    <div><i></i><span>Completed / other</span><b>62</b></div>
                </div>
            </div>
        </article>
    </section>

    <section class="ad-dashboard-split">
        <article class="ad-card">
            <header class="ad-card-head">
                <div><span class="ad-overline">Operations</span><h2>Recent orders</h2><p>Latest marketplace purchases across all sellers.</p></div>
                <span class="ad-overline">Seller and logistics owned</span>
            </header>
            <div class="ad-table-wrap">
                <table class="ad-table">
                    <thead><tr><th>Order</th><th>Buyer</th><th>Seller</th><th>Total</th><th>Delivery</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td><strong>{{ $order['id'] }}</strong></td><td>{{ $order['buyer'] }}</td><td>{{ $order['seller'] }}</td>
                                <td><strong>₱{{ number_format($order['total'], 2) }}</strong></td><td>{{ $order['delivery'] }}</td>
                                <td><span class="ad-status is-{{ strtolower(str_replace(' ', '-', $order['status'])) }}">{{ $order['status'] }}</span></td>
                                <td><span class="ad-muted">Fulfillment access restricted</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <article class="ad-card">
            <header class="ad-card-head">
                <div><span class="ad-overline">Priority queue</span><h2>Needs attention</h2><p>Risk-ranked work for administrators.</p></div>
            </header>
            <div class="ad-queue">
                <a href="{{ route('admin.products', ['view' => 'monitor']) }}" class="ad-queue-item is-danger"><span class="ad-queue-icon">5</span><span class="ad-queue-copy"><strong>High-risk product flags</strong><span>Possible weapons or controlled items</span></span><span>→</span></a>
                <a href="{{ route('admin.registrations') }}" class="ad-queue-item is-warning"><span class="ad-queue-icon">24</span><span class="ad-queue-copy"><strong>Pending applications</strong><span>7 older than 24 hours</span></span><span>→</span></a>
                <a href="{{ route('admin.complaints') }}" class="ad-queue-item is-warning"><span class="ad-queue-icon">3</span><span class="ad-queue-copy"><strong>Urgent disputes</strong><span>Evidence review due today</span></span><span>→</span></a>
            </div>
        </article>
    </section>

    <div class="ad-note">
        <strong>Governance note:</strong>
        Automated product monitoring only creates risk signals. An administrator must review evidence before removing a listing, warning a user, or applying a ban.
    </div>
</div>
@endsection
