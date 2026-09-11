@extends('layouts.admin')

@section('title', 'Financial Reports')
@section('subtitle', 'Generate date-bounded reports for sales, profit, orders, users, and marketplace risk.')
@section('active', 'reports')

@php
    $reports = [
        [
            'title' => 'Sales Report',
            'description' => 'Gross sales, discounts, refunds, and completed order value.',
            'format' => 'CSV / PDF',
            'icon' => 'sales',
        ],
        [
            'title' => 'Commission Report',
            'description' => 'The fixed 10% commission earned per eligible order.',
            'format' => 'CSV / PDF',
            'icon' => 'commission',
        ],
        [
            'title' => 'Order Report',
            'description' => 'Volume, status, cancellations, and fulfillment performance.',
            'format' => 'CSV / XLSX',
            'icon' => 'orders',
        ],
        [
            'title' => 'Seller Performance',
            'description' => 'Sales, service quality, violations, and settlement overview.',
            'format' => 'CSV / PDF',
            'icon' => 'seller',
        ],
        [
            'title' => 'User Growth',
            'description' => 'Buyer, seller, logistics-center, and rider account trends.',
            'format' => 'CSV / XLSX',
            'icon' => 'users',
        ],
        [
            'title' => 'Compliance Report',
            'description' => 'Product flags, complaints, enforcement, and outcomes.',
            'format' => 'PDF',
            'icon' => 'compliance',
        ],
        [
            'title' => 'Delivery Performance',
            'description' => 'Transit time, delivery exceptions, and completion rate.',
            'format' => 'CSV / PDF',
            'icon' => 'delivery',
        ],
        [
            'title' => 'Refund Report',
            'description' => 'Return reasons, refund amounts, parties, and resolution time.',
            'format' => 'CSV / PDF',
            'icon' => 'refund',
        ],
    ];

    $recentExports = [
        [
            'report' => 'August Commission Report',
            'period' => 'Aug 1–31, 2026',
            'requested_by' => 'Admin User',
            'generated' => 'Sep 1, 9:20 AM',
            'format' => 'PDF',
            'status' => 'Ready',
        ],
        [
            'report' => 'Weekly Delivery Performance',
            'period' => 'Aug 25–31, 2026',
            'requested_by' => 'Admin User',
            'generated' => 'Sep 1, 8:15 AM',
            'format' => 'CSV',
            'status' => 'Ready',
        ],
    ];
@endphp

@section('content')
<style>
    :root {
        --reports-bg: #FBF7F2;
        --reports-bg-soft: #F6EFE7;
        --reports-bg-alt: #EFE7DE;
        --reports-card: #FFFDF9;

        --reports-border: #EADCCC;
        --reports-border-strong: #DBCEC1;

        --reports-maroon: #561C17;
        --reports-maroon-2: #642920;
        --reports-maroon-dark: #3E130F;

        --reports-text: #3B211B;
        --reports-brown: #6C4936;
        --reports-muted: #987865;
        --reports-muted-2: #A99386;

        --reports-tan: #C19771;

        --reports-success: #256F4A;
        --reports-success-soft: #EAF7EF;

        --reports-warning: #9A5B11;
        --reports-warning-soft: #FFF6DE;

        --reports-danger: #B42318;
        --reports-danger-soft: #FCEBE9;

        --reports-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --reports-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ad-reports-page {
        color: var(--reports-text);

        --ad-blue: var(--reports-maroon);
        --ad-blue-dark: var(--reports-maroon-dark);
        --ad-blue-soft: #F3E4DE;
        --ad-blue-border: #E6C7BE;
    }

    .ad-reports-page .ad-page-head {
        padding: 32px 36px;

        border: 1px solid var(--reports-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--reports-shadow-soft);
    }

    .ad-reports-page .ad-overline {
        color: var(--reports-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-reports-page .ad-page-head h2 {
        margin-top: 10px;

        color: var(--reports-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-reports-page .ad-page-head p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--reports-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .ad-reports-page .ad-btn-primary {
        background: var(--reports-maroon) !important;
        border-color: var(--reports-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ad-reports-page .ad-btn-primary:hover {
        background: var(--reports-maroon-dark) !important;
        border-color: var(--reports-maroon-dark) !important;
    }

    .ad-reports-page .ad-btn-secondary {
        background: var(--reports-card) !important;
        border-color: var(--reports-tan) !important;
        color: var(--reports-maroon) !important;
    }

    .ad-reports-page .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--reports-maroon) !important;
    }

    .ad-reports-filter-card,
    .ad-reports-history-card {
        overflow: hidden;

        border: 1px solid var(--reports-border) !important;
        border-radius: 24px !important;

        background: var(--reports-card) !important;
        color: var(--reports-text) !important;

        box-shadow: var(--reports-shadow-soft) !important;
    }

    .ad-reports-page .ad-filter-bar {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 14px;

        padding: 18px;

        border-bottom: 0;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ad-reports-page .ad-inline-actions {
        display: flex;
        align-items: end;
        flex-wrap: wrap;
        gap: 12px;
    }

    .ad-reports-page .ad-filter-bar .ad-inline-actions {
        flex: 1;
    }

    .ad-reports-page .ad-field {
        display: grid;
        min-width: 180px;
        gap: 7px;
    }

    .ad-reports-page .ad-field span {
        color: var(--reports-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .ad-reports-page .ad-field input,
    .ad-reports-page .ad-field select {
        width: 100%;
        min-height: 43px;
        padding: 0 13px;

        border: 1px solid var(--reports-border);
        border-radius: 14px;

        background: var(--reports-bg-soft);
        color: var(--reports-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;

        transition: 160ms ease;
    }

    .ad-reports-page .ad-field input:focus,
    .ad-reports-page .ad-field select:focus {
        border-color: var(--reports-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-reports-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .ad-report-card {
        display: grid;
        gap: 14px;

        min-height: 230px;
        padding: 18px;

        border: 1px solid var(--reports-border);
        border-radius: 22px;

        background:
            radial-gradient(circle at 94% 6%, rgba(193, 151, 113, 0.16), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        color: var(--reports-text);
        box-shadow: var(--reports-shadow-soft);

        transition:
            transform 160ms ease,
            border-color 160ms ease,
            box-shadow 160ms ease;
    }

    .ad-report-card:hover {
        transform: translateY(-3px);
        border-color: var(--reports-tan);
        box-shadow: var(--reports-shadow-card);
    }

    .ad-report-icon {
        display: grid;
        width: 46px;
        height: 46px;
        place-items: center;

        border-radius: 16px;

        background: #F1E4D7;
        color: var(--reports-maroon);
    }

    .ad-report-icon svg {
        width: 22px;
        height: 22px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ad-report-card h3 {
        margin: 0;

        color: var(--reports-text);

        font-size: 15px;
        font-weight: 950;
        line-height: 1.25;
        letter-spacing: -0.035em;
    }

    .ad-report-card p {
        margin: -6px 0 0;

        color: var(--reports-muted);

        font-size: 12px;
        line-height: 1.65;
    }

    .ad-report-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;

        margin-top: auto;
        padding-top: 14px;

        border-top: 1px dashed var(--reports-border-strong);
    }

    .ad-reports-page .ad-status {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        justify-content: center;

        padding: 0 10px;

        border: 1px solid var(--reports-border);
        border-radius: 999px;

        background: var(--reports-bg-soft);
        color: var(--reports-brown);

        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .ad-reports-page .ad-status.is-completed,
    .ad-reports-page .ad-status.is-ready,
    .ad-reports-page .ad-status.is-success {
        background: var(--reports-success-soft);
        border-color: #CFE8DA;
        color: var(--reports-success);
    }

    .ad-reports-page .ad-status.is-pending {
        background: var(--reports-warning-soft);
        border-color: #EAD39A;
        color: var(--reports-warning);
    }

    .ad-reports-page .ad-card-head {
        padding: 20px 22px;

        border-bottom: 1px solid var(--reports-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .ad-reports-page .ad-card-head h2 {
        margin-top: 7px;

        color: var(--reports-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 32px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-reports-page .ad-card-head p {
        color: var(--reports-muted) !important;
    }

    .ad-reports-page .ad-table {
        width: 100%;
        min-width: 920px;
        border-collapse: collapse;
    }

    .ad-reports-page .ad-table thead {
        background: var(--reports-bg-soft);
    }

    .ad-reports-page .ad-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--reports-border);

        color: var(--reports-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .ad-reports-page .ad-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--reports-brown);

        font-size: 12px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .ad-reports-page .ad-table tbody tr:hover td {
        background: var(--reports-bg-soft);
    }

    .ad-reports-page .ad-table td strong {
        color: var(--reports-text);
        font-size: 12px;
        font-weight: 950;
    }

    .ad-reports-page .ad-table td:last-child {
        text-align: right;
    }

    @media (max-width: 1280px) {
        .ad-reports-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 820px) {
        .ad-reports-page .ad-page-head,
        .ad-reports-page .ad-filter-bar {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .ad-reports-page .ad-inline-actions,
        .ad-reports-page .ad-field,
        .ad-reports-page .ad-btn {
            width: 100%;
        }

        .ad-reports-grid {
            grid-template-columns: 1fr;
        }

        .ad-report-card-footer {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    html.dark .ad-reports-page .ad-page-head,
    html.dark .ad-reports-filter-card,
    html.dark .ad-reports-history-card,
    html.dark .ad-report-card,
    html.dark .ad-reports-page .ad-card-head,
    html.dark .ad-reports-page .ad-filter-bar {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-reports-page .ad-page-head h2,
    html.dark .ad-reports-page .ad-card-head h2,
    html.dark .ad-report-card h3,
    html.dark .ad-reports-page .ad-table td strong {
        color: #F5EFE8 !important;
    }

    html.dark .ad-reports-page .ad-page-head p,
    html.dark .ad-reports-page .ad-card-head p,
    html.dark .ad-report-card p,
    html.dark .ad-reports-page .ad-table td {
        color: #C8B7AD !important;
    }

    html.dark .ad-reports-page .ad-overline {
        color: #EBA99D !important;
    }

    html.dark .ad-reports-page .ad-field span {
        color: #C8B7AD !important;
    }

    html.dark .ad-reports-page .ad-field input,
    html.dark .ad-reports-page .ad-field select,
    html.dark .ad-reports-page .ad-table thead {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-reports-page .ad-table th,
    html.dark .ad-reports-page .ad-table td {
        border-color: #3B2E27 !important;
    }

    html.dark .ad-reports-page .ad-table tbody tr:hover td {
        background: #2D1414 !important;
    }

    html.dark .ad-report-icon {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }
</style>

<div class="ad-page ad-reports-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">
                Analytics export
            </span>

            <h2>
                Report center
            </h2>

            <p>
                Choose a date range, generate a preview, then export the report in the required format.
            </p>
        </div>

        <a
            class="ad-btn ad-btn-secondary"
            href="{{ route('admin.finance') }}"
        >
            Back to Finance
        </a>
    </div>

    <section class="ad-card ad-reports-filter-card">
        <form
            class="ad-filter-bar"
            data-demo-form
            data-success-message="Report preview generated"
        >
            <div class="ad-inline-actions">
                <label class="ad-field">
                    <span>From date</span>
                    <input type="date" value="2026-08-01">
                </label>

                <label class="ad-field">
                    <span>To date</span>
                    <input type="date" value="2026-09-04">
                </label>

                <label class="ad-field">
                    <span>Scope</span>
                    <select>
                        <option>All marketplace activity</option>
                        <option>Selected sellers</option>
                        <option>Selected categories</option>
                    </select>
                </label>
            </div>

            <button
                class="ad-btn ad-btn-primary"
                type="submit"
            >
                Generate preview
            </button>
        </form>
    </section>

    <section class="ad-reports-grid" aria-label="Available reports">
        @foreach($reports as $report)
            <article class="ad-report-card">
                <div class="ad-report-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M5 3h10l4 4v14H5z"/>
                        <path d="M15 3v5h5"/>
                        <path d="M8 13h8M8 17h6"/>
                    </svg>
                </div>

                <h3>
                    {{ $report['title'] }}
                </h3>

                <p>
                    {{ $report['description'] }}
                </p>

                <div class="ad-report-card-footer">
                    <span class="ad-status">
                        {{ $report['format'] }}
                    </span>

                    <button
                        type="button"
                        class="ad-btn ad-btn-secondary ad-btn-sm"
                        data-demo-action="{{ $report['title'] }} queued for export"
                    >
                        Generate
                    </button>
                </div>
            </article>
        @endforeach
    </section>

    <section class="ad-card ad-reports-history-card">
        <header class="ad-card-head">
            <div>
                <span class="ad-overline">
                    Recent exports
                </span>

                <h2>
                    Generated reports
                </h2>

                <p>
                    Frontend sample of downloadable report history.
                </p>
            </div>
        </header>

        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead>
                    <tr>
                        <th>Report</th>
                        <th>Period</th>
                        <th>Requested by</th>
                        <th>Generated</th>
                        <th>Format</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($recentExports as $export)
                        @php
                            $statusClass = \Illuminate\Support\Str::slug($export['status']);
                        @endphp

                        <tr
                            data-filter-item
                            data-search="{{ strtolower(implode(' ', $export)) }}"
                        >
                            <td>
                                <strong>
                                    {{ $export['report'] }}
                                </strong>
                            </td>

                            <td>{{ $export['period'] }}</td>
                            <td>{{ $export['requested_by'] }}</td>
                            <td>{{ $export['generated'] }}</td>
                            <td>{{ $export['format'] }}</td>

                            <td>
                                <span class="ad-status is-{{ $statusClass }}">
                                    {{ $export['status'] }}
                                </span>
                            </td>

                            <td>
                                <button
                                    class="ad-btn ad-btn-secondary ad-btn-sm"
                                    type="button"
                                    data-demo-action="Download started"
                                >
                                    Download
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection