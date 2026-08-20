@extends('Seller.layouts.app')
@section('title', 'Messages — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/customers.css') @endpush

@section('content')
<div class="messages-shell">
    <aside class="conversation-panel">
        <div class="p-4"><p class="page-eyebrow">INBOX</p><h1 class="mt-1 text-xl font-black">Messages</h1><label class="search-field mt-4"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input placeholder="Search conversations"></label></div>
        <div class="conversation-list">
            @foreach([
                ['Juan Dela Cruz','Order LH-20260820-0231','Can you confirm the natural color?','8:42 PM',true],
                ['Miguel Santos · Courier','Pickup JT-PU-2841','I’m 10 minutes away.','7:55 PM',true],
                ['Anne Reyes','Rattan Tote Bag','Thank you!','6:16 PM',false],
                ['LIKHAE Support','Ticket #SUP-0184','We reviewed your request.','Yesterday',false],
            ] as $c)
            <button class="conversation-item {{ $loop->first?'is-active':'' }}"><span class="avatar">{{ strtoupper(substr($c[0],0,1)) }}</span><span class="min-w-0 flex-1"><strong>{{ $c[0] }}</strong><small>{{ $c[1] }}</small><p>{{ $c[2] }}</p></span><span class="text-right"><time>{{ $c[3] }}</time>@if($c[4])<i></i>@endif</span></button>
            @endforeach
        </div>
    </aside>

    <section class="chat-panel">
        <header class="chat-header"><div><strong>Juan Dela Cruz</strong><span>Buyer · Online</span></div><a href="{{ url('/seller/orders/LH-20260820-0231') }}" class="btn-secondary">View Order</a></header>
        <div class="chat-body">
            <div class="message-row incoming"><div><p>Hello! Can you confirm if the natural color is the same as the product photo?</p><span>8:38 PM</span></div></div>
            <div class="message-row outgoing"><div><p>Yes. The current Natural variation matches the product photo. We can also send a quick photo before packing.</p><span>8:40 PM</span></div></div>
            <div class="message-row incoming"><div><p>Great, thank you!</p><span>8:42 PM</span></div></div>
        </div>
        <form class="chat-composer"><button type="button" class="icon-button">+</button><textarea placeholder="Type a message..."></textarea><button class="btn-primary">Send</button></form>
    </section>

    <aside class="context-panel">
        <p class="panel-eyebrow">RELATED ORDER</p><strong class="mt-2 block">LH-20260820-0231</strong><x-seller.status-badge status="To Prepare" class="mt-2"/>
        <div class="mt-5 border-t border-[#E5E0D9] pt-4"><span class="meta-label">PRODUCT</span><strong class="mt-1 block text-xs">Handwoven Rattan Tote Bag</strong><p class="mt-1 text-[10px] text-[#96918B]">Natural · Large · Qty 1</p></div>
        <a class="btn-secondary mt-5 w-full" href="{{ url('/seller/orders/LH-20260820-0231') }}">Open Order</a>
    </aside>
</div>
@endsection
