@props([
    'title' => 'Dashboard',
    'subtitle' => 'Platform operations and marketplace health at a glance.',
])

@php
    $admin = auth()->user();
    $adminName = data_get($admin, 'name', 'Admin User');
    $initials = collect(explode(' ', $adminName))->filter()->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') ?: 'A';
@endphp

<header class="ad-header">
    <div class="ad-header-title">
        <button class="ad-icon-btn ad-mobile-menu" type="button" data-admin-mobile-menu aria-label="Open navigation" aria-controls="admin-sidebar">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div>
            <h1>{{ $title }}</h1>
            <p>{{ $subtitle }}</p>
        </div>
    </div>

    <form class="ad-search" action="{{ route('admin.users') }}" method="GET" role="search">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input name="q" type="search" value="{{ request('q') }}" placeholder="Search users, orders, products..." aria-label="Search Admin Center">
        <kbd>⌘ K</kbd>
    </form>

    <div class="ad-header-actions">
        <a class="ad-icon-btn" href="{{ route('admin.messages') }}" aria-label="Messages">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v12H8l-4 3z"/><path d="M8 9h8M8 13h5"/></svg>
            <span class="ad-badge">4</span>
        </a>
        <a class="ad-icon-btn" href="{{ route('admin.notifications') }}" aria-label="Notifications">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
            <span class="ad-badge">8</span>
        </a>
        <a class="ad-profile" href="{{ route('admin.account') }}">
            <span class="ad-avatar">{{ $initials }}</span>
            <span><strong>{{ $adminName }}</strong><small>Platform Administrator</small></span>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 10 3 3 3-3"/></svg>
        </a>
    </div>
</header>
