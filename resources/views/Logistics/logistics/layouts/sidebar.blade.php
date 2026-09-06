<aside
    id="logisticsSidebar"
    class="
        fixed
        inset-y-0
        left-0
        z-50

        flex
        w-[245px]
        -translate-x-full
        flex-col

        overflow-y-auto

        border-r
        border-white/10

        bg-sidebar
        text-sidebar-text

        shadow-[10px_0_35px_rgba(50,15,18,0.08)]

        transition-transform
        duration-300
        ease-out

        lg:translate-x-0
    "
>

    {{-- =====================================================
        BRAND
    ====================================================== --}}

    <div
        class="
            flex
            min-h-[78px]
            items-center
            justify-between

            border-b
            border-white/10

            px-5
        "
    >

        <a
            href="{{ route('logistics.dashboard') }}"
            class="
                flex
                min-w-0
                items-center
                gap-3
            "
        >

            <span
                class="
                    grid
                    h-9
                    w-9
                    shrink-0
                    place-items-center

                    rounded-[10px]

                    bg-white

                    text-[15px]
                    font-black
                    text-[#74181c]

                    shadow-sm

                    dark:bg-primary
                    dark:text-white
                "
            >
                L
            </span>


            <span class="min-w-0">

                <strong
                    class="
                        block

                        text-[14px]
                        font-extrabold
                        leading-none
                        tracking-[-0.04em]
                        text-white
                    "
                >
                    LIKHAE
                </strong>


                <small
                    class="
                        mt-1
                        block

                        text-[7px]
                        font-semibold
                        uppercase
                        tracking-[0.18em]

                        text-white/45
                    "
                >
                    Logistics Center
                </small>

            </span>

        </a>


        {{-- MOBILE CLOSE --}}

        <button
            type="button"
            id="mobileSidebarClose"
            aria-label="Close navigation"
            class="
                grid
                h-8
                w-8
                place-items-center

                rounded-lg

                text-white/60

                transition

                hover:bg-white/10
                hover:text-white

                lg:hidden
            "
        >

            <svg
                viewBox="0 0 24 24"
                class="
                    h-4
                    w-4

                    fill-none
                    stroke-current
                    stroke-[1.7]
                "
            >
                <path d="M6 6l12 12"></path>
                <path d="M18 6 6 18"></path>
            </svg>

        </button>

    </div>


    {{-- =====================================================
        LOGISTICS USER
    ====================================================== --}}

    <div class="px-4 py-4">

        <div
            class="
                flex
                items-center
                gap-3

                rounded-xl

                border
                border-white/10

                bg-white/[0.07]

                p-3
            "
        >

            <div
                class="
                    grid
                    h-9
                    w-9
                    shrink-0
                    place-items-center

                    rounded-full

                    bg-white

                    text-[9px]
                    font-extrabold
                    text-[#74181c]

                    dark:bg-primary
                    dark:text-white
                "
            >
                LC
            </div>


            <div class="min-w-0 flex-1">

                <strong
                    class="
                        block
                        truncate

                        text-[10px]
                        font-semibold
                        text-white
                    "
                >
                    Logistics Center
                </strong>


                <span
                    class="
                        mt-0.5
                        block

                        text-[7px]
                        text-white/45
                    "
                >
                    Operations Staff
                </span>

            </div>


            <span
                class="
                    h-2
                    w-2
                    shrink-0

                    rounded-full

                    bg-[#55a879]

                    shadow-[0_0_0_3px_rgba(85,168,121,0.13)]
                "
                title="Online"
            ></span>

        </div>

    </div>


    {{-- =====================================================
        NAVIGATION
    ====================================================== --}}

    <nav
        class="
            flex
            flex-1
            flex-col

            px-3
            pb-5
        "
    >

        {{-- =================================================
            DASHBOARD
        ================================================== --}}

        <a
            href="{{ route('logistics.dashboard') }}"
            class="
                group
                relative

                flex
                min-h-[42px]
                items-center
                gap-3

                rounded-[10px]

                px-3

                text-[9px]
                font-semibold

                transition-all
                duration-200

                {{
                    request()->routeIs('logistics.dashboard')
                        ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                        : 'text-white/65 hover:bg-white/10 hover:text-white'
                }}
            "
        >

            @if(request()->routeIs('logistics.dashboard'))

                <span
                    class="
                        absolute
                        bottom-[10px]
                        left-0
                        top-[10px]

                        w-[3px]

                        rounded-r-full

                        bg-[#74181c]

                        dark:bg-[#d45c62]
                    "
                ></span>

            @endif


            <svg
                viewBox="0 0 24 24"
                class="
                    h-[17px]
                    w-[17px]
                    shrink-0

                    fill-none
                    stroke-current
                    stroke-[1.5]

                    {{
                        request()->routeIs('logistics.dashboard')
                            ? 'text-[#74181c] dark:text-[#d45c62]'
                            : 'text-white/55 group-hover:text-white'
                    }}
                "
            >
                <rect
                    x="3"
                    y="3"
                    width="7"
                    height="7"
                    rx="1"
                ></rect>

                <rect
                    x="14"
                    y="3"
                    width="7"
                    height="7"
                    rx="1"
                ></rect>

                <rect
                    x="3"
                    y="14"
                    width="7"
                    height="7"
                    rx="1"
                ></rect>

                <rect
                    x="14"
                    y="14"
                    width="7"
                    height="7"
                    rx="1"
                ></rect>
            </svg>


            <span>
                Dashboard
            </span>

        </a>


        {{-- =================================================
            OPERATIONS
        ================================================== --}}

        <div class="mt-6">

            <p
                class="
                    mb-2
                    px-3

                    text-[6px]
                    font-bold
                    uppercase
                    tracking-[0.2em]

                    text-white/30
                "
            >
                Operations
            </p>


            {{-- ALL PARCELS --}}

            <a
                href="{{ route('logistics.parcels.index') }}"
                class="
                    group
                    relative

                    mb-1
                    flex
                    min-h-[40px]
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-[9px]
                    font-semibold

                    transition-all

                    {{
                        request()->routeIs('logistics.parcels.index')
                        || request()->routeIs('logistics.parcels.show')
                            ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-white/60 hover:bg-white/10 hover:text-white'
                    }}
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4
                        shrink-0

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <path d="M21 8 12 3 3 8l9 5 9-5Z"></path>
                    <path d="M3 8v8l9 5 9-5V8"></path>
                    <path d="M12 13v8"></path>
                </svg>


                <span>
                    All Parcels
                </span>

            </a>


            {{-- RECEIVE PARCEL --}}

            <a
                href="{{ route('logistics.parcels.receive') }}"
                class="
                    group
                    relative

                    mb-1
                    flex
                    min-h-[40px]
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-[9px]
                    font-semibold

                    transition-all

                    {{
                        request()->routeIs('logistics.parcels.receive')
                            ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-white/60 hover:bg-white/10 hover:text-white'
                    }}
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>


                <span>
                    Receive Parcel
                </span>

            </a>


            {{-- SORTING --}}

            <a
                href="{{ route('logistics.sorting.index') }}"
                class="
                    group
                    relative

                    mb-1
                    flex
                    min-h-[40px]
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-[9px]
                    font-semibold

                    transition-all

                    {{
                        request()->routeIs('logistics.sorting.*')
                            ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-white/60 hover:bg-white/10 hover:text-white'
                    }}
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <path d="M8 3v18"></path>
                    <path d="m4 7 4-4 4 4"></path>

                    <path d="M16 21V3"></path>
                    <path d="m12 17 4 4 4-4"></path>
                </svg>


                <span>
                    Parcel Sorting
                </span>


                <span
                    class="
                        ml-auto

                        min-w-[22px]

                        rounded-full

                        bg-white/10

                        px-1.5
                        py-1

                        text-center
                        text-[6px]
                        font-bold
                        text-white/75

                        {{
                            request()->routeIs('logistics.sorting.*')
                                ? 'bg-[#f6e5e3] text-[#74181c] dark:bg-white/10 dark:text-white'
                                : ''
                        }}
                    "
                >
                    34
                </span>

            </a>


            {{-- RIDER ASSIGNMENT --}}

            <a
                href="{{ route('logistics.assignments.index') }}"
                class="
                    group
                    relative

                    mb-1
                    flex
                    min-h-[40px]
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-[9px]
                    font-semibold

                    transition-all

                    {{
                        request()->routeIs('logistics.assignments.*')
                            ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-white/60 hover:bg-white/10 hover:text-white'
                    }}
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <circle cx="8" cy="7" r="3"></circle>
                    <path d="M3 19c0-3 2-5 5-5"></path>
                    <path d="M13 13h8"></path>
                    <path d="m18 9 4 4-4 4"></path>
                </svg>


                <span>
                    Rider Assignment
                </span>


                <span
                    class="
                        ml-auto

                        min-w-[22px]

                        rounded-full

                        bg-white/10

                        px-1.5
                        py-1

                        text-center
                        text-[6px]
                        font-bold
                        text-white/75

                        {{
                            request()->routeIs('logistics.assignments.*')
                                ? 'bg-[#f6e5e3] text-[#74181c] dark:bg-white/10 dark:text-white'
                                : ''
                        }}
                    "
                >
                    18
                </span>

            </a>


            {{-- TRACKING --}}

            <a
                href="{{ route('logistics.parcels.tracking') }}"
                class="
                    group
                    relative

                    mb-1
                    flex
                    min-h-[40px]
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-[9px]
                    font-semibold

                    transition-all

                    {{
                        request()->routeIs('logistics.parcels.tracking')
                            ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-white/60 hover:bg-white/10 hover:text-white'
                    }}
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <circle cx="12" cy="12" r="8"></circle>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>


                <span>
                    Parcel Tracking
                </span>

            </a>

        </div>


        {{-- =================================================
            DELIVERY MANAGEMENT
        ================================================== --}}

        <div class="mt-6">

            <p
                class="
                    mb-2
                    px-3

                    text-[6px]
                    font-bold
                    uppercase
                    tracking-[0.2em]

                    text-white/30
                "
            >
                Delivery Management
            </p>


            {{-- RIDERS --}}

            <a
                href="{{ route('logistics.riders.index') }}"
                class="
                    group

                    mb-1
                    flex
                    min-h-[40px]
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-[9px]
                    font-semibold

                    transition

                    {{
                        request()->routeIs('logistics.riders.index')
                        || request()->routeIs('logistics.riders.show')
                            ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-white/60 hover:bg-white/10 hover:text-white'
                    }}
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <circle cx="9" cy="7" r="3"></circle>
                    <path d="M3 20c0-4 2.5-6 6-6s6 2 6 6"></path>
                    <path d="M17 11h4"></path>
                    <path d="M19 9v4"></path>
                </svg>


                <span>
                    Riders
                </span>

            </a>


            {{-- RIDER APPLICATIONS --}}

            <a
                href="{{ route('logistics.riders.applications') }}"
                class="
                    group

                    mb-1
                    flex
                    min-h-[40px]
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-[9px]
                    font-semibold

                    transition

                    {{
                        request()->routeIs('logistics.riders.applications')
                            ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-white/60 hover:bg-white/10 hover:text-white'
                    }}
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <path d="M6 3h9l4 4v14H6z"></path>
                    <path d="M14 3v5h5"></path>
                    <path d="m9 14 2 2 4-4"></path>
                </svg>


                <span>
                    Rider Applications
                </span>


                <span
                    class="
                        ml-auto

                        min-w-[22px]

                        rounded-full

                        bg-[#f4c6c8]

                        px-1.5
                        py-1

                        text-center
                        text-[6px]
                        font-bold
                        text-[#74181c]

                        dark:bg-[#a92b31]/25
                        dark:text-[#e9969a]
                    "
                >
                    5
                </span>

            </a>


            {{-- DELIVERY AREAS --}}

            <a
                href="{{ route('logistics.delivery-areas.index') }}"
                class="
                    group

                    mb-1
                    flex
                    min-h-[40px]
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-[9px]
                    font-semibold

                    transition

                    {{
                        request()->routeIs('logistics.delivery-areas.*')
                            ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-white/60 hover:bg-white/10 hover:text-white'
                    }}
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <path
                        d="
                            M12 21
                            s7-5 7-11
                            a7 7 0 1 0-14 0
                            c0 6 7 11 7 11Z
                        "
                    ></path>

                    <circle
                        cx="12"
                        cy="10"
                        r="2"
                    ></circle>
                </svg>


                <span>
                    Delivery Areas
                </span>

            </a>

        </div>


        {{-- =================================================
            COMMUNICATION
        ================================================== --}}

        <div class="mt-6">

            <p
                class="
                    mb-2
                    px-3

                    text-[6px]
                    font-bold
                    uppercase
                    tracking-[0.2em]

                    text-white/30
                "
            >
                Communication
            </p>


            <a
                href="{{ route('logistics.messages.index') }}"
                class="
                    group

                    flex
                    min-h-[40px]
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-[9px]
                    font-semibold

                    transition

                    {{
                        request()->routeIs('logistics.messages.*')
                            ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-white/60 hover:bg-white/10 hover:text-white'
                    }}
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <path
                        d="
                            M21 15
                            a4 4 0 0 1-4 4
                            H8
                            l-5 3
                            V7
                            a4 4 0 0 1 4-4
                            h10
                            a4 4 0 0 1 4 4
                            Z
                        "
                    ></path>
                </svg>


                <span>
                    Messages
                </span>


                <span
                    class="
                        ml-auto

                        h-1.5
                        w-1.5

                        rounded-full

                        bg-[#f3a3a7]
                    "
                ></span>

            </a>

        </div>


        {{-- =================================================
            ANALYTICS
        ================================================== --}}

        <div class="mt-6">

            <p
                class="
                    mb-2
                    px-3

                    text-[6px]
                    font-bold
                    uppercase
                    tracking-[0.2em]

                    text-white/30
                "
            >
                Analytics
            </p>


            <a
                href="{{ route('logistics.reports.index') }}"
                class="
                    group

                    flex
                    min-h-[40px]
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-[9px]
                    font-semibold

                    transition

                    {{
                        request()->routeIs('logistics.reports.*')
                            ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-white/60 hover:bg-white/10 hover:text-white'
                    }}
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <path d="M5 20V10"></path>
                    <path d="M12 20V4"></path>
                    <path d="M19 20v-7"></path>
                </svg>


                <span>
                    Reports
                </span>

            </a>

        </div>


        {{-- =================================================
            SPACER
        ================================================== --}}

        <div class="min-h-6 flex-1"></div>


        {{-- =================================================
            APPEARANCE
        ================================================== --}}

        <div
            class="
                border-t
                border-white/10

                pt-5
            "
        >

            <p
                class="
                    mb-2
                    px-3

                    text-[6px]
                    font-bold
                    uppercase
                    tracking-[0.2em]

                    text-white/30
                "
            >
                Appearance
            </p>


            {{-- THEME TOGGLE --}}

            <button
                type="button"
                id="logisticsThemeToggle"
                aria-label="Toggle light and dark theme"
                aria-pressed="false"
                class="
                    group

                    flex
                    min-h-[60px]
                    w-full
                    items-center
                    gap-4

                    rounded-2xl

                    border
                    border-white/10

                    bg-white/[0.06]

                    px-5

                    text-left

                    transition

                    hover:bg-white/10
                "
            >

                {{-- ICON --}}

                <span
                    class="
                        grid
                        h-11
                        w-11
                        shrink-0
                        place-items-center

                        rounded-xl

                        bg-surface

                        text-ink
                    "
                >

                    {{-- MOON --}}
                    <svg
                        id="themeMoonIcon"
                        viewBox="0 0 24 24"
                        class="
                            h-4
                            w-4

                            fill-none
                            stroke-current
                            stroke-[1.6]
                        "
                    >
                        <path
                            d="
                                M20 15.5
                                A8.5 8.5 0 0 1
                                8.5 4
                                A8.5 8.5 0 1 0
                                20 15.5Z
                            "
                        ></path>
                    </svg>


                    {{-- SUN --}}
                    <svg
                        id="themeSunIcon"
                        viewBox="0 0 24 24"
                        class="
                            hidden
                            h-4
                            w-4

                            fill-none
                            stroke-current
                            stroke-[1.6]
                        "
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="4"
                        ></circle>

                        <path d="M12 2v2"></path>
                        <path d="M12 20v2"></path>

                        <path d="M4.93 4.93l1.41 1.41"></path>
                        <path d="M17.66 17.66l1.41 1.41"></path>

                        <path d="M2 12h2"></path>
                        <path d="M20 12h2"></path>

                        <path d="M6.34 17.66l-1.41 1.41"></path>
                        <path d="M19.07 4.93l-1.41 1.41"></path>
                    </svg>

                </span>


                {{-- LABEL --}}

                <span class="min-w-0 flex-1">

                    <strong
                        id="themeToggleTitle"
                        class="
                            block

                            text-sm
                            font-semibold
                            text-ink
                        "
                    >
                        Dark Mode
                    </strong>


                    <small
                        id="themeToggleDescription"
                        class="
                            mt-0.5
                            block

                            text-xs
                            text-muted
                        "
                    >
                        Switch to dark theme
                    </small>

                </span>


                {{-- TOGGLE TRACK --}}

                <span
                    id="themeToggleTrack"
                    class="
                        rounded-full
                        bg-primary
                        px-3
                        py-1
                        text-xs
                        font-bold
                        text-white
                    "
                >
                    ON
                </span>

            </button>

        </div>


        {{-- =================================================
            PROFILE
        ================================================== --}}

        <div class="mt-4">

            <a
                href="{{ route('logistics.profile.index') }}"
                class="
                    group

                    flex
                    min-h-[40px]
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-[9px]
                    font-semibold

                    transition

                    {{
                        request()->routeIs('logistics.profile.*')
                            ? 'bg-white text-[#74181c] shadow-sm dark:bg-white/10 dark:text-white'
                            : 'text-white/60 hover:bg-white/10 hover:text-white'
                    }}
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <circle cx="12" cy="8" r="4"></circle>

                    <path
                        d="
                            M4 21
                            c0-5 3-8 8-8
                            s8 3 8 8
                        "
                    ></path>
                </svg>


                <span>
                    My Profile
                </span>

            </a>


            {{-- FRONTEND PLACEHOLDER LOGOUT --}}

            <button
                type="button"
                id="logisticsLogoutButton"
                class="
                    group

                    mt-1

                    flex
                    min-h-[40px]
                    w-full
                    items-center
                    gap-3

                    rounded-[10px]

                    px-3

                    text-left
                    text-[9px]
                    font-semibold
                    text-white/50

                    transition

                    hover:bg-[#a92b31]/25
                    hover:text-[#ffd7d8]
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-4
                        w-4

                        fill-none
                        stroke-current
                        stroke-[1.5]
                    "
                >
                    <path
                        d="
                            M9 21
                            H5
                            a2 2 0 0 1-2-2
                            V5
                            a2 2 0 0 1 2-2
                            h4
                        "
                    ></path>

                    <path d="m16 17 5-5-5-5"></path>

                    <path d="M21 12H9"></path>
                </svg>


                <span>
                    Logout
                </span>

            </button>

        </div>

    </nav>

</aside>