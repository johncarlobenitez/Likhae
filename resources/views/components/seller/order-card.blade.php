@props(['order'])

@php
    $id = data_get($order, 'id');
    $dbId = data_get($order, 'db_id');
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
    <header class="sl-order-card-head"><div><span class="sl-order-id">Order #{{ $id }}</span><small>Placed {{ data_get($order, 'date') }}</small></div><span class="sl-status {{ $statusTone }}" data-order-status>{{ $status }}</span></header>
    <div class="sl-order-card-body">
        <div class="sl-order-product"><span class="sl-order-thumb">{{ mb_strtoupper(mb_substr(data_get($order, 'product','P'),0,1)) }}</span><div><strong>{{ data_get($order,'product') }}</strong><small>{{ data_get($order,'variant','Standard') }} · Qty: {{ data_get($order,'quantity',1) }}</small></div></div>
        <dl class="sl-order-details"><div><dt>Buyer</dt><dd>{{ data_get($order,'buyer') }}</dd></div><div><dt>Payment</dt><dd>{{ data_get($order,'payment') }}</dd></div><div><dt>Shipping</dt><dd>{{ data_get($order,'shipping') }}</dd></div><div><dt>Total</dt><dd><strong>₱{{ number_format((float) data_get($order,'total'),2) }}</strong></dd></div></dl>
    </div>
    <footer class="sl-order-card-actions">
        <a href="{{ route('seller.orders',['mode'=>'show','order'=>$id]) }}" class="sl-btn sl-btn-ghost sl-btn-sm">View Details</a>
        @if ($statusKey === 'placed')
            <form method="POST" action="{{ route('seller.orders.status',$dbId) }}">@csrf @method('PATCH')<input type="hidden" name="action" value="confirm"><button class="sl-btn sl-btn-primary sl-btn-sm" type="submit">Accept Order</button></form>
        @elseif ($statusKey === 'confirmed')
            <form method="POST" action="{{ route('seller.orders.status',$dbId) }}">@csrf @method('PATCH')<input type="hidden" name="action" value="prepare"><button class="sl-btn sl-btn-primary sl-btn-sm" type="submit">Begin Preparation</button></form>
        @elseif ($statusKey === 'preparing')
            <a target="_blank" href="{{ route('seller.orders.waybill',$dbId) }}" class="sl-btn sl-btn-ghost sl-btn-sm">Print Waybill</a>
            <form method="POST" action="{{ route('seller.orders.status',$dbId) }}">@csrf @method('PATCH')<input type="hidden" name="action" value="ready"><button class="sl-btn sl-btn-primary sl-btn-sm" type="submit">Ready for Pickup</button></form>
        @elseif ($statusKey === 'ready-for-pickup')
            <span class="sl-status is-info">Waiting for Logistics</span>
        @elseif ($statusKey === 'shipping')
            <a href="{{ route('seller.logistics',['view'=>'tracking','order'=>$id]) }}" class="sl-btn sl-btn-primary sl-btn-sm">Track Shipment</a>
        @elseif ($statusKey === 'returns')
            <form method="POST" action="{{ route('seller.orders.status',$dbId) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="completed"><button class="sl-btn sl-btn-primary sl-btn-sm" type="submit">Resolve Return</button></form>
        @endif
    </footer>
</article>
