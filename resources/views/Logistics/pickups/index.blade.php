@extends('logistics.app')

@section('title', 'Pickup Requests - LIKHAE Logistics')

@section('content')
<div class="space-y-6">
    <header>
        <p class="text-xs font-bold uppercase text-primary">First-mile operations</p>
        <h1 class="mt-2 text-2xl font-bold text-ink">Seller Pickup Requests</h1>
        <p class="mt-2 text-sm text-muted">Assign active riders using seller-area match and current workload.</p>
    </header>

    @if(session('status'))
        <div class="border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('status') }}</div>
    @endif

    <div class="overflow-x-auto border border-line bg-surface">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-line bg-gray-50 text-xs uppercase text-muted">
                <tr><th class="p-4">Parcel</th><th class="p-4">Seller pickup</th><th class="p-4">Status</th><th class="p-4">Assignment</th></tr>
            </thead>
            <tbody class="divide-y divide-line">
            @forelse($pickupRows as $row)
                <tr>
                    <td class="p-4"><a class="font-semibold text-primary" href="{{ route('logistics.parcels.show', $row['delivery']) }}">{{ $row['parcel']['tracking'] }}</a><div class="text-muted">{{ $row['parcel']['order'] }}</div></td>
                    <td class="p-4"><div class="font-semibold text-ink">{{ $row['parcel']['seller'] }}</div><div class="max-w-sm text-muted">{{ $row['parcel']['pickup_address'] ?: 'Seller address not recorded' }}</div></td>
                    <td class="p-4 font-semibold">{{ Str::headline($row['delivery']->status) }}</td>
                    <td class="p-4">
                        @if($row['delivery']->status === 'unassigned')
                            <form method="POST" action="{{ route('logistics.pickups.assign', $row['delivery']) }}" class="flex min-w-72 gap-2">
                                @csrf
                                <select name="rider_id" required class="min-h-11 flex-1 border border-line bg-white px-3">
                                    <option value="">Select rider</option>
                                    @foreach($row['riders'] as $rider)
                                        <option value="{{ $rider['id'] }}">{{ $rider['name'] }} - {{ $rider['area'] }} ({{ $rider['workload'] }} active)</option>
                                    @endforeach
                                </select>
                                <button class="bg-primary px-4 py-2 font-semibold text-white">Accept &amp; Assign</button>
                            </form>
                        @else
                            <span class="text-muted">{{ $row['delivery']->rider?->user?->name ?: 'Assignment recorded' }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-8 text-center text-muted">No seller pickup requests.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
