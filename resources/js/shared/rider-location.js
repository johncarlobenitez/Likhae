const LOCATION_UPDATE_INTERVAL_MS = 10000;
const LOCATION_MOVEMENT_THRESHOLD_METERS = 25;
const MAX_ACCURACY_METERS = 250;
const MAX_TIMESTAMP_SKEW_MS = 120000;

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

function parseAssignments(shell) {
    try {
        const assignments = JSON.parse(shell.dataset.riderLocationAssignments || '[]');
        return Array.isArray(assignments) ? assignments.filter((assignment) => assignment?.endpoint) : [];
    } catch {
        return [];
    }
}

function initializeRiderLocationTracker() {
    const shell = document.querySelector('[data-rider-live-location]');
    if (!shell || window.LikhaeRiderLocationTracker) return;

    let assignments = parseAssignments(shell);
    let watchId;
    let lastSentAt = 0;
    let lastCoordinates = null;
    let stoppedByRider = false;

    const statusElement = shell.querySelector('[data-rider-location-status]');
    const controls = () => [
        ...document.querySelectorAll('[data-rider-location-toggle], [data-map-rider-sharing]'),
    ];

    const setStatus = (message, state = 'ready') => {
        if (statusElement) {
            statusElement.textContent = message;
            statusElement.dataset.state = state;
            statusElement.classList.toggle('is-error', ['error', 'offline', 'poor-accuracy'].includes(state));
        }
        shell.dataset.riderLocationState = state;
        window.dispatchEvent(new CustomEvent('likhae:rider-location-status', {
            detail: { message, state, running: watchId !== undefined },
        }));
    };

    const syncControls = () => {
        const active = watchId !== undefined;
        controls().forEach((button) => {
            button.hidden = assignments.length === 0 || !navigator.geolocation;
            button.textContent = active ? 'Pause rider GPS' : 'Resume rider GPS';
            button.setAttribute('aria-pressed', String(active));
        });
    };

    const stop = (message = 'Rider GPS tracking paused.') => {
        if (watchId !== undefined && navigator.geolocation) {
            navigator.geolocation.clearWatch(watchId);
        }
        watchId = undefined;
        lastSentAt = 0;
        lastCoordinates = null;
        syncControls();
        if (message) setStatus(message, 'paused');
    };

    const removeAssignment = (endpoint) => {
        assignments = assignments.filter((assignment) => assignment.endpoint !== endpoint);
        if (!assignments.length) {
            stop('The active rider assignment has ended. GPS tracking stopped.');
        } else {
            syncControls();
        }
    };

    const postLocation = async (assignment, payload) => {
        const controller = new AbortController();
        const timeout = window.setTimeout(() => controller.abort(), 10000);
        try {
            const response = await fetch(assignment.endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                signal: controller.signal,
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            if (response.ok) return true;
            if (response.status === 403 || response.status === 409) removeAssignment(assignment.endpoint);
            return false;
        } catch {
            return false;
        } finally {
            window.clearTimeout(timeout);
        }
    };

    const send = (position) => {
        if (!assignments.length || !navigator.onLine) {
            setStatus('Device offline. GPS updates will resume when the connection returns.', 'offline');
            return;
        }

        const latitude = Number(position.coords?.latitude);
        const longitude = Number(position.coords?.longitude);
        const accuracy = Number(position.coords?.accuracy);
        const timestamp = Number(position.timestamp);
        const coordinates = [longitude, latitude];
        const recordedAt = new Date(timestamp);

        if (!Number.isFinite(latitude) || !Number.isFinite(longitude)
            || latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180
            || !Number.isFinite(timestamp) || Number.isNaN(recordedAt.getTime())) {
            setStatus('The device returned an invalid GPS coordinate. Waiting for a valid fix.', 'error');
            return;
        }

        if (!Number.isFinite(accuracy) || accuracy < 0 || accuracy > MAX_ACCURACY_METERS) {
            setStatus(`GPS accuracy is too poor (${Number.isFinite(accuracy) ? `${Math.round(accuracy)}m` : 'unknown'}). Move to an open area and wait for a better fix.`, 'poor-accuracy');
            return;
        }

        if (Math.abs(Date.now() - timestamp) > MAX_TIMESTAMP_SKEW_MS) {
            setStatus('The device GPS timestamp is stale. Waiting for a fresh fix.', 'error');
            return;
        }

        const now = Date.now();
        if (lastCoordinates
            && now - lastSentAt < LOCATION_UPDATE_INTERVAL_MS
            && distanceMeters(lastCoordinates, coordinates) < LOCATION_MOVEMENT_THRESHOLD_METERS) return;

        lastSentAt = now;
        lastCoordinates = coordinates;
        setStatus('Live GPS is active. Keep this Rider page in the foreground while traveling.', 'active');
        const payload = {
            latitude,
            longitude,
            accuracy,
            recorded_at: recordedAt.toISOString(),
        };
        Promise.all(assignments.map((assignment) => postLocation(assignment, payload)))
            .then((results) => {
                if (assignments.length && results.some(Boolean)) {
                    setStatus('Live GPS is active. Keep this Rider page in the foreground while traveling.', 'active');
                } else if (assignments.length) {
                    setStatus('GPS was read, but the server could not receive the update. Retrying.', 'offline');
                }
            });
    };

    const start = () => {
        if (watchId !== undefined || !assignments.length) return;
        if (!navigator.geolocation) {
            setStatus('This browser does not support device GPS.', 'error');
            syncControls();
            return;
        }
        if (!navigator.onLine) {
            setStatus('Device offline. GPS will start sending when the connection returns.', 'offline');
        } else {
            setStatus('Requesting device GPS permission for the active Rider assignment…', 'ready');
        }
        stoppedByRider = false;
        watchId = navigator.geolocation.watchPosition(send, (error) => {
            if (error.code === 1) {
                stop('GPS permission was denied. Enable location access in browser settings to continue tracking.');
                shell.dataset.riderLocationState = 'permission-denied';
                return;
            }
            if (error.code === 2) {
                setStatus('Device GPS is temporarily unavailable. Waiting for another fix.', 'error');
                return;
            }
            setStatus('Device GPS timed out. Waiting for another fix.', 'error');
        }, { enableHighAccuracy: true, maximumAge: 10000, timeout: 15000 });
        syncControls();
    };

    const toggle = () => {
        if (watchId === undefined) {
            stoppedByRider = false;
            start();
        } else {
            stoppedByRider = true;
            stop();
        }
    };

    const tracker = {
        start,
        stop,
        toggle,
        isRunning: () => watchId !== undefined,
    };
    window.LikhaeRiderLocationTracker = tracker;

    controls().forEach((button) => button.addEventListener('click', toggle));
    window.addEventListener('online', () => {
        if (assignments.length && watchId !== undefined) setStatus('Connection restored. Resuming live GPS updates.', 'active');
        else if (assignments.length && !stoppedByRider) start();
    });
    window.addEventListener('offline', () => setStatus('Device offline. GPS updates will resume when the connection returns.', 'offline'));
    window.addEventListener('pagehide', () => stop(null), { once: true });

    syncControls();
    start();
}

document.addEventListener('DOMContentLoaded', initializeRiderLocationTracker);

export { initializeRiderLocationTracker };
