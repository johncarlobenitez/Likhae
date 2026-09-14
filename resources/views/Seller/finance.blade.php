@extends('layouts.seller')

@php
    $currentTab = $tab ?? request('tab', 'sales');
    $stats = $financeStats;
    $maxTrend = max(1, (float) $financeTrend->max('value'));
    $rateLabel = number_format($commissionRate * 100, 1).'% rate';
@endphp

@section('title', 'Finance')
@section('active', 'finance')
@section('subtitle', 'Monitor revenue, platform fees, balances, and payouts.')

@section('content')
<div class="sl-page">
    <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Financial Management</span><h2>Finance Overview</h2><p>Figures are calculated from seller orders and transactions.</p></div><div class="sl-toolbar-group"><a href="{{ route('seller.finance.statement') }}" class="sl-btn sl-btn-ghost">Download Statement</a><a href="{{ route('seller.reports') }}" class="sl-btn sl-btn-primary">Generate Report</a></div></div>
    <section class="sl-stat-grid sl-stat-grid-4">
        <x-seller.stat-card label="Gross Sales" :value="'₱'.number_format($stats['gross'],2)" change="Live" icon="sales" />
        <x-seller.stat-card label="Commission" :value="'₱'.number_format($stats['commission'],2)" :change="$rateLabel" direction="down" icon="finance" />
        <x-seller.stat-card label="Net Earnings" :value="'₱'.number_format($stats['net'],2)" change="After commission" icon="revenue" />
        <x-seller.stat-card label="Pending Balance" :value="'₱'.number_format($stats['pending'],2)" change="Open orders" icon="finance" />
    </section>

    <nav class="sl-tabs"><a href="{{ route('seller.finance',['tab'=>'sales']) }}" class="{{ $currentTab==='sales'?'is-active':'' }}">Sales Overview</a><a href="{{ route('seller.finance',['tab'=>'transactions']) }}" class="{{ $currentTab==='transactions'?'is-active':'' }}">Transactions</a><a href="{{ route('seller.finance',['tab'=>'payouts']) }}" class="{{ $currentTab==='payouts'?'is-active':'' }}">Payouts</a></nav>

    @if($currentTab === 'sales')
        <div class="sl-dashboard-grid">
            <section class="sl-card sl-sales-panel"><header class="sl-card-head"><div><span class="sl-eyebrow">Net Sales</span><h2>Earnings Trend</h2><p>Last 30 days of seller order value.</p></div></header><div class="sl-chart-summary"><div><span>Net earnings</span><strong>₱{{ number_format($stats['net'],2) }}</strong></div><span class="sl-positive">Database-backed</span></div><div class="sl-bar-chart" aria-label="30-day sales chart">@foreach($financeTrend->take(-10) as $point)<div class="sl-bar-column" title="{{ $point['date']->format('M d') }}: ₱{{ number_format($point['value'],2) }}"><span style="height:{{ max(6,(int) round(($point['value']/$maxTrend)*100)) }}%"></span><small>{{ $point['date']->format('d') }}</small></div>@endforeach</div></section>
            <section class="sl-card"><header class="sl-card-head"><div><span class="sl-eyebrow">Deductions</span><h2>Fee Breakdown</h2><p>Current marketplace commission calculation.</p></div></header><div class="sl-fee-list"><div><span><i class="is-blue"></i>Marketplace commission</span><strong>₱{{ number_format($stats['commission'],2) }}</strong></div><div><span><i class="is-indigo"></i>Net seller earnings</span><strong>₱{{ number_format($stats['net'],2) }}</strong></div></div><div class="sl-fee-total"><span>Gross sales</span><strong>₱{{ number_format($stats['gross'],2) }}</strong></div></section>
        </div>
    @elseif($currentTab === 'payouts')
        <section class="sl-card sl-payout-hero"><div><span class="sl-eyebrow">Available / Pending</span><h2>₱{{ number_format($stats['pending'],2) }}</h2><p>This amount comes from open fulfillment orders and changes as orders complete.</p></div><span class="sl-status is-info">Calculated</span></section>
        <section class="sl-card"><div class="sl-table-toolbar"><div><h3>Paid Transactions</h3><p>Completed transaction records ready for settlement reconciliation.</p></div></div><div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Transaction</th><th>Order</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th></tr></thead><tbody>@forelse($transactions->where('status','paid') as $transaction)<tr><td><strong>{{ $transaction->transaction_number }}</strong></td><td>#{{ $transaction->order?->order_number }}</td><td><strong>₱{{ number_format($transaction->amount,2) }}</strong></td><td>{{ strtoupper($transaction->method) }}</td><td>{{ $transaction->created_at?->format('M d, Y') }}</td><td><span class="sl-status is-success">Paid</span></td></tr>@empty<tr><td colspan="6">No paid transactions yet.</td></tr>@endforelse</tbody></table></div></section>
    @else
        <section class="sl-card"><div class="sl-table-toolbar"><div><h3>Transactions</h3><p>Search with the browser or export the statement for spreadsheet analysis.</p></div><a href="{{ route('seller.finance.statement') }}" class="sl-btn sl-btn-ghost sl-btn-sm">Export CSV</a></div><div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Transaction</th><th>Order</th><th>Amount</th><th>Commission</th><th>Net Amount</th><th>Date</th><th>Status</th></tr></thead><tbody>
            @forelse($transactions as $transaction) @php $commission=round((float)$transaction->amount*$commissionRate,2); @endphp <tr><td><strong>{{ $transaction->transaction_number }}</strong></td><td>#{{ $transaction->order?->order_number }}</td><td>₱{{ number_format($transaction->amount,2) }}</td><td>−₱{{ number_format($commission,2) }}</td><td><strong>₱{{ number_format((float)$transaction->amount-$commission,2) }}</strong></td><td>{{ $transaction->created_at?->format('M d, Y') }}</td><td><span class="sl-status {{ $transaction->status==='paid'?'is-success':'is-warning' }}">{{ str($transaction->status)->headline() }}</span></td></tr>
            @empty<tr><td colspan="7">No transactions yet.</td></tr>@endforelse
        </tbody></table></div></section>
    @endif
</div>
@endsection
