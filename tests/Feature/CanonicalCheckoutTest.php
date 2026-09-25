<?php

namespace Tests\Feature;

use App\Models\Buyer\Address;
use App\Models\Buyer\Cart;
use App\Models\Seller\Category;
use App\Models\Logistics\LogisticsProvider;
use App\Models\Seller\Product;
use App\Models\Seller\ProductVariant;
use App\Models\Seller\Seller;
use App\Models\Logistics\ServiceArea;
use App\Models\Rider\Rider;
use App\Models\User;
use App\Services\CheckoutService;
use App\Services\LedgerService;
use App\Models\Buyer\ReturnRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanonicalCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_one_order_and_one_seller_order_per_seller(): void
    {
        [$buyer, $address, $provider] = $this->buyerAndCourier();
        $cart = Cart::create(['user_id' => $buyer->id]);

        $couriers = [];
        $variants = [];
        foreach ([129900, 75000] as $index => $price) {
            $variant = $this->variant($index + 1, $price, 5);
            $variants[] = $variant;
            $couriers[$variant->product->seller_id] = $provider->id;
            $cart->items()->create(['product_variant_id' => $variant->id, 'quantity' => 2, 'selected' => true]);
        }

        $order = app(CheckoutService::class)->place($buyer, $address, 'cod', $couriers);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('seller_orders', 2);
        $this->assertDatabaseCount('order_items', 2);
        $this->assertDatabaseCount('payments', 1);
        $this->assertSame(2, $order->sellerOrders->count());
        $this->assertSame(3, $variants[0]->fresh()->stock);
        $this->assertSame(3, $variants[1]->fresh()->stock);
        $this->assertDatabaseCount('cart_items', 0);

        $sellerOrder = $order->sellerOrders->first();
        $otherBuyer = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);
        $otherBuyer->grant('buyer');
        $this->actingAs($otherBuyer)->get(route('buyer.orders.show', $sellerOrder->id))->assertForbidden();
        $otherSellerOwner = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);
        $otherSellerOwner->grant('seller');
        Seller::create(['user_id' => $otherSellerOwner->id, 'name' => 'Other Seller', 'slug' => 'other-seller', 'status' => 'approved']);
        $this->actingAs($otherSellerOwner)->patch(route('seller.orders.status', $sellerOrder), ['status' => 'accepted'])->assertForbidden();
        $sellerOrder->transitionTo('accepted', $sellerOrder->seller->owner)
            ->transitionTo('packed', $sellerOrder->seller->owner)
            ->transitionTo('ready_to_ship', $sellerOrder->seller->owner);
        $riderUser = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $riderUser->grant('rider');
        $rider = Rider::create(['user_id' => $riderUser->id, 'logistics_provider_id' => $provider->id, 'is_active' => true]);
        $shipment = $sellerOrder->fresh()->shipment;
        $shipment->assignTo($rider, $provider->owner);
        $shipment->transitionTo('picked_up', $riderUser)->transitionTo('in_transit', $riderUser)->transitionTo('out_for_delivery', $riderUser)
            ->transitionTo('delivered', $riderUser, null, 'proofs/test.jpg', 'Maria Santos');
        $this->assertSame('delivered', $sellerOrder->fresh()->status);
        $this->assertTrue($shipment->fresh()->cod_collected);
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('paid', $order->payments()->sole()->status);
        $this->assertDatabaseHas('delivery_events', ['shipment_id' => $shipment->id, 'status' => 'delivered', 'receiver_name' => 'Maria Santos']);

        $otherOwner = User::factory()->create(['status'=>'active','email_verified_at'=>now()]);
        $otherOwner->grant('logistics');
        LogisticsProvider::create(['user_id'=>$otherOwner->id,'name'=>'Other Courier','slug'=>'other-courier','status'=>'approved']);
        $this->actingAs($otherOwner)->post(route('logistics.dispatch.assign',$shipment),['rider_id'=>$rider->id])->assertForbidden();
        $otherRiderUser = User::factory()->create(['status'=>'active','email_verified_at'=>now()]);
        $otherRiderUser->grant('rider');
        Rider::create(['user_id'=>$otherRiderUser->id,'logistics_provider_id'=>$provider->id,'is_active'=>true]);
        $this->actingAs($otherRiderUser)->patch(route('rider.shipments.transition',$shipment),['status'=>'failed','note'=>'Not mine'])->assertForbidden();
        $this->get(route('tracking.show', $shipment->tracking_code))
            ->assertOk()->assertSee($shipment->tracking_code)->assertSee('Manila')
            ->assertDontSee('Maria Santos')->assertDontSee('09170000000')->assertDontSee('1 Main St');

        $this->actingAs($buyer)->post(route('buyer.orders.received', $sellerOrder))
            ->assertRedirect(route('buyer.orders.review', $sellerOrder));
        $this->assertSame('completed', $sellerOrder->fresh()->status);
        $this->assertDatabaseHas('seller_order_events', [
            'seller_order_id' => $sellerOrder->id,
            'from_status' => 'delivered',
            'to_status' => 'completed',
            'user_id' => $buyer->id,
        ]);
        $this->assertDatabaseHas('ledger_entries', ['account_type' => 'seller', 'account_id' => $sellerOrder->seller_id, 'type' => 'sale']);
        $return = ReturnRequest::create(['seller_order_id' => $sellerOrder->id, 'buyer_id' => $buyer->id, 'reason' => 'Damaged', 'status' => 'approved']);
        app(LedgerService::class)->refund($return, $buyer);
        $this->assertSame(5, $variants[0]->fresh()->stock);
        $this->assertDatabaseHas('return_requests', ['id' => $return->id, 'status' => 'refunded', 'cod_repayment_status' => 'pending']);
        $this->assertDatabaseHas('ledger_entries', ['account_type' => 'seller', 'account_id' => $sellerOrder->seller_id, 'type' => 'refund_reversal']);
    }

    public function test_checkout_refuses_overselling_without_partial_writes(): void
    {
        [$buyer, $address, $provider] = $this->buyerAndCourier();
        $variant = $this->variant(1, 50000, 1);
        Cart::create(['user_id' => $buyer->id])->items()->create(['product_variant_id' => $variant->id, 'quantity' => 2, 'selected' => true]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        try {
        app(CheckoutService::class)->place($buyer, $address, 'cod', [$variant->product->seller_id => $provider->id]);
        } finally {
            $this->assertDatabaseCount('orders', 0);
            $this->assertSame(1, $variant->fresh()->stock);
        }
    }

    public function test_shipping_fee_uses_database_variant_weight_times_quantity_with_a_legacy_fallback(): void
    {
        [$buyer, $address, $provider] = $this->buyerAndCourier();
        $variant = $this->variant(1, 50000, 10);
        $variant->update(['weight_grams' => 450]);
        $cart = Cart::create(['user_id' => $buyer->id]);
        $cart->items()->create(['product_variant_id' => $variant->id, 'quantity' => 3, 'selected' => true]);

        $order = app(CheckoutService::class)->place($buyer, $address, 'cod', [$variant->product->seller_id => $provider->id]);
        $this->assertSame(6000, $order->sellerOrders()->sole()->shipping_fee_minor); // 450 g x 3 = 1,350 g.

        $fallbackVariant = $this->variant(2, 50000, 10);
        $fallbackVariant->update(['weight_grams' => null]);
        $cart->items()->delete();
        $cart->items()->create(['product_variant_id' => $fallbackVariant->id, 'quantity' => 3, 'selected' => true]);

        $fallbackOrder = app(CheckoutService::class)->place($buyer, $address, 'cod', [$fallbackVariant->product->seller_id => $provider->id]);
        $this->assertSame(6000, $fallbackOrder->sellerOrders()->sole()->shipping_fee_minor); // 500 g x 3 fallback.
    }

    public function test_shipping_sums_variant_grams_before_rounding_a_single_seller_parcel(): void
    {
        [$buyer, $address, $provider] = $this->buyerAndCourier();
        $first = $this->variant(1, 50000, 10);
        $first->update(['weight_grams' => 250]);
        $second = ProductVariant::create([
            'product_id' => $first->product_id, 'sku' => 'SKU-1-HEAVY', 'name' => 'Heavy',
            'options' => ['Size' => 'Large'], 'price_minor' => 50000, 'stock' => 10,
            'weight_grams' => 700, 'is_active' => true,
        ]);
        $cart = Cart::create(['user_id' => $buyer->id]);
        $cart->items()->create(['product_variant_id' => $first->id, 'quantity' => 2, 'selected' => true]);
        $cart->items()->create(['product_variant_id' => $second->id, 'quantity' => 1, 'selected' => true]);

        $order = app(CheckoutService::class)->place($buyer, $address, 'cod', [$first->product->seller_id => $provider->id]);

        $this->assertSame(1, $order->sellerOrders()->count());
        $this->assertSame(6000, $order->sellerOrders()->sole()->shipping_fee_minor); // (250 g x 2) + (700 g x 1) = 1,200 g.
    }

    public function test_third_failed_delivery_returns_the_shipment_and_records_each_attempt(): void
    {
        [$buyer, $address, $provider] = $this->buyerAndCourier();
        $variant = $this->variant(1, 50000, 5);
        Cart::create(['user_id' => $buyer->id])->items()->create(['product_variant_id' => $variant->id, 'quantity' => 1, 'selected' => true]);
        $sellerOrder = app(CheckoutService::class)->place($buyer, $address, 'cod', [$variant->product->seller_id => $provider->id])->sellerOrders()->sole();
        $sellerOrder->transitionTo('accepted', $sellerOrder->seller->owner)->transitionTo('packed', $sellerOrder->seller->owner)->transitionTo('ready_to_ship', $sellerOrder->seller->owner);
        $riderUser = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);
        $riderUser->grant('rider');
        $rider = Rider::create(['user_id' => $riderUser->id, 'logistics_provider_id' => $provider->id, 'is_active' => true]);
        $shipment = $sellerOrder->fresh()->shipment;

        foreach (range(1, 3) as $attempt) {
            $shipment = $shipment->assignTo($rider, $provider->owner);
            $shipment = $shipment->transitionTo('picked_up', $riderUser)->transitionTo('in_transit', $riderUser)->transitionTo('out_for_delivery', $riderUser);
            $shipment = $shipment->transitionTo('failed', $riderUser, 'Recipient unavailable');
            $this->assertSame($attempt === 3 ? 'returned' : 'failed', $shipment->status);
        }

        $this->assertSame(3, $shipment->attempts);
        $this->assertDatabaseCount('delivery_events', 17);
        $this->assertDatabaseHas('delivery_events', ['shipment_id' => $shipment->id, 'status' => 'returned', 'attempt' => 3]);
    }

    public function test_rider_delivery_queue_keeps_picked_up_and_in_transit_shipments_visible(): void
    {
        [$buyer, $address, $provider] = $this->buyerAndCourier();
        $variant = $this->variant(1, 50000, 5);
        Cart::create(['user_id' => $buyer->id])->items()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'selected' => true,
        ]);
        $sellerOrder = app(CheckoutService::class)->place(
            $buyer,
            $address,
            'cod',
            [$variant->product->seller_id => $provider->id],
        )->sellerOrders()->sole();
        $sellerOrder->transitionTo('accepted', $sellerOrder->seller->owner)
            ->transitionTo('packed', $sellerOrder->seller->owner)
            ->transitionTo('ready_to_ship', $sellerOrder->seller->owner);

        $riderUser = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);
        $riderUser->grant('rider');
        $rider = Rider::create([
            'user_id' => $riderUser->id,
            'logistics_provider_id' => $provider->id,
            'is_active' => true,
        ]);
        $shipment = $sellerOrder->fresh()->shipment->assignTo($rider, $provider->owner)
            ->transitionTo('picked_up', $riderUser)
            ->transitionTo('in_transit', $riderUser);

        $this->actingAs($riderUser)->get(route('rider.deliveries'))
            ->assertOk()
            ->assertSee($shipment->tracking_code)
            ->assertSeeText('In Transit');
    }

    private function buyerAndCourier(): array
    {
        $buyer = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);
        $buyer->grant('buyer');
        $address = Address::create(['user_id' => $buyer->id, 'recipient' => 'Buyer', 'phone' => '09170000000', 'line1' => '1 Main St', 'city' => 'Manila', 'city_code' => 'MAN', 'province' => 'Metro Manila', 'is_default' => true]);
        $owner = User::factory()->create(['status' => 'active']);
        $provider = LogisticsProvider::create(['user_id' => $owner->id, 'name' => 'Courier', 'slug' => 'courier', 'status' => 'approved']);
        ServiceArea::create(['logistics_provider_id' => $provider->id, 'province' => 'Metro Manila', 'city' => 'Manila', 'city_code' => 'MAN', 'base_fee_minor' => 5000, 'per_kg_fee_minor' => 1000]);
        return [$buyer, $address, $provider];
    }

    private function variant(int $sellerNumber, int $price, int $stock): ProductVariant
    {
        $owner = User::factory()->create(['status' => 'active']);
        $seller = Seller::create(['user_id' => $owner->id, 'name' => "Seller $sellerNumber", 'slug' => "seller-$sellerNumber", 'status' => 'approved', 'commission_bps' => 800]);
        $parent = Category::firstOrCreate(['slug' => 'parent'], ['name' => 'Parent', 'is_active' => true]);
        $child = Category::firstOrCreate(['slug' => 'child'], ['parent_id' => $parent->id, 'name' => 'Child', 'is_active' => true]);
        $product = Product::create(['seller_id' => $seller->id, 'category_id' => $child->id, 'name' => "Product $sellerNumber", 'slug' => "product-$sellerNumber", 'min_price_minor' => $price, 'is_active' => true]);
        return ProductVariant::create(['product_id' => $product->id, 'sku' => "SKU-$sellerNumber", 'name' => 'Default', 'options' => [], 'price_minor' => $price, 'stock' => $stock, 'weight_grams' => 800, 'is_active' => true]);
    }
}
