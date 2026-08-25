<x-marketplace.layout title="Registration Submitted" :hide-nav="true">
<div class="lk-status-page">
    <a class="lk-logo" href="/">LIKHAE</a>
    <div class="lk-status-card">
        <div class="lk-status-icon">✓</div>
        <span class="lk-kicker">REGISTRATION SUBMITTED</span>
        <h1>Your account is under review.</h1>
        <p>Thank you for creating a LIKHAE buyer profile. To protect our local artisan community, all registrations are reviewed by an administrator within 24 hours.</p>
        
        <div class="lk-approval-track">
            <div class="is-done"><b>1</b><span>Submitted</span></div>
            <div class="is-active"><b>2</b><span>Under Review</span></div>
            <div><b>3</b><span>Approved</span></div>
        </div>

        <div class="lk-stack" style="max-width:320px;margin:30px auto 0">
            <a class="lk-btn lk-btn--primary lk-btn--wide" href="{{ route('login') }}">Return to Sign In</a>
            <a class="lk-btn lk-btn--ghost lk-btn--wide" href="{{ url('/') }}">Browse Marketplace as Guest</a>
        </div>

        <details style="margin-top:35px">
            <summary style="cursor:pointer;color:var(--slate);font-size:0.8rem">Developer demo status simulator</summary>
            <div class="lk-demo-states" style="margin-top:10px">
                <span class="lk-status-pill lk-status-pill--pending">Pending Review</span>
                <span class="lk-status-pill lk-status-pill--approved">Approved</span>
                <span class="lk-status-pill lk-status-pill--revision">Needs Revision</span>
            </div>
        </details>
    </div>
</div>
</x-marketplace.layout>
