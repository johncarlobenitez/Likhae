@extends('layouts.seller')

@php $currentTab = $tab ?? request('tab', 'sales'); @endphp

@section('title', 'Finance')
@section('active', 'finance')
@section('subtitle', 'Monitor revenue, platform fees, balances, and payouts.')

@section('content')
<div class="sl-page">
    <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Financial Management</span><h2>Finance Overview</h2><p>Transparent earnings and transaction monitoring for your store.</p></div><div class="sl-toolbar-group"><button type="button" class="sl-btn sl-btn-ghost" data-demo-action="Finance statement downloaded.">Download Statement</button><a href="{{ route('seller.reports') }}" class="sl-btn sl-btn-primary">Generate Report</a></div></div>
    <section class="sl-stat-grid sl-stat-grid-4">
        <x-seller.stat-card label="Gross Sales" value="₱184,650" change="6.4%" icon="sales" />
        <x-seller.stat-card label="Commission" value="₱12,926" change="7.0% rate" direction="down" icon="finance" />
        <x-seller.stat-card label="Net Earnings" value="₱171,724" change="5.8%" icon="revenue" />
        <x-seller.stat-card label="Pending Balance" value="₱38,450" change="Next payout Sep 8" icon="finance" />
    </section>
    <nav class="sl-tabs"><a href="{{ route('seller.finance', ['tab' => 'sales']) }}" class="{{ $currentTab === 'sales' ? 'is-active' : '' }}">Sales Overview</a><a href="{{ route('seller.finance', ['tab' => 'transactions']) }}" class="{{ $currentTab === 'transactions' ? 'is-active' : '' }}">Transactions</a><a href="{{ route('seller.finance', ['tab' => 'payouts']) }}" class="{{ $currentTab === 'payouts' ? 'is-active' : '' }}">Payouts</a></nav>
    @if ($currentTab === 'sales')
        <div class="sl-dashboard-grid">
            <section class="sl-card"><header class="sl-card-head"><div><span class="sl-eyebrow">Net Sales</span><h2>Earnings Trend</h2><p>Gross sales minus marketplace commission.</p></div><select class="sl-select sl-select-sm"><option>Last 30 days</option><option>This quarter</option></select></header><div class="sl-chart-summary"><div><span>Net earnings</span><strong>₱171,724</strong></div><span class="sl-positive">↑ 5.8% vs last period</span></div><div class="sl-line-chart"><svg viewBox="0 0 700 220" preserveAspectRatio="none" aria-label="Earnings trend chart"><defs><linearGradient id="financeFill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2563eb" stop-opacity=".22"/><stop offset="1" stop-color="#2563eb" stop-opacity="0"/></linearGradient></defs><path class="sl-grid-line" d="M0 35H700M0 90H700M0 145H700M0 200H700"/><path class="sl-area" d="M0 178 C70 162 105 172 150 135 S235 92 290 120 S390 72 450 82 S535 42 590 55 S650 30 700 38 L700 220 L0 220Z"/><path class="sl-line" d="M0 178 C70 162 105 172 150 135 S235 92 290 120 S390 72 450 82 S535 42 590 55 S650 30 700 38"/></svg><div><span>Aug 06</span><span>Aug 13</span><span>Aug 20</span><span>Aug 27</span><span>Sep 04</span></div></div></section>
            <section class="sl-card"><header class="sl-card-head"><div><span class="sl-eyebrow">Deductions</span><h2>Fee Breakdown</h2><p>Costs deducted from gross sales.</p></div></header><div class="sl-fee-list"><div><span><i class="is-blue"></i>Marketplace commission</span><strong>₱12,926</strong></div><div><span><i class="is-indigo"></i>Payment processing</span><strong>₱3,124</strong></div><div><span><i class="is-slate"></i>Seller-funded vouchers</span><strong>₱2,850</strong></div><div><span><i class="is-light"></i>Adjustments</span><strong>₱420</strong></div></div><div class="sl-fee-total"><span>Total deductions</span><strong>₱19,320</strong></div></section>
        </div>
    @elseif ($currentTab === 'payouts')
        <section class="sl-card sl-payout-hero"><div><span class="sl-eyebrow">Next Payout</span><h2>₱38,450.00</h2><p>Scheduled for September 8, 2026 to BDO •••• 4721</p></div><span class="sl-status is-info">Processing</span></section>
        <section class="sl-card"><div class="sl-table-toolbar"><div><h3>Payout History</h3><p>Completed and scheduled store settlements.</p></div></div><div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Payout ID</th><th>Period</th><th>Bank Account</th><th>Amount</th><th>Date</th><th>Status</th></tr></thead><tbody>@foreach ([['PAY-0918','Aug 25–31','BDO •••• 4721','₱42,180','Sep 01, 2026','Paid'],['PAY-0917','Aug 18–24','BDO •••• 4721','₱36,520','Aug 25, 2026','Paid'],['PAY-0916','Aug 11–17','BDO •••• 4721','₱31,875','Aug 18, 2026','Paid']] as $payout)<tr><td><strong>{{ $payout[0] }}</strong></td><td>{{ $payout[1] }}</td><td>{{ $payout[2] }}</td><td><strong>{{ $payout[3] }}</strong></td><td>{{ $payout[4] }}</td><td><span class="sl-status is-success">{{ $payout[5] }}</span></td></tr>@endforeach</tbody></table></div></section>
    @else
        <section class="sl-card"><div class="sl-table-toolbar"><div class="sl-search-input"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" placeholder="Search order or transaction"></div><div class="sl-toolbar-group"><input type="date" class="sl-input"><select class="sl-select"><option>All status</option><option>Completed</option><option>Pending</option><option>Refunded</option></select></div></div><div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Order ID</th><th>Amount</th><th>Commission</th><th>Net Amount</th><th>Date</th><th>Status</th></tr></thead><tbody>@foreach ([['#10005',1980,138.60,1841.40,'Sep 04, 2026','Completed'],['#10004',3490,244.30,3245.70,'Sep 03, 2026','Pending'],['#10003',1490,104.30,1385.70,'Sep 03, 2026','Pending'],['#10002',5580,390.60,5189.40,'Sep 02, 2026','Completed'],['#10001',12990,909.30,12080.70,'Sep 01, 2026','Completed']] as $transaction)<tr><td><strong>{{ $transaction[0] }}</strong></td><td>₱{{ number_format($transaction[1], 2) }}</td><td>−₱{{ number_format($transaction[2], 2) }}</td><td><strong>₱{{ number_format($transaction[3], 2) }}</strong></td><td>{{ $transaction[4] }}</td><td><span class="sl-status {{ $transaction[5] === 'Completed' ? 'is-success' : 'is-warning' }}">{{ $transaction[5] }}</span></td></tr>@endforeach</tbody></table></div></section>
    @endif
</div>
@endsection
