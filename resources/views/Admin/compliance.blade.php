@extends('layouts.admin')

@section('title', 'Compliance')
@section('subtitle', 'Review seller obligations and product-violation reports with traceable decisions.')
@section('active', 'compliance')

@php
    $requestedTab = request('tab', 'sellers');

    $tab = in_array($requestedTab, ['sellers', 'violations'], true)
        ? $requestedTab
        : 'sellers';

    $sellers = [
        ['seller' => 'LIKHA Studio', 'category' => 'Fashion & Home', 'documents' => 'Verified', 'score' => '98 / 100', 'violations' => 0, 'last' => 'Sep 1, 2026', 'status' => 'Compliant'],
        ['seller' => 'Wellness Corner', 'category' => 'Beauty & Wellness', 'documents' => 'Verified', 'score' => '74 / 100', 'violations' => 2, 'last' => 'Sep 4, 2026', 'status' => 'Under Review'],
        ['seller' => 'RapidCart PH', 'category' => 'General Merchandise', 'documents' => 'Expired permit', 'score' => '42 / 100', 'violations' => 5, 'last' => 'Sep 4, 2026', 'status' => 'Suspended'],
        ['seller' => 'Paper & Loom', 'category' => 'Books & Stationery', 'documents' => 'Verified', 'score' => '92 / 100', 'violations' => 0, 'last' => 'Aug 29, 2026', 'status' => 'Compliant'],
    ];

    $reports = [
        ['id' => 'VIO-2281', 'product' => 'Herbal Relief Capsules', 'seller' => 'Wellness Corner', 'reported' => 'Buyer · Camille S.', 'reason' => 'Unverified medical claim', 'evidence' => '3 files', 'status' => 'Under Review'],
        ['id' => 'VIO-2280', 'product' => 'Replica Collector Pistol', 'seller' => 'Hobby House', 'reported' => 'Seller · North & Pine', 'reason' => 'Possible prohibited weapon', 'evidence' => '2 files', 'status' => 'Open'],
        ['id' => 'VIO-2279', 'product' => 'Industrial Solvent 1L', 'seller' => 'BuildRight', 'reported' => 'Logistics · NorthLink', 'reason' => 'Undeclared hazardous item', 'evidence' => '4 files', 'status' => 'Open'],
        ['id' => 'VIO-2278', 'product' => 'Counterfeit Logo Wallet', 'seller' => 'Urban Value', 'reported' => 'Rider · Juan D.', 'reason' => 'Suspected counterfeit', 'evidence' => '1 file', 'status' => 'Resolved'],
    ];

    $pageTitle = $tab === 'violations'
        ? 'Product violation reports'
        : 'Seller compliance';

    $pageDescription = 'Apply published policy consistently and keep evidence attached to every enforcement action.';
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

    .ad-compliance-page {
        color: var(--cmp-text);

        --ad-blue: var(--cmp-maroon);
        --ad-blue-dark: var(--cmp-maroon-dark);
        --ad-blue-soft: #F3E4DE;
        --ad-blue-border: #E6C7BE;
    }

    .ad-compliance-page .ad-page-head {
        padding: 32px 36px;

        border: 1px solid var(--cmp-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--cmp-shadow-soft);
    }

    .ad-compliance-page .ad-overline {
        color: var(--cmp-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-compliance-page .ad-page-head h2 {
        margin-top: 10px;

        color: var(--cmp-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-compliance-page .ad-page-head p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--cmp-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .ad-compliance-page .ad-btn-primary {
        background: var(--cmp-maroon) !important;
        border-color: var(--cmp-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ad-compliance-page .ad-btn-primary:hover {
        background: var(--cmp-maroon-dark) !important;
        border-color: var(--cmp-maroon-dark) !important;
    }

    .ad-compliance-page .ad-btn-secondary {
        background: var(--cmp-card) !important;
        border-color: var(--cmp-tan) !important;
        color: var(--cmp-maroon) !important;
    }

    .ad-compliance-page .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--cmp-maroon) !important;
    }

    .ad-compliance-page .ad-btn-danger-soft {
        background: var(--cmp-danger-soft) !important;
        border-color: #F0C9C4 !important;
        color: var(--cmp-danger) !important;
    }

    .ad-compliance-page .ad-btn-danger-soft:hover {
        background: #F7DCD7 !important;
        border-color: var(--cmp-danger) !important;
    }

    .ad-compliance-page .ad-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .ad-compliance-page .ad-mini-stat {
        padding: 18px;

        border: 1px solid var(--cmp-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        box-shadow: var(--cmp-shadow-soft);
    }

    .ad-compliance-page .ad-mini-stat span {
        display: block;

        color: var(--cmp-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .ad-compliance-page .ad-mini-stat strong {
        display: block;
        margin-top: 8px;

        color: var(--cmp-maroon);

        font-size: 32px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-compliance-page .ad-mini-stat small {
        display: block;
        margin-top: 8px;

        color: var(--cmp-muted);

        font-size: 11px;
        line-height: 1.5;
    }

    .ad-compliance-page .ad-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 6px;

        border: 1px solid var(--cmp-border);
        border-radius: 16px;

        background: var(--cmp-card);
        box-shadow: var(--cmp-shadow-soft);
    }

    .ad-compliance-page .ad-tab {
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

    .ad-compliance-page .ad-tab:hover {
        background: var(--cmp-bg-soft);
        color: var(--cmp-maroon);
    }

    .ad-compliance-page .ad-tab.is-active {
        background: var(--cmp-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .ad-compliance-card {
        overflow: hidden;

        border: 1px solid var(--cmp-border) !important;
        border-radius: 24px !important;

        background: var(--cmp-card) !important;
        color: var(--cmp-text) !important;

        box-shadow: var(--cmp-shadow-soft) !important;
    }

    .ad-compliance-page .ad-filter-bar {
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

    .ad-compliance-page .ad-filter-search {
        position: relative;
        flex: 1;
        max-width: 480px;
    }

    .ad-compliance-page .ad-filter-search svg {
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

    .ad-compliance-page .ad-filter-search input {
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

    .ad-compliance-page .ad-filter-search input:focus {
        border-color: var(--cmp-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-compliance-page .ad-filter-search input::placeholder {
        color: var(--cmp-muted-2);
    }

    .ad-compliance-page .ad-select {
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

    .ad-compliance-page .ad-select:focus {
        border-color: var(--cmp-tan);
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-compliance-page .ad-table {
        width: 100%;
        min-width: 920px;
        border-collapse: collapse;
    }

    .ad-compliance-page .ad-table thead {
        background: var(--cmp-bg-soft);
    }

    .ad-compliance-page .ad-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--cmp-border);

        color: var(--cmp-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .ad-compliance-page .ad-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--cmp-brown);

        font-size: 12px;
        vertical-align: middle;
    }

    .ad-compliance-page .ad-table tbody tr:hover td {
        background: var(--cmp-bg-soft);
    }

    .ad-compliance-page .ad-table td strong {
        color: var(--cmp-text);
        font-size: 12px;
        font-weight: 950;
    }

    .ad-compliance-page .ad-table td small {
        display: block;
        margin-top: 4px;

        color: var(--cmp-muted);

        font-size: 10px;
        line-height: 1.4;
    }

    .ad-compliance-page .ad-status {
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

    .ad-compliance-page .ad-status.is-compliant,
    .ad-compliance-page .ad-status.is-resolved {
        background: var(--cmp-success-soft);
        border-color: #CFE8DA;
        color: var(--cmp-success);
    }

    .ad-compliance-page .ad-status.is-under-review {
        background: var(--cmp-warning-soft);
        border-color: #EAD39A;
        color: var(--cmp-warning);
    }

    .ad-compliance-page .ad-status.is-open,
    .ad-compliance-page .ad-status.is-suspended {
        background: var(--cmp-danger-soft);
        border-color: #F0C9C4;
        color: var(--cmp-danger);
    }

    .ad-compliance-page .ad-row-actions {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 7px;
    }

    .ad-compliance-empty {
        padding: 48px 24px;

        color: var(--cmp-muted);

        font-size: 13px;
        font-weight: 700;
        text-align: center;
    }

    @media (max-width: 1100px) {
        .ad-compliance-page .ad-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ad-compliance-page .ad-filter-bar {
            align-items: stretch;
            flex-direction: column;
        }

        .ad-compliance-page .ad-filter-search {
            max-width: none;
        }
    }

    @media (max-width: 700px) {
        .ad-compliance-page .ad-page-head {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .ad-compliance-page .ad-summary-grid {
            grid-template-columns: 1fr;
        }

        .ad-compliance-page .ad-filter-search,
        .ad-compliance-page .ad-select,
        .ad-compliance-page .ad-btn {
            width: 100%;
        }
    }

    html.dark .ad-compliance-page .ad-page-head,
    html.dark .ad-compliance-page .ad-mini-stat,
    html.dark .ad-compliance-page .ad-tabs,
    html.dark .ad-compliance-card,
    html.dark .ad-compliance-page .ad-filter-bar {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-compliance-page .ad-page-head h2,
    html.dark .ad-compliance-page .ad-mini-stat strong,
    html.dark .ad-compliance-page .ad-table td strong {
        color: #F5EFE8 !important;
    }

    html.dark .ad-compliance-page .ad-page-head p,
    html.dark .ad-compliance-page .ad-mini-stat span,
    html.dark .ad-compliance-page .ad-mini-stat small,
    html.dark .ad-compliance-page .ad-table td,
    html.dark .ad-compliance-page .ad-table td small {
        color: #C8B7AD !important;
    }

    html.dark .ad-compliance-page .ad-overline,
    html.dark .ad-compliance-page .ad-mini-stat strong {
        color: #EBA99D !important;
    }

    html.dark .ad-compliance-page .ad-filter-search input,
    html.dark .ad-compliance-page .ad-select,
    html.dark .ad-compliance-page .ad-table thead {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-compliance-page .ad-table th,
    html.dark .ad-compliance-page .ad-table td {
        border-color: #3B2E27 !important;
    }

    html.dark .ad-compliance-page .ad-table tbody tr:hover td {
        background: #2D1414 !important;
    }

    html.dark .ad-compliance-page .ad-tab {
        color: #C8B7AD !important;
    }

    html.dark .ad-compliance-page .ad-tab:hover {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-compliance-page .ad-tab.is-active {
        background: #8A3A2F !important;
        color: #FFFFFF !important;
    }
</style>

<div class="ad-page ad-compliance-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">
                Trust & safety
            </span>

            <h2>
                {{ $pageTitle }}
            </h2>

            <p>
                {{ $pageDescription }}
            </p>
        </div>

        <a
            class="ad-btn ad-btn-secondary"
            href="{{ route('admin.settings', ['tab' => 'policies']) }}"
        >
            View policies
        </a>
    </div>

    <section class="ad-summary-grid" aria-label="Compliance summary">
        <div class="ad-mini-stat">
            <span>Verified sellers</span>
            <strong>1,806</strong>
            <small>93.9% of sellers</small>
        </div>

        <div class="ad-mini-stat">
            <span>Under review</span>
            <strong>28</strong>
            <small>12 due today</small>
        </div>

        <div class="ad-mini-stat">
            <span>Open violations</span>
            <strong>17</strong>
            <small>5 high priority</small>
        </div>

        <div class="ad-mini-stat">
            <span>Suspended sellers</span>
            <strong>9</strong>
            <small>Pending remediation</small>
        </div>
    </section>

    <nav class="ad-tabs" aria-label="Compliance sections">
        <a
            class="ad-tab {{ $tab === 'sellers' ? 'is-active' : '' }}"
            href="{{ route('admin.compliance', ['tab' => 'sellers']) }}"
        >
            Seller Compliance
        </a>

        <a
            class="ad-tab {{ $tab === 'violations' ? 'is-active' : '' }}"
            href="{{ route('admin.compliance', ['tab' => 'violations']) }}"
        >
            Product Violations
        </a>
    </nav>

    <section class="ad-card ad-compliance-card" id="compliance-table">
        <div class="ad-filter-bar">
            <label class="ad-filter-search">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>

                <input
                    type="search"
                    data-filter-input="#compliance-table"
                    placeholder="Search seller, product, or case ID"
                >
            </label>

            <select class="ad-select">
                <option>All statuses</option>
                <option>Open</option>
                <option>Under Review</option>
                <option>Resolved</option>
                <option>Compliant</option>
                <option>Suspended</option>
            </select>
        </div>

        <div class="ad-table-wrap">
            <table class="ad-table">
                @if($tab === 'violations')
                    <thead>
                        <tr>
                            <th>Report / product</th>
                            <th>Seller</th>
                            <th>Reported by</th>
                            <th>Reason</th>
                            <th>Evidence</th>
                            <th>Status</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($reports as $report)
                            @php
                                $statusClass = \Illuminate\Support\Str::slug($report['status']);
                            @endphp

                            <tr
                                data-filter-item
                                data-search="{{ strtolower(implode(' ', $report)) }}"
                            >
                                <td>
                                    <strong>
                                        {{ $report['product'] }}
                                    </strong>

                                    <small>
                                        {{ $report['id'] }}
                                    </small>
                                </td>

                                <td>{{ $report['seller'] }}</td>

                                <td>{{ $report['reported'] }}</td>

                                <td>{{ $report['reason'] }}</td>

                                <td>{{ $report['evidence'] }}</td>

                                <td>
                                    <span class="ad-status is-{{ $statusClass }}">
                                        {{ $report['status'] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="ad-row-actions">
                                        <button
                                            class="ad-btn ad-btn-secondary ad-btn-sm"
                                            type="button"
                                            data-demo-action="Opening evidence for {{ $report['id'] }}"
                                        >
                                            Review
                                        </button>

                                        @if($report['status'] !== 'Resolved')
                                            <button
                                                class="ad-btn ad-btn-danger-soft ad-btn-sm"
                                                type="button"
                                                data-confirm-action
                                                data-confirm-title="Remove this listing?"
                                                data-confirm-message="Remove {{ $report['product'] }} after reviewing the submitted evidence."
                                                data-require-reason="true"
                                                data-success-message="Listing removal recorded"
                                            >
                                                Remove
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="ad-compliance-empty">
                                        No product violation reports found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                @else
                    <thead>
                        <tr>
                            <th>Seller</th>
                            <th>Category</th>
                            <th>Documents</th>
                            <th>Compliance score</th>
                            <th>Violations</th>
                            <th>Last review</th>
                            <th>Status</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($sellers as $seller)
                            @php
                                $statusClass = \Illuminate\Support\Str::slug($seller['status']);
                            @endphp

                            <tr
                                data-filter-item
                                data-search="{{ strtolower(implode(' ', $seller)) }}"
                            >
                                <td>
                                    <strong>
                                        {{ $seller['seller'] }}
                                    </strong>
                                </td>

                                <td>{{ $seller['category'] }}</td>

                                <td>{{ $seller['documents'] }}</td>

                                <td>
                                    <strong>
                                        {{ $seller['score'] }}
                                    </strong>
                                </td>

                                <td>{{ $seller['violations'] }}</td>

                                <td>{{ $seller['last'] }}</td>

                                <td>
                                    <span class="ad-status is-{{ $statusClass }}">
                                        {{ $seller['status'] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="ad-row-actions">
                                        <button
                                            class="ad-btn ad-btn-secondary ad-btn-sm"
                                            type="button"
                                            data-demo-action="Opening compliance file for {{ $seller['seller'] }}"
                                        >
                                            Review
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="ad-compliance-empty">
                                        No seller compliance records found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                @endif
            </table>
        </div>
    </section>
</div>
@endsection