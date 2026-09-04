@props([
    'label',
    'value',
    'change' => null,
    'direction' => 'up',
    'icon' => 'chart',
])

@php
    $icons = [
        'sales'    => '<path d="M3 7h18v13H3zM3 10h18M7 16h4"/>',
        'orders'   => '<path d="M6 3h12l2 4v14H4V7zM4 7h16M9 12h6"/>',
        'revenue'  => '<path d="M12 2v20M17 6H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
        'products' => '<path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5zM4 7.5l8 4.5 8-4.5M12 12v9"/>',
        'shipping' => '<path d="M3 6h11v11H3zM14 10h4l3 3v4h-7z"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/>',
        'chart'    => '<path d="M4 19V9M10 19V5M16 19v-7M22 19H2"/>',
    ];

    $iconPath = $icons[$icon] ?? $icons['chart'];
@endphp

<article class="sl-stat-card">
    <span class="sl-stat-icon">
        <svg viewBox="0 0 24 24" aria-hidden="true">{!! $iconPath !!}</svg>
    </span>
    <div class="sl-stat-body">
        <p class="sl-stat-label">{{ $label }}</p>
        <strong class="sl-stat-value">{{ $value }}</strong>
    </div>
    @if ($change)
        <span class="sl-stat-change {{ $direction === 'down' ? 'is-down' : '' }}">
            {{ $direction === 'down' ? '↓' : '↑' }} {{ $change }}
        </span>
    @endif
</article>