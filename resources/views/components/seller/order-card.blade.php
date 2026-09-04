@props(['order'])

@php
    $id = data_get($order, 'id', 'LK-10001');
    $status = data_get($order, 'status', 'To Process');
    $statusKey = (string) data_get($order, 'status_key', 'to-process');
    $statusTone = match ($statusKey) {
        'completed' => 'is-success',
        'cancelled', 'returns' => 'is-danger',
        'shipping', 'ready-pickup' => 'is-info',
        'to-prepare' => 'is-warning',
        default => 'is-neutral',
    };
@endphp

<article class="sl-order-card" data-order-card data-status="{{ $statusKey }}" data-search="{{ mb_strtolower($id.' '.data_get($order, 'buyer').' '.data_get($order, 'product')) }}">
    <header class="sl-order-card-head">
        <div>
            <span class="sl-order-id">Order #{{ $id }}</span>
            <small>Placed {{ data_get($order, 'date', 'Sep 04, 2026 · 9:30 AM') }}</small>
        </div>
        <span class="sl-status {{ $statusTone }}" data-order-status>{{ $status }}</span>
    </header>

    <div class="sl-order-card-body">
        <div class="sl-order-product">
            <span class="sl-order-thumb">{{ mb_strtoupper(mb_substr(data_get($order, 'product', 'P'), 0, 1)) }}</span>
            <div><strong>{{ data_get($order, 'product', 'Product') }}</strong><small>Qty: {{ data_get($order, 'quantity', 1) }}</small></div>
        </div>
        <dl class="sl-order-details">
            <div><dt>Buyer</dt><dd>{{ data_get($order, 'buyer', 'Buyer') }}</dd></div>
            <div><dt>Payment</dt><dd>{{ data_get($order, 'payment', 'COD') }}</dd></div>
            <div><dt>Shipping</dt><dd>{{ data_get($order, 'shipping', 'Standard') }}</dd></div>
            <div><dt>Total</dt><dd><strong>₱{{ number_format((float) data_get($order, 'total', 0), 2) }}</strong></dd></div>
        </dl>
    </div>

    <footer class="sl-order-card-actions">
        <a href="{{ route('seller.orders', ['mode' => 'show', 'order' => $id]) }}" class="sl-btn sl-btn-ghost sl-btn-sm">View Details</a>
        @if ($statusKey === 'to-process')
            <button type="button" class="sl-btn sl-btn-primary sl-btn-sm" data-order-action="accept">Accept Order</button>
        @elseif ($statusKey === 'to-prepare')
            <button type="button" class="sl-btn sl-btn-soft sl-btn-sm" data-demo-action="Waybill opened for printing">Print Waybill</button>
            <button type="button" class="sl-btn sl-btn-primary sl-btn-sm" data-order-action="prepare">Prepare Package</button>
        @elseif ($statusKey === 'ready-pickup')
            <a href="{{ route('seller.logistics', ['view' => 'couriers', 'order' => $id]) }}" class="sl-btn sl-btn-primary sl-btn-sm">Assign Courier</a>
        @elseif ($statusKey === 'shipping')
            <a href="{{ route('seller.logistics', ['view' => 'tracking', 'order' => $id]) }}" class="sl-btn sl-btn-primary sl-btn-sm">Track Shipment</a>
        @elseif ($statusKey === 'returns')
            <button type="button" class="sl-btn sl-btn-primary sl-btn-sm" data-demo-action="Return request opened">Review Request</button>
        @endif
    </footer>
</article>
