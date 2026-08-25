<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>My Cart — LIKHAE</title>

    @vite([
        'resources/css/buyer/cart.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo Buyer / Cart Data
    |--------------------------------------------------------------------------
    | Replace these arrays with your CartController data later.
    */
    $buyer = [
        'name' => auth()->check() ? auth()->user()->name : 'Juan Dela Cruz',
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'cart_count' => 3,
        'message_count' => 2,
        'notification_count' => 4,
    ];

    $categories = [
        'Electronics',
        'Fashion',
        'Home & Living',
        'Beauty & Care',
        'Food & Grocery',
        'Sports',
        'Books',
        'Toys & Games',
        'Health & Wellness',
        'Automotive',
    ];

    $cartGroups = [
        [
            'seller' => 'TECHHUB PH',
            'seller_slug' => 'techhub-ph',
            'items' => [
                [
                    'id' => 1,
                    'product_id' => 1,
                    'name' => 'Baseus Wireless Earbuds A3i Pro',
                    'slug' => 'baseus-wireless-earbuds-a3i-pro',
                    'price' => 599,
                    'old_price' => 1299,
                    'quantity' => 1,
                    'stock' => 45,
                    'color' => 'Black',
                    'variation' => 'Standard',
                    'free_shipping' => true,
                    'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=90',
                ],
                [
                    'id' => 2,
                    'product_id' => 3,
                    'name' => 'Mechanical Keyboard TKL RGB',
                    'slug' => 'mechanical-keyboard-tkl-rgb',
                    'price' => 1799,
                    'old_price' => 3499,
                    'quantity' => 1,
                    'stock' => 28,
                    'color' => 'Black',
                    'variation' => 'Red Switch',
                    'free_shipping' => true,
                    'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=900&q=90',
                ],
            ],
        ],
        [
            'seller' => 'STYLE MANILA',
            'seller_slug' => 'style-manila',
            'items' => [
                [
                    'id' => 3,
                    'product_id' => 4,
                    'name' => 'Premium Cotton Polo Shirt',
                    'slug' => 'premium-cotton-polo-shirt',
                    'price' => 249,
                    'old_price' => 599,
                    'quantity' => 2,
                    'stock' => 76,
                    'color' => 'White',
                    'variation' => 'Large',
                    'free_shipping' => false,
                    'image' => 'https://images.unsplash.com/photo-1603252109303-2751441dd157?auto=format&fit=crop&w=900&q=90',
                ],
            ],
        ],
    ];

    $subtotal = 0;

    foreach ($cartGroups as $group) {
        foreach ($group['items'] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
    }

    $shipping = 60;
    $discount = 0;
    $total = $subtotal + $shipping - $discount;
@endphp

{{-- =========================================================
     BUYER HEADER
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
                    <select
                        name="category"
                        class="w-[100px] border-r border-[#dedad3] bg-white px-3 text-xs text-[#5d5a55] outline-none"
                    >
                        <option value="">All</option>

                        @foreach($categories as $category)
                            <option value="{{ \Illuminate\Support\Str::slug($category) }}">
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>

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
                        @if($buyer['notification_count'] > 0)
                            <span class="header-count">{{ $buyer['notification_count'] }}</span>
                        @endif
                    </span>
                    <span class="hidden text-[10px] lg:block">Alerts</span>
                </a>

                <a href="{{ url('/buyer/messages') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M4 5h16v11H8l-4 4V5Z"></path>
                        </svg>
                        @if($buyer['message_count'] > 0)
                            <span class="header-count">{{ $buyer['message_count'] }}</span>
                        @endif
                    </span>
                    <span class="hidden text-[10px] lg:block">Messages</span>
                </a>

                <a href="{{ url('/buyer/wishlist') }}" class="buyer-header-action">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 5.9l-1.3-1.3a5.3 5.3 0 1 0-7.5 7.5L12 21l8.8-8.9a5.3 5.3 0 0 0 0-7.5Z"></path>
                    </svg>
                    <span class="hidden text-[10px] lg:block">Wishlist</span>
                </a>

                <a href="{{ url('/buyer/cart') }}" class="buyer-header-action text-[#d92d2f]">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6"></path>
                            <circle cx="9" cy="20" r="1"></circle>
                            <circle cx="17" cy="20" r="1"></circle>
                        </svg>
                        @if($buyer['cart_count'] > 0)
                            <span class="header-count">{{ $buyer['cart_count'] }}</span>
                        @endif
                    </span>
                    <span class="hidden text-[10px] lg:block">Cart</span>
                </a>

                <a href="{{ url('/buyer/account') }}" class="ml-1 flex items-center gap-2 border-l border-[#ece7e0] pl-3">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-[#111] text-xs font-black text-white">
                        {{ strtoupper(substr($buyer['first_name'], 0, 1)) }}
                    </span>

                    <span class="hidden xl:block">
                        <span class="block max-w-[100px] truncate text-[11px] font-bold">
                            {{ $buyer['first_name'] }}
                        </span>
                        <span class="block text-[9px] text-[#a39c94]">Buyer</span>
                    </span>
                </a>
            </nav>
        </div>

        <form action="{{ url('/buyer/products') }}" method="GET" class="pb-3 md:hidden">
            <div class="flex h-10 overflow-hidden border border-[#dedad3] bg-white">
                <input
                    type="search"
                    name="q"
                    placeholder="Search LIKHAE..."
                    class="min-w-0 flex-1 px-3 text-sm outline-none"
                >

                <button type="submit" class="w-12 bg-[#d92d2f] text-white" aria-label="Search">
                    <svg class="mx-auto h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <div class="border-t border-[#ece8e1]">
        <div class="likhae-container flex h-10 items-center gap-7 overflow-x-auto whitespace-nowrap text-xs text-[#4e4a45] hide-scrollbar">
            @foreach($categories as $category)
                <a
                    href="{{ url('/buyer/products?category=' . \Illuminate\Support\Str::slug($category)) }}"
                    class="transition hover:text-[#d92d2f]"
                >
                    {{ $category }}
                </a>
            @endforeach

            <a
                href="{{ url('/buyer/flash-deals') }}"
                class="ml-auto border-b border-[#d92d2f] px-4 py-[13px] font-bold text-[#d92d2f]"
            >
                ⚡ Flash Deals
            </a>
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
                <a href="{{ url('/buyer/home') }}" class="transition hover:text-[#d92d2f]">
                    Home
                </a>

                <span class="mx-2">/</span>

                <span class="text-[#4d4944]">
                    My Cart
                </span>
            </div>

            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="buyer-section-eyebrow">SHOPPING CART</p>

                    <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">
                        My Cart
                    </h1>

                    <p class="mt-2 text-sm text-[#8b847c]">
                        Review your selected items before checkout.
                    </p>
                </div>

                <a
                    href="{{ url('/buyer/products') }}"
                    class="text-xs font-bold text-[#d92d2f] transition hover:text-[#aa1f22]"
                >
                    Continue Shopping →
                </a>
            </div>
        </div>
    </section>

    {{-- =====================================================
         CART
    ====================================================== --}}
    <section class="py-8 lg:py-12">
        <div class="likhae-container">
            <form
                id="cartForm"
                action="{{ url('/buyer/checkout') }}"
                method="POST"
            >
                @csrf

                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_340px]">

                    {{-- =============================================
                         LEFT: CART ITEMS
                    ============================================== --}}
                    <div class="min-w-0">

                        {{-- Select all --}}
                        <div class="mb-4 flex items-center justify-between gap-4 border border-[#dfd9d2] bg-white px-4 py-4 sm:px-5">
                            <label class="flex cursor-pointer items-center gap-3">
                                <input
                                    id="selectAll"
                                    type="checkbox"
                                    class="h-4 w-4 accent-[#d92d2f]"
                                    checked
                                >

                                <span class="text-sm font-bold">
                                    Select All Items
                                </span>
                            </label>

                            <span class="text-xs text-[#9a938b]">
                                {{ collect($cartGroups)->sum(fn($group) => count($group['items'])) }} items
                            </span>
                        </div>

                        {{-- Seller groups --}}
                        <div class="space-y-5">
                            @foreach($cartGroups as $groupIndex => $group)
                                <section class="border border-[#dfd9d2] bg-white">
                                    {{-- Seller --}}
                                    <div class="flex items-center justify-between gap-4 border-b border-[#ebe5de] px-4 py-4 sm:px-5">
                                        <label class="flex cursor-pointer items-center gap-3">
                                            <input
                                                type="checkbox"
                                                class="seller-checkbox h-4 w-4 accent-[#d92d2f]"
                                                data-group="{{ $groupIndex }}"
                                                checked
                                            >

                                            <span class="flex items-center gap-2">
                                                <span class="grid h-8 w-8 place-items-center border border-[#ddd7d0] bg-[#faf8f5] text-[#8d867e]">
                                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path d="M4 10h16v10H4z"></path>
                                                        <path d="m5 10 1-5h12l1 5"></path>
                                                        <path d="M9 14h6"></path>
                                                    </svg>
                                                </span>

                                                <span class="text-xs font-black uppercase tracking-[0.08em]">
                                                    {{ $group['seller'] }}
                                                </span>
                                            </span>
                                        </label>

                                        <a
                                            href="{{ url('/buyer/seller/' . $group['seller_slug']) }}"
                                            class="text-[10px] font-bold text-[#d92d2f]"
                                        >
                                            Visit Shop →
                                        </a>
                                    </div>

                                    {{-- Items --}}
                                    @foreach($group['items'] as $item)
                                        <article
                                            class="cart-item border-b border-[#eee8e1] p-4 last:border-b-0 sm:p-5"
                                            data-price="{{ $item['price'] }}"
                                            data-item="{{ $item['id'] }}"
                                            data-group="{{ $groupIndex }}"
                                        >
                                            <div class="grid gap-4 sm:grid-cols-[auto_110px_minmax(0,1fr)] lg:grid-cols-[auto_120px_minmax(0,1fr)]">
                                                {{-- Checkbox --}}
                                                <div class="pt-2">
                                                    <input
                                                        type="checkbox"
                                                        name="selected_items[]"
                                                        value="{{ $item['id'] }}"
                                                        class="item-checkbox h-4 w-4 accent-[#d92d2f]"
                                                        data-group="{{ $groupIndex }}"
                                                        checked
                                                    >
                                                </div>

                                                {{-- Image --}}
                                                <a
                                                    href="{{ url('/buyer/products/' . $item['slug']) }}"
                                                    class="block aspect-square overflow-hidden bg-[#eee8e0]"
                                                >
                                                    <img
                                                        src="{{ $item['image'] }}"
                                                        alt="{{ $item['name'] }}"
                                                        class="h-full w-full object-cover transition duration-500 hover:scale-105"
                                                    >
                                                </a>

                                                {{-- Info --}}
                                                <div class="min-w-0">
                                                    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_150px_115px] xl:items-start">
                                                        <div class="min-w-0">
                                                            <a
                                                                href="{{ url('/buyer/products/' . $item['slug']) }}"
                                                                class="block text-sm font-bold leading-6 transition hover:text-[#d92d2f]"
                                                            >
                                                                {{ $item['name'] }}
                                                            </a>

                                                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-[10px] text-[#938c84]">
                                                                <span>
                                                                    Color:
                                                                    <strong class="font-semibold text-[#5f5953]">
                                                                        {{ $item['color'] }}
                                                                    </strong>
                                                                </span>

                                                                <span>
                                                                    Variation:
                                                                    <strong class="font-semibold text-[#5f5953]">
                                                                        {{ $item['variation'] }}
                                                                    </strong>
                                                                </span>
                                                            </div>

                                                            @if($item['free_shipping'])
                                                                <p class="mt-3 text-[10px] font-bold text-[#079b72]">
                                                                    ♧ FREE SHIPPING
                                                                </p>
                                                            @endif

                                                            <div class="mt-4 flex flex-wrap items-center gap-4">
                                                                <button
                                                                    type="button"
                                                                    class="text-[10px] font-semibold text-[#7d766f] transition hover:text-[#d92d2f]"
                                                                >
                                                                    ♡ Move to Wishlist
                                                                </button>

                                                                <button
                                                                    type="button"
                                                                    class="text-[10px] font-semibold text-[#a49d95] transition hover:text-[#d92d2f]"
                                                                >
                                                                    Remove
                                                                </button>
                                                            </div>
                                                        </div>

                                                        {{-- Quantity --}}
                                                        <div>
                                                            <p class="mb-2 text-[9px] font-bold uppercase tracking-[0.14em] text-[#9b948c]">
                                                                Quantity
                                                            </p>

                                                            <div class="flex h-10 w-fit border border-[#ddd6ce] bg-white">
                                                                <button
                                                                    type="button"
                                                                    class="quantity-decrease grid w-10 place-items-center text-[#6e6861] transition hover:bg-[#f3efe9]"
                                                                    data-item="{{ $item['id'] }}"
                                                                >
                                                                    −
                                                                </button>

                                                                <input
                                                                    type="number"
                                                                    name="quantities[{{ $item['id'] }}]"
                                                                    value="{{ $item['quantity'] }}"
                                                                    min="1"
                                                                    max="{{ $item['stock'] }}"
                                                                    class="quantity-input w-12 border-x border-[#e7e1da] bg-white text-center text-sm font-semibold outline-none"
                                                                    data-item="{{ $item['id'] }}"
                                                                >

                                                                <button
                                                                    type="button"
                                                                    class="quantity-increase grid w-10 place-items-center text-[#6e6861] transition hover:bg-[#f3efe9]"
                                                                    data-item="{{ $item['id'] }}"
                                                                >
                                                                    +
                                                                </button>
                                                            </div>

                                                            <p class="mt-2 text-[9px] text-[#aaa39b]">
                                                                {{ $item['stock'] }} available
                                                            </p>
                                                        </div>

                                                        {{-- Price --}}
                                                        <div class="xl:text-right">
                                                            <p class="text-lg font-black text-[#d92d2f]">
                                                                ₱{{ number_format($item['price']) }}
                                                            </p>

                                                            <p class="mt-1 text-[10px] text-[#aaa39b] line-through">
                                                                ₱{{ number_format($item['old_price']) }}
                                                            </p>

                                                            <p class="mt-4 text-[9px] uppercase tracking-[0.12em] text-[#9b948c]">
                                                                Subtotal
                                                            </p>

                                                            <p
                                                                class="item-subtotal mt-1 text-sm font-black"
                                                                data-item="{{ $item['id'] }}"
                                                            >
                                                                ₱{{ number_format($item['price'] * $item['quantity']) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </section>
                            @endforeach
                        </div>
                    </div>

                    {{-- =============================================
                         RIGHT: ORDER SUMMARY
                    ============================================== --}}
                    <aside>
                        <div class="sticky top-[132px] space-y-4">

                            {{-- Voucher --}}
                            <section class="border border-[#dfd9d2] bg-white p-5">
                                <div class="flex items-center justify-between gap-4">
                                    <h2 class="text-xs font-black uppercase tracking-[0.16em]">
                                        Voucher
                                    </h2>

                                    <span class="text-[10px] text-[#9d968e]">
                                        Optional
                                    </span>
                                </div>

                                <div class="mt-4 flex">
                                    <input
                                        id="voucherCode"
                                        name="voucher_code"
                                        type="text"
                                        placeholder="Enter voucher code"
                                        class="min-w-0 flex-1 border border-r-0 border-[#ddd6ce] bg-white px-3 text-xs outline-none focus:border-[#d92d2f]"
                                    >

                                    <button
                                        id="applyVoucher"
                                        type="button"
                                        class="h-11 bg-[#111] px-4 text-xs font-bold text-white transition hover:bg-[#d92d2f]"
                                    >
                                        Apply
                                    </button>
                                </div>

                                <p id="voucherMessage" class="mt-2 hidden text-[10px]"></p>
                            </section>

                            {{-- Summary --}}
                            <section class="border border-[#dfd9d2] bg-white p-5">
                                <h2 class="text-xs font-black uppercase tracking-[0.16em]">
                                    Order Summary
                                </h2>

                                <div class="mt-5 space-y-4 text-sm">
                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-[#817a72]">
                                            Selected Items
                                        </span>

                                        <strong id="selectedItemCount">
                                            3
                                        </strong>
                                    </div>

                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-[#817a72]">
                                            Subtotal
                                        </span>

                                        <strong id="summarySubtotal">
                                            ₱{{ number_format($subtotal) }}
                                        </strong>
                                    </div>

                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-[#817a72]">
                                            Shipping
                                        </span>

                                        <strong id="summaryShipping">
                                            ₱{{ number_format($shipping) }}
                                        </strong>
                                    </div>

                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-[#817a72]">
                                            Voucher / Discount
                                        </span>

                                        <strong
                                            id="summaryDiscount"
                                            class="text-[#079b72]"
                                        >
                                            -₱{{ number_format($discount) }}
                                        </strong>
                                    </div>
                                </div>

                                <div class="mt-5 border-t border-[#e9e3dc] pt-5">
                                    <div class="flex items-end justify-between gap-4">
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#908981]">
                                                Total
                                            </p>

                                            <p class="mt-1 text-[10px] text-[#aaa39b]">
                                                Shipping calculated before checkout
                                            </p>
                                        </div>

                                        <strong
                                            id="summaryTotal"
                                            class="text-2xl font-black text-[#d92d2f]"
                                        >
                                            ₱{{ number_format($total) }}
                                        </strong>
                                    </div>
                                </div>

                                <button
                                    id="checkoutButton"
                                    type="submit"
                                    class="mt-6 flex h-12 w-full items-center justify-center gap-2 bg-[#d92d2f] px-5 text-sm font-bold text-white transition hover:bg-[#bd2024]"
                                >
                                    Proceed to Checkout
                                    <span>→</span>
                                </button>

                                <p class="mt-3 text-center text-[9px] leading-4 text-[#aaa39b]">
                                    Only selected cart items will be included in checkout.
                                </p>
                            </section>

                            {{-- Protection --}}
                            <section class="border border-[#dfd9d2] bg-[#faf8f5] p-4">
                                <div class="flex gap-3">
                                    <span class="mt-0.5 text-[#079b72]">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"></path>
                                        </svg>
                                    </span>

                                    <div>
                                        <p class="text-[11px] font-bold">
                                            LIKHAE Buyer Protection
                                        </p>

                                        <p class="mt-1 text-[10px] leading-5 text-[#9b948c]">
                                            Your payment is protected until your order is successfully completed.
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
<footer class="mt-6 bg-[#0a0a0a] text-white">
    <div class="likhae-container py-14">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.5fr_repeat(4,1fr)]">
            <div>
                <a href="{{ url('/buyer/home') }}" class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black">
                        L
                    </span>

                    <span class="text-xl font-black">
                        LIKHAE
                    </span>
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
                    <a href="{{ url('/buyer/categories') }}">Categories</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">MY ACCOUNT</h3>
                <div class="footer-links">
                    <a href="{{ url('/buyer/orders') }}">My Orders</a>
                    <a href="{{ url('/buyer/wishlist') }}">Wishlist</a>
                    <a href="{{ url('/buyer/messages') }}">Messages</a>
                    <a href="{{ url('/buyer/account') }}">Profile</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">SUPPORT</h3>
                <div class="footer-links">
                    <a href="#">Help Center</a>
                    <a href="{{ url('/buyer/orders') }}">Track Order</a>
                    <a href="#">Returns</a>
                    <a href="#">Buyer Protection</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">COMPANY</h3>
                <div class="footer-links">
                    <a href="#">About LIKHAE</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms</a>
                    <a href="#">Contact Us</a>
                </div>
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-4 border-t border-white/10 pt-7 text-[10px] text-white/25 md:flex-row md:items-center md:justify-between">
            <p>© {{ date('Y') }} LIKHAE, Inc. — Made with pride in the Philippines.</p>
            <p>GCASH · MAYA · VISA · MASTERCARD · COD · BPI</p>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        const sellerCheckboxes = document.querySelectorAll('.seller-checkbox');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const decreaseButtons = document.querySelectorAll('.quantity-decrease');
        const increaseButtons = document.querySelectorAll('.quantity-increase');

        const selectedItemCount = document.getElementById('selectedItemCount');
        const summarySubtotal = document.getElementById('summarySubtotal');
        const summaryShipping = document.getElementById('summaryShipping');
        const summaryDiscount = document.getElementById('summaryDiscount');
        const summaryTotal = document.getElementById('summaryTotal');

        const voucherCode = document.getElementById('voucherCode');
        const applyVoucher = document.getElementById('applyVoucher');
        const voucherMessage = document.getElementById('voucherMessage');

        let discount = 0;

        const formatPeso = (amount) => {
            return '₱' + Math.round(amount).toLocaleString('en-PH');
        };

        const getItemRow = (itemId) => {
            return document.querySelector('.cart-item[data-item="' + itemId + '"]');
        };

        const getQuantityInput = (itemId) => {
            return document.querySelector('.quantity-input[data-item="' + itemId + '"]');
        };

        const getItemCheckbox = (itemId) => {
            return document.querySelector('.item-checkbox[value="' + itemId + '"]');
        };

        function updateItemSubtotal(itemId) {
            const row = getItemRow(itemId);
            const input = getQuantityInput(itemId);

            if (!row || !input) return;

            const price = parseFloat(row.dataset.price || '0');
            const quantity = parseInt(input.value || '1', 10);

            const subtotalElement = document.querySelector(
                '.item-subtotal[data-item="' + itemId + '"]'
            );

            if (subtotalElement) {
                subtotalElement.textContent = formatPeso(price * quantity);
            }
        }

        function updateSellerCheckboxes() {
            sellerCheckboxes.forEach(function (sellerCheckbox) {
                const group = sellerCheckbox.dataset.group;

                const groupItems = Array.from(
                    document.querySelectorAll(
                        '.item-checkbox[data-group="' + group + '"]'
                    )
                );

                const checkedCount = groupItems.filter(function (checkbox) {
                    return checkbox.checked;
                }).length;

                sellerCheckbox.checked =
                    checkedCount === groupItems.length && groupItems.length > 0;

                sellerCheckbox.indeterminate =
                    checkedCount > 0 && checkedCount < groupItems.length;
            });
        }

        function updateSelectAll() {
            const items = Array.from(itemCheckboxes);

            const checkedCount = items.filter(function (checkbox) {
                return checkbox.checked;
            }).length;

            selectAll.checked =
                checkedCount === items.length && items.length > 0;

            selectAll.indeterminate =
                checkedCount > 0 && checkedCount < items.length;
        }

        function updateSummary() {
            let subtotal = 0;
            let selectedCount = 0;
            let allSelectedFreeShipping = true;

            itemCheckboxes.forEach(function (checkbox) {
                if (!checkbox.checked) return;

                const itemId = checkbox.value;
                const row = getItemRow(itemId);
                const quantityInput = getQuantityInput(itemId);

                if (!row || !quantityInput) return;

                const price = parseFloat(row.dataset.price || '0');
                const quantity = parseInt(quantityInput.value || '1', 10);

                subtotal += price * quantity;
                selectedCount++;
            });

            /*
             * Demo shipping calculation.
             * Replace with real shipping logic in the backend.
             */
            const shipping = selectedCount > 0 ? 60 : 0;

            const total = Math.max(0, subtotal + shipping - discount);

            selectedItemCount.textContent = selectedCount;
            summarySubtotal.textContent = formatPeso(subtotal);
            summaryShipping.textContent = formatPeso(shipping);
            summaryDiscount.textContent = '-' + formatPeso(discount);
            summaryTotal.textContent = formatPeso(total);
        }

        selectAll.addEventListener('change', function () {
            itemCheckboxes.forEach(function (checkbox) {
                checkbox.checked = selectAll.checked;
            });

            sellerCheckboxes.forEach(function (checkbox) {
                checkbox.checked = selectAll.checked;
                checkbox.indeterminate = false;
            });

            updateSummary();
        });

        sellerCheckboxes.forEach(function (sellerCheckbox) {
            sellerCheckbox.addEventListener('change', function () {
                const group = sellerCheckbox.dataset.group;

                document.querySelectorAll(
                    '.item-checkbox[data-group="' + group + '"]'
                ).forEach(function (itemCheckbox) {
                    itemCheckbox.checked = sellerCheckbox.checked;
                });

                updateSelectAll();
                updateSummary();
            });
        });

        itemCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                updateSellerCheckboxes();
                updateSelectAll();
                updateSummary();
            });
        });

        decreaseButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const itemId = button.dataset.item;
                const input = getQuantityInput(itemId);

                if (!input) return;

                const min = parseInt(input.min || '1', 10);
                const current = parseInt(input.value || '1', 10);

                input.value = Math.max(min, current - 1);

                updateItemSubtotal(itemId);
                updateSummary();
            });
        });

        increaseButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const itemId = button.dataset.item;
                const input = getQuantityInput(itemId);

                if (!input) return;

                const max = parseInt(input.max || '999', 10);
                const current = parseInt(input.value || '1', 10);

                input.value = Math.min(max, current + 1);

                updateItemSubtotal(itemId);
                updateSummary();
            });
        });

        quantityInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                const min = parseInt(input.min || '1', 10);
                const max = parseInt(input.max || '999', 10);

                let current = parseInt(input.value || '1', 10);

                if (Number.isNaN(current)) {
                    current = min;
                }

                input.value = Math.max(min, Math.min(max, current));

                updateItemSubtotal(input.dataset.item);
                updateSummary();
            });
        });

        applyVoucher.addEventListener('click', function () {
            const code = voucherCode.value.trim().toUpperCase();

            voucherMessage.classList.remove(
                'hidden',
                'text-[#079b72]',
                'text-[#d92d2f]'
            );

            /*
             * Demo voucher only.
             * Replace this with validation from your Laravel backend.
             */
            if (code === 'LIKHAE50') {
                discount = 50;
                voucherMessage.textContent = 'Voucher applied: ₱50 discount.';
                voucherMessage.classList.add('text-[#079b72]');
            } else if (code === '') {
                discount = 0;
                voucherMessage.textContent = 'Enter a voucher code.';
                voucherMessage.classList.add('text-[#d92d2f]');
            } else {
                discount = 0;
                voucherMessage.textContent = 'Voucher is invalid or unavailable.';
                voucherMessage.classList.add('text-[#d92d2f]');
            }

            updateSummary();
        });

        updateSellerCheckboxes();
        updateSelectAll();
        updateSummary();
    });
</script>

</body>
</html>