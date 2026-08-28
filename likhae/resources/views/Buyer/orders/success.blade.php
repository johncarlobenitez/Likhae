<x-buyer.layout title="Order Placed"><div class="b-page"><div class="b-container">
<section class="b-card b-success-card b-shadow"><div class="b-success-icon">✓</div><h1 class="b-title" style="font-size:38px">Order successfully placed!</h1><p class="b-muted" style="font-size:16px">Thank you for shopping with LIKHAE.</p>
<div style="max-width:470px;margin:20px auto;padding:14px;background:#faf8f5;border-radius:12px"><div class="b-muted" style="font-size:12px">Order Number</div><strong style="font-size:20px">LKH-2026-001234</strong><div class="b-muted" style="font-size:12px;margin-top:10px">Estimated delivery</div><strong>September 1–3, 2026</strong></div>
<div style="display:flex;justify-content:center;gap:9px;flex-wrap:wrap"><a class="b-btn b-btn-primary" href="{{ route('buyer.orders.show','001234') }}">View Order</a><a class="b-btn b-btn-secondary" href="{{ route('buyer.products') }}">Continue Shopping</a></div></section>
</div></div></x-buyer.layout>
