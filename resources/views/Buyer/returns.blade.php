@extends('layouts.buyer')
@section('title', 'Returns & Refunds')
@section('active', 'orders')
@section('content')
<style>
    .returns-page{width:min(100%,1000px)!important}.return-card{border:1px solid #eadbce;border-radius:18px;background:#fffdf9}.return-pill{display:inline-flex;border-radius:999px;padding:5px 10px;background:#fff0e9;color:#8f382d;font-size:11px;font-weight:700}.dark .return-card{border-color:#4b3831;background:#241b18;color:#f7eee9}.dark .return-pill{background:#4a2923;color:#f4b4a7}
</style>
<div class="returns-page">
    <div class="lk-page-title mb-5"><div><span class="lk-kicker">Buyer support</span><h1>Returns & Refunds</h1><p>Track your submitted requests and their review status.</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.orders') }}">My Orders</a></div>
    @if(session('buyer_notice'))<div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800">{{ session('buyer_notice') }}</div>@endif
    <div class="grid gap-4">
        @forelse($requests as $returnRequest)
            <article class="return-card p-5 shadow-sm"><div class="flex flex-wrap items-start justify-between gap-3"><div><span class="return-pill">{{ str($returnRequest->status)->headline() }}</span><h2 class="mt-2 text-lg font-bold">{{ $returnRequest->request_number }}</h2><p class="text-sm text-stone-500 dark:text-stone-300">Order {{ $returnRequest->order?->order_number }} · {{ $returnRequest->submitted_at?->format('M j, Y g:i A') }}</p></div><strong class="text-lg text-red-900 dark:text-red-200">₱{{ number_format((float) $returnRequest->requested_amount, 2) }}</strong></div><div class="mt-4 grid gap-3 border-t border-[#eadbce] pt-4 text-sm dark:border-[#4b3831] sm:grid-cols-3"><div><span class="block text-xs uppercase tracking-wide text-stone-500">Issue</span><strong>{{ str($returnRequest->issue_category)->headline() }}</strong></div><div><span class="block text-xs uppercase tracking-wide text-stone-500">Reason</span><strong>{{ $returnRequest->issue_reason }}</strong></div><div><span class="block text-xs uppercase tracking-wide text-stone-500">Solution</span><strong>{{ $returnRequest->solution }}</strong></div></div><div class="mt-4 flex justify-end"><a class="lk-btn lk-btn-light" href="{{ route('buyer.orders.show', $returnRequest->order) }}">View Order</a></div></article>
        @empty
            <section class="return-card p-8 text-center"><h2 class="text-xl font-bold">No return or refund requests yet</h2><p class="mt-2 text-sm text-stone-500 dark:text-stone-300">When you submit a request, its status and request ID will appear here.</p><a class="lk-btn lk-btn-red mt-5" href="{{ route('buyer.orders') }}">View My Orders</a></section>
        @endforelse
    </div>
    @if($requests->hasPages())<div class="mt-5">{{ $requests->links() }}</div>@endif
</div>
@endsection
