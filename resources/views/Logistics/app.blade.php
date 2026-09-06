<!DOCTYPE html>
<html
    lang="en"
    class="bg-[#f7f3ec]"
>

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield(
            'title',
            'LIKHAE Logistics'
        )
    </title>

    {{-- =====================================================
        THEME INITIALIZER
        Default = LIGHT
    ====================================================== --}}

    <script>
        (() => {

            const savedTheme =
                localStorage.getItem(
                    'likhae-theme'
                );

            if (savedTheme === 'dark') {

                document.documentElement
                    .classList
                    .add('dark');

            } else {

                document.documentElement
                    .classList
                    .remove('dark');

            }

        })();
    </script>

    @vite([
        'resources/css/logistic/app.css',
        'resources/js/logistic/app.js'
    ])

    @stack('styles')

</head>

<body
    class="
        logistics-shell
        min-h-screen
        overflow-x-hidden

        bg-page
        text-ink

        antialiased

        transition-colors
        duration-300
    "
>

    <div class="min-h-screen">

        {{-- =================================================
            SIDEBAR
        ================================================== --}}

        @include('logistics.layouts.sidebar')


        {{-- =================================================
            MOBILE OVERLAY
        ================================================== --}}

        <div
            id="sidebarOverlay"
            class="
                invisible
                fixed
                inset-0
                z-40

                bg-black/45

                opacity-0

                backdrop-blur-[2px]

                transition-all
                duration-300

                lg:hidden
            "
        ></div>


        {{-- =================================================
            MAIN CONTENT
        ================================================== --}}

        <main
            class="
                min-h-screen

                bg-page

                transition-all
                duration-300

                lg:ml-[245px]
            "
        >

            {{-- =================================================
                MOBILE HEADER
            ================================================== --}}

            <header
                class="
                    sticky
                    top-0
                    z-30

                    flex
                    h-16
                    items-center
                    justify-between

                    border-b
                    border-line

                    bg-page/95

                    px-4

                    backdrop-blur-xl

                    lg:hidden
                "
            >

                {{-- OPEN SIDEBAR --}}

                <button
                    type="button"
                    id="mobileSidebarToggle"
                    aria-label="Open navigation"
                    class="
                        grid
                        h-9
                        w-9
                        place-items-center

                        rounded-lg

                        text-ink

                        transition

                        hover:bg-surface-hover
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="
                            h-5
                            w-5

                            fill-none
                            stroke-current
                            stroke-[1.7]
                        "
                    >
                        <path d="M4 7h16"></path>
                        <path d="M4 12h16"></path>
                        <path d="M4 17h16"></path>
                    </svg>

                </button>


                {{-- MOBILE BRAND --}}

                <a
                    href="{{ route('logistics.dashboard') }}"
                    class="
                        flex
                        items-center
                        gap-2
                    "
                >

                    <span
                        class="
                            grid
                            h-8
                            w-8
                            place-items-center

                            rounded-lg

                            bg-primary

                            text-[12px]
                            font-black
                            text-white
                        "
                    >
                        L
                    </span>


                    <span
                        class="
                            text-[13px]
                            font-extrabold
                            tracking-[-0.04em]
                            text-ink
                        "
                    >
                        LIKHAE
                    </span>

                </a>


                {{-- MOBILE PROFILE --}}

                <a
                    href="{{ route('logistics.profile') }}"
                    aria-label="Open profile"
                    class="
                        grid
                        h-9
                        w-9
                        place-items-center

                        rounded-full

                        bg-primary-soft

                        text-[8px]
                        font-bold
                        text-primary

                        transition

                        hover:bg-primary
                        hover:text-white
                    "
                >
                    LC
                </a>

            </header>


            {{-- =================================================
                PAGE CONTENT
            ================================================== --}}

            <div
                class="
                    mx-auto
                    w-full
                    max-w-[1500px]

                    p-5

                    sm:p-6
                    lg:p-8
                    xl:p-9
                "
            >

                @yield('content')

            </div>

        </main>

    </div>


    {{-- =====================================================
        PAGE-SPECIFIC SCRIPTS
    ====================================================== --}}

    @stack('scripts')

</body>

</html>
