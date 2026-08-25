<x-marketplace.layout title="Messages" :buyer="true">
<section class="lk-messages lk-container" data-messages-page>
    <aside class="lk-conversations">
        <span class="lk-kicker">MESSAGES</span>
        <h1>Conversations</h1>
        <input placeholder="Search conversations..." data-conversation-search aria-label="Search conversations">
        <div style="margin-top:16px;display:grid;gap:6px">
            <button class="lk-conversation is-active" type="button">
                <span class="lk-avatar">WS</span>
                <span>
                    <strong>Wear Sundays</strong>
                    <small>Yes, the Terracotta M is available.</small>
                </span>
            </button>
            <button class="lk-conversation" type="button">
                <span class="lk-avatar">HN</span>
                <span>
                    <strong>Habi Norte</strong>
                    <small>Your tote is handwoven to order.</small>
                </span>
            </button>
            <button class="lk-conversation" type="button">
                <span class="lk-avatar">CS</span>
                <span>
                    <strong>Clay Story</strong>
                    <small>New stoneware batch arriving this Friday.</small>
                </span>
            </button>
        </div>
    </aside>

    <section class="lk-chat">
        <header>
            <div>
                <strong style="font-size:1.1rem;display:block">Wear Sundays</strong>
                <small style="color:var(--slate);display:flex;align-items:center;gap:6px;margin-top:2px">
                    <i style="display:inline-block;width:7px;height:7px;background:#6a9f66;border-radius:50%"></i>
                    Active now · Cebu City Studio
                </small>
            </div>
        </header>

        {{-- Product Reference Card --}}
        <div style="margin:12px 0 6px;padding:10px 14px;background:var(--paper);border:1px solid var(--line);border-radius:10px;display:flex;align-items:center;gap:12px">
            <img src="https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=120&q=80" alt="Linen Lounge Set" style="width:44px;height:44px;border-radius:6px;object-fit:cover">
            <div style="flex:1">
                <small style="color:var(--slate);text-transform:uppercase;font-size:0.65rem;font-weight:700">Referenced Product</small>
                <strong style="display:block;font-size:0.9rem">Linen Lounge Set — ₱1,890</strong>
            </div>
            <a href="{{ route('buyer.product-details', 'linen-lounge-set') }}" class="lk-text-link" style="font-size:0.8rem">View Item</a>
        </div>

        <div class="lk-chat-thread" data-chat-thread>
            <div class="lk-message lk-message--them">
                Hi Maria! Thank you for your interest in Wear Sundays. How can we help you today with the Linen Lounge Set?
                <small>10:24 AM</small>
            </div>
            <div class="lk-message lk-message--me">
                Hello! Is the Terracotta color in Medium available for immediate dispatch to Cebu City?
                <small>10:26 AM</small>
            </div>
            <div class="lk-message lk-message--them">
                Yes! The Terracotta M is in stock. We currently have 4 pieces left from this weekly batch and can ship tomorrow morning.
                <small>10:28 AM</small>
            </div>
        </div>

        <form class="lk-chat-compose" data-chat-form>
            <button type="button" aria-label="Attach photo or image" style="border:0;background:none;font-size:1.4rem;cursor:pointer;padding:0 8px">+</button>
            <input placeholder="Write a message to Wear Sundays..." required data-chat-input aria-label="Message input">
            <button class="lk-btn lk-btn--primary" type="submit">Send</button>
        </form>
    </section>
</section>
</x-marketplace.layout>
