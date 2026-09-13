@extends('logistics.app')

@section('title', 'Messages — LIKHAE Logistics')

@php
    $conversations = [
        'juan' => [
            'name' => 'Juan Dela Cruz',
            'type' => 'Rider',
            'status' => 'Online',
            'avatar' => 'rider',
            'unread' => 2,
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
            'unread' => 1,
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
            'unread' => 0,
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

    $icons = [
        'rider' => '
            <circle cx="8" cy="7" r="3"/>
            <path d="M3 19c0-3 2-5 5-5"/>
            <path d="M14 7h7"/>
            <path d="M17.5 3.5v7"/>
        ',

        'seller' => '
            <path d="M3 10h18"/>
            <path d="M5 10v10h14V10"/>
            <path d="M4 4h16l1 6H3z"/>
            <path d="M9 14h6"/>
        ',

        'buyer' => '
            <circle cx="12" cy="8" r="4"/>
            <path d="M4 21c0-5 3-8 8-8s8 3 8 8"/>
        ',

        'chat' => '
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        ',

        'search' => '
            <circle cx="11" cy="11" r="7"/>
            <path d="m20 20-3.5-3.5"/>
        ',

        'send' => '
            <path d="m22 2-7 20-4-9-9-4z"/>
            <path d="M22 2 11 13"/>
        ',

        'paperclip' => '
            <path d="m21.4 11.6-8.5 8.5a6 6 0 0 1-8.5-8.5l9.2-9.2a4 4 0 1 1 5.7 5.7l-9.2 9.2a2 2 0 0 1-2.8-2.8l8.5-8.5"/>
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

        --msg-shadow: 0 8px 24px rgba(86, 28, 23, 0.055);
        --msg-shadow-hover: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .msg-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--msg-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .msg-page * {
        box-sizing: border-box;
    }

    .msg-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--msg-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--msg-shadow);
    }

    .msg-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--msg-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .msg-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .msg-hero h1 {
        margin: 10px 0 0;
        color: var(--msg-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .msg-hero p {
        max-width: 700px;
        margin: 13px 0 0;
        color: var(--msg-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .msg-status-pill {
        display: inline-flex;
        min-height: 30px;
        align-items: center;
        gap: 7px;
        padding: 0 11px;
        border: 1px solid #CFE8DA;
        border-radius: 999px;
        background: var(--msg-success-soft);
        color: var(--msg-success);
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .msg-status-pill::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .msg-shell {
        display: grid;
        min-height: 720px;
        overflow: hidden;
        border: 1px solid var(--msg-border);
        border-radius: 22px;
        background: var(--msg-card);
        box-shadow: var(--msg-shadow);
        grid-template-columns: 340px minmax(0, 1fr);
    }

    .msg-sidebar {
        min-width: 0;
        border-right: 1px solid var(--msg-border);
        background: var(--msg-card);
    }

    .msg-sidebar-head {
        padding: 18px;
        border-bottom: 1px solid var(--msg-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.11), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .msg-sidebar-head h2 {
        margin: 0;
        color: var(--msg-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 26px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .msg-sidebar-head p {
        margin: 6px 0 0;
        color: var(--msg-muted);
        font-size: 8px;
        line-height: 1.5;
    }

    .msg-search {
        position: relative;
        margin-top: 14px;
    }

    .msg-search svg {
        position: absolute;
        top: 50%;
        left: 13px;
        width: 15px;
        height: 15px;
        color: var(--msg-muted);
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .msg-search input {
        width: 100%;
        min-height: 40px;
        padding: 0 14px 0 39px;
        border: 1px solid var(--msg-border);
        border-radius: 12px;
        background: var(--msg-bg-soft);
        color: var(--msg-text);
        font-size: 10px;
        font-weight: 750;
        outline: none;
        transition: 150ms ease;
    }

    .msg-search input::placeholder {
        color: var(--msg-muted-light);
    }

    .msg-search input:focus {
        border-color: var(--msg-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86,28,23,.07);
    }

    .msg-list {
        display: grid;
        max-height: 660px;
        overflow-y: auto;
    }

    .msg-list::-webkit-scrollbar,
    .msg-thread::-webkit-scrollbar {
        width: 7px;
    }

    .msg-list::-webkit-scrollbar-thumb,
    .msg-thread::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: #D9C9BC;
    }

    .msg-user {
        display: grid;
        grid-template-columns: auto minmax(0,1fr);
        gap: 12px;
        width: 100%;
        padding: 14px 16px;
        border: 0;
        border-bottom: 1px solid var(--msg-border);
        background: transparent;
        color: inherit;
        text-align: left;
        cursor: pointer;
        transition: 150ms ease;
    }

    .msg-user:hover {
        background: var(--msg-bg-soft);
    }

    .msg-user.is-active {
        position: relative;
        background: var(--msg-bg-warm);
    }

    .msg-user.is-active::before {
        position: absolute;
        top: 10px;
        bottom: 10px;
        left: 0;
        width: 3px;
        border-radius: 0 999px 999px 0;
        background: var(--msg-maroon);
        content: "";
    }

    .msg-avatar {
        position: relative;
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 999px;
        background: var(--msg-bg-warm);
        color: var(--msg-maroon);
    }

    .msg-avatar svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .msg-online-dot {
        position: absolute;
        right: 0;
        bottom: 1px;
        width: 10px;
        height: 10px;
        border: 2px solid var(--msg-card);
        border-radius: 999px;
        background: var(--msg-success);
    }

    .msg-user-main {
        min-width: 0;
    }

    .msg-user-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .msg-user-name {
        min-width: 0;
        overflow: hidden;
        color: var(--msg-text);
        font-size: 10px;
        font-weight: 950;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .msg-user-time {
        flex: 0 0 auto;
        color: var(--msg-muted-light);
        font-size: 7px;
        font-weight: 750;
    }

    .msg-user-type {
        display: block;
        margin-top: 3px;
        color: var(--msg-maroon);
        font-size: 8px;
        font-weight: 850;
    }

    .msg-user-bottom {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
    }

    .msg-user-preview {
        min-width: 0;
        flex: 1;
        overflow: hidden;
        color: var(--msg-muted);
        font-size: 8px;
        line-height: 1.35;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .msg-unread {
        display: inline-flex;
        min-width: 18px;
        height: 18px;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
        border-radius: 999px;
        background: var(--msg-maroon);
        color: #FFFFFF;
        font-size: 7px;
        font-weight: 950;
    }

    .msg-panel {
        min-width: 0;
        display: flex;
        flex-direction: column;
        background: var(--msg-card);
    }

    .msg-empty {
        display: grid;
        flex: 1;
        min-height: 650px;
        place-items: center;
        padding: 30px;
        text-align: center;
    }

    .msg-empty-icon {
        display: grid;
        width: 70px;
        height: 70px;
        place-items: center;
        margin-inline: auto;
        border: 1px solid #E6C7BE;
        border-radius: 24px;
        background: var(--msg-bg-warm);
        color: var(--msg-maroon);
        box-shadow: var(--msg-shadow);
    }

    .msg-empty-icon svg {
        width: 28px;
        height: 28px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.6;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .msg-empty h2 {
        margin: 16px 0 0;
        color: var(--msg-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 30px;
        font-weight: 400;
        letter-spacing: -.04em;
    }

    .msg-empty p {
        margin: 7px 0 0;
        color: var(--msg-muted);
        font-size: 9px;
    }

    .msg-window {
        display: none;
        min-height: 720px;
        flex: 1;
        flex-direction: column;
    }

    .msg-window.is-open {
        display: flex;
    }

    .msg-chat-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 15px 18px;
        border-bottom: 1px solid var(--msg-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.09), transparent 30%),
            #FFFDF9;
    }

    .msg-chat-person {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 11px;
    }

    .msg-chat-copy {
        min-width: 0;
    }

    .msg-chat-copy h2 {
        margin: 0;
        overflow: hidden;
        color: var(--msg-text);
        font-size: 11px;
        font-weight: 950;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .msg-chat-copy p {
        margin: 4px 0 0;
        color: var(--msg-muted);
        font-size: 8px;
        font-weight: 700;
    }

    .msg-chat-presence {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        gap: 6px;
        padding: 0 9px;
        border: 1px solid #CFE8DA;
        border-radius: 999px;
        background: var(--msg-success-soft);
        color: var(--msg-success);
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .msg-chat-presence::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .msg-chat-presence.is-offline {
        border-color: var(--msg-border);
        background: var(--msg-bg-soft);
        color: var(--msg-muted);
    }

    .msg-thread {
        display: flex;
        flex: 1;
        min-height: 0;
        flex-direction: column;
        gap: 11px;
        overflow-y: auto;
        padding: 22px;
        background:
            radial-gradient(circle at 90% 8%, rgba(193,151,113,.08), transparent 24%),
            var(--msg-bg);
    }

    .msg-row {
        display: flex;
        width: 100%;
    }

    .msg-row.is-sent {
        justify-content: flex-end;
    }

    .msg-row.is-received {
        justify-content: flex-start;
    }

    .msg-bubble {
        max-width: min(560px, 78%);
        padding: 11px 13px 9px;
        border-radius: 16px;
        font-size: 9px;
        line-height: 1.55;
        box-shadow: 0 5px 14px rgba(86,28,23,.04);
    }

    .msg-row.is-sent .msg-bubble {
        border-bottom-right-radius: 5px;
        background: var(--msg-maroon);
        color: #FFFFFF;
    }

    .msg-row.is-received .msg-bubble {
        border: 1px solid var(--msg-border);
        border-bottom-left-radius: 5px;
        background: var(--msg-card);
        color: var(--msg-text);
    }

    .msg-bubble-time {
        display: block;
        margin-top: 5px;
        font-size: 7px;
        font-weight: 700;
        opacity: .62;
    }

    .msg-composer {
        padding: 14px 16px;
        border-top: 1px solid var(--msg-border);
        background: var(--msg-card);
    }

    .msg-composer-inner {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 8px;
    }

    .msg-icon-btn,
    .msg-send-btn {
        display: inline-grid;
        height: 40px;
        place-items: center;
        border: 1px solid transparent;
        border-radius: 12px;
        cursor: pointer;
        transition: 150ms ease;
    }

    .msg-icon-btn {
        width: 40px;
        border-color: var(--msg-border);
        background: var(--msg-bg-soft);
        color: var(--msg-maroon);
    }

    .msg-icon-btn:hover {
        background: var(--msg-bg-warm);
        border-color: var(--msg-tan);
    }

    .msg-send-btn {
        grid-auto-flow: column;
        gap: 7px;
        padding: 0 14px;
        background: var(--msg-maroon);
        color: #FFFFFF;
        font-size: 9px;
        font-weight: 900;
    }

    .msg-send-btn:hover {
        background: var(--msg-maroon-dark);
    }

    .msg-icon-btn svg,
    .msg-send-btn svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .msg-composer input {
        width: 100%;
        min-height: 40px;
        padding: 0 13px;
        border: 1px solid var(--msg-border);
        border-radius: 12px;
        background: var(--msg-bg-soft);
        color: var(--msg-text);
        font-size: 9px;
        font-weight: 700;
        outline: none;
        transition: 150ms ease;
    }

    .msg-composer input::placeholder {
        color: var(--msg-muted-light);
    }

    .msg-composer input:focus {
        border-color: var(--msg-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86,28,23,.07);
    }

    .msg-no-results {
        display: none;
        padding: 28px 16px;
        color: var(--msg-muted);
        font-size: 9px;
        text-align: center;
    }

    .msg-no-results.is-visible {
        display: block;
    }

    @media (max-width: 1050px) {
        .msg-shell {
            grid-template-columns: 300px minmax(0, 1fr);
        }
    }

    @media (max-width: 820px) {
        .msg-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .msg-shell {
            grid-template-columns: 1fr;
        }

        .msg-sidebar {
            border-right: 0;
            border-bottom: 1px solid var(--msg-border);
        }

        .msg-list {
            max-height: 320px;
        }

        .msg-empty,
        .msg-window {
            min-height: 560px;
        }
    }

    @media (max-width: 560px) {
        .msg-chat-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .msg-composer-inner {
            grid-template-columns: minmax(0,1fr) auto;
        }

        .msg-icon-btn {
            display: none;
        }

        .msg-bubble {
            max-width: 90%;
        }
    }

    html.dark .msg-page {
        color: #F5EFE8;
    }

    html.dark .msg-hero,
    html.dark .msg-shell,
    html.dark .msg-sidebar,
    html.dark .msg-panel {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .msg-sidebar-head,
    html.dark .msg-chat-head {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .msg-hero h1,
    html.dark .msg-sidebar-head h2,
    html.dark .msg-user-name,
    html.dark .msg-empty h2,
    html.dark .msg-chat-copy h2,
    html.dark .msg-row.is-received .msg-bubble {
        color: #F5EFE8 !important;
    }

    html.dark .msg-hero p,
    html.dark .msg-sidebar-head p,
    html.dark .msg-user-time,
    html.dark .msg-user-preview,
    html.dark .msg-empty p,
    html.dark .msg-chat-copy p {
        color: #AFA19A !important;
    }

    html.dark .msg-eyebrow,
    html.dark .msg-user-type {
        color: #EBA99D !important;
    }

    html.dark .msg-search input,
    html.dark .msg-composer input {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .msg-search input:focus,
    html.dark .msg-composer input:focus {
        background: #211B17 !important;
        border-color: #60463A !important;
    }

    html.dark .msg-user {
        border-color: #30231F !important;
    }

    html.dark .msg-user:hover {
        background: #241817 !important;
    }

    html.dark .msg-user.is-active {
        background: #2D1816 !important;
    }

    html.dark .msg-avatar,
    html.dark .msg-empty-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .msg-online-dot {
        border-color: #1A1412 !important;
    }

    html.dark .msg-thread {
        background:
            radial-gradient(circle at 90% 8%, rgba(193,151,113,.04), transparent 24%),
            #171210 !important;
    }

    html.dark .msg-row.is-received .msg-bubble {
        background: #211B17 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .msg-composer {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .msg-icon-btn {
        background: #211B17 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .msg-send-btn {
        background: #A84538 !important;
    }

    html.dark .msg-send-btn:hover {
        background: #B84B43 !important;
    }

    html.dark .msg-user.is-active::before,
    html.dark .msg-unread {
        background: #A84538 !important;
    }
</style>

<div class="msg-page">

    <section class="msg-hero">
        <div>
            <span class="msg-eyebrow">
                Communication Center
            </span>

            <h1>
                Messages
            </h1>

            <p>
                Coordinate conversations between logistics, riders, sellers,
                and buyers from one centralized workspace.
            </p>
        </div>

        <span class="msg-status-pill">
            Messaging Active
        </span>
    </section>

    <section class="msg-shell">

        <aside class="msg-sidebar">
            <div class="msg-sidebar-head">
                <h2>
                    Conversations
                </h2>

                <p>
                    Search and open a recent logistics conversation.
                </p>

                <div class="msg-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        {!! $icons['search'] !!}
                    </svg>

                    <input
                        type="search"
                        id="messageSearch"
                        placeholder="Search messages..."
                        autocomplete="off"
                    >
                </div>
            </div>

            <div class="msg-list" id="conversationList">
                @foreach($conversations as $key => $chat)
                    @php
                        $lastMessage = $chat['messages'][count($chat['messages']) - 1];
                    @endphp

                    <button
                        type="button"
                        class="msg-user"
                        data-chat-id="{{ $key }}"
                        data-search="{{ mb_strtolower(
                            $chat['name']
                            . ' '
                            . $chat['type']
                            . ' '
                            . $lastMessage['text']
                        ) }}"
                    >
                        <span class="msg-avatar">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons[$chat['avatar']] !!}
                            </svg>

                            @if($chat['status'] === 'Online')
                                <span class="msg-online-dot"></span>
                            @endif
                        </span>

                        <span class="msg-user-main">
                            <span class="msg-user-top">
                                <span class="msg-user-name">
                                    {{ $chat['name'] }}
                                </span>

                                <span class="msg-user-time">
                                    {{ $lastMessage['time'] }}
                                </span>
                            </span>

                            <span class="msg-user-type">
                                {{ $chat['type'] }}
                            </span>

                            <span class="msg-user-bottom">
                                <span class="msg-user-preview">
                                    {{ $lastMessage['text'] }}
                                </span>

                                @if($chat['unread'] > 0)
                                    <span class="msg-unread">
                                        {{ $chat['unread'] }}
                                    </span>
                                @endif
                            </span>
                        </span>
                    </button>
                @endforeach

                <div id="messageSearchEmpty" class="msg-no-results">
                    No conversations match your search.
                </div>
            </div>
        </aside>

        <section class="msg-panel">

            <div id="emptyChat" class="msg-empty">
                <div>
                    <span class="msg-empty-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            {!! $icons['chat'] !!}
                        </svg>
                    </span>

                    <h2>
                        Select a conversation
                    </h2>

                    <p>
                        Choose a rider, seller, or buyer to view the message thread.
                    </p>
                </div>
            </div>

            <div id="chatWindow" class="msg-window">

                <header class="msg-chat-head">
                    <div class="msg-chat-person">
                        <span id="chatAvatar" class="msg-avatar"></span>

                        <div class="msg-chat-copy">
                            <h2 id="chatName"></h2>
                            <p id="chatMeta"></p>
                        </div>
                    </div>

                    <span id="chatPresence" class="msg-chat-presence">
                        Online
                    </span>
                </header>

                <div id="messageBox" class="msg-thread"></div>

                <footer class="msg-composer">
                    <form id="messageComposer" class="msg-composer-inner">
                        <button
                            type="button"
                            class="msg-icon-btn"
                            aria-label="Attach file"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['paperclip'] !!}
                            </svg>
                        </button>

                        <input
                            id="messageInput"
                            type="text"
                            placeholder="Type a message..."
                            autocomplete="off"
                        >

                        <button
                            type="submit"
                            class="msg-send-btn"
                        >
                            Send

                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['send'] !!}
                            </svg>
                        </button>
                    </form>
                </footer>

            </div>

        </section>

    </section>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const conversations =
        @json($conversations);

    const avatarIcons = {
        rider: @json('<svg viewBox="0 0 24 24"><circle cx="8" cy="7" r="3"/><path d="M3 19c0-3 2-5 5-5"/><path d="M14 7h7"/><path d="M17.5 3.5v7"/></svg>'),

        seller: @json('<svg viewBox="0 0 24 24"><path d="M3 10h18"/><path d="M5 10v10h14V10"/><path d="M4 4h16l1 6H3z"/><path d="M9 14h6"/></svg>'),

        buyer: @json('<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-5 3-8 8-8s8 3 8 8"/></svg>')
    };

    const conversationButtons =
        Array.from(
            document.querySelectorAll('.msg-user')
        );

    const emptyChat =
        document.getElementById('emptyChat');

    const chatWindow =
        document.getElementById('chatWindow');

    const chatAvatar =
        document.getElementById('chatAvatar');

    const chatName =
        document.getElementById('chatName');

    const chatMeta =
        document.getElementById('chatMeta');

    const chatPresence =
        document.getElementById('chatPresence');

    const messageBox =
        document.getElementById('messageBox');

    const composer =
        document.getElementById('messageComposer');

    const input =
        document.getElementById('messageInput');

    const search =
        document.getElementById('messageSearch');

    const searchEmpty =
        document.getElementById('messageSearchEmpty');

    let activeChat = null;


    function createMessageBubble(message) {
        const row =
            document.createElement('div');

        row.className =
            'msg-row ' +
            (
                message.type === 'sent'
                    ? 'is-sent'
                    : 'is-received'
            );

        const bubble =
            document.createElement('div');

        bubble.className =
            'msg-bubble';

        const text =
            document.createElement('div');

        text.textContent =
            message.text;

        const time =
            document.createElement('span');

        time.className =
            'msg-bubble-time';

        time.textContent =
            message.time;

        bubble.appendChild(text);
        bubble.appendChild(time);
        row.appendChild(bubble);

        return row;
    }


    function renderMessages(chat) {
        if (!messageBox) {
            return;
        }

        messageBox.innerHTML = '';

        chat.messages.forEach(function (message) {
            messageBox.appendChild(
                createMessageBubble(message)
            );
        });

        requestAnimationFrame(function () {
            messageBox.scrollTop =
                messageBox.scrollHeight;
        });
    }


    function openChat(id) {
        const chat =
            conversations[id];

        if (!chat) {
            return;
        }

        activeChat = id;

        emptyChat?.style.setProperty(
            'display',
            'none'
        );

        chatWindow?.classList.add(
            'is-open'
        );

        conversationButtons.forEach(
            function (button) {
                button.classList.toggle(
                    'is-active',
                    button.dataset.chatId === id
                );

                if (
                    button.dataset.chatId === id
                ) {
                    button
                        .querySelector('.msg-unread')
                        ?.remove();
                }
            }
        );

        if (chatAvatar) {
            chatAvatar.innerHTML =
                avatarIcons[chat.avatar] || '';
        }

        if (chatName) {
            chatName.textContent =
                chat.name;
        }

        if (chatMeta) {
            chatMeta.textContent =
                chat.type + ' · ' + chat.status;
        }

        if (chatPresence) {
            chatPresence.textContent =
                chat.status;

            chatPresence.classList.toggle(
                'is-offline',
                chat.status !== 'Online'
            );
        }

        renderMessages(chat);

        input?.focus();
    }


    conversationButtons.forEach(
        function (button) {
            button.addEventListener(
                'click',
                function () {
                    openChat(
                        button.dataset.chatId
                    );
                }
            );
        }
    );


    composer?.addEventListener(
        'submit',
        function (event) {
            event.preventDefault();

            if (!activeChat) {
                return;
            }

            const value =
                (input?.value || '')
                    .trim();

            if (!value) {
                input?.focus();
                return;
            }

            conversations[activeChat]
                .messages
                .push({
                    type: 'sent',
                    text: value,
                    time: 'Now'
                });

            if (input) {
                input.value = '';
            }

            renderMessages(
                conversations[activeChat]
            );

            const activeButton =
                document.querySelector(
                    `[data-chat-id="${activeChat}"]`
                );

            const preview =
                activeButton?.querySelector(
                    '.msg-user-preview'
                );

            const time =
                activeButton?.querySelector(
                    '.msg-user-time'
                );

            if (preview) {
                preview.textContent = value;
            }

            if (time) {
                time.textContent = 'Now';
            }

            input?.focus();
        }
    );


    search?.addEventListener(
        'input',
        function () {
            const term =
                search.value
                    .trim()
                    .toLowerCase();

            let visible = 0;

            conversationButtons.forEach(
                function (button) {
                    const matches =
                        !term ||
                        (
                            button.dataset.search || ''
                        ).includes(term);

                    button.hidden = !matches;

                    if (matches) {
                        visible++;
                    }
                }
            );

            searchEmpty?.classList.toggle(
                'is-visible',
                visible === 0
            );
        }
    );
});
</script>
@endpush
