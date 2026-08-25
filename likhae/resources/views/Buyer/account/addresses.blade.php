<x-marketplace.layout title="Saved Addresses" :buyer="true">
<section class="lk-page-head lk-container">
    <span class="lk-kicker">DELIVERY PREFERENCES</span>
    <h1>Saved Addresses</h1>
</section>

<section class="lk-account-layout lk-container">
    <x-marketplace.account-nav current="addresses" />

    <section class="lk-account-card">
        <div class="lk-card-heading">
            <h2>Your Addresses</h2>
            <button class="lk-btn lk-btn--secondary" type="button" data-toggle-new-address>+ Add New Address</button>
        </div>

        <div style="display:grid;gap:16px;margin-top:20px">
            {{-- Default Address --}}
            <article class="lk-address-card" style="background:var(--sand)">
                <div>
                    <span class="lk-status-pill lk-status-pill--approved" style="margin-bottom:6px;display:inline-block">DEFAULT ADDRESS</span>
                    <strong style="display:block;font-size:1.1rem">Maria Dela Cruz · +63 917 123 4567</strong>
                    <p style="color:var(--slate);margin:4px 0 0">18 Narra Street, Brgy. Lahug, Cebu City, Cebu 6000</p>
                </div>
                <div style="display:flex;gap:10px;align-items:center">
                    <button class="lk-text-link" type="button">Edit</button>
                </div>
            </article>

            {{-- Secondary Address --}}
            <article class="lk-address-card">
                <div>
                    <strong style="display:block;font-size:1.1rem">Maria Dela Cruz (Office) · +63 917 123 4567</strong>
                    <p style="color:var(--slate);margin:4px 0 0">Unit 1402 Tower One, Ayala Avenue, Makati City, Metro Manila 1226</p>
                </div>
                <div style="display:flex;gap:10px;align-items:center">
                    <button class="lk-text-link" type="button">Set as Default</button>
                    <button class="lk-text-link" type="button" style="color:var(--coral)">Delete</button>
                </div>
            </article>
        </div>

        {{-- Add Address Form --}}
        <div data-new-address-form hidden style="margin-top:28px;padding-top:24px;border-top:1px solid var(--line)">
            <h3>Add New Delivery Address</h3>
            <form class="lk-form" data-add-address-form>
                <div class="lk-form-grid">
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
                    <label>Street Address *
                        <input name="street" required placeholder="House No., Street Name">
                    </label>
                    <label>Postal Code
                        <input name="postal" inputmode="numeric" placeholder="e.g. 6000">
                    </label>
                </div>
                <div style="display:flex;gap:12px;margin-top:16px">
                    <button class="lk-btn lk-btn--primary" type="submit">Save Address</button>
                    <button class="lk-btn lk-btn--ghost" type="button" data-toggle-new-address>Cancel</button>
                </div>
            </form>
        </div>
    </section>
</section>
</x-marketplace.layout>
