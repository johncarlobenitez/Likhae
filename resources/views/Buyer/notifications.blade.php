@extends('layouts.buyer')

@section('title', 'Notifications')
@section('active', 'account')
@section('subtitle', 'Order, message, reward, and account updates.')

@section('content')
@php
    $type = request('type', 'all');
    $notifications = collect([
        ['id'=>1,'type'=>'orders','title'=>'Your parcel was delivered','message'=>'Order #LK-2076 was delivered. Confirm receipt now or it will be completed automatically after 3 days.','time'=>'18 minutes ago','unread'=>true,'url'=>route('buyer.orders.show',['id'=>'LK-2076'])],
        ['id'=>2,'type'=>'messages','title'=>'New message from Metro Finds PH','message'=>'Your order has been packed and will be handed to the courier today.','time'=>'1 hour ago','unread'=>true,'url'=>route('buyer.messages',['seller'=>'metro-finds-ph'])],
        ['id'=>3,'type'=>'rewards','title'=>'A voucher is ready to use','message'=>'Use LIKHAE100 for ₱100 off an eligible purchase before September 30.','time'=>'3 hours ago','unread'=>true,'url'=>route('buyer.rewards',['tab'=>'vouchers'])],
        ['id'=>4,'type'=>'orders','title'=>'Payment confirmed','message'=>'Payment for order #LK-2079 was confirmed. The seller is preparing your parcel.','time'=>'Yesterday','unread'=>false,'url'=>route('buyer.orders.show',['id'=>'LK-2079'])],
        ['id'=>5,'type'=>'account','title'=>'Security settings updated','message'=>'Your account security preferences were saved successfully.','time'=>'Sep 1, 2026','unread'=>false,'url'=>route('buyer.account',['tab'=>'security'])],
    ]);
    $visible = $type === 'all' ? $notifications : $notifications->where('type', $type);
    $types = ['all'=>'All','orders'=>'Orders','messages'=>'Messages','rewards'=>'Rewards','account'=>'Account'];
    $icons = ['orders'=>'□','messages'=>'✉','rewards'=>'%','account'=>'○'];
@endphp

<div class="lk-page-narrow">
    <div class="lk-page-title"><div><span class="lk-kicker">Notification Center</span><h1>Notifications</h1><p>Important activity from your LIKHAE account.</p></div><button type="button" class="lk-btn lk-btn-light" data-mark-all-read>Mark all as read</button></div>

    <nav class="flex gap-1 overflow-x-auto rounded-xl border border-stone-200 bg-white p-1 shadow-sm" aria-label="Notification filters">@foreach($types as $key=>$label)<a href="{{ route('buyer.notifications',['type'=>$key]) }}" class="whitespace-nowrap rounded-lg px-3.5 py-2.5 text-xs font-semibold {{ $type === $key ? 'bg-red-900 text-white' : 'text-stone-600 hover:bg-stone-50' }}">{{ $label }}@if($key==='all') <span class="ml-1 opacity-70">{{ $notifications->where('unread',true)->count() }}</span>@endif</a>@endforeach</nav>

    <section class="mt-5 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm" data-notification-list>
        @forelse($visible as $notification)
            <a href="{{ $notification['url'] }}" class="flex gap-3 border-b border-stone-100 p-4 transition last:border-0 hover:bg-stone-50 sm:p-5 {{ $notification['unread'] ? 'bg-red-50/40' : '' }}" data-notification-item>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $notification['unread'] ? 'bg-red-900 text-white' : 'bg-stone-100 text-stone-500' }} text-sm font-bold">{{ $icons[$notification['type']] ?? '•' }}</span>
                <span class="min-w-0 flex-1"><span class="flex flex-wrap items-center justify-between gap-2"><strong class="text-sm text-stone-900">{{ $notification['title'] }}</strong><time class="text-[10px] text-stone-400">{{ $notification['time'] }}</time></span><span class="mt-1 block text-xs leading-5 text-stone-500">{{ $notification['message'] }}</span></span>
                @if($notification['unread'])<i class="mt-2 h-2 w-2 shrink-0 rounded-full bg-red-800" aria-label="Unread"></i>@endif
            </a>
        @empty
            <div class="p-5"><x-buyer.empty-state title="No notifications" message="There are no updates in this section." /></div>
        @endforelse
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('[data-mark-all-read]')?.addEventListener('click', () => {
        document.querySelectorAll('[data-notification-item]').forEach((item) => {
            item.classList.remove('bg-red-50/40');
            item.querySelector('[aria-label="Unread"]')?.remove();
        });
        window.lkBuyerToast?.('All visible notifications marked as read.');
    });
});
</script>
@endpush
