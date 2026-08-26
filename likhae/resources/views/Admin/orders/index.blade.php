@extends('Admin.layouts.app')
@section('title', 'Order Management — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">OPERATIONS</p>
        <h1 class="page-title">Order Management</h1>
        <p class="page-description">Monitor all platform orders across all sellers and buyers.</p>
    </div>
</div>

<div class="admin-stat-grid">
    @foreach([['TOTAL ORDERS','8,341',''],['COMPLETED','6,120','text-[#079B72]'],['IN PROGRESS','1,890','text-[#3977B8]'],['DISPUTED','331','text-[#D92D2F]']] as [$label,$val,$tone])
        <div class="kpi-card">
            <p class="kpi-label">{{ $label }}</p>
            <strong class="mt-3 block text-2xl font-black tracking-[-.04em] {{ $tone }}">{{ $val }}</strong>
        </div>
    @endforeach
</div>

<div class="filter-bar mt-5">
    <div class="search-field flex-1">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input type="search" placeholder="Search by order ID, buyer, or seller...">
    </div>
    <select class="filter-input">
        <option>All Status</option>
        <option>Pending</option>
        <option>To Prepare</option>
        <option>Shipped</option>
        <option>Completed</option>
        <option>Disputed</option>
    </select>
    <select class="filter-input">
        <option>All Payment</option>
        <option>GCash</option>
        <option>Maya</option>
        <option>COD</option>
    </select>
    <input type="date" class="filter-input">
</div>

<div class="tabs-row mt-0 border-t-0">
    @foreach(['All (8,341)','Pending (214)','To Prepare (380)','Shipped (1,296)','Completed (6,120)','Disputed (331)'] as $i => $tab)
        <button class="tab-button {{ $i === 0 ? 'is-active' : '' }}">{{ $tab }}</button>
    @endforeach
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Order ID</th><th>Buyer</th><th>Seller</th><th>Items</th><th>Amount</th><th>Payment</th><th>Date</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach([
                    ['LH-20260820-0231','Juan Dela Cruz','Maria\'s Finds','2 items','₱1,498','GCash','Aug 20','Completed'],
                    ['LH-20260820-0214','Anne Reyes','Tablea Shop','1 item','₱899','COD','Aug 20','To Prepare'],
                    ['LH-20260819-0188','Paolo Lim','Rattan Co.','3 items','₱2,347','Maya','Aug 19','Shipped'],
                    ['LH-20260819-0175','Lea Santos','Capiz Crafts','1 item','₱560','GCash','Aug 19','Disputed'],
                    ['LH-20260818-0162','Rico Cruz','Bayong Atbp.','2 items','₱1,200','Maya','Aug 18','Completed'],
                ] as [$id,$buyer,$seller,$items,$amount,$payment,$date,$status])
                    <tr>
                        <td><strong>{{ $id }}</strong></td>
                        <td>{{ $buyer }}</td>
                        <td>{{ $seller }}</td>
                        <td>{{ $items }}</td>
                        <td>{{ $amount }}</td>
                        <td>{{ $payment }}</td>
                        <td>{{ $date }}</td>
                        <td>
                            @php $tone = match($status) { 'Completed' => 'status-success', 'Shipped' => 'status-info', 'To Prepare' => 'status-warning', 'Disputed' => 'status-danger', default => 'status-neutral' }; @endphp
                            <span class="status-badge {{ $tone }}"><span class="status-dot"></span>{{ $status }}</span>
                        </td>
                        <td><button class="btn-secondary text-[10px]">View</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-row">
        <span>Showing 1–5 of 8,341 orders</span>
        <div><button>‹</button><button class="is-active">1</button><button>2</button><button>3</button><button>›</button></div>
    </div>
</div>
@endsection
