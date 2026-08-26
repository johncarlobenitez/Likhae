@extends('Admin.layouts.app')
@section('title', 'Rider Management — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">RIDERS</p>
        <h1 class="page-title">Rider Management</h1>
        <p class="page-description">Manage courier/rider accounts and approve new applications.</p>
    </div>
</div>

<div class="admin-stat-grid">
    @foreach([['TOTAL RIDERS','714',''],['ACTIVE','580','text-[#079B72]'],['PENDING APPROVAL','10','text-[#D99A22]'],['SUSPENDED','124','text-[#D92D2F]']] as [$label,$val,$tone])
        <div class="kpi-card">
            <p class="kpi-label">{{ $label }}</p>
            <strong class="mt-3 block text-2xl font-black tracking-[-.04em] {{ $tone }}">{{ $val }}</strong>
        </div>
    @endforeach
</div>

<div class="filter-bar mt-5">
    <div class="search-field flex-1">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input type="search" placeholder="Search by name, email, or plate number...">
    </div>
    <select class="filter-input">
        <option>All Status</option>
        <option>Active</option>
        <option>Pending</option>
        <option>Suspended</option>
    </select>
    <select class="filter-input">
        <option>All Vehicles</option>
        <option>Motorcycle</option>
        <option>Car</option>
        <option>Van</option>
    </select>
</div>

<div class="tabs-row mt-0 border-t-0">
    @foreach(['All (714)','Pending (10)','Active (580)','Suspended (124)'] as $i => $tab)
        <button class="tab-button {{ $i === 0 ? 'is-active' : '' }}">{{ $tab }}</button>
    @endforeach
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Vehicle</th><th>Plate No.</th><th>Deliveries</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach([
                    ['Pedro Reyes',   'pedro@email.com', 'Motorcycle', 'ABC-1234', 142, 'Active'],
                    ['Tony Cruz',     'tony@email.com',  'Car',        'XYZ-5678', 98,  'Active'],
                    ['Ben Santos',    'ben@email.com',   'Motorcycle', 'DEF-9012', 0,   'Pending'],
                    ['Ricky Lim',     'ricky@email.com', 'Van',        'GHI-3456', 210, 'Active'],
                    ['Mark Bautista', 'mark@email.com',  'Motorcycle', 'JKL-7890', 55,  'Suspended'],
                ] as [$name, $email, $vehicle, $plate, $deliveries, $status])
                    <tr>
                        <td><strong>{{ $name }}</strong></td>
                        <td>{{ $email }}</td>
                        <td>{{ $vehicle }}</td>
                        <td>{{ $plate }}</td>
                        <td>{{ $deliveries }}</td>
                        <td>
                            @php $tone = match($status) { 'Active' => 'status-success', 'Pending' => 'status-warning', default => 'status-danger' }; @endphp
                            <span class="status-badge {{ $tone }}"><span class="status-dot"></span>{{ $status }}</span>
                        </td>
                        <td class="flex gap-2">
                            @if($status === 'Pending')
                                <button class="btn-primary text-[10px]">Approve</button>
                                <button class="btn-danger text-[10px]">Reject</button>
                            @else
                                <button class="btn-secondary text-[10px]">View</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-row">
        <span>Showing 1–5 of 714 riders</span>
        <div>
            <button>‹</button><button class="is-active">1</button><button>2</button><button>›</button>
        </div>
    </div>
</div>
@endsection
