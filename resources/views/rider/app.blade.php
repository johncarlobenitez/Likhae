<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <title>@yield('title', 'LIKHAE Rider Panel')</title>

    @vite([
        'resources/css/logistic/app.css'
    ])
</head>

<body class="rider-shell min-h-screen bg-page text-[14px] text-ink antialiased">
    <div class="flex min-h-screen bg-page">

        {{-- SIDEBAR --}}
        <aside
            id="riderSidebar"
            class="
                fixed
                inset-y-0
                left-0
                z-50
                -translate-x-full
                flex
                w-[270px]
                flex-col
                overflow-y-auto
                border-r
                border-line
                bg-sidebar
                text-sidebar-text
                shadow-[0_0_0_1px_rgba(255,255,255,0.02)]
                transition-transform
                duration-300
                ease-out
                lg:translate-x-0
            "
        >

            {{-- BRAND --}}
            <div class="flex h-[110px] items-center gap-4 border-b border-white/10 px-7">
                <div class="grid h-11 w-11 place-items-center rounded-2xl bg-[#c92d2f] text-lg font-bold text-white shadow-sm">
                    L
                </div>

                <div>
                    <h1 class="text-[24px] font-bold tracking-[-0.06em] text-sidebar-text">LIKHAE</h1>
                    <p class="text-[10px] uppercase tracking-[0.18em] text-sidebar-muted">Rider Panel</p>
                </div>
            </div>


            {{-- NAVIGATION --}}

{{-- NAVIGATION --}}
<nav
class="
flex-1
space-y-1.5
overflow-y-auto
px-3
py-6
"
>



<a
href="{{ route('rider.dashboard') }}"
class="
flex
items-center
gap-4
rounded-xl
px-4
py-3.5
text-[15px]
font-medium
text-sidebar-muted
transition
hover:bg-white/5
{{ request()->routeIs('rider.dashboard') ? 'bg-white/8 text-white ring-1 ring-white/10' : 'text-sidebar-muted hover:text-white' }}
"
>


<svg viewBox="0 0 24 24" class="h-[18px] w-[18px] shrink-0 fill-none stroke-current stroke-[1.7]">
    <path d="M3 12L12 3l9 9"></path>
    <path d="M9 21V12h6v9"></path>
    <path d="M5 10v11h14V10"></path>
</svg>


Dashboard


</a>







<a
href="{{ route('rider.pickups') }}"
class="
flex
items-center
gap-4
rounded-xl
px-4
py-3.5
text-[15px]
font-medium
transition
hover:bg-white/5
{{ request()->routeIs('rider.pickups.*') ? 'bg-white/8 text-white ring-1 ring-white/10' : 'text-sidebar-muted hover:text-white' }}
"
>


<svg viewBox="0 0 24 24" class="h-[18px] w-[18px] shrink-0 fill-none stroke-current stroke-[1.7]">
    <path d="M21 8 12 3 3 8l9 5 9-5Z"></path>
    <path d="M3 8v8l9 5 9-5V8"></path>
    <path d="M12 13v8"></path>
</svg>


Pickup Assignments


</a>








<a
href="{{ route('rider.deliveries') }}"
class="
flex
items-center
gap-4
rounded-xl
px-4
py-3.5
text-[15px]
font-medium
transition
hover:bg-white/5
{{ request()->routeIs('rider.deliveries.*') ? 'bg-white/8 text-white ring-1 ring-white/10' : 'text-sidebar-muted hover:text-white' }}
"
>


<svg viewBox="0 0 24 24" class="h-[18px] w-[18px] shrink-0 fill-none stroke-current stroke-[1.7]">
    <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"></path>
    <rect x="9" y="11" width="14" height="10" rx="2"></rect>
    <circle cx="12" cy="21" r="1"></circle>
    <circle cx="20" cy="21" r="1"></circle>
</svg>


Delivery Assignments


</a>







<a
href="{{ route('rider.history') }}"
class="
flex
items-center
gap-4
rounded-xl
px-4
py-3.5
text-[15px]
font-medium
transition
hover:bg-white/5
{{ request()->routeIs('rider.history') ? 'bg-white/8 text-white ring-1 ring-white/10' : 'text-sidebar-muted hover:text-white' }}
"
>


<svg viewBox="0 0 24 24" class="h-[18px] w-[18px] shrink-0 fill-none stroke-current stroke-[1.7]">
    <circle cx="12" cy="12" r="9"></circle>
    <path d="M12 7v5l3 3"></path>
</svg>


Delivery History


</a>







<a
href="{{ route('rider.earnings') }}"
class="
flex
items-center
gap-4
rounded-xl
px-4
py-3.5
text-[15px]
font-medium
transition
hover:bg-white/5
{{ request()->routeIs('rider.earnings') ? 'bg-white/8 text-white ring-1 ring-white/10' : 'text-sidebar-muted hover:text-white' }}
"
>


<svg viewBox="0 0 24 24" class="h-[18px] w-[18px] shrink-0 fill-none stroke-current stroke-[1.7]">
    <path d="M6 3h8a4 4 0 0 1 0 8H6z"></path>
    <path d="M6 11h8a4 4 0 0 1 0 8H6z"></path>
    <path d="M4 7h4"></path>
    <path d="M4 15h4"></path>
    <path d="M6 3v18"></path>
</svg>


Earnings


</a>







<a
href="{{ route('rider.account') }}"
class="
flex
items-center
gap-4
rounded-xl
px-4
py-3.5
text-[15px]
font-medium
transition
hover:bg-white/5
{{ request()->routeIs('rider.profile*') ? 'bg-white/8 text-white ring-1 ring-white/10' : 'text-sidebar-muted hover:text-white' }}
"
>


<svg viewBox="0 0 24 24" class="h-[18px] w-[18px] shrink-0 fill-none stroke-current stroke-[1.7]">
    <circle cx="12" cy="8" r="4"></circle>
    <path d="M4 21c0-5 3-8 8-8s8 3 8 8"></path>
</svg>


Profile


</a>



</nav>









{{-- THEME SWITCH --}}

<div
class="
border-t
border-line
p-5
"
>


<button
id="themeToggle"
class="
flex
w-full
items-center
justify-between
rounded-2xl
border
border-white/10
bg-white/5
px-5
py-4
transition
hover:border-white/20
"
>


<div class="flex items-center gap-4">


<div
class="
grid
h-11
w-11
place-items-center
rounded-xl
bg-surface
shadow-sm
"
>
    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px] fill-none stroke-current stroke-[1.6] text-ink">
        <path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"></path>
    </svg>
</div>



<div class="text-left">


<p
class="
text-sm
font-semibold
text-sidebar-text
"
>
Appearance
</p>



<p
class="
text-xs
text-sidebar-muted
"
>
Light / Dark Mode
</p>


</div>


</div>





<div
id="themeToggleBadge"
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
</div>



</button>



</div>









{{-- USER PROFILE --}}

<div
class="
border-t
border-line
p-6
"
>


<div
class="
flex
items-center
gap-4
"
>


<div
class="
grid
h-12
w-12
place-items-center
rounded-full
bg-primary-soft
text-primary
text-sm
font-bold
"
>

JD

</div>





<div>


<h3
class="
text-sm
font-bold
text-sidebar-text
"
>
Juan Dela Cruz
</h3>



<p
class="
text-xs
text-sidebar-muted
"
>
Verified Rider
</p>


</div>



</div>

<form method="POST" action="{{ route('logout') }}" class="mt-4">
    @csrf
    <button
        type="submit"
        class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 px-4 py-3 text-xs font-semibold text-sidebar-muted transition hover:bg-white/5 hover:text-white"
    >
        <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.7]">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <path d="m16 17 5-5-5-5"></path>
            <path d="M21 12H9"></path>
        </svg>
        Logout
    </button>
</form>

</div>

</aside>








{{-- MAIN CONTENT --}}

<main
class="
ml-[270px]
flex-1
bg-page
"
>





<header
class="
hidden
lg:flex
flex
h-[110px]
items-center
justify-between
border-b
border-line
bg-page
px-10
shadow-[inset_0_1px_0_rgba(255,255,255,0.02)]
"
>


<div>


<h2
class="
text-[22px]
font-bold
tracking-[-0.04em]
text-ink
"
>
LIKHAE Rider
</h2>


<p
class="
mt-1
text-[15px]
text-muted
"
>
Operations workspace
</p>


</div>





<button
id="mobileTheme"
class="
hidden
rounded-xl
bg-page-secondary
px-4
py-2
text-sm
font-semibold
"
>
Theme
</button>



</header>







<div
class="
px-8
py-6
"
>


@yield('content')


</div>





</main>



</div>








<script>
    const root = document.documentElement;
    const badge = document.getElementById('themeToggleBadge');
    const sidebar = document.getElementById('riderSidebar');
    const sidebarToggle = document.getElementById('riderMobileSidebarToggle');
    const sidebarOverlay = document.getElementById('riderSidebarOverlay');

    function openSidebar() {
        sidebar?.classList.remove('-translate-x-full');
        sidebar?.classList.add('translate-x-0');
        sidebarOverlay?.classList.remove('invisible', 'opacity-0');
        sidebarOverlay?.classList.add('visible', 'opacity-100');
        document.body.classList.add('overflow-hidden');
    }

    function closeSidebar() {
        sidebar?.classList.remove('translate-x-0');
        sidebar?.classList.add('-translate-x-full');
        sidebarOverlay?.classList.remove('visible', 'opacity-100');
        sidebarOverlay?.classList.add('invisible', 'opacity-0');
        document.body.classList.remove('overflow-hidden');
    }

    sidebarToggle?.addEventListener('click', openSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebar);
    sidebar?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeSidebar);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });

    const saved = localStorage.getItem('likhae-theme') ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    if (saved === 'dark') root.classList.add('dark');

    function syncTheme() {
        const dark = root.classList.contains('dark');
        badge.textContent = dark ? 'ON' : 'OFF';
        badge.className = dark
            ? 'rounded-full bg-primary px-3 py-1 text-xs font-bold text-white'
            : 'rounded-full bg-page-secondary px-3 py-1 text-xs font-bold text-ink';
    }

    document.getElementById('themeToggle')?.addEventListener('click', () => {
        const dark = root.classList.toggle('dark');
        localStorage.setItem('likhae-theme', dark ? 'dark' : 'light');
        syncTheme();
    });

    syncTheme();
</script>

@stack('scripts')



</body>


</html>
