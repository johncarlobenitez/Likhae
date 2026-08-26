@extends('Seller.layouts.app')
@section('title', 'Prepare Order — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/orders.css') @endpush

@section('content')
<x-seller.page-header eyebrow="FULFILLMENT" title="Prepare Order" description="Order LH-20260820-0231 · Verify each item before sealing the parcel.">
    <x-slot:actions><button class="btn-secondary">Print Packing Slip</button><button class="btn-secondary">Print Waybill</button></x-slot:actions>
</x-seller.page-header>

<div class="grid gap-5 xl:grid-cols-[1fr_380px]">
    <section class="panel">
        <div class="panel-header"><div><p class="panel-eyebrow">PACKING LIST</p><h2>Items to Pack</h2></div></div>
        @foreach([
            ['Handwoven Rattan Tote Bag','RAT-TOTE-NAT','Natural · Large','1'],
            ['Premium Philippine Tablea','TAB-250-BX','250g Gift Box','2'],
        ] as $p)
            <div class="packing-row">
                <div class="product-thumb"></div>
                <div class="min-w-0 flex-1"><strong>{{ $p[0] }}</strong><span>SKU {{ $p[1] }} · {{ $p[2] }}</span></div>
                <strong>× {{ $p[3] }}</strong>
            </div>
        @endforeach

        <div class="p-5">
            <p class="panel-eyebrow">PACKING CHECKLIST</p>
            <div class="checklist mt-3">
                @foreach(['Correct item selected','Correct variation confirmed','Quantity verified','Item condition checked','Protective packaging completed'] as $item)
                <label><input type="checkbox"><span>{{ $item }}</span></label>
                @endforeach
            </div>
        </div>
    </section>

    <aside class="panel p-5">
        <p class="panel-eyebrow">WAYBILL PREVIEW</p>
        <div class="waybill mt-4">
            <div class="flex items-center justify-between"><x-seller.logo/><strong>J&T</strong></div>
            <div class="barcode mt-5"></div>
            <div class="mt-4 border-y border-black py-3 text-center"><strong class="text-lg">JT-PH-82731942</strong></div>
            <div class="mt-4 grid grid-cols-2 gap-4 text-[10px]">
                <div><span>FROM</span><strong>Maria’s Local Finds</strong><p>Cebu City, Cebu</p></div>
                <div><span>TO</span><strong>Juan Dela Cruz</strong><p>Makati City, Metro Manila</p></div>
            </div>
            <div class="mt-4 border-t border-black pt-3 text-xs"><strong>Order: LH-20260820-0231</strong><br>Parcel: 1 of 1 · 1.2 kg</div>
        </div>
        <button class="btn-primary mt-5 w-full">Mark as Packed</button>
    </aside>
</div>
@endsection
