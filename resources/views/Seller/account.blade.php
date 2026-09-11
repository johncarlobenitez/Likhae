@extends('layouts.seller')

@section('title', 'Account')
@section('active', 'account')
@section('subtitle', 'Manage your seller identity, business records, security, and preferences.')

@php
    $tabs = [
        'profile' => 'Profile',
        'business' => 'Business Information',
        'verification' => 'Verification',
        'security' => 'Security',
        'notifications' => 'Notification Settings',
    ];

    $requestedTab = $tab ?? request('tab', 'profile');

    $currentTab = array_key_exists($requestedTab, $tabs)
        ? $requestedTab
        : 'profile';

    $seller = auth()->user();

    $sellerName = data_get($seller, 'name', 'Mariel Santos');
    $sellerEmail = data_get($seller, 'email', 'mariel@likhaestudio.ph');

    $initials = collect(explode(' ', $sellerName))
        ->filter()
        ->take(2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('') ?: 'MS';

    $documents = [
        ['title' => 'Valid Government ID', 'name' => 'Philippine Passport', 'verified' => 'Verified Aug 12, 2026'],
        ['title' => 'Business Permit', 'name' => 'Santa Cruz Business Permit 2026', 'verified' => 'Verified Aug 12, 2026'],
        ['title' => 'DTI Registration', 'name' => 'LIKHAE Studio Trading', 'verified' => 'Verified Aug 11, 2026'],
    ];

    $notificationSettings = [
        ['name' => 'New order', 'description' => 'Receive an alert when a buyer places an order.', 'email' => true, 'push' => true, 'sms' => true],
        ['name' => 'Order cancellation', 'description' => 'Know when an order is cancelled.', 'email' => true, 'push' => true, 'sms' => false],
        ['name' => 'Pickup and shipping', 'description' => 'Courier assignment and shipment status changes.', 'email' => true, 'push' => true, 'sms' => true],
        ['name' => 'Inventory alerts', 'description' => 'Low-stock and out-of-stock reminders.', 'email' => true, 'push' => true, 'sms' => false],
        ['name' => 'Buyer messages', 'description' => 'New chat messages and support concerns.', 'email' => false, 'push' => true, 'sms' => false],
        ['name' => 'Finance and payouts', 'description' => 'Balance updates and payout confirmations.', 'email' => true, 'push' => true, 'sms' => true],
        ['name' => 'Marketing updates', 'description' => 'Campaign invitations and promotional tips.', 'email' => true, 'push' => false, 'sms' => false],
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

    .sl-account-page {
        color: var(--acct-text);

        --sl-blue: var(--acct-maroon);
        --sl-blue-dark: var(--acct-maroon-dark);
        --sl-blue-soft: #F3E4DE;
        --sl-indigo: var(--acct-tan);
    }

    .sl-account-page .sl-page-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;

        min-height: auto;
        padding: 32px 36px;

        border: 1px solid var(--acct-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--acct-shadow-soft);
    }

    .sl-account-page .sl-eyebrow {
        color: var(--acct-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .sl-account-page .sl-page-toolbar h2 {
        margin-top: 10px;

        color: var(--acct-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .sl-account-page .sl-page-toolbar p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--acct-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .sl-account-page .sl-account-status {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        gap: 8px;

        padding: 0 13px;

        border: 1px solid #CFE8DA;
        border-radius: 999px;

        background: var(--acct-success-soft);
        color: var(--acct-success);

        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
    }

    .sl-account-page .sl-account-status i {
        width: 8px;
        height: 8px;

        border-radius: 999px;

        background: var(--acct-success);
    }

    .sl-account-page .sl-account-layout {
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .sl-account-page .sl-account-nav {
        position: sticky;
        top: 92px;

        overflow: hidden;
        padding: 0;

        border: 1px solid var(--acct-border) !important;
        border-radius: 24px !important;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.14), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%) !important;

        box-shadow: var(--acct-shadow-soft) !important;
    }

    .sl-account-page .sl-account-person {
        display: flex;
        align-items: center;
        gap: 12px;

        padding: 18px;

        border-bottom: 1px solid var(--acct-border);
    }

    .sl-account-page .sl-avatar {
        display: grid;
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        place-items: center;

        border-radius: 15px;

        background: var(--acct-maroon) !important;
        color: #FFFFFF !important;

        font-size: 13px;
        font-weight: 950;
    }

    .sl-account-page .sl-account-person strong {
        display: block;

        color: var(--acct-text);

        font-size: 13px;
        font-weight: 950;
    }

    .sl-account-page .sl-account-person small {
        display: block;
        margin-top: 3px;

        color: var(--acct-muted);

        font-size: 10px;
        font-weight: 700;
    }

    .sl-account-page .sl-account-nav nav {
        display: grid;
        gap: 5px;

        padding: 10px;
    }

    .sl-account-page .sl-account-nav nav a {
        display: grid;
        grid-template-columns: 26px minmax(0, 1fr) auto;
        min-height: 44px;
        align-items: center;
        gap: 9px;

        padding: 0 12px;

        border-radius: 14px;

        color: var(--acct-brown);
        text-decoration: none;

        font-size: 11px;
        font-weight: 850;

        transition: 160ms ease;
    }

    .sl-account-page .sl-account-nav nav a:hover {
        background: var(--acct-bg-soft);
        color: var(--acct-maroon);
    }

    .sl-account-page .sl-account-nav nav a.is-active {
        background: var(--acct-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .sl-account-page .sl-account-nav nav a > span {
        display: grid;
        width: 24px;
        height: 24px;
        place-items: center;

        border-radius: 9px;

        background: #F1E4D7;
        color: var(--acct-maroon);

        font-size: 9px;
        font-weight: 950;
    }

    .sl-account-page .sl-account-nav nav a.is-active > span {
        background: rgba(255, 255, 255, 0.18);
        color: #FFFFFF;
    }

    .sl-account-page .sl-account-nav nav i {
        color: inherit;
        font-size: 17px;
        font-style: normal;
        opacity: 0.7;
    }

    .sl-account-page .sl-account-content {
        min-width: 0;
    }

    .sl-account-page .sl-form-section,
    .sl-account-page .sl-verification-card {
        overflow: hidden;

        border: 1px solid var(--acct-border) !important;
        border-radius: 24px !important;

        background: var(--acct-card) !important;
        color: var(--acct-text) !important;

        box-shadow: var(--acct-shadow-soft) !important;
    }

    .sl-account-page .sl-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding: 21px 24px;

        border-bottom: 1px solid var(--acct-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .sl-account-page .sl-card-head h2 {
        margin: 0;

        color: var(--acct-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 34px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-account-page .sl-card-head p {
        margin-top: 8px;

        color: var(--acct-muted) !important;

        font-size: 12px;
        line-height: 1.65;
    }

    .sl-account-page .sl-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;

        padding: 22px;
    }

    .sl-account-page .sl-span-2 {
        grid-column: 1 / -1;
    }

    .sl-account-page .sl-field {
        display: grid;
        gap: 7px;
    }

    .sl-account-page .sl-field > span {
        color: var(--acct-muted) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sl-account-page .sl-field input,
    .sl-account-page .sl-field select,
    .sl-account-page .sl-field textarea {
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

    .sl-account-page .sl-field textarea {
        min-height: 130px;
        padding-block: 12px;

        line-height: 1.65;
        resize: vertical;
    }

    .sl-account-page .sl-field input:focus,
    .sl-account-page .sl-field select:focus,
    .sl-account-page .sl-field textarea:focus {
        border-color: var(--acct-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .sl-account-page .sl-field input:disabled,
    .sl-account-page .sl-field input[readonly] {
        background: #EFE7DE;
        color: var(--acct-maroon);
        cursor: not-allowed;
    }

    .sl-account-page .sl-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;

        padding: 0 22px 22px;
    }

    .sl-account-page .sl-btn {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 8px;

        padding: 0 16px;

        border: 1px solid transparent;
        border-radius: 14px;

        font-size: 12px;
        font-weight: 900;
        text-decoration: none;

        transition: 160ms ease;
    }

    .sl-account-page .sl-btn:hover {
        transform: translateY(-1px);
    }

    .sl-account-page .sl-btn-primary {
        background: var(--acct-maroon) !important;
        border-color: var(--acct-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .sl-account-page .sl-btn-primary:hover {
        background: var(--acct-maroon-dark) !important;
        border-color: var(--acct-maroon-dark) !important;
    }

    .sl-account-page .sl-btn-ghost,
    .sl-account-page .sl-btn-soft {
        background: var(--acct-card) !important;
        border-color: var(--acct-tan) !important;
        color: var(--acct-maroon) !important;
    }

    .sl-account-page .sl-btn-ghost:hover,
    .sl-account-page .sl-btn-soft:hover {
        background: #F3E4DE !important;
        border-color: var(--acct-maroon) !important;
    }

    .sl-account-page .sl-btn-sm {
        min-height: 34px;
        padding-inline: 12px;
        border-radius: 11px;

        font-size: 11px;
    }

    .sl-account-page .sl-profile-upload {
        display: flex;
        align-items: center;
        gap: 14px;

        margin: 22px 22px 0;
        padding: 16px;

        border: 1px solid var(--acct-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.16), transparent 28%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 100%);
    }

    .sl-account-page .sl-profile-upload .sl-avatar {
        width: 58px;
        height: 58px;
        flex-basis: 58px;

        border-radius: 18px;

        font-size: 17px;
    }

    .sl-account-page .sl-profile-upload strong {
        color: var(--acct-text);

        font-size: 13px;
        font-weight: 950;
    }

    .sl-account-page .sl-profile-upload p {
        margin: 4px 0 10px;

        color: var(--acct-muted);

        font-size: 11px;
    }

    .sl-account-page .sl-status {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        justify-content: center;

        padding: 0 10px;

        border: 1px solid transparent;
        border-radius: 999px;

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .sl-account-page .sl-status.is-success,
    .sl-account-page .sl-status.is-active {
        background: var(--acct-success-soft);
        border-color: #CFE8DA;
        color: var(--acct-success);
    }

    .sl-account-page .sl-verification-banner {
        display: flex;
        gap: 13px;

        margin: 22px;
        padding: 17px;

        border: 1px solid #CFE8DA;
        border-radius: 18px;

        background: var(--acct-success-soft);
    }

    .sl-account-page .sl-verification-banner > span {
        display: grid;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        place-items: center;

        border-radius: 14px;

        background: var(--acct-success);
        color: #FFFFFF;

        font-size: 14px;
        font-weight: 950;
    }

    .sl-account-page .sl-verification-banner strong {
        color: var(--acct-text);

        font-size: 13px;
        font-weight: 950;
    }

    .sl-account-page .sl-verification-banner p {
        margin: 4px 0 0;

        color: var(--acct-muted);

        font-size: 11px;
        line-height: 1.6;
    }

    .sl-account-page .sl-document-list {
        display: grid;

        margin: 0 22px 22px;

        border: 1px solid var(--acct-border);
        border-radius: 18px;

        background: var(--acct-card);

        overflow: hidden;
    }

    .sl-account-page .sl-document-list article {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto auto;
        align-items: center;
        gap: 12px;

        min-height: 72px;
        padding: 14px 16px;

        border-bottom: 1px solid var(--acct-border);

        background:
            radial-gradient(circle at 96% 8%, rgba(193, 151, 113, 0.09), transparent 28%),
            #FFFDF9;
    }

    .sl-account-page .sl-document-list article:last-child {
        border-bottom: 0;
    }

    .sl-account-page .sl-file-icon,
    .sl-account-page .sl-security-icon {
        display: grid;
        width: 40px;
        height: 40px;
        place-items: center;

        border-radius: 14px;

        background: #F1E4D7;
        color: var(--acct-maroon);

        font-size: 10px;
        font-weight: 950;
    }

    .sl-account-page .sl-document-list strong,
    .sl-account-page .sl-security-list strong {
        color: var(--acct-text);

        font-size: 12px;
        font-weight: 950;
    }

    .sl-account-page .sl-document-list small,
    .sl-account-page .sl-security-list p {
        margin-top: 3px;

        color: var(--acct-muted);

        font-size: 10px;
        line-height: 1.5;
    }

    .sl-account-page .sl-help-card {
        display: flex;
        align-items: center;
        gap: 13px;

        margin: 0 22px 22px;
        padding: 16px;

        border: 1px solid var(--acct-border);
        border-radius: 18px;

        background: var(--acct-bg-soft);
    }

    .sl-account-page .sl-help-card > span {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;

        border-radius: 13px;

        background: var(--acct-maroon);
        color: #FFFFFF;

        font-size: 13px;
        font-weight: 950;
    }

    .sl-account-page .sl-help-card div {
        min-width: 0;
        flex: 1;
    }

    .sl-account-page .sl-help-card strong {
        color: var(--acct-text);

        font-size: 12px;
        font-weight: 950;
    }

    .sl-account-page .sl-help-card p {
        margin: 4px 0 0;

        color: var(--acct-muted);

        font-size: 11px;
        line-height: 1.5;
    }

    .sl-account-page .sl-security-score {
        display: grid;
        grid-template-columns: minmax(160px, 220px) minmax(0, 1fr);
        align-items: center;
        gap: 16px;

        margin: 22px;
        padding: 16px;

        border: 1px solid var(--acct-border);
        border-radius: 18px;

        background: var(--acct-bg-soft);
    }

    .sl-account-page .sl-security-score > span {
        display: block;
        height: 10px;
        overflow: hidden;

        border-radius: 999px;

        background: #E7D9CB;
    }

    .sl-account-page .sl-security-score i {
        display: block;
        height: 100%;

        border-radius: inherit;

        background: var(--acct-maroon);
    }

    .sl-account-page .sl-security-score strong {
        color: var(--acct-text);

        font-size: 13px;
        font-weight: 950;
    }

    .sl-account-page .sl-security-score small {
        color: var(--acct-muted);

        font-size: 10px;
    }

    .sl-account-page .sl-security-list {
        display: grid;

        border-top: 1px solid var(--acct-border);
    }

    .sl-account-page .sl-security-list article {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 13px;

        padding: 15px 22px;

        border-bottom: 1px solid var(--acct-border);
    }

    .sl-account-page .sl-security-list article:last-child {
        border-bottom: 0;
    }

    .sl-account-page .sl-switch {
        position: relative;

        display: inline-flex;
        width: 46px;
        height: 26px;
        flex: 0 0 46px;
    }

    .sl-account-page .sl-switch input {
        position: absolute;
        opacity: 0;
    }

    .sl-account-page .sl-switch span {
        position: absolute;
        inset: 0;

        border: 1px solid var(--acct-border-strong);
        border-radius: 999px;

        background: #E7D9CB;

        cursor: pointer;
        transition: 180ms ease;
    }

    .sl-account-page .sl-switch span::after {
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

    .sl-account-page .sl-switch input:checked + span {
        border-color: var(--acct-maroon);
        background: var(--acct-maroon);
    }

    .sl-account-page .sl-switch input:checked + span::after {
        transform: translateX(20px);
    }

    .sl-account-page .sl-notification-settings {
        overflow: hidden;

        margin: 22px;

        border: 1px solid var(--acct-border);
        border-radius: 18px;

        background: var(--acct-card);
    }

    .sl-account-page .sl-notification-setting-head,
    .sl-account-page .sl-notification-setting {
        display: grid;
        grid-template-columns: minmax(0, 1fr) repeat(3, 80px);
        align-items: center;
        gap: 10px;

        padding: 13px 16px;

        border-bottom: 1px solid var(--acct-border);
    }

    .sl-account-page .sl-notification-setting-head {
        background: var(--acct-bg-soft);
        color: var(--acct-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: center;
        text-transform: uppercase;
    }

    .sl-account-page .sl-notification-setting-head span:first-child {
        text-align: left;
    }

    .sl-account-page .sl-notification-setting:last-child {
        border-bottom: 0;
    }

    .sl-account-page .sl-notification-setting strong {
        color: var(--acct-text);

        font-size: 12px;
        font-weight: 950;
    }

    .sl-account-page .sl-notification-setting small {
        display: block;
        margin-top: 3px;

        color: var(--acct-muted);

        font-size: 10px;
        line-height: 1.45;
    }

    .sl-account-page .sl-check {
        display: grid;
        width: 24px;
        height: 24px;
        place-items: center;
        justify-self: center;

        cursor: pointer;
    }

    .sl-account-page .sl-check input {
        position: absolute;
        opacity: 0;
    }

    .sl-account-page .sl-check span {
        display: grid;
        width: 22px;
        height: 22px;
        place-items: center;

        border: 1px solid var(--acct-border-strong);
        border-radius: 8px;

        background: #FFFFFF;
        color: transparent;

        font-size: 11px;
        font-weight: 950;

        transition: 160ms ease;
    }

    .sl-account-page .sl-check input:checked + span {
        border-color: var(--acct-maroon);
        background: var(--acct-maroon);
        color: #FFFFFF;
    }

    .sl-account-modal .sl-modal-dialog {
        overflow: hidden;

        border: 1px solid var(--acct-border);
        border-radius: 24px;

        background: var(--acct-card);
        color: var(--acct-text);

        box-shadow: 0 24px 70px rgba(86, 28, 23, 0.22);
    }

    .sl-account-modal .sl-modal-dialog > header {
        border-bottom-color: var(--acct-border);

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .sl-account-modal .sl-modal-dialog h2 {
        color: var(--acct-text);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 32px;
        font-weight: 400;
        letter-spacing: -0.045em;
    }

    .sl-account-modal .sl-icon-btn {
        background: var(--acct-bg-soft);
        border-color: var(--acct-border);
        color: var(--acct-maroon);
    }

    .sl-account-modal footer {
        border-top-color: var(--acct-border) !important;
        background: var(--acct-card);
    }

    @media (max-width: 1100px) {
        .sl-account-page .sl-account-layout {
            grid-template-columns: 1fr;
        }

        .sl-account-page .sl-account-nav {
            position: static;
        }

        .sl-account-page .sl-account-nav nav {
            display: flex;
            overflow-x: auto;
        }

        .sl-account-page .sl-account-nav nav a {
            min-width: max-content;
        }
    }

    @media (max-width: 760px) {
        .sl-account-page .sl-page-toolbar {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .sl-account-page .sl-form-grid,
        .sl-account-page .sl-notification-setting-head,
        .sl-account-page .sl-notification-setting,
        .sl-account-page .sl-security-score {
            grid-template-columns: 1fr;
        }

        .sl-account-page .sl-span-2 {
            grid-column: auto;
        }

        .sl-account-page .sl-document-list article,
        .sl-account-page .sl-security-list article {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .sl-account-page .sl-document-list article .sl-status,
        .sl-account-page .sl-document-list article .sl-btn,
        .sl-account-page .sl-security-list article .sl-btn,
        .sl-account-page .sl-security-list article .sl-switch {
            grid-column: 1 / -1;
            justify-self: start;
        }

        .sl-account-page .sl-help-card,
        .sl-account-page .sl-profile-upload {
            align-items: flex-start;
            flex-direction: column;
        }

        .sl-account-page .sl-btn,
        .sl-account-page .sl-form-actions {
            width: 100%;
        }

        .sl-account-page .sl-check {
            justify-self: start;
        }
    }

    html.dark .sl-account-page .sl-page-toolbar,
    html.dark .sl-account-page .sl-account-nav,
    html.dark .sl-account-page .sl-form-section,
    html.dark .sl-account-page .sl-verification-card,
    html.dark .sl-account-page .sl-profile-upload,
    html.dark .sl-account-page .sl-card-head,
    html.dark .sl-account-page .sl-document-list,
    html.dark .sl-account-page .sl-document-list article,
    html.dark .sl-account-page .sl-help-card,
    html.dark .sl-account-page .sl-security-score,
    html.dark .sl-account-page .sl-notification-settings {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;

        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .sl-account-page .sl-page-toolbar h2,
    html.dark .sl-account-page .sl-card-head h2,
    html.dark .sl-account-page .sl-account-person strong,
    html.dark .sl-account-page .sl-profile-upload strong,
    html.dark .sl-account-page .sl-document-list strong,
    html.dark .sl-account-page .sl-help-card strong,
    html.dark .sl-account-page .sl-security-score strong,
    html.dark .sl-account-page .sl-security-list strong,
    html.dark .sl-account-page .sl-notification-setting strong {
        color: #F5EFE8 !important;
    }

    html.dark .sl-account-page .sl-page-toolbar p,
    html.dark .sl-account-page .sl-card-head p,
    html.dark .sl-account-page .sl-account-person small,
    html.dark .sl-account-page .sl-profile-upload p,
    html.dark .sl-account-page .sl-document-list small,
    html.dark .sl-account-page .sl-help-card p,
    html.dark .sl-account-page .sl-security-score small,
    html.dark .sl-account-page .sl-security-list p,
    html.dark .sl-account-page .sl-notification-setting small {
        color: #C8B7AD !important;
    }

    html.dark .sl-account-page .sl-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .sl-account-page .sl-account-nav nav a {
        color: #C8B7AD !important;
    }

    html.dark .sl-account-page .sl-account-nav nav a:hover,
    html.dark .sl-account-page .sl-account-nav nav a.is-active {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-account-page .sl-field input,
    html.dark .sl-account-page .sl-field select,
    html.dark .sl-account-page .sl-field textarea {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .sl-account-page .sl-avatar,
    html.dark .sl-account-page .sl-switch input:checked + span,
    html.dark .sl-account-page .sl-check input:checked + span {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }
</style>

<div class="sl-page sl-account-page">
    <div class="sl-page-toolbar">
        <div>
            <span class="sl-eyebrow">
                Account Management
            </span>

            <h2>
                Seller Account
            </h2>

            <p>
                Keep your personal and business information secure and current.
            </p>
        </div>

        <span class="sl-account-status">
            <i></i>
            Account in good standing
        </span>
    </div>

    <div class="sl-account-layout">
        <aside class="sl-card sl-account-nav">
            <div class="sl-account-person">
                <span class="sl-avatar">
                    {{ $initials }}
                </span>

                <div>
                    <strong>
                        {{ $sellerName }}
                    </strong>

                    <small>
                        Seller ID: SLR-20481
                    </small>
                </div>
            </div>

            <nav aria-label="Seller account sections">
                @foreach ($tabs as $key => $label)
                    <a
                        href="{{ route('seller.account', ['tab' => $key]) }}"
                        class="{{ $currentTab === $key ? 'is-active' : '' }}"
                    >
                        <span>
                            {{ $loop->iteration }}
                        </span>

                        {{ $label }}

                        <i aria-hidden="true">
                            ›
                        </i>
                    </a>
                @endforeach
            </nav>
        </aside>

        <main class="sl-account-content">
            @if ($currentTab === 'business')
                <form
                    class="sl-card sl-form-section"
                    data-demo-form
                    data-success="Business information updated."
                >
                    <header class="sl-card-head">
                        <div>
                            <h2>
                                Business Information
                            </h2>

                            <p>
                                Legal information used for compliance and payouts.
                            </p>
                        </div>
                    </header>

                    <div class="sl-form-grid">
                        <label class="sl-field">
                            <span>Registered business name</span>
                            <input value="LIKHAE Studio Trading">
                        </label>

                        <label class="sl-field">
                            <span>Business type</span>
                            <select>
                                <option>Sole Proprietorship</option>
                                <option>Partnership</option>
                                <option>Corporation</option>
                            </select>
                        </label>

                        <label class="sl-field">
                            <span>DTI/SEC registration number</span>
                            <input value="DTI-2026-104829">
                        </label>

                        <label class="sl-field">
                            <span>TIN</span>
                            <input value="***-***-482-000">
                        </label>

                        <label class="sl-field sl-span-2">
                            <span>Registered business address</span>
                            <input value="24 Rizal Street, Santa Cruz, Laguna 4009">
                        </label>

                        <label class="sl-field">
                            <span>Authorized representative</span>
                            <input value="Mariel Santos">
                        </label>

                        <label class="sl-field">
                            <span>Business contact</span>
                            <input value="0917 000 2481">
                        </label>
                    </div>

                    <div class="sl-form-actions">
                        <button type="submit" class="sl-btn sl-btn-primary">
                            Save Business Information
                        </button>
                    </div>
                </form>
            @elseif ($currentTab === 'verification')
                <section class="sl-card sl-verification-card">
                    <header class="sl-card-head">
                        <div>
                            <h2>
                                Seller Verification
                            </h2>

                            <p>
                                Your documents help maintain a safe marketplace.
                            </p>
                        </div>

                        <span class="sl-status is-success">
                            Verified
                        </span>
                    </header>

                    <div class="sl-verification-banner">
                        <span>
                            ✓
                        </span>

                        <div>
                            <strong>
                                Your seller account is verified
                            </strong>

                            <p>
                                Verification completed on August 12, 2026. No action is currently required.
                            </p>
                        </div>
                    </div>

                    <div class="sl-document-list">
                        @foreach ($documents as $doc)
                            <article>
                                <span class="sl-file-icon">
                                    PDF
                                </span>

                                <div>
                                    <strong>
                                        {{ $doc['title'] }}
                                    </strong>

                                    <small>
                                        {{ $doc['name'] }} · {{ $doc['verified'] }}
                                    </small>
                                </div>

                                <span class="sl-status is-success">
                                    Verified
                                </span>

                                <button
                                    type="button"
                                    class="sl-btn sl-btn-ghost sl-btn-sm"
                                    data-demo-action="Document viewer opened."
                                >
                                    View
                                </button>
                            </article>
                        @endforeach
                    </div>

                    <div class="sl-help-card">
                        <span>
                            i
                        </span>

                        <div>
                            <strong>
                                Need to update a document?
                            </strong>

                            <p>
                                Contact Seller Support. Re-verification may temporarily limit selected account changes.
                            </p>
                        </div>

                        <a href="{{ route('seller.messages') }}" class="sl-btn sl-btn-soft sl-btn-sm">
                            Contact Support
                        </a>
                    </div>
                </section>
            @elseif ($currentTab === 'security')
                <section class="sl-card sl-form-section">
                    <header class="sl-card-head">
                        <div>
                            <h2>
                                Security
                            </h2>

                            <p>
                                Protect access to your Seller Center.
                            </p>
                        </div>
                    </header>

                    <div class="sl-security-score">
                        <span>
                            <i style="width: 82%"></i>
                        </span>

                        <div>
                            <strong>
                                Security score: Strong
                            </strong>

                            <small>
                                Enable all recommendations for maximum protection.
                            </small>
                        </div>
                    </div>

                    <div class="sl-security-list">
                        <article>
                            <span class="sl-security-icon">••</span>

                            <div>
                                <strong>Password</strong>
                                <p>Last changed 42 days ago</p>
                            </div>

                            <button
                                type="button"
                                class="sl-btn sl-btn-ghost sl-btn-sm"
                                data-modal-open="passwordModal"
                            >
                                Change
                            </button>
                        </article>

                        <article>
                            <span class="sl-security-icon">2F</span>

                            <div>
                                <strong>Two-factor authentication</strong>
                                <p>Receive a code when signing in on a new device.</p>
                            </div>

                            <label class="sl-switch">
                                <input type="checkbox" checked>
                                <span></span>
                            </label>
                        </article>

                        <article>
                            <span class="sl-security-icon">DV</span>

                            <div>
                                <strong>Trusted devices</strong>
                                <p>3 devices currently have access.</p>
                            </div>

                            <button
                                type="button"
                                class="sl-btn sl-btn-ghost sl-btn-sm"
                                data-demo-action="Trusted devices opened."
                            >
                                Manage
                            </button>
                        </article>

                        <article>
                            <span class="sl-security-icon">LG</span>

                            <div>
                                <strong>Login activity</strong>
                                <p>Last sign-in: Today, 8:42 AM · Santa Cruz, Laguna.</p>
                            </div>

                            <button
                                type="button"
                                class="sl-btn sl-btn-ghost sl-btn-sm"
                                data-demo-action="Login activity opened."
                            >
                                Review
                            </button>
                        </article>
                    </div>
                </section>
            @elseif ($currentTab === 'notifications')
                <form
                    class="sl-card sl-form-section"
                    data-demo-form
                    data-success="Notification preferences saved."
                >
                    <header class="sl-card-head">
                        <div>
                            <h2>
                                Notification Settings
                            </h2>

                            <p>
                                Choose which Seller Center alerts you want to receive.
                            </p>
                        </div>
                    </header>

                    <div class="sl-notification-settings">
                        <div class="sl-notification-setting-head">
                            <span>Notification Type</span>
                            <span>Email</span>
                            <span>Push</span>
                            <span>SMS</span>
                        </div>

                        @foreach ($notificationSettings as $setting)
                            <div class="sl-notification-setting">
                                <div>
                                    <strong>
                                        {{ $setting['name'] }}
                                    </strong>

                                    <small>
                                        {{ $setting['description'] }}
                                    </small>
                                </div>

                                <label class="sl-check">
                                    <input type="checkbox" @checked($setting['email'])>
                                    <span>✓</span>
                                </label>

                                <label class="sl-check">
                                    <input type="checkbox" @checked($setting['push'])>
                                    <span>✓</span>
                                </label>

                                <label class="sl-check">
                                    <input type="checkbox" @checked($setting['sms'])>
                                    <span>✓</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="sl-form-actions">
                        <button type="submit" class="sl-btn sl-btn-primary">
                            Save Preferences
                        </button>
                    </div>
                </form>
            @else
                <form
                    class="sl-card sl-form-section"
                    data-demo-form
                    data-success="Profile updated."
                >
                    <header class="sl-card-head">
                        <div>
                            <h2>
                                Profile Information
                            </h2>

                            <p>
                                Your private account identity and contact information.
                            </p>
                        </div>
                    </header>

                    <div class="sl-profile-upload">
                        <span class="sl-avatar">
                            {{ $initials }}
                        </span>

                        <div>
                            <strong>
                                Profile photo
                            </strong>

                            <p>
                                JPG or PNG · maximum 2 MB
                            </p>

                            <button
                                type="button"
                                class="sl-btn sl-btn-ghost sl-btn-sm"
                                data-demo-action="Profile photo picker opened."
                            >
                                Change Photo
                            </button>
                        </div>
                    </div>

                    <div class="sl-form-grid">
                        <label class="sl-field">
                            <span>First name</span>
                            <input value="Mariel" required>
                        </label>

                        <label class="sl-field">
                            <span>Last name</span>
                            <input value="Santos" required>
                        </label>

                        <label class="sl-field">
                            <span>Email address</span>
                            <input type="email" value="{{ $sellerEmail }}" required>
                        </label>

                        <label class="sl-field">
                            <span>Mobile number</span>
                            <input value="0917 000 2481" required>
                        </label>

                        <label class="sl-field">
                            <span>Role</span>
                            <input value="Store Owner" disabled>
                        </label>

                        <label class="sl-field">
                            <span>Seller ID</span>
                            <input value="SLR-20481" disabled>
                        </label>
                    </div>

                    <div class="sl-form-actions">
                        <button type="submit" class="sl-btn sl-btn-primary">
                            Save Profile
                        </button>
                    </div>
                </form>
            @endif
        </main>
    </div>
</div>

<div class="sl-modal sl-account-modal" data-modal="passwordModal" hidden>
    <div class="sl-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="passwordTitle">
        <header>
            <div>
                <span class="sl-eyebrow">
                    Account Security
                </span>

                <h2 id="passwordTitle">
                    Change Password
                </h2>
            </div>

            <button
                type="button"
                class="sl-icon-btn"
                data-modal-close
                aria-label="Close"
            >
                ×
            </button>
        </header>

        <form data-demo-form data-success="Password changed successfully.">
            <div class="sl-form-grid">
                <label class="sl-field sl-span-2">
                    <span>Current password</span>
                    <input type="password" required>
                </label>

                <label class="sl-field sl-span-2">
                    <span>New password</span>
                    <input type="password" minlength="8" required>
                </label>

                <label class="sl-field sl-span-2">
                    <span>Confirm new password</span>
                    <input type="password" minlength="8" required>
                </label>
            </div>

            <footer>
                <button
                    type="button"
                    class="sl-btn sl-btn-ghost"
                    data-modal-close
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="sl-btn sl-btn-primary"
                >
                    Update Password
                </button>
            </footer>
        </form>
    </div>
</div>
@endsection