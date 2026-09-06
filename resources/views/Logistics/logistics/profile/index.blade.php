@extends('logistics.app')

@section('title', 'Profile & Settings — LIKHAE Logistics')

@section('content')

<div class="flex w-full flex-col gap-6">

    {{-- PAGE HEADER --}}
    <section>
        <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">
            Account
        </span>

        <h1 class="mt-2 font-display text-[30px] font-semibold tracking-[-0.04em] text-ink sm:text-[34px]">
            Profile & Settings
        </h1>

        <p class="mt-2 max-w-[680px] text-[10px] leading-5 text-muted">
            Manage your logistics account, security, notifications, and appearance preferences.
        </p>
    </section>


    {{-- PROFILE --}}
    <section class="rounded-xl border border-line bg-surface p-5">

        <div class="flex flex-col gap-5 md:flex-row md:items-center">

            <div
                id="profilePhotoPreview"
                class="grid h-20 w-20 shrink-0 place-items-center overflow-hidden rounded-full bg-primary-soft text-primary"
            >
                <span id="profilePhotoInitials" class="text-[22px] font-bold">LG</span>
            </div>

            <div class="flex-1">
                <h2 class="text-[18px] font-semibold text-ink">
                    Logistics Administrator
                </h2>

                <p class="mt-1 text-[9px] text-muted">
                    logistics@likhae.test
                </p>

                <span class="mt-3 inline-flex rounded-full bg-success-soft px-3 py-1 text-[8px] font-semibold text-success">
                    Active Account
                </span>
            </div>

            <button
                type="button"
                id="changePhotoButton"
                class="rounded-lg border border-line px-4 py-2 text-[8px] font-semibold text-ink hover:bg-page-secondary"
            >
                Change Photo
            </button>

            <input
                type="file"
                id="profilePhotoInput"
                accept="image/jpeg,image/png,image/webp"
                class="hidden"
            >

        </div>

    </section>


    {{-- PERSONAL INFORMATION --}}
    <section class="rounded-xl border border-line bg-surface p-5">

        <div>
            <h2 class="text-[13px] font-semibold text-ink">
                Personal Information
            </h2>

            <p class="mt-1 text-[9px] text-muted">
                Update the information associated with your logistics account.
            </p>
        </div>


        <form id="profileForm" class="mt-5 grid gap-4 md:grid-cols-2">

            <div>
                <label class="mb-1.5 block text-[8px] font-semibold uppercase tracking-[0.1em] text-muted">
                    First Name
                </label>

                <input
                    type="text"
                    value="Logistics"
                    class="h-10 w-full rounded-lg border border-line bg-surface px-3 text-[9px] text-ink outline-none focus:border-primary"
                >
            </div>


            <div>
                <label class="mb-1.5 block text-[8px] font-semibold uppercase tracking-[0.1em] text-muted">
                    Last Name
                </label>

                <input
                    type="text"
                    value="Administrator"
                    class="h-10 w-full rounded-lg border border-line bg-surface px-3 text-[9px] text-ink outline-none focus:border-primary"
                >
            </div>


            <div>
                <label class="mb-1.5 block text-[8px] font-semibold uppercase tracking-[0.1em] text-muted">
                    Email
                </label>

                <input
                    type="email"
                    value="logistics@likhae.test"
                    class="h-10 w-full rounded-lg border border-line bg-surface px-3 text-[9px] text-ink outline-none focus:border-primary"
                >
            </div>


            <div>
                <label class="mb-1.5 block text-[8px] font-semibold uppercase tracking-[0.1em] text-muted">
                    Contact Number
                </label>

                <input
                    type="text"
                    value="0917 000 0000"
                    class="h-10 w-full rounded-lg border border-line bg-surface px-3 text-[9px] text-ink outline-none focus:border-primary"
                >
            </div>


            <div class="md:col-span-2">
                <label class="mb-1.5 block text-[8px] font-semibold uppercase tracking-[0.1em] text-muted">
                    Role
                </label>

                <input
                    type="text"
                    value="Logistics Administrator"
                    readonly
                    class="h-10 w-full cursor-not-allowed rounded-lg border border-line bg-page-secondary px-3 text-[9px] text-muted outline-none"
                >
            </div>


            <div class="flex justify-end md:col-span-2">

                <button
                    type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-[9px] font-semibold text-white hover:bg-primary-hover"
                >
                    Save Changes
                </button>

            </div>

        </form>

    </section>


    {{-- SECURITY + NOTIFICATIONS --}}
    <section class="grid gap-5 lg:grid-cols-2">


        {{-- PASSWORD --}}
        <section class="rounded-xl border border-line bg-surface p-5">

            <h2 class="text-[13px] font-semibold text-ink">
                Security
            </h2>

            <p class="mt-1 text-[9px] text-muted">
                Keep your logistics account protected.
            </p>


            <form id="passwordForm" class="mt-5 space-y-4">

                <div>
                    <label class="mb-1.5 block text-[8px] font-semibold uppercase tracking-[0.1em] text-muted">
                        Current Password
                    </label>

                    <input
                        type="password"
                        placeholder="Enter current password"
                        class="h-10 w-full rounded-lg border border-line bg-surface px-3 text-[9px] text-ink outline-none focus:border-primary"
                    >
                </div>


                <div>
                    <label class="mb-1.5 block text-[8px] font-semibold uppercase tracking-[0.1em] text-muted">
                        New Password
                    </label>

                    <input
                        type="password"
                        placeholder="Enter new password"
                        class="h-10 w-full rounded-lg border border-line bg-surface px-3 text-[9px] text-ink outline-none focus:border-primary"
                    >
                </div>


                <div>
                    <label class="mb-1.5 block text-[8px] font-semibold uppercase tracking-[0.1em] text-muted">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        placeholder="Confirm new password"
                        class="h-10 w-full rounded-lg border border-line bg-surface px-3 text-[9px] text-ink outline-none focus:border-primary"
                    >
                </div>


                <button
                    type="submit"
                    class="rounded-lg border border-line px-5 py-2.5 text-[9px] font-semibold text-ink hover:bg-page-secondary"
                >
                    Update Password
                </button>

            </form>

        </section>



        {{-- NOTIFICATIONS --}}
        <section class="rounded-xl border border-line bg-surface p-5">

            <h2 class="text-[13px] font-semibold text-ink">
                Notifications
            </h2>

            <p class="mt-1 text-[9px] text-muted">
                Choose which logistics updates you want to receive.
            </p>


            <div class="mt-5 space-y-3">

                @foreach([
                    ['Parcel updates','Receive notifications when parcel status changes',true],
                    ['Rider applications','Get notified when a new rider applies',true],
                    ['Delivery alerts','Receive failed or delayed delivery alerts',true],
                    ['Daily summary','Receive the daily logistics summary',false],
                ] as $notification)

                <label class="flex cursor-pointer items-center justify-between gap-4 rounded-lg bg-page-secondary p-4">

                    <div>
                        <strong class="block text-[9px] font-semibold text-ink">
                            {{ $notification[0] }}
                        </strong>

                        <span class="mt-1 block text-[7px] leading-4 text-muted">
                            {{ $notification[1] }}
                        </span>
                    </div>

                    <input
                        type="checkbox"
                        class="notification-toggle h-4 w-4 accent-primary"
                        {{ $notification[2] ? 'checked' : '' }}
                    >

                </label>

                @endforeach

            </div>

        </section>

    </section>


    {{-- APPEARANCE --}}
    <section class="rounded-xl border border-line bg-surface p-5">

        <h2 class="text-[13px] font-semibold text-ink">
            Appearance
        </h2>

        <p class="mt-1 text-[9px] text-muted">
            Choose how the LIKHAE Logistics interface appears.
        </p>


        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">


            <button
                type="button"
                data-theme-option="light"
                class="theme-option rounded-xl border border-primary bg-primary-soft p-4 text-left"
            >
                <span class="block text-[10px] font-semibold text-ink">
                    Light
                </span>

                <span class="mt-1 block text-[8px] text-muted">
                    Clean and bright interface.
                </span>
            </button>


            <button
                type="button"
                data-theme-option="dark"
                class="theme-option rounded-xl border border-line bg-page-secondary p-4 text-left hover:border-primary"
            >
                <span class="block text-[10px] font-semibold text-ink">
                    Dark
                </span>

                <span class="mt-1 block text-[8px] text-muted">
                    Dark interface for low-light use.
                </span>
            </button>


            <button
                type="button"
                data-theme-option="system"
                class="theme-option rounded-xl border border-line bg-page-secondary p-4 text-left hover:border-primary"
            >
                <span class="block text-[10px] font-semibold text-ink">
                    System
                </span>

                <span class="mt-1 block text-[8px] text-muted">
                    Follow your device preference.
                </span>
            </button>


        </div>

    </section>


    {{-- SYSTEM INFORMATION --}}
    <section class="rounded-xl border border-line bg-surface p-5">

        <h2 class="text-[13px] font-semibold text-ink">
            System Information
        </h2>


        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">

            @foreach([
                ['Platform','LIKHAE Marketplace'],
                ['Module','Logistics'],
                ['Version','v1.0.0'],
                ['Status','Operational']
            ] as $item)

            <div class="rounded-lg bg-page-secondary p-4">

                <span class="text-[8px] uppercase tracking-[0.1em] text-muted">
                    {{ $item[0] }}
                </span>

                <strong class="mt-2 block text-[10px] font-semibold text-ink">
                    {{ $item[1] }}
                </strong>

            </div>

            @endforeach

        </div>

    </section>


    {{-- DANGER ZONE --}}
    <section class="rounded-xl border border-danger/20 bg-danger-soft p-5">

        <h2 class="text-[13px] font-semibold text-danger">
            Account Actions
        </h2>

        <p class="mt-1 text-[9px] text-danger/70">
            Sign out of the current logistics session.
        </p>


        <form method="POST" action="#" class="mt-4">

            @csrf

            <button
                type="button"
                id="logoutButton"
                class="rounded-lg border border-danger/30 bg-surface px-5 py-2.5 text-[9px] font-semibold text-danger hover:bg-danger-soft"
            >
                Log Out
            </button>

        </form>

    </section>

</div>


{{-- FEEDBACK MODAL --}}
<div
    id="settingsModal"
    class="invisible fixed inset-0 z-[100] grid place-items-center bg-black/50 p-5 opacity-0 backdrop-blur-sm transition"
>

    <div
        id="settingsModalPanel"
        class="w-full max-w-[420px] scale-95 rounded-2xl border border-line bg-surface p-6 shadow-likhae-lg transition"
    >

        <div class="grid h-12 w-12 place-items-center rounded-full bg-success-soft text-success">
            ✓
        </div>


        <h2 id="settingsModalTitle" class="mt-4 text-[18px] font-semibold text-ink">
            Settings Saved
        </h2>


        <p id="settingsModalText" class="mt-2 text-[9px] leading-5 text-muted">
            Your changes have been saved.
        </p>


        <button
            type="button"
            id="closeSettingsModal"
            class="mt-5 h-10 w-full rounded-lg bg-primary text-[9px] font-semibold text-white"
        >
            Done
        </button>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('settingsModal');
    const panel = document.getElementById('settingsModalPanel');
    const modalTitle = document.getElementById('settingsModalTitle');
    const modalText = document.getElementById('settingsModalText');
    const changePhotoButton = document.getElementById('changePhotoButton');
    const profilePhotoInput = document.getElementById('profilePhotoInput');
    const profilePhotoPreview = document.getElementById('profilePhotoPreview');
    const profilePhotoInitials = document.getElementById('profilePhotoInitials');

    const savedPhoto = localStorage.getItem('likhae-profile-photo');

    function displayProfilePhoto(photoUrl) {

        profilePhotoPreview.style.backgroundImage = `url("${photoUrl}")`;
        profilePhotoPreview.style.backgroundPosition = 'center';
        profilePhotoPreview.style.backgroundSize = 'cover';
        profilePhotoInitials.classList.add('hidden');

    }

    if (savedPhoto) {
        displayProfilePhoto(savedPhoto);
    }


    function openModal(title, text) {

        modalTitle.textContent = title;
        modalText.textContent = text;

        modal.classList.remove('invisible', 'opacity-0');
        modal.classList.add('visible', 'opacity-100');

        panel.classList.remove('scale-95');
        panel.classList.add('scale-100');

        document.body.classList.add('overflow-hidden');
    }


    function closeModal() {

        modal.classList.remove('visible', 'opacity-100');
        modal.classList.add('invisible', 'opacity-0');

        panel.classList.remove('scale-100');
        panel.classList.add('scale-95');

        document.body.classList.remove('overflow-hidden');
    }


    document.getElementById('profileForm')?.addEventListener('submit', function (event) {

        event.preventDefault();

        openModal(
            'Profile Saved',
            'Your profile information has been updated. Connect this form to your Laravel controller when the backend is ready.'
        );

    });


    document.getElementById('passwordForm')?.addEventListener('submit', function (event) {

        event.preventDefault();

        openModal(
            'Password Update',
            'Your password form is ready. Connect it to Laravel authentication and password validation.'
        );

    });


    changePhotoButton?.addEventListener('click', function () {

        profilePhotoInput?.click();

    });


    profilePhotoInput?.addEventListener('change', function () {

        const file = this.files?.[0];

        if (!file) {
            return;
        }

        if (!file.type.startsWith('image/') || file.size > 5 * 1024 * 1024) {
            this.value = '';
            openModal(
                'Photo Not Added',
                'Choose a JPG, PNG, or WEBP image that is smaller than 5 MB.'
            );
            return;
        }

        const reader = new FileReader();

        reader.addEventListener('load', function () {

            const photoUrl = String(reader.result);

            displayProfilePhoto(photoUrl);
            localStorage.setItem('likhae-profile-photo', photoUrl);
            openModal('Profile Photo Updated', 'Your profile photo has been updated on this device.');

        });

        reader.readAsDataURL(file);

    });


    document.querySelectorAll('.theme-option').forEach(function (button) {

        button.addEventListener('click', function () {

            const theme = button.dataset.themeOption;

            localStorage.setItem('likhae-theme', theme);

            document.querySelectorAll('.theme-option').forEach(function (option) {

                option.classList.remove(
                    'border-primary',
                    'bg-primary-soft'
                );

                option.classList.add(
                    'border-line',
                    'bg-page-secondary'
                );

            });


            button.classList.remove(
                'border-line',
                'bg-page-secondary'
            );

            button.classList.add(
                'border-primary',
                'bg-primary-soft'
            );


            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (theme === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                const prefersDark =
                    window.matchMedia(
                        '(prefers-color-scheme: dark)'
                    ).matches;

                document.documentElement.classList.toggle(
                    'dark',
                    prefersDark
                );
            }

        });

    });


    document.getElementById('logoutButton')?.addEventListener('click', function () {

        openModal(
            'Log Out',
            'Connect this button to your Laravel logout route when authentication is implemented.'
        );

    });


    document.getElementById('closeSettingsModal')?.addEventListener(
        'click',
        closeModal
    );


    modal?.addEventListener('click', function (event) {

        if (event.target === modal) {
            closeModal();
        }

    });


    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            closeModal();
        }

    });


    // Restore saved theme.
    const savedTheme =
        localStorage.getItem('likhae-theme') || 'light';

    const selectedTheme =
        document.querySelector(
            `[data-theme-option="${savedTheme}"]`
        );

    selectedTheme?.click();

});
</script>

@endpush
