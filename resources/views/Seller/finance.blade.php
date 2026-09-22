@extends('layouts.seller')
@php
    $currentTab = $tab ?? request('tab', 'sales');
    $stats = $financeStats;
    $maxTrend = max(1, (float) $financeTrend->max('value'));
@endphp
@section('title', 'Finance')
@section('active', 'finance')
@section('subtitle', 'Monitor ledger earnings, balances, and payouts.')
@section('content')
<div class="sl-page">
    <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Financial Management</span><h2>Finance Overview</h2><p>Figures come from completed seller orders and the append-only ledger.</p></div><div class="sl-toolbar-group"><a href="{{ route('seller.finance.statement') }}" class="sl-btn sl-btn-ghost">Download Statement</a><a href="{{ route('seller.reports') }}" class="sl-btn sl-btn-primary">Generate Report</a></div></div>
    <section class="sl-stat-grid sl-stat-grid-4">
        <x-seller.stat-card label="Gross Sales" :value="'PHP '.number_format($stats['gross'],2)" change="Completed orders" icon="sales" />
        <x-seller.stat-card label="Commission" :value="'PHP '.number_format($stats['commission'],2)" change="Persisted fees" direction="down" icon="finance" />
        <x-seller.stat-card label="Net Earnings" :value="'PHP '.number_format($stats['net'],2)" change="After commission" icon="revenue" />
        <x-seller.stat-card label="Available Balance" :value="'PHP '.number_format($stats['pending'],2)" change="Ledger balance" icon="finance" />
    </section>
    <nav class="sl-tabs"><a href="{{ route('seller.finance',['tab'=>'sales']) }}" class="{{ $currentTab==='sales'?'is-active':'' }}">Sales Overview</a><a href="{{ route('seller.finance',['tab'=>'transactions']) }}" class="{{ $currentTab==='transactions'?'is-active':'' }}">Ledger</a><a href="{{ route('seller.finance',['tab'=>'payouts']) }}" class="{{ $currentTab==='payouts'?'is-active':'' }}">Payouts</a></nav>
    @if($currentTab === 'sales')
        <div class="sl-dashboard-grid">
            <section class="sl-card sl-sales-panel"><header class="sl-card-head"><div><span class="sl-eyebrow">Completed Sales</span><h2>Earnings Trend</h2><p>Last 30 days of completed seller-order value.</p></div></header><div class="sl-chart-summary"><div><span>Net earnings</span><strong>PHP {{ number_format($stats['net'],2) }}</strong></div><span class="sl-positive">Database-backed</span></div><div class="sl-bar-chart">@foreach($financeTrend->take(-10) as $point)<div class="sl-bar-column" title="{{ $point['date']->format('M d') }}: PHP {{ number_format($point['value'],2) }}"><span style="height:{{ max(6,(int) round(($point['value']/$maxTrend)*100)) }}%"></span><small>{{ $point['date']->format('d') }}</small></div>@endforeach</div></section>
            <section class="sl-card"><header class="sl-card-head"><div><span class="sl-eyebrow">Settlement</span><h2>Fee Breakdown</h2></div></header><div class="sl-fee-list"><div><span>Marketplace commission</span><strong>PHP {{ number_format($stats['commission'],2) }}</strong></div><div><span>Net seller earnings</span><strong>PHP {{ number_format($stats['net'],2) }}</strong></div></div><div class="sl-fee-total"><span>Gross sales</span><strong>PHP {{ number_format($stats['gross'],2) }}</strong></div></section>
        </div>
    @elseif($currentTab === 'payouts')
        <section class="sl-card sl-payout-hero"><div><span class="sl-eyebrow">Available Balance</span><h2>PHP {{ number_format($stats['pending'],2) }}</h2><p>A request reserves funds immediately.</p></div><form method="POST" action="{{ route('seller.finance.payouts.store') }}">@csrf<label class="sl-field"><span>Amount in centavos</span><input type="number" name="amount_minor" min="1" max="{{ max(0,(int)round($stats['pending']*100)) }}" required></label><button class="sl-btn sl-btn-primary">Request Payout</button></form></section>
        <section class="sl-card"><div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Request</th><th>Amount</th><th>Date</th><th>Status</th></tr></thead><tbody>@forelse($payouts as $payout)<tr><td>PAYOUT-{{ $payout->id }}</td><td>PHP {{ number_format($payout->amount_minor/100,2) }}</td><td>{{ $payout->created_at?->format('M d, Y') }}</td><td>{{ str($payout->status)->headline() }}</td></tr>@empty<tr><td colspan="4">No payout requests yet.</td></tr>@endforelse</tbody></table></div></section>
    @else
        <section class="sl-card"><div class="sl-table-toolbar"><div><h3>Ledger Entries</h3><p>Append-only credits and debits.</p></div><a href="{{ route('seller.finance.statement') }}" class="sl-btn sl-btn-ghost sl-btn-sm">Export CSV</a></div><div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Entry</th><th>Reference</th><th>Amount</th><th>Type</th><th>Date</th><th>Direction</th></tr></thead><tbody>
        @forelse($transactions as $entry)<tr><td>LEDGER-{{ $entry->id }}</td><td>{{ class_basename($entry->reference_type) }} #{{ $entry->reference_id }}</td><td>PHP {{ number_format($entry->amount_minor/100,2) }}</td><td>{{ str($entry->type)->headline() }}</td><td>{{ $entry->created_at?->format('M d, Y') }}</td><td><span class="sl-status {{ $entry->amount_minor>=0?'is-success':'is-warning' }}">{{ $entry->amount_minor>=0?'Credit':'Debit' }}</span></td></tr>@empty<tr><td colspan="6">No ledger entries yet.</td></tr>@endforelse
        </tbody></table></div></section>
    @endif
</div>
@endsection
