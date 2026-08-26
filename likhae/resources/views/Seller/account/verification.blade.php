@extends('Seller.layouts.app')
@section('title', 'Verification — LIKHAE Seller')
@push('styles') @vite('resources/css/seller/account.css') @endpush

@section('content')
<x-seller.page-header eyebrow="ACCOUNT" title="Business Verification" description="Keep your identity and business documents valid and up to date."/>

<section class="panel">
    @foreach([
        ['Personal Identity','Government ID verified','Verified'],
        ['Business Information','Business details verified','Verified'],
        ['Business Permit','Valid until March 2027','Verified'],
        ['Email','maria@example.com','Verified'],
        ['Phone','+63 917 123 4567','Verified'],
        ['DTI Registration','Document expires in 42 days','Under Review'],
    ] as $v)
    <div class="verification-row"><div class="verification-icon"><svg viewBox="0 0 24 24"><path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"/><path d="m9 12 2 2 4-5"/></svg></div><div class="flex-1"><strong>{{ $v[0] }}</strong><span>{{ $v[1] }}</span></div><x-seller.status-badge :status="$v[2]"/><button class="btn-secondary">View</button></div>
    @endforeach
</section>

<section class="panel mt-5 p-5"><p class="panel-eyebrow">DOCUMENT MANAGEMENT</p><p class="mt-2 text-sm text-[#6B6864]">Replace an expired or rejected document without changing your verified account information.</p><button class="btn-primary mt-4">Upload Updated Document</button></section>
@endsection
