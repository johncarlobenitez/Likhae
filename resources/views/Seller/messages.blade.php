@extends('layouts.seller')

@php $currentMode = $mode ?? 'messages'; @endphp

@section('title', $currentMode === 'reviews' ? 'Reviews' : 'Messages')
@section('active', $currentMode === 'reviews' ? 'reviews' : 'messages')
@section('subtitle', $currentMode === 'reviews' ? 'Monitor buyer feedback and protect your store reputation.' : 'Answer buyer questions and resolve order concerns quickly.')

@section('content')
<div class="sl-page">
    @if ($currentMode === 'reviews')
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Customer Service</span><h2>Buyer Reviews</h2><p>Respond professionally and use feedback to improve your products.</p></div><button type="button" class="sl-btn sl-btn-ghost" data-demo-action="Reviews exported.">Export Reviews</button></div>
        <section class="sl-review-summary">
            <div class="sl-card sl-rating-overview"><div><strong>4.8</strong><span>★★★★★</span><small>Based on 1,248 reviews</small></div><div class="sl-rating-bars">@foreach ([5 => 78, 4 => 16, 3 => 4, 2 => 1, 1 => 1] as $stars => $percent)<p><span>{{ $stars }} star</span><i><b style="width: {{ $percent }}%"></b></i><small>{{ $percent }}%</small></p>@endforeach</div></div>
            <div class="sl-mini-stats sl-card"><div><span>New Reviews</span><strong>18</strong></div><div><span>Awaiting Reply</span><strong>7</strong></div><div><span>With Photos</span><strong>64%</strong></div><div><span>Positive</span><strong>94%</strong></div></div>
        </section>
        <section class="sl-card">
            <div class="sl-table-toolbar"><div class="sl-search-input"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" placeholder="Search reviews"></div><select class="sl-select"><option>All ratings</option><option>5 stars</option><option>4 stars</option><option>3 stars and below</option></select><select class="sl-select"><option>All replies</option><option>Awaiting reply</option><option>Replied</option></select></div>
            <div class="sl-review-list">
                @foreach ([
                    ['Angela Cruz', '27-inch Borderless Monitor', 5, 'The monitor arrived safely and the colors look great. Seller packed it very well and shipped fast.', '2 hours ago', false],
                    ['Marco Reyes', 'Mechanical Keyboard 87 Keys', 5, 'Solid build and responsive keys. The seller also answered my questions before I ordered.', 'Yesterday', true],
                    ['Sarah Lim', 'Wireless Gaming Mouse', 4, 'Good mouse for the price. Delivery was quick, but I hope more color options become available.', 'Sep 02, 2026', false],
                ] as $review)
                    <article class="sl-review-item">
                        <span class="sl-avatar">{{ mb_substr($review[0], 0, 1) }}</span>
                        <div class="sl-review-content"><div class="sl-review-head"><div><strong>{{ $review[0] }}</strong><span>{{ $review[1] }}</span></div><small>{{ $review[4] }}</small></div><div class="sl-review-stars">{{ str_repeat('★', $review[2]) }}{{ str_repeat('☆', 5 - $review[2]) }}</div><p>{{ $review[3] }}</p>@if ($review[5])<div class="sl-seller-reply"><strong>Your reply</strong><p>Thank you for your feedback! We are glad you are enjoying your order.</p></div>@else<div class="sl-review-reply"><textarea rows="2" placeholder="Write a professional public reply..."></textarea><button type="button" class="sl-btn sl-btn-primary sl-btn-sm" data-demo-action="Review reply published.">Reply</button></div>@endif</div>
                    </article>
                @endforeach
            </div>
        </section>
    @else
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Customer Service</span><h2>Buyer Messages</h2><p>Keep product questions and order conversations in one workspace.</p></div><span class="sl-response-chip"><i></i> Average response time: 8 min</span></div>
        <section class="sl-messages-layout" data-messages>
            <aside class="sl-conversation-panel">
                <div class="sl-conversation-head"><div><h3>Conversations</h3><span>3 unread</span></div><button type="button" class="sl-icon-btn" data-demo-action="Starting a new conversation." aria-label="New conversation"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></button></div>
                <div class="sl-conversation-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" placeholder="Search buyer or order" data-conversation-search></div>
                <div class="sl-conversation-filters"><button class="is-active" type="button" data-conversation-filter="all">All</button><button type="button" data-conversation-filter="unread">Unread</button><button type="button" data-conversation-filter="orders">Orders</button></div>
                <div class="sl-conversation-list">
                    @foreach ([
                        ['angela', 'Angela Cruz', 'Is the monitor compatible with a MacBook?', '2m', 'AC', '2', 'Order #10001', true],
                        ['marco', 'Marco Reyes', 'Thank you, I received the waybill update.', '18m', 'MR', '', 'Order #10002', false],
                        ['sarah', 'Sarah Lim', 'Can I change the delivery address?', '1h', 'SL', '1', 'Order #10003', true],
                        ['daniel', 'Daniel Tan', 'The headphones sound great!', 'Yesterday', 'DT', '', 'Order #10004', false],
                        ['patricia', 'Patricia Go', 'Do you have this lamp in black?', 'Sep 02', 'PG', '', 'Product question', false],
                    ] as $conversation)
                        <button type="button" class="sl-conversation {{ $loop->first ? 'is-active' : '' }}" data-conversation data-name="{{ $conversation[1] }}" data-key="{{ $conversation[0] }}" data-search="{{ mb_strtolower($conversation[1].' '.$conversation[6]) }}" data-unread="{{ $conversation[7] ? 'true' : 'false' }}" data-order="{{ str_contains($conversation[6], 'Order') ? 'true' : 'false' }}">
                            <span class="sl-avatar">{{ $conversation[4] }}</span><span class="sl-conversation-copy"><span><strong>{{ $conversation[1] }}</strong><small>{{ $conversation[3] }}</small></span><em>{{ $conversation[6] }}</em><p>{{ $conversation[2] }}</p></span>@if ($conversation[5])<b class="sl-unread">{{ $conversation[5] }}</b>@endif
                        </button>
                    @endforeach
                </div>
            </aside>
            <main class="sl-chat-panel">
                <header class="sl-chat-head"><div class="sl-chat-person"><span class="sl-avatar" data-chat-avatar>AC</span><div><strong data-chat-name>Angela Cruz</strong><small><i></i> Active now · <span data-chat-order>Order #10001</span></small></div></div><div><a href="{{ route('seller.orders', ['mode' => 'show', 'order' => '10001']) }}" class="sl-btn sl-btn-soft sl-btn-sm">View Order</a><button type="button" class="sl-icon-btn" data-demo-action="Conversation options opened." aria-label="Conversation options">•••</button></div></header>
                <div class="sl-chat-context"><div class="sl-order-thumb">M</div><div><small>ORDER REFERENCE</small><strong data-chat-product>27-inch Borderless Monitor</strong><span>₱12,990.00 · To Process</span></div><a href="{{ route('seller.orders', ['mode' => 'show', 'order' => '10001']) }}">View details →</a></div>
                <div class="sl-chat-messages" data-chat-messages>
                    <div class="sl-message-day">Today</div>
                    <div class="sl-message is-buyer"><p>Hello! Is the 27-inch monitor compatible with a MacBook using USB-C?</p><small>9:18 AM</small></div>
                    <div class="sl-message is-seller"><p>Hi Angela! Yes, it works with a MacBook. You will need a USB-C to HDMI adapter because the monitor uses HDMI input.</p><small>9:23 AM · Seen</small></div>
                    <div class="sl-message is-buyer"><p>Great, thank you. Does the package include an HDMI cable?</p><small>9:24 AM</small></div>
                </div>
                <form class="sl-chat-composer" data-chat-form><div class="sl-chat-tools"><button type="button" aria-label="Attach file" data-demo-action="File attachment opened."><svg viewBox="0 0 24 24"><path d="m20 12-8 8a6 6 0 0 1-8-8l9-9a4 4 0 0 1 6 6l-9 9a2 2 0 0 1-3-3l8-8"/></svg></button><button type="button" aria-label="Add quick reply" data-demo-action="Quick replies opened."><svg viewBox="0 0 24 24"><path d="M4 5h16v12H8l-4 4zM8 9h8M8 13h5"/></svg></button></div><textarea rows="1" placeholder="Write a message..." aria-label="Message" data-chat-input></textarea><button type="submit" class="sl-chat-send" aria-label="Send message"><svg viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4zM22 2 11 13"/></svg></button></form>
            </main>
        </section>
    @endif
</div>
@endsection
