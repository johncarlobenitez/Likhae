document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const loginForm =
        document.querySelector(
            '.auth-form'
        );


    const emailInput =
        document.getElementById(
            'email'
        );


    const passwordInput =
        document.getElementById(
            'password'
        );


    const togglePasswordButton =
        document.getElementById(
            'togglePassword'
        );


    const eyeOpen =
        document.getElementById(
            'eyeOpen'
        );


    const eyeClosed =
        document.getElementById(
            'eyeClosed'
        );


    const submitButton =
        document.querySelector(
            '.auth-submit'
        );


    /*
    |--------------------------------------------------------------------------
    | PASSWORD VISIBILITY
    |--------------------------------------------------------------------------
    */

    function togglePasswordVisibility() {

        if (
            !passwordInput
            ||
            !togglePasswordButton
        ) {
            return;
        }


        const passwordHidden =
            passwordInput.type === 'password';


        passwordInput.type =
            passwordHidden
                ? 'text'
                : 'password';


        eyeOpen?.classList.toggle(
            'hidden',
            passwordHidden
        );


        eyeClosed?.classList.toggle(
            'hidden',
            !passwordHidden
        );


        togglePasswordButton.setAttribute(
            'aria-label',
            passwordHidden
                ? 'Hide password'
                : 'Show password'
        );


        togglePasswordButton.setAttribute(
            'aria-pressed',
            passwordHidden
                ? 'true'
                : 'false'
        );

    }


    togglePasswordButton?.addEventListener(
        'click',
        togglePasswordVisibility
    );


    /*
    |--------------------------------------------------------------------------
    | REMOVE ERROR STYLE WHILE TYPING
    |--------------------------------------------------------------------------
    */

    [
        emailInput,
        passwordInput
    ].forEach((field) => {

        field?.addEventListener(
            'input',
            () => {

                field.classList.remove(
                    'is-invalid'
                );


                const fieldWrapper =
                    field.closest(
                        '.auth-field'
                    );


                fieldWrapper?.classList.remove(
                    'has-error'
                );

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | SIMPLE CLIENT VALIDATION
    |--------------------------------------------------------------------------
    */

    function validateEmail(email) {

        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/
            .test(email);

    }


    function validateLoginForm() {

        let valid =
            true;


        if (emailInput) {

            const email =
                emailInput.value.trim();


            if (
                !email
                ||
                !validateEmail(email)
            ) {

                emailInput.classList.add(
                    'is-invalid'
                );


                emailInput
                    .closest('.auth-field')
                    ?.classList.add(
                        'has-error'
                    );


                if (valid) {
                    emailInput.focus();
                }


                valid =
                    false;

            }

        }


        if (passwordInput) {

            if (
                passwordInput.value.trim()
                    .length === 0
            ) {

                passwordInput.classList.add(
                    'is-invalid'
                );


                passwordInput
                    .closest('.auth-field')
                    ?.classList.add(
                        'has-error'
                    );


                if (
                    valid
                    &&
                    !emailInput
                ) {

                    passwordInput.focus();

                }


                valid =
                    false;

            }

        }


        return valid;

    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT LOADING STATE
    |--------------------------------------------------------------------------
    */

    function setSubmittingState() {

        if (!submitButton) {
            return;
        }


        submitButton.disabled =
            true;


        submitButton.classList.add(
            'is-loading'
        );


        submitButton.dataset.originalHtml =
            submitButton.innerHTML;


        submitButton.innerHTML = `
            <span class="auth-spinner"></span>
            <span>Signing in...</span>
        `;

    }


    function resetSubmittingState() {

        if (!submitButton) {
            return;
        }


        submitButton.disabled =
            false;


        submitButton.classList.remove(
            'is-loading'
        );


        if (
            submitButton.dataset.originalHtml
        ) {

            submitButton.innerHTML =
                submitButton.dataset.originalHtml;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | FORM SUBMIT
    |--------------------------------------------------------------------------
    */

    loginForm?.addEventListener(
        'submit',
        (event) => {

            if (
                !validateLoginForm()
            ) {

                event.preventDefault();

                return;

            }


            setSubmittingState();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | ENTER KEY
    |--------------------------------------------------------------------------
    */

    [
        emailInput,
        passwordInput
    ].forEach((field) => {

        field?.addEventListener(
            'keydown',
            (event) => {

                if (
                    event.key === 'Enter'
                    &&
                    loginForm
                ) {

                    event.preventDefault();


                    if (
                        typeof loginForm.requestSubmit
                        === 'function'
                    ) {

                        loginForm.requestSubmit();

                    } else {

                        loginForm.submit();

                    }

                }

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | EMAIL NORMALIZATION
    |--------------------------------------------------------------------------
    */

    emailInput?.addEventListener(
        'blur',
        () => {

            emailInput.value =
                emailInput.value
                    .trim()
                    .toLowerCase();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | PASSWORD CAPS LOCK WARNING
    |--------------------------------------------------------------------------
    */

    if (passwordInput) {

        const capsLockMessage =
            document.createElement(
                'small'
            );


        capsLockMessage.className =
            'auth-caps-lock';


        capsLockMessage.textContent =
            'Caps Lock is on';


        capsLockMessage.hidden =
            true;


        passwordInput
            .closest('.auth-field')
            ?.appendChild(
                capsLockMessage
            );


        passwordInput.addEventListener(
            'keydown',
            (event) => {

                capsLockMessage.hidden =
                    !event.getModifierState(
                        'CapsLock'
                    );

            }
        );


        passwordInput.addEventListener(
            'keyup',
            (event) => {

                capsLockMessage.hidden =
                    !event.getModifierState(
                        'CapsLock'
                    );

            }
        );


        passwordInput.addEventListener(
            'blur',
            () => {

                capsLockMessage.hidden =
                    true;

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | RESET LOADING IF USER RETURNS USING BROWSER BACK
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'pageshow',
        (event) => {

            if (
                event.persisted
            ) {

                resetSubmittingState();

            }

        }
    );

});