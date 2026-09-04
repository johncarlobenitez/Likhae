@extends('layouts.admin')

@section('title', 'Compliance')
@section('subtitle', 'Review seller obligations and product-violation reports with traceable decisions.')
@section('active', 'compliance')

@section('content')
@php
    $tab = request('tab', 'sellers');
    $sellers = [
        ['seller' => 'LIKHA Studio', 'category' => 'Fashion & Home', 'documents' => 'Verified', 'score' => '98 / 100', 'violations' => 0, 'last' => 'Sep 1, 2026', 'status' => 'Compliant'],
        ['seller' => 'Wellness Corner', 'category' => 'Beauty & Wellness', 'documents' => 'Verified', 'score' => '74 / 100', 'violations' => 2, 'last' => 'Sep 4, 2026', 'status' => 'Under Review'],
        ['seller' => 'RapidCart PH', 'category' => 'General Merchandise', 'documents' => 'Expired permit', 'score' => '42 / 100', 'violations' => 5, 'last' => 'Sep 4, 2026', 'status' => 'Suspended'],
        ['seller' => 'Paper & Loom', 'category' => 'Books & Stationery', 'documents' => 'Verified', 'score' => '92 / 100', 'violations' => 0, 'last' => 'Aug 29, 2026', 'status' => 'Compliant'],
    ];
    $reports = [
        ['id' => 'VIO-2281', 'product' => 'Herbal Relief Capsules', 'seller' => 'Wellness Corner', 'reported' => 'Buyer · Camille S.', 'reason' => 'Unverified medical claim', 'evidence' => '3 files', 'status' => 'Under Review'],
        ['id' => 'VIO-2280', 'product' => 'Replica Collector Pistol', 'seller' => 'Hobby House', 'reported' => 'Seller · North & Pine', 'reason' => 'Possible prohibited weapon', 'evidence' => '2 files', 'status' => 'Open'],
        ['id' => 'VIO-2279', 'product' => 'Industrial Solvent 1L', 'seller' => 'BuildRight', 'reported' => 'Logistics · NorthLink', 'reason' => 'Undeclared hazardous item', 'evidence' => '4 files', 'status' => 'Open'],
        ['id' => 'VIO-2278', 'product' => 'Counterfeit Logo Wallet', 'seller' => 'Urban Value', 'reported' => 'Rider · Juan D.', 'reason' => 'Suspected counterfeit', 'evidence' => '1 file', 'status' => 'Resolved'],
    ];
@endphp

<div class="ad-page">
    <div class="ad-page-head"><div><span class="ad-overline">Trust & safety</span><h2>{{ $tab === 'violations' ? 'Product violation reports' : 'Seller compliance' }}</h2><p>Apply published policy consistently and keep evidence attached to every enforcement action.</p></div><a class="ad-btn ad-btn-secondary" href="{{ route('admin.settings', ['tab' => 'policies']) }}">View policies</a></div>
    <section class="ad-summary-grid"><div class="ad-mini-stat"><span>Verified sellers</span><strong>1,806</strong><small>93.9% of sellers</small></div><div class="ad-mini-stat"><span>Under review</span><strong>28</strong><small>12 due today</small></div><div class="ad-mini-stat"><span>Open violations</span><strong>17</strong><small>5 high priority</small></div><div class="ad-mini-stat"><span>Suspended sellers</span><strong>9</strong><small>Pending remediation</small></div></section>
    <div class="ad-tabs"><a class="ad-tab {{ $tab === 'sellers' ? 'is-active' : '' }}" href="{{ route('admin.compliance', ['tab' => 'sellers']) }}">Seller Compliance</a><a class="ad-tab {{ $tab === 'violations' ? 'is-active' : '' }}" href="{{ route('admin.compliance', ['tab' => 'violations']) }}">Product Violations</a></div>

    <section class="ad-card" id="compliance-table">
        <div class="ad-filter-bar"><label class="ad-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" data-filter-input="#compliance-table" placeholder="Search seller, product, or case ID"></label><select class="ad-select"><option>All statuses</option><option>Open</option><option>Under Review</option><option>Resolved</option></select></div>
        <div class="ad-table-wrap"><table class="ad-table">
            @if($tab === 'violations')
                <thead><tr><th>Report / product</th><th>Seller</th><th>Reported by</th><th>Reason</th><th>Evidence</th><th>Status</th><th style="text-align:right">Actions</th></tr></thead><tbody>@foreach($reports as $report)<tr data-filter-item data-search="{{ strtolower(implode(' ', $report)) }}"><td><strong>{{ $report['product'] }}</strong><small>{{ $report['id'] }}</small></td><td>{{ $report['seller'] }}</td><td>{{ $report['reported'] }}</td><td>{{ $report['reason'] }}</td><td>{{ $report['evidence'] }}</td><td><span class="ad-status is-{{ strtolower(str_replace(' ', '-', $report['status'])) }}">{{ $report['status'] }}</span></td><td><div class="ad-row-actions"><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Opening evidence for {{ $report['id'] }}">Review</button>@if($report['status'] !== 'Resolved')<button class="ad-btn ad-btn-danger-soft ad-btn-sm" data-confirm-action data-confirm-title="Remove this listing?" data-confirm-message="Remove {{ $report['product'] }} after reviewing the submitted evidence." data-require-reason="true" data-success-message="Listing removal recorded">Remove</button>@endif</div></td></tr>@endforeach</tbody>
            @else
                <thead><tr><th>Seller</th><th>Category</th><th>Documents</th><th>Compliance score</th><th>Violations</th><th>Last review</th><th>Status</th><th></th></tr></thead><tbody>@foreach($sellers as $seller)<tr data-filter-item data-search="{{ strtolower(implode(' ', $seller)) }}"><td><strong>{{ $seller['seller'] }}</strong></td><td>{{ $seller['category'] }}</td><td>{{ $seller['documents'] }}</td><td><strong>{{ $seller['score'] }}</strong></td><td>{{ $seller['violations'] }}</td><td>{{ $seller['last'] }}</td><td><span class="ad-status is-{{ strtolower(str_replace(' ', '-', $seller['status'])) }}">{{ $seller['status'] }}</span></td><td><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Opening compliance file for {{ $seller['seller'] }}">Review</button></td></tr>@endforeach</tbody>
            @endif
        </table></div>
    </section>
</div>
@endsection
