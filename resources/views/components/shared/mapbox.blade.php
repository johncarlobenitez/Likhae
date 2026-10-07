@props([
    'markers' => [],
    'center' => null,
    'zoom' => null,
    'title' => 'Location map',
    'height' => '220px',
    'allowUserLocation' => true,
    'userLocationTarget' => null,
    'class' => '',
])

<section
    class="lk-map-shell {{ $class }}"
    data-likhae-map
    data-map-token="{{ config('mapbox.public_token') }}"
    data-map-style="{{ config('mapbox.style') }}"
    data-map-markers="{{ json_encode(array_values($markers), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
    data-map-center="{{ json_encode($center ?: config('mapbox.default_center')) }}"
    data-map-zoom="{{ $zoom ?: config('mapbox.default_zoom') }}"
    data-map-user-target="{{ $userLocationTarget }}"
    data-map-live="{{ auth()->check() ? 'true' : 'false' }}"
    style="--lk-map-height: {{ $height }}"
>
    <div class="lk-map-toolbar">
        <div>
            <span class="lk-map-kicker">LIVE LOCATION</span>
            <h2>{{ $title }}</h2>
        </div>
        <div class="lk-map-actions">
            @if($allowUserLocation)
                <button type="button" class="lk-map-button lk-map-location" data-map-location>Use my location</button>
            @endif
            <button type="button" class="lk-map-button lk-map-fullscreen" data-map-fullscreen aria-pressed="false">Open full map</button>
        </div>
    </div>
    <div class="lk-map-canvas" data-map-canvas role="application" aria-label="{{ $title }}"></div>
    <p class="lk-map-status" data-map-status aria-live="polite">Shipment and rider coordinates are shown when available.</p>
</section>
