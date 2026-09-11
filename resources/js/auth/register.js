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

        const logistics =
            accountTypeInput?.value === 'logistics';

        const rider =
            accountTypeInput?.value === 'rider';

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


        if (seller || logistics) {
            steps.push({
                name:
                    'business',

                title:
                    'Business Information',
            });

        }





        if (rider) {

            steps.push({
                name:
                    'rider-vehicle',

                title:
                    'Vehicle Information',
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

        const logistics =
            type === 'logistics';

        const rider =
            type === 'rider';

        if (accountTypeInput) {

            accountTypeInput.value =
                type;

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
                !(seller || logistics);

        }


        if (sellerProgressStep) {

            sellerProgressStep.hidden =
                !(seller || logistics);

        }


        if (businessPermitUpload) {

            businessPermitUpload.hidden =
                !(seller || logistics);

        }


        sellerRequiredFields.forEach(
            (field) => {

                field.required = seller || (logistics && field.name !== 'line_of_business');
                field.disabled = !field.required;

            }
        );

        /*
        |--------------------------------------------------------------
        | Logistics specific
        |--------------------------------------------------------------
        */

        const logisticsStep =
            document.querySelector(
                '[data-logistics-step]'
            );

        const logisticsProgressStep =
            document.querySelector(
                '[data-logistics-progress-step]'
            );

        const logisticsBusinessPermitUpload =
            document.getElementById(
                'logisticsBusinessPermitUpload'
            );

        if (logisticsStep) {

            logisticsStep.hidden =
                !logistics;

        }


        if (logisticsProgressStep) {

            logisticsProgressStep.hidden =
                !logistics;

        }


        if (logisticsBusinessPermitUpload) {

            logisticsBusinessPermitUpload.hidden =
                !logistics;

        }


        const logisticsRequiredFields =
            document.querySelectorAll(
                '[data-logistics-required]'
            );

        logisticsRequiredFields.forEach(
            (field) => {

                field.required =
                    logistics;

            }
        );

        /*
        |--------------------------------------------------------------
        | Rider specific
        |--------------------------------------------------------------
        */

        const riderStep =
            document.querySelector(
                '[data-rider-step]'
            );

        const riderProgressStep =
            document.querySelector(
                '[data-rider-progress-step]'
            );

        const riderVehicleSection =
            document.getElementById(
                'riderVehicleSection'
            );

        if (riderStep) {

            riderStep.hidden =
                !rider;

        }


        if (riderProgressStep) {

            riderProgressStep.hidden =
                !rider;

        }


        if (riderVehicleSection) {

            riderVehicleSection.hidden =
                !rider;

        }


        const riderRequiredFields =
            document.querySelectorAll(
                '[data-rider-required]'
            );

        riderRequiredFields.forEach(
            (field) => {

                field.required = rider;
                field.disabled = !rider;

            }
        );

        /*
        |--------------------------------------------------------------
        | Step numbering
        |--------------------------------------------------------------
        */

        if (verificationNumber) {

            verificationNumber.textContent =
                seller || logistics || rider
                    ? '05'
                    : '04';

        }


        if (verificationStepNumber) {

            verificationStepNumber.textContent =
                seller || logistics || rider
                    ? '5'
                    : '4';

        }


        /*
        |--------------------------------------------------------------
        | Submit text
        |--------------------------------------------------------------
        */

        if (submitLabel) {

            if (seller) {

                submitLabel.textContent =
                    'Submit Seller Application';

            } else if (logistics) {

                submitLabel.textContent =
                    'Submit Logistics Application';

            } else if (rider) {

                submitLabel.textContent =
                    'Submit Rider Application';

            } else {

                submitLabel.textContent =
                    'Submit Buyer Application';

            }

        }


        /*
        |--------------------------------------------------------------
        | Reset step when changing role
        |--------------------------------------------------------------
        */

        const businessLine = document.getElementById('line_of_business');
        if (businessLine) businessLine.closest('.register-field').hidden = !seller;
        if (sellerProgressStep) {
            sellerProgressStep.hidden = !(seller || logistics || rider);
            sellerProgressStep.lastElementChild.textContent = rider ? 'Vehicle' : 'Business';
        }
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

            const steps = getSteps();
            if (currentStep < steps.length - 1) {
                event.preventDefault();
                nextButton?.click();
                return;
            }
            const password = document.getElementById('password');
            const confirmation = document.getElementById('password_confirmation');
            confirmation.setCustomValidity(password.value === confirmation.value ? '' : 'Passwords must match.');
            if (!validateCurrentStep()) {
                event.preventDefault();
                confirmation.reportValidity();
                return;
            }
            submitButton.disabled = true;
            submitLabel.textContent = 'Submitting application...';

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

    const region =
        document.querySelector(
            '[data-address-region]'
        );

    const oldRegion =
        region?.dataset.oldValue
        ?? '';

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

    const oldProvince =
        province?.dataset.oldValue
        ?? '';

    const oldMunicipality =
        municipality?.dataset.oldValue
        ?? '';

    const oldBarangay =
        barangay?.dataset.oldValue
        ?? '';


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

                option.dataset.prv =
                    record.prv ?? '';

                option.dataset.mun =
                    record.mun ?? '';

                option.dataset.level =
                    record.level ?? '';


                select.appendChild(
                    option
                );

            }
        );

    }


    async function loadRegions() {

        if (!region) {
            return;
        }


        try {

            region.innerHTML =
                '<option value="">Loading regions...</option>';


            const response =
                await fetch(
                    form.dataset.addressBase + '/regions',
                    {
                        headers: {
                            Accept:
                                'application/json'
                        }
                    }
                );


            if (!response.ok) {

                    throw new Error(
                        'Region request failed'
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


            region.innerHTML =
                '<option value="">Select region</option>';


            addOptions(
                region,
                records
            );


            region.disabled =
                false;

        } catch (error) {

            console.error(
                'Region API:',
                error
            );


            region.innerHTML =
                '<option value="">Address service unavailable — refresh to retry</option>';


            region.disabled =
                false;

        }

    }


    region?.addEventListener(
        'change',
        async () => {

            resetSelect(
                province,
                'Select province'
            );

            resetSelect(
                municipality,
                'Select municipality / city'
            );

            resetSelect(
                barangay,
                'Select barangay'
            );


            if (!region.value) {
                return;
            }


            try {

                province.innerHTML =
                    '<option value="">Loading provinces...</option>';

                const response =
                    await fetch(
                        `${form.dataset.addressBase}/regions/${encodeURIComponent(
                            region.value
                        )}/provinces`,
                        {
                            headers: {
                                Accept:
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {
                    throw new Error('Province request failed');
                }


                const result = await response.json();
                const records = Array.isArray(result)
                    ? result
                    : (result.data ?? result.provinces ?? []);


                province.innerHTML =
                    '<option value="">Select province</option>';

                addOptions(province, records);
                province.disabled = false;

                if (oldProvince) {
                    province.value = oldProvince;
                    province.dispatchEvent(new Event('change'));
                }

            } catch (error) {

                console.error('Province API:', error);
                province.innerHTML =
                    '<option value="">Address service unavailable — refresh to retry</option>';
                province.disabled = false;

            }

        }
    );


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

                const selectedProvince =
                    province.selectedOptions[0];

                if (selectedProvince?.dataset.level === 'City') {
                    municipality.innerHTML =
                        '<option value="">Select municipality / city</option>';

                    const cityOption = new Option(
                        selectedProvince.textContent,
                        selectedProvince.value
                    );

                    cityOption.dataset.prv =
                        selectedProvince.dataset.prv ?? '';

                    cityOption.dataset.mun = '0';
                    municipality.appendChild(cityOption);
                    municipality.disabled = false;

                    municipality.value = oldMunicipality || selectedProvince.value;
                    municipality.dispatchEvent(new Event('change'));
                    return;
                }

                municipality.innerHTML =
                    '<option value="">Loading municipalities...</option>';


                const response =
                    await fetch(
                        `${form.dataset.addressBase}/provinces/${encodeURIComponent(
                            province.value
                        )}/municipalities?prv=${encodeURIComponent(
                            province.selectedOptions[0]?.dataset.prv
                            || province.value
                        )}`,
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

                if (oldMunicipality) {
                    municipality.value = oldMunicipality;
                    municipality.dispatchEvent(new Event('change'));
                }

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
                        `${form.dataset.addressBase}/municipalities/${encodeURIComponent(
                            municipality.value
                        )}/barangays?mun=${encodeURIComponent(
                            municipality.selectedOptions[0]?.dataset.mun
                            || municipality.value
                        )}&prv=${encodeURIComponent(
                            municipality.selectedOptions[0]?.dataset.prv
                            || province.selectedOptions[0]?.dataset.prv
                            || ''
                        )}`,
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

                if (oldBarangay) {
                    barangay.value = oldBarangay;
                }

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

    loadRegions().then(() => {
        if (oldRegion) {
            region.value = oldRegion;
            region.dispatchEvent(new Event('change'));
        }
    });


    window.addEventListener('pageshow', () => {
        submitButton.disabled = false;
    });
    const initialAccountType = accountTypeInput?.value;

    setAccountType(
        ['buyer', 'seller', 'logistics', 'rider'].includes(initialAccountType)
            ? initialAccountType
            : 'buyer'
    );

});
