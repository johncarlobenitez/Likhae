@extends('layouts.admin')

@section('title', 'Finance')
@section('subtitle', 'Monitor the fixed 10% platform commission, payments, and marketplace settlements.')
@section('active', 'finance')

@php
    $requestedTab = request('tab', 'commission');

    $tab = in_array($requestedTab, ['commission', 'transactions', 'payments'], true)
        ? $requestedTab
        : 'commission';

    $financeTabs = [
        'commission' => 'Commission Management',
        'transactions' => 'Transactions',
        'payments' => 'Payments',
    ];

    $pageTitle = match ($tab) {
        'transactions' => 'Transaction ledger',
        'payments' => 'Payment monitoring',
        default => 'Commission management',
    };

    $transactions = [
        ['id' => 'TXN-980142', 'order' => '#LK-10482', 'seller' => 'LIKHA Studio', 'gross' => 12990, 'commission' => 1299, 'net' => 11691, 'method' => 'GCash', 'date' => 'Sep 4, 2026', 'status' => 'Paid'],
        ['id' => 'TXN-980141', 'order' => '#LK-10481', 'seller' => 'MNL Tech', 'gross' => 5580, 'commission' => 558, 'net' => 5022, 'method' => 'COD', 'date' => 'Sep 4, 2026', 'status' => 'Processing'],
        ['id' => 'TXN-980140', 'order' => '#LK-10480', 'seller' => 'MNL Tech', 'gross' => 1490, 'commission' => 149, 'net' => 1341, 'method' => 'Maya', 'date' => 'Sep 4, 2026', 'status' => 'Paid'],
        ['id' => 'TXN-980139', 'order' => '#LK-10479', 'seller' => 'Casa Local', 'gross' => 3490, 'commission' => 349, 'net' => 3141, 'method' => 'Card', 'date' => 'Sep 3, 2026', 'status' => 'Paid'],
        ['id' => 'TXN-980138', 'order' => '#LK-10478', 'seller' => 'Paper & Loom', 'gross' => 1980, 'commission' => 198, 'net' => 1782, 'method' => 'GCash', 'date' => 'Sep 3, 2026', 'status' => 'Failed'],
    ];
@endphp

@section('content')
<style>
    :root {
        --fin-bg: #FBF7F2;
        --fin-bg-soft: #F6EFE7;
        --fin-bg-alt: #EFE7DE;
        --fin-card: #FFFDF9;

        --fin-border: #EADCCC;
        --fin-border-strong: #DBCEC1;

        --fin-maroon: #561C17;
        --fin-maroon-2: #642920;
        --fin-maroon-dark: #3E130F;

        --fin-text: #3B211B;
        --fin-brown: #6C4936;
        --fin-muted: #987865;
        --fin-muted-2: #A99386;

        --fin-tan: #C19771;

        --fin-success: #256F4A;
        --fin-success-soft: #EAF7EF;

        --fin-warning: #9A5B11;
        --fin-warning-soft: #FFF6DE;

        --fin-danger: #B42318;
        --fin-danger-soft: #FCEBE9;

        --fin-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --fin-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ad-finance-page {
        color: var(--fin-text);

        --ad-blue: var(--fin-maroon);
        --ad-blue-dark: var(--fin-maroon-dark);
        --ad-blue-soft: #F3E4DE;
        --ad-blue-border: #E6C7BE;
    }

    .ad-finance-page .ad-page-head {
        padding: 32px 36px;

        border: 1px solid var(--fin-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--fin-shadow-soft);
    }

    .ad-finance-page .ad-overline {
        color: var(--fin-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-finance-page .ad-page-head h2 {
        margin-top: 10px;

        color: var(--fin-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-finance-page .ad-page-head p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--fin-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .ad-finance-page .ad-btn-primary {
        background: var(--fin-maroon) !important;
        border-color: var(--fin-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ad-finance-page .ad-btn-primary:hover {
        background: var(--fin-maroon-dark) !important;
        border-color: var(--fin-maroon-dark) !important;
    }

    .ad-finance-page .ad-btn-secondary {
        background: var(--fin-card) !important;
        border-color: var(--fin-tan) !important;
        color: var(--fin-maroon) !important;
    }

    .ad-finance-page .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--fin-maroon) !important;
    }

    .ad-finance-page .ad-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .ad-finance-page .ad-stat,
    .ad-finance-page .ad-stat-grid > * {
        border: 1px solid var(--fin-border) !important;
        border-radius: 22px !important;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%) !important;

        color: var(--fin-text) !important;
        box-shadow: var(--fin-shadow-soft) !important;
    }

    .ad-finance-page .ad-stat:hover,
    .ad-finance-page .ad-stat-grid > *:hover {
        border-color: var(--fin-tan) !important;
        box-shadow: var(--fin-shadow-card) !important;
    }

    .ad-finance-page .ad-stat-icon {
        background: #F1E4D7 !important;
        color: var(--fin-maroon) !important;
    }

    .ad-finance-page .ad-stat-icon svg {
        stroke: currentColor !important;
    }

    .ad-finance-page .ad-stat-label,
    .ad-finance-page .ad-stat-copy small {
        color: var(--fin-muted) !important;
    }

    .ad-finance-page .ad-stat-copy strong {
        color: var(--fin-text) !important;
    }

    .ad-finance-page .ad-stat-copy small b {
        color: var(--fin-maroon) !important;
    }

    .ad-finance-page .ad-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 6px;

        border: 1px solid var(--fin-border);
        border-radius: 16px;

        background: var(--fin-card);
        box-shadow: var(--fin-shadow-soft);
    }

    .ad-finance-page .ad-tab {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;

        padding: 0 16px;

        border-radius: 12px;

        color: var(--fin-brown);

        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;

        transition: 160ms ease;
    }

    .ad-finance-page .ad-tab:hover {
        background: var(--fin-bg-soft);
        color: var(--fin-maroon);
    }

    .ad-finance-page .ad-tab.is-active {
        background: var(--fin-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .ad-finance-page .ad-dashboard-split {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(340px, 0.8fr);
        gap: 18px;
        align-items: start;
    }

    .ad-finance-page .ad-card {
        overflow: hidden;

        border: 1px solid var(--fin-border) !important;
        border-radius: 24px !important;

        background: var(--fin-card) !important;
        color: var(--fin-text) !important;

        box-shadow: var(--fin-shadow-soft) !important;
    }

    .ad-finance-page .ad-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding: 20px 22px;

        border-bottom: 1px solid var(--fin-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .ad-finance-page .ad-card-head h2,
    .ad-finance-page .ad-card-head h3 {
        margin-top: 7px;

        color: var(--fin-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 32px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-finance-page .ad-card-head p {
        color: var(--fin-muted) !important;
    }

    .ad-finance-page .ad-settings-section {
        display: grid;
        gap: 18px;

        padding: 22px;
    }

    .ad-finance-page .ad-note {
        padding: 16px 18px;

        border: 1px solid var(--fin-border);
        border-radius: 18px;

        background:
            radial-gradient(circle at 96% 8%, rgba(193, 151, 113, 0.12), transparent 28%),
            var(--fin-bg-soft) !important;

        color: var(--fin-brown);

        font-size: 12px;
        line-height: 1.7;
    }

    .ad-finance-page .ad-note strong {
        color: var(--fin-maroon);
        font-weight: 950;
    }

    .ad-finance-page .ad-field {
        display: grid;
        gap: 7px;
    }

    .ad-finance-page .ad-field span {
        color: var(--fin-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .ad-finance-page .ad-field small {
        color: var(--fin-muted);
        font-size: 11px;
        line-height: 1.6;
    }

    .ad-finance-page .ad-field input,
    .ad-finance-page .ad-field select {
        width: 100%;
        min-height: 43px;
        padding: 0 13px;

        border: 1px solid var(--fin-border);
        border-radius: 14px;

        background: var(--fin-bg-soft);
        color: var(--fin-text);

        font-size: 12px;
        font-weight: 700;
        outline: 0;

        transition: 160ms ease;
    }

    .ad-finance-page .ad-field input:focus,
    .ad-finance-page .ad-field select:focus {
        border-color: var(--fin-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-finance-page .ad-field input[readonly] {
        background: #EFE7DE;
        color: var(--fin-maroon);
        cursor: not-allowed;
    }

    .ad-finance-page .ad-setting-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;

        padding: 16px 0;

        border-bottom: 1px solid var(--fin-border);
    }

    .ad-finance-page .ad-setting-row:last-child {
        border-bottom: 0;
    }

    .ad-finance-page .ad-setting-row strong {
        display: block;

        color: var(--fin-text);

        font-size: 13px;
        font-weight: 950;
        letter-spacing: -0.025em;
    }

    .ad-finance-page .ad-setting-row p {
        margin: 5px 0 0;

        color: var(--fin-muted);

        font-size: 11px;
        line-height: 1.6;
    }

    .ad-finance-total {
        color: var(--fin-maroon) !important;
        font-size: 17px !important;
    }

    .ad-finance-page .ad-inline-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ad-finance-page .ad-status {
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

    .ad-finance-page .ad-status.is-active,
    .ad-finance-page .ad-status.is-paid {
        background: var(--fin-success-soft);
        border-color: #CFE8DA;
        color: var(--fin-success);
    }

    .ad-finance-page .ad-status.is-processing,
    .ad-finance-page .ad-status.is-pending {
        background: var(--fin-warning-soft);
        border-color: #EAD39A;
        color: var(--fin-warning);
    }

    .ad-finance-page .ad-status.is-failed {
        background: var(--fin-danger-soft);
        border-color: #F0C9C4;
        color: var(--fin-danger);
    }

    .ad-finance-page .ad-filter-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;

        padding: 15px;

        border-bottom: 1px solid var(--fin-border);

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.12), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ad-finance-page .ad-filter-search {
        position: relative;
        flex: 1;
        max-width: 500px;
    }

    .ad-finance-page .ad-filter-search svg {
        position: absolute;
        left: 14px;
        top: 50%;

        width: 16px;
        height: 16px;

        color: var(--fin-muted-2);

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;

        transform: translateY(-50%);
    }

    .ad-finance-page .ad-filter-search input {
        width: 100%;
        min-height: 42px;
        padding: 0 14px 0 42px;

        border: 1px solid var(--fin-border);
        border-radius: 14px;

        background: var(--fin-bg-soft);
        color: var(--fin-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;
    }

    .ad-finance-page .ad-filter-search input:focus {
        border-color: var(--fin-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-finance-page .ad-filter-search input::placeholder {
        color: var(--fin-muted-2);
    }

    .ad-finance-page .ad-select {
        min-height: 42px;
        padding: 0 12px;

        border: 1px solid var(--fin-border);
        border-radius: 14px;

        background: var(--fin-bg-soft);
        color: var(--fin-text);

        font-size: 11px;
        font-weight: 800;
        outline: none;
    }

    .ad-finance-page .ad-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .ad-finance-page .ad-table thead {
        background: var(--fin-bg-soft);
    }

    .ad-finance-page .ad-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--fin-border);

        color: var(--fin-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .ad-finance-page .ad-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--fin-brown);

        font-size: 12px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .ad-finance-page .ad-table tbody tr:hover td {
        background: var(--fin-bg-soft);
    }

    .ad-finance-page .ad-table td strong {
        color: var(--fin-text);
        font-size: 12px;
        font-weight: 950;
    }

    .ad-finance-page .ad-table td small {
        display: block;
        margin-top: 4px;

        color: var(--fin-muted);

        font-size: 10px;
        line-height: 1.4;
    }

    .ad-finance-page .ad-table td:last-child {
        text-align: right;
    }

    @media (max-width: 1180px) {
        .ad-finance-page .ad-stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ad-finance-page .ad-dashboard-split {
            grid-template-columns: 1fr;
        }

        .ad-finance-page .ad-filter-bar {
            align-items: stretch;
            flex-direction: column;
        }

        .ad-finance-page .ad-filter-search {
            max-width: none;
        }
    }

    @media (max-width: 700px) {
        .ad-finance-page .ad-page-head {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .ad-finance-page .ad-stat-grid {
            grid-template-columns: 1fr;
        }

        .ad-finance-page .ad-inline-actions,
        .ad-finance-page .ad-filter-search,
        .ad-finance-page .ad-select,
        .ad-finance-page .ad-btn {
            width: 100%;
        }

        .ad-finance-page .ad-setting-row {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    html.dark .ad-finance-page .ad-page-head,
    html.dark .ad-finance-page .ad-stat,
    html.dark .ad-finance-page .ad-stat-grid > *,
    html.dark .ad-finance-page .ad-tabs,
    html.dark .ad-finance-page .ad-card,
    html.dark .ad-finance-page .ad-card-head,
    html.dark .ad-finance-page .ad-filter-bar,
    html.dark .ad-finance-page .ad-note {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-finance-page .ad-page-head h2,
    html.dark .ad-finance-page .ad-card-head h2,
    html.dark .ad-finance-page .ad-card-head h3,
    html.dark .ad-finance-page .ad-stat-copy strong,
    html.dark .ad-finance-page .ad-setting-row strong,
    html.dark .ad-finance-page .ad-table td strong {
        color: #F5EFE8 !important;
    }

    html.dark .ad-finance-page .ad-page-head p,
    html.dark .ad-finance-page .ad-card-head p,
    html.dark .ad-finance-page .ad-stat-label,
    html.dark .ad-finance-page .ad-stat-copy small,
    html.dark .ad-finance-page .ad-setting-row p,
    html.dark .ad-finance-page .ad-table td,
    html.dark .ad-finance-page .ad-table td small,
    html.dark .ad-finance-page .ad-note {
        color: #C8B7AD !important;
    }

    html.dark .ad-finance-page .ad-overline,
    html.dark .ad-finance-page .ad-finance-total {
        color: #EBA99D !important;
    }

    html.dark .ad-finance-page .ad-tab {
        color: #C8B7AD !important;
    }

    html.dark .ad-finance-page .ad-tab:hover {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-finance-page .ad-tab.is-active {
        background: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .ad-finance-page .ad-stat-icon {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-finance-page .ad-field input,
    html.dark .ad-finance-page .ad-field select,
    html.dark .ad-finance-page .ad-filter-search input,
    html.dark .ad-finance-page .ad-select,
    html.dark .ad-finance-page .ad-table thead {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-finance-page .ad-table th,
    html.dark .ad-finance-page .ad-table td,
    html.dark .ad-finance-page .ad-setting-row {
        border-color: #3B2E27 !important;
    }

    html.dark .ad-finance-page .ad-table tbody tr:hover td {
        background: #2D1414 !important;
    }
</style>

<div class="ad-page ad-finance-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">
                Financial control
            </span>

            <h2>
                {{ $pageTitle }}
            </h2>

            <p>
                Reconcile platform earnings and seller settlements using one consistent commission rule.
            </p>
        </div>

        <a
            href="{{ route('admin.reports') }}"
            class="ad-btn ad-btn-secondary"
        >
            Financial reports
        </a>
    </div>

    <section class="ad-stat-grid" aria-label="Finance summary">
        <x-admin.stat-card
            label="Gross sales"
            value="₱4.20M"
            trend="+6.4%"
            detail="this week"
            icon="money"
        />

        <x-admin.stat-card
            label="Commission earned"
            value="₱420,650"
            trend="10%"
            detail="fixed platform rate"
            icon="chart"
        />

        <x-admin.stat-card
            label="Seller net amount"
            value="₱3.78M"
            detail="before other adjustments"
            icon="money"
            tone="neutral"
        />

        <x-admin.stat-card
            label="Pending settlement"
            value="₱286,420"
            detail="48 transactions"
            icon="orders"
            tone="warning"
        />
    </section>

    <nav class="ad-tabs" aria-label="Finance sections">
        @foreach($financeTabs as $key => $label)
            <a
                class="ad-tab {{ $tab === $key ? 'is-active' : '' }}"
                href="{{ route('admin.finance', ['tab' => $key]) }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </nav>

    @if($tab === 'commission')
        <section class="ad-dashboard-split">
            <article class="ad-card">
                <header class="ad-card-head">
                    <div>
                        <span class="ad-overline">
                            Platform rule
                        </span>

                        <h2>
                            Marketplace commission
                        </h2>

                        <p>
                            Applied to completed eligible orders.
                        </p>
                    </div>

                    <span class="ad-status is-active">
                        Active
                    </span>
                </header>

                <form
                    class="ad-settings-section"
                    data-demo-form
                    data-success-message="Commission policy saved for this frontend preview"
                >
                    <div class="ad-note">
                        <strong>Current policy:</strong>
                        LIKHAE retains 10% of the eligible order amount. The seller receives the remaining 90% before refunds or approved adjustments.
                    </div>

                    <label class="ad-field">
                        <span>Platform commission</span>
                        <input value="10%" readonly>
                        <small>
                            This project requirement is fixed at 10%. Connect policy changes to an approval workflow if made configurable later.
                        </small>
                    </label>

                    <label class="ad-field">
                        <span>Effective scope</span>
                        <select>
                            <option>All marketplace categories</option>
                        </select>
                    </label>

                    <label class="ad-field">
                        <span>Settlement trigger</span>
                        <select>
                            <option>Buyer confirms receipt / order completes</option>
                        </select>
                    </label>

                    <div class="ad-inline-actions">
                        <button
                            class="ad-btn ad-btn-primary"
                            type="submit"
                        >
                            Save policy
                        </button>

                        <button
                            class="ad-btn ad-btn-secondary"
                            type="button"
                            data-demo-action="Opening commission history"
                        >
                            View change history
                        </button>
                    </div>
                </form>
            </article>

            <article class="ad-card">
                <header class="ad-card-head">
                    <div>
                        <span class="ad-overline">
                            Example calculation
                        </span>

                        <h3>
                            Order #LK-10482
                        </h3>

                        <p>
                            Transparent commission breakdown.
                        </p>
                    </div>
                </header>

                <div class="ad-settings-section">
                    <div class="ad-setting-row">
                        <div>
                            <strong>Eligible order amount</strong>
                            <p>After buyer discounts</p>
                        </div>

                        <strong>₱12,990.00</strong>
                    </div>

                    <div class="ad-setting-row">
                        <div>
                            <strong>LIKHAE commission</strong>
                            <p>10% × ₱12,990.00</p>
                        </div>

                        <strong>₱1,299.00</strong>
                    </div>

                    <div class="ad-setting-row">
                        <div>
                            <strong>Seller net amount</strong>
                            <p>Before approved adjustments</p>
                        </div>

                        <strong class="ad-finance-total">
                            ₱11,691.00
                        </strong>
                    </div>
                </div>
            </article>
        </section>
    @else
        <section class="ad-card" id="transaction-table">
            <div class="ad-filter-bar">
                <label class="ad-filter-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    <input
                        type="search"
                        data-filter-input="#transaction-table"
                        placeholder="Search transaction, order, or seller"
                    >
                </label>

                <div class="ad-inline-actions">
                    <select class="ad-select">
                        <option>All statuses</option>
                        <option>Paid</option>
                        <option>Processing</option>
                        <option>Failed</option>
                    </select>

                    <button
                        class="ad-btn ad-btn-secondary ad-btn-sm"
                        type="button"
                        data-demo-action="Finance table exported"
                    >
                        Export
                    </button>
                </div>
            </div>

            <div class="ad-table-wrap">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Transaction / order</th>
                            <th>Seller</th>
                            <th>Gross amount</th>
                            <th>Commission (10%)</th>
                            <th>Net amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($transactions as $transaction)
                            @php
                                $statusClass = \Illuminate\Support\Str::slug($transaction['status']);
                            @endphp

                            <tr
                                data-filter-item
                                data-search="{{ strtolower(implode(' ', $transaction)) }}"
                            >
                                <td>
                                    <strong>
                                        {{ $transaction['id'] }}
                                    </strong>

                                    <small>
                                        {{ $transaction['order'] }}
                                    </small>
                                </td>

                                <td>{{ $transaction['seller'] }}</td>

                                <td>
                                    ₱{{ number_format($transaction['gross'], 2) }}
                                </td>

                                <td>
                                    <strong>
                                        ₱{{ number_format($transaction['commission'], 2) }}
                                    </strong>
                                </td>

                                <td>
                                    ₱{{ number_format($transaction['net'], 2) }}
                                </td>

                                <td>{{ $transaction['method'] }}</td>
                                <td>{{ $transaction['date'] }}</td>

                                <td>
                                    <span class="ad-status is-{{ $statusClass }}">
                                        {{ $transaction['status'] }}
                                    </span>
                                </td>

                                <td>
                                    <button
                                        class="ad-btn ad-btn-secondary ad-btn-sm"
                                        type="button"
                                        data-demo-action="Opening {{ $transaction['id'] }}"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
@endsection