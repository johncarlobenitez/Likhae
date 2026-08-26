<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Create Buyer Account — LIKHAE</title>

    @vite([
        'resources/css/Guest/auth/register.css',
        'resources/js/app.js',
        'resources/js/guest/auth/register.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

<main class="min-h-screen px-4 py-10 sm:py-14">
    <div class="mx-auto w-full max-w-[760px]">

        {{-- =====================================================
             LOGO
        ====================================================== --}}
        <a href="{{ url('/') }}" class="mb-8 inline-flex items-center gap-2">
            <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">
                L
            </span>

            <span class="text-xl font-black tracking-tight">
                LIKHAE
            </span>
        </a>

        {{-- =====================================================
             REGISTRATION CARD
        ====================================================== --}}
        <section class="border border-[#ddd6ce] bg-white shadow-[0_18px_45px_rgba(0,0,0,0.06)]">

            {{-- Tabs --}}
            <div class="grid grid-cols-2 border-b border-[#e6e0d8]">
                <a
                    href="{{ route('login') }}"
                    class="auth-tab"
                >
                    Sign In
                </a>

                <a
                    href="{{ route('register') }}"
                    class="auth-tab is-active"
                >
                    Create Account
                </a>
            </div>

            <div class="p-6 sm:p-8">

                {{-- Heading --}}
                <div class="text-center">
                    <p class="text-sm font-semibold text-[#5f5953]">
                        Create your LIKHAE Buyer Account
                    </p>

                    <p class="mt-2 text-xs leading-5 text-[#9a938b]">
                        Complete the form below. Fields marked with
                        <span class="font-bold text-[#d92d2f]">*</span>
                        are required.
                    </p>
                </div>

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="mt-6 border border-[#f1c7c8] bg-[#fff2f2] px-4 py-3">
                        <ul class="space-y-1 text-sm text-[#b82024]">
                            @foreach ($errors->all() as $error)
                                <li class="flex gap-2">
                                    <span>•</span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('register.store') }}"
                    enctype="multipart/form-data"
                    class="mt-8"
                >
                    @csrf

                    {{-- Role (hidden — this is the buyer registration form) --}}
                    <input type="hidden" name="role" value="buyer">

                    {{-- =================================================
                         PERSONAL INFORMATION
                    ================================================== --}}
                    <section>
                        <div class="form-section-heading">
                            <div>
                                <p class="form-section-number">01</p>
                                <h2>Personal Information</h2>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">

                            {{-- Last Name --}}
                            <div>
                                <label for="last_name" class="form-label">
                                    Last Name <span>*</span>
                                </label>

                                <input
                                    id="last_name"
                                    name="last_name"
                                    type="text"
                                    value="{{ old('last_name') }}"
                                    required
                                    autocomplete="family-name"
                                    placeholder="Dela Cruz"
                                    class="form-input"
                                >
                            </div>

                            {{-- First Name --}}
                            <div>
                                <label for="first_name" class="form-label">
                                    First Name <span>*</span>
                                </label>

                                <input
                                    id="first_name"
                                    name="first_name"
                                    type="text"
                                    value="{{ old('first_name') }}"
                                    required
                                    autocomplete="given-name"
                                    placeholder="Juan"
                                    class="form-input"
                                >
                            </div>

                            {{-- Middle Initial --}}
                            <div>
                                <label for="middle_initial" class="form-label">
                                    Middle Initial
                                </label>

                                <input
                                    id="middle_initial"
                                    name="middle_initial"
                                    type="text"
                                    value="{{ old('middle_initial') }}"
                                    maxlength="2"
                                    placeholder="S."
                                    class="form-input"
                                >
                            </div>

                            {{-- Sex --}}
                            <div>
                                <label for="sex" class="form-label">
                                    Sex <span>*</span>
                                </label>

                                <select
                                    id="sex"
                                    name="sex"
                                    required
                                    class="form-input"
                                >
                                    <option value="">Select sex</option>
                                    <option value="male" @selected(old('sex') === 'male')>Male</option>
                                    <option value="female" @selected(old('sex') === 'female')>Female</option>
                                    <option value="prefer_not_to_say" @selected(old('sex') === 'prefer_not_to_say')>
                                        Prefer not to say
                                    </option>
                                </select>
                            </div>

                            {{-- Birthday --}}
                            <div>
                                <label for="birthday" class="form-label">
                                    Birthday <span>*</span>
                                </label>

                                <input
                                    id="birthday"
                                    name="birthday"
                                    type="date"
                                    value="{{ old('birthday') }}"
                                    required
                                    autocomplete="bday"
                                    class="form-input"
                                >
                            </div>

                            {{-- Age --}}
                            <div>
                                <label for="age" class="form-label">
                                    Age <span>*</span>
                                </label>

                                <input
                                    id="age"
                                    type="number"
                                    placeholder="Auto-generated"
                                    readonly
                                    class="form-input cursor-not-allowed bg-[#f7f4f0] text-[#807970]"
                                >

                                <p class="mt-2 text-[10px] leading-4 text-[#a49d95]">
                                    Age is automatically calculated from your birthday.
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- =================================================
                         CONTACT INFORMATION
                    ================================================== --}}
                    <section class="form-section">
                        <div class="form-section-heading">
                            <div>
                                <p class="form-section-number">02</p>
                                <h2>Contact Information</h2>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">

                            {{-- Email --}}
                            <div>
                                <label for="email" class="form-label">
                                    E-mail <span>*</span>
                                </label>

                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="email"
                                    placeholder="juan@email.com"
                                    class="form-input"
                                >
                            </div>

                            {{-- Contact --}}
                            <div>
                                <label for="contact_number" class="form-label">
                                    Contact No. <span>*</span>
                                </label>

                                <input
                                    id="contact_number"
                                    name="contact_number"
                                    type="tel"
                                    value="{{ old('contact_number') }}"
                                    required
                                    autocomplete="tel"
                                    placeholder="09XXXXXXXXX"
                                    class="form-input"
                                >
                            </div>
                        </div>
                    </section>

                    {{-- =================================================
                         ADDRESS
                    ================================================== --}}
                    <section class="form-section">
                        <div class="form-section-heading">
                            <div>
                                <p class="form-section-number">03</p>
                                <h2>Address</h2>
                            </div>
                        </div>

                        <p class="mt-3 text-xs leading-5 text-[#918a82]">
                            Choose your country, Province, Municipality/City, and Barangay.
                            Enter your exact street and house/unit details manually.
                        </p>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">

                            {{-- Country --}}
                            <div>
                                <label for="country" class="form-label">
                                    Country <span>*</span>
                                </label>

                                <select id="country" name="country" required class="form-input">
                                    <option value="">Select country</option>
                                    <option value="PH" @selected(old('country', 'PH') === 'PH')>Philippines</option>
                                </select>
                            </div>

                            {{-- Province --}}
                            <div>
                                <label for="province" class="form-label">
                                    Province <span>*</span>
                                </label>

                                <select
                                    id="province"
                                    name="province"
                                    data-old-value="{{ old('province') }}"
                                    required
                                    class="form-input"
                                >
                                    <option value="">Select province</option>
                                </select>

                                <input
                                    id="province_code"
                                    name="province_code"
                                    type="hidden"
                                    value="{{ old('province_code') }}"
                                >
                            </div>

                            {{-- Municipality --}}
                            <div>
                                <label for="municipality" class="form-label">
                                    Municipality / City <span>*</span>
                                </label>

                                <select
                                    id="municipality"
                                    name="municipality"
                                    data-old-value="{{ old('municipality') }}"
                                    required
                                    disabled
                                    class="form-input disabled:cursor-not-allowed disabled:bg-[#f7f4f0]"
                                >
                                    <option value="">Select municipality / city</option>
                                </select>

                                <input
                                    id="municipality_code"
                                    name="municipality_code"
                                    type="hidden"
                                    value="{{ old('municipality_code') }}"
                                >
                            </div>

                            {{-- Barangay --}}
                            <div>
                                <label for="barangay" class="form-label">
                                    Barangay <span>*</span>
                                </label>

                                <select
                                    id="barangay"
                                    name="barangay"
                                    data-old-value="{{ old('barangay') }}"
                                    required
                                    disabled
                                    class="form-input disabled:cursor-not-allowed disabled:bg-[#f7f4f0]"
                                >
                                    <option value="">Select barangay</option>
                                </select>

                                <input
                                    id="barangay_code"
                                    name="barangay_code"
                                    type="hidden"
                                    value="{{ old('barangay_code') }}"
                                >
                            </div>

                            {{-- House Number --}}
                            <div>
                                <label for="house_number" class="form-label">
                                    House No. / Unit <span>*</span>
                                </label>

                                <input
                                    id="house_number"
                                    name="house_number"
                                    type="text"
                                    value="{{ old('house_number') }}"
                                    required
                                    placeholder="123 / Unit 4B"
                                    class="form-input"
                                >
                            </div>

                            {{-- Street --}}
                            <div class="sm:col-span-2">
                                <label for="street" class="form-label">
                                    Street / Subdivision <span>*</span>
                                </label>

                                <input
                                    id="street"
                                    name="street"
                                    type="text"
                                    value="{{ old('street') }}"
                                    required
                                    autocomplete="street-address"
                                    placeholder="Rizal Street, Sampaguita Village"
                                    class="form-input"
                                >
                            </div>

                            {{-- Additional Address --}}
                            <div class="sm:col-span-2">
                                <label for="address_details" class="form-label">
                                    Additional Address Details
                                </label>

                                <textarea
                                    id="address_details"
                                    name="address_details"
                                    rows="3"
                                    placeholder="Landmark, building name, floor, nearby establishment, etc."
                                    class="form-input min-h-[96px] resize-y py-3"
                                >{{ old('address_details') }}</textarea>
                            </div>
                        </div>
                    </section>

                    {{-- =================================================
                         IDENTITY VERIFICATION
                    ================================================== --}}
                    <section class="form-section">
                        <div class="form-section-heading">
                            <div>
                                <p class="form-section-number">04</p>
                                <h2>Identity Verification</h2>
                            </div>
                        </div>

                        <div class="mt-5">
                            <label for="valid_id" class="form-label">
                                Upload Valid ID <span>*</span>
                            </label>

                            <label
                                for="valid_id"
                                class="mt-2 flex cursor-pointer flex-col items-center justify-center border border-dashed border-[#cfc7bd] bg-[#faf8f5] px-5 py-8 text-center transition hover:border-[#d92d2f] hover:bg-[#fff7f7]"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-7 w-7 text-[#9c958d]"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                >
                                    <path d="M12 16V4"></path>
                                    <path d="m7 9 5-5 5 5"></path>
                                    <path d="M5 14v5h14v-5"></path>
                                </svg>

                                <span class="mt-3 text-sm font-semibold text-[#5f5953]">
                                    Choose a valid ID image
                                </span>

                                <span id="fileName" class="mt-1 text-xs text-[#9d968e]">
                                    JPG, JPEG, PNG or PDF
                                </span>
                            </label>

                            <input
                                id="valid_id"
                                name="valid_id"
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf"
                                required
                                class="sr-only"
                            >

                            <p class="mt-2 text-[10px] leading-5 text-[#9d968e]">
                                Upload a clear and readable government-issued or accepted valid ID.
                            </p>
                        </div>
                    </section>

                    {{-- =================================================
                         ACCOUNT SECURITY
                    ================================================== --}}
                    <section class="form-section">
                        <div class="form-section-heading">
                            <div>
                                <p class="form-section-number">05</p>
                                <h2>Account Security</h2>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">

                            {{-- Password --}}
                            <div>
                                <label for="password" class="form-label">
                                    Password <span>*</span>
                                </label>

                                <div class="relative">
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        required
                                        autocomplete="new-password"
                                        placeholder="Create password"
                                        class="form-input pr-12"
                                    >

                                    <button
                                        type="button"
                                        class="password-toggle"
                                        data-target="password"
                                        aria-label="Show password"
                                    >
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                            <circle cx="12" cy="12" r="2.7"></circle>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label for="password_confirmation" class="form-label">
                                    Confirm Password <span>*</span>
                                </label>

                                <div class="relative">
                                    <input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        required
                                        autocomplete="new-password"
                                        placeholder="Confirm password"
                                        class="form-input pr-12"
                                    >

                                    <button
                                        type="button"
                                        class="password-toggle"
                                        data-target="password_confirmation"
                                        aria-label="Show password"
                                    >
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                            <circle cx="12" cy="12" r="2.7"></circle>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- =================================================
                         APPROVAL NOTICE
                    ================================================== --}}
                    <div class="mt-8 border border-[#ead8a8] bg-[#fff9e8] px-4 py-4 sm:px-5">
                        <div class="flex gap-3">
                            <div class="mt-0.5 shrink-0 text-[#b78612]">
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                >
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M12 8v5"></path>
                                    <circle cx="12" cy="16.5" r=".6" fill="currentColor"></circle>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-bold text-[#7a5b0f]">
                                    Administrator approval required
                                </p>

                                <p class="mt-1 text-xs leading-5 text-[#927728]">
                                    After submitting your registration, please wait for the administrator's approval.
                                    The approval status will be sent to your registered email address.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Terms --}}
                    <label class="mt-6 flex cursor-pointer items-start gap-3 text-xs leading-5 text-[#777068]">
                        <input
                            type="checkbox"
                            name="terms"
                            value="1"
                            required
                            class="mt-0.5 h-4 w-4 shrink-0 accent-[#d92d2f]"
                        >

                        <span>
                            I confirm that the information provided is correct and I agree to LIKHAE's
                            <a href="#" class="font-semibold text-[#d92d2f] hover:underline">Terms</a>
                            and
                            <a href="#" class="font-semibold text-[#d92d2f] hover:underline">Privacy Policy</a>.
                        </span>
                    </label>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="mt-6 flex h-13 w-full items-center justify-center gap-2 bg-[#d92d2f] px-5 text-sm font-bold text-white shadow-[0_8px_20px_rgba(217,45,47,0.18)] transition hover:bg-[#bd2024]"
                    >
                        Create Buyer Account
                        <span aria-hidden="true">→</span>
                    </button>

                    <p class="mt-4 text-center text-xs text-[#938c84]">
                        Already have an account?
                        <a
                            href="{{ route('login') }}"
                            class="font-bold text-[#d92d2f] transition hover:text-[#b82024]"
                        >
                            Sign In
                        </a>
                    </p>
                </form>
            </div>
        </section>

        {{-- Back --}}
        <div class="mt-6 text-center">
            <a
                href="{{ url('/') }}"
                class="text-xs font-semibold text-[#827b73] transition hover:text-[#d92d2f]"
            >
                ← Back to LIKHAE Marketplace
            </a>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | Automatic Age Calculation
        |--------------------------------------------------------------------------
        */
        const birthdayInput = document.getElementById('birthday');
        const ageInput = document.getElementById('age');

        function calculateAge() {
            if (!birthdayInput.value) {
                ageInput.value = '';
                return;
            }

            const birthday = new Date(birthdayInput.value + 'T00:00:00');
            const today = new Date();

            let age = today.getFullYear() - birthday.getFullYear();

            const monthDifference = today.getMonth() - birthday.getMonth();

            if (
                monthDifference < 0 ||
                (
                    monthDifference === 0 &&
                    today.getDate() < birthday.getDate()
                )
            ) {
                age--;
            }

            ageInput.value = age >= 0 ? age : '';
        }

        birthdayInput.addEventListener('change', calculateAge);
        calculateAge();

        /*
        |--------------------------------------------------------------------------
        | ID File Name
        |--------------------------------------------------------------------------
        */
        const validIdInput = document.getElementById('valid_id');
        const fileName = document.getElementById('fileName');

        validIdInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            } else {
                fileName.textContent = 'JPG, JPEG, PNG or PDF';
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Password Visibility
        |--------------------------------------------------------------------------
        */
        document.querySelectorAll('.password-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                const target = document.getElementById(button.dataset.target);

                if (!target) return;

                const isHidden = target.type === 'password';

                target.type = isHidden ? 'text' : 'password';

                button.setAttribute(
                    'aria-label',
                    isHidden ? 'Hide password' : 'Show password'
                );
            });
        });
    });
</script>

</body>
</html>
