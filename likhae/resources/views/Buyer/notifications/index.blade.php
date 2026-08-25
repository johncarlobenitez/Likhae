<x-marketplace.layout title="Notifications" :buyer="true">
<section class="lk-page-head lk-container">
    <span class="lk-kicker">UPDATES & ALERTS</span>
    <h1>Notifications</h1>
</section>

<section class="lk-narrow lk-container" data-notifications>
    <div class="lk-card-heading" style="margin-bottom:16px">
        <strong>Recent Activity</strong>
        <button class="lk-text-link" type="button" data-mark-all-read style="color:var(--coral);font-weight:700">Mark all as read</button>
    </div>

    @foreach([
        ['Your order is on the way!', 'Linen Lounge Set left the Cebu central logistics hub and is en route.', '15 min ago', 'order'],
        ['Wear Sundays replied to your inquiry', '"Yes! The Terracotta M is in stock. We can dispatch tomorrow..."', '1 hr ago', 'message'],
        ['Special Offer: LOCAL10 is active', 'Enjoy 10% off selected independent Filipino designers and artisans this week.', 'Yesterday', 'promo'],
        ['Registration Approved', 'Welcome to LIKHAE! Your verified buyer account has been approved.', '3 days ago', 'account']
    ] as $i => $n)
        <article class="lk-notification {{ $i < 2 ? 'is-unread' : '' }}" data-notification>
            <b></b>
            <div>
                <strong style="font-size:0.95rem">{{ $n[0] }}</strong>
                <p style="margin:4px 0 6px;font-size:0.85rem;color:var(--slate)">{{ $n[1] }}</p>
                <small style="color:#8a939b;font-size:0.75rem">{{ $n[2] }}</small>
            </div>
            <button class="lk-text-link" type="button" data-mark-read style="font-size:0.8rem">Mark read</button>
        </article>
    @endforeach
</section>
</x-marketplace.layout>
