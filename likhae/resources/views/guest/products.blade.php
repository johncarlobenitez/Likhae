@include('components.marketplace.product-data')
<x-marketplace.layout title="Discover" :buyer="false">
<section class="lk-catalog-hero lk-container">
    <span class="lk-kicker">DISCOVER</span>
    <h1>Find something<br><em>made with intention.</em></h1>
    <p>Curated products from Filipino makers, studios, and independent regional brands.</p>
</section>

<section class="lk-catalog lk-container" data-catalog>
    <aside class="lk-filters" aria-label="Catalog filters">
        <h3>Filter by</h3>
        <details open>
            <summary>Category <span>+</span></summary>
            <label><input type="checkbox" data-filter="category" value="Wear"> Wear (Clothing & Accessories)</label>
            <label><input type="checkbox" data-filter="category" value="Live"> Live (Home & Objects)</label>
            <label><input type="checkbox" data-filter="category" value="Taste"> Taste (Pantry & Coffee)</label>
            <label><input type="checkbox" data-filter="category" value="Glow"> Glow (Botanical Care)</label>
            <label><input type="checkbox" data-filter="category" value="Move"> Move (Activewear)</label>
        </details>
        <details open>
            <summary>Location / Region <span>+</span></summary>
            <label><input type="checkbox" data-filter="location" value="Cebu City"> Cebu City</label>
            <label><input type="checkbox" data-filter="location" value="Benguet"> Benguet / Baguio</label>
            <label><input type="checkbox" data-filter="location" value="Marikina"> Marikina</label>
            <label><input type="checkbox" data-filter="location" value="Davao City"> Davao City</label>
            <label><input type="checkbox" data-filter="location" value="Manila"> Metro Manila</label>
        </details>
        <details>
            <summary>Price Range <span>+</span></summary>
            <label><input type="checkbox" data-filter="price" value="under-1000"> Under ₱1,000</label>
            <label><input type="checkbox" data-filter="price" value="1000-2500"> ₱1,000 – ₱2,500</label>
            <label><input type="checkbox" data-filter="price" value="over-2500"> Above ₱2,500</label>
        </details>
        <details>
            <summary>Availability <span>+</span></summary>
            <label><input type="checkbox" data-filter="availability" value="in-stock" checked> In Stock</label>
            <label><input type="checkbox" data-filter="availability" value="limited"> Limited Drop</label>
        </details>
    </aside>

    <div class="lk-results">
        <div class="lk-results__bar">
            <div><strong data-result-count>{{ count($likhaeProducts) }}</strong> pieces found</div>
            <div class="lk-chips">
                <button type="button" class="is-active" data-chip="All">All</button>
                <button type="button" data-chip="Wear">Wear</button>
                <button type="button" data-chip="Live">Live</button>
                <button type="button" data-chip="Taste">Taste</button>
                <button type="button" data-chip="Glow">Glow</button>
                <button type="button" data-chip="Move">Move</button>
            </div>
            <select data-sort aria-label="Sort products">
                <option value="featured">Sort: Featured</option>
                <option value="newest">Sort: Newest</option>
                <option value="low">Price: Low to High</option>
                <option value="high">Price: High to Low</option>
                <option value="rating">Top Rated</option>
            </select>
        </div>

        <div class="lk-product-grid lk-product-grid--catalog" data-catalog-grid>
            @foreach($likhaeProducts as $product)
                <x-marketplace.product-card :product="$product" :buyer="false" />
            @endforeach
        </div>
    </div>
</section>
</x-marketplace.layout>
