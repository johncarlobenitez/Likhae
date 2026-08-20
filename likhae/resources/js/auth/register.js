const forms = [...document.querySelectorAll('.registration-form')];
const cards = [...document.querySelectorAll('[data-form-target]')];

const locationData = {
    'Metro Manila': { 'Quezon City': ['Bago Bantay', 'Commonwealth', 'Diliman'], Manila: ['Ermita', 'Malate', 'Tondo'] },
    Cebu: { 'Cebu City': ['Apas', 'Lahug', 'Mabolo'], 'Lapu-Lapu': ['Basak', 'Pajo', 'Pusok'] },
    Laguna: { Calamba: ['Canlubang', 'Halang', 'Pansol'], 'Santa Rosa': ['Balibago', 'Dila', 'Tagapo'] },
};

const setOptions = (select, values, placeholder) => {
    select.innerHTML = `<option value="">${placeholder}</option>`;
    values.forEach((value) => select.insertAdjacentHTML('beforeend', `<option value="${value}">${value}</option>`));
};

const initializeForm = (form) => {
    const province = form.querySelector('.province-select');
    const municipality = form.querySelector('.municipality-select');
    const barangay = form.querySelector('.barangay-select');
    setOptions(province, Object.keys(locationData), 'Select province');

    province.addEventListener('change', () => {
        const cities = Object.keys(locationData[province.value] || {});
        setOptions(municipality, cities, 'Select municipality / city');
        municipality.disabled = cities.length === 0;
        setOptions(barangay, [], 'Select barangay');
        barangay.disabled = true;
    });
    municipality.addEventListener('change', () => {
        const barangays = locationData[province.value]?.[municipality.value] || [];
        setOptions(barangay, barangays, 'Select barangay');
        barangay.disabled = barangays.length === 0;
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
