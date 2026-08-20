@extends('Seller.layouts.app')
@section('title', 'Inventory — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/inventory.css') @endpush

@section('content')
<x-seller.page-header eyebrow="INVENTORY" title="Stock Management" description="Monitor available, reserved, low, and out-of-stock inventory.">
    <x-slot:actions><button class="btn-secondary">Import Stock</button><button class="btn-primary">Bulk Update</button></x-slot:actions>
</x-seller.page-header>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-seller.kpi-card label="TOTAL SKUS" value="184"/>
    <x-seller.kpi-card label="IN STOCK" value="163" meta="88.6%" tone="success"/>
    <x-seller.kpi-card label="LOW STOCK" value="14" meta="Needs attention" tone="warning"/>
    <x-seller.kpi-card label="OUT OF STOCK" value="7" meta="Restock required" tone="warning"/>
</div>

<div class="filter-bar mt-5"><label class="search-field"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input placeholder="Search SKU or product"></label><select class="filter-input"><option>Stock: All</option><option>Low Stock</option><option>Out of Stock</option></select></div>

<section class="panel mt-4">
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Product</th><th>Variation</th><th>SKU</th><th>Available</th><th>Reserved</th><th>Sold</th><th>Threshold</th><th>Status</th><th>Quick Update</th></tr></thead>
            <tbody>
                @foreach([
                    ['Handwoven Rattan Tote Bag','Natural / Large','RAT-TOTE-NAT',36,4,391,8,'Active'],
                    ['Capiz Shell Pendant Lamp','White','CAP-PEN-WHT',6,2,218,10,'Low Stock'],
                    ['Cordillera Woven Bag','Blue','CRD-BAG-BLU',0,0,286,8,'Out of Stock'],
                ] as $r)
                <tr><td><strong>{{ $r[0] }}</strong></td><td>{{ $r[1] }}</td><td>{{ $r[2] }}</td><td>{{ $r[3] }}</td><td>{{ $r[4] }}</td><td>{{ $r[5] }}</td><td>{{ $r[6] }}</td><td><x-seller.status-badge :status="$r[7]"/></td><td><div class="stock-edit"><input value="{{ $r[3] }}"><button>Save</button></div></td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
