<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rate & Review — LIKHAE</title>

    @vite([
        'resources/css/buyer/review.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo Review Data
    |--------------------------------------------------------------------------
    | Replace with a real completed $order later.
    */
    $buyer = [
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'cart_count' => 2,
        'message_count' => 3,
        'notification_count' => 4,
    ];

    $order = [
        'number' => 'LH-20260810-0012',
        'status' => 'Completed',
        'delivered_at' => 'August 13, 2026',
        'seller' => 'LIKHA ARTISANS',
        'seller_slug' => 'likha-artisans',
        'courier' => 'Carlo M. Dela Cruz',
        'items' => [
            [
                'id' => 11,
                'name' => 'Handwoven Rattan Tote Bag',
                'slug' => 'handwoven-rattan-tote-bag',
                'variation' => 'Natural / Standard',
                'quantity' => 1,
                'price' => 899,
                'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=800&q=90',
            ],
        ],
    ];
@endphp

{{-- =========================================================
     HEADER
========================================================= --}}
<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container">
        <div class="flex h-16 items-center gap-4">

            <a href="{{ url('/buyer/home') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">
                    L
                </span>
                <span class="text-xl font-black tracking-tight">LIKHAE</span>
            </a>

            <form action="{{ url('/buyer/products') }}" method="GET" class="hidden min-w-0 flex-1 md:flex">
                <div class="flex h-11 w-full overflow-hidden border border-[#dedad3] bg-white">
                    <input
                        type="search"
                        name="q"
                        placeholder="Search products, brands, Filipino finds..."
                        class="min-w-0 flex-1 bg-transparent px-4 text-sm outline-none placeholder:text-[#b9b4ad]"
                    >

                    <button
                        type="submit"
                        class="flex w-[108px] items-center justify-center gap-2 bg-[#d92d2f] px-4 text-sm font-bold text-white transition hover:bg-[#bd2024]"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>
                        Search
                    </button>
                </div>
            </form>

            <nav class="ml-auto flex items-center gap-1 sm:gap-2">
                <a href="{{ url('/buyer/notifications') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                            <path d="M10 21h4"></path>
                        </svg>
                        <span class="header-count">{{ $buyer['notification_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Alerts</span>
                </a>

                <a href="{{ url('/buyer/messages') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M4 5h16v11H8l-4 4V5Z"></path>
                        </svg>
                        <span class="header-count">{{ $buyer['message_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Messages</span>
                </a>

                <a href="{{ url('/buyer/cart') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6"></path>
                            <circle cx="9" cy="20" r="1"></circle>
                            <circle cx="17" cy="20" r="1"></circle>
                        </svg>
                        <span class="header-count">{{ $buyer['cart_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Cart</span>
                </a>

                <a href="{{ url('/buyer/account') }}" class="ml-1 flex items-center gap-2 border-l border-[#ece7e0] pl-3">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-[#111] text-xs font-black text-white">
                        {{ strtoupper(substr($buyer['first_name'], 0, 1)) }}
                    </span>

                    <span class="hidden xl:block">
                        <span class="block text-[11px] font-bold">{{ $buyer['first_name'] }}</span>
                        <span class="block text-[9px] text-[#a39c94]">Buyer</span>
                    </span>
                </a>
            </nav>
        </div>
    </div>
</header>

<main>

    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}
    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container py-8">
            <div class="text-[11px] text-[#9b958d]">
                <a href="{{ url('/buyer/home') }}" class="transition hover:text-[#d92d2f]">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ url('/buyer/orders') }}" class="transition hover:text-[#d92d2f]">My Orders</a>
                <span class="mx-2">/</span>
                <a href="{{ url('/buyer/orders/' . $order['number']) }}" class="transition hover:text-[#d92d2f]">
                    {{ $order['number'] }}
                </a>
                <span class="mx-2">/</span>
                <span class="text-[#4d4944]">Rate & Review</span>
            </div>

            <div class="mt-5">
                <p class="buyer-section-eyebrow">SHARE YOUR EXPERIENCE</p>

                <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">
                    Rate & Review
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#8b847c]">
                    Tell other LIKHAE buyers about your purchase. Your review helps sellers improve and helps other shoppers decide.
                </p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         REVIEW FORM
    ====================================================== --}}
    <section class="py-8 lg:py-12">
        <div class="likhae-container">
            <form
                id="reviewForm"
                method="POST"
                action="{{ url('/buyer/orders/' . $order['number'] . '/review') }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="mx-auto grid max-w-5xl gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">

                    {{-- LEFT --}}
                    <div class="space-y-5">

                        {{-- Order Info --}}
                        <section class="review-card">
                            <div class="review-card-header">
                                <div>
                                    <p class="review-eyebrow">ORDER</p>
                                    <h2>{{ $order['number'] }}</h2>
                                </div>

                                <span class="completed-badge">
                                    COMPLETED
                                </span>
                            </div>

                            <div class="grid gap-4 border-t border-[#e9e3dc] p-5 text-xs sm:grid-cols-2 sm:p-6">
                                <div>
                                    <p class="review-eyebrow">SELLER</p>
                                    <p class="mt-1 font-bold text-[#4f4a45]">
                                        {{ $order['seller'] }}
                                    </p>
                                </div>

                                <div>
                                    <p class="review-eyebrow">DELIVERED</p>
                                    <p class="mt-1 font-bold text-[#4f4a45]">
                                        {{ $order['delivered_at'] }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        {{-- Product Reviews --}}
                        @foreach($order['items'] as $item)
                            <section class="review-card">
                                <div class="grid gap-4 border-b border-[#e9e3dc] p-5 sm:grid-cols-[86px_minmax(0,1fr)] sm:items-center sm:p-6">
                                    <a
                                        href="{{ url('/buyer/products/' . $item['slug']) }}"
                                        class="block aspect-square overflow-hidden bg-[#eee8e0]"
                                    >
                                        <img
                                            src="{{ $item['image'] }}"
                                            alt="{{ $item['name'] }}"
                                            class="h-full w-full object-cover"
                                        >
                                    </a>

                                    <div class="min-w-0">
                                        <p class="review-eyebrow">PRODUCT</p>

                                        <a
                                            href="{{ url('/buyer/products/' . $item['slug']) }}"
                                            class="mt-1 block text-sm font-black leading-6 transition hover:text-[#d92d2f]"
                                        >
                                            {{ $item['name'] }}
                                        </a>

                                        <p class="mt-1 text-[10px] text-[#918a82]">
                                            {{ $item['variation'] }} · Qty {{ $item['quantity'] }}
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-7 p-5 sm:p-6">

                                    {{-- Product rating --}}
                                    <div>
                                        <label class="review-field-label">
                                            Product Rating <span>*</span>
                                        </label>

                                        <div
                                            class="star-rating mt-3"
                                            data-name="product_ratings[{{ $item['id'] }}]"
                                        >
                                            @for($star = 1; $star <= 5; $star++)
                                                <button
                                                    type="button"
                                                    class="star-button"
                                                    data-value="{{ $star }}"
                                                    aria-label="{{ $star }} star{{ $star > 1 ? 's' : '' }}"
                                                >
                                                    ★
                                                </button>
                                            @endfor

                                            <input
                                                type="hidden"
                                                name="product_ratings[{{ $item['id'] }}]"
                                                value=""
                                                class="rating-value"
                                                required
                                            >
                                        </div>

                                        <p class="rating-text mt-2 text-[10px] text-[#9a938b]">
                                            Select your rating.
                                        </p>
                                    </div>

                                    {{-- Review --}}
                                    <div>
                                        <label
                                            for="review_{{ $item['id'] }}"
                                            class="review-field-label"
                                        >
                                            Product Review <span>*</span>
                                        </label>

                                        <textarea
                                            id="review_{{ $item['id'] }}"
                                            name="reviews[{{ $item['id'] }}]"
                                            rows="5"
                                            maxlength="1000"
                                            required
                                            placeholder="How was the quality, appearance, packaging, and overall experience?"
                                            class="review-textarea"
                                        ></textarea>

                                        <div class="mt-2 flex justify-between gap-3 text-[9px] text-[#a09991]">
                                            <span>Keep your review respectful and useful.</span>
                                            <span class="char-count">0 / 1000</span>
                                        </div>
                                    </div>

                                    {{-- Photo upload --}}
                                    <div>
                                        <label class="review-field-label">
                                            Add Photos
                                            <span class="font-normal text-[#aaa39b]">(Optional)</span>
                                        </label>

                                        <label
                                            for="photos_{{ $item['id'] }}"
                                            class="photo-upload mt-3"
                                        >
                                            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6">
                                                <path d="M4 5h16v14H4z"></path>
                                                <path d="m7 15 3-3 2 2 2-2 3 3"></path>
                                                <circle cx="9" cy="9" r="1.5"></circle>
                                            </svg>

                                            <span>
                                                <strong>Upload product photos</strong>
                                                <small>JPG or PNG, up to 5 images</small>
                                            </span>
                                        </label>

                                        <input
                                            id="photos_{{ $item['id'] }}"
                                            name="review_photos[{{ $item['id'] }}][]"
                                            type="file"
                                            accept="image/jpeg,image/png"
                                            multiple
                                            class="sr-only review-photo-input"
                                        >

                                        <div class="photo-file-list mt-3 hidden"></div>
                                    </div>
                                </div>
                            </section>
                        @endforeach

                        {{-- Seller Review --}}
                        <section class="review-card">
                            <div class="review-card-header">
                                <div>
                                    <p class="review-eyebrow">SELLER EXPERIENCE</p>
                                    <h2>Rate {{ $order['seller'] }}</h2>
                                </div>
                            </div>

                            <div class="space-y-6 border-t border-[#e9e3dc] p-5 sm:p-6">
                                <div>
                                    <label class="review-field-label">
                                        Seller Service Rating <span>*</span>
                                    </label>

                                    <div class="star-rating mt-3" data-name="seller_rating">
                                        @for($star = 1; $star <= 5; $star++)
                                            <button
                                                type="button"
                                                class="star-button"
                                                data-value="{{ $star }}"
                                                aria-label="{{ $star }} star{{ $star > 1 ? 's' : '' }}"
                                            >
                                                ★
                                            </button>
                                        @endfor

                                        <input
                                            type="hidden"
                                            name="seller_rating"
                                            value=""
                                            class="rating-value"
                                            required
                                        >
                                    </div>

                                    <p class="rating-text mt-2 text-[10px] text-[#9a938b]">
                                        Rate seller communication, packaging, and service.
                                    </p>
                                </div>

                                <div>
                                    <label for="seller_feedback" class="review-field-label">
                                        Seller Feedback
                                        <span class="font-normal text-[#aaa39b]">(Optional)</span>
                                    </label>

                                    <textarea
                                        id="seller_feedback"
                                        name="seller_feedback"
                                        rows="4"
                                        maxlength="700"
                                        placeholder="How was your experience with the seller?"
                                        class="review-textarea"
                                    ></textarea>
                                </div>
                            </div>
                        </section>

                        {{-- Delivery Review --}}
                        <section class="review-card">
                            <div class="review-card-header">
                                <div>
                                    <p class="review-eyebrow">DELIVERY EXPERIENCE</p>
                                    <h2>Rate Your Delivery</h2>
                                </div>
                            </div>

                            <div class="space-y-6 border-t border-[#e9e3dc] p-5 sm:p-6">
                                <div class="flex items-center gap-3 border border-[#e3ddd5] bg-[#faf8f5] p-4">
                                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#111] text-xs font-black text-white">
                                        CM
                                    </div>

                                    <div>
                                        <p class="text-xs font-black">
                                            {{ $order['courier'] }}
                                        </p>
                                        <p class="mt-1 text-[10px] text-[#9a938b]">
                                            Assigned courier
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label class="review-field-label">
                                        Delivery Rating <span>*</span>
                                    </label>

                                    <div class="star-rating mt-3" data-name="delivery_rating">
                                        @for($star = 1; $star <= 5; $star++)
                                            <button
                                                type="button"
                                                class="star-button"
                                                data-value="{{ $star }}"
                                                aria-label="{{ $star }} star{{ $star > 1 ? 's' : '' }}"
                                            >
                                                ★
                                            </button>
                                        @endfor

                                        <input
                                            type="hidden"
                                            name="delivery_rating"
                                            value=""
                                            class="rating-value"
                                            required
                                        >
                                    </div>

                                    <p class="rating-text mt-2 text-[10px] text-[#9a938b]">
                                        Rate delivery speed, handling, and courier service.
                                    </p>
                                </div>

                                <div>
                                    <label for="delivery_feedback" class="review-field-label">
                                        Delivery Feedback
                                        <span class="font-normal text-[#aaa39b]">(Optional)</span>
                                    </label>

                                    <textarea
                                        id="delivery_feedback"
                                        name="delivery_feedback"
                                        rows="4"
                                        maxlength="700"
                                        placeholder="Share any feedback about the delivery experience."
                                        class="review-textarea"
                                    ></textarea>
                                </div>
                            </div>
                        </section>
                    </div>

                    {{-- RIGHT --}}
                    <aside>
                        <div class="sticky top-[88px] space-y-4">

                            {{-- Review Summary --}}
                            <section class="review-card p-5">
                                <p class="review-eyebrow">BEFORE SUBMITTING</p>

                                <h2 class="mt-2 text-base font-black">
                                    Review Guidelines
                                </h2>

                                <ul class="mt-4 space-y-3 text-[11px] leading-5 text-[#7d766f]">
                                    <li class="flex gap-2">
                                        <span class="text-[#079b72]">✓</span>
                                        <span>Share your actual experience with the product.</span>
                                    </li>

                                    <li class="flex gap-2">
                                        <span class="text-[#079b72]">✓</span>
                                        <span>Keep feedback respectful and relevant.</span>
                                    </li>

                                    <li class="flex gap-2">
                                        <span class="text-[#079b72]">✓</span>
                                        <span>Do not include personal contact information.</span>
                                    </li>

                                    <li class="flex gap-2">
                                        <span class="text-[#079b72]">✓</span>
                                        <span>Only upload photos related to the purchased product.</span>
                                    </li>
                                </ul>
                            </section>

                            {{-- Submit --}}
                            <section class="review-card p-5">
                                <label class="flex cursor-pointer items-start gap-3 text-xs leading-5 text-[#777068]">
                                    <input
                                        id="reviewConfirmation"
                                        type="checkbox"
                                        name="confirm_review"
                                        value="1"
                                        required
                                        class="mt-0.5 h-4 w-4 shrink-0 accent-[#d92d2f]"
                                    >

                                    <span>
                                        I confirm that this review reflects my genuine experience with this order.
                                    </span>
                                </label>

                                <button
                                    id="submitReviewButton"
                                    type="submit"
                                    disabled
                                    class="mt-6 flex h-12 w-full items-center justify-center gap-2 bg-[#d92d2f] px-5 text-sm font-bold text-white transition hover:bg-[#bd2024] disabled:cursor-not-allowed disabled:bg-[#c9c3bc]"
                                >
                                    Submit Review
                                    <span>→</span>
                                </button>

                                <a
                                    href="{{ url('/buyer/orders/' . $order['number']) }}"
                                    class="mt-2 flex h-11 w-full items-center justify-center border border-[#d8d1c9] bg-white text-xs font-bold text-[#5f5953] transition hover:border-[#d92d2f] hover:text-[#d92d2f]"
                                >
                                    Back to Order
                                </a>
                            </section>

                            {{-- Privacy --}}
                            <section class="border border-[#dfd9d2] bg-[#faf8f5] p-4">
                                <div class="flex gap-3">
                                    <span class="mt-0.5 text-[#079b72]">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"></path>
                                        </svg>
                                    </span>

                                    <div>
                                        <p class="text-[11px] font-bold">
                                            Review Privacy
                                        </p>

                                        <p class="mt-1 text-[10px] leading-5 text-[#9b948c]">
                                            Public reviews should display only the buyer name format allowed by your marketplace policy.
                                        </p>
                                    </div>
                                </div>
                            </section>

                        </div>
                    </aside>
                </div>
            </form>
        </div>
    </section>
</main>

{{-- =========================================================
     FOOTER
========================================================= --}}
<footer class="mt-4 bg-[#0a0a0a] text-white">
    <div class="likhae-container py-12">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.5fr_repeat(4,1fr)]">
            <div>
                <a href="{{ url('/buyer/home') }}" class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black">L</span>
                    <span class="text-xl font-black">LIKHAE</span>
                </a>

                <p class="mt-4 max-w-[230px] text-sm leading-6 text-white/35">
                    Shop More. Discover More. Live More. — Your Philippine marketplace.
                </p>
            </div>

            <div>
                <h3 class="footer-title">SHOP</h3>

                <div class="footer-links">
                    <a href="{{ url('/buyer/products') }}">All Products</a>
                    <a href="{{ url('/buyer/flash-deals') }}">Flash Deals</a>
                    <a href="{{ url('/buyer/local-finds') }}">Local Finds</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">MY ACCOUNT</h3>

                <div class="footer-links">
                    <a href="{{ url('/buyer/orders') }}">My Orders</a>
                    <a href="{{ url('/buyer/wishlist') }}">Wishlist</a>
                    <a href="{{ url('/buyer/messages') }}">Messages</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">SUPPORT</h3>

                <div class="footer-links">
                    <a href="#">Help Center</a>
                    <a href="{{ url('/buyer/orders') }}">Track Order</a>
                    <a href="#">Buyer Protection</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">COMPANY</h3>

                <div class="footer-links">
                    <a href="#">About LIKHAE</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms</a>
                </div>
            </div>
        </div>

        <div class="mt-12 border-t border-white/10 pt-6 text-[10px] text-white/25">
            © {{ date('Y') }} LIKHAE, Inc. — Made with pride in the Philippines.
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ratingGroups = document.querySelectorAll('.star-rating');

        const ratingLabels = {
            1: 'Poor',
            2: 'Fair',
            3: 'Good',
            4: 'Very Good',
            5: 'Excellent',
        };

        ratingGroups.forEach(function (group) {
            const buttons = Array.from(group.querySelectorAll('.star-button'));
            const input = group.querySelector('.rating-value');
            const text = group.parentElement.querySelector('.rating-text');

            function paint(value) {
                buttons.forEach(function (button) {
                    const starValue = parseInt(button.dataset.value, 10);
                    button.classList.toggle('is-active', starValue <= value);
                });
            }

            buttons.forEach(function (button) {
                button.addEventListener('mouseenter', function () {
                    paint(parseInt(button.dataset.value, 10));
                });

                button.addEventListener('click', function () {
                    const value = parseInt(button.dataset.value, 10);

                    input.value = value;
                    paint(value);

                    if (text) {
                        text.textContent = value + '/5 — ' + ratingLabels[value];
                    }
                });
            });

            group.addEventListener('mouseleave', function () {
                paint(parseInt(input.value || '0', 10));
            });
        });

        document.querySelectorAll('.review-textarea').forEach(function (textarea) {
            const wrapper = textarea.parentElement;
            const counter = wrapper.querySelector('.char-count');

            if (!counter) return;

            function updateCount() {
                counter.textContent =
                    textarea.value.length + ' / ' + (textarea.maxLength || 1000);
            }

            textarea.addEventListener('input', updateCount);
            updateCount();
        });

        document.querySelectorAll('.review-photo-input').forEach(function (input) {
            input.addEventListener('change', function () {
                const fileList = input.parentElement.querySelector('.photo-file-list');

                if (!fileList) return;

                const files = Array.from(input.files || []).slice(0, 5);

                if (!files.length) {
                    fileList.innerHTML = '';
                    fileList.classList.add('hidden');
                    return;
                }

                fileList.classList.remove('hidden');
                fileList.innerHTML = files.map(function (file) {
                    return '<span class="photo-file-chip">' +
                        file.name.replace(/[<>&"]/g, '') +
                        '</span>';
                }).join('');
            });
        });

        const confirmation = document.getElementById('reviewConfirmation');
        const submitButton = document.getElementById('submitReviewButton');

        confirmation.addEventListener('change', function () {
            submitButton.disabled = !confirmation.checked;
        });
    });
</script>

</body>
</html>