<x-marketplace.layout title="Orders & Purchases" :buyer="true">
<section class="lk-page-head lk-container">
    <span class="lk-kicker">YOUR PURCHASES</span>
    <h1>Orders</h1>
    <p>Track your active shipments, view order history, and submit reviews for delivered crafts.</p>
</section>

<section class="lk-container" data-orders-page>
    <div class="lk-tabbar" data-order-tabs>
        @foreach(['All', 'To Pay', 'To Ship', 'In Transit', 'Out for Delivery', 'Delivered', 'Completed', 'Cancelled'] as $i => $tab)
            <button type="button" class="{{ $i === 0 ? 'is-active' : '' }}" data-tab="{{ $tab }}">{{ $tab }}</button>
        @endforeach
    </div>

    <div class="lk-order-list" data-order-list>
        {{-- Mock order 1 --}}
        <article class="lk-order-card" data-order-status="In Transit">
            <div class="lk-order-card__top">
                <div>
                    <small style="color:var(--slate);letter-spacing:0.05em">ORDER #LKH-2026-0825-1048 · Aug 25, 2026</small>
                    <strong style="display:block;margin-top:2px">Wear Sundays · Cebu City</strong>
                </div>
                <span class="lk-status-pill lk-status-pill--pending">In Transit</span>
            </div>
            <div class="lk-order-product">
                <img src="https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=300&q=80" alt="Linen Lounge Set">
                <div>
                    <strong>Linen Lounge Set</strong>
                    <span style="color:var(--slate);font-size:0.85rem">Color: Terracotta · Size: M · Qty: 1</span>
                    <b style="font-size:1.1rem;margin-top:4px;display:block">₱1,890</b>
                </div>
            </div>
            <div class="lk-order-card__actions">
                <div>
                    <span style="font-size:0.85rem;color:var(--slate)">Total Amount: <strong>₱2,010</strong> (incl. ₱120 shipping)</span>
                </div>
                <div style="display:flex;gap:10px;align-items:center">
                    <a class="lk-btn lk-btn--secondary" href="{{ route('buyer.orders.show', 'LKH-2026-0825-1048') }}">Track Order</a>
                    <a class="lk-text-link" href="{{ route('buyer.messages') }}">Message Seller</a>
                </div>
            </div>
        </article>

        {{-- Mock order 2 --}}
        <article class="lk-order-card" data-order-status="Delivered">
            <div class="lk-order-card__top">
                <div>
                    <small style="color:var(--slate);letter-spacing:0.05em">ORDER #LKH-2026-0810-0921 · Aug 10, 2026</small>
                    <strong style="display:block;margin-top:2px">Clay Story · Quezon City</strong>
                </div>
                <span class="lk-status-pill lk-status-pill--approved">Delivered</span>
            </div>
            <div class="lk-order-product">
                <img src="https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?auto=format&fit=crop&w=300&q=80" alt="Sienna Stoneware Mug">
                <div>
                    <strong>Sienna Stoneware Mug</strong>
                    <span style="color:var(--slate);font-size:0.85rem">Handmade Ceramic · Qty: 2</span>
                    <b style="font-size:1.1rem;margin-top:4px;display:block">₱1,360</b>
                </div>
            </div>
            <div class="lk-order-card__actions">
                <div>
                    <span style="font-size:0.85rem;color:var(--slate)">Total Amount: <strong>₱1,480</strong></span>
                </div>
                <div style="display:flex;gap:10px;align-items:center">
                    <a class="lk-btn lk-btn--primary" href="{{ route('buyer.orders.review', 'LKH-2026-0810-0921') }}">Rate Product</a>
                    <a class="lk-btn lk-btn--secondary" href="{{ route('buyer.product-details', 'sienna-stoneware-mug') }}">Buy Again</a>
                </div>
            </div>
        </article>
    </div>
</section>
</x-marketplace.layout>
