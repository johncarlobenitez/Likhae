<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $accountLabel }} Registration — LIKHAE</title>

    @vite([
        'resources/css/auth/register.css'
    ])
</head>
<body>
<div class="register-page">
    <aside class="register-side">
        <div class="register-side__inner">
            <a href="{{ $homeRoute }}" class="register-brand">
                <span class="register-brand__mark">L</span>
                <span class="register-brand__name">LIKHAE</span>
            </a>

            <div class="register-side__content">
                <span class="register-eyebrow">{{ $accountLabel }} application</span>

                <h1>{{ $headline }}</h1>

                <p>{{ $description }}</p>

                <div class="register-side__features">
                    @if ($workspace === 'logistics')
                        <div class="register-feature">
                            <span class="register-feature__icon">1</span>
                            <div>
                                <strong>Submit business information</strong>
                                <span>Provide your logistics center details and operating address.</span>
                            </div>
                        </div>
                        <div class="register-feature">
                            <span class="register-feature__icon">2</span>
                            <div>
                                <strong>Upload verification documents</strong>
                                <span>A valid ID and business or DTI permit are required.</span>
                            </div>
                        </div>
                        <div class="register-feature">
                            <span class="register-feature__icon">3</span>
                            <div>
                                <strong>Wait for Admin approval</strong>
                                <span>Admin reviews Logistics / Sorting Center registrations.</span>
                            </div>
                        </div>
                    @else
                        <div class="register-feature">
                            <span class="register-feature__icon">1</span>
                            <div>
                                <strong>Complete your rider profile</strong>
                                <span>Provide your personal and delivery address information.</span>
                            </div>
                        </div>
                        <div class="register-feature">
                            <span class="register-feature__icon">2</span>
                            <div>
                                <strong>Add vehicle documents</strong>
                                <span>Vehicle type, plate number, OR/CR, and driver's license are required.</span>
                            </div>
                        </div>
                        <div class="register-feature">
                            <span class="register-feature__icon">3</span>
                            <div>
                                <strong>Wait for Logistics approval</strong>
                                <span>Your Logistics / Sorting Center approves rider applications.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <p class="register-side__footer">
                LIKHAE · {{ $accountLabel }}
            </p>
        </div>
    </aside>

    <main class="register-main">
        <div class="register-container">
            <header class="register-header">
                <span class="register-header__eyebrow">Account Registration</span>
                <h2>{{ $accountLabel }}</h2>
                <p>Complete the required information below. Fields marked with * are required.</p>
            </header>

            @if ($errors->any())
                <div class="register-alert register-alert--error">
                    <strong>Please correct the following:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="register-alert register-alert--success">
                    {{ session('status') }}
                </div>
            @endif

            <form
                method="POST"
                action="{{ $storeRoute }}"
                enctype="multipart/form-data"
                class="register-form"
                id="workspaceRegistrationForm"
            >
                @csrf

                <section class="register-section">
                    <div class="register-section__head">
                        <span class="register-section__number">01</span>
                        <div>
                            <h3>Personal information</h3>
                            <p>Basic account-holder information.</p>
                        </div>
                    </div>

                    <div class="register-grid">
                        <div class="register-field">
                            <label for="first_name">First name <span>*</span></label>
                            <input id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                        </div>

                        <div class="register-field">
                            <label for="last_name">Last name <span>*</span></label>
                            <input id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                        </div>

                        <div class="register-field">
                            <label for="middle_initial">Middle initial</label>
                            <input id="middle_initial" name="middle_initial" maxlength="2" value="{{ old('middle_initial') }}">
                        </div>

                        <div class="register-field">
                            <label for="sex">Sex <span>*</span></label>
                            <select id="sex" name="sex" required>
                                <option value="">Select</option>
                                <option value="male" @selected(old('sex') === 'male')>Male</option>
                                <option value="female" @selected(old('sex') === 'female')>Female</option>
                                <option value="prefer_not_to_say" @selected(old('sex') === 'prefer_not_to_say')>Prefer not to say</option>
                            </select>
                        </div>

                        <div class="register-field">
                            <label for="birthday">Birthday <span>*</span></label>
                            <input id="birthday" name="birthday" type="date" value="{{ old('birthday') }}" required>
                        </div>

                        <div class="register-field">
                            <label for="contact_number">Contact number <span>*</span></label>
                            <input id="contact_number" name="contact_number" value="{{ old('contact_number') }}" placeholder="09XXXXXXXXX" required>
                        </div>

                        <div class="register-field">
                            <label for="email">Email <span>*</span></label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="register-field">
                            <label for="password">Password <span>*</span></label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required>
                            <small>Minimum 8 characters with uppercase, lowercase, and a number.</small>
                        </div>

                        <div class="register-field">
                            <label for="password_confirmation">Confirm password <span>*</span></label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                        </div>
                    </div>
                </section>

                <section class="register-section">
                    <div class="register-section__head">
                        <span class="register-section__number">02</span>
                        <div>
                            <h3>Address</h3>
                            <p>Provide the complete Philippine address used for this account.</p>
                        </div>
                    </div>

                    <div class="register-grid">
                        <div class="register-field">
                            <label for="province">Province <span>*</span></label>
                            <input id="province" name="province" value="{{ old('province') }}" placeholder="e.g. Laguna" required>
                        </div>

                        <div class="register-field">
                            <label for="municipality">Municipality / City <span>*</span></label>
                            <input id="municipality" name="municipality" value="{{ old('municipality') }}" placeholder="e.g. Santa Cruz" required>
                        </div>

                        <div class="register-field">
                            <label for="barangay">Barangay <span>*</span></label>
                            <input id="barangay" name="barangay" value="{{ old('barangay') }}" required>
                        </div>

                        <div class="register-field">
                            <label for="house_number">House / Building No. <span>*</span></label>
                            <input id="house_number" name="house_number" value="{{ old('house_number') }}" required>
                        </div>

                        <div class="register-field">
                            <label for="street">Street / Subdivision <span>*</span></label>
                            <input id="street" name="street" value="{{ old('street') }}" required>
                        </div>
                    </div>
                </section>

                @if ($workspace === 'logistics')
                    <section class="register-section">
                        <div class="register-section__head">
                            <span class="register-section__number">03</span>
                            <div>
                                <h3>Logistics business verification</h3>
                                <p>These documents are reviewed by the LIKHAE Admin.</p>
                            </div>
                        </div>

                        <div class="register-grid">
                            <div class="register-field">
                                <label for="business_name">Business name <span>*</span></label>
                                <input id="business_name" name="business_name" value="{{ old('business_name') }}" required>
                            </div>

                            <div class="register-field">
                                <label for="valid_id">Valid ID <span>*</span></label>
                                <input id="valid_id" name="valid_id" type="file" accept=".jpg,.jpeg,.png,.pdf" required>
                                <small>JPG, PNG, or PDF up to 5 MB.</small>
                            </div>

                            <div class="register-field">
                                <label for="business_permit">Business / DTI permit <span>*</span></label>
                                <input id="business_permit" name="business_permit" type="file" accept=".jpg,.jpeg,.png,.pdf" required>
                                <small>JPG, PNG, or PDF up to 5 MB.</small>
                            </div>
                        </div>
                    </section>
                @else
                    <section class="register-section">
                        <div class="register-section__head">
                            <span class="register-section__number">03</span>
                            <div>
                                <h3>Vehicle and rider verification</h3>
                                <p>Your Logistics / Sorting Center reviews these documents.</p>
                            </div>
                        </div>

                        <div class="register-grid">
                            <div class="register-field">
                                <label for="vehicle_type">Vehicle <span>*</span></label>
                                <select id="vehicle_type" name="vehicle_type" required>
                                    <option value="">Select vehicle</option>
                                    <option value="motorcycle" @selected(old('vehicle_type') === 'motorcycle')>Motorcycle</option>
                                    <option value="car" @selected(old('vehicle_type') === 'car')>Car</option>
                                    <option value="van" @selected(old('vehicle_type') === 'van')>Van</option>
                                    <option value="truck" @selected(old('vehicle_type') === 'truck')>Truck</option>
                                </select>
                            </div>

                            <div class="register-field">
                                <label for="plate_number">Plate number <span>*</span></label>
                                <input id="plate_number" name="plate_number" value="{{ old('plate_number') }}" required>
                            </div>

                            <div class="register-field">
                                <label for="or_cr">Vehicle OR / CR <span>*</span></label>
                                <input id="or_cr" name="or_cr" type="file" accept=".jpg,.jpeg,.png,.pdf" required>
                                <small>JPG, PNG, or PDF up to 5 MB.</small>
                            </div>

                            <div class="register-field">
                                <label for="drivers_license">Driver's license / Valid ID <span>*</span></label>
                                <input id="drivers_license" name="drivers_license" type="file" accept=".jpg,.jpeg,.png,.pdf" required>
                                <small>JPG, PNG, or PDF up to 5 MB.</small>
                            </div>
                        </div>
                    </section>
                @endif

                <section class="register-section">
                    <div class="register-section__head">
                        <span class="register-section__number">04</span>
                        <div>
                            <h3>Application confirmation</h3>
                            <p>Review your information before submitting.</p>
                        </div>
                    </div>

                    <label class="register-login-link" style="display:flex; gap:10px; align-items:flex-start; line-height:1.6;">
                        <input type="checkbox" name="terms" value="1" required style="width:16px; height:16px; margin-top:2px;">
                        <span>I confirm that the information and documents submitted are accurate and I agree to LIKHAE's platform policies.</span>
                    </label>
                </section>

                <div class="register-navigation">
                    <div class="register-navigation__left">
                        <a href="{{ $loginRoute }}" class="register-login-link">
                            Already registered? <strong>Sign in</strong>
                        </a>
                    </div>

                    <button type="submit" class="register-submit">
                        Submit application
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 12h14"></path>
                            <path d="m13 6 6 6-6 6"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
