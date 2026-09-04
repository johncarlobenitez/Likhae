@extends('layouts.admin')

@section('title', 'Account')
@section('subtitle', 'Manage your administrator profile, security, and notification preferences.')
@section('active', 'account')

@section('content')
@php
    $tab = request('tab', 'profile');
    $tabs = ['profile' => 'Profile', 'security' => 'Security', 'notifications' => 'Notification Settings'];
    $admin = auth()->user();
@endphp
<div class="ad-page">
    <div class="ad-page-head"><div><span class="ad-overline">Administrator account</span><h2>{{ $tabs[$tab] ?? 'Profile' }}</h2><p>Protect privileged access and keep administrative contact information current.</p></div></div>
    <section class="ad-settings-layout"><nav class="ad-card ad-settings-nav">@foreach($tabs as $key => $label)<a class="{{ $tab === $key ? 'is-active' : '' }}" href="{{ route('admin.account', ['tab' => $key]) }}">{{ $label }}</a>@endforeach</nav><div class="ad-card">
        @if($tab === 'security')
            <form class="ad-settings-section" data-demo-form data-success-message="Security settings updated"><h2>Security</h2><p>Use a unique password and enable additional account protection.</p><div class="ad-form-grid"><label class="ad-field is-full"><span>Current password</span><input type="password" placeholder="Enter current password"></label><label class="ad-field"><span>New password</span><input type="password" placeholder="At least 12 characters"></label><label class="ad-field"><span>Confirm new password</span><input type="password" placeholder="Repeat new password"></label></div><div class="ad-setting-row"><div><strong>Two-factor authentication</strong><p>Require a verification code when signing in.</p></div><label class="ad-switch"><input type="checkbox" checked><span></span></label></div><div class="ad-setting-row"><div><strong>Sign-in alerts</strong><p>Notify you about new devices and unusual sessions.</p></div><label class="ad-switch"><input type="checkbox" checked><span></span></label></div><div class="ad-inline-actions"><button class="ad-btn ad-btn-primary" type="submit">Update security</button><button class="ad-btn ad-btn-danger-soft" type="button" data-confirm-action data-confirm-title="Sign out other sessions?" data-confirm-message="All sessions except this browser will be revoked." data-success-message="Other sessions signed out">Sign out other sessions</button></div></form>
        @elseif($tab === 'notifications')
            <form class="ad-settings-section" data-demo-form data-success-message="Notification preferences saved"><h2>Notification settings</h2><p>Choose the operational events that should interrupt your work.</p>@foreach([['High-risk product flags','Immediate alerts for suspected prohibited listings.',true],['Urgent disputes','Cases with critical priority or expiring response deadlines.',true],['Registration backlog','Alert when pending applications exceed the service target.',true],['Delivery exceptions','Failed or materially delayed marketplace deliveries.',true],['Finance settlement failures','Payment and seller-settlement errors.',true],['Weekly platform digest','Consolidated performance and risk summary.',false]] as [$name,$description,$checked])<div class="ad-setting-row"><div><strong>{{ $name }}</strong><p>{{ $description }}</p></div><label class="ad-switch"><input type="checkbox" @checked($checked)><span></span></label></div>@endforeach<button class="ad-btn ad-btn-primary" type="submit">Save preferences</button></form>
        @else
            <form class="ad-settings-section" data-demo-form data-success-message="Administrator profile saved"><h2>Profile information</h2><p>This information is visible in audit trails and internal assignments.</p><div class="ad-form-grid"><label class="ad-field"><span>Full name</span><input value="{{ data_get($admin, 'name', 'Admin User') }}"></label><label class="ad-field"><span>Email address</span><input type="email" value="{{ data_get($admin, 'email', 'admin@likhae.ph') }}"></label><label class="ad-field"><span>Role</span><input value="Platform Administrator" readonly></label><label class="ad-field"><span>Contact number</span><input value="+63 917 000 0000"></label><label class="ad-field is-full"><span>Administrative note</span><textarea rows="4">Marketplace operations and trust & safety.</textarea></label></div><button class="ad-btn ad-btn-primary" type="submit">Save profile</button></form>
        @endif
    </div></section>
</div>
@endsection
