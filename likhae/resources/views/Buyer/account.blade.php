@extends('layouts.buyer')

@section('title', 'Account — LIKHAE')
@section('active', 'account')

@section('content')
@php
    $user = auth()->user();

    $buyerName = data_get($user, 'name', 'Buyer Name');
    $buyerEmail = data_get($user, 'email', 'buyer@example.com');
    $buyerPhone = data_get($user, 'phone', '');
    $buyerBirthday = data_get($user, 'birthday', '');
    $buyerGender = data_get($user, 'gender', '');

    $profilePhoto = data_get($user, 'profile_photo')
        ?? data_get($user, 'profile_picture')
        ?? null;

    $profilePhotoUrl = $profilePhoto
        ? asset('storage/' . ltrim($profilePhoto, '/'))
        : null;

    $buyerInitial = strtoupper(mb_substr($buyerName, 0, 1));

    $allowedTabs = [
        'profile',
        'addresses',
        'password',
        'privacy',
        'deletion',
        'notifications',
    ];

    $requestedTab = request('tab', 'profile');

    $activeTab = in_array($requestedTab, $allowedTabs, true)
        ? $requestedTab
        : 'profile';

    $tabs = [
        'profile' => [
            'label' => 'Profile',
            'description' => 'Personal information',
        ],
        'addresses' => [
            'label' => 'Addresses',
            'description' => 'Delivery locations',
        ],
        'password' => [
            'label' => 'Change Password',
            'description' => 'Account security',
        ],
        'privacy' => [
            'label' => 'Privacy Settings',
            'description' => 'Data and visibility',
        ],
        'deletion' => [
            'label' => 'Account Deletion',
            'description' => 'Close your account',
        ],
        'notifications' => [
            'label' => 'Notification Settings',
            'description' => 'Alerts and updates',
        ],
    ];

    $tabIcon = function (string $tab): string {
        return match ($tab) {
            'profile' => '
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M20 21a8 8 0 0 0-16 0"/>
                </svg>
            ',

            'addresses' => '
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/>
                    <circle cx="12" cy="10" r="2.5"/>
                </svg>
            ',

            'password' => '
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="4" y="10" width="16" height="11" rx="2"/>
                    <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                    <path d="M12 14v3"/>
                </svg>
            ',

            'privacy' => '
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3 5 6v5c0 4.8 2.9 8.4 7 10 4.1-1.6 7-5.2 7-10V6z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            ',

            'deletion' => '
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 7h16"/>
                    <path d="M9 7V4h6v3"/>
                    <path d="m6 7 1 14h10l1-14"/>
                    <path d="M10 11v6M14 11v6"/>
                </svg>
            ',

            'notifications' => '
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M10 21h4"/>
                </svg>
            ',

            default => '
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                </svg>
            ',
        };
    };
@endphp

<div class="mx-auto w-full max-w-[1500px] px-4 py-6 sm:px-6 lg:px-8">
    {{-- Page heading --}}
    <header class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-700">
                Buyer Center
            </span>

            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-stone-900">
                Account Management
            </h1>

            <p class="mt-1 text-sm text-stone-500">
                Manage your profile, addresses, security, and account preferences.
            </p>
        </div>

        <span class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            Account active
        </span>
    </header>

    <div>
        {{-- Account content --}}
        <main class="min-w-0 rounded-2xl border border-stone-200 bg-white shadow-sm">
            @if ($activeTab === 'profile')
                <section aria-labelledby="profile-heading">
                    <div class="border-b border-stone-100 px-5 py-4 sm:px-6">
                        <h2 id="profile-heading" class="text-base font-semibold text-stone-900">
                            Profile Information
                        </h2>

                        <p class="mt-1 text-xs text-stone-500">
                            Update your personal details and profile photo.
                        </p>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="mb-6 flex flex-col gap-4 rounded-xl border border-stone-200 bg-stone-50 p-4 sm:flex-row sm:items-center">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full bg-amber-100 text-xl font-bold text-amber-800">
                                @if ($profilePhotoUrl)
                                    <img
                                        src="{{ $profilePhotoUrl }}"
                                        alt="{{ $buyerName }}"
                                        class="h-full w-full object-cover"
                                    >
                                @else
                                    {{ $buyerInitial }}
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-semibold text-stone-900">
                                    Profile photo
                                </h3>

                                <p class="mt-1 text-xs leading-5 text-stone-500">
                                    Upload a JPG or PNG image. Maximum file size is 2 MB.
                                </p>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <label class="inline-flex cursor-pointer items-center rounded-lg bg-amber-600 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-amber-700">
                                        Upload photo

                                        <input
                                            type="file"
                                            name="profile_photo"
                                            accept="image/png,image/jpeg"
                                            class="sr-only"
                                        >
                                    </label>

                                    <button
                                        type="button"
                                        class="rounded-lg border border-stone-300 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 transition hover:bg-stone-100"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <form data-account-form>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label for="profile-name" class="mb-1.5 block text-xs font-semibold text-stone-700">
                                        Full name
                                    </label>

                                    <input
                                        id="profile-name"
                                        type="text"
                                        name="name"
                                        value="{{ $buyerName }}"
                                        class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                    >
                                </div>

                                <div>
                                    <label for="profile-email" class="mb-1.5 block text-xs font-semibold text-stone-700">
                                        Email address
                                    </label>

                                    <input
                                        id="profile-email"
                                        type="email"
                                        name="email"
                                        value="{{ $buyerEmail }}"
                                        class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                    >
                                </div>

                                <div>
                                    <label for="profile-phone" class="mb-1.5 block text-xs font-semibold text-stone-700">
                                        Mobile number
                                    </label>

                                    <input
                                        id="profile-phone"
                                        type="tel"
                                        name="phone"
                                        value="{{ $buyerPhone }}"
                                        placeholder="09XX XXX XXXX"
                                        class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                    >
                                </div>

                                <div>
                                    <label for="profile-birthday" class="mb-1.5 block text-xs font-semibold text-stone-700">
                                        Date of birth
                                    </label>

                                    <input
                                        id="profile-birthday"
                                        type="date"
                                        name="birthday"
                                        value="{{ $buyerBirthday }}"
                                        class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                    >
                                </div>

                                <div>
                                    <label for="profile-gender" class="mb-1.5 block text-xs font-semibold text-stone-700">
                                        Gender
                                    </label>

                                    <select
                                        id="profile-gender"
                                        name="gender"
                                        class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                    >
                                        <option value="">Prefer not to say</option>
                                        <option value="male" @selected($buyerGender === 'male')>Male</option>
                                        <option value="female" @selected($buyerGender === 'female')>Female</option>
                                        <option value="other" @selected($buyerGender === 'other')>Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end border-t border-stone-100 pt-5">
                                <button
                                    type="button"
                                    class="rounded-xl bg-amber-600 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-200"
                                >
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            @endif

            @if ($activeTab === 'addresses')
                <section aria-labelledby="addresses-heading">
                    <div class="flex flex-col gap-3 border-b border-stone-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <h2 id="addresses-heading" class="text-base font-semibold text-stone-900">
                                Delivery Addresses
                            </h2>

                            <p class="mt-1 text-xs text-stone-500">
                                Manage the addresses used for your orders.
                            </p>
                        </div>

                        <button
                            type="button"
                            class="w-fit rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                        >
                            Add New Address
                        </button>
                    </div>

                    <div class="space-y-4 p-5 sm:p-6">
                        <article class="rounded-xl border-2 border-amber-300 bg-amber-50/40 p-4">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-semibold text-stone-900">
                                            {{ $buyerName }}
                                        </h3>

                                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-amber-800">
                                            Default
                                        </span>
                                    </div>

                                    <p class="mt-2 text-xs font-medium text-stone-600">
                                        0912 345 6789
                                    </p>

                                    <p class="mt-2 max-w-xl text-sm leading-6 text-stone-600">
                                        123 Sample Street, Barangay Poblacion,
                                        Santa Cruz, Laguna, 4009
                                    </p>
                                </div>

                                <div class="flex gap-2">
                                    <button type="button" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                                        Edit
                                    </button>

                                    <button type="button" class="text-xs font-semibold text-red-600 hover:text-red-700">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </article>

                        <article class="rounded-xl border border-stone-200 p-4">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-stone-900">
                                        {{ $buyerName }}
                                    </h3>

                                    <p class="mt-2 text-xs font-medium text-stone-600">
                                        0912 345 6789
                                    </p>

                                    <p class="mt-2 max-w-xl text-sm leading-6 text-stone-600">
                                        45 Marketplace Avenue, Barangay Bubukal,
                                        Santa Cruz, Laguna, 4009
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <button type="button" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                                        Set as default
                                    </button>

                                    <button type="button" class="text-xs font-semibold text-stone-600 hover:text-stone-900">
                                        Edit
                                    </button>

                                    <button type="button" class="text-xs font-semibold text-red-600 hover:text-red-700">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
            @endif

            @if ($activeTab === 'password')
                <section aria-labelledby="password-heading">
                    <div class="border-b border-stone-100 px-5 py-4 sm:px-6">
                        <h2 id="password-heading" class="text-base font-semibold text-stone-900">
                            Change Password
                        </h2>

                        <p class="mt-1 text-xs text-stone-500">
                            Use a strong password that you do not use elsewhere.
                        </p>
                    </div>

                    <div class="p-5 sm:p-6">
                        <form class="max-w-xl space-y-5" data-account-form>
                            <div>
                                <label for="current-password" class="mb-1.5 block text-xs font-semibold text-stone-700">
                                    Current password
                                </label>

                                <input
                                    id="current-password"
                                    type="password"
                                    name="current_password"
                                    autocomplete="current-password"
                                    class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                >
                            </div>

                            <div>
                                <label for="new-password" class="mb-1.5 block text-xs font-semibold text-stone-700">
                                    New password
                                </label>

                                <input
                                    id="new-password"
                                    type="password"
                                    name="new_password"
                                    autocomplete="new-password"
                                    class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                >
                            </div>

                            <div>
                                <label for="confirm-password" class="mb-1.5 block text-xs font-semibold text-stone-700">
                                    Confirm new password
                                </label>

                                <input
                                    id="confirm-password"
                                    type="password"
                                    name="new_password_confirmation"
                                    autocomplete="new-password"
                                    class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                >
                            </div>

                            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-xs leading-5 text-blue-800">
                                Your password should contain at least eight characters,
                                one uppercase letter, one number, and one special character.
                            </div>

                            <button
                                type="button"
                                class="rounded-xl bg-amber-600 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                            >
                                Update Password
                            </button>
                        </form>
                    </div>
                </section>
            @endif

            @if ($activeTab === 'privacy')
                <section aria-labelledby="privacy-heading">
                    <div class="border-b border-stone-100 px-5 py-4 sm:px-6">
                        <h2 id="privacy-heading" class="text-base font-semibold text-stone-900">
                            Privacy Settings
                        </h2>

                        <p class="mt-1 text-xs text-stone-500">
                            Control how your information is used within LIKHAE.
                        </p>
                    </div>

                    <div class="divide-y divide-stone-100 p-5 sm:p-6">
                        @php
                            $privacyOptions = [
                                [
                                    'name' => 'Personalized recommendations',
                                    'description' => 'Use your shopping activity to improve product recommendations.',
                                    'checked' => true,
                                ],
                                [
                                    'name' => 'Activity visibility',
                                    'description' => 'Allow sellers to see when you have read their messages.',
                                    'checked' => true,
                                ],
                                [
                                    'name' => 'Shopping analytics',
                                    'description' => 'Share anonymous usage information to help improve LIKHAE.',
                                    'checked' => false,
                                ],
                            ];
                        @endphp

                        @foreach ($privacyOptions as $option)
                            <label class="flex cursor-pointer items-start justify-between gap-5 py-4 first:pt-0 last:pb-0">
                                <span>
                                    <strong class="block text-sm font-semibold text-stone-800">
                                        {{ $option['name'] }}
                                    </strong>

                                    <span class="mt-1 block max-w-xl text-xs leading-5 text-stone-500">
                                        {{ $option['description'] }}
                                    </span>
                                </span>

                                <span class="relative mt-0.5 inline-flex shrink-0">
                                    <input
                                        type="checkbox"
                                        class="peer sr-only"
                                        @checked($option['checked'])
                                    >

                                    <span class="h-6 w-11 rounded-full bg-stone-300 transition peer-checked:bg-amber-600 peer-focus-visible:ring-4 peer-focus-visible:ring-amber-200"></span>

                                    <span class="pointer-events-none absolute left-1 top-1 h-4 w-4 rounded-full bg-white shadow-sm transition peer-checked:translate-x-5"></span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex justify-end border-t border-stone-100 px-5 py-4 sm:px-6">
                        <button
                            type="button"
                            class="rounded-xl bg-amber-600 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                        >
                            Save Privacy Settings
                        </button>
                    </div>
                </section>
            @endif

            @if ($activeTab === 'deletion')
                <section aria-labelledby="deletion-heading">
                    <div class="border-b border-stone-100 px-5 py-4 sm:px-6">
                        <h2 id="deletion-heading" class="text-base font-semibold text-stone-900">
                            Account Deletion
                        </h2>

                        <p class="mt-1 text-xs text-stone-500">
                            Permanently close your LIKHAE buyer account.
                        </p>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="max-w-2xl rounded-xl border border-red-200 bg-red-50 p-4">
                            <h3 class="text-sm font-semibold text-red-800">
                                This action cannot be undone
                            </h3>

                            <p class="mt-2 text-xs leading-5 text-red-700">
                                Your account, saved addresses, wishlist, rewards, and
                                account preferences will be permanently removed. Existing
                                orders and active return requests must be completed first.
                            </p>
                        </div>

                        <form class="mt-6 max-w-xl space-y-5" data-account-form>
                            <div>
                                <label for="deletion-reason" class="mb-1.5 block text-xs font-semibold text-stone-700">
                                    Reason for leaving
                                </label>

                                <select
                                    id="deletion-reason"
                                    name="reason"
                                    class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-100"
                                >
                                    <option value="">Select a reason</option>
                                    <option>I no longer use LIKHAE</option>
                                    <option>I created another account</option>
                                    <option>I have privacy concerns</option>
                                    <option>I had a poor shopping experience</option>
                                    <option>Other reason</option>
                                </select>
                            </div>

                            <div>
                                <label for="deletion-password" class="mb-1.5 block text-xs font-semibold text-stone-700">
                                    Confirm your password
                                </label>

                                <input
                                    id="deletion-password"
                                    type="password"
                                    name="password"
                                    autocomplete="current-password"
                                    class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-100"
                                >
                            </div>

                            <label class="flex cursor-pointer items-start gap-3">
                                <input
                                    type="checkbox"
                                    name="confirm_deletion"
                                    class="mt-0.5 h-4 w-4 rounded border-stone-300 text-red-600 focus:ring-red-500"
                                >

                                <span class="text-xs leading-5 text-stone-600">
                                    I understand that deleting my account is permanent
                                    and cannot be reversed.
                                </span>
                            </label>

                            <button
                                type="button"
                                class="rounded-xl bg-red-600 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-red-700"
                            >
                                Request Account Deletion
                            </button>
                        </form>
                    </div>
                </section>
            @endif

            @if ($activeTab === 'notifications')
                <section aria-labelledby="notifications-heading">
                    <div class="border-b border-stone-100 px-5 py-4 sm:px-6">
                        <h2 id="notifications-heading" class="text-base font-semibold text-stone-900">
                            Notification Settings
                        </h2>

                        <p class="mt-1 text-xs text-stone-500">
                            Choose which updates you want to receive.
                        </p>
                    </div>

                    <div class="divide-y divide-stone-100 p-5 sm:p-6">
                        @php
                            $notificationOptions = [
                                [
                                    'name' => 'Order updates',
                                    'description' => 'Payment, shipping, delivery, and return updates.',
                                    'checked' => true,
                                ],
                                [
                                    'name' => 'Messages',
                                    'description' => 'New messages from sellers and customer support.',
                                    'checked' => true,
                                ],
                                [
                                    'name' => 'Rewards and vouchers',
                                    'description' => 'Voucher availability, points, and cashback alerts.',
                                    'checked' => true,
                                ],
                                [
                                    'name' => 'Promotions and recommendations',
                                    'description' => 'Product suggestions, deals, and marketplace campaigns.',
                                    'checked' => false,
                                ],
                                [
                                    'name' => 'Security alerts',
                                    'description' => 'Sign-ins, password changes, and important account activity.',
                                    'checked' => true,
                                ],
                            ];
                        @endphp

                        @foreach ($notificationOptions as $option)
                            <label class="flex cursor-pointer items-start justify-between gap-5 py-4 first:pt-0 last:pb-0">
                                <span>
                                    <strong class="block text-sm font-semibold text-stone-800">
                                        {{ $option['name'] }}
                                    </strong>

                                    <span class="mt-1 block max-w-xl text-xs leading-5 text-stone-500">
                                        {{ $option['description'] }}
                                    </span>
                                </span>

                                <span class="relative mt-0.5 inline-flex shrink-0">
                                    <input
                                        type="checkbox"
                                        class="peer sr-only"
                                        @checked($option['checked'])
                                    >

                                    <span class="h-6 w-11 rounded-full bg-stone-300 transition peer-checked:bg-amber-600 peer-focus-visible:ring-4 peer-focus-visible:ring-amber-200"></span>

                                    <span class="pointer-events-none absolute left-1 top-1 h-4 w-4 rounded-full bg-white shadow-sm transition peer-checked:translate-x-5"></span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex justify-end border-t border-stone-100 px-5 py-4 sm:px-6">
                        <button
                            type="button"
                            class="rounded-xl bg-amber-600 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                        >
                            Save Notification Settings
                        </button>
                    </div>
                </section>
            @endif
        </main>
    </div>
</div>
@endsection