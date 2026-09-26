@extends('layouts.buyer')
@section('title', 'Track Parcel')
@section('content')
<div class="space-y-6"><h1 class="text-2xl font-bold">Tracking {{ $shipment->tracking_number }}</h1><div class="rounded-2xl border bg-white p-5"><p>Status: {{ $shipment->current_status }}</p><p>Destination: {{ $shipment->destination_barangay_name }}, {{ $shipment->destination_municipality_name }}</p></div><div class="rounded-2xl border bg-white p-5"><h2 class="font-bold">Timeline</h2><ul class="mt-3 space-y-2">@foreach($shipment->events as $event)<li>{{ $event->status }} — {{ $event->notes }} — {{ $event->occurred_at }}</li>@endforeach</ul></div></div>
@endsection
