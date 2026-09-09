@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.admin')

@section('title', 'Registration Management')
@section('subtitle', 'Review buyer, seller, logistics, and rider applications.')
@section('active', 'registrations')

@section('content')
<div class="ad-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">Onboarding governance</span>
            <h2>Application review queue</h2>
            <p>Verify identity and business records before granting platform access.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="ad-alert ad-alert--success" style="margin-bottom:1rem;padding:.75rem 1rem;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:6px;">
            {{ session('success') }}
        </div>
    @endif

    <section class="ad-summary-grid">
        <div class="ad-mini-stat"><span>Pending review</span><strong>{{ $stats['pending'] }}</strong></div>
        <div class="ad-mini-stat"><span>Approved accounts</span><strong>{{ $stats['approved'] }}</strong></div>
        <div class="ad-mini-stat"><span>Rejected</span><strong>{{ $stats['rejected'] }}</strong></div>
    </section>

    <div class="ad-tabs" role="navigation" aria-label="Application type">
        <a class="ad-tab {{ $type === 'buyers' ? 'is-active' : '' }}" href="{{ route('admin.registrations', ['type' => 'buyers']) }}">
            Buyers <b>{{ $counts['buyers'] }}</b>
        </a>
        <a class="ad-tab {{ $type === 'sellers' ? 'is-active' : '' }}" href="{{ route('admin.registrations', ['type' => 'sellers']) }}">
            Sellers <b>{{ $counts['sellers'] }}</b>
        </a>
        <a class="ad-tab {{ $type === 'logistics' ? 'is-active' : '' }}" href="{{ route('admin.registrations', ['type' => 'logistics']) }}">
            Logistics <b>{{ $counts['logistics'] }}</b>
        </a>
        <a class="ad-tab {{ $type === 'riders' ? 'is-active' : '' }}" href="{{ route('admin.registrations', ['type' => 'riders']) }}">
            Riders <b>{{ $counts['riders'] }}</b>
        </a>
    </div>

    <section class="ad-card" id="applications-list">
        <div class="ad-card-body ad-card-list">
            @forelse($applications as $user)
                <article class="ad-application-card" data-filter-item>
                    <div class="ad-application-main">
                        <span class="ad-avatar is-soft">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                        <div>
                            <div class="ad-inline-title">
                                <strong>{{ $user->name }}</strong>
                                <span class="ad-status is-{{ $user->status }}">{{ ucfirst($user->status) }}</span>
                            </div>
                            <p>{{ $user->email }} · {{ ucfirst($user->role) }} application</p>
                            <small>
                                Registered {{ $user->created_at->diffForHumans() }}
                                @if($user->province) · {{ $user->municipality }}, {{ $user->province }} @endif
                                @if($user->business_name) · {{ $user->business_name }} @endif
                            </small>
                        </div>
                    </div>

                    <dl class="ad-application-meta">
                        <div><dt>Contact</dt><dd>{{ $user->contact_number ?? '—' }}</dd></div>
                        <div><dt>Birthday</dt><dd>{{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('M d, Y') : '—' }}</dd></div>
                        <div><dt>Valid ID</dt><dd>{{ $user->valid_id_path ? 'Uploaded' : 'None' }}</dd></div>
                        @if($user->role === 'seller')
                            <div><dt>Business</dt><dd>{{ $user->business_name ?? '—' }}</dd></div>
                            <div><dt>Line</dt><dd>{{ $user->line_of_business ?? '—' }}</dd></div>
                            <div><dt>Permit</dt><dd>{{ $user->business_permit_path ? 'Uploaded' : 'None' }}</dd></div>
                        @endif
                        @if($user->role === 'courier')
                            <div><dt>Vehicle</dt><dd>{{ $user->vehicle_type ?? '—' }}</dd></div>
                            <div><dt>Plate</dt><dd>{{ $user->plate_number ?? '—' }}</dd></div>
                        @endif
                    </dl>

                    @if($user->valid_id_path)
                        <div style="margin:.5rem 0;">
                            <a href="{{ Storage::url($user->valid_id_path) }}" target="_blank" class="ad-btn ad-btn-secondary ad-btn-sm">View ID</a>
                            @if($user->business_permit_path)
                                <a href="{{ Storage::url($user->business_permit_path) }}" target="_blank" class="ad-btn ad-btn-secondary ad-btn-sm">View Permit</a>
                            @endif
                        </div>
                    @endif

                    @if($user->status === 'pending')
                        <div class="ad-card-actions">
                            <form method="POST" action="{{ route('admin.registrations.approve', $user) }}" style="display:inline;">
                                @csrf
                                <button class="ad-btn ad-btn-primary ad-btn-sm" type="submit">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.registrations.reject', $user) }}" style="display:inline;">
                                @csrf
                                <button class="ad-btn ad-btn-danger-soft ad-btn-sm" type="submit"
                                    onclick="return confirm('Reject {{ addslashes($user->name) }}\'s application?')">
                                    Reject
                                </button>
                            </form>
                        </div>
                    @endif
                </article>
            @empty
                <div style="padding:2rem;text-align:center;color:#6b7280;">
                    No {{ rtrim($type, 's') }} applications found.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
