@extends('layouts.buyer')

@section('title', 'Messages — LIKHAE')
@section('active', 'messages')

@section('content')
@php
    if (!isset($conversations)) {
        $conversations = collect([
            [
                'id' => 1,
                'seller_name' => 'Habi Local Crafts',
                'seller_status' => 'Online',
                'last_message' => 'Your tote bag is ready for pickup.',
                'time' => '10:42 AM',
                'unread' => 2,
                'messages' => [
                    ['body' => 'Hello! Is the handwoven tote bag still available?', 'time' => '10:30 AM', 'is_mine' => true],
                    ['body' => 'Hi! Yes, it is still available. We currently have the natural brown and olive variants.', 'time' => '10:34 AM', 'is_mine' => false],
                    ['body' => 'I ordered the natural brown variant. Thank you!', 'time' => '10:38 AM', 'is_mine' => true],
                    ['body' => 'Thank you! Your tote bag is ready for pickup.', 'time' => '10:42 AM', 'is_mine' => false],
                ],
            ],
            [
                'id' => 2,
                'seller_name' => 'Clay & Co. Studio',
                'seller_status' => 'Active 5 minutes ago',
                'last_message' => 'We packed your ceramic mug securely.',
                'time' => 'Yesterday',
                'unread' => 0,
                'messages' => [
                    ['body' => 'Can you make sure the mug is packed securely?', 'time' => '4:12 PM', 'is_mine' => true],
                    ['body' => 'Of course! We use protective wrapping and a reinforced box for ceramic products.', 'time' => '4:18 PM', 'is_mine' => false],
                    ['body' => 'We packed your ceramic mug securely.', 'time' => '4:25 PM', 'is_mine' => false],
                ],
            ],
            [
                'id' => 3,
                'seller_name' => 'Likha Home Essentials',
                'seller_status' => 'Offline',
                'last_message' => 'The available colors are cream and sage.',
                'time' => 'Sep 2',
                'unread' => 0,
                'messages' => [
                    ['body' => 'What colors are available for this product?', 'time' => '2:04 PM', 'is_mine' => true],
                    ['body' => 'The available colors are cream and sage.', 'time' => '2:15 PM', 'is_mine' => false],
                ],
            ],
        ]);
    } else {
        $conversations = collect($conversations);
    }

    $conversations = $conversations->map(function ($conversation, $index) {
        $sellerValue = data_get($conversation, 'seller.store_name')
            ?? data_get($conversation, 'seller.shop_name')
            ?? data_get($conversation, 'seller.name')
            ?? data_get($conversation, 'seller_name')
            ?? 'LIKHAE Seller';

        $sellerName = is_scalar($sellerValue) ? (string) $sellerValue : 'LIKHAE Seller';

        $messages = collect(data_get($conversation, 'messages', []))->map(fn ($m) => [
            'body'    => (string) (data_get($m, 'body') ?? data_get($m, 'message') ?? ''),
            'time'    => (string) data_get($m, 'time', ''),
            'is_mine' => (bool) (data_get($m, 'is_mine') ?? data_get($m, 'mine') ?? false),
        ]);

        return [
            'id'            => data_get($conversation, 'id', 'conversation-' . ($index + 1)),
            'seller_name'   => $sellerName,
            'seller_status' => (string) data_get($conversation, 'seller_status', 'Offline'),
            'seller_avatar' => data_get($conversation, 'seller.avatar') ?? data_get($conversation, 'seller_avatar'),
            'last_message'  => (string) (data_get($conversation, 'latest_message.body') ?? data_get($conversation, 'last_message') ?? 'Start a conversation'),
            'time'          => (string) data_get($conversation, 'time', ''),
            'unread'        => max(0, (int) data_get($conversation, 'unread', 0)),
            'messages'      => $messages,
        ];
    });

    $requestedConversation = request('conversation');
    $activeConversation = filled($requestedConversation)
        ? $conversations->first(fn ($c) => (string) $c['id'] === (string) $requestedConversation)
        : null;

    $totalUnread = $conversations->sum('unread');
@endphp

<div class="mx-auto w-full max-w-[1500px] px-4 py-6 sm:px-6 lg:px-8">
    <header class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-700">Communication</span>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-stone-900">Messages</h1>
            <p class="mt-1 text-sm text-stone-500">Keep conversations with local sellers in one place.</p>
        </div>
        @if ($totalUnread > 0)
            <span class="inline-flex w-fit items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-800">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                {{ $totalUnread }} {{ $totalUnread === 1 ? 'unread message' : 'unread messages' }}
            </span>
        @endif
    </header>

    <section class="grid min-h-[620px] overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm lg:h-[calc(100vh-190px)] lg:grid-cols-[330px_minmax(0,1fr)]">
        {{-- Conversation list --}}
        <aside class="{{ $activeConversation ? 'hidden lg:flex' : 'flex' }} min-h-0 flex-col border-stone-200 lg:border-r" aria-label="Conversation list">
            <div class="border-b border-stone-100 p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-stone-900">Conversations</h2>
                        <p class="mt-0.5 text-[11px] text-stone-500">{{ $conversations->count() }} {{ $conversations->count() === 1 ? 'conversation' : 'conversations' }}</p>
                    </div>
                    <button type="button" class="flex h-9 w-9 items-center justify-center rounded-xl border border-stone-200 text-stone-500 transition hover:bg-stone-50 hover:text-stone-900" aria-label="Start a new conversation">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4z"/></svg>
                    </button>
                </div>
                <div class="relative mt-4">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-stone-400" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input type="search" placeholder="Search conversations..." class="w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-10 pr-3 text-xs text-stone-800 outline-none transition placeholder:text-stone-400 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-100" data-conversation-search>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto" data-conversation-list>
                @forelse ($conversations as $conversation)
                    @php
                        $isActive = $activeConversation && (string) $activeConversation['id'] === (string) $conversation['id'];
                        $sellerInitial = strtoupper(mb_substr($conversation['seller_name'], 0, 1));
                        $isOnline = strtolower($conversation['seller_status']) === 'online';
                    @endphp
                    <a href="{{ route('buyer.messages', ['conversation' => $conversation['id']]) }}"
                       class="relative flex gap-3 border-b border-stone-100 px-4 py-4 transition {{ $isActive ? 'bg-amber-50' : 'bg-white hover:bg-stone-50' }}"
                       data-conversation-item data-conversation-name="{{ mb_strtolower($conversation['seller_name']) }}"
                       @if ($isActive) aria-current="page" @endif>
                        @if ($isActive)<span class="absolute inset-y-0 left-0 w-1 bg-amber-500"></span>@endif
                        <div class="relative shrink-0">
                            <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-amber-100 text-sm font-bold text-amber-800">
                                @if ($conversation['seller_avatar'])
                                    <img src="{{ $conversation['seller_avatar'] }}" alt="{{ $conversation['seller_name'] }}" class="h-full w-full object-cover">
                                @else
                                    {{ $sellerInitial }}
                                @endif
                            </div>
                            @if ($isOnline)<span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-emerald-500"></span>@endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <strong class="truncate text-xs font-semibold text-stone-900">{{ $conversation['seller_name'] }}</strong>
                                <time class="shrink-0 text-[9px] text-stone-400">{{ $conversation['time'] }}</time>
                            </div>
                            <div class="mt-1.5 flex items-center justify-between gap-3">
                                <p class="line-clamp-1 min-w-0 text-[11px] leading-5 text-stone-500">{{ $conversation['last_message'] }}</p>
                                @if ($conversation['unread'] > 0)
                                    <span class="flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-amber-600 px-1.5 text-[9px] font-bold text-white">{{ $conversation['unread'] > 99 ? '99+' : $conversation['unread'] }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="flex h-full min-h-80 flex-col items-center justify-center px-6 py-12 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-50 text-amber-700">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v11H8l-4 4z"/><path d="M8 9h8"/><path d="M8 13h5"/></svg>
                        </div>
                        <h3 class="mt-4 text-sm font-semibold text-stone-900">No conversations yet</h3>
                        <p class="mt-2 max-w-56 text-xs leading-5 text-stone-500">Message a seller from any product detail page to begin a conversation.</p>
                        <a href="{{ route('buyer.products') }}" class="mt-5 rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700">Browse Products</a>
                    </div>
                @endforelse
                <div class="hidden px-6 py-12 text-center" data-conversation-no-results>
                    <p class="text-sm font-semibold text-stone-800">No matching conversations</p>
                    <span class="mt-1 block text-xs text-stone-500">Try searching for another seller.</span>
                </div>
            </div>
        </aside>

        {{-- Chat window --}}
        <main class="{{ $activeConversation ? 'flex' : 'hidden lg:flex' }} min-h-0 flex-col bg-stone-50/70">
            @if ($activeConversation)
                @php
                    $activeSellerInitial = strtoupper(mb_substr($activeConversation['seller_name'], 0, 1));
                    $activeSellerOnline = strtolower($activeConversation['seller_status']) === 'online';
                @endphp
                <header class="flex items-center justify-between gap-4 border-b border-stone-200 bg-white px-4 py-3.5 sm:px-5">
                    <div class="flex min-w-0 items-center gap-3">
                        <a href="{{ route('buyer.messages') }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-stone-500 transition hover:bg-stone-100 lg:hidden" aria-label="Return to conversations">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                        </a>
                        <div class="relative shrink-0">
                            <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-amber-100 text-sm font-bold text-amber-800">
                                @if ($activeConversation['seller_avatar'])
                                    <img src="{{ $activeConversation['seller_avatar'] }}" alt="{{ $activeConversation['seller_name'] }}" class="h-full w-full object-cover">
                                @else
                                    {{ $activeSellerInitial }}
                                @endif
                            </div>
                            @if ($activeSellerOnline)<span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-emerald-500"></span>@endif
                        </div>
                        <div class="min-w-0">
                            <h2 class="truncate text-sm font-semibold text-stone-900">{{ $activeConversation['seller_name'] }}</h2>
                            <p class="mt-0.5 truncate text-[10px] {{ $activeSellerOnline ? 'text-emerald-600' : 'text-stone-400' }}">{{ $activeConversation['seller_status'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button type="button" class="flex h-9 w-9 items-center justify-center rounded-xl text-stone-500 transition hover:bg-stone-100 hover:text-stone-900" aria-label="Search messages">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        </button>
                        <button type="button" class="flex h-9 w-9 items-center justify-center rounded-xl text-stone-500 transition hover:bg-stone-100 hover:text-stone-900" aria-label="Conversation options">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                        </button>
                    </div>
                </header>

                <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-4 py-5 sm:px-6" data-chat-messages>
                    <div class="flex justify-center">
                        <span class="rounded-full border border-stone-200 bg-white px-3 py-1 text-[9px] font-medium text-stone-400 shadow-sm">Today</span>
                    </div>
                    @forelse ($activeConversation['messages'] as $message)
                        <div class="flex {{ $message['is_mine'] ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[82%] sm:max-w-[68%]">
                                <div class="rounded-2xl px-4 py-2.5 text-xs leading-5 shadow-sm {{ $message['is_mine'] ? 'rounded-br-md bg-amber-600 text-white' : 'rounded-bl-md border border-stone-200 bg-white text-stone-700' }}">
                                    {{ $message['body'] }}
                                </div>
                                <time class="mt-1 block px-1 text-[9px] text-stone-400 {{ $message['is_mine'] ? 'text-right' : 'text-left' }}">{{ $message['time'] }}</time>
                            </div>
                        </div>
                    @empty
                        <div class="flex h-full min-h-64 flex-col items-center justify-center text-center">
                            <h3 class="text-sm font-semibold text-stone-800">Start this conversation</h3>
                            <p class="mt-1 text-xs text-stone-500">Send a message to {{ $activeConversation['seller_name'] }}.</p>
                        </div>
                    @endforelse
                </div>

                <footer class="border-t border-stone-200 bg-white p-3 sm:p-4">
                    <form class="flex items-end gap-2" data-message-form>
                        <button type="button" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-stone-500 transition hover:bg-stone-100 hover:text-stone-900" aria-label="Attach a file">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                        </button>
                        <div class="flex min-w-0 flex-1 items-end rounded-2xl border border-stone-200 bg-stone-50 px-3 transition focus-within:border-amber-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-amber-100">
                            <textarea rows="1" placeholder="Write a message..." class="max-h-28 min-h-10 flex-1 resize-none border-0 bg-transparent py-2.5 text-xs leading-5 text-stone-800 outline-none placeholder:text-stone-400 focus:ring-0" data-message-input></textarea>
                            <button type="button" class="mb-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-stone-400 transition hover:bg-stone-100 hover:text-amber-700" aria-label="Add an emoji">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><path d="M9 9h.01M15 9h.01"/></svg>
                            </button>
                        </div>
                        <button type="submit" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-600 text-white transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:bg-stone-300" data-send-message aria-label="Send message" disabled>
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4z"/><path d="M22 2 11 13"/></svg>
                        </button>
                    </form>
                </footer>
            @else
                <div class="flex h-full min-h-[620px] flex-col items-center justify-center px-6 py-12 text-center">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-amber-50 text-amber-700">
                        <svg width="37" height="37" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v11H8l-4 4z"/><path d="M8 9h8"/><path d="M8 13h5"/></svg>
                    </div>
                    <h2 class="mt-5 text-base font-semibold text-stone-900">No conversation selected</h2>
                    <p class="mt-2 max-w-sm text-sm leading-6 text-stone-500">Select a conversation from the list to start messaging.</p>
                </div>
            @endif
        </main>
    </section>
</div>
@endsection
