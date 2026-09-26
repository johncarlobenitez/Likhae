@extends('Rider.app')

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
                @if($parcel['status'] === 'picked_up')
                    <form method="POST" action="{{ route('rider.shipments.transition', $delivery) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="in_transit"><button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Mark In Transit</button></form>
                @endif
                @if($parcel['status'] === 'in_transit')
                    <form method="POST" action="{{ route('rider.shipments.transition', $delivery) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="out_for_delivery"><button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Out For Delivery</button></form>
                @endif
                @if($parcel['status'] === 'out_for_delivery')
                    <form method="POST" enctype="multipart/form-data" action="{{ route('rider.shipments.transition', $delivery) }}" class="grid gap-3">@csrf @method('PATCH')<input type="hidden" name="status" value="delivered"><input name="receiver_name" required class="w-full rounded-xl border border-line bg-white px-4 py-3 text-sm" placeholder="Receiver name"><input type="file" name="proof" accept="image/*" capture="environment" required class="w-full rounded-xl border border-line bg-white px-4 py-3 text-sm"><button type="submit" class="w-full rounded-xl bg-green-700 px-5 py-3 text-sm font-semibold text-white">Mark Delivered</button></form>
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
