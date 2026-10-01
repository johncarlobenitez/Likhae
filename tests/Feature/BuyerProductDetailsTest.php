<?php

namespace Tests\Feature;

use App\Models\Seller\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerProductDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_open_a_product_details_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $buyer = User::query()->where('email', 'buyer@likhae.com')->sole();
        $product = Product::query()->where('status', 'ACTIVE')->sole();

        $this->actingAs($buyer)
            ->get(route('buyer.product-details', $product->slug))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('data-test-add-cart', escape: false)
            ->assertSee('data-test-buy-now', escape: false)
            ->assertSee('data-variation-id="'.$product->variants()->firstOrFail()->id.'"', escape: false)
            ->assertSee(route('buyer.cart.add', $product), escape: false);
    }
}
