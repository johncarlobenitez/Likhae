const FALLBACK_INTERVAL_MS = 5000;

const connectionFor = () => window.Echo?.connector?.pusher?.connection || null;

export const startRealtimeFallback = (poll) => {
    let timer = null;
    let disposed = false;
    const connection = connectionFor();

    const stop = () => {
        if (timer !== null) window.clearInterval(timer);
        timer = null;
    };

    const start = () => {
        if (disposed || timer !== null) return;
        // Poll once immediately after the connection drops, then every five
        // seconds only until Reverb is connected again.
        poll();
        timer = window.setInterval(poll, FALLBACK_INTERVAL_MS);
    };

    const sync = () => {
        const connected = connection?.state === 'connected';
        if (connected) stop();
        else start();
    };

    if (connection) {
        ['connected', 'connecting', 'disconnected', 'unavailable', 'failed'].forEach((state) => {
            connection.bind(state, sync);
        });
    }
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) sync();
    });
    sync();

    return () => {
        disposed = true;
        stop();
        if (connection) {
            ['connected', 'connecting', 'disconnected', 'unavailable', 'failed'].forEach((state) => {
                connection.unbind(state, sync);
            });
        }
    };
};
