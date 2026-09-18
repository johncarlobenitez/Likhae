<?php

namespace App\Http\Controllers;

use App\Models\BuyerAddress;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Message;
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
use App\Models\WishlistItem;
use App\Models\WorkspaceNotification;
use App\Support\BuyerMarketplace;
use App\Services\ParcelWorkflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BuyerController extends Controller
{
    public function home(Request $request): View
    {
        return view('Buyer.home', ['buyerProducts' => $this->marketplaceProducts()]);
    }

    public function products(Request $request): View
    {
        return view('Buyer.products', [
            'mode' => 'grid',
            'focus' => $request->input('focus'),
            'buyerProducts' => $this->marketplaceProducts(),
        ]);
    }

    public function product(string $slug): View
    {
        $product = $this->marketplaceQuery()->where('slug', $slug)->firstOrFail();
        return view('Buyer.product-details', [
            'product' => BuyerMarketplace::product($product),
            'buyerProducts' => $this->marketplaceProducts(),
        ]);
    }

    public function cart(Request $request): View|RedirectResponse
    {
        if ($request->filled('add')) {
            $product = $this->findPurchasableProduct((string) $request->input('add'));
            $variation = $this->variationFromRequest($request, $product);
            if (! $variation && $product->variations()->exists() && ! $request->filled('variant') && ! $request->filled('color') && ! $request->filled('size')) {
                $variation = $product->variations()->orderBy('id')->first();
            }
            $variant = $variation ? $this->variationLabel($variation) : $this->variantFromRequest($request);
            $quantity = max(1, (int) $request->input('quantity', 1));
            $this->putInCart($request->user()->id, $product, $variant, $quantity, $variation);
            if ($request->boolean('checkout')) return redirect()->route('buyer.checkout');
            return redirect()->route('buyer.cart')->with('buyer_notice', 'Product added to cart.');
        }

        $defaultAddress = BuyerAddress::where('buyer_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->latest()
            ->first();

        return view('Buyer.cart', [
            'cartItems' => $this->cartRows($request->user()->id),
            'defaultAddress' => $defaultAddress,
        ]);
    }

    public function addCart(Request $request, Product $product): RedirectResponse
    {
        $this->guardPurchasable($product);
        $validated = $request->validate([
            'quantity' => ['nullable','integer','min:1'],
            'variant' => ['nullable','string','max:255'],
            'product_variation_id' => ['nullable','integer'],
        ]);
        $variation = $this->variationFromRequest($request, $product);
        $this->putInCart($request->user()->id, $product, $variation ? $this->variationLabel($variation) : ($validated['variant'] ?? 'Standard'), (int) ($validated['quantity'] ?? 1), $variation);
        return back()->with('buyer_notice', 'Product added to cart.');
    }

    public function updateCart(Request $request, CartItem $item): RedirectResponse
    {
        $this->authorizeCartItem($request, $item);
        $quantity = (int) $request->validate(['quantity' => ['required','integer','min:1']])['quantity'];
        $available = $item->variation
            ? $this->availableVariationStock($item->product, $item->variation)
            : max(0, (int) $item->product->stock - (int) CartItem::where('cart_id', $item->cart_id)->where('product_id', $item->product_id)->whereNull('product_variation_id')->whereKeyNot($item->id)->sum('quantity'));
        abort_if($quantity > $available, 422, 'Requested quantity exceeds available stock for this option.');
        $item->update(['quantity' => $quantity]);
        return back()->with('buyer_notice', 'Cart updated.');
    }

    public function removeCart(Request $request, CartItem $item): RedirectResponse
    {
        $this->authorizeCartItem($request, $item);
        $item->delete();
        return back()->with('buyer_notice', 'Item removed from cart.');
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $source = $request->filled('buy') || $request->input('checkout_source') === 'direct' ? 'direct' : 'cart';
        $checkoutItems = $request->input('items');

        if ($source === 'direct') {
            $product = $this->findPurchasableProduct((string) $request->input('buy'));
            $variation = $this->variationFromRequest($request, $product);
            if (! $variation && $product->variations()->exists() && ! $request->filled('variant') && ! $request->filled('color') && ! $request->filled('size')) {
                $variation = $product->variations()->orderBy('id')->first();
            }
            $variant = $variation ? $this->variationLabel($variation) : $this->variantFromRequest($request);
            $quantity = max(1, (int) $request->input('quantity', 1));
            $checkoutItems = json_encode([[
                'product_id' => $product->id,
                'product_variation_id' => $variation?->id,
                'variant' => $variant,
                'quantity' => $quantity,
            ]]);
            $items = $this->directCheckoutRows($checkoutItems);
        } else {
            $selectedRows = $this->selectedCartRows($checkoutItems);
            $items = $this->cartRows($request->user()->id)
                ->when($selectedRows !== [], fn ($rows) => $rows->whereIn('id', array_keys($selectedRows)))
                ->map(function ($item) use ($selectedRows) {
                    if (isset($selectedRows[$item['id']])) $item['quantity'] = $selectedRows[$item['id']];
                    return $item;
                })
                ->values();
            $checkoutItems = json_encode($items->map(fn ($item) => [
                'id' => $item['cart_item_id'],
                'product_id' => $item['product_id'],
                'product_variation_id' => $item['product_variation_id'],
                'variant' => $item['variant'],
                'quantity' => $item['quantity'],
            ])->values()->all());
        }

        if ($items->isEmpty()) return redirect()->route('buyer.cart')->with('buyer_notice', 'Select at least one item before checkout.');
        $voucherCode = Str::upper(trim((string) $request->input('voucher_code', '')));
        $voucher = null;
        $voucherError = null;
        if ($voucherCode !== '') {
            try {
                $voucher = $this->resolveVoucher($voucherCode, $this->checkoutSellerSubtotals($items));
            } catch (ValidationException $exception) {
                $voucherError = collect($exception->errors())->flatten()->first();
            }
        }
        $defaultAddress = BuyerAddress::where('buyer_id', $request->user()->id)->orderByDesc('is_default')->latest()->first();
        return view('Buyer.checkout', compact('items', 'defaultAddress', 'source', 'checkoutItems', 'voucherCode', 'voucher', 'voucherError'));
    }

    public function storeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', Rule::in(['cash_on_delivery','cod','online','gcash','maya','card','credit_card'])],
            'recipient_name' => ['nullable','string','max:160'],
            'contact_number' => ['nullable','string','max:40'],
            'delivery_address' => ['nullable','string','max:1000'],
            'items' => ['nullable','string'],
            'checkout_source' => ['nullable', Rule::in(['cart','direct'])],
            'voucher_code' => ['nullable','string','max:40'],
        ]);

        $buyer = $request->user();
        $checkoutSource = $validated['checkout_source'] ?? 'cart';
        $cartItemIdsToDelete = collect();

        if ($checkoutSource === 'direct') {
            $cartItems = $this->directOrderItems($validated['items'] ?? null);
        } else {
            $cart = $this->buyerCart($buyer->id);
            $selectionProvided = isset($validated['items']) && trim((string) $validated['items']) !== '';
            $selectedRows = $this->selectedCartRows($validated['items'] ?? null);
            if ($selectionProvided && $selectedRows === []) {
                return redirect()->route('buyer.cart')->with('buyer_notice', 'Select at least one valid cart item.');
            }
            $selectedIds = array_keys($selectedRows);
            $cartItems = CartItem::with(['product', 'variation'])->where('cart_id', $cart->id)
                ->when($selectedIds !== [], fn ($q) => $q->whereIn('id', $selectedIds))->get();
            if ($selectedRows !== []) {
                $cartItems->each(function (CartItem $item) use ($selectedRows) {
                    if (isset($selectedRows[$item->id])) $item->quantity = $selectedRows[$item->id];
                });
            }
            $cartItemIdsToDelete = $cartItems->pluck('id');

            if ($selectionProvided && $cartItems->count() !== count($selectedRows)) {
                $snapshotItems = $this->directOrderItems($validated['items'] ?? null);
                if ($snapshotItems->count() === count($selectedRows)) {
                    $cartItems = $snapshotItems;
                }
            }
        }
        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart')->with('buyer_notice', 'Your selected items are no longer available. Please select them again.');
        }

        $address = trim((string) ($validated['delivery_address'] ?? ''));
        if ($address === '') {
            $default = BuyerAddress::where('buyer_id', $buyer->id)->orderByDesc('is_default')->latest()->first();
            $address = $default?->formatted() ?: $this->userAddress($buyer);
        }
        abort_if($address === '', 422, 'A delivery address is required.');

        $recipientName = trim((string) ($validated['recipient_name'] ?? $buyer->name ?? ''));
        $contactNumber = trim((string) ($validated['contact_number'] ?? $buyer->contact_number ?? ''));
        $shippingSnapshot = collect([
            $recipientName !== '' ? 'Recipient: '.$recipientName : null,
            $contactNumber !== '' ? 'Contact: '.$contactNumber : null,
            $address,
        ])->filter()->implode("\n");

        $orders = DB::transaction(function () use ($cartItems, $cartItemIdsToDelete, $buyer, $validated, $shippingSnapshot) {
            $created = collect();
            $voucher = null;
            $voucherCode = Str::upper(trim((string) ($validated['voucher_code'] ?? '')));
            if ($voucherCode !== '') {
                $sellerSubtotals = $cartItems->groupBy(fn ($item) => $item->product->seller_id)->map(fn ($rows) => (float) $rows->sum(function ($item) {
                    return (float) ($item->variation?->price ?? $item->product->price) * $item->quantity;
                }));
                $voucher = $this->resolveVoucher($voucherCode, $sellerSubtotals, true);
            }
            foreach ($cartItems->groupBy(fn ($item) => $item->product->seller_id) as $sellerId => $sellerItems) {
                $locked = Product::whereIn('id', $sellerItems->pluck('product_id')->unique())->lockForUpdate()->get()->keyBy('id');
                $lockedVariations = ProductVariation::whereIn('id', $sellerItems->pluck('product_variation_id')->filter()->unique())->lockForUpdate()->get()->keyBy('id');
                $requiredByProduct = $sellerItems->whereNull('product_variation_id')->groupBy('product_id')->map(fn ($rows) => (int) $rows->sum('quantity'));
                foreach ($requiredByProduct as $productId => $required) {
                    $product = $locked->get($productId);
                    abort_if(! $product || $product->stock < $required, 422, ($product?->name ?: 'A product').' no longer has enough stock.');
                    $this->guardPurchasable($product);
                }
                foreach ($sellerItems->whereNotNull('product_variation_id')->groupBy('product_variation_id') as $variationId => $rows) {
                    $variation = $lockedVariations->get((int) $variationId);
                    $product = $variation ? $locked->get($variation->product_id) : null;
                    $required = (int) $rows->sum('quantity');
                    abort_if(! $variation || ! $product || $this->availableVariationStock($product, $variation) < $required, 422, ($product?->name ?: 'A product').' no longer has enough stock for the selected option.');
                    $this->guardPurchasable($product);
                }

                $subtotal = (float) $sellerItems->sum(function ($item) use ($locked, $lockedVariations) {
                    $variation = $item->product_variation_id ? $lockedVariations->get($item->product_variation_id) : null;
                    return (float) ($variation?->price ?? $locked[$item->product_id]->price) * $item->quantity;
                });
                $discount = $voucher && (int) $voucher['campaign']->seller_id === (int) $sellerId
                    ? $this->voucherDiscount($voucher['campaign'], $subtotal)
                    : 0.0;
                $orderTotal = max(0, round($subtotal - $discount, 2));
                $order = Order::create([
                    'order_number' => $this->orderNumber(),
                    'buyer_id' => $buyer->id,
                    'seller_id' => (int) $sellerId,
                    'total_amount' => $orderTotal,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'pending',
                    'status' => 'placed',
                    'shipping_address' => $shippingSnapshot,
                ]);

                foreach ($sellerItems as $cartItem) {
                    $product = $locked[$cartItem->product_id];
                    $variation = $cartItem->product_variation_id ? $lockedVariations->get($cartItem->product_variation_id) : null;
                    $unitPrice = (float) ($variation?->price ?? $product->price);
                    $lineTotal = $unitPrice * $cartItem->quantity;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'variant' => $cartItem->variant ?: 'Standard',
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $lineTotal,
                    ]);
                    if ($cartItem->product_variation_id && $variation && $variation->stock !== null) {
                        $lockedVariations[$cartItem->product_variation_id]->decrement('stock', $cartItem->quantity);
                    }
                    $product->decrement('stock', $cartItem->quantity);
                }

                Transaction::create([
                    'transaction_number' => $this->transactionNumber(),
                    'order_id' => $order->id,
                    'buyer_id' => $buyer->id,
                    'amount' => $orderTotal,
                    'method' => $validated['payment_method'],
                    'status' => 'pending',
                ]);
                WorkspaceNotification::create([
                    'user_id' => (int) $sellerId,
                    'type' => 'orders',
                    'title' => 'New buyer order',
                    'body' => "Order {$order->order_number} was placed and is ready to process.",
                    'action_url' => route('seller.orders', ['order' => $order->order_number], false),
                ]);
                $created->push($order);
            }
            if ($voucher) $voucher['campaign']->increment('uses');
            if ($cartItemIdsToDelete->isNotEmpty()) CartItem::whereIn('id', $cartItemIdsToDelete)->delete();
            return $created;
        }, 3);

        return redirect()->route('buyer.orders.success')->with('buyer_notice', $orders->count().' order'.($orders->count() === 1 ? '' : 's').' placed successfully.');
    }

    public function orders(Request $request, string $mode = 'index', ?string $id = null): View
    {
        $orders = Order::with(['items.product','seller','buyer','delivery.statusHistory'])->where('buyer_id', $request->user()->id)->latest()->get();
        if ($id !== null && in_array($mode, ['show', 'return', 'review'], true)) {
            abort_unless($orders->contains(fn (Order $order) => $order->order_number === $id || (string) $order->id === (string) $id), 404);
        }

        return view('Buyer.orders', [
            'mode' => $mode,
            'selectedOrderId' => $id,
            'buyerOrders' => $orders->map(fn (Order $order) => BuyerMarketplace::order($order)),
        ]);
    }

    public function cancelOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate(['order_id' => ['required','string'], 'reason' => ['required','string','max:255'], 'note' => ['nullable','string','max:1000']]);
        $order = $this->buyerOrder($request, $validated['order_id']);
        abort_unless(in_array($order->status, ['pending','to_process','placed','confirmed','preparing'], true), 422, 'This order can no longer be cancelled.');
        DB::transaction(function () use ($order) {
            $order->load('items.product');
            foreach ($order->items as $item) if ($item->product_id) Product::whereKey($item->product_id)->increment('stock', $item->quantity);
            $order->update(['status' => 'cancelled']);
            $order->transaction?->update(['status' => 'failed']);
            WorkspaceNotification::create(['user_id'=>$order->seller_id,'type'=>'orders','title'=>'Order cancelled','body'=>"Buyer cancelled {$order->order_number}.",'action_url'=>route('seller.orders',['order'=>$order->order_number], false)]);
        });
        return redirect()->route('buyer.orders')->with('buyer_notice', 'Order cancelled and stock returned to the seller inventory.');
    }

    public function received(Request $request, string $id): RedirectResponse
    {
        $order = $this->buyerOrder($request, $id)->load('delivery');
        if ($order->status === 'completed') {
            return redirect()->route('buyer.orders.show', ['id' => $order->order_number])->with('buyer_notice', 'This order was already completed.');
        }
        abort_unless($order->delivery?->status === 'delivered', 422, 'This order is not ready for receipt confirmation.');

        DB::transaction(function () use ($order, $request) {
            $order->update(['status' => 'completed', 'payment_status' => 'paid']);
            $order->transaction?->update(['status' => 'paid']);
            app(ParcelWorkflow::class)->transition($order->delivery, 'completed', $request->user(), 'Buyer confirmed the parcel was received.');
            WorkspaceNotification::create(['user_id'=>$order->seller_id,'type'=>'orders','title'=>'Order completed','body'=>"Buyer confirmed receipt of {$order->order_number}.",'action_url'=>route('seller.orders',['order'=>$order->order_number], false)]);
        }, 3);

        return redirect()->route('buyer.orders.review', ['id' => $order->order_number])
            ->with('buyer_notice', 'Order received and completed. You can now review the product.');
    }

    public function review(Request $request, string $id): RedirectResponse
    {
        $order = $this->buyerOrder($request, $id)->load('items.product');
        abort_unless($order->status === 'completed', 422, 'Only completed orders can be reviewed.');
        $validated = $request->validate(['rating'=>['required','integer','between:1,5'], 'review'=>['required','string','max:3000']]);
        foreach ($order->items->whereNotNull('product_id') as $item) {
            ProductReview::updateOrCreate(['product_id'=>$item->product_id,'buyer_id'=>$request->user()->id], ['seller_id'=>$order->seller_id,'rating'=>$validated['rating'],'body'=>$validated['review'],'has_photo'=>false]);
        }
        WorkspaceNotification::create(['user_id'=>$order->seller_id,'type'=>'review','title'=>'New product review','body'=>"A buyer reviewed {$order->order_number}.",'action_url'=>route('seller.reviews', [], false)]);
        return redirect()->route('buyer.orders.show', ['id'=>$order->order_number])->with('buyer_notice', 'Review submitted.');
    }

    public function requestReturn(Request $request, string $id): RedirectResponse
    {
        $order = $this->buyerOrder($request, $id);
        abort_unless(in_array($order->status, ['shipping','shipped','completed'], true), 422, 'This order is not eligible for a return request.');
        $validated = $request->validate(['request_type'=>['required','string','max:80'], 'reason'=>['required','string','max:255'], 'details'=>['required','string','min:20','max:3000']]);
        Refund::create(['refund_number'=>$this->refundNumber(),'order_id'=>$order->id,'buyer_id'=>$request->user()->id,'amount'=>$order->total_amount,'reason'=>$validated['request_type'].' — '.$validated['reason'].': '.$validated['details'],'status'=>'open']);
        $order->update(['status'=>'returns']);
        WorkspaceNotification::create(['user_id'=>$order->seller_id,'type'=>'returns','title'=>'Return / refund requested','body'=>"Buyer opened a case for {$order->order_number}.",'action_url'=>route('seller.orders',['status'=>'returns','order'=>$order->order_number], false)]);
        return redirect()->route('buyer.orders.show', ['id'=>$order->order_number])->with('buyer_notice', 'Return or refund request submitted for seller review.');
    }

    public function wishlist(Request $request): View
    {
        $rows = WishlistItem::with(['product.seller','product.category','product.variations','product.reviews.buyer','product.orderItems.order'])->where('buyer_id',$request->user()->id)->latest()->get();
        return view('Buyer.wishlist', ['wishlistProducts' => $rows->filter(fn ($row) => $row->product)->map(fn ($row) => BuyerMarketplace::product($row->product) + ['wishlist_added_at'=>$row->created_at])]);
    }

    public function toggleWishlist(Request $request, Product $product): RedirectResponse
    {
        $this->guardPurchasable($product);
        $row = WishlistItem::where('buyer_id',$request->user()->id)->where('product_id',$product->id)->first();
        if ($row) { $row->delete(); $message = 'Removed from wishlist.'; }
        else { WishlistItem::create(['buyer_id'=>$request->user()->id,'product_id'=>$product->id]); $message = 'Saved to wishlist.'; }
        return back()->with('buyer_notice',$message);
    }

    public function messages(Request $request): View
    {
        $buyer = $request->user();
        $sellerId = $this->sellerIdFromInput($request->input('seller'));
        $sellerIds = Message::where('sender_id',$buyer->id)->orWhere('recipient_id',$buyer->id)->get()->flatMap(fn ($m) => [$m->sender_id,$m->recipient_id])->reject(fn ($id) => $id === $buyer->id)->unique();
        if ($sellerId) $sellerIds->prepend($sellerId);
        $sellers = User::whereIn('id',$sellerIds)->where('role','seller')->get();
        $activeSeller = $sellerId ? $sellers->firstWhere('id',$sellerId) : $sellers->first();
        $chatMessages = collect();
        if ($activeSeller) {
            $chatMessages = Message::with('sender')->where(fn($q)=>$q->where('sender_id',$buyer->id)->where('recipient_id',$activeSeller->id))->orWhere(fn($q)=>$q->where('sender_id',$activeSeller->id)->where('recipient_id',$buyer->id))->orderBy('created_at')->get();
            Message::where('sender_id',$activeSeller->id)->where('recipient_id',$buyer->id)->whereNull('read_at')->update(['read_at'=>now()]);
        }
        $conversationRows = $sellers->map(function (User $seller) use ($buyer) {
            $last = Message::where(fn($q)=>$q->where('sender_id',$buyer->id)->where('recipient_id',$seller->id))
                ->orWhere(fn($q)=>$q->where('sender_id',$seller->id)->where('recipient_id',$buyer->id))
                ->latest()->first();
            $name = $seller->store_name ?: $seller->business_name ?: $seller->name;
            return [
                'id' => $seller->id,
                'name' => $name,
                'slug' => Str::slug($name).'-'.$seller->id,
                'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=561C17&color=fff',
                'last_message' => $last?->body ?: 'Start a conversation with this seller.',
                'time' => $last?->created_at?->diffForHumans() ?: '',
                'unread' => Message::where('sender_id',$seller->id)->where('recipient_id',$buyer->id)->whereNull('read_at')->count(),
            ];
        })->sortByDesc(fn($row) => $row['time'] ? 1 : 0)->values();
        return view('Buyer.messages', ['conversationRows'=>$conversationRows,'dbActiveSeller'=>$activeSeller,'chatMessages'=>$chatMessages,'buyerProducts'=>$this->marketplaceProducts()]);
    }

    public function sendMessage(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate(['recipient_id'=>['required','exists:users,id'],'body'=>['required','string','max:2000'],'order_id'=>['nullable','exists:orders,id']]);
        $seller = User::whereKey($validated['recipient_id'])->where('role','seller')->where('status','active')->firstOrFail();
        $orderId = null;
        if (!empty($validated['order_id'])) $orderId = Order::whereKey($validated['order_id'])->where('buyer_id',$request->user()->id)->where('seller_id',$seller->id)->firstOrFail()->id;
        $message = Message::create(['sender_id'=>$request->user()->id,'recipient_id'=>$seller->id,'order_id'=>$orderId,'body'=>$validated['body']]);
        WorkspaceNotification::create(['user_id'=>$seller->id,'type'=>'message','title'=>'New buyer message','body'=>$request->user()->name.' sent you a message.','action_url'=>route('seller.messages',['buyer'=>$request->user()->id], false)]);

        if ($request->expectsJson()) {
            return response()->json(['message' => $this->messagePayload($message, $request->user()->id)], 201);
        }

        return back()->with('buyer_notice','Message sent.');
    }

    public function messageStream(Request $request): StreamedResponse
    {
        $buyer = $request->user();
        $sellerId = $this->sellerIdFromInput($request->query('seller_id') ?? $request->query('seller'));

        abort_unless($sellerId, 404);
        User::whereKey($sellerId)->where('role', 'seller')->where('status', 'active')->firstOrFail();

        $lastId = max(0, (int) $request->query('after', 0));

        return response()->stream(function () use ($buyer, $sellerId, $lastId): void {
            $after = $lastId;
            $started = now();

            while (! connection_aborted() && now()->diffInSeconds($started) < 60) {
                $messages = Message::with('sender')
                    ->where('id', '>', $after)
                    ->where(function ($query) use ($buyer, $sellerId) {
                        $query->where(function ($q) use ($buyer, $sellerId) {
                            $q->where('sender_id', $buyer->id)->where('recipient_id', $sellerId);
                        })->orWhere(function ($q) use ($buyer, $sellerId) {
                            $q->where('sender_id', $sellerId)->where('recipient_id', $buyer->id);
                        });
                    })
                    ->orderBy('id')
                    ->limit(50)
                    ->get();

                foreach ($messages as $message) {
                    $after = max($after, (int) $message->id);
                    echo "event: message\n";
                    echo 'data: '.json_encode($this->messagePayload($message, $buyer->id))."\n\n";
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

    public function notifications(Request $request): View
    {
        $notifications = WorkspaceNotification::where('user_id',$request->user()->id)->latest()->get();
        return view('Buyer.notifications', ['dbNotifications'=>$notifications]);
    }

    public function rewards(Request $request): View
    {
        $buyer = $request->user();
        $now = now();

        $completedOrders = Order::where('buyer_id', $buyer->id)
            ->whereIn('status', ['completed', 'delivered'])
            ->latest()
            ->get();

        $reviewRows = ProductReview::where('buyer_id', $buyer->id)->latest()->get();

        $pointActivities = collect()
            ->merge($completedOrders->map(fn (Order $order) => [
                'label' => 'Order '.$order->order_number.' completed',
                'amount' => '+50 points',
                'date' => $order->updated_at?->format('M j, Y') ?: $order->created_at?->format('M j, Y'),
                'negative' => false,
            ]))
            ->merge($reviewRows->map(fn (ProductReview $review) => [
                'label' => 'Product review submitted',
                'amount' => '+20 points',
                'date' => $review->created_at?->format('M j, Y'),
                'negative' => false,
            ]))
            ->sortByDesc(fn (array $row) => strtotime((string) $row['date']) ?: 0)
            ->values();

        $pointsBalance = ($completedOrders->count() * 50) + ($reviewRows->count() * 20);

        $activeVouchers = SellerCampaign::with('seller')
            ->where('type', 'voucher')
            ->where('status', 'active')
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')->orWhereColumn('uses', '<', 'usage_limit');
            })
            ->latest()
            ->get()
            ->map(fn (SellerCampaign $campaign) => [
                'code' => $campaign->code ?: 'SELLER-'.$campaign->id,
                'value' => $campaign->discount_type === 'percent'
                    ? rtrim(rtrim(number_format((float) $campaign->discount_value, 2), '0'), '.').'% OFF'
                    : 'PHP '.number_format((float) $campaign->discount_value, 2).' OFF',
                'condition' => trim(($campaign->name ?: 'Seller voucher').((float) $campaign->minimum_spend > 0 ? ' - Min spend PHP '.number_format((float) $campaign->minimum_spend, 2) : '')),
                'expires' => $campaign->ends_at?->format('M j, Y') ?: 'No expiry',
                'status' => 'Available',
                'icon' => $campaign->discount_type === 'percent' ? '%' : 'PHP',
            ]);

        $voucherHistory = WorkspaceNotification::where('user_id', $buyer->id)
            ->where('type', 'rewards')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (WorkspaceNotification $notification) => [
                'voucher' => $notification->title,
                'benefit' => Str::limit((string) $notification->body, 80),
                'order' => '-',
                'status' => $notification->read_at ? 'Read' : 'Unread',
            ]);

        $cashbackRate = 0.02;
        $availableCashback = $completedOrders->sum(fn (Order $order) => round((float) $order->total_amount * $cashbackRate, 2));
        $pendingCashback = Order::where('buyer_id', $buyer->id)
            ->whereNotIn('status', ['completed', 'delivered', 'cancelled', 'returns', 'refunded'])
            ->sum('total_amount') * $cashbackRate;

        $cashbackActivities = $completedOrders
            ->map(fn (Order $order) => [
                'order' => 'Order '.$order->order_number,
                'type' => 'Earned',
                'amount' => 'PHP '.number_format(round((float) $order->total_amount * $cashbackRate, 2), 2),
                'date' => $order->updated_at?->format('M j, Y') ?: $order->created_at?->format('M j, Y'),
            ])
            ->values();

        return view('Buyer.rewards', [
            'activeVouchers' => $activeVouchers,
            'voucherHistory' => $voucherHistory,
            'pointsBalance' => $pointsBalance,
            'pointActivities' => $pointActivities,
            'availableCashback' => $availableCashback,
            'pendingCashback' => $pendingCashback,
            'cashbackActivities' => $cashbackActivities,
        ]);
    }

    public function markNotificationsRead(Request $request): RedirectResponse
    {
        WorkspaceNotification::where('user_id',$request->user()->id)->whereNull('read_at')->update(['read_at'=>now()]);
        return back()->with('buyer_notice','Notifications marked as read.');
    }

    public function shop(Request $request, string $seller): View
    {
        $sellerId = $this->sellerIdFromInput($seller);
        $user = User::whereKey($sellerId)->where('role','seller')->where('status','active')->firstOrFail();
        $profile = SellerProfile::where('seller_id',$user->id)->first();
        $products = $this->marketplaceQuery()->where('seller_id',$user->id)->get()->map(fn($p)=>BuyerMarketplace::product($p));
        $name = $profile?->shop_name ?: $user->store_name ?: $user->business_name ?: $user->name;
        $avatar = $profile?->avatar_path ? (Str::startsWith($profile->avatar_path, ['http://','https://']) ? $profile->avatar_path : Storage::url($profile->avatar_path)) : 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=561C17&color=fff';
        return view('Buyer.store', ['seller'=>['id'=>$user->id,'name'=>$name,'slug'=>Str::slug($name).'-'.$user->id,'avatar'=>$avatar,'location'=>$profile?->location ?: $this->userAddress($user),'joined'=>$user->created_at?->format('Y'),'rating'=>number_format((float)$products->avg('rating'),1),'followers'=>'Not tracked','response'=>'Not tracked','fulfillment'=>'Not tracked','description'=>$profile?->description ?: 'Verified LIKHAE seller.','hours'=>$profile?->business_hours ?: 'Business hours vary.'],'sellerSlug'=>$seller,'storeProducts'=>$products,'buyerProducts'=>$products]);
    }

    public function account(Request $request): View
    {
        return view('Buyer.account', ['tab'=>$request->input('tab','profile'), 'buyerAddresses'=>BuyerAddress::where('buyer_id',$request->user()->id)->orderByDesc('is_default')->get()]);
    }

    public function saveProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name'=>['required','string','max:160'],'email'=>['required','email','max:255',Rule::unique('users','email')->ignore($request->user()->id)],'phone'=>['nullable','string','max:40'],'birthday'=>['nullable','date'],'gender'=>['nullable',Rule::in(['male','female','other'])],'profile_photo'=>['nullable','image','max:2048'],'remove_profile_photo'=>['nullable','boolean']]);
        $user = $request->user();
        $user->fill(['name'=>$validated['name'],'email'=>$validated['email'],'contact_number'=>$validated['phone'] ?? null,'birthday'=>$validated['birthday'] ?? null,'sex'=>$validated['gender'] ?? null]);

        if ($request->boolean('remove_profile_photo')) {
            $this->deletePublicFile($user->profile_photo_path);
            $user->profile_photo_path = null;
        }

        if ($request->hasFile('profile_photo')) {
            $this->deletePublicFile($user->profile_photo_path);
            $user->profile_photo_path = $request->file('profile_photo')->storeAs(
                "users/{$user->id}/profile",
                $this->uploadFileName($request->file('profile_photo'), 'profile'),
                'public'
            );
        }

        $user->save();
        return back()->with('buyer_notice','Profile updated.');
    }

    public function savePassword(Request $request): RedirectResponse
    {
        $validated=$request->validate(['current_password'=>['required','current_password'],'new_password'=>['required','string','min:8','confirmed']]);
        $request->user()->update(['password'=>Hash::make($validated['new_password'])]);
        return back()->with('buyer_notice','Password changed.');
    }

    public function saveAddress(Request $request): RedirectResponse
    {
        $validated=$request->validate(['label'=>['required','string','max:80'],'recipient_name'=>['required','string','max:160'],'contact_number'=>['required','string','max:40'],'region'=>['nullable','string','max:160'],'province'=>['nullable','string','max:160'],'municipality'=>['nullable','string','max:160'],'barangay'=>['nullable','string','max:160'],'house_number'=>['nullable','string','max:80'],'street'=>['nullable','string','max:255'],'postal_code'=>['nullable','string','max:20'],'landmark'=>['nullable','string','max:255'],'is_default'=>['nullable','boolean']]);
        if ($request->boolean('is_default')) BuyerAddress::where('buyer_id',$request->user()->id)->update(['is_default'=>false]);
        BuyerAddress::create($validated + ['buyer_id'=>$request->user()->id,'is_default'=>$request->boolean('is_default')]);
        return back()->with('buyer_notice','Delivery address saved.');
    }

    public function deleteAddress(Request $request, BuyerAddress $address): RedirectResponse
    {
        abort_unless($address->buyer_id === $request->user()->id,403); $address->delete(); return back()->with('buyer_notice','Address removed.');
    }

    private function marketplaceProducts(): Collection { return $this->marketplaceQuery()->latest()->get()->map(fn(Product $p)=>BuyerMarketplace::product($p)); }
    private function marketplaceQuery() { return Product::query()->with(['seller','category.parent','variations','images','specifications','reviews.buyer','orderItems.order'])->where('stock','>',0)->where(fn($q)=>$q->where('listing_status','active')->orWhere(fn($x)=>$x->whereNull('listing_status')->where('status','active')))->where(fn($q)=>$q->whereNull('admin_status')->orWhere('admin_status','approved'))->whereHas('seller',fn($q)=>$q->where('role','seller')->where('status','active'))->whereNotExists(function($q){ $q->selectRaw('1')->from('seller_profiles')->whereColumn('seller_profiles.seller_id','products.seller_id')->where(function($p){ $p->where('seller_profiles.store_visibility',false)->orWhere('seller_profiles.vacation_mode',true); }); }); }
    private function buyerCart(int $buyerId): Cart { return Cart::firstOrCreate(['buyer_id'=>$buyerId]); }
    private function cartRows(int $buyerId): Collection { $cart=$this->buyerCart($buyerId); return CartItem::with(['variation','product.seller','product.category','product.variations','product.images','product.specifications'])->where('cart_id',$cart->id)->get()->filter(fn($i)=>$i->product)->map(function($i){ $p=BuyerMarketplace::product($i->product); $price=(float)($i->variation?->price ?? $i->product->price); return $p+['cart_item_id'=>$i->id,'id'=>$i->id,'product_id'=>$i->product_id,'product_variation_id'=>$i->product_variation_id,'quantity'=>(int)$i->quantity,'variant'=>$i->variant,'price'=>$price,'old_price'=>$price,'stock'=>$i->variation ? (int)$i->variation->stock : (int)$i->product->stock]; }); }
    private function directCheckoutRows(?string $json): Collection { return $this->directOrderItems($json)->map(function(CartItem $i){ $p=BuyerMarketplace::product($i->product); $price=(float)($i->variation?->price ?? $i->product->price); return $p+['id'=>'direct-'.$i->product_id.'-'.($i->product_variation_id ?: 'standard'),'product_id'=>$i->product_id,'quantity'=>(int)$i->quantity,'variant'=>$i->variant,'price'=>$price,'old_price'=>$price,'stock'=>$i->variation ? (int)$i->variation->stock : (int)$i->product->stock]; }); }
    private function directOrderItems(?string $json): Collection { $rows=json_decode((string)$json,true); if(!is_array($rows))return collect(); $productIds=collect($rows)->pluck('product_id')->filter(fn($id)=>ctype_digit((string)$id))->map(fn($id)=>(int)$id)->unique(); $variationIds=collect($rows)->pluck('product_variation_id')->filter(fn($id)=>ctype_digit((string)$id))->map(fn($id)=>(int)$id)->unique(); $products=$this->marketplaceQuery()->whereIn('products.id',$productIds)->get()->keyBy('id'); $variations=ProductVariation::whereIn('id',$variationIds)->get()->keyBy('id'); return collect($rows)->map(function($row) use ($products,$variations){ $product=$products->get((int)($row['product_id']??0)); if(!$product)return null; $variationId=(int)($row['product_variation_id']??0); $variation=$variationId ? $variations->get($variationId) : null; if($variation && $variation->product_id !== $product->id)return null; $variant=$variation ? $this->variationLabel($variation) : (trim((string)($row['variant']??'')) ?: 'Standard'); $quantity=max(1,(int)($row['quantity']??1)); $available=$variation ? $this->availableVariationStock($product,$variation) : (int)$product->stock; if($available < $quantity)return null; $item=new CartItem(['product_id'=>$product->id,'product_variation_id'=>$variation?->id,'variant'=>$variant,'quantity'=>$quantity]); $item->setRelation('product',$product); $item->setRelation('variation',$variation); return $item; })->filter()->values(); }

    private function checkoutSellerSubtotals(Collection $items): Collection
    {
        return $items->groupBy(fn ($item) => (int) data_get($item, 'seller_id'))
            ->map(fn ($rows) => (float) $rows->sum(fn ($item) => (float) data_get($item, 'price', 0) * max(1, (int) data_get($item, 'quantity', 1))));
    }

    private function resolveVoucher(string $code, Collection $sellerSubtotals, bool $lock = false): array
    {
        $query = SellerCampaign::with('seller')
            ->where('type', 'voucher')
            ->whereIn('seller_id', $sellerSubtotals->keys()->filter())
            ->whereRaw('LOWER(code) = ?', [Str::lower($code)]);
        if ($lock) $query->lockForUpdate();
        $campaigns = $query->get();

        $campaign = $campaigns->first(function (SellerCampaign $candidate) use ($sellerSubtotals) {
            $subtotal = (float) $sellerSubtotals->get($candidate->seller_id, 0);
            return $candidate->status === 'active'
                && (! $candidate->starts_at || $candidate->starts_at->lte(now()))
                && (! $candidate->ends_at || $candidate->ends_at->gte(now()))
                && (! $candidate->usage_limit || $candidate->uses < $candidate->usage_limit)
                && $subtotal >= (float) $candidate->minimum_spend;
        });

        if (! $campaign) {
            throw ValidationException::withMessages(['voucher_code' => 'This voucher is invalid, expired, fully used, or does not meet the seller minimum spend.']);
        }

        $sellerSubtotal = (float) $sellerSubtotals->get($campaign->seller_id, 0);
        return [
            'campaign' => $campaign,
            'discount' => $this->voucherDiscount($campaign, $sellerSubtotal),
            'seller_subtotal' => $sellerSubtotal,
        ];
    }

    private function voucherDiscount(SellerCampaign $campaign, float $subtotal): float
    {
        $discount = $campaign->discount_type === 'percent'
            ? $subtotal * ((float) $campaign->discount_value / 100)
            : (float) $campaign->discount_value;

        return round(min($subtotal, max(0, $discount)), 2);
    }
    private function putInCart(int $buyerId, Product $product, string $variant, int $quantity, ?ProductVariation $variation = null): void { $this->guardPurchasable($product); abort_if(! $variation && $product->variations()->exists(),422,'Choose a product option before adding this item to cart.'); $cart=$this->buyerCart($buyerId); $variant=trim($variant) ?: 'Standard'; abort_if($variation && $variation->product_id !== $product->id,422,'Selected product option is invalid.'); $lookup=['cart_id'=>$cart->id,'product_id'=>$product->id,'product_variation_id'=>$variation?->id]; if(! $variation) $lookup['variant']=$variant; $item=CartItem::firstOrNew($lookup); $available=$variation ? $this->availableVariationStock($product,$variation) : max(0,(int)$product->stock-(int)CartItem::where('cart_id',$cart->id)->where('product_id',$product->id)->whereNull('product_variation_id')->where('variant','!=',$variant)->sum('quantity')); $new=min($available,($item->exists?(int)$item->quantity:0)+$quantity); abort_if($new<1,422,'Product is out of stock for the selected option.'); $item->variant=$variant; $item->quantity=$new; $item->save(); }
    private function findPurchasableProduct(string $id): Product { $q=$this->marketplaceQuery(); return ctype_digit($id)?$q->whereKey((int)$id)->firstOrFail():$q->where('slug',$id)->firstOrFail(); }
    private function guardPurchasable(Product $product): void { $profile=SellerProfile::where('seller_id',$product->seller_id)->first(); abort_if($product->stock<1 || !in_array($product->listing_status ?: $product->status,['active'],true) || ($product->admin_status && $product->admin_status!=='approved') || $profile?->store_visibility===false || $profile?->vacation_mode===true,422,'This product is not currently available for ordering.'); }
    private function variantFromRequest(Request $r): string { return trim(implode(' / ',array_filter([(string)$r->input('color'),(string)$r->input('size'),(string)$r->input('variant')]))) ?: 'Standard'; }
    private function variationFromRequest(Request $request, Product $product): ?ProductVariation { $id=$request->input('product_variation_id'); if(! $id) return null; $variation=ProductVariation::where('product_id',$product->id)->whereKey((int)$id)->first(); abort_unless($variation,422,'Selected product option is invalid.'); return $variation; }
    private function variationLabel(ProductVariation $variation): string { return trim($variation->name.': '.$variation->value) ?: 'Standard'; }
    private function availableVariationStock(Product $product, ProductVariation $variation): int { return $variation->stock === null ? (int) $product->stock : (int) $variation->stock; }
    private function selectedCartRows(?string $json): array { if(!$json)return []; $rows=json_decode($json,true); if(!is_array($rows))return []; $out=[]; foreach($rows as $row){ $id=(string)($row['id']??''); if(!ctype_digit($id))continue; $out[(int)$id]=max(1,(int)($row['quantity']??1)); } return $out; }
    private function authorizeCartItem(Request $request, CartItem $item): void { abort_unless($item->cart?->buyer_id === $request->user()->id,403); }
    private function buyerOrder(Request $request,string $id): Order { return Order::with('transaction')->where('buyer_id',$request->user()->id)->where(function($q) use ($id){ $q->where('order_number',$id); if(ctype_digit($id)) $q->orWhere('id',(int)$id); })->firstOrFail(); }
    private function sellerIdFromInput(mixed $value): ?int { if(!$value)return null; if(ctype_digit((string)$value))return (int)$value; if(preg_match('/-(\d+)$/',(string)$value,$m))return (int)$m[1]; $slug=(string)$value; return User::where('role','seller')->where('status','active')->get()->first(fn(User $u)=>Str::slug($u->store_name ?: $u->business_name ?: $u->name)===$slug)?->id; }
    private function userAddress(User $u): string { return collect([$u->house_number,$u->street,$u->barangay,$u->municipality,$u->province,$u->postal_code])->filter()->implode(', '); }
    private function orderNumber(): string { do{$v='LH-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));}while(Order::where('order_number',$v)->exists()); return $v; }
    private function transactionNumber(): string { do{$v='TXN-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));}while(Transaction::where('transaction_number',$v)->exists()); return $v; }
    private function refundNumber(): string { do{$v='REF-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));}while(Refund::where('refund_number',$v)->exists()); return $v; }
    private function messagePayload(Message $message, int $viewerId): array { return ['id'=>$message->id,'sender_id'=>$message->sender_id,'recipient_id'=>$message->recipient_id,'order_id'=>$message->order_id,'body'=>$message->body,'from_me'=>$message->sender_id===$viewerId,'created_at'=>$message->created_at?->toIso8601String(),'time'=>$message->created_at?->diffForHumans() ?: 'Just now']; }
    private function uploadFileName($file, string $fallback): string { $name=Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: $fallback; $extension=$file->extension() ?: $file->guessExtension() ?: 'jpg'; return $name.'-'.Str::random(10).'.'.$extension; }
    private function deletePublicFile(?string $path): void { if ($path && ! Str::startsWith($path, ['http://','https://'])) Storage::disk('public')->delete($path); }
}
