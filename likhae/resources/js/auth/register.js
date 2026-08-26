const forms = [...document.querySelectorAll('.registration-form')];
const cards = [...document.querySelectorAll('[data-form-target]')];

const ENDPOINTS = {
    province: () => '/address/philippines/provinces',
    municipality: (prv) => `/address/philippines/provinces/${prv}/municipalities`,
    barangay: (mun, prv) => `/address/philippines/municipalities/${mun}/barangays?prv=${prv}`,
};

const load = async (url) => {
    const res = await fetch(url, { headers: { Accept: 'application/json' } });
    if (!res.ok) throw new Error('Unable to load address data.');
    return res.json();
};

const setOptions = (select, items, placeholder) => {
    select.innerHTML = `<option value="">${placeholder}</option>`;
    items.forEach(({ code, name, prv, mun }) => {
        const opt = new Option(name, name);
        opt.dataset.code = code;
        opt.dataset.prv = prv ?? '';   // integer filter value (e.g. 28)
        opt.dataset.mun = mun ?? '';   // integer filter value
        select.append(opt);
    });
};

const initializeForm = (form) => {
    const province = form.querySelector('.province-select');
    const municipality = form.querySelector('.municipality-select');
    const barangay = form.querySelector('.barangay-select');

    load(ENDPOINTS.province()).then((items) => {
        setOptions(province, items, 'Select province');
    }).catch(() => {
        province.innerHTML = '<option value="">Could not load provinces</option>';
    });

    province.addEventListener('change', async () => {
        municipality.innerHTML = '<option value="">Loading…</option>';
        municipality.disabled = true;
        barangay.innerHTML = '<option value="">Select barangay</option>';
        barangay.disabled = true;

        if (!province.value) {
            municipality.innerHTML = '<option value="">Select municipality / city</option>';
            return;
        }

        const prv = province.selectedOptions[0]?.dataset.prv;
        try {
            const items = await load(ENDPOINTS.municipality(prv));
            setOptions(municipality, items, 'Select municipality / city');
            municipality.disabled = false;
        } catch {
            municipality.innerHTML = '<option value="">Could not load municipalities</option>';
        }
    });

    municipality.addEventListener('change', async () => {
        barangay.innerHTML = '<option value="">Loading…</option>';
        barangay.disabled = true;

        if (!municipality.value) {
            barangay.innerHTML = '<option value="">Select barangay</option>';
            return;
        }

        const mun = municipality.selectedOptions[0]?.dataset.mun;
        const prv = province.selectedOptions[0]?.dataset.prv;
        try {
            const items = await load(ENDPOINTS.barangay(mun, prv));
            setOptions(barangay, items, 'Select barangay');
            barangay.disabled = false;
        } catch {
            barangay.innerHTML = '<option value="">Could not load barangays</option>';
        }
    });

    const birthday = form.querySelector('.birthday-input');
    const age = form.querySelector('.age-output');
    birthday.addEventListener('input', () => {
        if (!birthday.value) return (age.value = '');
        const birthDate = new Date(`${birthday.value}T00:00:00`);
        const today = new Date();
        let years = today.getFullYear() - birthDate.getFullYear();
        if (today < new Date(today.getFullYear(), birthDate.getMonth(), birthDate.getDate())) years -= 1;
        age.value = years >= 0 ? years : '';
    });
};

forms.forEach(initializeForm);
cards.forEach((card) => card.addEventListener('click', () => {
    cards.forEach((item) => item.classList.toggle('is-selected', item === card));
    forms.forEach((form) => form.classList.toggle('hidden', form.id !== card.dataset.formTarget));
}));
