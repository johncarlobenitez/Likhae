@props(['order'])

@php
    $id = data_get($order, 'id');
    $status = data_get($order, 'status', 'to-pay');
    $statusLabel = data_get($order, 'status_label', \Illuminate\Support\Str::headline($status));
    $products = collect(data_get($order, 'products', []));
    $statusClass = match($status) {
        'to-pay' => 'bg-amber-50 text-amber-800 border-amber-200',
        'to-ship' => 'bg-stone-50 text-stone-700 border-stone-200',
        'to-receive' => 'bg-red-50 text-red-900 border-red-200',
        'completed' => 'bg-stone-100 text-stone-700 border-stone-200',
        'cancelled' => 'bg-red-50 text-red-800 border-red-200',
        'returns' => 'bg-orange-50 text-orange-800 border-orange-200',
        default => 'bg-stone-50 text-stone-700 border-stone-200',
    };
@endphp

<article class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm" data-order-card="{{ $id }}">
    <header class="flex flex-wrap items-center justify-between gap-3 border-b border-stone-100 bg-stone-50/70 px-4 py-3 sm:px-5">
        <div class="flex flex-wrap items-center gap-3"><strong class="text-xs text-stone-900">Order #{{ $id }}</strong><span class="text-[10px] text-stone-400">Placed {{ data_get($order, 'placed_at') }}</span></div>
        <span class="rounded-full border px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $statusClass }}">{{ $statusLabel }}</span>
    </header>

    <div class="divide-y divide-stone-100">
        @foreach($products as $product)
            <div class="flex gap-3 p-4 sm:px-5"><img class="h-16 w-16 shrink-0 rounded-xl bg-stone-50 object-cover" src="{{ data_get($product, 'image') }}" alt="{{ data_get($product, 'name') }}"><div class="min-w-0 flex-1"><strong class="line-clamp-2 text-sm text-stone-900">{{ data_get($product, 'name') }}</strong><p class="mt-1 text-[11px] text-stone-500">{{ data_get($product, 'variant') }} · Qty {{ data_get($product, 'quantity', 1) }}</p><span class="mt-1 block text-xs font-semibold text-red-900">₱{{ number_format((float) data_get($product, 'price'), 2) }}</span></div></div>
        @endforeach
    </div>

    @if($status === 'to-receive')
        <div class="border-t border-stone-100 bg-red-50/60 px-4 py-3 text-[11px] text-red-900 sm:px-5">
            @if(data_get($order, 'delivered_at')) Delivered {{ data_get($order, 'delivered_at') }}. Auto-receive on {{ data_get($order, 'auto_receive_at') }} if no action is taken. @else Parcel is in transit. Track it for the latest courier update. @endif
        </div>
    @endif
    @if($status === 'returns')
        <div class="border-t border-stone-100 bg-orange-50/60 px-4 py-3 text-[11px] text-orange-900 sm:px-5"><strong>Seller review:</strong> {{ data_get($order, 'case_status', 'Submitted for review') }} · {{ data_get($order, 'case_note', 'The seller will review the evidence and respond to the request.') }}</div>
    @endif

    <footer class="flex flex-col gap-3 border-t border-stone-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <div class="text-xs text-stone-500">{{ data_get($order, 'payment') }} · <strong class="text-sm text-stone-900">₱{{ number_format((float) data_get($order, 'total'), 2) }}</strong></div>
        <div class="flex flex-wrap gap-2">
            <a class="lk-btn lk-btn-light" href="{{ route('buyer.orders.show', ['id' => $id]) }}">View Details</a>
            @if($status === 'to-pay')
                <button type="button" class="lk-btn lk-btn-light" data-cancel-order data-order-id="{{ $id }}">Cancel</button><button type="button" class="lk-btn lk-btn-red" data-demo-action="Payment window opened for order #{{ $id }}.">Pay Now</button>
            @elseif($status === 'to-receive')
                @if(data_get($order, 'delivered_at'))<form method="POST" action="{{ route('buyer.orders.received', ['id' => $id]) }}">@csrf<button class="lk-btn lk-btn-red" type="submit">Order Received</button></form>@else<button type="button" class="lk-btn lk-btn-red" data-demo-action="Shipment tracking opened for order #{{ $id }}.">Track Parcel</button>@endif
            @elseif($status === 'completed')
                <a class="lk-btn lk-btn-light" href="{{ route('buyer.orders.return', ['id' => $id]) }}">Return / Refund</a><a class="lk-btn lk-btn-red" href="{{ route('buyer.orders.review', ['id' => $id]) }}">Write Review</a>
            @elseif($status === 'returns')
                <a class="lk-btn lk-btn-red" href="{{ route('buyer.orders.show', ['id' => $id]) }}">View Case</a>
            @endif
        </div>
    </footer>
</article>
