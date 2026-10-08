import { initPostalAddressForm } from './postal-code.js';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-postal-address]').forEach(initPostalAddressForm);
});
