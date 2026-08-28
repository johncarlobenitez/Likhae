<footer style="margin-top:44px;background:#fff;border-top:1px solid var(--likhae-border)">
    <div class="g-container" style="padding:34px 0">
        <div style="display:grid;grid-template-columns:minmax(220px,1.6fr) repeat(3,minmax(130px,1fr));gap:24px">
            <div>
                <a class="g-brand" href="{{ url('/') }}">LIKHAE</a>
                <p class="g-muted" style="max-width:390px;line-height:1.65;font-size:13px">
                    A modern multi-category marketplace built to make everyday shopping simpler,
                    safer, and more organized.
                </p>
            </div>

            <div>
                <strong style="font-size:13px">Shop</strong>
                <div style="display:grid;gap:8px;margin-top:10px;font-size:13px;color:var(--likhae-muted)">
                    <a href="{{ url('/products') }}">All Products</a>
                    <a href="{{ url('/products') }}?category=Electronics">Electronics</a>
                    <a href="{{ url('/products') }}?category=Fashion">Fashion</a>
                </div>
            </div>

            <div>
                <strong style="font-size:13px">Account</strong>
                <div style="display:grid;gap:8px;margin-top:10px;font-size:13px;color:var(--likhae-muted)">
                    <a href="{{ route('login') }}">Sign In</a>
                    <a href="{{ route('register') }}">Register</a>
                </div>
            </div>

            <div>
                <strong style="font-size:13px">Trust</strong>
                <div style="display:grid;gap:8px;margin-top:10px;font-size:13px;color:var(--likhae-muted)">
                    <span>Buyer Protection</span>
                    <span>Secure Checkout</span>
                    <span>Order Tracking</span>
                </div>
            </div>
        </div>

        <div style="margin-top:26px;padding-top:18px;border-top:1px solid var(--likhae-border);font-size:12px;color:var(--likhae-muted)">
            © {{ date('Y') }} LIKHAE. All rights reserved.
        </div>
    </div>
</footer>
