@extends('layouts.admin')

@section('title', 'Complaints & Disputes')
@section('subtitle', 'Coordinate evidence, case decisions, returns, and refunds across marketplace roles.')
@section('active', 'complaints')

@section('content')
@php
    $tab = request('tab', 'complaints');
    $complaints = [
        ['id' => 'DSP-1882', 'title' => 'Item materially different from listing', 'summary' => 'Buyer uploaded unboxing photos; seller response is due in two hours.', 'reported_by' => 'Buyer · Angela Cruz', 'against' => 'Seller · Urban Value', 'status' => 'Open', 'priority' => 'High', 'updated' => '12 min ago'],
        ['id' => 'DSP-1881', 'title' => 'Parcel damaged during transport', 'summary' => 'Packaging and hub scan photos are available from both parties.', 'reported_by' => 'Buyer · Noel Garcia', 'against' => 'Logistics · NorthLink', 'status' => 'Under Review', 'priority' => 'Medium', 'updated' => '35 min ago'],
        ['id' => 'DSP-1880', 'title' => 'Courier conduct complaint', 'summary' => 'Customer service escalated a rider interaction for logistics response.', 'reported_by' => 'Buyer · Lara Ong', 'against' => 'Rider · Carlo Diaz', 'status' => 'Open', 'priority' => 'Critical', 'updated' => '1 hr ago'],
        ['id' => 'DSP-1879', 'title' => 'Charge dispute resolved', 'summary' => 'Payment trail confirmed delivery and buyer accepted the resolution.', 'reported_by' => 'Seller · Casa Local', 'against' => 'Buyer · Marco Reyes', 'status' => 'Resolved', 'priority' => 'Low', 'updated' => 'Yesterday'],
    ];
    $returns = [
        ['id' => 'RRF-6201', 'order' => '#LK-10398', 'buyer' => 'Angela Cruz', 'seller' => 'Urban Value', 'amount' => 1590, 'reason' => 'Not as described', 'stage' => 'Evidence Review', 'status' => 'Pending'],
        ['id' => 'RRF-6200', 'order' => '#LK-10394', 'buyer' => 'Noel Garcia', 'seller' => 'Casa Local', 'amount' => 890, 'reason' => 'Damaged in transit', 'stage' => 'Seller Response', 'status' => 'Under Review'],
        ['id' => 'RRF-6199', 'order' => '#LK-10382', 'buyer' => 'Sarah Lim', 'seller' => 'MNL Tech', 'amount' => 2790, 'reason' => 'Wrong variation', 'stage' => 'Refund Processing', 'status' => 'Approved'],
        ['id' => 'RRF-6198', 'order' => '#LK-10375', 'buyer' => 'Daniel Tan', 'seller' => 'North & Pine', 'amount' => 2190, 'reason' => 'Changed mind', 'stage' => 'Closed', 'status' => 'Rejected'],
    ];
@endphp

<div class="ad-page">
    <div class="ad-page-head"><div><span class="ad-overline">Case management</span><h2>{{ $tab === 'returns' ? 'Returns and refunds' : 'Complaint resolution' }}</h2><p>Keep communication, evidence, and decisions together in one auditable case.</p></div><button class="ad-btn ad-btn-primary" type="button" data-demo-action="New case form opened">Create case</button></div>
    <section class="ad-summary-grid"><div class="ad-mini-stat"><span>Open cases</span><strong>11</strong><small>3 urgent</small></div><div class="ad-mini-stat"><span>Median resolution</span><strong>18.4h</strong><small>Within 24-hour target</small></div><div class="ad-mini-stat"><span>Pending returns</span><strong>14</strong><small>₱38,420 at issue</small></div><div class="ad-mini-stat"><span>Resolved this week</span><strong>42</strong><small>91% accepted outcomes</small></div></section>
    <div class="ad-tabs"><a class="ad-tab {{ $tab === 'complaints' ? 'is-active' : '' }}" href="{{ route('admin.complaints', ['tab' => 'complaints']) }}">Complaints / Disputes</a><a class="ad-tab {{ $tab === 'returns' ? 'is-active' : '' }}" href="{{ route('admin.complaints', ['tab' => 'returns']) }}">Returns / Refunds</a></div>

    @if($tab === 'returns')
        <section class="ad-card" id="return-table"><div class="ad-filter-bar"><label class="ad-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" data-filter-input="#return-table" placeholder="Search return, order, buyer, or seller"></label><select class="ad-select"><option>All stages</option><option>Evidence Review</option><option>Seller Response</option><option>Refund Processing</option></select></div><div class="ad-table-wrap"><table class="ad-table"><thead><tr><th>Case / order</th><th>Buyer</th><th>Seller</th><th>Amount</th><th>Reason</th><th>Stage</th><th>Status</th><th></th></tr></thead><tbody>@foreach($returns as $return)<tr data-filter-item data-search="{{ strtolower(implode(' ', $return)) }}"><td><strong>{{ $return['id'] }}</strong><small>{{ $return['order'] }}</small></td><td>{{ $return['buyer'] }}</td><td>{{ $return['seller'] }}</td><td><strong>₱{{ number_format($return['amount'], 2) }}</strong></td><td>{{ $return['reason'] }}</td><td>{{ $return['stage'] }}</td><td><span class="ad-status is-{{ strtolower(str_replace(' ', '-', $return['status'])) }}">{{ $return['status'] }}</span></td><td><button class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Opening {{ $return['id'] }}">Review</button></td></tr>@endforeach</tbody></table></div></section>
    @else
        <section id="complaint-list"><div class="ad-filter-bar ad-card" style="margin-bottom:12px;border-radius:12px"><label class="ad-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" data-filter-input="#complaint-list" placeholder="Search cases, parties, or issue"></label><select class="ad-select"><option>Priority: all</option><option>Critical</option><option>High</option><option>Medium</option></select></div><div class="ad-complaint-grid">@foreach($complaints as $complaint)<x-admin.complaint-card :complaint="$complaint" />@endforeach</div></section>
    @endif
</div>
@endsection
