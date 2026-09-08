<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <title>{{ $accountLabel }} Login — LIKHAE</title>

    @vite([
        'resources/css/auth/login.css',
        'resources/js/auth/login.js'
    ])
</head>
<body>
<div class="auth-shell">
    <section class="auth-hero">
        <div class="auth-hero__inner">
            <a href="{{ $homeRoute }}" class="auth-brand">
                <x-likhae-logo :context="$accountLabel" class="likhae-logo--auth" />
            </a>

            <div class="auth-hero__content">
                <div class="auth-hero__copy">
                    <span class="auth-eyebrow">{{ $accountLabel }} workspace</span>

                    <h1>{{ $headline }}</h1>

                    <p>{{ $description }}</p>
                </div>

                <ul class="auth-hero__pills">
                    @if ($workspace === 'logistics')
                        <li><span class="auth-pill-icon">✓</span> Parcel intake and sorting</li>
                        <li><span class="auth-pill-icon">✓</span> Rider application management</li>
                        <li><span class="auth-pill-icon">✓</span> Delivery assignment monitoring</li>
                    @else
                        <li><span class="auth-pill-icon">✓</span> Pickup assignments</li>
                        <li><span class="auth-pill-icon">✓</span> Delivery assignments</li>
                        <li><span class="auth-pill-icon">✓</span> Earnings and delivery history</li>
                    @endif
                </ul>
            </div>

            <div class="auth-hero__footer">
                LIKHAE · {{ $accountLabel }}
            </div>

            <div class="auth-hero__deco" aria-hidden="true">
                <div class="deco-ring deco-ring--1"></div>
                <div class="deco-ring deco-ring--2"></div>
            </div>
        </div>
    </section>

    <section class="auth-panel">
        <div class="auth-panel__inner">
            <div class="auth-mobile-brand">
                <a href="{{ $homeRoute }}" class="auth-brand">
                    <x-likhae-logo :context="$accountLabel" class="likhae-logo--auth" />
                </a>
            </div>

            <header class="auth-panel__head">
                <span class="auth-panel__eyebrow">{{ $accountLabel }}</span>
                <h2>Welcome back</h2>
                <p>Sign in to enter your dedicated {{ strtolower($accountLabel) }} workspace.</p>
            </header>

            @if ($errors->any())
                <div class="auth-alert auth-alert--error">
                    @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div class="auth-alert auth-alert--success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ $loginRoute }}" class="auth-form">
                @csrf

                <div class="auth-field">
                    <label for="email">Email address</label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon">@</span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="{{ $demoEmail }}"
                            autocomplete="email"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="auth-field">
                    <div class="auth-field__row">
                        <label for="password">Password</label>
                    </div>

                    <div class="auth-field__wrap">
                        <span class="auth-input-icon">•</span>
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
                            <svg id="eyeOpen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" />
                                <circle cx="12" cy="12" r="2.7" />
                            </svg>

                            <svg id="eyeClosed" class="hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m3 3 18 18" />
                                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8" />
                                <path d="M9.9 4.2A10.8 10.8 0 0 1 12 4c6 0 9.5 8 9.5 8a15.8 15.8 0 0 1-2.1 3.2" />
                                <path d="M6.2 6.2C3.7 8 2.5 12 2.5 12s3.5 8 9.5 8a9.8 9.8 0 0 0 4.2-.9" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-submit">
                    <span>Sign in to {{ $accountLabel }}</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M5 12h14"></path>
                        <path d="m13 6 6 6-6 6"></path>
                    </svg>
                </button>
            </form>

            <div class="auth-divider">Demo access</div>

            <div class="auth-alert auth-alert--success">
                <span><strong>Email:</strong> {{ $demoEmail }}</span>
                <span><strong>Password:</strong> {{ $demoPassword }}</span>
            </div>

            <p class="auth-switch">
                Need a {{ strtolower($accountLabel) }} account?
                <a href="{{ $registerRoute }}">Register here</a>
            </p>

            <p class="auth-switch" style="margin-top: 10px;">
                <a href="{{ route('login') }}">Return to marketplace login</a>
            </p>
        </div>
    </section>
</div>
</body>
</html>
