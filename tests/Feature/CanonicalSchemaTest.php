<?php

namespace Tests\Feature;

use App\Models\Buyer\Address;
use App\Models\Logistics\LogisticsProvider;
use App\Models\Rider\Rider;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CanonicalSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_milestone_four_uses_only_the_canonical_business_schema(): void
    {
        $required = [
            'users', 'roles', 'role_user', 'addresses', 'sellers', 'logistics_providers', 'riders',
            'categories', 'products', 'product_variants', 'product_images', 'carts', 'cart_items',
            'orders', 'seller_orders', 'seller_order_events', 'order_items', 'payments', 'shipments',
            'delivery_events', 'service_areas', 'settings', 'audit_logs', 'ledger_entries', 'payouts',
            'cod_remittances', 'return_requests', 'reviews', 'notifications',
        ];

        foreach ($required as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing canonical table: {$table}");
        }

        foreach (['buyer_addresses', 'seller_profiles', 'product_variations', 'deliveries', 'transactions', 'refunds', 'user_status_changes'] as $table) {
            $this->assertFalse(Schema::hasTable($table), "Legacy table remains: {$table}");
        }

        foreach (['role', 'business_name', 'store_name', 'province', 'municipality', 'barangay', 'street', 'vehicle_type', 'plate_number'] as $column) {
            $this->assertFalse(Schema::hasColumn('users', $column), "Legacy users.{$column} remains");
        }

        $this->assertFalse(Schema::hasColumn('products', 'price'));
        $this->assertFalse(Schema::hasColumn('products', 'stock'));
        $this->assertFalse(Schema::hasColumn('order_items', 'order_id'));
        $this->assertFalse(Schema::hasColumn('orders', 'address_id'));

        $primaryColumns = collect(DB::select("SHOW INDEX FROM role_user WHERE Key_name = 'PRIMARY'"))
            ->sortBy('Seq_in_index')
            ->pluck('Column_name')
            ->values()
            ->all();
        $this->assertSame(['role_id', 'user_id'], $primaryColumns);
    }

    public function test_user_roles_and_profiles_use_canonical_tables(): void
    {
        $owner = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $address = Address::create([
            'user_id' => $owner->id, 'label' => 'Pickup', 'recipient' => $owner->name,
            'phone' => '09170000000', 'line1' => '10 Main Street', 'city' => 'Manila',
            'province' => 'Metro Manila', 'is_default' => true,
        ]);
        $seller = Seller::create([
            'user_id' => $owner->id, 'name' => 'Canonical Crafts', 'slug' => 'canonical-crafts',
            'description' => 'Handmade locally.', 'pickup_address_id' => $address->id, 'status' => 'approved',
        ]);
        $owner->grant('seller');

        $this->assertTrue($owner->fresh()->hasRole('buyer'));
        $this->assertTrue($owner->fresh()->hasRole('seller'));
        $this->assertSame($seller->id, $owner->fresh()->sellers()->sole()->id);
        $this->assertSame($address->id, $seller->fresh()->pickupAddress->id);
    }

    public function test_rider_belongs_to_exactly_one_provider(): void
    {
        $providerOwner = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $provider = LogisticsProvider::create([
            'user_id' => $providerOwner->id, 'name' => 'Canonical Express',
            'slug' => 'canonical-express', 'status' => 'approved',
        ]);
        $riderUser = User::factory()->create(['role' => 'rider', 'status' => 'active']);
        $rider = Rider::create([
            'user_id' => $riderUser->id, 'logistics_provider_id' => $provider->id,
            'vehicle_type' => 'motorcycle', 'plate_no' => 'ABC-123', 'is_active' => true,
        ]);

        $this->assertSame($provider->id, $rider->provider->id);
        $this->assertSame($rider->id, $riderUser->rider->id);
    }
}
