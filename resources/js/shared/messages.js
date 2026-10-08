import './echo.js';
import { startRealtimeFallback } from './realtime-fallback.js';

const formatMessageTime = (payload) => {
    if (payload.time) return payload.time;
    if (payload.sent_at) return new Date(payload.sent_at).toLocaleTimeString('en-PH', {
        timeZone: 'Asia/Manila',
        hour: 'numeric',
        minute: '2-digit',
    });
    return 'Just now';
};

const appendRealtimeMessage = (thread, payload) => {
    const id = String(payload.id || '');
    if (!id || [...thread.querySelectorAll('[data-message-id]')].some((row) => row.dataset.messageId === id)) return;

    const currentUserId = String(thread.dataset.currentUserId || '');
    const isMine = String(payload.sender_id || '') === currentUserId;
    const previousRow = [...thread.querySelectorAll('[data-message-id]')].pop();
    const previousBody = previousRow?.querySelector('.lk-message-bubble')?.textContent?.trim();
    const isBuyer = Boolean(thread.closest('.lk-messages-page'));
    const previousIsMine = isBuyer
        ? previousRow?.classList.contains('is-buyer')
        : previousRow?.classList.contains('is-mine');
    if (previousRow && previousBody === String(payload.body || '').trim()
        && previousIsMine === isMine) return;
    const otherAvatar = thread.dataset.otherAvatar || thread.dataset.sellerAvatar || '';
    const otherName = thread.dataset.otherName || thread.dataset.sellerName || 'User';

    thread.querySelector('.lk-messages-empty')?.remove();

    const row = document.createElement('div');
    row.className = `lk-message-row${isMine ? (isBuyer ? ' is-buyer' : ' is-mine') : ''}`;
    row.dataset.messageId = id;

    if (!isMine) {
        const avatar = document.createElement('img');
        avatar.className = 'lk-message-small-avatar';
        avatar.src = otherAvatar;
        avatar.alt = otherName;
        row.appendChild(avatar);
    }

    const wrapper = document.createElement('div');
    const bubble = document.createElement('div');
    bubble.className = 'lk-message-bubble';
    bubble.textContent = payload.body || '';
    const time = document.createElement('span');
    time.className = 'lk-message-time';
    time.textContent = formatMessageTime(payload);
    wrapper.append(bubble, time);
    row.appendChild(wrapper);
    thread.appendChild(row);
    thread.scrollTop = thread.scrollHeight;
};

const setConnectionStatus = (thread, message, error = false) => {
    const status = thread.closest('.lk-chat-area')?.querySelector('[data-message-connection-status]');
    if (!status) return;
    const dot = status.querySelector('.lk-chat-status-dot');
    status.lastChild.textContent = message;
    status.classList.toggle('is-error', error);
    dot?.classList.toggle('is-error', error);
};

const subscribeToThread = (thread) => {
    const conversationId = thread.dataset.conversationId;
    if (!conversationId) return;

    if (window.Echo && thread.dataset.realtimeSubscribed !== 'true') {
        thread.dataset.realtimeSubscribed = 'true';
        window.Echo.private(`conversations.${conversationId}`)
            .listen('.message.sent', (payload) => appendRealtimeMessage(thread, payload));
    }

    const connection = window.Echo?.connector?.pusher?.connection;
    if (connection && thread.dataset.connectionStatusBound !== 'true') {
        thread.dataset.connectionStatusBound = 'true';
        connection.bind('connected', () => setConnectionStatus(thread, 'Live conversation'));
        connection.bind('disconnected', () => setConnectionStatus(thread, 'Reconnecting; 5-second fallback active', true));
        connection.bind('unavailable', () => setConnectionStatus(thread, '5-second fallback active', true));
        setConnectionStatus(
            thread,
            connection.state === 'connected' ? 'Live conversation' : 'Connecting; fallback active',
            connection.state !== 'connected',
        );
    }

    if (thread.dataset.realtimeFallbackBound === 'true' || !thread.dataset.streamUrl) return;
    thread.dataset.realtimeFallbackBound = 'true';
    let polling = false;
    startRealtimeFallback(async () => {
        if (polling) return;
        polling = true;
        try {
            const response = await fetch(thread.dataset.streamUrl, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            const payload = await response.json();
            if (!response.ok || !Array.isArray(payload.messages)) return;
            payload.messages.forEach((message) => appendRealtimeMessage(thread, {
                ...message,
                sender_id: message.sender_id ?? message.sender_user_id,
            }));
            setConnectionStatus(thread, '5-second fallback active', true);
        } catch (_) {
            // The next five-second fallback attempt will retry.
            setConnectionStatus(thread, 'Message updates temporarily unavailable', true);
        } finally {
            polling = false;
        }
    });
};

const bindLiveSend = (form) => {
    if (form.dataset.liveSendBound === 'true') return;
    form.dataset.liveSendBound = 'true';

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (form.dataset.sending === 'true') return;

        const input = form.querySelector('input[name="body"], textarea[name="body"]');
        const button = form.querySelector('button[type="submit"]');
        const thread = form.closest('.lk-chat-area')?.querySelector('.lk-chat-stream[data-message-thread]');
        const body = input?.value.trim();
        if (!body || !thread) return;

        form.dataset.sending = 'true';
        if (button) {
            button.disabled = true;
            button.dataset.originalText ||= button.textContent;
            button.textContent = 'Sending...';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                body: new FormData(form),
            });
            const payload = await response.json().catch(() => ({}));
            if (!response.ok || !payload.message) {
                const validationMessage = payload?.errors ? Object.values(payload.errors).flat()[0] : null;
                throw new Error(validationMessage || payload?.message || 'Message could not be sent.');
            }

            appendRealtimeMessage(thread, payload.message);
            input.value = '';
            setConnectionStatus(thread, 'Message sent');
        } catch (error) {
            setConnectionStatus(thread, error?.message || 'Message could not be sent.', true);
        } finally {
            form.dataset.sending = 'false';
            if (button) {
                button.disabled = false;
                button.textContent = button.dataset.originalText || 'Send';
            }
            input?.focus();
        }
    });
};

window.lkSubscribeMessageThread = (thread, payload = null) => {
    if (payload?.conversation_id && !thread.dataset.conversationId) {
        thread.dataset.conversationId = String(payload.conversation_id);
    }

    subscribeToThread(thread);
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.lk-chat-stream[data-conversation-id]').forEach(subscribeToThread);
    document.querySelectorAll('[data-live-message-form]').forEach(bindLiveSend);
});
