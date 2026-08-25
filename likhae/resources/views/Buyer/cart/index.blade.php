<x-marketplace.layout title="Your Bag" :buyer="true">
<section class="lk-page-head lk-container">
    <span class="lk-kicker">SHOPPING BAG</span>
    <h1>Your Bag</h1>
    <p>Review items added to your bag, apply discount vouchers, and proceed to checkout.</p>
</section>

<section class="lk-cart-layout lk-container" data-cart-page>
    <div>
        <div class="lk-cart-toolbar">
            <label class="lk-checkbox">
                <input type="checkbox" data-cart-select-all checked>
                <span>Select All Items</span>
            </label>
            <span data-selected-count>0 items selected</span>
        </div>

        <div data-cart-items>
            {{-- Dynamically populated by setupCart() in app.js --}}
        </div>

        <div class="lk-empty-state" data-cart-empty hidden>
            <h2>Your bag is currently empty.</h2>
            <p style="color:var(--slate);margin:12px 0 24px">Explore thoughtfully made pieces from independent Filipino makers.</p>
            <a class="lk-btn lk-btn--primary" href="{{ route('buyer.products') }}">Discover Marketplace</a>
        </div>
    </div>

    <aside class="lk-summary-card">
        <h2>Order Summary</h2>
        <div>
            <span>Merchandise Subtotal</span>
            <strong data-cart-subtotal>₱0</strong>
        </div>
        <div>
            <span>Shipping Estimate</span>
            <strong data-cart-shipping>₱0</strong>
        </div>
        <div>
            <span>Voucher Discount</span>
            <strong data-cart-discount>−₱0</strong>
        </div>
        
        <label style="margin-top:16px;display:block">
            <span style="font-size:0.78rem;font-weight:700;display:block;margin-bottom:6px">Promotional Voucher</span>
            <div class="lk-voucher">
                <input placeholder="e.g. LOCAL10" data-voucher-code aria-label="Voucher code">
                <button type="button" data-apply-voucher>Apply</button>
            </div>
        </label>
        <p class="lk-helper" data-voucher-message>Try <code>LIKHAE100</code> (₱100 off), <code>LOCAL10</code> (10% off), or <code>FREESHIP</code>.</p>

        <div class="lk-summary-total">
            <span>Total to Pay</span>
            <strong data-cart-total>₱0</strong>
        </div>
        <a class="lk-btn lk-btn--primary lk-btn--wide" href="{{ route('buyer.checkout') }}" data-checkout-link>
            Proceed to Checkout
        </a>
    </aside>
</section>
</x-marketplace.layout>
