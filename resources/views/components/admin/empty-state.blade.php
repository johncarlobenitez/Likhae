@props([
    'title' => 'Nothing to review',
    'message' => 'Items matching this view will appear here.',
    'action' => null,
    'href' => null,
])

<div {{ $attributes->class('ad-empty') }}>
    <span class="ad-empty-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4zM8 9h8M8 13h5"/></svg></span>
    <h3>{{ $title }}</h3>
    <p>{{ $message }}</p>
    @if($action && $href)<a class="ad-btn ad-btn-primary" href="{{ $href }}">{{ $action }}</a>@endif
</div>
