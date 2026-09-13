@extends('layouts.admin')

@section('title', 'Marketplace Management')
@section('subtitle', 'Oversee listings, categories, and human-reviewed prohibited-item risk signals.')
@section('active', 'products')

@php
    $view = request('view', 'products');

    $products = [
        ['id' => 'PRD-8451', 'name' => 'Handwoven Abaca Tote', 'seller' => 'LIKHA Studio', 'category' => 'Fashion', 'price' => 1590, 'stock' => 34, 'sold' => 126, 'status' => 'Active'],
        ['id' => 'PRD-8450', 'name' => 'Mechanical Keyboard 87 Keys', 'seller' => 'MNL Tech', 'category' => 'Electronics', 'price' => 2790, 'stock' => 3, 'sold' => 98, 'status' => 'Active'],
        ['id' => 'PRD-8449', 'name' => 'Artisan Soy Candle Set', 'seller' => 'Casa Local', 'category' => 'Home', 'price' => 890, 'stock' => 18, 'sold' => 72, 'status' => 'Active'],
        ['id' => 'PRD-8448', 'name' => 'Unverified Tactical Blade', 'seller' => 'RapidCart PH', 'category' => 'Other', 'price' => 1290, 'stock' => 11, 'sold' => 0, 'status' => 'Flagged'],
    ];

    $categories = [
        ['name' => 'Fashion', 'products' => 2814, 'active' => 2760, 'updated' => 'Sep 4, 2026'],
        ['name' => 'Electronics', 'products' => 1942, 'active' => 1889, 'updated' => 'Sep 3, 2026'],
        ['name' => 'Home & Living', 'products' => 1640, 'active' => 1602, 'updated' => 'Sep 2, 2026'],
        ['name' => 'Books', 'products' => 721, 'active' => 709, 'updated' => 'Aug 30, 2026'],
        ['name' => 'Beauty', 'products' => 983, 'active' => 951, 'updated' => 'Aug 28, 2026'],
    ];

    $flags = [
        ['id' => 'FLG-3088', 'product' => 'Unverified Tactical Blade', 'seller' => 'RapidCart PH', 'signal' => 'Possible weapon', 'confidence' => 94, 'source' => 'Automated scan', 'status' => 'Open'],
        ['id' => 'FLG-3087', 'product' => 'Herbal Relief Capsules', 'seller' => 'Wellness Corner', 'signal' => 'Possible controlled claim', 'confidence' => 82, 'source' => 'Buyer report', 'status' => 'Under Review'],
        ['id' => 'FLG-3086', 'product' => 'Industrial Solvent 1L', 'seller' => 'BuildRight', 'signal' => 'Restricted chemical', 'confidence' => 76, 'source' => 'Keyword + image scan', 'status' => 'Open'],
        ['id' => 'FLG-3085', 'product' => 'Replica Collector Pistol', 'seller' => 'Hobby House', 'signal' => 'Weapon-like item', 'confidence' => 71, 'source' => 'Seller report', 'status' => 'Under Review'],
    ];

    $pageTitle = match ($view) {
        'categories' => 'Category management',
        'monitor' => 'Prohibited-item monitor',
        default => 'Product catalog',
    };

    $pageDescription = $view === 'monitor'
        ? 'Risk signals support review; they never replace an evidence-based administrator decision.'
        : 'Keep the marketplace organized, accurate, and policy-compliant.';
@endphp

@section('content')
<style>
    :root {
        --market-bg: #FBF7F2;
        --market-bg-soft: #F6EFE7;
        --market-bg-alt: #EFE7DE;
        --market-card: #FFFDF9;

        --market-border: #EADCCC;
        --market-border-strong: #DBCEC1;

        --market-maroon: #561C17;
        --market-maroon-2: #642920;
        --market-maroon-dark: #3E130F;

        --market-text: #3B211B;
        --market-brown: #6C4936;
        --market-muted: #987865;
        --market-muted-2: #A99386;

        --market-tan: #C19771;

        --market-success: #256F4A;
        --market-success-soft: #EAF7EF;

        --market-warning: #9A5B11;
        --market-warning-soft: #FFF6DE;

        --market-danger: #B42318;
        --market-danger-soft: #FCEBE9;

        --market-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --market-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ad-market-page {
        color: var(--market-text);
    }

    .ad-market-page .ad-page-head {
        padding: 32px 36px;
        border: 1px solid var(--market-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--market-shadow-soft);
    }

    .ad-market-page .ad-overline {
        color: var(--market-maroon) !important;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-market-page .ad-page-head h2 {
        margin-top: 10px;
        color: var(--market-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-market-page .ad-page-head p {
        max-width: 660px;
        margin-top: 13px;
        color: var(--market-muted) !important;
        font-size: 13px;
        line-height: 1.7;
    }

    .ad-market-page .ad-btn-primary {
        background: var(--market-maroon) !important;
        border-color: var(--market-maroon) !important;
        color: #FFFFFF !important;
        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ad-market-page .ad-btn-primary:hover {
        background: var(--market-maroon-dark) !important;
        border-color: var(--market-maroon-dark) !important;
    }

    .ad-market-page .ad-btn-secondary {
        background: var(--market-card) !important;
        border-color: var(--market-tan) !important;
        color: var(--market-maroon) !important;
    }

    .ad-market-page .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--market-maroon) !important;
    }

    .ad-market-page .ad-btn-danger-soft {
        background: var(--market-danger-soft) !important;
        border-color: #F0C9C4 !important;
        color: var(--market-danger) !important;
    }

    .ad-market-page .ad-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 6px;

        border: 1px solid var(--market-border);
        border-radius: 16px;

        background: var(--market-card);
        box-shadow: var(--market-shadow-soft);
    }

    .ad-market-page .ad-tab {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 8px;

        padding: 0 16px;

        border-radius: 12px;

        color: var(--market-brown);

        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;

        transition: 160ms ease;
    }

    .ad-market-page .ad-tab:hover {
        background: var(--market-bg-soft);
        color: var(--market-maroon);
    }

    .ad-market-page .ad-tab.is-active {
        background: var(--market-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .ad-market-page .ad-tab b {
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

    .ad-market-page .ad-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .ad-market-page .ad-mini-stat {
        padding: 18px;

        border: 1px solid var(--market-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        box-shadow: var(--market-shadow-soft);
    }

    .ad-market-page .ad-mini-stat span {
        display: block;

        color: var(--market-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .ad-market-page .ad-mini-stat strong {
        display: block;
        margin-top: 8px;

        color: var(--market-maroon);

        font-size: 32px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-market-page .ad-mini-stat small {
        display: block;
        margin-top: 8px;

        color: var(--market-muted);

        font-size: 11px;
        line-height: 1.5;
    }

    .ad-market-page .ad-card {
        overflow: hidden;

        border: 1px solid var(--market-border) !important;
        border-radius: 24px !important;

        background: var(--market-card) !important;
        color: var(--market-text) !important;

        box-shadow: var(--market-shadow-soft) !important;
    }

    .ad-market-page .ad-card-head {
        padding: 20px 22px;

        border-bottom: 1px solid var(--market-border) !important;

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.14), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%) !important;
    }

    .ad-market-page .ad-card-head h3 {
        margin-top: 7px;
        color: var(--market-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-market-page .ad-card-head p {
        color: var(--market-muted) !important;
    }

    .ad-market-page .ad-filter-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;

        padding: 15px;

        border-bottom: 1px solid var(--market-border);

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.12), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ad-market-page .ad-filter-search {
        position: relative;
        flex: 1;
        max-width: 460px;
    }

    .ad-market-page .ad-filter-search svg {
        position: absolute;
        left: 14px;
        top: 50%;

        width: 16px;
        height: 16px;

        color: var(--market-muted-2);

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;

        transform: translateY(-50%);
    }

    .ad-market-page .ad-filter-search input {
        width: 100%;
        min-height: 42px;
        padding: 0 14px 0 42px;

        border: 1px solid var(--market-border);
        border-radius: 14px;

        background: var(--market-bg-soft);
        color: var(--market-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;
    }

    .ad-market-page .ad-filter-search input:focus {
        border-color: var(--market-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-market-page .ad-select {
        min-height: 42px;
        padding: 0 12px;

        border: 1px solid var(--market-border);
        border-radius: 14px;

        background: var(--market-bg-soft);
        color: var(--market-text);

        font-size: 11px;
        font-weight: 800;
        outline: none;
    }

    .ad-market-page .ad-table {
        width: 100%;
        min-width: 880px;
        border-collapse: collapse;
    }

    .ad-market-page .ad-table thead {
        background: var(--market-bg-soft);
    }

    .ad-market-page .ad-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--market-border);

        color: var(--market-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .ad-market-page .ad-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--market-brown);

        font-size: 12px;
        vertical-align: middle;
    }

    .ad-market-page .ad-table tbody tr:hover td {
        background: var(--market-bg-soft);
    }

    .ad-market-page .ad-table td strong {
        color: var(--market-text);
        font-size: 12px;
        font-weight: 950;
    }

    .ad-market-page .ad-table td small {
        display: block;
        margin-top: 4px;

        color: var(--market-muted);

        font-size: 10px;
        line-height: 1.4;
    }

    .ad-market-page .ad-product-thumb {
        display: grid;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        place-items: center;

        border: 1px solid var(--market-border);
        border-radius: 13px;

        background: #F1E4D7;
        color: var(--market-maroon);

        font-size: 12px;
        font-weight: 950;
    }

    .ad-market-page .ad-cell-product {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .ad-market-page .ad-status {
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

    .ad-market-page .ad-status.is-active {
        background: var(--market-success-soft);
        border-color: #CFE8DA;
        color: var(--market-success);
    }

    .ad-market-page .ad-status.is-flagged,
    .ad-market-page .ad-status.is-open {
        background: var(--market-danger-soft);
        border-color: #F0C9C4;
        color: var(--market-danger);
    }

    .ad-market-page .ad-status.is-under-review {
        background: var(--market-warning-soft);
        border-color: #EAD39A;
        color: var(--market-warning);
    }

    .ad-market-page .ad-note {
        padding: 16px 18px;

        border: 1px solid #EAD39A;
        border-radius: 18px;

        background: var(--market-warning-soft);
        color: var(--market-warning);

        font-size: 12px;
        line-height: 1.7;

        box-shadow: var(--market-shadow-soft);
    }

    .ad-market-page .ad-note strong {
        color: var(--market-warning);
        font-weight: 950;
    }

    .ad-market-page .ad-risk-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(320px, 0.75fr);
        gap: 16px;
    }

    .ad-market-page .ad-risk-score {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        color: var(--market-danger);
        font-weight: 900;
    }

    .ad-market-page .ad-risk-score i {
        position: relative;

        display: block;
        width: 48px;
        height: 7px;
        overflow: hidden;

        border-radius: 999px;

        background: #F4D8D3;
    }

    .ad-market-page .ad-risk-score i::after {
        display: block;
        width: var(--risk, 50%);
        height: 100%;

        border-radius: inherit;

        background: var(--market-danger);
        content: "";
    }

    .ad-market-page .ad-risk-summary {
        display: grid;
        gap: 12px;
        padding: 18px;
    }

    .ad-market-page .ad-risk-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;

        padding: 14px;

        border: 1px solid #F0C9C4;
        border-radius: 16px;

        background: var(--market-danger-soft);
    }

    .ad-market-page .ad-risk-item.is-warning {
        border-color: #EAD39A;
        background: var(--market-warning-soft);
    }

    .ad-market-page .ad-risk-number {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;

        border-radius: 13px;

        background: #FFFFFF;
        color: var(--market-danger);

        font-size: 13px;
        font-weight: 950;
    }

    .ad-market-page .ad-risk-item.is-warning .ad-risk-number {
        color: var(--market-warning);
    }

    .ad-market-page .ad-risk-item strong {
        color: var(--market-text);
        font-size: 12px;
        font-weight: 950;
    }

    .ad-market-page .ad-risk-item p {
        margin: 4px 0 0;

        color: var(--market-muted);

        font-size: 11px;
        line-height: 1.5;
    }

    .ad-market-page .ad-row-actions {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 7px;
    }

    @media (max-width: 1180px) {
        .ad-market-page .ad-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ad-market-page .ad-risk-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .ad-market-page .ad-page-head,
        .ad-market-page .ad-filter-bar {
            align-items: flex-start;
            flex-direction: column;
        }

        .ad-market-page .ad-page-head {
            padding: 26px 22px;
        }

        .ad-market-page .ad-summary-grid {
            grid-template-columns: 1fr;
        }

        .ad-market-page .ad-filter-search,
        .ad-market-page .ad-inline-actions,
        .ad-market-page .ad-select,
        .ad-market-page .ad-btn {
            width: 100%;
            max-width: none;
        }
    }

    html.dark .ad-market-page .ad-page-head,
    html.dark .ad-market-page .ad-tabs,
    html.dark .ad-market-page .ad-card,
    html.dark .ad-market-page .ad-mini-stat,
    html.dark .ad-market-page .ad-filter-bar {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-market-page .ad-page-head h2,
    html.dark .ad-market-page .ad-mini-stat strong,
    html.dark .ad-market-page .ad-card-head h3,
    html.dark .ad-market-page .ad-table td strong,
    html.dark .ad-market-page .ad-risk-item strong {
        color: #F5EFE8 !important;
    }

    html.dark .ad-market-page .ad-page-head p,
    html.dark .ad-market-page .ad-mini-stat span,
    html.dark .ad-market-page .ad-mini-stat small,
    html.dark .ad-market-page .ad-table td,
    html.dark .ad-market-page .ad-table td small,
    html.dark .ad-market-page .ad-risk-item p {
        color: #C8B7AD !important;
    }

    html.dark .ad-market-page .ad-overline,
    html.dark .ad-market-page .ad-mini-stat strong {
        color: #EBA99D !important;
    }

    html.dark .ad-market-page .ad-tab {
        color: #C8B7AD !important;
    }

    html.dark .ad-market-page .ad-tab:hover {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-market-page .ad-tab.is-active {
        background: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .ad-market-page .ad-filter-search input,
    html.dark .ad-market-page .ad-select,
    html.dark .ad-market-page .ad-table thead {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-market-page .ad-table th,
    html.dark .ad-market-page .ad-table td {
        border-color: #3B2E27 !important;
    }

    html.dark .ad-market-page .ad-table tbody tr:hover td {
        background: #2D1414 !important;
    }

    html.dark .ad-market-page .ad-product-thumb,
    html.dark .ad-market-page .ad-risk-number {
        background: #2D1414 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }
</style>

<div class="ad-page ad-market-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">
                Marketplace governance
            </span>

            <h2>
                {{ $pageTitle }}
            </h2>

            <p>
                {{ $pageDescription }}
            </p>
        </div>

        @if($view === 'categories')
            <button
                class="ad-btn ad-btn-primary"
                type="button"
                data-demo-action="New category form opened"
            >
                Add category
            </button>
        @else
            <a
                class="ad-btn ad-btn-secondary"
                href="{{ route('admin.products', ['view' => 'monitor']) }}"
            >
                Review flags
            </a>
        @endif
    </div>

    <div class="ad-tabs" aria-label="Marketplace sections">
        <a
            class="ad-tab {{ $view === 'products' ? 'is-active' : '' }}"
            href="{{ route('admin.products', ['view' => 'products']) }}"
        >
            Products
        </a>

        <a
            class="ad-tab {{ $view === 'categories' ? 'is-active' : '' }}"
            href="{{ route('admin.products', ['view' => 'categories']) }}"
        >
            Categories
        </a>

        <a
            class="ad-tab {{ $view === 'monitor' ? 'is-active' : '' }}"
            href="{{ route('admin.products', ['view' => 'monitor']) }}"
        >
            Prohibited Item Monitor <b>17</b>
        </a>
    </div>

    @if($view === 'monitor')
        <section class="ad-summary-grid" aria-label="Prohibited item monitor summary">
            <div class="ad-mini-stat">
                <span>Open signals</span>
                <strong>17</strong>
                <small>5 high-confidence</small>
            </div>

            <div class="ad-mini-stat">
                <span>Reviewed today</span>
                <strong>31</strong>
                <small>Median: 18 minutes</small>
            </div>

            <div class="ad-mini-stat">
                <span>Listings removed</span>
                <strong>6</strong>
                <small>Evidence retained</small>
            </div>

            <div class="ad-mini-stat">
                <span>False positives</span>
                <strong>9</strong>
                <small>Signals cleared by Admin</small>
            </div>
        </section>

        <div class="ad-note is-warning">
            <strong>Human review required:</strong>
            Image, keyword, seller-history, and community-report signals may be inaccurate.
            Open the listing and evidence before taking any enforcement action.
        </div>

        <section class="ad-risk-layout">
            <div class="ad-card" id="flag-table">
                <div class="ad-filter-bar">
                    <label class="ad-filter-search">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5"/>
                        </svg>

                        <input
                            type="search"
                            data-filter-input="#flag-table"
                            placeholder="Search flag, product, or seller"
                        >
                    </label>

                    <select class="ad-select">
                        <option>Risk: all</option>
                        <option>High confidence</option>
                        <option>Medium confidence</option>
                    </select>
                </div>

                <div class="ad-table-wrap">
                    <table class="ad-table">
                        <thead>
                            <tr>
                                <th>Flag / product</th>
                                <th>Seller</th>
                                <th>Risk signal</th>
                                <th>Confidence</th>
                                <th>Source</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($flags as $flag)
                                <tr
                                    data-filter-item
                                    data-search="{{ strtolower(implode(' ', $flag)) }}"
                                >
                                    <td>
                                        <strong>{{ $flag['product'] }}</strong>
                                        <small>{{ $flag['id'] }}</small>
                                    </td>

                                    <td>{{ $flag['seller'] }}</td>
                                    <td>{{ $flag['signal'] }}</td>

                                    <td>
                                        <span class="ad-risk-score">
                                            <i style="--risk:{{ $flag['confidence'] }}%"></i>
                                            {{ $flag['confidence'] }}%
                                        </span>
                                    </td>

                                    <td>{{ $flag['source'] }}</td>

                                    <td>
                                        <span class="ad-status is-{{ \Illuminate\Support\Str::slug($flag['status']) }}">
                                            {{ $flag['status'] }}
                                        </span>
                                    </td>

                                    <td>
                                        <button
                                            class="ad-btn ad-btn-secondary ad-btn-sm"
                                            type="button"
                                            data-demo-action="Opening evidence for {{ $flag['id'] }}"
                                        >
                                            Review
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="ad-card">
                <header class="ad-card-head">
                    <div>
                        <span class="ad-overline">
                            Signal categories
                        </span>

                        <h3>
                            Risk breakdown
                        </h3>

                        <p>
                            Open items by detected concern.
                        </p>
                    </div>
                </header>

                <div class="ad-risk-summary">
                    <div class="ad-risk-item">
                        <span class="ad-risk-number">5</span>

                        <div>
                            <strong>Weapons or weapon-like items</strong>
                            <p>Highest priority; manually inspect media and description.</p>
                        </div>
                    </div>

                    <div class="ad-risk-item">
                        <span class="ad-risk-number">4</span>

                        <div>
                            <strong>Controlled or illegal substances</strong>
                            <p>Check ingredients, claims, permits, and seller history.</p>
                        </div>
                    </div>

                    <div class="ad-risk-item is-warning">
                        <span class="ad-risk-number">8</span>

                        <div>
                            <strong>Restricted claims or chemicals</strong>
                            <p>Validate against current marketplace policy before action.</p>
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    @elseif($view === 'categories')
        <section class="ad-card" id="category-table">
            <div class="ad-filter-bar">
                <label class="ad-filter-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    <input
                        type="search"
                        data-filter-input="#category-table"
                        placeholder="Search categories"
                    >
                </label>
            </div>

            <div class="ad-table-wrap">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total products</th>
                            <th>Active listings</th>
                            <th>Last updated</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($categories as $category)
                            <tr
                                data-filter-item
                                data-search="{{ strtolower($category['name']) }}"
                            >
                                <td>
                                    <strong>{{ $category['name'] }}</strong>
                                </td>

                                <td>{{ number_format($category['products']) }}</td>
                                <td>{{ number_format($category['active']) }}</td>
                                <td>{{ $category['updated'] }}</td>

                                <td>
                                    <div class="ad-row-actions">
                                        <button
                                            class="ad-btn ad-btn-secondary ad-btn-sm"
                                            type="button"
                                            data-demo-action="Editing {{ $category['name'] }}"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            class="ad-btn ad-btn-danger-soft ad-btn-sm"
                                            type="button"
                                            data-confirm-action
                                            data-confirm-title="Archive category?"
                                            data-confirm-message="Existing listings will need a replacement category."
                                            data-success-message="Category archived"
                                        >
                                            Archive
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
        <section class="ad-card" id="product-table">
            <div class="ad-filter-bar">
                <label class="ad-filter-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    <input
                        type="search"
                        data-filter-input="#product-table"
                        placeholder="Search product, seller, or ID"
                    >
                </label>

                <div class="ad-inline-actions">
                    <select class="ad-select">
                        <option>All categories</option>
                        <option>Fashion</option>
                        <option>Electronics</option>
                        <option>Home</option>
                    </select>

                    <button
                        class="ad-btn ad-btn-secondary ad-btn-sm"
                        type="button"
                        data-demo-action="Catalog exported"
                    >
                        Export
                    </button>
                </div>
            </div>

            <div class="ad-table-wrap">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Seller</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Sold</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($products as $product)
                            <tr
                                data-filter-item
                                data-search="{{ strtolower(implode(' ', $product)) }}"
                            >
                                <td>
                                    <div class="ad-cell-product">
                                        <span class="ad-product-thumb">
                                            {{ mb_strtoupper(mb_substr($product['name'], 0, 1)) }}
                                        </span>

                                        <span>
                                            <strong>{{ $product['name'] }}</strong>
                                            <small>{{ $product['id'] }}</small>
                                        </span>
                                    </div>
                                </td>

                                <td>{{ $product['seller'] }}</td>
                                <td>{{ $product['category'] }}</td>

                                <td>
                                    <strong>₱{{ number_format($product['price'], 2) }}</strong>
                                </td>

                                <td>{{ $product['stock'] }}</td>
                                <td>{{ $product['sold'] }}</td>

                                <td>
                                    <span class="ad-status is-{{ \Illuminate\Support\Str::slug($product['status']) }}">
                                        {{ $product['status'] }}
                                    </span>
                                </td>

                                <td>
                                    <button
                                        class="ad-btn ad-btn-secondary ad-btn-sm"
                                        type="button"
                                        data-demo-action="Opening {{ $product['name'] }}"
                                    >
                                        Review
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