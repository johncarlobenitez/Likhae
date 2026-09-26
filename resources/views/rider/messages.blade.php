@extends('Rider.app')

@section('title', 'Rider Messages - LIKHAE')
@section('active', 'messages')

@section('content')
<div class="space-y-5">
    <header>
        <span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Rider Communication</span>
        <h1 class="mt-2 text-2xl font-bold text-ink">Messages</h1>
        <p class="text-sm text-muted">Conversation support is connected through the final messaging module.</p>
    </header>

    @if(session('status'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-700">{{ session('status') }}</div>
    @endif

    <section class="rounded-3xl border border-line bg-surface p-6">
        @forelse($conversations ?? [] as $conversation)
            <article class="border-b border-line py-4 last:border-b-0">
                <h2 class="font-semibold text-ink">Conversation #{{ $conversation->id }}</h2>
                <p class="text-sm text-muted">{{ $conversation->type ?? 'General' }}</p>
            </article>
        @empty
            <div class="text-center text-sm text-muted">
                <p>No rider messages yet.</p>
                <p>Order/shipment conversations will appear here once messaging records exist.</p>
            </div>
        @endforelse
    </section>
</div>
@endsection
