@extends('Admin.layouts.app')
@section('title', 'Reports — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">INSIGHTS</p>
        <h1 class="page-title">Reports</h1>
        <p class="page-description">Platform-wide analytics and downloadable reports.</p>
    </div>
    <div class="flex gap-2">
        <select class="filter-input">
            <option>Last 7 Days</option>
            <option>Last 30 Days</option>
            <option>Last 3 Months</option>
            <option>This Year</option>
        </select>
        <button class="btn-primary">Export CSV</button>
    </div>
</div>

{{-- Summary KPIs --}}
<div class="admin-stat-grid">
    @foreach([['GROSS REVENUE','₱4,218,600','+12.4%','text-[#079B72]'],['TOTAL ORDERS','8,341','+7.1%','text-[#079B72]'],['NEW USERS','1,240','+3.2%','text-[#079B72]'],['REFUND RATE','0.49%','-0.1%','text-[#079B72]']] as [$label,$val,$meta,$tone])
        <div class="kpi-card">
            <p class="kpi-label">{{ $label }}</p>
            <div class="mt-3 flex items-end justify-between gap-4">
                <strong class="text-2xl font-black tracking-[-.04em]">{{ $val }}</strong>
                <span class="kpi-meta {{ $tone }}">{{ $meta }}</span>
            </div>
        </div>
    @endforeach
</div>

{{-- Revenue Chart --}}
<div class="panel mt-5">
    <div class="panel-header">
        <div><p class="panel-eyebrow">REVENUE</p><h2>Revenue Trend</h2></div>
        <div class="filter-pills">
            <button>Daily</button><button class="is-active">Weekly</button><button>Monthly</button>
        </div>
    </div>
    <div class="chart-summary">
        <div><span>Gross Revenue</span><strong>₱4,218,600</strong></div>
        <div><span>Platform Fee (5%)</span><strong>₱210,930</strong></div>
        <div><span>Seller Payouts</span><strong>₱4,007,670</strong></div>
        <div><span>Avg. Order Value</span><strong>₱506</strong></div>
    </div>
    <svg class="sales-chart" viewBox="0 0 720 220" preserveAspectRatio="none">
        <path class="chart-grid" d="M0 40H720M0 90H720M0 140H720M0 190H720"/>
        <path class="chart-previous" d="M0 170 C80 155,120 145,200 130 S320 120,400 125 S520 100,620 95 S680 88,720 90"/>
        <path class="chart-current" d="M0 175 C70 160,130 115,200 138 S300 75,380 95 S460 55,540 72 S640 30,720 48"/>
    </svg>
    <div class="chart-legend">
        <span><i class="current"></i>Current period</span>
        <span><i></i>Previous period</span>
    </div>
</div>

{{-- Report Downloads --}}
<div class="panel mt-5">
    <div class="panel-header">
        <div><p class="panel-eyebrow">DOWNLOADS</p><h2>Available Reports</h2></div>
    </div>
    <div class="divide-y divide-[#E5E0D9]">
        @foreach([
            ['Sales Report',          'Complete breakdown of all sales transactions.',       'Aug 2026'],
            ['User Growth Report',    'New registrations and user activity summary.',        'Aug 2026'],
            ['Seller Performance',    'Top sellers, revenue, and order fulfillment rates.',  'Aug 2026'],
            ['Delivery Report',       'Rider performance and delivery success rates.',       'Aug 2026'],
            ['Refund & Returns',      'All refund requests and resolution outcomes.',        'Aug 2026'],
            ['Payment Summary',       'Breakdown by payment method and transaction status.','Aug 2026'],
        ] as [$title, $desc, $period])
            <div class="flex items-center justify-between gap-4 p-5">
                <div>
                    <strong class="block text-sm">{{ $title }}</strong>
                    <p class="mt-0.5 text-xs text-[#6B6864]">{{ $desc }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <span class="text-[10px] text-[#96918B]">{{ $period }}</span>
                    <button class="btn-secondary text-[10px]">Download CSV</button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
