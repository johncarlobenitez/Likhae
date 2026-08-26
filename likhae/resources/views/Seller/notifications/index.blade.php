@extends('Seller.layouts.app')
@section('title', 'Notifications — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/notifications.css') @endpush

@section('content')
<x-seller.page-header eyebrow="UPDATES" title="Notifications" description="Orders, inventory, shipping, messages, finance, and account alerts.">
    <x-slot:actions><button class="btn-secondary">Mark All as Read</button></x-slot:actions>
</x-seller.page-header>

<div class="tabs-row">@foreach(['All','Orders','Inventory','Shipping','Messages','Finance','Account'] as $i=>$tab)<button class="tab-button {{ $i===0?'is-active':'' }}">{{ $tab }}</button>@endforeach</div>

<section class="panel mt-4">
    @foreach([
        ['Orders','New order received','You received order LH-20260820-0231 worth ₱1,498.','12 min ago',true],
        ['Inventory','Low stock alert','Capiz Shell Pendant Lamp has only 6 units remaining.','45 min ago',true],
        ['Shipping','Courier pickup scheduled','J&T pickup is scheduled for 2:00–4:00 PM.','2 hrs ago',true],
        ['Messages','New buyer message','Juan sent a message about order LH-20260820-0231.','3 hrs ago',false],
        ['Finance','Balance updated','₱1,360 was added to your pending balance.','Yesterday',false],
        ['Account','Verification approved','Your business permit was verified.','Aug 18',false],
    ] as $n)
    <article class="notification-row {{ $n[4]?'is-unread':'' }}"><div class="notification-icon">{{ substr($n[0],0,1) }}</div><div class="flex-1"><span class="meta-label">{{ $n[0] }}</span><strong>{{ $n[1] }}</strong><p>{{ $n[2] }}</p><time>{{ $n[3] }}</time></div><div class="flex gap-2"><button class="btn-secondary">View</button>@if($n[4])<button class="btn-secondary">Mark Read</button>@endif</div></article>
    @endforeach
</section>
@endsection
