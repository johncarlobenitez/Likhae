<?php

namespace Tests\Feature;

use App\Models\Buyer\Address;
use App\Models\Buyer\Cart;
use App\Models\Buyer\CartItem;
use App\Models\Logistics\LogisticsCenter;
use App\Models\Logistics\ServiceArea;
use App\Models\Logistics\ServiceAreaLocation;
use App\Models\Seller\Category;
use App\Models\Seller\Product;
use App\Models\Seller\ProductVariant;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use App\Services\Marketplace\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutAddressSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_keeps_the_selected_address_and_coordinates_after_profile_edit(): void
    {
        $buyer = User::factory()->create(['account_type' => User::TYPE_BUYER]);
        $sellerUser = User::factory()->create(['account_type' => User::TYPE_SELLER]);
        $centerUser = User::factory()->create(['account_type' => User::TYPE_LOGISTICS]);

        $center = LogisticsCenter::create([
            'owner_user_id' => $centerUser->id,
            'code' => 'SNAPSHOT-CENTER',
            'business_name' => 'Snapshot Center',
            'status' => 'ACTIVE',
        ]);
        $area = ServiceArea::create([
            'logistics_center_id' => $center->id,
            'code' => 'SNAPSHOT-AREA',
            'name' => 'Snapshot Area',
            'is_active' => true,
        ]);
        ServiceAreaLocation::create([
            'service_area_id' => $area->id,
            'province_code' => 'P-SNAPSHOT',
            'province_name' => 'Snapshot Province',
            'municipality_code' => 'M-SNAPSHOT',
            'municipality_name' => 'Snapshot Municipality',
            'barangay_code' => 'B-SNAPSHOT',
            'barangay_name' => 'Snapshot Barangay',
        ]);

        $category = Category::create([
            'name' => 'Snapshot Category',
            'slug' => 'snapshot-category',
            'is_active' => true,
        ]);
        $seller = SellerProfile::create([
            'user_id' => $sellerUser->id,
            'primary_category_id' => $category->id,
            'business_name' => 'Snapshot Seller',
            'status' => 'ACTIVE',
        ]);
        $product = Product::create([
            'seller_profile_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Snapshot Product',
            'slug' => 'snapshot-product',
            'status' => 'ACTIVE',
            'published_at' => now(),
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SNAPSHOT-SKU',
            'price' => 100,
            'stock' => 5,
            'is_default' => true,
            'is_active' => true,
        ]);
        $cart = Cart::create(['buyer_user_id' => $buyer->id, 'status' => Cart::STATUS_ACTIVE]);
        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);
        $address = Address::create([
            'user_id' => $buyer->id,
            'label' => 'Home',
            'recipient_name' => $buyer->name,
            'contact_number' => $buyer->contact_number,
            'province_code' => 'P-SNAPSHOT',
            'province_name' => 'Snapshot Province',
            'municipality_code' => 'M-SNAPSHOT',
            'municipality_name' => 'Snapshot Municipality',
            'barangay_code' => 'B-SNAPSHOT',
            'barangay_name' => 'Snapshot Barangay',
            'street_address' => 'Original Street',
            'latitude' => '14.5995000',
            'longitude' => '120.9842000',
            'is_default' => true,
        ]);

        $order = app(CheckoutService::class)->placeOrder(
            $buyer,
            $address,
            collect([$item]),
            'COD',
            [],
            $center->id,
        );

        $address->update([
            'street_address' => 'Updated Street',
            'latitude' => '14.7000000',
            'longitude' => '121.1000000',
        ]);

        $this->assertDatabaseHas('order_addresses', [
            'order_id' => $order->id,
            'street_address' => 'Original Street',
            'latitude' => '14.5995000',
            'longitude' => '120.9842000',
        ]);
        $this->assertDatabaseMissing('order_addresses', [
            'order_id' => $order->id,
            'street_address' => 'Updated Street',
        ]);
    }
}
