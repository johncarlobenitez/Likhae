@extends('logistics.app')

@section('title', 'Parcel Scanner | LIKHAE Logistics')

@section('content')
<div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
    <nav class="flex items-center gap-2 text-[10px] text-muted"><a href="{{ route('logistics.dashboard') }}" class="hover:text-primary">Dashboard</a><span>/</span><span class="font-semibold text-ink">Parcel Scanner</span></nav>
    <section class="rounded-2xl border border-line bg-surface p-5 shadow-sm sm:p-7"><span class="text-[10px] font-bold uppercase tracking-widest text-primary">Parcel intake</span><h1 class="mt-2 text-xl font-bold text-ink">Scan a parcel</h1><p class="mt-2 text-sm leading-6 text-muted">Use a connected barcode scanner or enter the tracking number manually to open the parcel record.</p>
        <form class="mt-6" data-scanner-form><label for="tracking" class="text-xs font-semibold text-ink">Tracking number</label><div class="mt-2 flex flex-col gap-2 sm:flex-row"><input id="tracking" required autocomplete="off" autofocus placeholder="LH-2026-1001" class="h-11 flex-1 rounded-xl border border-line bg-page px-3 text-sm text-ink outline-none focus:border-primary" data-tracking-input><button class="h-11 rounded-xl bg-primary px-5 text-sm font-bold text-white hover:bg-primary-hover" type="submit">Open Parcel</button></div><p class="mt-3 text-[11px] text-muted">Demo records: LH-2026-1001, LH-2026-1002, LH-2026-1003.</p><p class="mt-3 hidden rounded-lg bg-danger-soft px-3 py-2 text-xs text-danger" data-scan-error></p></form>
    </section>
</div>
@push('scripts')
<script>
document.querySelector('[data-scanner-form]')?.addEventListener('submit', (event) => {
    event.preventDefault();
    const input = document.querySelector('[data-tracking-input]');
    const error = document.querySelector('[data-scan-error]');
    const match = String(input?.value || '').trim().toUpperCase().match(/^LH-2026-(100[1-3])$/);
    if (!match) { error.textContent = 'Parcel not found in this Logistics Center.'; error.classList.remove('hidden'); return; }
    window.location.assign(@json(url('/logistics/parcels')) + '/' + Number(match[1]));
});
</script>
@endpush
@endsection
