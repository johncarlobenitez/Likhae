@extends('layouts.admin')

@section('title', 'Finance')
@section('subtitle', 'Monitor the fixed 10% platform commission, payments, and marketplace settlements.')
@section('active', 'finance')

@section('content')
@php
    $tab = request('tab', 'commission');
    $transactions = [
        ['id' => 'TXN-980142', 'order' => '#LK-10482', 'seller' => 'LIKHA Studio', 'gross' => 12990, 'commission' => 1299, 'net' => 11691, 'method' => 'GCash', 'date' => 'Sep 4, 2026', 'status' => 'Paid'],
        ['id' => 'TXN-980141', 'order' => '#LK-10481', 'seller' => 'MNL Tech', 'gross' => 5580, 'commission' => 558, 'net' => 5022, 'method' => 'COD', 'date' => 'Sep 4, 2026', 'status' => 'Processing'],
        ['id' => 'TXN-980140', 'order' => '#LK-10480', 'seller' => 'MNL Tech', 'gross' => 1490, 'commission' => 149, 'net' => 1341, 'method' => 'Maya', 'date' => 'Sep 4, 2026', 'status' => 'Paid'],
        ['id' => 'TXN-980139', 'order' => '#LK-10479', 'seller' => 'Casa Local', 'gross' => 3490, 'commission' => 349, 'net' => 3141, 'method' => 'Card', 'date' => 'Sep 3, 2026', 'status' => 'Paid'],
        ['id' => 'TXN-980138', 'order' => '#LK-10478', 'seller' => 'Paper & Loom', 'gross' => 1980, 'commission' => 198, 'net' => 1782, 'method' => 'GCash', 'date' => 'Sep 3, 2026', 'status' => 'Failed'],
    ];
@endphp

<div class="ad-page">
    <div class="ad-page-head"><div><span class="ad-overline">Financial control</span><h2>{{ match($tab) {'transactions' => 'Transaction ledger', 'payments' => 'Payment monitoring', default => 'Commission management'} }}</h2><p>Reconcile platform earnings and seller settlements using one consistent commission rule.</p></div><a href="{{ route('admin.reports') }}" class="ad-btn ad-btn-secondary">Financial reports</a></div>
    <section class="ad-stat-grid" style="grid-template-columns:repeat(4,minmax(0,1fr))">
        <x-admin.stat-card label="Gross sales" value="₱4.20M" trend="+6.4%" detail="this week" icon="money" />
        <x-admin.stat-card label="Commission earned" value="₱420,650" trend="10%" detail="fixed platform rate" icon="chart" />
        <x-admin.stat-card label="Seller net amount" value="₱3.78M" detail="before other adjustments" icon="money" tone="neutral" />
        <x-admin.stat-card label="Pending settlement" value="₱286,420" detail="48 transactions" icon="orders" tone="warning" />
    </section>
    <div class="ad-tabs"><a class="ad-tab {{ $tab === 'commission' ? 'is-active' : '' }}" href="{{ route('admin.finance', ['tab' => 'commission']) }}">Commission Management</a><a class="ad-tab {{ $tab === 'transactions' ? 'is-active' : '' }}" href="{{ route('admin.finance', ['tab' => 'transactions']) }}">Transactions</a><a class="ad-tab {{ $tab === 'payments' ? 'is-active' : '' }}" href="{{ route('admin.finance', ['tab' => 'payments']) }}">Payments</a></div>

    @if($tab === 'commission')
        <section class="ad-dashboard-split">
            <article class="ad-card"><header class="ad-card-head"><div><span class="ad-overline">Platform rule</span><h2>Marketplace commission</h2><p>Applied to completed eligible orders.</p></div><span class="ad-status is-active">Active</span></header><form class="ad-settings-section" data-demo-form data-success-message="Commission policy saved for this frontend preview"><div class="ad-note"><strong>Current policy:</strong> LIKHAE retains 10% of the eligible order amount. The seller receives the remaining 90% before refunds or approved adjustments.</div><label class="ad-field"><span>Platform commission</span><input value="10%" readonly><small>This project requirement is fixed at 10%. Connect policy changes to an approval workflow if made configurable later.</small></label><label class="ad-field"><span>Effective scope</span><select><option>All marketplace categories</option></select></label><label class="ad-field"><span>Settlement trigger</span><select><option>Buyer confirms receipt / order completes</option></select></label><div class="ad-inline-actions"><button class="ad-btn ad-btn-primary" type="submit">Save policy</button><button class="ad-btn ad-btn-secondary" type="button" data-demo-action="Opening commission history">View change history</button></div></form></article>
            <article class="ad-card"><header class="ad-card-head"><div><span class="ad-overline">Example calculation</span><h3>Order #LK-10482</h3><p>Transparent commission breakdown.</p></div></header><div class="ad-settings-section"><div class="ad-setting-row"><div><strong>Eligible order amount</strong><p>After buyer discounts</p></div><strong>₱12,990.00</strong></div><div class="ad-setting-row"><div><strong>LIKHAE commission</strong><p>10% × ₱12,990.00</p></div><strong>₱1,299.00</strong></div><div class="ad-setting-row"><div><strong>Seller net amount</strong><p>Before approved adjustments</p></div><strong style="color:#2563eb">₱11,691.00</strong></div></div></article>
        </section>
    @else
        <section class="ad-card" id="transaction-table"><div class="ad-filter-bar"><label class="ad-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" data-filter-input="#transaction-table" placeholder="Search transaction, order, or seller"></label><div class="ad-inline-actions"><select class="ad-select"><option>All statuses</option><option>Paid</option><option>Processing</option><option>Failed</option></select><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Finance table exported">Export</button></div></div><div class="ad-table-wrap"><table class="ad-table"><thead><tr><th>Transaction / order</th><th>Seller</th><th>Gross amount</th><th>Commission (10%)</th><th>Net amount</th><th>Method</th><th>Date</th><th>Status</th><th></th></tr></thead><tbody>@foreach($transactions as $transaction)<tr data-filter-item data-search="{{ strtolower(implode(' ', $transaction)) }}"><td><strong>{{ $transaction['id'] }}</strong><small>{{ $transaction['order'] }}</small></td><td>{{ $transaction['seller'] }}</td><td>₱{{ number_format($transaction['gross'], 2) }}</td><td><strong>₱{{ number_format($transaction['commission'], 2) }}</strong></td><td>₱{{ number_format($transaction['net'], 2) }}</td><td>{{ $transaction['method'] }}</td><td>{{ $transaction['date'] }}</td><td><span class="ad-status is-{{ strtolower($transaction['status']) }}">{{ $transaction['status'] }}</span></td><td><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Opening {{ $transaction['id'] }}">View</button></td></tr>@endforeach</tbody></table></div></section>
    @endif
</div>
@endsection
