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

function markerCoordinates(marker) {
    const lat = Number(marker?.latitude);
    const lng = Number(marker?.longitude);
    return Number.isFinite(lat) && Number.isFinite(lng) ? [lng, lat] : null;
}

function drawRouteGeometry(map, id, geometry, color = '#800000', dash = false) {
    const sourceId = `likhae-route-source-${id}`;
    const layerId = `likhae-route-layer-${id}`;
    const data = {
        type: 'Feature',
        properties: {},
        geometry,
    };

    if (map.getSource(sourceId)) {
        map.getSource(sourceId).setData(data);
        return;
    }

    map.addSource(sourceId, { type: 'geojson', data });
    map.addLayer({
        id: layerId,
        type: 'line',
        source: sourceId,
        layout: { 'line-cap': 'round', 'line-join': 'round' },
        paint: {
            'line-color': color,
            'line-width': 3,
            'line-opacity': 0.82,
            ...(dash ? { 'line-dasharray': [1.5, 1.5] } : {}),
        },
    });
}

async function fetchRoadGeometry(token, from, to) {
    const coordinates = `${from[0]},${from[1]};${to[0]},${to[1]}`;
    const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${coordinates}?overview=full&geometries=geojson&access_token=${encodeURIComponent(token)}`;
    const response = await fetch(url, { headers: { Accept: 'application/json' } });
    if (!response.ok) throw new Error(`Directions request failed (${response.status}).`);
    const payload = await response.json();
    const geometry = payload.routes?.[0]?.geometry;
    if (!geometry?.coordinates?.length) throw new Error('No driving route was returned.');
    return geometry;
}

async function drawPersistedRoutes(map, markers, shell, token) {
    const grouped = markers.reduce((groups, marker) => {
        if (marker.shipment_id) {
            groups[marker.shipment_id] ||= [];
            groups[marker.shipment_id].push(marker);
        }
        return groups;
    }, {});
    shell._likhaeRouteState ||= {};
    const activeRouteIds = new Set();

    Object.entries(grouped).forEach(([shipmentId, shipmentMarkers]) => {
        const riders = shipmentMarkers.filter((marker) => marker.kind === 'rider' && marker.active_rider);
        const targets = shipmentMarkers.filter((marker) => ['seller', 'center', 'destination'].includes(marker.kind));

        riders.forEach((rider) => {
            const from = markerCoordinates(rider);
            if (!from) return;

            const destinationKind = rider.active_destination_kind;
            const target = targets.find((candidate) => candidate.target_kind === destinationKind);
            const to = markerCoordinates(target);
            if (!to) return;

            const routeId = `${shipmentId}-${rider.assignment_id || 'rider'}-${destinationKind}`;
            activeRouteIds.add(routeId);
            const previous = shell._likhaeRouteState?.[routeId];
            if (previous && distanceMeters(previous.from, from) < 75
                && previous.to[0] === to[0] && previous.to[1] === to[1]) return;
            const routeState = { from, to };
            shell._likhaeRouteState[routeId] = routeState;

            fetchRoadGeometry(token, from, to)
                .then((geometry) => {
                    if (shell._likhaeRouteState?.[routeId] !== routeState) return;
                    drawRouteGeometry(map, routeId, geometry, '#800000');
                })
                .catch(() => {
                    if (shell._likhaeRouteState?.[routeId] !== routeState) return;
                    delete shell._likhaeRouteState[routeId];
                    updateStatus(shell, 'Live rider location is available, but the road route is temporarily unavailable.', true);
                });
        });
    });

    Object.keys(shell._likhaeRouteState)
        .filter((routeId) => !activeRouteIds.has(routeId))
        .forEach((routeId) => {
            const layerId = `likhae-route-layer-${routeId}`;
            const sourceId = `likhae-route-source-${routeId}`;
            if (map.getLayer(layerId)) map.removeLayer(layerId);
            if (map.getSource(sourceId)) map.removeSource(sourceId);
            delete shell._likhaeRouteState[routeId];
        });
}

function drawUserRoute() {
    // Browser location is only a local centering aid. It is never used for
    // parcel navigation or as the buyer's delivery destination.
    return false;
}

function addUserLocation(shell, map, mapboxgl, markers) {
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
            updateStatus(shell, drawUserRoute(shell, map, markers, [lng, lat])
                ? 'Your location and route to the parcel address are shown on this device only.'
                : 'Your location is shown on this device only.');
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

function distanceMeters(first, second) {
    if (!first || !second) return Number.POSITIVE_INFINITY;
    const toRadians = (value) => value * Math.PI / 180;
    const earthRadius = 6371000;
    const dLat = toRadians(second[1] - first[1]);
    const dLng = toRadians(second[0] - first[0]);
    const a = Math.sin(dLat / 2) ** 2
        + Math.cos(toRadians(first[1])) * Math.cos(toRadians(second[1])) * Math.sin(dLng / 2) ** 2;
    return earthRadius * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function renderMarkers(shell, map, mapboxgl, markers, fit = false) {
    shell._likhaeMarkerRefs ||= {};
    const seen = new Set();
    const bounds = new mapboxgl.LngLatBounds();

    markers.forEach((markerData) => {
        const coordinates = markerCoordinates(markerData);
        if (!coordinates) return;

        const key = String(markerData.id || `${markerData.kind}-${markerData.latitude}-${markerData.longitude}`);
        seen.add(key);
        let marker = shell._likhaeMarkerRefs[key];
        if (!marker) {
            const element = document.createElement('div');
            element.className = 'lk-map-marker';
            marker = new mapboxgl.Marker({ element }).setLngLat(coordinates).addTo(map);
            shell._likhaeMarkerRefs[key] = marker;
        } else {
            marker.setLngLat(coordinates);
        }

        marker.getElement().dataset.kind = markerData.kind || 'destination';
        marker.getElement().dataset.activeRider = markerData.active_rider ? 'true' : 'false';
        marker.setPopup(new mapboxgl.Popup({ offset: 16 }).setHTML(
            `<div class="lk-map-popup"><strong>${escapeHtml(markerData.title || markerData.label || 'Location')}</strong><span>${escapeHtml(markerData.popup || '')}</span></div>`,
        ));
        bounds.extend(coordinates);
    });

    Object.entries(shell._likhaeMarkerRefs).forEach(([key, marker]) => {
        if (!seen.has(key)) {
            marker.remove();
            delete shell._likhaeMarkerRefs[key];
        }
    });

    if (fit && !bounds.isEmpty()) {
        map.fitBounds(bounds, { padding: 54, maxZoom: 14, duration: 0 });
    }
}

async function refreshTracking(shell, map, mapboxgl, payload) {
    const markers = Array.isArray(payload?.markers) ? payload.markers : [];
    const endpoints = markers.map((marker) => marker.tracking_endpoint).filter(Boolean);
    const channels = markers.map((marker) => marker.tracking_channel).filter(Boolean);
    if (endpoints.length) shell._likhaeTrackingEndpoints = [...new Set(endpoints)];
    if (channels.length) shell._likhaeTrackingChannels = [...new Set(channels)];
    shell._likhaeCurrentMarkers = markers;
    renderMarkers(shell, map, mapboxgl, markers);
    startRiderLocationUpdates(shell);
    await drawPersistedRoutes(map, markers, shell, shell.dataset.mapToken);

    const tracking = payload?.tracking || {};
    const fitKey = `${tracking.status || ''}:${tracking.active_assignment_id || ''}:${tracking.active_destination_kind || ''}`;
    if (tracking.live && shell._likhaeFitKey !== fitKey) {
        const rider = markers.find((marker) => marker.kind === 'rider' && marker.active_rider);
        const target = markers.find((marker) => marker.target_kind === tracking.active_destination_kind);
        const riderCoordinates = markerCoordinates(rider);
        const targetCoordinates = markerCoordinates(target);
        if (riderCoordinates && targetCoordinates) {
            const bounds = new mapboxgl.LngLatBounds();
            bounds.extend(riderCoordinates);
            bounds.extend(targetCoordinates);
            map.fitBounds(bounds, { padding: 64, maxZoom: 14, duration: 450 });
        }
        shell._likhaeFitKey = fitKey;
    }
    if (tracking.live) {
        updateStatus(shell, `Rider live location updated · ${tracking.status || 'Active shipment'}.`);
    } else if (['READY_FOR_PICKUP', 'PICKED_UP', 'ASSIGNED_TO_RIDER', 'OUT_FOR_DELIVERY'].includes(tracking.status)) {
        updateStatus(shell, `Waiting for the active rider location · ${tracking.status.replaceAll('_', ' ')}.`);
    } else if (markers.length) {
        updateStatus(shell, `${markers.length} saved location${markers.length === 1 ? '' : 's'} shown. Live movement starts when the active delivery phase begins.`);
    } else {
        updateStatus(shell, 'No saved coordinates yet.');
    }
}

function startRiderLocationUpdates(shell) {
    const ownMarker = shell._likhaeCurrentMarkers?.find((marker) => marker.active_rider && marker.location_update_endpoint);
    const endpoint = ownMarker?.location_update_endpoint || null;
    if (shell._likhaeLocationWatchEndpoint === endpoint) return;

    if (shell._likhaeLocationWatchId !== undefined && navigator.geolocation) {
        navigator.geolocation.clearWatch(shell._likhaeLocationWatchId);
        shell._likhaeLocationWatchId = undefined;
    }
    shell._likhaeLocationWatchEndpoint = endpoint;
    if (!endpoint || !navigator.geolocation) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const send = (position) => fetch(endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
        }),
    }).catch(() => null);

    shell._likhaeLocationWatchId = navigator.geolocation.watchPosition(send, () => {
        // The rider can continue using the page if location permission is denied.
    }, { enableHighAccuracy: true, maximumAge: 10000, timeout: 15000 });
}

async function pollTracking(shell, map, mapboxgl) {
    const endpoints = shell._likhaeTrackingEndpoints || [];
    if (!endpoints.length) return;

    try {
        const payloads = await Promise.all(endpoints.map(async (endpoint) => {
            const response = await fetch(endpoint, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            return response.ok ? response.json() : null;
        }));
        const valid = payloads.filter(Boolean);
        if (!valid.length) return;
        const mergedMarkers = valid.flatMap((payload) => payload.markers || []);
        await refreshTracking(shell, map, mapboxgl, {
            markers: [...new Map(mergedMarkers.map((marker) => [marker.id, marker])).values()],
            tracking: valid[0].tracking || {},
        });
    } catch {
        // The initial map remains usable when polling is temporarily offline.
    }
}

function connectLiveTracking(shell, map, mapboxgl) {
    if (shell.dataset.mapLive !== 'true' || !shell._likhaeTrackingChannels?.length) return;

    const subscribe = () => {
        if (!window.Echo || shell._likhaeEchoChannels) return;

        try {
            shell._likhaeEchoChannels = shell._likhaeTrackingChannels.map((channel) => window.Echo.private(channel)
                .listen('.rider.location.updated', () => pollTracking(shell, map, mapboxgl))
                .listen('.shipment.tracking.updated', () => pollTracking(shell, map, mapboxgl)));
        } catch {
            shell._likhaeEchoChannels = null;
        }
    };

    subscribe();
    window.setTimeout(subscribe, 1200);
    shell._likhaeTrackingTimer = window.setInterval(() => pollTracking(shell, map, mapboxgl), 5000);
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

    shell._likhaeCurrentMarkers = markers;
    shell._likhaeTrackingEndpoints = [...new Set(markers.map((marker) => marker.tracking_endpoint).filter(Boolean))];
    shell._likhaeTrackingChannels = [...new Set(markers.map((marker) => marker.tracking_channel).filter(Boolean))];
    renderMarkers(shell, map, mapboxgl, markers, true);

    shell.querySelector('[data-map-fullscreen]')?.addEventListener('click', () => toggleFullscreen(shell, map));
    addUserLocation(shell, map, mapboxgl, markers);
    map.on('load', () => {
        drawPersistedRoutes(map, markers, shell, token);
        startRiderLocationUpdates(shell);
        updateStatus(shell, markers.length ? `${markers.length} location${markers.length === 1 ? '' : 's'} loaded.` : 'No saved coordinates yet.');
        connectLiveTracking(shell, map, mapboxgl);
    });
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
