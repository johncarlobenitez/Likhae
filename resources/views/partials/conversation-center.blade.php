@if(session('status'))<div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800">{{ session('status') }}</div>@endif
@if($errors->any())<div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800">{{ $errors->first() }}</div>@endif
<div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
    <section class="space-y-4">
        @forelse($conversations as $conversation)
            @php($others = $conversation->participants->where('id','!=',auth()->id())->pluck('name')->join(', '))
            <article class="rounded-xl border border-line bg-surface p-5">
                <header class="mb-4 flex items-center justify-between gap-3"><div><strong class="text-ink">{{ $others ?: 'Conversation' }}</strong><small class="block text-muted">{{ str($conversation->type)->headline() }}</small></div><span class="text-xs text-muted">{{ $conversation->updated_at?->diffForHumans() }}</span></header>
                <div class="max-h-72 space-y-2 overflow-y-auto">
                    @foreach($conversation->messages as $message)<div class="flex {{ $message->sender_user_id === auth()->id() ? 'justify-end' : 'justify-start' }}"><div class="max-w-[80%] rounded-lg px-3 py-2 text-sm {{ $message->sender_user_id === auth()->id() ? 'bg-primary text-white' : 'bg-page-secondary text-ink' }}"><strong class="block text-xs">{{ $message->sender?->name }}</strong>{{ $message->body }}</div></div>@endforeach
                </div>
            </article>
        @empty<div class="rounded-xl border border-dashed border-line bg-surface p-8 text-center text-sm text-muted">No conversations yet. Start one using the form.</div>@endforelse
    </section>
    <aside class="h-fit rounded-xl border border-line bg-surface p-5"><h2 class="font-semibold text-ink">Send a message</h2><form method="POST" action="{{ $sendRoute }}" class="mt-4 space-y-3">@csrf<select name="recipient_user_id" required class="w-full rounded-lg border border-line bg-white px-3 py-2 text-sm"><option value="">Select recipient</option>@foreach($contacts as $contact)<option value="{{ $contact->id }}">{{ $contact->name }} — {{ str($contact->account_type)->headline() }}</option>@endforeach</select><textarea name="body" required maxlength="5000" rows="5" class="w-full rounded-lg border border-line px-3 py-2 text-sm" placeholder="Type your message"></textarea><button class="w-full rounded-lg bg-primary px-4 py-2 font-semibold text-white">Send Message</button></form></aside>
</div>
