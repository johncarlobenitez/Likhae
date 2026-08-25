<x-marketplace.layout title="My Reviews" :buyer="true">
<section class="lk-page-head lk-container">
    <span class="lk-kicker">FEEDBACK HISTORY</span>
    <h1>My Reviews</h1>
</section>

<section class="lk-account-layout lk-container">
    <x-marketplace.account-nav current="reviews" />

    <section class="lk-account-card">
        <h2>Submitted Product Reviews</h2>
        <p style="color:var(--slate);margin-bottom:24px">Reviews you have shared with the LIKHAE artisan community.</p>

        <div style="display:grid;gap:18px">
            <article style="padding:18px;border:1px solid var(--line);border-radius:12px;display:grid;grid-template-columns:80px 1fr;gap:16px">
                <img src="https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?auto=format&fit=crop&w=200&q=80" alt="Sienna Stoneware Mug" style="width:80px;height:80px;border-radius:8px;object-fit:cover">
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:start">
                        <div>
                            <strong style="font-size:1.1rem">Sienna Stoneware Mug</strong>
                            <span style="color:var(--slate);display:block;font-size:0.8rem">Clay Story · Reviewed on Aug 18, 2026</span>
                        </div>
                        <span style="color:#c89226;font-size:1.1rem">★★★★★</span>
                    </div>
                    <p style="margin:10px 0 0;font-size:0.9rem;color:var(--ink)">
                        "The glaze has a wonderfully organic texture in person. It feels solid in the hand and keeps coffee warm for a long time. Will definitely order from Clay Story again."
                    </p>
                </div>
            </article>
        </div>
    </section>
</section>
</x-marketplace.layout>
