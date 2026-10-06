@extends('layouts.admin')

@section('title', 'Account Details')
@section('subtitle', 'Review registration details and manage account access.')
@section('active', 'users')

@section('content')
@php
    $managedAccount = $user->isAccountType(\App\Models\User::TYPE_BUYER, \App\Models\User::TYPE_SELLER, \App\Models\User::TYPE_LOGISTICS);
    $address = $user->addresses->firstWhere('is_default', true) ?? $user->addresses->first();
    $shop = $user->sellerProfile;
    $provider = $user->logisticsCenter;
    $status = strtolower($user->status);
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
        <header class="ad-card-head"><h2>Account information</h2><span class="ad-status is-{{ $status }}">{{ $status === 'pending' ? 'Pending approval' : ucfirst($status) }}</span></header>
        <div class="ad-card-body">
            <dl class="ad-application-meta">
                <div><dt>Role</dt><dd>{{ ucfirst(strtolower($user->account_type)) }}</dd></div>
                <div><dt>Registered</dt><dd>{{ $user->created_at->format('M d, Y H:i') }}</dd></div>
                <div><dt>Contact number</dt><dd>{{ $user->contact_number ?: 'Not provided' }}</dd></div>
                <div><dt>Birthday</dt><dd>{{ $user->birthday?->format('M d, Y') ?? 'Not provided' }}</dd></div>
                <div><dt>Sex</dt><dd>{{ $user->sex ? ucfirst(str_replace('_', ' ', $user->sex)) : 'Not provided' }}</dd></div>
                <div><dt>Street address</dt><dd>{{ trim(($address?->house_number ?? '').' '.($address?->street_address ?? '')) ?: 'Not provided' }}</dd></div>
                <div><dt>Barangay</dt><dd>{{ $address?->barangay_name ?: 'Not provided' }}</dd></div>
                <div><dt>Municipality / City</dt><dd>{{ $address?->municipality_name ?: 'Not provided' }}</dd></div>
                <div><dt>Province</dt><dd>{{ $address?->province_name ?: 'Not provided' }}</dd></div>
                <div><dt>Postal code</dt><dd>{{ $address?->postal_code ?: 'Not provided' }}</dd></div>
                <div><dt>Landmark</dt><dd>{{ $address?->landmark ?: 'Not provided' }}</dd></div>
            </dl>
            @if($user->isAccountType(\App\Models\User::TYPE_SELLER, \App\Models\User::TYPE_LOGISTICS))
                <h3>Business details</h3>
                <dl class="ad-application-meta">
                    <div><dt>Business name</dt><dd>{{ $shop?->business_name ?? $provider?->business_name ?? 'Not provided' }}</dd></div>
                    <div><dt>Registration number</dt><dd>{{ $shop?->business_registration_number ?? $provider?->business_registration_number ?? 'Not provided' }}</dd></div>
                    @if($provider)<div><dt>DTI number</dt><dd>{{ $provider->dti_registration_number ?: 'Not provided' }}</dd></div>@endif
                </dl>
            @endif
        </div>
    </section>

    <section class="ad-card" id="account-access">
        <header class="ad-card-head"><h2>Account access</h2></header>
        <div class="ad-card-body">
            @if($managedAccount && in_array($status, ['active', 'suspended'], true))
                @php($suspended = $status === 'suspended')
                <p>{{ $suspended ? 'Reactivate this account to let the user sign in again.' : 'Suspending this account blocks sign-in and access to its workspace.' }}</p>
                <form method="POST" action="{{ route($suspended ? 'admin.users.reactivate' : 'admin.users.suspend', $user) }}" onsubmit="return confirm('Apply this account access change?')">
                    @csrf
                    <label class="ad-field">
                        <span>{{ $suspended ? 'Reason for reactivation' : 'Reason for suspension' }}</span>
                        <textarea name="reason" rows="3" required maxlength="2000" placeholder="Explain why this account's access is changing.">{{ old('reason') }}</textarea>
                    </label>
                    <button class="ad-btn {{ $suspended ? 'ad-btn-primary' : 'ad-btn-danger-soft' }}" type="submit">{{ $suspended ? 'Reactivate account' : 'Suspend account' }}</button>
                </form>
            @elseif($user->isAccountType(\App\Models\User::TYPE_ADMIN))
                <p>Administrator access cannot be changed from the user directory.</p>
            @else
                <p>This account has not been approved. Access changes are available after registration approval.</p>
            @endif
        </div>
    </section>

    <section class="ad-card">
        <header class="ad-card-head"><h2>Access history</h2><p>Recorded account changes.</p></header>
        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead><tr><th>Date</th><th>Change</th><th>Administrator</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($changes as $change)
                        <tr><td>{{ $change->created_at->format('M d, Y H:i') }}</td><td>{{ str($change->event)->replace('.', ' ')->headline() }}</td><td>{{ $change->actor?->name ?? 'System' }}</td><td>{{ data_get($change->old_values, 'status', '—') }} → {{ data_get($change->new_values, 'status', '—') }}</td></tr>
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
