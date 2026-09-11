@extends('layouts.seller')

@section('title', 'Notifications')
@section('active', 'notifications')
@section('subtitle', 'Review operational alerts, account updates, and marketplace announcements.')

@php
    $notifications = collect([
        [
            'type' => 'orders',
            'label' => 'Orders',
            'title' => 'New order received',
            'message' => '#10001 from Angela Cruz is ready for review.',
            'time' => '2 minutes ago',
            'icon' => 'OR',
            'unread' => true,
            'href' => route('seller.orders', ['mode' => 'show', 'order' => '10001']),
        ],
        [
            'type' => 'inventory',
            'label' => 'Inventory',
            'title' => 'Low stock alert',
            'message' => 'Mechanical Keyboard has only 3 units remaining.',
            'time' => '18 minutes ago',
            'icon' => 'ST',
            'unread' => true,
            'href' => route('seller.products', ['mode' => 'inventory']),
        ],
        [
            'type' => 'orders',
            'label' => 'Orders',
            'title' => 'Pickup request accepted',
            'message' => 'Juan Rider will collect Order #10003 today between 10AM and 12PM.',
            'time' => '45 minutes ago',
            'icon' => 'PK',
            'unread' => true,
            'href' => route('seller.logistics', ['view' => 'pickups']),
        ],
        [
            'type' => 'finance',
            'label' => 'Finance',
            'title' => 'Payout is being processed',
            'message' => 'Your ₱38,450 payout is scheduled for September 8.',
            'time' => '2 hours ago',
            'icon' => '₱',
            'unread' => false,
            'href' => route('seller.finance', ['tab' => 'payouts']),
        ],
        [
            'type' => 'system',
            'label' => 'System',
            'title' => '9.9 campaign invitation',
            'message' => 'Twelve of your products are eligible for the Local Finds Festival.',
            'time' => '4 hours ago',
            'icon' => 'MK',
            'unread' => false,
            'href' => route('seller.marketing', ['tab' => 'promotions']),
        ],
        [
            'type' => 'orders',
            'label' => 'Orders',
            'title' => 'Order delivered',
            'message' => '#10005 was delivered and confirmed by the buyer.',
            'time' => 'Yesterday',
            'icon' => 'DL',
            'unread' => false,
            'href' => route('seller.orders', ['status' => 'completed']),
        ],
        [
            'type' => 'inventory',
            'label' => 'Inventory',
            'title' => 'Product automatically archived',
            'message' => '7-in-1 USB-C Hub reached zero stock and was archived.',
            'time' => 'Yesterday',
            'icon' => 'ST',
            'unread' => false,
            'href' => route('seller.products', ['mode' => 'inventory']),
        ],
        [
            'type' => 'system',
            'label' => 'System',
            'title' => 'Security check completed',
            'message' => 'No unusual activity was detected on your seller account.',
            'time' => 'Sep 02, 2026',
            'icon' => 'SC',
            'unread' => false,
            'href' => route('seller.account', ['tab' => 'security']),
        ],
    ]);

    $filters = [
        'all' => ['label' => 'All', 'count' => $notifications->count()],
        'orders' => ['label' => 'Orders', 'count' => $notifications->where('type', 'orders')->count()],
        'inventory' => ['label' => 'Inventory', 'count' => $notifications->where('type', 'inventory')->count()],
        'finance' => ['label' => 'Finance', 'count' => $notifications->where('type', 'finance')->count()],
        'system' => ['label' => 'System', 'count' => $notifications->where('type', 'system')->count()],
    ];

    $unreadCount = $notifications->where('unread', true)->count();
@endphp

@section('content')
<style>
    :root {
        --ntf-bg: #FBF7F2;
        --ntf-bg-soft: #F6EFE7;
        --ntf-bg-alt: #EFE7DE;
        --ntf-card: #FFFDF9;

        --ntf-border: #EADCCC;
        --ntf-border-strong: #DBCEC1;

        --ntf-maroon: #561C17;
        --ntf-maroon-2: #642920;
        --ntf-maroon-dark: #3E130F;

        --ntf-text: #3B211B;
        --ntf-brown: #6C4936;
        --ntf-muted: #987865;
        --ntf-muted-2: #A99386;

        --ntf-tan: #C19771;

        --ntf-success: #256F4A;
        --ntf-success-soft: #EAF7EF;

        --ntf-warning: #9A5B11;
        --ntf-warning-soft: #FFF6DE;

        --ntf-danger: #B42318;
        --ntf-danger-soft: #FCEBE9;

        --ntf-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --ntf-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .sl-notifications-page {
        max-width: 1180px;
        margin-inline: auto;

        color: var(--ntf-text);

        --sl-blue: var(--ntf-maroon);
        --sl-blue-dark: var(--ntf-maroon-dark);
        --sl-blue-soft: #F3E4DE;
        --sl-indigo: var(--ntf-tan);
    }

    .sl-notifications-page .sl-page-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;

        padding: 32px 36px;

        border: 1px solid var(--ntf-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--ntf-shadow-soft);
    }

    .sl-notifications-page .sl-eyebrow {
        color: var(--ntf-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .sl-notifications-page .sl-page-toolbar h2 {
        margin-top: 10px;

        color: var(--ntf-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .sl-notifications-page .sl-page-toolbar p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--ntf-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .sl-notifications-page .sl-toolbar-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .sl-notifications-page .sl-unread-pill {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        gap: 8px;

        padding: 0 13px;

        border: 1px solid #E6C7BE;
        border-radius: 999px;

        background: #F3E4DE;
        color: var(--ntf-maroon);

        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
    }

    .sl-notifications-page .sl-unread-pill i {
        width: 8px;
        height: 8px;

        border-radius: 999px;

        background: var(--ntf-maroon);
    }

    .sl-notifications-page .sl-btn {
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

    .sl-notifications-page .sl-btn:hover {
        transform: translateY(-1px);
    }

    .sl-notifications-page .sl-btn-ghost,
    .sl-notifications-page .sl-btn-soft {
        background: var(--ntf-card) !important;
        border-color: var(--ntf-tan) !important;
        color: var(--ntf-maroon) !important;
    }

    .sl-notifications-page .sl-btn-ghost:hover,
    .sl-notifications-page .sl-btn-soft:hover {
        background: #F3E4DE !important;
        border-color: var(--ntf-maroon) !important;
    }

    .sl-notifications-page .sl-card {
        overflow: hidden;

        border: 1px solid var(--ntf-border) !important;
        border-radius: 24px !important;

        background: var(--ntf-card) !important;
        color: var(--ntf-text) !important;

        box-shadow: var(--ntf-shadow-soft) !important;
    }

    .sl-notifications-page .sl-notification-layout {
        display: grid;
        grid-template-columns: 270px minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .sl-notifications-page .sl-notification-filter {
        position: sticky;
        top: 92px;

        display: grid;
        gap: 7px;

        padding: 12px;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.14), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%) !important;
    }

    .sl-notifications-page .sl-notification-filter button {
        display: flex;
        min-height: 46px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;

        width: 100%;
        padding: 0 13px;

        border: 1px solid transparent;
        border-radius: 15px;

        background: transparent;
        color: var(--ntf-brown);

        font-size: 12px;
        font-weight: 900;
        text-align: left;

        cursor: pointer;
        transition: 160ms ease;
    }

    .sl-notifications-page .sl-notification-filter button:hover {
        background: var(--ntf-bg-soft);
        color: var(--ntf-maroon);
    }

    .sl-notifications-page .sl-notification-filter button.is-active {
        background: var(--ntf-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .sl-notifications-page .sl-notification-filter button span {
        display: grid;
        min-width: 26px;
        height: 24px;
        place-items: center;

        padding: 0 7px;

        border-radius: 999px;

        background: #F1E4D7;
        color: var(--ntf-maroon);

        font-size: 10px;
        font-weight: 950;
    }

    .sl-notifications-page .sl-notification-filter button.is-active span {
        background: rgba(255, 255, 255, 0.18);
        color: #FFFFFF;
    }

    .sl-notifications-page .sl-notification-list {
        display: grid;
        gap: 12px;
    }

    .sl-notifications-page .sl-notification-item {
        position: relative;

        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto auto;
        align-items: center;
        gap: 14px;

        padding: 16px;

        border: 1px solid var(--ntf-border);
        border-radius: 22px;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.12), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        color: var(--ntf-text);
        text-decoration: none;

        box-shadow: var(--ntf-shadow-soft);

        transition:
            transform 160ms ease,
            border-color 160ms ease,
            box-shadow 160ms ease,
            background 160ms ease;
    }

    .sl-notifications-page .sl-notification-item:hover {
        transform: translateY(-2px);
        border-color: var(--ntf-tan);
        box-shadow: var(--ntf-shadow-card);
    }

    .sl-notifications-page .sl-notification-item.is-unread {
        border-color: #E6C7BE;
        background:
            radial-gradient(circle at 96% 6%, rgba(86, 28, 23, 0.08), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF4EC 100%);
    }

    .sl-notifications-page .sl-notification-icon {
        display: grid;
        width: 48px;
        height: 48px;
        place-items: center;

        border-radius: 16px;

        background: #F1E4D7;
        color: var(--ntf-maroon);

        font-size: 12px;
        font-weight: 950;
        letter-spacing: -0.02em;
    }

    .sl-notifications-page .sl-notification-icon.is-orders {
        background: #F3E4DE;
        color: var(--ntf-maroon);
    }

    .sl-notifications-page .sl-notification-icon.is-inventory {
        background: var(--ntf-warning-soft);
        color: var(--ntf-warning);
    }

    .sl-notifications-page .sl-notification-icon.is-finance {
        background: var(--ntf-success-soft);
        color: var(--ntf-success);
    }

    .sl-notifications-page .sl-notification-icon.is-system {
        background: var(--ntf-bg-soft);
        color: var(--ntf-brown);
    }

    .sl-notifications-page .sl-notification-copy {
        min-width: 0;
    }

    .sl-notifications-page .sl-notification-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;

        margin-bottom: 5px;
    }

    .sl-notifications-page .sl-notification-type {
        display: inline-flex;
        min-height: 22px;
        align-items: center;

        padding: 0 8px;

        border-radius: 999px;

        background: var(--ntf-bg-soft);
        color: var(--ntf-maroon);

        font-size: 9px;
        font-weight: 950;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .sl-notifications-page .sl-notification-time {
        color: var(--ntf-muted-2);

        font-size: 10px;
        font-weight: 850;
    }

    .sl-notifications-page .sl-notification-copy strong {
        display: block;

        color: var(--ntf-text);

        font-size: 13px;
        font-weight: 950;
        line-height: 1.25;
        letter-spacing: -0.025em;
    }

    .sl-notifications-page .sl-notification-copy p {
        max-width: 660px;
        margin: 5px 0 0;

        color: var(--ntf-muted);

        font-size: 12px;
        line-height: 1.6;
    }

    .sl-notifications-page .sl-notification-dot {
        display: block;
        width: 9px;
        height: 9px;

        border-radius: 999px;

        background: var(--ntf-maroon);
        box-shadow: 0 0 0 5px rgba(86, 28, 23, 0.10);
    }

    .sl-notifications-page .sl-notification-arrow {
        display: grid;
        width: 32px;
        height: 32px;
        place-items: center;

        border: 1px solid var(--ntf-border);
        border-radius: 999px;

        background: var(--ntf-bg-soft);
        color: var(--ntf-maroon);

        font-size: 18px;
        font-weight: 900;

        transition: 160ms ease;
    }

    .sl-notifications-page .sl-notification-item:hover .sl-notification-arrow {
        background: var(--ntf-maroon);
        border-color: var(--ntf-maroon);
        color: #FFFFFF;
    }

    .sl-notifications-page .sl-empty-state {
        padding: 48px 22px;

        border: 1px dashed var(--ntf-border-strong);
        border-radius: 22px;

        background: var(--ntf-card);
        color: var(--ntf-muted);

        text-align: center;
    }

    .sl-notifications-page .sl-empty-state h3 {
        margin: 0;

        color: var(--ntf-text);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 34px;
        font-weight: 400;
        letter-spacing: -0.045em;
    }

    .sl-notifications-page .sl-empty-state p {
        margin: 8px 0 0;

        color: var(--ntf-muted);

        font-size: 12px;
        line-height: 1.6;
    }

    @media (max-width: 1000px) {
        .sl-notifications-page .sl-notification-layout {
            grid-template-columns: 1fr;
        }

        .sl-notifications-page .sl-notification-filter {
            position: static;

            display: flex;
            overflow-x: auto;
        }

        .sl-notifications-page .sl-notification-filter button {
            min-width: max-content;
        }
    }

    @media (max-width: 720px) {
        .sl-notifications-page .sl-page-toolbar {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .sl-notifications-page .sl-toolbar-actions,
        .sl-notifications-page .sl-btn {
            width: 100%;
        }

        .sl-notifications-page .sl-notification-item {
            grid-template-columns: auto minmax(0, 1fr);
            align-items: flex-start;
        }

        .sl-notifications-page .sl-notification-dot,
        .sl-notifications-page .sl-notification-arrow {
            grid-column: 2;
            justify-self: start;
        }
    }

    html.dark .sl-notifications-page .sl-page-toolbar,
    html.dark .sl-notifications-page .sl-card,
    html.dark .sl-notifications-page .sl-notification-filter,
    html.dark .sl-notifications-page .sl-notification-item,
    html.dark .sl-notifications-page .sl-empty-state {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;

        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .sl-notifications-page .sl-page-toolbar h2,
    html.dark .sl-notifications-page .sl-notification-copy strong,
    html.dark .sl-notifications-page .sl-empty-state h3 {
        color: #F5EFE8 !important;
    }

    html.dark .sl-notifications-page .sl-page-toolbar p,
    html.dark .sl-notifications-page .sl-notification-copy p,
    html.dark .sl-notifications-page .sl-notification-time,
    html.dark .sl-notifications-page .sl-empty-state p {
        color: #C8B7AD !important;
    }

    html.dark .sl-notifications-page .sl-eyebrow,
    html.dark .sl-notifications-page .sl-notification-type {
        color: #EBA99D !important;
    }

    html.dark .sl-notifications-page .sl-notification-type,
    html.dark .sl-notifications-page .sl-notification-arrow,
    html.dark .sl-notifications-page .sl-notification-filter button span,
    html.dark .sl-notifications-page .sl-btn-ghost {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-notifications-page .sl-notification-filter button {
        color: #C8B7AD !important;
    }

    html.dark .sl-notifications-page .sl-notification-filter button:hover,
    html.dark .sl-notifications-page .sl-notification-filter button.is-active,
    html.dark .sl-notifications-page .sl-notification-item:hover .sl-notification-arrow {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .sl-notifications-page .sl-notification-icon {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }
</style>

<div class="sl-page sl-notifications-page">
    <div class="sl-page-toolbar">
        <div>
            <span class="sl-eyebrow">
                Activity Center
            </span>

            <h2>
                Notifications
            </h2>

            <p>
                Prioritize seller updates that need action, from new orders to inventory, finance, and account alerts.
            </p>
        </div>

        <div class="sl-toolbar-actions">
            <span class="sl-unread-pill">
                <i></i>
                {{ $unreadCount }} unread alerts
            </span>

            <button
                type="button"
                class="sl-btn sl-btn-ghost"
                data-mark-all-read
            >
                Mark All as Read
            </button>
        </div>
    </div>

    <section class="sl-notification-layout">
        <nav class="sl-notification-filter sl-card" aria-label="Notification filters">
            @foreach ($filters as $key => $filter)
                <button
                    type="button"
                    class="{{ $key === 'all' ? 'is-active' : '' }}"
                    data-notification-filter="{{ $key }}"
                >
                    {{ $filter['label'] }}

                    <span>
                        {{ $filter['count'] }}
                    </span>
                </button>
            @endforeach
        </nav>

        <div class="sl-notification-list" data-notification-list>
            @foreach ($notifications as $notification)
                <a
                    href="{{ $notification['href'] }}"
                    class="sl-notification-item {{ $notification['unread'] ? 'is-unread' : '' }}"
                    data-notification
                    data-type="{{ $notification['type'] }}"
                >
                    <span class="sl-notification-icon is-{{ $notification['type'] }}">
                        {{ $notification['icon'] }}
                    </span>

                    <div class="sl-notification-copy">
                        <div class="sl-notification-meta">
                            <span class="sl-notification-type">
                                {{ $notification['label'] }}
                            </span>

                            <span class="sl-notification-time">
                                {{ $notification['time'] }}
                            </span>
                        </div>

                        <strong>
                            {{ $notification['title'] }}
                        </strong>

                        <p>
                            {{ $notification['message'] }}
                        </p>
                    </div>

                    @if ($notification['unread'])
                        <i class="sl-notification-dot" aria-label="Unread notification"></i>
                    @else
                        <span></span>
                    @endif

                    <span class="sl-notification-arrow" aria-hidden="true">
                        ›
                    </span>
                </a>
            @endforeach

            <div class="sl-empty-state" data-notification-empty hidden>
                <h3>
                    No notifications in this category
                </h3>

                <p>
                    New seller updates will appear here when they match the selected filter.
                </p>
            </div>
        </div>
    </section>
</div>
@endsection