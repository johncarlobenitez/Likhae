@include('components.marketplace.product-data')
<x-marketplace.layout title="Your LIKHAE" :buyer="true">

{{-- Buyer Hero --}}
<section class="lk-hero lk-hero--buyer">
    <div class="lk-hero__image">
        <img src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=1800&q=90" alt="Curated lifestyle pieces">
    </div>
    <div class="lk-hero__overlay"></div>
    <div class="lk-container lk-hero__content">
        <span class="lk-kicker lk-kicker--light">YOUR WEEKLY EDIT</span>
        <h1>Objects with<br><em>a point of view.</em></h1>
        <p>Discover local pieces selected around craft, regional heritage, and everyday beauty.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
            <a class="lk-btn lk-btn--light" href="{{ route('buyer.products') }}">Shop the edit</a>
            <a class="lk-btn lk-btn--ghost" href="{{ route('buyer.local-finds') }}" style="color:#fff;border:1px solid rgba(255,255,255,0.4)">Explore Local Finds</a>
        </div>
    </div>
</section>

{{-- Category Discovery --}}
<section class="lk-section lk-container">
    <div class="lk-section-heading">
        <div>
            <span class="lk-kicker">CURATED COLLECTIONS</span>
            <h2>Shop by feeling.</h2>
        </div>
        <a class="lk-text-link" href="{{ route('buyer.products') }}">View all pieces →</a>
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
            <a class="lk-category-card" href="{{ route('buyer.products').'?category='.urlencode($category[0]) }}">
                <img src="{{ $category[2] }}" alt="{{ $category[0] }}" loading="lazy">
                <div>
                    <span>{{ $category[1] }}</span>
                    <strong>{{ $category[0] }}</strong>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- New & Noteworthy --}}
<section class="lk-section lk-container">
    <div class="lk-section-heading">
        <div>
            <span class="lk-kicker">FOR YOU</span>
            <h2>New & noteworthy.</h2>
        </div>
        <a class="lk-text-link" href="{{ route('buyer.products') }}">View all →</a>
    </div>
    <div class="lk-product-grid">
        @foreach(array_slice($likhaeProducts, 0, 4) as $product)
            <x-marketplace.product-card :product="$product" :buyer="true" />
        @endforeach
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
        <a class="lk-btn lk-btn--light" href="{{ route('buyer.flash-deals') }}">Explore limited drops</a>
    </div>
</section>

{{-- Local Makers & Places --}}
<section class="lk-section lk-container" id="stories">
    <div class="lk-editorial-grid">
        <article class="lk-feature-story lk-feature-story--large">
            <img src="https://images.unsplash.com/photo-1590739225287-bd31519780c3?auto=format&fit=crop&w=1400&q=85" alt="Artisan making handcrafted goods">
            <div class="lk-story-copy">
                <span class="lk-kicker lk-kicker--light">MAKERS ISSUE</span>
                <h2>The hands behind what we keep.</h2>
                <p>Meet independent craftspeople and designers building a quieter, more personal kind of Filipino material culture.</p>
                <a href="{{ route('buyer.local-finds') }}" class="lk-btn lk-btn--light">Explore regional hubs</a>
            </div>
        </article>
        <div class="lk-place-panel">
            <span class="lk-kicker">FROM AROUND THE PHILIPPINES</span>
            <h2>Where do you want to go?</h2>
            @foreach(['Cebu', 'Benguet', 'Marikina', 'Davao', 'Manila'] as $place)
                <a href="{{ route('buyer.products').'?location='.$place }}">
                    {{ $place }} <span>→</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

</x-marketplace.layout>
