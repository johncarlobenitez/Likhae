<x-marketplace.layout title="Create Buyer Account" :hide-nav="true">
<div class="lk-registration-shell" data-registration>
    <aside class="lk-registration-aside">
        <a class="lk-logo" href="/">LIKHAE</a>
        <span class="lk-kicker">BUYER REGISTRATION</span>
        <h1>A thoughtful marketplace starts with trust.</h1>
        <p>Create your buyer profile in four short steps. Your account will be reviewed by our team before live purchasing is enabled.</p>
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
        <form class="lk-registration-form" method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data" novalidate data-registration-form>
            @csrf
            
            {{-- Step 1: Personal Details --}}
            <section class="lk-form-step is-active" data-step="1">
                <span class="lk-kicker">STEP 1 OF 4</span>
                <h2>Personal details</h2>
                <p>Tell us a little about yourself to personalize your marketplace experience.</p>
                <div class="lk-form-grid">
                    <label>Last Name *
                        <input name="last_name" required placeholder="e.g. Dela Cruz" autocomplete="family-name">
                    </label>
                    <label>First Name *
                        <input name="first_name" required placeholder="e.g. Maria" autocomplete="given-name">
                    </label>
                    <label>Middle Initial
                        <input name="middle_initial" maxlength="2" placeholder="e.g. S.">
                    </label>
                    <label>Sex *
                        <select name="sex" required>
                            <option value="">Select Sex</option>
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                            <option value="Non-binary / Prefer not to say">Prefer not to say</option>
                        </select>
                    </label>
                    <label>Birthday *
                        <input type="date" name="birthday" required data-birthday max="{{ date('Y-m-d') }}">
                    </label>
                    <label>Age * (Auto-calculated)
                        <input name="age" readonly data-age placeholder="Auto-calculated from birthday" style="background:#f0ebe1;cursor:not-allowed">
                    </label>
                </div>
            </section>

            {{-- Step 2: Contact & Address --}}
            <section class="lk-form-step" data-step="2">
                <span class="lk-kicker">STEP 2 OF 4</span>
                <h2>Contact & address</h2>
                <p>Where can we reach you and deliver your handmade goods?</p>
                <div class="lk-form-grid">
                    <label>Email Address *
                        <input type="email" name="email" required placeholder="you@example.com" autocomplete="email">
                    </label>
                    <label>Philippine Contact Number *
                        <div class="lk-prefix-input">
                            <span>+63</span>
                            <input name="contact" inputmode="numeric" maxlength="10" placeholder="917 123 4567" required autocomplete="tel-national">
                        </div>
                    </label>
                    <label>Province *
                        <select name="province" required data-province>
                            <option value="">Choose province</option>
                        </select>
                    </label>
                    <label>Municipality / City *
                        <select name="municipality" required disabled data-municipality>
                            <option value="">Choose city</option>
                        </select>
                    </label>
                    <label>Barangay *
                        <select name="barangay" required disabled data-barangay>
                            <option value="">Choose barangay</option>
                        </select>
                    </label>
                    <label>House / Unit Number
                        <input name="house" placeholder="e.g. Unit 4B / House #12">
                    </label>
                    <label>Building / Subdivision
                        <input name="building" placeholder="e.g. Acacia Heights">
                    </label>
                    <label>Street *
                        <input name="street" required placeholder="e.g. Narra Street">
                    </label>
                    <label>Postal Code
                        <input name="postal" inputmode="numeric" placeholder="e.g. 6000">
                    </label>
                    <label class="lk-field-span">Additional Address Details (Landmarks / Instructions)
                        <textarea name="address_details" rows="3" placeholder="e.g. Near the barangay hall, green gate"></textarea>
                    </label>
                </div>
                <p class="lk-helper" data-address-status>Philippine location data is connected to our regional cascading service.</p>
            </section>

            {{-- Step 3: ID Verification --}}
            <section class="lk-form-step" data-step="3">
                <span class="lk-kicker">STEP 3 OF 4</span>
                <h2>Identity verification</h2>
                <p>Upload one government-issued identification document (UMID, Passport, Driver's License, PhilID). Your ID remains confidential.</p>
                <label class="lk-upload" data-upload-zone>
                    <input type="file" name="verification_id" accept="image/png,image/jpeg,application/pdf" data-id-upload>
                    <span class="lk-upload__icon">↑</span>
                    <strong>Drop your ID here or browse files</strong>
                    <small>Accepted formats: JPG, PNG, or PDF · Max file size: 5 MB</small>
                </label>
                <div class="lk-upload-preview" data-upload-preview hidden>
                    <img data-upload-image alt="ID document preview">
                    <div>
                        <strong data-upload-name></strong>
                        <small data-upload-size></small>
                        <button type="button" class="lk-text-link" data-remove-upload style="color:var(--coral);display:block;margin-top:6px">Remove file</button>
                    </div>
                </div>
                <div class="lk-privacy-note">
                    <strong>Privacy Assurance</strong>
                    <p>ID uploads are encrypted and used solely for authenticating buyer profiles to prevent fraud against local makers. Files are never shared publicly.</p>
                </div>
            </section>

            {{-- Step 4: Review & Confirm --}}
            <section class="lk-form-step" data-step="4">
                <span class="lk-kicker">STEP 4 OF 4</span>
                <h2>Review your details</h2>
                <p>Please double-check your information before submitting for administrator review.</p>
                <div class="lk-review-summary" data-registration-summary></div>
                <label class="lk-checkbox lk-confirm">
                    <input type="checkbox" required data-confirm>
                    <span>I confirm that the information provided is true and accurate.</span>
                </label>
            </section>

            <p class="lk-field-error" data-registration-error></p>

            <div class="lk-registration-actions">
                <button class="lk-btn lk-btn--ghost" type="button" data-prev-step hidden>← Back</button>
                <a href="{{ route('login') }}" class="lk-text-link" data-cancel-link>Already registered? Sign in</a>
                <button class="lk-btn lk-btn--primary" type="button" data-next-step>Continue →</button>
                <button class="lk-btn lk-btn--primary" type="submit" data-submit-registration hidden disabled>Submit Registration</button>
            </div>
        </form>
    </section>
</div>
</x-marketplace.layout>
