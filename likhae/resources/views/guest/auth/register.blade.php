<x-marketplace.layout title="Create Account" :show-footer="false">
    <div class="g-page" data-guest-register-page>
        <div class="g-container" style="max-width:980px">
            <section class="g-card g-register-card g-shadow">
                <span class="g-eyebrow">Buyer registration</span>

                <h1>Create your LIKHAE account</h1>

                <p>
                    Buyer registration uses the project's existing registration workflow
                    so your personal information, Philippine address, and identification
                    can be validated without replacing the current backend.
                </p>

                <div class="g-register-highlights">
                    <div class="g-register-highlight">
                        <span class="g-register-highlight-icon">1</span>
                        <div>
                            <strong>Personal Information</strong>
                            <div class="g-muted" style="font-size:12px;margin-top:3px">
                                Name, sex, birthday, and age.
                            </div>
                        </div>
                    </div>

                    <div class="g-register-highlight">
                        <span class="g-register-highlight-icon">2</span>
                        <div>
                            <strong>Contact & Philippine Address</strong>
                            <div class="g-muted" style="font-size:12px;margin-top:3px">
                                Province → City / Municipality → Barangay.
                            </div>
                        </div>
                    </div>

                    <div class="g-register-highlight">
                        <span class="g-register-highlight-icon">3</span>
                        <div>
                            <strong>ID Verification</strong>
                            <div class="g-muted" style="font-size:12px;margin-top:3px">
                                Upload the identification required by the existing backend.
                            </div>
                        </div>
                    </div>

                    <div class="g-register-highlight">
                        <span class="g-register-highlight-icon">4</span>
                        <div>
                            <strong>Administrator Approval</strong>
                            <div class="g-muted" style="font-size:12px;margin-top:3px">
                                Approval status will be sent through the project's registration process.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="g-alert g-alert-success" style="margin-bottom:18px">
                    After submitting your registration, please wait for the administrator's approval,
                    which will be sent to your email.
                </div>

                <div style="display:flex;gap:9px;flex-wrap:wrap">
                    <a class="g-btn g-btn-primary" href="{{ route('register') }}">
                        Continue to Registration
                    </a>

                    <a class="g-btn g-btn-secondary" href="{{ route('login') }}">
                        I already have an account
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-marketplace.layout>
