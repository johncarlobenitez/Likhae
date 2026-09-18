<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Delivery;
use App\Models\Message;
use App\Models\PlatformSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariation;
use App\Models\Refund;
use App\Models\SellerCampaign;
use App\Models\SellerProfile;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WorkspaceNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SellerController extends Controller
{
    public function dashboard(Request $request): View
    {
        $seller = $this->seller($request);
        $orders = $this->sellerOrdersQuery($seller)->get();
        $products = $this->sellerProductsQuery($seller)->get();

        $today = now()->startOfDay();
        $todaySales = (float) $orders->filter(fn (Order $order) => $order->created_at?->gte($today) && $this->isRevenueOrder($order))->sum('total_amount');
        $revenue = (float) $orders->filter(fn (Order $order) => $this->isRevenueOrder($order))->sum('total_amount');
        $productsSold = (int) $orders->sum(fn (Order $order) => $order->items->sum('quantity'));
        $pendingShipment = $orders->filter(fn (Order $order) => in_array($this->canonicalOrderStatus($order->status), ['to_prepare', 'ready_pickup'], true))->count();
        $inventoryAlerts = $products->filter(fn (Product $product) => $product->stock <= 5)->count();

        $chart = collect(range(6, 0))->map(function ($offset) use ($orders) {
            $day = now()->subDays($offset);
            $value = (float) $orders
                ->filter(fn (Order $order) => $order->created_at?->isSameDay($day) && $this->isRevenueOrder($order))
                ->sum('total_amount');

            return [
                'label' => $day->format('D'),
                'value' => $value,
            ];
        });

        $maxChart = max(1, (float) $chart->max('value'));
        $salesChart = $chart->map(fn ($point) => array_merge($point, [
            'height' => max(8, (int) round(($point['value'] / $maxChart) * 100)),
        ]));

        $statusCounts = [
            'placed' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'to_process')->count(),
            'preparing' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'to_prepare')->count(),
            'ready-for-pickup' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'ready_pickup')->count(),
            'shipping' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'shipping')->count(),
            'completed' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'completed')->count(),
            'returns' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'returns')->count(),
        ];

        return view('Seller.dashboard', [
            'seller' => $seller,
            'dashboardStats' => [
                'today_sales' => $todaySales,
                'orders' => $orders->count(),
                'revenue' => $revenue,
                'products_sold' => $productsSold,
                'pending_shipment' => $pendingShipment,
                'inventory_alerts' => $inventoryAlerts,
            ],
            'salesChart' => $salesChart,
            'orderStatusCounts' => $statusCounts,
            'sellerOrders' => $orders->take(5)->map(fn (Order $order) => $this->orderViewData($order)),
            'inventoryAlerts' => $products->sortBy('stock')->take(3)->map(fn (Product $product) => $this->productViewData($product)),
        ]);
    }

    public function products(Request $request): View
    {
        $seller = $this->seller($request);
        $products = $this->sellerProductsQuery($seller)->get();
        $selected = null;

        if ($request->filled('product')) {
            $selected = $products->first(fn (Product $product) =>
                (string) $product->id === (string) $request->input('product')
                || $product->sku === $request->input('product')
            );
        }

        return view('Seller.products', [
            'mode' => $request->input('mode', 'list'),
            'selectedProduct' => $selected ? (string) $selected->id : $request->input('product'),
            'sellerProducts' => $products->map(fn (Product $product) => $this->productViewData($product)),
            'lineOfBusinessCategory' => $this->sellerLineOfBusinessCategory($seller),
            'categories' => $this->sellerSubcategories($seller),
        ]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $seller = $this->seller($request);
        $validated = $this->validateProduct($request, null, $seller);

        $product = new Product();
        $product->seller_id = $seller->id;
        $this->fillProduct($product, $validated, $request);
        $product->save();
        $this->syncProductVariations($product, $request);
        $this->syncProductSpecifications($product, $request);
        $this->storeProductImageIfPresent($request, $seller, $product);
        if ($product->isDirty('image_path')) {
            $product->save();
        }

        return redirect()->route('seller.products')
            ->with('status', "{$product->name} was published successfully.");
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $seller = $this->seller($request);
        $this->authorizeProduct($seller, $product);
        $validated = $this->validateProduct($request, $product, $seller);

        $this->fillProduct($product, $validated, $request);
        $product->save();
        $this->syncProductVariations($product, $request);
        $this->syncProductSpecifications($product, $request);
        $this->storeProductImageIfPresent($request, $seller, $product);
        if ($product->isDirty('image_path')) {
            $product->save();
        }

        return redirect()->route('seller.products', ['mode' => 'edit', 'product' => $product->id])
            ->with('status', 'Product changes saved.');
    }

    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        $seller = $this->seller($request);
        $this->authorizeProduct($seller, $product);
        $validated = $request->validate(['stock' => ['required', 'integer', 'min:0', 'max:999999']]);

        $product->stock = $validated['stock'];
        $product->save();

        return back()->with('status', "Stock updated for {$product->name}.");
    }

    public function toggleProduct(Request $request, Product $product): RedirectResponse
    {
        $seller = $this->seller($request);
        $this->authorizeProduct($seller, $product);
        $currentStatus = $product->listing_status ?: $product->status;
        $product->listing_status = $currentStatus === 'archived' ? 'active' : 'archived';
        $product->status = $product->listing_status;
        $product->save();

        return back()->with('status', $product->listing_status === 'archived' ? 'Product archived.' : 'Product restored.');
    }

    public function exportProducts(Request $request): StreamedResponse
    {
        $seller = $this->seller($request);
        $products = $this->sellerProductsQuery($seller)->get();

        return $this->csv('seller-products-'.now()->format('Ymd-His').'.csv', [
            ['ID', 'SKU', 'Product', 'Category', 'Price', 'Stock', 'Listing Status', 'Admin Status'],
            ...$products->map(fn (Product $product) => [
                $product->id,
                $product->sku,
                $product->name,
                $product->category?->name,
                $product->price,
                $product->stock,
                $product->listing_status ?: $product->status,
                $product->admin_status ?: 'approved',
            ])->all(),
        ]);
    }

    public function orders(Request $request): View
    {
        $seller = $this->seller($request);
        $orders = $this->sellerOrdersQuery($seller)->get();
        $status = $request->input('status', 'all');
        $mode = $request->input('mode', 'index');
        $selected = $this->findOrder($orders, $request->input('order')) ?? $orders->first();

        return view('Seller.orders', [
            'pageMode' => 'orders',
            'mode' => $mode,
            'status' => $status,
            'selectedOrder' => $selected ? $selected->order_number : null,
            'sellerOrders' => $orders->map(fn (Order $order) => $this->orderViewData($order)),
            'statusCounts' => $this->orderCounts($orders),
        ]);
    }

    public function updateOrderStatus(Request $request, Order $order): RedirectResponse
    {
        $seller = $this->seller($request);
        $this->authorizeOrder($seller, $order);
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                'placed', 'confirmed', 'preparing', 'ready_for_pickup',
                'to_process', 'to_prepare', 'ready_pickup',
                'shipping', 'completed', 'cancelled', 'returns',
            ])],
        ]);

        $workflowStatus = fn (?string $status) => match ($status) {
            'pending', 'to_process' => 'placed',
            'to_prepare' => 'preparing',
            'ready_pickup' => 'ready_for_pickup',
            default => $status,
        };
        $currentStatus = $workflowStatus($order->status);
        $targetStatus = $workflowStatus($validated['status']);

        $allowed = [
            'placed' => ['confirmed', 'cancelled'],
            'confirmed' => ['preparing', 'cancelled'],
            'preparing' => ['ready_for_pickup', 'cancelled'],
            'ready_for_pickup' => ['shipping', 'cancelled'],
            'shipping' => ['completed', 'returns'],
            'completed' => ['returns'],
            'returns' => ['completed'],
            'cancelled' => [],
        ];

        if (! in_array($targetStatus, $allowed[$currentStatus] ?? [], true) && $targetStatus !== $currentStatus) {
            return back()->withErrors(['status' => 'That order status transition is not allowed.']);
        }

        $order->status = $targetStatus;
        $order->save();

        if ($order->status === 'completed') {
            $order->transaction()->updateOrCreate(
                ['order_id' => $order->id],
                [
                    'transaction_number' => 'TXN-'.now()->format('Ymd').'-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT),
                    'buyer_id' => $order->buyer_id,
                    'amount' => $order->total_amount,
                    'method' => $order->payment_method ?: 'cod',
                    'status' => 'paid',
                ]
            );
        }

        WorkspaceNotification::create([
            'user_id' => $order->buyer_id,
            'type' => 'orders',
            'title' => 'Order status updated',
            'body' => "Order {$order->order_number} is now {$this->statusLabel($order->status)}.",
            'action_url' => null,
        ]);

        return back()->with('status', 'Order moved to '.$this->statusLabel($order->status).'.');
    }

    public function exportOrders(Request $request): StreamedResponse
    {
        $seller = $this->seller($request);
        $orders = $this->sellerOrdersQuery($seller)->get();

        return $this->csv('seller-orders-'.now()->format('Ymd-His').'.csv', [
            ['Order', 'Buyer', 'Total', 'Payment', 'Status', 'Placed'],
            ...$orders->map(fn (Order $order) => [
                $order->order_number,
                $order->buyer?->name,
                $order->total_amount,
                $order->payment_method,
                $this->statusLabel($order->status),
                $order->created_at?->toDateTimeString(),
            ])->all(),
        ]);
    }

    public function waybill(Request $request, Order $order): View
    {
        $seller = $this->seller($request);
        $this->authorizeOrder($seller, $order);
        $order->load(['buyer', 'items.product', 'delivery']);

        return view('Seller.waybill', [
            'order' => $order,
            'seller' => $seller,
        ]);
    }

    public function logistics(Request $request): View
    {
        $seller = $this->seller($request);
        $orders = $this->sellerOrdersQuery($seller)->get();
        $selected = $this->findOrder($orders, $request->input('order'))
            ?? $orders->firstWhere('status', 'ready_pickup')
            ?? $orders->first();

        $deliveries = Delivery::with(['order.buyer', 'rider'])
            ->whereHas('order', fn ($query) => $query->where('seller_id', $seller->id))
            ->latest()
            ->get();

        return view('Seller.orders', [
            'pageMode' => 'logistics',
            'mode' => $request->input('view', 'couriers'),
            'status' => $request->input('status', 'all'),
            'selectedOrder' => $selected?->order_number,
            'sellerOrders' => $orders->map(fn (Order $order) => $this->orderViewData($order)),
            'deliveries' => $deliveries,
            'riders' => User::whereIn('role', ['rider', 'courier'])->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function requestPickup(Request $request, Order $order): RedirectResponse
    {
        $seller = $this->seller($request);
        $this->authorizeOrder($seller, $order);
        abort_unless(in_array($this->canonicalOrderStatus($order->status), ['ready_pickup', 'shipping'], true), 422, 'The order must be prepared before requesting pickup.');

        $validated = $request->validate([
            'handover_method' => ['required', Rule::in(['logistics_pickup', 'seller_dropoff'])],
            'pickup_date' => ['nullable', 'required_if:handover_method,logistics_pickup', 'date'],
            'pickup_window' => ['nullable', 'required_if:handover_method,logistics_pickup', 'string', 'max:80'],
            'pickup_note' => ['nullable', 'string', 'max:1000'],
        ]);
        $isPickup = $validated['handover_method'] === 'logistics_pickup';

        $delivery = Delivery::updateOrCreate(
            ['order_id' => $order->id],
            [
                'address' => $order->shipping_address ?: 'Buyer delivery address',
                'provider' => 'LIKHAE Logistics',
                'tracking_number' => optional($order->delivery)->tracking_number ?: 'LH-'.now()->format('Ymd').'-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT),
                'handover_method' => $validated['handover_method'],
                'pickup_window' => $isPickup
                    ? Carbon::parse($validated['pickup_date'])->format('M d, Y').' - '.$validated['pickup_window']
                    : null,
                'pickup_note' => $validated['pickup_note'] ?? null,
                'requested_at' => now(),
                'status' => $isPickup ? 'awaiting_pickup_assignment' : 'awaiting_dropoff',
            ]
        );

        $order->status = 'shipping';
        $order->save();

        return redirect()->route('seller.logistics', ['view' => 'tracking', 'order' => $order->order_number])
            ->with('status', $isPickup
                ? 'Logistics pickup requested. The parcel is waiting for rider assignment.'
                : 'Seller drop-off selected. Bring the parcel to the logistics center.');
    }

    public function messages(Request $request): View
    {
        $seller = $this->seller($request);

        $buyerIds = Message::where('sender_id', $seller->id)
            ->orWhere('recipient_id', $seller->id)
            ->get()
            ->flatMap(fn (Message $message) => [$message->sender_id, $message->recipient_id])
            ->reject(fn ($id) => (int) $id === (int) $seller->id)
            ->merge(Order::where('seller_id', $seller->id)->pluck('buyer_id'))
            ->unique()
            ->values();

        $buyers = User::where('role', 'buyer')
            ->where('status', 'active')
            ->whereIn('id', $buyerIds)
            ->orderBy('name')
            ->get();

        $conversationBuyerId = (int) $request->input('buyer', $buyers->first()?->id);
        $conversationBuyer = $buyers->firstWhere('id', $conversationBuyerId) ?? $buyers->first();

        $messages = collect();
        if ($conversationBuyer) {
            $messages = Message::with(['sender', 'recipient', 'order'])
                ->where(function ($query) use ($seller, $conversationBuyer) {
                    $query->where('sender_id', $seller->id)->where('recipient_id', $conversationBuyer->id);
                })
                ->orWhere(function ($query) use ($seller, $conversationBuyer) {
                    $query->where('sender_id', $conversationBuyer->id)->where('recipient_id', $seller->id);
                })
                ->orderBy('created_at')
                ->get();

            Message::where('sender_id', $conversationBuyer->id)
                ->where('recipient_id', $seller->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $conversationRows = $buyers->map(function (User $buyer) use ($seller) {
            $last = Message::where(function ($query) use ($seller, $buyer) {
                $query->where('sender_id', $seller->id)->where('recipient_id', $buyer->id);
            })->orWhere(function ($query) use ($seller, $buyer) {
                $query->where('sender_id', $buyer->id)->where('recipient_id', $seller->id);
            })->latest()->first();

            return [
                'buyer' => $buyer,
                'last' => $last,
                'unread' => Message::where('sender_id', $buyer->id)->where('recipient_id', $seller->id)->whereNull('read_at')->count(),
            ];
        })->sortByDesc(fn ($row) => $row['last']?->created_at?->timestamp ?? 0)->values();

        return view('Seller.messages', [
            'mode' => 'messages',
            'seller' => $seller,
            'conversationRows' => $conversationRows,
            'conversationBuyer' => $conversationBuyer,
            'chatMessages' => $messages,
        ]);
    }

    public function sendMessage(Request $request): RedirectResponse|JsonResponse
    {
        $seller = $this->seller($request);
        $validated = $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $buyer = User::whereKey($validated['recipient_id'])->where('role', 'buyer')->firstOrFail();
        $orderId = null;

        if (! empty($validated['order_id'])) {
            $order = Order::whereKey($validated['order_id'])
                ->where('seller_id', $seller->id)
                ->where('buyer_id', $buyer->id)
                ->firstOrFail();

            $orderId = $order->id;
        }

        $message = Message::create([
            'sender_id' => $seller->id,
            'recipient_id' => $buyer->id,
            'order_id' => $orderId,
            'body' => $validated['body'],
        ]);

        WorkspaceNotification::create([
            'user_id' => $buyer->id,
            'type' => 'message',
            'title' => 'New seller message',
            'body' => "Message from {$seller->name}",
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => $this->messagePayload($message, $seller->id)], 201);
        }

        return redirect()->route('seller.messages', ['buyer' => $buyer->id])->with('status', 'Message sent.');
    }

    public function messageStream(Request $request): StreamedResponse
    {
        $seller = $this->seller($request);
        $buyerId = (int) $request->query('buyer_id');
        $buyer = User::whereKey($buyerId)->where('role', 'buyer')->where('status', 'active')->firstOrFail();
        $hasRelationship = Order::where('seller_id', $seller->id)->where('buyer_id', $buyer->id)->exists()
            || Message::where(function ($query) use ($seller, $buyer) {
                $query->where('sender_id', $seller->id)->where('recipient_id', $buyer->id);
            })->orWhere(function ($query) use ($seller, $buyer) {
                $query->where('sender_id', $buyer->id)->where('recipient_id', $seller->id);
            })->exists();

        abort_unless($hasRelationship, 403);

        $lastId = max(0, (int) $request->query('after', 0));

        return response()->stream(function () use ($seller, $buyer, $lastId): void {
            $after = $lastId;
            $started = now();

            while (! connection_aborted() && now()->diffInSeconds($started) < 60) {
                $messages = Message::with('sender')
                    ->where('id', '>', $after)
                    ->where(function ($query) use ($seller, $buyer) {
                        $query->where(function ($q) use ($seller, $buyer) {
                            $q->where('sender_id', $seller->id)->where('recipient_id', $buyer->id);
                        })->orWhere(function ($q) use ($seller, $buyer) {
                            $q->where('sender_id', $buyer->id)->where('recipient_id', $seller->id);
                        });
                    })
                    ->orderBy('id')
                    ->limit(50)
                    ->get();

                foreach ($messages as $message) {
                    $after = max($after, (int) $message->id);
                    echo "event: message\n";
                    echo 'data: '.json_encode($this->messagePayload($message, $seller->id))."\n\n";
                }

                if ($messages->isEmpty()) {
                    echo "event: heartbeat\n";
                    echo 'data: '.json_encode(['after' => $after])."\n\n";
                }

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
                sleep(2);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function reviews(Request $request): View
    {
        $seller = $this->seller($request);
        $reviews = ProductReview::with(['buyer', 'product'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->get();

        return view('Seller.messages', [
            'mode' => 'reviews',
            'reviews' => $reviews,
            'reviewStats' => [
                'average' => round((float) $reviews->avg('rating'), 1),
                'count' => $reviews->count(),
                'new' => $reviews->where('created_at', '>=', now()->subDays(7))->count(),
                'awaiting' => $reviews->whereNull('reply')->count(),
                'with_photos' => $reviews->count() ? round($reviews->where('has_photo', true)->count() / $reviews->count() * 100) : 0,
                'positive' => $reviews->count() ? round($reviews->where('rating', '>=', 4)->count() / $reviews->count() * 100) : 0,
            ],
            'ratingBars' => collect(range(5, 1))->mapWithKeys(function ($rating) use ($reviews) {
                $count = $reviews->where('rating', $rating)->count();
                return [$rating => $reviews->count() ? (int) round($count / $reviews->count() * 100) : 0];
            }),
        ]);
    }

    public function replyReview(Request $request, ProductReview $review): RedirectResponse
    {
        $seller = $this->seller($request);
        abort_unless($review->seller_id === $seller->id, 403);
        $validated = $request->validate(['reply' => ['required', 'string', 'max:1200']]);
        $review->update(['reply' => $validated['reply'], 'replied_at' => now()]);

        return back()->with('status', 'Review reply published.');
    }

    public function exportReviews(Request $request): StreamedResponse
    {
        $seller = $this->seller($request);
        $reviews = ProductReview::with(['buyer', 'product'])->where('seller_id', $seller->id)->latest()->get();

        return $this->csv('seller-reviews-'.now()->format('Ymd-His').'.csv', [
            ['Buyer', 'Product', 'Rating', 'Review', 'Reply', 'Date'],
            ...$reviews->map(fn (ProductReview $review) => [
                $review->buyer?->name,
                $review->product?->name,
                $review->rating,
                $review->body,
                $review->reply,
                $review->created_at?->toDateTimeString(),
            ])->all(),
        ]);
    }

    public function marketing(Request $request): View
    {
        $seller = $this->seller($request);
        $tab = $request->input('tab', 'discounts');

        return view('Seller.marketing', [
            'tab' => $tab,
            'campaigns' => SellerCampaign::where('seller_id', $seller->id)->latest()->get(),
        ]);
    }

    public function storeCampaign(Request $request): RedirectResponse
    {
        $seller = $this->seller($request);
        $validated = $request->validate([
            'type' => ['required', Rule::in(['discount', 'voucher', 'promotion'])],
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:40'],
            'discount_type' => ['required', Rule::in(['percent', 'fixed'])],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'minimum_spend' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $validated['seller_id'] = $seller->id;
        $validated['status'] = 'active';
        SellerCampaign::create($validated);

        return redirect()->route('seller.marketing', ['tab' => $this->campaignTab($validated['type'])])
            ->with('status', 'Campaign created successfully.');
    }

    public function toggleCampaign(Request $request, SellerCampaign $campaign): RedirectResponse
    {
        $seller = $this->seller($request);
        abort_unless($campaign->seller_id === $seller->id, 403);
        $campaign->status = $campaign->status === 'active' ? 'paused' : 'active';
        $campaign->save();

        return back()->with('status', 'Campaign status updated.');
    }

    public function finance(Request $request): View
    {
        $seller = $this->seller($request);
        $orders = $this->sellerOrdersQuery($seller)->get();
        $transactions = Transaction::with('order')
            ->whereHas('order', fn ($query) => $query->where('seller_id', $seller->id))
            ->latest()
            ->get();

        $gross = $this->grossRevenue($orders);
        $commissionRate = $this->commissionRate();
        $commission = $this->commissionFor($gross);
        $net = $gross - $commission;
        $pending = (float) $orders->filter(fn (Order $order) => in_array($this->canonicalOrderStatus($order->status), ['to_process', 'to_prepare', 'ready_pickup', 'shipping'], true))->sum('total_amount');

        $trend = collect(range(29, 0))->map(function ($offset) use ($orders) {
            $day = now()->subDays($offset);
            return [
                'date' => $day,
                'value' => (float) $orders->filter(fn (Order $order) => $order->created_at?->isSameDay($day) && $this->isRevenueOrder($order))->sum('total_amount'),
            ];
        });

        return view('Seller.finance', [
            'tab' => $request->input('tab', 'sales'),
            'financeStats' => compact('gross', 'commission', 'net', 'pending'),
            'commissionRate' => $commissionRate,
            'transactions' => $transactions,
            'financeTrend' => $trend,
        ]);
    }

    public function exportStatement(Request $request): StreamedResponse
    {
        $seller = $this->seller($request);
        $transactions = Transaction::with('order')
            ->whereHas('order', fn ($query) => $query->where('seller_id', $seller->id))
            ->latest()
            ->get();

        return $this->csv('seller-finance-statement-'.now()->format('Ymd-His').'.csv', [
            ['Transaction', 'Order', 'Gross Amount', 'Commission', 'Net Amount', 'Method', 'Status', 'Date'],
            ...$transactions->map(fn (Transaction $transaction) => [
                $transaction->transaction_number,
                $transaction->order?->order_number,
                $transaction->amount,
                $this->commissionFor((float) $transaction->amount),
                (float) $transaction->amount - $this->commissionFor((float) $transaction->amount),
                $transaction->method,
                $transaction->status,
                $transaction->created_at?->toDateTimeString(),
            ])->all(),
        ]);
    }

    public function reports(Request $request): View
    {
        $seller = $this->seller($request);
        $report = $request->input('report', 'sales');
        $from = Carbon::parse($request->input('from', now()->subDays(30)->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();
        $orders = $this->sellerOrdersQuery($seller)->whereBetween('created_at', [$from, $to])->get();

        return view('Seller.reports', [
            'report' => $report,
            'reportSummary' => $this->reportSummary($orders),
            'reportFrom' => $from,
            'reportTo' => $to,
        ]);
    }

    public function downloadReport(Request $request): StreamedResponse
    {
        $seller = $this->seller($request);
        $validated = $request->validate([
            'report' => ['required', Rule::in(['sales', 'profit', 'orders', 'products'])],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);
        $orders = $this->sellerOrdersQuery($seller)
            ->whereBetween('created_at', [Carbon::parse($validated['from'])->startOfDay(), Carbon::parse($validated['to'])->endOfDay()])
            ->get();

        $rows = [['Order', 'Buyer', 'Status', 'Total', 'Placed']];
        foreach ($orders as $order) {
            $rows[] = [$order->order_number, $order->buyer?->name, $this->statusLabel($order->status), $order->total_amount, $order->created_at?->toDateTimeString()];
        }

        return $this->csv("seller-{$validated['report']}-report-".now()->format('Ymd-His').'.csv', $rows);
    }

    public function store(Request $request): View
    {
        $seller = $this->seller($request);
        $profile = SellerProfile::firstOrCreate(
            ['seller_id' => $seller->id],
            [
                'shop_name' => $seller->store_name ?: $seller->business_name ?: 'LIKHAE Studio',
                'location' => $this->userAddress($seller),
                'business_days' => 'Monday to Saturday',
                'business_hours' => '9:00 AM – 6:00 PM',
            ]
        );

        return view('Seller.store', [
            'tab' => $request->input('tab', 'profile'),
            'seller' => $seller,
            'storeProfile' => $profile,
        ]);
    }

    public function updateStore(Request $request): RedirectResponse
    {
        $seller = $this->seller($request);
        $validated = $request->validate([
            'shop_name' => ['nullable', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'business_days' => ['nullable', 'string', 'max:80'],
            'business_hours' => ['nullable', 'string', 'max:80'],
            'processing_days' => ['nullable', 'integer', 'min:1', 'max:30'],
            'order_cutoff' => ['nullable', 'string', 'max:80'],
            'vacation_mode' => ['nullable', 'boolean'],
            'auto_accept_orders' => ['nullable', 'boolean'],
            'store_visibility' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'max:5120'],
            'banner' => ['nullable', 'image', 'max:8192'],
        ]);

        foreach (['vacation_mode', 'auto_accept_orders', 'store_visibility'] as $field) {
            $validated[$field] = $request->boolean($field);
        }

        $profile = SellerProfile::firstOrNew(['seller_id' => $seller->id]);
        $profile->fill(collect($validated)->except(['avatar', 'banner'])->all());

        if ($request->hasFile('avatar')) {
            $this->deletePublicFile($profile->avatar_path);
            $profile->avatar_path = $this->storeSellerProfileImage($request, $seller, 'avatar');
        }

        if ($request->hasFile('banner')) {
            $this->deletePublicFile($profile->banner_path);
            $profile->banner_path = $this->storeSellerProfileImage($request, $seller, 'banner');
        }

        $profile->save();
        if (isset($validated['shop_name'])) {
            $seller->store_name = $validated['shop_name'];
            $seller->save();
        }

        return back()->with('status', 'Store settings saved.');
    }

    public function account(Request $request): View
    {
        $seller = $this->seller($request);
        $profile = SellerProfile::firstOrCreate(['seller_id' => $seller->id]);

        return view('Seller.account', [
            'tab' => $request->input('tab', 'profile'),
            'seller' => $seller,
            'storeProfile' => $profile,
        ]);
    }

    public function updateAccountProfile(Request $request): RedirectResponse
    {
        $seller = $this->seller($request);
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($seller->id)],
            'contact_number' => ['nullable', 'string', 'max:30'],
        ]);

        $seller->fill($validated);
        $seller->name = trim($validated['first_name'].' '.$validated['last_name']);
        $seller->save();

        return back()->with('status', 'Profile updated.');
    }

    public function updateBusiness(Request $request): RedirectResponse
    {
        $seller = $this->seller($request);
        $validated = $request->validate([
            'business_name' => ['nullable', 'string', 'max:160'],
            'business_type' => ['nullable', 'string', 'max:80'],
            'dti_sec_number' => ['nullable', 'string', 'max:80'],
            'tin' => ['nullable', 'string', 'max:80'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'province' => ['nullable', 'string', 'max:100'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'barangay' => ['nullable', 'string', 'max:100'],
            'street' => ['nullable', 'string', 'max:120'],
            'house_number' => ['nullable', 'string', 'max:40'],
        ]);

        $seller->fill($validated)->save();

        return back()->with('status', 'Business information updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $seller = $this->seller($request);
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $seller->password = Hash::make($validated['password']);
        $seller->save();

        return back()->with('status', 'Password changed successfully.');
    }

    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $seller = $this->seller($request);
        $profile = SellerProfile::firstOrCreate(['seller_id' => $seller->id]);
        $keys = ['new_order', 'order_cancellation', 'pickup_shipping', 'inventory_alerts', 'buyer_messages', 'finance_payouts', 'marketing_updates'];
        $channels = ['email', 'push', 'sms'];
        $preferences = [];

        foreach ($keys as $key) {
            foreach ($channels as $channel) {
                $preferences[$key][$channel] = $request->boolean("preferences.{$key}.{$channel}");
            }
        }

        $profile->notification_preferences = $preferences;
        $profile->save();

        return back()->with('status', 'Notification preferences saved.');
    }

    public function notifications(Request $request): View
    {
        $seller = $this->seller($request);
        $notifications = WorkspaceNotification::where('user_id', $seller->id)->latest()->get();

        return view('Seller.notifications', [
            'notifications' => $notifications,
            'notificationCounts' => [
                'all' => $notifications->count(),
                'orders' => $notifications->where('type', 'orders')->count(),
                'inventory' => $notifications->where('type', 'inventory')->count(),
                'finance' => $notifications->where('type', 'finance')->count(),
                'system' => $notifications->where('type', 'system')->count(),
            ],
        ]);
    }

    public function markNotificationsRead(Request $request): RedirectResponse
    {
        $seller = $this->seller($request);
        WorkspaceNotification::where('user_id', $seller->id)->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('status', 'All notifications marked as read.');
    }

    private function seller(Request $request): User
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'seller', 403);
        return $user;
    }

    private function sellerProductsQuery(User $seller)
    {
        return Product::with(['category', 'variations', 'images', 'specifications', 'orderItems.order', 'reviews'])
            ->where('seller_id', $seller->id)
            ->latest();
    }

    private function sellerOrdersQuery(User $seller)
    {
        return Order::with(['buyer', 'items.product', 'transaction', 'delivery.rider', 'refunds'])
            ->where('seller_id', $seller->id)
            ->latest();
    }

    private function productViewData(Product $product): array
    {
        $sold = (int) $product->orderItems
            ->filter(fn (OrderItem $item) => $item->order && ! in_array($item->order->status, ['cancelled', 'returns'], true))
            ->sum('quantity');

        return [
            'db_id' => $product->id,
            'id' => (string) $product->id,
            'sku' => $product->sku ?: $this->generateSku($product),
            'name' => $product->name,
            'category' => $product->category?->name ?: 'Uncategorized',
            'category_id' => $product->category_id,
            'price' => (float) $product->price,
            'stock' => (int) $product->stock,
            'sold' => $sold,
            'rating' => round((float) ($product->reviews->avg('rating') ?: 0), 1),
            'status' => ($product->listing_status ?: $product->status) === 'archived' ? 'Archived' : ucfirst($product->listing_status ?: $product->status),
            'admin_status' => ucfirst($product->admin_status ?: 'approved'),
            'description' => $product->description,
            'image' => $product->image_path
                ? (Str::startsWith($product->image_path, ['http://', 'https://']) ? $product->image_path : Storage::url($product->image_path))
                : null,
            'variation_rows' => $product->variations
                ->map(fn (ProductVariation $variation) => [
                    'name' => $variation->name,
                    'value' => $variation->value,
                    'sku' => $variation->sku,
                    'price' => $variation->price,
                    'stock' => $variation->stock,
                ])
                ->values()
                ->all(),
            'specification_rows' => $product->specifications
                ->map(fn ($specification) => [
                    'name' => $specification->name,
                    'value' => $specification->value,
                ])
                ->values()
                ->all(),
        ];
    }

    private function orderViewData(Order $order): array
    {
        $item = $order->items->first();
        $product = $item?->product;
        $delivery = $order->delivery;
        $status = $this->canonicalOrderStatus($order->status);

        return [
            'db_id' => $order->id,
            'id' => $order->order_number,
            'tracking' => $delivery?->tracking_number,
            'buyer_id' => $order->buyer_id,
            'buyer' => $order->buyer?->name ?: 'Buyer',
            'product' => $product?->name ?: ($item?->product_name ?: 'Order items'),
            'variant' => $item?->variant ?: 'Standard',
            'quantity' => (int) ($item?->quantity ?: $order->items->sum('quantity') ?: 1),
            'payment' => $this->paymentLabel($order->payment_method),
            'shipping' => $delivery?->provider ?: 'LIKHAE Logistics',
            'total' => (float) $order->total_amount,
            'date' => $order->created_at?->format('M d, Y · g:i A'),
            'status' => $this->statusLabel($status),
            'status_key' => str_replace('_', '-', $status),
            'shipping_address' => $order->shipping_address,
            'delivery' => $delivery,
        ];
    }

    private function findOrder(Collection $orders, mixed $identifier): ?Order
    {
        if (! $identifier) {
            return null;
        }

        return $orders->first(fn (Order $order) =>
            (string) $order->id === (string) $identifier
            || $order->order_number === (string) $identifier
        );
    }

    private function orderCounts(Collection $orders): array
    {
        return [
            'all' => $orders->count(),
            'to-process' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'to_process')->count(),
            'to-prepare' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'to_prepare')->count(),
            'ready-pickup' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'ready_pickup')->count(),
            'shipping' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'shipping')->count(),
            'completed' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'completed')->count(),
            'cancelled' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'cancelled')->count(),
            'returns' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'returns')->count(),
        ];
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'to_process', 'pending' => 'To Process',
            'to_prepare' => 'To Prepare',
            'ready_pickup' => 'Ready Pickup',
            'shipping', 'shipped' => 'Shipping',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'returns', 'disputed' => 'Returns / Refunds',
            default => Str::headline($status),
        };
    }

    private function paymentLabel(?string $method): string
    {
        return match (strtolower((string) $method)) {
            'gcash' => 'GCash',
            'maya' => 'Maya',
            'credit_card', 'card' => 'Credit Card',
            'cod', 'cash_on_delivery' => 'Cash on Delivery',
            default => $method ? Str::headline($method) : 'Cash on Delivery',
        };
    }

    private function validateProduct(Request $request, ?Product $product = null, ?User $seller = null): array
    {
        $seller ??= $this->seller($request);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'new_subcategory' => ['nullable', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:3000'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'listing_status' => ['nullable', Rule::in(['active', 'draft', 'archived'])],
            'image' => ['nullable', 'image', 'max:5120'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'variation_name' => ['nullable', 'array'],
            'variation_name.*' => ['nullable', 'string', 'max:80'],
            'variation_value' => ['nullable', 'array'],
            'variation_value.*' => ['nullable', 'string', 'max:160'],
            'variation_sku' => ['nullable', 'array'],
            'variation_sku.*' => ['nullable', 'string', 'max:80'],
            'variation_price' => ['nullable', 'array'],
            'variation_price.*' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'variation_stock' => ['nullable', 'array'],
            'variation_stock.*' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'specification_name' => ['nullable', 'array'],
            'specification_name.*' => ['nullable', 'string', 'max:120'],
            'specification_value' => ['nullable', 'array'],
            'specification_value.*' => ['nullable', 'string', 'max:500'],
            'specifications_text' => ['nullable', 'string', 'max:2000'],
        ]);

        $parent = $this->sellerLineOfBusinessCategory($seller);
        $selectedCategory = ! empty($validated['category_id'])
            ? Category::whereKey($validated['category_id'])->where('status', 'active')->first()
            : null;

        if (! $parent && $selectedCategory) {
            $parent = $selectedCategory->parent_id
                ? Category::whereKey($selectedCategory->parent_id)->where('status', 'active')->first()
                : $selectedCategory;

            if ($parent) {
                SellerProfile::updateOrCreate(
                    ['seller_id' => $seller->id],
                    ['line_of_business_category_id' => $parent->id]
                );
            }
        }

        abort_unless($parent, 422, 'Your seller account has no registered line of business category.');

        $newSubcategory = trim((string) ($validated['new_subcategory'] ?? ''));

        if ($newSubcategory !== '') {
            $validated['category_id'] = $this->firstOrCreateSellerSubcategory($seller, $parent, $newSubcategory)->id;
        } elseif ($selectedCategory && ! $selectedCategory->parent_id && $selectedCategory->id === $parent->id) {
            $validated['category_id'] = $this->firstOrCreateSellerSubcategory($seller, $parent, 'General')->id;
        }

        abort_if(empty($validated['category_id']), 422, 'Choose a subcategory for this product.');

        $category = Category::whereKey($validated['category_id'])
            ->where('status', 'active')
            ->where('parent_id', $parent->id)
            ->first();

        abort_unless($category, 422, 'The selected subcategory is not allowed for your registered line of business.');

        return $validated;
    }

    private function fillProduct(Product $product, array $validated, Request $request): void
    {
        $product->fill([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'listing_status' => $validated['listing_status'] ?? ($product->listing_status ?: 'active'),
            'status' => $validated['listing_status'] ?? ($product->listing_status ?: $product->status ?: 'active'),
            'admin_status' => $product->admin_status ?: 'approved',
        ]);

        if (! $product->sku) {
            $product->sku = $this->generateSku($product);
        }

        $baseSlug = Str::slug($validated['name']) ?: 'product';
        $slug = $baseSlug;
        $counter = 2;
        while (Product::where('slug', $slug)->when($product->exists, fn ($query) => $query->where('id', '!=', $product->id))->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }
        $product->slug = $slug;

    }

    private function sellerLineOfBusinessCategory(User $seller): ?Category
    {
        $profile = SellerProfile::with('lineOfBusinessCategory')->where('seller_id', $seller->id)->first();

        if ($profile?->lineOfBusinessCategory) {
            return $profile->lineOfBusinessCategory;
        }

        if ($seller->line_of_business) {
            $category = Category::whereNull('parent_id')
                ->where('status', 'active')
                ->where('name', $seller->line_of_business)
                ->first();

            if ($category) {
                SellerProfile::updateOrCreate(
                    ['seller_id' => $seller->id],
                    ['line_of_business_category_id' => $category->id]
                );

                return $category;
            }
        }

        return null;
    }

    private function sellerSubcategories(User $seller): Collection
    {
        $parent = $this->sellerLineOfBusinessCategory($seller);

        if (! $parent) {
            return collect();
        }

        return Category::where('parent_id', $parent->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    private function firstOrCreateSellerSubcategory(User $seller, Category $parent, string $name): Category
    {
        $normalized = Str::of($name)->squish()->toString();
        $slug = Str::slug($normalized).'-'.$parent->id;

        abort_if($normalized === '' || ! preg_match('/^[\\pL\\pN][\\pL\\pN\\s&\\-\\/.,()]+$/u', $normalized), 422, 'Enter a valid subcategory name.');

        $existing = Category::where('parent_id', $parent->id)
            ->get()
            ->first(fn (Category $category) => Str::of($category->name)->squish()->lower()->toString() === Str::of($normalized)->lower()->toString());

        if ($existing) {
            return $existing;
        }

        return Category::create([
            'parent_id' => $parent->id,
            'name' => $normalized,
            'slug' => $slug,
            'status' => 'active',
            'source' => 'seller',
            'created_by_user_id' => $seller->id,
            'created_by_seller_id' => $seller->id,
        ]);
    }

    private function storeProductImageIfPresent(Request $request, User $seller, Product $product): void
    {
        $storedPaths = [];

        if ($request->hasFile('image')) {
            $this->deletePublicFile($product->image_path);
            $product->image_path = $request->file('image')->storeAs(
                "sellers/{$seller->id}/products/{$product->id}",
                $this->uploadFileName($request->file('image'), 'product'),
                'public'
            );
            $storedPaths[] = $product->image_path;
        }

        foreach ($request->file('images', []) as $index => $image) {
            if (! $image) {
                continue;
            }

            $path = $image->storeAs(
                "sellers/{$seller->id}/products/{$product->id}",
                $this->uploadFileName($image, 'product-gallery'),
                'public'
            );

            if (! $product->image_path) {
                $product->image_path = $path;
            }

            $product->images()->create([
                'path' => $path,
                'sort_order' => (int) $product->images()->max('sort_order') + $index + 1,
            ]);

            $storedPaths[] = $path;
        }

        if ($product->image_path && ! $product->images()->where('path', $product->image_path)->exists()) {
            $product->images()->create([
                'path' => $product->image_path,
                'sort_order' => 0,
            ]);
        }
    }

    private function syncProductVariations(Product $product, Request $request): void
    {
        if (! $request->has('variation_name')) {
            return;
        }

        $names = $request->input('variation_name', []);
        $values = $request->input('variation_value', []);
        $skus = $request->input('variation_sku', []);
        $prices = $request->input('variation_price', []);
        $stocks = $request->input('variation_stock', []);
        $rows = [];

        foreach ($names as $index => $name) {
            $name = trim((string) $name);
            $value = trim((string) ($values[$index] ?? ''));

            if ($name === '' || $value === '') {
                continue;
            }

            $rows[] = [
                'name' => $name,
                'value' => $value,
                'sku' => trim((string) ($skus[$index] ?? '')) ?: null,
                'price' => isset($prices[$index]) && $prices[$index] !== '' ? (float) $prices[$index] : null,
                'stock' => isset($stocks[$index]) && $stocks[$index] !== '' ? (int) $stocks[$index] : null,
            ];
        }

        $product->variations()->delete();

        foreach ($rows as $row) {
            $product->variations()->create($row);
        }

        if ($rows !== []) {
            $product->stock = collect($rows)->sum(fn ($row) => (int) ($row['stock'] ?? 0));
            $product->save();
        }
    }

    private function syncProductSpecifications(Product $product, Request $request): void
    {
        if ($request->has('specifications_text')) {
            $text = trim((string) $request->input('specifications_text', ''));
            $product->specifications()->delete();

            if ($text === '') {
                return;
            }

            foreach (preg_split('/\R+/', $text) as $index => $line) {
                $line = trim((string) $line);

                if ($line === '') {
                    continue;
                }

                if (str_contains($line, ':')) {
                    [$name, $value] = array_map('trim', explode(':', $line, 2));
                } else {
                    $name = 'Detail '.($index + 1);
                    $value = $line;
                }

                if ($name === '' || $value === '') {
                    continue;
                }

                $product->specifications()->create([
                    'name' => Str::limit($name, 120, ''),
                    'value' => Str::limit($value, 500, ''),
                ]);
            }

            return;
        }

        if (! $request->has('specification_name')) {
            return;
        }

        $names = $request->input('specification_name', []);
        $values = $request->input('specification_value', []);
        $rows = [];

        foreach ($names as $index => $name) {
            $name = trim((string) $name);
            $value = trim((string) ($values[$index] ?? ''));

            if ($name === '' || $value === '') {
                continue;
            }

            $rows[$name] = ['name' => $name, 'value' => $value];
        }

        $product->specifications()->delete();

        foreach ($rows as $row) {
            $product->specifications()->create($row);
        }
    }

    private function storeSellerProfileImage(Request $request, User $seller, string $field): string
    {
        return $request->file($field)->storeAs(
            "sellers/{$seller->id}/profile",
            $this->uploadFileName($request->file($field), $field),
            'public'
        );
    }

    private function uploadFileName($file, string $fallback): string
    {
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: $fallback;
        $extension = $file->extension() ?: $file->guessExtension() ?: 'jpg';

        return $name.'-'.Str::random(10).'.'.$extension;
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('public')->delete($path);
        }
    }

    private function authorizeProduct(User $seller, Product $product): void
    {
        abort_unless($product->seller_id === $seller->id, 403);
    }

    private function authorizeOrder(User $seller, Order $order): void
    {
        abort_unless($order->seller_id === $seller->id, 403);
    }

    private function reportSummary(Collection $orders): array
    {
        $gross = $this->grossRevenue($orders);
        $commission = $this->commissionFor($gross);
        return [
            'orders' => $orders->count(),
            'gross' => $gross,
            'commission' => $commission,
            'net' => $gross - $commission,
            'completed' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'completed')->count(),
            'cancelled' => $orders->filter(fn (Order $order) => $this->canonicalOrderStatus($order->status) === 'cancelled')->count(),
        ];
    }

    private function canonicalOrderStatus(?string $status): string
    {
        return match ($status) {
            'pending' => 'to_process',
            'shipped' => 'shipping',
            'disputed' => 'returns',
            default => $status ?: 'to_process',
        };
    }

    private function isRevenueOrder(Order $order): bool
    {
        return ! in_array($this->canonicalOrderStatus($order->status), ['cancelled', 'returns'], true);
    }

    private function grossRevenue(Collection $orders): float
    {
        return (float) $orders->filter(fn (Order $order) => $this->isRevenueOrder($order))->sum('total_amount');
    }

    private function commissionRate(): float
    {
        return PlatformSetting::commissionRate();
    }

    private function commissionFor(float $amount): float
    {
        return round($amount * $this->commissionRate(), 2);
    }

    private function generateSku(Product $product): string
    {
        $prefix = 'SLR'.str_pad((string) ($product->seller_id ?: 0), 5, '0', STR_PAD_LEFT);
        $base = $prefix.'-'.strtoupper(Str::random(6));
        $sku = $base;
        $counter = 2;

        while (Product::where('sku', $sku)->when($product->exists, fn ($query) => $query->where('id', '!=', $product->id))->exists()) {
            $sku = $base.'-'.$counter++;
        }

        return $sku;
    }

    private function campaignTab(string $type): string
    {
        return match ($type) {
            'voucher' => 'vouchers',
            'promotion' => 'promotions',
            default => 'discounts',
        };
    }

    private function userAddress(User $user): string
    {
        return collect([$user->house_number, $user->street, $user->barangay, $user->municipality, $user->province])
            ->filter()
            ->implode(', ');
    }

    private function csv(string $filename, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function messagePayload(Message $message, int $viewerId): array
    {
        return [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'recipient_id' => $message->recipient_id,
            'order_id' => $message->order_id,
            'body' => $message->body,
            'from_me' => $message->sender_id === $viewerId,
            'created_at' => $message->created_at?->toIso8601String(),
            'time' => $message->created_at?->diffForHumans() ?: 'Just now',
        ];
    }
}
