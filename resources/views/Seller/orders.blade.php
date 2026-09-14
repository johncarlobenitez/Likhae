@extends('layouts.seller')

@php
    $isLogistics = ($pageMode ?? 'orders') === 'logistics';
    $currentMode = $mode ?? ($isLogistics ? request('view', 'couriers') : request('mode', 'index'));
    $currentStatus = $status ?? request('status', 'all');
    $orderId = $selectedOrder ?? request('order');
    $order = $sellerOrders->firstWhere('id', (string) $orderId) ?? $sellerOrders->first();
    $delivery = data_get($order, 'delivery');
@endphp

@section('title', $isLogistics ? 'Logistics' : ($currentMode === 'show' && $order ? 'Order #'.$order['id'] : 'Orders'))
@section('active', $isLogistics ? 'logistics' : 'orders')
@section('subtitle', $isLogistics ? 'Assign couriers, submit pickup requests, and monitor shipments.' : 'Review purchases and move each order through fulfillment.')

@section('content')
<div class="sl-page">
    @if ($isLogistics && $currentMode === 'couriers')
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Logistics</span><h2>Request Pickup</h2><p>Prepared orders can now create a real delivery/pickup record.</p></div><a href="{{ route('seller.orders',['status'=>'ready-pickup']) }}" class="sl-btn sl-btn-ghost">Ready Pickup Orders</a></div>
        @if($order)
            <div class="sl-logistics-layout">
                <section class="sl-card sl-logistics-order"><header class="sl-card-head"><div><span class="sl-eyebrow">Prepared Package</span><h2>Order #{{ $order['id'] }}</h2><p>Confirm the parcel information before requesting logistics.</p></div><span class="sl-status is-info">{{ $order['status'] }}</span></header><div class="sl-package-summary"><div class="sl-order-thumb">{{ mb_strtoupper(mb_substr($order['product'],0,1)) }}</div><div><strong>{{ $order['product'] }}</strong><span>{{ $order['buyer'] }} · Qty {{ $order['quantity'] }}</span><small>{{ $order['shipping_address'] }}</small></div><strong>₱{{ number_format($order['total'],2) }}</strong></div></section>
                <form class="sl-card sl-courier-select" method="POST" action="{{ route('seller.logistics.pickup',$order['db_id']) }}">
                    @csrf
                    <header class="sl-card-head"><div><span class="sl-eyebrow">Available Logistics</span><h2>Select Logistics Provider</h2><p>The selected provider is saved on the delivery record.</p></div></header>
                    <div class="sl-provider-list">
                        @foreach ([['LIKHAE Logistics','Tomorrow, 10:00 AM','1–2 days','₱145.00','Recommended'],['J&T Express','Tomorrow, 2:00 PM','1–3 days','₱135.00','Lowest rate'],['Flash Express','Sep 13, 9:00 AM','1–3 days','₱155.00',''],['Local Courier','Today, 4:30 PM','Same day','₱220.00','Fastest']] as $provider)
                            <label class="sl-provider-option"><input type="radio" name="provider" value="{{ $provider[0] }}" @checked($loop->first)><span class="sl-radio-mark"></span><span class="sl-provider-logo">{{ collect(explode(' ',$provider[0]))->map(fn($w)=>mb_substr($w,0,1))->join('') }}</span><span class="sl-provider-main"><strong>{{ $provider[0] }}</strong><small>Pickup: {{ $provider[1] }}</small></span><span class="sl-provider-meta"><small>{{ $provider[2] }}</small><strong>{{ $provider[3] }}</strong></span>@if($provider[4])<span class="sl-provider-tag">{{ $provider[4] }}</span>@endif</label>
                        @endforeach
                    </div>
                    <div class="sl-form-grid"><label class="sl-field"><span>Pickup date</span><input name="pickup_date" type="date" value="{{ now()->addDay()->toDateString() }}" required></label><label class="sl-field"><span>Pickup window</span><select name="pickup_window" required><option>10:00 AM – 12:00 PM</option><option>1:00 PM – 3:00 PM</option><option>3:00 PM – 5:00 PM</option></select></label><label class="sl-field sl-span-2"><span>Pickup note</span><textarea name="pickup_note" rows="3">Package is sealed and available at the store counter.</textarea></label></div>
                    <div class="sl-form-actions"><button type="submit" class="sl-btn sl-btn-primary">Submit Pickup Request</button></div>
                </form>
            </div>
        @else
            <section class="sl-card"><x-seller.empty-state title="No order available" message="Prepare an order before requesting pickup." action="View Orders" :href="route('seller.orders')" /></section>
        @endif

    @elseif ($isLogistics && $currentMode === 'pickups')
        @php
            $deliveryCollection = $deliveries ?? collect();
        @endphp
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Logistics</span><h2>Pickup Requests</h2><p>Delivery records are loaded from the database.</p></div><a href="{{ route('seller.logistics',['view'=>'couriers']) }}" class="sl-btn sl-btn-primary">New Pickup Request</a></div>
        <section class="sl-mini-stats"><div><span>Total Requests</span><strong>{{ $deliveryCollection->count() }}</strong></div><div><span>Requested</span><strong>{{ $deliveryCollection->where('status','requested')->count() }}</strong></div><div><span>In Transit</span><strong>{{ $deliveryCollection->whereIn('status',['assigned','out_for_delivery'])->count() }}</strong></div><div><span>Delivered</span><strong>{{ $deliveryCollection->where('status','delivered')->count() }}</strong></div></section>
        <section class="sl-card"><div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Tracking</th><th>Order</th><th>Provider</th><th>Pickup Window</th><th>Courier</th><th>Status</th><th></th></tr></thead><tbody>
            @forelse($deliveryCollection as $pickup)
                <tr><td><strong>{{ $pickup->tracking_number ?: 'Pending' }}</strong></td><td>#{{ $pickup->order?->order_number }}</td><td>{{ $pickup->provider ?: 'LIKHAE Logistics' }}</td><td>{{ $pickup->pickup_window ?: 'Not scheduled' }}</td><td>{{ $pickup->rider?->name ?: 'Waiting assignment' }}</td><td><span class="sl-status {{ $pickup->status==='delivered'?'is-success':($pickup->status==='requested'?'is-warning':'is-info') }}">{{ str($pickup->status)->headline() }}</span></td><td><a class="sl-btn sl-btn-ghost sl-btn-sm" href="{{ route('seller.logistics',['view'=>'tracking','order'=>$pickup->order?->order_number]) }}">Details</a></td></tr>
            @empty<tr><td colspan="7">No pickup requests yet.</td></tr>@endforelse
        </tbody></table></div></section>

    @elseif ($isLogistics && $currentMode === 'tracking')
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Logistics</span><h2>Shipment Tracking</h2><p>Live status is read from the order and delivery records.</p></div></div>
        @if($order)
            <div class="sl-tracking-layout">
                <section class="sl-card sl-shipment-summary"><div class="sl-shipment-head"><div><span class="sl-eyebrow">Order #{{ $order['id'] }}</span><h2>{{ $order['product'] }}</h2><p>Tracking no. {{ data_get($delivery,'tracking_number','Not assigned') }}</p></div><span class="sl-status is-info">{{ data_get($delivery,'status') ? str(data_get($delivery,'status'))->headline() : $order['status'] }}</span></div><dl class="sl-shipment-meta"><div><dt>Logistics</dt><dd>{{ data_get($delivery,'provider',$order['shipping']) }}</dd></div><div><dt>Courier</dt><dd>{{ data_get($delivery,'rider.name','Waiting assignment') }}</dd></div><div><dt>Buyer</dt><dd>{{ $order['buyer'] }}</dd></div><div><dt>Address</dt><dd>{{ $order['shipping_address'] }}</dd></div></dl></section>
                <section class="sl-card"><header class="sl-card-head"><div><span class="sl-eyebrow">Progress</span><h2>Shipment Timeline</h2><p>Timeline follows the current persisted delivery state.</p></div></header><ol class="sl-timeline">
                    @php $steps=['requested'=>'Pickup Requested','assigned'=>'Courier Assigned','out_for_delivery'=>'Out for Delivery','delivered'=>'Delivered']; $current=data_get($delivery,'status','requested'); $keys=array_keys($steps); $currentIndex=array_search($current,$keys,true); @endphp
                    @foreach($steps as $key=>$label)<li class="{{ array_search($key,$keys,true) <= ($currentIndex===false?0:$currentIndex) ? 'is-complete':'' }}"><span>{{ array_search($key,$keys,true) <= ($currentIndex===false?0:$currentIndex) ? '✓':'' }}</span><div><strong>{{ $label }}</strong><small>{{ data_get($delivery,'updated_at')?->format('M d · g:i A') }}</small><p>{{ $key===$current?'Current shipment state':'Shipment milestone' }}</p></div></li>@endforeach
                </ol></section>
            </div>
        @else<section class="sl-card"><x-seller.empty-state title="No shipment selected" message="Choose a delivery request to track." action="Pickup Requests" :href="route('seller.logistics',['view'=>'pickups'])" /></section>@endif

    @elseif (!$isLogistics && $currentMode === 'show')
        @if($order)
            <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Order Details</span><h2>Order #{{ $order['id'] }}</h2><p>Placed {{ $order['date'] }}</p></div><a href="{{ route('seller.orders',['status'=>$order['status_key']]) }}" class="sl-btn sl-btn-ghost">Back to Orders</a></div>
            <div class="sl-order-detail-layout"><div class="sl-order-detail-main"><section class="sl-card"><header class="sl-card-head"><div><h2>Ordered Product</h2><p>Items included in this purchase.</p></div><span class="sl-status is-warning">{{ $order['status'] }}</span></header><div class="sl-package-summary"><div class="sl-order-thumb">{{ mb_strtoupper(mb_substr($order['product'],0,1)) }}</div><div><strong>{{ $order['product'] }}</strong><span>Variation: {{ $order['variant'] }}</span><small>₱{{ number_format($order['total']/$order['quantity'],2) }} × {{ $order['quantity'] }}</small></div><strong>₱{{ number_format($order['total'],2) }}</strong></div></section>
            <section class="sl-card"><header class="sl-card-head"><div><h2>Fulfillment Actions</h2><p>Available actions depend on the current status.</p></div></header><div class="sl-action-panel"><span class="sl-action-panel-icon">✓</span><div><strong>{{ $order['status'] }}</strong><p>Use the valid next action to move the order through fulfillment.</p></div>
                @if($order['status_key']==='to-process')<form method="POST" action="{{ route('seller.orders.status',$order['db_id']) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="to_prepare"><button class="sl-btn sl-btn-primary" type="submit">Accept Order</button></form>@endif
                @if($order['status_key']==='to-prepare')<a target="_blank" class="sl-btn sl-btn-ghost" href="{{ route('seller.orders.waybill',$order['db_id']) }}">Print Waybill</a><form method="POST" action="{{ route('seller.orders.status',$order['db_id']) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="ready_pickup"><button class="sl-btn sl-btn-primary" type="submit">Mark as Prepared</button></form>@endif
                @if($order['status_key']==='ready-pickup')<a href="{{ route('seller.logistics',['view'=>'couriers','order'=>$order['id']]) }}" class="sl-btn sl-btn-primary">Assign Courier</a>@endif
                @if($order['status_key']==='shipping')<a href="{{ route('seller.logistics',['view'=>'tracking','order'=>$order['id']]) }}" class="sl-btn sl-btn-primary">Track Shipment</a>@endif
            </div></section></div><aside class="sl-order-detail-side"><section class="sl-card"><h3>Buyer Information</h3><dl class="sl-detail-list"><div><dt>Name</dt><dd>{{ $order['buyer'] }}</dd></div><div><dt>Delivery Address</dt><dd>{{ $order['shipping_address'] }}</dd></div></dl><a href="{{ route('seller.messages',['buyer'=>$order['buyer_id']]) }}" class="sl-btn sl-btn-soft sl-btn-block">Message Buyer</a></section><section class="sl-card"><h3>Payment Summary</h3><dl class="sl-price-list"><div><dt>Order total</dt><dd>₱{{ number_format($order['total'],2) }}</dd></div><div class="is-total"><dt>Payment</dt><dd>{{ $order['payment'] }}</dd></div></dl></section></aside></div>
        @else<section class="sl-card"><x-seller.empty-state title="Order not found" message="The selected order does not exist." action="Back to Orders" :href="route('seller.orders')" /></section>@endif

    @else
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Order Management</span><h2>Manage Orders</h2><p>Actions now persist real order status changes.</p></div><a href="{{ route('seller.orders.export') }}" class="sl-btn sl-btn-ghost">Export CSV</a></div>
        <section class="sl-order-tabs" aria-label="Order filters">
            @foreach(['all'=>'All Orders','to-process'=>'To Process','to-prepare'=>'To Prepare','ready-pickup'=>'Ready Pickup','shipping'=>'Shipping','completed'=>'Completed','cancelled'=>'Cancelled','returns'=>'Returns/Refunds'] as $key=>$label)<a href="{{ route('seller.orders',['status'=>$key]) }}" class="{{ $currentStatus===$key?'is-active':'' }}">{{ $label }} @if(($statusCounts[$key]??0)>0)<span>{{ $statusCounts[$key] }}</span>@endif</a>@endforeach
        </section>
        <section class="sl-card sl-order-filters"><div class="sl-search-input"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" name="q" value="{{ request('q') }}" placeholder="Search order, buyer, or product" data-order-search></div></section>
        <div class="sl-order-list">@php $visibleOrders=$currentStatus==='all'?$sellerOrders:$sellerOrders->where('status_key',$currentStatus); @endphp @forelse($visibleOrders as $item)<x-seller.order-card :order="$item" />@empty<section class="sl-card"><x-seller.empty-state title="No orders in this stage" message="Orders will appear here when they enter this fulfillment stage." action="View All Orders" :href="route('seller.orders')" /></section>@endforelse</div><div class="sl-no-results sl-card" data-order-empty hidden>No orders match your search.</div>
    @endif
</div>
@endsection
