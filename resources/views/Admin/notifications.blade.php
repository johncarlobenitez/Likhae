@extends('layouts.admin')

@section('title', 'Notifications')
@section('subtitle', 'Review risk, operations, finance, and system alerts in one prioritized inbox.')
@section('active', 'notifications')

@php
    $notifications = [
        [
            'type' => 'Risk',
            'title' => '5 high-confidence product signals need review',
            'message' => 'The prohibited-item monitor identified possible weapons or controlled products.',
            'time' => '8 min ago',
            'tone' => 'danger',
            'href' => route('admin.products', ['view' => 'monitor']),
        ],
        [
            'type' => 'Dispute',
            'title' => 'Urgent courier conduct complaint',
            'message' => 'Case DSP-1880 was escalated and requires an administrator assignment.',
            'time' => '18 min ago',
            'tone' => 'warning',
            'href' => route('admin.complaints'),
        ],
        [
            'type' => 'Registration',
            'title' => '7 applications exceeded the review target',
            'message' => 'Buyer, seller, and logistics-center documents are ready for review.',
            'time' => '42 min ago',
            'tone' => 'warning',
            'href' => route('admin.registrations'),
        ],
        [
            'type' => 'Finance',
            'title' => 'Settlement TXN-980138 failed',
            'message' => 'The payment provider returned a timeout. No duplicate payout was created.',
            'time' => '1 hr ago',
            'tone' => 'danger',
            'href' => route('admin.finance', ['tab' => 'payments']),
        ],
        [
            'type' => 'System',
            'title' => 'Restricted products policy published',
            'message' => 'Revision 3.2 is now visible to sellers and used during review.',
            'time' => 'Yesterday',
            'tone' => 'system',
            'href' => route('admin.settings', ['tab' => 'policies']),
        ],
    ];

    $filters = [
        'all' => 'All',
        'risk' => 'Risk',
        'operations' => 'Operations',
        'finance' => 'Finance',
        'system' => 'System',
    ];
@endphp

@section('content')
<style>
    :root {
        --notif-bg: #FBF7F2;
        --notif-bg-soft: #F6EFE7;
        --notif-bg-alt: #EFE7DE;
        --notif-card: #FFFDF9;

        --notif-border: #EADCCC;
        --notif-border-strong: #DBCEC1;

        --notif-maroon: #561C17;
        --notif-maroon-2: #642920;
        --notif-maroon-dark: #3E130F;

        --notif-text: #3B211B;
        --notif-brown: #6C4936;
        --notif-muted: #987865;
        --notif-muted-2: #A99386;

        --notif-tan: #C19771;

        --notif-success: #256F4A;
        --notif-success-soft: #EAF7EF;

        --notif-warning: #9A5B11;
        --notif-warning-soft: #FFF6DE;

        --notif-danger: #B42318;
        --notif-danger-soft: #FCEBE9;

        --notif-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --notif-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ad-notification-page {
        color: var(--notif-text);

        --ad-blue: var(--notif-maroon);
        --ad-blue-dark: var(--notif-maroon-dark);
        --ad-blue-soft: #F3E4DE;
        --ad-blue-border: #E6C7BE;
    }

    .ad-notification-page .ad-page-head {
        padding: 32px 36px;

        border: 1px solid var(--notif-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--notif-shadow-soft);
    }

    .ad-notification-page .ad-overline {
        color: var(--notif-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-notification-page .ad-page-head h2 {
        margin-top: 10px;

        color: var(--notif-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-notification-page .ad-page-head p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--notif-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .ad-notification-page .ad-inline-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ad-notification-page .ad-btn-primary {
        background: var(--notif-maroon) !important;
        border-color: var(--notif-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ad-notification-page .ad-btn-primary:hover {
        background: var(--notif-maroon-dark) !important;
        border-color: var(--notif-maroon-dark) !important;
    }

    .ad-notification-page .ad-btn-secondary {
        background: var(--notif-card) !important;
        border-color: var(--notif-tan) !important;
        color: var(--notif-maroon) !important;
    }

    .ad-notification-page .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--notif-maroon) !important;
    }

    .ad-notification-card {
        overflow: hidden;

        border: 1px solid var(--notif-border) !important;
        border-radius: 24px !important;

        background: var(--notif-card) !important;
        color: var(--notif-text) !important;

        box-shadow: var(--notif-shadow-soft) !important;
    }

    .ad-notification-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        margin: 14px;
        padding: 6px;

        border: 1px solid var(--notif-border);
        border-radius: 16px;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.12), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ad-notification-page .ad-tab {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 8px;

        padding: 0 16px;

        border: 0;
        border-radius: 12px;

        background: transparent;
        color: var(--notif-brown);

        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;

        cursor: pointer;
        transition: 160ms ease;
    }

    .ad-notification-page .ad-tab:hover {
        background: var(--notif-bg-soft);
        color: var(--notif-maroon);
    }

    .ad-notification-page .ad-tab.is-active {
        background: var(--notif-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .ad-notification-page .ad-tab b {
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

    .ad-notification-list {
        display: grid;
        gap: 12px;

        padding: 4px 18px 18px;
    }

    .ad-notification-item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto auto;
        align-items: center;
        gap: 14px;

        padding: 16px;

        border: 1px solid var(--notif-border);
        border-radius: 18px;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.12), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        color: var(--notif-text);
        text-decoration: none;

        transition:
            transform 160ms ease,
            border-color 160ms ease,
            box-shadow 160ms ease,
            background 160ms ease;
    }

    .ad-notification-item:hover {
        transform: translateY(-2px);
        border-color: var(--notif-tan);
        box-shadow: var(--notif-shadow-card);
    }

    .ad-notification-icon {
        display: grid;
        width: 44px;
        height: 44px;
        place-items: center;

        border-radius: 15px;

        font-size: 16px;
        font-weight: 950;
        line-height: 1;
    }

    .ad-notification-icon.is-danger {
        background: var(--notif-danger-soft);
        color: var(--notif-danger);
    }

    .ad-notification-icon.is-warning {
        background: var(--notif-warning-soft);
        color: var(--notif-warning);
    }

    .ad-notification-icon.is-system {
        background: #F1E4D7;
        color: var(--notif-maroon);
    }

    .ad-notification-copy {
        min-width: 0;
    }

    .ad-notification-copy strong {
        display: block;
        margin-top: 4px;

        color: var(--notif-text);

        font-size: 13px;
        font-weight: 950;
        line-height: 1.25;
        letter-spacing: -0.03em;
    }

    .ad-notification-copy p {
        max-width: 720px;
        margin: 5px 0 0;

        color: var(--notif-muted);

        font-size: 12px;
        line-height: 1.6;
    }

    .ad-notification-time {
        color: var(--notif-muted-2);

        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .ad-notification-arrow {
        display: grid;
        width: 30px;
        height: 30px;
        place-items: center;

        border: 1px solid var(--notif-border);
        border-radius: 999px;

        background: var(--notif-bg-soft);
        color: var(--notif-maroon);

        font-size: 15px;
        font-weight: 900;

        transition: 160ms ease;
    }

    .ad-notification-item:hover .ad-notification-arrow {
        background: var(--notif-maroon);
        border-color: var(--notif-maroon);
        color: #FFFFFF;
    }

    @media (max-width: 800px) {
        .ad-notification-page .ad-page-head {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .ad-notification-page .ad-page-head .ad-btn,
        .ad-notification-page .ad-inline-actions {
            width: 100%;
        }

        .ad-notification-item {
            grid-template-columns: auto minmax(0, 1fr);
            align-items: start;
        }

        .ad-notification-time,
        .ad-notification-arrow {
            grid-column: 2;
            justify-self: start;
        }
    }

    @media (max-width: 540px) {
        .ad-notification-list {
            padding-inline: 14px;
        }

        .ad-notification-item {
            padding: 14px;
        }

        .ad-notification-copy p {
            font-size: 11px;
        }
    }

    html.dark .ad-notification-page .ad-page-head,
    html.dark .ad-notification-card,
    html.dark .ad-notification-tabs,
    html.dark .ad-notification-item {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-notification-page .ad-page-head h2,
    html.dark .ad-notification-copy strong {
        color: #F5EFE8 !important;
    }

    html.dark .ad-notification-page .ad-page-head p,
    html.dark .ad-notification-copy p,
    html.dark .ad-notification-time {
        color: #C8B7AD !important;
    }

    html.dark .ad-notification-page .ad-overline {
        color: #EBA99D !important;
    }

    html.dark .ad-notification-page .ad-tab {
        color: #C8B7AD !important;
    }

    html.dark .ad-notification-page .ad-tab:hover {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-notification-page .ad-tab.is-active {
        background: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .ad-notification-icon.is-system,
    html.dark .ad-notification-arrow {
        background: #2D1414 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-notification-item:hover .ad-notification-arrow {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }
</style>

<div class="ad-page ad-notification-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">
                Admin inbox
            </span>

            <h2>
                Operational alerts
            </h2>

            <p>
                Critical risk and enforcement notices stay visually distinct from routine updates.
            </p>
        </div>

        <div class="ad-inline-actions">
            <button
                class="ad-btn ad-btn-secondary"
                type="button"
                data-demo-action="All notifications marked read"
            >
                Mark all as read
            </button>

            <a
                class="ad-btn ad-btn-primary"
                href="{{ route('admin.account', ['tab' => 'notifications']) }}"
            >
                Preferences
            </a>
        </div>
    </div>

    <section class="ad-card ad-notification-card">
        <div class="ad-notification-tabs" role="tablist" aria-label="Notification filters">
            @foreach($filters as $key => $label)
                <button
                    class="ad-tab {{ $key === 'all' ? 'is-active' : '' }}"
                    type="button"
                >
                    {{ $label }}

                    @if($key === 'all')
                        <b>8</b>
                    @endif
                </button>
            @endforeach
        </div>

        <div class="ad-notification-list">
            @foreach($notifications as $notification)
                @php
                    $tone = $notification['tone'];
                    $icon = match ($tone) {
                        'danger' => '!',
                        'warning' => '!',
                        default => 'i',
                    };
                @endphp

                <a
                    href="{{ $notification['href'] }}"
                    class="ad-notification-item"
                    data-filter-item
                    data-search="{{ strtolower($notification['type'] . ' ' . $notification['title'] . ' ' . $notification['message']) }}"
                >
                    <span class="ad-notification-icon is-{{ $tone }}">
                        {{ $icon }}
                    </span>

                    <span class="ad-notification-copy">
                        <span class="ad-overline">
                            {{ $notification['type'] }}
                        </span>

                        <strong>
                            {{ $notification['title'] }}
                        </strong>

                        <p>
                            {{ $notification['message'] }}
                        </p>
                    </span>

                    <small class="ad-notification-time">
                        {{ $notification['time'] }}
                    </small>

                    <span class="ad-notification-arrow" aria-hidden="true">
                        →
                    </span>
                </a>
            @endforeach
        </div>
    </section>
</div>
@endsection