@props([
    'order',
])

@php
    $id = data_get($order, 'id', '10001');

    $product = data_get($order, 'product', 'Product');
    $buyer = data_get($order, 'buyer', 'Buyer');
    $variant = data_get($order, 'variant', 'Standard');
    $quantity = (int) data_get($order, 'quantity', 1);
    $total = (float) data_get($order, 'total', 0);

    $status = data_get($order, 'status', 'To Process');
    $statusKey = (string) data_get($order, 'status_key', 'to-process');

    $payment = data_get($order, 'payment', 'COD');
    $shipping = data_get($order, 'shipping', 'Standard');
    $date = data_get($order, 'date', 'Sep 04, 2026 · 9:30 AM');

    $statusTone = match ($statusKey) {
        'completed' => 'is-success',
        'cancelled', 'returns', 'refunded' => 'is-danger',
        'shipping', 'ready-pickup', 'in-transit', 'out-for-delivery' => 'is-info',
        'to-process', 'to-prepare' => 'is-warning',
        default => 'is-neutral',
    };

    $searchText = mb_strtolower($id . ' ' . $buyer . ' ' . $product . ' ' . $status . ' ' . $payment);

    $initial = mb_strtoupper(mb_substr($product ?: 'P', 0, 1));

    $nextAction = match ($statusKey) {
        'to-process' => [
            'label' => 'Accept Order',
            'type' => 'button',
            'class' => 'sl-btn sl-btn-primary sl-btn-sm',
            'attrs' => 'data-order-action=accept',
        ],
        'to-prepare' => [
            'label' => 'Prepare Package',
            'type' => 'button',
            'class' => 'sl-btn sl-btn-primary sl-btn-sm',
            'attrs' => 'data-order-action=prepare',
        ],
        'ready-pickup' => [
            'label' => 'Request Pickup',
            'type' => 'link',
            'class' => 'sl-btn sl-btn-primary sl-btn-sm',
            'href' => route('seller.logistics', ['view' => 'couriers', 'order' => $id]),
        ],
        'shipping' => [
            'label' => 'Track Shipment',
            'type' => 'link',
            'class' => 'sl-btn sl-btn-primary sl-btn-sm',
            'href' => route('seller.logistics', ['view' => 'tracking', 'order' => $id]),
        ],
        'returns' => [
            'label' => 'Review Request',
            'type' => 'button',
            'class' => 'sl-btn sl-btn-primary sl-btn-sm',
            'attrs' => 'data-demo-action=Return request opened',
        ],
        default => null,
    };
@endphp

<article
    class="sl-order-card"
    data-order-card
    data-status="{{ $statusKey }}"
    data-search="{{ $searchText }}"
>
    <header class="sl-order-card-head">
        <div class="sl-order-heading">
            <span class="sl-order-id">
                Order #{{ $id }}
            </span>

            <small>
                Placed {{ $date }}
            </small>
        </div>

        <span class="sl-status {{ $statusTone }}" data-order-status>
            {{ $status }}
        </span>
    </header>

    <div class="sl-order-card-body">
        <div class="sl-order-product">
            <span class="sl-order-thumb" aria-hidden="true">
                {{ $initial }}
            </span>

            <div>
                <strong>
                    {{ $product }}
                </strong>

                <small>
                    {{ $variant }} · Qty {{ $quantity }}
                </small>
            </div>
        </div>

        <dl class="sl-order-details">
            <div>
                <dt>Buyer</dt>
                <dd>{{ $buyer }}</dd>
            </div>

            <div>
                <dt>Payment</dt>
                <dd>{{ $payment }}</dd>
            </div>

            <div>
                <dt>Shipping</dt>
                <dd>{{ $shipping }}</dd>
            </div>

            <div>
                <dt>Total</dt>
                <dd>
                    <strong>
                        ₱{{ number_format($total, 2) }}
                    </strong>
                </dd>
            </div>
        </dl>
    </div>

    <footer class="sl-order-card-actions">
        <a
            href="{{ route('seller.orders', ['mode' => 'show', 'order' => $id]) }}"
            class="sl-btn sl-btn-ghost sl-btn-sm"
        >
            View Details
        </a>

        @if ($statusKey === 'to-prepare')
            <button
                type="button"
                class="sl-btn sl-btn-soft sl-btn-sm"
                data-demo-action="Waybill opened for printing."
            >
                Print Waybill
            </button>
        @endif

        @if ($nextAction)
            @if ($nextAction['type'] === 'link')
                <a
                    href="{{ $nextAction['href'] }}"
                    class="{{ $nextAction['class'] }}"
                >
                    {{ $nextAction['label'] }}
                </a>
            @else
                <button
                    type="button"
                    class="{{ $nextAction['class'] }}"
                    @if (isset($nextAction['attrs']) && str_contains($nextAction['attrs'], 'data-order-action=accept'))
                        data-order-action="accept"
                    @elseif (isset($nextAction['attrs']) && str_contains($nextAction['attrs'], 'data-order-action=prepare'))
                        data-order-action="prepare"
                    @elseif (isset($nextAction['attrs']) && str_contains($nextAction['attrs'], 'data-demo-action=Return request opened'))
                        data-demo-action="Return request opened."
                    @endif
                >
                    {{ $nextAction['label'] }}
                </button>
            @endif
        @endif
    </footer>
</article>