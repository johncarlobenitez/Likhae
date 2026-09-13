@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Platform health, risk signals, and operational priorities at a glance.')
@section('active', 'dashboard')

@php
    $recentOrders = [
        ['id' => '#LK-10482', 'buyer' => 'Angela Cruz', 'seller' => 'LIKHA Studio', 'total' => 12990, 'delivery' => 'J&T Express', 'status' => 'Processing'],
        ['id' => '#LK-10481', 'buyer' => 'Marco Reyes', 'seller' => 'North & Pine', 'total' => 5580, 'delivery' => 'Flash Express', 'status' => 'Shipping'],
        ['id' => '#LK-10480', 'buyer' => 'Sarah Lim', 'seller' => 'MNL Tech', 'total' => 1490, 'delivery' => 'LBC', 'status' => 'Delivered'],
        ['id' => '#LK-10479', 'buyer' => 'Daniel Tan', 'seller' => 'Casa Local', 'total' => 3490, 'delivery' => 'Local Courier', 'status' => 'Completed'],
        ['id' => '#LK-10478', 'buyer' => 'Patricia Go', 'seller' => 'Paper & Loom', 'total' => 1980, 'delivery' => 'J&T Express', 'status' => 'Under Review'],
    ];
@endphp

@section('content')
<style>
    :root {
        --photo-bg: #FBF7F2;
        --photo-bg-soft: #F6EFE7;
        --photo-bg-alt: #EFE7DE;
        --photo-card: #FFFDF9;

        --photo-border: #EADCCC;
        --photo-border-strong: #DBCEC1;

        --photo-maroon: #561C17;
        --photo-maroon-2: #642920;
        --photo-maroon-dark: #3E130F;

        --photo-text: #3B211B;
        --photo-brown: #6C4936;
        --photo-muted: #987865;
        --photo-muted-2: #A99386;

        --photo-tan: #C19771;
        --photo-gold: #C88418;

        --photo-success: #256F4A;
        --photo-success-soft: #EAF7EF;

        --photo-warning: #9A5B11;
        --photo-warning-soft: #FFF6DE;

        --photo-danger: #B42318;
        --photo-danger-soft: #FCEBE9;

        --photo-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --photo-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    body,
    .ad-page {
        background: var(--photo-bg) !important;
        color: var(--photo-text) !important;
    }

    .ad-page {
        display: grid;
        gap: 18px;
    }

    .ad-page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;

        padding: 34px 38px;
        border: 1px solid var(--photo-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%) !important;

        box-shadow: var(--photo-shadow-soft);
    }

    .ad-overline {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        color: var(--photo-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-overline::before {
        width: 24px;
        height: 1px;
        background: var(--photo-maroon);
        content: "";
    }

    .ad-card-head .ad-overline::before,
    .ad-stat-grid .ad-overline::before {
        display: none;
    }

    .ad-page-head h2 {
        margin: 10px 0 0;

        color: var(--photo-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px, 5vw, 70px);
        font-weight: 400;
        line-height: 0.92;
        letter-spacing: -0.055em;
    }

    .ad-page-head p {
        max-width: 600px;
        margin: 14px 0 0;

        color: var(--photo-muted) !important;

        font-size: 13px;
        line-height: 1.75;
    }

    .ad-inline-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ad-btn {
        display: inline-flex;
        min-height: 43px;
        align-items: center;
        justify-content: center;
        gap: 8px;

        padding: 0 17px;

        border: 1px solid transparent;
        border-radius: 13px;

        font-size: 12px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;

        transition:
            transform 160ms ease,
            background 160ms ease,
            border-color 160ms ease,
            color 160ms ease,
            box-shadow 160ms ease;
    }

    .ad-btn:hover {
        transform: translateY(-1px);
    }

    .ad-btn-primary {
        background: var(--photo-maroon) !important;
        border-color: var(--photo-maroon) !important;
        color: #FFFFFF !important;
        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ad-btn-primary:hover {
        background: var(--photo-maroon-dark) !important;
        border-color: var(--photo-maroon-dark) !important;
    }

    .ad-btn-secondary {
        background: rgba(255, 253, 249, 0.82) !important;
        border-color: var(--photo-tan) !important;
        color: var(--photo-maroon) !important;
    }

    .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--photo-maroon) !important;
    }

    .ad-stat-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 16px;
    }

    .ad-stat-grid > * {
        position: relative;
        overflow: hidden;

        border: 1px solid var(--photo-border) !important;
        border-radius: 22px !important;

        background:
            radial-gradient(circle at 94% 6%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%) !important;

        color: var(--photo-text) !important;
        box-shadow: var(--photo-shadow-soft) !important;

        transition:
            transform 180ms ease,
            border-color 180ms ease,
            box-shadow 180ms ease;
    }

    .ad-stat-grid > *:hover {
        transform: translateY(-3px);
        border-color: var(--photo-tan) !important;
        box-shadow: var(--photo-shadow-card) !important;
    }

    .ad-stat-grid * {
        border-color: var(--photo-border) !important;
    }

    .ad-stat-grid h2,
    .ad-stat-grid h3,
    .ad-stat-grid strong,
    .ad-stat-grid b {
        color: var(--photo-text) !important;
    }

    .ad-stat-grid p,
    .ad-stat-grid span,
    .ad-stat-grid small {
        color: var(--photo-muted) !important;
    }

    .ad-stat-grid svg {
        color: var(--photo-maroon) !important;
        stroke: currentColor !important;
    }

    .ad-dashboard-grid,
    .ad-dashboard-split {
        display: grid;
        gap: 18px;
    }

    .ad-dashboard-grid {
        grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.65fr);
    }

    .ad-dashboard-split {
        grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.6fr);
    }

    .ad-card {
        overflow: hidden;

        border: 1px solid var(--photo-border) !important;
        border-radius: 24px !important;

        background: var(--photo-card) !important;
        color: var(--photo-text) !important;

        box-shadow: var(--photo-shadow-soft) !important;
    }

    .ad-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;

        padding: 20px 22px;

        border-bottom: 1px solid var(--photo-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .ad-card-head h2 {
        margin: 7px 0 0;

        color: var(--photo-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-card-head p {
        margin: 8px 0 0;

        color: var(--photo-muted) !important;

        font-size: 12px;
        line-height: 1.65;
    }

    .ad-card-body {
        padding: 22px;
    }

    .ad-select {
        min-height: 38px;
        padding: 0 12px;

        border: 1px solid var(--photo-border);
        border-radius: 12px;

        background: var(--photo-bg-soft);
        color: var(--photo-text);

        font-size: 11px;
        font-weight: 800;
        outline: none;
    }

    .ad-select:focus {
        border-color: var(--photo-tan);
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-chart-summary {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .ad-chart-summary small {
        display: block;
        color: var(--photo-muted);
        font-size: 11px;
        line-height: 1.6;
    }

    .ad-chart-summary strong {
        display: block;
        margin: 5px 0;

        color: var(--photo-maroon);

        font-size: 32px;
        font-weight: 950;
        letter-spacing: -0.05em;
    }

    .ad-chart-trend {
        color: var(--photo-maroon);
        font-weight: 900;
    }

    .ad-chart-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ad-chart-legend span {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        color: var(--photo-muted);

        font-size: 11px;
        font-weight: 700;
    }

    .ad-chart-legend i {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: var(--photo-maroon);
    }

    .ad-chart-legend i.is-muted {
        background: var(--photo-tan);
        opacity: 0.6;
    }

    .ad-bar-chart {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        align-items: end;
        gap: 12px;

        min-height: 220px;
        padding: 18px 16px 8px;

        border: 1px solid var(--photo-border);
        border-radius: 18px;

        background:
            linear-gradient(180deg, rgba(255, 253, 249, 0.9), rgba(246, 239, 231, 0.75));
    }

    .ad-bar {
        display: grid;
        height: 180px;
        align-items: end;
        gap: 9px;
    }

    .ad-bar i {
        display: block;
        min-height: 22px;

        border-radius: 999px 999px 10px 10px;

        background:
            linear-gradient(180deg, var(--photo-maroon-2), var(--photo-maroon));

        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.14);
    }

    .ad-bar span {
        color: var(--photo-muted);
        font-size: 10px;
        font-weight: 800;
        text-align: center;
    }

    .ad-donut-wrap {
        display: grid;
        grid-template-columns: 170px minmax(0, 1fr);
        align-items: center;
        gap: 22px;
    }

    .ad-donut {
        display: grid;
        width: 170px;
        height: 170px;
        place-items: center;

        border-radius: 999px;

        background:
            conic-gradient(
                var(--photo-maroon) 0 42%,
                var(--photo-tan) 42% 66%,
                #D9B99D 66% 84%,
                #EFE1D5 84% 100%
            );
    }

    .ad-donut strong {
        display: grid;
        width: 112px;
        height: 112px;
        place-items: center;

        border-radius: 999px;

        background: var(--photo-card);
        color: var(--photo-text);

        font-size: 32px;
        font-weight: 950;
        line-height: 1;
        text-align: center;
    }

    .ad-donut small {
        display: block;
        color: var(--photo-muted);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .ad-donut-list {
        display: grid;
        gap: 12px;
    }

    .ad-donut-list div {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px;

        color: var(--photo-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .ad-donut-list i {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: var(--photo-maroon);
    }

    .ad-donut-list div:nth-child(2) i {
        background: var(--photo-tan);
    }

    .ad-donut-list div:nth-child(3) i {
        background: #D9B99D;
    }

    .ad-donut-list div:nth-child(4) i {
        background: #EFE1D5;
    }

    .ad-donut-list b {
        color: var(--photo-text);
        font-weight: 950;
    }

    .ad-table-wrap {
        overflow-x: auto;
    }

    .ad-table {
        width: 100%;
        min-width: 780px;
        border-collapse: collapse;
    }

    .ad-table thead {
        background: var(--photo-bg-soft);
    }

    .ad-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--photo-border);

        color: var(--photo-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .ad-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--photo-brown);

        font-size: 12px;
    }

    .ad-table tbody tr:hover td {
        background: var(--photo-bg-soft);
    }

    .ad-table td strong {
        color: var(--photo-text);
        font-weight: 900;
    }

    .ad-muted {
        color: var(--photo-muted-2) !important;
        font-size: 11px;
    }

    .ad-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 26px;
        padding: 0 10px;

        border: 1px solid transparent;
        border-radius: 999px;

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .ad-status.is-processing {
        background: var(--photo-warning-soft);
        border-color: #EAD39A;
        color: var(--photo-warning);
    }

    .ad-status.is-shipping {
        background: #F3E4DE;
        border-color: #E6C7BE;
        color: var(--photo-maroon);
    }

    .ad-status.is-delivered {
        background: var(--photo-success-soft);
        border-color: #CFE8DA;
        color: var(--photo-success);
    }

    .ad-status.is-completed {
        background: var(--photo-bg-soft);
        border-color: var(--photo-border-strong);
        color: var(--photo-brown);
    }

    .ad-status.is-under-review {
        background: var(--photo-danger-soft);
        border-color: #F0C9C4;
        color: var(--photo-danger);
    }

    .ad-queue {
        display: grid;
        gap: 12px;
        padding: 20px;
    }

    .ad-queue-item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 13px;

        padding: 15px;

        border: 1px solid var(--photo-border);
        border-radius: 16px;

        background: #FFFDF9;
        color: var(--photo-text);
        text-decoration: none;

        transition:
            transform 160ms ease,
            background 160ms ease,
            border-color 160ms ease;
    }

    .ad-queue-item:hover {
        transform: translateY(-2px);
        background: var(--photo-bg-soft);
        border-color: var(--photo-tan);
    }

    .ad-queue-icon {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;

        border-radius: 14px;

        background: #F1E4D7;
        color: var(--photo-maroon);

        font-size: 14px;
        font-weight: 950;
    }

    .ad-queue-item.is-danger .ad-queue-icon {
        background: var(--photo-danger-soft);
        color: var(--photo-danger);
    }

    .ad-queue-item.is-warning .ad-queue-icon {
        background: var(--photo-warning-soft);
        color: var(--photo-warning);
    }

    .ad-queue-copy {
        display: grid;
        min-width: 0;
        gap: 3px;
    }

    .ad-queue-copy strong {
        color: var(--photo-text);
        font-size: 12px;
        font-weight: 950;
    }

    .ad-queue-copy span {
        color: var(--photo-muted);
        font-size: 11px;
        line-height: 1.45;
    }

    .ad-note {
        padding: 16px 18px;

        border: 1px solid var(--photo-border);
        border-radius: 18px;

        background:
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 100%);

        color: var(--photo-muted);

        font-size: 12px;
        line-height: 1.7;

        box-shadow: var(--photo-shadow-soft);
    }

    .ad-note strong {
        color: var(--photo-maroon);
        font-weight: 950;
    }

    html.dark .ad-page,
    html.dark body {
        background: #161210 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-page-head,
    html.dark .ad-card,
    html.dark .ad-stat-grid > *,
    html.dark .ad-note {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;

        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-card-head {
        background: #211B17 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .ad-page-head h2,
    html.dark .ad-card-head h2,
    html.dark .ad-table td strong,
    html.dark .ad-donut strong,
    html.dark .ad-queue-copy strong {
        color: #F5EFE8 !important;
    }

    html.dark .ad-page-head p,
    html.dark .ad-card-head p,
    html.dark .ad-chart-summary small,
    html.dark .ad-chart-legend span,
    html.dark .ad-donut-list div,
    html.dark .ad-table td,
    html.dark .ad-muted,
    html.dark .ad-queue-copy span,
    html.dark .ad-note {
        color: #C8B7AD !important;
    }

    html.dark .ad-overline,
    html.dark .ad-chart-summary strong {
        color: #EBA99D !important;
    }

    html.dark .ad-overline::before {
        background: #EBA99D;
    }

    html.dark .ad-select,
    html.dark .ad-bar-chart,
    html.dark .ad-table thead,
    html.dark .ad-queue-item {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-table td,
    html.dark .ad-table th {
        border-color: #3B2E27 !important;
    }

    html.dark .ad-btn-secondary {
        background: #1E1A17 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .ad-btn-primary {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
    }

    @media (max-width: 1280px) {
        .ad-stat-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .ad-dashboard-grid,
        .ad-dashboard-split {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .ad-page-head,
        .ad-card-head,
        .ad-chart-summary {
            align-items: flex-start;
            flex-direction: column;
        }

        .ad-page-head {
            padding: 26px 22px;
        }

        .ad-inline-actions,
        .ad-inline-actions .ad-btn {
            width: 100%;
        }

        .ad-stat-grid {
            grid-template-columns: 1fr;
        }

        .ad-donut-wrap {
            grid-template-columns: 1fr;
            justify-items: center;
        }

        .ad-bar-chart {
            gap: 8px;
            padding-inline: 10px;
        }
    }
</style>

<div class="ad-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">Control center</span>

            <h2>
                Marketplace overview
            </h2>

            <p>
                Prioritized for review speed, platform safety, and financial visibility.
            </p>
        </div>

        <div class="ad-inline-actions">
            <a
                href="{{ route('admin.reports') }}"
                class="ad-btn ad-btn-secondary"
            >
                Export report
            </a>

            <a
                href="{{ route('admin.messages', ['tab' => 'announcements']) }}"
                class="ad-btn ad-btn-primary"
            >
                New announcement
            </a>
        </div>
    </div>

    <section class="ad-stat-grid" aria-label="Platform summary">
        <x-admin.stat-card
            label="Pending applications"
            :value="number_format($accountStats['pending'])"
            detail="Awaiting administrator review"
            icon="users"
            tone="warning"
            :href="route('admin.registrations')"
        />

        <x-admin.stat-card
            label="Active users"
            :value="number_format($accountStats['active'])"
            detail="Approved marketplace accounts"
            icon="users"
            :href="route('admin.users')"
        />

        <x-admin.stat-card
            label="Platform reports"
            value="14"
            trend="+2"
            detail="generated this week"
            icon="reports"
            :href="route('admin.reports')"
        />

        <x-admin.stat-card
            label="Flagged products"
            value="17"
            trend="5 high risk"
            detail="human review needed"
            icon="flag"
            tone="danger"
            :href="route('admin.products', ['view' => 'monitor'])"
        />

        <x-admin.stat-card
            label="Open disputes"
            value="11"
            trend="3 urgent"
            detail="awaiting decision"
            icon="case"
            tone="warning"
            :href="route('admin.complaints')"
        />

        <x-admin.stat-card
            label="Platform revenue"
            value="PHP 420K"
            trend="10%"
            detail="commission rate"
            icon="money"
            :href="route('admin.finance')"
        />
    </section>

    <section class="ad-dashboard-grid">
        <article class="ad-card ad-chart-card">
            <header class="ad-card-head">
                <div>
                    <span class="ad-overline">
                        Performance
                    </span>

                    <h2>
                        Marketplace revenue
                    </h2>

                    <p>
                        Gross merchandise value for the last seven days.
                    </p>
                </div>

                <select class="ad-select" aria-label="Revenue period">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                    <option>This quarter</option>
                </select>
            </header>

            <div class="ad-card-body">
                <div class="ad-chart-summary">
                    <div>
                        <small>
                            Gross merchandise value
                        </small>

                        <strong>
                            PHP 4,206,500
                        </strong>

                        <small>
                            <span class="ad-chart-trend">
                                Up 6.4%
                            </span>
                            vs previous week
                        </small>
                    </div>

                    <div class="ad-chart-legend">
                        <span>
                            <i></i>
                            Gross sales
                        </span>

                        <span>
                            <i class="is-muted"></i>
                            Prior period
                        </span>
                    </div>
                </div>

                <div class="ad-bar-chart" aria-label="Revenue bar chart">
                    @foreach([45, 62, 51, 74, 66, 86, 78] as $height)
                        <div class="ad-bar">
                            <i style="height: {{ $height }}%"></i>

                            <span>
                                {{ ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'][$loop->index] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="ad-card">
            <header class="ad-card-head">
                <div>
                    <span class="ad-overline">
                        Order health
                    </span>

                    <h2>
                        Fulfillment status
                    </h2>

                    <p>
                        386 orders placed today.
                    </p>
                </div>

                <span class="ad-overline">
                    Read-only summary
                </span>
            </header>

            <div class="ad-card-body ad-donut-wrap">
                <div class="ad-donut">
                    <strong>
                        386
                        <small>orders</small>
                    </strong>
                </div>

                <div class="ad-donut-list">
                    <div>
                        <i></i>
                        <span>Processing</span>
                        <b>162</b>
                    </div>

                    <div>
                        <i></i>
                        <span>Shipping</span>
                        <b>93</b>
                    </div>

                    <div>
                        <i></i>
                        <span>Delivered</span>
                        <b>69</b>
                    </div>

                    <div>
                        <i></i>
                        <span>Completed / other</span>
                        <b>62</b>
                    </div>
                </div>
            </div>
        </article>
    </section>

    <section class="ad-dashboard-split">
        <article class="ad-card">
            <header class="ad-card-head">
                <div>
                    <span class="ad-overline">
                        Operations
                    </span>

                    <h2>
                        Recent orders
                    </h2>

                    <p>
                        Latest marketplace purchases across all sellers.
                    </p>
                </div>

                <span class="ad-overline">
                    Seller and logistics owned
                </span>
            </header>

            <div class="ad-table-wrap">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Buyer</th>
                            <th>Seller</th>
                            <th>Total</th>
                            <th>Delivery</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td>
                                    <strong>
                                        {{ $order['id'] }}
                                    </strong>
                                </td>

                                <td>{{ $order['buyer'] }}</td>

                                <td>{{ $order['seller'] }}</td>

                                <td>
                                    <strong>
                                        ₱{{ number_format($order['total'], 2) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $order['delivery'] }}
                                </td>

                                <td>
                                    <span class="ad-status is-{{ strtolower(str_replace(' ', '-', $order['status'])) }}">
                                        {{ $order['status'] }}
                                    </span>
                                </td>

                                <td>
                                    <span class="ad-muted">
                                        Fulfillment access restricted
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <article class="ad-card">
            <header class="ad-card-head">
                <div>
                    <span class="ad-overline">
                        Priority queue
                    </span>

                    <h2>
                        Needs attention
                    </h2>

                    <p>
                        Risk-ranked work for administrators.
                    </p>
                </div>
            </header>

            <div class="ad-queue">
                <a
                    href="{{ route('admin.products', ['view' => 'monitor']) }}"
                    class="ad-queue-item is-danger"
                >
                    <span class="ad-queue-icon">5</span>

                    <span class="ad-queue-copy">
                        <strong>
                            High-risk product flags
                        </strong>

                        <span>
                            Possible weapons or controlled items
                        </span>
                    </span>

                    <span>-&gt;</span>
                </a>

                <a
                    href="{{ route('admin.registrations') }}"
                    class="ad-queue-item is-warning"
                >
                    <span class="ad-queue-icon">{{ number_format($accountStats['pending']) }}</span>

                    <span class="ad-queue-copy">
                        <strong>
                            Pending applications
                        </strong>

                        <span>
                            {{ number_format($accountStats['pending_older_than_day']) }} older than 24 hours
                        </span>
                    </span>

                    <span>-&gt;</span>
                </a>

                <a
                    href="{{ route('admin.complaints') }}"
                    class="ad-queue-item is-warning"
                >
                    <span class="ad-queue-icon">3</span>

                    <span class="ad-queue-copy">
                        <strong>
                            Urgent disputes
                        </strong>

                        <span>
                            Evidence review due today
                        </span>
                    </span>

                    <span>-&gt;</span>
                </a>
            </div>
        </article>
    </section>

    <div class="ad-note">
        <strong>Governance note:</strong>
        Automated product monitoring only creates risk signals. An administrator must review evidence before removing a listing, warning a user, or applying a ban.
    </div>
</div>
@endsection
