@extends('layouts.admin')

@section('title', 'Account')
@section('subtitle', 'Manage your administrator profile, security, and notification preferences.')
@section('active', 'account')

@php
    $tabs = [
        'profile' => 'Profile',
        'security' => 'Security',
        'notifications' => 'Notification Settings',
    ];

    $requestedTab = request('tab', 'profile');

    $tab = array_key_exists($requestedTab, $tabs)
        ? $requestedTab
        : 'profile';

    $admin = auth()->user();

    $notificationSettings = [
        [
            'name' => 'High-risk product flags',
            'description' => 'Immediate alerts for suspected prohibited listings.',
            'checked' => true,
        ],
        [
            'name' => 'Urgent disputes',
            'description' => 'Cases with critical priority or expiring response deadlines.',
            'checked' => true,
        ],
        [
            'name' => 'Registration backlog',
            'description' => 'Alert when pending applications exceed the service target.',
            'checked' => true,
        ],
        [
            'name' => 'Delivery exceptions',
            'description' => 'Failed or materially delayed marketplace deliveries.',
            'checked' => true,
        ],
        [
            'name' => 'Finance settlement failures',
            'description' => 'Payment and seller-settlement errors.',
            'checked' => true,
        ],
        [
            'name' => 'Weekly platform digest',
            'description' => 'Consolidated performance and risk summary.',
            'checked' => false,
        ],
    ];
@endphp

@section('content')
<style>
    :root {
        --acct-bg: #FBF7F2;
        --acct-bg-soft: #F6EFE7;
        --acct-bg-alt: #EFE7DE;
        --acct-card: #FFFDF9;

        --acct-border: #EADCCC;
        --acct-border-strong: #DBCEC1;

        --acct-maroon: #561C17;
        --acct-maroon-2: #642920;
        --acct-maroon-dark: #3E130F;

        --acct-text: #3B211B;
        --acct-brown: #6C4936;
        --acct-muted: #987865;
        --acct-muted-2: #A99386;

        --acct-tan: #C19771;

        --acct-success: #256F4A;
        --acct-success-soft: #EAF7EF;

        --acct-warning: #9A5B11;
        --acct-warning-soft: #FFF6DE;

        --acct-danger: #B42318;
        --acct-danger-soft: #FCEBE9;

        --acct-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --acct-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ad-account-page {
        color: var(--acct-text);

        --ad-blue: var(--acct-maroon);
        --ad-blue-dark: var(--acct-maroon-dark);
        --ad-blue-soft: #F3E4DE;
        --ad-blue-border: #E6C7BE;
    }

    .ad-account-page .ad-page-head {
        padding: 32px 36px;

        border: 1px solid var(--acct-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--acct-shadow-soft);
    }

    .ad-account-page .ad-overline {
        color: var(--acct-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-account-page .ad-page-head h2 {
        margin-top: 10px;

        color: var(--acct-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-account-page .ad-page-head p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--acct-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .ad-account-layout {
        display: grid;
        grid-template-columns: 260px minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .ad-account-nav {
        display: grid;
        gap: 6px;

        padding: 8px;

        border: 1px solid var(--acct-border) !important;
        border-radius: 22px !important;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.14), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%) !important;

        box-shadow: var(--acct-shadow-soft) !important;
    }

    .ad-account-nav a {
        display: flex;
        min-height: 42px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;

        padding: 0 14px;

        border-radius: 14px;

        color: var(--acct-brown);

        font-size: 12px;
        font-weight: 900;
        text-decoration: none;

        transition: 160ms ease;
    }

    .ad-account-nav a:hover {
        background: var(--acct-bg-soft);
        color: var(--acct-maroon);
    }

    .ad-account-nav a.is-active {
        background: var(--acct-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .ad-account-panel {
        overflow: hidden;

        border: 1px solid var(--acct-border) !important;
        border-radius: 24px !important;

        background: var(--acct-card) !important;
        color: var(--acct-text) !important;

        box-shadow: var(--acct-shadow-soft) !important;
    }

    .ad-account-section {
        display: grid;
        gap: 20px;

        padding: 24px;
    }

    .ad-account-section h2 {
        margin: 0;

        color: var(--acct-text);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 34px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-account-section > p {
        max-width: 660px;
        margin: -10px 0 0;

        color: var(--acct-muted);

        font-size: 12px;
        line-height: 1.65;
    }

    .ad-account-profile-card {
        display: flex;
        align-items: center;
        gap: 14px;

        padding: 16px;

        border: 1px solid var(--acct-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.16), transparent 28%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 100%);
    }

    .ad-account-avatar {
        display: grid;
        width: 58px;
        height: 58px;
        flex: 0 0 58px;
        place-items: center;

        border-radius: 18px;

        background: var(--acct-maroon);
        color: #FFFFFF;

        font-size: 20px;
        font-weight: 950;
        letter-spacing: -0.03em;
    }

    .ad-account-profile-copy {
        display: grid;
        min-width: 0;
        gap: 4px;
    }

    .ad-account-profile-copy strong {
        color: var(--acct-text);
        font-size: 15px;
        font-weight: 950;
        letter-spacing: -0.025em;
    }

    .ad-account-profile-copy span {
        color: var(--acct-muted);
        font-size: 11px;
        font-weight: 700;
    }

    .ad-account-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .ad-account-form-grid .is-full {
        grid-column: 1 / -1;
    }

    .ad-account-page .ad-field {
        display: grid;
        gap: 7px;
    }

    .ad-account-page .ad-field span {
        color: var(--acct-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .ad-account-page .ad-field input,
    .ad-account-page .ad-field select,
    .ad-account-page .ad-field textarea {
        width: 100%;
        min-height: 43px;
        padding: 0 13px;

        border: 1px solid var(--acct-border);
        border-radius: 14px;

        background: var(--acct-bg-soft);
        color: var(--acct-text);

        font-size: 12px;
        font-weight: 700;
        outline: 0;

        transition: 160ms ease;
    }

    .ad-account-page .ad-field textarea {
        min-height: 130px;
        padding-block: 12px;

        line-height: 1.65;
        resize: vertical;
    }

    .ad-account-page .ad-field input:focus,
    .ad-account-page .ad-field select:focus,
    .ad-account-page .ad-field textarea:focus {
        border-color: var(--acct-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-account-page .ad-field input[readonly] {
        background: #EFE7DE;
        color: var(--acct-maroon);
        cursor: not-allowed;
    }

    .ad-account-page .ad-btn-primary {
        background: var(--acct-maroon) !important;
        border-color: var(--acct-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ad-account-page .ad-btn-primary:hover {
        background: var(--acct-maroon-dark) !important;
        border-color: var(--acct-maroon-dark) !important;
    }

    .ad-account-page .ad-btn-secondary {
        background: var(--acct-card) !important;
        border-color: var(--acct-tan) !important;
        color: var(--acct-maroon) !important;
    }

    .ad-account-page .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--acct-maroon) !important;
    }

    .ad-account-page .ad-btn-danger-soft {
        background: var(--acct-danger-soft) !important;
        border-color: #F0C9C4 !important;
        color: var(--acct-danger) !important;
    }

    .ad-account-page .ad-btn-danger-soft:hover {
        background: #F7DCD7 !important;
        border-color: var(--acct-danger) !important;
    }

    .ad-account-page .ad-inline-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ad-account-page .ad-setting-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;

        padding: 17px 0;

        border-bottom: 1px solid var(--acct-border);
    }

    .ad-account-page .ad-setting-row:last-child {
        border-bottom: 0;
    }

    .ad-account-page .ad-setting-row strong {
        display: block;

        color: var(--acct-text);

        font-size: 13px;
        font-weight: 950;
        letter-spacing: -0.025em;
    }

    .ad-account-page .ad-setting-row p {
        margin: 5px 0 0;

        color: var(--acct-muted);

        font-size: 11px;
        line-height: 1.6;
    }

    .ad-account-page .ad-switch {
        position: relative;

        display: inline-flex;
        width: 46px;
        height: 26px;
        flex: 0 0 46px;
    }

    .ad-account-page .ad-switch input {
        position: absolute;
        opacity: 0;
    }

    .ad-account-page .ad-switch span {
        position: absolute;
        inset: 0;

        border: 1px solid var(--acct-border-strong);
        border-radius: 999px;

        background: #E7D9CB;

        cursor: pointer;
        transition: 180ms ease;
    }

    .ad-account-page .ad-switch span::after {
        position: absolute;
        top: 3px;
        left: 3px;

        width: 18px;
        height: 18px;

        border-radius: 999px;

        background: #FFFFFF;
        box-shadow: 0 2px 7px rgba(86, 28, 23, 0.18);

        content: "";
        transition: 180ms ease;
    }

    .ad-account-page .ad-switch input:checked + span {
        border-color: var(--acct-maroon);
        background: var(--acct-maroon);
    }

    .ad-account-page .ad-switch input:checked + span::after {
        transform: translateX(20px);
    }

    .ad-account-page .ad-notification-list {
        display: grid;
        gap: 0;

        overflow: hidden;

        border: 1px solid var(--acct-border);
        border-radius: 20px;

        background: var(--acct-card);
    }

    .ad-account-page .ad-notification-list .ad-setting-row {
        padding: 17px 18px;
        background:
            radial-gradient(circle at 96% 8%, rgba(193, 151, 113, 0.09), transparent 28%),
            #FFFDF9;
    }

    .ad-account-page .ad-notification-list .ad-setting-row + .ad-setting-row {
        border-top: 1px solid var(--acct-border);
    }

    @media (max-width: 980px) {
        .ad-account-layout {
            grid-template-columns: 1fr;
        }

        .ad-account-nav {
            display: flex;
            overflow-x: auto;
        }

        .ad-account-nav a {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .ad-account-form-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .ad-account-page .ad-page-head {
            padding: 26px 22px;
        }

        .ad-account-section {
            padding: 20px;
        }

        .ad-account-profile-card,
        .ad-account-page .ad-setting-row {
            align-items: flex-start;
            flex-direction: column;
        }

        .ad-account-page .ad-btn,
        .ad-account-page .ad-inline-actions {
            width: 100%;
        }
    }

    html.dark .ad-account-page .ad-page-head,
    html.dark .ad-account-nav,
    html.dark .ad-account-panel,
    html.dark .ad-account-profile-card,
    html.dark .ad-account-page .ad-notification-list,
    html.dark .ad-account-page .ad-notification-list .ad-setting-row {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-account-page .ad-page-head h2,
    html.dark .ad-account-section h2,
    html.dark .ad-account-profile-copy strong,
    html.dark .ad-account-page .ad-setting-row strong {
        color: #F5EFE8 !important;
    }

    html.dark .ad-account-page .ad-page-head p,
    html.dark .ad-account-section > p,
    html.dark .ad-account-profile-copy span,
    html.dark .ad-account-page .ad-setting-row p {
        color: #C8B7AD !important;
    }

    html.dark .ad-account-page .ad-overline {
        color: #EBA99D !important;
    }

    html.dark .ad-account-nav a {
        color: #C8B7AD !important;
    }

    html.dark .ad-account-nav a:hover {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-account-nav a.is-active {
        background: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .ad-account-page .ad-field input,
    html.dark .ad-account-page .ad-field select,
    html.dark .ad-account-page .ad-field textarea {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-account-page .ad-setting-row,
    html.dark .ad-account-page .ad-notification-list .ad-setting-row + .ad-setting-row {
        border-color: #3B2E27 !important;
    }

    html.dark .ad-account-avatar {
        background: #8A3A2F !important;
        color: #FFFFFF !important;
    }
</style>

<div class="ad-page ad-account-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">
                Administrator account
            </span>

            <h2>
                {{ $tabs[$tab] }}
            </h2>

            <p>
                Protect privileged access and keep administrative contact information current.
            </p>
        </div>
    </div>

    <section class="ad-account-layout">
        <nav class="ad-card ad-account-nav" aria-label="Account sections">
            @foreach($tabs as $key => $label)
                <a
                    class="{{ $tab === $key ? 'is-active' : '' }}"
                    href="{{ route('admin.account', ['tab' => $key]) }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="ad-card ad-account-panel">
            @if($tab === 'security')
                <form
                    class="ad-account-section"
                    data-demo-form
                    data-success-message="Security settings updated"
                >
                    <h2>
                        Security
                    </h2>

                    <p>
                        Use a unique password and enable additional account protection for privileged admin access.
                    </p>

                    <div class="ad-account-form-grid">
                        <label class="ad-field is-full">
                            <span>Current password</span>
                            <input type="password" placeholder="Enter current password">
                        </label>

                        <label class="ad-field">
                            <span>New password</span>
                            <input type="password" placeholder="At least 12 characters">
                        </label>

                        <label class="ad-field">
                            <span>Confirm new password</span>
                            <input type="password" placeholder="Repeat new password">
                        </label>
                    </div>

                    <div>
                        <div class="ad-setting-row">
                            <div>
                                <strong>Two-factor authentication</strong>
                                <p>Require a verification code when signing in.</p>
                            </div>

                            <label class="ad-switch">
                                <input type="checkbox" checked>
                                <span></span>
                            </label>
                        </div>

                        <div class="ad-setting-row">
                            <div>
                                <strong>Sign-in alerts</strong>
                                <p>Notify you about new devices and unusual sessions.</p>
                            </div>

                            <label class="ad-switch">
                                <input type="checkbox" checked>
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="ad-inline-actions">
                        <button
                            class="ad-btn ad-btn-primary"
                            type="submit"
                        >
                            Update security
                        </button>

                        <button
                            class="ad-btn ad-btn-danger-soft"
                            type="button"
                            data-confirm-action
                            data-confirm-title="Sign out other sessions?"
                            data-confirm-message="All sessions except this browser will be revoked."
                            data-success-message="Other sessions signed out"
                        >
                            Sign out other sessions
                        </button>
                    </div>
                </form>
            @elseif($tab === 'notifications')
                <form
                    class="ad-account-section"
                    data-demo-form
                    data-success-message="Notification preferences saved"
                >
                    <h2>
                        Notification settings
                    </h2>

                    <p>
                        Choose the operational events that should interrupt your work.
                    </p>

                    <div class="ad-notification-list">
                        @foreach($notificationSettings as $setting)
                            <div class="ad-setting-row">
                                <div>
                                    <strong>
                                        {{ $setting['name'] }}
                                    </strong>

                                    <p>
                                        {{ $setting['description'] }}
                                    </p>
                                </div>

                                <label class="ad-switch">
                                    <input type="checkbox" @checked($setting['checked'])>
                                    <span></span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <button
                        class="ad-btn ad-btn-primary"
                        type="submit"
                    >
                        Save preferences
                    </button>
                </form>
            @else
                <form
                    class="ad-account-section"
                    data-demo-form
                    data-success-message="Administrator profile saved"
                >
                    <div class="ad-account-profile-card">
                        <span class="ad-account-avatar">
                            {{ mb_strtoupper(mb_substr(data_get($admin, 'name', 'Admin User'), 0, 1)) }}
                        </span>

                        <div class="ad-account-profile-copy">
                            <strong>
                                {{ data_get($admin, 'name', 'Admin User') }}
                            </strong>

                            <span>
                                Platform Administrator · {{ data_get($admin, 'email', 'admin@likhae.ph') }}
                            </span>
                        </div>
                    </div>

                    <h2>
                        Profile information
                    </h2>

                    <p>
                        This information is visible in audit trails, case decisions, and internal assignments.
                    </p>

                    <div class="ad-account-form-grid">
                        <label class="ad-field">
                            <span>Full name</span>
                            <input value="{{ data_get($admin, 'name', 'Admin User') }}">
                        </label>

                        <label class="ad-field">
                            <span>Email address</span>
                            <input type="email" value="{{ data_get($admin, 'email', 'admin@likhae.ph') }}">
                        </label>

                        <label class="ad-field">
                            <span>Role</span>
                            <input value="Platform Administrator" readonly>
                        </label>

                        <label class="ad-field">
                            <span>Contact number</span>
                            <input value="+63 917 000 0000">
                        </label>

                        <label class="ad-field is-full">
                            <span>Administrative note</span>
                            <textarea rows="4">Marketplace operations and trust & safety.</textarea>
                        </label>
                    </div>

                    <button
                        class="ad-btn ad-btn-primary"
                        type="submit"
                    >
                        Save profile
                    </button>
                </form>
            @endif
        </div>
    </section>
</div>
@endsection