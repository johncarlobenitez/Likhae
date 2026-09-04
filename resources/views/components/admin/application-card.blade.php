@props(['application'])

@php
    $status = strtolower(data_get($application, 'status', 'pending'));
    $type = data_get($application, 'type', 'Seller');
    $name = data_get($application, 'name', 'Applicant');
    $id = data_get($application, 'id', 'APP-0000');
@endphp

<article class="ad-application-card" data-filter-item data-search="{{ strtolower($id.' '.$name.' '.$type.' '.$status) }}">
    <div class="ad-application-main">
        <span class="ad-avatar is-soft">{{ mb_strtoupper(mb_substr($name, 0, 1)) }}</span>
        <div>
            <div class="ad-inline-title"><strong>{{ $name }}</strong><span class="ad-status is-{{ $status }}">{{ ucfirst($status) }}</span></div>
            <p>{{ $id }} · {{ $type }} application</p>
            <small>{{ data_get($application, 'detail', 'Identity and contact documents submitted') }}</small>
        </div>
    </div>
    <dl class="ad-application-meta">
        <div><dt>Submitted</dt><dd>{{ data_get($application, 'submitted', 'Today, 9:30 AM') }}</dd></div>
        <div><dt>Documents</dt><dd>{{ data_get($application, 'documents', '4 of 4') }}</dd></div>
        <div><dt>Risk check</dt><dd>{{ data_get($application, 'risk', 'Clear') }}</dd></div>
    </dl>
    <div class="ad-card-actions">
        <button class="ad-btn ad-btn-secondary ad-btn-sm" type="button" data-demo-action="Opening {{ $id }} details">Review</button>
        @if($status === 'pending')
            <button class="ad-btn ad-btn-primary ad-btn-sm" type="button" data-demo-action="{{ $id }} approved">Approve</button>
            <button class="ad-btn ad-btn-danger-soft ad-btn-sm" type="button" data-confirm-action data-confirm-title="Reject application?" data-confirm-message="Reject {{ $name }}'s application and record the reason." data-require-reason="true" data-success-message="{{ $id }} rejected">Reject</button>
        @endif
    </div>
</article>
