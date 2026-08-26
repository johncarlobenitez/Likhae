@extends('Seller.layouts.app')
@section('title', 'Order Details — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/orders.css') @endpush

@section('content')
<x-seller.page-header eyebrow="ORDER LH-20260820-0231" title="Order Details" description="Placed August 20, 2026 at 8:31 PM">
    <x-slot:actions>
        <a class="btn-secondary" href="{{ url('/seller/orders/LH-20260820-0231/prepare') }}">Prepare Order</a>
        <button class="btn-primary">Confirm Order</button>
    </x-slot:actions>
</x-seller.page-header>

<section class="order-status-strip">
    <div><span class="meta-label">ORDER STATUS</span><x-seller.status-badge status="To Prepare"/></div>
    <div><span class="meta-label">PAYMENT</span><x-seller.status-badge status="Paid"/></div>
    <div><span class="meta-label">SHIPPING</span><strong>Standard Delivery</strong></div>
    <div><span class="meta-label">COURIER</span><strong>J&T Express</strong></div>
    <div><span class="meta-label">TRACKING</span><strong>JT-PH-82731942</strong></div>
</section>

<div class="mt-5 grid gap-5 xl:grid-cols-[1.35fr_.65fr]">
    <div class="space-y-5">
        <section class="panel">
            <div class="panel-header"><div><p class="panel-eyebrow">ORDER ITEMS</p><h2>2 Products</h2></div></div>
            <div class="divide-y divide-[#E5E0D9]">
                @foreach([
                    ['Handwoven Rattan Tote Bag','Natural · Large','RAT-TOTE-NAT','1','₱899','₱899'],
                    ['Premium Philippine Tablea','250g Gift Box','TAB-250-BX','2','₱329','₱658'],
                ] as $p)
                <div class="order-product-row">
                    <div class="product-thumb"></div>
                    <div class="min-w-0 flex-1"><strong>{{ $p[0] }}</strong><span>{{ $p[1] }} · {{ $p[2] }}</span></div>
                    <div><span>Qty</span><strong>{{ $p[3] }}</strong></div>
                    <div><span>Unit</span><strong>{{ $p[4] }}</strong></div>
                    <div><span>Subtotal</span><strong>{{ $p[5] }}</strong></div>
                </div>
                @endforeach
            </div>
        </section>

        <section class="panel">
            <div class="panel-header"><div><p class="panel-eyebrow">FULFILLMENT</p><h2>Order Timeline</h2></div></div>
            <div class="timeline">
                @foreach([
                    ['Order confirmed','Aug 20 · 8:35 PM',true],
                    ['Payment received via GCash','Aug 20 · 8:34 PM',true],
                    ['Order placed','Aug 20 · 8:31 PM',true],
                    ['Packed','Pending',false],
                    ['Ready for pickup','Pending',false],
                ] as [$label,$time,$done])
                <div class="timeline-item {{ $done ? 'is-done' : '' }}"><i></i><div><strong>{{ $label }}</strong><span>{{ $time }}</span></div></div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="space-y-5">
        <section class="panel p-5">
            <p class="panel-eyebrow">BUYER</p><h2 class="mt-1 text-sm font-black">Juan Dela Cruz</h2><p class="mt-2 text-xs leading-5 text-[#6B6864]">0917 555 0134<br>Unit 5B, Ayala Avenue<br>Makati City, Metro Manila 1226</p>
            <a class="btn-secondary mt-4 w-full" href="{{ url('/seller/messages') }}">Message Buyer</a>
        </section>

        <section class="panel p-5">
            <p class="panel-eyebrow">SELLER EARNINGS</p>
            <div class="money-list">
                <div><span>Merchandise</span><strong>₱1,557</strong></div>
                <div><span>Seller Discount</span><strong>-₱50</strong></div>
                <div><span>Platform Discount</span><strong>-₱30</strong></div>
                <div><span>Commission Fee</span><strong>-₱93</strong></div>
                <div><span>Transaction Fee</span><strong>-₱24</strong></div>
                <div class="total"><span>Net Seller Amount</span><strong>₱1,360</strong></div>
            </div>
        </section>
    </div>
</div>
@endsection
