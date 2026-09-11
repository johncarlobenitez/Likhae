@extends('layouts.seller')

@php
    $requestedMode = $mode ?? request('mode', 'messages');

    $currentMode = in_array($requestedMode, ['messages', 'reviews'], true)
        ? $requestedMode
        : 'messages';

    $reviews = [
        [
            'buyer' => 'Angela Cruz',
            'product' => '27-inch Borderless Monitor',
            'rating' => 5,
            'message' => 'The monitor arrived safely and the colors look great. Seller packed it very well and shipped fast.',
            'time' => '2 hours ago',
            'replied' => false,
        ],
        [
            'buyer' => 'Marco Reyes',
            'product' => 'Mechanical Keyboard 87 Keys',
            'rating' => 5,
            'message' => 'Solid build and responsive keys. The seller also answered my questions before I ordered.',
            'time' => 'Yesterday',
            'replied' => true,
        ],
        [
            'buyer' => 'Sarah Lim',
            'product' => 'Wireless Gaming Mouse',
            'rating' => 4,
            'message' => 'Good mouse for the price. Delivery was quick, but I hope more color options become available.',
            'time' => 'Sep 02, 2026',
            'replied' => false,
        ],
    ];

    $conversations = [
        [
            'key' => 'angela',
            'name' => 'Angela Cruz',
            'preview' => 'Is the monitor compatible with a MacBook?',
            'time' => '2m',
            'avatar' => 'AC',
            'unread' => '2',
            'reference' => 'Order #10001',
            'is_unread' => true,
            'product' => '27-inch Borderless Monitor',
            'amount' => '₱12,990.00',
            'status' => 'To Process',
        ],
        [
            'key' => 'marco',
            'name' => 'Marco Reyes',
            'preview' => 'Thank you, I received the waybill update.',
            'time' => '18m',
            'avatar' => 'MR',
            'unread' => '',
            'reference' => 'Order #10002',
            'is_unread' => false,
            'product' => 'Mechanical Keyboard 87 Keys',
            'amount' => '₱5,580.00',
            'status' => 'Shipping',
        ],
        [
            'key' => 'sarah',
            'name' => 'Sarah Lim',
            'preview' => 'Can I change the delivery address?',
            'time' => '1h',
            'avatar' => 'SL',
            'unread' => '1',
            'reference' => 'Order #10003',
            'is_unread' => true,
            'product' => 'Wireless Gaming Mouse',
            'amount' => '₱1,490.00',
            'status' => 'To Prepare',
        ],
        [
            'key' => 'daniel',
            'name' => 'Daniel Tan',
            'preview' => 'The headphones sound great!',
            'time' => 'Yesterday',
            'avatar' => 'DT',
            'unread' => '',
            'reference' => 'Order #10004',
            'is_unread' => false,
            'product' => 'Wireless Headphones',
            'amount' => '₱3,490.00',
            'status' => 'Completed',
        ],
        [
            'key' => 'patricia',
            'name' => 'Patricia Go',
            'preview' => 'Do you have this lamp in black?',
            'time' => 'Sep 02',
            'avatar' => 'PG',
            'unread' => '',
            'reference' => 'Product question',
            'is_unread' => false,
            'product' => 'Minimal Desk Lamp',
            'amount' => 'Product Inquiry',
            'status' => 'Question',
        ],
    ];
@endphp

@section('title', $currentMode === 'reviews' ? 'Reviews' : 'Messages')
@section('active', $currentMode === 'reviews' ? 'reviews' : 'messages')
@section('subtitle', $currentMode === 'reviews' ? 'Monitor buyer feedback and protect your store reputation.' : 'Answer buyer questions and resolve order concerns quickly.')

@section('content')
<style>
    :root {
        --msg-bg: #FBF7F2;
        --msg-bg-soft: #F6EFE7;
        --msg-bg-alt: #EFE7DE;
        --msg-card: #FFFDF9;

        --msg-border: #EADCCC;
        --msg-border-strong: #DBCEC1;

        --msg-maroon: #561C17;
        --msg-maroon-2: #642920;
        --msg-maroon-dark: #3E130F;

        --msg-text: #3B211B;
        --msg-brown: #6C4936;
        --msg-muted: #987865;
        --msg-muted-2: #A99386;

        --msg-tan: #C19771;
        --msg-star: #C88418;

        --msg-success: #256F4A;
        --msg-success-soft: #EAF7EF;

        --msg-warning: #9A5B11;
        --msg-warning-soft: #FFF6DE;

        --msg-danger: #B42318;
        --msg-danger-soft: #FCEBE9;

        --msg-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --msg-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .sl-support-page {
        color: var(--msg-text);

        --sl-blue: var(--msg-maroon);
        --sl-blue-dark: var(--msg-maroon-dark);
        --sl-blue-soft: #F3E4DE;
        --sl-indigo: var(--msg-tan);
    }

    .sl-support-page .sl-page-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;

        padding: 32px 36px;

        border: 1px solid var(--msg-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--msg-shadow-soft);
    }

    .sl-support-page .sl-eyebrow {
        color: var(--msg-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .sl-support-page .sl-page-toolbar h2 {
        margin-top: 10px;

        color: var(--msg-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .sl-support-page .sl-page-toolbar p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--msg-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .sl-support-page .sl-btn {
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

    .sl-support-page .sl-btn:hover {
        transform: translateY(-1px);
    }

    .sl-support-page .sl-btn-primary {
        background: var(--msg-maroon) !important;
        border-color: var(--msg-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .sl-support-page .sl-btn-primary:hover {
        background: var(--msg-maroon-dark) !important;
        border-color: var(--msg-maroon-dark) !important;
    }

    .sl-support-page .sl-btn-ghost,
    .sl-support-page .sl-btn-soft {
        background: var(--msg-card) !important;
        border-color: var(--msg-tan) !important;
        color: var(--msg-maroon) !important;
    }

    .sl-support-page .sl-btn-ghost:hover,
    .sl-support-page .sl-btn-soft:hover {
        background: #F3E4DE !important;
        border-color: var(--msg-maroon) !important;
    }

    .sl-support-page .sl-btn-sm {
        min-height: 34px;
        padding-inline: 12px;
        border-radius: 11px;

        font-size: 11px;
    }

    .sl-support-page .sl-card {
        overflow: hidden;

        border: 1px solid var(--msg-border) !important;
        border-radius: 24px !important;

        background: var(--msg-card) !important;
        color: var(--msg-text) !important;

        box-shadow: var(--msg-shadow-soft) !important;
    }

    .sl-support-page .sl-response-chip {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        gap: 8px;

        padding: 0 14px;

        border: 1px solid #CFE8DA;
        border-radius: 999px;

        background: var(--msg-success-soft);
        color: var(--msg-success);

        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
    }

    .sl-support-page .sl-response-chip i {
        width: 8px;
        height: 8px;

        border-radius: 999px;

        background: var(--msg-success);
    }

    .sl-support-page .sl-review-summary {
        display: grid;
        grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
        gap: 18px;
        align-items: stretch;
    }

    .sl-support-page .sl-rating-overview {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr);
        gap: 22px;
        align-items: center;

        padding: 22px;
    }

    .sl-support-page .sl-rating-overview > div:first-child {
        display: grid;
        gap: 7px;

        padding: 20px;

        border: 1px solid var(--msg-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.18), transparent 28%),
            var(--msg-bg-soft);

        text-align: center;
    }

    .sl-support-page .sl-rating-overview strong {
        color: var(--msg-maroon);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 66px;
        font-weight: 400;
        line-height: 0.9;
        letter-spacing: -0.055em;
    }

    .sl-support-page .sl-rating-overview span {
        color: var(--msg-star);
        font-size: 14px;
        letter-spacing: 0.08em;
    }

    .sl-support-page .sl-rating-overview small {
        color: var(--msg-muted);

        font-size: 10px;
        font-weight: 800;
    }

    .sl-support-page .sl-rating-bars {
        display: grid;
        gap: 10px;
    }

    .sl-support-page .sl-rating-bars p {
        display: grid;
        grid-template-columns: 52px minmax(0, 1fr) 38px;
        align-items: center;
        gap: 10px;

        margin: 0;
    }

    .sl-support-page .sl-rating-bars p span,
    .sl-support-page .sl-rating-bars p small {
        color: var(--msg-muted);

        font-size: 10px;
        font-weight: 900;
    }

    .sl-support-page .sl-rating-bars i {
        display: block;
        height: 9px;
        overflow: hidden;

        border-radius: 999px;

        background: #E7D9CB;
    }

    .sl-support-page .sl-rating-bars b {
        display: block;
        height: 100%;

        border-radius: inherit;

        background: linear-gradient(90deg, var(--msg-maroon), var(--msg-tan));
    }

    .sl-support-page .sl-mini-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        padding: 18px;
    }

    .sl-support-page .sl-mini-stats div {
        padding: 17px;

        border: 1px solid var(--msg-border);
        border-radius: 18px;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.14), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
    }

    .sl-support-page .sl-mini-stats span {
        display: block;

        color: var(--msg-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .sl-support-page .sl-mini-stats strong {
        display: block;
        margin-top: 8px;

        color: var(--msg-maroon);

        font-size: 30px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-support-page .sl-table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;

        padding: 15px;

        border-bottom: 1px solid var(--msg-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.12), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .sl-support-page .sl-search-input,
    .sl-support-page .sl-conversation-search {
        position: relative;
        flex: 1;
        max-width: 500px;
    }

    .sl-support-page .sl-search-input svg,
    .sl-support-page .sl-conversation-search svg {
        position: absolute;
        top: 50%;
        left: 14px;

        width: 16px;
        height: 16px;

        color: var(--msg-muted-2);

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;

        transform: translateY(-50%);
    }

    .sl-support-page .sl-search-input input,
    .sl-support-page .sl-conversation-search input,
    .sl-support-page .sl-select {
        width: 100%;
        min-height: 42px;

        border: 1px solid var(--msg-border);
        border-radius: 14px;

        background: var(--msg-bg-soft);
        color: var(--msg-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;

        transition: 160ms ease;
    }

    .sl-support-page .sl-search-input input,
    .sl-support-page .sl-conversation-search input {
        padding: 0 14px 0 42px;
    }

    .sl-support-page .sl-select {
        max-width: 180px;
        padding: 0 12px;
    }

    .sl-support-page .sl-search-input input:focus,
    .sl-support-page .sl-conversation-search input:focus,
    .sl-support-page .sl-select:focus {
        border-color: var(--msg-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .sl-support-page .sl-review-list {
        display: grid;
        gap: 14px;

        padding: 18px;
    }

    .sl-support-page .sl-review-item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 14px;

        padding: 16px;

        border: 1px solid var(--msg-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 96% 8%, rgba(193, 151, 113, 0.12), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
    }

    .sl-support-page .sl-avatar {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;

        border-radius: 15px;

        background: var(--msg-maroon) !important;
        color: #FFFFFF !important;

        font-size: 12px;
        font-weight: 950;
    }

    .sl-support-page .sl-review-content {
        min-width: 0;
    }

    .sl-support-page .sl-review-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .sl-support-page .sl-review-head strong {
        display: block;

        color: var(--msg-text);

        font-size: 13px;
        font-weight: 950;
    }

    .sl-support-page .sl-review-head span {
        display: block;
        margin-top: 3px;

        color: var(--msg-muted);

        font-size: 10px;
        font-weight: 800;
    }

    .sl-support-page .sl-review-head small {
        color: var(--msg-muted-2);

        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .sl-support-page .sl-review-stars {
        margin-top: 9px;

        color: var(--msg-star);

        font-size: 13px;
        letter-spacing: 0.06em;
    }

    .sl-support-page .sl-review-content > p {
        margin: 9px 0 0;

        color: var(--msg-brown);

        font-size: 12px;
        line-height: 1.65;
    }

    .sl-support-page .sl-seller-reply {
        margin-top: 12px;
        padding: 13px;

        border: 1px solid var(--msg-border);
        border-radius: 15px;

        background: var(--msg-bg-soft);
    }

    .sl-support-page .sl-seller-reply strong {
        color: var(--msg-maroon);

        font-size: 11px;
        font-weight: 950;
    }

    .sl-support-page .sl-seller-reply p {
        margin: 5px 0 0;

        color: var(--msg-muted);

        font-size: 11px;
        line-height: 1.6;
    }

    .sl-support-page .sl-review-reply {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px;

        margin-top: 12px;
    }

    .sl-support-page .sl-review-reply textarea {
        min-height: 44px;
        padding: 11px 13px;

        border: 1px solid var(--msg-border);
        border-radius: 14px;

        background: var(--msg-bg-soft);
        color: var(--msg-text);

        font-size: 12px;
        font-weight: 700;
        resize: vertical;
        outline: none;
    }

    .sl-support-page .sl-review-reply textarea:focus {
        border-color: var(--msg-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .sl-support-page .sl-messages-layout {
        display: grid;
        grid-template-columns: 370px minmax(0, 1fr);
        min-height: 720px;

        border: 1px solid var(--msg-border);
        border-radius: 26px;

        background: var(--msg-card);
        box-shadow: var(--msg-shadow-soft);

        overflow: hidden;
    }

    .sl-support-page .sl-conversation-panel {
        display: grid;
        grid-template-rows: auto auto auto minmax(0, 1fr);

        border-right: 1px solid var(--msg-border);

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.11), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
    }

    .sl-support-page .sl-conversation-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;

        padding: 20px;

        border-bottom: 1px solid var(--msg-border);
    }

    .sl-support-page .sl-conversation-head h3 {
        margin: 0;

        color: var(--msg-text);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 32px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-support-page .sl-conversation-head span {
        display: block;
        margin-top: 5px;

        color: var(--msg-muted);

        font-size: 11px;
        font-weight: 800;
    }

    .sl-support-page .sl-icon-btn {
        display: grid;
        width: 36px;
        height: 36px;
        place-items: center;

        border: 1px solid var(--msg-border);
        border-radius: 12px;

        background: var(--msg-card);
        color: var(--msg-maroon);

        font-size: 12px;
        font-weight: 950;
        cursor: pointer;

        transition: 160ms ease;
    }

    .sl-support-page .sl-icon-btn:hover {
        background: var(--msg-maroon);
        border-color: var(--msg-maroon);
        color: #FFFFFF;
    }

    .sl-support-page .sl-icon-btn svg {
        width: 16px;
        height: 16px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sl-support-page .sl-conversation-search {
        max-width: none;
        margin: 14px 16px 10px;
    }

    .sl-support-page .sl-conversation-filters {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 0 16px 14px;
    }

    .sl-support-page .sl-conversation-filters button {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        justify-content: center;

        padding: 0 12px;

        border: 1px solid var(--msg-border);
        border-radius: 999px;

        background: var(--msg-card);
        color: var(--msg-brown);

        font-size: 11px;
        font-weight: 900;

        cursor: pointer;
        transition: 160ms ease;
    }

    .sl-support-page .sl-conversation-filters button:hover,
    .sl-support-page .sl-conversation-filters button.is-active {
        background: var(--msg-maroon);
        border-color: var(--msg-maroon);
        color: #FFFFFF;
    }

    .sl-support-page .sl-conversation-list {
        display: grid;
        align-content: start;
        gap: 8px;

        padding: 0 12px 16px;
        overflow-y: auto;
    }

    .sl-support-page .sl-conversation {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        gap: 12px;
        align-items: start;

        width: 100%;
        padding: 13px;

        border: 1px solid transparent;
        border-radius: 18px;

        background: transparent;
        color: var(--msg-text);

        text-align: left;
        cursor: pointer;

        transition: 160ms ease;
    }

    .sl-support-page .sl-conversation:hover,
    .sl-support-page .sl-conversation.is-active {
        border-color: var(--msg-tan);
        background: #FFF8F0;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.06);
    }

    .sl-support-page .sl-conversation-copy {
        display: grid;
        min-width: 0;
        gap: 4px;
    }

    .sl-support-page .sl-conversation-copy > span {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .sl-support-page .sl-conversation-copy strong {
        color: var(--msg-text);

        font-size: 12px;
        font-weight: 950;
    }

    .sl-support-page .sl-conversation-copy small {
        color: var(--msg-muted-2);

        font-size: 10px;
        font-weight: 800;
    }

    .sl-support-page .sl-conversation-copy em {
        color: var(--msg-maroon);

        font-size: 10px;
        font-style: normal;
        font-weight: 900;
    }

    .sl-support-page .sl-conversation-copy p {
        overflow: hidden;
        margin: 0;

        color: var(--msg-muted);

        font-size: 11px;
        line-height: 1.45;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sl-support-page .sl-unread {
        display: grid;
        min-width: 20px;
        height: 20px;
        place-items: center;

        padding: 0 6px;

        border-radius: 999px;

        background: var(--msg-maroon);
        color: #FFFFFF;

        font-size: 9px;
        font-weight: 950;
    }

    .sl-support-page .sl-chat-panel {
        display: grid;
        grid-template-rows: auto auto minmax(0, 1fr) auto;

        min-width: 0;

        background:
            radial-gradient(circle at 96% 8%, rgba(193, 151, 113, 0.10), transparent 30%),
            var(--msg-bg);
    }

    .sl-support-page .sl-chat-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;

        padding: 18px 20px;

        border-bottom: 1px solid var(--msg-border);

        background: rgba(255, 253, 249, 0.76);
        backdrop-filter: blur(12px);
    }

    .sl-support-page .sl-chat-person {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .sl-support-page .sl-chat-person strong {
        display: block;

        color: var(--msg-text);

        font-size: 14px;
        font-weight: 950;
    }

    .sl-support-page .sl-chat-person small {
        display: flex;
        align-items: center;
        gap: 6px;

        margin-top: 3px;

        color: var(--msg-muted);

        font-size: 10px;
        font-weight: 800;
    }

    .sl-support-page .sl-chat-person small i {
        width: 7px;
        height: 7px;

        border-radius: 999px;

        background: var(--msg-success);
    }

    .sl-support-page .sl-chat-head > div:last-child {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sl-support-page .sl-chat-context {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;

        margin: 16px 20px 0;
        padding: 14px;

        border: 1px solid var(--msg-border);
        border-radius: 18px;

        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.13), transparent 28%),
            rgba(255, 253, 249, 0.82);
    }

    .sl-support-page .sl-order-thumb {
        display: grid;
        width: 46px;
        height: 46px;
        place-items: center;

        border-radius: 15px;

        background: #F1E4D7;
        color: var(--msg-maroon);

        font-size: 15px;
        font-weight: 950;
    }

    .sl-support-page .sl-chat-context small {
        color: var(--msg-muted);

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.1em;
    }

    .sl-support-page .sl-chat-context strong {
        display: block;
        margin-top: 4px;

        color: var(--msg-text);

        font-size: 12px;
        font-weight: 950;
    }

    .sl-support-page .sl-chat-context span {
        display: block;
        margin-top: 3px;

        color: var(--msg-muted);

        font-size: 11px;
    }

    .sl-support-page .sl-chat-context a {
        color: var(--msg-maroon);

        font-size: 11px;
        font-weight: 950;
        text-decoration: none;
        white-space: nowrap;
    }

    .sl-support-page .sl-chat-context a:hover {
        text-decoration: underline;
    }

    .sl-support-page .sl-chat-messages {
        display: flex;
        flex-direction: column;
        gap: 10px;

        padding: 22px 20px;
        overflow-y: auto;
    }

    .sl-support-page .sl-message-day {
        align-self: center;

        padding: 6px 10px;

        border: 1px solid var(--msg-border);
        border-radius: 999px;

        background: rgba(255, 253, 249, 0.78);
        color: var(--msg-muted);

        font-size: 10px;
        font-weight: 900;
    }

    .sl-support-page .sl-message {
        max-width: min(560px, 78%);
    }

    .sl-support-page .sl-message p {
        margin: 0;
        padding: 12px 14px;

        border: 1px solid var(--msg-border);
        border-radius: 18px 18px 18px 6px;

        background: var(--msg-card);
        color: var(--msg-brown);

        font-size: 12px;
        line-height: 1.65;

        box-shadow: 0 6px 18px rgba(86, 28, 23, 0.04);
    }

    .sl-support-page .sl-message small {
        display: block;
        margin-top: 5px;

        color: var(--msg-muted);

        font-size: 10px;
        font-weight: 700;
    }

    .sl-support-page .sl-message.is-seller {
        align-self: flex-end;
    }

    .sl-support-page .sl-message.is-seller p {
        border-color: var(--msg-maroon);
        border-radius: 18px 18px 6px 18px;

        background: var(--msg-maroon);
        color: #FFFFFF;
    }

    .sl-support-page .sl-message.is-seller small {
        text-align: right;
    }

    .sl-support-page .sl-chat-composer {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        gap: 10px;
        align-items: end;

        padding: 14px 16px;

        border-top: 1px solid var(--msg-border);

        background: rgba(255, 253, 249, 0.86);
        backdrop-filter: blur(12px);
    }

    .sl-support-page .sl-chat-tools {
        display: flex;
        gap: 6px;
    }

    .sl-support-page .sl-chat-tools button,
    .sl-support-page .sl-chat-send {
        display: grid;
        width: 40px;
        height: 40px;
        place-items: center;

        border: 1px solid var(--msg-border);
        border-radius: 13px;

        background: var(--msg-card);
        color: var(--msg-maroon);

        cursor: pointer;
        transition: 160ms ease;
    }

    .sl-support-page .sl-chat-tools button:hover {
        background: #F3E4DE;
        border-color: var(--msg-tan);
    }

    .sl-support-page .sl-chat-tools svg,
    .sl-support-page .sl-chat-send svg {
        width: 18px;
        height: 18px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sl-support-page .sl-chat-composer textarea {
        min-height: 42px;
        max-height: 130px;
        padding: 12px 14px;

        border: 1px solid var(--msg-border);
        border-radius: 14px;

        background: var(--msg-bg-soft);
        color: var(--msg-text);

        font-size: 12px;
        font-weight: 700;
        line-height: 1.5;
        resize: none;
        outline: none;
    }

    .sl-support-page .sl-chat-composer textarea:focus {
        border-color: var(--msg-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .sl-support-page .sl-chat-send {
        border-color: var(--msg-maroon);
        background: var(--msg-maroon);
        color: #FFFFFF;
    }

    .sl-support-page .sl-chat-send:hover {
        background: var(--msg-maroon-dark);
        border-color: var(--msg-maroon-dark);
    }

    @media (max-width: 1180px) {
        .sl-support-page .sl-review-summary {
            grid-template-columns: 1fr;
        }

        .sl-support-page .sl-messages-layout {
            grid-template-columns: 320px minmax(0, 1fr);
        }

        .sl-support-page .sl-mini-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 880px) {
        .sl-support-page .sl-page-toolbar,
        .sl-support-page .sl-table-toolbar {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .sl-support-page .sl-search-input,
        .sl-support-page .sl-select,
        .sl-support-page .sl-btn {
            width: 100%;
            max-width: none;
        }

        .sl-support-page .sl-messages-layout {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .sl-support-page .sl-conversation-panel {
            max-height: 420px;
            border-right: 0;
            border-bottom: 1px solid var(--msg-border);
        }

        .sl-support-page .sl-chat-panel {
            min-height: 640px;
        }

        .sl-support-page .sl-chat-context {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .sl-support-page .sl-chat-context a {
            grid-column: 2;
        }
    }

    @media (max-width: 620px) {
        .sl-support-page .sl-rating-overview,
        .sl-support-page .sl-mini-stats,
        .sl-support-page .sl-review-reply,
        .sl-support-page .sl-chat-composer {
            grid-template-columns: 1fr;
        }

        .sl-support-page .sl-review-head,
        .sl-support-page .sl-chat-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .sl-support-page .sl-message {
            max-width: 100%;
        }

        .sl-support-page .sl-chat-tools {
            order: 2;
        }

        .sl-support-page .sl-chat-send {
            width: 100%;
        }
    }

    html.dark .sl-support-page .sl-page-toolbar,
    html.dark .sl-support-page .sl-card,
    html.dark .sl-support-page .sl-rating-overview > div:first-child,
    html.dark .sl-support-page .sl-mini-stats div,
    html.dark .sl-support-page .sl-table-toolbar,
    html.dark .sl-support-page .sl-review-item,
    html.dark .sl-support-page .sl-seller-reply,
    html.dark .sl-support-page .sl-messages-layout,
    html.dark .sl-support-page .sl-conversation-panel,
    html.dark .sl-support-page .sl-chat-panel,
    html.dark .sl-support-page .sl-chat-head,
    html.dark .sl-support-page .sl-chat-context,
    html.dark .sl-support-page .sl-message-day,
    html.dark .sl-support-page .sl-message p,
    html.dark .sl-support-page .sl-chat-composer {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;

        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .sl-support-page .sl-page-toolbar h2,
    html.dark .sl-support-page .sl-review-head strong,
    html.dark .sl-support-page .sl-conversation-head h3,
    html.dark .sl-support-page .sl-conversation-copy strong,
    html.dark .sl-support-page .sl-chat-person strong,
    html.dark .sl-support-page .sl-chat-context strong {
        color: #F5EFE8 !important;
    }

    html.dark .sl-support-page .sl-page-toolbar p,
    html.dark .sl-support-page .sl-rating-overview small,
    html.dark .sl-support-page .sl-mini-stats span,
    html.dark .sl-support-page .sl-review-head span,
    html.dark .sl-support-page .sl-review-head small,
    html.dark .sl-support-page .sl-review-content > p,
    html.dark .sl-support-page .sl-seller-reply p,
    html.dark .sl-support-page .sl-conversation-head span,
    html.dark .sl-support-page .sl-conversation-copy p,
    html.dark .sl-support-page .sl-conversation-copy small,
    html.dark .sl-support-page .sl-chat-person small,
    html.dark .sl-support-page .sl-chat-context span,
    html.dark .sl-support-page .sl-message small {
        color: #C8B7AD !important;
    }

    html.dark .sl-support-page .sl-eyebrow,
    html.dark .sl-support-page .sl-conversation-copy em,
    html.dark .sl-support-page .sl-chat-context a {
        color: #EBA99D !important;
    }

    html.dark .sl-support-page .sl-search-input input,
    html.dark .sl-support-page .sl-conversation-search input,
    html.dark .sl-support-page .sl-select,
    html.dark .sl-support-page .sl-review-reply textarea,
    html.dark .sl-support-page .sl-chat-composer textarea,
    html.dark .sl-support-page .sl-chat-tools button,
    html.dark .sl-support-page .sl-icon-btn {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .sl-support-page .sl-conversation:hover,
    html.dark .sl-support-page .sl-conversation.is-active,
    html.dark .sl-support-page .sl-conversation-filters button:hover,
    html.dark .sl-support-page .sl-conversation-filters button.is-active {
        background: #2D1414 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .sl-support-page .sl-message.is-seller p,
    html.dark .sl-support-page .sl-chat-send {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }
</style>

<div class="sl-page sl-support-page">
    @if ($currentMode === 'reviews')
        <div class="sl-page-toolbar">
            <div>
                <span class="sl-eyebrow">
                    Customer Service
                </span>

                <h2>
                    Buyer Reviews
                </h2>

                <p>
                    Respond professionally and use feedback to improve your products and store reputation.
                </p>
            </div>

            <button
                type="button"
                class="sl-btn sl-btn-ghost"
                data-demo-action="Reviews exported."
            >
                Export Reviews
            </button>
        </div>

        <section class="sl-review-summary">
            <div class="sl-card sl-rating-overview">
                <div>
                    <strong>4.8</strong>
                    <span>★★★★★</span>
                    <small>Based on 1,248 reviews</small>
                </div>

                <div class="sl-rating-bars">
                    @foreach ([5 => 78, 4 => 16, 3 => 4, 2 => 1, 1 => 1] as $stars => $percent)
                        <p>
                            <span>{{ $stars }} star</span>

                            <i>
                                <b style="width: {{ $percent }}%"></b>
                            </i>

                            <small>{{ $percent }}%</small>
                        </p>
                    @endforeach
                </div>
            </div>

            <div class="sl-mini-stats sl-card">
                <div>
                    <span>New Reviews</span>
                    <strong>18</strong>
                </div>

                <div>
                    <span>Awaiting Reply</span>
                    <strong>7</strong>
                </div>

                <div>
                    <span>With Photos</span>
                    <strong>64%</strong>
                </div>

                <div>
                    <span>Positive</span>
                    <strong>94%</strong>
                </div>
            </div>
        </section>

        <section class="sl-card">
            <div class="sl-table-toolbar">
                <div class="sl-search-input">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-4-4"/>
                    </svg>

                    <input type="search" placeholder="Search reviews">
                </div>

                <select class="sl-select">
                    <option>All ratings</option>
                    <option>5 stars</option>
                    <option>4 stars</option>
                    <option>3 stars and below</option>
                </select>

                <select class="sl-select">
                    <option>All replies</option>
                    <option>Awaiting reply</option>
                    <option>Replied</option>
                </select>
            </div>

            <div class="sl-review-list">
                @foreach ($reviews as $review)
                    <article class="sl-review-item">
                        <span class="sl-avatar">
                            {{ mb_strtoupper(mb_substr($review['buyer'], 0, 1)) }}
                        </span>

                        <div class="sl-review-content">
                            <div class="sl-review-head">
                                <div>
                                    <strong>
                                        {{ $review['buyer'] }}
                                    </strong>

                                    <span>
                                        {{ $review['product'] }}
                                    </span>
                                </div>

                                <small>
                                    {{ $review['time'] }}
                                </small>
                            </div>

                            <div class="sl-review-stars">
                                {{ str_repeat('★', $review['rating']) }}{{ str_repeat('☆', 5 - $review['rating']) }}
                            </div>

                            <p>
                                {{ $review['message'] }}
                            </p>

                            @if ($review['replied'])
                                <div class="sl-seller-reply">
                                    <strong>Your reply</strong>

                                    <p>
                                        Thank you for your feedback! We are glad you are enjoying your order.
                                    </p>
                                </div>
                            @else
                                <div class="sl-review-reply">
                                    <textarea rows="2" placeholder="Write a professional public reply..."></textarea>

                                    <button
                                        type="button"
                                        class="sl-btn sl-btn-primary sl-btn-sm"
                                        data-demo-action="Review reply published."
                                    >
                                        Reply
                                    </button>
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @else
        <div class="sl-page-toolbar">
            <div>
                <span class="sl-eyebrow">
                    Customer Service
                </span>

                <h2>
                    Buyer Messages
                </h2>

                <p>
                    Keep product questions and order conversations in one clean workspace.
                </p>
            </div>

            <span class="sl-response-chip">
                <i></i>
                Average response time: 8 min
            </span>
        </div>

        <section class="sl-messages-layout" data-messages>
            <aside class="sl-conversation-panel">
                <div class="sl-conversation-head">
                    <div>
                        <h3>Conversations</h3>
                        <span>3 unread</span>
                    </div>

                    <button
                        type="button"
                        class="sl-icon-btn"
                        data-demo-action="Starting a new conversation."
                        aria-label="New conversation"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                    </button>
                </div>

                <div class="sl-conversation-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-4-4"/>
                    </svg>

                    <input
                        type="search"
                        placeholder="Search buyer or order"
                        data-conversation-search
                    >
                </div>

                <div class="sl-conversation-filters">
                    <button class="is-active" type="button" data-conversation-filter="all">
                        All
                    </button>

                    <button type="button" data-conversation-filter="unread">
                        Unread
                    </button>

                    <button type="button" data-conversation-filter="orders">
                        Orders
                    </button>
                </div>

                <div class="sl-conversation-list">
                    @foreach ($conversations as $conversation)
                        <button
                            type="button"
                            class="sl-conversation {{ $loop->first ? 'is-active' : '' }}"
                            data-conversation
                            data-name="{{ $conversation['name'] }}"
                            data-key="{{ $conversation['key'] }}"
                            data-avatar="{{ $conversation['avatar'] }}"
                            data-reference="{{ $conversation['reference'] }}"
                            data-product="{{ $conversation['product'] }}"
                            data-amount="{{ $conversation['amount'] }}"
                            data-status="{{ $conversation['status'] }}"
                            data-search="{{ mb_strtolower($conversation['name'] . ' ' . $conversation['reference']) }}"
                            data-unread="{{ $conversation['is_unread'] ? 'true' : 'false' }}"
                            data-order="{{ str_contains($conversation['reference'], 'Order') ? 'true' : 'false' }}"
                        >
                            <span class="sl-avatar">
                                {{ $conversation['avatar'] }}
                            </span>

                            <span class="sl-conversation-copy">
                                <span>
                                    <strong>
                                        {{ $conversation['name'] }}
                                    </strong>

                                    <small>
                                        {{ $conversation['time'] }}
                                    </small>
                                </span>

                                <em>
                                    {{ $conversation['reference'] }}
                                </em>

                                <p>
                                    {{ $conversation['preview'] }}
                                </p>
                            </span>

                            @if ($conversation['unread'])
                                <b class="sl-unread">
                                    {{ $conversation['unread'] }}
                                </b>
                            @endif
                        </button>
                    @endforeach
                </div>
            </aside>

            <main class="sl-chat-panel">
                <header class="sl-chat-head">
                    <div class="sl-chat-person">
                        <span class="sl-avatar" data-chat-avatar>
                            AC
                        </span>

                        <div>
                            <strong data-chat-name>
                                Angela Cruz
                            </strong>

                            <small>
                                <i></i>
                                Active now · <span data-chat-order>Order #10001</span>
                            </small>
                        </div>
                    </div>

                    <div>
                        <a
                            href="{{ route('seller.orders', ['mode' => 'show', 'order' => '10001']) }}"
                            class="sl-btn sl-btn-soft sl-btn-sm"
                        >
                            View Order
                        </a>

                        <button
                            type="button"
                            class="sl-icon-btn"
                            data-demo-action="Conversation options opened."
                            aria-label="Conversation options"
                        >
                            •••
                        </button>
                    </div>
                </header>

                <div class="sl-chat-context">
                    <div class="sl-order-thumb">
                        M
                    </div>

                    <div>
                        <small>ORDER REFERENCE</small>

                        <strong data-chat-product>
                            27-inch Borderless Monitor
                        </strong>

                        <span data-chat-meta>
                            ₱12,990.00 · To Process
                        </span>
                    </div>

                    <a href="{{ route('seller.orders', ['mode' => 'show', 'order' => '10001']) }}">
                        View details →
                    </a>
                </div>

                <div class="sl-chat-messages" data-chat-messages>
                    <div class="sl-message-day">
                        Today
                    </div>

                    <div class="sl-message is-buyer">
                        <p>
                            Hello! Is the 27-inch monitor compatible with a MacBook using USB-C?
                        </p>

                        <small>9:18 AM</small>
                    </div>

                    <div class="sl-message is-seller">
                        <p>
                            Hi Angela! Yes, it works with a MacBook. You will need a USB-C to HDMI adapter because the monitor uses HDMI input.
                        </p>

                        <small>9:23 AM · Seen</small>
                    </div>

                    <div class="sl-message is-buyer">
                        <p>
                            Great, thank you. Does the package include an HDMI cable?
                        </p>

                        <small>9:24 AM</small>
                    </div>
                </div>

                <form class="sl-chat-composer" data-chat-form>
                    <div class="sl-chat-tools">
                        <button
                            type="button"
                            aria-label="Attach file"
                            data-demo-action="File attachment opened."
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m20 12-8 8a6 6 0 0 1-8-8l9-9a4 4 0 0 1 6 6l-9 9a2 2 0 0 1-3-3l8-8"/>
                            </svg>
                        </button>

                        <button
                            type="button"
                            aria-label="Add quick reply"
                            data-demo-action="Quick replies opened."
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 5h16v12H8l-4 4zM8 9h8M8 13h5"/>
                            </svg>
                        </button>
                    </div>

                    <textarea
                        rows="1"
                        placeholder="Write a message..."
                        aria-label="Message"
                        data-chat-input
                    ></textarea>

                    <button
                        type="submit"
                        class="sl-chat-send"
                        aria-label="Send message"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m22 2-7 20-4-9-9-4zM22 2 11 13"/>
                        </svg>
                    </button>
                </form>
            </main>
        </section>
    @endif
</div>
@endsection