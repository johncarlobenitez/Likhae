@props([
    'status' => 'Active',
])

@php
    $label = trim((string) $status);

    $key = str($label)
        ->lower()
        ->replace(['/', '_'], '-')
        ->replaceMatches('/\s+/', '-')
        ->trim('-')
        ->toString();

    $tones = [
        // Success
        'active' => 'is-success',
        'approved' => 'is-success',
        'verified' => 'is-success',
        'paid' => 'is-success',
        'delivered' => 'is-success',
        'completed' => 'is-success',
        'resolved' => 'is-success',
        'picked-up' => 'is-success',

        // Warning
        'pending' => 'is-warning',
        'to-process' => 'is-warning',
        'to-prepare' => 'is-warning',
        'new-order' => 'is-warning',
        'low-stock' => 'is-warning',
        'processing' => 'is-warning',
        'scheduled' => 'is-warning',
        'requested' => 'is-warning',
        'awaiting-courier' => 'is-warning',

        // Info
        'ready-pickup' => 'is-info',
        'ready-for-pickup' => 'is-info',
        'ready-pickup-orders' => 'is-info',
        'in-transit' => 'is-info',
        'shipping' => 'is-info',
        'out-for-delivery' => 'is-info',
        'under-review' => 'is-info',
        'courier-assigned' => 'is-info',

        // Danger
        'cancelled' => 'is-danger',
        'rejected' => 'is-danger',
        'declined' => 'is-danger',
        'failed' => 'is-danger',
        'out-of-stock' => 'is-danger',
        'return-requested' => 'is-danger',
        'returns' => 'is-danger',
        'disputed' => 'is-danger',

        // Neutral
        'draft' => 'is-neutral',
        'archived' => 'is-neutral',
        'inactive' => 'is-neutral',
        'refunded' => 'is-neutral',
        'closed' => 'is-neutral',
    ];

    $tone = $tones[$key] ?? 'is-neutral';

    $legacyClass = match ($tone) {
        'is-success' => 'status-success',
        'is-warning' => 'status-warning',
        'is-info' => 'status-info',
        'is-danger' => 'status-danger',
        default => 'status-neutral',
    };
@endphp

<span
    {{ $attributes->merge([
        'class' => "status-badge sl-status {$tone} {$legacyClass}",
    ]) }}
>
    <span class="status-dot" aria-hidden="true"></span>

    {{ $label ?: 'Status' }}
</span>