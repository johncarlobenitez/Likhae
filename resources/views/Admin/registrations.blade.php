@extends('layouts.admin')

@section('title', 'Registration Management')
@section('subtitle', 'Review buyer, seller, logistics, and rider applications.')
@section('active', 'registrations')

@php
    $type = $type ?? request('type', 'buyers');

    $stats = array_merge([
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
    ], $stats ?? []);

    $counts = array_merge([
        'buyers' => 0,
        'sellers' => 0,
        'logistics' => 0,
        'riders' => 0,
    ], $counts ?? []);

    $applications = $applications ?? collect();

    $documentLabels = [
        'valid_id' => 'ID',
        'business_permit' => 'Permit',
        'or_cr' => 'OR / CR',
        'drivers_license' => "Driver's License",
    ];
@endphp

@push('head')
<style>
    :root {
        --ad-photo-bg: #FBF7F2;
        --ad-photo-bg-soft: #F6EFE7;
        --ad-photo-bg-alt: #EFE7DE;
        --ad-photo-card: #FFFDF9;

        --ad-photo-border: #EADCCC;
        --ad-photo-border-strong: #DBCEC1;

        --ad-photo-maroon: #561C17;
        --ad-photo-maroon-2: #642920;
        --ad-photo-maroon-dark: #3E130F;

        --ad-photo-text: #3B211B;
        --ad-photo-brown: #6C4936;
        --ad-photo-muted: #987865;
        --ad-photo-muted-2: #A99386;

        --ad-photo-tan: #C19771;

        --ad-photo-success: #256F4A;
        --ad-photo-success-soft: #EAF7EF;
        --ad-photo-warning: #9A5B11;
        --ad-photo-warning-soft: #FFF6DE;
        --ad-photo-danger: #B42318;
        --ad-photo-danger-soft: #FCEBE9;

        --ad-photo-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --ad-photo-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ad-registration-page {
        color: var(--ad-photo-text);
    }

    .ad-registration-page .ad-page-head {
        padding: 32px 36px;

        border: 1px solid var(--ad-photo-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--ad-photo-shadow-soft);
    }

    .ad-registration-page .ad-overline {
        color: var(--ad-photo-maroon) !important;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
    }

    .ad-registration-page .ad-page-head h2 {
        margin-top: 10px;

        color: var(--ad-photo-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-registration-page .ad-page-head p {
        max-width: 620px;
        margin-top: 13px;

        color: var(--ad-photo-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .ad-alert {
        padding: 14px 16px;

        border: 1px solid var(--ad-photo-border);
        border-radius: 16px;

        background: var(--ad-photo-card);
        color: var(--ad-photo-text);

        box-shadow: var(--ad-photo-shadow-soft);

        font-size: 12px;
        line-height: 1.6;
    }

    .ad-alert p {
        margin: 0;
    }

    .ad-alert p + p {
        margin-top: 4px;
    }

    .ad-alert--success {
        border-color: #CFE8DA;
        background: var(--ad-photo-success-soft);
        color: var(--ad-photo-success);
    }

    .ad-alert--error {
        border-color: #F0C9C4;
        background: var(--ad-photo-danger-soft);
        color: var(--ad-photo-danger);
    }

    .ad-registration-page .ad-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .ad-registration-page .ad-mini-stat {
        padding: 18px;

        border: 1px solid var(--ad-photo-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        box-shadow: var(--ad-photo-shadow-soft);
    }

    .ad-registration-page .ad-mini-stat span {
        display: block;

        color: var(--ad-photo-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .ad-registration-page .ad-mini-stat strong {
        display: block;
        margin-top: 8px;

        color: var(--ad-photo-maroon);

        font-size: 32px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-registration-page .ad-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 6px;

        border: 1px solid var(--ad-photo-border);
        border-radius: 16px;

        background: var(--ad-photo-card);
        box-shadow: var(--ad-photo-shadow-soft);
    }

    .ad-registration-page .ad-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;

        min-height: 40px;
        padding: 0 16px;

        border-radius: 12px;

        color: var(--ad-photo-brown);

        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;

        transition: 160ms ease;
    }

    .ad-registration-page .ad-tab:hover {
        background: var(--ad-photo-bg-soft);
        color: var(--ad-photo-maroon);
    }

    .ad-registration-page .ad-tab.is-active {
        background: var(--ad-photo-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .ad-registration-page .ad-tab b {
        display: inline-flex;
        min-width: 22px;
        height: 22px;
        align-items: center;
        justify-content: center;

        padding: 0 7px;

        border-radius: 999px;

        background: rgba(255, 255, 255, 0.72);
        color: var(--ad-photo-maroon);

        font-size: 10px;
        font-weight: 950;
    }

    .ad-registration-page .ad-tab.is-active b {
        background: rgba(255, 255, 255, 0.18);
        color: #FFFFFF;
    }

    .ad-registration-card {
        border: 1px solid var(--ad-photo-border) !important;
        border-radius: 24px !important;

        background: var(--ad-photo-card) !important;
        box-shadow: var(--ad-photo-shadow-soft) !important;
    }

    .ad-registration-card .ad-card-body {
        padding: 16px;
    }

    .ad-registration-list {
        display: grid;
        gap: 14px;
    }

    .ad-registration-item {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) minmax(360px, 1.25fr) minmax(240px, auto);
        align-items: center;
        gap: 18px;

        padding: 18px;

        border: 1px solid var(--ad-photo-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 96% 4%, rgba(193, 151, 113, 0.12), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        transition:
            transform 160ms ease,
            border-color 160ms ease,
            box-shadow 160ms ease;
    }

    .ad-registration-item:hover {
        transform: translateY(-2px);
        border-color: var(--ad-photo-tan);
        box-shadow: var(--ad-photo-shadow-card);
    }

    .ad-registration-main {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 12px;
    }

    .ad-registration-avatar {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;

        border-radius: 14px;

        background: var(--ad-photo-maroon);
        color: #FFFFFF;

        font-size: 14px;
        font-weight: 950;
    }

    .ad-registration-copy {
        min-width: 0;
    }

    .ad-registration-title {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ad-registration-title strong {
        color: var(--ad-photo-text);
        font-size: 13px;
        font-weight: 950;
    }

    .ad-registration-copy p {
        margin: 5px 0 0;

        color: var(--ad-photo-brown);

        font-size: 11px;
        line-height: 1.5;
    }

    .ad-registration-copy small {
        display: block;
        margin-top: 4px;

        color: var(--ad-photo-muted);

        font-size: 10px;
        line-height: 1.5;
    }

    .ad-registration-page .ad-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 24px;
        padding: 0 9px;

        border: 1px solid transparent;
        border-radius: 999px;

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .ad-registration-page .ad-status.is-pending {
        background: var(--ad-photo-warning-soft);
        border-color: #EAD39A;
        color: var(--ad-photo-warning);
    }

    .ad-registration-page .ad-status.is-approved {
        background: var(--ad-photo-success-soft);
        border-color: #CFE8DA;
        color: var(--ad-photo-success);
    }

    .ad-registration-page .ad-status.is-rejected {
        background: var(--ad-photo-danger-soft);
        border-color: #F0C9C4;
        color: var(--ad-photo-danger);
    }

    .ad-registration-meta {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;

        margin: 0;
    }

    .ad-registration-meta div {
        min-width: 0;
        padding: 10px;

        border: 1px solid var(--ad-photo-border);
        border-radius: 13px;

        background: rgba(246, 239, 231, 0.65);
    }

    .ad-registration-meta dt {
        color: var(--ad-photo-muted);

        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .ad-registration-meta dd {
        overflow: hidden;
        margin: 4px 0 0;

        color: var(--ad-photo-text);

        font-size: 11px;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ad-registration-actions {
        display: grid;
        align-content: center;
        justify-items: end;
        gap: 10px;
    }

    .ad-registration-docs {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 7px;
    }

    .ad-registration-review-note {
        margin: 0;

        color: var(--ad-photo-muted);

        font-size: 10px;
        line-height: 1.55;
        text-align: right;
    }

    .ad-registration-review-note strong {
        color: var(--ad-photo-text);
    }

    .ad-registration-reject-form {
        display: grid;
        gap: 8px;
        width: min(260px, 100%);
    }

    .ad-registration-reject-form .ad-field {
        gap: 5px;
    }

    .ad-registration-reject-form .ad-field span {
        color: var(--ad-photo-muted);
        font-size: 9px;
        font-weight: 800;
    }

    .ad-registration-reject-form textarea {
        min-height: 58px;

        border: 1px solid var(--ad-photo-border);
        border-radius: 12px;

        background: #FFFFFF;
        color: var(--ad-photo-text);

        font-size: 11px;
        resize: vertical;
    }

    .ad-registration-reject-form textarea:focus {
        border-color: var(--ad-photo-tan);
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-registration-approval-actions {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ad-registration-page .ad-btn-primary {
        background: var(--ad-photo-maroon) !important;
        border-color: var(--ad-photo-maroon) !important;
        color: #FFFFFF !important;
    }

    .ad-registration-page .ad-btn-primary:hover {
        background: var(--ad-photo-maroon-dark) !important;
        border-color: var(--ad-photo-maroon-dark) !important;
    }

    .ad-registration-page .ad-btn-secondary {
        background: var(--ad-photo-card) !important;
        border-color: var(--ad-photo-tan) !important;
        color: var(--ad-photo-maroon) !important;
    }

    .ad-registration-page .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--ad-photo-maroon) !important;
    }

    .ad-registration-page .ad-btn-danger-soft {
        background: var(--ad-photo-danger-soft) !important;
        border-color: #F0C9C4 !important;
        color: var(--ad-photo-danger) !important;
    }

    .ad-registration-page .ad-btn-danger-soft:hover {
        background: #F7DCD7 !important;
        border-color: var(--ad-photo-danger) !important;
    }

    .ad-registration-empty {
        padding: 54px 24px;

        color: var(--ad-photo-muted);

        font-size: 13px;
        font-weight: 700;
        text-align: center;
    }

    .ad-registration-pagination {
        margin-top: 4px;
    }

    @media (max-width: 1180px) {
        .ad-registration-item {
            grid-template-columns: 1fr;
            align-items: start;
        }

        .ad-registration-actions {
            justify-items: start;
        }

        .ad-registration-docs,
        .ad-registration-approval-actions {
            justify-content: flex-start;
        }

        .ad-registration-review-note {
            text-align: left;
        }

        .ad-registration-reject-form {
            width: 100%;
        }
    }

    @media (max-width: 760px) {
        .ad-registration-page .ad-page-head {
            padding: 26px 22px;
        }

        .ad-registration-page .ad-summary-grid,
        .ad-registration-meta {
            grid-template-columns: 1fr;
        }

        .ad-registration-item {
            padding: 15px;
        }

        .ad-registration-main {
            align-items: flex-start;
        }

        .ad-registration-approval-actions,
        .ad-registration-docs {
            width: 100%;
        }

        .ad-registration-page .ad-btn,
        .ad-registration-approval-actions form {
            width: 100%;
        }
    }

    html.dark .ad-registration-page .ad-page-head,
    html.dark .ad-registration-card,
    html.dark .ad-registration-item,
    html.dark .ad-registration-page .ad-mini-stat,
    html.dark .ad-registration-page .ad-tabs {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-registration-page .ad-page-head h2,
    html.dark .ad-registration-title strong,
    html.dark .ad-registration-meta dd,
    html.dark .ad-registration-page .ad-mini-stat strong {
        color: #F5EFE8 !important;
    }

    html.dark .ad-registration-page .ad-page-head p,
    html.dark .ad-registration-copy p,
    html.dark .ad-registration-copy small,
    html.dark .ad-registration-meta dt,
    html.dark .ad-registration-review-note {
        color: #C8B7AD !important;
    }

    html.dark .ad-registration-page .ad-overline,
    html.dark .ad-registration-page .ad-mini-stat strong {
        color: #EBA99D !important;
    }

    html.dark .ad-registration-meta div,
    html.dark .ad-registration-reject-form textarea {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-registration-page .ad-tab {
        color: #C8B7AD !important;
    }

    html.dark .ad-registration-page .ad-tab:hover {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-registration-page .ad-tab.is-active {
        background: #8A3A2F !important;
        color: #FFFFFF !important;
    }
</style>
@endpush

@section('content')
<div class="ad-page ad-registration-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">
                Onboarding governance
            </span>

            <h2>
                Application review queue
            </h2>

            <p>
                Verify identity, business records, courier details, and rider documents before granting platform access.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="ad-alert ad-alert--success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div role="alert" class="ad-alert ad-alert--error">
            @foreach($errors->all() as $error)
                <p>
                    {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <section class="ad-summary-grid" aria-label="Application summary">
        <div class="ad-mini-stat">
            <span>Pending review</span>
            <strong>{{ $stats['pending'] }}</strong>
        </div>

        <div class="ad-mini-stat">
            <span>Approved accounts</span>
            <strong>{{ $stats['approved'] }}</strong>
        </div>

        <div class="ad-mini-stat">
            <span>Rejected</span>
            <strong>{{ $stats['rejected'] }}</strong>
        </div>
    </section>

    <nav class="ad-tabs" role="navigation" aria-label="Application type">
        <a
            class="ad-tab {{ $type === 'buyers' ? 'is-active' : '' }}"
            href="{{ route('admin.registrations', ['type' => 'buyers']) }}"
        >
            Buyers <b>{{ $counts['buyers'] }}</b>
        </a>

        <a
            class="ad-tab {{ $type === 'sellers' ? 'is-active' : '' }}"
            href="{{ route('admin.registrations', ['type' => 'sellers']) }}"
        >
            Sellers <b>{{ $counts['sellers'] }}</b>
        </a>

        <a
            class="ad-tab {{ $type === 'logistics' ? 'is-active' : '' }}"
            href="{{ route('admin.registrations', ['type' => 'logistics']) }}"
        >
            Logistics <b>{{ $counts['logistics'] }}</b>
        </a>

        <a
            class="ad-tab {{ $type === 'riders' ? 'is-active' : '' }}"
            href="{{ route('admin.registrations', ['type' => 'riders']) }}"
        >
            Riders <b>{{ $counts['riders'] }}</b>
        </a>
    </nav>

    <section class="ad-card ad-registration-card" id="applications-list">
        <div class="ad-card-body ad-registration-list">
            @forelse($applications as $user)
                @php
                    $statusSlug = \Illuminate\Support\Str::slug($user->status ?? 'pending');

                    $role = $user->role ?? 'buyer';

                    $location = collect([
                        $user->municipality ?? null,
                        $user->province ?? null,
                    ])->filter()->implode(', ');

                    $initial = mb_strtoupper(
                        mb_substr($user->name ?? 'U', 0, 1)
                    );
                @endphp

                <article class="ad-registration-item" data-filter-item>
                    <div class="ad-registration-main">
                        <span class="ad-registration-avatar">
                            {{ $initial }}
                        </span>

                        <div class="ad-registration-copy">
                            <div class="ad-registration-title">
                                <strong>
                                    {{ $user->name }}
                                </strong>

                                <span class="ad-status is-{{ $statusSlug }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </div>

                            <p>
                                {{ $user->email }} · {{ ucfirst($role) }} application
                            </p>

                            <small>
                                Registered {{ $user->created_at?->diffForHumans() ?? 'recently' }}

                                @if($location)
                                    · {{ $location }}
                                @endif

                                @if($user->business_name)
                                    · {{ $user->business_name }}
                                @endif
                            </small>
                        </div>
                    </div>

                    <dl class="ad-registration-meta">
                        <div>
                            <dt>Contact</dt>
                            <dd>{{ $user->contact_number ?? '—' }}</dd>
                        </div>

                        <div>
                            <dt>Birthday</dt>
                            <dd>
                                {{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('M d, Y') : '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt>Valid ID</dt>
                            <dd>{{ $user->valid_id_path ? 'Uploaded' : 'None' }}</dd>
                        </div>

                        @if(in_array($role, ['seller', 'logistics'], true))
                            <div>
                                <dt>Business</dt>
                                <dd>{{ $user->business_name ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt>Line</dt>
                                <dd>{{ $user->line_of_business ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt>Permit</dt>
                                <dd>{{ $user->business_permit_path ? 'Uploaded' : 'None' }}</dd>
                            </div>
                        @endif

                        @if(in_array($role, ['courier', 'rider'], true))
                            <div>
                                <dt>Vehicle</dt>
                                <dd>{{ $user->vehicle_type ?? '—' }}</dd>
                            </div>

                            <div>
                                <dt>Plate</dt>
                                <dd>{{ $user->plate_number ?? '—' }}</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="ad-registration-actions">
                        <div class="ad-registration-docs">
                            @foreach($documentLabels as $document => $label)
                                @if($user->getAttribute($document . '_path'))
                                    <a
                                        href="{{ route('admin.registrations.document', [$user, $document]) }}"
                                        class="ad-btn ad-btn-secondary ad-btn-sm"
                                    >
                                        Download {{ $label }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        @if($user->reviewed_at)
                            <p class="ad-registration-review-note">
                                Reviewed by
                                <strong>{{ $user->reviewer?->name ?? 'Administrator' }}</strong>
                                on {{ $user->reviewed_at->format('M d, Y H:i') }}.

                                @if($user->rejection_reason)
                                    <br>
                                    Reason: {{ $user->rejection_reason }}
                                @endif
                            </p>
                        @endif

                        @if($user->status === 'pending')
                            <div class="ad-registration-approval-actions">
                                <form
                                    method="POST"
                                    action="{{ route('admin.registrations.approve', $user) }}"
                                >
                                    @csrf

                                    <button
                                        class="ad-btn ad-btn-primary ad-btn-sm"
                                        type="submit"
                                    >
                                        Approve
                                    </button>
                                </form>

                                <form
                                    method="POST"
                                    action="{{ route('admin.registrations.reject', $user) }}"
                                    class="ad-registration-reject-form"
                                >
                                    @csrf

                                    <label
                                        class="ad-field"
                                        for="reason-{{ $user->id }}"
                                    >
                                        <span>Reason for rejection</span>

                                        <textarea
                                            id="reason-{{ $user->id }}"
                                            name="rejection_reason"
                                            required
                                            maxlength="2000"
                                            rows="2"
                                            placeholder="Explain why this application is rejected..."
                                        ></textarea>
                                    </label>

                                    <button
                                        class="ad-btn ad-btn-danger-soft ad-btn-sm"
                                        type="submit"
                                        onclick="return confirm('Reject this application?')"
                                    >
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="ad-registration-empty">
                    No {{ rtrim($type, 's') }} applications found.
                </div>
            @endforelse
        </div>
    </section>

    @if(method_exists($applications, 'links'))
        <div class="ad-registration-pagination">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection