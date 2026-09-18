<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariation;
use App\Models\Refund;
use App\Models\SellerCampaign;
use App\Models\User;
use App\Models\WishlistItem;
use App\Models\WorkspaceNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerGuestMarketplaceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_applies_only_a_valid_voucher_created_by_the_product_seller(): void
    {
        $category = Category::create(['name' => 'Decor', 'slug' => 'decor', 'status' => 'active']);
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Woven Basket',
            'slug' => 'woven-basket',
            'sku' => 'WB-001',
            'price' => 500,
            'stock' => 5,
            'status' => 'active',
            'listing_status' => 'active',
            'admin_status' => 'approved',
        ]);
        $cart = Cart::create(['buyer_id' => $buyer->id]);
        $cartItem = CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'variant' => 'Standard', 'quantity' => 2]);
        $voucher = SellerCampaign::create([
            'seller_id' => $seller->id,
            'type' => 'voucher',
            'name' => 'Seller Welcome Voucher',
            'code' => 'WELCOME10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'minimum_spend' => 500,
            'usage_limit' => 10,
            'status' => 'active',
        ]);
        $items = json_encode([['id' => $cartItem->id, 'quantity' => 2]]);

        $this->actingAs($buyer)->post(route('buyer.checkout.post'), [
            'checkout_source' => 'cart',
            'items' => $items,
            'voucher_code' => 'welcome10',
        ])->assertOk()->assertSee('You save &#8369;100.00.', false);

        $this->actingAs($buyer)->post(route('buyer.order.store'), [
            'checkout_source' => 'cart',
            'items' => $items,
            'voucher_code' => 'NOT-A-SELLER-VOUCHER',
            'payment_method' => 'cash_on_delivery',
            'recipient_name' => 'Buyer',
            'contact_number' => '09170000000',
            'delivery_address' => 'Manila',
        ])->assertSessionHasErrors('voucher_code');
        $this->assertDatabaseCount('orders', 0);

        $checkoutSnapshot = json_encode([[
            'id' => $cartItem->id,
            'product_id' => $product->id,
            'product_variation_id' => null,
            'variant' => 'Standard',
            'quantity' => 2,
        ]]);
        $cartItem->delete();

        $this->actingAs($buyer)->post(route('buyer.order.store'), [
            'checkout_source' => 'cart',
            'items' => $checkoutSnapshot,
            'voucher_code' => 'WELCOME10',
            'payment_method' => 'cash_on_delivery',
            'recipient_name' => 'Buyer',
            'contact_number' => '09170000000',
            'delivery_address' => 'Manila',
        ])->assertRedirect(route('buyer.orders.success'));

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'total_amount' => '900.00',
        ]);
        $this->assertSame(1, $voucher->fresh()->uses);
    }

    public function test_guest_preview_hides_restricted_product_data_and_buyer_checkout_creates_seller_orders(): void
    {
        $category = Category::create([
            'name' => 'Bags',
            'slug' => 'bags',
            'status' => 'active',
        ]);

        $sellerA = User::factory()->create([
            'name' => 'Seller Alpha',
            'role' => 'seller',
            'status' => 'active',
        ]);

        $sellerB = User::factory()->create([
            'name' => 'Seller Beta',
            'role' => 'seller',
            'status' => 'active',
        ]);

        $buyer = User::factory()->create([
            'name' => 'Buyer One',
            'role' => 'buyer',
            'status' => 'active',
            'contact_number' => '09171234567',
        ]);

        $this->actingAs($sellerA)
            ->post(route('seller.products.store'), [
                'name' => 'Handcrafted Bag',
                'category_id' => $category->id,
                'description' => 'Locally handcrafted everyday bag.',
                'price' => 799,
                'stock' => 10,
                'listing_status' => 'active',
            ])
            ->assertRedirect(route('seller.products'));

        $productA = Product::where('seller_id', $sellerA->id)
            ->where('name', 'Handcrafted Bag')
            ->firstOrFail();

        $this->assertSame($sellerA->id, $productA->seller_id);
        $this->assertSame('799.00', (string) $productA->price);
        $this->assertSame(10, $productA->stock);

        ProductVariation::create(['product_id' => $productA->id, 'name' => 'Color', 'value' => 'Black']);
        ProductVariation::create(['product_id' => $productA->id, 'name' => 'Color', 'value' => 'Brown']);

        $productB = Product::create([
            'seller_id' => $sellerB->id,
            'category_id' => $category->id,
            'name' => 'Woven Tote',
            'slug' => 'woven-tote',
            'sku' => 'BAG-BETA',
            'description' => 'Durable woven tote.',
            'price' => 499,
            'stock' => 5,
            'status' => 'active',
            'listing_status' => 'active',
            'admin_status' => 'approved',
        ]);

        $guestResponse = $this->get('/products/handcrafted-bag')->assertOk();
        $guestResponse->assertSee('Handcrafted Bag');
        $guestResponse->assertSee('Login to view price and purchase');
        $guestResponse->assertDontSee('799');
        $guestResponse->assertDontSee('Stock');
        $guestResponse->assertDontSee('Black');
        $guestResponse->assertDontSee('Specifications');
        $guestResponse->assertDontSee('Reviews');
        $guestResponse->assertDontSee('sold');

        $cart = Cart::create(['buyer_id' => $buyer->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $productA->id, 'variant' => 'Black', 'quantity' => 2]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $productB->id, 'variant' => 'Standard', 'quantity' => 1]);

        $this->actingAs($buyer)
            ->get(route('buyer.product-details', ['slug' => $productA->slug]))
            ->assertOk()
            ->assertSee((string) number_format((float) $productA->price, 2))
            ->assertSee('data-test-add-cart', false)
            ->assertSee('data-test-buy-now', false)
            ->assertSee('data-wishlist', false)
            ->assertSee('data-product-id="'.$productA->id.'"', false)
            ->assertSee('Black');

        $this->actingAs($buyer)
            ->get(route('buyer.products', ['sort' => 'featured']))
            ->assertOk()
            ->assertSee('Handcrafted Bag');

        $addResponse = $this->actingAs($buyer)
            ->get(route('buyer.cart', ['add' => $productA->slug, 'quantity' => 1]));

        $addResponse->assertRedirect(route('buyer.cart'));

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $productA->id,
            'quantity' => 1,
        ]);

        CartItem::query()->delete();

        $buyNowResponse = $this->actingAs($buyer)
            ->get(route('buyer.cart', ['add' => $productA->slug, 'quantity' => 1, 'checkout' => 1]));

        $buyNowResponse->assertRedirect(route('buyer.checkout'));

        CartItem::query()->delete();

        $this->actingAs($buyer)
            ->post(route('buyer.wishlist.toggle', ['product' => $productA->id]))
            ->assertRedirect();

        $this->assertDatabaseHas('wishlist_items', [
            'buyer_id' => $buyer->id,
            'product_id' => $productA->id,
        ]);

        $this->actingAs($buyer)
            ->post(route('buyer.wishlist.toggle', ['product' => $productA->id]))
            ->assertRedirect();

        $this->assertSame(0, WishlistItem::where('buyer_id', $buyer->id)->where('product_id', $productA->id)->count());

        CartItem::create(['cart_id' => $cart->id, 'product_id' => $productA->id, 'variant' => 'Black', 'quantity' => 2]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $productB->id, 'variant' => 'Standard', 'quantity' => 1]);

        $this->actingAs($buyer)
            ->post(route('buyer.order.store'), [
                'payment_method' => 'cash_on_delivery',
                'recipient_name' => 'Buyer One',
                'contact_number' => '09171234567',
                'delivery_address' => '123 Sample Street, Manila',
                'total_amount' => '1.00',
                'price' => '1.00',
            ])
            ->assertRedirect(route('buyer.orders.success'));

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $buyer->id,
            'seller_id' => $sellerA->id,
            'total_amount' => '1598.00',
        ]);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $buyer->id,
            'seller_id' => $sellerB->id,
            'total_amount' => '499.00',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $productA->id,
            'variant' => 'Black',
            'quantity' => 2,
            'unit_price' => '799.00',
        ]);

        $this->assertSame(8, $productA->fresh()->stock);
        $this->assertSame(4, $productB->fresh()->stock);
        $this->assertSame(0, CartItem::where('cart_id', $cart->id)->count());
        $this->assertCount(2, Order::where('buyer_id', $buyer->id)->get());

        $sellerOrder = Order::where('seller_id', $sellerA->id)->firstOrFail();
        $this->actingAs($sellerA)->get(route('seller.orders', ['mode' => 'show', 'order' => $sellerOrder->order_number]))->assertOk();
        $this->actingAs($sellerB)->get(route('seller.orders'))->assertOk()->assertDontSee($sellerOrder->order_number);
        $this->actingAs($buyer)->get(route('buyer.orders.show', ['id' => $sellerOrder->order_number]))->assertOk();

        $this->actingAs($sellerB)
            ->patch(route('seller.orders.status', $sellerOrder), ['status' => 'confirmed'])
            ->assertForbidden();

        $this->actingAs($sellerA)
            ->patch(route('seller.orders.status', $sellerOrder), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertSame('confirmed', $sellerOrder->fresh()->status);
        $this->actingAs($buyer)
            ->get(route('buyer.orders.show', ['id' => $sellerOrder->order_number]))
            ->assertOk()
            ->assertSee('To Ship');

        $this->assertDatabaseHas('workspace_notifications', [
            'user_id' => $buyer->id,
            'type' => 'orders',
            'title' => 'Order status updated',
        ]);

        $this->actingAs($buyer)
            ->post(route('buyer.messages.send'), [
                'recipient_id' => $sellerA->id,
                'body' => 'Can you prepare this today?',
                'order_id' => $sellerOrder->id,
            ])
            ->assertRedirect();

        $buyerMessage = Message::where('sender_id', $buyer->id)->where('recipient_id', $sellerA->id)->firstOrFail();
        $this->assertSame($sellerOrder->id, $buyerMessage->order_id);
        $this->actingAs($sellerA)->get(route('seller.messages', ['buyer' => $buyer->id]))->assertOk()->assertSee('Can you prepare this today?');

        $this->actingAs($sellerA)
            ->post(route('seller.messages.send'), [
                'recipient_id' => $buyer->id,
                'body' => 'Preparing it now.',
            ])
            ->assertRedirect();

        $this->actingAs($buyer)->get(route('buyer.messages', ['seller' => $sellerA->id]))->assertOk()->assertSee('Preparing it now.');

        $sellerOrder->refresh()->update(['status' => 'completed']);

        $this->actingAs($buyer)
            ->post(route('buyer.orders.review.store', ['id' => $sellerOrder->order_number]), [
                'rating' => 5,
                'review' => 'Excellent quality and fast preparation.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $productA->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $sellerA->id,
            'rating' => 5,
        ]);
        $this->actingAs($buyer)
            ->get(route('buyer.product-details', ['slug' => $productA->slug]))
            ->assertOk()
            ->assertSee('Excellent quality and fast preparation.')
            ->assertSee($buyer->name);
        $this->actingAs($sellerA)->get(route('seller.reviews'))->assertOk()->assertSee('Excellent quality');

        $this->actingAs($buyer)
            ->post(route('buyer.orders.return.store', ['id' => $sellerOrder->order_number]), [
                'request_type' => 'Return',
                'reason' => 'Item issue',
                'details' => 'The buyer is requesting review of this completed order.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('refunds', [
            'order_id' => $sellerOrder->id,
            'buyer_id' => $buyer->id,
            'status' => 'open',
        ]);
        $this->assertSame('returns', $sellerOrder->fresh()->status);
    }

    public function test_stock_cart_cancellation_and_ownership_guards_are_enforced(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $buyerA = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $buyerB = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Limited Stock Item',
            'slug' => 'limited-stock-item',
            'sku' => 'LIMITED-1',
            'description' => 'A limited item.',
            'price' => 1000,
            'stock' => 5,
            'status' => 'active',
            'listing_status' => 'active',
            'admin_status' => 'approved',
        ]);

        $cartA = Cart::create(['buyer_id' => $buyerA->id]);
        $cartItem = CartItem::create(['cart_id' => $cartA->id, 'product_id' => $product->id, 'variant' => 'Red', 'quantity' => 3]);
        CartItem::create(['cart_id' => $cartA->id, 'product_id' => $product->id, 'variant' => 'Blue', 'quantity' => 3]);

        $this->actingAs($buyerB)
            ->patch(route('buyer.cart.update', ['item' => $cartItem->id]), ['quantity' => 1])
            ->assertForbidden();

        $this->actingAs($buyerA)
            ->post(route('buyer.order.store'), [
                'payment_method' => 'cash_on_delivery',
                'recipient_name' => 'Buyer A',
                'contact_number' => '09170000000',
                'delivery_address' => 'Buyer A Address',
            ])
            ->assertStatus(422);

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(0, Order::count());
        $this->assertSame(2, CartItem::where('cart_id', $cartA->id)->count());

        CartItem::where('cart_id', $cartA->id)->delete();
        CartItem::create(['cart_id' => $cartA->id, 'product_id' => $product->id, 'variant' => 'Standard', 'quantity' => 2]);

        $this->actingAs($buyerA)
            ->post(route('buyer.order.store'), [
                'payment_method' => 'cash_on_delivery',
                'recipient_name' => 'Buyer A',
                'contact_number' => '09170000000',
                'delivery_address' => 'Buyer A Address',
            ])
            ->assertRedirect(route('buyer.orders.success'));

        $order = Order::where('buyer_id', $buyerA->id)->firstOrFail();
        $this->assertSame(3, $product->fresh()->stock);

        $this->actingAs($buyerB)
            ->get(route('buyer.orders.show', ['id' => $order->order_number]))
            ->assertNotFound();

        $this->actingAs($buyerB)
            ->post(route('buyer.orders.cancel'), [
                'order_id' => $order->order_number,
                'reason' => 'Not mine',
            ])
            ->assertNotFound();

        $this->actingAs($buyerA)
            ->post(route('buyer.orders.cancel'), [
                'order_id' => $order->order_number,
                'reason' => 'Changed my mind',
            ])
            ->assertRedirect(route('buyer.orders'));

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock);

        $this->actingAs($buyerA)
            ->post(route('buyer.orders.cancel'), [
                'order_id' => $order->order_number,
                'reason' => 'Try restore twice',
            ])
            ->assertStatus(422);

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(1, WorkspaceNotification::where('user_id', $seller->id)->where('title', 'Order cancelled')->count());
        $this->assertSame(0, ProductReview::count());
        $this->assertSame(0, Refund::count());
    }
}
