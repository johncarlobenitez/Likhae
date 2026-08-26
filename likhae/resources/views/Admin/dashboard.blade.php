@extends('Admin.layouts.app')
@section('title', 'Dashboard — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">OVERVIEW</p>
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="page-description">Platform-wide overview of users, orders, and revenue.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a class="btn-secondary" href="{{ url('/admin/reports') }}">Generate Report</a>
    </div>
</div>

{{-- KPI Cards --}}
<div class="admin-stat-grid">
    <div class="kpi-card">
        <p class="kpi-label">TOTAL USERS</p>
        <div class="mt-3 flex items-end justify-between gap-4">
            <strong class="text-2xl font-black tracking-[-.04em]">12,480</strong>
            <span class="kpi-meta text-[#079B72]">+3.2% this week</span>
        </div>
    </div>
    <div class="kpi-card">
        <p class="kpi-label">TOTAL ORDERS</p>
        <div class="mt-3 flex items-end justify-between gap-4">
            <strong class="text-2xl font-black tracking-[-.04em]">8,341</strong>
            <span class="kpi-meta text-[#079B72]">+7.1% this week</span>
        </div>
    </div>
    <div class="kpi-card">
        <p class="kpi-label">GROSS REVENUE</p>
        <div class="mt-3 flex items-end justify-between gap-4">
            <strong class="text-2xl font-black tracking-[-.04em]">₱4.2M</strong>
            <span class="kpi-meta text-[#079B72]">+12.4% this month</span>
        </div>
    </div>
    <div class="kpi-card">
        <p class="kpi-label">PENDING APPROVALS</p>
        <div class="mt-3 flex items-end justify-between gap-4">
            <strong class="text-2xl font-black tracking-[-.04em]">24</strong>
            <span class="kpi-meta text-[#D99A22]">Needs attention</span>
        </div>
    </div>
</div>

{{-- Action Queue + Quick Links --}}
<div class="admin-grid mt-5">
    <div class="panel">
        <div class="panel-header">
            <div>
                <p class="panel-eyebrow">ACTION REQUIRED</p>
                <h2>Work Queue</h2>
            </div>
            <span class="text-xs text-[#96918B]">Updated just now</span>
        </div>
        <div class="action-grid">
            @foreach([
                ['Pending Seller Approvals', 14, '/admin/sellers/approvals'],
                ['Pending Rider Approvals',  10, '/admin/riders'],
                ['Open Refund Requests',      6, '/admin/refunds'],
                ['Flagged Products',           4, '/admin/products'],
                ['Disputed Orders',            3, '/admin/orders'],
                ['Unresolved Reports',         2, '/admin/reports'],
            ] as [$label, $count, $url])
                <a href="{{ url($url) }}" class="action-tile">
                    <span>{{ $label }}</span>
                    <strong>{{ $count }}</strong>
                    <svg viewBox="0 0 24 24"><path d="m9 5 7 7-7 7"/></svg>
                </a>
            @endforeach
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <p class="panel-eyebrow">QUICK ACTIONS</p>
                <h2>Common Tasks</h2>
            </div>
        </div>
        <div class="quick-list">
            <a href="{{ url('/admin/users') }}">Manage Users <span>→</span></a>
            <a href="{{ url('/admin/sellers/approvals') }}">Review Seller Applications <span>→</span></a>
            <a href="{{ url('/admin/categories') }}">Manage Categories <span>→</span></a>
            <a href="{{ url('/admin/payments') }}">Monitor Payments <span>→</span></a>
            <a href="{{ url('/admin/settings') }}">Platform Settings <span>→</span></a>
        </div>
    </div>
</div>

{{-- Revenue Chart --}}
<div class="panel mt-5">
    <div class="panel-header">
        <div>
            <p class="panel-eyebrow">PERFORMANCE</p>
            <h2>Revenue Overview</h2>
        </div>
        <div class="filter-pills">
            <button>Today</button>
            <button class="is-active">7 Days</button>
            <button>30 Days</button>
        </div>
    </div>
    <div class="chart-summary">
        <div><span>Gross Revenue</span><strong>₱4,218,600</strong></div>
        <div><span>Net Revenue</span><strong>₱3,796,740</strong></div>
        <div><span>Total Orders</span><strong>8,341</strong></div>
        <div><span>Avg. Order Value</span><strong>₱506</strong></div>
    </div>
    <svg class="sales-chart" viewBox="0 0 720 220" preserveAspectRatio="none" aria-label="Revenue chart">
        <path class="chart-grid" d="M0 40H720M0 90H720M0 140H720M0 190H720"/>
        <path class="chart-previous" d="M0 170 C80 155,120 145,200 130 S320 120,400 125 S520 100,620 95 S680 88,720 90"/>
        <path class="chart-current" d="M0 175 C70 160,130 115,200 138 S300 75,380 95 S460 55,540 72 S640 30,720 48"/>
    </svg>
    <div class="chart-legend">
        <span><i class="current"></i>Current period</span>
        <span><i></i>Previous period</span>
    </div>
</div>

{{-- Recent Orders + Top Sellers --}}
<div class="mt-5 grid gap-5 xl:grid-cols-[1.4fr_.6fr]">
    <div class="panel">
        <div class="panel-header">
            <div><p class="panel-eyebrow">ORDERS</p><h2>Recent Orders</h2></div>
            <a href="{{ url('/admin/orders') }}" class="text-link">View all</a>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th><th>Buyer</th><th>Seller</th><th>Amount</th><th>Payment</th><th>Status</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([
                        ['LH-20260820-0231','Juan Dela Cruz','Maria\'s Finds','₱1,498','GCash','Completed'],
                        ['LH-20260820-0214','Anne Reyes','Tablea Shop','₱899','COD','To Prepare'],
                        ['LH-20260819-0188','Paolo Lim','Rattan Co.','₱2,347','Maya','Shipped'],
                        ['LH-20260819-0175','Lea Santos','Capiz Crafts','₱560','GCash','Pending'],
                    ] as [$id, $buyer, $seller, $amount, $payment, $status])
                        <tr>
                            <td><strong>{{ $id }}</strong></td>
                            <td>{{ $buyer }}</td>
                            <td>{{ $seller }}</td>
                            <td>{{ $amount }}</td>
                            <td>{{ $payment }}</td>
                            <td>
                                @php
                                    $tone = match($status) {
                                        'Completed' => 'status-success',
                                        'Shipped'   => 'status-info',
                                        'To Prepare'=> 'status-warning',
                                        default     => 'status-neutral',
                                    };
                                @endphp
                                <span class="status-badge {{ $tone }}"><span class="status-dot"></span>{{ $status }}</span>
                            </td>
                            <td><a class="text-link" href="{{ url('/admin/orders') }}">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div><p class="panel-eyebrow">SELLERS</p><h2>Top Sellers</h2></div>
            <a href="{{ url('/admin/sellers/approvals') }}" class="text-link">Manage</a>
        </div>
        <div class="divide-y divide-[#E5E0D9]">
            @foreach([
                ["Maria's Local Finds", '₱146,820', 214],
                ['Tablea Gift Shop',    '₱98,340',  178],
                ['Rattan Co.',          '₱87,120',  143],
                ['Capiz Crafts PH',     '₱64,500',  112],
                ['Bayong Atbp.',        '₱52,880',   98],
            ] as [$name, $revenue, $orders])
                <div class="flex items-center justify-between gap-4 py-3 px-5">
                    <div>
                        <strong class="block text-xs">{{ $name }}</strong>
                        <span class="text-[10px] text-[#96918B]">{{ $orders }} orders</span>
                    </div>
                    <span class="text-xs font-black text-[#079B72]">{{ $revenue }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
