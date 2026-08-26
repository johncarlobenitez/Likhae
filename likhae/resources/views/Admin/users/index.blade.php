@extends('Admin.layouts.app')
@section('title', 'User Management — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">USERS</p>
        <h1 class="page-title">User Management</h1>
        <p class="page-description">View and manage all registered buyers, sellers, and couriers.</p>
    </div>
</div>

{{-- Stats --}}
<div class="admin-stat-grid">
    @foreach([
        ['TOTAL USERS','12,480',''],
        ['BUYERS','9,842','text-[#3977B8]'],
        ['SELLERS','1,924','text-[#079B72]'],
        ['COURIERS','714','text-[#D99A22]'],
    ] as [$label, $val, $tone])
        <div class="kpi-card">
            <p class="kpi-label">{{ $label }}</p>
            <strong class="mt-3 block text-2xl font-black tracking-[-.04em] {{ $tone }}">{{ $val }}</strong>
        </div>
    @endforeach
</div>

{{-- Filter Bar --}}
<div class="filter-bar mt-5">
    <div class="search-field flex-1">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input type="search" placeholder="Search by name, email, or contact...">
    </div>
    <select class="filter-input">
        <option value="">All Roles</option>
        <option>Buyer</option>
        <option>Seller</option>
        <option>Courier</option>
    </select>
    <select class="filter-input">
        <option value="">All Status</option>
        <option>Active</option>
        <option>Pending</option>
        <option>Suspended</option>
    </select>
</div>

{{-- Tabs --}}
<div class="tabs-row mt-0 border-t-0">
    @foreach(['All (12,480)','Buyers (9,842)','Sellers (1,924)','Couriers (714)','Pending (24)','Suspended (12)'] as $i => $tab)
        <button class="tab-button {{ $i === 0 ? 'is-active' : '' }}">{{ $tab }}</button>
    @endforeach
</div>

{{-- Table --}}
<div class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Role</th><th>Contact</th><th>Registered</th><th>Status</th><th></th>
                </tr>
            </thead>
            <tbody>
                @foreach([
                    ['Juan Dela Cruz',  'juan@email.com',  'Buyer',   '09171234567', 'Aug 20, 2026', 'Active'],
                    ['Maria Santos',    'maria@email.com', 'Seller',  '09281234567', 'Aug 18, 2026', 'Active'],
                    ['Pedro Reyes',     'pedro@email.com', 'Courier', '09391234567', 'Aug 15, 2026', 'Pending'],
                    ['Ana Lim',         'ana@email.com',   'Buyer',   '09451234567', 'Aug 10, 2026', 'Active'],
                    ['Carlo Bautista',  'carlo@email.com', 'Seller',  '09561234567', 'Aug 5, 2026',  'Suspended'],
                ] as [$name, $email, $role, $contact, $date, $status])
                    <tr>
                        <td><strong>{{ $name }}</strong></td>
                        <td>{{ $email }}</td>
                        <td>
                            @php
                                $roleTone = match($role) { 'Seller' => 'status-success', 'Courier' => 'status-warning', default => 'status-info' };
                            @endphp
                            <span class="status-badge {{ $roleTone }}">{{ $role }}</span>
                        </td>
                        <td>{{ $contact }}</td>
                        <td>{{ $date }}</td>
                        <td>
                            @php
                                $tone = match($status) { 'Active' => 'status-success', 'Pending' => 'status-warning', default => 'status-danger' };
                            @endphp
                            <span class="status-badge {{ $tone }}"><span class="status-dot"></span>{{ $status }}</span>
                        </td>
                        <td class="flex gap-2">
                            <button class="btn-secondary text-[10px]">View</button>
                            @if($status === 'Active')
                                <button class="btn-danger text-[10px]">Suspend</button>
                            @elseif($status === 'Suspended')
                                <button class="btn-secondary text-[10px]">Restore</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-row">
        <span>Showing 1–5 of 12,480 users</span>
        <div>
            <button>‹</button>
            <button class="is-active">1</button>
            <button>2</button>
            <button>3</button>
            <button>›</button>
        </div>
    </div>
</div>
@endsection
