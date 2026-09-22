@extends('Logistics.app')
@section('title', 'Delivery Areas - LIKHAE Logistics')
@section('content')
<div class="space-y-6">
    <header><h1 class="text-2xl font-bold text-ink">Delivery Areas</h1><p class="mt-2 text-sm text-muted">Set the cities you serve and the shipping rates buyers see at checkout.</p></header>
    @if(session('status'))<p role="status" class="rounded-xl border border-line bg-surface p-4">{{ session('status') }}</p>@endif
    @if($errors->any())<p role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">{{ $errors->first() }}</p>@endif
    <form method="POST" action="{{ route('logistics.delivery-areas.store') }}" class="grid gap-4 rounded-xl border border-line bg-surface p-5 sm:grid-cols-2">
        @csrf
        <h2 class="font-semibold sm:col-span-2">Add or update coverage</h2>
        <label class="text-sm">Province<input required name="province" value="{{ old('province') }}" maxlength="255" class="mt-1 block w-full rounded-lg border border-line p-3"></label>
        <label class="text-sm">City / Municipality<input required name="city" value="{{ old('city') }}" maxlength="255" class="mt-1 block w-full rounded-lg border border-line p-3"></label>
        <label class="text-sm">City code (optional)<input name="city_code" value="{{ old('city_code') }}" maxlength="20" class="mt-1 block w-full rounded-lg border border-line p-3"></label>
        <label class="text-sm">First kilogram (PHP)<input required type="number" name="base_fee" value="{{ old('base_fee', '50.00') }}" min="0" max="10000" step="0.01" class="mt-1 block w-full rounded-lg border border-line p-3"></label>
        <label class="text-sm">Each additional kilogram (PHP)<input required type="number" name="per_kg_fee" value="{{ old('per_kg_fee', '10.00') }}" min="0" max="10000" step="0.01" class="mt-1 block w-full rounded-lg border border-line p-3"></label>
        <p class="text-sm text-muted">Use the province and city names from buyer addresses. Saving the same location updates its rates and activates coverage.</p>
        <button class="rounded-lg bg-primary px-4 py-3 font-semibold text-white sm:col-span-2">Save coverage</button>
    </form>
    <section class="overflow-x-auto rounded-xl border border-line bg-surface">
        <table class="w-full text-left text-sm"><thead><tr><th class="p-4">Location</th><th class="p-4">First kg</th><th class="p-4">Additional kg</th><th class="p-4">Status</th><th class="p-4">Action</th></tr></thead><tbody>
        @forelse($areas as $area)
            <tr class="border-t border-line"><td class="p-4">{{ $area->city }}, {{ $area->province }}</td><td class="p-4">PHP {{ number_format($area->base_fee_minor / 100, 2) }}</td><td class="p-4">PHP {{ number_format($area->per_kg_fee_minor / 100, 2) }}</td><td class="p-4">{{ $area->is_active ? 'Active' : 'Paused' }}</td><td class="p-4"><form method="POST" action="{{ route('logistics.delivery-areas.toggle', $area) }}">@csrf @method('PATCH')<input type="hidden" name="is_active" value="{{ $area->is_active ? 0 : 1 }}"><button class="rounded-lg border border-line px-3 py-2">{{ $area->is_active ? 'Pause' : 'Activate' }}</button></form></td></tr>
        @empty<tr><td colspan="5" class="p-6 text-muted">No delivery areas yet. Add coverage so buyers can select your service at checkout.</td></tr>@endforelse
        </tbody></table>
    </section>
</div>
@endsection
