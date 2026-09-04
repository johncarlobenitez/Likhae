<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In — LIKHAE</title>

    @vite([
        'resources/css/auth/login.css',
        'resources/js/auth/login.js'
    ])
</head>

<body>

    <div class="auth-shell">

        {{-- LEFT PANEL --}}
        <section class="auth-hero">

            <div class="auth-hero__inner">

                <a href="{{ url('/') }}" class="auth-brand">
                    <span class="auth-brand__mark">
                        L
                    </span>

                    <span class="auth-brand__name">
                        LIKHAE
                    </span>
                </a>


                <div class="auth-hero__content">

                    <div class="auth-hero__copy">

                        <span class="auth-eyebrow">
                            Marketplace Philippines
                        </span>

                        <h1>
                            Your local marketplace,
                            <br>
                            reimagined.
                        </h1>

                        <p>
                            Discover everyday products, local finds, flash deals,
                            and trusted sellers across the Philippines.
                        </p>

                    </div>


                    <div class="auth-product-showcase">

                        <div class="auth-product-card auth-product-card--large">
                            <div class="auth-product-visual">
                                🎧
                            </div>

                            <span>
                                Electronics
                            </span>
                        </div>


                        <div class="auth-product-column">

                            <div class="auth-product-card">
                                <div class="auth-product-visual">
                                    👟
                                </div>

                                <span>
                                    Fashion
                                </span>
                            </div>


                            <div class="auth-product-card">
                                <div class="auth-product-visual">
                                    👜
                                </div>

                                <span>
                                    Local Finds
                                </span>
                            </div>

                        </div>

                    </div>


                    <ul class="auth-hero__pills">

                        <li>
                            <span class="auth-pill-icon">
                                ✓
                            </span>

                            Thousands of products
                        </li>

                        <li>
                            <span class="auth-pill-icon">
                                ✓
                            </span>

                            Nationwide delivery
                        </li>

                        <li>
                            <span class="auth-pill-icon">
                                ✓
                            </span>

                            Secure checkout
                        </li>

                    </ul>

                </div>


                <div class="auth-hero__footer">
                    Shop smarter with LIKHAE.
                </div>


                <div
                    class="auth-hero__deco"
                    aria-hidden="true"
                >
                    <div class="deco-ring deco-ring--1"></div>

                    <div class="deco-ring deco-ring--2"></div>
                </div>

            </div>

        </section>


        {{-- RIGHT PANEL --}}
        <section class="auth-panel">

            <div class="auth-panel__inner">

                <div class="auth-mobile-brand">

                    <a href="{{ url('/') }}" class="auth-brand">

                        <span class="auth-brand__mark">
                            L
                        </span>

                        <span class="auth-brand__name">
                            LIKHAE
                        </span>

                    </a>

                </div>


                <header class="auth-panel__head">

                    <span class="auth-panel__eyebrow">
                        
                    </span>

                    <h2>
                        Welcome back
                    </h2>

                    <p>
                        Sign in to continue shopping on LIKHAE.
                    </p>

                </header>


                @if ($errors->any())

                    <div class="auth-alert auth-alert--error">

                        @foreach ($errors->all() as $error)

                            <span>
                                {{ $error }}
                            </span>

                        @endforeach

                    </div>

                @endif


                @if (session('status'))

                    <div class="auth-alert auth-alert--success">
                        {{ session('status') }}
                    </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('login.post') }}"
                    class="auth-form"
                >

                    @csrf


                    <div class="auth-field">

                        <label for="email">
                            Email address
                        </label>

                        <div class="auth-input-wrap">

                            <span class="auth-input-icon">
                                @
                            </span>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="juan@email.com"
                                autocomplete="email"
                                required
                                autofocus
                            >

                        </div>

                    </div>


                    <div class="auth-field">

                        <div class="auth-field__row">

                            <label for="password">
                                Password
                            </label>

                            <a
                                href="#"
                                class="auth-forgot"
                            >
                                Forgot password?
                            </a>

                        </div>


                        <div class="auth-field__wrap">

                            <span class="auth-input-icon">
                                •
                            </span>

                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                            >


                            <button
                                type="button"
                                id="togglePassword"
                                class="auth-eye"
                                aria-label="Show password"
                            >

                                <svg
                                    id="eyeOpen"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"
                                    />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="2.7"
                                    />
                                </svg>


                                <svg
                                    id="eyeClosed"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="hidden"
                                >
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"
                                    />

                                    <path
                                        d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"
                                    />

                                    <line
                                        x1="1"
                                        y1="1"
                                        x2="23"
                                        y2="23"
                                    />
                                </svg>

                            </button>

                        </div>

                    </div>


                    <div class="auth-form-options">

                        <label class="auth-remember">

                            <input
                                type="checkbox"
                                name="remember"
                            >

                            <span>
                                Remember me
                            </span>

                        </label>


                        <span class="auth-security-text">
                            Secure login
                        </span>

                    </div>


                    <button
                        type="submit"
                        class="auth-submit"
                    >

                        <span>
                            Sign In
                        </span>

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M5 12h14" />
                            <path d="M12 5l7 7-7 7" />
                        </svg>

                    </button>

                </form>


                <div class="auth-divider">

                    <span>
                        or continue with
                    </span>

                </div>


                <div class="auth-socials">

                    <a
                        href="{{ url('/auth/google') }}"
                        class="auth-social"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            width="18"
                            height="18"
                        >
                            <path
                                fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            />

                            <path
                                fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            />

                            <path
                                fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
                            />

                            <path
                                fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            />
                        </svg>

                        Google

                    </a>


                    <a
                        href="#"
                        class="auth-social"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            width="18"
                            height="18"
                            fill="#1877F2"
                        >
                            <path
                                d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"
                            />
                        </svg>

                        Facebook

                    </a>

                </div>


                <p class="auth-switch">

                    Don't have an account?

                    <a href="{{ route('register') }}">
                        Create one — it's free
                    </a>

                </p>


                <div class="auth-trust">

                    <span>
                        🔒 Secure
                    </span>

                    <span>
                        ✓ Buyer protected
                    </span>

                    <span>
                        🇵🇭 Local marketplace
                    </span>

                </div>

            </div>

        </section>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const password = document.getElementById('password');

            const toggleButton = document.getElementById('togglePassword');

            const eyeOpen = document.getElementById('eyeOpen');

            const eyeClosed = document.getElementById('eyeClosed');


            toggleButton?.addEventListener('click', () => {

                if (!password) {
                    return;
                }


                const showPassword = password.type === 'password';


                password.type = showPassword
                    ? 'text'
                    : 'password';


                eyeOpen?.classList.toggle(
                    'hidden',
                    showPassword
                );


                eyeClosed?.classList.toggle(
                    'hidden',
                    !showPassword
                );


                toggleButton.setAttribute(
                    'aria-label',
                    showPassword
                        ? 'Hide password'
                        : 'Show password'
                );

            });

        });
    </script>

</body>
</html>