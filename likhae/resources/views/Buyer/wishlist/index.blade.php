<x-marketplace.layout title="Saved Wishlist" :buyer="true">
<section class="lk-page-head lk-container">
    <span class="lk-kicker">SAVED PIECES</span>
    <h1>Wishlist</h1>
    <p>Pieces you've saved from Filipino studios to revisit and collect.</p>
</section>

<section class="lk-container" data-wishlist-page>
    <div class="lk-product-grid" data-wishlist-items>
        {{-- Dynamically populated from localStorage wishlist in setupWishlist() --}}
    </div>

    <div class="lk-empty-state" data-wishlist-empty hidden>
        <h2>Your wishlist is empty.</h2>
        <p style="color:var(--slate);margin:12px 0 24px">Explore local fashion, home objects, and regional craft finds.</p>
        <a class="lk-btn lk-btn--primary" href="{{ route('buyer.products') }}">Discover Marketplace</a>
    </div>
</section>
</x-marketplace.layout>
