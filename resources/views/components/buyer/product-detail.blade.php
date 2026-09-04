@props(['product', 'products' => collect(), 'guest' => false])

@php
    $slug = data_get($product, 'slug', data_get($product, 'id', 'product'));
    $name = (string) data_get($product, 'name', 'Product');
    $category = (string) data_get($product, 'category', 'Local Find');
    $seller = (string) data_get($product, 'seller', 'LIKHAE Seller');
    $sellerSlug = data_get($product, 'seller_slug', \Illuminate\Support\Str::slug($seller));
    $sellerAvatar = data_get($product, 'seller_avatar', 'https://ui-avatars.com/api/?name='.urlencode($seller).'&background=7f1d1d&color=fff');
    $images = collect(data_get($product, 'gallery', []))->filter()->values();
    if ($images->isEmpty()) $images = collect([data_get($product, 'image')]);
    $price = (float) data_get($product, 'price', 0);
    $oldPrice = (float) data_get($product, 'old_price', 0);
    $discount = $oldPrice > $price ? (int) round((($oldPrice - $price) / $oldPrice) * 100) : (int) data_get($product, 'discount', 0);
    $rating = (float) data_get($product, 'rating', 0);
    $reviews = collect(data_get($product, 'customer_reviews', []));
    $reviewCount = (int) data_get($product, 'reviews', $reviews->count());
    $stock = (int) data_get($product, 'stock', 0);
    $specs = collect(data_get($product, 'specs', []));
    $variations = collect(data_get($product, 'variations', []));
    $storeUrl = $guest ? route('home') : route('buyer.shop', ['seller' => $sellerSlug]);
    $related = collect($products)->where('category', $category)->where('slug', '!=', $slug)->take(4);
    if ($related->count() < 4) $related = collect($products)->where('slug', '!=', $slug)->take(4);
@endphp

<div class="lk-page">
    <div class="mb-4 flex flex-wrap items-center gap-2 text-xs text-stone-500">
        <a class="hover:text-red-800" href="{{ $guest ? route('home') : route('buyer.products') }}">Products</a>
        <span>/</span><span>{{ $category }}</span><span>/</span><strong class="text-stone-700">{{ $name }}</strong>
    </div>

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(360px,.9fr)]">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm" data-product-gallery>
            <div class="aspect-square overflow-hidden rounded-xl bg-stone-50">
                <img class="h-full w-full object-cover" src="{{ $images->first() }}" alt="{{ $name }}" data-gallery-main>
            </div>
            @if($images->count() > 1)
                <div class="mt-3 grid grid-cols-5 gap-2">
                    @foreach($images->take(5) as $index => $galleryImage)
                        <button type="button" class="overflow-hidden rounded-lg border-2 {{ $index === 0 ? 'border-red-800' : 'border-transparent' }} bg-stone-50" data-gallery-thumb data-image="{{ $galleryImage }}" aria-label="View image {{ $index + 1 }}">
                            <img class="aspect-square h-full w-full object-cover" src="{{ $galleryImage }}" alt="{{ $name }} view {{ $index + 1 }}">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-red-800">{{ $category }}</span>
                @if($discount > 0)<span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-800">Save {{ $discount }}%</span>@endif
            </div>
            <h1 class="mt-3 text-2xl font-bold leading-tight text-stone-950 sm:text-3xl">{{ $name }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs">
                <strong class="flex items-center gap-1 text-amber-600">★ {{ number_format($rating, 1) }}</strong>
                <a class="border-l border-stone-200 pl-3 text-stone-600 hover:text-red-800" href="#reviews">{{ number_format($reviewCount) }} reviews</a>
                <span class="border-l border-stone-200 pl-3 text-stone-600">{{ number_format((int) data_get($product, 'sold', 0)) }} sold</span>
            </div>
            <div class="mt-5 rounded-xl bg-stone-50 p-4">
                <div class="flex flex-wrap items-end gap-3"><strong class="text-3xl text-red-900">₱{{ number_format($price, 2) }}</strong>@if($oldPrice > $price)<del class="pb-1 text-sm text-stone-400">₱{{ number_format($oldPrice, 2) }}</del>@endif</div>
                <p class="mt-1 text-xs text-stone-500">VAT included · Secure LIKHAE checkout</p>
            </div>

            <div class="mt-5 space-y-4">
                @foreach($variations as $variationName => $options)
                    <div><span class="mb-2 block text-xs font-semibold text-stone-700">{{ $variationName }}</span><div class="flex flex-wrap gap-2" data-variation-group>@foreach((array) $options as $option)<button type="button" class="rounded-lg border px-3 py-2 text-xs font-medium transition hover:border-red-800 hover:text-red-800 {{ $loop->first ? 'border-red-800 bg-red-50 text-red-900' : 'border-stone-300 text-stone-700' }}" data-variation-option aria-pressed="{{ $loop->first ? 'true' : 'false' }}">{{ $option }}</button>@endforeach</div></div>
                @endforeach
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-stone-100 pt-4">
                    <div><span class="block text-xs font-semibold text-stone-700">Quantity</span><span class="text-[11px] text-stone-500">{{ $stock }} available</span></div>
                    <div class="flex items-center overflow-hidden rounded-lg border border-stone-300" data-quantity-control><button type="button" class="h-9 w-9 text-stone-600 hover:bg-stone-50" data-quantity-minus>−</button><input id="detailQuantity" class="h-9 w-12 border-x border-stone-300 text-center text-sm outline-none" type="number" value="1" min="1" max="{{ max(1, $stock) }}" data-quantity-input><button type="button" class="h-9 w-9 text-stone-600 hover:bg-stone-50" data-quantity-plus>+</button></div>
                </div>
            </div>

            <div class="mt-5 grid gap-2 sm:grid-cols-2">
                @if($guest)
                    <button type="button" class="lk-btn lk-btn-light lk-btn-full" data-auth-required data-auth-message="Sign in to add {{ $name }} to your cart.">Add to Cart</button>
                    <button type="button" class="lk-btn lk-btn-red lk-btn-full" data-auth-required data-auth-message="Create a Buyer account or sign in to purchase this product.">Buy Now</button>
                    <button type="button" class="lk-btn lk-btn-light lk-btn-full sm:col-span-2" data-auth-required data-auth-message="Sign in to save products to your wishlist.">♡ Save to Wishlist</button>
                @else
                    <button type="button" class="lk-btn lk-btn-light lk-btn-full" data-add-cart data-product-id="{{ $slug }}" data-quantity-source="#detailQuantity">Add to Cart</button>
                    <a class="lk-btn lk-btn-red lk-btn-full" href="{{ route('buyer.cart', ['buy_now' => $slug]) }}">Buy Now</a>
                    <button type="button" class="lk-btn lk-btn-light lk-btn-full sm:col-span-2" data-wishlist data-product-id="{{ $slug }}">♡ Save to Wishlist</button>
                @endif
            </div>

            <div class="mt-5 grid gap-2 rounded-xl border border-stone-200 p-4 text-xs text-stone-600 sm:grid-cols-2">
                <div><strong class="block text-stone-800">Delivery</strong>{{ data_get($product, 'shipping', 'Metro Manila: 1–3 days') }}</div>
                <div><strong class="block text-stone-800">Buyer protection</strong>Payment held until receipt</div>
            </div>
        </div>
    </section>

    <section class="mt-5 flex flex-col gap-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center">
        <img class="h-16 w-16 rounded-xl object-cover" src="{{ $sellerAvatar }}" alt="{{ $seller }}">
        <div class="min-w-0 flex-1"><span class="text-[10px] font-bold uppercase tracking-widest text-red-800">Official seller</span><h2 class="truncate text-lg font-bold text-stone-900">{{ $seller }}</h2><p class="text-xs text-stone-500">{{ data_get($product, 'location', 'Philippines') }} · {{ data_get($product, 'seller_rating', '4.8') }} seller rating · {{ data_get($product, 'seller_products', '24') }} products</p></div>
        <div class="flex gap-2"><a href="{{ $storeUrl }}" class="lk-btn lk-btn-light">View Store</a>@if($guest)<button class="lk-btn lk-btn-red" type="button" data-auth-required data-auth-message="Sign in to message this seller.">Message</button>@else<a href="{{ route('buyer.messages', ['seller' => $sellerSlug, 'product' => $slug]) }}" class="lk-btn lk-btn-red">Message</a>@endif</div>
    </section>

    <div class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
        <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-bold text-stone-900">Description</h2>
            <div class="mt-3 whitespace-pre-line text-sm leading-7 text-stone-600">{{ data_get($product, 'description', 'A carefully selected product from a trusted LIKHAE seller. Built for everyday use and covered by marketplace buyer protection.') }}</div>
        </section>
        <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-bold text-stone-900">Specifications</h2>
            <dl class="mt-3 divide-y divide-stone-100 text-xs">@forelse($specs as $label => $value)<div class="grid grid-cols-[110px_1fr] gap-3 py-2.5"><dt class="text-stone-500">{{ $label }}</dt><dd class="font-medium text-stone-800">{{ $value }}</dd></div>@empty<div class="py-3 text-stone-500">Seller has not added specifications yet.</div>@endforelse</dl>
        </section>
    </div>

    <section id="reviews" class="mt-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-stone-100 pb-5"><div><span class="text-[10px] font-bold uppercase tracking-widest text-red-800">Customer feedback</span><h2 class="text-lg font-bold text-stone-900">Ratings & Reviews</h2></div><div class="text-right"><strong class="text-3xl text-red-900">{{ number_format($rating, 1) }}</strong><span class="text-sm text-stone-400"> / 5</span><div class="text-xs text-amber-500">★★★★★</div><small class="text-stone-500">{{ number_format($reviewCount) }} verified ratings</small></div></div>
        <div class="divide-y divide-stone-100">
            @forelse($reviews as $review)
                <article class="py-5"><div class="flex items-start gap-3"><img class="h-10 w-10 rounded-full object-cover" src="{{ data_get($review, 'avatar', 'https://ui-avatars.com/api/?name='.urlencode(data_get($review,'name','Buyer')).'&background=e7e5e4&color=44403c') }}" alt=""><div class="min-w-0 flex-1"><div class="flex flex-wrap items-center justify-between gap-2"><div><strong class="text-sm text-stone-900">{{ data_get($review, 'name', 'Verified Buyer') }}</strong><div class="text-xs text-amber-500">{{ str_repeat('★', (int) data_get($review, 'rating', 5)) }}<span class="text-stone-300">{{ str_repeat('★', 5 - (int) data_get($review, 'rating', 5)) }}</span></div></div><time class="text-[11px] text-stone-400">{{ data_get($review, 'date', 'Recently') }}</time></div><p class="mt-2 text-sm leading-6 text-stone-600">{{ data_get($review, 'comment') }}</p>@if(data_get($review, 'variant'))<span class="mt-2 inline-block text-[11px] text-stone-400">Variation: {{ data_get($review, 'variant') }}</span>@endif</div></div></article>
            @empty
                <div class="py-10 text-center text-sm text-stone-500">No written reviews yet.</div>
            @endforelse
        </div>
    </section>

    @if($related->isNotEmpty())
        <section class="lk-section"><div class="lk-section-head"><div><span class="lk-kicker">Keep exploring</span><h2>Related Products</h2><p>More finds you may like.</p></div></div><div class="lk-product-grid">@foreach($related as $relatedProduct)<x-buyer.product-card :product="$relatedProduct" :guest="$guest" />@endforeach</div></section>
    @endif
</div>
