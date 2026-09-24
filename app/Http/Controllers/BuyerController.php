<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\ReturnRequest;
use App\Models\Seller;
use App\Models\SellerCampaign;
use App\Models\SellerOrder;
use App\Models\User;
use App\Models\WishlistItem;
use App\Models\WorkspaceNotification;
use App\Support\BuyerMarketplace;
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
        if ($request->filled('buy')) {
            $product = $this->findPurchasableProduct((string) $request->input('buy'));
            $variation = $this->variationFromRequest($request, $product);
            $quantity = max(1, (int) $request->input('quantity', 1));

            if (! $variation && $product->variants()->exists()) {
                abort(422, 'Select a valid product option before buying this item.');
            }

            if ($variation) {
                abort_unless($variation->is_active && $variation->product_id === $product->id, 422, 'Selected product option is invalid.');
                abort_if($quantity > (int) $variation->stock, 422, 'Requested quantity exceeds available stock for this option.');
            }

            $request->session()->put('buyer_buy_now', [
                'product_variant_id' => $variation?->id,
                'quantity' => $quantity,
            ]);

            return redirect()->route('buyer.checkout');
        }

        if ($request->filled('add')) {
            $product = $this->findPurchasableProduct((string) $request->input('add'));
            $variation = $this->variationFromRequest($request, $product);
            $variant = $variation ? $this->variationLabel($variation) : $this->variantFromRequest($request);
            $quantity = max(1, (int) $request->input('quantity', 1));
            $this->putInCart($request->user()->id, $product, $variant, $quantity, $variation);
            if ($request->boolean('checkout')) {
                return redirect()->route('buyer.checkout');
            }

            return redirect()->route('buyer.cart')->with('buyer_notice', 'Product added to cart.');
        }

        $defaultAddress = Address::where('user_id', $request->user()->id)
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
            'quantity' => ['nullable', 'integer', 'min:1'],
            'variant' => ['nullable', 'string', 'max:255'],
            'product_variant_id' => ['nullable', 'integer'],
        ]);
        $variation = $this->variationFromRequest($request, $product);
        $this->putInCart($request->user()->id, $product, $variation ? $this->variationLabel($variation) : ($validated['variant'] ?? 'Standard'), (int) ($validated['quantity'] ?? 1), $variation);

        return back()->with('buyer_notice', 'Product added to cart.');
    }

    public function updateCart(Request $request, CartItem $item): RedirectResponse
    {
        $this->authorizeCartItem($request, $item);
        $quantity = (int) $request->validate(['quantity' => ['required', 'integer', 'min:1']])['quantity'];
        $available = (int) $item->variation()->where('is_active', true)->value('stock');
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

    public function wishlist(Request $request): View
    {
        $rows = WishlistItem::with(['product.seller', 'product.category', 'product.variants', 'product.reviews.buyer', 'product.orderItems.order'])->where('buyer_id', $request->user()->id)->latest()->get();

        return view('Buyer.wishlist', ['wishlistProducts' => $rows->filter(fn ($row) => $row->product)->map(fn ($row) => BuyerMarketplace::product($row->product) + ['wishlist_added_at' => $row->created_at])]);
    }

    public function toggleWishlist(Request $request, Product $product): RedirectResponse
    {
        $this->guardPurchasable($product);
        $row = WishlistItem::where('buyer_id', $request->user()->id)->where('product_id', $product->id)->first();
        if ($row) {
            $row->delete();
            $message = 'Removed from wishlist.';
        } else {
            WishlistItem::create(['buyer_id' => $request->user()->id, 'product_id' => $product->id]);
            $message = 'Saved to wishlist.';
        }

        return back()->with('buyer_notice', $message);
    }

    public function messages(Request $request): View
    {
        $buyer = $request->user();
        $sellerId = $this->sellerIdFromInput($request->input('seller'));
        $sellerIds = Message::where('sender_id', $buyer->id)->orWhere('recipient_id', $buyer->id)->get()->flatMap(fn ($m) => [$m->sender_id, $m->recipient_id])->reject(fn ($id) => $id === $buyer->id)->unique();
        if ($sellerId) {
            $sellerIds->prepend($sellerId);
        }
        $sellers = User::with('sellers')->whereIn('id', $sellerIds)->role('seller')->get();
        $activeSeller = $sellerId ? $sellers->firstWhere('id', $sellerId) : $sellers->first();
        $chatMessages = collect();
        if ($activeSeller) {
            $chatMessages = Message::with('sender')->where(fn ($q) => $q->where('sender_id', $buyer->id)->where('recipient_id', $activeSeller->id))->orWhere(fn ($q) => $q->where('sender_id', $activeSeller->id)->where('recipient_id', $buyer->id))->orderBy('created_at')->get();
            Message::where('sender_id', $activeSeller->id)->where('recipient_id', $buyer->id)->whereNull('read_at')->update(['read_at' => now()]);
        }
        $conversationRows = $sellers->map(function (User $seller) use ($buyer) {
            $last = Message::where(fn ($q) => $q->where('sender_id', $buyer->id)->where('recipient_id', $seller->id))
                ->orWhere(fn ($q) => $q->where('sender_id', $seller->id)->where('recipient_id', $buyer->id))
                ->latest()->first();
            $name = $seller->sellers->firstWhere('status', 'approved')?->name ?: $seller->name;

            return [
                'id' => $seller->id,
                'name' => $name,
                'slug' => Str::slug($name).'-'.$seller->id,
                'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=561C17&color=fff',
                'last_message' => $last?->body ?: 'Start a conversation with this seller.',
                'time' => $last?->created_at?->diffForHumans() ?: '',
                'unread' => Message::where('sender_id', $seller->id)->where('recipient_id', $buyer->id)->whereNull('read_at')->count(),
            ];
        })->sortByDesc(fn ($row) => $row['time'] ? 1 : 0)->values();

        return view('Buyer.messages', ['conversationRows' => $conversationRows, 'dbActiveSeller' => $activeSeller, 'chatMessages' => $chatMessages, 'buyerProducts' => $this->marketplaceProducts()]);
    }

    public function sendMessage(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate(['recipient_id' => ['required', 'exists:users,id'], 'body' => ['required', 'string', 'max:2000'], 'order_id' => ['nullable', 'exists:orders,id']]);
        $seller = User::whereKey($validated['recipient_id'])->role('seller')->where('status', 'active')->firstOrFail();
        $orderId = null;
        if (! empty($validated['order_id'])) {
            $shopId = $seller->sellers()->where('status', 'approved')->value('id');
            $orderId = Order::whereKey($validated['order_id'])->where('buyer_id', $request->user()->id)
                ->whereHas('sellerOrders', fn ($query) => $query->where('seller_id', $shopId))->firstOrFail()->id;
        }
        $message = Message::create(['sender_id' => $request->user()->id, 'recipient_id' => $seller->id, 'order_id' => $orderId, 'body' => $validated['body']]);
        WorkspaceNotification::create(['user_id' => $seller->id, 'type' => 'message', 'title' => 'New buyer message', 'body' => $request->user()->name.' sent you a message.', 'action_url' => route('seller.messages', ['buyer' => $request->user()->id], false)]);

        if ($request->expectsJson()) {
            return response()->json(['message' => $this->messagePayload($message, $request->user()->id)], 201);
        }

        return back()->with('buyer_notice', 'Message sent.');
    }

    public function messageStream(Request $request): StreamedResponse
    {
        $buyer = $request->user();
        $sellerId = $this->sellerIdFromInput($request->query('seller_id') ?? $request->query('seller'));

        abort_unless($sellerId, 404);
        User::whereKey($sellerId)->role('seller')->where('status', 'active')->firstOrFail();

        $lastId = max(0, (int) $request->query('after', 0));

        return response()->stream(function () use ($buyer, $sellerId, $lastId): void {
            $after = $lastId;
            $started = now();

            while (! connection_aborted() && $started->diffInSeconds(now()) < 20) {
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
        $notifications = WorkspaceNotification::where('user_id', $request->user()->id)->latest()->get();

        return view('Buyer.notifications', ['dbNotifications' => $notifications]);
    }

    public function rewards(Request $request): View
    {
        $buyer = $request->user();
        $now = now();

        $completedOrders = SellerOrder::with('order')->whereHas('order', fn ($query) => $query->where('buyer_id', $buyer->id))
            ->whereIn('status', ['completed', 'delivered'])
            ->latest()
            ->get();

        $reviewRows = ProductReview::where('buyer_id', $buyer->id)->latest()->get();

        $pointActivities = collect()
            ->merge($completedOrders->map(fn (SellerOrder $order) => [
                'label' => 'Order '.$order->order->reference.'-'.$order->id.' completed',
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
        $availableCashback = $completedOrders->sum(fn (SellerOrder $order) => round(($order->subtotal_minor / 100) * $cashbackRate, 2));
        $pendingCashback = SellerOrder::whereHas('order', fn ($query) => $query->where('buyer_id', $buyer->id))
            ->whereNotIn('status', ['completed', 'delivered', 'cancelled', 'refunded'])
            ->sum('subtotal_minor') / 100 * $cashbackRate;

        $cashbackActivities = $completedOrders
            ->map(fn (SellerOrder $order) => [
                'order' => 'Order '.$order->order->reference.'-'.$order->id,
                'type' => 'Earned',
                'amount' => 'PHP '.number_format(round(($order->subtotal_minor / 100) * $cashbackRate, 2), 2),
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
        WorkspaceNotification::where('user_id', $request->user()->id)->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('buyer_notice', 'Notifications marked as read.');
    }

    public function shop(Request $request, string $seller): View
    {
        $shop = \App\Models\Seller::with('owner')->where('status', 'approved')->where(function ($query) use ($seller) {
            $query->where('slug', $seller);
            if (ctype_digit($seller)) $query->orWhere('id', (int) $seller);
            if (preg_match('/-(\d+)$/', $seller, $match)) $query->orWhere('id', (int) $match[1]);
        })->firstOrFail();
        $user = $shop->owner;
        abort_unless($user->status === 'active', 404);
        $products = $this->marketplaceQuery()->where('seller_id', $shop->id)->get()->map(fn ($p) => BuyerMarketplace::product($p));
        $name = $shop->name;
        $avatar = $shop->logo_path ? Storage::url($shop->logo_path) : 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=561C17&color=fff';

        return view('Buyer.store', ['seller' => ['id' => $shop->id, 'name' => $name, 'slug' => $shop->slug, 'avatar' => $avatar, 'location' => data_get($shop->settings, 'location') ?: $this->userAddress($user), 'joined' => $shop->created_at?->format('Y'), 'rating' => number_format((float) $products->avg('rating'), 1), 'followers' => 'Not tracked', 'response' => 'Not tracked', 'fulfillment' => 'Not tracked', 'description' => $shop->description ?: 'Verified LIKHAE seller.', 'hours' => data_get($shop->settings, 'business_hours', 'Business hours vary.')], 'sellerSlug' => $seller, 'storeProducts' => $products, 'buyerProducts' => $products]);
    }

    public function account(Request $request): View
    {
        return view('Buyer.account', ['tab' => $request->input('tab', 'profile'), 'buyerAddresses' => Address::where('user_id', $request->user()->id)->orderByDesc('is_default')->get()]);
    }

    public function saveProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:160'], 'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->user()->id)], 'phone' => ['nullable', 'string', 'max:40'], 'birthday' => ['nullable', 'date'], 'gender' => ['nullable', Rule::in(['male', 'female', 'other'])], 'profile_photo' => ['nullable', 'image', 'max:2048'], 'remove_profile_photo' => ['nullable', 'boolean']]);
        $user = $request->user();
        $user->fill(['name' => $validated['name'], 'email' => $validated['email'], 'contact_number' => $validated['phone'] ?? null, 'birthday' => $validated['birthday'] ?? null, 'sex' => $validated['gender'] ?? null]);

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

        return back()->with('buyer_notice', 'Profile updated.');
    }

    public function savePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate(['current_password' => ['required', 'current_password'], 'new_password' => ['required', 'string', 'min:8', 'confirmed']]);
        $request->user()->update(['password' => Hash::make($validated['new_password'])]);

        return back()->with('buyer_notice', 'Password changed.');
    }

    public function saveAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate(['label' => ['required', 'string', 'max:80'], 'recipient_name' => ['required', 'string', 'max:160'], 'contact_number' => ['required', 'string', 'max:40'], 'region' => ['nullable', 'string', 'max:160'], 'province' => ['required', 'string', 'max:160'], 'municipality' => ['required', 'string', 'max:160'], 'barangay' => ['required', 'string', 'max:160'], 'house_number' => ['nullable', 'string', 'max:80'], 'street' => ['nullable', 'string', 'max:255'], 'postal_code' => ['required', 'string', 'max:20'], 'landmark' => ['nullable', 'string', 'max:255'], 'is_default' => ['nullable', 'boolean']]);
        if ($request->boolean('is_default')) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }
        Address::create([
            'user_id' => $request->user()->id, 'label' => $validated['label'],
            'recipient' => $validated['recipient_name'], 'phone' => $validated['contact_number'],
            'line1' => trim(collect([$validated['house_number'] ?? null, $validated['street'] ?? null])->filter()->implode(' ')),
            'region' => $validated['region'] ?? null, 'province' => $validated['province'],
            'city' => $validated['municipality'], 'barangay' => $validated['barangay'],
            'postal_code' => $validated['postal_code'], 'landmark' => $validated['landmark'] ?? null,
            'is_default' => $request->boolean('is_default') || ! Address::where('user_id', $request->user()->id)->exists(),
        ]);

        return back()->with('buyer_notice', 'Delivery address saved.');
    }

    public function deleteAddress(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        abort_if(\App\Models\Seller::where('pickup_address_id', $address->id)->whereIn('status', ['pending', 'approved'])->exists(), 409, 'Reassign the seller pickup address before deleting it.');
        $wasDefault = $address->is_default;
        $address->delete();
        if ($wasDefault) Address::where('user_id', $request->user()->id)->oldest()->first()?->update(['is_default' => true]);

        return back()->with('buyer_notice', 'Address removed.');
    }

    private function marketplaceProducts(): Collection
    {
        return $this->marketplaceQuery()->latest()->get()->map(fn (Product $p) => BuyerMarketplace::product($p));
    }

    private function marketplaceQuery()
    {
        return Product::visible()
            ->with([
                'seller.owner',
                'seller.pickupAddress',
                'category.parent',
                'variants',
                'images',
                'specifications',
                'reviews.buyer',
                'orderItems.sellerOrder.order',
            ])
            ->whereHas('seller.owner', fn ($query) => $query->where('status', 'active'))
            ;
    }

    private function buyerCart(int $buyerId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $buyerId]);
    }

    private function cartRows(int $buyerId): Collection
    {
        $cart = $this->buyerCart($buyerId);

        return CartItem::with(['variation.product.seller', 'variation.product.category', 'variation.product.variants', 'variation.product.images', 'variation.product.specifications'])->where('cart_id', $cart->id)->get()->filter(fn ($i) => $i->variation?->product)->map(function ($i) {
            $product = $i->variation->product;
            $p = BuyerMarketplace::product($product);
            $price = $i->variation->price_minor / 100;

            return array_replace($p, ['cart_item_id' => $i->id, 'id' => $i->id, 'product_id' => $product->id, 'product_variant_id' => $i->product_variant_id, 'quantity' => (int) $i->quantity, 'variant' => $i->variation->name, 'price' => $price, 'old_price' => $price, 'stock' => (int) $i->variation->stock]);
        });
    }

    private function putInCart(int $buyerId, Product $product, string $variant, int $quantity, ?ProductVariant $variation = null): void
    {
        $this->guardPurchasable($product);
        $variation ??= $product->variants()->where('is_active', true)->first();
        abort_unless($variation, 422, 'Choose an available product option before adding this item to cart.');
        abort_unless($variation->is_active, 422, 'This product option is no longer available.');
        $cart = $this->buyerCart($buyerId);
        $variant = trim($variant) ?: 'Standard';
        abort_if($variation && $variation->product_id !== $product->id, 422, 'Selected product option is invalid.');
        $item = CartItem::firstOrNew(['cart_id' => $cart->id, 'product_variant_id' => $variation->id]);
        $available = (int) $variation->stock;
        $new = ($item->exists ? (int) $item->quantity : 0) + $quantity;
        abort_if($new < 1, 422, 'Choose a valid quantity.');
        abort_if($new > $available, 422, 'Requested quantity exceeds available stock for this option.');
        $item->quantity = $new;
        $item->selected = true;
        $item->save();
    }

    private function findPurchasableProduct(string $id): Product
    {
        $q = $this->marketplaceQuery();

        return ctype_digit($id) ? $q->whereKey((int) $id)->firstOrFail() : $q->where('slug', $id)->firstOrFail();
    }

    private function guardPurchasable(Product $product): void
    {
        abort_unless(Product::visible()->whereKey($product->id)->exists(), 422, 'This product is not currently available for ordering.');
    }

    private function variantFromRequest(Request $r): string
    {
        return trim(implode(' / ', array_filter([(string) $r->input('color'), (string) $r->input('size'), (string) $r->input('variant')]))) ?: 'Standard';
    }

    private function variationFromRequest(Request $request, Product $product): ?ProductVariant
    {
        $id = $request->input('product_variant_id');
        if (! $id) {
            if ($product->variants()->exists()) {
                abort(422, 'Select a valid product option before adding this item to cart.');
            }

            return null;
        }

        $variation = ProductVariant::where('product_id', $product->id)->whereKey((int) $id)->first();
        abort_unless($variation, 422, 'Selected product option is invalid.');

        return $variation;
    }

    private function variationLabel(ProductVariant $variation): string
    {
        return trim($variation->option_name.': '.$variation->value) ?: 'Standard';
    }

    private function availableVariationStock(Product $product, ProductVariant $variation): int
    {
        return (int) $variation->stock;
    }

    private function selectedCartRows(?string $json): array
    {
        if (! $json) {
            return [];
        } $rows = json_decode($json, true);
        if (! is_array($rows)) {
            return [];
        } $out = [];
        foreach ($rows as $row) {
            $id = (string) ($row['id'] ?? '');
            if (! ctype_digit($id)) {
                continue;
            } $out[(int) $id] = max(1, (int) ($row['quantity'] ?? 1));
        }

return $out;
    }

    private function authorizeCartItem(Request $request, CartItem $item): void
    {
        abort_unless($item->cart?->user_id === $request->user()->id, 403);
    }

    private function sellerIdFromInput(mixed $value): ?int
    {
        if (! $value) {
            return null;
        } if (ctype_digit((string) $value)) {
            return (int) $value;
        } if (preg_match('/-(\d+)$/', (string) $value, $m)) {
            return (int) $m[1];
        } $slug = (string) $value;

        return Seller::query()->where('status', 'approved')->where('slug', $slug)->value('user_id');
    }

    private function userAddress(User $u): string
    {
        $address = $u->addresses()->where('is_default', true)->first() ?? $u->addresses()->first();
        return collect([$address?->line1, $address?->barangay, $address?->city, $address?->province, $address?->postal_code])->filter()->implode(', ');
    }

    private function messagePayload(Message $message, int $viewerId): array
    {
        return ['id' => $message->id, 'sender_id' => $message->sender_id, 'recipient_id' => $message->recipient_id, 'order_id' => $message->order_id, 'body' => $message->body, 'from_me' => $message->sender_id === $viewerId, 'created_at' => $message->created_at?->toIso8601String(), 'time' => $message->created_at?->diffForHumans() ?: 'Just now'];
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
}
