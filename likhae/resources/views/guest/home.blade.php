@include('components.marketplace.product-data')
<x-marketplace.layout title="Discover Filipino Design" :buyer="false">

{{-- Hero Section --}}
<section class="lk-hero">
    <div class="lk-hero__image">
        <img src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=1800&q=90" alt="Curated Filipino-inspired lifestyle fashion">
    </div>
    <div class="lk-hero__overlay"></div>
    <div class="lk-container lk-hero__content">
        <span class="lk-kicker lk-kicker--light">SUMMER ISSUE · AUGUST 2026</span>
        <h1>Made here.<br><em>Loved everywhere.</em></h1>
        <p>The best of Filipino brands, makers, and regional design studios — curated for the way you live.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
            <a class="lk-btn lk-btn--light" href="{{ url('/products') }}">Discover the issue</a>
            <a class="lk-btn lk-btn--ghost" href="#makers-issue" style="color:#fff;border:1px solid rgba(255,255,255,0.4)">Read Maker Stories</a>
        </div>
    </div>
</section>

{{-- Category Discovery ("Shop How You Feel") --}}
<section class="lk-section lk-container">
    <div class="lk-section-heading">
        <div>
            <span class="lk-kicker">FIND YOUR WAY IN</span>
            <h2>Shop by feeling, not just category.</h2>
        </div>
        <a class="lk-text-link" href="{{ url('/products') }}">View entire collection →</a>
    </div>
    <div class="lk-category-grid">
        @foreach([
            ['Wear', 'Modern Filipino dressing & textiles', 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&q=80'],
            ['Live', 'Objects for everyday rituals', 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=900&q=80'],
            ['Taste', 'Pantry finds with a story', 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=80'],
            ['Glow', 'Local botanical self-care', 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80'],
            ['Move', 'Made to move with you', 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=900&q=80'],
            ['Local', 'Meet the independent makers', 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=900&q=80']
        ] as $category)
            <a class="lk-category-card" href="{{ url('/products?category='.urlencode($category[0])) }}">
                <img src="{{ $category[2] }}" alt="{{ $category[0] }}" loading="lazy">
                <div>
                    <span>{{ $category[1] }}</span>
                    <strong>{{ $category[0] }}</strong>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- The LIKHAE Edit --}}
<section class="lk-section lk-section--tint">
    <div class="lk-container">
        <div class="lk-section-heading">
            <div>
                <span class="lk-kicker">THE LIKHAE EDIT</span>
                <h2>Pieces worth knowing.</h2>
            </div>
            <p class="lk-section-copy">Small-batch, thoughtfully made, and chosen for lasting character across Philippine homes.</p>
        </div>
        <div class="lk-product-grid">
            @foreach(array_slice($likhaeProducts, 0, 4) as $product)
                <x-marketplace.product-card :product="$product" :buyer="false" />
            @endforeach
        </div>
    </div>
</section>

{{-- Editorial Feature Story & Regional Hubs --}}
<section class="lk-section lk-container" id="makers-issue">
    <div class="lk-editorial-grid">
        <article class="lk-feature-story lk-feature-story--large">
            <img src="https://images.unsplash.com/photo-1590739225287-bd31519780c3?auto=format&fit=crop&w=1400&q=85" alt="Local artisan crafting ceramics">
            <div class="lk-story-copy">
                <span class="lk-kicker lk-kicker--light">MAKERS ISSUE</span>
                <h2>The hands behind what we keep.</h2>
                <p>Meet independent craftspeople and designers building a quieter, more personal kind of Filipino material culture.</p>
                <a href="{{ url('/products?filter=local') }}" class="lk-btn lk-btn--light">Meet local makers</a>
            </div>
        </article>
        <div class="lk-place-panel">
            <span class="lk-kicker">FROM AROUND THE PHILIPPINES</span>
            <h2>Where do you want to go?</h2>
            @foreach(['Cebu', 'Benguet', 'Marikina', 'Davao', 'Manila'] as $place)
                <a href="{{ url('/products?location='.$place) }}">
                    {{ $place }} <span>→</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Limited Drop Banner --}}
<section class="lk-section lk-section--ink">
    <div class="lk-container lk-banner">
        <div>
            <span class="lk-kicker lk-kicker--light">LIMITED DROP</span>
            <h2>Made in small numbers.</h2>
            <p>Fresh releases and numbered editions from independent Filipino studios. Once they are gone, they may not return.</p>
        </div>
        <a class="lk-btn lk-btn--light" href="{{ url('/products?filter=limited') }}">Explore limited drops</a>
    </div>
</section>

{{-- Marketplace Trust Section --}}
<section class="lk-section lk-container">
    <div class="lk-section-heading">
        <div>
            <span class="lk-kicker">OUR PROMISE</span>
            <h2>A marketplace built on craft & trust.</h2>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:24px">
        <div style="padding:28px;background:var(--paper);border:1px solid var(--line);border-radius:14px">
            <strong style="font-family:var(--serif);font-size:1.4rem;display:block;margin-bottom:8px">100% Filipino Makers</strong>
            <p style="color:var(--slate);margin:0">Every product is created or curated by authentic homegrown creators and regional studios.</p>
        </div>
        <div style="padding:28px;background:var(--paper);border:1px solid var(--line);border-radius:14px">
            <strong style="font-family:var(--serif);font-size:1.4rem;display:block;margin-bottom:8px">Direct Maker Support</strong>
            <p style="color:var(--slate);margin:0">Your purchases directly fund artisan workshops, fair wages, and traditional indigenous weaving communities.</p>
        </div>
        <div style="padding:28px;background:var(--paper);border:1px solid var(--line);border-radius:14px">
            <strong style="font-family:var(--serif);font-size:1.4rem;display:block;margin-bottom:8px">Protected Transactions</strong>
            <p style="color:var(--slate);margin:0">Orders are carefully packed and backed by our hassle-free local return and satisfaction guarantee.</p>
        </div>
    </div>
</section>

</x-marketplace.layout>
