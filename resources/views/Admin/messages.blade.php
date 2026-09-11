@extends('layouts.admin')

@section('title', 'Communication')
@section('subtitle', 'Manage support conversations and publish targeted platform announcements.')
@section('active', 'messages')

@php
    $requestedTab = request('tab', 'messages');

    $tab = in_array($requestedTab, ['messages', 'announcements'], true)
        ? $requestedTab
        : 'messages';

    $pageTitle = $tab === 'announcements'
        ? 'Announcements'
        : 'Support messages';

    $pageDescription = 'Keep conversations clear, role-aware, and connected to marketplace records.';

    $conversations = [
        [
            'initials' => 'AC',
            'name' => 'Angela Cruz',
            'subject' => 'Buyer · Order #LK-10482',
            'preview' => 'My order status has not updated...',
            'time' => '10:42',
            'active' => true,
        ],
        [
            'initials' => 'LS',
            'name' => 'LIKHA Studio',
            'subject' => 'Seller · Compliance question',
            'preview' => 'We submitted the renewed permit...',
            'time' => '09:18',
            'active' => false,
        ],
        [
            'initials' => 'NH',
            'name' => 'NorthLink Hub',
            'subject' => 'Logistics · Delivery exception',
            'preview' => 'Requesting help with a duplicate...',
            'time' => 'Yesterday',
            'active' => false,
        ],
        [
            'initials' => 'JD',
            'name' => 'Juan Dela Cruz',
            'subject' => 'Rider · Account access',
            'preview' => 'My account is showing under review...',
            'time' => 'Yesterday',
            'active' => false,
        ],
    ];

    $announcements = [
        [
            'initial' => 'A',
            'title' => 'Scheduled maintenance',
            'meta' => 'All users · Sep 3 · 12,204 delivered',
            'status' => 'Sent',
        ],
        [
            'initial' => 'S',
            'title' => 'Updated product policy',
            'meta' => 'Sellers · Sep 1 · 1,902 delivered',
            'status' => 'Sent',
        ],
        [
            'initial' => 'L',
            'title' => 'Holiday pickup schedule',
            'meta' => 'Logistics · Scheduled Sep 6',
            'status' => 'Scheduled',
        ],
    ];
@endphp

@section('content')
<style>
    :root {
        --comm-bg: #FBF7F2;
        --comm-bg-soft: #F6EFE7;
        --comm-bg-alt: #EFE7DE;
        --comm-card: #FFFDF9;

        --comm-border: #EADCCC;
        --comm-border-strong: #DBCEC1;

        --comm-maroon: #561C17;
        --comm-maroon-2: #642920;
        --comm-maroon-dark: #3E130F;

        --comm-text: #3B211B;
        --comm-brown: #6C4936;
        --comm-muted: #987865;
        --comm-muted-2: #A99386;

        --comm-tan: #C19771;

        --comm-success: #256F4A;
        --comm-success-soft: #EAF7EF;

        --comm-warning: #9A5B11;
        --comm-warning-soft: #FFF6DE;

        --comm-danger: #B42318;
        --comm-danger-soft: #FCEBE9;

        --comm-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --comm-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ad-communication-page {
        color: var(--comm-text);

        --ad-blue: var(--comm-maroon);
        --ad-blue-dark: var(--comm-maroon-dark);
        --ad-blue-soft: #F3E4DE;
        --ad-blue-border: #E6C7BE;
    }

    .ad-communication-page .ad-page-head {
        padding: 32px 36px;

        border: 1px solid var(--comm-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--comm-shadow-soft);
    }

    .ad-communication-page .ad-overline {
        color: var(--comm-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-communication-page .ad-page-head h2 {
        margin-top: 10px;

        color: var(--comm-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-communication-page .ad-page-head p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--comm-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .ad-communication-page .ad-btn-primary {
        background: var(--comm-maroon) !important;
        border-color: var(--comm-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ad-communication-page .ad-btn-primary:hover {
        background: var(--comm-maroon-dark) !important;
        border-color: var(--comm-maroon-dark) !important;
    }

    .ad-communication-page .ad-btn-secondary {
        background: var(--comm-card) !important;
        border-color: var(--comm-tan) !important;
        color: var(--comm-maroon) !important;
    }

    .ad-communication-page .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--comm-maroon) !important;
    }

    .ad-communication-page .ad-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 6px;

        border: 1px solid var(--comm-border);
        border-radius: 16px;

        background: var(--comm-card);
        box-shadow: var(--comm-shadow-soft);
    }

    .ad-communication-page .ad-tab {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 8px;

        padding: 0 16px;

        border-radius: 12px;

        color: var(--comm-brown);

        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;

        transition: 160ms ease;
    }

    .ad-communication-page .ad-tab:hover {
        background: var(--comm-bg-soft);
        color: var(--comm-maroon);
    }

    .ad-communication-page .ad-tab.is-active {
        background: var(--comm-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .ad-communication-page .ad-tab b {
        display: inline-flex;
        min-width: 22px;
        height: 22px;
        align-items: center;
        justify-content: center;

        padding: 0 7px;

        border-radius: 999px;

        background: rgba(255, 255, 255, 0.18);
        color: inherit;

        font-size: 10px;
        font-weight: 950;
    }

    .ad-communication-page .ad-dashboard-split {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(320px, 0.75fr);
        gap: 18px;
    }

    .ad-communication-page .ad-card {
        overflow: hidden;

        border: 1px solid var(--comm-border) !important;
        border-radius: 24px !important;

        background: var(--comm-card) !important;
        color: var(--comm-text) !important;

        box-shadow: var(--comm-shadow-soft) !important;
    }

    .ad-communication-page .ad-card-head {
        padding: 20px 22px;

        border-bottom: 1px solid var(--comm-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .ad-communication-page .ad-card-head h2,
    .ad-communication-page .ad-card-head h3 {
        margin-top: 7px;

        color: var(--comm-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-communication-page .ad-card-head p {
        color: var(--comm-muted) !important;
    }

    .ad-communication-page .ad-settings-section {
        display: grid;
        gap: 18px;
        padding: 22px;
    }

    .ad-communication-page .ad-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .ad-communication-page .ad-form-grid .is-full {
        grid-column: 1 / -1;
    }

    .ad-communication-page .ad-field {
        display: grid;
        gap: 7px;
    }

    .ad-communication-page .ad-field span {
        color: var(--comm-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .ad-communication-page .ad-field input,
    .ad-communication-page .ad-field select,
    .ad-communication-page .ad-field textarea {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;

        border: 1px solid var(--comm-border);
        border-radius: 14px;

        background: var(--comm-bg-soft);
        color: var(--comm-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;
        resize: vertical;
    }

    .ad-communication-page .ad-field textarea {
        min-height: 170px;
        line-height: 1.65;
    }

    .ad-communication-page .ad-field input:focus,
    .ad-communication-page .ad-field select:focus,
    .ad-communication-page .ad-field textarea:focus {
        border-color: var(--comm-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-communication-page .ad-field input::placeholder,
    .ad-communication-page .ad-field textarea::placeholder {
        color: var(--comm-muted-2);
    }

    .ad-communication-page .ad-queue {
        display: grid;
        gap: 12px;
        padding: 18px;
    }

    .ad-communication-page .ad-queue-item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;

        padding: 14px;

        border: 1px solid var(--comm-border);
        border-radius: 16px;

        background:
            radial-gradient(circle at 96% 0%, rgba(193, 151, 113, 0.11), transparent 28%),
            #FFFDF9;

        color: var(--comm-text);
    }

    .ad-communication-page .ad-queue-item.is-warning {
        background: var(--comm-warning-soft);
        border-color: #EAD39A;
    }

    .ad-communication-page .ad-queue-icon {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;

        border-radius: 14px;

        background: #F1E4D7;
        color: var(--comm-maroon);

        font-size: 14px;
        font-weight: 950;
    }

    .ad-communication-page .ad-queue-copy {
        display: grid;
        gap: 4px;
        min-width: 0;
    }

    .ad-communication-page .ad-queue-copy strong {
        color: var(--comm-text);
        font-size: 12px;
        font-weight: 950;
    }

    .ad-communication-page .ad-queue-copy span {
        color: var(--comm-muted);
        font-size: 11px;
        line-height: 1.45;
    }

    .ad-communication-page .ad-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 25px;
        padding: 0 10px;

        border: 1px solid transparent;
        border-radius: 999px;

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .ad-communication-page .ad-status.is-completed,
    .ad-communication-page .ad-status.is-sent {
        background: var(--comm-success-soft);
        border-color: #CFE8DA;
        color: var(--comm-success);
    }

    .ad-communication-page .ad-status.is-pending,
    .ad-communication-page .ad-status.is-scheduled {
        background: var(--comm-warning-soft);
        border-color: #EAD39A;
        color: var(--comm-warning);
    }

    .ad-communication-page .ad-status.is-open {
        background: var(--comm-danger-soft);
        border-color: #F0C9C4;
        color: var(--comm-danger);
    }

    .ad-communication-page .ad-messages {
        display: grid;
        min-height: 650px;
        grid-template-columns: 330px minmax(0, 1fr);

        overflow: hidden;

        border: 1px solid var(--comm-border);
        border-radius: 24px;

        background: var(--comm-card);
        box-shadow: var(--comm-shadow-soft);
    }

    .ad-communication-page .ad-conversations {
        border-right: 1px solid var(--comm-border);
        background:
            radial-gradient(circle at 0% 0%, rgba(193, 151, 113, 0.13), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ad-communication-page .ad-conversation-head,
    .ad-communication-page .ad-chat-head {
        display: flex;
        min-height: 72px;
        align-items: center;
        justify-content: space-between;
        gap: 12px;

        padding: 16px 18px;

        border-bottom: 1px solid var(--comm-border);
    }

    .ad-communication-page .ad-conversation-head h2,
    .ad-communication-page .ad-chat-head h2 {
        margin: 0;

        color: var(--comm-text);

        font-size: 15px;
        font-weight: 950;
        letter-spacing: -0.035em;
    }

    .ad-communication-page .ad-chat-head p {
        margin: 4px 0 0;

        color: var(--comm-muted);

        font-size: 11px;
        font-weight: 700;
    }

    .ad-communication-page .ad-conversation-list {
        display: grid;
        padding: 10px;
        gap: 8px;
    }

    .ad-communication-page .ad-conversation {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 11px;

        width: 100%;
        padding: 12px;

        border: 1px solid transparent;
        border-radius: 16px;

        background: transparent;
        color: var(--comm-text);

        text-align: left;
        cursor: pointer;

        transition: 160ms ease;
    }

    .ad-communication-page .ad-conversation:hover,
    .ad-communication-page .ad-conversation.is-active {
        background: #FFFDF9;
        border-color: var(--comm-tan);
        box-shadow: 0 8px 20px rgba(86, 28, 23, 0.07);
    }

    .ad-communication-page .ad-avatar {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;

        border-radius: 14px;

        background: var(--comm-maroon);
        color: #FFFFFF;

        font-size: 12px;
        font-weight: 950;
    }

    .ad-communication-page .ad-avatar.is-soft {
        background: #F1E4D7 !important;
        color: var(--comm-maroon) !important;
    }

    .ad-communication-page .ad-conversation strong {
        display: block;

        color: var(--comm-text);

        font-size: 12px;
        font-weight: 950;
    }

    .ad-communication-page .ad-conversation p {
        overflow: hidden;
        max-width: 170px;
        margin: 4px 0 0;

        color: var(--comm-muted);

        font-size: 10px;
        line-height: 1.4;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ad-communication-page .ad-conversation time {
        color: var(--comm-muted-2);

        font-size: 9px;
        font-weight: 800;
    }

    .ad-communication-page .ad-chat {
        display: grid;
        grid-template-rows: auto 1fr auto;
        min-width: 0;

        background: var(--comm-card);
    }

    .ad-communication-page .ad-chat-body {
        display: flex;
        flex-direction: column;
        gap: 13px;

        overflow-y: auto;

        padding: 22px;

        background:
            radial-gradient(circle at 100% 0%, rgba(193, 151, 113, 0.12), transparent 32%),
            var(--comm-bg);
    }

    .ad-communication-page .ad-bubble {
        max-width: min(76%, 560px);
        padding: 12px 14px;

        border: 1px solid var(--comm-border);
        border-radius: 18px 18px 18px 5px;

        background: #FFFDF9;
        color: var(--comm-brown);

        font-size: 12px;
        line-height: 1.65;

        box-shadow: 0 8px 22px rgba(86, 28, 23, 0.055);
    }

    .ad-communication-page .ad-bubble.is-admin {
        align-self: flex-end;

        border-color: var(--comm-maroon);
        border-radius: 18px 18px 5px 18px;

        background: var(--comm-maroon);
        color: #FFFFFF;
    }

    .ad-communication-page .ad-bubble time {
        display: block;
        margin-top: 6px;

        color: inherit;

        font-size: 9px;
        font-weight: 800;
        opacity: 0.7;
    }

    .ad-communication-page .ad-chat-compose {
        display: flex;
        gap: 10px;

        padding: 14px;

        border-top: 1px solid var(--comm-border);

        background:
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ad-communication-page .ad-chat-compose input {
        min-width: 0;
        height: 44px;
        flex: 1;

        padding: 0 14px;

        border: 1px solid var(--comm-border);
        border-radius: 14px;

        background: var(--comm-bg-soft);
        color: var(--comm-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;
    }

    .ad-communication-page .ad-chat-compose input:focus {
        border-color: var(--comm-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-communication-page .ad-inline-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    @media (max-width: 1120px) {
        .ad-communication-page .ad-dashboard-split {
            grid-template-columns: 1fr;
        }

        .ad-communication-page .ad-messages {
            grid-template-columns: 280px minmax(0, 1fr);
        }
    }

    @media (max-width: 820px) {
        .ad-communication-page .ad-page-head {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .ad-communication-page .ad-messages {
            grid-template-columns: 1fr;
        }

        .ad-communication-page .ad-conversations {
            border-right: 0;
            border-bottom: 1px solid var(--comm-border);
        }

        .ad-communication-page .ad-conversation-list {
            grid-auto-flow: column;
            grid-auto-columns: minmax(240px, 1fr);
            overflow-x: auto;
        }

        .ad-communication-page .ad-chat-head,
        .ad-communication-page .ad-chat-compose {
            align-items: stretch;
            flex-direction: column;
        }

        .ad-communication-page .ad-chat-compose .ad-btn,
        .ad-communication-page .ad-page-head .ad-btn,
        .ad-communication-page .ad-inline-actions .ad-btn {
            width: 100%;
        }

        .ad-communication-page .ad-form-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .ad-communication-page .ad-bubble {
            max-width: 100%;
        }

        .ad-communication-page .ad-queue-item {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .ad-communication-page .ad-queue-item .ad-status {
            grid-column: 1 / -1;
            justify-self: start;
        }
    }

    html.dark .ad-communication-page .ad-page-head,
    html.dark .ad-communication-page .ad-tabs,
    html.dark .ad-communication-page .ad-card,
    html.dark .ad-communication-page .ad-card-head,
    html.dark .ad-communication-page .ad-messages,
    html.dark .ad-communication-page .ad-conversations,
    html.dark .ad-communication-page .ad-conversation-head,
    html.dark .ad-communication-page .ad-chat-head,
    html.dark .ad-communication-page .ad-chat-compose,
    html.dark .ad-communication-page .ad-queue-item {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-communication-page .ad-page-head h2,
    html.dark .ad-communication-page .ad-card-head h2,
    html.dark .ad-communication-page .ad-card-head h3,
    html.dark .ad-communication-page .ad-chat-head h2,
    html.dark .ad-communication-page .ad-conversation-head h2,
    html.dark .ad-communication-page .ad-conversation strong,
    html.dark .ad-communication-page .ad-queue-copy strong {
        color: #F5EFE8 !important;
    }

    html.dark .ad-communication-page .ad-page-head p,
    html.dark .ad-communication-page .ad-card-head p,
    html.dark .ad-communication-page .ad-chat-head p,
    html.dark .ad-communication-page .ad-conversation p,
    html.dark .ad-communication-page .ad-conversation time,
    html.dark .ad-communication-page .ad-queue-copy span {
        color: #C8B7AD !important;
    }

    html.dark .ad-communication-page .ad-overline {
        color: #EBA99D !important;
    }

    html.dark .ad-communication-page .ad-tab {
        color: #C8B7AD !important;
    }

    html.dark .ad-communication-page .ad-tab:hover {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-communication-page .ad-tab.is-active {
        background: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .ad-communication-page .ad-field input,
    html.dark .ad-communication-page .ad-field select,
    html.dark .ad-communication-page .ad-field textarea,
    html.dark .ad-communication-page .ad-chat-compose input {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-communication-page .ad-chat-body {
        background: #161210 !important;
    }

    html.dark .ad-communication-page .ad-bubble {
        background: #211B17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-communication-page .ad-bubble.is-admin {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .ad-communication-page .ad-avatar.is-soft,
    html.dark .ad-communication-page .ad-queue-icon {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }
</style>

<div class="ad-page ad-communication-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">
                Platform communication
            </span>

            <h2>
                {{ $pageTitle }}
            </h2>

            <p>
                {{ $pageDescription }}
            </p>
        </div>

        @if($tab === 'announcements')
            <button
                class="ad-btn ad-btn-primary"
                type="button"
                data-demo-action="Announcement composer ready"
            >
                New announcement
            </button>
        @endif
    </div>

    <nav class="ad-tabs" aria-label="Communication sections">
        <a
            class="ad-tab {{ $tab === 'messages' ? 'is-active' : '' }}"
            href="{{ route('admin.messages', ['tab' => 'messages']) }}"
        >
            Messages <b>4</b>
        </a>

        <a
            class="ad-tab {{ $tab === 'announcements' ? 'is-active' : '' }}"
            href="{{ route('admin.messages', ['tab' => 'announcements']) }}"
        >
            Announcements
        </a>
    </nav>

    @if($tab === 'announcements')
        <section class="ad-dashboard-split">
            <article class="ad-card">
                <header class="ad-card-head">
                    <div>
                        <span class="ad-overline">
                            Compose
                        </span>

                        <h2>
                            Create announcement
                        </h2>

                        <p>
                            Send only to the roles that need the update.
                        </p>
                    </div>
                </header>

                <form
                    class="ad-settings-section"
                    data-demo-form
                    data-success-message="Announcement scheduled for publishing"
                >
                    <div class="ad-form-grid">
                        <label class="ad-field">
                            <span>Audience</span>

                            <select>
                                <option>All platform users</option>
                                <option>Buyers only</option>
                                <option>Sellers only</option>
                                <option>Logistics centers only</option>
                                <option>Riders / couriers only</option>
                            </select>
                        </label>

                        <label class="ad-field">
                            <span>Priority</span>

                            <select>
                                <option>Standard</option>
                                <option>Important</option>
                                <option>Urgent</option>
                            </select>
                        </label>

                        <label class="ad-field is-full">
                            <span>Announcement title</span>

                            <input placeholder="Short, specific title">
                        </label>

                        <label class="ad-field is-full">
                            <span>Message</span>

                            <textarea
                                rows="7"
                                placeholder="Write the announcement details..."
                            ></textarea>
                        </label>

                        <label class="ad-field">
                            <span>Publish date</span>

                            <input type="datetime-local">
                        </label>

                        <label class="ad-field">
                            <span>Delivery channels</span>

                            <select>
                                <option>In-app notification</option>
                                <option>In-app + email</option>
                            </select>
                        </label>
                    </div>

                    <div class="ad-inline-actions">
                        <button
                            class="ad-btn ad-btn-primary"
                            type="submit"
                        >
                            Publish announcement
                        </button>

                        <button
                            class="ad-btn ad-btn-secondary"
                            type="button"
                            data-demo-action="Announcement draft saved"
                        >
                            Save draft
                        </button>
                    </div>
                </form>
            </article>

            <aside class="ad-card">
                <header class="ad-card-head">
                    <div>
                        <span class="ad-overline">
                            Published
                        </span>

                        <h3>
                            Recent announcements
                        </h3>

                        <p>
                            Latest messages sent by Admin.
                        </p>
                    </div>
                </header>

                <div class="ad-queue">
                    @foreach($announcements as $announcement)
                        @php
                            $statusClass = \Illuminate\Support\Str::slug($announcement['status']);
                        @endphp

                        <div class="ad-queue-item {{ $announcement['status'] === 'Scheduled' ? 'is-warning' : '' }}">
                            <span class="ad-queue-icon">
                                {{ $announcement['initial'] }}
                            </span>

                            <span class="ad-queue-copy">
                                <strong>
                                    {{ $announcement['title'] }}
                                </strong>

                                <span>
                                    {{ $announcement['meta'] }}
                                </span>
                            </span>

                            <span class="ad-status is-{{ $statusClass }}">
                                {{ $announcement['status'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </aside>
        </section>
    @else
        <section class="ad-messages">
            <aside class="ad-conversations">
                <div class="ad-conversation-head">
                    <h2>
                        Conversations
                    </h2>

                    <span class="ad-status is-open">
                        4 open
                    </span>
                </div>

                <div class="ad-conversation-list">
                    @foreach($conversations as $conversation)
                        <button
                            type="button"
                            class="ad-conversation {{ $conversation['active'] ? 'is-active' : '' }}"
                            data-conversation
                            data-name="{{ $conversation['name'] }}"
                            data-subject="{{ $conversation['subject'] }}"
                        >
                            <span class="ad-avatar is-soft">
                                {{ $conversation['initials'] }}
                            </span>

                            <span>
                                <strong>
                                    {{ $conversation['name'] }}
                                </strong>

                                <p>
                                    {{ $conversation['preview'] }}
                                </p>
                            </span>

                            <time>
                                {{ $conversation['time'] }}
                            </time>
                        </button>
                    @endforeach
                </div>
            </aside>

            <main class="ad-chat">
                <header class="ad-chat-head">
                    <div>
                        <h2 data-chat-name>
                            Angela Cruz
                        </h2>

                        <p data-chat-subject>
                            Buyer · Order #LK-10482
                        </p>
                    </div>

                    <div class="ad-inline-actions">
                        <button
                            class="ad-btn ad-btn-secondary ad-btn-sm"
                            type="button"
                            data-demo-action="Opening linked order"
                        >
                            View order
                        </button>

                        <button
                            class="ad-btn ad-btn-secondary ad-btn-sm"
                            type="button"
                            data-demo-action="Conversation assigned to you"
                        >
                            Assign
                        </button>
                    </div>
                </header>

                <div class="ad-chat-body" data-chat-body>
                    <div class="ad-bubble">
                        Hello, my order status has not updated since this morning. Can you check it?
                        <time>10:36 AM</time>
                    </div>

                    <div class="ad-bubble is-admin">
                        Hi Angela. I can see that the seller prepared the parcel and J&T has a pickup scheduled today. I’ll monitor the first courier scan for you.
                        <time>10:39 AM</time>
                    </div>

                    <div class="ad-bubble">
                        Thank you. Will I receive a notification when it is picked up?
                        <time>10:42 AM</time>
                    </div>
                </div>

                <form class="ad-chat-compose" data-chat-form>
                    <input
                        type="text"
                        placeholder="Write a clear response..."
                        aria-label="Message"
                    >

                    <button
                        type="submit"
                        class="ad-btn ad-btn-primary"
                    >
                        Send
                    </button>
                </form>
            </main>
        </section>
    @endif
</div>
@endsection