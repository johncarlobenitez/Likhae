@php
$orders=[
['id'=>'001234','number'=>'LKH-2026-001234','seller'=>'Metro Finds PH','date'=>'Aug 29, 2026','status'=>'in-transit','status_label'=>'In Transit','badge_type'=>'warning','name'=>'Premium Wireless Headphones','image'=>'/images/buyer/products/headphones.svg','variation'=>'Black · Standard','qty'=>1,'total'=>2499],
['id'=>'001198','number'=>'LKH-2026-001198','seller'=>'Urban Carry Co.','date'=>'Aug 23, 2026','status'=>'completed','status_label'=>'Delivered','badge_type'=>'success','name'=>'Classic Everyday Backpack','image'=>'/images/buyer/products/backpack.svg','variation'=>'Brown','qty'=>1,'total'=>999],
['id'=>'001166','number'=>'LKH-2026-001166','seller'=>'Stride PH','date'=>'Aug 20, 2026','status'=>'to-ship','status_label'=>'To Ship','badge_type'=>'neutral','name'=>'Lightweight Running Shoes','image'=>'/images/buyer/products/shoes.svg','variation'=>'White · Size 42','qty'=>1,'total'=>1799]
];
@endphp
<x-buyer.layout title="My Orders"><div class="b-page" data-buyer-orders-page><div class="b-container">
<x-buyer.breadcrumbs :items="[['label'=>'My Orders','url'=>null]]" />
<div class="b-section-head"><div><h1 class="b-title">My Orders</h1><p class="b-muted">Track purchases and manage completed orders.</p></div></div>
<div class="b-order-tabs">@foreach([['all','All'],['to-pay','To Pay'],['to-ship','To Ship'],['in-transit','In Transit'],['out-for-delivery','Out for Delivery'],['completed','Delivered / Completed'],['cancelled','Cancelled']] as $i=>[$status,$label])<button class="b-order-tab {{ $i===0?'is-active':'' }}" data-order-tab="{{ $status }}">{{ $label }}</button>@endforeach</div>
@foreach($orders as $order)<x-buyer.order-card :order="$order" />@endforeach
</div></div></x-buyer.layout>
