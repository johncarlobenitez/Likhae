<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Wishlist — LIKHAE</title>

    @vite([
        'resources/css/buyer/wishlist.css',
        'resources/js/app.js'
    ])
</head>
<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    $buyer = [
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'cart_count' => 2,
        'message_count' => 3,
        'notification_count' => 4,
    ];

    $wishlistItems = [
        [
            'id' => 1,
            'name' => 'Baseus Wireless Earbuds A3i Pro',
            'slug' => 'baseus-wireless-earbuds-a3i-pro',
            'seller' => 'TECHHUB PH',
            'seller_slug' => 'techhub-ph',
            'price' => 599,
            'old_price' => 899,
            'rating' => 4.8,
            'sold' => '1.2K',
            'stock' => 18,
            'variation' => 'Black / Standard',
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=90',
            'added_at' => 'Aug 18, 2026',
        ],
        [
            'id' => 2,
            'name' => 'Mechanical Keyboard TKL RGB',
            'slug' => 'mechanical-keyboard-tkl-rgb',
            'seller' => 'TECHHUB PH',
            'seller_slug' => 'techhub-ph',
            'price' => 1799,
            'old_price' => 2199,
            'rating' => 4.9,
            'sold' => '846',
            'stock' => 7,
            'variation' => 'Black / Red Switch',
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=90',
            'added_at' => 'Aug 17, 2026',
        ],
        [
            'id' => 3,
            'name' => 'Handwoven Rattan Tote Bag',
            'slug' => 'handwoven-rattan-tote-bag',
            'seller' => 'LIKHA ARTISANS',
            'seller_slug' => 'likha-artisans',
            'price' => 899,
            'old_price' => 1099,
            'rating' => 4.7,
            'sold' => '391',
            'stock' => 12,
            'variation' => 'Natural / Standard',
            'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=800&q=90',
            'added_at' => 'Aug 14, 2026',
        ],
        [
            'id' => 4,
            'name' => 'Capiz Shell Pendant Lamp',
            'slug' => 'capiz-shell-pendant-lamp',
            'seller' => 'CEBU CRAFTS CO.',
            'seller_slug' => 'cebu-crafts-co',
            'price' => 1899,
            'old_price' => 2299,
            'rating' => 4.6,
            'sold' => '218',
            'stock' => 0,
            'variation' => 'Natural / Medium',
            'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=800&q=90',
            'added_at' => 'Aug 12, 2026',
        ],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container">
        <div class="flex h-16 items-center gap-4">
            <a href="{{ url('/buyer/home') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">L</span>
                <span class="text-xl font-black tracking-tight">LIKHAE</span>
            </a>

            <form action="{{ url('/buyer/products') }}" method="GET" class="hidden min-w-0 flex-1 md:flex">
                <div class="flex h-11 w-full overflow-hidden border border-[#dedad3] bg-white">
                    <input type="search" name="q" placeholder="Search products, brands, Filipino finds..." class="min-w-0 flex-1 bg-transparent px-4 text-sm outline-none placeholder:text-[#b9b4ad]">
                    <button type="submit" class="flex w-[108px] items-center justify-center gap-2 bg-[#d92d2f] px-4 text-sm font-bold text-white transition hover:bg-[#bd2024]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
                        Search
                    </button>
                </div>
            </form>

            <nav class="ml-auto flex items-center gap-1 sm:gap-2">
                <a href="{{ url('/buyer/notifications') }}" class="buyer-header-action">
                    <span class="relative"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M10 21h4"></path></svg><span class="header-count">{{ $buyer['notification_count'] }}</span></span>
                    <span class="hidden text-[10px] lg:block">Alerts</span>
                </a>
                <a href="{{ url('/buyer/messages') }}" class="buyer-header-action">
                    <span class="relative"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 5h16v11H8l-4 4V5Z"></path></svg><span class="header-count">{{ $buyer['message_count'] }}</span></span>
                    <span class="hidden text-[10px] lg:block">Messages</span>
                </a>
                <a href="{{ url('/buyer/wishlist') }}" class="buyer-header-action text-[#d92d2f]">
                    <span class="relative"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"></path></svg></span>
                    <span class="hidden text-[10px] lg:block">Wishlist</span>
                </a>
                <a href="{{ url('/buyer/cart') }}" class="buyer-header-action">
                    <span class="relative"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6"></path><circle cx="9" cy="20" r="1"></circle><circle cx="17" cy="20" r="1"></circle></svg><span class="header-count">{{ $buyer['cart_count'] }}</span></span>
                    <span class="hidden text-[10px] lg:block">Cart</span>
                </a>
                <a href="{{ url('/buyer/account') }}" class="ml-1 flex items-center gap-2 border-l border-[#ece7e0] pl-3">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-[#111] text-xs font-black text-white">{{ strtoupper(substr($buyer['first_name'], 0, 1)) }}</span>
                    <span class="hidden xl:block"><span class="block text-[11px] font-bold">{{ $buyer['first_name'] }}</span><span class="block text-[9px] text-[#a39c94]">Buyer</span></span>
                </a>
            </nav>
        </div>
    </div>
</header>

<main>
    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container py-8">
            <div class="text-[11px] text-[#9b958d]">
                <a href="{{ url('/buyer/home') }}" class="transition hover:text-[#d92d2f]">Home</a>
                <span class="mx-2">/</span>
                <span class="text-[#4d4944]">Wishlist</span>
            </div>

            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="buyer-section-eyebrow">SAVED PRODUCTS</p>
                    <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">My Wishlist</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#8b847c]">Keep track of products you love and move them to your cart whenever you're ready.</p>
                </div>
                <div class="text-xs text-[#8e877f]">{{ count($wishlistItems) }} saved item{{ count($wishlistItems) === 1 ? '' : 's' }}</div>
            </div>
        </div>
    </section>

    <section class="py-8 lg:py-12">
        <div class="likhae-container">
            @if(count($wishlistItems) > 0)
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-[#918a82]">Products may change in price or availability while saved.</p>
                    <form action="{{ url('/buyer/wishlist') }}" method="GET">
                        <select name="sort" onchange="this.form.submit()" class="h-10 border border-[#d8d1c9] bg-white px-3 text-xs font-semibold text-[#655f59] outline-none focus:border-[#d92d2f]">
                            <option value="recent">Recently Added</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="rating">Highest Rated</option>
                        </select>
                    </form>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($wishlistItems as $item)
                        @php
                            $discount = $item['old_price'] > $item['price']
                                ? round((($item['old_price'] - $item['price']) / $item['old_price']) * 100)
                                : 0;
                        @endphp

                        <article class="wishlist-card">
                            <div class="relative">
                                <a href="{{ url('/buyer/products/' . $item['slug']) }}" class="block aspect-[4/3] overflow-hidden bg-[#eee8e0]">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                                </a>

                                @if($discount > 0)
                                    <span class="discount-badge">-{{ $discount }}%</span>
                                @endif

                                <form method="POST" action="{{ url('/buyer/wishlist/' . $item['id']) }}" class="absolute right-3 top-3" onsubmit="return confirm('Remove this item from your wishlist?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="remove-heart" aria-label="Remove from wishlist" title="Remove from wishlist">♥</button>
                                </form>
                            </div>

                            <div class="p-4">
                                <a href="{{ url('/buyer/seller/' . $item['seller_slug']) }}" class="text-[9px] font-black uppercase tracking-[0.12em] text-[#8f8880] transition hover:text-[#d92d2f]">{{ $item['seller'] }}</a>

                                <a href="{{ url('/buyer/products/' . $item['slug']) }}" class="mt-2 line-clamp-2 min-h-[44px] text-sm font-black leading-5 transition hover:text-[#d92d2f]">{{ $item['name'] }}</a>
                                <p class="mt-2 text-[10px] text-[#969087]">{{ $item['variation'] }}</p>

                                <div class="mt-3 flex items-center gap-2 text-[10px]">
                                    <span class="text-[#f2a000]">★</span>
                                    <span class="font-bold">{{ number_format($item['rating'], 1) }}</span>
                                    <span class="text-[#aaa39b]">· {{ $item['sold'] }} sold</span>
                                </div>

                                <div class="mt-4 flex flex-wrap items-end gap-2">
                                    <span class="text-xl font-black text-[#d92d2f]">₱{{ number_format($item['price']) }}</span>
                                    @if($item['old_price'] > $item['price'])
                                        <span class="pb-0.5 text-[10px] text-[#aaa39b] line-through">₱{{ number_format($item['old_price']) }}</span>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    @if($item['stock'] > 0)
                                        <span class="stock-badge in-stock">IN STOCK · {{ $item['stock'] }} LEFT</span>
                                    @else
                                        <span class="stock-badge out-of-stock">OUT OF STOCK</span>
                                    @endif
                                </div>

                                <div class="mt-5 space-y-2">
                                    @if($item['stock'] > 0)
                                        <form method="POST" action="{{ url('/buyer/cart') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="wishlist-action primary w-full">Add to Cart</button>
                                        </form>

                                        <form method="POST" action="{{ url('/buyer/checkout/buy-now') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="wishlist-action secondary w-full">Buy Now</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ url('/buyer/products/' . $item['slug'] . '/notify') }}">
                                            @csrf
                                            <button type="submit" class="wishlist-action secondary w-full">Notify Me</button>
                                        </form>
                                    @endif
                                </div>

                                <div class="mt-4 border-t border-[#eee8e1] pt-3 text-[9px] text-[#aaa39b]">Saved {{ $item['added_at'] }}</div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="border border-[#dfd9d2] bg-white px-6 py-20 text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#f5f2ed] text-2xl text-[#d92d2f]">♡</div>
                    <h2 class="mt-5 text-xl font-black">Your wishlist is empty</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#918a82]">Save products you love and come back to them whenever you're ready to shop.</p>
                    <a href="{{ url('/buyer/products') }}" class="wishlist-action primary mt-6">Explore Products</a>
                </div>
            @endif
        </div>
    </section>
</main>

<footer class="mt-4 bg-[#0a0a0a] text-white">
    <div class="likhae-container py-12">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.5fr_repeat(4,1fr)]">
            <div>
                <a href="{{ url('/buyer/home') }}" class="flex items-center gap-2"><span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black">L</span><span class="text-xl font-black">LIKHAE</span></a>
                <p class="mt-4 max-w-[230px] text-sm leading-6 text-white/35">Shop More. Discover More. Live More. — Your Philippine marketplace.</p>
            </div>
            <div><h3 class="footer-title">SHOP</h3><div class="footer-links"><a href="{{ url('/buyer/products') }}">All Products</a><a href="{{ url('/buyer/flash-deals') }}">Flash Deals</a><a href="{{ url('/buyer/local-finds') }}">Local Finds</a></div></div>
            <div><h3 class="footer-title">MY ACCOUNT</h3><div class="footer-links"><a href="{{ url('/buyer/orders') }}">My Orders</a><a href="{{ url('/buyer/wishlist') }}">Wishlist</a><a href="{{ url('/buyer/messages') }}">Messages</a></div></div>
            <div><h3 class="footer-title">SUPPORT</h3><div class="footer-links"><a href="#">Help Center</a><a href="{{ url('/buyer/orders') }}">Track Order</a><a href="#">Buyer Protection</a></div></div>
            <div><h3 class="footer-title">COMPANY</h3><div class="footer-links"><a href="#">About LIKHAE</a><a href="#">Privacy Policy</a><a href="#">Terms</a></div></div>
        </div>
        <div class="mt-12 border-t border-white/10 pt-6 text-[10px] text-white/25">© {{ date('Y') }} LIKHAE, Inc. — Made with pride in the Philippines.</div>
    </div>
</footer>

</body>
</html>