<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Security — LIKHAE</title>

    @vite([
        'resources/css/buyer/security.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo Buyer / Security Data
    |--------------------------------------------------------------------------
    | Replace with authenticated user/session data later.
    */
    $buyer = [
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'email' => auth()->check() ? auth()->user()->email : 'juan.delacruz@example.com',
        'cart_count' => 2,
        'message_count' => 3,
        'notification_count' => 4,
        'password_updated_at' => 'August 10, 2026',
        'last_login' => 'August 20, 2026 • 7:42 PM',
        'last_login_location' => 'Makati City, Metro Manila',
    ];

    $sessions = [
        [
            'device' => 'Windows PC',
            'browser' => 'Chrome',
            'location' => 'Makati City, Metro Manila',
            'last_active' => 'Active now',
            'current' => true,
        ],
        [
            'device' => 'Android Phone',
            'browser' => 'Chrome Mobile',
            'location' => 'Makati City, Metro Manila',
            'last_active' => '2 days ago',
            'current' => false,
        ],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container">
        <div class="flex h-16 items-center gap-4">
            <a href="{{ url('/buyer/home') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">L</span>
                <span class="text-xl font-black tracking-tight">LIKHAE</span>
            </a>

            <form action="{{ url('/buyer/products') }}" method="GET" class="hidden min-w-0 flex-1 md:flex">
                <div class="flex h-11 w-full overflow-hidden border border-[#dedad3] bg-white">
                    <input
                        type="search"
                        name="q"
                        placeholder="Search products, brands, Filipino finds..."
                        class="min-w-0 flex-1 bg-transparent px-4 text-sm outline-none placeholder:text-[#b9b4ad]"
                    >

                    <button
                        type="submit"
                        class="flex w-[108px] items-center justify-center gap-2 bg-[#d92d2f] px-4 text-sm font-bold text-white transition hover:bg-[#bd2024]"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>
                        Search
                    </button>
                </div>
            </form>

            <nav class="ml-auto flex items-center gap-1 sm:gap-2">
                <a href="{{ url('/buyer/notifications') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                            <path d="M10 21h4"></path>
                        </svg>
                        <span class="header-count">{{ $buyer['notification_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Alerts</span>
                </a>

                <a href="{{ url('/buyer/messages') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M4 5h16v11H8l-4 4V5Z"></path>
                        </svg>
                        <span class="header-count">{{ $buyer['message_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Messages</span>
                </a>

                <a href="{{ url('/buyer/cart') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6"></path>
                            <circle cx="9" cy="20" r="1"></circle>
                            <circle cx="17" cy="20" r="1"></circle>
                        </svg>
                        <span class="header-count">{{ $buyer['cart_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Cart</span>
                </a>

                <a href="{{ url('/buyer/account') }}" class="ml-1 flex items-center gap-2 border-l border-[#ece7e0] pl-3">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-[#111] text-xs font-black text-white">
                        {{ strtoupper(substr($buyer['first_name'], 0, 1)) }}
                    </span>
                    <span class="hidden xl:block">
                        <span class="block text-[11px] font-bold">{{ $buyer['first_name'] }}</span>
                        <span class="block text-[9px] text-[#a39c94]">Buyer</span>
                    </span>
                </a>
            </nav>
        </div>
    </div>
</header>

<main>
    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container py-8">
            <div class="text-[11px] text-[#9b958d]">
                <a href="{{ url('/buyer/home') }}" class="transition hover:text-[#d92d2f]">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ url('/buyer/account') }}" class="transition hover:text-[#d92d2f]">My Account</a>
                <span class="mx-2">/</span>
                <span class="text-[#4d4944]">Security</span>
            </div>

            <div class="mt-5">
                <p class="buyer-section-eyebrow">ACCOUNT PROTECTION</p>
                <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">Security</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#8b847c]">
                    Manage your password, review active sessions, and keep your LIKHAE account protected.
                </p>
            </div>
        </div>
    </section>

    <section class="py-8 lg:py-12">
        <div class="likhae-container">
            <div class="grid gap-8 lg:grid-cols-[260px_minmax(0,1fr)]">

                <aside>
                    <nav class="account-nav-card">
                        <a href="{{ url('/buyer/account') }}" class="account-menu-item">Profile</a>
                        <a href="{{ url('/buyer/account/addresses') }}" class="account-menu-item">Addresses</a>
                        <a href="{{ url('/buyer/orders') }}" class="account-menu-item">My Orders</a>
                        <a href="{{ url('/buyer/wishlist') }}" class="account-menu-item">Wishlist</a>
                        <a href="{{ url('/buyer/messages') }}" class="account-menu-item">Messages</a>
                        <a href="{{ url('/buyer/account/security') }}" class="account-menu-item is-active">Security</a>
                    </nav>
                </aside>

                <div class="space-y-5">

                    @if(session('status'))
                        <div class="border border-[#bee5d6] bg-[#f2fbf7] px-4 py-3 text-sm font-semibold text-[#087858]">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Password --}}
                    <section class="security-card">
                        <div class="security-card-header">
                            <div>
                                <p class="security-eyebrow">PASSWORD</p>
                                <h2>Change Password</h2>
                            </div>

                            <p class="text-[10px] text-[#9c958d]">
                                Last updated {{ $buyer['password_updated_at'] }}
                            </p>
                        </div>

                        <form
                            method="POST"
                            action="{{ url('/buyer/account/security/password') }}"
                            class="p-5 sm:p-6"
                        >
                            @csrf
                            @method('PUT')

                            <div class="max-w-2xl space-y-5">
                                <div>
                                    <label for="current_password" class="form-label">Current Password *</label>

                                    <div class="password-field">
                                        <input
                                            id="current_password"
                                            name="current_password"
                                            type="password"
                                            autocomplete="current-password"
                                            required
                                            class="security-input"
                                        >

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            data-target="current_password"
                                            aria-label="Show current password"
                                        >
                                            Show
                                        </button>
                                    </div>

                                    @error('current_password')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="form-label">New Password *</label>

                                    <div class="password-field">
                                        <input
                                            id="password"
                                            name="password"
                                            type="password"
                                            autocomplete="new-password"
                                            minlength="8"
                                            required
                                            class="security-input"
                                        >

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            data-target="password"
                                            aria-label="Show new password"
                                        >
                                            Show
                                        </button>
                                    </div>

                                    @error('password')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror

                                    <div class="mt-3">
                                        <div class="password-strength-track">
                                            <span id="passwordStrengthBar" class="password-strength-bar"></span>
                                        </div>

                                        <p id="passwordStrengthText" class="mt-2 text-[10px] text-[#99928a]">
                                            Enter a strong password.
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label for="password_confirmation" class="form-label">Confirm New Password *</label>

                                    <div class="password-field">
                                        <input
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            type="password"
                                            autocomplete="new-password"
                                            minlength="8"
                                            required
                                            class="security-input"
                                        >

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            data-target="password_confirmation"
                                            aria-label="Show confirmed password"
                                        >
                                            Show
                                        </button>
                                    </div>

                                    <p id="passwordMatchText" class="mt-2 text-[10px] text-[#99928a]">
                                        Re-enter your new password.
                                    </p>
                                </div>

                                <div class="border border-[#e5dfd8] bg-[#faf8f5] p-4">
                                    <p class="text-[10px] font-black uppercase tracking-[0.13em] text-[#746e67]">
                                        Password Guidelines
                                    </p>

                                    <div class="mt-3 grid gap-2 text-[10px] text-[#8b847c] sm:grid-cols-2">
                                        <p id="ruleLength">○ At least 8 characters</p>
                                        <p id="ruleUpper">○ One uppercase letter</p>
                                        <p id="ruleLower">○ One lowercase letter</p>
                                        <p id="ruleNumber">○ One number</p>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <button type="submit" class="primary-action">
                                        Update Password
                                    </button>

                                    <a href="{{ url('/buyer/account') }}" class="secondary-action">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </section>

                    {{-- Security Overview --}}
                    <section class="security-card">
                        <div class="security-card-header">
                            <div>
                                <p class="security-eyebrow">ACCOUNT ACTIVITY</p>
                                <h2>Recent Login</h2>
                            </div>
                        </div>

                        <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-6">
                            <div class="security-info-block">
                                <p class="security-eyebrow">LAST LOGIN</p>
                                <p class="mt-2 text-sm font-black">{{ $buyer['last_login'] }}</p>
                            </div>

                            <div class="security-info-block">
                                <p class="security-eyebrow">LOCATION</p>
                                <p class="mt-2 text-sm font-black">{{ $buyer['last_login_location'] }}</p>
                            </div>

                            <div class="security-info-block">
                                <p class="security-eyebrow">EMAIL</p>
                                <p class="mt-2 break-all text-sm font-black">{{ $buyer['email'] }}</p>
                            </div>

                            <div class="security-info-block">
                                <p class="security-eyebrow">ACCOUNT STATUS</p>
                                <p class="mt-2 inline-flex items-center gap-2 text-sm font-black text-[#079b72]">
                                    <span class="h-2 w-2 rounded-full bg-[#079b72]"></span>
                                    Protected
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- Sessions --}}
                    <section class="security-card">
                        <div class="security-card-header">
                            <div>
                                <p class="security-eyebrow">SESSIONS</p>
                                <h2>Logged-in Devices</h2>
                            </div>
                        </div>

                        <div class="divide-y divide-[#eee8e1]">
                            @foreach($sessions as $session)
                                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                                    <div class="flex gap-4">
                                        <div class="grid h-11 w-11 shrink-0 place-items-center bg-[#f5f2ed] text-[#716a63]">
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <rect x="3" y="4" width="18" height="13" rx="1"></rect>
                                                <path d="M8 21h8"></path>
                                                <path d="M12 17v4"></path>
                                            </svg>
                                        </div>

                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-black">{{ $session['device'] }}</p>

                                                @if($session['current'])
                                                    <span class="current-session-badge">CURRENT DEVICE</span>
                                                @endif
                                            </div>

                                            <p class="mt-1 text-xs text-[#79726b]">
                                                {{ $session['browser'] }} · {{ $session['location'] }}
                                            </p>

                                            <p class="mt-2 text-[10px] {{ $session['current'] ? 'text-[#079b72]' : 'text-[#aaa39b]' }}">
                                                {{ $session['last_active'] }}
                                            </p>
                                        </div>
                                    </div>

                                    @if(!$session['current'])
                                        <form method="POST" action="{{ url('/buyer/account/security/sessions/logout') }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="danger-action">
                                                Sign Out Device
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-[#e9e3dc] bg-[#faf8f5] p-5 sm:p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-xs font-black">Don't recognize a device?</p>
                                    <p class="mt-1 text-[10px] leading-5 text-[#918a82]">
                                        Sign out of other sessions and change your password immediately.
                                    </p>
                                </div>

                                <form
                                    method="POST"
                                    action="{{ url('/buyer/account/security/sessions/logout-others') }}"
                                    onsubmit="return confirm('Sign out of all other devices?');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="secondary-action">
                                        Sign Out Other Devices
                                    </button>
                                </form>
                            </div>
                        </div>
                    </section>

                    {{-- Security Notice --}}
                    <section class="border border-[#d7e9e2] bg-[#f4fbf8] p-5 sm:p-6">
                        <div class="flex gap-4">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#079b72] text-white">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"></path>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-black">Keep Your Account Secure</p>
                                <p class="mt-1 text-xs leading-5 text-[#6f7f79]">
                                    LIKHAE will never ask for your password through chat. Avoid sharing verification codes, passwords, or payment credentials with sellers or couriers.
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- Logout --}}
                    <section class="security-card p-5 sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="security-eyebrow">SESSION</p>
                                <h2 class="mt-1 text-sm font-black">Sign Out</h2>
                                <p class="mt-2 text-xs leading-5 text-[#8e877f]">
                                    End your current LIKHAE session on this device.
                                </p>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="danger-action">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="mt-4 bg-[#0a0a0a] text-white">
    <div class="likhae-container py-12">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.5fr_repeat(4,1fr)]">
            <div>
                <a href="{{ url('/buyer/home') }}" class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black">L</span>
                    <span class="text-xl font-black">LIKHAE</span>
                </a>
                <p class="mt-4 max-w-[230px] text-sm leading-6 text-white/35">
                    Shop More. Discover More. Live More. — Your Philippine marketplace.
                </p>
            </div>

            <div>
                <h3 class="footer-title">SHOP</h3>
                <div class="footer-links">
                    <a href="{{ url('/buyer/products') }}">All Products</a>
                    <a href="{{ url('/buyer/flash-deals') }}">Flash Deals</a>
                    <a href="{{ url('/buyer/local-finds') }}">Local Finds</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">MY ACCOUNT</h3>
                <div class="footer-links">
                    <a href="{{ url('/buyer/orders') }}">My Orders</a>
                    <a href="{{ url('/buyer/wishlist') }}">Wishlist</a>
                    <a href="{{ url('/buyer/messages') }}">Messages</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">SUPPORT</h3>
                <div class="footer-links">
                    <a href="#">Help Center</a>
                    <a href="{{ url('/buyer/orders') }}">Track Order</a>
                    <a href="#">Buyer Protection</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">COMPANY</h3>
                <div class="footer-links">
                    <a href="#">About LIKHAE</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms</a>
                </div>
            </div>
        </div>

        <div class="mt-12 border-t border-white/10 pt-6 text-[10px] text-white/25">
            © {{ date('Y') }} LIKHAE, Inc. — Made with pride in the Philippines.
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.password-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(button.dataset.target);
                const showing = input.type === 'text';

                input.type = showing ? 'password' : 'text';
                button.textContent = showing ? 'Show' : 'Hide';
            });
        });

        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('passwordStrengthText');
        const matchText = document.getElementById('passwordMatchText');

        const rules = {
            length: document.getElementById('ruleLength'),
            upper: document.getElementById('ruleUpper'),
            lower: document.getElementById('ruleLower'),
            number: document.getElementById('ruleNumber'),
        };

        function setRule(element, passed, text) {
            element.textContent = (passed ? '✓ ' : '○ ') + text;
            element.classList.toggle('text-[#079b72]', passed);
            element.classList.toggle('font-bold', passed);
        }

        function updateStrength() {
            const value = password.value;

            const checks = {
                length: value.length >= 8,
                upper: /[A-Z]/.test(value),
                lower: /[a-z]/.test(value),
                number: /\d/.test(value),
            };

            setRule(rules.length, checks.length, 'At least 8 characters');
            setRule(rules.upper, checks.upper, 'One uppercase letter');
            setRule(rules.lower, checks.lower, 'One lowercase letter');
            setRule(rules.number, checks.number, 'One number');

            const score = Object.values(checks).filter(Boolean).length;
            const widths = ['0%', '25%', '50%', '75%', '100%'];
            const labels = ['Enter a strong password.', 'Weak', 'Fair', 'Good', 'Strong'];

            strengthBar.style.width = widths[score];
            strengthText.textContent = labels[score];

            updateMatch();
        }

        function updateMatch() {
            if (!confirmation.value) {
                matchText.textContent = 'Re-enter your new password.';
                matchText.className = 'mt-2 text-[10px] text-[#99928a]';
                return;
            }

            if (password.value === confirmation.value) {
                matchText.textContent = 'Passwords match.';
                matchText.className = 'mt-2 text-[10px] font-bold text-[#079b72]';
            } else {
                matchText.textContent = 'Passwords do not match.';
                matchText.className = 'mt-2 text-[10px] font-bold text-[#d92d2f]';
            }
        }

        password?.addEventListener('input', updateStrength);
        confirmation?.addEventListener('input', updateMatch);
    });
</script>

</body>
</html>