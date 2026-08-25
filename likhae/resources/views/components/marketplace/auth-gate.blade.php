<div class="lk-modal" data-auth-gate hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="auth-gate-title">
    <div class="lk-modal__backdrop" data-close-modal></div>
    <section class="lk-modal__panel">
        <button class="lk-modal__close" type="button" aria-label="Close dialog" data-close-modal>×</button>
        <span class="lk-kicker">JOIN OUR COMMUNITY</span>
        <h2 id="auth-gate-title">Sign in to continue.</h2>
        <p>Purchasing, saving wishlist items, messaging makers, and submitting reviews require a LIKHAE buyer account.</p>
        <div class="lk-stack">
            <a class="lk-btn lk-btn--primary" href="{{ route('login') }}">Sign In</a>
            <a class="lk-btn lk-btn--secondary" href="{{ route('register') }}">Create Buyer Account</a>
            <button class="lk-btn lk-btn--ghost" type="button" data-close-modal>Continue Browsing</button>
        </div>
    </section>
</div>

