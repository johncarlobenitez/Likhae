@extends('rider.app')

@section('title', 'Delivery Details - LIKHAE Rider')

@section('content')
<div class="flex flex-col gap-8">
    @if(session('status'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-700">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-3xl border border-line bg-surface p-8">
        <span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Delivery Details</span>
        <h1 class="mt-3 text-2xl font-bold text-ink">{{ $parcel['tracking'] }}</h1>
        <p class="mt-3 text-sm text-muted">{{ $parcel['status_label'] }} - Buyer: {{ $parcel['buyer'] }}</p>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <article class="rounded-3xl border border-line bg-surface p-8">
            <h2 class="text-lg font-bold text-ink">Recipient</h2>
            <dl class="mt-5 grid gap-3 text-sm">
                <div><dt class="text-muted">Buyer</dt><dd class="font-semibold text-ink">{{ $parcel['buyer'] }}</dd></div>
                <div><dt class="text-muted">Contact</dt><dd class="font-semibold text-ink">{{ $parcel['contact'] }}</dd></div>
                <div><dt class="text-muted">Delivery Address</dt><dd class="font-semibold text-ink whitespace-pre-line">{{ $parcel['address'] }}</dd></div>
                <div><dt class="text-muted">Order Amount</dt><dd class="font-semibold text-ink">{{ $parcel['amount'] }}</dd></div>
            </dl>
        </article>

        <article class="rounded-3xl border border-line bg-surface p-8">
            <h2 class="text-lg font-bold text-ink">Delivery Actions</h2>
            <div class="mt-5 grid gap-3">
                @if($parcel['status'] === 'delivery_assigned')
                    <form method="POST" action="{{ route('rider.shipments.transition', $delivery) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="delivery_accepted"><button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Accept Delivery Assignment</button></form>
                @endif
                @if($parcel['status'] === 'delivery_accepted')
                    @if($verified)
                        <div class="border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800">Hub parcel verified: {{ $delivery->tracking_code }}</div>
                        <form method="POST" action="{{ route('rider.shipments.transition', $delivery) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="delivery_collected"><input type="hidden" name="tracking" value="{{ $delivery->tracking_code }}"><button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Receive Parcel From Logistics</button></form>
                    @else
                        <x-parcel-scanner :action="route('rider.deliveries.show', $delivery)" :tracking="request('tracking', '')" title="Scan Parcel At Logistics Center" description="Verify the assigned parcel before taking custody for delivery." button="Verify Parcel" />
                    @endif
                @endif
                @if($parcel['status'] === 'delivery_collected')
                    <form method="POST" action="{{ route('rider.shipments.transition', $delivery) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="out_for_delivery"><button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Out For Delivery</button></form>
                @endif
                @if($parcel['status'] === 'out_for_delivery')
                    <form method="POST" enctype="multipart/form-data" action="{{ route('rider.shipments.transition', $delivery) }}" class="grid gap-3" data-delivery-proof-form>@csrf @method('PATCH')<input type="hidden" name="status" value="delivered"><input name="receiver_name" required class="w-full rounded-xl border border-line bg-white px-4 py-3 text-sm" placeholder="Receiver name"><input type="file" name="proof" accept="image/jpeg,image/png,image/webp" capture="environment" required class="w-full rounded-xl border border-line bg-white px-4 py-3 text-sm" data-delivery-proof><p class="text-xs text-muted" data-proof-status>Photos are optimized automatically before upload.</p><button type="submit" class="w-full rounded-xl bg-green-700 px-5 py-3 text-sm font-semibold text-white" data-delivery-submit>Mark Delivered</button></form>
                    <form method="POST" action="{{ route('rider.shipments.transition', $delivery) }}" class="grid gap-3">
                        @csrf @method('PATCH')<input type="hidden" name="status" value="failed">
                        <textarea name="note" rows="3" required class="w-full rounded-xl border border-line bg-white px-4 py-3 text-sm" placeholder="Reason for failed delivery"></textarea>
                        <button class="w-full rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-semibold text-red-700">Record Delivery Failed</button>
                    </form>
                @endif
                <a href="{{ route('tracking.show', $delivery->tracking_code) }}" class="rounded-xl border border-line px-5 py-3 text-center text-sm font-semibold text-ink">View Tracking</a>
                <a href="{{ route('rider.deliveries') }}" class="rounded-xl border border-line px-5 py-3 text-center text-sm font-semibold text-ink">Back To Deliveries</a>
            </div>
        </article>
    </section>

</div>
@endsection

@push('scripts')
<script>
document.querySelector('[data-delivery-proof-form]')?.addEventListener('submit', async function (event) {
    if (this.dataset.optimized === 'true') return;
    const input = this.querySelector('[data-delivery-proof]');
    const file = input?.files?.[0];
    if (!file || file.size <= 1800000 || !window.DataTransfer) return;

    event.preventDefault();
    const status = this.querySelector('[data-proof-status]');
    const button = this.querySelector('[data-delivery-submit]');
    status.textContent = 'Optimizing proof photo...';
    button.disabled = true;

    try {
        const bitmap = await createImageBitmap(file);
        const scale = Math.min(1, 1600 / Math.max(bitmap.width, bitmap.height));
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(bitmap.width * scale));
        canvas.height = Math.max(1, Math.round(bitmap.height * scale));
        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
        bitmap.close?.();
        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.78));
        if (!blob) throw new Error('Photo optimization failed.');
        const transfer = new DataTransfer();
        transfer.items.add(new File([blob], 'delivery-proof.jpg', { type: 'image/jpeg' }));
        input.files = transfer.files;
        this.dataset.optimized = 'true';
        status.textContent = 'Proof photo ready to upload.';
        this.requestSubmit();
    } catch (error) {
        status.textContent = 'Unable to optimize this photo. Choose a smaller image and try again.';
        button.disabled = false;
    }
});
</script>
@endpush
