@props(['status' => 'Active'])

@php
    $key = strtolower(str_replace([' ', '/'], ['-', '-'], $status));
    $styles = [
        'active' => 'status-success',
        'approved' => 'status-success',
        'verified' => 'status-success',
        'delivered' => 'status-success',
        'completed' => 'status-success',
        'paid' => 'status-success',
        'to-prepare' => 'status-warning',
        'new-order' => 'status-warning',
        'ready-for-pickup' => 'status-info',
        'in-transit' => 'status-info',
        'picked-up' => 'status-info',
        'under-review' => 'status-info',
        'pending' => 'status-warning',
        'low-stock' => 'status-warning',
        'cancelled' => 'status-danger',
        'rejected' => 'status-danger',
        'out-of-stock' => 'status-danger',
        'return-requested' => 'status-danger',
        'refunded' => 'status-neutral',
        'draft' => 'status-neutral',
        'archived' => 'status-neutral',
    ];
    $class = $styles[$key] ?? 'status-neutral';
@endphp

<span {{ $attributes->merge(['class' => "status-badge {$class}"]) }}>
    <span class="status-dot"></span>
    {{ $status }}
</span>
