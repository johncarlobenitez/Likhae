@extends('Seller.layouts.app')
@section('title', 'Returns & Refunds — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/orders.css') @endpush

@section('content')
<x-seller.page-header eyebrow="CUSTOMER CARE" title="Returns & Refunds" description="Review return requests, evidence, and refund status."/>

<div class="tabs-row">
    @foreach(['Requested','Under Review','Approved','Returning','Received','Refunded','Rejected'] as $i=>$tab)
        <button class="tab-button {{ $i===0?'is-active':'' }}">{{ $tab }} @if($i===0)<span>2</span>@endif</button>
    @endforeach
</div>

<section class="mt-4 space-y-4">
    @foreach([
        ['LH-20260818-0092','Ana Villanueva','Capiz Shell Pendant Lamp','Item arrived damaged','₱1,899'],
        ['LH-20260817-0068','Mark Tan','Rattan Tote Bag','Wrong variation received','₱899'],
    ] as $r)
    <article class="panel p-5">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex gap-4">
                <div class="product-thumb"></div>
                <div><span class="meta-label">RETURN REQUEST · {{ $r[0] }}</span><h2 class="mt-1 text-sm font-black">{{ $r[2] }}</h2><p class="mt-1 text-xs text-[#6B6864]">{{ $r[1] }} · {{ $r[3] }}</p><div class="mt-3"><x-seller.status-badge status="Return Requested"/></div></div>
            </div>
            <div class="text-left lg:text-right"><span class="meta-label">REQUESTED REFUND</span><strong class="mt-1 block text-lg">{{ $r[4] }}</strong></div>
        </div>
        <div class="mt-5 flex flex-wrap gap-2 border-t border-[#E5E0D9] pt-4">
            <button class="btn-primary">Approve Return</button><button class="btn-danger">Dispute</button><button class="btn-secondary">Message Buyer</button><button class="btn-secondary">View Evidence</button>
        </div>
    </article>
    @endforeach
</section>
@endsection
