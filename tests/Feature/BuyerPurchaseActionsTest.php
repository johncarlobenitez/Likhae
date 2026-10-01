<?php

namespace Tests\Feature;

use App\Models\Buyer\CartItem;
use App\Models\Seller\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerPurchaseActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_to_cart_stays_in_the_shopping_flow_without_starting_checkout(): void
    {
        [$buyer, $product, $variantId] = $this->purchaseFixture();

        $this->actingAs($buyer)
            ->from(route('buyer.product-details', $product->slug))
            ->post(route('buyer.cart.add', $product), [
                'product_variant_id' => $variantId,
                'quantity' => 2,
            ])
            ->assertRedirect(route('buyer.product-details', $product->slug))
            ->assertSessionMissing('checkout_cart_item_ids');

        $this->assertDatabaseHas('cart_items', [
            'product_variant_id' => $variantId,
            'quantity' => 2,
        ]);
    }

    public function test_buy_now_opens_checkout_for_only_the_purchased_item(): void
    {
        [$buyer, $product, $variantId] = $this->purchaseFixture();

        $response = $this->actingAs($buyer)->post(route('buyer.cart.add', $product), [
            'product_variant_id' => $variantId,
            'quantity' => 1,
            'checkout' => 1,
        ]);

        $item = CartItem::query()->where('product_variant_id', $variantId)->sole();

        $response
            ->assertRedirect(route('buyer.checkout'))
            ->assertSessionHas('checkout_cart_item_ids', [$item->id]);
    }

    public function test_checkout_uses_the_items_selected_from_the_cart(): void
    {
        [$buyer, $product, $variantId] = $this->purchaseFixture();
        $item = app(\App\Services\Marketplace\CartService::class)->add($buyer, $product, $variantId, 1);

        $this->actingAs($buyer)
            ->post(route('buyer.checkout.post'), ['cart_item_ids' => [$item->id]])
            ->assertRedirect(route('buyer.checkout'))
            ->assertSessionHas('checkout_cart_item_ids', [$item->id]);
    }

    /** @return array{User, Product, int} */
    private function purchaseFixture(): array
    {
        $this->seed(DatabaseSeeder::class);

        $buyer = User::query()->where('email', 'buyer@likhae.com')->sole();
        $product = Product::query()->with('variants')->where('status', 'ACTIVE')->sole();

        return [$buyer, $product, (int) $product->variants->firstOrFail()->id];
    }
}
