@extends('Seller.layouts.app')
@section('title', 'Flash Deals — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/marketing.css') @endpush

@section('content')
<x-seller.page-header eyebrow="PROMOTIONS" title="Flash Deal Management" description="Nominate products, define deal stock, and track scheduled or active offers.">
    <x-slot:actions><button class="btn-primary">Create Flash Deal</button></x-slot:actions>
</x-seller.page-header>

<div class="tabs-row"><button class="tab-button is-active">Scheduled <span>3</span></button><button class="tab-button">Active <span>2</span></button><button class="tab-button">Ended</button></div>

<section class="panel mt-4">
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Product</th><th>Regular</th><th>Deal Price</th><th>Discount</th><th>Stock</th><th>Deal Stock</th><th>Start</th><th>End</th><th>Status</th></tr></thead>
            <tbody>
                @foreach([
                    ['Rattan Tote Bag','₱899','₱699','22%','36','20','Aug 21 · 12 PM','Aug 21 · 6 PM','Pending'],
                    ['Tablea Gift Pack','₱329','₱249','24%','54','30','Aug 22 · 8 AM','Aug 22 · 2 PM','Pending'],
                    ['Capiz Pendant Lamp','₱1,899','₱1,499','21%','6','6','Aug 23 · 6 PM','Aug 24 · 12 AM','Pending'],
                ] as $d)
                <tr>@foreach(array_slice($d,0,8) as $cell)<td>{{ $cell }}</td>@endforeach<td><x-seller.status-badge :status="$d[8]"/></td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
