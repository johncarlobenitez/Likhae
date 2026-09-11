@extends('logistics.app')

@section('title', 'Messages — LIKHAE Logistics')

@php
    $conversations = [
        'juan' => [
            'name' => 'Juan Dela Cruz',
            'type' => 'Rider',
            'status' => 'Online',
            'avatar' => 'rider',
            'messages' => [
                [
                    'type' => 'received',
                    'text' => 'Good morning. The parcel has been picked up from the seller.',
                    'time' => '10:32 AM',
                ],
                [
                    'type' => 'sent',
                    'text' => 'Confirmed. Please proceed to the sorting center.',
                    'time' => '10:34 AM',
                ],
                [
                    'type' => 'received',
                    'text' => 'Parcel has arrived at the sorting center.',
                    'time' => '10:35 AM',
                ],
            ],
        ],

        'seller' => [
            'name' => 'ABC Handmade Store',
            'type' => 'Seller',
            'status' => 'Online',
            'avatar' => 'seller',
            'messages' => [
                [
                    'type' => 'received',
                    'text' => 'The parcel is ready for pickup.',
                    'time' => '9:20 AM',
                ],
                [
                    'type' => 'sent',
                    'text' => 'A rider has been assigned.',
                    'time' => '9:25 AM',
                ],
                [
                    'type' => 'received',
                    'text' => 'Thank you. Parcel is prepared.',
                    'time' => '9:27 AM',
                ],
            ],
        ],

        'buyer' => [
            'name' => 'Maria Santos',
            'type' => 'Buyer',
            'status' => 'Offline',
            'avatar' => 'buyer',
            'messages' => [
                [
                    'type' => 'received',
                    'text' => 'Where is my order?',
                    'time' => 'Yesterday',
                ],
                [
                    'type' => 'sent',
                    'text' => 'Your parcel is currently being processed.',
                    'time' => 'Yesterday',
                ],
            ],
        ],
    ];

    $conversationIcons = [
        'rider' => '
            <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/>
            <rect x="9" y="11" width="14" height="10" rx="2"/>
            <circle cx="12" cy="21" r="1"/>
            <circle cx="20" cy="21" r="1"/>
        ',

        'seller' => '
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
        ',

        'buyer' => '
            <circle cx="12" cy="8" r="4"/>
            <path d="M4 21c0-5 3-8 8-8s8 3 8 8"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --msg-bg: #FBF7F2;
        --msg-bg-soft: #F6EFE7;
        --msg-bg-warm: #F3E4DE;
        --msg-card: #FFFDF9;

        --msg-border: #EADCCC;
        --msg-border-strong: #DBCEC1;

        --msg-maroon: #561C17;
        --msg-maroon-2: #642920;
        --msg-maroon-dark: #3E130F;

        --msg-text: #3B211B;
        --msg-text-dark: #1C160F;
        --msg-brown: #6C4936;
        --msg-muted: #987865;
        --msg-muted-light: #A99386;

        --msg-tan: #C19771;

        --msg-success: #256F4A;
        --msg-success-soft: #EAF7EF;

        --msg-danger: #B42318;

        --msg-shadow:
            0 8px 24px rgba(86, 28, 23, 0.055);

        --msg-shadow-hover:
            0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .lm-page {
        display: grid;
        gap: 18px;

        width: 100%;

        color: var(--msg-text);

        font-family:
            "DM Sans",
            Poppins,
            system-ui,
            sans-serif;
    }

    .lm-page * {
        box-sizing: border-box;
    }

    /* =====================================================
       HEADER
    ====================================================== */

    .lm-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;

        padding: 32px 36px;

        border: 1px solid var(--msg-border);
        border-radius: 28px;

        background:
            radial-gradient(
                circle at 94% 10%,
                rgba(193, 151, 113, 0.24),
                transparent 30%
            ),
            radial-gradient(
                circle at 8% 16%,
                rgba(86, 28, 23, 0.055),
                transparent 30%
            ),
            linear-gradient(
                135deg,
                #FFFDF9 0%,
                #F6EFE7 58%,
                #EFE7DE 100%
            );

        box-shadow: var(--msg-shadow);
    }

    .lm-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        color: var(--msg-maroon);

        font-size: 10px;
        font-weight: 950;
        letter-spacing: 0.20em;
        text-transform: uppercase;
    }

    .lm-eyebrow::before {
        width: 24px;
        height: 1px;

        background: currentColor;

        content: "";
    }

    .lm-hero h1 {
        margin: 10px 0 0;

        color: var(--msg-text);

        font-family:
            "Instrument Serif",
            Georgia,
            serif;

        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: 0.94;
        letter-spacing: -0.055em;
    }

    .lm-hero p {
        max-width: 700px;
        margin: 13px 0 0;

        color: var(--msg-muted);

        font-size: 12px;
        line-height: 1.7;
    }

    .lm-hero-badge {
        display: inline-flex;
        min-height: 32px;
        align-items: center;
        gap: 7px;

        padding: 0 12px;

        border: 1px solid #CFE8DA;
        border-radius: 999px;

        background: var(--msg-success-soft);
        color: var(--msg-success);

        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .lm-hero-badge::before {
        width: 7px;
        height: 7px;

        border-radius: 999px;

        background: currentColor;

        content: "";
    }

    /* =====================================================
       CHAT SHELL
    ====================================================== */

    .lm-shell {
        display: grid;
        grid-template-columns: 340px minmax(0, 1fr);

        min-height: 700px;
        overflow: hidden;

        border: 1px solid var(--msg-border);
        border-radius: 24px;

        background: var(--msg-card);

        box-shadow: var(--msg-shadow);
    }

    /* =====================================================
       LEFT SIDEBAR
    ====================================================== */

    .lm-conversations {
        min-width: 0;

        border-right: 1px solid var(--msg-border);

        background: var(--msg-card);
    }

    .lm-conversations-head {
        padding: 20px;

        border-bottom: 1px solid var(--msg-border);

        background:
            radial-gradient(
                circle at 96% 6%,
                rgba(193, 151, 113, 0.13),
                transparent 30%
            ),
            linear-gradient(
                135deg,
                #FFFDF9 0%,
                #F8F0E8 100%
            );
    }

    .lm-conversations-head h2 {
        margin: 0;

        color: var(--msg-text);

        font-family:
            "Instrument Serif",
            Georgia,
            serif;

        font-size: 30px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .lm-conversations-head p {
        margin: 7px 0 0;

        color: var(--msg-muted);

        font-size: 10px;
    }

    .lm-search {
        position: relative;

        margin-top: 16px;
    }

    .lm-search svg {
        position: absolute;
        top: 50%;
        left: 13px;

        width: 16px;
        height: 16px;

        color: var(--msg-muted);

        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;

        transform: translateY(-50%);

        pointer-events: none;
    }

    .lm-search input {
        width: 100%;
        min-height: 42px;

        padding: 0 14px 0 40px;

        border: 1px solid var(--msg-border);
        border-radius: 13px;

        background: var(--msg-bg-soft);
        color: var(--msg-text);

        font-size: 10px;
        font-weight: 750;

        outline: none;

        transition: 150ms ease;
    }

    .lm-search input::placeholder {
        color: var(--msg-muted-light);
    }

    .lm-search input:focus {
        border-color: var(--msg-tan);
        background: #FFFFFF;

        box-shadow:
            0 0 0 4px rgba(86, 28, 23, 0.07);
    }

    /* =====================================================
       CONVERSATION ROW
    ====================================================== */

    .lm-chat-list {
        display: grid;
    }

    .lm-chat-user {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 12px;

        width: 100%;

        padding: 15px 17px;

        border: 0;
        border-bottom: 1px solid var(--msg-border);

        background: transparent;
        color: var(--msg-text);

        font: inherit;
        text-align: left;

        cursor: pointer;

        transition: 150ms ease;
    }

    .lm-chat-user:hover {
        background: var(--msg-bg-soft);
    }

    .lm-chat-user.is-active {
        background: var(--msg-bg-warm);

        box-shadow:
            inset 3px 0 0 var(--msg-maroon);
    }

    .lm-avatar {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;

        border: 1px solid #E6C7BE;
        border-radius: 14px;

        background: var(--msg-bg-warm);
        color: var(--msg-maroon);
    }

    .lm-avatar svg {
        width: 19px;
        height: 19px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .lm-chat-copy {
        min-width: 0;
    }

    .lm-chat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .lm-chat-top strong {
        min-width: 0;
        overflow: hidden;

        color: var(--msg-text);

        font-size: 11px;
        font-weight: 950;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .lm-chat-top time {
        flex: 0 0 auto;

        color: var(--msg-muted-light);

        font-size: 8px;
        font-weight: 700;
    }

    .lm-chat-role {
        display: inline-flex;
        margin-top: 4px;

        color: var(--msg-maroon);

        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .lm-chat-preview {
        display: block;
        overflow: hidden;

        margin-top: 6px;

        color: var(--msg-muted);

        font-size: 9px;
        line-height: 1.45;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .lm-no-results {
        display: none;

        padding: 30px 20px;

        color: var(--msg-muted);

        font-size: 10px;
        text-align: center;
    }

    /* =====================================================
       CHAT AREA
    ====================================================== */

    .lm-chat-area {
        display: flex;
        min-width: 0;
        flex-direction: column;

        background: var(--msg-card);
    }

    /* =====================================================
       EMPTY STATE
    ====================================================== */

    .lm-empty {
        display: flex;
        min-height: 600px;
        flex: 1;
        align-items: center;
        justify-content: center;

        padding: 30px;

        background:
            radial-gradient(
                circle at 50% 44%,
                rgba(193, 151, 113, 0.10),
                transparent 28%
            ),
            var(--msg-card);
    }

    .lm-empty-content {
        max-width: 340px;

        text-align: center;
    }

    .lm-empty-icon {
        display: grid;
        width: 72px;
        height: 72px;
        place-items: center;

        margin-inline: auto;

        border: 1px solid #E6C7BE;
        border-radius: 24px;

        background: var(--msg-bg-warm);
        color: var(--msg-maroon);
    }

    .lm-empty-icon svg {
        width: 28px;
        height: 28px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.65;
    }

    .lm-empty h2 {
        margin: 18px 0 0;

        color: var(--msg-text);

        font-family:
            "Instrument Serif",
            Georgia,
            serif;

        font-size: 31px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.035em;
    }

    .lm-empty p {
        margin: 10px 0 0;

        color: var(--msg-muted);

        font-size: 11px;
        line-height: 1.6;
    }

    /* =====================================================
       CHAT WINDOW
    ====================================================== */

    .lm-window {
        display: none;
        min-height: 0;
        flex: 1;
        flex-direction: column;
    }

    .lm-window.is-open {
        display: flex;
    }

    .lm-chat-header {
        display: flex;
        min-height: 82px;
        align-items: center;
        justify-content: space-between;
        gap: 16px;

        padding: 16px 20px;

        border-bottom: 1px solid var(--msg-border);

        background:
            radial-gradient(
                circle at 96% 6%,
                rgba(193, 151, 113, 0.12),
                transparent 30%
            ),
            linear-gradient(
                135deg,
                #FFFDF9 0%,
                #F8F0E8 100%
            );
    }

    .lm-chat-person {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 12px;
    }

    .lm-chat-person-copy {
        min-width: 0;
    }

    .lm-chat-person-copy h2 {
        margin: 0;

        overflow: hidden;

        color: var(--msg-text);

        font-size: 12px;
        font-weight: 950;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .lm-chat-person-copy p {
        margin: 4px 0 0;

        color: var(--msg-muted);

        font-size: 9px;
        font-weight: 700;
    }

    .lm-status {
        display: inline-flex;
        min-height: 27px;
        align-items: center;
        gap: 6px;

        padding: 0 10px;

        border: 1px solid #CFE8DA;
        border-radius: 999px;

        background: var(--msg-success-soft);
        color: var(--msg-success);

        font-size: 9px;
        font-weight: 900;
        white-space: nowrap;
    }

    .lm-status::before {
        width: 6px;
        height: 6px;

        border-radius: 999px;

        background: currentColor;

        content: "";
    }

    .lm-status.is-offline {
        border-color: var(--msg-border);
        background: var(--msg-bg-soft);
        color: var(--msg-muted);
    }

    /* =====================================================
       MESSAGES
    ====================================================== */

    .lm-message-box {
        display: flex;
        min-height: 0;
        flex: 1;
        flex-direction: column;
        gap: 12px;

        overflow-y: auto;

        padding: 24px;

        background:
            linear-gradient(
                180deg,
                #FBF7F2 0%,
                #FFFDF9 100%
            );
    }

    .lm-message {
        display: flex;
        flex-direction: column;

        max-width: min(72%, 560px);
    }

    .lm-message.is-received {
        align-self: flex-start;
    }

    .lm-message.is-sent {
        align-self: flex-end;
        align-items: flex-end;
    }

    .lm-message-bubble {
        padding: 11px 14px;

        border: 1px solid var(--msg-border);
        border-radius: 16px 16px 16px 5px;

        background: var(--msg-card);
        color: var(--msg-text);

        font-size: 10px;
        font-weight: 650;
        line-height: 1.6;

        box-shadow:
            0 5px 16px rgba(86, 28, 23, 0.04);
    }

    .lm-message.is-sent .lm-message-bubble {
        border-color: var(--msg-maroon);

        border-radius: 16px 16px 5px 16px;

        background: var(--msg-maroon);
        color: #FFFFFF;
    }

    .lm-message time {
        display: block;

        margin-top: 5px;

        color: var(--msg-muted-light);

        font-size: 8px;
        font-weight: 700;
    }

    /* =====================================================
       COMPOSER
    ====================================================== */

    .lm-composer {
        padding: 15px 18px;

        border-top: 1px solid var(--msg-border);

        background: var(--msg-card);
    }

    .lm-composer-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px;
    }

    .lm-message-input {
        width: 100%;
        min-height: 44px;

        padding: 0 16px;

        border: 1px solid var(--msg-border);
        border-radius: 14px;

        background: var(--msg-bg-soft);
        color: var(--msg-text);

        font-size: 10px;
        font-weight: 700;

        outline: none;

        transition: 150ms ease;
    }

    .lm-message-input::placeholder {
        color: var(--msg-muted-light);
    }

    .lm-message-input:focus {
        border-color: var(--msg-tan);
        background: #FFFFFF;

        box-shadow:
            0 0 0 4px rgba(86, 28, 23, 0.07);
    }

    .lm-send {
        display: inline-flex;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        gap: 8px;

        padding: 0 18px;

        border: 1px solid var(--msg-maroon);
        border-radius: 14px;

        background: var(--msg-maroon);
        color: #FFFFFF;

        font-size: 10px;
        font-weight: 900;

        cursor: pointer;

        box-shadow:
            0 10px 22px rgba(86, 28, 23, 0.14);

        transition: 150ms ease;
    }

    .lm-send:hover {
        transform: translateY(-1px);

        background: var(--msg-maroon-dark);
        border-color: var(--msg-maroon-dark);
    }

    .lm-send svg {
        width: 15px;
        height: 15px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 1050px) {
        .lm-shell {
            grid-template-columns: 300px minmax(0, 1fr);
        }
    }

    @media (max-width: 780px) {
        .lm-hero {
            align-items: flex-start;
            flex-direction: column;

            padding: 26px 22px;
        }

        .lm-shell {
            grid-template-columns: 1fr;
        }

        .lm-conversations {
            border-right: 0;
            border-bottom: 1px solid var(--msg-border);
        }

        .lm-chat-list {
            max-height: 340px;

            overflow-y: auto;
        }

        .lm-empty {
            min-height: 430px;
        }

        .lm-message {
            max-width: 86%;
        }
    }

    @media (max-width: 520px) {
        .lm-message-box {
            padding: 16px;
        }

        .lm-composer {
            padding: 12px;
        }

        .lm-composer-row {
            grid-template-columns: 1fr;
        }

        .lm-send {
            width: 100%;
        }
    }

    /* =====================================================
       DARK MODE
    ====================================================== */

    html.dark .lm-page {
        color: #F5EFE8;
    }

    html.dark .lm-hero,
    html.dark .lm-shell,
    html.dark .lm-conversations,
    html.dark .lm-chat-area {
        background:
            radial-gradient(
                circle at 94% 8%,
                rgba(193, 151, 113, 0.07),
                transparent 28%
            ),
            linear-gradient(
                180deg,
                #211B17 0%,
                #1A1412 100%
            ) !important;

        border-color: #3B2E27 !important;

        box-shadow: none !important;
    }

    html.dark .lm-conversations-head,
    html.dark .lm-chat-header {
        background:
            radial-gradient(
                circle at 96% 6%,
                rgba(193, 151, 113, 0.07),
                transparent 30%
            ),
            #1D1715 !important;

        border-color: #3B2E27 !important;
    }

    html.dark .lm-hero h1,
    html.dark .lm-conversations-head h2,
    html.dark .lm-chat-top strong,
    html.dark .lm-empty h2,
    html.dark .lm-chat-person-copy h2 {
        color: #F5EFE8 !important;
    }

    html.dark .lm-hero p,
    html.dark .lm-conversations-head p,
    html.dark .lm-chat-preview,
    html.dark .lm-empty p,
    html.dark .lm-chat-person-copy p {
        color: #AFA19A !important;
    }

    html.dark .lm-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .lm-search input,
    html.dark .lm-message-input {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .lm-search input:focus,
    html.dark .lm-message-input:focus {
        border-color: #60463A !important;
        background: #211B17 !important;
    }

    html.dark .lm-chat-user {
        border-color: #30231F !important;
    }

    html.dark .lm-chat-user:hover {
        background: #241817 !important;
    }

    html.dark .lm-chat-user.is-active {
        background: #351817 !important;

        box-shadow:
            inset 3px 0 0 #A84538 !important;
    }

    html.dark .lm-avatar,
    html.dark .lm-empty-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .lm-chat-role {
        color: #EBA99D !important;
    }

    html.dark .lm-message-box {
        background:
            linear-gradient(
                180deg,
                #15100F 0%,
                #1A1412 100%
            ) !important;
    }

    html.dark .lm-message-bubble {
        background: #211B17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;

        box-shadow: none !important;
    }

    html.dark .lm-message.is-sent .lm-message-bubble {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .lm-composer {
        background: #1A1412 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .lm-send {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .lm-send:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }
</style>


<div class="lm-page">

    {{-- =====================================================
       HERO
    ====================================================== --}}

    <section class="lm-hero">
        <div>
            <span class="lm-eyebrow">
                Communication Center
            </span>

            <h1>
                Messages
            </h1>

            <p>
                Manage conversations with riders, sellers, and buyers from one
                organized logistics communication center.
            </p>
        </div>

        <span class="lm-hero-badge">
            Communication Active
        </span>
    </section>


    {{-- =====================================================
       CHAT INTERFACE
    ====================================================== --}}

    <section class="lm-shell">

        {{-- LEFT --}}
        <aside class="lm-conversations">

            <header class="lm-conversations-head">
                <h2>
                    Conversations
                </h2>

                <p>
                    Riders, sellers, and buyers
                </p>

                <div class="lm-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>

                    <input
                        type="search"
                        id="conversationSearch"
                        placeholder="Search messages..."
                        autocomplete="off"
                    >
                </div>
            </header>

            <div class="lm-chat-list" id="conversationList">

                @foreach($conversations as $key => $chat)
                    @php
                        $lastMessage = $chat['messages'][count($chat['messages']) - 1];
                    @endphp

                    <button
                        type="button"
                        class="lm-chat-user"
                        id="user-{{ $key }}"
                        data-chat-key="{{ $key }}"
                        data-search="{{ mb_strtolower($chat['name'] . ' ' . $chat['type'] . ' ' . $lastMessage['text']) }}"
                    >
                        <span class="lm-avatar" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                {!! $conversationIcons[$chat['avatar']] ?? $conversationIcons['buyer'] !!}
                            </svg>
                        </span>

                        <span class="lm-chat-copy">

                            <span class="lm-chat-top">
                                <strong>
                                    {{ $chat['name'] }}
                                </strong>

                                <time>
                                    {{ $lastMessage['time'] }}
                                </time>
                            </span>

                            <span class="lm-chat-role">
                                {{ $chat['type'] }}
                            </span>

                            <span class="lm-chat-preview">
                                {{ $lastMessage['text'] }}
                            </span>

                        </span>
                    </button>
                @endforeach

            </div>

            <div class="lm-no-results" id="conversationEmpty">
                No conversations found.
            </div>

        </aside>


        {{-- RIGHT --}}
        <section class="lm-chat-area">

            {{-- EMPTY --}}
            <div class="lm-empty" id="emptyChat">
                <div class="lm-empty-content">

                    <span class="lm-empty-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </span>

                    <h2>
                        Select a conversation
                    </h2>

                    <p>
                        Choose a rider, seller, or buyer from the conversation
                        list to view and continue the discussion.
                    </p>

                </div>
            </div>


            {{-- CHAT WINDOW --}}
            <div class="lm-window" id="chatWindow">

                <header class="lm-chat-header">

                    <div class="lm-chat-person">

                        <span class="lm-avatar" id="chatAvatar"></span>

                        <div class="lm-chat-person-copy">
                            <h2 id="chatName"></h2>
                            <p id="chatMeta"></p>
                        </div>

                    </div>

                    <span class="lm-status" id="chatStatus">
                        Online
                    </span>

                </header>


                <div class="lm-message-box" id="messageBox"></div>


                <footer class="lm-composer">

                    <form class="lm-composer-row" id="messageForm">

                        <input
                            type="text"
                            id="messageInput"
                            class="lm-message-input"
                            placeholder="Type a message..."
                            autocomplete="off"
                        >

                        <button
                            type="submit"
                            class="lm-send"
                        >
                            Send

                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m4 4 16 8-16 8 3-8-3-8Z"></path>
                                <path d="M7 12h13"></path>
                            </svg>
                        </button>

                    </form>

                </footer>

            </div>

        </section>

    </section>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const conversations = @json($conversations);

    const conversationList = document.getElementById('conversationList');
    const conversationSearch = document.getElementById('conversationSearch');
    const conversationEmpty = document.getElementById('conversationEmpty');

    const emptyChat = document.getElementById('emptyChat');
    const chatWindow = document.getElementById('chatWindow');

    const chatAvatar = document.getElementById('chatAvatar');
    const chatName = document.getElementById('chatName');
    const chatMeta = document.getElementById('chatMeta');
    const chatStatus = document.getElementById('chatStatus');

    const messageBox = document.getElementById('messageBox');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');

    let activeChat = null;

    const avatarIcons = {
        rider: `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"></path>
                <rect x="9" y="11" width="14" height="10" rx="2"></rect>
                <circle cx="12" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
            </svg>
        `,

        seller: `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
        `,

        buyer: `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="8" r="4"></circle>
                <path d="M4 21c0-5 3-8 8-8s8 3 8 8"></path>
            </svg>
        `
    };

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value;
        return div.innerHTML;
    }

    function getCurrentTime() {
        return new Intl.DateTimeFormat('en-PH', {
            hour: 'numeric',
            minute: '2-digit'
        }).format(new Date());
    }

    function renderMessages() {
        if (!activeChat || !conversations[activeChat]) {
            return;
        }

        const messages = conversations[activeChat].messages;

        messageBox.innerHTML = messages.map(function (message) {
            const sent = message.type === 'sent';

            return `
                <article class="lm-message ${sent ? 'is-sent' : 'is-received'}">
                    <div class="lm-message-bubble">
                        ${escapeHtml(message.text)}
                    </div>

                    <time>
                        ${escapeHtml(message.time)}
                    </time>
                </article>
            `;
        }).join('');

        requestAnimationFrame(function () {
            messageBox.scrollTop = messageBox.scrollHeight;
        });
    }

    function openChat(key) {
        const chat = conversations[key];

        if (!chat) {
            return;
        }

        activeChat = key;

        emptyChat.style.display = 'none';
        chatWindow.classList.add('is-open');

        document.querySelectorAll('.lm-chat-user').forEach(function (button) {
            button.classList.toggle(
                'is-active',
                button.dataset.chatKey === key
            );
        });

        chatAvatar.innerHTML =
            avatarIcons[chat.avatar] || avatarIcons.buyer;

        chatName.textContent = chat.name;
        chatMeta.textContent = chat.type + ' · ' + chat.status;

        chatStatus.textContent = chat.status;
        chatStatus.classList.toggle(
            'is-offline',
            chat.status.toLowerCase() !== 'online'
        );

        renderMessages();

        messageInput.focus();
    }

    conversationList?.addEventListener('click', function (event) {
        const button = event.target.closest('.lm-chat-user');

        if (!button) {
            return;
        }

        openChat(button.dataset.chatKey);
    });

    conversationSearch?.addEventListener('input', function () {
        const term = this.value
            .trim()
            .toLowerCase();

        let visible = 0;

        document.querySelectorAll('.lm-chat-user').forEach(function (button) {
            const matches =
                !term ||
                button.dataset.search.includes(term);

            button.style.display =
                matches ? '' : 'none';

            if (matches) {
                visible++;
            }
        });

        conversationEmpty.style.display =
            visible === 0 ? 'block' : 'none';
    });

    messageForm?.addEventListener('submit', function (event) {
        event.preventDefault();

        if (!activeChat) {
            return;
        }

        const value = messageInput.value.trim();

        if (!value) {
            messageInput.focus();
            return;
        }

        conversations[activeChat].messages.push({
            type: 'sent',
            text: value,
            time: getCurrentTime()
        });

        messageInput.value = '';

        renderMessages();

        const conversationButton =
            document.querySelector(
                `[data-chat-key="${activeChat}"]`
            );

        const preview =
            conversationButton?.querySelector(
                '.lm-chat-preview'
            );

        const time =
            conversationButton?.querySelector(
                '.lm-chat-top time'
            );

        if (preview) {
            preview.textContent = value;
        }

        if (time) {
            time.textContent = getCurrentTime();
        }

        messageInput.focus();
    });

    /*
     * Auto-open the first chat on desktop-sized screens.
     * On smaller screens the empty state remains until selected.
     */
    if (window.innerWidth >= 781) {
        const firstChat =
            document.querySelector('.lm-chat-user');

        if (firstChat) {
            openChat(firstChat.dataset.chatKey);
        }
    }
});
</script>

@endsection