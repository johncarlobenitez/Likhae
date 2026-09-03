@extends('layouts.buyer')

@section('title', 'Notifications — LIKHAE')
@section('active', 'notifications')

@section('content')
@php
    $notifications = $notifications ?? collect([
        [
            'id'      => 1,
            'type'    => 'order',
            'title'   => 'Order Shipped',
            'body'    => 'Your order #ORD-00123 (Classic Everyday Backpack) has been shipped and is on its way.',
            'time'    => '10 minutes ago',
            'read'    => false,
            'icon'    => 'order',
        ],
        [
            'id'      => 2,
            'type'    => 'promo',
            'title'   => 'Flash Deal — Up to 30% Off',
            'body'    => 'Limited-time flash deals are live now. Shop before they run out!',
            'time'    => '1 hour ago',
            'read'    => false,
            'icon'    => 'promo',
        ],
        [
            'id'      => 3,
            'type'    => 'order',
            'title'   => 'Order Delivered',
            'body'    => 'Your order #ORD-00118 (Premium Wireless Headphones) has been delivered. Enjoy your purchase!',
            'time'    => 'Yesterday',
            'read'    => true,
            'icon'    => 'order',
        ],
        [
            'id'      => 4,
            'type'    => 'review',
            'title'   => 'Leave a Review',
            'body'    => 'How was your Lightweight Running Shoes? Share your experience to help other buyers.',
            'time'    => '2 days ago',
            'read'    => true,
            'icon'    => 'review',
        ],
        [
            'id'      => 5,
            'type'    => 'voucher',
            'title'   => 'New Voucher Available',
            'body'    => 'You have a new ₱50 voucher waiting. Use it on your next order before it expires.',
            'time'    => '3 days ago',
            'read'    => true,
            'icon'    => 'voucher',
        ],
    ]);

    $unreadCount = collect($notifications)->where('read', false)->count();

    $icons = [
        'order'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h12l2 4v16H4V6z"/><path d="M6 6h12"/><path d="M8 11h8"/><path d="M8 15h6"/></svg>',
        'promo'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7"/><path d="M2 7h20v5H2z"/><path d="M12 7v14"/></svg>',
        'review'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
        'voucher' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6"/><path d="M2 12h20"/><path d="M2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6"/><path d="M12 12v4"/></svg>',
    ];

    $iconColors = [
        'order'   => 'bg-blue-50 text-blue-600',
        'promo'   => 'bg-amber-50 text-amber-600',
        'review'  => 'bg-yellow-50 text-yellow-600',
        'voucher' => 'bg-green-50 text-green-600',
    ];
@endphp

<div class="lk-page">
    <div class="lk-page-title">
        <div>
            <span class="lk-kicker">Updates</span>
            <h1 style="font-size:1.4rem;font-weight:700;margin:4px 0 0;color:var(--lk-ink)">Notifications</h1>
        </div>
        @if ($unreadCount > 0)
            <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-800">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                {{ $unreadCount }} unread
            </span>
        @endif
    </div>

    <div class="lk-card" style="overflow:hidden;padding:0">
        @forelse (collect($notifications) as $notification)
            <div class="flex gap-4 border-b border-stone-100 px-5 py-4 transition last:border-0 {{ !$notification['read'] ? 'bg-amber-50/60' : 'bg-white hover:bg-stone-50' }}">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $iconColors[$notification['icon']] ?? 'bg-stone-100 text-stone-500' }}">
                    {!! $icons[$notification['icon']] ?? $icons['order'] !!}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-xs font-semibold text-stone-900">
                            {{ $notification['title'] }}
                            @if (!$notification['read'])
                                <span class="ml-1.5 inline-block h-2 w-2 rounded-full bg-amber-500 align-middle"></span>
                            @endif
                        </p>
                        <time class="shrink-0 text-[10px] text-stone-400">{{ $notification['time'] }}</time>
                    </div>
                    <p class="mt-1 text-[11px] leading-5 text-stone-500">{{ $notification['body'] }}</p>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-semibold text-stone-900">No notifications yet</h3>
                <p class="mt-1 text-xs text-stone-500">You're all caught up! Check back later for updates.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
