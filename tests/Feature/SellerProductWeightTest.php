<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerProductWeightTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_persists_edits_and_reloads_a_default_variant_weight(): void
    {
        [$sellerUser, $category] = $this->sellerAndCategory();

        $this->actingAs($sellerUser)->post(route('seller.products.store'), $this->payload($category->id, 300))
            ->assertRedirect();

        $product = Product::where('slug', 'weighted-mug')->sole();
        $variant = $product->variants()->sole();
        $this->assertSame(300, $variant->weight_grams);

        $this->actingAs($sellerUser)->put(route('seller.products.update', $product), $this->payload($category->id, 350))
            ->assertSessionHasNoErrors();

        $this->assertSame($variant->id, $product->fresh()->variants()->sole()->id);
        $this->assertSame(350, $variant->fresh()->weight_grams);
        $this->actingAs($sellerUser)->get(route('seller.products', ['mode' => 'edit', 'product' => $product->id]))
            ->assertOk()
            ->assertSee('value="350"', false);

        $otherSeller = User::factory()->create(['status' => 'active']);
        $otherSeller->grant('seller');
        Seller::create(['user_id' => $otherSeller->id, 'name' => 'Other Shop', 'slug' => 'other-shop', 'status' => 'approved']);
        $this->actingAs($otherSeller)->put(route('seller.products.update', $product), $this->payload($category->id, 999))
            ->assertForbidden();
        $this->assertSame(350, $variant->fresh()->weight_grams);
    }

    public function test_each_submitted_variant_keeps_its_own_weight(): void
    {
        [$sellerUser, $category] = $this->sellerAndCategory();
        $payload = $this->payload($category->id, 300);
        $payload['variation_name'] = ['Size', 'Size'];
        $payload['variation_value'] = ['S', 'L'];
        $payload['variation_sku'] = ['MUG-S', 'MUG-L'];
        $payload['variation_price'] = ['299.00', '349.00'];
        $payload['variation_stock'] = [4, 5];
        $payload['variation_weight_grams'] = [250, 700];

        $this->actingAs($sellerUser)->post(route('seller.products.store'), $payload)->assertRedirect();

        $weights = Product::where('slug', 'weighted-mug')->sole()->variants()->orderBy('sku')->pluck('weight_grams', 'sku')->all();
        $this->assertSame(['MUG-L' => 700, 'MUG-S' => 250], $weights);
    }

    private function sellerAndCategory(): array
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->grant('seller');
        Seller::create(['user_id' => $user->id, 'name' => 'Weight Shop', 'slug' => 'weight-shop', 'status' => 'approved']);
        $parent = Category::create(['name' => 'Home', 'slug' => 'home', 'is_active' => true]);
        $child = Category::create(['parent_id' => $parent->id, 'name' => 'Kitchen', 'slug' => 'kitchen', 'is_active' => true]);

        return [$user, $child];
    }

    private function payload(int $categoryId, int $weight): array
    {
        return [
            'name' => 'Weighted Mug', 'category_id' => $categoryId, 'description' => 'A ceramic mug.',
            'price' => '299.00', 'stock' => 10, 'weight_grams' => $weight, 'listing_status' => 'active',
        ];
    }
}
