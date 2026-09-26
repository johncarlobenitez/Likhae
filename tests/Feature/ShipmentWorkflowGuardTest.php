<?php

namespace Tests\Feature;

use App\Models\Buyer\Order;
use App\Models\Logistics\LogisticsCenter;
use App\Models\Logistics\Shipment;
use App\Models\Rider\RiderAssignment;
use App\Models\Rider\RiderProfile;
use App\Models\Seller\Category;
use App\Models\Seller\SellerOrder;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use App\Services\Fulfillment\ShipmentWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ShipmentWorkflowGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_pickup_cannot_complete_without_the_matching_waybill_code(): void
    {
        [$user, $shipment, $assignment] = $this->pickupFixture();

        try {
            app(ShipmentWorkflowService::class)->riderTransition($assignment, 'pickup_complete', $user, []);
            $this->fail('Pickup completion should require the actual scanned waybill code.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('shipments', ['id' => $shipment->id, 'current_status' => 'READY_FOR_PICKUP']);
            $this->assertDatabaseMissing('parcel_scans', ['shipment_id' => $shipment->id]);
        }

        try {
            app(ShipmentWorkflowService::class)->riderTransition($assignment, 'pickup_complete', $user, ['scanned_code' => 'WRONG-CODE']);
            $this->fail('Pickup completion should reject an incorrect waybill code.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('shipments', ['id' => $shipment->id, 'current_status' => 'READY_FOR_PICKUP']);
            $this->assertDatabaseMissing('parcel_scans', ['shipment_id' => $shipment->id]);
        }
    }

    public function test_started_assignment_cannot_be_rejected(): void
    {
        [$user, $shipment, $assignment] = $this->pickupFixture('IN_PROGRESS');

        $this->expectException(\RuntimeException::class);
        app(ShipmentWorkflowService::class)->riderTransition($assignment, 'reject', $user, ['reason' => 'too late']);
    }

    public function test_center_receipt_requires_tracking_code_and_correct_center(): void
    {
        $centerUser = User::factory()->create(['account_type' => User::TYPE_LOGISTICS]);
        $center = LogisticsCenter::create([
            'owner_user_id' => $centerUser->id,
            'code' => 'CENTER-RECEIVE-TEST',
            'business_name' => 'Receive Test Center',
            'status' => 'ACTIVE',
        ]);
        $shipment = Shipment::create([
            'seller_order_id' => $this->sellerOrder()->id,
            'tracking_number' => 'TRACK-RECEIVE-TEST',
            'logistics_center_id' => $center->id,
            'destination_province_code' => 'P1',
            'destination_province_name' => 'Province',
            'destination_municipality_code' => 'M1',
            'destination_municipality_name' => 'Municipality',
            'destination_barangay_code' => 'B1',
            'destination_barangay_name' => 'Barangay',
            'current_status' => 'PICKED_UP',
        ]);

        try {
            app(ShipmentWorkflowService::class)->receiveAtCenter($shipment, $center, $centerUser, '');
            $this->fail('Center receipt should require a matching scan.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('shipments', ['id' => $shipment->id, 'current_status' => 'PICKED_UP']);
        }
    }

    private function pickupFixture(string $assignmentStatus = 'IN_PROGRESS'): array
    {
        $centerOwner = User::factory()->create(['account_type' => User::TYPE_LOGISTICS]);
        $center = LogisticsCenter::create([
            'owner_user_id' => $centerOwner->id,
            'code' => 'CENTER-PICKUP-'.uniqid(),
            'business_name' => 'Pickup Test Center',
            'status' => 'ACTIVE',
        ]);
        $riderUser = User::factory()->create(['account_type' => User::TYPE_RIDER]);
        $rider = RiderProfile::create([
            'user_id' => $riderUser->id,
            'logistics_center_id' => $center->id,
            'vehicle_type' => 'motorcycle',
            'plate_number' => 'PICKUP-'.uniqid(),
            'drivers_license_number' => 'LICENSE-'.uniqid(),
            'status' => 'ACTIVE',
        ]);
        $sellerOrder = $this->sellerOrder();
        $shipment = Shipment::create([
            'seller_order_id' => $sellerOrder->id,
            'tracking_number' => 'TRACK-PICKUP-'.uniqid(),
            'logistics_center_id' => $center->id,
            'destination_province_code' => 'P1',
            'destination_province_name' => 'Province',
            'destination_municipality_code' => 'M1',
            'destination_municipality_name' => 'Municipality',
            'destination_barangay_code' => 'B1',
            'destination_barangay_name' => 'Barangay',
            'current_status' => 'READY_FOR_PICKUP',
        ]);
        $assignment = RiderAssignment::create([
            'shipment_id' => $shipment->id,
            'rider_profile_id' => $rider->id,
            'assignment_type' => 'PICKUP',
            'status' => $assignmentStatus,
            'assigned_at' => now(),
            'started_at' => now(),
        ]);

        return [$riderUser, $shipment, $assignment];
    }

    private function sellerOrder(): SellerOrder
    {
        $buyer = User::factory()->create(['account_type' => User::TYPE_BUYER]);
        $sellerUser = User::factory()->create(['account_type' => User::TYPE_SELLER]);
        $category = Category::create([
            'name' => 'Test category',
            'slug' => 'test-category-'.uniqid(),
            'is_active' => true,
        ]);
        $seller = SellerProfile::create([
            'user_id' => $sellerUser->id,
            'primary_category_id' => $category->id,
            'business_name' => 'Test seller',
            'status' => 'ACTIVE',
        ]);
        $order = Order::create([
            'order_number' => 'ORDER-'.uniqid(),
            'buyer_user_id' => $buyer->id,
            'status' => 'PLACED',
        ]);

        return SellerOrder::create([
            'seller_order_number' => 'SELLER-ORDER-'.uniqid(),
            'order_id' => $order->id,
            'seller_profile_id' => $seller->id,
            'status' => 'READY_FOR_PICKUP',
        ]);
    }
}
