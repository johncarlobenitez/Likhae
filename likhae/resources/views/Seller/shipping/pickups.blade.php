@extends('Seller.layouts.app')
@section('title', 'Courier Pickups — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/shipping.css') @endpush

@section('content')
<x-seller.page-header eyebrow="SHIPPING" title="Courier Pickups" description="Schedule parcel handovers and monitor courier pickup status.">
    <x-slot:actions><button class="btn-primary" data-modal-open="pickupModal">Schedule Pickup</button></x-slot:actions>
</x-seller.page-header>

<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-seller.kpi-card label="READY FOR PICKUP" value="5"/>
    <x-seller.kpi-card label="SCHEDULED TODAY" value="3"/>
    <x-seller.kpi-card label="PICKED UP" value="18"/>
    <x-seller.kpi-card label="FAILED PICKUPS" value="1" meta="Needs action" tone="warning"/>
</section>

<section class="panel mt-5">
    <div class="panel-header"><div><p class="panel-eyebrow">TODAY</p><h2>Pickup Schedule</h2></div></div>
    @foreach([
        ['J&T Express','2:00–4:00 PM','4 parcels','Rider assigned','Miguel Santos','JT-PU-2841'],
        ['Flash Express','4:00–6:00 PM','2 parcels','Scheduled','Pending assignment','FL-PU-1950'],
    ] as $p)
    <div class="pickup-row">
        <div class="courier-mark">{{ substr($p[0],0,2) }}</div>
        <div><strong>{{ $p[0] }}</strong><span>{{ $p[1] }} · {{ $p[2] }}</span></div>
        <div><span class="meta-label">COURIER</span><strong>{{ $p[4] }}</strong></div>
        <div><span class="meta-label">REFERENCE</span><strong>{{ $p[5] }}</strong></div>
        <div><x-seller.status-badge :status="$p[3] === 'Scheduled' ? 'Pending' : 'Ready for Pickup'"/></div>
        <button class="btn-secondary">View</button>
    </div>
    @endforeach
</section>

<div id="pickupModal" class="modal-shell hidden">
    <div class="modal-card">
        <div class="modal-header"><div><p class="panel-eyebrow">NEW PICKUP</p><h2>Schedule Courier Pickup</h2></div><button class="icon-button" data-modal-close="pickupModal">×</button></div>
        <form class="p-5">
            <div class="form-grid">
                <div class="sm:col-span-2"><label class="form-label">Pickup Address</label><textarea class="form-input min-h-20">Maria’s Local Finds, Cebu City, Cebu</textarea></div>
                <div><label class="form-label">Pickup Date</label><input class="form-input" type="date"></div>
                <div><label class="form-label">Time Window</label><select class="form-input"><option>2:00 PM – 4:00 PM</option></select></div>
                <div><label class="form-label">Number of Parcels</label><input class="form-input" value="4"></div>
                <div><label class="form-label">Estimated Weight</label><input class="form-input" value="5.2 kg"></div>
                <div class="sm:col-span-2"><label class="form-label">Courier</label><select class="form-input"><option>J&T Express</option><option>Flash Express</option></select></div>
            </div>
            <div class="mt-5 flex justify-end gap-2"><button type="button" class="btn-secondary" data-modal-close="pickupModal">Cancel</button><button class="btn-primary">Schedule Pickup</button></div>
        </form>
    </div>
</div>
@endsection
