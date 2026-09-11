@extends('layouts.seller')

@section('title', 'Store')
@section('active', 'store')
@section('subtitle', 'Manage your public shop identity, information, and operating settings.')

@php
    $tabs = [
        'profile' => 'Shop Profile',
        'settings' => 'Store Settings',
    ];

    $requestedTab = $tab ?? request('tab', 'profile');

    $currentTab = array_key_exists($requestedTab, $tabs)
        ? $requestedTab
        : 'profile';

    $settingsLinks = [
        ['id' => 'operations', 'label' => 'Operations'],
        ['id' => 'orders', 'label' => 'Order Preferences'],
        ['id' => 'service', 'label' => 'Customer Service'],
        ['id' => 'visibility', 'label' => 'Store Visibility'],
    ];
@endphp

@section('content')
<style>
    :root {
        --store-bg: #FBF7F2;
        --store-bg-soft: #F6EFE7;
        --store-bg-alt: #EFE7DE;
        --store-card: #FFFDF9;

        --store-border: #EADCCC;
        --store-border-strong: #DBCEC1;

        --store-maroon: #561C17;
        --store-maroon-2: #642920;
        --store-maroon-dark: #3E130F;

        --store-text: #3B211B;
        --store-brown: #6C4936;
        --store-muted: #987865;
        --store-muted-2: #A99386;

        --store-tan: #C19771;
        --store-star: #C88418;

        --store-success: #256F4A;
        --store-success-soft: #EAF7EF;

        --store-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --store-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .sl-store-page {
        color: var(--store-text);

        --sl-blue: var(--store-maroon);
        --sl-blue-dark: var(--store-maroon-dark);
        --sl-blue-soft: #F3E4DE;
        --sl-indigo: var(--store-tan);
    }

    .sl-store-page .sl-page-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;

        padding: 32px 36px;

        border: 1px solid var(--store-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--store-shadow-soft);
    }

    .sl-store-page .sl-eyebrow {
        color: var(--store-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .sl-store-page .sl-page-toolbar h2 {
        margin-top: 10px;

        color: var(--store-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .sl-store-page .sl-page-toolbar p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--store-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .sl-store-page .sl-btn {
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
        line-height: 1;
        text-decoration: none;

        cursor: pointer;
        transition: 160ms ease;
    }

    .sl-store-page .sl-btn:hover {
        transform: translateY(-1px);
    }

    .sl-store-page .sl-btn-primary {
        background: var(--store-maroon) !important;
        border-color: var(--store-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .sl-store-page .sl-btn-primary:hover {
        background: var(--store-maroon-dark) !important;
        border-color: var(--store-maroon-dark) !important;
    }

    .sl-store-page .sl-btn-ghost,
    .sl-store-page .sl-btn-soft {
        background: var(--store-card) !important;
        border-color: var(--store-tan) !important;
        color: var(--store-maroon) !important;
    }

    .sl-store-page .sl-btn-ghost:hover,
    .sl-store-page .sl-btn-soft:hover {
        background: #F3E4DE !important;
        border-color: var(--store-maroon) !important;
    }

    .sl-store-page .sl-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 6px;

        border: 1px solid var(--store-border);
        border-radius: 16px;

        background: var(--store-card);
        box-shadow: var(--store-shadow-soft);
    }

    .sl-store-page .sl-tabs a {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;

        padding: 0 16px;

        border-radius: 12px;

        color: var(--store-brown);
        text-decoration: none;

        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;

        transition: 160ms ease;
    }

    .sl-store-page .sl-tabs a:hover {
        background: var(--store-bg-soft);
        color: var(--store-maroon);
    }

    .sl-store-page .sl-tabs a.is-active {
        background: var(--store-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .sl-store-page .sl-card {
        overflow: hidden;

        border: 1px solid var(--store-border) !important;
        border-radius: 24px !important;

        background: var(--store-card) !important;
        color: var(--store-text) !important;

        box-shadow: var(--store-shadow-soft) !important;
    }

    .sl-store-page .sl-store-layout,
    .sl-store-page .sl-settings-layout {
        display: grid;
        grid-template-columns: 340px minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .sl-store-page .sl-store-preview,
    .sl-store-page .sl-settings-nav {
        position: sticky;
        top: 92px;
    }

    .sl-store-page .sl-store-preview {
        text-align: center;
        overflow: hidden;
    }

    .sl-store-page .sl-shop-cover {
        position: relative;

        display: flex;
        min-height: 150px;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;

        padding: 16px;

        background:
            radial-gradient(circle at 80% 10%, rgba(255, 253, 249, 0.20), transparent 28%),
            linear-gradient(135deg, var(--store-maroon) 0%, var(--store-maroon-2) 58%, var(--store-maroon-dark) 100%);

        color: #FFFFFF;
    }

    .sl-store-page .sl-shop-cover span {
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        opacity: 0.82;
    }

    .sl-store-page .sl-shop-cover button,
    .sl-store-page .sl-shop-logo button {
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 999px;

        background: rgba(255, 255, 255, 0.12);
        color: #FFFFFF;

        font-size: 10px;
        font-weight: 900;

        cursor: pointer;
    }

    .sl-store-page .sl-shop-cover button {
        min-height: 30px;
        padding: 0 11px;
    }

    .sl-store-page .sl-shop-logo {
        position: relative;

        display: grid;
        width: 88px;
        height: 88px;
        place-items: center;

        margin: -44px auto 0;

        border: 6px solid var(--store-card);
        border-radius: 28px;

        background: #F1E4D7;
        color: var(--store-maroon);

        font-size: 26px;
        font-weight: 950;
        box-shadow: 0 12px 30px rgba(86, 28, 23, 0.14);
    }

    .sl-store-page .sl-shop-logo button {
        position: absolute;
        right: -8px;
        bottom: -8px;

        display: grid;
        width: 32px;
        height: 32px;
        place-items: center;

        background: var(--store-maroon);
    }

    .sl-store-page .sl-store-preview h2 {
        margin: 16px 22px 0;

        color: var(--store-text);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 38px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.05em;
    }

    .sl-store-page .sl-store-preview p {
        margin: 10px 26px 0;

        color: var(--store-muted);

        font-size: 12px;
        line-height: 1.65;
    }

    .sl-store-page .sl-store-rating {
        display: inline-grid;
        gap: 4px;

        margin-top: 18px;
        padding: 13px 18px;

        border: 1px solid var(--store-border);
        border-radius: 18px;

        background: var(--store-bg-soft);
    }

    .sl-store-page .sl-store-rating strong {
        color: var(--store-maroon);

        font-size: 24px;
        font-weight: 950;
        line-height: 1;
    }

    .sl-store-page .sl-store-rating span {
        color: var(--store-star);
        font-size: 11px;
        letter-spacing: 0.08em;
    }

    .sl-store-page .sl-store-rating small {
        color: var(--store-muted);

        font-size: 10px;
        font-weight: 800;
    }

    .sl-store-page .sl-store-preview dl {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;

        margin: 20px 18px 0;
    }

    .sl-store-page .sl-store-preview dl div {
        padding: 12px 8px;

        border: 1px solid var(--store-border);
        border-radius: 16px;

        background: linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
    }

    .sl-store-page .sl-store-preview dt {
        color: var(--store-muted);

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sl-store-page .sl-store-preview dd {
        margin: 5px 0 0;

        color: var(--store-text);

        font-size: 15px;
        font-weight: 950;
    }

    .sl-store-page .sl-verified-store {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        gap: 7px;

        margin: 20px 0 22px;
        padding: 0 13px;

        border: 1px solid #CFE8DA;
        border-radius: 999px;

        background: var(--store-success-soft);
        color: var(--store-success);

        font-size: 11px;
        font-weight: 900;
    }

    .sl-store-page .sl-store-form,
    .sl-store-page .sl-settings-main {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .sl-store-page .sl-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding: 20px 22px;

        border-bottom: 1px solid var(--store-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .sl-store-page .sl-card-head h2 {
        margin: 0;

        color: var(--store-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 34px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-store-page .sl-card-head p {
        margin-top: 8px;

        color: var(--store-muted) !important;

        font-size: 12px;
        line-height: 1.65;
    }

    .sl-store-page .sl-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;

        padding: 22px;
    }

    .sl-store-page .sl-span-2 {
        grid-column: 1 / -1;
    }

    .sl-store-page .sl-field {
        display: grid;
        gap: 7px;
    }

    .sl-store-page .sl-field > span {
        color: var(--store-muted) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sl-store-page .sl-field b {
        color: var(--store-maroon);
    }

    .sl-store-page .sl-field input,
    .sl-store-page .sl-field select,
    .sl-store-page .sl-field textarea {
        width: 100%;
        min-height: 43px;
        padding: 0 13px;

        border: 1px solid var(--store-border);
        border-radius: 14px;

        background: var(--store-bg-soft);
        color: var(--store-text);

        font-size: 12px;
        font-weight: 700;
        outline: 0;

        transition: 160ms ease;
    }

    .sl-store-page .sl-field textarea {
        min-height: 132px;
        padding-block: 12px;

        line-height: 1.65;
        resize: vertical;
    }

    .sl-store-page .sl-field input:focus,
    .sl-store-page .sl-field select:focus,
    .sl-store-page .sl-field textarea:focus {
        border-color: var(--store-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .sl-store-page .sl-field small {
        color: var(--store-muted);

        font-size: 10px;
        line-height: 1.5;
    }

    .sl-store-page .sl-settings-nav {
        display: grid;
        gap: 7px;

        padding: 14px;
    }

    .sl-store-page .sl-settings-nav strong {
        margin: 4px 8px 8px;

        color: var(--store-text);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        letter-spacing: -0.045em;
    }

    .sl-store-page .sl-settings-nav a {
        display: flex;
        min-height: 42px;
        align-items: center;

        padding: 0 13px;

        border-radius: 14px;

        color: var(--store-brown);
        text-decoration: none;

        font-size: 12px;
        font-weight: 900;

        transition: 160ms ease;
    }

    .sl-store-page .sl-settings-nav a:hover {
        background: var(--store-bg-soft);
        color: var(--store-maroon);
    }

    .sl-store-page .sl-settings-nav a.is-active {
        background: var(--store-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .sl-store-page .sl-setting-switch {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;

        margin: 18px 22px;
        padding: 16px;

        border: 1px solid var(--store-border);
        border-radius: 18px;

        background: var(--store-bg-soft);
    }

    .sl-store-page .sl-setting-switch + .sl-setting-switch {
        margin-top: -6px;
    }

    .sl-store-page .sl-setting-switch strong {
        color: var(--store-text);

        font-size: 13px;
        font-weight: 950;
    }

    .sl-store-page .sl-setting-switch p {
        margin: 5px 0 0;

        color: var(--store-muted);

        font-size: 11px;
        line-height: 1.55;
    }

    .sl-store-page .sl-form-section > .sl-field {
        margin: 0 22px 22px;
    }

    .sl-store-page .sl-switch {
        position: relative;

        display: inline-flex;
        width: 48px;
        height: 28px;
        flex: 0 0 48px;
    }

    .sl-store-page .sl-switch input {
        position: absolute;
        opacity: 0;
    }

    .sl-store-page .sl-switch span {
        position: absolute;
        inset: 0;

        border: 1px solid var(--store-border-strong);
        border-radius: 999px;

        background: #E7D9CB;

        cursor: pointer;
        transition: 180ms ease;
    }

    .sl-store-page .sl-switch span::after {
        position: absolute;
        top: 3px;
        left: 3px;

        width: 20px;
        height: 20px;

        border-radius: 999px;

        background: #FFFFFF;
        box-shadow: 0 2px 7px rgba(86, 28, 23, 0.18);

        content: "";
        transition: 180ms ease;
    }

    .sl-store-page .sl-switch input:checked + span {
        border-color: var(--store-maroon);
        background: var(--store-maroon);
    }

    .sl-store-page .sl-switch input:checked + span::after {
        transform: translateX(20px);
    }

    .sl-store-page .sl-sticky-actions {
        position: sticky;
        bottom: 18px;
        z-index: 5;

        display: flex;
        justify-content: flex-end;
        gap: 10px;

        padding: 14px;

        border: 1px solid var(--store-border);
        border-radius: 20px;

        background: rgba(255, 253, 249, 0.82);
        box-shadow: 0 12px 34px rgba(86, 28, 23, 0.10);
        backdrop-filter: blur(14px);
    }

    @media (max-width: 1120px) {
        .sl-store-page .sl-store-layout,
        .sl-store-page .sl-settings-layout {
            grid-template-columns: 1fr;
        }

        .sl-store-page .sl-store-preview,
        .sl-store-page .sl-settings-nav {
            position: static;
        }

        .sl-store-page .sl-settings-nav {
            display: flex;
            overflow-x: auto;
        }

        .sl-store-page .sl-settings-nav strong {
            display: none;
        }

        .sl-store-page .sl-settings-nav a {
            min-width: max-content;
        }
    }

    @media (max-width: 760px) {
        .sl-store-page .sl-page-toolbar,
        .sl-store-page .sl-card-head,
        .sl-store-page .sl-setting-switch,
        .sl-store-page .sl-sticky-actions {
            align-items: flex-start;
            flex-direction: column;
        }

        .sl-store-page .sl-page-toolbar {
            padding: 26px 22px;
        }

        .sl-store-page .sl-form-grid {
            grid-template-columns: 1fr;
        }

        .sl-store-page .sl-span-2 {
            grid-column: auto;
        }

        .sl-store-page .sl-btn,
        .sl-store-page .sl-sticky-actions {
            width: 100%;
        }

        .sl-store-page .sl-store-preview dl {
            grid-template-columns: 1fr;
        }
    }

    html.dark .sl-store-page .sl-page-toolbar,
    html.dark .sl-store-page .sl-tabs,
    html.dark .sl-store-page .sl-card,
    html.dark .sl-store-page .sl-card-head,
    html.dark .sl-store-page .sl-store-preview dl div,
    html.dark .sl-store-page .sl-setting-switch,
    html.dark .sl-store-page .sl-sticky-actions {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;

        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .sl-store-page .sl-page-toolbar h2,
    html.dark .sl-store-page .sl-store-preview h2,
    html.dark .sl-store-page .sl-card-head h2,
    html.dark .sl-store-page .sl-settings-nav strong,
    html.dark .sl-store-page .sl-setting-switch strong,
    html.dark .sl-store-page .sl-store-preview dd {
        color: #F5EFE8 !important;
    }

    html.dark .sl-store-page .sl-page-toolbar p,
    html.dark .sl-store-page .sl-store-preview p,
    html.dark .sl-store-page .sl-card-head p,
    html.dark .sl-store-page .sl-field small,
    html.dark .sl-store-page .sl-setting-switch p,
    html.dark .sl-store-page .sl-store-preview dt {
        color: #C8B7AD !important;
    }

    html.dark .sl-store-page .sl-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .sl-store-page .sl-tabs a,
    html.dark .sl-store-page .sl-settings-nav a {
        color: #C8B7AD !important;
    }

    html.dark .sl-store-page .sl-tabs a:hover,
    html.dark .sl-store-page .sl-tabs a.is-active,
    html.dark .sl-store-page .sl-settings-nav a:hover,
    html.dark .sl-store-page .sl-settings-nav a.is-active {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-store-page .sl-field input,
    html.dark .sl-store-page .sl-field select,
    html.dark .sl-store-page .sl-field textarea,
    html.dark .sl-store-page .sl-btn-ghost {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-store-page .sl-btn-primary,
    html.dark .sl-store-page .sl-switch input:checked + span {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .sl-store-page .sl-shop-logo {
        border-color: #211B17 !important;
        background: #2D1414 !important;
        color: #EBA99D !important;
    }
</style>

<div class="sl-page sl-store-page">
    <div class="sl-page-toolbar">
        <div>
            <span class="sl-eyebrow">
                Store Management
            </span>

            <h2>
                Your Shop
            </h2>

            <p>
                Keep your storefront accurate, trustworthy, and easy for buyers to contact.
            </p>
        </div>

        <button
            type="button"
            class="sl-btn sl-btn-ghost"
            data-demo-action="Store preview opened."
        >
            Preview Store
        </button>
    </div>

    <nav class="sl-tabs" aria-label="Store sections">
        @foreach ($tabs as $key => $label)
            <a
                href="{{ route('seller.store', ['tab' => $key]) }}"
                class="{{ $currentTab === $key ? 'is-active' : '' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </nav>

    @if ($currentTab === 'settings')
        <form
            class="sl-settings-layout"
            data-demo-form
            data-success="Store settings saved."
        >
            <div class="sl-settings-nav sl-card">
                <strong>Settings</strong>

                @foreach ($settingsLinks as $link)
                    <a
                        href="#{{ $link['id'] }}"
                        class="{{ $loop->first ? 'is-active' : '' }}"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="sl-settings-main">
                <section class="sl-card sl-form-section" id="operations">
                    <header class="sl-card-head">
                        <div>
                            <h2>Store Operations</h2>
                            <p>Set regular hours and handling capacity.</p>
                        </div>
                    </header>

                    <div class="sl-form-grid">
                        <label class="sl-field">
                            <span>Opening time</span>
                            <input type="time" value="09:00">
                        </label>

                        <label class="sl-field">
                            <span>Closing time</span>
                            <input type="time" value="18:00">
                        </label>

                        <label class="sl-field">
                            <span>Operating days</span>

                            <select>
                                <option>Monday to Saturday</option>
                                <option>Monday to Friday</option>
                                <option>Every day</option>
                            </select>
                        </label>

                        <label class="sl-field">
                            <span>Daily order capacity</span>
                            <input type="number" min="1" value="80">
                        </label>
                    </div>
                </section>

                <section class="sl-card sl-form-section" id="orders">
                    <header class="sl-card-head">
                        <div>
                            <h2>Order Preferences</h2>
                            <p>Configure default preparation and cancellation rules.</p>
                        </div>
                    </header>

                    <div class="sl-setting-switch">
                        <div>
                            <strong>Automatic order acceptance</strong>
                            <p>Accept paid orders automatically when stock is available.</p>
                        </div>

                        <label class="sl-switch">
                            <input type="checkbox">
                            <span></span>
                        </label>
                    </div>

                    <div class="sl-setting-switch">
                        <div>
                            <strong>Low stock protection</strong>
                            <p>Pause a listing automatically when its stock reaches zero.</p>
                        </div>

                        <label class="sl-switch">
                            <input type="checkbox" checked>
                            <span></span>
                        </label>
                    </div>

                    <label class="sl-field">
                        <span>Default preparation time</span>

                        <select>
                            <option>1 business day</option>
                            <option>2 business days</option>
                            <option>3 business days</option>
                        </select>
                    </label>
                </section>

                <section class="sl-card sl-form-section" id="service">
                    <header class="sl-card-head">
                        <div>
                            <h2>Customer Service</h2>
                            <p>Set expectations for buyer communication.</p>
                        </div>
                    </header>

                    <div class="sl-setting-switch">
                        <div>
                            <strong>Show response time</strong>
                            <p>Display your average chat response time on the shop.</p>
                        </div>

                        <label class="sl-switch">
                            <input type="checkbox" checked>
                            <span></span>
                        </label>
                    </div>

                    <label class="sl-field">
                        <span>Automatic welcome message</span>

                        <textarea rows="4">Hello! Thank you for messaging LIKHAE Studio. How can we help with your order today?</textarea>
                    </label>
                </section>

                <section class="sl-card sl-form-section" id="visibility">
                    <header class="sl-card-head">
                        <div>
                            <h2>Store Visibility</h2>
                            <p>Control whether buyers can discover and order from your shop.</p>
                        </div>
                    </header>

                    <div class="sl-setting-switch">
                        <div>
                            <strong>Store is visible</strong>
                            <p>Buyers can visit the shop and purchase active listings.</p>
                        </div>

                        <label class="sl-switch">
                            <input type="checkbox" checked>
                            <span></span>
                        </label>
                    </div>

                    <div class="sl-setting-switch">
                        <div>
                            <strong>Vacation mode</strong>
                            <p>Temporarily stop new orders while keeping the store page visible.</p>
                        </div>

                        <label class="sl-switch">
                            <input type="checkbox">
                            <span></span>
                        </label>
                    </div>
                </section>

                <div class="sl-sticky-actions">
                    <button type="button" class="sl-btn sl-btn-ghost">
                        Discard
                    </button>

                    <button type="submit" class="sl-btn sl-btn-primary">
                        Save Settings
                    </button>
                </div>
            </div>
        </form>
    @else
        <form
            class="sl-store-layout"
            data-demo-form
            data-success="Shop profile updated."
        >
            <aside class="sl-card sl-store-preview">
                <div class="sl-shop-cover">
                    <span>Shop cover</span>

                    <button
                        type="button"
                        data-demo-action="Cover image picker opened."
                    >
                        Change
                    </button>
                </div>

                <div class="sl-shop-logo">
                    LS

                    <button
                        type="button"
                        data-demo-action="Shop logo picker opened."
                        aria-label="Change shop logo"
                    >
                        ✎
                    </button>
                </div>

                <h2>
                    LIKHAE Studio
                </h2>

                <p>
                    Verified technology and workspace essentials from Laguna.
                </p>

                <div class="sl-store-rating">
                    <strong>4.8</strong>
                    <span>★★★★★</span>
                    <small>1,248 reviews</small>
                </div>

                <dl>
                    <div>
                        <dt>Products</dt>
                        <dd>126</dd>
                    </div>

                    <div>
                        <dt>Followers</dt>
                        <dd>8.4k</dd>
                    </div>

                    <div>
                        <dt>Response</dt>
                        <dd>98%</dd>
                    </div>
                </dl>

                <span class="sl-verified-store">
                    ✓ Verified Seller
                </span>
            </aside>

            <div class="sl-store-form">
                <section class="sl-card sl-form-section">
                    <header class="sl-card-head">
                        <div>
                            <h2>Shop Information</h2>
                            <p>This information is visible to buyers.</p>
                        </div>
                    </header>

                    <div class="sl-form-grid">
                        <label class="sl-field sl-span-2">
                            <span>Shop name <b>*</b></span>

                            <input value="LIKHAE Studio" required>

                            <small>Shop names can be changed once every 30 days.</small>
                        </label>

                        <label class="sl-field sl-span-2">
                            <span>Description</span>

                            <textarea rows="5">Verified technology and workspace essentials from Laguna. We provide carefully selected products, secure packaging, and responsive after-sales support.</textarea>
                        </label>

                        <label class="sl-field">
                            <span>Store category</span>

                            <select>
                                <option>Electronics & Accessories</option>
                                <option>Home & Living</option>
                                <option>Fashion</option>
                            </select>
                        </label>

                        <label class="sl-field">
                            <span>Public contact</span>
                            <input value="support@likhaestudio.ph" type="email">
                        </label>
                    </div>
                </section>

                <section class="sl-card sl-form-section">
                    <header class="sl-card-head">
                        <div>
                            <h2>Location & Business Hours</h2>
                            <p>Help buyers understand where and when you operate.</p>
                        </div>
                    </header>

                    <div class="sl-form-grid">
                        <label class="sl-field sl-span-2">
                            <span>Store location</span>
                            <input value="24 Rizal Street, Santa Cruz, Laguna 4009">
                        </label>

                        <label class="sl-field">
                            <span>Business days</span>

                            <select>
                                <option>Monday to Saturday</option>
                                <option>Monday to Friday</option>
                            </select>
                        </label>

                        <label class="sl-field">
                            <span>Business hours</span>
                            <input value="9:00 AM – 6:00 PM">
                        </label>
                    </div>
                </section>

                <div class="sl-sticky-actions">
                    <button type="button" class="sl-btn sl-btn-ghost">
                        Cancel
                    </button>

                    <button type="submit" class="sl-btn sl-btn-primary">
                        Save Shop Profile
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection