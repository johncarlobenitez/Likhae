@extends('logistics.app')

@section('title', 'Parcel Scanner - LIKHAE Logistics')

@section('content')
<div class="rounded-3xl border border-line bg-surface p-8">
    <span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Scanner</span>
    <h1 class="mt-3 text-2xl font-bold text-ink">Scan Parcel</h1>
    <p class="mt-3 text-sm text-muted">Enter a real tracking number or order number to find the parcel in MySQL.</p>

    @if($errors->any())
        <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('logistics.scanner.scan') }}" class="mt-6 flex flex-col gap-3 sm:flex-row">
        @csrf
        <input name="tracking" value="{{ old('tracking') }}" required autofocus class="min-h-12 flex-1 rounded-xl border border-line bg-white px-4 text-sm" placeholder="Tracking number or order number">
        <button class="rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white">Scan</button>
    </form>
</div>
@endsection
