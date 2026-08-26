@extends('Seller.layouts.app')
@section('title', 'Marketing — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/marketing.css') @endpush

@section('content')
<x-seller.page-header eyebrow="GROWTH" title="Marketing Center" description="Create discounts, vouchers, flash deals, and store promotions."/>

<div class="marketing-grid">
    @foreach([
        ['Product Discounts','Set sale prices for selected listings.','18 active','/seller/products'],
        ['Vouchers','Reward shoppers with store voucher codes.','6 active','#voucher'],
        ['Flash Deals','Nominate products for limited-time offers.','3 scheduled','/seller/marketing/flash-deals'],
        ['Store Promotions','Build campaigns around collections or events.','2 active','#'],
    ] as $m)
    <a href="{{ url($m[3]) }}" class="marketing-card"><span class="meta-label">{{ $m[2] }}</span><h2>{{ $m[0] }}</h2><p>{{ $m[1] }}</p><span class="mt-auto pt-5 text-xs font-black text-[#D92D2F]">Manage →</span></a>
    @endforeach
</div>

<section id="voucher" class="panel mt-5">
    <div class="panel-header"><div><p class="panel-eyebrow">VOUCHERS</p><h2>Store Vouchers</h2></div><button class="btn-primary">Create Voucher</button></div>
    <div class="grid gap-4 p-5 lg:grid-cols-3">
        @foreach([
            ['LIKHAE50','₱50 OFF','Min. spend ₱500','328 / 1,000 used','Active'],
            ['LOCAL10','10% OFF','Max. discount ₱120','142 / 500 used','Active'],
            ['WELCOME100','₱100 OFF','New buyers · Min. ₱999','Starts Aug 25','Pending'],
        ] as $v)
        <article class="voucher-card"><div class="voucher-cut"></div><span class="meta-label">{{ $v[4] }}</span><h3>{{ $v[0] }}</h3><strong>{{ $v[1] }}</strong><p>{{ $v[2] }}</p><small>{{ $v[3] }}</small><div class="mt-4 flex gap-2"><button class="btn-secondary flex-1">Edit</button><button class="btn-secondary flex-1">Details</button></div></article>
        @endforeach
    </div>
</section>
@endsection
