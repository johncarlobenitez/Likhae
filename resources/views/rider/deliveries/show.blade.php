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
                @if($parcel['status'] === 'ASSIGNED_TO_RIDER')
                    @php $finalAssignment = $delivery->assignments->firstWhere('assignment_type', 'final_delivery'); @endphp
                    @if($finalAssignment?->status === 'assigned')
                        <form method="POST" action="{{ route('rider.deliveries.accept', $delivery) }}">@csrf<button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Accept Delivery Assignment</button></form>
                    @elseif(!$released)
                        <div class="border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-800">Waiting for Logistics to scan and authorize parcel release.</div>
                    @elseif($verified)
                        <div class="border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800">Parcel and final assignment verified.</div>
                        <form method="POST" action="{{ route('rider.deliveries.pickup-sorting', $parcel['id']) }}">@csrf<input type="hidden" name="tracking" value="{{ $delivery->tracking_number }}"><button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white">Confirm Collection From Logistics</button></form>
                    @else
                        <p class="text-sm text-muted">Logistics authorized release. Scan the parcel below to confirm your final-delivery assignment.</p>
                    @endif
                @endif
                @if($parcel['status'] === 'OUT_FOR_DELIVERY')
                    <form method="POST" action="{{ route('rider.deliveries.delivered', $delivery) }}">@csrf<button type="submit" class="w-full rounded-xl bg-green-700 px-5 py-3 text-sm font-semibold text-white">Mark Delivered</button></form>
                    <form method="POST" action="{{ route('rider.deliveries.failed', $parcel['id']) }}" class="grid gap-3">
                        @csrf
                        <textarea name="failure_reason" rows="3" required class="w-full rounded-xl border border-line bg-white px-4 py-3 text-sm" placeholder="Reason for failed delivery"></textarea>
                        <button class="w-full rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-semibold text-red-700">Record Delivery Failed</button>
                    </form>
                @endif
                <a href="{{ route('rider.deliveries.tracking', $parcel['id']) }}" class="rounded-xl border border-line px-5 py-3 text-center text-sm font-semibold text-ink">View Tracking</a>
                <a href="{{ route('rider.deliveries') }}" class="rounded-xl border border-line px-5 py-3 text-center text-sm font-semibold text-ink">Back To Deliveries</a>
            </div>
        </article>
    </section>

    @if($parcel['status'] === 'ASSIGNED_TO_RIDER' && $released && $finalAssignment?->status === 'accepted')
        <x-parcel-scanner :action="route('rider.deliveries.show', $delivery)" :tracking="request('tracking', '')" title="Scan Parcel At Logistics" description="Verify this is your assigned final-delivery parcel before collecting it." button="Verify Parcel" />
        @if(request()->filled('tracking') && !$verified)<div class="border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">The scanned tracking number does not match this delivery assignment.</div>@endif
    @endif
</div>
@endsection
