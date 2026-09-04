@extends('layouts.seller')

@php $currentTab = $tab ?? request('tab', 'discounts'); @endphp

@section('title', 'Marketing')
@section('active', 'marketing')
@section('subtitle', 'Create controlled promotions that grow sales without hurting margins.')

@section('content')
<div class="sl-page">
    <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Growth Tools</span><h2>Marketing Center</h2><p>Manage discounts, vouchers, and campaign participation.</p></div><button type="button" class="sl-btn sl-btn-primary" data-modal-open="campaignModal"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Create Campaign</button></div>
    <nav class="sl-tabs" aria-label="Marketing tabs">
        <a href="{{ route('seller.marketing', ['tab' => 'discounts']) }}" class="{{ $currentTab === 'discounts' ? 'is-active' : '' }}">Discounts</a>
        <a href="{{ route('seller.marketing', ['tab' => 'vouchers']) }}" class="{{ $currentTab === 'vouchers' ? 'is-active' : '' }}">Vouchers</a>
        <a href="{{ route('seller.marketing', ['tab' => 'promotions']) }}" class="{{ $currentTab === 'promotions' ? 'is-active' : '' }}">Promotions</a>
    </nav>

    @if ($currentTab === 'vouchers')
        <section class="sl-mini-stats"><div><span>Active Vouchers</span><strong>4</strong></div><div><span>Claims</span><strong>1,284</strong></div><div><span>Uses</span><strong>648</strong></div><div><span>Voucher Sales</span><strong>₱92,450</strong></div></section>
        <section class="sl-card">
            <div class="sl-table-toolbar"><div><h3>Store Vouchers</h3><p>Create buyer incentives with controlled limits.</p></div><button type="button" class="sl-btn sl-btn-primary sl-btn-sm" data-modal-open="campaignModal">Create Voucher</button></div>
            <div class="sl-table-wrap"><table class="sl-table"><thead><tr><th>Voucher</th><th>Benefit</th><th>Minimum Spend</th><th>Usage</th><th>Validity</th><th>Status</th><th>Action</th></tr></thead><tbody>
                @foreach ([['WELCOME100','₱100 off','₱1,000','245 / 500','Sep 01–30','Active'],['TECH10','10% off','₱2,500','302 / 400','Sep 01–15','Active'],['SHIPFREE','Free shipping','₱799','101 / 250','Sep 04–10','Scheduled']] as $voucher)
                    <tr><td><strong class="sl-code">{{ $voucher[0] }}</strong></td><td>{{ $voucher[1] }}</td><td>{{ $voucher[2] }}</td><td>{{ $voucher[3] }}</td><td>{{ $voucher[4] }}</td><td><span class="sl-status {{ $voucher[5] === 'Active' ? 'is-success' : 'is-neutral' }}">{{ $voucher[5] }}</span></td><td><button type="button" class="sl-btn sl-btn-ghost sl-btn-sm" data-demo-action="Voucher editor opened.">Manage</button></td></tr>
                @endforeach
            </tbody></table></div>
        </section>
    @elseif ($currentTab === 'promotions')
        <section class="sl-card sl-campaign-banner"><div><span class="sl-eyebrow">Marketplace Campaign</span><h2>9.9 Local Finds Festival</h2><p>Feature eligible products in LIKHAE’s September discovery campaign.</p><div><span>Registration closes</span><strong>September 6, 2026</strong></div></div><button type="button" class="sl-btn sl-btn-white" data-demo-action="Campaign registration opened.">Join Campaign</button></section>
        <div class="sl-card-grid-3">
            @foreach ([['Payday Tech Deals','Sep 14–16','Electronics and accessories','12 products eligible'],['Home Office Week','Sep 20–25','Workspace essentials','8 products eligible'],['Month-End Sale','Sep 27–30','Storewide campaign','All active products']] as $campaign)
                <article class="sl-card sl-campaign-card"><span class="sl-campaign-date">{{ $campaign[1] }}</span><h3>{{ $campaign[0] }}</h3><p>{{ $campaign[2] }}</p><small>{{ $campaign[3] }}</small><button type="button" class="sl-btn sl-btn-soft sl-btn-block" data-demo-action="Campaign details opened.">View Requirements</button></article>
            @endforeach
        </div>
    @else
        <section class="sl-mini-stats"><div><span>Active Discounts</span><strong>3</strong></div><div><span>Discounted Products</span><strong>18</strong></div><div><span>Sales Generated</span><strong>₱68,240</strong></div><div><span>Avg. Conversion</span><strong>8.4%</strong></div></section>
        <section class="sl-card">
            <div class="sl-table-toolbar"><div><h3>Product Discounts</h3><p>Scheduled and active price promotions.</p></div><button type="button" class="sl-btn sl-btn-primary sl-btn-sm" data-modal-open="campaignModal">Create Discount</button></div>
            <div class="sl-promo-list">
                @foreach ([['Weekend Tech Sale','10% off','8 products','Sep 04–06','Active',72],['Monitor Launch Offer','₱1,299 off','1 product','Sep 01–15','Active',54],['Accessories Bundle','15% off','9 products','Sep 10–20','Scheduled',0]] as $discount)
                    <article class="sl-promo-row"><span class="sl-promo-icon">%</span><div class="sl-promo-main"><strong>{{ $discount[0] }}</strong><span>{{ $discount[2] }} · {{ $discount[3] }}</span></div><div><small>Benefit</small><strong>{{ $discount[1] }}</strong></div><div><small>Performance</small><span class="sl-progress"><i style="width: {{ $discount[5] }}%"></i></span></div><span class="sl-status {{ $discount[4] === 'Active' ? 'is-success' : 'is-neutral' }}">{{ $discount[4] }}</span><button type="button" class="sl-icon-btn" data-demo-action="Discount options opened." aria-label="Discount actions">•••</button></article>
                @endforeach
            </div>
        </section>
    @endif
</div>

<div class="sl-modal" data-modal="campaignModal" hidden>
    <div class="sl-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="campaignTitle">
        <header><div><span class="sl-eyebrow">Marketing Tool</span><h2 id="campaignTitle">Create Campaign</h2></div><button type="button" class="sl-icon-btn" data-modal-close aria-label="Close">×</button></header>
        <form data-demo-form data-success="Marketing campaign created.">
            <div class="sl-form-grid"><label class="sl-field sl-span-2"><span>Campaign name</span><input type="text" placeholder="e.g. September Product Sale" required></label><label class="sl-field"><span>Campaign type</span><select><option>Product discount</option><option>Store voucher</option><option>Marketplace promotion</option></select></label><label class="sl-field"><span>Discount value</span><div class="sl-input-suffix"><input type="number" min="1" max="100" value="10"><i>%</i></div></label><label class="sl-field"><span>Start date</span><input type="date" required></label><label class="sl-field"><span>End date</span><input type="date" required></label><label class="sl-field sl-span-2"><span>Products</span><select><option>All active products</option><option>Select specific products</option></select></label></div>
            <footer><button type="button" class="sl-btn sl-btn-ghost" data-modal-close>Cancel</button><button type="submit" class="sl-btn sl-btn-primary">Create Campaign</button></footer>
        </form>
    </div>
</div>
@endsection
