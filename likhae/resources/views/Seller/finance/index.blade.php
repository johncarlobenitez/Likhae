@extends('Seller.layouts.app')
@section('title', 'Finance — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/finance.css') @endpush

@section('content')
<x-seller.page-header eyebrow="FINANCE" title="Seller Finance" description="Track balances, fees, transactions, and payouts.">
    <x-slot:actions><button class="btn-primary">Request Payout</button></x-slot:actions>
</x-seller.page-header>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-seller.kpi-card label="AVAILABLE BALANCE" value="₱84,240"/>
    <x-seller.kpi-card label="PENDING BALANCE" value="₱16,430"/>
    <x-seller.kpi-card label="TOTAL SALES" value="₱218,900"/>
    <x-seller.kpi-card label="PLATFORM FEES" value="₱12,475"/>
</div>

<div class="mt-5 grid gap-5 xl:grid-cols-[1fr_360px]">
    <section class="panel">
        <div class="panel-header"><div><p class="panel-eyebrow">TRANSACTIONS</p><h2>Recent Activity</h2></div><button class="btn-secondary">Export</button></div>
        <div class="table-wrap"><table class="data-table"><thead><tr><th>Date</th><th>Reference</th><th>Type</th><th>Gross</th><th>Fees</th><th>Net</th><th>Status</th></tr></thead><tbody>
        @foreach([
            ['Aug 20','LH-20260820-0231','Order','₱1,557','₱197','₱1,360','Pending'],
            ['Aug 20','LH-20260820-0214','Order','₱899','₱104','₱795','Pending'],
            ['Aug 19','PO-20260819-0051','Payout','₱25,000','₱0','₱25,000','Completed'],
        ] as $t)<tr>@foreach(array_slice($t,0,6) as $cell)<td>{{ $cell }}</td>@endforeach<td><x-seller.status-badge :status="$t[6]"/></td></tr>@endforeach
        </tbody></table></div>
    </section>

    <aside class="space-y-5">
        <section class="panel p-5"><p class="panel-eyebrow">PAYOUT METHOD</p><h2 class="mt-1 text-sm font-black">BDO ···· 2841</h2><p class="mt-2 text-xs text-[#6B6864]">Maria Santos · Default payout method</p><button class="btn-secondary mt-4 w-full">Manage Methods</button></section>
        <section class="panel p-5"><p class="panel-eyebrow">UPCOMING PAYOUT</p><strong class="mt-2 block text-2xl">₱31,420</strong><p class="mt-2 text-xs text-[#6B6864]">Estimated Aug 23, 2026</p></section>
    </aside>
</div>
@endsection
