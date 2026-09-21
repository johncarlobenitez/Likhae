@extends('layouts.admin')

@section('title', 'Account Details')
@section('subtitle', 'Review registration details and manage account access.')
@section('active', 'users')

@section('content')
@php
    $managedAccount = in_array($user->primary_role, \App\Models\User::MANAGED_ROLES, true);
    $address = $user->addresses->firstWhere('is_default', true) ?? $user->addresses->first();
    $shop = $user->sellers->first();
    $provider = $user->logisticsProvider;
    $rider = $user->rider;
@endphp
<div class="ad-page">
    <div class="ad-page-head">
        <div><span class="ad-overline">USR-{{ $user->id }}</span><h2>{{ $user->name }}</h2><p>{{ $user->email }}</p></div>
        <a class="ad-btn ad-btn-secondary" href="{{ route('admin.users') }}">Back to users</a>
    </div>
    @if(session('success'))<div class="ad-note" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="ad-note" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
    @endif

    <section class="ad-card">
        <header class="ad-card-head"><h2>Account information</h2><span class="ad-status is-{{ $user->status }}">{{ $user->status === 'pending' ? 'Pending approval' : ucfirst($user->status) }}</span></header>
        <div class="ad-card-body">
            <dl class="ad-application-meta">
                <div><dt>Role</dt><dd>{{ in_array($user->primary_role, ['rider', 'courier']) ? 'Rider' : ucfirst($user->primary_role) }}</dd></div>
                <div><dt>Registered</dt><dd>{{ $user->created_at->format('M d, Y H:i') }}</dd></div>
                <div><dt>Contact number</dt><dd>{{ $user->contact_number ?: 'Not provided' }}</dd></div>
                <div><dt>Birthday</dt><dd>{{ $user->birthday?->format('M d, Y') ?? 'Not provided' }}</dd></div>
                <div><dt>Sex</dt><dd>{{ $user->sex ? ucfirst(str_replace('_', ' ', $user->sex)) : 'Not provided' }}</dd></div>
                <div><dt>Street address</dt><dd>{{ $address?->line1 ?: 'Not provided' }}</dd></div>
                <div><dt>Barangay</dt><dd>{{ $address?->barangay ?: 'Not provided' }}</dd></div>
                <div><dt>Municipality / City</dt><dd>{{ $address?->city ?: 'Not provided' }}</dd></div>
                <div><dt>Province</dt><dd>{{ $address?->province ?: 'Not provided' }}</dd></div>
                <div><dt>Region</dt><dd>{{ $address?->region ?: 'Not provided' }}</dd></div>
                <div><dt>Postal code</dt><dd>{{ $address?->postal_code ?: 'Not provided' }}</dd></div>
                <div><dt>Landmark</dt><dd>{{ $address?->landmark ?: 'Not provided' }}</dd></div>
            </dl>
            @if(in_array($user->primary_role, ['seller', 'logistics']))
                <h3>Business details</h3>
                <dl class="ad-application-meta">
                    <div><dt>Business name</dt><dd>{{ $shop?->name ?? $provider?->name ?? 'Not provided' }}</dd></div>
                    <div><dt>Business type</dt><dd>{{ data_get($shop?->settings, 'business_type', 'Not provided') }}</dd></div>
                    <div><dt>DTI / SEC number</dt><dd>{{ data_get($shop?->settings, 'dti_sec_number', 'Not provided') }}</dd></div>
                    <div><dt>TIN</dt><dd>{{ data_get($shop?->settings, 'tin', 'Not provided') }}</dd></div>
                </dl>
            @endif
            @if(in_array($user->primary_role, ['courier', 'rider']))
                <h3>Vehicle details</h3>
                <dl class="ad-application-meta">
                    <div><dt>Vehicle type</dt><dd>{{ $rider?->vehicle_type ? ucfirst($rider->vehicle_type) : 'Not provided' }}</dd></div>
                    <div><dt>Plate number</dt><dd>{{ $rider?->plate_no ?: 'Not provided' }}</dd></div>
                </dl>
            @endif
        </div>
    </section>

    <section class="ad-card" id="account-access">
        <header class="ad-card-head"><h2>Account access</h2></header>
        <div class="ad-card-body">
            @if($managedAccount && in_array($user->status, ['active', 'suspended']))
                @php($suspended = $user->status === 'suspended')
                <p>{{ $suspended ? 'Reactivate this account to let the user sign in again.' : 'Suspending this account blocks sign-in and access to its workspace.' }}</p>
                <form method="POST" action="{{ route($suspended ? 'admin.users.reactivate' : 'admin.users.suspend', $user) }}" onsubmit="return confirm('Apply this account access change?')">
                    @csrf
                    <label class="ad-field">
                        <span>{{ $suspended ? 'Reason for reactivation' : 'Reason for suspension' }}</span>
                        <textarea name="reason" rows="3" required maxlength="2000" placeholder="Explain why this account's access is changing.">{{ old('reason') }}</textarea>
                    </label>
                    <button class="ad-btn {{ $suspended ? 'ad-btn-primary' : 'ad-btn-danger-soft' }}" type="submit">{{ $suspended ? 'Reactivate account' : 'Suspend account' }}</button>
                </form>
            @elseif($user->primary_role === 'admin')
                <p>Administrator access cannot be changed from the user directory.</p>
            @else
                <p>This account has not been approved. Access changes are available after registration approval.</p>
            @endif
        </div>
    </section>

    <section class="ad-card">
        <header class="ad-card-head"><h2>Access history</h2><p>Reasons and administrators recorded for each access change.</p></header>
        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead><tr><th>Date</th><th>Change</th><th>Administrator</th><th>Reason</th></tr></thead>
                <tbody>
                    @forelse($changes as $change)
                        <tr><td>{{ $change->created_at->format('M d, Y H:i') }}</td><td>{{ ucfirst(data_get($change->metadata, 'before.status')) }} to {{ ucfirst(data_get($change->metadata, 'after.status')) }}</td><td>{{ $change->actor?->name ?? 'Administrator' }}</td><td>{{ $change->description }}</td></tr>
                    @empty
                        <tr><td colspan="4">No access changes recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($changes->hasPages())<div class="ad-card-body">{{ $changes->links() }}</div>@endif
    </section>
</div>
@endsection
