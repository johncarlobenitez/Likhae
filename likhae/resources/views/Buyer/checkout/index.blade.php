<x-marketplace.layout title="Checkout" :buyer="true">
<section class="lk-page-head lk-container">
    <span class="lk-kicker">CHECKOUT</span>
    <h1>Review your order</h1>
    <p>Confirm your delivery address, shipping method, and payment details.</p>
</section>

<section class="lk-checkout-layout lk-container" data-checkout-page>
    <div class="lk-checkout-sections">
        {{-- Delivery Address --}}
        <article class="lk-checkout-card">
            <div class="lk-card-heading">
                <h2>1. Delivery Address</h2>
                <a href="{{ route('buyer.account.addresses') }}" class="lk-text-link">Manage Addresses</a>
            </div>
            <div style="margin-top:14px;padding:14px;background:var(--sand);border-radius:10px">
                <span class="lk-status-pill lk-status-pill--approved" style="margin-bottom:6px;display:inline-block">DEFAULT ADDRESS</span>
                <strong style="display:block;font-size:1.05rem">Maria Dela Cruz · +63 917 123 4567</strong>
                <p style="color:var(--slate);margin:4px 0 0">18 Narra Street, Brgy. Lahug, Cebu City, Cebu 6000</p>
            </div>
        </article>

        {{-- Order Items --}}
        <article class="lk-checkout-card">
            <h2>2. Order Items</h2>
            <div data-checkout-items style="margin-top:12px">
                {{-- Dynamically populated by setupCheckout() --}}
            </div>
        </article>

        {{-- Shipping Method --}}
        <article class="lk-checkout-card">
            <h2>3. Shipping Method</h2>
            <label class="lk-radio-card">
                <input type="radio" name="shipping" value="standard" checked data-shipping-option data-fee="120">
                <span>
                    <strong>Standard Delivery (₱120)</strong>
                    <small>Estimated delivery: 3–5 business days</small>
                </span>
                <b>₱120</b>
            </label>
            <label class="lk-radio-card">
                <input type="radio" name="shipping" value="express" data-shipping-option data-fee="220">
                <span>
                    <strong>Express Courier Delivery (₱220)</strong>
                    <small>Priority handling: 1–2 business days</small>
                </span>
                <b>₱220</b>
            </label>
        </article>

        {{-- Payment Method --}}
        <article class="lk-checkout-card">
            <h2>4. Payment Method</h2>
            <div class="lk-payment-grid">
                @foreach(['Cash on Delivery', 'GCash', 'Maya', 'Credit / Debit Card'] as $i => $method)
                    <label>
                        <input type="radio" name="payment" value="{{ $method }}" {{ $i === 0 ? 'checked' : '' }} data-payment>
                        <span>{{ $method }}</span>
                    </label>
                @endforeach
            </div>
            <p class="lk-helper" style="margin-top:12px">
                <em>Note:</em> Payment flows are mock demo integrations for preview purposes. No real payment charge will occur.
            </p>
        </article>
    </div>

    {{-- Order Summary Sidebar --}}
    <aside class="lk-summary-card">
        <h2>Order Summary</h2>
        <div>
            <span>Merchandise Subtotal</span>
            <strong data-checkout-subtotal>₱0</strong>
        </div>
        <div>
            <span>Shipping Fee</span>
            <strong data-checkout-shipping>₱120</strong>
        </div>
        <div>
            <span>Voucher Discount</span>
            <strong data-checkout-discount>−₱0</strong>
        </div>
        <div class="lk-summary-total">
            <span>Total Amount</span>
            <strong data-checkout-total>₱0</strong>
        </div>
        <button class="lk-btn lk-btn--primary lk-btn--wide" type="button" data-place-order style="margin-top:16px">
            Place Order
        </button>
        <p style="font-size:0.75rem;color:var(--slate);text-align:center;margin-top:12px">
            By placing your order, you agree to LIKHAE's terms and maker agreement.
        </p>
    </aside>
</section>

{{-- Order Placed Modal --}}
<div class="lk-modal" data-order-success hidden role="dialog" aria-modal="true">
    <div class="lk-modal__backdrop"></div>
    <section class="lk-modal__panel" style="text-align:center">
        <div class="lk-status-icon">✓</div>
        <span class="lk-kicker">ORDER CONFIRMED</span>
        <h2>Thank you for shopping local.</h2>
        <p>Your order has been placed with the maker. Your mock order number is:</p>
        <p style="background:var(--sand);padding:10px;border-radius:8px;font-family:monospace;font-size:1.1rem;font-weight:700" data-order-number>LKH-2026-0825-1048</p>
        <div class="lk-stack">
            <a class="lk-btn lk-btn--primary" href="{{ route('buyer.orders') }}">View Your Orders</a>
            <a class="lk-btn lk-btn--secondary" href="{{ route('buyer.products') }}">Continue Shopping</a>
        </div>
    </section>
</div>
</x-marketplace.layout>
