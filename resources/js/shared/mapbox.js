const MAPBOX_SCRIPT = 'https://api.mapbox.com/mapbox-gl-js/v3.15.0/mapbox-gl.js';
const MAPBOX_CSS = 'https://api.mapbox.com/mapbox-gl-js/v3.15.0/mapbox-gl.css';

let mapboxPromise;

function loadMapbox() {
    if (window.mapboxgl) return Promise.resolve(window.mapboxgl);
    if (mapboxPromise) return mapboxPromise;

    if (!document.querySelector('link[data-likhae-mapbox-css]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = MAPBOX_CSS;
        link.dataset.likhaeMapboxCss = 'true';
        document.head.appendChild(link);
    }

    mapboxPromise = new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = MAPBOX_SCRIPT;
        script.async = true;
        script.onload = () => resolve(window.mapboxgl);
        script.onerror = () => reject(new Error('Mapbox could not be loaded.'));
        document.head.appendChild(script);
    });

    return mapboxPromise;
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>'"]/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;',
    }[character]));
}

function parseJson(value, fallback) {
    try { return JSON.parse(value); } catch { return fallback; }
}

function updateStatus(shell, message, isError = false) {
    const status = shell.querySelector('[data-map-status]');
    if (!status) return;
    status.textContent = message;
    status.classList.toggle('is-error', isError);
}

function toggleFullscreen(shell, map) {
    const fullscreen = shell.classList.toggle('is-fullscreen');
    const button = shell.querySelector('[data-map-fullscreen]');
    if (button) {
        button.textContent = fullscreen ? 'Close full map' : 'Open full map';
        button.setAttribute('aria-pressed', String(fullscreen));
    }
    document.body.classList.toggle('lk-map-lock-scroll', fullscreen);
    window.setTimeout(() => map.resize(), 80);
}

function addUserLocation(shell, map, mapboxgl) {
    const button = shell.querySelector('[data-map-location]');
    if (!button) return;

    button.addEventListener('click', () => {
        if (!navigator.geolocation) {
            updateStatus(shell, 'Location is not supported by this browser.', true);
            return;
        }

        button.disabled = true;
        updateStatus(shell, 'Waiting for your location permission…');
        navigator.geolocation.getCurrentPosition((position) => {
            const lng = position.coords.longitude;
            const lat = position.coords.latitude;
            shell._likhaeUserMarker?.remove();
            const marker = new mapboxgl.Marker({ element: Object.assign(document.createElement('div'), {
                className: 'lk-map-marker',
            }) }).setLngLat([lng, lat]).addTo(map);
            marker.getElement().dataset.kind = 'user';
            marker.getElement().dataset.mapUserMarker = 'true';
            shell._likhaeUserMarker = marker;
            map.flyTo({ center: [lng, lat], zoom: Math.max(map.getZoom(), 14), essential: true });
            updateStatus(shell, 'Your location is shown on this device only.');
            button.disabled = false;
        }, (error) => {
            const message = error.code === 1
                ? 'Location permission was not granted. You can enable it in browser settings.'
                : 'We could not read your location right now. Try again.';
            updateStatus(shell, message, true);
            button.disabled = false;
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 });
    });
}

function initMap(shell, mapboxgl) {
    const token = shell.dataset.mapToken;
    const canvas = shell.querySelector('[data-map-canvas]');
    if (!token || !canvas) {
        updateStatus(shell, 'Map is ready for setup. Add MAPBOX_PUBLIC_TOKEN to the server environment.', true);
        return;
    }

    const markers = parseJson(shell.dataset.mapMarkers || '[]', []);
    const center = parseJson(shell.dataset.mapCenter || '[121,14.6]', [121, 14.6]);
    const zoom = Number(shell.dataset.mapZoom || 10);
    mapboxgl.accessToken = token;

    const map = new mapboxgl.Map({
        container: canvas,
        style: shell.dataset.mapStyle || 'mapbox://styles/mapbox/streets-v12',
        center,
        zoom,
        attributionControl: true,
    });
    map.addControl(new mapboxgl.NavigationControl({ showCompass: true }), 'top-right');

    const bounds = new mapboxgl.LngLatBounds();
    markers.forEach((markerData) => {
        const lat = Number(markerData.latitude);
        const lng = Number(markerData.longitude);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

        const element = document.createElement('div');
        element.className = 'lk-map-marker';
        element.dataset.kind = markerData.kind || 'destination';
        const marker = new mapboxgl.Marker({ element }).setLngLat([lng, lat]).addTo(map);
        const popup = new mapboxgl.Popup({ offset: 16 }).setHTML(
            `<div class="lk-map-popup"><strong>${escapeHtml(markerData.title || markerData.label || 'Location')}</strong><span>${escapeHtml(markerData.popup || '')}</span></div>`,
        );
        marker.setPopup(popup);
        bounds.extend([lng, lat]);
    });

    if (!bounds.isEmpty()) {
        map.fitBounds(bounds, { padding: 54, maxZoom: 14, duration: 0 });
    }

    shell.querySelector('[data-map-fullscreen]')?.addEventListener('click', () => toggleFullscreen(shell, map));
    addUserLocation(shell, map, mapboxgl);
    map.on('load', () => updateStatus(shell, markers.length ? `${markers.length} location${markers.length === 1 ? '' : 's'} loaded from LIKHAE records.` : 'No saved coordinates yet. Use “Use my location” to center the map.'));
}

function bootMaps() {
    const shells = [...document.querySelectorAll('[data-likhae-map]')];
    if (!shells.length) return;

    loadMapbox()
        .then((mapboxgl) => shells.forEach((shell) => initMap(shell, mapboxgl)))
        .catch(() => shells.forEach((shell) => updateStatus(shell, 'Map service is unavailable right now. The page is still usable.', true)));

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        document.querySelectorAll('[data-likhae-map].is-fullscreen').forEach((shell) => {
            shell.querySelector('[data-map-fullscreen]')?.click();
        });
    });
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bootMaps);
else bootMaps();
