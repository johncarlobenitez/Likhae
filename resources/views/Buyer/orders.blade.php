@extends('layouts.buyer')
@section('title', 'My Orders')
@section('active', 'orders')

@section('content')
@php
    $mode = $mode ?? 'index';
    $orderCollection = isset($orders) && method_exists($orders, 'getCollection') ? $orders->getCollection() : collect($orders ?? []);
    $filter = strtoupper((string) request('status', ''));
    $visibleOrders = $filter === '' || $filter === 'ALL' ? $orderCollection : $orderCollection->where('status', $filter);
@endphp

<div class="lk-page-narrow">
    @if(session('buyer_notice'))<div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800">{{ session('buyer_notice') }}</div>@endif

    @if($mode === 'success')
        <section class="rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm"><div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 text-3xl text-red-900">✓</div><span class="mt-5 block text-[10px] font-bold uppercase tracking-widest text-red-800">Order placed</span><h1 class="mt-2 text-2xl font-bold text-stone-950">Thank you for your order</h1><p class="mt-2 text-sm text-stone-500">{{ $selectedOrder?->order_number ? 'Order '.$selectedOrder->order_number.' was placed successfully.' : 'Your order was placed successfully.' }}</p><div class="mt-6 flex justify-center gap-2"><a class="lk-btn lk-btn-red" href="{{ route('buyer.orders') }}">View My Orders</a><a class="lk-btn lk-btn-light" href="{{ route('buyer.products') }}">Continue Shopping</a></div></section>
    @elseif($mode === 'show' && $selectedOrder)
        <div class="lk-page-title"><div><span class="lk-kicker">Order details</span><h1>{{ $selectedOrder->order_number }}</h1><p>{{ str($selectedOrder->status)->headline() }}</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.orders') }}">Back to Orders</a></div>
        @foreach($selectedOrder->sellerOrders as $sellerOrder)
            <section class="mt-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><h2 class="font-bold">{{ $sellerOrder->seller_order_number }}</h2><span class="text-xs font-semibold">{{ str($sellerOrder->status)->headline() }}</span></div><div class="mt-4 divide-y divide-stone-100">@foreach($sellerOrder->items as $item)<div class="flex justify-between gap-4 py-3 text-sm"><div><strong>{{ $item->product_name }}</strong><span class="block text-xs text-stone-500">{{ $item->variant_description ?: 'Standard' }} · Qty {{ $item->quantity }}</span></div><strong>&#8369;{{ number_format((float) $item->line_total, 2) }}</strong></div>@endforeach</div>@if($sellerOrder->shipment)<p class="mt-3 text-xs text-stone-500">Tracking: {{ $sellerOrder->shipment->tracking_number }} · {{ str($sellerOrder->shipment->current_status)->headline() }}</p>@endif</section>
        @endforeach
        @php($allDelivered = $selectedOrder->sellerOrders->isNotEmpty() && $selectedOrder->sellerOrders->every(fn ($sellerOrder) => $sellerOrder->shipment?->current_status === 'DELIVERED'))
        <div class="mt-5 flex flex-wrap justify-end gap-2">
            @if(in_array($selectedOrder->status, ['PLACED', 'PROCESSING'], true))<form method="POST" action="{{ route('buyer.orders.cancel', $selectedOrder) }}">@csrf<button class="lk-btn lk-btn-light" type="submit">Cancel Order</button></form>@endif
            @if($allDelivered)<form method="POST" action="{{ route('buyer.orders.received', $selectedOrder) }}">@csrf<button class="lk-btn lk-btn-red" type="submit">Confirm Order Received</button></form>@endif
        </div>
    @else
        <div class="lk-page-title"><div><span class="lk-kicker">Order Center</span><h1>My Orders</h1><p>Track fulfillment and confirm receipt after delivery.</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.products') }}">Shop Products</a></div>
        <nav class="mt-4 flex gap-1 overflow-x-auto rounded-xl border border-stone-200 bg-white p-1 shadow-sm">@foreach(['' => 'All', 'PLACED' => 'Placed', 'PROCESSING' => 'Processing', 'COMPLETED' => 'Completed', 'CANCELLED' => 'Cancelled'] as $key => $label)<a href="{{ route('buyer.orders', ['status' => $key]) }}" class="rounded-lg px-3.5 py-2.5 text-xs font-semibold {{ $filter === $key ? 'bg-red-900 text-white' : 'text-stone-600' }}">{{ $label }}</a>@endforeach</nav>
        <div class="mt-5 space-y-4">@forelse($visibleOrders as $order)<article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><div class="flex flex-wrap items-start justify-between gap-3"><div><span class="text-xs text-stone-500">{{ optional($order->placed_at)->format('M j, Y g:i A') }}</span><h2 class="mt-1 font-bold text-stone-900">{{ $order->order_number }}</h2></div><span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold">{{ str($order->status)->headline() }}</span></div><div class="mt-4 flex items-end justify-between border-t border-stone-100 pt-4"><div><span class="block text-xs text-stone-500">{{ $order->sellerOrders->sum(fn ($sellerOrder) => $sellerOrder->items->sum('quantity')) }} item(s)</span><strong>&#8369;{{ number_format((float) $order->grand_total, 2) }}</strong></div><a class="lk-btn lk-btn-red" href="{{ route('buyer.orders.show', $order) }}">View Details</a></div></article>@empty<x-buyer.empty-state title="No orders found" message="Your matching orders will appear here." />@endforelse</div>
        @if(isset($orders) && method_exists($orders, 'links'))<div class="mt-5">{{ $orders->links() }}</div>@endif
    @endif
</div>
@endsection
