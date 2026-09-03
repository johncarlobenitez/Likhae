@extends('layouts.buyer')

@section('title', 'My Orders — LIKHAE')
@section('active', 'orders')

@section('content')
@php
    /*
    |--------------------------------------------------------------------------
    | Front-end sample data
    |--------------------------------------------------------------------------
    |
    | Samples are used only when the controller does not provide $orders.
    | Passing an empty collection will display the real empty state.
    |
    */

    if (!isset($orders)) {
        $orders = collect([
            [
                'id' => 1001,
                'order_number' => 'LKH-2026-090401',
                'status' => 'to_receive',
                'placed_at' => 'September 4, 2026',
                'estimated_delivery' => 'September 5–6, 2026',
                'seller' => 'Habi Local Crafts',
                'payment_method' => 'Cash on Delivery',
                'address' => '123 Sample Street, Barangay Poblacion, Santa Cruz, Laguna, 4009',
                'recipient' => 'Buyer Name',
                'phone' => '0912 345 6789',
                'shipping' => 80,
                'discount' => 100,
                'items' => [
                    [
                        'name' => 'Handwoven Everyday Tote Bag',
                        'variant' => 'Natural Brown',
                        'price' => 849,
                        'quantity' => 1,
                        'image' => null,
                    ],
                    [
                        'name' => 'Woven Coin Purse',
                        'variant' => 'Terracotta',
                        'price' => 249,
                        'quantity' => 1,
                        'image' => null,
                    ],
                ],
            ],
            [
                'id' => 1002,
                'order_number' => 'LKH-2026-090302',
                'status' => 'to_ship',
                'placed_at' => 'September 3, 2026',
                'estimated_delivery' => 'September 7–8, 2026',
                'seller' => 'Clay & Co. Studio',
                'payment_method' => 'GCash',
                'address' => '123 Sample Street, Barangay Poblacion, Santa Cruz, Laguna, 4009',
                'recipient' => 'Buyer Name',
                'phone' => '0912 345 6789',
                'shipping' => 60,
                'discount' => 0,
                'items' => [
                    [
                        'name' => 'Minimalist Ceramic Coffee Mug',
                        'variant' => 'Cream • 350 ml',
                        'price' => 329,
                        'quantity' => 2,
                        'image' => null,
                    ],
                ],
            ],
            [
                'id' => 1003,
                'order_number' => 'LKH-2026-082805',
                'status' => 'completed',
                'placed_at' => 'August 28, 2026',
                'estimated_delivery' => null,
                'seller' => 'Likha Home Essentials',
                'payment_method' => 'Cash on Delivery',
                'address' => '123 Sample Street, Barangay Poblacion, Santa Cruz, Laguna, 4009',
                'recipient' => 'Buyer Name',
                'phone' => '0912 345 6789',
                'shipping' => 80,
                'discount' => 50,
                'items' => [
                    [
                        'name' => 'Scented Soy Candle',
                        'variant' => 'Vanilla and Sandalwood',
                        'price' => 459,
                        'quantity' => 1,
                        'image' => null,
                    ],
                ],
            ],
            [
                'id' => 1004,
                'order_number' => 'LKH-2026-090406',
                'status' => 'to_pay',
                'placed_at' => 'September 4, 2026',
                'estimated_delivery' => null,
                'seller' => 'Gawang Lokal',
                'payment_method' => 'GCash',
                'address' => '123 Sample Street, Barangay Poblacion, Santa Cruz, Laguna, 4009',
                'recipient' => 'Buyer Name',
                'phone' => '0912 345 6789',
                'shipping' => 70,
                'discount' => 0,
                'items' => [
                    [
                        'name' => 'Local Artisan Notebook',
                        'variant' => 'A5 • Kraft',
                        'price' => 279,
                        'quantity' => 2,
                        'image' => null,
                    ],
                ],
            ],
            [
                'id' => 1005,
                'order_number' => 'LKH-2026-082201',
                'status' => 'cancelled',
                'placed_at' => 'August 22, 2026',
                'estimated_delivery' => null,
                'seller' => 'Amara Accessories',
                'payment_method' => 'Cash on Delivery',
                'address' => '123 Sample Street, Barangay Poblacion, Santa Cruz, Laguna, 4009',
                'recipient' => 'Buyer Name',
                'phone' => '0912 345 6789',
                'shipping' => 60,
                'discount' => 0,
                'items' => [
                    [
                        'name' => 'Handmade Beaded Bracelet',
                        'variant' => 'Amber',
                        'price' => 189,
                        'quantity' => 1,
                        'image' => null,
                    ],
                ],
            ],
            [
                'id' => 1006,
                'order_number' => 'LKH-2026-081903',
                'status' => 'return_requested',
                'placed_at' => 'August 19, 2026',
                'estimated_delivery' => null,
                'seller' => 'Casa Lokal',
                'payment_method' => 'GCash',
                'address' => '123 Sample Street, Barangay Poblacion, Santa Cruz, Laguna, 4009',
                'recipient' => 'Buyer Name',
                'phone' => '0912 345 6789',
                'shipping' => 80,
                'discount' => 0,
                'items' => [
                    [
                        'name' => 'Linen Table Runner',
                        'variant' => 'Sage Green',
                        'price' => 599,
                        'quantity' => 1,
                        'image' => null,
                    ],
                ],
            ],
        ]);
    } else {
        $orders = collect($orders);
    }

    $statusToTab = function (string $status): string {
        $status = strtolower(
            str_replace('-', '_', trim($status))
        );

        return match ($status) {
            'new',
            'pending',
            'pending_payment',
            'unpaid',
            'to_pay' => 'to-pay',

            'paid',
            'processing',
            'preparing',
            'ready_for_pickup',
            'to_ship' => 'to-ship',

            'courier_accepted',
            'heading_pickup',
            'arrived_pickup',
            'in_transit',
            'out_for_delivery',
            'arrived_buyer',
            'to_receive' => 'to-receive',

            'delivered',
            'completed' => 'completed',

            'cancelled',
            'canceled' => 'cancelled',

            'return_requested',
            'return_approved',
            'return_in_transit',
            'returned',
            'refund_pending',
            'refunded',
            'returns' => 'returns',

            default => 'all',
        };
    };

    $statusLabels = [
        'to-pay' => 'To Pay',
        'to-ship' => 'To Ship',
        'to-receive' => 'To Receive',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'returns' => 'Return/Refund',
        'all' => 'Processing',
    ];

    $statusClasses = [
        'to-pay' => 'border-amber-200 bg-amber-50 text-amber-800',
        'to-ship' => 'border-blue-200 bg-blue-50 text-blue-700',
        'to-receive' => 'border-violet-200 bg-violet-50 text-violet-700',
        'completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'cancelled' => 'border-red-200 bg-red-50 text-red-700',
        'returns' => 'border-orange-200 bg-orange-50 text-orange-700',
        'all' => 'border-stone-200 bg-stone-50 text-stone-700',
    ];

    $orders = $orders->map(function ($order, $index) use ($statusToTab) {
        $status = (string) data_get(
            $order,
            'status',
            'processing'
        );

        $sellerValue = data_get($order, 'seller.store_name')
            ?? data_get($order, 'seller.shop_name')
            ?? data_get($order, 'seller.name')
            ?? data_get($order, 'seller')
            ?? 'LIKHAE Seller';

        $seller = is_scalar($sellerValue)
            ? (string) $sellerValue
            : 'LIKHAE Seller';

        $items = collect(
            data_get($order, 'items', [])
        )->map(function ($item) {
            $product = data_get($item, 'product');

            return [
                'id' => data_get($item, 'id')
                    ?? data_get($product, 'id'),

                'name' => (string) (
                    data_get($product, 'name')
                    ?? data_get($item, 'name')
                    ?? 'Product Name'
                ),

                'variant' => (string) (
                    data_get($item, 'variant')
                    ?? data_get($item, 'variation')
                    ?? 'Standard'
                ),

                'price' => max(
                    0,
                    (float) (
                        data_get($item, 'price')
                        ?? data_get($product, 'price')
                        ?? 0
                    )
                ),

                'quantity' => max(
                    1,
                    (int) data_get($item, 'quantity', 1)
                ),

                'image' => data_get($item, 'image')
                    ?? data_get($product, 'image')
                    ?? data_get($product, 'image_url'),
            ];
        });

        $computedSubtotal = $items->sum(
            fn ($item) => $item['price'] * $item['quantity']
        );

        $subtotal = max(
            0,
            (float) data_get(
                $order,
                'subtotal',
                $computedSubtotal
            )
        );

        $shipping = max(
            0,
            (float) data_get($order, 'shipping', 0)
        );

        $discount = max(
            0,
            (float) data_get($order, 'discount', 0)
        );

        $total = max(
            0,
            (float) data_get(
                $order,
                'total',
                $subtotal + $shipping - $discount
            )
        );

        return [
            'id' => data_get(
                $order,
                'id',
                $index + 1
            ),

            'number' => (string) (
                data_get($order, 'order_number')
                ?? data_get($order, 'number')
                ?? 'LKH-' . str_pad(
                    $index + 1,
                    6,
                    '0',
                    STR_PAD_LEFT
                )
            ),

            'status' => $status,
            'tab' => $statusToTab($status),
            'placed_at' => (string) data_get(
                $order,
                'placed_at',
                data_get($order, 'created_at', '')
            ),

            'estimated_delivery' => data_get(
                $order,
                'estimated_delivery'
            ),

            'seller' => $seller,
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => $total,

            'payment_method' => (string) data_get(
                $order,
                'payment_method',
                'Cash on Delivery'
            ),

            'recipient' => (string) data_get(
                $order,
                'recipient',
                data_get(auth()->user(), 'name', 'Buyer Name')
            ),

            'phone' => (string) data_get(
                $order,
                'phone',
                '0912 345 6789'
            ),

            'address' => (string) data_get(
                $order,
                'address',
                'Default delivery address'
            ),
        ];
    });

    $tabs = [
        'all' => 'All',
        'to-pay' => 'To Pay',
        'to-ship' => 'To Ship',
        'to-receive' => 'To Receive',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'returns' => 'Returns/Refunds',
    ];

    $requestedStatus = request('status', 'all');

    $activeStatus = array_key_exists($requestedStatus, $tabs)
        ? $requestedStatus
        : 'all';

    $filteredOrders = $activeStatus === 'all'
        ? $orders
        : $orders->where('tab', $activeStatus)->values();

    $allowedModes = [
        'index',
        'show',
        'review',
        'success',
    ];

    $requestedMode = request('mode', 'index');

    $mode = in_array(
        $requestedMode,
        $allowedModes,
        true
    ) ? $requestedMode : 'index';

    $requestedOrder = request('order');

    $activeOrder = filled($requestedOrder)
        ? $orders->first(
            fn ($order) =>
                (string) $order['id'] ===
                (string) $requestedOrder
        )
        : null;

    if (
        in_array($mode, ['show', 'review'], true) &&
        !$activeOrder
    ) {
        $mode = 'index';
    }

    $tabCount = function (string $tab) use ($orders): int {
        if ($tab === 'all') {
            return $orders->count();
        }

        return $orders->where('tab', $tab)->count();
    };

    $progressSteps = [
        'Order Placed',
        'Payment Confirmed',
        'Seller Preparing',
        'Out for Delivery',
        'Delivered',
    ];

    $progressIndex = $activeOrder
        ? match ($activeOrder['tab']) {
            'to-pay' => 0,
            'to-ship' => 2,
            'to-receive' => 3,
            'completed', 'returns' => 4,
            default => 0,
        }
        : 0;
@endphp

<div class="mx-auto w-full max-w-[1500px] px-4 py-6 sm:px-6 lg:px-8">
    @if ($mode === 'index')
        {{-- Page heading --}}
        <header class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-700">
                    Order History
                </span>

                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-stone-900">
                    My Orders
                </h1>

                <p class="mt-1 text-sm text-stone-500">
                    Track purchases, deliveries, returns, and completed orders.
                </p>
            </div>

            <a
                href="{{ route('buyer.products') }}"
                class="inline-flex w-fit items-center justify-center gap-2 rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-xs font-semibold text-stone-700 transition hover:bg-stone-50 focus:outline-none focus:ring-4 focus:ring-stone-200"
            >
                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M6 8h12l1 13H5z"/>
                    <path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                </svg>

                Shop Products
            </a>
        </header>

        {{-- Tabs --}}
        <section class="mb-5 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <nav
                class="flex overflow-x-auto px-2"
                aria-label="Order status"
            >
                @foreach ($tabs as $tabKey => $tabLabel)
                    <a
                        href="{{ route('buyer.orders', ['status' => $tabKey]) }}"
                        class="relative flex min-w-max items-center gap-2 px-4 py-4 text-xs font-semibold transition
                            {{ $activeStatus === $tabKey
                                ? 'text-amber-700'
                                : 'text-stone-500 hover:text-stone-900' }}"
                        @if ($activeStatus === $tabKey) aria-current="page" @endif
                    >
                        {{ $tabLabel }}

                        @if ($tabCount($tabKey) > 0)
                            <span class="flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[9px]
                                {{ $activeStatus === $tabKey
                                    ? 'bg-amber-100 text-amber-800'
                                    : 'bg-stone-100 text-stone-500' }}"
                            >
                                {{ $tabCount($tabKey) }}
                            </span>
                        @endif

                        @if ($activeStatus === $tabKey)
                            <span class="absolute inset-x-3 bottom-0 h-0.5 rounded-full bg-amber-600"></span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </section>

        {{-- Search --}}
        @if ($orders->isNotEmpty())
            <div class="mb-5 rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                <div class="relative max-w-md">
                    <svg
                        width="17"
                        height="17"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400"
                        aria-hidden="true"
                    >
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    <input
                        type="search"
                        placeholder="Search order number, product, or seller..."
                        class="w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-10 pr-4 text-xs text-stone-800 outline-none transition placeholder:text-stone-400 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-100"
                        data-order-search
                    >
                </div>
            </div>
        @endif

        {{-- Orders --}}
        <div class="space-y-4" data-order-list>
            @forelse ($filteredOrders as $order)
                @php
                    $statusLabel =
                        $statusLabels[$order['tab']]
                        ?? 'Processing';

                    $statusClass =
                        $statusClasses[$order['tab']]
                        ?? $statusClasses['all'];

                    $firstItem = $order['items']->first();

                    $searchText = mb_strtolower(
                        trim(
                            $order['number'] . ' ' .
                            $order['seller'] . ' ' .
                            $order['items']->pluck('name')->join(' ')
                        )
                    );
                @endphp

                <article
                    class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition hover:border-stone-300"
                    data-order-card
                    data-order-search="{{ $searchText }}"
                >
                    <header class="flex flex-col gap-3 border-b border-stone-100 bg-stone-50/70 px-4 py-3.5 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                            <strong class="text-xs font-semibold text-stone-900">
                                {{ $order['number'] }}
                            </strong>

                            <span class="text-[10px] text-stone-400">
                                Ordered {{ $order['placed_at'] }}
                            </span>
                        </div>

                        <span class="inline-flex w-fit items-center rounded-full border px-2.5 py-1 text-[10px] font-semibold {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </header>

                    <div class="p-4 sm:p-5">
                        <div class="flex items-center justify-between gap-4 border-b border-stone-100 pb-3">
                            <div class="flex min-w-0 items-center gap-2">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-50 text-xs font-bold text-amber-700">
                                    {{ strtoupper(mb_substr($order['seller'], 0, 1)) }}
                                </span>

                                <strong class="truncate text-xs font-semibold text-stone-800">
                                    {{ $order['seller'] }}
                                </strong>
                            </div>

                            <a
                                href="{{ route('buyer.messages') }}"
                                class="shrink-0 text-[10px] font-semibold text-amber-700 hover:text-amber-800"
                            >
                                Message Seller
                            </a>
                        </div>

                        <div class="divide-y divide-stone-100">
                            @foreach ($order['items'] as $item)
                                <div class="flex gap-3 py-4">
                                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-stone-200 bg-stone-50">
                                        @if ($item['image'])
                                            <img
                                                src="{{ $item['image'] }}"
                                                alt="{{ $item['name'] }}"
                                                class="h-full w-full object-cover"
                                            >
                                        @else
                                            <svg
                                                width="29"
                                                height="29"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                                class="text-stone-300"
                                                aria-hidden="true"
                                            >
                                                <path d="M4 5h16v14H4z"/>
                                                <path d="m4 15 4-4 4 4 3-3 5 5"/>
                                                <circle cx="15.5" cy="8.5" r="1.5"/>
                                            </svg>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <h2 class="line-clamp-2 text-sm font-semibold leading-5 text-stone-900">
                                            {{ $item['name'] }}
                                        </h2>

                                        <p class="mt-1 text-[11px] text-stone-500">
                                            {{ $item['variant'] }}
                                        </p>

                                        <div class="mt-3 flex items-center justify-between gap-4">
                                            <span class="text-[11px] text-stone-500">
                                                Quantity: {{ $item['quantity'] }}
                                            </span>

                                            <strong class="text-sm font-semibold text-stone-900">
                                                ₱{{ number_format($item['price'] * $item['quantity'], 2) }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-col gap-4 border-t border-stone-100 pt-4 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                @if ($order['tab'] === 'to-receive' && $order['estimated_delivery'])
                                    <p class="text-[10px] font-medium text-violet-700">
                                        Estimated delivery
                                    </p>

                                    <strong class="mt-1 block text-xs font-semibold text-stone-800">
                                        {{ $order['estimated_delivery'] }}
                                    </strong>
                                @elseif ($order['tab'] === 'to-ship')
                                    <p class="text-xs text-stone-500">
                                        The seller is preparing your order.
                                    </p>
                                @elseif ($order['tab'] === 'to-pay')
                                    <p class="text-xs text-amber-700">
                                        Payment is required to process this order.
                                    </p>
                                @elseif ($order['tab'] === 'completed')
                                    <p class="text-xs text-emerald-700">
                                        Order delivered successfully.
                                    </p>
                                @elseif ($order['tab'] === 'cancelled')
                                    <p class="text-xs text-red-600">
                                        This order has been cancelled.
                                    </p>
                                @elseif ($order['tab'] === 'returns')
                                    <p class="text-xs text-orange-700">
                                        Your return or refund request is under review.
                                    </p>
                                @endif
                            </div>

                            <div class="flex flex-col items-stretch gap-3 sm:items-end">
                                <p class="text-right text-xs text-stone-500">
                                    Order Total

                                    <strong class="ml-2 text-lg font-semibold text-amber-700">
                                        ₱{{ number_format($order['total'], 2) }}
                                    </strong>
                                </p>

                                <div class="flex flex-wrap justify-end gap-2">
                                    <a
                                        href="{{ route('buyer.orders', [
                                            'status' => $activeStatus,
                                            'mode' => 'show',
                                            'order' => $order['id'],
                                        ]) }}"
                                        class="rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-xs font-semibold text-stone-700 transition hover:bg-stone-50"
                                    >
                                        View Details
                                    </a>

                                    @if ($order['tab'] === 'to-pay')
                                        <button
                                            type="button"
                                            class="rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                                        >
                                            Pay Now
                                        </button>
                                    @elseif ($order['tab'] === 'to-receive')
                                        <a
                                            href="{{ route('buyer.orders', [
                                                'status' => $activeStatus,
                                                'mode' => 'show',
                                                'order' => $order['id'],
                                            ]) }}"
                                            class="rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                                        >
                                            Track Order
                                        </a>
                                    @elseif ($order['tab'] === 'completed')
                                        <a
                                            href="{{ route('buyer.orders', [
                                                'status' => $activeStatus,
                                                'mode' => 'review',
                                                'order' => $order['id'],
                                            ]) }}"
                                            class="rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                                        >
                                            Write Review
                                        </a>
                                    @elseif ($order['tab'] === 'cancelled')
                                        <a
                                            href="{{ route('buyer.products') }}"
                                            class="rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                                        >
                                            Shop Again
                                        </a>
                                    @elseif ($order['tab'] === 'returns')
                                        <a
                                            href="{{ route('buyer.messages') }}"
                                            class="rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                                        >
                                            Contact Support
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <section class="rounded-2xl border border-stone-200 bg-white px-6 py-16 text-center shadow-sm">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-amber-50 text-amber-700">
                        <svg
                            width="36"
                            height="36"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M6 2h12l2 4v16H4V6z"/>
                            <path d="M6 6h12"/>
                            <path d="M8 11h8"/>
                            <path d="M8 15h6"/>
                        </svg>
                    </div>

                    <h2 class="mt-5 text-lg font-semibold text-stone-900">
                        {{ $activeStatus === 'all' ? 'No orders yet' : 'No orders in this section' }}
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-500">
                        {{ $activeStatus === 'all'
                            ? 'Your purchases and delivery updates will appear here.'
                            : 'Orders with this status will appear here when available.' }}
                    </p>

                    <a
                        href="{{ route('buyer.products') }}"
                        class="mt-6 inline-flex rounded-xl bg-amber-600 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                    >
                        Start Shopping
                    </a>
                </section>
            @endforelse

            <section
                class="hidden rounded-2xl border border-stone-200 bg-white px-6 py-14 text-center shadow-sm"
                data-order-no-results
            >
                <h2 class="text-base font-semibold text-stone-900">
                    No matching orders
                </h2>

                <p class="mt-2 text-sm text-stone-500">
                    Try searching with another order number, product, or seller.
                </p>
            </section>
        </div>
    @endif

    @if ($mode === 'show' && $activeOrder)
        {{-- Order details heading --}}
        <header class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a
                    href="{{ route('buyer.orders', ['status' => $activeStatus]) }}"
                    class="mb-3 inline-flex items-center gap-2 text-xs font-semibold text-stone-500 transition hover:text-amber-700"
                >
                    <svg
                        width="15"
                        height="15"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="m15 18-6-6 6-6"/>
                    </svg>

                    Back to Orders
                </a>

                <span class="block text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-700">
                    Order Details
                </span>

                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-stone-900">
                    {{ $activeOrder['number'] }}
                </h1>

                <p class="mt-1 text-sm text-stone-500">
                    Ordered on {{ $activeOrder['placed_at'] }}
                </p>
            </div>

            <span class="inline-flex w-fit items-center rounded-full border px-3 py-1.5 text-xs font-semibold {{ $statusClasses[$activeOrder['tab']] ?? $statusClasses['all'] }}">
                {{ $statusLabels[$activeOrder['tab']] ?? 'Processing' }}
            </span>
        </header>

        {{-- Progress --}}
        @if ($activeOrder['tab'] !== 'cancelled')
            <section class="mb-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-sm font-semibold text-stone-900">
                    Order Progress
                </h2>

                <div class="mt-5 grid gap-3 sm:grid-cols-5">
                    @foreach ($progressSteps as $index => $step)
                        @php
                            $isComplete = $index <= $progressIndex;
                            $isCurrent = $index === $progressIndex;
                        @endphp

                        <div class="rounded-xl border p-3 text-center
                            {{ $isComplete
                                ? 'border-amber-200 bg-amber-50'
                                : 'border-stone-200 bg-stone-50' }}"
                        >
                            <span class="mx-auto flex h-7 w-7 items-center justify-center rounded-full text-[10px] font-bold
                                {{ $isComplete
                                    ? 'bg-amber-600 text-white'
                                    : 'bg-stone-200 text-stone-500' }}"
                            >
                                @if ($index < $progressIndex)
                                    ✓
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </span>

                            <strong class="mt-2 block text-[10px] leading-4
                                {{ $isCurrent
                                    ? 'text-amber-800'
                                    : ($isComplete ? 'text-stone-700' : 'text-stone-400') }}"
                            >
                                {{ $step }}
                            </strong>
                        </div>
                    @endforeach
                </div>

                @if ($activeOrder['estimated_delivery'])
                    <p class="mt-4 text-center text-xs text-stone-500">
                        Estimated delivery:
                        <strong class="text-stone-800">
                            {{ $activeOrder['estimated_delivery'] }}
                        </strong>
                    </p>
                @endif
            </section>
        @else
            <section class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-5">
                <h2 class="text-sm font-semibold text-red-800">
                    Order Cancelled
                </h2>

                <p class="mt-1 text-xs leading-5 text-red-700">
                    This order was cancelled and will no longer be processed.
                </p>
            </section>
        @endif

        <div class="grid items-start gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-5">
                {{-- Ordered items --}}
                <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                    <header class="flex items-center justify-between gap-4 border-b border-stone-100 px-5 py-4">
                        <div>
                            <h2 class="text-sm font-semibold text-stone-900">
                                Ordered Products
                            </h2>

                            <p class="mt-1 text-xs text-stone-500">
                                Sold by {{ $activeOrder['seller'] }}
                            </p>
                        </div>

                        <a
                            href="{{ route('buyer.messages') }}"
                            class="text-xs font-semibold text-amber-700 hover:text-amber-800"
                        >
                            Message Seller
                        </a>
                    </header>

                    <div class="divide-y divide-stone-100 px-5">
                        @foreach ($activeOrder['items'] as $item)
                            <article class="flex gap-4 py-5">
                                <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-stone-200 bg-stone-50">
                                    @if ($item['image'])
                                        <img
                                            src="{{ $item['image'] }}"
                                            alt="{{ $item['name'] }}"
                                            class="h-full w-full object-cover"
                                        >
                                    @else
                                        <svg
                                            width="32"
                                            height="32"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            class="text-stone-300"
                                            aria-hidden="true"
                                        >
                                            <path d="M4 5h16v14H4z"/>
                                            <path d="m4 15 4-4 4 4 3-3 5 5"/>
                                            <circle cx="15.5" cy="8.5" r="1.5"/>
                                        </svg>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-semibold text-stone-900">
                                        {{ $item['name'] }}
                                    </h3>

                                    <p class="mt-1 text-xs text-stone-500">
                                        {{ $item['variant'] }}
                                    </p>

                                    <div class="mt-4 flex items-end justify-between gap-4">
                                        <span class="text-xs text-stone-500">
                                            Quantity: {{ $item['quantity'] }}
                                        </span>

                                        <strong class="text-sm font-semibold text-stone-900">
                                            ₱{{ number_format($item['price'] * $item['quantity'], 2) }}
                                        </strong>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                {{-- Delivery details --}}
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-sm font-semibold text-stone-900">
                        Delivery Address
                    </h2>

                    <div class="mt-4 flex gap-3 rounded-xl border border-stone-200 bg-stone-50 p-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/>
                                <circle cx="12" cy="10" r="2.5"/>
                            </svg>
                        </div>

                        <div>
                            <strong class="text-xs font-semibold text-stone-900">
                                {{ $activeOrder['recipient'] }}
                            </strong>

                            <span class="ml-2 text-xs text-stone-500">
                                {{ $activeOrder['phone'] }}
                            </span>

                            <p class="mt-2 text-xs leading-5 text-stone-600">
                                {{ $activeOrder['address'] }}
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Summary --}}
            <aside class="space-y-5 xl:sticky xl:top-24">
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-sm font-semibold text-stone-900">
                        Payment Summary
                    </h2>

                    <div class="mt-4 space-y-3 text-xs">
                        <div class="flex justify-between gap-4">
                            <span class="text-stone-500">Subtotal</span>
                            <span class="font-medium text-stone-700">
                                ₱{{ number_format($activeOrder['subtotal'], 2) }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-stone-500">Shipping</span>
                            <span class="font-medium text-stone-700">
                                ₱{{ number_format($activeOrder['shipping'], 2) }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-stone-500">Discount</span>
                            <span class="font-medium text-emerald-700">
                                −₱{{ number_format($activeOrder['discount'], 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 flex items-end justify-between border-t border-stone-200 pt-4">
                        <span class="text-sm font-semibold text-stone-900">
                            Total
                        </span>

                        <strong class="text-xl font-semibold text-amber-700">
                            ₱{{ number_format($activeOrder['total'], 2) }}
                        </strong>
                    </div>

                    <p class="mt-4 rounded-xl bg-stone-50 p-3 text-[11px] text-stone-500">
                        Payment method:
                        <strong class="text-stone-700">
                            {{ $activeOrder['payment_method'] }}
                        </strong>
                    </p>
                </section>

                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-sm font-semibold text-stone-900">
                        Available Actions
                    </h2>

                    <div class="mt-4 grid gap-2">
                        @if ($activeOrder['tab'] === 'to-pay')
                            <button
                                type="button"
                                class="rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-amber-700"
                            >
                                Pay Now
                            </button>

                            <button
                                type="button"
                                class="rounded-xl border border-red-200 px-4 py-2.5 text-xs font-semibold text-red-600 hover:bg-red-50"
                            >
                                Cancel Order
                            </button>
                        @elseif ($activeOrder['tab'] === 'to-receive')
                            <button
                                type="button"
                                class="rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-amber-700"
                            >
                                Confirm Order Received
                            </button>
                        @elseif ($activeOrder['tab'] === 'completed')
                            <a
                                href="{{ route('buyer.orders', [
                                    'status' => $activeStatus,
                                    'mode' => 'review',
                                    'order' => $activeOrder['id'],
                                ]) }}"
                                class="rounded-xl bg-amber-600 px-4 py-2.5 text-center text-xs font-semibold text-white hover:bg-amber-700"
                            >
                                Write a Review
                            </a>
                            <button type="button" onclick="openReturnModal()"
                                class="rounded-xl border border-stone-300 px-4 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50">
                                Return / Refund
                            </button>
                        @elseif ($activeOrder['tab'] === 'returns')
                            <button type="button" onclick="openReturnModal()"
                                class="rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-amber-700">
                                View / Update Request
                            </button>
                        @endif

                        <a
                            href="{{ route('buyer.products') }}"
                            class="rounded-xl border border-stone-300 px-4 py-2.5 text-center text-xs font-semibold text-stone-700 hover:bg-stone-50"
                        >
                            Shop Again
                        </a>
                    </div>
                </section>
            </aside>
        </div>
    @endif

    @if ($mode === 'review' && $activeOrder)
        <header class="mb-6">
            <a
                href="{{ route('buyer.orders', [
                    'status' => $activeStatus,
                    'mode' => 'show',
                    'order' => $activeOrder['id'],
                ]) }}"
                class="mb-3 inline-flex items-center gap-2 text-xs font-semibold text-stone-500 hover:text-amber-700"
            >
                <svg
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <path d="m15 18-6-6 6-6"/>
                </svg>

                Back to Order Details
            </a>

            <span class="block text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-700">
                Product Feedback
            </span>

            <h1 class="mt-1 text-2xl font-semibold text-stone-900">
                Write a Review
            </h1>

            <p class="mt-1 text-sm text-stone-500">
                Share your experience with products from {{ $activeOrder['seller'] }}.
            </p>
        </header>

        <section class="mx-auto max-w-3xl overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-100 p-5">
                @foreach ($activeOrder['items'] as $item)
                    <div class="flex items-center gap-3">
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-stone-200 bg-stone-50">
                            @if ($item['image'])
                                <img
                                    src="{{ $item['image'] }}"
                                    alt="{{ $item['name'] }}"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                <svg
                                    width="25"
                                    height="25"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    class="text-stone-300"
                                    aria-hidden="true"
                                >
                                    <path d="M4 5h16v14H4z"/>
                                    <path d="m4 15 4-4 4 4 3-3 5 5"/>
                                </svg>
                            @endif
                        </div>

                        <div>
                            <h2 class="text-sm font-semibold text-stone-900">
                                {{ $item['name'] }}
                            </h2>

                            <p class="mt-1 text-xs text-stone-500">
                                {{ $item['variant'] }}
                            </p>
                        </div>
                    </div>

                    @break
                @endforeach
            </div>

            <form
                method="GET"
                action="{{ route('buyer.orders') }}"
                class="p-5 sm:p-6"
                data-review-form
            >
                <input type="hidden" name="mode" value="success">
                <input type="hidden" name="status" value="{{ $activeStatus }}">
                <input type="hidden" name="order" value="{{ $activeOrder['id'] }}">

                <fieldset>
                    <legend class="text-sm font-semibold text-stone-900">
                        Overall Rating
                    </legend>

                    <p class="mt-1 text-xs text-stone-500">
                        How satisfied are you with this order?
                    </p>

                    <div class="mt-4 flex items-center gap-1" data-rating-picker>
                        @for ($rating = 1; $rating <= 5; $rating++)
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="rating"
                                    value="{{ $rating }}"
                                    class="sr-only"
                                    data-rating-input
                                    required
                                >

                                <svg
                                    width="32"
                                    height="32"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                    class="text-stone-300 transition hover:text-amber-500"
                                    data-rating-star
                                    aria-hidden="true"
                                >
                                    <path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2Z"/>
                                </svg>

                                <span class="sr-only">
                                    {{ $rating }} stars
                                </span>
                            </label>
                        @endfor
                    </div>

                    <p class="mt-2 text-xs font-medium text-amber-700" data-rating-label>
                        Select a rating
                    </p>
                </fieldset>

                <div class="mt-6">
                    <label for="review-message" class="text-sm font-semibold text-stone-900">
                        Your Review
                    </label>

                    <textarea
                        id="review-message"
                        name="review"
                        rows="5"
                        maxlength="500"
                        placeholder="Tell other buyers about the product quality, packaging, and your experience with the seller."
                        class="mt-2 w-full resize-none rounded-xl border border-stone-300 px-4 py-3 text-sm leading-6 text-stone-800 outline-none transition placeholder:text-stone-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                        data-review-message
                    ></textarea>

                    <div class="mt-1 flex justify-end">
                        <span class="text-[10px] text-stone-400" data-review-count>
                            0/500
                        </span>
                    </div>
                </div>

                <label class="mt-5 flex cursor-pointer items-start gap-3">
                    <input
                        type="checkbox"
                        name="anonymous"
                        value="1"
                        class="mt-0.5 h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500"
                    >

                    <span>
                        <strong class="block text-xs font-semibold text-stone-700">
                            Submit anonymously
                        </strong>

                        <small class="mt-1 block text-[10px] text-stone-500">
                            Your buyer name will not be displayed publicly.
                        </small>
                    </span>
                </label>

                {{-- ── SELLER RATING ────────────────────── --}}
                <div class="mt-6 border-t border-stone-100 pt-5">
                    <p class="text-sm font-semibold text-stone-900">Rate the Seller</p>
                    <p class="mt-0.5 text-xs text-stone-500">How was your experience with {{ $activeOrder['seller'] }}?</p>
                    <div class="mt-3 flex items-center gap-1" data-seller-rating-picker>
                        @for($s = 1; $s <= 5; $s++)
                            <label class="cursor-pointer">
                                <input type="radio" name="seller_rating" value="{{ $s }}" class="sr-only" data-seller-rating-input>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="1.6" class="text-stone-300 transition hover:text-amber-500"
                                     data-seller-rating-star aria-hidden="true">
                                    <path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2Z"/>
                                </svg>
                                <span class="sr-only">{{ $s }} stars</span>
                            </label>
                        @endfor
                    </div>
                </div>

                {{-- ── DELIVERY RATING ──────────────────── --}}
                <div class="mt-5">
                    <p class="text-sm font-semibold text-stone-900">Rate the Delivery</p>
                    <p class="mt-0.5 text-xs text-stone-500">How was the delivery speed and handling?</p>
                    <div class="mt-3 flex items-center gap-1" data-delivery-rating-picker>
                        @for($d = 1; $d <= 5; $d++)
                            <label class="cursor-pointer">
                                <input type="radio" name="delivery_rating" value="{{ $d }}" class="sr-only" data-delivery-rating-input>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="1.6" class="text-stone-300 transition hover:text-amber-500"
                                     data-delivery-rating-star aria-hidden="true">
                                    <path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2Z"/>
                                </svg>
                                <span class="sr-only">{{ $d }} stars</span>
                            </label>
                        @endfor
                    </div>
                </div>

                {{-- ── PHOTO / VIDEO UPLOAD ─────────────── --}}
                <div class="mt-5">
                    <p class="text-sm font-semibold text-stone-900">Add Photos or Videos <span class="text-[10px] font-normal text-stone-400">(optional)</span></p>
                    <p class="mt-0.5 text-xs text-stone-500">Help other buyers by uploading images of the product.</p>
                    <label class="mt-3 flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-stone-300 bg-stone-50 px-5 py-8 text-center transition hover:border-amber-400 hover:bg-amber-50">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" class="text-stone-400">
                            <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                            <path d="m12 12 0 9"/>
                            <path d="m8 17 4-5 4 5"/>
                        </svg>
                        <span class="text-xs font-semibold text-stone-600">Click to upload</span>
                        <span class="text-[10px] text-stone-400">JPG, PNG, MP4 · Max 10MB each · Up to 5 files</span>
                        <input type="file" name="review_media[]" accept="image/*,video/*" multiple class="sr-only" data-review-upload>
                    </label>
                    <div id="reviewMediaPreview" class="mt-3 hidden flex-wrap gap-2"></div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 border-t border-stone-100 pt-5 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('buyer.orders', [
                            'status' => $activeStatus,
                            'mode' => 'show',
                            'order' => $activeOrder['id'],
                        ]) }}"
                        class="rounded-xl border border-stone-300 px-5 py-2.5 text-center text-xs font-semibold text-stone-700 hover:bg-stone-50"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-xl bg-amber-600 px-5 py-2.5 text-xs font-semibold text-white hover:bg-amber-700"
                    >
                        Submit Review
                    </button>
                </div>
            </form>
        </section>
    @endif

    @if ($mode === 'success')
        <section class="mx-auto max-w-xl rounded-2xl border border-stone-200 bg-white px-6 py-14 text-center shadow-sm">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                <svg
                    width="38"
                    height="38"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m8 12 2.5 2.5L16 9"/>
                </svg>
            </div>

            <span class="mt-5 block text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-700">
                Review Submitted
            </span>

            <h1 class="mt-2 text-xl font-semibold text-stone-900">
                Thank you for your feedback
            </h1>

            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-500">
                Your review helps other buyers make better shopping decisions
                and helps local sellers improve their products.
            </p>

            <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                <a
                    href="{{ route('buyer.orders', ['status' => 'completed']) }}"
                    class="rounded-xl border border-stone-300 px-5 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50"
                >
                    Return to Orders
                </a>

                <a
                    href="{{ route('buyer.products') }}"
                    class="rounded-xl bg-amber-600 px-5 py-2.5 text-xs font-semibold text-white hover:bg-amber-700"
                >
                    Buy Again
                </a>
            </div>
        </section>
    @endife('buyer.products') }}"
                    class="rounded-xl bg-amber-600 px-5 py-2.5 text-xs font-semibold text-white hover:bg-amber-700"
                >
                    Continue Shopping
                </a>
            </div>
        </section>
    @endif
</div>

{{-- ══════════════════════════════════════════
     RETURN / REFUND REQUEST MODAL
══════════════════════════════════════════ --}}
<div id="returnModal"
     style="display:none;position:fixed;inset:0;z-index:200;align-items:center;justify-content:center;
            background:rgba(0,0,0,.45);backdrop-filter:blur(4px);padding:20px;"
     onclick="if(event.target===this)closeReturnModal()">
    <div style="background:#fff;border-radius:24px;max-width:520px;width:100%;max-height:88vh;
                overflow-y:auto;box-shadow:0 24px 56px rgba(0,0,0,.18);position:relative;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #f5f5f4;">
            <div>
                <h2 style="margin:0;font-size:15px;font-weight:700;color:#1c1917;">Return / Refund Request</h2>
                <p style="margin:3px 0 0;font-size:11px;color:#78716c;">Submit your request and we'll review it within 24 hours.</p>
            </div>
            <button onclick="closeReturnModal()"
                    style="width:32px;height:32px;border-radius:8px;background:#f5f5f4;border:0;cursor:pointer;
                           display:grid;place-items:center;color:#78716c;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="m18 6-12 12M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="returnForm" style="padding:24px;display:grid;gap:18px;">

            {{-- Reason --}}
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#1c1917;margin-bottom:8px;">
                    Reason for Return / Refund <span style="color:#dc2626;">*</span>
                </label>
                <div style="display:grid;gap:8px;">
                    @foreach([
                        ['defective',    'Item is defective or damaged'],
                        ['wrong_item',   'Wrong item was delivered'],
                        ['not_described','Item is not as described'],
                        ['missing_parts','Missing parts or accessories'],
                        ['change_mind',  'Changed my mind'],
                        ['other',        'Other reason'],
                    ] as [$val, $label])
                        <label style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #e7e5e4;border-radius:10px;cursor:pointer;font-size:12px;transition:border-color .15s;"
                               onmouseover="this.style.borderColor='#8f1719'" onmouseout="if(!this.querySelector('input').checked)this.style.borderColor='#e7e5e4'"
                               onclick="document.querySelectorAll('#returnForm .reason-label').forEach(l=>{l.style.borderColor='#e7e5e4';l.style.background='#fff'});this.style.borderColor='#8f1719';this.style.background='#faeceb';"
                               class="reason-label">
                            <input type="radio" name="return_reason" value="{{ $val }}" style="accent-color:#8f1719;width:15px;height:15px;flex-shrink:0;">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Solution --}}
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#1c1917;margin-bottom:8px;">
                    Preferred Solution <span style="color:#dc2626;">*</span>
                </label>
                <div style="display:grid;gap:8px;">
                    @foreach([
                        ['refund_only',      'Refund only (keep the item)'],
                        ['return_refund',    'Return item and get a refund'],
                        ['replacement',      'Replace with same item'],
                    ] as [$val, $label])
                        <label style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #e7e5e4;border-radius:10px;cursor:pointer;font-size:12px;transition:border-color .15s;"
                               onmouseover="this.style.borderColor='#8f1719'" onmouseout="if(!this.querySelector('input').checked)this.style.borderColor='#e7e5e4'"
                               onclick="document.querySelectorAll('#returnForm .solution-label').forEach(l=>{l.style.borderColor='#e7e5e4';l.style.background='#fff'});this.style.borderColor='#8f1719';this.style.background='#faeceb';"
                               class="solution-label">
                            <input type="radio" name="return_solution" value="{{ $val }}" style="accent-color:#8f1719;width:15px;height:15px;flex-shrink:0;">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="returnDesc" style="display:block;font-size:11px;font-weight:700;color:#1c1917;margin-bottom:8px;">
                    Describe the Issue <span style="color:#dc2626;">*</span>
                </label>
                <textarea id="returnDesc" name="return_description" rows="3" required
                          placeholder="Describe the issue in detail to help us process your request faster…"
                          style="width:100%;padding:10px 12px;border:1px solid #e7e5e4;border-radius:10px;
                                 font-size:12px;outline:0;resize:vertical;font-family:inherit;
                                 transition:border-color .15s;"
                          onfocus="this.style.borderColor='#8f1719'" onblur="this.style.borderColor='#e7e5e4'"></textarea>
            </div>

            {{-- Evidence upload --}}
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#1c1917;margin-bottom:8px;">
                    Upload Evidence <span style="font-weight:400;color:#78716c;">(optional — photos or videos)</span>
                </label>
                <label style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;
                               padding:20px;border:2px dashed #e7e5e4;border-radius:12px;cursor:pointer;
                               background:#fafaf9;transition:border-color .15s;"
                       onmouseover="this.style.borderColor='#8f1719'" onmouseout="this.style.borderColor='#e7e5e4'">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#a8a097" stroke-width="1.6" stroke-linecap="round">
                        <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                        <path d="m12 12 0 9"/><path d="m8 17 4-5 4 5"/>
                    </svg>
                    <span style="font-size:11px;font-weight:600;color:#57534e;">Click to upload files</span>
                    <span style="font-size:9px;color:#a8a097;">JPG, PNG, MP4 · Max 10MB · Up to 5 files</span>
                    <input type="file" name="return_evidence[]" accept="image/*,video/*" multiple class="sr-only"
                           onchange="previewReturnFiles(this)">
                </label>
                <div id="returnFilesPreview" style="display:none;flex-wrap:wrap;gap:8px;margin-top:10px;"></div>
            </div>

            {{-- Actions --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;padding-top:4px;border-top:1px solid #f5f5f4;">
                <button type="button" onclick="closeReturnModal()"
                        style="height:38px;border:1px solid #e7e5e4;border-radius:10px;background:#fff;
                               font-size:12px;font-weight:600;color:#57534e;cursor:pointer;">
                    Cancel
                </button>
                <button type="button" onclick="submitReturnRequest()"
                        style="height:38px;background:#8f1719;border:0;border-radius:10px;
                               color:#fff;font-size:12px;font-weight:700;cursor:pointer;">
                    Submit Request
                </button>
            </div>

        </form>
    </div>
</div>

<script>
/* ── Return / Refund modal ──────────────────────── */
function openReturnModal() {
    const modal = document.getElementById('returnModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeReturnModal() {
    document.getElementById('returnModal').style.display = 'none';
    document.body.style.overflow = '';
}

function previewReturnFiles(input) {
    const preview = document.getElementById('returnFilesPreview');
    if (!preview || !input.files.length) return;
    preview.style.display = 'flex';
    preview.innerHTML = '';
    Array.from(input.files).slice(0, 5).forEach(file => {
        const tag = document.createElement('span');
        tag.style.cssText = 'display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:#f5f5f4;border-radius:999px;font-size:10px;color:#57534e;';
        tag.textContent = file.name.length > 22 ? file.name.slice(0, 22) + '…' : file.name;
        preview.appendChild(tag);
    });
}

function submitReturnRequest() {
    const form    = document.getElementById('returnForm');
    const reason  = form.querySelector('[name=return_reason]:checked');
    const solution= form.querySelector('[name=return_solution]:checked');
    const desc    = document.getElementById('returnDesc');

    if (!reason)   { alert('Please select a reason.'); return; }
    if (!solution) { alert('Please select a preferred solution.'); return; }
    if (!desc.value.trim()) { desc.focus(); desc.style.borderColor='#dc2626'; return; }

    // Frontend: show confirmation, then close
    closeReturnModal();
    // Show a simple in-page toast — buyer.js handles [data-return-submitted] if wired
    const toast = document.createElement('div');
    toast.textContent = 'Return request submitted. We'll review it within 24 hours.';
    toast.style.cssText = `
        position:fixed;bottom:24px;right:24px;z-index:9999;
        background:#1c1917;color:#fff;font-size:12px;font-weight:500;
        padding:12px 18px;border-radius:12px;
        box-shadow:0 8px 24px rgba(0,0,0,.2);
        animation:lkToastIn .25s ease both;
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

/* ── Sub-rating pickers (seller + delivery) ──────── */
document.addEventListener('DOMContentLoaded', () => {

    function initSubRating(inputSelector, starSelector) {
        const inputs = Array.from(document.querySelectorAll(inputSelector));
        const stars  = Array.from(document.querySelectorAll(starSelector));
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                const val = Number(input.value);
                stars.forEach((star, i) => {
                    const on = i < val;
                    star.classList.toggle('text-amber-500', on);
                    star.classList.toggle('text-stone-300', !on);
                    star.setAttribute('fill', on ? 'currentColor' : 'none');
                });
            });
        });
    }

    initSubRating('[data-seller-rating-input]',   '[data-seller-rating-star]');
    initSubRating('[data-delivery-rating-input]', '[data-delivery-rating-star]');

    /* ── Photo upload preview ────────────────────── */
    const uploadInput = document.querySelector('[data-review-upload]');
    const preview     = document.getElementById('reviewMediaPreview');

    uploadInput?.addEventListener('change', () => {
        if (!preview || !uploadInput.files.length) return;
        preview.classList.remove('hidden');
        preview.style.display = 'flex';
        preview.innerHTML = '';
        Array.from(uploadInput.files).slice(0, 5).forEach(file => {
            const tag = document.createElement('span');
            tag.style.cssText = 'display:inline-flex;align-items:center;gap:6px;padding:5px 12px;background:#f5f5f4;border-radius:999px;font-size:10px;color:#57534e;';
            tag.textContent = file.name.length > 24 ? file.name.slice(0, 24) + '…' : file.name;
            preview.appendChild(tag);
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const orderSearch = document.querySelector(
        '[data-order-search]:not([data-order-card])'
    );

    const orderCards = Array.from(
        document.querySelectorAll('[data-order-card]')
    );

    const noResults = document.querySelector(
        '[data-order-no-results]'
    );

    orderSearch?.addEventListener('input', () => {
        const value = orderSearch.value
            .trim()
            .toLowerCase();

        let visibleOrders = 0;

        orderCards.forEach((card) => {
            const searchText =
                card.dataset.orderSearch || '';

            const isVisible =
                searchText.includes(value);

            card.classList.toggle(
                'hidden',
                !isVisible
            );

            if (isVisible) {
                visibleOrders += 1;
            }
        });

        noResults?.classList.toggle(
            'hidden',
            visibleOrders > 0 || value === ''
        );
    });

    const ratingInputs = Array.from(
        document.querySelectorAll('[data-rating-input]')
    );

    const ratingStars = Array.from(
        document.querySelectorAll('[data-rating-star]')
    );

    const ratingLabel = document.querySelector(
        '[data-rating-label]'
    );

    const ratingLabels = {
        1: 'Very poor',
        2: 'Poor',
        3: 'Good',
        4: 'Very good',
        5: 'Excellent',
    };

    const updateRating = (rating) => {
        ratingStars.forEach((star, index) => {
            const selected = index < rating;

            star.classList.toggle(
                'text-amber-500',
                selected
            );

            star.classList.toggle(
                'text-stone-300',
                !selected
            );

            star.setAttribute(
                'fill',
                selected ? 'currentColor' : 'none'
            );
        });

        if (ratingLabel) {
            ratingLabel.textContent =
                ratingLabels[rating] || 'Select a rating';
        }
    };

    ratingInputs.forEach((input) => {
        input.addEventListener('change', () => {
            updateRating(Number(input.value));
        });
    });

    const reviewMessage = document.querySelector(
        '[data-review-message]'
    );

    const reviewCount = document.querySelector(
        '[data-review-count]'
    );

    reviewMessage?.addEventListener('input', () => {
        if (reviewCount) {
            reviewCount.textContent =
                `${reviewMessage.value.length}/500`;
        }
    });
});
</script>
@endsection