@extends('layouts.buyer')
@section('title', 'My Orders')
@section('active', 'orders')

@section('content')
<style>
    @foreach(range(1, 5) as $selectedCount)
        fieldset:has(input[name="rating"][value="{{ $selectedCount }}"]:checked) label:nth-child(-n+{{ $selectedCount }}) > span,
        fieldset:has(input[name="rider_rating"][value="{{ $selectedCount }}"]:checked) label:nth-child(-n+{{ $selectedCount }}) > span {
            background-color: #facc15 !important;
            border-color: #eab308 !important;
            color: #713f12 !important;
        }
    @endforeach
</style>
@php
    $mode = $mode ?? 'index';
    $orderCollection = isset($orders) && method_exists($orders, 'getCollection') ? $orders->getCollection() : collect($orders ?? []);
    $filter = strtoupper((string) request('status', ''));
    $visibleOrders = $filter === '' || $filter === 'ALL' ? $orderCollection : $orderCollection->where('status', $filter);
@endphp

<div class="lk-page-narrow">
    @if(session('buyer_notice'))<div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800">{{ session('buyer_notice') }}</div>@endif

    @if($mode === 'success')
        <section class="rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm"><div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 text-3xl text-red-900">✓</div><span class="mt-5 block text-[10px] font-bold uppercase tracking-widest text-red-800">Order placed</span><h1 class="mt-2 text-2xl font-bold text-stone-950">Thank you for your order</h1><p class="mt-2 text-sm text-stone-500">{{ $selectedOrder?->order_number ? 'Order '.$selectedOrder->order_number.' was placed successfully.' : 'Your order was placed successfully.' }}</p><div class="mt-6 flex justify-center gap-2"><a class="lk-btn lk-btn-red" href="{{ route('buyer.orders') }}">View My Orders</a><a class="lk-btn lk-btn-light" href="{{ route('buyer.products') }}">Continue Shopping</a></div></section>
    @elseif($mode === 'show' && $selectedOrder)
        @php($detailDelivered = $selectedOrder->sellerOrders->isNotEmpty() && $selectedOrder->sellerOrders->every(fn ($sellerOrder) => in_array($sellerOrder->shipment?->current_status, ['DELIVERED', 'COMPLETED'], true)))
        @php($shippingEvents = $selectedOrder->sellerOrders->flatMap(fn ($sellerOrder) => $sellerOrder->shipment?->events ?? collect())->sortBy('occurred_at'))
        @php($deliveryAssignment = $selectedOrder->sellerOrders->pluck('shipment')->filter()->flatMap->riderAssignments->where('assignment_type', 'DELIVERY')->sortByDesc('id')->first())
        @php($payment = $selectedOrder->payments->first())
        <div class="lk-page-title"><div><span class="lk-kicker">Order details</span><h1>{{ $selectedOrder->order_number }}</h1><p>{{ $detailDelivered && $selectedOrder->status !== 'COMPLETED' ? 'Delivered — please confirm receipt' : str($selectedOrder->status)->headline() }}</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.orders') }}">Back to Orders</a></div>
        <section class="mt-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-stone-900">Shipping information</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Current status</p><strong class="mt-1 block text-red-900">{{ $selectedOrder->sellerOrders->pluck('shipment.current_status')->filter()->map(fn ($status) => str($status)->headline())->join(', ') }}</strong>@if($deliveryAssignment)<p class="mt-2 text-sm text-stone-600">Rider: {{ $deliveryAssignment->riderProfile?->user?->name ?? 'Assigned rider' }}</p>@endif</div>
                <div><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Delivery information</p><strong class="mt-1 block">{{ $selectedOrder->address?->recipient_name }}</strong><p class="text-sm text-stone-600">{{ $selectedOrder->address?->contact_number }}<br>{{ $selectedOrder->address?->formatted() }}</p></div>
            </div>
            @php($completedEvent = $shippingEvents->where('status', 'COMPLETED')->sortBy('occurred_at')->last())
            <div class="mt-4 border-t border-stone-100 pt-3">@if($completedEvent)<div class="flex items-start justify-between gap-3 text-xs"><div><strong class="font-semibold">Completed</strong><p class="leading-4 text-stone-500">{{ $completedEvent->notes }}</p></div><time class="whitespace-nowrap text-[10px] text-stone-500">{{ $completedEvent->occurred_at?->format('M j, Y g:i A') }}</time></div>@else<p class="text-xs text-stone-500">Completion update will appear here.</p>@endif</div>
        </section>
        @foreach($selectedOrder->sellerOrders as $sellerOrder)
            <div class="mt-5 flex items-center justify-between"><div><span class="text-xs font-semibold uppercase tracking-wide text-stone-500">Store</span><h2 class="text-lg font-bold text-stone-900">{{ $sellerOrder->sellerProfile?->business_name }}</h2></div><a href="{{ route('buyer.messages') }}" class="text-sm font-semibold text-red-900">Contact Seller</a></div>
            <section class="mt-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><h2 class="font-bold">{{ $sellerOrder->seller_order_number }}</h2><span class="text-xs font-semibold">{{ str($sellerOrder->status)->headline() }}</span></div><div class="mt-4 divide-y divide-stone-100">@foreach($sellerOrder->items as $item)<div class="flex items-center justify-between gap-4 py-3 text-sm"><div class="flex items-center gap-3"><x-product-thumbnail :item="$item" size="64"/><div><strong>{{ $item->product_name }}</strong><span class="block text-xs text-stone-500">{{ $item->variant_description ?: 'Standard' }} · Qty {{ $item->quantity }}</span></div></div><strong>&#8369;{{ number_format((float) $item->line_total, 2) }}</strong></div>@endforeach</div><div class="mt-3 flex justify-between border-t border-stone-100 pt-3 text-sm"><span class="font-semibold">Order total</span><strong>&#8369;{{ number_format((float) $sellerOrder->grand_total, 2) }}</strong></div>@if($sellerOrder->shipment)<p class="mt-3 text-xs text-stone-500">Tracking: {{ $sellerOrder->shipment->tracking_number }} · {{ str($sellerOrder->shipment->current_status)->headline() }}</p>@endif</section>
        @endforeach
        @php($allDelivered = $detailDelivered)
        @php($hasPickedUpParcel = $selectedOrder->sellerOrders->contains(fn ($sellerOrder) => in_array($sellerOrder->shipment?->current_status, ['PICKED_UP', 'AT_SORTING_CENTER', 'SORTED', 'ASSIGNED_TO_RIDER', 'OUT_FOR_DELIVERY', 'DELIVERED', 'COMPLETED'], true)))
        @php($deliveredAt = $selectedOrder->sellerOrders->flatMap(fn ($sellerOrder) => $sellerOrder->shipment?->events ?? collect())->where('status', 'DELIVERED')->max('occurred_at'))
        @php($returnEligible = in_array($selectedOrder->status, ['PROCESSING', 'COMPLETED'], true) && $allDelivered && $deliveredAt && now()->lte($deliveredAt->copy()->addDays(5)))
        @php($hasActiveReturn = $selectedOrder->disputes->where('type', 'RETURN_REFUND')->whereIn('status', ['OPEN', 'UNDER_REVIEW'])->isNotEmpty())
        <div class="mt-5 flex flex-wrap justify-end gap-2">
            @if(!$hasPickedUpParcel && in_array($selectedOrder->status, ['PLACED', 'PROCESSING'], true))<form method="POST" action="{{ route('buyer.orders.cancel', $selectedOrder) }}">@csrf<button class="lk-btn lk-btn-light" type="submit">Cancel Order</button></form>@endif
            @if($returnEligible && !$hasActiveReturn)<details class="relative"><summary class="lk-btn lk-btn-light cursor-pointer list-none">Return / Refund</summary><form method="POST" action="{{ route('buyer.orders.return-refund', $selectedOrder) }}" class="mt-2 grid min-w-72 gap-2 rounded-xl border border-stone-200 bg-white p-4 shadow-lg">@csrf<label class="text-xs font-semibold text-stone-700">Reason for return or refund</label><textarea name="reason" required minlength="10" maxlength="2000" rows="4" class="rounded-lg border border-stone-300 p-3 text-sm" placeholder="Describe the issue with your order"></textarea><button class="lk-btn lk-btn-light" type="submit">Submit Request</button><small class="text-stone-500">Available until {{ $deliveredAt->copy()->addDays(5)->format('M j, Y g:i A') }}.</small></form></details>@endif
            @if($hasActiveReturn)<span class="lk-btn lk-btn-light">Return / Refund Under Review</span>@endif
            @if($selectedOrder->status !== 'COMPLETED' && $allDelivered && !$hasActiveReturn)<form method="POST" action="{{ route('buyer.orders.received', $selectedOrder) }}">@csrf<button class="lk-btn lk-btn-red" type="submit">Confirm Order Received</button></form>@endif
        </div>
        <section class="mt-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-stone-900">Order and payment information</h2>
            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2"><div><dt class="text-stone-500">Order ID</dt><dd class="font-semibold">{{ $selectedOrder->order_number }}</dd></div><div><dt class="text-stone-500">Payment method</dt><dd class="font-semibold">{{ $payment?->method === 'COD' ? 'Cash on Delivery (COD)' : str($payment?->method ?? 'Pending')->headline() }}</dd></div><div><dt class="text-stone-500">Ordered</dt><dd class="font-semibold">{{ $selectedOrder->placed_at?->format('M j, Y g:i A') }}</dd></div><div><dt class="text-stone-500">Payment time</dt><dd class="font-semibold">{{ ($payment?->paid_at ?: $payment?->initiated_at)?->format('M j, Y g:i A') ?? 'Pending' }}</dd></div><div><dt class="text-stone-500">Shipped / picked up</dt><dd class="font-semibold">{{ $shippingEvents->firstWhere('status', 'PICKED_UP')?->occurred_at?->format('M j, Y g:i A') ?? 'Pending' }}</dd></div><div><dt class="text-stone-500">Completed</dt><dd class="font-semibold">{{ $selectedOrder->completed_at?->format('M j, Y g:i A') ?? 'Pending confirmation' }}</dd></div></dl>
        </section>
        <section class="mt-5 grid gap-3 sm:grid-cols-2"><a href="{{ route('buyer.messages') }}" class="lk-btn lk-btn-light justify-center">Contact Seller</a><a href="{{ route('buyer.messages') }}" class="lk-btn lk-btn-light justify-center">Help Center / Admin Support</a></section>
        @if($selectedOrder->status === 'COMPLETED')
            <section id="reviews" class="mt-6 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-stone-900">Rate and review your order</h2>
                <p class="mt-1 text-sm text-stone-500">Ratings and photos lock after your first submission. Review text can still be edited.</p>
                @if($errors->any())<p class="mt-2 text-sm font-medium text-red-700">{{ $errors->first() }}</p>@endif
                <div class="mt-4 space-y-4">
                    @foreach($selectedOrder->sellerOrders->flatMap->items as $item)
                        @php($deliveryAssignment = $item->sellerOrder->shipment?->riderAssignments->where('assignment_type', 'DELIVERY')->sortByDesc('id')->first())
                        <form method="POST" enctype="multipart/form-data" action="{{ route('buyer.orders.review.store', $item) }}" class="grid gap-3 rounded-xl border border-stone-200 p-4">
                            @csrf
                            <div class="flex items-center gap-3">
                                <x-product-thumbnail :item="$item" size="56"/>
                                <div>
                                    <strong class="block">{{ $item->product_name }}</strong>
                                    <small class="text-stone-500">{{ $item->variant_description ?: 'Standard' }} · Qty {{ $item->quantity }}</small>
                                </div>
                            </div>

                            <fieldset>
                                <legend class="text-sm font-semibold">Product rating</legend>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach(range(1, 5) as $rating)
                                        <label class="cursor-pointer">
                                            <input class="peer sr-only" type="radio" name="rating" value="{{ $rating }}" @checked((int) $item->review?->rating === $rating) @disabled($item->review)>
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-300 text-lg text-yellow-500 hover:border-yellow-400 hover:bg-yellow-50 peer-checked:border-yellow-500 peer-checked:bg-yellow-400 peer-checked:text-yellow-950">★</span>
                                            <span class="sr-only">{{ $rating }} product stars</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>

                            @if($item->sellerOrder->items->first()?->id === $item->id && $deliveryAssignment)
                                <fieldset>
                                    <legend class="text-sm font-semibold">Delivery rider rating <span class="font-normal text-stone-500">{{ $deliveryAssignment->riderProfile?->user?->name }}</span></legend>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach(range(1, 5) as $rating)
                                            <label class="cursor-pointer">
                                                <input class="peer sr-only" type="radio" name="rider_rating" value="{{ $rating }}" @checked(($riderRatingsByShipment[$item->sellerOrder->shipment->id] ?? null) === $rating) @disabled($item->review)>
                                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-300 text-lg text-yellow-500 hover:border-yellow-400 hover:bg-yellow-50 peer-checked:border-yellow-500 peer-checked:bg-yellow-400 peer-checked:text-yellow-950">★</span>
                                                <span class="sr-only">{{ $rating }} rider stars</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                                <label class="grid gap-1 text-sm font-medium">Rider review (optional)
                                    <textarea name="rider_comment" maxlength="2000" rows="2" class="rounded-lg border border-stone-300 p-3 text-sm" placeholder="Comment on the delivery experience">{{ $item->review?->rider_comment }}</textarea>
                                </label>
                            @endif

                            @if(isset($reviewImagesById[$item->review?->id]))
                                <img src="{{ $reviewImagesById[$item->review->id] }}" alt="Current review photo" class="h-24 w-24 rounded-lg object-cover">
                            @endif

                            <label class="grid gap-1 text-sm font-medium">Add a product photo
                                <span class="text-xs font-normal text-stone-500">JPG, PNG, or WebP, up to 5 MB</span>
                                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-stone-100 file:px-3 file:py-2" @disabled($item->review)>
                            </label>
                            <textarea name="comment" maxlength="2000" rows="3" class="rounded-lg border border-stone-300 p-3 text-sm" placeholder="Write a product review (optional)">{{ $item->review?->comment }}</textarea>
                            <button class="lk-btn lk-btn-red justify-self-start" type="submit">{{ $item->review ? 'Update Review' : 'Submit Rating' }}</button>
                        </form>
                    @endforeach
                </div>
            </section>
        @endif
    @else
        <div class="lk-page-title"><div><span class="lk-kicker">Order Center</span><h1>My Orders</h1><p>Track fulfillment and confirm receipt after delivery.</p></div><a class="lk-btn lk-btn-light" href="{{ route('buyer.products') }}">Shop Products</a></div>
        <nav class="mt-4 flex gap-1 overflow-x-auto rounded-xl border border-stone-200 bg-white p-1 shadow-sm">@foreach(['' => 'All', 'PLACED' => 'Placed', 'PROCESSING' => 'Processing', 'COMPLETED' => 'Completed', 'CANCELLED' => 'Cancelled'] as $key => $label)<a href="{{ route('buyer.orders', ['status' => $key]) }}" class="rounded-lg px-3.5 py-2.5 text-xs font-semibold {{ $filter === $key ? 'bg-red-900 text-white' : 'text-stone-600' }}">{{ $label }}</a>@endforeach</nav>
        <div class="mt-5 space-y-4">@forelse($visibleOrders as $order)@php($displayStatus = $order->status === 'PROCESSING' && $order->sellerOrders->isNotEmpty() && $order->sellerOrders->every(fn ($sellerOrder) => in_array($sellerOrder->shipment?->current_status, ['DELIVERED', 'COMPLETED'], true)) ? 'DELIVERED' : $order->status)<article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><div class="flex flex-wrap items-start justify-between gap-3"><div><span class="text-xs text-stone-500">{{ optional($order->placed_at)->format('M j, Y g:i A') }}</span><h2 class="mt-1 font-bold text-stone-900">{{ $order->order_number }}</h2></div><span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold">{{ str($displayStatus)->headline() }}</span></div><div class="mt-4 flex items-end justify-between border-t border-stone-100 pt-4"><div><span class="block text-xs text-stone-500">{{ $order->sellerOrders->sum(fn ($sellerOrder) => $sellerOrder->items->sum('quantity')) }} item(s)</span><strong>&#8369;{{ number_format((float) $order->grand_total, 2) }}</strong></div><a class="lk-btn lk-btn-red" href="{{ route('buyer.orders.show', $order) }}">View Details</a></div></article>@empty<x-buyer.empty-state title="No orders found" message="Your matching orders will appear here." />@endforelse</div>
        @if(isset($orders) && method_exists($orders, 'links'))<div class="mt-5">{{ $orders->links() }}</div>@endif
    @endif
</div>
@endsection
