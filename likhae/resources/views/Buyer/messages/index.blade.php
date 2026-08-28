<x-buyer.layout title="Messages"><div class="b-page"><div class="b-container">
<x-buyer.breadcrumbs :items="[['label'=>'Messages','url'=>null]]" />
<section class="b-card b-inbox" data-buyer-messages><aside class="b-conversations"><div class="b-inbox-title">Messages</div>
@foreach([['Metro Finds PH','Your order has been shipped.','10:32 AM',true],['Urban Carry Co.','The brown variant is available.','Yesterday',false],['Stride PH','Thank you for your order!','Aug 26',false]] as $i=>[$name,$message,$time,$unread])
<div class="b-conversation {{ $i===0?'is-active':'' }}" data-conversation data-name="{{ $name }}"><div class="b-avatar">{{ strtoupper(substr($name,0,1)) }}</div><div class="b-conversation-copy"><div style="display:flex;justify-content:space-between"><strong>{{ $name }}</strong><small class="b-muted">{{ $time }}</small></div><span>{{ $message }}</span></div>@if($unread)<span class="b-notification-dot"></span>@endif</div>@endforeach
</aside>
<div class="b-chat"><header class="b-chat-head"><div style="display:flex;gap:10px;align-items:center"><button class="b-icon-button" data-show-conversations>←</button><div><strong data-chat-name>Metro Finds PH</strong><div class="b-muted" style="font-size:11px">Usually replies within an hour</div></div></div><a class="b-btn b-btn-secondary" href="{{ route('buyer.products') }}">Visit Store</a></header>
<div class="b-chat-messages" data-chat-body><div class="b-bubble-row"><div class="b-bubble">Hi! Your order has been shipped.<time>10:28 AM</time></div></div><div class="b-bubble-row mine"><div class="b-bubble">Thank you! I’ll keep an eye on tracking.<time>10:30 AM</time></div></div></div>
<form class="b-chat-compose" data-chat-form><input class="b-input" placeholder="Type a message..." data-chat-input><button class="b-btn b-btn-primary" type="submit">Send</button></form></div></section>
</div></div></x-buyer.layout>
