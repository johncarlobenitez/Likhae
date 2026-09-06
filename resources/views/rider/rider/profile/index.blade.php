@extends('rider.app')

@section('title', 'Rider Profile — LIKHAE')

@section('content')

@php

    $personalInfo = [
        ['label' => 'Full Name', 'value' => 'Juan Dela Cruz'],
        ['label' => 'Email', 'value' => 'juan@email.com'],
        ['label' => 'Contact Number', 'value' => '0917 555 1234'],
        ['label' => 'Birthday', 'value' => 'January 10, 1998'],
        ['label' => 'Sex', 'value' => 'Male'],
        ['label' => 'Address', 'value' => 'Calamba, Laguna'],
    ];


    $vehicleInfo = [
        ['label' => 'Vehicle Type', 'value' => 'Motorcycle'],
        ['label' => 'Plate Number', 'value' => 'ABC-1234'],
        ['label' => 'OR / CR Status', 'value' => 'Verified'],
        ['label' => 'Driver License', 'value' => 'Verified'],
    ];


    $notifications = [
        'New pickup assignment',
        'Delivery reminders',
        'Customer messages',
    ];

@endphp


<div class="mx-auto w-full max-w-[1280px] space-y-6">


    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <section
        class="
            flex
            flex-col
            gap-4
            border-b
            border-line
            pb-5
        "
    >

        <div>

            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-primary">
                Account Settings
            </p>

            <h1 class="mt-1.5 text-[28px] font-bold leading-tight tracking-tight text-ink">
                My Profile
            </h1>

            <p class="mt-1.5 text-sm text-muted">
                Manage your rider information, vehicle details, and account settings.
            </p>

        </div>

    </section>



    {{-- =========================================================
        PROFILE OVERVIEW
    ========================================================== --}}

    <section
        class="
            rounded-2xl
            border
            border-line
            bg-surface
            p-5
            sm:p-6
        "
    >

        <div
            class="
                flex
                flex-col
                gap-5
                sm:flex-row
                sm:items-center
                sm:justify-between
            "
        >

            <div class="flex items-center gap-4">


                {{-- Avatar --}}

                <div
                    class="
                        grid
                        h-16
                        w-16
                        shrink-0
                        place-items-center
                        rounded-2xl
                        bg-primary-soft
                        text-primary
                    "
                >

                    <span class="text-xl font-bold">
                        JD
                    </span>

                </div>



                {{-- Rider Information --}}

                <div>

                    <h2 class="text-lg font-bold text-ink">
                        Juan Dela Cruz
                    </h2>

                    <p class="mt-0.5 text-xs text-muted">
                        Rider ID: RID-0001
                    </p>


                    <div class="mt-2.5 flex flex-wrap items-center gap-2">

                        <span
                            class="
                                inline-flex
                                items-center
                                gap-1.5
                                rounded-full
                                bg-success-soft
                                px-2.5
                                py-1
                                text-[10px]
                                font-semibold
                                text-success
                            "
                        >

                            <span class="h-1.5 w-1.5 rounded-full bg-success"></span>

                            Verified Rider

                        </span>


                        <span
                            class="
                                inline-flex
                                items-center
                                gap-1.5
                                rounded-full
                                bg-primary-soft
                                px-2.5
                                py-1
                                text-[10px]
                                font-semibold
                                text-primary
                            "
                        >

                            <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>

                            Online

                        </span>

                    </div>

                </div>

            </div>



            {{-- Profile Status --}}

            <div
                class="
                    hidden
                    rounded-xl
                    bg-page-secondary
                    px-4
                    py-3
                    sm:block
                "
            >

                <p class="text-[10px] font-medium uppercase tracking-wide text-muted">
                    Account Status
                </p>

                <p class="mt-1 text-sm font-semibold text-success">
                    Active
                </p>

            </div>

        </div>

    </section>



    {{-- =========================================================
        PERSONAL INFORMATION
    ========================================================== --}}

    <section
        class="
            rounded-2xl
            border
            border-line
            bg-surface
            p-5
            sm:p-6
        "
    >

        <div>

            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">
                Personal Details
            </p>

            <h2 class="mt-1 text-lg font-bold text-ink">
                Personal Information
            </h2>

            <p class="mt-1 text-xs text-muted">
                Your registered rider account information.
            </p>

        </div>


        <div class="mt-5 grid gap-3 md:grid-cols-2">

            @foreach($personalInfo as $info)

                <div
                    class="
                        rounded-xl
                        border
                        border-line
                        bg-page-secondary
                        px-4
                        py-3.5
                    "
                >

                    <p
                        class="
                            text-[10px]
                            font-semibold
                            uppercase
                            tracking-wide
                            text-muted
                        "
                    >
                        {{ $info['label'] }}
                    </p>

                    <p class="mt-1 text-sm font-semibold text-ink">
                        {{ $info['value'] }}
                    </p>

                </div>

            @endforeach

        </div>

    </section>



    {{-- =========================================================
        VEHICLE INFORMATION
    ========================================================== --}}

    <section
        class="
            rounded-2xl
            border
            border-line
            bg-surface
            p-5
            sm:p-6
        "
    >

        <div>

            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">
                Rider Vehicle
            </p>

            <h2 class="mt-1 text-lg font-bold text-ink">
                Vehicle Information
            </h2>

            <p class="mt-1 text-xs text-muted">
                Registered vehicle and verification details.
            </p>

        </div>


        <div class="mt-5 grid gap-3 sm:grid-cols-2">

            @foreach($vehicleInfo as $vehicle)

                <div
                    class="
                        flex
                        items-center
                        justify-between
                        gap-4
                        rounded-xl
                        border
                        border-line
                        bg-page-secondary
                        px-4
                        py-3.5
                    "
                >

                    <div>

                        <p
                            class="
                                text-[10px]
                                font-semibold
                                uppercase
                                tracking-wide
                                text-muted
                            "
                        >
                            {{ $vehicle['label'] }}
                        </p>

                        <p class="mt-1 text-sm font-semibold text-ink">
                            {{ $vehicle['value'] }}
                        </p>

                    </div>


                    @if(in_array($vehicle['value'], ['Verified']))

                        <span
                            class="
                                rounded-full
                                bg-success-soft
                                px-2.5
                                py-1
                                text-[10px]
                                font-semibold
                                text-success
                            "
                        >
                            Verified
                        </span>

                    @endif

                </div>

            @endforeach

        </div>

    </section>



    {{-- =========================================================
        VERIFICATION DOCUMENTS
    ========================================================== --}}

    <section
        class="
            rounded-2xl
            border
            border-line
            bg-surface
            p-5
            sm:p-6
        "
    >

        <div>

            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">
                Verification
            </p>

            <h2 class="mt-1 text-lg font-bold text-ink">
                Verification Documents
            </h2>

            <p class="mt-1 text-xs text-muted">
                Documents submitted for rider verification.
            </p>

        </div>


        <div class="mt-5 grid gap-3 md:grid-cols-2">


            {{-- OR / CR --}}

            <div
                class="
                    rounded-xl
                    border
                    border-line
                    bg-page-secondary
                    p-4
                "
            >

                <div class="flex items-start justify-between gap-3">

                    <div class="flex items-center gap-3">

                        <div
                            class="
                                grid
                                h-10
                                w-10
                                place-items-center
                                rounded-lg
                                bg-primary-soft
                                text-primary
                            "
                        >

                            <svg
                                viewBox="0 0 24 24"
                                class="h-5 w-5 fill-none stroke-current"
                            >
                                <path
                                    d="M6 2h9l3 3v17H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z"
                                    stroke-width="1.5"
                                ></path>

                                <path
                                    d="M14 2v4h4"
                                    stroke-width="1.5"
                                ></path>

                                <path
                                    d="M8 12h8M8 16h6"
                                    stroke-width="1.5"
                                ></path>
                            </svg>

                        </div>


                        <div>

                            <h3 class="text-sm font-semibold text-ink">
                                OR / CR Document
                            </h3>

                            <p class="mt-0.5 text-xs text-muted">
                                Vehicle registration
                            </p>

                        </div>

                    </div>


                    <span
                        class="
                            rounded-full
                            bg-success-soft
                            px-2.5
                            py-1
                            text-[10px]
                            font-semibold
                            text-success
                        "
                    >
                        Verified
                    </span>

                </div>


                <button
                    type="button"
                    class="
                        mt-4
                        rounded-lg
                        border
                        border-line
                        bg-surface
                        px-3.5
                        py-2
                        text-xs
                        font-semibold
                        text-ink
                        transition
                        hover:bg-page
                    "
                    data-document="OR / CR Document"
                >
                    View Document
                </button>

            </div>



            {{-- Driver License --}}

            <div
                class="
                    rounded-xl
                    border
                    border-line
                    bg-page-secondary
                    p-4
                "
            >

                <div class="flex items-start justify-between gap-3">

                    <div class="flex items-center gap-3">

                        <div
                            class="
                                grid
                                h-10
                                w-10
                                place-items-center
                                rounded-lg
                                bg-primary-soft
                                text-primary
                            "
                        >

                            <svg
                                viewBox="0 0 24 24"
                                class="h-5 w-5 fill-none stroke-current"
                            >
                                <rect
                                    x="3"
                                    y="5"
                                    width="18"
                                    height="14"
                                    rx="2"
                                    stroke-width="1.5"
                                ></rect>

                                <circle
                                    cx="8"
                                    cy="11"
                                    r="2"
                                    stroke-width="1.5"
                                ></circle>

                                <path
                                    d="M13 10h5M13 14h4"
                                    stroke-width="1.5"
                                ></path>
                            </svg>

                        </div>


                        <div>

                            <h3 class="text-sm font-semibold text-ink">
                                Driver License / ID
                            </h3>

                            <p class="mt-0.5 text-xs text-muted">
                                Rider identification
                            </p>

                        </div>

                    </div>


                    <span
                        class="
                            rounded-full
                            bg-success-soft
                            px-2.5
                            py-1
                            text-[10px]
                            font-semibold
                            text-success
                        "
                    >
                        Verified
                    </span>

                </div>


                <button
                    type="button"
                    class="
                        mt-4
                        rounded-lg
                        border
                        border-line
                        bg-surface
                        px-3.5
                        py-2
                        text-xs
                        font-semibold
                        text-ink
                        transition
                        hover:bg-page
                    "
                    data-document="Driver License / ID"
                >
                    View Document
                </button>

            </div>

        </div>

    </section>



    {{-- =========================================================
        SECURITY
    ========================================================== --}}

    <section
        class="
            rounded-2xl
            border
            border-line
            bg-surface
            p-5
            sm:p-6
        "
    >

        <div>

            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">
                Security
            </p>

            <h2 class="mt-1 text-lg font-bold text-ink">
                Change Password
            </h2>

            <p class="mt-1 text-xs text-muted">
                Update your password to keep your rider account secure.
            </p>

        </div>


        <div class="mt-5 grid gap-3 md:grid-cols-2">


            <div>

                <label
                    for="current-password"
                    class="mb-1.5 block text-[10px] font-semibold text-muted"
                >
                    Current Password
                </label>

                <input
                    id="current-password"
                    type="password"
                    placeholder="Enter current password"
                    class="
                        h-10
                        w-full
                        rounded-lg
                        border
                        border-line
                        bg-page
                        px-3
                        text-sm
                        text-ink
                        outline-none
                        placeholder:text-muted
                        focus:border-primary
                    "
                >

            </div>



            <div>

                <label
                    for="new-password"
                    class="mb-1.5 block text-[10px] font-semibold text-muted"
                >
                    New Password
                </label>

                <input
                    id="new-password"
                    type="password"
                    placeholder="Enter new password"
                    class="
                        h-10
                        w-full
                        rounded-lg
                        border
                        border-line
                        bg-page
                        px-3
                        text-sm
                        text-ink
                        outline-none
                        placeholder:text-muted
                        focus:border-primary
                    "
                >

            </div>

        </div>


        <button
            type="button"
            id="update-password-btn"
            class="
                mt-4
                rounded-lg
                bg-primary
                px-4
                py-2.5
                text-xs
                font-semibold
                text-white
                transition
                hover:opacity-90
            "
        >
            Update Password
        </button>

    </section>



    {{-- =========================================================
        ACCOUNT PREFERENCES
    ========================================================== --}}

    <section class="grid gap-4 lg:grid-cols-2">


        {{-- Notifications --}}

        <section
            class="
                rounded-2xl
                border
                border-line
                bg-surface
                p-5
                sm:p-6
            "
        >

            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">
                Preferences
            </p>

            <h2 class="mt-1 text-lg font-bold text-ink">
                Notifications
            </h2>

            <p class="mt-1 text-xs text-muted">
                Choose which rider notifications you want to receive.
            </p>


            <div class="mt-5 space-y-2">

                @foreach($notifications as $notification)

                    <label
                        class="
                            flex
                            cursor-pointer
                            items-center
                            justify-between
                            gap-4
                            rounded-xl
                            border
                            border-line
                            bg-page-secondary
                            px-4
                            py-3
                        "
                    >

                        <span class="text-sm font-medium text-ink">
                            {{ $notification }}
                        </span>


                        <input
                            type="checkbox"
                            checked
                            class="h-4 w-4 accent-primary"
                        >

                    </label>

                @endforeach

            </div>

        </section>



        {{-- Appearance --}}

        <section
            class="
                rounded-2xl
                border
                border-line
                bg-surface
                p-5
                sm:p-6
            "
        >

            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">
                Interface
            </p>

            <h2 class="mt-1 text-lg font-bold text-ink">
                Appearance
            </h2>

            <p class="mt-1 text-xs text-muted">
                Choose how the Rider Panel should look.
            </p>


            <div class="mt-5 grid gap-2 sm:grid-cols-2">


                {{-- Light --}}

                <button
                    type="button"
                    id="lightModeButton"
                    class="
                        rounded-xl
                        border
                        border-line
                        bg-page-secondary
                        p-4
                        text-left
                        transition
                        hover:border-primary
                    "
                    aria-pressed="false"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="
                                grid
                                h-9
                                w-9
                                place-items-center
                                rounded-lg
                                bg-surface
                                text-ink
                            "
                        >

                            <svg
                                viewBox="0 0 24 24"
                                class="h-4 w-4 fill-none stroke-current"
                            >
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="4"
                                    stroke-width="1.6"
                                ></circle>

                                <path
                                    d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"
                                    stroke-width="1.6"
                                ></path>
                            </svg>

                        </div>


                        <div>

                            <p class="text-sm font-semibold text-ink">
                                Light
                            </p>

                            <p class="mt-0.5 text-[11px] text-muted">
                                Bright interface
                            </p>

                        </div>

                    </div>

                </button>



                {{-- Dark --}}

                <button
                    type="button"
                    id="darkModeButton"
                    class="
                        rounded-xl
                        border
                        border-line
                        bg-page-secondary
                        p-4
                        text-left
                        transition
                        hover:border-primary
                    "
                    aria-pressed="false"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="
                                grid
                                h-9
                                w-9
                                place-items-center
                                rounded-lg
                                bg-surface
                                text-ink
                            "
                        >

                            <svg
                                viewBox="0 0 24 24"
                                class="h-4 w-4 fill-none stroke-current"
                            >
                                <path
                                    d="M20 15.5A8.5 8.5 0 0 1 8.5 4a8.5 8.5 0 1 0 11.5 11.5Z"
                                    stroke-width="1.6"
                                ></path>
                            </svg>

                        </div>


                        <div>

                            <p class="text-sm font-semibold text-ink">
                                Dark
                            </p>

                            <p class="mt-0.5 text-[11px] text-muted">
                                Low-light interface
                            </p>

                        </div>

                    </div>

                </button>

            </div>

        </section>

    </section>



    {{-- =========================================================
        ACCOUNT ACTIONS
    ========================================================== --}}

    <section
        class="
            rounded-2xl
            border
            border-danger/20
            bg-danger-soft
            p-5
            sm:p-6
        "
    >

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-danger">
                    Account
                </p>

                <h2 class="mt-1 text-lg font-bold text-danger">
                    Account Actions
                </h2>

                <p class="mt-1 text-xs text-danger/70">
                    Sign out from your LIKHAE Rider account.
                </p>

            </div>


            <button
                type="button"
                id="logoutButton"
                class="
                    w-fit
                    rounded-lg
                    border
                    border-danger/30
                    bg-surface
                    px-4
                    py-2.5
                    text-xs
                    font-semibold
                    text-danger
                    transition
                    hover:bg-danger/5
                "
            >
                Log Out
            </button>

        </div>

    </section>


</div>



{{-- =============================================================
    DOCUMENT MODAL
============================================================= --}}

<div
    id="documentModal"
    class="
        fixed
        inset-0
        z-50
        hidden
        items-center
        justify-center
        bg-black/40
        p-4
    "
>

    <div
        class="
            w-full
            max-w-md
            rounded-2xl
            border
            border-line
            bg-surface
            p-6
            shadow-xl
        "
    >

        <div class="flex items-start justify-between gap-4">

            <div>

                <p class="text-[11px] font-bold uppercase tracking-wide text-primary">
                    Verification
                </p>

                <h2
                    id="documentModalTitle"
                    class="mt-1 text-lg font-bold text-ink"
                >
                    Document
                </h2>

            </div>


            <button
                type="button"
                id="closeDocumentModal"
                class="
                    grid
                    h-8
                    w-8
                    place-items-center
                    rounded-lg
                    bg-page-secondary
                    text-muted
                    hover:text-ink
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-4 w-4"
                >
                    <path
                        d="M6 6l12 12M18 6 6 18"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    ></path>
                </svg>

            </button>

        </div>


        <div
            class="
                mt-5
                flex
                min-h-[180px]
                items-center
                justify-center
                rounded-xl
                border
                border-dashed
                border-line
                bg-page-secondary
            "
        >

            <div class="text-center">

                <div
                    class="
                        mx-auto
                        grid
                        h-12
                        w-12
                        place-items-center
                        rounded-xl
                        bg-primary-soft
                        text-primary
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-6 w-6 fill-none stroke-current"
                    >
                        <path
                            d="M6 2h9l3 3v17H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z"
                            stroke-width="1.5"
                        ></path>

                        <path
                            d="M14 2v4h4"
                            stroke-width="1.5"
                        ></path>

                    </svg>

                </div>

                <p class="mt-3 text-sm font-semibold text-ink">
                    Verified Document
                </p>

                <p class="mt-1 text-xs text-muted">
                    Document preview would appear here.
                </p>

            </div>

        </div>


        <button
            type="button"
            id="modalDoneButton"
            class="
                mt-5
                w-full
                rounded-lg
                bg-primary
                px-4
                py-2.5
                text-xs
                font-semibold
                text-white
            "
        >
            Done
        </button>

    </div>

</div>



@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    */

    const THEME_KEY = 'likhae-theme';

    const lightButton =
        document.getElementById('lightModeButton');

    const darkButton =
        document.getElementById('darkModeButton');


    function syncThemeButtons(theme) {

        const isDark =
            theme === 'dark';


        if (lightButton) {

            lightButton.classList.toggle(
                'border-primary',
                !isDark
            );

            lightButton.classList.toggle(
                'bg-primary-soft',
                !isDark
            );

            lightButton.classList.toggle(
                'border-line',
                isDark
            );

            lightButton.setAttribute(
                'aria-pressed',
                (!isDark).toString()
            );

        }


        if (darkButton) {

            darkButton.classList.toggle(
                'border-primary',
                isDark
            );

            darkButton.classList.toggle(
                'bg-primary-soft',
                isDark
            );

            darkButton.classList.toggle(
                'border-line',
                !isDark
            );

            darkButton.setAttribute(
                'aria-pressed',
                isDark.toString()
            );

        }

    }


    function applyTheme(theme) {

        const selected =
            theme === 'dark'
                ? 'dark'
                : 'light';


        document.documentElement.classList.toggle(
            'dark',
            selected === 'dark'
        );


        localStorage.setItem(
            THEME_KEY,
            selected
        );


        syncThemeButtons(selected);

    }


    lightButton?.addEventListener(
        'click',
        function () {
            applyTheme('light');
        }
    );


    darkButton?.addEventListener(
        'click',
        function () {
            applyTheme('dark');
        }
    );


    const savedTheme =
        localStorage.getItem(THEME_KEY);


    applyTheme(
        savedTheme ||
        (
            window.matchMedia &&
            window.matchMedia(
                '(prefers-color-scheme: dark)'
            ).matches
                ? 'dark'
                : 'light'
        )
    );



    /*
    |--------------------------------------------------------------------------
    | Document Modal
    |--------------------------------------------------------------------------
    */

    const modal =
        document.getElementById('documentModal');

    const modalTitle =
        document.getElementById('documentModalTitle');

    const closeModal =
        document.getElementById('closeDocumentModal');

    const doneButton =
        document.getElementById('modalDoneButton');


    document
        .querySelectorAll('[data-document]')
        .forEach(function (button) {

            button.addEventListener(
                'click',
                function () {

                    modalTitle.textContent =
                        this.dataset.document;

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                }
            );

        });


    function hideModal() {

        modal.classList.add('hidden');
        modal.classList.remove('flex');

    }


    closeModal?.addEventListener(
        'click',
        hideModal
    );


    doneButton?.addEventListener(
        'click',
        hideModal
    );


    modal?.addEventListener(
        'click',
        function (event) {

            if (event.target === modal) {
                hideModal();
            }

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Update Password
    |--------------------------------------------------------------------------
    */

    const passwordButton =
        document.getElementById(
            'update-password-btn'
        );


    passwordButton?.addEventListener(
        'click',
        function () {

            const current =
                document.getElementById(
                    'current-password'
                );

            const next =
                document.getElementById(
                    'new-password'
                );


            if (!current.value || !next.value) {

                alert(
                    'Please enter your current and new password.'
                );

                return;

            }


            passwordButton.disabled = true;

            passwordButton.textContent =
                'Updating...';


            setTimeout(function () {

                passwordButton.textContent =
                    'Password Updated';

                passwordButton.classList.remove(
                    'bg-primary'
                );

                passwordButton.classList.add(
                    'bg-success'
                );


                current.value = '';
                next.value = '';


                setTimeout(function () {

                    passwordButton.textContent =
                        'Update Password';

                    passwordButton.classList.remove(
                        'bg-success'
                    );

                    passwordButton.classList.add(
                        'bg-primary'
                    );

                    passwordButton.disabled = false;

                }, 1500);

            }, 700);

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('logoutButton')
        ?.addEventListener(
            'click',
            function () {

                const confirmed =
                    confirm(
                        'Are you sure you want to log out?'
                    );


                if (!confirmed) {
                    return;
                }


                window.location.href =
                    "{{ route('login') }}";

            }
        );

});

</script>

@endpush

@endsection