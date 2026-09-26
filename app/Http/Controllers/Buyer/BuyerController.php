<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Notification;
use App\Models\Buyer\Address;
use App\Models\Buyer\CartItem;
use App\Models\Buyer\Order;
use App\Models\Seller\Product;
use App\Models\Seller\SellerProfile;
use App\Services\Communication\ConversationService;
use App\Services\Marketplace\CartService;
use App\Services\Marketplace\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BuyerController extends Controller
{
    public function __construct(
        private readonly ProductCatalogService $catalog,
        private readonly CartService $cartService,
        private readonly ConversationService $conversations,
    ) {}

    public function home(Request $request): View
    {
        $products = $this->catalog->paginated($request, 12);

        return view('Buyer.home', [
            'products' => $products,
            'buyerProducts' => $products->getCollection()->map(fn (Product $product) => $this->catalog->productPayload($product)),
            'categories' => $this->catalog->categories(),
        ]);
    }

    public function products(Request $request): View
    {
        $products = $this->catalog->paginated($request, 24);

        return view('Buyer.products', [
            'products' => $products,
            'buyerProducts' => $products->getCollection()->map(fn (Product $product) => $this->catalog->productPayload($product)),
            'categories' => $this->catalog->categories(),
            'focus' => $request->query('focus'),
        ]);
    }

    public function product(string $slug): View
    {
        $product = $this->catalog->findVisibleBySlug($slug);

        return view('Buyer.product-details', [
            'productModel' => $product,
            'product' => $this->catalog->productPayload($product, true),
            'relatedProducts' => $this->catalog->visibleQuery()
                ->whereKeyNot($product->id)
                ->where('category_id', $product->category_id)
                ->limit(4)
                ->get()
                ->map(fn (Product $item) => $this->catalog->productPayload($item)),
        ]);
    }

    public function shop(string $seller, Request $request): View
    {
        $sellerProfile = $this->catalog->sellerByRouteKey($seller);
        $query = Product::query()
            ->visible()
            ->where('seller_profile_id', $sellerProfile->id)
            ->with($this->catalog->productRelations());

        $products = $this->catalog->applySort(
            $this->catalog->applyFilters($query, $request),
            $request->query('sort')
        )->paginate(24)->withQueryString();

        return view('Buyer.store', [
            'seller' => $sellerProfile,
            'products' => $products,
            'buyerProducts' => $products->getCollection()->map(fn (Product $product) => $this->catalog->productPayload($product)),
            'categories' => $this->catalog->categories(),
        ]);
    }

    public function cart(Request $request): View
    {
        return view('Buyer.cart', [
            'cartItems' => $this->cartService->items($request->user()),
            'defaultAddress' => $request->user()->addresses()->orderByDesc('is_default')->latest()->first(),
        ]);
    }

    public function addCart(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->status === 'ACTIVE', 404);

        $data = $request->validate([
            'product_variant_id' => ['required', 'integer', Rule::exists('product_variants', 'id')->where('product_id', $product->id)],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
            'checkout' => ['nullable', 'boolean'],
        ]);

        $this->cartService->add($request->user(), $product, (int) $data['product_variant_id'], (int) ($data['quantity'] ?? 1));

        if ($request->boolean('checkout')) {
            return redirect()->route('buyer.checkout');
        }

        return back()->with('buyer_notice', 'Product added to cart.');
    }

    public function updateCart(Request $request, CartItem $item): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $this->cartService->update($request->user(), $item, (int) $data['quantity']);

        return back()->with('buyer_notice', 'Cart updated.');
    }

    public function removeCart(Request $request, CartItem $item): RedirectResponse
    {
        $this->cartService->remove($request->user(), $item);

        return back()->with('buyer_notice', 'Item removed from cart.');
    }

    public function messages(Request $request): View
    {
        $conversations = $this->conversations->listFor($request->user());
        $rows = $conversations->map(function ($conversation) use ($request): array {
            $other = $conversation->participants->first(fn ($participant) => (int) $participant->id !== (int) $request->user()->id);
            $seller = $other?->sellerProfile;
            $name = $seller?->business_name ?: $other?->name ?: 'LIKHAE User';

            return [
                'id' => $other?->id,
                'conversation_id' => $conversation->id,
                'name' => $name,
                'slug' => 'conversation-'.$conversation->id,
                'store_key' => $seller?->id,
                'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=561C17&color=fff',
                'last_message' => $conversation->latestMessage?->body ?: 'Start a conversation.',
                'time' => $conversation->latestMessage?->sent_at?->diffForHumans() ?? '',
                'unread' => 0,
                'messages' => $conversation->messages,
            ];
        })->filter(fn (array $row): bool => filled($row['id']))->values();

        $selectedSlug = (string) $request->query('seller', '');
        $active = $rows->firstWhere('slug', $selectedSlug) ?: $rows->first();

        return view('Buyer.messages', [
            'buyerProducts' => collect(),
            'conversationRows' => $rows,
            'dbActiveSeller' => null,
            'chatMessages' => collect($active['messages'] ?? []),
        ]);
    }

    public function messageStream(Request $request): JsonResponse
    {
        $recipientId = (int) $request->query('seller_id');
        $conversation = $this->conversations->listFor($request->user())
            ->first(fn ($thread) => $thread->participants->contains(fn ($participant) => (int) $participant->id === $recipientId));

        return response()->json([
            'success' => true,
            'messages' => $conversation?->messages?->map(fn ($message): array => [
                'id' => $message->id,
                'body' => $message->body,
                'sender_user_id' => $message->sender_user_id,
                'sent_at' => $message->sent_at?->toIso8601String(),
            ])->values() ?? [],
        ]);
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'recipient_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $recipient = SellerProfile::query()
            ->where('status', 'ACTIVE')
            ->whereHas('user', fn ($query) => $query->whereKey((int) $data['recipient_id'])->where('status', 'ACTIVE'))
            ->firstOrFail();

        $this->conversations->send($request->user(), $recipient->user_id, trim($data['body']));

        return back()->with('buyer_notice', 'Message sent.');
    }

    public function wishlist(): RedirectResponse
    {
        return redirect()->route('buyer.products')->with('buyer_notice', 'Wishlist is not part of the final 57-table scope.');
    }

    public function toggleWishlist(): RedirectResponse
    {
        return back()->with('buyer_notice', 'Wishlist is disabled in the final scope.');
    }

    public function rewards(): RedirectResponse
    {
        return redirect()->route('buyer.home')->with('buyer_notice', 'Rewards are not part of the final 57-table scope.');
    }

    public function notifications(Request $request): View
    {
        return view('Buyer.notifications', [
            'notifications' => $request->user()->notifications()->latest()->paginate(20),
        ]);
    }

    public function markNotificationsRead(Request $request): RedirectResponse
    {
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('buyer_notice', 'Notifications marked as read.');
    }

    public function account(Request $request): View
    {
        return view('Buyer.account', [
            'tab' => $request->query('tab', 'profile'),
            'addresses' => $request->user()->addresses()->latest()->get(),
            'reviews' => $request->user()->orders()->with('items.review')->latest()->get()->flatMap->items->pluck('review')->filter(),
        ]);
    }

    public function saveProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_initial' => ['nullable', 'string', 'max:10'],
            'last_name' => ['required', 'string', 'max:100'],
            'contact_number' => ['required', 'string', 'max:30', Rule::unique('users', 'contact_number')->ignore($request->user()->id)],
        ]);

        $request->user()->update($data);

        return back()->with('buyer_notice', 'Profile updated.');
    }

    public function savePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('buyer_notice', 'Password updated.');
    }

    public function saveAddress(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:200'],
            'contact_number' => ['required', 'string', 'max:30'],
            'province_code' => ['required', 'string', 'max:50'],
            'province_name' => ['required', 'string', 'max:150'],
            'municipality_code' => ['required', 'string', 'max:50'],
            'municipality_name' => ['required', 'string', 'max:150'],
            'barangay_code' => ['required', 'string', 'max:50'],
            'barangay_name' => ['required', 'string', 'max:150'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'house_number' => ['nullable', 'string', 'max:100'],
            'street_address' => ['required', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_default')) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $request->user()->addresses()->create($data + ['is_default' => $request->boolean('is_default')]);

        return back()->with('buyer_notice', 'Address saved.');
    }

    public function deleteAddress(Request $request, Address $address): RedirectResponse
    {
        abort_unless((int) $address->user_id === (int) $request->user()->id, 403);
        $address->delete();

        return back()->with('buyer_notice', 'Address deleted.');
    }
}
