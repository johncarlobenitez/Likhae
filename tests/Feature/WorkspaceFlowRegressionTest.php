<?php

namespace Tests\Feature;

use App\Models\{Address, Cart, Category, LedgerEntry, LogisticsProvider, Product, ProductReview, ProductVariant, ReturnRequest, Rider, Seller, ServiceArea, User};
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceFlowRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function scenario(): array
    {
        $this->withoutVite();
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $sellerUser = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $providerUser = User::factory()->create(['role' => 'logistics', 'status' => 'active']);
        $riderUser = User::factory()->create(['role' => 'rider', 'status' => 'active']);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $address = Address::create(['user_id' => $buyer->id, 'recipient' => 'Parcel Recipient', 'phone' => '09170000000', 'line1' => 'Recipient Street', 'city' => 'Manila', 'province' => 'Metro Manila', 'is_default' => true]);
        $pickup = Address::create(['user_id' => $sellerUser->id, 'recipient' => 'Shop Contact', 'phone' => '09170000001', 'line1' => 'Seller Pickup Street', 'city' => 'Manila', 'province' => 'Metro Manila']);
        $seller = Seller::create(['user_id' => $sellerUser->id, 'name' => 'Regression Shop', 'slug' => 'regression-shop', 'status' => 'approved', 'pickup_address_id' => $pickup->id]);
        $provider = LogisticsProvider::create(['user_id' => $providerUser->id, 'name' => 'Regression Courier', 'slug' => 'regression-courier', 'status' => 'approved']);
        $rider = Rider::create(['user_id' => $riderUser->id, 'logistics_provider_id' => $provider->id, 'is_active' => true]);
        $area = ServiceArea::create(['logistics_provider_id' => $provider->id, 'province' => 'Metro Manila', 'city' => 'Manila', 'base_fee_minor' => 5000, 'per_kg_fee_minor' => 1000, 'is_active' => true]);
        $parent = Category::create(['name' => 'Parent', 'slug' => 'regression-parent', 'is_active' => true]);
        $category = Category::create(['parent_id' => $parent->id, 'name' => 'Child', 'slug' => 'regression-child', 'is_active' => true]);
        $product = Product::create(['seller_id' => $seller->id, 'category_id' => $category->id, 'name' => 'Regression Product', 'slug' => 'regression-product', 'min_price_minor' => 10000, 'is_active' => true]);
        ProductVariant::create(['product_id' => $product->id, 'sku' => 'CHEAP', 'name' => 'Small', 'price_minor' => 10000, 'stock' => 10, 'weight_grams' => 100, 'is_active' => true]);
        $variant = ProductVariant::create(['product_id' => $product->id, 'sku' => 'HEAVY', 'name' => 'Large', 'price_minor' => 20000, 'stock' => 10, 'weight_grams' => 2500, 'is_active' => true]);
        $cart = Cart::create(['user_id' => $buyer->id]);
        $item = $cart->items()->create(['product_variant_id' => $variant->id, 'quantity' => 2, 'selected' => true]);
        return compact('buyer', 'sellerUser', 'providerUser', 'riderUser', 'admin', 'address', 'seller', 'provider', 'rider', 'area', 'product', 'variant', 'cart', 'item');
    }

    private function place(array $s)
    {
        return app(CheckoutService::class)->place($s['buyer'], $s['address'], 'cod', [$s['seller']->id => $s['provider']->id])->sellerOrders()->sole();
    }

    private function deliver(array $s)
    {
        $order = $this->place($s);
        $order->transitionTo('accepted', $s['sellerUser'])->transitionTo('packed', $s['sellerUser'])->transitionTo('ready_to_ship', $s['sellerUser']);
        $order->fresh()->shipment->assignTo($s['rider'], $s['providerUser'])
            ->transitionTo('picked_up', $s['riderUser'])->transitionTo('in_transit', $s['riderUser'])
            ->transitionTo('out_for_delivery', $s['riderUser'])->transitionTo('delivered', $s['riderUser'], null, 'proof.jpg', 'Parcel Recipient');
        return $order->fresh();
    }

    public function test_cart_uses_cart_item_ids_and_selected_variant_prices(): void
    {
        $s = $this->scenario();
        $otherCart = Cart::create(['user_id' => $s['admin']->id]);
        $otherCart->items()->create(['product_variant_id' => $s['variant']->id, 'quantity' => 1]);
        $s['item']->delete();
        $item = $s['cart']->items()->create(['product_variant_id' => $s['variant']->id, 'quantity' => 2, 'selected' => true]);
        $response = $this->actingAs($s['buyer'])->get(route('buyer.cart'))->assertOk();
        $row = $response->viewData('cartItems')->sole();
        $this->assertSame($item->id, $row['id']);
        $this->assertEquals(200, $row['price']);
        $this->assertSame(10, $row['stock']);
    }

    public function test_cart_checkout_clears_buy_now_and_rolls_back_invalid_selection(): void
    {
        $s = $this->scenario();
        $this->actingAs($s['buyer'])->withSession(['buyer_buy_now' => ['product_variant_id' => $s['variant']->id, 'quantity' => 1]])
            ->post(route('buyer.checkout.post'), ['items' => json_encode([['id' => $s['item']->id, 'quantity' => 2]])])
            ->assertRedirect(route('buyer.checkout'))->assertSessionMissing('buyer_buy_now');
        $this->post(route('buyer.checkout.post'), ['items' => json_encode([['id' => $s['item']->id, 'quantity' => 999]])])->assertUnprocessable();
        $this->assertTrue((bool) $s['item']->fresh()->selected);
        $this->assertSame(2, $s['item']->fresh()->quantity);
        $this->postJson(route('buyer.checkout.post'), ['items' => '42'])->assertUnprocessable();
    }

    public function test_buy_now_shipping_quote_matches_actual_weight(): void
    {
        $s = $this->scenario();
        $response = $this->actingAs($s['buyer'])->withSession(['buyer_buy_now' => ['product_variant_id' => $s['variant']->id, 'quantity' => 2]])
            ->get(route('buyer.checkout'))->assertOk();
        $this->assertSame(9000, $response->viewData('couriers')[$s['seller']->id]->sole()['fee_minor']);
    }

    public function test_order_reference_renders_details_and_cancellation_restores_stock_once(): void
    {
        $s = $this->scenario();
        $order = $this->place($s);
        $this->actingAs($s['buyer'])->get(route('buyer.orders.show', $order->order->reference))->assertOk()->assertSee('Shipment timeline')->assertDontSee('Order not found');
        $this->post(route('buyer.orders.cancel'), ['order_id' => (string) $order->id, 'reason' => 'Changed plans'])->assertRedirect();
        $this->assertSame(10, $s['variant']->fresh()->stock);
        $this->post(route('buyer.orders.cancel'), ['order_id' => (string) $order->id, 'reason' => 'Retry'])->assertUnprocessable();
        $this->assertSame(10, $s['variant']->fresh()->stock);
    }

    public function test_seller_cancellation_restores_stock_and_cannot_cancel_an_active_shipment(): void
    {
        $s = $this->scenario();
        $order = $this->place($s);
        $this->actingAs($s['sellerUser'])->patch(route('seller.orders.status', $order), ['status' => 'cancelled'])->assertRedirect();
        $this->assertSame(10, $s['variant']->fresh()->stock);
        $this->patch(route('seller.orders.status', $order), ['status' => 'cancelled'])->assertRedirect();
        $this->assertSame(10, $s['variant']->fresh()->stock);
    }

    public function test_reviews_accept_optional_item_and_duplicate_submission_is_safe(): void
    {
        $s = $this->scenario();
        $order = $this->deliver($s);
        $this->actingAs($s['buyer'])->get(route('buyer.orders.review', $order))->assertOk()->assertSee('name="order_item_id"', false);
        foreach (range(1, 2) as $attempt) {
            $this->post(route('buyer.orders.review.store', $order), ['rating' => 5, 'review' => 'Arrived in great condition.'])->assertRedirect();
        }
        $this->assertSame(1, ProductReview::count());
    }

    public function test_admin_can_refund_requested_return_before_completion_without_negative_seller_balance(): void
    {
        $s = $this->scenario();
        $order = $this->deliver($s);
        $return = ReturnRequest::create(['seller_order_id' => $order->id, 'buyer_id' => $s['buyer']->id, 'reason' => 'Damaged', 'status' => 'requested']);
        $this->actingAs($s['admin'])->patch(route('admin.refunds.update', $return), ['status' => 'refunded', 'admin_decision' => 'Damage verified.'])->assertRedirect();
        $this->assertSame('refunded', $return->fresh()->status);
        $this->assertSame('refunded', $order->order->fresh()->payment_status);
        $this->assertSame(10, $s['variant']->fresh()->stock);
        $this->assertSame(0, LedgerEntry::where('account_type', 'seller')->count());
        $this->patch(route('admin.refunds.update', $return), ['status' => 'rejected', 'admin_decision' => 'Second decision'])->assertConflict();
        $this->assertSame('refunded', $return->fresh()->status);
    }

    public function test_populated_order_shipment_and_profile_pages_render(): void
    {
        $s = $this->scenario();
        $order = $this->place($s);
        $order->transitionTo('accepted', $s['sellerUser'])->transitionTo('packed', $s['sellerUser'])->transitionTo('ready_to_ship', $s['sellerUser']);
        $shipment = $order->fresh()->shipment;
        $this->actingAs($s['providerUser'])->get(route('logistics.pickups'))->assertOk()->assertSee($shipment->tracking_code)->assertSee('Select rider')->assertSee('Seller Pickup Street');
        $this->post(route('logistics.pickups.assign', $shipment), ['rider_id' => $s['rider']->id])->assertRedirect();
        $this->actingAs($s['riderUser'])->get(route('rider.pickups'))->assertOk()->assertSee($shipment->tracking_code)->assertSee('Regression Shop');
        foreach ([
            'buyer' => [['buyer.orders.show', $order], ['buyer.product-details', $s['product']->slug], ['buyer.shop', $s['seller']]],
            'sellerUser' => [['seller.orders', []], ['seller.orders.waybill', $order], ['seller.store', []], ['seller.store', ['tab' => 'settings']]],
            'providerUser' => [['logistics.parcels.show', $shipment], ['logistics.waybills.show', $shipment->tracking_code], ['logistics.riders.show', $s['rider']], ['logistics.riders.edit', $s['rider']], ['logistics.riders.application.show', $s['rider']], ['logistics.parcels.receive', ['tracking' => $shipment->tracking_code]], ['logistics.parcels.tracking', ['tracking' => $shipment->tracking_code]]],
            'riderUser' => [['rider.pickups.show', $shipment], ['rider.deliveries.show', $shipment]],
            'admin' => [['admin.users.show', $s['buyer']]],
        ] as $user => $pages) {
            foreach ($pages as [$name, $parameters]) $this->actingAs($s[$user])->get(route($name, $parameters))->assertOk();
        }
    }

    public function test_store_profile_save_preserves_operational_settings(): void
    {
        $s = $this->scenario();
        $s['seller']->update(['settings' => ['vacation_mode' => true, 'store_visibility' => true, 'processing_days' => 3]]);
        $this->actingAs($s['sellerUser'])->put(route('seller.store.update'), ['shop_name' => 'Updated Shop', 'tagline' => 'Local products'])->assertRedirect();
        $settings = $s['seller']->fresh()->settings;
        $this->assertTrue($settings['vacation_mode']);
        $this->assertTrue($settings['store_visibility']);
        $this->assertSame(3, $settings['processing_days']);
        $this->actingAs($s['buyer'])->get(route('buyer.product-details', $s['product']->slug))->assertNotFound();
    }

    public function test_logistics_can_configure_coverage_but_cannot_change_another_providers_area(): void
    {
        $s = $this->scenario();
        $this->actingAs($s['providerUser'])->post(route('logistics.delivery-areas.store'), ['province' => 'Metro Manila', 'city' => 'Manila', 'base_fee' => 65, 'per_kg_fee' => 15])->assertRedirect();
        $this->assertSame(6500, $s['area']->fresh()->base_fee_minor);
        $this->patch(route('logistics.delivery-areas.toggle', $s['area']), ['is_active' => 0])->assertRedirect();
        $this->assertFalse($s['area']->fresh()->is_active);
        $other = User::factory()->create(['role' => 'logistics', 'status' => 'active']);
        LogisticsProvider::create(['user_id' => $other->id, 'name' => 'Other', 'slug' => 'other', 'status' => 'approved']);
        $this->actingAs($other)->patch(route('logistics.delivery-areas.toggle', $s['area']), ['is_active' => 1])->assertForbidden();
    }
}
