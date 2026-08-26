@extends('Admin.layouts.app')
@section('title', 'Refund Management — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">OPERATIONS</p>
        <h1 class="page-title">Refund Management</h1>
        <p class="page-description">Review and process buyer refund requests.</p>
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-3 mb-5">
    @foreach([['OPEN REQUESTS','6','text-[#D99A22]'],['PROCESSED THIS MONTH','28','text-[#079B72]'],['TOTAL REFUNDED','₱42,800','text-[#3977B8]']] as [$label,$val,$tone])
        <div class="kpi-card">
            <p class="kpi-label">{{ $label }}</p>
            <strong class="mt-3 block text-2xl font-black tracking-[-.04em] {{ $tone }}">{{ $val }}</strong>
        </div>
    @endforeach
</div>

<div class="tabs-row">
    @foreach(['Open (6)','Processing (3)','Completed (28)','Rejected (4)','All (41)'] as $i => $tab)
        <button class="tab-button {{ $i === 0 ? 'is-active' : '' }}">{{ $tab }}</button>
    @endforeach
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Refund ID</th><th>Order ID</th><th>Buyer</th><th>Amount</th><th>Reason</th><th>Requested</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach([
                    ['REF-001','LH-20260819-0175','Lea Santos','₱560','Item not received','Aug 20, 2026','Open'],
                    ['REF-002','LH-20260818-0140','Mark Reyes','₱1,299','Wrong item delivered','Aug 19, 2026','Open'],
                    ['REF-003','LH-20260817-0128','Gina Cruz','₱850','Defective product','Aug 18, 2026','Processing'],
                    ['REF-004','LH-20260815-0110','Ben Santos','₱2,499','Changed mind','Aug 16, 2026','Rejected'],
                    ['REF-005','LH-20260814-0098','Ana Lim','₱480','Item not as described','Aug 15, 2026','Completed'],
                ] as [$refId,$orderId,$buyer,$amount,$reason,$date,$status])
                    <tr>
                        <td><strong>{{ $refId }}</strong></td>
                        <td>{{ $orderId }}</td>
                        <td>{{ $buyer }}</td>
                        <td><strong>{{ $amount }}</strong></td>
                        <td class="max-w-[160px] truncate">{{ $reason }}</td>
                        <td>{{ $date }}</td>
                        <td>
                            @php $tone = match($status) { 'Completed' => 'status-success', 'Processing' => 'status-info', 'Open' => 'status-warning', default => 'status-danger' }; @endphp
                            <span class="status-badge {{ $tone }}"><span class="status-dot"></span>{{ $status }}</span>
                        </td>
                        <td class="flex gap-2">
                            @if($status === 'Open')
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
        <span>Showing 1–5 of 41 refund requests</span>
        <div><button>‹</button><button class="is-active">1</button><button>›</button></div>
    </div>
</div>
@endsection
