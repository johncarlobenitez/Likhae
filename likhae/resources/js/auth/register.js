document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const form =
        document.getElementById(
            'registrationForm'
        );


    const accountTypeInput =
        document.getElementById(
            'accountType'
        );


    const accountButtons =
        document.querySelectorAll(
            '[data-account-type]'
        );


    const sellerStep =
        document.querySelector(
            '[data-seller-step]'
        );


    const sellerProgressStep =
        document.querySelector(
            '[data-seller-progress-step]'
        );


    const businessPermitUpload =
        document.getElementById(
            'businessPermitUpload'
        );


    const sellerRequiredFields =
        document.querySelectorAll(
            '[data-seller-required]'
        );


    const verificationNumber =
        document.querySelector(
            '[data-verification-number]'
        );


    const verificationStepNumber =
        document.querySelector(
            '[data-verification-step-number]'
        );


    const submitLabel =
        document.querySelector(
            '[data-submit-label]'
        );


    const nextButton =
        document.querySelector(
            '[data-step-next]'
        );


    const backButton =
        document.querySelector(
            '[data-step-back]'
        );


    const submitButton =
        document.querySelector(
            '[data-submit-button]'
        );


    const loginLink =
        document.querySelector(
            '[data-login-link]'
        );


    const progressTitle =
        document.querySelector(
            '[data-progress-title]'
        );


    const progressCounter =
        document.querySelector(
            '[data-progress-counter]'
        );


    const progressFill =
        document.querySelector(
            '[data-progress-fill]'
        );


    const stepError =
        document.querySelector(
            '[data-step-error]'
        );


    let currentStep = 0;


    /*
    |--------------------------------------------------------------------------
    | BUILD ACTIVE STEPS
    |--------------------------------------------------------------------------
    */

    function getSteps() {

        const seller =
            accountTypeInput?.value === 'seller';


        const steps = [
            {
                name:
                    'personal',

                title:
                    'Personal Information',
            },

            {
                name:
                    'contact',

                title:
                    'Contact Information',
            },

            {
                name:
                    'address',

                title:
                    'Address',
            },
        ];


        if (seller) {

            steps.push({
                name:
                    'business',

                title:
                    'Business Information',
            });

        }


        steps.push({
            name:
                'verification',

            title:
                'Account Verification',
        });


        return steps;

    }


    /*
    |--------------------------------------------------------------------------
    | ACCOUNT TYPE
    |--------------------------------------------------------------------------
    */

    function setAccountType(type) {

        const seller =
            type === 'seller';


        if (accountTypeInput) {

            accountTypeInput.value =
                seller
                    ? 'seller'
                    : 'buyer';

        }


        accountButtons.forEach(
            (button) => {

                const active =
                    button.dataset.accountType ===
                    type;


                button.classList.toggle(
                    'is-active',
                    active
                );


                button.setAttribute(
                    'aria-pressed',
                    active
                        ? 'true'
                        : 'false'
                );

            }
        );


        /*
        |--------------------------------------------------------------
        | Seller step
        |--------------------------------------------------------------
        */

        if (sellerStep) {

            sellerStep.hidden =
                !seller;

        }


        if (sellerProgressStep) {

            sellerProgressStep.hidden =
                !seller;

        }


        if (businessPermitUpload) {

            businessPermitUpload.hidden =
                !seller;

        }


        sellerRequiredFields.forEach(
            (field) => {

                field.required =
                    seller;

            }
        );


        /*
        |--------------------------------------------------------------
        | Step numbering
        |--------------------------------------------------------------
        */

        if (verificationNumber) {

            verificationNumber.textContent =
                seller
                    ? '05'
                    : '04';

        }


        if (verificationStepNumber) {

            verificationStepNumber.textContent =
                seller
                    ? '5'
                    : '4';

        }


        /*
        |--------------------------------------------------------------
        | Submit text
        |--------------------------------------------------------------
        */

        if (submitLabel) {

            submitLabel.textContent =
                seller
                    ? 'Submit Seller Application'
                    : 'Submit Buyer Application';

        }


        /*
        |--------------------------------------------------------------
        | Reset step when changing role
        |--------------------------------------------------------------
        */

        currentStep = 0;


        showStep(
            currentStep
        );

    }


    accountButtons.forEach(
        (button) => {

            button.addEventListener(
                'click',
                () => {

                    setAccountType(
                        button.dataset.accountType
                    );

                }
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | STEP ELEMENT
    |--------------------------------------------------------------------------
    */

    function findStepElement(name) {

        return document.querySelector(
            `[data-form-step][data-step="${name}"]`
        );

    }


    /*
    |--------------------------------------------------------------------------
    | DISPLAY STEP
    |--------------------------------------------------------------------------
    */

    function showStep(index) {

        const steps =
            getSteps();


        if (index < 0) {
            index = 0;
        }


        if (
            index >
            steps.length - 1
        ) {

            index =
                steps.length - 1;

        }


        currentStep =
            index;


        /*
        |--------------------------------------------------------------
        | Hide everything
        |--------------------------------------------------------------
        */

        document
            .querySelectorAll(
                '[data-form-step]'
            )
            .forEach(
                (section) => {

                    section.hidden =
                        true;


                    section.classList.remove(
                        'is-active'
                    );

                }
            );


        /*
        |--------------------------------------------------------------
        | Show active section
        |--------------------------------------------------------------
        */

        const current =
            steps[currentStep];


        const currentElement =
            findStepElement(
                current.name
            );


        if (currentElement) {

            currentElement.hidden =
                false;


            currentElement.classList.add(
                'is-active'
            );

        }


        /*
        |--------------------------------------------------------------
        | Progress header
        |--------------------------------------------------------------
        */

        if (progressTitle) {

            progressTitle.textContent =
                current.title;

        }


        if (progressCounter) {

            progressCounter.textContent =
                `Step ${currentStep + 1} of ${steps.length}`;

        }


        if (progressFill) {

            const percent =
                (
                    (
                        currentStep + 1
                    )
                    /
                    steps.length
                )
                *
                100;


            progressFill.style.width =
                `${percent}%`;

        }


        /*
        |--------------------------------------------------------------
        | Progress dots
        |--------------------------------------------------------------
        */

        updateProgressSteps();


        /*
        |--------------------------------------------------------------
        | Buttons
        |--------------------------------------------------------------
        */

        const lastStep =
            currentStep ===
            steps.length - 1;


        if (backButton) {

            backButton.hidden =
                currentStep === 0;

        }


        if (loginLink) {

            loginLink.hidden =
                currentStep !== 0;

        }


        if (nextButton) {

            nextButton.hidden =
                lastStep;

        }


        if (submitButton) {

            submitButton.hidden =
                !lastStep;

        }


        /*
        |--------------------------------------------------------------
        | Error message
        |--------------------------------------------------------------
        */

        hideStepError();


        /*
        |--------------------------------------------------------------
        | Scroll
        |--------------------------------------------------------------
        */

        document
            .querySelector(
                '.registration-progress'
            )
            ?.scrollIntoView({
                behavior:
                    'smooth',

                block:
                    'start',
            });

    }


    /*
    |--------------------------------------------------------------------------
    | PROGRESS DOTS
    |--------------------------------------------------------------------------
    */

    function updateProgressSteps() {

        const steps =
            getSteps();


        const progressButtons =
            document.querySelectorAll(
                '[data-progress-step]'
            );


        progressButtons.forEach(
            (button) => {

                button.classList.remove(
                    'is-active',
                    'is-complete'
                );

            }
        );


        steps.forEach(
            (step, index) => {

                let button;


                if (
                    step.name ===
                    'verification'
                ) {

                    button =
                        document.querySelector(
                            '[data-verification-progress-step]'
                        );

                } else {

                    button =
                        document.querySelector(
                            `[data-progress-step="${index}"]`
                        );

                }


                if (!button) {
                    return;
                }


                if (
                    index <
                    currentStep
                ) {

                    button.classList.add(
                        'is-complete'
                    );

                }


                if (
                    index ===
                    currentStep
                ) {

                    button.classList.add(
                        'is-active'
                    );

                }

            });

    }


    /*
    |--------------------------------------------------------------------------
    | CURRENT STEP VALIDATION
    |--------------------------------------------------------------------------
    */

    function validateCurrentStep() {

        const steps =
            getSteps();


        const current =
            steps[currentStep];


        const section =
            findStepElement(
                current.name
            );


        if (!section) {
            return true;
        }


        const fields =
            section.querySelectorAll(
                'input, select, textarea'
            );


        let valid =
            true;


        let firstInvalid =
            null;


        fields.forEach(
            (field) => {

                /*
                |----------------------------------------------------------
                | Ignore disabled / non-required fields
                |----------------------------------------------------------
                */

                if (
                    field.disabled
                    ||
                    !field.required
                ) {

                    field.classList.remove(
                        'is-invalid'
                    );


                    return;

                }


                /*
                |----------------------------------------------------------
                | Native browser validation
                |----------------------------------------------------------
                */

                if (
                    !field.checkValidity()
                ) {

                    valid =
                        false;


                    field.classList.add(
                        'is-invalid'
                    );


                    if (!firstInvalid) {

                        firstInvalid =
                            field;

                    }

                } else {

                    field.classList.remove(
                        'is-invalid'
                    );

                }

            }
        );


        if (!valid) {

            showStepError();


            firstInvalid?.focus();


            return false;

        }


        hideStepError();


        return true;

    }


    /*
    |--------------------------------------------------------------------------
    | ERROR
    |--------------------------------------------------------------------------
    */

    function showStepError() {

        if (stepError) {

            stepError.hidden =
                false;

        }

    }


    function hideStepError() {

        if (stepError) {

            stepError.hidden =
                true;

        }


        document
            .querySelectorAll(
                '.is-invalid'
            )
            .forEach(
                (field) => {

                    field.classList.remove(
                        'is-invalid'
                    );

                }
            );

    }


    /*
    |--------------------------------------------------------------------------
    | NEXT
    |--------------------------------------------------------------------------
    */

    nextButton?.addEventListener(
        'click',
        () => {

            if (
                !validateCurrentStep()
            ) {

                return;

            }


            const steps =
                getSteps();


            if (
                currentStep <
                steps.length - 1
            ) {

                currentStep++;


                showStep(
                    currentStep
                );

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | BACK
    |--------------------------------------------------------------------------
    */

    backButton?.addEventListener(
        'click',
        () => {

            if (
                currentStep > 0
            ) {

                currentStep--;


                showStep(
                    currentStep
                );

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | PREVENT ACCIDENTAL PROGRESS STEP SKIPPING
    |--------------------------------------------------------------------------
    |
    | Completed steps may be clicked to go back.
    | Users cannot jump ahead and bypass validation.
    |
    */

    document
        .querySelectorAll(
            '[data-progress-step]'
        )
        .forEach(
            (button) => {

                button.addEventListener(
                    'click',
                    () => {

                        const steps =
                            getSteps();


                        let targetIndex =
                            -1;


                        if (
                            button.hasAttribute(
                                'data-verification-progress-step'
                            )
                        ) {

                            targetIndex =
                                steps.findIndex(
                                    (step) =>
                                        step.name ===
                                        'verification'
                                );

                        } else {

                            const raw =
                                Number(
                                    button.dataset.progressStep
                                );


                            if (
                                Number.isInteger(
                                    raw
                                )
                            ) {

                                targetIndex =
                                    raw;

                            }

                        }


                        if (
                            targetIndex >= 0
                            &&
                            targetIndex <
                            currentStep
                        ) {

                            showStep(
                                targetIndex
                            );

                        }

                    }
                );

            }
        );


    /*
    |--------------------------------------------------------------------------
    | FINAL SUBMIT VALIDATION
    |--------------------------------------------------------------------------
    */

    form?.addEventListener(
        'submit',
        (event) => {

            if (
                !validateCurrentStep()
            ) {

                event.preventDefault();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | BIRTHDAY -> AGE
    |--------------------------------------------------------------------------
    */

    const birthday =
        document.getElementById(
            'birthday'
        );


    const age =
        document.getElementById(
            'age'
        );


    function calculateAge() {

        if (
            !birthday
            ||
            !age
            ||
            !birthday.value
        ) {

            if (age) {

                age.value =
                    '';

            }


            return;

        }


        const birthDate =
            new Date(
                `${birthday.value}T00:00:00`
            );


        const today =
            new Date();


        let calculatedAge =
            today.getFullYear()
            -
            birthDate.getFullYear();


        const monthDifference =
            today.getMonth()
            -
            birthDate.getMonth();


        if (
            monthDifference < 0
            ||
            (
                monthDifference === 0
                &&
                today.getDate()
                <
                birthDate.getDate()
            )
        ) {

            calculatedAge--;

        }


        age.value =
            Math.max(
                0,
                calculatedAge
            );

    }


    birthday?.addEventListener(
        'change',
        calculateAge
    );


    birthday?.addEventListener(
        'input',
        calculateAge
    );


    calculateAge();


    /*
    |--------------------------------------------------------------------------
    | FILE DISPLAY
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll(
            '[data-file-input]'
        )
        .forEach(
            (input) => {

                input.addEventListener(
                    'change',
                    () => {

                        const uploadBox =
                            input.closest(
                                '.upload-box'
                            );


                        const fileName =
                            uploadBox?.querySelector(
                                '[data-file-name]'
                            );


                        if (!fileName) {
                            return;
                        }


                        const file =
                            input.files?.[0];


                        if (!file) {

                            fileName.textContent =
                                'No file selected';


                            return;

                        }


                        const fileSize =
                            (
                                file.size
                                /
                                1024
                                /
                                1024
                            )
                            .toFixed(2);


                        fileName.textContent =
                            `${file.name} · ${fileSize} MB`;

                    }
                );

            }
        );


    /*
    |--------------------------------------------------------------------------
    | PHILIPPINE ADDRESS
    |--------------------------------------------------------------------------
    */

    const province =
        document.querySelector(
            '[data-address-province]'
        );


    const municipality =
        document.querySelector(
            '[data-address-municipality]'
        );


    const barangay =
        document.querySelector(
            '[data-address-barangay]'
        );


    function resetSelect(
        select,
        placeholder
    ) {

        if (!select) {
            return;
        }


        select.innerHTML =
            `<option value="">${placeholder}</option>`;


        select.disabled =
            true;

    }


    function addOptions(
        select,
        records
    ) {

        records.forEach(
            (record) => {

                const option =
                    document.createElement(
                        'option'
                    );


                const value =
                    record.code
                    ??
                    record.id
                    ??
                    record.value
                    ??
                    record.name;


                const label =
                    record.name
                    ??
                    record.label
                    ??
                    record.description
                    ??
                    value;


                option.value =
                    value;


                option.textContent =
                    label;


                select.appendChild(
                    option
                );

            }
        );

    }


    async function loadProvinces() {

        if (!province) {
            return;
        }


        try {

            province.innerHTML =
                '<option value="">Loading provinces...</option>';


            const response =
                await fetch(
                    '/address/philippines/provinces',
                    {
                        headers: {
                            Accept:
                                'application/json'
                        }
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'Province request failed'
                );

            }


            const result =
                await response.json();


            const records =
                Array.isArray(result)
                    ? result
                    : (
                        result.data
                        ??
                        result.provinces
                        ??
                        []
                    );


            province.innerHTML =
                '<option value="">Select province</option>';


            addOptions(
                province,
                records
            );


            province.disabled =
                false;

        } catch (error) {

            console.error(
                'Province API:',
                error
            );


            province.innerHTML =
                '<option value="">Address service unavailable — refresh to retry</option>';


            province.disabled =
                false;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | PROVINCE -> MUNICIPALITY
    |--------------------------------------------------------------------------
    */

    province?.addEventListener(
        'change',
        async () => {

            resetSelect(
                municipality,
                'Select municipality / city'
            );


            resetSelect(
                barangay,
                'Select barangay'
            );


            if (!province.value) {
                return;
            }


            try {

                municipality.innerHTML =
                    '<option value="">Loading municipalities...</option>';


                const response =
                    await fetch(
                        `/address/philippines/provinces/${encodeURIComponent(
                            province.value
                        )}/municipalities`,
                        {
                            headers: {
                                Accept:
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'Municipality request failed'
                    );

                }


                const result =
                    await response.json();


                const records =
                    Array.isArray(result)
                        ? result
                        : (
                            result.data
                            ??
                            result.municipalities
                            ??
                            []
                        );


                municipality.innerHTML =
                    '<option value="">Select municipality / city</option>';


                addOptions(
                    municipality,
                    records
                );


                municipality.disabled =
                    false;

            } catch (error) {

                console.error(
                    'Municipality API:',
                    error
                );


                municipality.innerHTML =
                    '<option value="">Address service unavailable</option>';

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | MUNICIPALITY -> BARANGAY
    |--------------------------------------------------------------------------
    */

    municipality?.addEventListener(
        'change',
        async () => {

            resetSelect(
                barangay,
                'Select barangay'
            );


            if (!municipality.value) {
                return;
            }


            try {

                barangay.innerHTML =
                    '<option value="">Loading barangays...</option>';


                const response =
                    await fetch(
                        `/address/philippines/municipalities/${encodeURIComponent(
                            municipality.value
                        )}/barangays`,
                        {
                            headers: {
                                Accept:
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'Barangay request failed'
                    );

                }


                const result =
                    await response.json();


                const records =
                    Array.isArray(result)
                        ? result
                        : (
                            result.data
                            ??
                            result.barangays
                            ??
                            []
                        );


                barangay.innerHTML =
                    '<option value="">Select barangay</option>';


                addOptions(
                    barangay,
                    records
                );


                barangay.disabled =
                    false;

            } catch (error) {

                console.error(
                    'Barangay API:',
                    error
                );


                barangay.innerHTML =
                    '<option value="">Address service unavailable</option>';

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE
    |--------------------------------------------------------------------------
    */

    loadProvinces();


    setAccountType(
        accountTypeInput?.value === 'seller'
            ? 'seller'
            : 'buyer'
    );

});
const provinceSelect =
    document.querySelector(
        '[data-address-province]'
    );


const municipalitySelect =
    document.querySelector(
        '[data-address-municipality]'
    );


const barangaySelect =
    document.querySelector(
        '[data-address-barangay]'
    );


/*
|--------------------------------------------------------------------------
| Original Values
|--------------------------------------------------------------------------
|
| Useful if Laravel sends validation errors and reloads the form.
|
*/

const oldProvince =
    provinceSelect?.dataset.oldValue
    ?? '';


const oldMunicipality =
    municipalitySelect?.dataset.oldValue
    ?? '';


const oldBarangay =
    barangaySelect?.dataset.oldValue
    ?? '';


/*
|--------------------------------------------------------------------------
| Reset Select
|--------------------------------------------------------------------------
*/

function resetAddressSelect(
    select,
    placeholder
) {

    if (!select) {
        return;
    }


    select.innerHTML =
        `<option value="">${placeholder}</option>`;


    select.disabled =
        true;

}


/*
|--------------------------------------------------------------------------
| Loading State
|--------------------------------------------------------------------------
*/

function setAddressLoading(
    select,
    message
) {

    if (!select) {
        return;
    }


    select.disabled =
        true;


    select.innerHTML =
        `<option value="">${message}</option>`;

}


/*
|--------------------------------------------------------------------------
| Error State
|--------------------------------------------------------------------------
*/

function setAddressError(
    select
) {

    if (!select) {
        return;
    }


    select.disabled =
        false;


    select.innerHTML = `
        <option value="">
            Address service unavailable — refresh to retry
        </option>
    `;

}


/*
|--------------------------------------------------------------------------
| Populate Options
|--------------------------------------------------------------------------
*/

function populateAddressOptions(
    select,
    records,
    placeholder,
    selectedValue = ''
) {

    if (!select) {
        return;
    }


    select.innerHTML =
        `<option value="">${placeholder}</option>`;


    records.forEach(
        (record) => {

            const option =
                document.createElement(
                    'option'
                );


            option.value =
                record.code;


            option.textContent =
                record.name;


            if (
                String(record.code)
                ===
                String(selectedValue)
            ) {

                option.selected =
                    true;

            }


            select.appendChild(
                option
            );

        }
    );


    select.disabled =
        false;

}


/*
|--------------------------------------------------------------------------
| Get JSON
|--------------------------------------------------------------------------
*/

async function fetchAddressData(
    url
) {

    const response =
        await fetch(
            url,
            {
                headers: {
                    Accept:
                        'application/json',

                    'X-Requested-With':
                        'XMLHttpRequest',
                },
            }
        );


    if (!response.ok) {

        throw new Error(
            `Address API request failed: ${response.status}`
        );

    }


    const result =
        await response.json();


    return Array.isArray(result)
        ? result
        : (
            result.data
            ??
            []
        );

}


/*
|--------------------------------------------------------------------------
| Load Provinces
|--------------------------------------------------------------------------
*/

async function loadProvinces(
    selectedProvince = ''
) {

    if (!provinceSelect) {
        return;
    }


    try {

        setAddressLoading(
            provinceSelect,
            'Loading provinces...'
        );


        const provinces =
            await fetchAddressData(
                '/address/philippines/provinces'
            );


        populateAddressOptions(
            provinceSelect,
            provinces,
            'Select province',
            selectedProvince
        );


    } catch (error) {

        console.error(
            'Province loading error:',
            error
        );


        setAddressError(
            provinceSelect
        );

    }

}


/*
|--------------------------------------------------------------------------
| Load Cities / Municipalities
|--------------------------------------------------------------------------
*/

async function loadMunicipalities(
    provinceCode,
    selectedMunicipality = ''
) {

    resetAddressSelect(
        municipalitySelect,
        'Select municipality / city'
    );


    resetAddressSelect(
        barangaySelect,
        'Select barangay'
    );


    if (!provinceCode) {
        return;
    }


    try {

        setAddressLoading(
            municipalitySelect,
            'Loading cities / municipalities...'
        );


        const municipalities =
            await fetchAddressData(
                `/address/philippines/provinces/${encodeURIComponent(
                    provinceCode
                )}/municipalities`
            );


        populateAddressOptions(
            municipalitySelect,
            municipalities,
            'Select municipality / city',
            selectedMunicipality
        );


    } catch (error) {

        console.error(
            'Municipality loading error:',
            error
        );


        setAddressError(
            municipalitySelect
        );

    }

}


/*
|--------------------------------------------------------------------------
| Load Barangays
|--------------------------------------------------------------------------
*/

async function loadBarangays(
    municipalityCode,
    selectedBarangay = ''
) {

    resetAddressSelect(
        barangaySelect,
        'Select barangay'
    );


    if (!municipalityCode) {
        return;
    }


    try {

        setAddressLoading(
            barangaySelect,
            'Loading barangays...'
        );


        const barangays =
            await fetchAddressData(
                `/address/philippines/municipalities/${encodeURIComponent(
                    municipalityCode
                )}/barangays`
            );


        populateAddressOptions(
            barangaySelect,
            barangays,
            'Select barangay',
            selectedBarangay
        );


    } catch (error) {

        console.error(
            'Barangay loading error:',
            error
        );


        setAddressError(
            barangaySelect
        );

    }

}


/*
|--------------------------------------------------------------------------
| Province Change
|--------------------------------------------------------------------------
*/

provinceSelect?.addEventListener(
    'change',
    async () => {

        await loadMunicipalities(
            provinceSelect.value
        );

    }
);


/*
|--------------------------------------------------------------------------
| Municipality Change
|--------------------------------------------------------------------------
*/

municipalitySelect?.addEventListener(
    'change',
    async () => {

        await loadBarangays(
            municipalitySelect.value
        );

    }
);


/*
|--------------------------------------------------------------------------
| Initialize Address
|--------------------------------------------------------------------------
*/

async function initializeAddressSelection() {

    await loadProvinces(
        oldProvince
    );


    if (oldProvince) {

        await loadMunicipalities(
            oldProvince,
            oldMunicipality
        );

    }


    if (
        oldMunicipality
    ) {

        await loadBarangays(
            oldMunicipality,
            oldBarangay
        );

    }

}


initializeAddressSelection();