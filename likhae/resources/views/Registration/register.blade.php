<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account - LIKHAE</title>
    @vite(['resources/css/Guest/register.css', 'resources/js/auth/register.js'])
</head>
<body class="min-h-screen bg-[#f5f2ed] text-[#111] antialiased">
<main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 sm:py-12">
    <a href="{{ url('/') }}" class="mb-8 inline-flex items-center gap-2"><span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">L</span><span class="text-xl font-black tracking-tight">LIKHAE</span></a>
    <section class="overflow-hidden border border-[#ddd6ce] bg-white shadow-[0_18px_45px_rgba(0,0,0,.06)]">
        <div class="grid grid-cols-2 border-b border-[#e6e0d8]"><a href="{{ route('login') }}" class="auth-tab">Sign In</a><a href="{{ route('register') }}" class="auth-tab is-active">Create Account</a></div>
        <div class="p-5 sm:p-9">
            <div class="text-center"><p class="text-sm font-semibold text-[#5f5953]">Create your LIKHAE account</p><p class="mt-2 text-xs leading-5 text-[#9a938b]">Choose an account type, then complete its registration form.</p></div>
            @if ($errors->any())<div class="mt-6 border border-[#f1c7c8] bg-[#fff2f2] px-4 py-3 text-sm text-[#b82024]" role="alert"><ul class="space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <section class="mt-8"><div class="form-section-heading"><span class="form-section-number">01</span><div><h2>Account Type</h2><p>Select one registration path.</p></div></div><div class="mt-5 grid gap-4 md:grid-cols-3"><button type="button" class="role-card is-selected" data-form-target="buyer-form"><span class="role-icon">B</span><span><strong>BUYER</strong><small>Shop from local makers.</small></span></button><button type="button" class="role-card" data-form-target="seller-form"><span class="role-icon">S</span><span><strong>SELLER</strong><small>Grow your LIKHAE store.</small></span></button><button type="button" class="role-card" data-form-target="courier-form"><span class="role-icon">C</span><span><strong>COURIER</strong><small>Deliver orders locally.</small></span></button></div></section>

            @include('Registration.partials.registration-form', ['formId' => 'buyer-form', 'role' => 'buyer', 'title' => 'Buyer Registration'])
            @include('Registration.partials.registration-form', ['formId' => 'seller-form', 'role' => 'seller', 'title' => 'Seller Registration', 'seller' => true])
            @include('Registration.partials.registration-form', ['formId' => 'courier-form', 'role' => 'courier', 'title' => 'Courier Registration', 'courier' => true])
        </div>
    </section>
</main>
</body>
</html>
