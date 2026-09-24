<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\LogisticsProvider;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Seller;
use App\Models\ServiceArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerCheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_to_cart_updates_quantity_without_creating_an_order(): void
    {
        [$buyer, $product, $variant] = $this->buyerWithProduct();

        $this->actingAs($buyer)
            ->post(route('buyer.cart.add', $product), [
                'product_variant_id' => $variant->id,
                'quantity' => 2,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseCount('orders', 0);

        $this->actingAs($buyer)
            ->post(route('buyer.cart.add', $product), [
                'product_variant_id' => $variant->id,
                'quantity' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'product_variant_id' => $variant->id,
            'quantity' => 3,
        ]);
    }

    public function test_buy_now_prepares_only_the_selected_variant_without_destroying_the_existing_cart(): void
    {
        [$buyer, $product, $variant] = $this->buyerWithProduct();
        $existingCart = Cart::create(['user_id' => $buyer->id]);
        $existingProduct = Product::create([
            'seller_id' => $product->seller_id,
            'category_id' => $product->category_id,
            'name' => 'Cargo Pants',
            'slug' => 'cargo-pants',
            'min_price_minor' => 85000,
            'is_active' => true,
        ]);
        $existingVariant = ProductVariant::create([
            'product_id' => $existingProduct->id,
            'sku' => 'SKU-EXISTING',
            'name' => 'Blue / L',
            'options' => ['Color' => 'Blue', 'Size' => 'L'],
            'price_minor' => 9900,
            'stock' => 8,
            'weight_grams' => 300,
            'is_active' => true,
        ]);
        $existingCart->items()->create(['product_variant_id' => $existingVariant->id, 'quantity' => 1, 'selected' => true]);

        $this->actingAs($buyer)
            ->withSession([
                'buyer_buy_now' => [
                    'product_variant_id' => $variant->id,
                    'quantity' => 2,
                ],
            ])
            ->get(route('buyer.checkout'))
            ->assertOk()
            ->assertSee($variant->product->name)
            ->assertDontSee($existingProduct->name);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $existingCart->id,
            'product_variant_id' => $existingVariant->id,
            'quantity' => 1,
        ]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_page_keeps_remote_product_images_as_direct_urls(): void
    {
        [$buyer, $product, $variant] = $this->buyerWithProduct();
        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'https://images.example.com/shirt.jpg',
            'sort_order' => 1,
        ]);

        $cart = Cart::create(['user_id' => $buyer->id]);
        $cart->items()->create(['product_variant_id' => $variant->id, 'quantity' => 1, 'selected' => true]);

        $this->actingAs($buyer)
            ->get(route('buyer.checkout'))
            ->assertOk()
            ->assertSee('https://images.example.com/shirt.jpg');
    }

    private function buyerWithProduct(): array
    {
        $buyer = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);
        $buyer->grant('buyer');

        $owner = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);
        $seller = Seller::create([
            'user_id' => $owner->id,
            'name' => 'Demo Seller',
            'slug' => 'demo-seller',
            'status' => 'approved',
            'commission_bps' => 800,
        ]);

        $parent = Category::firstOrCreate(['slug' => 'parent-category'], ['name' => 'Parent Category', 'is_active' => true]);
        $child = Category::firstOrCreate(['slug' => 'child-category'], ['parent_id' => $parent->id, 'name' => 'Child Category', 'is_active' => true]);

        $product = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $child->id,
            'name' => 'Cotton Shirt',
            'slug' => 'cotton-shirt',
            'min_price_minor' => 50000,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-TSHIRT',
            'name' => 'Black / M',
            'options' => ['Color' => 'Black', 'Size' => 'M'],
            'price_minor' => 59900,
            'stock' => 12,
            'weight_grams' => 250,
            'is_active' => true,
        ]);

        $address = Address::create([
            'user_id' => $buyer->id,
            'recipient' => 'Buyer',
            'phone' => '09170000000',
            'line1' => '1 Main St',
            'city' => 'Manila',
            'city_code' => 'MAN',
            'province' => 'Metro Manila',
            'is_default' => true,
        ]);

        $providerOwner = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);
        $provider = LogisticsProvider::create([
            'user_id' => $providerOwner->id,
            'name' => 'Courier',
            'slug' => 'courier',
            'status' => 'approved',
        ]);
        ServiceArea::create([
            'logistics_provider_id' => $provider->id,
            'province' => 'Metro Manila',
            'city' => 'Manila',
            'city_code' => 'MAN',
            'base_fee_minor' => 5000,
            'per_kg_fee_minor' => 1000,
            'is_active' => true,
        ]);

        return [$buyer, $product, $variant, $address];
    }
}
