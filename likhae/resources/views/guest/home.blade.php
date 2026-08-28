@php
    $guestProducts = [
        ['id'=>'wireless-headphones','slug'=>'wireless-headphones','name'=>'Premium Wireless Headphones','category'=>'Electronics','seller'=>'Metro Finds PH','location'=>'Makati City','price'=>2499,'old_price'=>2999,'discount'=>17,'rating'=>4.8,'reviews'=>128,'sold'=>250,'stock'=>18,'image'=>'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80'],
        ['id'=>'classic-backpack','slug'=>'classic-backpack','name'=>'Classic Everyday Backpack','category'=>'Bags','seller'=>'Urban Carry Co.','location'=>'Quezon City','price'=>999,'old_price'=>1299,'discount'=>23,'rating'=>4.7,'reviews'=>92,'sold'=>410,'stock'=>26,'image'=>'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&q=80'],
        ['id'=>'running-shoes','slug'=>'running-shoes','name'=>'Lightweight Running Shoes','category'=>'Sports & Outdoors','seller'=>'Stride PH','location'=>'Pasig City','price'=>1799,'old_price'=>2199,'discount'=>18,'rating'=>4.9,'reviews'=>205,'sold'=>540,'stock'=>14,'image'=>'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80'],
        ['id'=>'smart-watch','slug'=>'smart-watch','name'=>'Everyday Smart Watch','category'=>'Electronics','seller'=>'Tech Avenue','location'=>'Taguig City','price'=>2190,'old_price'=>2690,'discount'=>19,'rating'=>4.7,'reviews'=>164,'sold'=>325,'stock'=>20,'image'=>'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80'],
        ['id'=>'skincare-set','slug'=>'skincare-set','name'=>'Daily Skincare Essentials Set','category'=>'Beauty & Health','seller'=>'Glow Market','location'=>'Manila','price'=>899,'old_price'=>1099,'discount'=>18,'rating'=>4.8,'reviews'=>110,'sold'=>290,'stock'=>31,'image'=>'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=400&q=80'],
        ['id'=>'mechanical-keyboard','slug'=>'mechanical-keyboard','name'=>'Compact Mechanical Keyboard','category'=>'Electronics','seller'=>'KeyHub PH','location'=>'Mandaluyong City','price'=>1899,'old_price'=>2299,'discount'=>17,'rating'=>4.9,'reviews'=>180,'sold'=>460,'stock'=>17,'image'=>'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&q=80'],
        ['id'=>'coffee-maker','slug'=>'coffee-maker','name'=>'Compact Home Coffee Maker','category'=>'Home & Living','seller'=>'Kitchen+ Manila','location'=>'Manila','price'=>1599,'old_price'=>1899,'discount'=>16,'rating'=>4.6,'reviews'=>84,'sold'=>170,'stock'=>11,'image'=>'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&q=80'],
        ['id'=>'desk-lamp','slug'=>'desk-lamp','name'=>'Minimal Adjustable Desk Lamp','category'=>'Home & Living','seller'=>'Home Basics MNL','location'=>'Marikina City','price'=>749,'old_price'=>899,'discount'=>17,'rating'=>4.6,'reviews'=>71,'sold'=>180,'stock'=>22,'image'=>'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&q=80'],
        ['id'=>'mirrorless-camera','slug'=>'mirrorless-camera','name'=>'Compact Mirrorless Camera','category'=>'Electronics','seller'=>'Frame House PH','location'=>'Makati City','price'=>16990,'old_price'=>18490,'discount'=>8,'rating'=>4.8,'reviews'=>66,'sold'=>94,'stock'=>7,'image'=>'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80'],
    ];
@endphp

<x-marketplace.layout title="Everything You Need">
    <div class="g-page">
        <div class="g-container">
            <section class="g-card g-hero g-shadow">
                <div class="g-hero-copy">
                    <span class="g-eyebrow">Modern marketplace · Philippines</span>

                    <h1>Everything you need, all in one place.</h1>

                    <p>
                        Discover products across every category from trusted sellers.
                        Shop with secure checkout, buyer protection, easy order tracking,
                        and direct seller messaging.
                    </p>

                    <div class="g-hero-actions">
                        <a class="g-btn g-btn-primary" href="{{ url('/products') }}">Shop Now</a>
                        <a class="g-btn g-btn-secondary" href="#guestCategories">Explore Categories</a>
                    </div>
                </div>

                <div class="g-hero-products" aria-label="Featured marketplace products">
                    @foreach(array_slice($guestProducts, 0, 4) as $product)
                        <a class="g-hero-product" href="{{ url('/products/' . $product['slug']) }}">
                            <img
                                src="{{ $product['image'] }}"
                                alt="{{ $product['name'] }}"
                            >
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="g-benefits" aria-label="Marketplace benefits">
                <div class="g-benefit">
                    <span class="g-benefit-icon">🚚</span>
                    <div>
                        <strong>Free Shipping</strong>
                        <small>Available on selected orders</small>
                    </div>
                </div>

                <div class="g-benefit">
                    <span class="g-benefit-icon">🔒</span>
                    <div>
                        <strong>Secure Payments</strong>
                        <small>Protected checkout experience</small>
                    </div>
                </div>

                <div class="g-benefit">
                    <span class="g-benefit-icon">↩</span>
                    <div>
                        <strong>Easy Returns</strong>
                        <small>Clear return support</small>
                    </div>
                </div>

                <div class="g-benefit">
                    <span class="g-benefit-icon">✓</span>
                    <div>
                        <strong>Buyer Protection</strong>
                        <small>Support throughout your order</small>
                    </div>
                </div>
            </section>

            <section class="g-section" id="guestCategories">
                <div class="g-section-head">
                    <div>
                        <h2 class="g-section-title">Shop by Category</h2>
                        <p class="g-muted">Explore popular categories across LIKHAE.</p>
                    </div>

                    <a class="g-section-link" href="{{ url('/products') }}">View all products →</a>
                </div>

                @php
                    $categories = [
                        ['Fashion', '/guest/categories/fashion.svg'],
                        ['Electronics', '/guest/categories/electronics.svg'],
                        ['Home & Living', '/guest/categories/home.svg'],
                        ['Beauty & Health', '/guest/categories/beauty.svg'],
                        ['Sports & Outdoors', '/guest/categories/sports.svg'],
                        ['Toys & Games', '/guest/categories/toys.svg'],
                        ['Automotive', '/guest/categories/automotive.svg'],
                        ['Books & Stationery', '/guest/categories/books.svg'],
                    ];
                @endphp

                <div class="g-category-grid">
                    @foreach($categories as [$category, $image])
                        <a
                            class="g-category-card"
                            href="{{ url('/products') }}?category={{ urlencode($category) }}"
                        >
                            <img src="{{ asset(ltrim($image, '/')) }}" alt="">
                            <strong>{{ $category }}</strong>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="g-section">
                <div class="g-section-head">
                    <div>
                        <h2 class="g-section-title">Flash Deals</h2>
                        <p class="g-muted">Limited-time savings across different categories.</p>
                    </div>

                    <a class="g-section-link" href="{{ url('/products') }}">See deals →</a>
                </div>

                <div class="g-product-grid">
                    @foreach(array_slice($guestProducts, 0, 4) as $product)
                        <x-marketplace.product-card :product="$product" />
                    @endforeach
                </div>
            </section>

            <section class="g-section">
                <div class="g-promo-banner">
                    <div class="g-promo-copy">
                        <h2>Find something useful. Discover something new.</h2>
                        <p>
                            From electronics and fashion to home essentials, beauty,
                            sports, and everyday supplies — LIKHAE brings more choices
                            into one organized shopping experience.
                        </p>

                        <div style="margin-top:20px">
                            <a
                                class="g-btn"
                                style="background:#fff;color:var(--likhae-burgundy)"
                                href="{{ url('/products') }}"
                            >
                                Explore Marketplace
                            </a>
                        </div>
                    </div>

                    <div class="g-promo-art" aria-hidden="true"></div>
                </div>
            </section>

            <section class="g-section">
                <div class="g-section-head">
                    <div>
                        <h2 class="g-section-title">Featured Products</h2>
                        <p class="g-muted">Popular picks from trusted marketplace sellers.</p>
                    </div>
                </div>

                <div class="g-product-grid">
                    @foreach(array_slice($guestProducts, 4, 4) as $product)
                        <x-marketplace.product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-marketplace.layout>
