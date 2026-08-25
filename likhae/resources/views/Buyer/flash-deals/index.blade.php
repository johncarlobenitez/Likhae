@include('components.marketplace.product-data')
<x-marketplace.layout title="Limited Drops" :buyer="true">
<section class="lk-catalog-hero lk-container">
    <span class="lk-kicker">CURATED RELEASES</span>
    <h1>Limited Drops</h1>
    <p>Numbered editions and small-batch crafts produced in strictly limited runs by independent Filipino studios.</p>
</section>

<section class="lk-container">
    <div style="background:var(--paper);border:1px solid var(--line);border-radius:16px;padding:32px;margin-bottom:40px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px">
        <div>
            <span class="lk-kicker" style="color:var(--coral)">THIS WEEK'S DROP</span>
            <h2 style="font-size:2.2rem;margin:4px 0 8px">Summer Studio Collection</h2>
            <p style="color:var(--slate);margin:0">Only a small number of each piece exists. Next release in 4 days.</p>
        </div>
        <div style="display:flex;gap:12px;text-align:center">
            <div style="background:var(--sand);padding:12px 18px;border-radius:10px">
                <strong style="font-size:1.8rem;font-family:var(--serif);display:block">03</strong>
                <small style="color:var(--slate);font-size:0.7rem;text-transform:uppercase">Days</small>
            </div>
            <div style="background:var(--sand);padding:12px 18px;border-radius:10px">
                <strong style="font-size:1.8rem;font-family:var(--serif);display:block">14</strong>
                <small style="color:var(--slate);font-size:0.7rem;text-transform:uppercase">Hours</small>
            </div>
            <div style="background:var(--sand);padding:12px 18px;border-radius:10px">
                <strong style="font-size:1.8rem;font-family:var(--serif);display:block">28</strong>
                <small style="color:var(--slate);font-size:0.7rem;text-transform:uppercase">Mins</small>
            </div>
        </div>
    </div>

    <div class="lk-product-grid">
        @foreach(collect($likhaeProducts)->filter(fn($p) => in_array($p['badge'], ['LIMITED', 'LOCAL MAKER', 'NEW']))->all() as $product)
            <x-marketplace.product-card :product="$product" :buyer="true" />
        @endforeach
    </div>
</section>
</x-marketplace.layout>
