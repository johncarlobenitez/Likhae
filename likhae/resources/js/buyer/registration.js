(() => {
    const $ = (sel, ctx = document) => ctx.querySelector(sel);

    const setLoading = (sel, msg = 'Loading\u2026') => {
        sel.disabled = true;
        sel.innerHTML = `<option value="">${msg}</option>`;
    };

    const resetSelect = (sel, placeholder) => {
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        sel.disabled = true;
    };

    const populate = (sel, items, placeholder) => {
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        items.forEach(({ code, name }) => {
            const opt = document.createElement('option');
            opt.value = code;
            opt.textContent = name;
            sel.appendChild(opt);
        });
        sel.disabled = false;
    };

    const apiFetch = (url) =>
        fetch(url).then(r => r.json()).then(({ data }) => data ?? []);

    // ── Address dropdowns ─────────────────────────────────────────────────────
    const regionEl       = $('[data-address-region]');
    const provinceEl     = $('[data-address-province]');
    const municipalityEl = $('[data-address-municipality]');
    const barangayEl     = $('[data-address-barangay]');

    if (regionEl) {
        // Load regions on page load
        apiFetch('/address/philippines/regions')
            .then(data => populate(regionEl, data, 'Select region'))
            .catch(() => {
                regionEl.innerHTML = '<option value="">Failed to load regions — refresh to retry</option>';
                regionEl.disabled = false;
            });

        // Region → Province
        regionEl.addEventListener('change', () => {
            resetSelect(provinceEl, 'Select province');
            resetSelect(municipalityEl, 'Select municipality / city');
            resetSelect(barangayEl, 'Select barangay');
            if (!regionEl.value) return;

            setLoading(provinceEl, 'Loading provinces\u2026');
            apiFetch(`/address/philippines/regions/${encodeURIComponent(regionEl.value)}/provinces`)
                .then(data => populate(provinceEl, data, 'Select province'))
                .catch(() => {
                    provinceEl.innerHTML = '<option value="">Failed to load provinces</option>';
                    provinceEl.disabled = false;
                });
        });

        // Province → Municipality
        provinceEl.addEventListener('change', () => {
            resetSelect(municipalityEl, 'Select municipality / city');
            resetSelect(barangayEl, 'Select barangay');
            if (!provinceEl.value) return;

            setLoading(municipalityEl, 'Loading municipalities\u2026');
            apiFetch(`/address/philippines/provinces/${encodeURIComponent(provinceEl.value)}/municipalities`)
                .then(data => populate(municipalityEl, data, 'Select municipality / city'))
                .catch(() => {
                    municipalityEl.innerHTML = '<option value="">Failed to load</option>';
                    municipalityEl.disabled = false;
                });
        });

        // Municipality → Barangay
        municipalityEl.addEventListener('change', () => {
            resetSelect(barangayEl, 'Select barangay');
            if (!municipalityEl.value) return;

            setLoading(barangayEl, 'Loading barangays\u2026');
            apiFetch(`/address/philippines/municipalities/${encodeURIComponent(municipalityEl.value)}/barangays`)
                .then(data => populate(barangayEl, data, 'Select barangay'))
                .catch(() => {
                    barangayEl.innerHTML = '<option value="">Failed to load</option>';
                    barangayEl.disabled = false;
                });
        });
    }

    // ── Birthday → Age ────────────────────────────────────────────────────────
    const birthdayEl = $('#birthday');
    const ageEl      = $('#age');
    if (birthdayEl && ageEl) {
        birthdayEl.addEventListener('change', () => {
            const dob = new Date(birthdayEl.value);
            if (isNaN(dob)) return;
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
            ageEl.value = age >= 0 ? age : '';
        });
    }

    // ── Account type toggle ───────────────────────────────────────────────────
    const typeButtons   = document.querySelectorAll('[data-account-type]');
    const accountTypeEl = $('#accountType');
    const sellerSection = $('#sellerSection');
    const bizPermitBox  = $('#businessPermitUpload');
    const submitLabel   = $('[data-submit-label]');
    const verNum        = $('[data-verification-number]');

    typeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.dataset.accountType;
            typeButtons.forEach(b => {
                b.classList.toggle('is-active', b === btn);
                b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
            });
            if (accountTypeEl) accountTypeEl.value = type;
            const isSeller = type === 'seller';
            if (sellerSection) sellerSection.hidden = !isSeller;
            if (bizPermitBox)  bizPermitBox.hidden  = !isSeller;
            document.querySelectorAll('[data-seller-required]').forEach(el => {
                el.required = isSeller;
            });
            if (submitLabel) submitLabel.textContent = isSeller ? 'Submit Seller Application' : 'Submit Buyer Application';
            if (verNum) verNum.textContent = isSeller ? '05' : '04';
        });
    });

    // ── File input label ──────────────────────────────────────────────────────
    document.querySelectorAll('[data-file-input]').forEach(input => {
        input.addEventListener('change', () => {
            const label = input.closest('.upload-box')?.querySelector('[data-file-name]');
            if (label) label.textContent = input.files[0]?.name || 'No file selected';
        });
    });
})();
