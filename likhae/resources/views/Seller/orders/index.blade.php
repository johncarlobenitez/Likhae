@extends('Seller.layouts.app')
@section('title', 'Orders — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/orders.css') @endpush

@section('content')
<x-seller.page-header eyebrow="ORDERS" title="Order Management" description="Review new orders, prepare parcels, and monitor fulfillment status.">
    <x-slot:actions><button class="btn-secondary">Export Orders</button></x-slot:actions>
</x-seller.page-header>

<div class="tabs-row">
    @foreach(['All','New Orders','To Prepare','Ready for Pickup','Shipped','Delivered','Cancelled','Returns'] as $i => $tab)
        <button class="tab-button {{ $i === 0 ? 'is-active' : '' }}">{{ $tab }} @if(in_array($i,[1,2,3,7]))<span>{{ [1=>8,2=>12,3=>5,7=>2][$i] }}</span>@endif</button>
    @endforeach
</div>

<div class="filter-bar mt-4">
    <label class="search-field"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input placeholder="Search order ID or buyer"></label>
    <input class="filter-input" type="date">
    <select class="filter-input"><option>Payment: All</option><option>GCash</option><option>COD</option><option>Maya</option></select>
    <select class="filter-input"><option>Courier: All</option><option>J&T Express</option><option>Flash Express</option></select>
    <select class="filter-input"><option>Status: All</option><option>To Prepare</option><option>Ready for Pickup</option></select>
</div>

<section class="panel mt-4">
    <div class="table-wrap desktop-orders">
        <table class="data-table">
            <thead><tr><th><input type="checkbox"></th><th>Order</th><th>Buyer</th><th>Items</th><th>Order Date</th><th>Amount</th><th>Payment</th><th>Shipping</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                @foreach([
                    ['LH-20260820-0231','Juan Dela Cruz','2 items','Aug 20, 8:31 PM','₱1,498','GCash','Standard','To Prepare'],
                    ['LH-20260820-0214','Anne Reyes','1 item','Aug 20, 7:16 PM','₱899','COD','Standard','New Order'],
                    ['LH-20260819-0188','Paolo Lim','3 items','Aug 19, 5:42 PM','₱2,347','Maya','Express','Ready for Pickup'],
                    ['LH-20260819-0146','Mika Cruz','1 item','Aug 19, 3:05 PM','₱549','GCash','Standard','In Transit'],
                ] as $row)
                <tr>
                    <td><input type="checkbox"></td>
                    @foreach(array_slice($row,0,7) as $cell)<td>{{ $cell }}</td>@endforeach
                    <td><x-seller.status-badge :status="$row[7]"/></td>
                    <td><a class="text-link" href="{{ url('/seller/orders/' . $row[0]) }}">View Order</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mobile-order-list">
        @foreach([
            ['LH-20260820-0231','Juan Dela Cruz','₱1,498','To Prepare'],
            ['LH-20260820-0214','Anne Reyes','₱899','New Order'],
            ['LH-20260819-0188','Paolo Lim','₱2,347','Ready for Pickup'],
        ] as $o)
        <a href="{{ url('/seller/orders/' . $o[0]) }}" class="mobile-order-card">
            <div><strong>{{ $o[0] }}</strong><span>{{ $o[1] }} · 2 items</span></div>
            <div class="text-right"><strong>{{ $o[2] }}</strong><x-seller.status-badge :status="$o[3]"/></div>
        </a>
        @endforeach
    </div>

    <div class="pagination-row"><span>Showing 1–20 of 214 orders</span><div><button>Previous</button><button class="is-active">1</button><button>2</button><button>3</button><button>Next</button></div></div>
</section>
@endsection
