@include('guest.product-data')

@php
    $searchQuery = trim((string) request('q', ''));
    $selectedCategory = trim((string) request('category', ''));
@endphp

<x-marketplace.layout title="Products">
    <div class="g-page" data-guest-products-page>
        <div class="g-container">
            <nav class="g-breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <span>›</span>
                <span>Products</span>
            </nav>

            <div class="g-products-head">
                <div>
                    <h1 class="g-title">
                        @if($searchQuery)
                            Search results for “{{ $searchQuery }}”
                        @elseif($selectedCategory)
                            {{ $selectedCategory }}
                        @else
                            Explore Products
                        @endif
                    </h1>

                    <p class="g-muted">
                        Browse products from multiple categories and marketplace sellers.
                    </p>
                </div>

                <button
                    class="g-btn g-btn-secondary g-filter-mobile-btn"
                    type="button"
                    data-guest-filter-toggle
                >
                    Filters
                </button>
            </div>

            <div class="g-catalog">
                <aside class="g-card g-filters" data-guest-filter-panel>
                    <div class="g-filter-title">
                        <strong>Filters</strong>
                    </div>

                    <div class="g-filter-group">
                        <strong>Find in results</strong>
                        <input
                            class="g-input"
                            type="search"
                            placeholder="Product, seller, category..."
                            value="{{ $searchQuery }}"
                            data-guest-product-filter-search
                        >
                    </div>

                    <div class="g-filter-group">
                        <strong>Category</strong>

                        <label class="g-filter-option">
                            <input
                                type="radio"
                                name="guest_category_filter"
                                value=""
                                data-guest-category-filter
                                {{ $selectedCategory === '' ? 'checked' : '' }}
                            >
                            All categories
                        </label>

                        @foreach([
                            'Electronics',
                            'Bags',
                            'Home & Living',
                            'Beauty & Health',
                            'Sports & Outdoors',
                        ] as $category)
                            <label class="g-filter-option">
                                <input
                                    type="radio"
                                    name="guest_category_filter"
                                    value="{{ $category }}"
                                    data-guest-category-filter
                                    {{ $selectedCategory === $category ? 'checked' : '' }}
                                >
                                {{ $category }}
                            </label>
                        @endforeach
                    </div>

                    <div class="g-filter-group">
                        <strong>Price Range</strong>
                        <div class="g-price-fields">
                            <input class="g-input" inputmode="numeric" placeholder="Min">
                            <input class="g-input" inputmode="numeric" placeholder="Max">
                        </div>
                    </div>

                    <div class="g-filter-group">
                        <strong>Rating</strong>
                        <label class="g-filter-option"><input type="checkbox"> 4★ & above</label>
                        <label class="g-filter-option"><input type="checkbox"> 3★ & above</label>
                    </div>

                    <div class="g-filter-group">
                        <strong>Availability</strong>
                        <label class="g-filter-option"><input type="checkbox"> In stock</label>
                    </div>
                </aside>

                <section class="g-catalog-main">
                    <div class="g-catalog-toolbar">
                        <div class="g-active-filter-row">
                            <span class="g-filter-chip">Trusted sellers</span>
                            <span class="g-filter-chip">Philippines</span>
                        </div>

                        <label>
                            <span class="sr-only">Sort products</span>
                            <select class="g-select g-sort-select" data-guest-sort>
                                <option value="relevant">Most Relevant</option>
                                <option value="best-selling">Best Selling</option>
                                <option value="highest-rated">Highest Rated</option>
                                <option value="price-low">Price Low to High</option>
                                <option value="price-high">Price High to Low</option>
                            </select>
                        </label>
                    </div>

                    @if(count($guestProducts))
                        <div class="g-product-grid" data-guest-product-grid>
                            @foreach($guestProducts as $product)
                                <x-marketplace.product-card :product="$product" />
                            @endforeach
                        </div>
                    @else
                        <div class="g-card g-empty">
                            <div class="g-empty-icon">⌕</div>
                            <h3>No products found</h3>
                            <p>Try another search term or explore a different category.</p>
                            <a class="g-btn g-btn-primary" href="{{ url('/products') }}">
                                View All Products
                            </a>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-marketplace.layout>
