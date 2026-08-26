@extends('Seller.layouts.app')
@section('title', 'Reports — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/analytics.css') @endpush

@section('content')
<x-seller.page-header eyebrow="ANALYTICS" title="Reports & Analytics" description="Measure revenue, profit, orders, and product performance.">
    <x-slot:actions><button class="btn-secondary">Export CSV</button><button class="btn-secondary">Export PDF</button><button class="btn-primary">Print Report</button></x-slot:actions>
</x-seller.page-header>

<div class="filter-bar"><div><label class="form-label">FROM</label><input class="filter-input" type="date"></div><div><label class="form-label">TO</label><input class="filter-input" type="date"></div><div class="filter-pills self-end"><button>Today</button><button class="is-active">7 Days</button><button>30 Days</button><button>Custom</button></div></div>

<div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-seller.kpi-card label="GROSS REVENUE" value="₱146,820"/>
    <x-seller.kpi-card label="NET REVENUE" value="₱132,540"/>
    <x-seller.kpi-card label="PROFIT" value="₱58,420" meta="+9.2%" tone="success"/>
    <x-seller.kpi-card label="ORDERS" value="214"/>
</div>

<div class="mt-5 grid gap-5 xl:grid-cols-2">
    <section class="panel p-5"><div class="panel-header !p-0 !pb-4"><div><p class="panel-eyebrow">REVENUE</p><h2>Revenue vs Profit</h2></div></div><svg class="sales-chart" viewBox="0 0 600 220" preserveAspectRatio="none"><path class="chart-grid" d="M0 40H600M0 90H600M0 140H600M0 190H600"/><path class="chart-previous" d="M0 170 C80 160,110 145,160 150 S260 112,320 125 S440 90,600 105"/><path class="chart-current" d="M0 180 C70 150,110 135,170 142 S260 90,340 102 S450 58,600 72"/></svg></section>
    <section class="panel p-5"><div class="panel-header !p-0 !pb-4"><div><p class="panel-eyebrow">ORDERS</p><h2>Orders Over Time</h2></div></div><div class="bar-chart">@foreach([42,58,36,72,65,88,76] as $v)<span style="height:{{$v}}%"></span>@endforeach</div></section>
</div>

<section class="panel mt-5"><div class="panel-header"><div><p class="panel-eyebrow">PRODUCT PERFORMANCE</p><h2>Top Products</h2></div></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Product</th><th>Units Sold</th><th>Revenue</th><th>Cost</th><th>Profit</th><th>Margin</th></tr></thead><tbody>
@foreach([
['Handwoven Rattan Tote Bag','96','₱82,240','₱43,200','₱39,040','47.5%'],
['Premium Philippine Tablea','88','₱28,952','₱15,840','₱13,112','45.3%'],
['Capiz Shell Pendant Lamp','17','₱32,283','₱20,060','₱12,223','37.9%'],
] as $r)<tr>@foreach($r as $c)<td>{{ $c }}</td>@endforeach</tr>@endforeach
</tbody></table></div></section>
@endsection
