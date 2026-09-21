@extends('logistics.app')

@section('title', 'Messages - LIKHAE Logistics')

@section('content')
<div class="flex flex-col gap-6">
    <section>
        <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Communication Center</span>
        <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink">Messages</h1>
        <p class="mt-2 max-w-[680px] text-[11px] leading-6 text-muted">Messages are stored in the database and sent to real LIKHAE users.</p>
    </section>

    @if(session('status'))
        <div class="rounded-xl border border-line bg-surface p-4 text-[10px] font-semibold text-primary">{{ session('status') }}</div>
    @endif

    <section class="grid gap-5 xl:grid-cols-[320px_minmax(0,1fr)]">
        <aside class="overflow-hidden rounded-xl border border-line bg-surface">
            <div class="border-b border-line px-5 py-4">
                <h2 class="text-[13px] font-semibold text-ink">Contacts</h2>
            </div>
            @forelse($contacts as $contact)
                <a href="{{ route('logistics.messages', ['contact' => $contact->id]) }}" class="block border-b border-line px-5 py-4 transition hover:bg-page-secondary {{ $selected?->id === $contact->id ? 'bg-page-secondary' : '' }}">
                    <strong class="block text-[10px] font-semibold text-ink">{{ $contact->name }}</strong>
                    <span class="mt-1 block text-[8px] text-muted">{{ Str::headline($contact->primary_role) }} - {{ $contact->email }}</span>
                </a>
            @empty
                <div class="p-5 text-[10px] text-muted">No active contacts are available.</div>
            @endforelse
        </aside>

        <section class="overflow-hidden rounded-xl border border-line bg-surface">
            <div class="border-b border-line px-5 py-4">
                <h2 class="text-[13px] font-semibold text-ink">{{ $selected?->name ?? 'Select a contact' }}</h2>
                <p class="mt-1 text-[9px] text-muted">{{ $selected ? Str::headline($selected->primary_role) : 'Open a contact to view the thread.' }}</p>
            </div>

            <div class="flex min-h-[420px] flex-col gap-3 bg-page-secondary p-5">
                @forelse($messages as $message)
                    @php($fromMe = $message->sender_id === auth()->id())
                    <div class="flex {{ $fromMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] rounded-xl {{ $fromMe ? 'bg-primary text-white' : 'bg-surface text-ink' }} px-4 py-3">
                            <p class="text-[10px] leading-5">{{ $message->body }}</p>
                            <span class="mt-2 block text-[8px] opacity-70">{{ $message->created_at?->format('M d, Y g:i A') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="m-auto text-center text-[10px] text-muted">No messages in this thread yet.</div>
                @endforelse
            </div>

            @if($selected)
                <form method="POST" action="{{ route('logistics.messages.send') }}" class="flex gap-3 border-t border-line p-4">
                    @csrf
                    <input type="hidden" name="recipient_id" value="{{ $selected->id }}">
                    <input name="body" required maxlength="2000" class="min-h-10 flex-1 rounded-lg border border-line bg-surface px-3 text-[10px] text-ink" placeholder="Type a message...">
                    <button class="rounded-lg bg-primary px-4 text-[10px] font-semibold text-white">Send</button>
                </form>
            @endif
        </section>
    </section>
</div>
@endsection
