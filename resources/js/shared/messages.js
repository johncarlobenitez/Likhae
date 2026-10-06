import './echo.js';

const formatMessageTime = (payload) => {
    if (payload.time) return payload.time;
    if (payload.sent_at) return new Date(payload.sent_at).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
    return 'Just now';
};

const appendRealtimeMessage = (thread, payload) => {
    const id = String(payload.id || '');
    if (!id || [...thread.querySelectorAll('[data-message-id]')].some((row) => row.dataset.messageId === id)) return;

    const currentUserId = String(thread.dataset.currentUserId || '');
    const isMine = String(payload.sender_id || '') === currentUserId;
    const isBuyer = Boolean(thread.closest('.lk-messages-page'));
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

const subscribeToThread = (thread) => {
    const conversationId = thread.dataset.conversationId;
    if (!conversationId || !window.Echo || thread.dataset.realtimeSubscribed === 'true') return;

    thread.dataset.realtimeSubscribed = 'true';

    window.Echo.private(`conversations.${conversationId}`)
        .listen('.message.sent', (payload) => appendRealtimeMessage(thread, payload));
};

window.lkSubscribeMessageThread = (thread, payload = null) => {
    if (payload?.conversation_id && !thread.dataset.conversationId) {
        thread.dataset.conversationId = String(payload.conversation_id);
    }

    subscribeToThread(thread);
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.lk-chat-stream[data-conversation-id]').forEach(subscribeToThread);
});
