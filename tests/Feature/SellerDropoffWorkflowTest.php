<?php

namespace Tests\Feature;

use App\Models\Buyer\Address;
use App\Models\Buyer\Order;
use App\Models\Buyer\OrderAddress;
use App\Models\Logistics\LogisticsCenter;
use App\Models\Logistics\ServiceArea;
use App\Models\Logistics\ServiceAreaLocation;
use App\Models\Seller\Category;
use App\Models\Seller\SellerOrder;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use App\Services\Fulfillment\ShipmentWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerDropoffWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_dropoff_is_confirmed_by_center_tracking_scan(): void
    {
        $this->withoutVite();
        $buyer = User::factory()->create(['account_type' => User::TYPE_BUYER]);
        $sellerUser = User::factory()->create(['account_type' => User::TYPE_SELLER]);
        $centerUser = User::factory()->create(['account_type' => User::TYPE_LOGISTICS]);
        $center = LogisticsCenter::create([
            'owner_user_id' => $centerUser->id,
            'code' => 'DROP-CENTER',
            'business_name' => 'Drop-off Test Center',
            'status' => 'ACTIVE',
        ]);
        $area = ServiceArea::create([
            'logistics_center_id' => $center->id,
            'code' => 'DROP-AREA',
            'name' => 'Drop-off Test Area',
            'is_active' => true,
        ]);
        $location = [
            'province_code' => 'DROP-P', 'province_name' => 'Test Province',
            'municipality_code' => 'DROP-M', 'municipality_name' => 'Test Municipality',
            'barangay_code' => 'DROP-B', 'barangay_name' => 'Test Barangay',
        ];
        ServiceAreaLocation::create(['service_area_id' => $area->id] + $location);
        $category = Category::create(['name' => 'Drop-off Category', 'slug' => 'dropoff-category', 'is_active' => true]);
        $sellerAddress = Address::create([
            'user_id' => $sellerUser->id,
            'label' => 'Business',
            'recipient_name' => $sellerUser->name,
            'contact_number' => $sellerUser->contact_number,
            ...$location,
            'street_address' => 'Seller Street',
        ]);
        $seller = SellerProfile::create([
            'user_id' => $sellerUser->id,
            'primary_category_id' => $category->id,
            'business_address_id' => $sellerAddress->id,
            'business_name' => 'Drop-off Test Seller',
            'status' => 'ACTIVE',
        ]);
        $order = Order::create([
            'order_number' => 'DROP-ORDER-001',
            'buyer_user_id' => $buyer->id,
            'status' => 'PLACED',
            'grand_total' => 100,
            'placed_at' => now(),
        ]);
        OrderAddress::create([
            'order_id' => $order->id,
            'recipient_name' => $buyer->name,
            'contact_number' => $buyer->contact_number,
            ...$location,
            'street_address' => 'Buyer Street',
        ]);
        $sellerOrder = SellerOrder::create([
            'seller_order_number' => 'DROP-SELLER-ORDER-001',
            'order_id' => $order->id,
            'seller_profile_id' => $seller->id,
            'status' => 'PLACED',
            'grand_total' => 100,
        ]);
        $workflow = app(ShipmentWorkflowService::class);
        $workflow->sellerTransition($sellerOrder, 'confirm', $sellerUser);
        $workflow->sellerTransition($sellerOrder, 'prepare', $sellerUser);

        $this->actingAs($sellerUser)->post(route('seller.logistics.pickup', $sellerOrder), [
            'handover_method' => 'seller_dropoff',
            'pickup_note' => 'Parcel sealed',
        ])->assertRedirect(route('seller.orders'));

        $shipment = $sellerOrder->shipment()->firstOrFail();
        $this->assertSame('READY_FOR_PICKUP', $shipment->current_status);
        $this->assertStringStartsWith('SELLER_DROPOFF:', (string) $shipment->pickupRequests()->firstOrFail()->notes);
        $this->actingAs($centerUser)->get(route('logistics.pickups'))->assertOk()->assertSee('Confirm Drop-off');

        $this->actingAs($centerUser)->post(route('logistics.parcels.receive.confirm', $shipment), [
            'scanned_code' => 'WRONG-CODE',
            'scan_method' => 'MANUAL',
        ])->assertSessionHasErrors('scanned_code');
        $this->assertSame('READY_FOR_PICKUP', $shipment->fresh()->current_status);

        $this->post(route('logistics.parcels.receive.confirm', $shipment), [
            'scanned_code' => $shipment->tracking_number,
            'scan_method' => 'BARCODE',
        ])->assertRedirect();

        $this->assertSame('AT_SORTING_CENTER', $shipment->fresh()->current_status);
        $this->assertSame('PICKED_UP', $sellerOrder->fresh()->status);
        $this->assertDatabaseHas('pickup_requests', ['shipment_id' => $shipment->id, 'status' => 'FULFILLED']);
        $this->assertDatabaseHas('parcel_scans', [
            'shipment_id' => $shipment->id,
            'scan_type' => 'CENTER_RECEIVE',
            'scanned_code' => $shipment->tracking_number,
            'result' => 'SUCCESS',
        ]);
    }
}
