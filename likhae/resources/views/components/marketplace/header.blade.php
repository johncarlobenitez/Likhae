<header class="g-header">
    <div class="g-container">
        <div class="g-header-row">
            <div class="g-header-left">
                <button
                    class="g-mobile-toggle"
                    type="button"
                    aria-label="Open navigation"
                    aria-expanded="false"
                    data-guest-mobile-toggle
                >
                    ☰
                </button>

                <a class="g-brand" href="{{ url('/') }}">LIKHAE</a>

                <button
                    class="g-category-btn"
                    type="button"
                    aria-expanded="false"
                    data-guest-category-toggle
                >
                    Categories ▾
                </button>

                <nav class="g-category-menu" data-guest-category-menu aria-label="Product categories">
                    @foreach([
                        'Fashion',
                        'Electronics',
                        'Home & Living',
                        'Beauty & Health',
                        'Sports & Outdoors',
                        'Toys & Games',
                        'Automotive',
                        'Books & Stationery',
                        'Groceries',
                        'Pet Supplies',
                        'Shoes & Accessories',
                        'Others',
                    ] as $category)
                        <a href="{{ url('/products') }}?category={{ urlencode($category) }}">
                            {{ $category }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <form class="g-search" action="{{ url('/products') }}" method="GET" role="search">
                <button class="g-search-button" type="submit" aria-label="Search products">⌕</button>

                <label class="sr-only" for="guestMarketplaceSearch">Search products</label>
                <input
                    class="g-input"
                    id="guestMarketplaceSearch"
                    name="q"
                    type="search"
                    value="{{ request('q') }}"
                    placeholder="Search for products, brands and more..."
                    autocomplete="off"
                    data-guest-search
                >

                <div class="g-search-suggestions" data-guest-search-suggestions></div>
            </form>

            <nav class="g-header-actions" aria-label="Guest account navigation">
                <a class="g-header-link" href="{{ route('login') }}">Sign In</a>
                <a class="g-header-link g-header-register" href="{{ route('register') }}">Create Account</a>
            </nav>
        </div>

        <nav class="g-mobile-nav" data-guest-mobile-nav aria-label="Mobile navigation">
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ url('/products') }}">Products</a>
            <a href="{{ route('login') }}">Sign In</a>
            <a href="{{ route('register') }}">Create Account</a>
        </nav>
    </div>
</header>
