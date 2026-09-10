@extends('layouts.admin')

@section('title', 'Account Details')
@section('subtitle', 'Review registration details and manage account access.')
@section('active', 'users')

@section('content')
@php
    $applicationType = match ($user->role) {
        'seller' => 'sellers', 'logistics' => 'logistics', 'rider', 'courier' => 'riders', default => 'buyers',
    };
    $publicAccount = in_array($user->role, \App\Models\User::PUBLIC_ROLES, true);
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
                <div><dt>Role</dt><dd>{{ in_array($user->role, ['rider', 'courier']) ? 'Rider' : ucfirst($user->role) }}</dd></div>
                <div><dt>Registered</dt><dd>{{ $user->created_at->format('M d, Y H:i') }}</dd></div>
                <div><dt>Contact number</dt><dd>{{ $user->contact_number ?: 'Not provided' }}</dd></div>
                <div><dt>Birthday</dt><dd>{{ $user->birthday?->format('M d, Y') ?? 'Not provided' }}</dd></div>
                <div><dt>Sex</dt><dd>{{ $user->sex ? ucfirst(str_replace('_', ' ', $user->sex)) : 'Not provided' }}</dd></div>
                <div><dt>Street address</dt><dd>{{ trim($user->house_number.' '.$user->street) ?: 'Not provided' }}</dd></div>
                <div><dt>Barangay</dt><dd>{{ $user->barangay ?: 'Not provided' }}</dd></div>
                <div><dt>Municipality / City</dt><dd>{{ $user->municipality ?: 'Not provided' }}</dd></div>
                <div><dt>Province</dt><dd>{{ $user->province ?: 'Not provided' }}</dd></div>
                <div><dt>Region</dt><dd>{{ $user->region ?: 'Not provided' }}</dd></div>
                <div><dt>Postal code</dt><dd>{{ $user->postal_code ?: 'Not provided' }}</dd></div>
                <div><dt>Landmark</dt><dd>{{ $user->landmark ?: 'Not provided' }}</dd></div>
            </dl>
            @if(in_array($user->role, ['seller', 'logistics']))
                <h3>Business details</h3>
                <dl class="ad-application-meta">
                    @foreach(['business_name' => 'Business name', 'store_name' => 'Store name', 'line_of_business' => 'Line of business', 'business_type' => 'Business type', 'dti_sec_number' => 'DTI / SEC number', 'tin' => 'TIN'] as $field => $label)
                        <div><dt>{{ $label }}</dt><dd>{{ $user->getAttribute($field) ?: 'Not provided' }}</dd></div>
                    @endforeach
                </dl>
            @endif
            @if(in_array($user->role, ['courier', 'rider']))
                <h3>Vehicle details</h3>
                <dl class="ad-application-meta">
                    <div><dt>Vehicle type</dt><dd>{{ $user->vehicle_type ? ucfirst($user->vehicle_type) : 'Not provided' }}</dd></div>
                    <div><dt>Plate number</dt><dd>{{ $user->plate_number ?: 'Not provided' }}</dd></div>
                </dl>
            @endif
        </div>
    </section>

    @if($publicAccount)
        <section class="ad-card">
            <header class="ad-card-head"><h2>Registration review</h2></header>
            <div class="ad-card-body">
                @if($user->reviewed_at)
                    <p>Reviewed by {{ $user->reviewer?->name ?? 'Administrator' }} on {{ $user->reviewed_at->format('M d, Y H:i') }}.</p>
                @elseif($user->status === 'pending')
                    <p>This application is waiting for administrator approval.</p>
                @else
                    <p>No registration review details were recorded for this account.</p>
                @endif
                @if($user->rejection_reason)<p>Rejection reason: {{ $user->rejection_reason }}</p>@endif
                <div class="ad-inline-actions">
                    @foreach(['valid_id' => 'Valid ID', 'business_permit' => 'Business permit', 'or_cr' => 'Vehicle OR / CR', 'drivers_license' => "Driver's license"] as $document => $label)
                        @if($user->getAttribute($document.'_path'))<a class="ad-btn ad-btn-secondary ad-btn-sm" href="{{ route('admin.registrations.document', [$user, $document]) }}">Download {{ $label }}</a>@endif
                    @endforeach
                    @if($user->status === 'pending')<a class="ad-btn ad-btn-primary" href="{{ route('admin.registrations', ['type' => $applicationType]) }}">Review application</a>@endif
                </div>
            </div>
        </section>
    @endif

    <section class="ad-card" id="account-access">
        <header class="ad-card-head"><h2>Account access</h2></header>
        <div class="ad-card-body">
            @if($publicAccount && in_array($user->status, ['active', 'suspended']))
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
            @elseif($user->role === 'admin')
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
                        <tr><td>{{ $change->created_at->format('M d, Y H:i') }}</td><td>{{ ucfirst($change->previous_status) }} → {{ ucfirst($change->status) }}</td><td>{{ $change->administrator?->name ?? 'Administrator' }}</td><td>{{ $change->reason }}</td></tr>
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
