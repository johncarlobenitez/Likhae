<?php

namespace Tests\Feature;

use App\Models\LogisticsProvider;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_logistics_navigation_routes_resolve_to_real_pages(): void
    {
        $user = User::factory()->create(['role' => 'logistics', 'status' => 'active', 'email_verified_at' => now()]);
        LogisticsProvider::create([
            'user_id' => $user->id,
            'name' => 'Swift Logistics',
            'slug' => 'swift-logistics',
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->get(route('logistics.dashboard'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('logistics.riders'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('logistics.pickups'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('logistics.messages'))
            ->assertOk();
    }

    public function test_logistics_operations_routes_render_distinct_pages(): void
    {
        $user = User::factory()->create(['role' => 'logistics', 'status' => 'active', 'email_verified_at' => now()]);
        LogisticsProvider::create([
            'user_id' => $user->id,
            'name' => 'Swift Logistics',
            'slug' => 'swift-logistics-distinct',
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->get(route('logistics.dispatch'))
            ->assertOk()
            ->assertSeeText('Shipment board');

        $this->actingAs($user)
            ->get(route('logistics.parcels'))
            ->assertOk()
            ->assertSeeText('All Parcels');

        $this->actingAs($user)
            ->get(route('logistics.pickups'))
            ->assertOk()
            ->assertSeeText('Seller Pickup Requests');

        $this->actingAs($user)
            ->get(route('logistics.parcels.receive'))
            ->assertOk()
            ->assertSeeText('Receive a parcel');

        $this->actingAs($user)
            ->get(route('logistics.sorting'))
            ->assertOk()
            ->assertSeeText('Sort parcels by destination.');

        $this->actingAs($user)
            ->get(route('logistics.parcels.tracking'))
            ->assertOk()
            ->assertSeeText('Track Parcel');
    }

    public function test_rider_navigation_routes_resolve_to_real_pages(): void
    {
        $providerUser = User::factory()->create(['role' => 'logistics', 'status' => 'active', 'email_verified_at' => now()]);
        $provider = LogisticsProvider::create([
            'user_id' => $providerUser->id,
            'name' => 'Rider Fleet',
            'slug' => 'rider-fleet',
            'status' => 'approved',
        ]);

        $riderUser = User::factory()->create(['role' => 'rider', 'status' => 'active', 'email_verified_at' => now()]);
        Rider::create([
            'user_id' => $riderUser->id,
            'logistics_provider_id' => $provider->id,
            'vehicle_type' => 'motorcycle',
            'plate_no' => 'ABC-123',
            'is_active' => true,
        ]);

        $this->actingAs($riderUser)
            ->get(route('rider.dashboard'))
            ->assertOk();

        $this->actingAs($riderUser)
            ->get(route('rider.shipments'))
            ->assertOk();

        $this->actingAs($riderUser)
            ->get(route('rider.pickups'))
            ->assertOk();

        $this->actingAs($riderUser)
            ->get(route('rider.deliveries'))
            ->assertOk();

        $this->actingAs($riderUser)
            ->get(route('rider.history'))
            ->assertOk();
    }

    public function test_rider_shipments_page_has_a_visible_empty_state(): void
    {
        $providerUser = User::factory()->create(['role' => 'logistics', 'status' => 'active', 'email_verified_at' => now()]);
        $provider = LogisticsProvider::create([
            'user_id' => $providerUser->id,
            'name' => 'Rider Fleet',
            'slug' => 'rider-fleet-empty',
            'status' => 'approved',
        ]);

        $riderUser = User::factory()->create(['role' => 'rider', 'status' => 'active', 'email_verified_at' => now()]);
        Rider::create([
            'user_id' => $riderUser->id,
            'logistics_provider_id' => $provider->id,
            'vehicle_type' => 'motorcycle',
            'plate_no' => 'ABC-456',
            'is_active' => true,
        ]);

        $this->actingAs($riderUser)
            ->get(route('rider.shipments'))
            ->assertOk()
            ->assertSeeText('Assigned Parcels')
            ->assertSeeText('No assigned parcels yet')
            ->assertSeeText('View pickup queue');
    }

    public function test_admin_compliance_route_loads_successfully(): void
    {
        $adminUser = User::factory()->create(['role' => 'admin', 'status' => 'active', 'email_verified_at' => now()]);

        $this->actingAs($adminUser)
            ->get(route('admin.compliance'))
            ->assertOk();
    }

    public function test_admin_messages_route_loads_successfully(): void
    {
        $adminUser = User::factory()->create(['role' => 'admin', 'status' => 'active', 'email_verified_at' => now()]);
        $buyerUser = User::factory()->create(['role' => 'buyer', 'status' => 'active', 'email_verified_at' => now()]);

        $this->actingAs($adminUser)
            ->get(route('admin.messages', ['partner' => $buyerUser->id]))
            ->assertOk();
    }
}
