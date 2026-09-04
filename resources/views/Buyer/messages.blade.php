
@php
    $sellers = collect(view()->shared('buyerProducts', []))->map(function ($p) {
        $name = data_get($p, 'seller', 'LIKHAE Seller');
        $slug = data_get($p, 'seller_slug', \Illuminate\Support\Str::slug($name));
        return [
            'name' => $name,
            'slug' => $slug,
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=7f1d1d&color=fff',
            'product' => data_get($p, 'name'),
            'last_message' => 'Hello! Thanks for reaching out. How can we assist you today?',
            'time' => 'Just now',
            'unread' => false,
        ];
    })->unique('slug')->values();

    $selectedSlug = request('seller') ?? (request('product') ? collect(view()->shared('buyerProducts', []))->firstWhere('slug', request('product'))['seller_slug'] ?? null : null);
    if (!$selectedSlug && $sellers->isNotEmpty()) {
        $selectedSlug = $sellers->first()['slug'];
    }

    $activeSeller = $sellers->firstWhere('slug', $selectedSlug);
    if (!$activeSeller && $selectedSlug) {
        $activeSeller = [
            'name' => ucwords(str_replace('-', ' ', $selectedSlug)),
            'slug' => $selectedSlug,
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($selectedSlug) . '&background=7f1d1d&color=fff',
            'last_message' => 'Hello! How can we assist you today?',
            'time' => 'Just now',
            'unread' => false,
        ];
        $sellers->prepend($activeSeller);
    }
@endphp

@extends('layouts.buyer')
@section('title', 'Messages')
@section('active', 'messages')
@section('content')

<div class="lk-page">
    <div class="lk-page-title">
        <div>
            <span class="lk-kicker">Communication</span>
            <h1>Messages</h1>
            <p>Keep conversations with local sellers in one place.</p>
        </div>
    </div>

    <section class="grid min-h-[580px] overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm lg:grid-cols-[320px_minmax(0,1fr)]">
        <!-- Sidebar Conversations -->
        <aside class="flex flex-col border-b border-stone-200 lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between border-b border-stone-100 p-4">
                <h2 class="text-sm font-bold text-stone-900">Conversations ({{ $sellers->count() }})</h2>
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-stone-100">
                @forelse($sellers as $item)
                    <a href="{{ route('buyer.messages', ['seller' => $item['slug']]) }}"
                       class="flex items-start gap-3 p-4 transition-colors hover:bg-stone-50 {{ $selectedSlug === $item['slug'] ? 'bg-red-50/70' : '' }}">
                        <img class="h-10 w-10 flex-shrink-0 rounded-full object-cover border border-stone-200" src="{{ $item['avatar'] }}" alt="{{ $item['name'] }}">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-1">
                                <strong class="truncate text-xs font-semibold text-stone-900">{{ $item['name'] }}</strong>
                                <span class="text-[10px] text-stone-400">{{ $item['time'] }}</span>
                            </div>
                            <p class="truncate text-xs text-stone-500 mt-0.5">{{ $item['last_message'] }}</p>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-xs text-stone-400">
                        No conversations yet
                    </div>
                @endforelse
            </div>
        </aside>

        <!-- Chat Area -->
        <main class="flex flex-col min-h-[480px]">
            @if($activeSeller)
                <!-- Chat Header -->
                <div class="flex items-center justify-between border-b border-stone-100 p-4 bg-stone-50/50">
                    <div class="flex items-center gap-3">
                        <img class="h-10 w-10 rounded-full object-cover border border-stone-200" src="{{ $activeSeller['avatar'] }}" alt="{{ $activeSeller['name'] }}">
                        <div>
                            <strong class="block text-sm font-bold text-stone-900">{{ $activeSeller['name'] }}</strong>
                            <span class="inline-flex items-center gap-1.5 text-[11px] text-emerald-600 font-medium">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Verified Seller · Online
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('buyer.shop', ['seller' => $activeSeller['slug']]) }}" class="lk-btn lk-btn-light !min-h-[34px] !px-3 !text-xs">
                        View Store
                    </a>
                </div>

                <!-- Chat Messages Stream -->
                <div id="chatMessages" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 bg-stone-50/30">
                    <div class="flex justify-center">
                        <span class="rounded-full bg-stone-100 px-3 py-1 text-[11px] font-medium text-stone-500">Today</span>
                    </div>

                    <!-- Seller message -->
                    <div class="flex items-start gap-2.5 max-w-[80%]">
                        <img class="h-7 w-7 rounded-full object-cover flex-shrink-0" src="{{ $activeSeller['avatar'] }}" alt="{{ $activeSeller['name'] }}">
                        <div>
                            <div class="rounded-2xl rounded-tl-sm bg-white p-3.5 border border-stone-200 text-xs text-stone-800 shadow-sm leading-relaxed">
                                Hello! Welcome to <strong>{{ $activeSeller['name'] }}</strong>. Feel free to ask about our stock, shipping timelines, or product inquiries.
                            </div>
                            <span class="text-[10px] text-stone-400 mt-1 block">Just now</span>
                        </div>
                    </div>

                    @if(request('product'))
                        @php
                            $refProduct = collect(view()->shared('buyerProducts', []))->firstWhere('slug', request('product'));
                        @endphp
                        @if($refProduct)
                            <!-- Product inquiry preview card -->
                            <div class="flex justify-end">
                                <div class="max-w-[80%] rounded-2xl bg-red-900 text-white p-3.5 shadow-sm text-xs space-y-2">
                                    <p class="font-medium">Inquiry regarding product:</p>
                                    <div class="flex items-center gap-2 bg-red-950/50 p-2 rounded-xl border border-red-800">
                                        <img src="{{ data_get($refProduct, 'image') }}" alt="{{ data_get($refProduct, 'name') }}" class="h-10 w-10 rounded-lg object-cover">
                                        <div class="min-w-0">
                                            <strong class="block truncate font-semibold text-white">{{ data_get($refProduct, 'name') }}</strong>
                                            <span class="text-red-200 text-[11px]">₱{{ number_format(data_get($refProduct, 'price', 0), 2) }}</span>
                                        </div>
                                    </div>
                                    <p>Hi, is this item currently available for immediate delivery?</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Message Input -->
                <form id="chatForm" class="flex items-center gap-2 border-t border-stone-200 p-3.5 bg-white" onsubmit="event.preventDefault(); const inp = this.querySelector('input'); if(!inp.value.trim()) return; const list = document.getElementById('chatMessages'); const d = document.createElement('div'); d.className = 'flex justify-end'; d.innerHTML = '<div class=\'max-w-[80%] rounded-2xl rounded-tr-sm bg-red-900 text-white p-3.5 shadow-sm text-xs leading-relaxed\'>' + inp.value.replace(/</g, '&lt;') + '<span class=\'text-[10px] text-red-200 block text-right mt-1\'>Just now</span></div>'; list.appendChild(d); inp.value = ''; list.scrollTop = list.scrollHeight; if(window.lkBuyerToast) window.lkBuyerToast('Message sent to seller.');">
                    <input type="text" placeholder="Type your message to {{ $activeSeller['name'] }}..." class="flex-1 rounded-xl border border-stone-300 px-4 py-2.5 text-xs outline-none focus:border-red-800 transition-colors">
                    <button type="submit" class="lk-btn lk-btn-red !min-h-[38px] !px-4 !text-xs">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        <span>Send</span>
                    </button>
                </form>
            @else
                <div class="flex flex-1 flex-col items-center justify-center p-8 text-center text-stone-400">
                    <svg viewBox="0 0 24 24" width="56" height="56" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-3">
                        <path d="M4 5h16v11H8l-4 4z"/>
                        <path d="M8 9h8"/><path d="M8 13h5"/>
                    </svg>
                    <h3 class="text-base font-semibold text-stone-800">No conversation selected</h3>
                    <p class="text-xs text-stone-500 mt-1">Select a conversation from the left to start chatting.</p>
                </div>
            @endif
        </main>
    </section>
</div>

@endsection
