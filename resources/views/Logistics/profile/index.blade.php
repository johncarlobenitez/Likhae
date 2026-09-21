@extends('logistics.app')

@section('title', 'Profile - LIKHAE Logistics')

@section('content')
@php
    $user = $accountUser ?? auth()->user();
    $initials = collect(explode(' ', $user?->name ?? 'Logistics'))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
@endphp

<div class="flex flex-col gap-6">
    <section>
        <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Account</span>
        <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink">Profile</h1>
        <p class="mt-2 max-w-[680px] text-[11px] leading-6 text-muted">This page shows the authenticated logistics account, not a sample profile.</p>
    </section>

    <section class="rounded-xl border border-line bg-surface p-5">
        <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <span class="grid h-16 w-16 place-items-center rounded-full bg-primary text-[18px] font-bold text-white">{{ $initials ?: 'L' }}</span>
                <div>
                    <h2 class="text-[16px] font-semibold text-ink">{{ $user?->name }}</h2>
                    <p class="mt-1 text-[10px] text-muted">{{ $user?->email }}</p>
                    <p class="mt-1 text-[9px] text-muted">{{ Str::headline($user?->primary_role ?? 'logistics') }} - {{ Str::headline($user?->status ?? 'pending') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-lg border border-line px-4 py-2 text-[10px] font-semibold text-ink">Log Out</button>
            </form>
        </div>
    </section>

    <section class="grid gap-5 lg:grid-cols-2">
        <article class="rounded-xl border border-line bg-surface p-5">
            <h2 class="text-[13px] font-semibold text-ink">Registration Details</h2>
            <dl class="mt-4 grid gap-3 text-[10px]">
                <div><dt class="text-muted">Contact No.</dt><dd class="mt-1 font-semibold text-ink">{{ $provider?->contact_phone ?: $user?->contact_number ?: 'Not provided' }}</dd></div>
                <div><dt class="text-muted">Business Name</dt><dd class="mt-1 font-semibold text-ink">{{ $provider?->name ?: 'Not provided' }}</dd></div>
                <div><dt class="text-muted">Address</dt><dd class="mt-1 font-semibold text-ink">{{ collect([$address?->line1, $address?->barangay, $address?->city, $address?->province, $address?->postal_code])->filter()->implode(', ') ?: 'Not provided' }}</dd></div>
            </dl>
        </article>

        <article class="rounded-xl border border-line bg-surface p-5">
            <h2 class="text-[13px] font-semibold text-ink">System Information</h2>
            <dl class="mt-4 grid gap-3 text-[10px]">
                <div><dt class="text-muted">Platform</dt><dd class="mt-1 font-semibold text-ink">LIKHAE Marketplace</dd></div>
                <div><dt class="text-muted">Module</dt><dd class="mt-1 font-semibold text-ink">Logistics</dd></div>
                <div><dt class="text-muted">Account Created</dt><dd class="mt-1 font-semibold text-ink">{{ $user?->created_at?->format('M d, Y g:i A') ?: 'Not recorded' }}</dd></div>
            </dl>
        </article>
    </section>
</div>
@endsection
