@extends('rider.app')

@section('title', 'Scan Parcel — LIKHAE Rider')

@section('content')

<div class="mx-auto w-full max-w-[1280px] space-y-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <section
        class="
            border-b
            border-line
            pb-5
        "
    >

        <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-primary">
            Parcel Verification
        </p>

        <h1 class="mt-1.5 text-[28px] font-bold leading-tight tracking-tight text-ink">
            Scan Parcel
        </h1>

        <p class="mt-1.5 text-sm text-muted">
            Confirm that the correct parcel has been collected from the seller.
        </p>

    </section>



    {{-- =========================================================
        SCANNER + PARCEL INFORMATION
    ========================================================== --}}

    <section
        class="
            grid
            gap-4
            xl:grid-cols-[minmax(0,1fr)_300px]
        "
    >


        {{-- =====================================================
            SCANNER
        ====================================================== --}}

        <section
            class="
                rounded-2xl
                border
                border-line
                bg-surface
                p-5
                sm:p-6
            "
        >

            <div>

                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-primary">
                    Verification
                </p>

                <h2 class="mt-1 text-base font-bold text-ink">
                    Barcode Scanner
                </h2>

                <p class="mt-1 text-xs text-muted">
                    Scan the parcel QR code or barcode.
                </p>

            </div>



            {{-- Scanner Area --}}

            <div
                id="scannerArea"
                class="
                    relative
                    mt-5
                    flex
                    h-[300px]
                    items-center
                    justify-center
                    overflow-hidden
                    rounded-xl
                    border
                    border-dashed
                    border-line
                    bg-page-secondary
                "
            >

                {{-- Scanner Frame --}}

                <div
                    id="scannerFrame"
                    class="
                        absolute
                        inset-8
                        rounded-xl
                        border
                        border-primary/30
                        opacity-0
                        transition
                    "
                ></div>


                <div class="relative z-10 text-center">

                    <div
                        id="scannerIcon"
                        class="
                            mx-auto
                            grid
                            h-14
                            w-14
                            place-items-center
                            rounded-xl
                            bg-primary-soft
                            text-primary
                        "
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-6 w-6 fill-none stroke-current"
                        >
                            <rect
                                x="3"
                                y="3"
                                width="18"
                                height="18"
                                rx="2"
                                stroke-width="1.5"
                            ></rect>

                            <path
                                d="M7 7h.01M7 12h.01M7 17h.01"
                                stroke-width="2"
                            ></path>

                            <path
                                d="M11 7h6M11 12h6M11 17h6"
                                stroke-width="1.5"
                            ></path>
                        </svg>

                    </div>


                    <h3
                        id="scannerTitle"
                        class="mt-4 text-sm font-bold text-ink"
                    >
                        Camera Scanner
                    </h3>


                    <p
                        id="scannerDescription"
                        class="mt-1.5 text-xs text-muted"
                    >
                        Point your camera at the parcel barcode.
                    </p>


                    {{-- Scan Result --}}

                    <div
                        id="scanResult"
                        class="mt-4 hidden"
                    >

                        <span
                            class="
                                inline-flex
                                items-center
                                gap-1.5
                                rounded-full
                                bg-success-soft
                                px-3
                                py-1.5
                                text-[10px]
                                font-semibold
                                text-success
                            "
                        >

                            <svg
                                viewBox="0 0 24 24"
                                class="h-3.5 w-3.5 fill-none stroke-current"
                            >
                                <path
                                    d="m5 12 4 4L19 6"
                                    stroke-width="2"
                                ></path>
                            </svg>

                            Parcel Scanned

                        </span>

                    </div>

                </div>

            </div>



            {{-- Scanner Button --}}

            <button
                id="startScannerBtn"
                type="button"
                class="
                    mt-4
                    flex
                    w-full
                    items-center
                    justify-center
                    gap-2
                    rounded-lg
                    bg-primary
                    px-4
                    py-3
                    text-xs
                    font-semibold
                    text-white
                    transition
                    hover:opacity-90
                    disabled:cursor-not-allowed
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-4 w-4 fill-none stroke-current"
                >
                    <path
                        d="M3 7h4l2-3h6l2 3h4v12H3V7Z"
                        stroke-width="1.5"
                    ></path>

                    <circle
                        cx="12"
                        cy="13"
                        r="3"
                        stroke-width="1.5"
                    ></circle>
                </svg>

                Start Scanner

            </button>


            <p
                id="scannerMessage"
                class="
                    mt-2
                    text-center
                    text-[10px]
                    text-muted
                "
            >
                Camera access will be requested when you start scanning.
            </p>

        </section>



        {{-- =====================================================
            RIGHT SIDEBAR
        ====================================================== --}}

        <aside class="space-y-4">


            {{-- Parcel Information --}}

            <section
                class="
                    rounded-2xl
                    border
                    border-line
                    bg-surface
                    p-5
                "
            >

                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-primary">
                    Parcel
                </p>

                <h2 class="mt-1 text-base font-bold text-ink">
                    Parcel Information
                </h2>


                <div class="mt-4 space-y-2.5">


                    {{-- Tracking Number --}}

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            p-3.5
                        "
                    >

                        <p class="text-[10px] text-muted">
                            Tracking Number
                        </p>

                        <p class="mt-1 text-sm font-bold text-ink">
                            LH-2026-1001
                        </p>

                    </div>


                    {{-- Seller --}}

                    <div
                        class="
                            rounded-xl
                            bg-page-secondary
                            p-3.5
                        "
                    >

                        <p class="text-[10px] text-muted">
                            Seller
                        </p>

                        <p class="mt-1 text-xs font-semibold text-ink">
                            ABC Handmade Store
                        </p>

                    </div>


                    {{-- Status --}}

                    <div
                        class="
                            rounded-xl
                            bg-warning-soft
                            p-3.5
                        "
                    >

                        <p class="text-[10px] text-warning">
                            Current Status
                        </p>

                        <p
                            id="parcelStatus"
                            class="mt-1 text-xs font-bold text-warning"
                        >
                            READY_FOR_PICKUP
                        </p>

                    </div>

                </div>

            </section>



            {{-- After Scan --}}

            <section
                class="
                    rounded-2xl
                    bg-primary
                    p-5
                    text-white
                "
            >

                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-white/60">
                    Status Flow
                </p>

                <h2 class="mt-1 text-base font-bold">
                    After Scan
                </h2>

                <p class="mt-1.5 text-xs leading-relaxed text-white/70">
                    The parcel will move through the delivery process after verification.
                </p>


                <div class="mt-4 space-y-2">


                    {{-- Picked Up --}}

                    <div
                        id="pickedUpStep"
                        class="
                            flex
                            items-center
                            gap-2.5
                            rounded-lg
                            bg-white/10
                            px-3
                            py-2.5
                        "
                    >

                        <span
                            class="
                                grid
                                h-6
                                w-6
                                shrink-0
                                place-items-center
                                rounded-full
                                bg-white/10
                            "
                        >

                            <svg
                                viewBox="0 0 24 24"
                                class="h-3.5 w-3.5 fill-none stroke-current"
                            >
                                <path
                                    d="m5 12 4 4L19 6"
                                    stroke-width="2"
                                ></path>
                            </svg>

                        </span>

                        <span class="text-[10px] font-semibold">
                            PICKED_UP
                        </span>

                    </div>


                    {{-- Sorting Center --}}

                    <div
                        id="sortingStep"
                        class="
                            flex
                            items-center
                            gap-2.5
                            rounded-lg
                            bg-white/10
                            px-3
                            py-2.5
                        "
                    >

                        <span
                            class="
                                grid
                                h-6
                                w-6
                                shrink-0
                                place-items-center
                                rounded-full
                                bg-white/10
                            "
                        >

                            <svg
                                viewBox="0 0 24 24"
                                class="h-3.5 w-3.5 fill-none stroke-current"
                            >
                                <path
                                    d="M12 3v18M3 12h18"
                                    stroke-width="1.5"
                                ></path>
                            </svg>

                        </span>

                        <span class="text-[10px] font-semibold">
                            AT_SORTING_CENTER
                        </span>

                    </div>

                </div>

            </section>

        </aside>

    </section>



    {{-- =========================================================
        CONFIRM PICKUP
    ========================================================== --}}

    <section
        class="
            rounded-2xl
            border
            border-line
            bg-surface
            p-5
            sm:p-6
        "
    >

        <div
            class="
                flex
                flex-col
                gap-1
            "
        >

            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-primary">
                Final Step
            </p>

            <h2 class="text-base font-bold text-ink">
                Confirm Pickup
            </h2>

            <p class="text-xs text-muted">
                Verify that the seller successfully handed over the parcel.
            </p>

        </div>


        {{-- Confirmation Buttons --}}

        <div
            class="
                mt-5
                grid
                gap-2.5
                sm:grid-cols-2
            "
        >

            <a
                href="{{ route('rider.pickups.index') }}"
                class="
                    flex
                    items-center
                    justify-center
                    rounded-lg
                    border
                    border-line
                    bg-surface
                    px-4
                    py-3
                    text-xs
                    font-semibold
                    text-ink
                    transition
                    hover:bg-page-secondary
                "
            >
                Cancel
            </a>


            <button
                id="confirmPickupBtn"
                type="button"
                class="
                    flex
                    items-center
                    justify-center
                    gap-2
                    rounded-lg
                    bg-success
                    px-4
                    py-3
                    text-xs
                    font-semibold
                    text-white
                    transition
                    hover:opacity-90
                    disabled:cursor-not-allowed
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-4 w-4 fill-none stroke-current"
                >
                    <path
                        d="m5 12 4 4L19 6"
                        stroke-width="2"
                    ></path>
                </svg>

                Confirm Parcel Pickup

            </button>

        </div>

    </section>

</div>



{{-- =============================================================
    PAGE SCRIPT
============================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const startScannerBtn = document.getElementById('startScannerBtn');
    const confirmPickupBtn = document.getElementById('confirmPickupBtn');

    const scannerFrame = document.getElementById('scannerFrame');
    const scannerIcon = document.getElementById('scannerIcon');
    const scannerTitle = document.getElementById('scannerTitle');
    const scannerDescription = document.getElementById('scannerDescription');

    const scanResult = document.getElementById('scanResult');
    const scannerMessage = document.getElementById('scannerMessage');

    const parcelStatus = document.getElementById('parcelStatus');

    const pickedUpStep = document.getElementById('pickedUpStep');
    const sortingStep = document.getElementById('sortingStep');


    /*
    |--------------------------------------------------------------------------
    | Start Scanner
    |--------------------------------------------------------------------------
    */

    startScannerBtn?.addEventListener('click', function () {

        startScannerBtn.disabled = true;

        startScannerBtn.innerHTML = `
            <svg
                viewBox="0 0 24 24"
                class="h-4 w-4 animate-spin fill-none stroke-current"
            >
                <circle
                    cx="12"
                    cy="12"
                    r="9"
                    stroke-width="1.5"
                    class="opacity-30"
                ></circle>

                <path
                    d="M21 12a9 9 0 0 1-9 9"
                    stroke-width="1.5"
                ></path>
            </svg>

            Scanning...
        `;

        scannerMessage.textContent =
            'Scanning for the parcel barcode...';

        scannerFrame.classList.remove('opacity-0');
        scannerFrame.classList.add('animate-pulse');


        /*
        |--------------------------------------------------------------------------
        | Demo Scan Result
        |--------------------------------------------------------------------------
        |
        | Replace this timeout later with your actual QR/barcode scanner.
        |
        */

        window.setTimeout(function () {

            scannerFrame.classList.remove('animate-pulse');

            scannerIcon.classList.remove(
                'bg-primary-soft',
                'text-primary'
            );

            scannerIcon.classList.add(
                'bg-success-soft',
                'text-success'
            );

            scannerTitle.textContent =
                'Parcel Verified';

            scannerDescription.textContent =
                'The parcel barcode matches this assignment.';

            scanResult.classList.remove('hidden');

            scannerMessage.textContent =
                'Parcel successfully scanned. You may now confirm the pickup.';


            startScannerBtn.innerHTML = `
                <svg
                    viewBox="0 0 24 24"
                    class="h-4 w-4 fill-none stroke-current"
                >
                    <path
                        d="m5 12 4 4L19 6"
                        stroke-width="2"
                    ></path>
                </svg>

                Scan Complete
            `;

            startScannerBtn.classList.remove('bg-primary');
            startScannerBtn.classList.add('bg-success');


            /*
            |--------------------------------------------------------------------------
            | Update Status
            |--------------------------------------------------------------------------
            */

            parcelStatus.textContent =
                'PICKED_UP';

            parcelStatus.classList.remove(
                'text-warning'
            );

            parcelStatus.classList.add(
                'text-success'
            );


            /*
            |--------------------------------------------------------------------------
            | Update Status Flow
            |--------------------------------------------------------------------------
            */

            pickedUpStep.classList.add(
                'bg-success/20'
            );

            sortingStep.classList.add(
                'bg-white/15'
            );


            /*
            |--------------------------------------------------------------------------
            | Enable Confirmation
            |--------------------------------------------------------------------------
            */

            confirmPickupBtn.disabled = false;

        }, 1200);

    });



    /*
    |--------------------------------------------------------------------------
    | Confirm Pickup
    |--------------------------------------------------------------------------
    */

    confirmPickupBtn?.addEventListener('click', function () {

        if (confirmPickupBtn.disabled) {
            return;
        }


        confirmPickupBtn.disabled = true;

        confirmPickupBtn.innerHTML = `
            <svg
                viewBox="0 0 24 24"
                class="h-4 w-4 animate-spin fill-none stroke-current"
            >
                <circle
                    cx="12"
                    cy="12"
                    r="9"
                    stroke-width="1.5"
                    class="opacity-30"
                ></circle>

                <path
                    d="M21 12a9 9 0 0 1-9 9"
                    stroke-width="1.5"
                ></path>
            </svg>

            Confirming...
        `;


        window.setTimeout(function () {

            confirmPickupBtn.innerHTML = `
                <svg
                    viewBox="0 0 24 24"
                    class="h-4 w-4 fill-none stroke-current"
                >
                    <path
                        d="m5 12 4 4L19 6"
                        stroke-width="2"
                    ></path>
                </svg>

                Pickup Confirmed
            `;


            window.setTimeout(function () {

                window.location.href =
                    '{{ route('rider.pickups.index') }}';

            }, 500);

        }, 700);

    });

});

</script>

@endpush

@endsection