<x-marketplace.layout title="Account Profile" :buyer="true">
<section class="lk-page-head lk-container">
    <span class="lk-kicker">ACCOUNT OVERVIEW</span>
    <h1>Your Profile</h1>
</section>

<section class="lk-account-layout lk-container">
    <x-marketplace.account-nav current="profile" />

    <section class="lk-account-card">
        <div class="lk-profile-top">
            <div class="lk-profile-photo">MD</div>
            <div>
                <strong style="font-size:1.4rem;display:block">Maria Dela Cruz</strong>
                <span class="lk-status-pill lk-status-pill--approved" style="margin-top:4px;display:inline-block">VERIFIED BUYER</span>
            </div>
        </div>

        <form class="lk-form" data-profile-form>
            <div class="lk-form-grid">
                <label>First Name *
                    <input name="first_name" value="Maria" required>
                </label>
                <label>Last Name *
                    <input name="last_name" value="Dela Cruz" required>
                </label>
                <label>Middle Initial
                    <input name="middle_initial" value="S." maxlength="2">
                </label>
                <label>Sex
                    <select name="sex">
                        <option value="Female" selected>Female</option>
                        <option value="Male">Male</option>
                        <option value="Prefer not to say">Prefer not to say</option>
                    </select>
                </label>
                <label>Birthday
                    <input type="date" name="birthday" value="1998-04-14" data-birthday>
                </label>
                <label>Age (Auto-calculated)
                    <input name="age" value="28" readonly data-age style="background:#f0ebe1">
                </label>
                <label>Email Address *
                    <input type="email" name="email" value="buyer@likhae.com" required>
                </label>
                <label>Contact Number *
                    <div class="lk-prefix-input">
                        <span>+63</span>
                        <input name="contact" value="9171234567" required>
                    </div>
                </label>
            </div>

            <div style="margin-top:24px">
                <button class="lk-btn lk-btn--primary" type="submit">Save Changes</button>
            </div>
        </form>
    </section>
</section>
</x-marketplace.layout>
