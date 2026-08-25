@props(['id' => 'LKH-2026-0825-1048'])
<x-marketplace.layout title="Order Tracking" :buyer="true">
<section class="lk-page-head lk-container">
    <span class="lk-kicker">ORDER #{{ $id }}</span>
    <h1>On the way to you.</h1>
    <p>Estimated delivery: August 27–28, 2026 via Standard Express Delivery</p>
</section>

<section class="lk-container lk-tracking">
    <div class="lk-tracking-card">
        <h2>Shipment Progression</h2>
        <div style="margin-top:24px">
            @foreach([
                ['Order Confirmed', 'Aug 25 · 10:48 AM', 1],
                ['Seller Preparing Order', 'Aug 25 · 1:20 PM', 1],
                ['Handed Over to Courier', 'Aug 26 · 8:05 AM', 1],
                ['In Transit (Cebu Sorting Hub)', 'Aug 26 · 4:40 PM', 2],
                ['Out for Delivery', 'Estimated Aug 27', 0],
                ['Delivered', '', 0]
            ] as $step)
                <div class="lk-track-step {{ $step[2] === 1 ? 'is-done' : ($step[2] === 2 ? 'is-active' : '') }}">
                    <b>{{ $step[2] === 1 ? '✓' : ($step[2] === 2 ? '●' : '○') }}</b>
                    <div>
                        <strong>{{ $step[0] }}</strong>
                        @if($step[1])
                            <small>{{ $step[1] }}</small>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <aside class="lk-summary-card">
        <h2>Order Details</h2>
        <p><strong>Maker:</strong> Wear Sundays · Cebu City</p>
        <p><strong>Item:</strong> Linen Lounge Set (Terracotta / M · Qty 1)</p>
        <p><strong>Shipping:</strong> Standard Express (₱120)</p>
        <p><strong>Payment:</strong> Cash on Delivery</p>
        <p><strong>Address:</strong> 18 Narra Street, Brgy. Lahug, Cebu City</p>
        <div class="lk-summary-total">
            <span>Total</span>
            <strong>₱2,010</strong>
        </div>
        <a class="lk-btn lk-btn--secondary lk-btn--wide" href="{{ route('buyer.messages') }}" style="margin-top:16px">
            Message Seller
        </a>
        <a class="lk-btn lk-btn--ghost lk-btn--wide" href="{{ route('buyer.orders') }}" style="margin-top:8px">
            ← Back to Orders
        </a>
    </aside>
</section>
</x-marketplace.layout>
