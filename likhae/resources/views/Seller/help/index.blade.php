@extends('Seller.layouts.app')
@section('title', 'Help & Support — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/help.css') @endpush

@section('content')
<x-seller.page-header eyebrow="SUPPORT" title="Seller Help Center" description="Find selling guides, marketplace policies, and support resources."/>

<section class="help-search"><h2>How can we help?</h2><label class="search-field"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input placeholder="Search Seller Help Center"></label></section>

<div class="help-grid mt-5">
    @foreach([
        ['Getting Started','Set up your store, profile, products, and pickup address.'],
        ['Product Listing Rules','Understand allowed products, content standards, and listing quality.'],
        ['Order Fulfillment','Learn packing, waybill, pickup, and shipment workflows.'],
        ['Shipping Guidelines','Courier schedules, parcel requirements, and tracking.'],
        ['Returns & Refunds','Handle return requests and seller disputes.'],
        ['Fees & Payments','Commission, transaction fees, balances, and payouts.'],
        ['Account Verification','Identity, business permits, and document updates.'],
        ['Marketplace Policies','Seller conduct, prohibited items, and account policies.'],
    ] as $h)<a class="help-card" href="#"><h3>{{ $h[0] }}</h3><p>{{ $h[1] }}</p><span>Read guide →</span></a>@endforeach
</div>

<section class="panel mt-5 p-5 sm:p-6"><div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center"><div><p class="panel-eyebrow">NEED MORE HELP?</p><h2 class="mt-1 text-lg font-black">Contact LIKHAE Support</h2><p class="mt-2 text-sm text-[#6B6864]">Create a support ticket for account, order, shipping, or payment concerns.</p></div><button class="btn-primary">Submit Support Ticket</button></div></section>
@endsection
