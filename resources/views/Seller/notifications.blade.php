@extends('layouts.seller')

@section('title', 'Notifications')
@section('active', 'notifications')
@section('subtitle', 'Review operational alerts, account updates, and marketplace announcements.')

@section('content')
<div class="sl-page sl-page-narrow">
    <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Activity Center</span><h2>Notifications</h2><p>Prioritize updates that require seller action.</p></div><button type="button" class="sl-btn sl-btn-ghost" data-mark-all-read>Mark All as Read</button></div>
    <section class="sl-notification-layout">
        <nav class="sl-notification-filter sl-card"><button type="button" class="is-active" data-notification-filter="all">All <span>8</span></button><button type="button" data-notification-filter="orders">Orders <span>3</span></button><button type="button" data-notification-filter="inventory">Inventory <span>2</span></button><button type="button" data-notification-filter="finance">Finance <span>1</span></button><button type="button" data-notification-filter="system">System <span>2</span></button></nav>
        <div class="sl-notification-list" data-notification-list>
            @foreach ([
                ['orders','New order received','#10001 from Angela Cruz is ready for review.','2 minutes ago','OR',true,route('seller.orders', ['mode'=>'show','order'=>'10001'])],
                ['inventory','Low stock alert','Mechanical Keyboard has only 3 units remaining.','18 minutes ago','ST',true,route('seller.products', ['mode'=>'inventory'])],
                ['orders','Pickup request accepted','Juan Rider will collect Order #10003 today between 10AM and 12PM.','45 minutes ago','PK',true,route('seller.logistics', ['view'=>'pickups'])],
                ['finance','Payout is being processed','Your ₱38,450 payout is scheduled for September 8.','2 hours ago','₱',false,route('seller.finance', ['tab'=>'payouts'])],
                ['system','9.9 campaign invitation','Twelve of your products are eligible for the Local Finds Festival.','4 hours ago','MK',false,route('seller.marketing', ['tab'=>'promotions'])],
                ['orders','Order delivered','#10005 was delivered and confirmed by the buyer.','Yesterday','DL',false,route('seller.orders', ['status'=>'completed'])],
                ['inventory','Product automatically archived','7-in-1 USB-C Hub reached zero stock and was archived.','Yesterday','ST',false,route('seller.products', ['mode'=>'inventory'])],
                ['system','Security check completed','No unusual activity was detected on your seller account.','Sep 02, 2026','SC',false,route('seller.account', ['tab'=>'security'])],
            ] as $notification)
                <a href="{{ $notification[6] }}" class="sl-notification-item {{ $notification[5] ? 'is-unread' : '' }}" data-notification data-type="{{ $notification[0] }}"><span class="sl-notification-icon is-{{ $notification[0] }}">{{ $notification[4] }}</span><div><strong>{{ $notification[1] }}</strong><p>{{ $notification[2] }}</p><small>{{ $notification[3] }}</small></div>@if ($notification[5])<i></i>@endif<span class="sl-notification-arrow">›</span></a>
            @endforeach
            <div class="sl-empty-state" data-notification-empty hidden><h3>No notifications in this category</h3><p>New seller updates will appear here.</p></div>
        </div>
    </section>
</div>
@endsection
