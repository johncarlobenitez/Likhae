@extends('Seller.layouts.app')
@section('title', 'Security — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/account.css') @endpush

@section('content')
<x-seller.page-header eyebrow="ACCOUNT" title="Security" description="Manage your password and active seller sessions."/>

<div class="grid gap-5 xl:grid-cols-[1fr_420px]">
    <section class="panel p-5 sm:p-6">
        <p class="panel-eyebrow">CHANGE PASSWORD</p>
        <form class="mt-5 max-w-xl space-y-4">
            <div><label class="form-label">Current Password</label><input class="form-input" type="password"></div>
            <div><label class="form-label">New Password</label><input class="form-input" type="password"><div class="password-meter mt-2"><span style="width:75%"></span></div><p class="mt-2 text-[10px] text-[#079B72]">Good password strength</p></div>
            <div><label class="form-label">Confirm New Password</label><input class="form-input" type="password"></div>
            <button class="btn-primary">Update Password</button>
        </form>
    </section>

    <aside class="panel p-5"><p class="panel-eyebrow">SECURITY REMINDER</p><p class="mt-2 text-sm leading-6 text-[#6B6864]">LIKHAE will never ask for your password or verification code through marketplace chat.</p></aside>
</div>

<section class="panel mt-5">
    <div class="panel-header"><div><p class="panel-eyebrow">SESSIONS</p><h2>Active Devices</h2></div><button class="btn-danger">Sign Out Other Devices</button></div>
    @foreach([
        ['Windows PC','Chrome · Cebu City, Cebu','Active Now',true],
        ['Android Phone','Chrome Mobile · Cebu City, Cebu','2 days ago',false],
    ] as $s)
    <div class="session-row"><div class="verification-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="13"/><path d="M8 21h8M12 17v4"/></svg></div><div class="flex-1"><strong>{{ $s[0] }}</strong><span>{{ $s[1] }}</span></div><div class="text-right"><strong class="{{ $s[3]?'text-[#079B72]':'' }}">{{ $s[2] }}</strong>@if(!$s[3])<button class="text-link block">Sign Out</button>@endif</div></div>
    @endforeach
</section>
@endsection
