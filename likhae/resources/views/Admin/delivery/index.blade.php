@extends('Admin.layouts.app')
@section('title', 'Delivery Management — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">OPERATIONS</p>
        <h1 class="page-title">Delivery Management</h1>
        <p class="page-description">Track all active deliveries and rider assignments across the platform.</p>
    </div>
</div>

<div class="admin-stat-grid">
    @foreach([['ACTIVE DELIVERIES','1,296',''],['OUT FOR DELIVERY','842','text-[#3977B8]'],['DELIVERED TODAY','454','text-[#079B72]'],['FAILED ATTEMPTS','38','text-[#D92D2F]']] as [$label,$val,$tone])
        <div class="kpi-card">
            <p class="kpi-label">{{ $label }}</p>
            <strong class="mt-3 block text-2xl font-black tracking-[-.04em] {{ $tone }}">{{ $val }}</strong>
        </div>
    @endforeach
</div>

<div class="filter-bar mt-5">
    <div class="search-field flex-1">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input type="search" placeholder="Search by order ID, rider, or address...">
    </div>
    <select class="filter-input">
        <option>All Status</option>
        <option>Assigned</option>
        <option>Out for Delivery</option>
        <option>Delivered</option>
        <option>Failed</option>
    </select>
</div>

<div class="panel mt-5">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Order ID</th><th>Buyer</th><th>Address</th><th>Rider</th><th>Vehicle</th><th>Assigned</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach([
                    ['LH-20260820-0231','Juan Dela Cruz','Brgy. Poblacion, Cebu City','Pedro Reyes','Motorcycle','Aug 20 · 9:00 AM','Out for Delivery'],
                    ['LH-20260820-0214','Anne Reyes','Brgy. Lahug, Cebu City','Tony Cruz','Car','Aug 20 · 10:30 AM','Assigned'],
                    ['LH-20260819-0188','Paolo Lim','Brgy. Mabolo, Cebu City','Pedro Reyes','Motorcycle','Aug 19 · 2:00 PM','Delivered'],
                    ['LH-20260819-0175','Lea Santos','Brgy. Banilad, Cebu City','Ricky Lim','Van','Aug 19 · 3:30 PM','Failed'],
                ] as [$id,$buyer,$address,$rider,$vehicle,$assigned,$status])
                    <tr>
                        <td><strong>{{ $id }}</strong></td>
                        <td>{{ $buyer }}</td>
                        <td class="max-w-[180px] truncate">{{ $address }}</td>
                        <td>{{ $rider }}</td>
                        <td>{{ $vehicle }}</td>
                        <td>{{ $assigned }}</td>
                        <td>
                            @php $tone = match($status) { 'Delivered' => 'status-success', 'Out for Delivery' => 'status-info', 'Assigned' => 'status-warning', default => 'status-danger' }; @endphp
                            <span class="status-badge {{ $tone }}"><span class="status-dot"></span>{{ $status }}</span>
                        </td>
                        <td><button class="btn-secondary text-[10px]">Track</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-row">
        <span>Showing 1–4 of 1,296 deliveries</span>
        <div><button>‹</button><button class="is-active">1</button><button>2</button><button>›</button></div>
    </div>
</div>
@endsection
