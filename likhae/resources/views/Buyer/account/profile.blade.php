<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>My Account — LIKHAE</title>

    @vite([
        'resources/css/buyer/profile.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo Buyer Profile Data
    |--------------------------------------------------------------------------
    | Replace with authenticated user/profile data from your controller.
    */
    $buyer = [
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'middle_initial' => 'M',
        'full_name' => 'Juan M. Dela Cruz',
        'email' => 'juan.delacruz@example.com',
        'contact_number' => '0917 123 4567',
        'sex' => 'Male',
        'birthday' => 'April 12, 2002',
        'status' => 'Approved',
        'member_since' => 'August 2026',
        'cart_count' => 2,
        'message_count' => 3,
        'notification_count' => 4,
        'profile_photo' => null,
    ];

    $stats = [
        ['label' => 'Orders', 'value' => 8],
        ['label' => 'Wishlist', 'value' => 12],
        ['label' => 'Reviews', 'value' => 3],
    ];
@endphp

{{-- =========================================================
     HEADER
========================================================= --}}
<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container">
        <div class="flex h-16 items-center gap-4">
            <a href="{{ url('/buyer/home') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">
                    L
                </span>
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
                        <span class="block text-[9px] text-[#d92d2f]">My Account</span>
                    </span>
                </a>
            </nav>
        </div>
    </div>
</header>

<main>

    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}
    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container py-8">
            <div class="text-[11px] text-[#9b958d]">
                <a href="{{ url('/buyer/home') }}" class="transition hover:text-[#d92d2f]">Home</a>
                <span class="mx-2">/</span>
                <span class="text-[#4d4944]">My Account</span>
            </div>

            <div class="mt-5">
                <p class="buyer-section-eyebrow">ACCOUNT MANAGEMENT</p>

                <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">
                    My Account
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#8b847c]">
                    Manage your personal information, addresses, security, and shopping preferences.
                </p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         ACCOUNT CONTENT
    ====================================================== --}}
    <section class="py-8 lg:py-12">
        <div class="likhae-container">
            <div class="grid gap-8 lg:grid-cols-[280px_minmax(0,1fr)]">

                {{-- =========================================
                     SIDEBAR
                ========================================== --}}
                <aside class="space-y-4">

                    {{-- Profile Summary --}}
                    <section class="account-card p-5 text-center">
                        <div class="relative mx-auto w-fit">
                            @if($buyer['profile_photo'])
                                <img
                                    src="{{ $buyer['profile_photo'] }}"
                                    alt="{{ $buyer['full_name'] }}"
                                    class="h-24 w-24 rounded-full object-cover"
                                >
                            @else
                                <div class="grid h-24 w-24 place-items-center rounded-full bg-[#111] text-2xl font-black text-white">
                                    {{ strtoupper(substr($buyer['first_name'], 0, 1) . substr($buyer['last_name'], 0, 1)) }}
                                </div>
                            @endif

                            <label
                                for="profilePhoto"
                                class="absolute bottom-0 right-0 grid h-8 w-8 cursor-pointer place-items-center rounded-full border-2 border-white bg-[#d92d2f] text-white transition hover:bg-[#bd2024]"
                                title="Change profile photo"
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 7h4l2-2h4l2 2h4v12H4z"></path>
                                    <circle cx="12" cy="13" r="3"></circle>
                                </svg>
                            </label>

                            <input
                                id="profilePhoto"
                                type="file"
                                accept="image/jpeg,image/png"
                                class="sr-only"
                            >
                        </div>

                        <h2 class="mt-4 text-base font-black">
                            {{ $buyer['full_name'] }}
                        </h2>

                        <p class="mt-1 text-xs text-[#928b83]">
                            {{ $buyer['email'] }}
                        </p>

                        <div class="mt-4 flex items-center justify-center gap-2">
                            <span class="approved-badge">
                                {{ strtoupper($buyer['status']) }}
                            </span>

                            <span class="buyer-badge">
                                BUYER
                            </span>
                        </div>

                        <p class="mt-4 text-[10px] text-[#aaa39b]">
                            Member since {{ $buyer['member_since'] }}
                        </p>

                        <div class="mt-5 grid grid-cols-3 border-t border-[#e8e2da] pt-5">
                            @foreach($stats as $stat)
                                <div>
                                    <p class="text-lg font-black">{{ $stat['value'] }}</p>
                                    <p class="mt-1 text-[9px] uppercase tracking-[0.1em] text-[#9a938b]">
                                        {{ $stat['label'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    {{-- Account Navigation --}}
                    <nav class="account-card overflow-hidden">
                        <a href="{{ url('/buyer/account') }}" class="account-menu-item is-active">
                            <span class="account-menu-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M4 21c.7-4 3.4-6 8-6s7.3 2 8 6"></path>
                                </svg>
                            </span>
                            <span>Profile</span>
                        </a>

                        <a href="{{ url('/buyer/account/addresses') }}" class="account-menu-item">
                            <span class="account-menu-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"></path>
                                    <circle cx="12" cy="10" r="2"></circle>
                                </svg>
                            </span>
                            <span>Addresses</span>
                        </a>

                        <a href="{{ url('/buyer/orders') }}" class="account-menu-item">
                            <span class="account-menu-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="m4 7 8-4 8 4-8 4-8-4Z"></path>
                                    <path d="M4 7v10l8 4 8-4V7"></path>
                                </svg>
                            </span>
                            <span>My Orders</span>
                        </a>

                        <a href="{{ url('/buyer/wishlist') }}" class="account-menu-item">
                            <span class="account-menu-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"></path>
                                </svg>
                            </span>
                            <span>Wishlist</span>
                        </a>

                        <a href="{{ url('/buyer/messages') }}" class="account-menu-item">
                            <span class="account-menu-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M4 5h16v11H8l-4 4V5Z"></path>
                                </svg>
                            </span>
                            <span>Messages</span>
                        </a>

                        <a href="{{ url('/buyer/account/security') }}" class="account-menu-item">
                            <span class="account-menu-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <rect x="5" y="10" width="14" height="10" rx="1"></rect>
                                    <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                                </svg>
                            </span>
                            <span>Security</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="border-t border-[#e9e3dc]">
                            @csrf

                            <button type="submit" class="account-menu-item w-full text-left text-[#d92d2f]">
                                <span class="account-menu-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M10 17l5-5-5-5"></path>
                                        <path d="M15 12H3"></path>
                                        <path d="M14 3h6v18h-6"></path>
                                    </svg>
                                </span>
                                <span>Logout</span>
                            </button>
                        </form>
                    </nav>
                </aside>

                {{-- =========================================
                     MAIN PROFILE CONTENT
                ========================================== --}}
                <div class="space-y-5">

                    {{-- Personal Information --}}
                    <section class="account-card">
                        <div class="account-card-header">
                            <div>
                                <p class="account-eyebrow">PROFILE</p>
                                <h2>Personal Information</h2>
                            </div>

                            <button
                                id="editProfileButton"
                                type="button"
                                class="secondary-action"
                            >
                                Edit Profile
                            </button>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
                                <div class="profile-field">
                                    <p class="profile-field-label">First Name</p>
                                    <p>{{ $buyer['first_name'] }}</p>
                                </div>

                                <div class="profile-field">
                                    <p class="profile-field-label">Last Name</p>
                                    <p>{{ $buyer['last_name'] }}</p>
                                </div>

                                <div class="profile-field">
                                    <p class="profile-field-label">Middle Initial</p>
                                    <p>{{ $buyer['middle_initial'] ?: '—' }}</p>
                                </div>

                                <div class="profile-field">
                                    <p class="profile-field-label">Sex</p>
                                    <p>{{ $buyer['sex'] }}</p>
                                </div>

                                <div class="profile-field">
                                    <p class="profile-field-label">Birthday</p>
                                    <p>{{ $buyer['birthday'] }}</p>
                                </div>

                                <div class="profile-field">
                                    <p class="profile-field-label">Account Status</p>
                                    <p class="font-bold text-[#079b72]">
                                        {{ $buyer['status'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Contact Information --}}
                    <section class="account-card">
                        <div class="account-card-header">
                            <div>
                                <p class="account-eyebrow">CONTACT</p>
                                <h2>Contact Information</h2>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
                                <div class="profile-field">
                                    <p class="profile-field-label">Email Address</p>
                                    <p>{{ $buyer['email'] }}</p>

                                    <span class="mt-2 inline-flex items-center gap-1 text-[9px] font-bold text-[#079b72]">
                                        <span class="h-1.5 w-1.5 rounded-full bg-[#079b72]"></span>
                                        VERIFIED
                                    </span>
                                </div>

                                <div class="profile-field">
                                    <p class="profile-field-label">Contact Number</p>
                                    <p>{{ $buyer['contact_number'] }}</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Quick Actions --}}
                    <section>
                        <div class="mb-4">
                            <p class="account-eyebrow">QUICK ACCESS</p>
                            <h2 class="mt-2 text-lg font-black">Manage Your Account</h2>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            <a href="{{ url('/buyer/account/addresses') }}" class="quick-account-card">
                                <span class="quick-account-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"></path>
                                        <circle cx="12" cy="10" r="2"></circle>
                                    </svg>
                                </span>

                                <span>
                                    <strong>My Addresses</strong>
                                    <small>Add or manage delivery addresses.</small>
                                </span>

                                <span class="ml-auto">→</span>
                            </a>

                            <a href="{{ url('/buyer/orders') }}" class="quick-account-card">
                                <span class="quick-account-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="m4 7 8-4 8 4-8 4-8-4Z"></path>
                                        <path d="M4 7v10l8 4 8-4V7"></path>
                                    </svg>
                                </span>

                                <span>
                                    <strong>My Orders</strong>
                                    <small>Track and manage your purchases.</small>
                                </span>

                                <span class="ml-auto">→</span>
                            </a>

                            <a href="{{ url('/buyer/wishlist') }}" class="quick-account-card">
                                <span class="quick-account-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"></path>
                                    </svg>
                                </span>

                                <span>
                                    <strong>Wishlist</strong>
                                    <small>View your saved products.</small>
                                </span>

                                <span class="ml-auto">→</span>
                            </a>

                            <a href="{{ url('/buyer/messages') }}" class="quick-account-card">
                                <span class="quick-account-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M4 5h16v11H8l-4 4V5Z"></path>
                                    </svg>
                                </span>

                                <span>
                                    <strong>Messages</strong>
                                    <small>Chat with sellers and couriers.</small>
                                </span>

                                <span class="ml-auto">→</span>
                            </a>

                            <a href="{{ url('/buyer/account/security') }}" class="quick-account-card">
                                <span class="quick-account-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <rect x="5" y="10" width="14" height="10" rx="1"></rect>
                                        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                                    </svg>
                                </span>

                                <span>
                                    <strong>Security</strong>
                                    <small>Change your password and security settings.</small>
                                </span>

                                <span class="ml-auto">→</span>
                            </a>

                            <a href="{{ url('/buyer/notifications') }}" class="quick-account-card">
                                <span class="quick-account-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                        <path d="M10 21h4"></path>
                                    </svg>
                                </span>

                                <span>
                                    <strong>Notifications</strong>
                                    <small>View account and order updates.</small>
                                </span>

                                <span class="ml-auto">→</span>
                            </a>
                        </div>
                    </section>

                    {{-- Account Protection --}}
                    <section class="border border-[#d7e9e2] bg-[#f4fbf8] p-5 sm:p-6">
                        <div class="flex gap-4">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#079b72] text-white">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"></path>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-black">Account Verified</p>
                                <p class="mt-1 text-xs leading-5 text-[#6f7f79]">
                                    Your buyer account is approved. Keep your contact information and password secure to protect your LIKHAE account.
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</main>

{{-- =========================================================
     EDIT PROFILE MODAL
========================================================= --}}
<div
    id="editProfileModal"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/45 p-4"
    aria-hidden="true"
>
    <div class="w-full max-w-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-[#e5dfd8] px-5 py-4 sm:px-6">
            <div>
                <p class="account-eyebrow">EDIT ACCOUNT</p>
                <h2 class="mt-1 text-lg font-black">Personal Information</h2>
            </div>

            <button
                id="closeProfileModal"
                type="button"
                class="grid h-9 w-9 place-items-center border border-[#ddd6ce] text-lg text-[#706961] transition hover:border-[#d92d2f] hover:text-[#d92d2f]"
            >
                ×
            </button>
        </div>

        <form
            method="POST"
            action="{{ url('/buyer/account/profile') }}"
            class="p-5 sm:p-6"
        >
            @csrf
            @method('PUT')

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="first_name" class="form-label">First Name</label>
                    <input
                        id="first_name"
                        name="first_name"
                        type="text"
                        value="{{ $buyer['first_name'] }}"
                        required
                        class="profile-input"
                    >
                </div>

                <div>
                    <label for="last_name" class="form-label">Last Name</label>
                    <input
                        id="last_name"
                        name="last_name"
                        type="text"
                        value="{{ $buyer['last_name'] }}"
                        required
                        class="profile-input"
                    >
                </div>

                <div>
                    <label for="middle_initial" class="form-label">Middle Initial</label>
                    <input
                        id="middle_initial"
                        name="middle_initial"
                        type="text"
                        maxlength="1"
                        value="{{ $buyer['middle_initial'] }}"
                        class="profile-input"
                    >
                </div>

                <div>
                    <label for="sex" class="form-label">Sex</label>
                    <select id="sex" name="sex" class="profile-input">
                        <option value="Male" {{ $buyer['sex'] === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $buyer['sex'] === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ $buyer['sex'] === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="email" class="form-label">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ $buyer['email'] }}"
                        required
                        class="profile-input"
                    >
                </div>

                <div>
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input
                        id="contact_number"
                        name="contact_number"
                        type="tel"
                        value="{{ $buyer['contact_number'] }}"
                        required
                        class="profile-input"
                    >
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button
                    id="cancelProfileEdit"
                    type="button"
                    class="secondary-action justify-center"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="primary-action"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================================
     FOOTER
========================================================= --}}
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
        const modal = document.getElementById('editProfileModal');
        const openButton = document.getElementById('editProfileButton');
        const closeButton = document.getElementById('closeProfileModal');
        const cancelButton = document.getElementById('cancelProfileEdit');
        const photoInput = document.getElementById('profilePhoto');

        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }

        openButton?.addEventListener('click', openModal);
        closeButton?.addEventListener('click', closeModal);
        cancelButton?.addEventListener('click', closeModal);

        modal?.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        photoInput?.addEventListener('change', function () {
            if (photoInput.files && photoInput.files.length) {
                alert('Selected profile photo: ' + photoInput.files[0].name + '\n\nConnect this input to your profile photo upload route later.');
            }
        });
    });
</script>

</body>
</html>