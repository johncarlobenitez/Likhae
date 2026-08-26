@extends('Admin.layouts.app')
@section('title', 'Payment Monitoring — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">OPERATIONS</p>
        <h1 class="page-title">Payment Monitoring</h1>
        <p class="page-description">Monitor all payment transactions across the platform.</p>
    </div>
</div>

<div class="admin-stat-grid">
    @foreach([['TOTAL COLLECTED','₱4.2M',''],['GCASH','₱2.1M','text-[#3977B8]'],['MAYA','₱1.4M','text-[#079B72]'],['COD','₱700K','text-[#D99A22]']] as [$label,$val,$tone])
        <div class="kpi-card">
            <p class="kpi-label">{{ $label }}</p>
            <strong class="mt-3 block text-2xl font-black tracking-[-.04em] {{ $tone }}">{{ $val }}</strong>
        </div>
    @endforeach
</div>

<div class="filter-bar mt-5">
    <div class="search-field flex-1">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input type="search" placeholder="Search by transaction ID or order ID...">
    </div>
    <select class="filter-input">
        <option>All Methods</option>
        <option>GCash</option>
        <option>Maya</option>
        <option>COD</option>
    </select>
    <select class="filter-input">
        <option>All Status</option>
        <option>Paid</option>
        <option>Pending</option>
        <option>Failed</option>
        <option>Refunded</option>
    </select>
    <input type="date" class="filter-input">
</div>

<div class="panel mt-5">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Transaction ID</th><th>Order ID</th><th>Buyer</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach([
                    ['TXN-2026-08201','LH-20260820-0231','Juan Dela Cruz','₱1,498','GCash','Aug 20 · 8:31 PM','Paid'],
                    ['TXN-2026-08202','LH-20260820-0214','Anne Reyes','₱899','COD','Aug 20 · 7:16 PM','Pending'],
                    ['TXN-2026-08203','LH-20260819-0188','Paolo Lim','₱2,347','Maya','Aug 19 · 5:42 PM','Paid'],
                    ['TXN-2026-08204','LH-20260819-0175','Lea Santos','₱560','GCash','Aug 19 · 3:10 PM','Refunded'],
                    ['TXN-2026-08205','LH-20260818-0162','Rico Cruz','₱1,200','Maya','Aug 18 · 6:00 PM','Failed'],
                ] as [$txn,$order,$buyer,$amount,$method,$date,$status])
                    <tr>
                        <td><strong>{{ $txn }}</strong></td>
                        <td>{{ $order }}</td>
                        <td>{{ $buyer }}</td>
                        <td><strong>{{ $amount }}</strong></td>
                        <td>{{ $method }}</td>
                        <td>{{ $date }}</td>
                        <td>
                            @php $tone = match($status) { 'Paid' => 'status-success', 'Pending' => 'status-warning', 'Refunded' => 'status-info', default => 'status-danger' }; @endphp
                            <span class="status-badge {{ $tone }}"><span class="status-dot"></span>{{ $status }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-row">
        <span>Showing 1–5 of 8,341 transactions</span>
        <div><button>‹</button><button class="is-active">1</button><button>2</button><button>›</button></div>
    </div>
</div>
@endsection
