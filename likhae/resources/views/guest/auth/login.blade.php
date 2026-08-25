<x-marketplace.layout title="Sign In" :hide-nav="true">
<div class="lk-auth-shell">
    <section class="lk-auth-art">
        <img src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=1400&q=90" alt="LIKHAE curated fashion and lifestyle">
        <div class="lk-auth-art__overlay"></div>
        <div class="lk-auth-art__copy">
            <a class="lk-logo lk-logo--light" href="/">LIKHAE</a>
            <span class="lk-kicker lk-kicker--light">A MARKETPLACE FOR THINGS WITH A STORY</span>
            <h1>Discover what<br><em>Filipino makers</em><br>are making next.</h1>
            <p>Thoughtfully made objects, regional crafts, and independent studios from across the Philippine islands.</p>
        </div>
    </section>
    <section class="lk-auth-form-wrap">
        <div class="lk-auth-form">
            <a class="lk-logo lk-auth-mobile-logo" href="/">LIKHAE</a>
            <span class="lk-kicker">WELCOME BACK</span>
            <h2>Sign in to LIKHAE</h2>
            <p>Save favorite pieces, message makers directly, track orders, and shop the curated edit.</p>
            
            @if(isset($errors) && $errors->any())
                <div class="lk-form-alert" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('status'))
                <div class="lk-form-alert" style="background:#e8f0e8;color:#2e5c33;border-color:#b9d5ba">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="lk-form">
                @csrf
                <label>Email address
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com">
                </label>
                <label>Password
                    <div class="lk-password-field">
                        <input type="password" name="password" required autocomplete="current-password" data-password placeholder="••••••••">
                        <button type="button" data-toggle-password aria-label="Toggle password visibility">Show</button>
                    </div>
                </label>
                <div class="lk-form-row">
                    <label class="lk-checkbox">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="lk-text-link" style="color:var(--slate)">Forgot password?</a>
                </div>
                <button class="lk-btn lk-btn--primary lk-btn--wide" type="submit">Sign In</button>
            </form>

            <div class="lk-or"><span>OR</span></div>

            <a class="lk-btn lk-btn--google lk-btn--wide" href="{{ route('google.placeholder') }}">
                <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true">
                    <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.616z"/>
                    <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/>
                    <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707 0-.59.102-1.167.282-1.707V4.961H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.039l3.007-2.332z"/>
                    <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z"/>
                </svg>
                Continue with Google
            </a>
            
            <a class="lk-btn lk-btn--secondary lk-btn--wide" href="{{ route('guest.continue') }}" style="margin-top:10px">
                Continue as Guest
            </a>

            <p class="lk-auth-foot">
                New to LIKHAE? <a href="{{ route('register') }}" class="lk-text-link" style="color:var(--coral);font-weight:700">Create a buyer account</a>
            </p>

            <details class="lk-demo-credentials">
                <summary>Developer Demo Credentials</summary>
                <div style="margin-top:8px;line-height:1.6;font-size:0.75rem">
                    <p><strong>Buyer:</strong> <code>buyer@likhae.com</code> / <code>buyer</code></p>
                    <p><strong>Seller:</strong> <code>seller@likhae.com</code> / <code>seller</code></p>
                    <p><strong>Admin:</strong> <code>admin@likhae.com</code> / <code>admin</code></p>
                </div>
            </details>
        </div>
    </section>
</div>
</x-marketplace.layout>
