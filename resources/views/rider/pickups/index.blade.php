@extends('Rider.app')

@section('title','Pickup Assignments - LIKHAE Rider')

@section('content')
<div class="flex flex-col gap-8">
    @if(session('status'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-700">{{ session('status') }}</div>
    @endif

    <section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Pickup Management</span>
            <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink">Pickup Assignments</h1>
            <p class="mt-3 text-sm text-muted">Accept seller pickup requests, confirm parcel pickup, then deliver to the sorting center.</p>
        </div>
        <div class="rounded-full bg-warning-soft px-5 py-3 text-sm font-semibold text-warning">{{ $pickupStats['ready'] }} Ready Pickup</div>
    </section>

    <section class="grid gap-5 md:grid-cols-3">
        <div class="rounded-3xl border border-line bg-surface p-6"><p class="text-sm text-muted">Ready For Pickup</p><h2 class="mt-3 text-4xl font-bold text-ink">{{ $pickupStats['ready'] }}</h2></div>
        <div class="rounded-3xl border border-line bg-surface p-6"><p class="text-sm text-muted">Accepted</p><h2 class="mt-3 text-4xl font-bold text-ink">{{ $pickupStats['accepted'] }}</h2></div>
        <div class="rounded-3xl border border-line bg-surface p-6"><p class="text-sm text-muted">Picked Up</p><h2 class="mt-3 text-4xl font-bold text-ink">{{ $pickupStats['picked_up'] }}</h2></div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-line bg-surface">
        <div class="border-b border-line px-8 py-6">
            <h2 class="text-lg font-bold text-ink">Pickup Tasks</h2>
            <p class="mt-2 text-xs text-muted">Only real delivery records from seller pickup requests are listed.</p>
        </div>

        <div class="divide-y divide-line">
            @forelse($pickups as $pickup)
                <article class="flex flex-col gap-5 p-8 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex gap-4">
                        <img src="{{ $pickup['image'] }}" alt="" class="h-16 w-16 rounded-2xl border border-line object-cover">
                        <div>
                            <h3 class="text-lg font-bold text-ink">{{ $pickup['tracking'] }}</h3>
                            <p class="mt-2 text-sm text-muted">Seller: {{ $pickup['seller'] }}</p>
                            <p class="mt-1 text-sm text-muted">{{ $pickup['address'] }}</p>
                            <p class="mt-2 text-sm text-muted">{{ $pickup['items'] }} item(s)</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full bg-warning-soft px-4 py-2 text-xs font-semibold text-warning">{{ $pickup['status_label'] }}</span>
                        <a href="{{ route('rider.pickups.show', $pickup['id']) }}" class="rounded-xl border border-line px-5 py-3 text-sm font-semibold text-ink hover:bg-page-secondary">View</a>
                    </div>
                </article>
            @empty
                <div class="p-8 text-sm text-muted">No pickup requests are available.</div>
            @endforelse
        </div>
    </section>
    <div>{{ $assignments->links() }}</div>
</div>
@endsection
