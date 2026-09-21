@extends('layouts.admin')
@section('title','Finance')
@section('subtitle','Payment and ledger-backed marketplace totals.')
@section('active','finance')
@section('content')
<div style="display:grid;gap:16px">
<div class="ad-page-head"><div><span class="ad-overline">Finance</span><h2>Marketplace finance</h2><p>Figures come from canonical payments and seller-order commissions.</p></div><a class="ad-btn ad-btn-secondary" href="{{ route('admin.finance.export') }}">Export CSV</a></div>
<div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px"><div class="ad-card ad-card-body"><small>Eligible gross</small><h2>PHP {{ number_format($gross,2) }}</h2></div><div class="ad-card ad-card-body"><small>Commission</small><h2>PHP {{ number_format($commission,2) }}</h2></div><div class="ad-card ad-card-body"><small>Seller net</small><h2>PHP {{ number_format($net,2) }}</h2></div><div class="ad-card ad-card-body"><small>Pending settlement</small><h2>PHP {{ number_format($pendingSettlement,2) }}</h2><small>{{ number_format($pendingCount) }} payments</small></div></div>
<form class="ad-card ad-card-body ad-filter-bar" method="GET"><div class="ad-inline-actions"><input name="q" value="{{ request('q') }}" placeholder="Payment or order"><select name="status"><option value="">All statuses</option>@foreach(['pending','paid','failed','refunded','partially_refunded'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ str($s)->headline() }}</option>@endforeach</select><button class="ad-btn ad-btn-primary">Filter</button><a class="ad-btn ad-btn-secondary" href="{{ route('admin.finance') }}">Reset</a></div></form>
<div class="ad-card"><div class="ad-table-wrap"><table class="ad-table"><thead><tr><th>Payment</th><th>Order</th><th>Buyer</th><th>Seller</th><th>Gross</th><th>Commission</th><th>Net</th><th>Method</th><th>Status</th></tr></thead><tbody>@forelse($transactionRecords as $payment)@php
    $fee = $payment->order?->sellerOrders?->sum('commission_minor') / 100;
    $amount = $payment->amount_minor / 100;
@endphp<tr><td><strong>{{ $payment->provider_ref ?: 'PAY-'.$payment->id }}</strong></td><td>{{ $payment->order?->reference }}</td><td>{{ $payment->order?->buyer?->name }}</td><td>{{ $payment->order?->sellerOrders?->pluck('seller.name')->filter()->join(', ') }}</td><td>PHP {{ number_format($amount,2) }}</td><td>PHP {{ number_format($fee,2) }}</td><td>PHP {{ number_format($amount-$fee,2) }}</td><td>{{ strtoupper($payment->method) }}</td><td>{{ str($payment->status)->headline() }}</td></tr>@empty<tr><td colspan="9">No payments found.</td></tr>@endforelse</tbody></table></div><div class="ad-card-body">{{ $transactionRecords->links() }}</div></div>
</div>
@endsection
