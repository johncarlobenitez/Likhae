@props(['complaint'])

@php
    $status = strtolower(str_replace(' ', '-', data_get($complaint, 'status', 'open')));
    $priority = strtolower(data_get($complaint, 'priority', 'medium'));
@endphp

<article class="ad-complaint-card" data-filter-item data-search="{{ strtolower(implode(' ', array_filter([
    data_get($complaint, 'id'), data_get($complaint, 'title'), data_get($complaint, 'reported_by'), data_get($complaint, 'against'), $status
]))) }}">
    <div class="ad-complaint-head">
        <div><span class="ad-overline">{{ data_get($complaint, 'id', 'DSP-0000') }}</span><h3>{{ data_get($complaint, 'title', 'Complaint review') }}</h3></div>
        <span class="ad-priority is-{{ $priority }}">{{ ucfirst($priority) }}</span>
    </div>
    <p>{{ data_get($complaint, 'summary', 'Evidence and account activity require an administrator review.') }}</p>
    <dl class="ad-complaint-meta">
        <div><dt>Reported by</dt><dd>{{ data_get($complaint, 'reported_by', 'Buyer') }}</dd></div>
        <div><dt>Against</dt><dd>{{ data_get($complaint, 'against', 'Seller') }}</dd></div>
        <div><dt>Status</dt><dd><span class="ad-status is-{{ $status }}">{{ data_get($complaint, 'status', 'Open') }}</span></dd></div>
        <div><dt>Updated</dt><dd>{{ data_get($complaint, 'updated', '10 min ago') }}</dd></div>
    </dl>
    <div class="ad-card-actions">
        <button type="button" class="ad-btn ad-btn-secondary ad-btn-sm" data-demo-action="Opening case {{ data_get($complaint, 'id') }}">View evidence</button>
        <button type="button" class="ad-btn ad-btn-primary ad-btn-sm" data-demo-action="Case assigned to you">Assign to me</button>
    </div>
</article>
