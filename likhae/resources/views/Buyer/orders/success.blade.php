<x-marketplace.layout title="Order Placed" :buyer="true">
<div class="lk-status-page">
    <div class="lk-status-card">
        <div class="lk-status-icon">✓</div>
        <span class="lk-kicker">ORDER CONFIRMED</span>
        <h1>Thank you for shopping local.</h1>
        <p>Your order has been forwarded to the artisan. You can monitor live tracking and delivery status in your orders dashboard.</p>
        <div class="lk-stack" style="max-width:320px;margin:28px auto 0">
            <a class="lk-btn lk-btn--primary lk-btn--wide" href="{{ route('buyer.orders') }}">View My Orders</a>
            <a class="lk-btn lk-btn--secondary lk-btn--wide" href="{{ route('buyer.products') }}">Continue Shopping</a>
        </div>
    </div>
</div>
</x-marketplace.layout>
