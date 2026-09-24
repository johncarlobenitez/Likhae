@extends('rider.app')

@section('title', 'Rider Profile - LIKHAE')

@php
    $user = $rider ?? auth()->user();
    $initials = collect(explode(' ', trim((string) ($user->name ?? 'Rider'))))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'R';

    $details = [
        ['Full Name', $user->name ?? 'Not recorded'],
        ['Email', $user->email ?? 'Not recorded'],
        ['Contact Number', $user->phone ?? $user->contact_number ?? 'Not recorded'],
        ['Birthday', filled($user->birthday ?? null) ? \Illuminate\Support\Carbon::parse($user->birthday)->format('M d, Y') : 'Not recorded'],
        ['Sex', $user->sex ?? 'Not recorded'],
        ['Address', $user->address ?? 'Not recorded'],
    ];

    $vehicle = [
        ['Vehicle Type', $user->vehicle_type ?? 'Not recorded'],
        ['Plate Number', $user->plate_number ?? 'Not recorded'],
        ['Account Status', \Illuminate\Support\Str::headline($user->status ?? 'active')],
        ['Role', \Illuminate\Support\Str::headline($user->primary_role ?? 'rider')],
    ];
@endphp

@section('content')
<div class="flex w-full flex-col gap-6">
    <section>
        <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Account Settings</span>
        <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink">My Profile</h1>
        <p class="mt-2 text-[10px] text-muted">Your profile is loaded from your authenticated rider account.</p>
    </section>

    <section class="rounded-xl border border-line bg-surface p-5">
        <div class="flex flex-col gap-5 md:flex-row md:items-center">
            <div class="grid h-24 w-24 place-items-center rounded-full bg-primary-soft text-primary">
                <span class="text-[24px] font-bold">{{ $initials }}</span>
            </div>

            <div>
                <h2 class="text-[20px] font-semibold text-ink">{{ $user->name ?? 'Rider Account' }}</h2>
                <p class="mt-1 text-[9px] text-muted">Rider ID: RID-{{ str_pad((string) ($user->id ?? 0), 4, '0', STR_PAD_LEFT) }}</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="rounded-full bg-success-soft px-3 py-1 text-[8px] font-semibold text-success">
                        {{ \Illuminate\Support\Str::headline($user->status ?? 'active') }}
                    </span>
                    <span class="rounded-full bg-primary-soft px-3 py-1 text-[8px] font-semibold text-primary">
                        {{ \Illuminate\Support\Str::headline($user->primary_role ?? 'rider') }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-line bg-surface p-5">
        <h2 class="text-[13px] font-semibold text-ink">Personal Information</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            @foreach($details as $info)
                <div class="rounded-lg bg-page-secondary p-4">
                    <span class="text-[8px] uppercase text-muted">{{ $info[0] }}</span>
                    <strong class="mt-2 block break-words text-[10px] font-semibold text-ink">{{ $info[1] }}</strong>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-xl border border-line bg-surface p-5">
        <h2 class="text-[13px] font-semibold text-ink">Rider Information</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            @foreach($vehicle as $item)
                <div class="rounded-lg bg-page-secondary p-4">
                    <span class="text-[8px] uppercase text-muted">{{ $item[0] }}</span>
                    <strong class="mt-2 block break-words text-[10px] font-semibold text-ink">{{ $item[1] }}</strong>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-xl border border-danger/20 bg-danger-soft p-5">
        <h2 class="text-[13px] font-semibold text-danger">Account Actions</h2>
        <p class="mt-2 text-[9px] text-danger/70">Logout from your rider account.</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="rounded-lg border border-danger/30 bg-surface px-5 py-3 text-[9px] font-semibold text-danger">
                Logout
            </button>
        </form>
    </section>
</div>
@endsection
