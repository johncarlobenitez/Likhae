@extends('layouts.admin')

@section('title', 'User Management')
@section('subtitle', 'Monitor platform accounts, issue violation notices, and apply proportionate enforcement.')
@section('active', 'users')

@section('content')
@php
    $role = request('role', 'all');
    $users = collect([
        ['id' => 'USR-10082', 'name' => 'Angela Cruz', 'email' => 'angela@example.com', 'role' => 'Buyer', 'joined' => 'Aug 28, 2026', 'orders' => 18, 'status' => 'Active'],
        ['id' => 'USR-10081', 'name' => 'LIKHA Studio', 'email' => 'hello@likha.studio', 'role' => 'Seller', 'joined' => 'Aug 26, 2026', 'orders' => 426, 'status' => 'Verified'],
        ['id' => 'USR-10080', 'name' => 'NorthLink Hub', 'email' => 'ops@northlink.ph', 'role' => 'Logistics', 'joined' => 'Aug 22, 2026', 'orders' => 1204, 'status' => 'Active'],
        ['id' => 'USR-10079', 'name' => 'Juan Dela Cruz', 'email' => 'juan.rider@example.com', 'role' => 'Rider', 'joined' => 'Aug 20, 2026', 'orders' => 312, 'status' => 'Active'],
        ['id' => 'USR-10078', 'name' => 'Marco Reyes', 'email' => 'marco@example.com', 'role' => 'Buyer', 'joined' => 'Aug 18, 2026', 'orders' => 6, 'status' => 'Under Review'],
        ['id' => 'USR-10077', 'name' => 'RapidCart PH', 'email' => 'support@rapidcart.ph', 'role' => 'Seller', 'joined' => 'Aug 10, 2026', 'orders' => 94, 'status' => 'Banned'],
    ]);
    $filtered = $users->filter(fn($user) => $role === 'all' || match($role) {
        'buyers' => $user['role'] === 'Buyer', 'sellers' => $user['role'] === 'Seller', 'logistics' => $user['role'] === 'Logistics', 'riders' => $user['role'] === 'Rider', default => true,
    });
@endphp

<div class="ad-page">
    <div class="ad-page-head">
        <div><span class="ad-overline">Platform accounts</span><h2>User directory</h2><p>Use warnings for correctable violations and bans only for substantiated platform risk.</p></div>
        <button class="ad-btn ad-btn-primary" type="button" data-demo-action="Opening notification composer">Notify selected users</button>
    </div>

    <section class="ad-summary-grid">
        <div class="ad-mini-stat"><span>Total users</span><strong>12,480</strong><small>+518 this month</small></div>
        <div class="ad-mini-stat"><span>Buyers</span><strong>9,842</strong><small>78.9% of accounts</small></div>
        <div class="ad-mini-stat"><span>Sellers</span><strong>1,924</strong><small>1,806 verified</small></div>
        <div class="ad-mini-stat"><span>Logistics & riders</span><strong>714</strong><small>22 partner centers</small></div>
    </section>

    <div class="ad-tabs" aria-label="User role">
        @foreach(['all' => 'All Users', 'buyers' => 'Buyers', 'sellers' => 'Sellers', 'logistics' => 'Logistics Centers', 'riders' => 'Riders / Couriers'] as $key => $label)
            <a class="ad-tab {{ $role === $key ? 'is-active' : '' }}" href="{{ route('admin.users', ['role' => $key]) }}">{{ $label }}</a>
        @endforeach
    </div>

    <section class="ad-card" id="user-table">
        <div class="ad-filter-bar">
            <label class="ad-filter-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" value="{{ request('q') }}" data-filter-input="#user-table" placeholder="Search name, email, or user ID"></label>
            <div class="ad-inline-actions"><select class="ad-select"><option>All statuses</option><option>Active</option><option>Under Review</option><option>Banned</option></select><button class="ad-btn ad-btn-secondary ad-btn-sm" type="button" data-demo-action="User list exported">Export</button></div>
        </div>
        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead><tr><th><input type="checkbox" data-select-all aria-label="Select all users"></th><th>User</th><th>Role</th><th>Joined</th><th>Orders / jobs</th><th>Status</th><th style="text-align:right">Actions</th></tr></thead>
                <tbody>
                    @foreach($filtered as $user)
                        <tr data-filter-item data-search="{{ strtolower(implode(' ', $user)) }}">
                            <td><input type="checkbox" aria-label="Select {{ $user['name'] }}"></td>
                            <td><div class="ad-cell-user"><span class="ad-avatar is-soft">{{ mb_strtoupper(mb_substr($user['name'], 0, 1)) }}</span><span><strong>{{ $user['name'] }}</strong><small>{{ $user['id'] }} · {{ $user['email'] }}</small></span></div></td>
                            <td>{{ $user['role'] }}</td><td>{{ $user['joined'] }}</td><td>{{ number_format($user['orders']) }}</td>
                            <td><span class="ad-status is-{{ strtolower(str_replace(' ', '-', $user['status'])) }}">{{ $user['status'] }}</span></td>
                            <td><div class="ad-row-actions"><button class="ad-btn ad-btn-secondary ad-btn-sm" type="button" data-demo-action="Opening {{ $user['name'] }} profile">View</button><button class="ad-btn ad-btn-warning-soft ad-btn-sm" type="button" data-demo-action="Violation notice composer opened">Notify</button>@if($user['status'] !== 'Banned')<button class="ad-btn ad-btn-danger-soft ad-btn-sm" type="button" data-confirm-action data-confirm-title="Ban this account?" data-confirm-message="Ban {{ $user['name'] }} from LIKHAE. This must be supported by documented evidence." data-require-reason="true" data-success-message="{{ $user['name'] }} was marked banned and logged">Ban</button>@endif</div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="ad-pagination"><span>Showing {{ $filtered->count() }} demo records</span><nav><a class="ad-page-btn" href="#">‹</a><a class="ad-page-btn is-active" href="#">1</a><a class="ad-page-btn" href="#">2</a><a class="ad-page-btn" href="#">›</a></nav></div>
    </section>
</div>
@endsection
