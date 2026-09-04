@extends('layouts.buyer')

@section('title', 'Orders')
@section('active', 'orders')
@section('subtitle', 'Track, receive, review, cancel, or request a return.')

@section('content')
@php
    $mode = $mode ?? 'index';
    $orders = collect($buyerOrders ?? []);
    $selectedOrder = $orders->firstWhere('id', $selectedOrderId ?? request()->route('id'));
    $status = request('status', 'all');
    $tabs = ['all' => 'All', 'to-pay' => 'To Pay', 'to-ship' => 'To Ship', 'to-receive' => 'To Receive', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'returns' => 'Returns / Refunds'];
    $visibleOrders = $status === 'all' ? $orders : $orders->where('status', $status);
@endphp

<div class="lk-page-narrow">
    @if(session('buyer_notice'))<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs font-medium text-red-900">{{ session('buyer_notice') }}</div>@endif

    @if($mode === 'success')
        <section class="rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm"><div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 text-3xl text-red-900">✓</div><span class="mt-5 block text-[10px] font-bold uppercase tracking-widest text-red-800">Order placed</span><h1 class="mt-2 text-2xl font-bold text-stone-950">Thank you for your order</h1><p class="mx-auto mt-2 max-w-md text-sm text-stone-500">Your order is now under To Pay or To Ship depending on the selected payment method. You can monitor every update here.</p><div class="mt-6 flex flex-wrap justify-center gap-2"><a class="lk-btn lk-btn-red" href="{{ route('buyer.orders') }}">View My Orders</a><a class="lk-btn lk-btn-light" href="{{ route('buyer.products') }}">Continue Shopping</a></div></section>
    @elseif(in_array($mode, ['review','return'], true))
        @if($selectedOrder)
            <div class="lk-page-title"><div><span class="lk-kicker">Order #{{ data_get($selectedOrder,'id') }}</span><h1>{{ $mode === 'review' ? 'Review Product' : 'Return / Refund Request' }}</h1><p>{{ $mode === 'review' ? 'Share an honest review to help other buyers.' : 'Provide the reason and evidence for administrator validation.' }}</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.orders') }}">Back to Orders</a></div>
            <form method="POST" action="{{ $mode === 'review' ? route('buyer.orders.review.store', ['id' => data_get($selectedOrder,'id')]) : route('buyer.orders.return.store', ['id' => data_get($selectedOrder,'id')]) }}" class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:p-6" enctype="multipart/form-data">
                @csrf
                @php $firstProduct = collect(data_get($selectedOrder,'products',[]))->first(); @endphp
                <div class="flex gap-3 border-b border-stone-100 pb-5"><img class="h-16 w-16 rounded-xl object-cover" src="{{ data_get($firstProduct,'image') }}" alt=""><div><strong class="text-sm text-stone-900">{{ data_get($firstProduct,'name') }}</strong><p class="mt-1 text-xs text-stone-500">{{ data_get($firstProduct,'variant') }}</p></div></div>
                @if($mode === 'review')
                    <fieldset class="mt-5"><legend class="text-xs font-semibold text-stone-700">Your rating</legend><div class="mt-2 flex gap-2">@foreach(range(5,1) as $star)<label class="cursor-pointer"><input class="peer sr-only" type="radio" name="rating" value="{{ $star }}" @checked($star===5)><span class="block rounded-lg border border-stone-200 px-3 py-2 text-sm text-amber-500 peer-checked:border-red-800 peer-checked:bg-red-50">{{ $star }} ★</span></label>@endforeach</div></fieldset>
                    <label class="mt-5 block text-xs font-semibold text-stone-700">Review<textarea required name="review" rows="5" class="mt-2 block w-full rounded-xl border border-stone-300 p-3 text-sm outline-none focus:border-red-800" placeholder="Quality, fit, packaging, and your experience..."></textarea></label>
                    <label class="mt-4 block text-xs font-semibold text-stone-700">Photos (optional)<input type="file" name="photos[]" multiple accept="image/*" class="mt-2 block w-full rounded-xl border border-dashed border-stone-300 p-3 text-xs text-stone-500"></label>
                    <button class="lk-btn lk-btn-red mt-5" type="submit">Submit Review</button>
                @else
                    <div class="mt-5 grid gap-4 sm:grid-cols-2"><label class="text-xs font-semibold text-stone-700">Request type<select required name="request_type" class="mt-2 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-red-800"><option value="">Choose an option</option><option>Return and refund</option><option>Refund only</option><option>Replacement</option></select></label><label class="text-xs font-semibold text-stone-700">Reason<select required name="reason" class="mt-2 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-red-800"><option value="">Choose a reason</option><option>Damaged item</option><option>Wrong item received</option><option>Missing parts</option><option>Item not as described</option><option>Suspected counterfeit</option></select></label></div>
                    <label class="mt-4 block text-xs font-semibold text-stone-700">Explain what happened<textarea required minlength="20" name="details" rows="5" class="mt-2 block w-full rounded-xl border border-stone-300 p-3 text-sm outline-none focus:border-red-800" placeholder="Give the administrator enough detail to validate this request."></textarea></label>
                    <label class="mt-4 block text-xs font-semibold text-stone-700">Evidence<input required type="file" name="evidence[]" multiple accept="image/*,video/*" class="mt-2 block w-full rounded-xl border border-dashed border-stone-300 p-3 text-xs text-stone-500"></label>
                    <div class="mt-4 rounded-xl bg-amber-50 p-4 text-xs leading-5 text-amber-900"><strong>Administrator validation:</strong> LIKHAE reviews your explanation, evidence, seller response, and delivery record before approving or rejecting the request.</div>
                    <button class="lk-btn lk-btn-red mt-5" type="submit">Submit for Validation</button>
                @endif
            </form>
        @else
            <x-buyer.empty-state title="Order not found" message="This order is unavailable or does not belong to your account." />
        @endif
    @elseif($mode === 'show')
        @if($selectedOrder)
            <div class="lk-page-title"><div><span class="lk-kicker">Order details</span><h1>#{{ data_get($selectedOrder,'id') }}</h1><p>{{ data_get($selectedOrder,'status_label') }} · {{ data_get($selectedOrder,'payment') }}</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.orders') }}">Back to Orders</a></div>
            <div class="grid gap-5 lg:grid-cols-[1fr_340px]">
                <div class="space-y-5"><x-buyer.order-card :order="$selectedOrder" /><section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Shipment timeline</h2><ol class="mt-4 space-y-0">@foreach(data_get($selectedOrder,'timeline',[]) as $event)<li class="relative flex gap-3 pb-5 last:pb-0"><span class="z-10 flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ data_get($event,'done') ? 'bg-red-900 text-white' : 'bg-stone-100 text-stone-400' }}">{{ data_get($event,'done') ? '✓' : '•' }}</span><span class="absolute left-[13px] top-7 h-[calc(100%-1.75rem)] w-px bg-stone-200 last:hidden"></span><div><strong class="block text-xs text-stone-800">{{ data_get($event,'label') }}</strong><span class="text-[10px] text-stone-400">{{ data_get($event,'time','Pending') }}</span></div></li>@endforeach</ol></section></div>
                <aside class="space-y-5"><section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Delivery address</h2><strong class="mt-3 block text-xs">Maria Santos</strong><p class="mt-1 text-xs leading-5 text-stone-500">0917 123 4567<br>123 Sampaguita Street, Barangay Central, Quezon City 1100</p></section><section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><h2 class="text-base font-bold text-stone-900">Payment summary</h2><dl class="mt-3 space-y-2 text-xs"><div class="flex justify-between"><dt class="text-stone-500">Items</dt><dd>₱{{ number_format(data_get($selectedOrder,'total')-80,2) }}</dd></div><div class="flex justify-between"><dt class="text-stone-500">Shipping</dt><dd>₱80.00</dd></div><div class="flex justify-between border-t border-stone-100 pt-3 font-bold"><dt>Total</dt><dd class="text-red-900">₱{{ number_format(data_get($selectedOrder,'total'),2) }}</dd></div></dl></section></aside>
            </div>
        @else<x-buyer.empty-state title="Order not found" message="This order is unavailable or does not belong to your account." />@endif
    @else
        <div class="lk-page-title"><div><span class="lk-kicker">Order Center</span><h1>My Orders</h1><p>One place for payment, delivery, receipt, reviews, and return cases.</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.products') }}">Shop Products</a></div>
        <nav class="flex gap-1 overflow-x-auto rounded-xl border border-stone-200 bg-white p-1 shadow-sm" aria-label="Order statuses">@foreach($tabs as $key => $label)<a href="{{ route('buyer.orders', ['status' => $key]) }}" class="whitespace-nowrap rounded-lg px-3.5 py-2.5 text-xs font-semibold {{ $status === $key ? 'bg-red-900 text-white' : 'text-stone-600 hover:bg-stone-50' }}">{{ $label }} <span class="ml-1 opacity-70">{{ $key === 'all' ? $orders->count() : $orders->where('status',$key)->count() }}</span></a>@endforeach</nav>
        <div class="mt-5 space-y-4">@forelse($visibleOrders as $order)<x-buyer.order-card :order="$order" />@empty<x-buyer.empty-state title="No orders in this section" message="Orders matching this status will appear here." />@endforelse</div>
    @endif
</div>

<dialog class="w-[min(92vw,480px)] rounded-2xl border border-stone-200 p-0 shadow-2xl backdrop:bg-stone-950/40" data-cancel-dialog>
    <form method="POST" action="{{ route('buyer.orders.cancel') }}" class="p-5 sm:p-6">@csrf<input type="hidden" name="order_id" data-cancel-order-id><div class="flex items-start justify-between gap-4"><div><span class="text-[10px] font-bold uppercase tracking-widest text-red-800">Cancellation</span><h2 class="text-lg font-bold text-stone-900">Cancel this order?</h2></div><button type="button" class="text-xl text-stone-400" data-close-cancel aria-label="Close">×</button></div><p class="mt-2 text-xs leading-5 text-stone-500">Cancellation is available while the order is under To Pay. Tell us why you need to cancel it.</p><label class="mt-4 block text-xs font-semibold text-stone-700">Reason<select required name="reason" class="mt-2 block w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-red-800"><option value="">Choose a reason</option><option>Changed my mind</option><option>Need to change address</option><option>Found a better option</option><option>Payment issue</option><option>Ordered by mistake</option></select></label><label class="mt-4 block text-xs font-semibold text-stone-700">Additional note<textarea name="note" rows="3" class="mt-2 block w-full rounded-xl border border-stone-300 p-3 text-sm outline-none focus:border-red-800"></textarea></label><div class="mt-5 flex justify-end gap-2"><button type="button" class="lk-btn lk-btn-light" data-close-cancel>Keep Order</button><button type="submit" class="lk-btn lk-btn-red">Confirm Cancellation</button></div></form>
</dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dialog = document.querySelector('[data-cancel-dialog]');
    document.querySelectorAll('[data-cancel-order]').forEach((button) => button.addEventListener('click', () => {
        if (!dialog) return;
        dialog.querySelector('[data-cancel-order-id]').value = button.dataset.orderId || '';
        dialog.showModal();
    }));
    document.querySelectorAll('[data-close-cancel]').forEach((button) => button.addEventListener('click', () => dialog?.close()));
});
</script>
@endpush
