@props(['title', 'description', 'action' => null, 'href' => '#'])

<div class="empty-state">
    <div class="mx-auto grid h-12 w-12 place-items-center border border-[#E5E0D9] bg-[#F6F4F0] text-[#6B6864]">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
            <path d="M4 7h16v12H4z"/>
            <path d="M8 7V5h8v2"/>
            <path d="M8 12h8"/>
        </svg>
    </div>
    <h3 class="mt-4 text-base font-bold">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#6B6864]">{{ $description }}</p>
    @if($action)
        <a class="btn-primary mt-5" href="{{ $href }}">{{ $action }}</a>
    @endif
</div>
