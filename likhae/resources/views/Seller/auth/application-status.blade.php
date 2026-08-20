@extends('Seller.layouts.auth')
@section('title', 'Application Status — LIKHAE Seller')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
    <div class="auth-card max-w-none text-center">
        <div class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-[#EEF5FF] text-[#3977B8]">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
        </div>
        <p class="page-eyebrow mt-5">APPLICATION SUBMITTED</p>
        <h1 class="mt-2 text-3xl font-black">Under Review</h1>
        <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-[#6B6864]">Thank you for applying to become a LIKHAE Seller. Your account and submitted documents are being reviewed. The administrator’s decision will be sent to your registered email.</p>
        <div class="mt-6 grid gap-3 border-y border-[#E5E0D9] py-5 text-left sm:grid-cols-3">
            <div><span class="meta-label">REFERENCE</span><strong class="mt-1 block text-sm">SELL-20260820-0142</strong></div>
            <div><span class="meta-label">SUBMITTED</span><strong class="mt-1 block text-sm">Aug 20, 2026</strong></div>
            <div><span class="meta-label">STATUS</span><div class="mt-1"><x-seller.status-badge status="Under Review"/></div></div>
        </div>
        <div class="mt-6 flex flex-wrap justify-center gap-2">
            <a href="{{ url('/') }}" class="btn-secondary">Return to LIKHAE</a>
            <button class="btn-primary">Refresh Status</button>
        </div>
    </div>
</section>
@endsection
