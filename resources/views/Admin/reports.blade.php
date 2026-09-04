@extends('layouts.admin')

@section('title', 'Financial Reports')
@section('subtitle', 'Generate date-bounded reports for sales, profit, orders, users, and marketplace risk.')
@section('active', 'reports')

@section('content')
@php
    $reports = [
        ['title' => 'Sales Report', 'description' => 'Gross sales, discounts, refunds, and completed order value.', 'format' => 'CSV / PDF'],
        ['title' => 'Commission Report', 'description' => 'The fixed 10% commission earned per eligible order.', 'format' => 'CSV / PDF'],
        ['title' => 'Order Report', 'description' => 'Volume, status, cancellations, and fulfillment performance.', 'format' => 'CSV / XLSX'],
        ['title' => 'Seller Performance', 'description' => 'Sales, service quality, violations, and settlement overview.', 'format' => 'CSV / PDF'],
        ['title' => 'User Growth', 'description' => 'Buyer, seller, logistics-center, and rider account trends.', 'format' => 'CSV / XLSX'],
        ['title' => 'Compliance Report', 'description' => 'Product flags, complaints, enforcement, and outcomes.', 'format' => 'PDF'],
        ['title' => 'Delivery Performance', 'description' => 'Transit time, delivery exceptions, and completion rate.', 'format' => 'CSV / PDF'],
        ['title' => 'Refund Report', 'description' => 'Return reasons, refund amounts, parties, and resolution time.', 'format' => 'CSV / PDF'],
    ];
@endphp

<div class="ad-page">
    <div class="ad-page-head"><div><span class="ad-overline">Analytics export</span><h2>Report center</h2><p>Choose a date range, generate a preview, then export in the required format.</p></div><a class="ad-btn ad-btn-secondary" href="{{ route('admin.finance') }}">Back to Finance</a></div>
    <section class="ad-card"><form class="ad-filter-bar" data-demo-form data-success-message="Report preview generated"><div class="ad-inline-actions" style="flex:1"><label class="ad-field"><span>From date</span><input type="date" value="2026-08-01"></label><label class="ad-field"><span>To date</span><input type="date" value="2026-09-04"></label><label class="ad-field"><span>Scope</span><select><option>All marketplace activity</option><option>Selected sellers</option><option>Selected categories</option></select></label></div><button class="ad-btn ad-btn-primary" type="submit">Generate preview</button></form></section>
    <section class="ad-report-grid">
        @foreach($reports as $report)
            <article class="ad-report-card"><svg viewBox="0 0 24 24"><path d="M5 3h10l4 4v14H5zM15 3v5h5M8 13h8M8 17h6"/></svg><h3>{{ $report['title'] }}</h3><p>{{ $report['description'] }}</p><div class="ad-inline-title"><span class="ad-status">{{ $report['format'] }}</span><button type="button" class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="{{ $report['title'] }} queued for export">Generate</button></div></article>
        @endforeach
    </section>
    <section class="ad-card"><header class="ad-card-head"><div><span class="ad-overline">Recent exports</span><h2>Generated reports</h2><p>Frontend sample of downloadable report history.</p></div></header><div class="ad-table-wrap"><table class="ad-table"><thead><tr><th>Report</th><th>Period</th><th>Requested by</th><th>Generated</th><th>Format</th><th>Status</th><th></th></tr></thead><tbody><tr><td><strong>August Commission Report</strong></td><td>Aug 1–31, 2026</td><td>Admin User</td><td>Sep 1, 9:20 AM</td><td>PDF</td><td><span class="ad-status is-completed">Ready</span></td><td><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Download started">Download</button></td></tr><tr><td><strong>Weekly Delivery Performance</strong></td><td>Aug 25–31, 2026</td><td>Admin User</td><td>Sep 1, 8:15 AM</td><td>CSV</td><td><span class="ad-status is-completed">Ready</span></td><td><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Download started">Download</button></td></tr></tbody></table></div></section>
</div>
@endsection
