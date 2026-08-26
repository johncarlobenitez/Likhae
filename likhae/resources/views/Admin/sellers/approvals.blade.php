@extends('Admin.layouts.app')
@section('title', 'Seller Approvals — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">SELLERS</p>
        <h1 class="page-title">Seller Approvals</h1>
        <p class="page-description">Review and approve or reject seller registration applications.</p>
    </div>
</div>

{{-- Stats --}}
<div class="grid gap-4 sm:grid-cols-3 mb-5">
    @foreach([['PENDING REVIEW','14','status-warning'],['APPROVED THIS MONTH','38','status-success'],['REJECTED','5','status-danger']] as [$label,$val,$tone])
        <div class="kpi-card">
            <p class="kpi-label">{{ $label }}</p>
            <div class="mt-3 flex items-end justify-between">
                <strong class="text-2xl font-black tracking-[-.04em]">{{ $val }}</strong>
                <span class="status-badge {{ $tone }}">{{ $label === 'PENDING REVIEW' ? 'Needs Action' : ($label === 'APPROVED THIS MONTH' ? 'Approved' : 'Rejected') }}</span>
            </div>
        </div>
    @endforeach
</div>

{{-- Tabs --}}
<div class="tabs-row">
    @foreach(['Pending (14)','Approved (38)','Rejected (5)','All (57)'] as $i => $tab)
        <button class="tab-button {{ $i === 0 ? 'is-active' : '' }}">{{ $tab }}</button>
    @endforeach
</div>

{{-- Approval Cards --}}
<div class="mt-5 grid gap-4 lg:grid-cols-2">
    @foreach([
        ['Maria Santos',    'maria@email.com',   "Maria's Local Finds",  'Handicrafts & Decor', 'Aug 20, 2026'],
        ['Carlo Bautista',  'carlo@email.com',   'Tablea Gift Shop',     'Food & Beverages',    'Aug 19, 2026'],
        ['Lea Reyes',       'lea@email.com',     'Rattan Co.',           'Furniture & Home',    'Aug 18, 2026'],
        ['Jose Cruz',       'jose@email.com',    'Bayong Atbp.',         'Fashion & Bags',      'Aug 17, 2026'],
    ] as [$name, $email, $store, $category, $date])
        <div class="approval-card">
            <div class="approval-card-header">
                <div>
                    <strong class="text-sm font-black">{{ $store }}</strong>
                    <p class="mt-0.5 text-xs text-[#6B6864]">{{ $category }}</p>
                </div>
                <span class="status-badge status-warning"><span class="status-dot"></span>Pending</span>
            </div>
            <div class="approval-meta mt-3 grid gap-1.5 text-xs text-[#6B6864]">
                <div class="flex gap-2"><span class="w-24 shrink-0">Owner</span><span class="font-semibold text-[#171717]">{{ $name }}</span></div>
                <div class="flex gap-2"><span class="w-24 shrink-0">Email</span><span class="font-semibold text-[#171717]">{{ $email }}</span></div>
                <div class="flex gap-2"><span class="w-24 shrink-0">Applied</span><span class="font-semibold text-[#171717]">{{ $date }}</span></div>
            </div>
            <div class="approval-actions mt-4 flex gap-2">
                <button class="btn-primary text-[11px]">Approve</button>
                <button class="btn-danger text-[11px]">Reject</button>
                <button class="btn-secondary text-[11px] ml-auto">View Documents</button>
            </div>
        </div>
    @endforeach
</div>
@endsection
