@extends('layouts.seller')

@section('title', 'Reports')
@section('active', 'reports')
@section('subtitle', 'Generate focused reports for sales, profit, orders, and products.')

@php
    $reportTypes = [
        'sales' => [
            'title' => 'Sales Report',
            'description' => 'Revenue, discounts, refunds, and net sales.',
        ],
        'profit' => [
            'title' => 'Profit Report',
            'description' => 'Margins, fees, deductions, and earnings.',
        ],
        'orders' => [
            'title' => 'Order Report',
            'description' => 'Order volume, status, and fulfillment performance.',
        ],
        'products' => [
            'title' => 'Product Performance',
            'description' => 'Views, conversion, sold units, and top listings.',
        ],
    ];

    $requestedReport = $report ?? request('report', 'sales');

    $currentReport = array_key_exists($requestedReport, $reportTypes)
        ? $requestedReport
        : 'sales';

    $selectedReport = $reportTypes[$currentReport];

    $recentReports = [
        [
            'title' => 'August Sales Report',
            'period' => 'Aug 01–31, 2026',
            'format' => 'PDF',
            'generated' => 'Sep 01, 2026',
        ],
        [
            'title' => 'August Profit Report',
            'period' => 'Aug 01–31, 2026',
            'format' => 'XLSX',
            'generated' => 'Sep 01, 2026',
        ],
        [
            'title' => 'Weekly Order Report',
            'period' => 'Aug 25–31, 2026',
            'format' => 'CSV',
            'generated' => 'Aug 31, 2026',
        ],
    ];
@endphp

@section('content')
<style>
    :root {
        --rep-bg: #FBF7F2;
        --rep-bg-soft: #F6EFE7;
        --rep-bg-alt: #EFE7DE;
        --rep-card: #FFFDF9;

        --rep-border: #EADCCC;
        --rep-border-strong: #DBCEC1;

        --rep-maroon: #561C17;
        --rep-maroon-2: #642920;
        --rep-maroon-dark: #3E130F;

        --rep-text: #3B211B;
        --rep-brown: #6C4936;
        --rep-muted: #987865;
        --rep-muted-2: #A99386;

        --rep-tan: #C19771;

        --rep-success: #256F4A;
        --rep-success-soft: #EAF7EF;

        --rep-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --rep-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .sl-reports-page {
        color: var(--rep-text);

        --sl-blue: var(--rep-maroon);
        --sl-blue-dark: var(--rep-maroon-dark);
        --sl-blue-soft: #F3E4DE;
        --sl-indigo: var(--rep-tan);
    }

    .sl-reports-page .sl-page-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;

        padding: 32px 36px;

        border: 1px solid var(--rep-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--rep-shadow-soft);
    }

    .sl-reports-page .sl-eyebrow {
        color: var(--rep-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .sl-reports-page .sl-page-toolbar h2 {
        margin-top: 10px;

        color: var(--rep-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .sl-reports-page .sl-page-toolbar p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--rep-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .sl-reports-page .sl-btn {
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

    .sl-reports-page .sl-btn:hover {
        transform: translateY(-1px);
    }

    .sl-reports-page .sl-btn-primary {
        background: var(--rep-maroon) !important;
        border-color: var(--rep-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .sl-reports-page .sl-btn-primary:hover {
        background: var(--rep-maroon-dark) !important;
        border-color: var(--rep-maroon-dark) !important;
    }

    .sl-reports-page .sl-btn-ghost,
    .sl-reports-page .sl-btn-soft {
        background: var(--rep-card) !important;
        border-color: var(--rep-tan) !important;
        color: var(--rep-maroon) !important;
    }

    .sl-reports-page .sl-btn-ghost:hover,
    .sl-reports-page .sl-btn-soft:hover {
        background: #F3E4DE !important;
        border-color: var(--rep-maroon) !important;
    }

    .sl-reports-page .sl-btn-sm {
        min-height: 34px;
        padding-inline: 12px;
        border-radius: 11px;
        font-size: 11px;
    }

    .sl-reports-page .sl-btn-block {
        width: 100%;
    }

    .sl-reports-page .sl-card {
        overflow: hidden;

        border: 1px solid var(--rep-border) !important;
        border-radius: 24px !important;

        background: var(--rep-card) !important;
        color: var(--rep-text) !important;

        box-shadow: var(--rep-shadow-soft) !important;
    }

    .sl-reports-page .sl-report-builder {
        display: grid;
        grid-template-columns: 340px minmax(0, 1fr);
        align-items: stretch;
        gap: 0;
    }

    .sl-reports-page .sl-report-types {
        display: grid;
        align-content: start;
        gap: 10px;

        padding: 18px;

        border-right: 1px solid var(--rep-border);

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.14), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
    }

    .sl-reports-page .sl-report-types a {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;

        padding: 14px;

        border: 1px solid transparent;
        border-radius: 18px;

        color: var(--rep-brown);
        text-decoration: none;

        transition: 160ms ease;
    }

    .sl-reports-page .sl-report-types a:hover {
        background: var(--rep-bg-soft);
        border-color: var(--rep-border);
        color: var(--rep-maroon);
    }

    .sl-reports-page .sl-report-types a.is-active {
        background: var(--rep-maroon);
        border-color: var(--rep-maroon);
        color: #FFFFFF;
        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .sl-reports-page .sl-report-types a > span {
        display: grid;
        width: 44px;
        height: 44px;
        place-items: center;

        border-radius: 15px;

        background: #F1E4D7;
        color: var(--rep-maroon);
    }

    .sl-reports-page .sl-report-types a.is-active > span {
        background: rgba(255, 255, 255, 0.18);
        color: #FFFFFF;
    }

    .sl-reports-page .sl-report-types svg {
        width: 20px;
        height: 20px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sl-reports-page .sl-report-types strong {
        display: block;

        color: inherit;

        font-size: 12px;
        font-weight: 950;
        line-height: 1.25;
    }

    .sl-reports-page .sl-report-types small {
        display: block;
        margin-top: 4px;

        color: currentColor;

        font-size: 10px;
        font-weight: 700;
        line-height: 1.45;
        opacity: 0.72;
    }

    .sl-reports-page .sl-report-types i {
        color: inherit;
        font-size: 15px;
        font-style: normal;
        font-weight: 950;
    }

    .sl-reports-page .sl-report-controls {
        display: grid;
        align-content: start;
        gap: 20px;

        padding: 26px;
    }

    .sl-reports-page .sl-report-controls h2 {
        margin-top: 8px;

        color: var(--rep-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(34px, 3.6vw, 54px);
        font-weight: 400;
        line-height: 0.96;
        letter-spacing: -0.055em;
    }

    .sl-reports-page .sl-report-controls p {
        max-width: 640px;
        margin-top: 10px;

        color: var(--rep-muted) !important;

        font-size: 12px;
        line-height: 1.7;
    }

    .sl-reports-page .sl-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .sl-reports-page .sl-field {
        display: grid;
        gap: 7px;
    }

    .sl-reports-page .sl-field > span {
        color: var(--rep-muted) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sl-reports-page .sl-field input,
    .sl-reports-page .sl-field select {
        width: 100%;
        min-height: 43px;
        padding: 0 13px;

        border: 1px solid var(--rep-border);
        border-radius: 14px;

        background: var(--rep-bg-soft);
        color: var(--rep-text);

        font-size: 12px;
        font-weight: 700;
        outline: 0;

        transition: 160ms ease;
    }

    .sl-reports-page .sl-field input:focus,
    .sl-reports-page .sl-field select:focus {
        border-color: var(--rep-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .sl-reports-page .sl-report-options {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .sl-reports-page .sl-report-options label {
        display: flex;
        min-height: 48px;
        align-items: center;
        gap: 9px;

        padding: 0 13px;

        border: 1px solid var(--rep-border);
        border-radius: 15px;

        background: var(--rep-bg-soft);
        color: var(--rep-brown);

        font-size: 11px;
        font-weight: 800;
        line-height: 1.35;
    }

    .sl-reports-page input[type="checkbox"] {
        accent-color: var(--rep-maroon);
    }

    .sl-reports-page .sl-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;

        padding: 20px 22px;

        border-bottom: 1px solid var(--rep-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .sl-reports-page .sl-card-head h2 {
        margin-top: 7px;

        color: var(--rep-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 32px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .sl-reports-page .sl-card-head p {
        margin-top: 8px;

        color: var(--rep-muted) !important;

        font-size: 12px;
        line-height: 1.65;
    }

    .sl-reports-page .sl-report-history {
        display: grid;
        gap: 12px;

        padding: 18px;
    }

    .sl-reports-page .sl-report-history article {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 14px;

        padding: 15px;

        border: 1px solid var(--rep-border);
        border-radius: 18px;

        background:
            radial-gradient(circle at 96% 8%, rgba(193, 151, 113, 0.12), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        transition: 160ms ease;
    }

    .sl-reports-page .sl-report-history article:hover {
        transform: translateY(-2px);
        border-color: var(--rep-tan);
        box-shadow: var(--rep-shadow-card);
    }

    .sl-reports-page .sl-file-icon {
        display: grid;
        width: 46px;
        height: 46px;
        place-items: center;

        border-radius: 16px;

        background: #F1E4D7;
        color: var(--rep-maroon);

        font-size: 10px;
        font-weight: 950;
        letter-spacing: 0.05em;
    }

    .sl-reports-page .sl-report-history strong {
        display: block;

        color: var(--rep-text);

        font-size: 13px;
        font-weight: 950;
        line-height: 1.25;
    }

    .sl-reports-page .sl-report-history small {
        display: block;
        margin-top: 4px;

        color: var(--rep-muted);

        font-size: 10px;
        font-weight: 750;
        line-height: 1.5;
    }

    @media (max-width: 1120px) {
        .sl-reports-page .sl-report-builder {
            grid-template-columns: 1fr;
        }

        .sl-reports-page .sl-report-types {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            border-right: 0;
            border-bottom: 1px solid var(--rep-border);
        }

        .sl-reports-page .sl-report-options {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .sl-reports-page .sl-page-toolbar,
        .sl-reports-page .sl-card-head {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .sl-reports-page .sl-report-types,
        .sl-reports-page .sl-form-grid {
            grid-template-columns: 1fr;
        }

        .sl-reports-page .sl-report-controls {
            padding: 22px;
        }

        .sl-reports-page .sl-btn {
            width: 100%;
        }

        .sl-reports-page .sl-report-history article {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .sl-reports-page .sl-report-history article .sl-btn {
            grid-column: 1 / -1;
        }
    }

    html.dark .sl-reports-page .sl-page-toolbar,
    html.dark .sl-reports-page .sl-card,
    html.dark .sl-reports-page .sl-report-types,
    html.dark .sl-reports-page .sl-card-head,
    html.dark .sl-reports-page .sl-report-history article {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;

        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .sl-reports-page .sl-page-toolbar h2,
    html.dark .sl-reports-page .sl-report-controls h2,
    html.dark .sl-reports-page .sl-card-head h2,
    html.dark .sl-reports-page .sl-report-history strong {
        color: #F5EFE8 !important;
    }

    html.dark .sl-reports-page .sl-page-toolbar p,
    html.dark .sl-reports-page .sl-report-controls p,
    html.dark .sl-reports-page .sl-card-head p,
    html.dark .sl-reports-page .sl-report-history small {
        color: #C8B7AD !important;
    }

    html.dark .sl-reports-page .sl-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .sl-reports-page .sl-report-types a {
        color: #C8B7AD !important;
    }

    html.dark .sl-reports-page .sl-report-types a:hover,
    html.dark .sl-reports-page .sl-report-types a.is-active {
        background: #2D1414 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .sl-reports-page .sl-field input,
    html.dark .sl-reports-page .sl-field select,
    html.dark .sl-reports-page .sl-report-options label,
    html.dark .sl-reports-page .sl-btn-ghost {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .sl-reports-page .sl-btn-primary {
        background: #8A3A2F !important;
        border-color: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .sl-reports-page .sl-file-icon,
    html.dark .sl-reports-page .sl-report-types a > span {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }
</style>

<div class="sl-page sl-reports-page">
    <div class="sl-page-toolbar">
        <div>
            <span class="sl-eyebrow">
                Business Intelligence
            </span>

            <h2>
                Report Center
            </h2>

            <p>
                Choose a report type, set a date range, and generate a clear store performance summary.
            </p>
        </div>

        <button
            type="button"
            class="sl-btn sl-btn-ghost"
            data-demo-action="Report schedule opened."
        >
            Schedule Report
        </button>
    </div>

    <section class="sl-card sl-report-builder">
        <div class="sl-report-types">
            @foreach ($reportTypes as $key => $item)
                <a
                    href="{{ route('seller.reports', ['report' => $key]) }}"
                    class="{{ $currentReport === $key ? 'is-active' : '' }}"
                >
                    <span aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>
                        </svg>
                    </span>

                    <div>
                        <strong>
                            {{ $item['title'] }}
                        </strong>

                        <small>
                            {{ $item['description'] }}
                        </small>
                    </div>

                    <i aria-hidden="true">
                        →
                    </i>
                </a>
            @endforeach
        </div>

        <form
            class="sl-report-controls"
            data-demo-form
            data-success="{{ $selectedReport['title'] }} generated."
        >
            <div>
                <span class="sl-eyebrow">
                    Report Configuration
                </span>

                <h2>
                    {{ $selectedReport['title'] }}
                </h2>

                <p>
                    Configure the report period, comparison, and export format before generating the preview.
                </p>
            </div>

            <div class="sl-form-grid">
                <label class="sl-field">
                    <span>From Date</span>
                    <input type="date" value="2026-08-01" required>
                </label>

                <label class="sl-field">
                    <span>To Date</span>
                    <input type="date" value="2026-09-04" required>
                </label>

                <label class="sl-field">
                    <span>Compare With</span>

                    <select>
                        <option>Previous period</option>
                        <option>Same period last year</option>
                        <option>No comparison</option>
                    </select>
                </label>

                <label class="sl-field">
                    <span>Format</span>

                    <select>
                        <option>On-screen summary</option>
                        <option>PDF document</option>
                        <option>CSV spreadsheet</option>
                    </select>
                </label>
            </div>

            <div class="sl-report-options">
                <label>
                    <input type="checkbox" checked>
                    Include summary metrics
                </label>

                <label>
                    <input type="checkbox" checked>
                    Include detailed table
                </label>

                <label>
                    <input type="checkbox" checked>
                    Include visual charts
                </label>
            </div>

            <button type="submit" class="sl-btn sl-btn-primary sl-btn-block">
                Generate Report
            </button>
        </form>
    </section>

    <section class="sl-card">
        <header class="sl-card-head">
            <div>
                <span class="sl-eyebrow">
                    Recent Exports
                </span>

                <h2>
                    Generated Reports
                </h2>

                <p>
                    Download reports created during the last 30 days.
                </p>
            </div>
        </header>

        <div class="sl-report-history">
            @foreach ($recentReports as $history)
                <article>
                    <span class="sl-file-icon">
                        {{ $history['format'] }}
                    </span>

                    <div>
                        <strong>
                            {{ $history['title'] }}
                        </strong>

                        <small>
                            {{ $history['period'] }} · Generated {{ $history['generated'] }}
                        </small>
                    </div>

                    <button
                        type="button"
                        class="sl-btn sl-btn-ghost sl-btn-sm"
                        data-demo-action="{{ $history['title'] }} downloaded."
                    >
                        Download
                    </button>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection