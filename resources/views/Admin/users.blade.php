@extends('layouts.admin')

@section('title', 'User Management')
@section('subtitle', 'Monitor platform accounts, issue violation notices, and apply proportionate enforcement.')
@section('active', 'users')

@php
    $role = request('role', 'all');

    $users = collect([
        ['id' => 'USR-10082', 'name' => 'Angela Cruz', 'email' => 'angela@example.com', 'role' => 'Buyer', 'joined' => 'Aug 28, 2026', 'orders' => 18, 'status' => 'Active'],
        ['id' => 'USR-10081', 'name' => 'LIKHA Studio', 'email' => 'hello@likha.studio', 'role' => 'Seller', 'joined' => 'Aug 26, 2026', 'orders' => 426, 'status' => 'Verified'],
        ['id' => 'USR-10080', 'name' => 'NorthLink Hub', 'email' => 'ops@northlink.ph', 'role' => 'Logistics', 'joined' => 'Aug 22, 2026', 'orders' => 1204, 'status' => 'Active'],
        ['id' => 'USR-10079', 'name' => 'Juan Dela Cruz', 'email' => 'juan.rider@example.com', 'role' => 'Rider', 'joined' => 'Aug 20, 2026', 'orders' => 312, 'status' => 'Active'],
        ['id' => 'USR-10078', 'name' => 'Marco Reyes', 'email' => 'marco@example.com', 'role' => 'Buyer', 'joined' => 'Aug 18, 2026', 'orders' => 6, 'status' => 'Under Review'],
        ['id' => 'USR-10077', 'name' => 'RapidCart PH', 'email' => 'support@rapidcart.ph', 'role' => 'Seller', 'joined' => 'Aug 10, 2026', 'orders' => 94, 'status' => 'Banned'],
    ]);

    $filtered = $users->filter(function ($user) use ($role) {
        return $role === 'all' || match ($role) {
            'buyers' => $user['role'] === 'Buyer',
            'sellers' => $user['role'] === 'Seller',
            'logistics' => $user['role'] === 'Logistics',
            'riders' => $user['role'] === 'Rider',
            default => true,
        };
    });

    $roleTabs = [
        'all' => 'All Users',
        'buyers' => 'Buyers',
        'sellers' => 'Sellers',
        'logistics' => 'Logistics Centers',
        'riders' => 'Riders / Couriers',
    ];
@endphp

@push('head')
<style>
    :root {
        --users-bg: #FBF7F2;
        --users-bg-soft: #F6EFE7;
        --users-bg-alt: #EFE7DE;
        --users-card: #FFFDF9;

        --users-border: #EADCCC;
        --users-border-strong: #DBCEC1;

        --users-maroon: #561C17;
        --users-maroon-2: #642920;
        --users-maroon-dark: #3E130F;

        --users-text: #3B211B;
        --users-brown: #6C4936;
        --users-muted: #987865;
        --users-muted-2: #A99386;

        --users-tan: #C19771;

        --users-success: #256F4A;
        --users-success-soft: #EAF7EF;

        --users-warning: #9A5B11;
        --users-warning-soft: #FFF6DE;

        --users-danger: #B42318;
        --users-danger-soft: #FCEBE9;

        --users-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --users-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ad-users-page {
        color: var(--users-text);
    }

    .ad-users-page .ad-page-head {
        padding: 32px 36px;

        border: 1px solid var(--users-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--users-shadow-soft);
    }

    .ad-users-page .ad-overline {
        color: var(--users-maroon) !important;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-users-page .ad-page-head h2 {
        margin-top: 10px;

        color: var(--users-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-users-page .ad-page-head p {
        max-width: 660px;
        margin-top: 13px;

        color: var(--users-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .ad-users-page .ad-btn-primary {
        background: var(--users-maroon) !important;
        border-color: var(--users-maroon) !important;
        color: #FFFFFF !important;
        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ad-users-page .ad-btn-primary:hover {
        background: var(--users-maroon-dark) !important;
        border-color: var(--users-maroon-dark) !important;
    }

    .ad-users-page .ad-btn-secondary {
        background: var(--users-card) !important;
        border-color: var(--users-tan) !important;
        color: var(--users-maroon) !important;
    }

    .ad-users-page .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--users-maroon) !important;
    }

    .ad-users-page .ad-btn-warning-soft {
        background: var(--users-warning-soft) !important;
        border-color: #EAD39A !important;
        color: var(--users-warning) !important;
    }

    .ad-users-page .ad-btn-danger-soft {
        background: var(--users-danger-soft) !important;
        border-color: #F0C9C4 !important;
        color: var(--users-danger) !important;
    }

    .ad-users-page .ad-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .ad-users-page .ad-mini-stat {
        padding: 18px;

        border: 1px solid var(--users-border);
        border-radius: 20px;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.15), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);

        box-shadow: var(--users-shadow-soft);
    }

    .ad-users-page .ad-mini-stat span {
        display: block;

        color: var(--users-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .ad-users-page .ad-mini-stat strong {
        display: block;
        margin-top: 8px;

        color: var(--users-maroon);

        font-size: 32px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-users-page .ad-mini-stat small {
        display: block;
        margin-top: 8px;

        color: var(--users-muted);

        font-size: 11px;
        line-height: 1.5;
    }

    .ad-users-page .ad-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;

        padding: 6px;

        border: 1px solid var(--users-border);
        border-radius: 16px;

        background: var(--users-card);
        box-shadow: var(--users-shadow-soft);
    }

    .ad-users-page .ad-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 40px;
        padding: 0 16px;

        border-radius: 12px;

        color: var(--users-brown);

        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;

        transition: 160ms ease;
    }

    .ad-users-page .ad-tab:hover {
        background: var(--users-bg-soft);
        color: var(--users-maroon);
    }

    .ad-users-page .ad-tab.is-active {
        background: var(--users-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .ad-users-card {
        overflow: hidden;

        border: 1px solid var(--users-border) !important;
        border-radius: 24px !important;

        background: var(--users-card) !important;
        box-shadow: var(--users-shadow-soft) !important;
    }

    .ad-users-page .ad-filter-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;

        padding: 15px;

        border-bottom: 1px solid var(--users-border);

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.12), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ad-users-page .ad-filter-search {
        position: relative;
        flex: 1;
        max-width: 460px;
    }

    .ad-users-page .ad-filter-search svg {
        position: absolute;
        left: 14px;
        top: 50%;

        width: 16px;
        height: 16px;

        color: var(--users-muted-2);

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;

        transform: translateY(-50%);
    }

    .ad-users-page .ad-filter-search input {
        width: 100%;
        min-height: 42px;
        padding: 0 14px 0 42px;

        border: 1px solid var(--users-border);
        border-radius: 14px;

        background: var(--users-bg-soft);
        color: var(--users-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;
    }

    .ad-users-page .ad-filter-search input:focus {
        border-color: var(--users-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-users-page .ad-filter-search input::placeholder {
        color: var(--users-muted-2);
    }

    .ad-users-page .ad-select {
        min-height: 42px;
        padding: 0 12px;

        border: 1px solid var(--users-border);
        border-radius: 14px;

        background: var(--users-bg-soft);
        color: var(--users-text);

        font-size: 11px;
        font-weight: 800;
        outline: none;
    }

    .ad-users-page .ad-select:focus {
        border-color: var(--users-tan);
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-users-page .ad-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .ad-users-page .ad-table thead {
        background: var(--users-bg-soft);
    }

    .ad-users-page .ad-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--users-border);

        color: var(--users-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .ad-users-page .ad-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--users-brown);

        font-size: 12px;
        vertical-align: middle;
    }

    .ad-users-page .ad-table tbody tr:hover td {
        background: var(--users-bg-soft);
    }

    .ad-users-page .ad-table td strong {
        color: var(--users-text);
        font-size: 12px;
        font-weight: 950;
    }

    .ad-users-page .ad-table td small {
        display: block;
        margin-top: 4px;

        color: var(--users-muted);

        font-size: 10px;
        line-height: 1.4;
    }

    .ad-users-page input[type="checkbox"] {
        width: 15px;
        height: 15px;

        accent-color: var(--users-maroon);
    }

    .ad-users-page .ad-cell-user {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 0;
    }

    .ad-users-page .ad-avatar {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;

        border-radius: 13px;

        background: var(--users-maroon);
        color: #FFFFFF;

        font-size: 12px;
        font-weight: 950;
    }

    .ad-users-page .ad-avatar.is-soft {
        background: #F1E4D7 !important;
        color: var(--users-maroon) !important;
    }

    .ad-users-page .ad-status {
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

    .ad-users-page .ad-status.is-active,
    .ad-users-page .ad-status.is-verified {
        background: var(--users-success-soft);
        border-color: #CFE8DA;
        color: var(--users-success);
    }

    .ad-users-page .ad-status.is-under-review {
        background: var(--users-warning-soft);
        border-color: #EAD39A;
        color: var(--users-warning);
    }

    .ad-users-page .ad-status.is-banned {
        background: var(--users-danger-soft);
        border-color: #F0C9C4;
        color: var(--users-danger);
    }

    .ad-users-page .ad-row-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 7px;
    }

    .ad-users-page .ad-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;

        padding: 14px 16px;

        border-top: 1px solid var(--users-border);

        background: var(--users-card);
        color: var(--users-muted);

        font-size: 11px;
        font-weight: 700;
    }

    .ad-users-page .ad-pagination nav {
        display: flex;
        gap: 5px;
    }

    .ad-users-page .ad-page-btn {
        display: grid;
        min-width: 32px;
        height: 32px;
        place-items: center;

        border: 1px solid var(--users-border);
        border-radius: 9px;

        background: var(--users-bg-soft);
        color: var(--users-brown);

        font-size: 11px;
        font-weight: 900;
        text-decoration: none;
    }

    .ad-users-page .ad-page-btn:hover {
        border-color: var(--users-tan);
        color: var(--users-maroon);
    }

    .ad-users-page .ad-page-btn.is-active {
        background: var(--users-maroon);
        border-color: var(--users-maroon);
        color: #FFFFFF;
    }

    @media (max-width: 1100px) {
        .ad-users-page .ad-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ad-users-page .ad-filter-bar {
            align-items: stretch;
            flex-direction: column;
        }

        .ad-users-page .ad-filter-search {
            max-width: none;
        }

        .ad-users-page .ad-inline-actions {
            width: 100%;
            justify-content: space-between;
        }
    }

    @media (max-width: 700px) {
        .ad-users-page .ad-page-head {
            padding: 26px 22px;
        }

        .ad-users-page .ad-summary-grid {
            grid-template-columns: 1fr;
        }

        .ad-users-page .ad-page-head,
        .ad-users-page .ad-pagination {
            align-items: flex-start;
            flex-direction: column;
        }

        .ad-users-page .ad-page-head .ad-btn,
        .ad-users-page .ad-inline-actions,
        .ad-users-page .ad-inline-actions .ad-btn,
        .ad-users-page .ad-select {
            width: 100%;
        }
    }

    html.dark .ad-users-page .ad-page-head,
    html.dark .ad-users-page .ad-mini-stat,
    html.dark .ad-users-page .ad-tabs,
    html.dark .ad-users-card,
    html.dark .ad-users-page .ad-filter-bar,
    html.dark .ad-users-page .ad-pagination {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-users-page .ad-page-head h2,
    html.dark .ad-users-page .ad-mini-stat strong,
    html.dark .ad-users-page .ad-table td strong {
        color: #F5EFE8 !important;
    }

    html.dark .ad-users-page .ad-page-head p,
    html.dark .ad-users-page .ad-mini-stat span,
    html.dark .ad-users-page .ad-mini-stat small,
    html.dark .ad-users-page .ad-table td,
    html.dark .ad-users-page .ad-table td small,
    html.dark .ad-users-page .ad-pagination {
        color: #C8B7AD !important;
    }

    html.dark .ad-users-page .ad-overline,
    html.dark .ad-users-page .ad-mini-stat strong {
        color: #EBA99D !important;
    }

    html.dark .ad-users-page .ad-filter-search input,
    html.dark .ad-users-page .ad-select,
    html.dark .ad-users-page .ad-page-btn {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-users-page .ad-table thead {
        background: #1E1A17 !important;
    }

    html.dark .ad-users-page .ad-table th,
    html.dark .ad-users-page .ad-table td {
        border-color: #3B2E27 !important;
    }

    html.dark .ad-users-page .ad-table tbody tr:hover td {
        background: #2D1414 !important;
    }

    html.dark .ad-users-page .ad-tab {
        color: #C8B7AD !important;
    }

    html.dark .ad-users-page .ad-tab:hover {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-users-page .ad-tab.is-active {
        background: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .ad-users-page .ad-avatar.is-soft {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }
</style>
@endpush

@section('content')
<div class="ad-page ad-users-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">
                Platform accounts
            </span>

            <h2>
                User directory
            </h2>

            <p>
                Use warnings for correctable violations and apply bans only for substantiated platform risk.
            </p>
        </div>

        <button
            class="ad-btn ad-btn-primary"
            type="button"
            data-demo-action="Opening notification composer"
        >
            Notify selected users
        </button>
    </div>

    <section class="ad-summary-grid" aria-label="User summary">
        <div class="ad-mini-stat">
            <span>Total users</span>
            <strong>12,480</strong>
            <small>+518 this month</small>
        </div>

        <div class="ad-mini-stat">
            <span>Buyers</span>
            <strong>9,842</strong>
            <small>78.9% of accounts</small>
        </div>

        <div class="ad-mini-stat">
            <span>Sellers</span>
            <strong>1,924</strong>
            <small>1,806 verified</small>
        </div>

        <div class="ad-mini-stat">
            <span>Logistics & riders</span>
            <strong>714</strong>
            <small>22 partner centers</small>
        </div>
    </section>

    <nav class="ad-tabs" aria-label="User role">
        @foreach($roleTabs as $key => $label)
            <a
                class="ad-tab {{ $role === $key ? 'is-active' : '' }}"
                href="{{ route('admin.users', ['role' => $key]) }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <section class="ad-card ad-users-card" id="user-table">
        <div class="ad-filter-bar">
            <label class="ad-filter-search">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>

                <input
                    type="search"
                    value="{{ request('q') }}"
                    data-filter-input="#user-table"
                    placeholder="Search name, email, or user ID"
                >
            </label>

            <div class="ad-inline-actions">
                <select class="ad-select">
                    <option>All statuses</option>
                    <option>Active</option>
                    <option>Under Review</option>
                    <option>Banned</option>
                </select>

                <button
                    class="ad-btn ad-btn-secondary ad-btn-sm"
                    type="button"
                    data-demo-action="User list exported"
                >
                    Export
                </button>
            </div>
        </div>

        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead>
                    <tr>
                        <th>
                            <input
                                type="checkbox"
                                data-select-all
                                aria-label="Select all users"
                            >
                        </th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Orders / jobs</th>
                        <th>Status</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($filtered as $user)
                        @php
                            $statusClass = strtolower(str_replace(' ', '-', $user['status']));
                            $searchText = strtolower(implode(' ', $user));
                        @endphp

                        <tr
                            data-filter-item
                            data-search="{{ $searchText }}"
                        >
                            <td>
                                <input
                                    type="checkbox"
                                    aria-label="Select {{ $user['name'] }}"
                                >
                            </td>

                            <td>
                                <div class="ad-cell-user">
                                    <span class="ad-avatar is-soft">
                                        {{ mb_strtoupper(mb_substr($user['name'], 0, 1)) }}
                                    </span>

                                    <span>
                                        <strong>
                                            {{ $user['name'] }}
                                        </strong>

                                        <small>
                                            {{ $user['id'] }} · {{ $user['email'] }}
                                        </small>
                                    </span>
                                </div>
                            </td>

                            <td>
                                {{ $user['role'] }}
                            </td>

                            <td>
                                {{ $user['joined'] }}
                            </td>

                            <td>
                                {{ number_format($user['orders']) }}
                            </td>

                            <td>
                                <span class="ad-status is-{{ $statusClass }}">
                                    {{ $user['status'] }}
                                </span>
                            </td>

                            <td>
                                <div class="ad-row-actions">
                                    <button
                                        class="ad-btn ad-btn-secondary ad-btn-sm"
                                        type="button"
                                        data-demo-action="Opening {{ $user['name'] }} profile"
                                    >
                                        View
                                    </button>

                                    <button
                                        class="ad-btn ad-btn-warning-soft ad-btn-sm"
                                        type="button"
                                        data-demo-action="Violation notice composer opened"
                                    >
                                        Notify
                                    </button>

                                    @if($user['status'] !== 'Banned')
                                        <button
                                            class="ad-btn ad-btn-danger-soft ad-btn-sm"
                                            type="button"
                                            data-confirm-action
                                            data-confirm-title="Ban this account?"
                                            data-confirm-message="Ban {{ $user['name'] }} from LIKHAE. This must be supported by documented evidence."
                                            data-require-reason="true"
                                            data-success-message="{{ $user['name'] }} was marked banned and logged"
                                        >
                                            Ban
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="ad-pagination">
            <span>
                Showing {{ $filtered->count() }} demo records
            </span>

            <nav>
                <a class="ad-page-btn" href="#">‹</a>
                <a class="ad-page-btn is-active" href="#">1</a>
                <a class="ad-page-btn" href="#">2</a>
                <a class="ad-page-btn" href="#">›</a>
            </nav>
        </div>
    </section>
</div>
@endsection