@extends('rider.app')

@section('title', 'Assigned Parcels')

@section('content')
<div class="space-y-6">
    <header class="space-y-2">
        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Operations</p>
        <h1 class="text-2xl font-bold text-ink">Assigned Parcels</h1>
        <p class="text-sm text-muted">Track the parcels currently assigned to your rider account and update each parcel state as you proceed.</p>
    </header>

    <x-parcel-scanner :action="route('rider.shipments')" :tracking="request('tracking','')" title="Scan Assigned Parcel" description="Scan the waybill or enter its tracking code. This only identifies your assigned parcel; use Confirm Picked Up to change its status." button="Find Assigned Parcel" />

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach([
            ['label' => 'Assigned', 'value' => $shipmentStats['assigned'] ?? 0],
            ['label' => 'Picked up', 'value' => $shipmentStats['picked_up'] ?? 0],
            ['label' => 'In transit', 'value' => $shipmentStats['in_transit'] ?? 0],
            ['label' => 'Out for delivery', 'value' => $shipmentStats['out_for_delivery'] ?? 0],
            ['label' => 'Delivered', 'value' => $shipmentStats['delivered'] ?? 0],
        ] as $stat)
            <article class="rounded-xl border border-line bg-surface p-4">
                <span class="text-[9px] font-semibold uppercase tracking-[0.08em] text-muted">{{ $stat['label'] }}</span>
                <strong class="mt-2 block text-[24px] font-bold text-ink">{{ $stat['value'] }}</strong>
            </article>
        @endforeach
    </div>

    @if($shipments->isEmpty())
        <section class="rounded-2xl border border-dashed border-line bg-surface p-8 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary-soft text-primary">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 8 12 3 3 8l9 5 9-5Z"/>
                    <path d="M3 8v8l9 5 9-5V8"/>
                    <path d="M12 13v8"/>
                </svg>
            </div>
            <h2 class="mt-4 text-xl font-bold text-ink">No assigned parcels yet</h2>
            <p class="mt-2 text-sm text-muted">There are no parcels assigned to your rider account right now. When new work is assigned, it will appear here.</p>
            <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('rider.pickups') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">View pickup queue</a>
                <a href="{{ route('rider.deliveries') }}" class="rounded-lg border border-line bg-white px-4 py-2 text-sm font-semibold text-ink">View delivery queue</a>
            </div>
        </section>
    @else
        @foreach($shipments as $shipment)
            <article class="rounded-2xl border border-line bg-surface p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Tracking</p>
                        <strong class="mt-2 block text-xl font-bold text-ink">{{ $shipment->tracking_code }}</strong>
                        <p class="mt-1 text-sm text-muted">{{ str($shipment->status)->headline() }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if($shipment->pickup_rider_id === auth()->user()->rider?->id && in_array($shipment->status, ['pickup_assigned','pickup_accepted','picked_up','in_transit_to_hub'], true))
                            <a class="rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white" href="{{ route('rider.pickups.show', $shipment) }}">Open Pickup Assignment</a>
                        @elseif($shipment->delivery_rider_id === auth()->user()->rider?->id && in_array($shipment->status, ['delivery_assigned','delivery_accepted','delivery_collected','out_for_delivery','failed'], true))
                            <a class="rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white" href="{{ route('rider.deliveries.show', $shipment) }}">Open Delivery Assignment</a>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    @endif
</div>
@endsection
