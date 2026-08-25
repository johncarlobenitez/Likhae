<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In — LIKHAE</title>

    @vite([
        'resources/css/guest/auth/login.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">
    <main class="min-h-screen px-4 py-12 sm:py-16">
        <div class="mx-auto w-full max-w-[420px]">

            {{-- Brand --}}
            <a href="{{ url('/') }}" class="mb-8 inline-flex items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">
                    L
                </span>
                <span class="text-xl font-black tracking-tight">LIKHAE</span>
            </a>

            {{-- Auth Card --}}
            <section class="border border-[#ddd6ce] bg-white shadow-[0_18px_45px_rgba(0,0,0,0.06)]">

                {{-- Tabs --}}
                <div class="grid grid-cols-2 border-b border-[#e6e0d8]">
                    <a
                        href="{{ route('login') }}"
                        class="auth-tab is-active"
                    >
                        Sign In
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="auth-tab"
                    >
                        Create Account
                    </a>
                </div>

                <div class="p-7 sm:p-8">
                    <p class="text-center text-sm text-[#746e67]">
                        Welcome back to LIKHAE
                    </p>

                    @if ($errors->any())
                        <div class="mt-5 border border-[#f1c7c8] bg-[#fff2f2] px-4 py-3 text-sm text-[#b82024]">
                            <ul class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mt-5 border border-[#bfe2d7] bg-[#effaf6] px-4 py-3 text-sm text-[#087c5c]">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}" class="mt-7">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label
                                for="email"
                                class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-[#8c857d]"
                            >
                                Email
                            </label>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                                autofocus
                                placeholder="juan@email.com"
                                class="auth-input"
                            >
                        </div>

                        {{-- Password --}}
                        <div class="mt-5">
                            <label
                                for="password"
                                class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-[#8c857d]"
                            >
                                Password
                            </label>

                            <div class="relative">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    placeholder="••••••••"
                                    class="auth-input pr-12"
                                >

                                <button
                                    id="togglePassword"
                                    type="button"
                                    class="absolute inset-y-0 right-0 grid w-12 place-items-center text-[#b3aca4] transition hover:text-[#d92d2f]"
                                    aria-label="Show password"
                                >
                                    <svg id="eyeOpen" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.7"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Remember/Forgot --}}
                        <div class="mt-5 flex items-center justify-between gap-4">
                            <label class="flex cursor-pointer items-center gap-2 text-sm text-[#746e67]">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="h-4 w-4 accent-[#d92d2f]"
                                >
                                <span>Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a
                                    href="{{ route('password.request') }}"
                                    class="text-xs font-semibold text-[#d92d2f] transition hover:text-[#b82024]"
                                >
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button
                            type="submit"
                            class="mt-5 flex h-12 w-full items-center justify-center gap-2 bg-[#d92d2f] px-5 text-sm font-bold text-white shadow-[0_8px_20px_rgba(217,45,47,0.18)] transition hover:bg-[#bd2024]"
                        >
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M10 17l5-5-5-5"></path>
                                <path d="M15 12H4"></path>
                                <path d="M14 4h5v16h-5"></path>
                            </svg>

                            Sign In
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="my-5 flex items-center gap-3">
                        <div class="h-px flex-1 bg-[#e5dfd7]"></div>
                        <span class="text-[11px] text-[#b0a9a1]">or continue with</span>
                        <div class="h-px flex-1 bg-[#e5dfd7]"></div>
                    </div>

                    {{-- Social --}}
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ url('/auth/google') }}" class="social-btn">
                            Google
                        </a>

                        <a href="{{ url('/auth/facebook') }}" class="social-btn">
                            Facebook
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const password = document.getElementById('password');
            const toggle = document.getElementById('togglePassword');

            if (!password || !toggle) return;

            toggle.addEventListener('click', () => {
                const hidden = password.type === 'password';
                password.type = hidden ? 'text' : 'password';
                toggle.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
            });
        });
    </script>
</body>
</html>