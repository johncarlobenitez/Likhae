@props([
    'label',
    'value',
    'meta' => null,
    'tone' => 'neutral'
])

<div {{ $attributes->merge(['class' => 'kpi-card']) }}>
    <p class="kpi-label">{{ $label }}</p>
    <div class="mt-3 flex items-end justify-between gap-4">
        <strong class="text-2xl font-black tracking-[-.04em] text-[#171717]">{{ $value }}</strong>
        @if($meta)
            <span class="kpi-meta {{ $tone === 'success' ? 'text-[#079B72]' : ($tone === 'warning' ? 'text-[#D99A22]' : 'text-[#6B6864]') }}">
                {{ $meta }}
            </span>
        @endif
    </div>
</div>
