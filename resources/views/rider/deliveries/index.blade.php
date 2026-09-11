@extends('rider.app')

@section('title', 'Delivery Assignments — LIKHAE Rider')

@php
    $deliveries = [
        [
            'tracking' => 'LH-2026-1007',
            'buyer' => 'Juan Dela Cruz',
            'address' => 'Los Baños, Laguna',
            'status' => 'ASSIGNED_TO_RIDER',
            'payment' => '₱1,200',
            'area' => 'Area C',
            'priority' => 'Standard',
        ],
        [
            'tracking' => 'LH-2026-1015',
            'buyer' => 'Maria Santos',
            'address' => 'Calamba, Laguna',
            'status' => 'SORTED',
            'payment' => '₱650',
            'area' => 'Area D',
            'priority' => 'Standard',
        ],
        [
            'tracking' => 'LH-2026-1020',
            'buyer' => 'Carlo Reyes',
            'address' => 'Santa Rosa, Laguna',
            'status' => 'OUT_FOR_DELIVERY',
            'payment' => '₱900',
            'area' => 'Area E',
            'priority' => 'Priority',
        ],
    ];

    $summary = [
        [
            'label' => 'Assigned',
            'value' => 8,
            'description' => 'Current assigned parcels',
            'tone' => 'primary',
            'icon' => 'parcel',
        ],
        [
            'label' => 'Ready Delivery',
            'value' => 5,
            'description' => 'Ready for pickup',
            'tone' => 'warning',
            'icon' => 'clock',
        ],
        [
            'label' => 'Delivered',
            'value' => 12,
            'description' => 'Completed today',
            'tone' => 'success',
            'icon' => 'check',
        ],
        [
            'label' => 'Failed',
            'value' => 1,
            'description' => 'Needs follow-up',
            'tone' => 'danger',
            'icon' => 'x',
        ],
    ];

    $process = [
        'Receive Assignment',
        'Pickup From Sorting',
        'Out For Delivery',
        'Deliver Parcel',
        'Buyer Confirms',
    ];

    $icons = [
        'truck' => '
            <path d="M3 6h11v11H3z"/>
            <path d="M14 10h4l3 3v4h-7z"/>
            <circle cx="7" cy="19" r="2"/>
            <circle cx="18" cy="19" r="2"/>
        ',
        'parcel' => '
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5z"/>
            <path d="M4 7.5l8 4.5 8-4.5"/>
            <path d="M12 12v9"/>
        ',
        'clock' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7v5l3 2"/>
        ',
        'check' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="m8 12 2.7 2.7L16.5 9"/>
        ',
        'x' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="m9 9 6 6"/>
            <path d="m15 9-6 6"/>
        ',
        'location' => '
            <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"/>
            <circle cx="12" cy="10" r="2"/>
        ',
        'eye' => '
            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
            <circle cx="12" cy="12" r="2.5"/>
        ',
        'arrow' => '
            <path d="M5 12h14"/>
            <path d="m14 7 5 5-5 5"/>
        ',
    ];
@endphp

@section('content')

<style>
    :root {
        --da-bg: #FBF7F2;
        --da-soft: #F6EFE7;
        --da-warm: #F3E4DE;
        --da-card: #FFFDF9;

        --da-border: #EADCCC;
        --da-border-strong: #DBCEC1;

        --da-maroon: #561C17;
        --da-maroon-2: #642920;
        --da-maroon-dark: #3E130F;

        --da-text: #3B211B;
        --da-text-dark: #1C160F;
        --da-brown: #6C4936;
        --da-muted: #987865;
        --da-muted-light: #A99386;

        --da-tan: #C19771;

        --da-success: #256F4A;
        --da-success-soft: #EAF7EF;

        --da-warning: #9A5B11;
        --da-warning-soft: #FFF6DE;

        --da-danger: #B42318;
        --da-danger-soft: #FCEBE9;

        --da-shadow: 0 8px 24px rgba(86,28,23,.055);
        --da-shadow-hover: 0 18px 44px rgba(86,28,23,.10);
    }

    .da-page {
        display: grid;
        gap: 18px;
        width: 100%;
        color: var(--da-text);
        font-family: "DM Sans", Poppins, system-ui, sans-serif;
    }

    .da-page * {
        box-sizing: border-box;
    }

    .da-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        padding: 32px 36px;
        border: 1px solid var(--da-border);
        border-radius: 28px;
        background:
            radial-gradient(circle at 94% 10%, rgba(193,151,113,.24), transparent 30%),
            radial-gradient(circle at 8% 16%, rgba(86,28,23,.055), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--da-shadow);
    }

    .da-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--da-maroon);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
    }

    .da-eyebrow::before {
        width: 24px;
        height: 1px;
        background: currentColor;
        content: "";
    }

    .da-hero h1 {
        margin: 10px 0 0;
        color: var(--da-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(42px,5vw,66px);
        font-weight: 400;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .da-hero p {
        max-width: 680px;
        margin: 13px 0 0;
        color: var(--da-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .da-active-pill {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        gap: 8px;
        padding: 0 12px;
        border: 1px solid #E6C7BE;
        border-radius: 999px;
        background: var(--da-warm);
        color: var(--da-maroon);
        font-size: 9px;
        font-weight: 900;
        white-space: nowrap;
    }

    .da-active-pill svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .da-stats {
        display: grid;
        grid-template-columns: repeat(4,minmax(0,1fr));
        gap: 14px;
    }

    .da-stat {
        display: flex;
        min-height: 128px;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        padding: 16px;
        border: 1px solid var(--da-border);
        border-radius: 20px;
        background:
            radial-gradient(circle at 94% 6%, rgba(193,151,113,.14), transparent 28%),
            linear-gradient(180deg, #FFFDF9 0%, #FFF9F2 100%);
        box-shadow: var(--da-shadow);
        transition: 160ms ease;
    }

    .da-stat:hover {
        transform: translateY(-2px);
        border-color: var(--da-tan);
        box-shadow: var(--da-shadow-hover);
    }

    .da-stat-icon {
        display: grid;
        width: 38px;
        height: 38px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 13px;
        background: var(--da-warm);
        color: var(--da-maroon);
    }

    .da-stat-icon.is-warning {
        border-color: #EAD39A;
        background: var(--da-warning-soft);
        color: var(--da-warning);
    }

    .da-stat-icon.is-success {
        border-color: #CFE8DA;
        background: var(--da-success-soft);
        color: var(--da-success);
    }

    .da-stat-icon.is-danger {
        border-color: #EDC9C5;
        background: var(--da-danger-soft);
        color: var(--da-danger);
    }

    .da-stat-icon svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.75;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .da-stat-label {
        display: block;
        color: var(--da-muted);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .da-stat-value {
        display: block;
        margin-top: 6px;
        color: var(--da-text-dark);
        font-size: 28px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.045em;
    }

    .da-stat-description {
        display: block;
        margin-top: 6px;
        color: var(--da-muted);
        font-size: 9px;
        font-weight: 700;
    }

    .da-card {
        overflow: hidden;
        border: 1px solid var(--da-border);
        border-radius: 22px;
        background: var(--da-card);
        box-shadow: var(--da-shadow);
    }

    .da-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--da-border);
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.13), transparent 30%),
            linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .da-card-head h2 {
        margin: 0;
        color: var(--da-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .da-card-head p {
        margin: 7px 0 0;
        color: var(--da-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .da-list {
        display: grid;
    }

    .da-delivery {
        display: grid;
        grid-template-columns: auto minmax(0,1fr) auto;
        align-items: center;
        gap: 14px;
        padding: 17px 20px;
        border-bottom: 1px solid var(--da-border);
        transition: 150ms ease;
    }

    .da-delivery:last-child {
        border-bottom: 0;
    }

    .da-delivery:hover {
        background: var(--da-soft);
    }

    .da-delivery-icon {
        display: grid;
        width: 48px;
        height: 48px;
        place-items: center;
        border: 1px solid #E6C7BE;
        border-radius: 15px;
        background: var(--da-warm);
        color: var(--da-maroon);
    }

    .da-delivery-icon svg {
        width: 21px;
        height: 21px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .da-delivery-main {
        min-width: 0;
    }

    .da-delivery-main h3 {
        margin: 0;
        color: var(--da-text);
        font-size: 11px;
        font-weight: 950;
    }

    .da-delivery-main p {
        margin: 5px 0 0;
        color: var(--da-muted);
        font-size: 9px;
    }

    .da-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px 12px;
        margin-top: 8px;
        color: var(--da-brown);
        font-size: 8px;
        font-weight: 750;
    }

    .da-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .da-meta svg {
        width: 12px;
        height: 12px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .da-cod {
        color: var(--da-maroon);
        font-weight: 950;
    }

    .da-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 7px;
    }

    .da-status {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        gap: 6px;
        padding: 0 9px;
        border: 1px solid #E6C7BE;
        border-radius: 999px;
        background: var(--da-warm);
        color: var(--da-maroon);
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .da-status::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        content: "";
    }

    .da-status.is-success {
        border-color: #CFE8DA;
        background: var(--da-success-soft);
        color: var(--da-success);
    }

    .da-btn {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 11px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: 9px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        transition: 150ms ease;
    }

    .da-btn:hover {
        transform: translateY(-1px);
    }

    .da-btn svg {
        width: 13px;
        height: 13px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .da-btn-soft {
        border-color: var(--da-border);
        background: var(--da-card);
        color: var(--da-maroon);
    }

    .da-btn-soft:hover {
        border-color: var(--da-tan);
        background: var(--da-warm);
    }

    .da-btn-primary {
        border-color: var(--da-maroon);
        background: var(--da-maroon);
        color: #FFFFFF;
        box-shadow: 0 8px 18px rgba(86,28,23,.12);
    }

    .da-btn-primary:hover {
        border-color: var(--da-maroon-dark);
        background: var(--da-maroon-dark);
    }

    .da-btn-success {
        border-color: var(--da-success) !important;
        background: var(--da-success) !important;
        color: #FFFFFF !important;
    }

    .da-btn[disabled] {
        opacity: .9;
        cursor: default;
        transform: none;
    }

    .da-process {
        display: grid;
        grid-template-columns: repeat(5,minmax(0,1fr));
        gap: 12px;
        padding: 18px 20px;
    }

    .da-step {
        position: relative;
        min-height: 145px;
        padding: 14px;
        border: 1px solid var(--da-border);
        border-radius: 16px;
        background: var(--da-soft);
    }

    .da-step:not(:last-child)::after {
        position: absolute;
        top: 31px;
        right: -13px;
        width: 14px;
        height: 1px;
        background: var(--da-border-strong);
        content: "";
    }

    .da-step-number {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border-radius: 11px;
        background: var(--da-maroon);
        color: #FFFFFF;
        font-size: 9px;
        font-weight: 950;
        box-shadow: 0 8px 16px rgba(86,28,23,.12);
    }

    .da-step h3 {
        margin: 13px 0 0;
        color: var(--da-text);
        font-size: 9px;
        font-weight: 950;
        line-height: 1.35;
    }

    .da-step p {
        margin: 6px 0 0;
        color: var(--da-muted);
        font-size: 8px;
        line-height: 1.45;
    }

    @media (max-width: 1180px) {
        .da-stats {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }

        .da-process {
            grid-template-columns: repeat(3,minmax(0,1fr));
        }

        .da-step::after {
            display: none;
        }

        .da-delivery {
            grid-template-columns: auto minmax(0,1fr);
        }

        .da-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
            padding-left: 62px;
        }
    }

    @media (max-width: 820px) {
        .da-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px;
        }

        .da-process {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }
    }

    @media (max-width: 560px) {
        .da-stats,
        .da-process {
            grid-template-columns: 1fr;
        }

        .da-delivery {
            grid-template-columns: 1fr;
        }

        .da-actions {
            grid-column: auto;
            justify-content: flex-start;
            padding-left: 0;
        }

        .da-card-head {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    html.dark .da-page {
        color: #F5EFE8;
    }

    html.dark .da-hero,
    html.dark .da-stat,
    html.dark .da-card {
        background:
            radial-gradient(circle at 94% 8%, rgba(193,151,113,.07), transparent 28%),
            linear-gradient(180deg, #211B17 0%, #1A1412 100%) !important;
        border-color: #3B2E27 !important;
        box-shadow: none !important;
    }

    html.dark .da-card-head {
        background:
            radial-gradient(circle at 96% 6%, rgba(193,151,113,.07), transparent 30%),
            #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .da-hero h1,
    html.dark .da-stat-value,
    html.dark .da-card-head h2,
    html.dark .da-delivery-main h3,
    html.dark .da-step h3 {
        color: #F5EFE8 !important;
    }

    html.dark .da-hero p,
    html.dark .da-stat-label,
    html.dark .da-stat-description,
    html.dark .da-card-head p,
    html.dark .da-delivery-main p,
    html.dark .da-meta,
    html.dark .da-step p {
        color: #AFA19A !important;
    }

    html.dark .da-eyebrow {
        color: #EBA99D !important;
    }

    html.dark .da-delivery {
        border-color: #30231F !important;
    }

    html.dark .da-delivery:hover {
        background: #241817 !important;
    }

    html.dark .da-delivery-icon,
    html.dark .da-stat-icon {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .da-status {
        background: #2D1816 !important;
        border-color: #60463A !important;
        color: #EBA99D !important;
    }

    html.dark .da-btn-soft {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
        color: #EBA99D !important;
    }

    html.dark .da-btn-primary {
        background: #A84538 !important;
        border-color: #A84538 !important;
    }

    html.dark .da-btn-primary:hover {
        background: #B84B43 !important;
        border-color: #B84B43 !important;
    }

    html.dark .da-step {
        background: #1D1715 !important;
        border-color: #3B2E27 !important;
    }

    html.dark .da-step-number {
        background: #A84538 !important;
    }
</style>

<div class="da-page">

    <section class="da-hero">
        <div>
            <span class="da-eyebrow">
                Delivery Management
            </span>

            <h1>
                Delivery Assignments
            </h1>

            <p>
                Review assigned parcels from the sorting center, accept delivery work,
                and continue each parcel through the final-mile process.
            </p>
        </div>

        <span class="da-active-pill">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                {!! $icons['truck'] !!}
            </svg>

            8 Active Deliveries
        </span>
    </section>

    <section class="da-stats" aria-label="Delivery assignment summary">
        @foreach($summary as $item)
            @php
                $toneClass = match($item['tone']) {
                    'warning' => 'is-warning',
                    'success' => 'is-success',
                    'danger' => 'is-danger',
                    default => '',
                };
            @endphp

            <article class="da-stat">
                <span class="da-stat-icon {{ $toneClass }}" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        {!! $icons[$item['icon']] !!}
                    </svg>
                </span>

                <div>
                    <span class="da-stat-label">
                        {{ $item['label'] }}
                    </span>

                    <strong class="da-stat-value">
                        {{ $item['value'] }}
                    </strong>

                    <span class="da-stat-description">
                        {{ $item['description'] }}
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="da-card">
        <header class="da-card-head">
            <div>
                <h2>
                    Assigned Deliveries
                </h2>

                <p>
                    Parcels assigned from the sorting center and ready for rider action.
                </p>
            </div>

            <span class="da-active-pill">
                {{ count($deliveries) }} shown
            </span>
        </header>

        <div class="da-list">
            @foreach($deliveries as $delivery)
                @php
                    $isOutForDelivery =
                        $delivery['status'] === 'OUT_FOR_DELIVERY';
                @endphp

                <article class="da-delivery">
                    <span class="da-delivery-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            {!! $icons['truck'] !!}
                        </svg>
                    </span>

                    <div class="da-delivery-main">
                        <h3>
                            {{ $delivery['tracking'] }}
                        </h3>

                        <p>
                            Buyer: {{ $delivery['buyer'] }}
                        </p>

                        <div class="da-meta">
                            <span>
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    {!! $icons['location'] !!}
                                </svg>

                                {{ $delivery['address'] }}
                            </span>

                            <span>
                                {{ $delivery['area'] }}
                            </span>

                            <span>
                                {{ $delivery['priority'] }}
                            </span>

                            <span class="da-cod">
                                COD {{ $delivery['payment'] }}
                            </span>
                        </div>
                    </div>

                    <div class="da-actions">
                        <span class="da-status {{ $isOutForDelivery ? 'is-success' : '' }}">
                            {{ $delivery['status'] }}
                        </span>

                        <a
                            href="{{ route('rider.deliveries.show', $delivery['tracking']) }}"
                            class="da-btn da-btn-soft"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $icons['eye'] !!}
                            </svg>

                            View
                        </a>

                        <button
                            type="button"
                            class="accept-delivery-btn da-btn {{ $isOutForDelivery ? 'da-btn-success' : 'da-btn-primary' }}"
                            data-tracking="{{ $delivery['tracking'] }}"
                            {{ $isOutForDelivery ? 'disabled' : '' }}
                        >
                            {{
                                $isOutForDelivery
                                    ? 'Accepted'
                                    : 'Accept Delivery'
                            }}
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="da-card">
        <header class="da-card-head">
            <div>
                <h2>
                    Delivery Process
                </h2>

                <p>
                    Follow the standard LIKHAE delivery flow from assignment to buyer confirmation.
                </p>
            </div>
        </header>

        <div class="da-process">
            @foreach($process as $index => $step)
                <article class="da-step">
                    <span class="da-step-number">
                        {{ $index + 1 }}
                    </span>

                    <h3>
                        {{ $step }}
                    </h3>

                    <p>
                        {{
                            match($index) {
                                0 => 'Review the parcel and accept the assigned delivery.',
                                1 => 'Collect the parcel from the LIKHAE sorting center.',
                                2 => 'Mark the parcel as actively heading to the customer.',
                                3 => 'Complete the handoff at the delivery destination.',
                                default => 'Wait for buyer confirmation and close the delivery.',
                            }
                        }}
                    </p>
                </article>
            @endforeach
        </div>
    </section>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document
        .querySelectorAll('.accept-delivery-btn')
        .forEach(function (button) {
            button.addEventListener(
                'click',
                function () {
                    if (button.disabled) {
                        return;
                    }

                    button.textContent =
                        'Accepted';

                    button.disabled =
                        true;

                    button.classList.remove(
                        'da-btn-primary'
                    );

                    button.classList.add(
                        'da-btn-success'
                    );

                    const actions =
                        button.closest('.da-actions');

                    const badge =
                        actions?.querySelector(
                            '.da-status'
                        );

                    if (badge) {
                        badge.textContent =
                            'OUT_FOR_DELIVERY';

                        badge.classList.add(
                            'is-success'
                        );
                    }

                    window.setTimeout(
                        function () {
                            window.location.href =
                                '{{ route('rider.deliveries.show', ':tracking') }}'
                                    .replace(
                                        ':tracking',
                                        button.dataset.tracking
                                    );
                        },
                        350
                    );
                }
            );
        });
});
</script>
@endpush
