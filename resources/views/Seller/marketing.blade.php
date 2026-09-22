@extends('layouts.seller')

@php
    $currentTab = $tab ?? request('tab', 'discounts');
    $type = match($currentTab) { 'vouchers' => 'voucher', 'promotions' => 'promotion', default => 'discount' };
    $visibleCampaigns = $campaigns->where('type', $type);
@endphp

@section('title', 'Marketing')
@section('active', 'marketing')
@section('subtitle', 'Create controlled promotions that grow sales without hurting margins.')

@section('content')
<div class="sl-page">
    <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Growth Tools</span><h2>Marketing Center</h2><p>Create, pause, and monitor seller campaigns.</p></div><button type="button" class="sl-btn sl-btn-primary" data-modal-open="campaignModal"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Create Campaign</button></div>
    <nav class="sl-tabs" aria-label="Marketing tabs"><a href="{{ route('seller.marketing',['tab'=>'discounts']) }}" class="{{ $currentTab==='discounts'?'is-active':'' }}">Discounts</a><a href="{{ route('seller.marketing',['tab'=>'vouchers']) }}" class="{{ $currentTab==='vouchers'?'is-active':'' }}">Vouchers</a><a href="{{ route('seller.marketing',['tab'=>'promotions']) }}" class="{{ $currentTab==='promotions'?'is-active':'' }}">Promotions</a></nav>

    <section class="sl-mini-stats">
        <div><span>Campaigns</span><strong>{{ $visibleCampaigns->count() }}</strong></div>
        <div><span>Active</span><strong>{{ $visibleCampaigns->where('status','active')->count() }}</strong></div>
        <div><span>Total Uses</span><strong>{{ number_format($visibleCampaigns->sum('uses')) }}</strong></div>
        <div><span>Usage Limit</span><strong>{{ number_format($visibleCampaigns->sum(fn($c)=>$c->usage_limit ?? 0)) }}</strong></div>
    </section>

    <section class="sl-card">
        <div class="sl-table-toolbar"><div><h3>{{ ucfirst($currentTab) }}</h3><p>Marketing records are persisted in the seller_campaigns table.</p></div><button type="button" class="sl-btn sl-btn-primary sl-btn-sm" data-modal-open="campaignModal">Create {{ str($type)->headline() }}</button></div>
        <div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Name / Code</th><th>Benefit</th><th>Minimum Spend</th><th>Usage</th><th>Validity</th><th>Status</th><th>Action</th></tr></thead><tbody>
            @forelse($visibleCampaigns as $campaign)
                <tr><td><strong>{{ $campaign->name }}</strong>@if($campaign->code)<small class="sl-code">{{ $campaign->code }}</small>@endif</td><td>{{ $campaign->discount_type==='percent' ? number_format($campaign->discount_value,0).'%' : '₱'.number_format($campaign->discount_value,2) }} off</td><td>₱{{ number_format($campaign->minimum_spend,2) }}</td><td>{{ $campaign->uses }} / {{ $campaign->usage_limit ?: '∞' }}</td><td>{{ $campaign->starts_at?->format('M d') ?: 'Now' }} – {{ $campaign->ends_at?->format('M d, Y') ?: 'No end' }}</td><td><span class="sl-status {{ $campaign->status==='active'?'is-success':'is-neutral' }}">{{ ucfirst($campaign->status) }}</span></td><td><form method="POST" action="{{ route('seller.marketing.campaigns.toggle',$campaign) }}">@csrf @method('PATCH')<button type="submit" class="sl-btn sl-btn-ghost sl-btn-sm">{{ $campaign->status==='active'?'Pause':'Activate' }}</button></form></td></tr>
            @empty<tr><td colspan="7">No {{ $currentTab }} yet. Create one to get started.</td></tr>@endforelse
        </tbody></table></div>
    </section>
</div>

<div class="sl-modal" data-modal="campaignModal" hidden>
    <div class="sl-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="campaignTitle">
        <header><div><span class="sl-eyebrow">Marketing Tool</span><h2 id="campaignTitle">Create Campaign</h2></div><button type="button" class="sl-icon-btn" data-modal-close aria-label="Close">×</button></header>
        <form method="POST" action="{{ route('seller.marketing.campaigns.store') }}">@csrf
            <div class="sl-form-grid">
                <label class="sl-field sl-span-2"><span>Campaign name</span><input name="name" type="text" required></label>
                <label class="sl-field"><span>Campaign type</span><select name="type"><option value="discount" @selected($type==='discount')>Product discount</option><option value="voucher" @selected($type==='voucher')>Store voucher</option><option value="promotion" @selected($type==='promotion')>Marketplace promotion</option></select></label>
                <label class="sl-field"><span>Code</span><input name="code" placeholder="Optional"></label>
                <label class="sl-field"><span>Discount type</span><select name="discount_type"><option value="percent">Percent</option><option value="fixed">Fixed amount</option></select></label>
                <label class="sl-field"><span>Discount value</span><input name="discount_value" type="number" min="0" step="0.01" value="10" required></label>
                <label class="sl-field"><span>Minimum spend</span><input name="minimum_spend" type="number" min="0" step="0.01" value="0"></label>
                <label class="sl-field"><span>Usage limit</span><input name="usage_limit" type="number" min="1"></label>
                <label class="sl-field"><span>Start date</span><input name="starts_at" type="datetime-local"></label>
                <label class="sl-field"><span>End date</span><input name="ends_at" type="datetime-local"></label>
            </div>
            <footer><button type="button" class="sl-btn sl-btn-ghost" data-modal-close>Cancel</button><button type="submit" class="sl-btn sl-btn-primary">Create Campaign</button></footer>
        </form>
    </div>
</div>
@endsection
