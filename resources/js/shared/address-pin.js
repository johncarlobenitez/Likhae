import { loadMapbox } from './mapbox';

function updateStatus(shell, message, isError = false) {
    const status = shell.querySelector('[data-address-pin-status]');
    if (!status) return;
    status.textContent = message;
    status.classList.toggle('is-error', isError);
}

function field(form, names) {
    for (const name of names) {
        const element = form?.querySelector(`[name="${name}"]`);
        if (element) return element;
    }
    return null;
}

function addressQuery(form, search) {
    const values = search.trim()
        ? [search.trim()]
        : [
            field(form, ['house_number'])?.value,
            field(form, ['street_address', 'street'])?.value,
            field(form, ['landmark'])?.value,
            field(form, ['barangay_name', 'barangay'])?.value,
            field(form, ['municipality_name', 'municipality'])?.value,
            field(form, ['province_name', 'province'])?.value,
            field(form, ['region_name', 'region'])?.value,
            field(form, ['postal_code'])?.value,
            'Philippines',
        ];

    return values.map((value) => String(value ?? '').trim()).filter(Boolean).join(', ');
}

function coordinatesFromFeature(feature) {
    const center = feature?.center;
    if (!Array.isArray(center) || center.length < 2) return null;
    const longitude = Number(center[0]);
    const latitude = Number(center[1]);
    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)
        || latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) return null;
    return { latitude, longitude };
}

function validCoordinates(latitudeValue, longitudeValue) {
    if (!String(latitudeValue ?? '').trim() || !String(longitudeValue ?? '').trim()) return null;
    const latitude = Number(latitudeValue);
    const longitude = Number(longitudeValue);
    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)
        || latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) return null;
    return { latitude, longitude };
}

function initialize(shell, mapboxgl) {
    const form = shell.closest('form');
    const canvas = shell.querySelector('[data-address-pin-canvas]');
    const token = shell.dataset.mapToken;
    const latitudeField = shell.querySelector('[data-address-latitude]');
    const longitudeField = shell.querySelector('[data-address-longitude]');
    const initialCoordinates = validCoordinates(latitudeField?.value, longitudeField?.value);
    const hasInitial = Boolean(initialCoordinates);
    let defaultCenter;
    try { defaultCenter = JSON.parse(shell.dataset.mapCenter || '[121,14.6]'); } catch { defaultCenter = [121, 14.6]; }
    const center = hasInitial ? [initialCoordinates.longitude, initialCoordinates.latitude] : defaultCenter;

    if (!form || !canvas || !token) {
        updateStatus(shell, 'Map pinning is unavailable until MAPBOX_PUBLIC_TOKEN is configured.', true);
        return;
    }

    mapboxgl.accessToken = token;
    const map = new mapboxgl.Map({
        container: canvas,
        style: shell.dataset.mapStyle || 'mapbox://styles/mapbox/streets-v12',
        center,
        zoom: hasInitial ? 15 : 10,
        attributionControl: true,
    });
    map.addControl(new mapboxgl.NavigationControl({ showCompass: true }), 'top-right');

    let marker = null;
    let pending = hasInitial ? { ...initialCoordinates } : null;
    let pendingReliable = hasInitial;
    let pendingManuallyAdjusted = false;
    let confirmed = hasInitial;
    const confirmButton = shell.querySelector('[data-address-confirm]');

    const setPending = (coordinates, message, { reliable = false, manual = false } = {}) => {
        if (!validCoordinates(coordinates?.latitude, coordinates?.longitude)) return;
        pending = coordinates;
        pendingReliable = reliable;
        pendingManuallyAdjusted = manual;
        confirmed = false;
        if (!marker) {
            marker = new mapboxgl.Marker({ draggable: true, color: '#800000' })
                .setLngLat([coordinates.longitude, coordinates.latitude])
                .addTo(map);
            marker.on('dragend', () => {
                const [longitude, latitude] = marker.getLngLat().toArray();
                setPending({ latitude, longitude }, 'Marker moved. Confirm the pin when it is exact.', { reliable: true, manual: true });
            });
        } else {
            marker.setLngLat([coordinates.longitude, coordinates.latitude]);
        }
        map.flyTo({ center: [coordinates.longitude, coordinates.latitude], zoom: Math.max(map.getZoom(), 15), essential: true });
        if (confirmButton) confirmButton.hidden = false;
        updateStatus(shell, message || 'Suggested location shown. Drag the marker to the exact position, then confirm it.');
    };

    confirmButton?.addEventListener('click', () => {
        if (!pending) return;
        if (!pendingReliable && !pendingManuallyAdjusted) {
            updateStatus(shell, 'This result is area-level. Move the pin to the exact building or entrance before confirming.', true);
            return;
        }
        latitudeField.value = pending.latitude.toFixed(7);
        longitudeField.value = pending.longitude.toFixed(7);
        confirmed = true;
        confirmButton.hidden = true;
        updateStatus(shell, `Confirmed pin: ${pending.latitude.toFixed(6)}, ${pending.longitude.toFixed(6)}.`);
    });

    map.on('click', (event) => {
        setPending({ latitude: event.lngLat.lat, longitude: event.lngLat.lng }, 'Map location selected. Confirm the pin to save it.', { reliable: true, manual: true });
    });

    const invalidatePin = () => {
        if (!confirmed && !pending) return;
        pending = null;
        pendingReliable = false;
        pendingManuallyAdjusted = false;
        confirmed = false;
        latitudeField.value = '';
        longitudeField.value = '';
        marker?.remove();
        marker = null;
        if (confirmButton) confirmButton.hidden = true;
        updateStatus(shell, 'Address changed. Search again or select the exact location on the map before saving.');
    };

    const addressInputs = ['region_name', 'region', 'province_name', 'province', 'municipality_name', 'municipality', 'barangay_name', 'barangay', 'house_number', 'street_address', 'street', 'landmark', 'postal_code']
        .map((name) => form.querySelector(`[name="${name}"]`))
        .filter(Boolean);
    let suggestionTimer;
    addressInputs.forEach((input) => {
        const handleAddressChange = (event) => {
            if (event.type === 'change' && event.isTrusted === false) return;
            invalidatePin();
            window.clearTimeout(suggestionTimer);
            if (!field(form, ['street_address', 'street'])?.value?.trim()) return;
            suggestionTimer = window.setTimeout(() => searchAddress(), 700);
        };
        input.addEventListener('input', handleAddressChange);
        input.addEventListener('change', handleAddressChange);
    });

    form.addEventListener('submit', (event) => {
        if (confirmed && validCoordinates(latitudeField.value, longitudeField.value)) return;
        event.preventDefault();
        updateStatus(shell, 'Confirm the exact address pin before saving.', true);
        confirmButton?.focus();
    });

    async function searchAddress() {
        const query = addressQuery(form, shell.querySelector('[data-address-search]')?.value || '');
        if (!query) return;
        updateStatus(shell, 'Searching Mapbox for a suggested location.');
        try {
            const response = await fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json?country=ph&language=en&limit=1&access_token=${encodeURIComponent(token)}`, { headers: { Accept: 'application/json' } });
            if (response.status === 429) throw new Error('rate-limit');
            if (!response.ok) throw new Error('geocoding-failed');
            const feature = (await response.json()).features?.[0];
            const coordinates = coordinatesFromFeature(feature);
            if (!coordinates) throw new Error('no-result');
            const reliable = (feature?.place_type || []).some((type) => ['address', 'poi', 'entrance'].includes(type));
            setPending(coordinates, reliable
                ? 'Suggested address location shown. Confirm it or move the pin to the exact entrance.'
                : 'Only an area-level result was found. Move the pin to the exact building or entrance before confirming.', { reliable });
        } catch (error) {
            updateStatus(shell, error.message === 'rate-limit'
                ? 'Mapbox is temporarily rate-limited. Select the exact location on the map manually.'
                : 'No exact map result was found. Select the exact location on the map or add more address detail.', true);
        }
    }

    shell.querySelector('[data-address-search-submit]')?.addEventListener('click', async () => {
        if (!addressQuery(form, shell.querySelector('[data-address-search]')?.value || '')) {
            updateStatus(shell, 'Enter an address or complete the address fields first.', true);
            return;
        }
        await searchAddress();
    });

    if (false) {
    shell.querySelector('[data-address-search-submit]')?.addEventListener('click', async () => {
        const query = addressQuery(form, shell.querySelector('[data-address-search]')?.value || '');
        if (!query) {
            updateStatus(shell, 'Enter an address or complete the address fields first.', true);
            return;
        }
        updateStatus(shell, 'Searching Mapbox for a suggested location…');
        try {
            const response = await fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json?country=ph&language=en&limit=1&access_token=${encodeURIComponent(token)}`, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('Geocoding request failed.');
            const feature = (await response.json()).features?.[0];
            const coordinates = coordinatesFromFeature(feature);
            if (!coordinates) throw new Error('No map result was found.');
            setPending(coordinates, 'Suggested location shown. Drag the marker to the exact position, then confirm it.');
        } catch {
            updateStatus(shell, 'No map result was found. Try adding a street, landmark, or postal code.', true);
        }
    });
    }

    shell.querySelector('[data-address-use-location]')?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            updateStatus(shell, 'Device location is not supported by this browser.', true);
            return;
        }
        updateStatus(shell, 'Waiting for location permission…');
        navigator.geolocation.getCurrentPosition(
            (position) => setPending({ latitude: position.coords.latitude, longitude: position.coords.longitude }, 'Device location shown. Confirm the pin to save it.', { reliable: true }),
            () => updateStatus(shell, 'Location permission was not granted or GPS is unavailable.', true),
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 },
        );
    });

    if (hasInitial) {
        map.on('load', () => {
            setPending(pending, 'Saved pin loaded. Drag it if the physical location needs correction.', { reliable: true });
            confirmed = true;
            if (confirmButton) confirmButton.hidden = true;
        });
    } else if (field(form, ['street_address', 'street'])?.value?.trim()) {
        window.setTimeout(() => searchAddress(), 700);
    }
}

function bootAddressPins() {
    const shells = [...document.querySelectorAll('[data-address-pin]')];
    if (!shells.length) return;
    loadMapbox()
        .then((mapboxgl) => shells.forEach((shell) => initialize(shell, mapboxgl)))
        .catch(() => shells.forEach((shell) => updateStatus(shell, 'Map service is unavailable. Confirm the exact pin before saving.', true)));
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bootAddressPins);
else bootAddressPins();
