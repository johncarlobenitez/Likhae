<x-marketplace.layout title="Account Security" :buyer="true">
<section class="lk-page-head lk-container">
    <span class="lk-kicker">LOGIN & SAFETY</span>
    <h1>Account Security</h1>
</section>

<section class="lk-account-layout lk-container">
    <x-marketplace.account-nav current="security" />

    <section class="lk-account-card">
        <h2>Change Password</h2>
        <form class="lk-form" data-password-form style="max-width:480px">
            <label>Current Password
                <input type="password" required placeholder="••••••••">
            </label>
            <label>New Password
                <input type="password" required placeholder="Minimum 8 characters">
            </label>
            <label>Confirm New Password
                <input type="password" required placeholder="Re-enter new password">
            </label>
            <div style="margin-top:12px">
                <button class="lk-btn lk-btn--primary" type="submit">Update Password</button>
            </div>
        </form>

        <hr>

        <h2>Connected Accounts</h2>
        <div style="display:grid;gap:14px;margin-top:16px">
            <div class="lk-connected-row" style="padding:14px;border:1px solid var(--line);border-radius:10px">
                <div>
                    <strong>Google Account</strong>
                    <p style="color:var(--slate);margin:2px 0 0;font-size:0.85rem">Ready for Socialite linking</p>
                </div>
                <button class="lk-btn lk-btn--secondary" type="button">Connect Google</button>
            </div>
        </div>

        <hr>

        <h2>Active Sessions</h2>
        <div style="margin-top:14px">
            <div style="padding:12px;background:var(--sand);border-radius:8px;display:flex;justify-content:space-between;align-items:center">
                <div>
                    <strong>Chrome on Windows 11 (This Device)</strong>
                    <small style="display:block;color:var(--slate)">Cebu City, Philippines · Active Now</small>
                </div>
                <span class="lk-status-pill lk-status-pill--approved">ACTIVE</span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:20px">
            @csrf
            <button class="lk-btn lk-btn--secondary" type="submit">Log Out All Sessions</button>
        </form>
    </section>
</section>
</x-marketplace.layout>
