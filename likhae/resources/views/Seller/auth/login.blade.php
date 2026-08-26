@extends('Seller.layouts.auth')
@section('title', 'Seller Login — LIKHAE')

@section('content')
<section class="auth-grid">
    <div class="auth-story">
        <p class="page-eyebrow">LIKHAE SELLER CENTER</p>
        <h1 class="mt-4 max-w-lg text-4xl font-black tracking-[-.05em] sm:text-5xl">Run your shop with clarity.</h1>
        <p class="mt-5 max-w-xl text-sm leading-7 text-[#6B6864]">Manage orders, products, inventory, shipping, customer messages, and earnings from one focused seller workspace.</p>

        <div class="mt-10 grid gap-3 sm:grid-cols-3">
            <div class="auth-feature"><strong>Orders</strong><span>Fulfill faster</span></div>
            <div class="auth-feature"><strong>Inventory</strong><span>Stay in stock</span></div>
            <div class="auth-feature"><strong>Finance</strong><span>Know your earnings</span></div>
        </div>
    </div>

    <div class="auth-card">
        <p class="page-eyebrow">SELLER ACCESS</p>
        <h2 class="mt-2 text-2xl font-black">Welcome back, Seller</h2>
        <p class="mt-2 text-sm text-[#6B6864]">Sign in to manage your LIKHAE shop.</p>

        @if(session('status'))
            <div class="alert alert-info mt-5">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="form-label" for="email">Email</label>
                <input id="email" name="email" type="email" class="form-input" value="{{ old('email') }}" required autofocus>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label class="form-label" for="password">Password</label>
                    <a href="#" class="text-[11px] font-bold text-[#D92D2F]">Forgot Password?</a>
                </div>
                <input id="password" name="password" type="password" class="form-input" required>
                @error('password')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <label class="flex items-center gap-2 text-xs text-[#6B6864]">
                <input type="checkbox" name="remember" class="h-4 w-4 accent-[#D92D2F]">
                Remember me
            </label>

            <button class="btn-primary w-full" type="submit">Sign In</button>
        </form>

        <p class="mt-6 border-t border-[#E5E0D9] pt-5 text-center text-xs text-[#6B6864]">
            New to LIKHAE Seller?
            <a href="{{ url('/seller/register') }}" class="font-black text-[#D92D2F]">Apply as a Seller</a>
        </p>
    </div>
</section>
@endsection
