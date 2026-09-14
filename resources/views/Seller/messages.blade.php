@extends('layouts.seller')

@php $currentMode = $mode ?? 'messages'; @endphp

@section('title', $currentMode === 'reviews' ? 'Reviews' : 'Messages')
@section('active', $currentMode === 'reviews' ? 'reviews' : 'messages')
@section('subtitle', $currentMode === 'reviews' ? 'Monitor buyer feedback and protect your store reputation.' : 'Answer buyer questions and resolve order concerns quickly.')

@section('content')
<div class="sl-page">
    @if ($currentMode === 'reviews')
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Customer Service</span><h2>Buyer Reviews</h2><p>Reply to feedback and export review records.</p></div><a href="{{ route('seller.reviews.export') }}" class="sl-btn sl-btn-ghost">Export Reviews</a></div>
        <section class="sl-review-summary">
            <div class="sl-card sl-rating-overview"><div><strong>{{ number_format($reviewStats['average'],1) }}</strong><span>★★★★★</span><small>Based on {{ number_format($reviewStats['count']) }} reviews</small></div><div class="sl-rating-bars">@foreach($ratingBars as $stars=>$percent)<p><span>{{ $stars }} star</span><i><b style="width:{{ $percent }}%"></b></i><small>{{ $percent }}%</small></p>@endforeach</div></div>
            <div class="sl-mini-stats sl-card"><div><span>New Reviews</span><strong>{{ $reviewStats['new'] }}</strong></div><div><span>Awaiting Reply</span><strong>{{ $reviewStats['awaiting'] }}</strong></div><div><span>With Photos</span><strong>{{ $reviewStats['with_photos'] }}%</strong></div><div><span>Positive</span><strong>{{ $reviewStats['positive'] }}%</strong></div></div>
        </section>
        <section class="sl-card"><div class="sl-review-list">
            @forelse($reviews as $review)
                <article class="sl-review-item"><span class="sl-avatar">{{ mb_strtoupper(mb_substr($review->buyer?->name ?? 'B',0,1)) }}</span><div class="sl-review-content"><div class="sl-review-head"><div><strong>{{ $review->buyer?->name }}</strong><span>{{ $review->product?->name }}</span></div><small>{{ $review->created_at?->diffForHumans() }}</small></div><div class="sl-review-stars">{{ str_repeat('★',$review->rating) }}{{ str_repeat('☆',5-$review->rating) }}</div><p>{{ $review->body }}</p>
                @if($review->reply)<div class="sl-seller-reply"><strong>Your reply</strong><p>{{ $review->reply }}</p></div>@else<form class="sl-review-reply" method="POST" action="{{ route('seller.reviews.reply',$review) }}">@csrf<textarea name="reply" rows="2" placeholder="Write a professional public reply..." required></textarea><button type="submit" class="sl-btn sl-btn-primary sl-btn-sm">Reply</button></form>@endif
                </div></article>
            @empty<div class="sl-empty-state"><h3>No reviews yet</h3><p>Buyer reviews will appear here.</p></div>@endforelse
        </div></section>
    @else
        <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Customer Service</span><h2>Buyer Messages</h2><p>Conversations are now stored in the database.</p></div><span class="sl-response-chip"><i></i>{{ $conversationRows->sum('unread') }} unread</span></div>
        <section class="sl-messages-layout" data-messages>
            <aside class="sl-conversation-panel">
                <div class="sl-conversation-head"><div><h3>Conversations</h3><span>{{ $conversationRows->sum('unread') }} unread</span></div></div>
                <div class="sl-conversation-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" placeholder="Search buyer" data-conversation-search></div>
                <div class="sl-conversation-list">
                    @foreach($conversationRows as $row)
                        @php $buyer=$row['buyer']; $last=$row['last']; @endphp
                        <a href="{{ route('seller.messages',['buyer'=>$buyer->id]) }}" class="sl-conversation {{ optional($conversationBuyer)->id===$buyer->id?'is-active':'' }}" data-conversation data-search="{{ mb_strtolower($buyer->name) }}" data-unread="{{ $row['unread']?'true':'false' }}" data-order="{{ $last?->order_id?'true':'false' }}" style="text-decoration:none">
                            <span class="sl-avatar">{{ collect(explode(' ',$buyer->name))->filter()->take(2)->map(fn($w)=>mb_strtoupper(mb_substr($w,0,1)))->implode('') }}</span><span class="sl-conversation-copy"><span><strong>{{ $buyer->name }}</strong><small>{{ $last?->created_at?->diffForHumans() }}</small></span><em>{{ $last?->order ? 'Order #'.$last->order->order_number : 'Buyer conversation' }}</em><p>{{ $last?->body ?: 'Start a conversation' }}</p></span>@if($row['unread'])<b class="sl-unread">{{ $row['unread'] }}</b>@endif
                        </a>
                    @endforeach
                </div>
            </aside>
            <main class="sl-chat-panel">
                @if($conversationBuyer)
                    <header class="sl-chat-head"><div class="sl-chat-person"><span class="sl-avatar">{{ mb_strtoupper(mb_substr($conversationBuyer->name,0,1)) }}</span><div><strong>{{ $conversationBuyer->name }}</strong><small><i></i> Buyer conversation</small></div></div></header>
                    <div class="sl-chat-messages" data-chat-messages><div class="sl-message-day">Conversation</div>@forelse($chatMessages as $message)<div class="sl-message {{ $message->sender_id===$seller->id?'is-seller':'is-buyer' }}"><p>{{ $message->body }}</p><small>{{ $message->created_at?->format('M d · g:i A') }}{{ $message->sender_id===$seller->id && $message->read_at?' · Seen':'' }}</small></div>@empty<div class="sl-empty-state"><h3>No messages yet</h3><p>Send the first message to this buyer.</p></div>@endforelse</div>
                    <form class="sl-chat-composer" method="POST" action="{{ route('seller.messages.send') }}">@csrf<input type="hidden" name="recipient_id" value="{{ $conversationBuyer->id }}"><textarea name="body" rows="1" placeholder="Write a message..." required></textarea><button type="submit" class="sl-chat-send" aria-label="Send message"><svg viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4zM22 2 11 13"/></svg></button></form>
                @else
                    <div class="sl-empty-state"><h3>No buyers available</h3><p>Buyer conversations will appear here.</p></div>
                @endif
            </main>
        </section>
    @endif
</div>
@endsection
