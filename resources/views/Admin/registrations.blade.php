@extends('layouts.admin')

@section('title', 'Registration Management')
@section('subtitle', 'Review buyer, seller, and logistics-center applications with consistent checks.')
@section('active', 'registrations')

@section('content')
@php
    $type = request('type', 'buyers');
    $applications = collect([
        ['id' => 'BUY-2026-0918', 'name' => 'Camille Santos', 'type' => 'Buyer', 'submitted' => 'Today, 9:42 AM', 'documents' => '2 of 2', 'risk' => 'Clear', 'status' => 'Pending', 'detail' => 'Government ID and address confirmation submitted'],
        ['id' => 'BUY-2026-0917', 'name' => 'Noel Garcia', 'type' => 'Buyer', 'submitted' => 'Today, 8:15 AM', 'documents' => '2 of 2', 'risk' => 'Review', 'status' => 'Pending', 'detail' => 'Identity match needs a manual confirmation'],
        ['id' => 'SEL-2026-0442', 'name' => 'Harana Home Goods', 'type' => 'Seller', 'submitted' => 'Yesterday, 4:20 PM', 'documents' => '5 of 5', 'risk' => 'Clear', 'status' => 'Pending', 'detail' => 'DTI, tax, owner ID, address, and payout details submitted'],
        ['id' => 'SEL-2026-0441', 'name' => 'Pixel Foundry PH', 'type' => 'Seller', 'submitted' => 'Yesterday, 1:05 PM', 'documents' => '4 of 5', 'risk' => 'Incomplete', 'status' => 'Pending', 'detail' => 'Business address document is still missing'],
        ['id' => 'LOG-2026-0108', 'name' => 'NorthLink Logistics', 'type' => 'Logistics', 'submitted' => 'Sep 3, 2:30 PM', 'documents' => '6 of 6', 'risk' => 'Clear', 'status' => 'Pending', 'detail' => 'Hub permit, fleet, coverage, and manager IDs submitted'],
        ['id' => 'LOG-2026-0107', 'name' => 'MetroSwift Hub', 'type' => 'Logistics', 'submitted' => 'Sep 2, 10:10 AM', 'documents' => '6 of 6', 'risk' => 'Clear', 'status' => 'Approved', 'detail' => 'Approved for Metro Manila service coverage'],
    ]);
    $filtered = $applications->filter(fn($application) => match($type) {
        'sellers' => $application['type'] === 'Seller',
        'logistics' => $application['type'] === 'Logistics',
        default => $application['type'] === 'Buyer',
    });
@endphp

<div class="ad-page">
    <div class="ad-page-head">
        <div><span class="ad-overline">Onboarding governance</span><h2>Application review queue</h2><p>Verify identity, business records, and risk checks before granting platform access.</p></div>
        <button class="ad-btn ad-btn-secondary" type="button" data-demo-action="Application queue refreshed">Refresh queue</button>
    </div>

    <section class="ad-summary-grid">
        <div class="ad-mini-stat"><span>Pending review</span><strong>24</strong><small>7 older than 24 hours</small></div>
        <div class="ad-mini-stat"><span>Approved this week</span><strong>86</strong><small>Median review: 3.2 hours</small></div>
        <div class="ad-mini-stat"><span>Needs documents</span><strong>9</strong><small>Awaiting applicant response</small></div>
        <div class="ad-mini-stat"><span>Rejected this week</span><strong>5</strong><small>Reasons stored in audit logs</small></div>
    </section>

    <div class="ad-tabs" role="navigation" aria-label="Application type">
        <a class="ad-tab {{ $type === 'buyers' ? 'is-active' : '' }}" href="{{ route('admin.registrations', ['type' => 'buyers']) }}">Buyer Applications <b>8</b></a>
        <a class="ad-tab {{ $type === 'sellers' ? 'is-active' : '' }}" href="{{ route('admin.registrations', ['type' => 'sellers']) }}">Seller Applications <b>11</b></a>
        <a class="ad-tab {{ $type === 'logistics' ? 'is-active' : '' }}" href="{{ route('admin.registrations', ['type' => 'logistics']) }}">Logistics Applications <b>5</b></a>
    </div>

    <section class="ad-card" id="applications-list">
        <div class="ad-filter-bar">
            <label class="ad-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" data-filter-input="#applications-list" placeholder="Search applicant or application ID"></label>
            <select class="ad-select" aria-label="Application status"><option>All statuses</option><option>Pending</option><option>Approved</option><option>Rejected</option></select>
        </div>
        <div class="ad-card-body ad-card-list">
            @forelse($filtered as $application)
                <x-admin.application-card :application="$application" />
            @empty
                <x-admin.empty-state title="No applications" message="There are no applications in this queue." />
            @endforelse
        </div>
    </section>

    <div class="ad-note"><strong>Role boundary:</strong> Admin approves logistics centers. Rider and courier onboarding is operationally approved by the assigned logistics center; Admin can still monitor accounts and apply platform-level enforcement.</div>
</div>
@endsection
