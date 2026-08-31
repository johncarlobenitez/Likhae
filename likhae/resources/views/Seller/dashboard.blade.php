@extends('Seller.layouts.app')
@section('title', 'Dashboard — LIKHAE Seller')

@section('content')
<x-seller.page-header eyebrow="OVERVIEW" title="Good evening, Maria" description="Here’s what needs your attention and how your store is performing today.">
    <x-slot:actions>
        <a class="btn-secondary" href="{{ url('/seller/reports') }}">Generate Report</a>
        <a class="btn-primary" href="{{ url('/seller/products/create') }}">Add Product</a>
    </x-slot:actions>
</x-seller.page-header>

<section class="store-strip">
    <div>
        <span class="meta-label">STORE STATUS</span>
        <div class="mt-2 flex items-center gap-2"><x-seller.status-badge status="Active"/><strong class="text-sm">Maria’s Local Finds</strong></div>
    </div>
    <div><span class="meta-label">RATING</span><strong>4.8 / 5</strong></div>
    <div><span class="meta-label">FOLLOWERS</span><strong>3,284</strong></div>
    <div><span class="meta-label">PRODUCTS</span><strong>128</strong></div>
    <div><span class="meta-label">RESPONSE RATE</span><strong>96%</strong></div>
</section>

<section class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-seller.kpi-card label="TODAY'S SALES" value="₱24,680" meta="+12.4%" tone="success"/>
    <x-seller.kpi-card label="ORDERS" value="38" meta="8 require action" tone="warning"/>
    <x-seller.kpi-card label="PRODUCT VIEWS" value="4,281" meta="+8.9%" tone="success"/>
    <x-seller.kpi-card label="CONVERSION RATE" value="3.7%" meta="+0.5%" tone="success"/>
</section>

<section class="dashboard-grid mt-5">
    <div class="panel">
        <div class="panel-header"><div><p class="panel-eyebrow">ACTION REQUIRED</p><h2>Work Queue</h2></div><span class="text-xs text-[#96918B]">Updated just now</span></div>
        <div class="action-grid">
            @foreach([
                ['New Orders',8,'/seller/orders?status=new'],
                ['To Prepare',12,'/seller/orders?status=to-prepare'],
                ['Ready for Pickup',5,'/seller/orders?status=ready'],
                ['Low Stock',7,'/seller/inventory?status=low'],
                ['Messages',4,'/seller/messages'],
                ['Returns',2,'/seller/orders/returns'],
            ] as [$label,$count,$url])
                <a href="{{ url($url) }}" class="action-tile"><span>{{ $label }}</span><strong>{{ $count }}</strong><svg viewBox="0 0 24 24"><path d="m9 5 7 7-7 7"/></svg></a>
            @endforeach
        </div>
    </div>

    <div class="panel">
        <div class="panel-header"><div><p class="panel-eyebrow">QUICK ACTIONS</p><h2>Common Tasks</h2></div></div>
        <div class="quick-list">
            <a href="{{ url('/seller/products/create') }}">Add Product <span>→</span></a>
            <a href="{{ url('/seller/inventory') }}">Update Inventory <span>→</span></a>
            <a href="{{ url('/seller/marketing') }}">Create Voucher <span>→</span></a>
            <a href="{{ url('/seller/shipping/pickups') }}">Schedule Pickup <span>→</span></a>
            <a href="{{ url('/seller/messages') }}">View Messages <span>→</span></a>
        </div>
    </div>
</section>

<section class="mt-5 grid gap-5 xl:grid-cols-[1.45fr_.85fr]">
    <div class="panel">
        <div class="panel-header">
            <div><p class="panel-eyebrow">PERFORMANCE</p><h2>Sales Overview</h2></div>
            <div class="filter-pills"><button>Today</button><button class="is-active">7 Days</button><button>30 Days</button><button>Custom</button></div>
        </div>
        <div class="chart-summary">
            <div><span>Gross Sales</span><strong>₱146,820</strong></div>
            <div><span>Net Sales</span><strong>₱132,540</strong></div>
            <div><span>Orders</span><strong>214</strong></div>
            <div><span>Avg. Order</span><strong>₱686</strong></div>
        </div>
        <svg class="sales-chart" viewBox="0 0 720 220" preserveAspectRatio="none" aria-label="Revenue chart">
            <path class="chart-grid" d="M0 40H720M0 90H720M0 140H720M0 190H720"/>
            <path class="chart-previous" d="M0 170 C80 150,90 160,160 128 S260 120,320 130 S430 95,500 118 S620 80,720 92"/>
            <path class="chart-current" d="M0 180 C70 165,120 120,180 145 S260 80,340 102 S420 60,500 84 S620 35,720 55"/>
        </svg>
        <div class="chart-legend"><span><i class="current"></i>Current period</span><span><i></i>Previous period</span></div>
    </div>

    <div class="panel">
        <div class="panel-header"><div><p class="panel-eyebrow">INVENTORY</p><h2>Low Stock</h2></div><a href="{{ url('/seller/inventory') }}" class="text-link">View all</a></div>
        <div class="divide-y divide-[#E5E0D9]">
            @foreach([
                ['Baseus Wireless Earbuds','BAS-A3I-BLK',4],
                ['Capiz Shell Pendant Lamp','CAP-PEN-WHT',6],
                ['Rattan Tote Bag','RAT-TOTE-NAT',3],
                ['Tablea Gift Pack','TAB-250-BX',5],
            ] as [$name,$sku,$stock])
                <div class="flex items-center justify-between gap-4 py-3">
                    <div><strong class="block text-xs">{{ $name }}</strong><span class="text-[10px] text-[#96918B]">{{ $sku }}</span></div>
                    <span class="text-xs font-black text-[#D99A22]">{{ $stock }} left</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="panel mt-5">
    <div class="panel-header"><div><p class="panel-eyebrow">ORDERS</p><h2>Recent Orders</h2></div><a href="{{ url('/seller/orders') }}" class="text-link">View all orders</a></div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Order ID</th><th>Customer</th><th>Items</th><th>Date</th><th>Amount</th><th>Payment</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach([
                    ['LH-20260820-0231','Juan Dela Cruz','2 items','Aug 20 · 8:31 PM','₱1,498','GCash','To Prepare'],
                    ['LH-20260820-0214','Anne Reyes','1 item','Aug 20 · 7:16 PM','₱899','COD','New Order'],
                    ['LH-20260819-0188','Paolo Lim','3 items','Aug 19 · 5:42 PM','₱2,347','Maya','Ready for Pickup'],
                ] as $order)
                    <tr>
                        @foreach(array_slice($order,0,6) as $cell)<td>{{ $cell }}</td>@endforeach
                        <td><x-seller.status-badge :status="$order[6]"/></td>
                        <td><a class="text-link" href="{{ url('/seller/orders/LH-20260820-0231') }}">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
