@extends('Logistics.app')
@section('title', 'Pickup Requests - LIKHAE Logistics')
@section('content')
<div class="space-y-6">
    <header><p class="text-xs font-bold uppercase text-primary">First-mile operations</p><h1 class="mt-2 text-2xl font-bold text-ink">Seller Pickup Requests</h1><p class="mt-2 text-sm text-muted">Approve requests and assign an active rider from this logistics center.</p></header>
    @if(session('status'))<div class="border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ $errors->first() }}</div>@endif
    <x-parcel-scanner :action="route('logistics.pickups')" :tracking="request('tracking', '')" title="Scan Pickup Waybill" button="Find Pickup" />
    <div class="overflow-x-auto border border-line bg-surface"><table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-gray-50 text-xs uppercase text-muted"><tr><th class="p-4">Parcel</th><th class="p-4">Seller</th><th class="p-4">Destination</th><th class="p-4">Status</th><th class="p-4">Assignment</th></tr></thead>
        <tbody class="divide-y divide-line">
        @forelse($pickups as $pickup)
            @php($shipment = $pickup->shipment)
            <tr>
                <td class="p-4"><a class="font-semibold text-primary" href="{{ route('logistics.parcels.show', $shipment) }}">{{ $shipment->tracking_number }}</a><div class="text-muted">{{ $shipment->sellerOrder?->seller_order_number }}</div></td>
                <td class="p-4">{{ $shipment->sellerOrder?->sellerProfile?->business_name }}</td>
                <td class="p-4">{{ collect([$shipment->destination_barangay_name,$shipment->destination_municipality_name,$shipment->destination_province_name])->filter()->implode(', ') }}</td>
                <td class="p-4 font-semibold">{{ str($pickup->status)->headline() }}</td>
                <td class="p-4">
                    @php($activeAssignment = $shipment->riderAssignments->where('assignment_type','PICKUP')->whereIn('status',['ASSIGNED','ACCEPTED','IN_PROGRESS'])->sortByDesc('id')->first())
                    @if($activeAssignment)<span class="font-semibold">{{ $activeAssignment->riderProfile?->user?->name }}</span><small class="block text-muted">{{ str($activeAssignment->status)->headline() }}</small>
                    @else<form method="POST" action="{{ route('logistics.pickups.assign', $shipment) }}" class="flex min-w-72 gap-2">@csrf
                        <select name="rider_profile_id" required class="min-h-11 flex-1 border border-line bg-white px-3"><option value="">Select active rider</option>@foreach($riders as $rider)<option value="{{ $rider->id }}">{{ $rider->user?->name }} — {{ $rider->vehicle_type }}</option>@endforeach</select>
                        <button class="bg-primary px-4 py-2 font-semibold text-white">Assign</button>
                    </form>@endif
                </td>
            </tr>
        @empty<tr><td colspan="5" class="p-8 text-center text-muted">{{ request('tracking') ? 'No pending pickup requests match that waybill.' : 'No pending pickup requests.' }}</td></tr>@endforelse
        </tbody>
    </table></div>{{ $pickups->links() }}
</div>
@endsection
