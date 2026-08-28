<x-marketplace.layout title="Create Buyer Account" :hide-nav="true" :registration="true">
<div class="lk-registration-shell" data-registration>
    <aside class="lk-registration-aside">
        <a class="lk-logo" href="/">LIKHAE</a>
        <span class="lk-kicker">BUYER REGISTRATION</span>
        <h1>Create your LIKHAE buyer account.</h1>
        <p>
            Register once to shop across all marketplace categories, save products,
            message sellers, check out securely, and track your orders.
        </p>

        <ol class="lk-stepper">
            @foreach(['Personal Details', 'Contact & Address', 'ID Verification', 'Review & Confirm'] as $i => $step)
                <li class="{{ $i === 0 ? 'is-active' : '' }}" data-step-indicator="{{ $i + 1 }}">
                    <b>{{ $i + 1 }}</b>
                    <span>{{ $step }}</span>
                </li>
            @endforeach
        </ol>
    </aside>

    <section class="lk-registration-main">
        <form
            class="lk-registration-form"
            method="POST"
            action="{{ route('register.store') }}"
            enctype="multipart/form-data"
            novalidate
            data-registration-form
        >
            @csrf

            <section class="lk-form-step is-active" data-step="1">
                <span class="lk-kicker">STEP 1 OF 4</span>
                <h2>Personal details</h2>
                <p>Enter the required information for your buyer profile.</p>

                <div class="lk-form-grid">
                    <label>Last Name *
                        <input
                            name="last_name"
                            value="{{ old('last_name') }}"
                            required
                            placeholder="e.g. Dela Cruz"
                            autocomplete="family-name"
                        >
                    </label>

                    <label>First Name *
                        <input
                            name="first_name"
                            value="{{ old('first_name') }}"
                            required
                            placeholder="e.g. Juan"
                            autocomplete="given-name"
                        >
                    </label>

                    <label>Middle Initial
                        <input
                            name="middle_initial"
                            value="{{ old('middle_initial') }}"
                            maxlength="2"
                            placeholder="e.g. M"
                        >
                    </label>

                    <label>Sex *
                        <select name="sex" required>
                            <option value="">Select sex</option>
                            <option value="Female" @selected(old('sex') === 'Female')>Female</option>
                            <option value="Male" @selected(old('sex') === 'Male')>Male</option>
                            <option value="Prefer not to say" @selected(old('sex') === 'Prefer not to say')>
                                Prefer not to say
                            </option>
                        </select>
                    </label>

                    <label>Birthday *
                        <input
                            type="date"
                            name="birthday"
                            value="{{ old('birthday') }}"
                            required
                            data-birthday
                            max="{{ date('Y-m-d') }}"
                        >
                    </label>

                    <label>Age * (Auto-generated)
                        <input
                            name="age"
                            readonly
                            data-age
                            placeholder="Calculated from birthday"
                        >
                    </label>
                </div>
            </section>

            <section class="lk-form-step" data-step="2">
                <span class="lk-kicker">STEP 2 OF 4</span>
                <h2>Contact and address</h2>
                <p>Your address uses Province → Municipality/City → Barangay dropdowns plus manual street details.</p>

                <div class="lk-form-grid">
                    <label>E-mail *
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="you@example.com"
                            autocomplete="email"
                        >
                    </label>

                    <label>Contact No. *
                        <div class="lk-prefix-input">
                            <span>+63</span>
                            <input
                                name="contact"
                                value="{{ old('contact') }}"
                                inputmode="numeric"
                                maxlength="10"
                                required
                                placeholder="9171234567"
                                autocomplete="tel-national"
                            >
                        </div>
                    </label>

                    <label>Province *
                        <select name="province" required data-province>
                            <option value="">Choose province</option>
                        </select>
                    </label>

                    <label>Municipality / City *
                        <select name="municipality" required data-municipality disabled>
                            <option value="">Choose municipality / city</option>
                        </select>
                    </label>

                    <label>Barangay *
                        <select name="barangay" required data-barangay disabled>
                            <option value="">Choose barangay</option>
                        </select>
                    </label>

                    <label>House / Unit Number
                        <input
                            name="house"
                            value="{{ old('house') }}"
                            placeholder="e.g. House 12 / Unit 4B"
                        >
                    </label>

                    <label>Building / Subdivision
                        <input
                            name="building"
                            value="{{ old('building') }}"
                            placeholder="e.g. Mahogany Residences"
                        >
                    </label>

                    <label>Street *
                        <input
                            name="street"
                            value="{{ old('street') }}"
                            required
                            placeholder="e.g. Mahogany Street"
                        >
                    </label>

                    <label>Postal Code
                        <input
                            name="postal"
                            value="{{ old('postal') }}"
                            inputmode="numeric"
                            placeholder="e.g. 4100"
                        >
                    </label>

                    <label class="lk-field-span">Additional Address Details
                        <textarea
                            name="address_details"
                            rows="3"
                            placeholder="Landmark, gate color, delivery instructions, etc."
                        >{{ old('address_details') }}</textarea>
                    </label>
                </div>

                <p class="lk-helper" data-address-status>
                    Connecting to Philippine address data...
                </p>
            </section>

            <section class="lk-form-step" data-step="3">
                <span class="lk-kicker">STEP 3 OF 4</span>
                <h2>Upload identification</h2>
                <p>
                    Upload one valid government-issued ID for administrator verification.
                    Accepted: JPG, PNG, or PDF up to 5 MB.
                </p>

                <label class="lk-upload" data-upload-zone>
                    <input
                        type="file"
                        name="verification_id"
                        required
                        accept="image/png,image/jpeg,application/pdf"
                        data-id-upload
                    >
                    <span class="lk-upload__icon">↑</span>
                    <strong>Click to upload or drag and drop</strong>
                    <small>JPG, PNG or PDF · Maximum 5 MB</small>
                </label>

                <div class="lk-upload-preview" data-upload-preview hidden>
                    <img data-upload-image alt="ID preview">
                    <div>
                        <strong data-upload-name></strong>
                        <small data-upload-size></small>
                        <button
                            type="button"
                            class="lk-text-link"
                            data-remove-upload
                            style="display:block;margin-top:7px"
                        >
                            Remove file
                        </button>
                    </div>
                </div>

                <div class="lk-privacy-note">
                    <strong>Verification and privacy</strong>
                    <p>
                        The ID is submitted for account verification and administrator review.
                        Do not display uploaded IDs publicly.
                    </p>
                </div>
            </section>

            <section class="lk-form-step" data-step="4">
                <span class="lk-kicker">STEP 4 OF 4</span>
                <h2>Review your registration</h2>
                <p>Check your details before submitting them for administrator approval.</p>

                <div class="lk-review-summary" data-registration-summary></div>

                <label class="lk-checkbox lk-confirm" style="margin-top:18px">
                    <input type="checkbox" required data-confirm>
                    <span>
                        I confirm that the information I provided is true and accurate.
                    </span>
                </label>
            </section>

            @if(isset($errors) && $errors->any())
                <p class="lk-field-error">{{ $errors->first() }}</p>
            @else
                <p class="lk-field-error" data-registration-error></p>
            @endif

            <div class="lk-registration-actions">
                <button class="lk-btn lk-btn--ghost" type="button" data-prev-step hidden>
                    ← Back
                </button>

                <a href="{{ route('login') }}" class="lk-text-link" data-cancel-link>
                    Already registered? Sign in
                </a>

                <button class="lk-btn lk-btn--primary" type="button" data-next-step>
                    Continue →
                </button>

                <button
                    class="lk-btn lk-btn--primary"
                    type="submit"
                    data-submit-registration
                    hidden
                    disabled
                >
                    Submit Registration
                </button>
            </div>

            <p style="margin:18px 0 0;text-align:center;color:#777;font-size:.78rem;line-height:1.5">
                After submitting your registration, please wait for the administrator's approval,
                which will be sent to your email.
            </p>
        </form>
    </section>
</div>
</x-marketplace.layout>
