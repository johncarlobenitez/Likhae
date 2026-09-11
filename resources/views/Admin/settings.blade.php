@extends('layouts.admin')

@section('title', 'System Management')
@section('subtitle', 'Maintain platform policies, operational settings, and traceable audit records.')
@section('active', 'settings')

@php
    $tabs = [
        'policies' => 'Platform Policies',
        'platform' => 'Platform Settings',
        'audit' => 'Audit Logs',
    ];

    $requestedTab = request('tab', 'policies');

    $tab = array_key_exists($requestedTab, $tabs)
        ? $requestedTab
        : 'policies';

    $policies = [
        [
            'title' => 'Restricted and prohibited products',
            'description' => 'Revision 3.2 · Published Sep 3, 2026 · Applies to all sellers',
            'status' => 'Published',
            'action' => 'Opening prohibited-products policy',
            'button' => 'Edit',
        ],
        [
            'title' => 'Seller compliance standard',
            'description' => 'Revision 2.8 · Published Aug 20, 2026',
            'status' => 'Published',
            'action' => 'Opening seller policy',
            'button' => 'Edit',
        ],
        [
            'title' => 'Returns and refund policy',
            'description' => 'Revision 4.1 · Published Aug 5, 2026',
            'status' => 'Published',
            'action' => 'Opening returns policy',
            'button' => 'Edit',
        ],
        [
            'title' => 'Privacy and account deletion',
            'description' => 'Revision 1.9 · Draft updated Sep 2, 2026',
            'status' => 'Draft',
            'action' => 'Opening privacy policy draft',
            'button' => 'Review',
        ],
    ];

    $auditLogs = [
        [
            'timestamp' => 'Sep 4, 10:51 AM',
            'actor' => 'Admin User',
            'action' => 'Listing removed',
            'target' => 'PRD-8448',
            'reason' => 'Prohibited weapon evidence confirmed',
            'ip' => '192.168.1.24',
            'result' => 'Success',
        ],
        [
            'timestamp' => 'Sep 4, 10:12 AM',
            'actor' => 'Admin User',
            'action' => 'Violation notice',
            'target' => 'USR-10078',
            'reason' => 'Misleading listing information',
            'ip' => '192.168.1.24',
            'result' => 'Success',
        ],
        [
            'timestamp' => 'Sep 4, 9:32 AM',
            'actor' => 'Finance Admin',
            'action' => 'Settlement retried',
            'target' => 'TXN-980138',
            'reason' => 'Payment provider timeout',
            'ip' => '192.168.1.19',
            'result' => 'Pending',
        ],
        [
            'timestamp' => 'Sep 3, 5:15 PM',
            'actor' => 'Super Admin',
            'action' => 'Policy published',
            'target' => 'POL-012',
            'reason' => 'Restricted products revision 3.2',
            'ip' => '192.168.1.10',
            'result' => 'Success',
        ],
    ];
@endphp

@section('content')
<style>
    :root {
        --sys-bg: #FBF7F2;
        --sys-bg-soft: #F6EFE7;
        --sys-bg-alt: #EFE7DE;
        --sys-card: #FFFDF9;

        --sys-border: #EADCCC;
        --sys-border-strong: #DBCEC1;

        --sys-maroon: #561C17;
        --sys-maroon-2: #642920;
        --sys-maroon-dark: #3E130F;

        --sys-text: #3B211B;
        --sys-brown: #6C4936;
        --sys-muted: #987865;
        --sys-muted-2: #A99386;

        --sys-tan: #C19771;

        --sys-success: #256F4A;
        --sys-success-soft: #EAF7EF;

        --sys-warning: #9A5B11;
        --sys-warning-soft: #FFF6DE;

        --sys-danger: #B42318;
        --sys-danger-soft: #FCEBE9;

        --sys-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
        --sys-shadow-card: 0 18px 44px rgba(86, 28, 23, 0.10);
    }

    .ad-system-page {
        color: var(--sys-text);

        --ad-blue: var(--sys-maroon);
        --ad-blue-dark: var(--sys-maroon-dark);
        --ad-blue-soft: #F3E4DE;
        --ad-blue-border: #E6C7BE;
    }

    .ad-system-page .ad-page-head {
        padding: 32px 36px;

        border: 1px solid var(--sys-border);
        border-radius: 28px;

        background:
            radial-gradient(circle at 94% 12%, rgba(193, 151, 113, 0.24), transparent 28%),
            radial-gradient(circle at 8% 18%, rgba(86, 28, 23, 0.06), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);

        box-shadow: var(--sys-shadow-soft);
    }

    .ad-system-page .ad-overline {
        color: var(--sys-maroon) !important;

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-system-page .ad-page-head h2 {
        margin-top: 10px;

        color: var(--sys-text) !important;

        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-system-page .ad-page-head p {
        max-width: 680px;
        margin-top: 13px;

        color: var(--sys-muted) !important;

        font-size: 13px;
        line-height: 1.7;
    }

    .ad-system-page .ad-btn-primary {
        background: var(--sys-maroon) !important;
        border-color: var(--sys-maroon) !important;
        color: #FFFFFF !important;

        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
    }

    .ad-system-page .ad-btn-primary:hover {
        background: var(--sys-maroon-dark) !important;
        border-color: var(--sys-maroon-dark) !important;
    }

    .ad-system-page .ad-btn-secondary {
        background: var(--sys-card) !important;
        border-color: var(--sys-tan) !important;
        color: var(--sys-maroon) !important;
    }

    .ad-system-page .ad-btn-secondary:hover {
        background: #F3E4DE !important;
        border-color: var(--sys-maroon) !important;
    }

    .ad-system-layout {
        display: grid;
        grid-template-columns: 260px minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .ad-system-nav {
        display: grid;
        gap: 6px;

        padding: 8px;

        border: 1px solid var(--sys-border) !important;
        border-radius: 22px !important;

        background:
            radial-gradient(circle at 92% 8%, rgba(193, 151, 113, 0.14), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%) !important;

        box-shadow: var(--sys-shadow-soft) !important;
    }

    .ad-system-nav a {
        display: flex;
        min-height: 42px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;

        padding: 0 14px;

        border-radius: 14px;

        color: var(--sys-brown);

        font-size: 12px;
        font-weight: 900;
        text-decoration: none;

        transition: 160ms ease;
    }

    .ad-system-nav a:hover {
        background: var(--sys-bg-soft);
        color: var(--sys-maroon);
    }

    .ad-system-nav a.is-active {
        background: var(--sys-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86, 28, 23, 0.16);
    }

    .ad-system-panel {
        overflow: hidden;

        border: 1px solid var(--sys-border) !important;
        border-radius: 24px !important;

        background: var(--sys-card) !important;
        color: var(--sys-text) !important;

        box-shadow: var(--sys-shadow-soft) !important;
    }

    .ad-system-section {
        display: grid;
        gap: 20px;

        padding: 24px;
    }

    .ad-system-section h2 {
        margin: 0;

        color: var(--sys-text);

        font-family: "Instrument Serif", Georgia, serif;
        font-size: 34px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-system-section > p {
        max-width: 660px;
        margin: -10px 0 0;

        color: var(--sys-muted);

        font-size: 12px;
        line-height: 1.65;
    }

    .ad-system-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .ad-system-form-grid .is-full {
        grid-column: 1 / -1;
    }

    .ad-system-page .ad-field {
        display: grid;
        gap: 7px;
    }

    .ad-system-page .ad-field span,
    .ad-system-page .ad-field label {
        color: var(--sys-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .ad-system-page .ad-field input,
    .ad-system-page .ad-field select,
    .ad-system-page .ad-field textarea {
        width: 100%;
        min-height: 43px;
        padding: 0 13px;

        border: 1px solid var(--sys-border);
        border-radius: 14px;

        background: var(--sys-bg-soft);
        color: var(--sys-text);

        font-size: 12px;
        font-weight: 700;
        outline: 0;

        transition: 160ms ease;
    }

    .ad-system-page .ad-field textarea {
        min-height: 150px;
        padding-block: 12px;
        resize: vertical;
    }

    .ad-system-page .ad-field input:focus,
    .ad-system-page .ad-field select:focus,
    .ad-system-page .ad-field textarea:focus {
        border-color: var(--sys-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-system-page .ad-field input[readonly] {
        background: #EFE7DE;
        color: var(--sys-maroon);
        cursor: not-allowed;
    }

    .ad-system-page .ad-setting-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;

        padding: 17px 0;

        border-bottom: 1px solid var(--sys-border);
    }

    .ad-system-page .ad-setting-row:last-child {
        border-bottom: 0;
    }

    .ad-system-page .ad-setting-row strong {
        display: block;

        color: var(--sys-text);

        font-size: 13px;
        font-weight: 950;
        letter-spacing: -0.025em;
    }

    .ad-system-page .ad-setting-row p {
        margin: 5px 0 0;

        color: var(--sys-muted);

        font-size: 11px;
        line-height: 1.6;
    }

    .ad-system-page .ad-switch {
        position: relative;

        display: inline-flex;
        width: 46px;
        height: 26px;
        flex: 0 0 46px;
    }

    .ad-system-page .ad-switch input {
        position: absolute;
        opacity: 0;
    }

    .ad-system-page .ad-switch span {
        position: absolute;
        inset: 0;

        border: 1px solid var(--sys-border-strong);
        border-radius: 999px;

        background: #E7D9CB;

        cursor: pointer;
        transition: 180ms ease;
    }

    .ad-system-page .ad-switch span::after {
        position: absolute;
        top: 3px;
        left: 3px;

        width: 18px;
        height: 18px;

        border-radius: 999px;

        background: #FFFFFF;
        box-shadow: 0 2px 7px rgba(86, 28, 23, 0.18);

        content: "";
        transition: 180ms ease;
    }

    .ad-system-page .ad-switch input:checked + span {
        border-color: var(--sys-maroon);
        background: var(--sys-maroon);
    }

    .ad-system-page .ad-switch input:checked + span::after {
        transform: translateX(20px);
    }

    .ad-system-page .ad-card-list {
        display: grid;
        gap: 2px;

        border: 1px solid var(--sys-border);
        border-radius: 20px;

        background: var(--sys-card);
        overflow: hidden;
    }

    .ad-policy-row {
        padding: 17px 18px !important;

        background:
            radial-gradient(circle at 96% 8%, rgba(193, 151, 113, 0.09), transparent 28%),
            #FFFDF9;
    }

    .ad-policy-row + .ad-policy-row {
        border-top: 1px solid var(--sys-border);
    }

    .ad-system-page .ad-status {
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

    .ad-system-page .ad-status.is-active,
    .ad-system-page .ad-status.is-published,
    .ad-system-page .ad-status.is-completed,
    .ad-system-page .ad-status.is-success {
        background: var(--sys-success-soft);
        border-color: #CFE8DA;
        color: var(--sys-success);
    }

    .ad-system-page .ad-status.is-pending,
    .ad-system-page .ad-status.is-draft {
        background: var(--sys-warning-soft);
        border-color: #EAD39A;
        color: var(--sys-warning);
    }

    .ad-system-page .ad-status.is-failed,
    .ad-system-page .ad-status.is-rejected {
        background: var(--sys-danger-soft);
        border-color: #F0C9C4;
        color: var(--sys-danger);
    }

    .ad-system-page .ad-filter-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;

        padding: 15px;

        border-bottom: 1px solid var(--sys-border);

        background:
            radial-gradient(circle at 96% 6%, rgba(193, 151, 113, 0.12), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ad-system-page .ad-filter-search {
        position: relative;
        flex: 1;
        max-width: 500px;
    }

    .ad-system-page .ad-filter-search svg {
        position: absolute;
        left: 14px;
        top: 50%;

        width: 16px;
        height: 16px;

        color: var(--sys-muted-2);

        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;

        transform: translateY(-50%);
    }

    .ad-system-page .ad-filter-search input {
        width: 100%;
        min-height: 42px;
        padding: 0 14px 0 42px;

        border: 1px solid var(--sys-border);
        border-radius: 14px;

        background: var(--sys-bg-soft);
        color: var(--sys-text);

        font-size: 12px;
        font-weight: 700;
        outline: none;
    }

    .ad-system-page .ad-filter-search input:focus {
        border-color: var(--sys-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-system-page .ad-select {
        min-height: 42px;
        padding: 0 12px;

        border: 1px solid var(--sys-border);
        border-radius: 14px;

        background: var(--sys-bg-soft);
        color: var(--sys-text);

        font-size: 11px;
        font-weight: 800;
        outline: none;
    }

    .ad-system-page .ad-table {
        width: 100%;
        min-width: 960px;
        border-collapse: collapse;
    }

    .ad-system-page .ad-table thead {
        background: var(--sys-bg-soft);
    }

    .ad-system-page .ad-table th {
        padding: 14px 16px;

        border-bottom: 1px solid var(--sys-border);

        color: var(--sys-muted);

        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .ad-system-page .ad-table td {
        padding: 15px 16px;

        border-bottom: 1px solid #EFE1D5;

        color: var(--sys-brown);

        font-size: 12px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .ad-system-page .ad-table tbody tr:hover td {
        background: var(--sys-bg-soft);
    }

    .ad-system-page .ad-table td strong {
        color: var(--sys-text);
        font-size: 12px;
        font-weight: 950;
    }

    @media (max-width: 980px) {
        .ad-system-layout {
            grid-template-columns: 1fr;
        }

        .ad-system-nav {
            display: flex;
            overflow-x: auto;
        }

        .ad-system-nav a {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .ad-system-form-grid {
            grid-template-columns: 1fr;
        }

        .ad-system-page .ad-filter-bar {
            align-items: stretch;
            flex-direction: column;
        }

        .ad-system-page .ad-filter-search {
            max-width: none;
        }
    }

    @media (max-width: 700px) {
        .ad-system-page .ad-page-head {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .ad-system-page .ad-page-head .ad-btn,
        .ad-system-page .ad-select,
        .ad-system-page .ad-btn {
            width: 100%;
        }

        .ad-system-section {
            padding: 20px;
        }

        .ad-system-page .ad-setting-row {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    html.dark .ad-system-page .ad-page-head,
    html.dark .ad-system-nav,
    html.dark .ad-system-panel,
    html.dark .ad-policy-row,
    html.dark .ad-system-page .ad-filter-bar {
        background:
            radial-gradient(circle at 94% 8%, rgba(193, 151, 113, 0.08), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1E1A17 100%) !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-system-page .ad-page-head h2,
    html.dark .ad-system-section h2,
    html.dark .ad-system-page .ad-setting-row strong,
    html.dark .ad-system-page .ad-table td strong {
        color: #F5EFE8 !important;
    }

    html.dark .ad-system-page .ad-page-head p,
    html.dark .ad-system-section > p,
    html.dark .ad-system-page .ad-setting-row p,
    html.dark .ad-system-page .ad-table td {
        color: #C8B7AD !important;
    }

    html.dark .ad-system-page .ad-overline {
        color: #EBA99D !important;
    }

    html.dark .ad-system-nav a {
        color: #C8B7AD !important;
    }

    html.dark .ad-system-nav a:hover {
        background: #2D1414 !important;
        color: #EBA99D !important;
    }

    html.dark .ad-system-nav a.is-active {
        background: #8A3A2F !important;
        color: #FFFFFF !important;
    }

    html.dark .ad-system-page .ad-field input,
    html.dark .ad-system-page .ad-field select,
    html.dark .ad-system-page .ad-field textarea,
    html.dark .ad-system-page .ad-filter-search input,
    html.dark .ad-system-page .ad-select,
    html.dark .ad-system-page .ad-table thead {
        background: #1E1A17 !important;
        border-color: #3B2E27 !important;
        color: #F5EFE8 !important;
    }

    html.dark .ad-system-page .ad-table th,
    html.dark .ad-system-page .ad-table td,
    html.dark .ad-system-page .ad-setting-row,
    html.dark .ad-system-page .ad-card-list {
        border-color: #3B2E27 !important;
    }

    html.dark .ad-system-page .ad-table tbody tr:hover td {
        background: #2D1414 !important;
    }
</style>

<div class="ad-page ad-system-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">
                System governance
            </span>

            <h2>
                {{ $tabs[$tab] }}
            </h2>

            <p>
                High-impact settings should be deliberate, permission-controlled, and auditable.
            </p>
        </div>

        @if($tab !== 'audit')
            <button
                class="ad-btn ad-btn-primary"
                type="button"
                data-demo-action="Changes saved for this frontend preview"
            >
                Save changes
            </button>
        @else
            <button
                class="ad-btn ad-btn-secondary"
                type="button"
                data-demo-action="Audit logs exported"
            >
                Export logs
            </button>
        @endif
    </div>

    <section class="ad-system-layout">
        <nav class="ad-card ad-system-nav" aria-label="System management sections">
            @foreach($tabs as $key => $label)
                <a
                    class="{{ $tab === $key ? 'is-active' : '' }}"
                    href="{{ route('admin.settings', ['tab' => $key]) }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="ad-card ad-system-panel">
            @if($tab === 'platform')
                <form
                    class="ad-system-section"
                    data-demo-form
                    data-success-message="Platform settings saved"
                >
                    <h2>
                        Marketplace configuration
                    </h2>

                    <p>
                        Manage general platform behavior, registration access, commission visibility, and operational controls.
                    </p>

                    <div class="ad-system-form-grid">
                        <label class="ad-field">
                            <span>Marketplace name</span>
                            <input value="LIKHAE Marketplace">
                        </label>

                        <label class="ad-field">
                            <span>Support email</span>
                            <input type="email" value="support@likhae.ph">
                        </label>

                        <label class="ad-field">
                            <span>Default currency</span>
                            <select>
                                <option>PHP — Philippine Peso</option>
                            </select>
                        </label>

                        <label class="ad-field">
                            <span>Platform commission</span>
                            <input value="10%" readonly>
                        </label>
                    </div>

                    <div>
                        <div class="ad-setting-row">
                            <div>
                                <strong>New registrations</strong>
                                <p>Allow new buyer, seller, logistics, and rider applications.</p>
                            </div>

                            <label class="ad-switch">
                                <input type="checkbox" checked>
                                <span></span>
                            </label>
                        </div>

                        <div class="ad-setting-row">
                            <div>
                                <strong>Automated product risk signals</strong>
                                <p>Flag listings for human review; never remove listings automatically.</p>
                            </div>

                            <label class="ad-switch">
                                <input type="checkbox" checked>
                                <span></span>
                            </label>
                        </div>

                        <div class="ad-setting-row">
                            <div>
                                <strong>Maintenance notice</strong>
                                <p>Display a platform-wide maintenance banner for scheduled downtime.</p>
                            </div>

                            <label class="ad-switch">
                                <input type="checkbox">
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <button
                        class="ad-btn ad-btn-primary"
                        type="submit"
                    >
                        Save platform settings
                    </button>
                </form>
            @elseif($tab === 'audit')
                <div id="audit-table">
                    <div class="ad-filter-bar">
                        <label class="ad-filter-search">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="m20 20-3.5-3.5"/>
                            </svg>

                            <input
                                type="search"
                                data-filter-input="#audit-table"
                                placeholder="Search actor, action, or record ID"
                            >
                        </label>

                        <select class="ad-select">
                            <option>All action types</option>
                            <option>Account enforcement</option>
                            <option>Product moderation</option>
                            <option>Policy change</option>
                        </select>
                    </div>

                    <div class="ad-table-wrap">
                        <table class="ad-table">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Actor</th>
                                    <th>Action</th>
                                    <th>Target</th>
                                    <th>Reason / change</th>
                                    <th>IP address</th>
                                    <th>Result</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($auditLogs as $log)
                                    @php
                                        $resultClass = \Illuminate\Support\Str::slug($log['result']);
                                    @endphp

                                    <tr
                                        data-filter-item
                                        data-search="{{ strtolower(implode(' ', $log)) }}"
                                    >
                                        <td>{{ $log['timestamp'] }}</td>

                                        <td>
                                            <strong>{{ $log['actor'] }}</strong>
                                        </td>

                                        <td>{{ $log['action'] }}</td>
                                        <td>{{ $log['target'] }}</td>
                                        <td>{{ $log['reason'] }}</td>
                                        <td>{{ $log['ip'] }}</td>

                                        <td>
                                            <span class="ad-status is-{{ $resultClass }}">
                                                {{ $log['result'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="ad-system-section">
                    <h2>
                        Platform policies
                    </h2>

                    <p>
                        Published rules used for registration, marketplace moderation, seller compliance, complaints, returns, and refunds.
                    </p>

                    <div class="ad-card-list">
                        @foreach($policies as $policy)
                            @php
                                $statusClass = \Illuminate\Support\Str::slug($policy['status']);
                            @endphp

                            <div
                                class="ad-setting-row ad-policy-row"
                                data-filter-item
                                data-search="{{ strtolower($policy['title'] . ' ' . $policy['description'] . ' ' . $policy['status']) }}"
                            >
                                <div>
                                    <strong>
                                        {{ $policy['title'] }}
                                    </strong>

                                    <p>
                                        {{ $policy['description'] }}
                                    </p>
                                </div>

                                <div class="ad-inline-actions">
                                    <span class="ad-status is-{{ $statusClass }}">
                                        {{ $policy['status'] }}
                                    </span>

                                    <button
                                        class="ad-btn ad-btn-secondary ad-btn-sm"
                                        type="button"
                                        data-demo-action="{{ $policy['action'] }}"
                                    >
                                        {{ $policy['button'] }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button
                        class="ad-btn ad-btn-primary"
                        type="button"
                        data-demo-action="New policy editor opened"
                    >
                        Create policy
                    </button>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection