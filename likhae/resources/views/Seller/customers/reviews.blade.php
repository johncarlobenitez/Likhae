@extends('Seller.layouts.app')
@section('title', 'Reviews — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/customers.css') @endpush

@section('content')
<x-seller.page-header eyebrow="CUSTOMER FEEDBACK" title="Reviews & Ratings" description="Understand buyer satisfaction and respond professionally."/>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-seller.kpi-card label="AVERAGE RATING" value="4.8 / 5"/>
    <x-seller.kpi-card label="5-STAR REVIEWS" value="84%"/>
    <x-seller.kpi-card label="REVIEWS THIS MONTH" value="126"/>
    <x-seller.kpi-card label="RESPONSE RATE" value="92%" meta="+4%" tone="success"/>
</div>

<div class="tabs-row mt-5">@foreach(['All','5 Star','4 Star','3 Star','2 Star','1 Star','Unanswered'] as $i=>$tab)<button class="tab-button {{ $i===0?'is-active':'' }}">{{ $tab }}</button>@endforeach</div>

<section class="mt-4 space-y-4">
    @foreach([
        ['Ana Villanueva','Handwoven Rattan Tote Bag',5,'Beautiful craftsmanship and the item arrived well packed.','Aug 19, 2026'],
        ['Jerome Tan','Premium Philippine Tablea',4,'Good quality and rich flavor. Shipping was also quick.','Aug 18, 2026'],
    ] as $r)
    <article class="review-card">
        <div class="flex items-start justify-between gap-4"><div><strong>{{ $r[0] }}</strong><span>{{ $r[1] }}</span></div><time>{{ $r[4] }}</time></div>
        <div class="mt-3 text-sm tracking-[.12em] text-[#D99A22]">{{ str_repeat('★',$r[2]) }}</div>
        <p class="mt-3 text-sm leading-6 text-[#6B6864]">{{ $r[3] }}</p>
        <div class="mt-4 flex gap-2"><button class="btn-primary">Reply</button><button class="btn-secondary">Report</button></div>
    </article>
    @endforeach
</section>
@endsection
