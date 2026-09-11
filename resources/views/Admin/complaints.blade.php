@extends('layouts.admin')

@section('title', 'Complaints & Disputes')
@section('subtitle', 'Coordinate evidence, case decisions, returns, and refunds across marketplace roles.')
@section('active', 'complaints')

@php
    $requestedTab = request('tab', 'complaints');

    $tab = in_array($requestedTab, ['complaints', 'returns'], true)
        ? $requestedTab
        : 'complaints';

    $complaints = [
        ['id' => 'DSP-1882', 'title' => 'Item materially different from listing', 'summary' => 'Buyer uploaded unboxing photos; seller response is due in two hours.', 'reported_by' => 'Buyer · Angela Cruz', 'against' => 'Seller · Urban Value', 'status' => 'Open', 'priority' => 'High', 'updated' => '12 min ago'],
        ['id' => 'DSP-1881', 'title' => 'Parcel damaged during transport', 'summary' => 'Packaging and hub scan photos are available from both parties.', 'reported_by' => 'Buyer · Noel Garcia', 'against' => 'Logistics · NorthLink', 'status' => 'Under Review', 'priority' => 'Medium', 'updated' => '35 min ago'],
        ['id' => 'DSP-1880', 'title' => 'Courier conduct complaint', 'summary' => 'Customer service escalated a rider interaction for logistics response.', 'reported_by' => 'Buyer · Lara Ong', 'against' => 'Rider · Carlo Diaz', 'status' => 'Open', 'priority' => 'Critical', 'updated' => '1 hr ago'],
        ['id' => 'DSP-1879', 'title' => 'Charge dispute resolved', 'summary' => 'Payment trail confirmed delivery and buyer accepted the resolution.', 'reported_by' => 'Seller · Casa Local', 'against' => 'Buyer · Marco Reyes', 'status' => 'Resolved', 'priority' => 'Low', 'updated' => 'Yesterday'],
    ];

    $returns = [
        ['id' => 'RRF-6201', 'order' => '#LK-10398', 'buyer' => 'Angela Cruz', 'seller' => 'Urban Value', 'amount' => 1590, 'reason' => 'Not as described', 'stage' => 'Evidence Review', 'status' => 'Pending'],
        ['id' => 'RRF-6200', 'order' => '#LK-10394', 'buyer' => 'Noel Garcia', 'seller' => 'Casa Local', 'amount' => 890, 'reason' => 'Damaged in transit', 'stage' => 'Seller Response', 'status' => 'Under Review'],
        ['id' => 'RRF-6199', 'order' => '#LK-10382', 'buyer' => 'Sarah Lim', 'seller' => 'MNL Tech', 'amount' => 2790, 'reason' => 'Wrong variation', 'stage' => 'Refund Processing', 'status' => 'Approved'],
        ['id' => 'RRF-6198', 'order' => '#LK-10375', 'buyer' => 'Daniel Tan', 'seller' => 'North & Pine', 'amount' => 2190, 'reason' => 'Changed mind', 'stage' => 'Closed', 'status' => 'Rejected'],
    ];

    $pageTitle = $tab === 'returns'
        ? 'Returns and refunds'
        : 'Complaint resolution';

    $pageDescription = 'Keep communication, evidence, and decisions together in one auditable case.';
@endphp

@section('content')
<style>
    :root {
        --cmp-bg: #FBF7F2;
        --cmp-bg-soft: #F6EFE7;
        --cmp-bg-alt: #EFE7DE;
        --cmp-card: #FFFDF9;

        --cmp-border: #EADCCC;
        --cmp-border-strong: #DBCEC1;

        --cmp-maroon: #561C17;
        --cmp-maroon-2: #642920;
        --cmp-maroon-dark: #3E130F;

        --cmp-text: #3B211B;
        --cmp-brown: #6C4936;
        --cmp-muted: #987865;
        --cmp-muted-2: #A99386;

        --cmp-tan: #C19771;

        --cmp-success: #256F4A;
        --cmp-success-soft: #EAF7EF;

        --cmp-warning: #9A5B11;
        --cmp-warning-soft: #FFF6DE;

        --cmp-danger: #B42318;
        --cmp-danger-soft: #FCEBE9;

        --cmp-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --cmp-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ad-complaints-page {
        color: var(--cmp-text);

        --ad-blue: var(--cmp-maroon);
        --ad-blue-dark: var(--cmp-maroon-dark);
        --ad-blue-soft: #F3E4DE;
        --ad-blue-border: #E6C7BE;
    }

    .ad-complaints-page .ad-page-head {
        padding: 32px 36px;

        border: 1px solid var(--cmp-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--cmp-shadow-soft);
    }

    .ad-complaints-page .ad-overline {
        color: var(--cmp-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-complaints-page .ad-page-head h2 {
        margin-top: 10px;

        color: var(--cmp-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-complaints-page .ad-page-head p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--cmp-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .ad-complaints-page .ad-btn-primary {
        background: var(--cmp-maroon) !important;
        border-color: var(--cmp-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ad-complaints-page .ad-btn-primary:hover {
        background: var(--cmp-maroon-dark) !important;
        border-color: var(--cmp-maroon-dark) !important;
    }

    .ad-complaints-page .ad-btn-secondary {
        background: var(--cmp-card) !important;
        border-color: var(--cmp-tan) !important;
        color: var(--cmp-maroon) !important;
    }

    .ad-complaints-page .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--cmp-maroon) !important;
    }

    .ad-complaints-page .ad-btn-danger-soft {
        background: var(--cmp-danger-soft) !important;
        border-color: #F0C9C4 !important;
        color: var(--cmp-danger) !important;
    }

    .ad-complaints-page .ad-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .ad-complaints-page .ad-mini-stat {
        padding: 18px;

        border: 1px solid var(--cmp-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        box-shadow: var(--cmp-shadow-soft);
    }

    .ad-complaints-page .ad-mini-stat span {
        display: block;

        color: var(--cmp-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .ad-complaints-page .ad-mini-stat strong {
        display: block;
        margin-top: 8px;

        color: var(--cmp-maroon);

        font-size: 32px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-complaints-page .ad-mini-stat small {
        display: block;
        margin-top: 8px;

        color: var(--cmp-muted);

        font-size: 11px;
        line-height: 1.5;
    }

    .ad-complaints-page .ad-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 6px;

        border: 1px solid var(--cmp-border);
        border-radius: 16px;

        background: var(--cmp-card);
        box-shadow: var(--cmp-shadow-soft);
    }

    .ad-complaints-page .ad-tab {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;

        padding: 0 16px;

        border-radius: 12px;

        color: var(--cmp-brown);

        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;

        transition: 160ms ease;
    }

    .ad-complaints-page .ad-tab:hover {
        background: var(--cmp-bg-soft);
        color: var(--cmp-maroon);
    }

    .ad-complaints-page .ad-tab.is-active {
        background: var(--cmp-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .ad-complaints-card {
        overflow: hidden;

        border: 1px solid var(--cmp-border) !important;
        border-radius: 24px !important;

        background: var(--cmp-card) !important;
        color: var(--cmp-text) !important;

        box-shadow: var(--cmp-shadow-soft) !important;
    }

    .ad-complaints-page .ad-filter-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;

        padding: 15px;

        border-bottom: 1px solid var(--cmp-border);

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.12), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ad-complaints-page .ad-filter-search {
        position: relative;
        flex: 1;
        max-width: 500px;
    }

    .ad-complaints-page .ad-filter-search svg {
        position: absolute;
        left: 14px;
        top: 50%;

        width: 16px;
        height: 16px;

        color: var(--cmp-muted-2);

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;

        transform: translateY(-50%);
    }

    .ad-complaints-page .ad-filter-search input {
        width: 100%;
        min-height: 42px;
        padding: 0 14px 0 42px;

        border: 1px solid var(--cmp-border);
        border-radius: 14px;

        background: var(--cmp-bg-soft);
        color: var(--cmp-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;
    }

    .ad-complaints-page .ad-filter-search input:focus {
        border-color: var(--cmp-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-complaints-page .ad-filter-search input::placeholder {
        color: var(--cmp-muted-2);
    }

    .ad-complaints-page .ad-select {
        min-height: 42px;
        padding: 0 12px;

        border: 1px solid var(--cmp-border);
        border-radius: 14px;

        background: var(--cmp-bg-soft);
        color: var(--cmp-text);

        font-size: 11px;
        font-weight: 800;
        outline: none;
    }

    .ad-complaints-page .ad-select:focus {
        border-color: var(--cmp-tan);
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-complaints-page .ad-table {
        width: 100%;
        min-width: 960px;
        border-collapse: collapse;
    }

    .ad-complaints-page .ad-table thead {
        background: var(--cmp-bg-soft);
    }

    .ad-complaints-page .ad-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--cmp-border);

        color: var(--cmp-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .ad-complaints-page .ad-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--cmp-brown);

        font-size: 12px;
        vertical-align: middle;
    }

    .ad-complaints-page .ad-table tbody tr:hover td {
        background: var(--cmp-bg-soft);
    }

    .ad-complaints-page .ad-table td strong {
        color: var(--cmp-text);
        font-size: 12px;
        font-weight: 950;
    }

    .ad-complaints-page .ad-table td small {
        display: block;
        margin-top: 4px;

        color: var(--cmp-muted);

        font-size: 10px;
        line-height: 1.4;
    }

    .ad-complaints-page .ad-status,
    .ad-complaints-page .ad-priority {
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

    .ad-complaints-page .ad-status.is-resolved,
    .ad-complaints-page .ad-status.is-approved {
        background: var(--cmp-success-soft);
        border-color: #CFE8DA;
        color: var(--cmp-success);
    }

    .ad-complaints-page .ad-status.is-under-review,
    .ad-complaints-page .ad-status.is-pending {
        background: var(--cmp-warning-soft);
        border-color: #EAD39A;
        color: var(--cmp-warning);
    }

    .ad-complaints-page .ad-status.is-open,
    .ad-complaints-page .ad-status.is-rejected {
        background: var(--cmp-danger-soft);
        border-color: #F0C9C4;
        color: var(--cmp-danger);
    }

    .ad-complaints-page .ad-priority.is-low {
        background: var(--cmp-bg-soft);
        border-color: var(--cmp-border-strong);
        color: var(--cmp-brown);
    }

    .ad-complaints-page .ad-priority.is-medium {
        background: var(--cmp-warning-soft);
        border-color: #EAD39A;
        color: var(--cmp-warning);
    }

    .ad-complaints-page .ad-priority.is-high,
    .ad-complaints-page .ad-priority.is-critical {
        background: var(--cmp-danger-soft);
        border-color: #F0C9C4;
        color: var(--cmp-danger);
    }

    .ad-complaint-toolbar {
        margin-bottom: 14px;
        overflow: hidden;

        border: 1px solid var(--cmp-border);
        border-radius: 20px;

        background: var(--cmp-card);
        box-shadow: var(--cmp-shadow-soft);
    }

    .ad-complaint-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .ad-complaint-item {
        display: grid;
        gap: 16px;

        padding: 18px;

        border: 1px solid var(--cmp-border);
        border-radius: 22px;

        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        box-shadow: var(--cmp-shadow-soft);

        transition:
            transform 160ms ease,
            border-color 160ms ease,
            box-shadow 160ms ease;
    }

    .ad-complaint-item:hover {
        transform: translateY(-3px);
        border-color: var(--cmp-tan);
        box-shadow: var(--cmp-shadow-card);
    }

    .ad-complaint-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .ad-complaint-head small {
        color: var(--cmp-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .ad-complaint-head h3 {
        margin: 6px 0 0;

        color: var(--cmp-text);

        font-size: 15px;
        font-weight: 950;
        line-height: 1.25;
        letter-spacing: -0.035em;
    }

    .ad-complaint-summary {
        margin: 0;

        color: var(--cmp-muted);

        font-size: 12px;
        line-height: 1.7;
    }

    .ad-complaint-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;

        margin: 0;
        padding: 0;
    }

    .ad-complaint-meta div {
        min-width: 0;
        padding: 11px;

        border: 1px solid var(--cmp-border);
        border-radius: 14px;

        background: rgba(246, 239, 231, 0.68);
    }

    .ad-complaint-meta dt {
        color: var(--cmp-muted);

        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .ad-complaint-meta dd {
        overflow: hidden;
        margin: 4px 0 0;

        color: var(--cmp-text);

        font-size: 11px;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ad-complaint-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;

        padding-top: 14px;

        border-top: 1px dashed var(--cmp-border-strong);
    }

    .ad-complaint-updated {
        color: var(--cmp-muted);

        font-size: 11px;
        font-weight: 700;
    }

    .ad-row-actions {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 7px;
    }

    @media (max-width: 1100px) {
        .ad-complaints-page .ad-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ad-complaint-grid {
            grid-template-columns: 1fr;
        }

        .ad-complaints-page .ad-filter-bar {
            align-items: stretch;
            flex-direction: column;
        }

        .ad-complaints-page .ad-filter-search {
            max-width: none;
        }
    }

    @media (max-width: 700px) {
        .ad-complaints-page .ad-page-head {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .ad-complaints-page .ad-summary-grid,
        .ad-complaint-meta {
            grid-template-columns: 1fr;
        }

        .ad-complaints-page .ad-filter-search,
        .ad-complaints-page .ad-select,
        .ad-complaints-page .ad-btn {
            width: 100%;
        }

        .ad-complaint-head,
        .ad-complaint-foot {
            align-items: flex-start;
            flex-direction: column;
        }

        .ad-row-actions {
            width: 100%;
        }
    }

    html.dark .ad-complaints-page .ad-page-head,
    html.dark .ad-complaints-page .ad-mini-stat,
    html.dark .ad-complaints-page .ad-tabs,
    html.dark .ad-complaints-card,
    html.dark .ad-complaint-toolbar,
    html.dark .ad-complaints-page .ad-filter-bar,
    html.dark .ad-complaint-item {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-complaints-page .ad-page-head h2,
    html.dark .ad-complaints-page .ad-mini-stat strong,
    html.dark .ad-complaints-page .ad-table td strong,
    html.dark .ad-complaint-head h3,
    html.dark .ad-complaint-meta dd {
        color: #F5EFE8 !important;
    }

    html.dark .ad-complaints-page .ad-page-head p,
    html.dark .ad-complaints-page .ad-mini-stat span,
    html.dark .ad-complaints-page .ad-mini-stat small,
    html.dark .ad-complaints-page .ad-table td,
    html.dark .ad-complaints-page .ad-table td small,
    html.dark .ad-complaint-summary,
    html.dark .ad-complaint-meta dt,
    html.dark .ad-complaint-updated {
        color: #C8B7AD !important;
    }

    html.dark .ad-complaints-page .ad-overline,
    html.dark .ad-complaints-page .ad-mini-stat strong {
        color: #EBA99D !important;
    }

    html.dark .ad-complaints-page .ad-filter-search input,
    html.dark .ad-complaints-page .ad-select,
    html.dark .ad-complaints-page .ad-table thead,
    html.dark .ad-complaint-meta div {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-complaints-page .ad-table th,
    html.dark .ad-complaints-page .ad-table td {
        border-color: #3B2E27 !important;
    }

    html.dark .ad-complaints-page .ad-table tbody tr:hover td {
        background: #2D1414 !important;
    }

    html.dark .ad-complaints-page .ad-tab {
        color: #C8B7AD !important;
    }

    html.dark .ad-complaints-page .ad-tab:hover {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-complaints-page .ad-tab.is-active {
        background: #8A3A2F !important;
        color: #FFFFFF !important;
    }
</style>

<div class="ad-page ad-complaints-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">
                Case management
            </span>

            <h2>
                {{ $pageTitle }}
            </h2>

            <p>
                {{ $pageDescription }}
            </p>
        </div>

        <button
            class="ad-btn ad-btn-primary"
            type="button"
            data-demo-action="New case form opened"
        >
            Create case
        </button>
    </div>

    <section class="ad-summary-grid" aria-label="Complaint and dispute summary">
        <div class="ad-mini-stat">
            <span>Open cases</span>
            <strong>11</strong>
            <small>3 urgent</small>
        </div>

        <div class="ad-mini-stat">
            <span>Median resolution</span>
            <strong>18.4h</strong>
            <small>Within 24-hour target</small>
        </div>

        <div class="ad-mini-stat">
            <span>Pending returns</span>
            <strong>14</strong>
            <small>₱38,420 at issue</small>
        </div>

        <div class="ad-mini-stat">
            <span>Resolved this week</span>
            <strong>42</strong>
            <small>91% accepted outcomes</small>
        </div>
    </section>

    <nav class="ad-tabs" aria-label="Complaint sections">
        <a
            class="ad-tab {{ $tab === 'complaints' ? 'is-active' : '' }}"
            href="{{ route('admin.complaints', ['tab' => 'complaints']) }}"
        >
            Complaints / Disputes
        </a>

        <a
            class="ad-tab {{ $tab === 'returns' ? 'is-active' : '' }}"
            href="{{ route('admin.complaints', ['tab' => 'returns']) }}"
        >
            Returns / Refunds
        </a>
    </nav>

    @if($tab === 'returns')
        <section class="ad-card ad-complaints-card" id="return-table">
            <div class="ad-filter-bar">
                <label class="ad-filter-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    <input
                        type="search"
                        data-filter-input="#return-table"
                        placeholder="Search return, order, buyer, or seller"
                    >
                </label>

                <select class="ad-select">
                    <option>All stages</option>
                    <option>Evidence Review</option>
                    <option>Seller Response</option>
                    <option>Refund Processing</option>
                </select>
            </div>

            <div class="ad-table-wrap">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Case / order</th>
                            <th>Buyer</th>
                            <th>Seller</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Stage</th>
                            <th>Status</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($returns as $return)
                            @php
                                $statusClass = \Illuminate\Support\Str::slug($return['status']);
                            @endphp

                            <tr
                                data-filter-item
                                data-search="{{ strtolower(implode(' ', $return)) }}"
                            >
                                <td>
                                    <strong>{{ $return['id'] }}</strong>
                                    <small>{{ $return['order'] }}</small>
                                </td>

                                <td>{{ $return['buyer'] }}</td>
                                <td>{{ $return['seller'] }}</td>

                                <td>
                                    <strong>₱{{ number_format($return['amount'], 2) }}</strong>
                                </td>

                                <td>{{ $return['reason'] }}</td>
                                <td>{{ $return['stage'] }}</td>

                                <td>
                                    <span class="ad-status is-{{ $statusClass }}">
                                        {{ $return['status'] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="ad-row-actions">
                                        <button
                                            class="ad-btn ad-btn-secondary ad-btn-sm"
                                            type="button"
                                            data-demo-action="Opening {{ $return['id'] }}"
                                        >
                                            Review
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @else
        <section id="complaint-list">
            <div class="ad-complaint-toolbar">
                <div class="ad-filter-bar">
                    <label class="ad-filter-search">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5"/>
                        </svg>

                        <input
                            type="search"
                            data-filter-input="#complaint-list"
                            placeholder="Search cases, parties, or issue"
                        >
                    </label>

                    <select class="ad-select">
                        <option>Priority: all</option>
                        <option>Critical</option>
                        <option>High</option>
                        <option>Medium</option>
                    </select>
                </div>
            </div>

            <div class="ad-complaint-grid">
                @foreach($complaints as $complaint)
                    @php
                        $statusClass = \Illuminate\Support\Str::slug($complaint['status']);
                        $priorityClass = \Illuminate\Support\Str::slug($complaint['priority']);
                    @endphp

                    <article
                        class="ad-complaint-item"
                        data-filter-item
                        data-search="{{ strtolower(implode(' ', $complaint)) }}"
                    >
                        <header class="ad-complaint-head">
                            <div>
                                <small>{{ $complaint['id'] }}</small>

                                <h3>
                                    {{ $complaint['title'] }}
                                </h3>
                            </div>

                            <span class="ad-priority is-{{ $priorityClass }}">
                                {{ $complaint['priority'] }}
                            </span>
                        </header>

                        <p class="ad-complaint-summary">
                            {{ $complaint['summary'] }}
                        </p>

                        <dl class="ad-complaint-meta">
                            <div>
                                <dt>Reported by</dt>
                                <dd>{{ $complaint['reported_by'] }}</dd>
                            </div>

                            <div>
                                <dt>Against</dt>
                                <dd>{{ $complaint['against'] }}</dd>
                            </div>

                            <div>
                                <dt>Status</dt>
                                <dd>
                                    <span class="ad-status is-{{ $statusClass }}">
                                        {{ $complaint['status'] }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt>Updated</dt>
                                <dd>{{ $complaint['updated'] }}</dd>
                            </div>
                        </dl>

                        <footer class="ad-complaint-foot">
                            <span class="ad-complaint-updated">
                                Last activity: {{ $complaint['updated'] }}
                            </span>

                            <div class="ad-row-actions">
                                <button
                                    class="ad-btn ad-btn-secondary ad-btn-sm"
                                    type="button"
                                    data-demo-action="Opening case {{ $complaint['id'] }}"
                                >
                                    Review
                                </button>

                                @if($complaint['status'] !== 'Resolved')
                                    <button
                                        class="ad-btn ad-btn-primary ad-btn-sm"
                                        type="button"
                                        data-demo-action="Decision panel opened for {{ $complaint['id'] }}"
                                    >
                                        Decide
                                    </button>
                                @endif
                            </div>
                        </footer>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection