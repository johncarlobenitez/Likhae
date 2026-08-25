document.addEventListener('DOMContentLoaded', () => {
    const LEVELS = ['province', 'municipality', 'barangay'];

    const selects = Object.fromEntries(LEVELS.map((l) => [l, document.getElementById(l)]));
    const codes = Object.fromEntries(LEVELS.map((l) => [l, document.getElementById(`${l}_code`)]));
    const oldValues = Object.fromEntries(LEVELS.map((l) => [l, selects[l]?.dataset.oldValue]));

    if (Object.values(selects).some((el) => !el)) return;

    const ENDPOINTS = {
        province: () => '/address/philippines/provinces',
        municipality: (p) => `/address/philippines/provinces/${p.prv}/municipalities`,
        barangay: (p) => `/address/philippines/municipalities/${p.mun}/barangays?prv=${p.prv}`,
    };

    const load = async (url) => {
        const response = await fetch(url, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Unable to load address data.');
        return response.json();
    };

    const reset = (level) => {
        const select = selects[level];
        select.innerHTML = `<option value="">Select ${level}</option>`;
        select.disabled = true;
        if (codes[level]) codes[level].value = '';
    };

    const parentData = (level) => {
        const parent = LEVELS[LEVELS.indexOf(level) - 1];
        return parent ? selects[parent].selectedOptions[0]?.dataset ?? {} : {};
    };

    const populate = async (level, selectedName = '') => {
        const select = selects[level];
        select.innerHTML = `<option value="">Loading ${level}…</option>`;
        select.disabled = true;

        try {
            const items = await load(ENDPOINTS[level](parentData(level)));

            select.innerHTML = `<option value="">Select ${level}</option>`;
            items.forEach(({ code, name, prv, mun }) => {
                const option = new Option(name, name, false, name === selectedName);
                option.dataset.code = code;
                option.dataset.prv = prv ?? '';
                option.dataset.mun = mun ?? '';
                select.append(option);
            });
            select.disabled = false;

            if (codes[level]) codes[level].value = select.selectedOptions[0]?.dataset.code ?? '';

            const next = LEVELS[LEVELS.indexOf(level) + 1];
            if (next && select.value) select.dispatchEvent(new Event('change'));
        } catch (error) {
            select.innerHTML = `<option value="">Could not load ${level}</option>`;
            console.error(error);
        }
    };

    LEVELS.forEach((level, index) => {
        selects[level].addEventListener('change', () => {
            for (let i = index + 1; i < LEVELS.length; i++) reset(LEVELS[i]);

            if (codes[level]) codes[level].value = selects[level].selectedOptions[0]?.dataset.code ?? '';

            const next = LEVELS[index + 1];
            if (next) populate(next, oldValues[next]);
        });
    });

    const country = document.getElementById('country');
    country?.addEventListener('change', () => {
        LEVELS.forEach(reset);
        if (country.value === 'PH') populate('province', oldValues.province);
    });

    if (country?.value === 'PH') country.dispatchEvent(new Event('change'));
});
