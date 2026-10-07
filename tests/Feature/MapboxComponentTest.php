<?php

namespace Tests\Feature;

use App\Models\Buyer\Order;
use App\Models\Buyer\OrderAddress;
use App\Models\Logistics\Shipment;
use App\Models\Rider\RiderAssignment;
use App\Models\Rider\RiderProfile;
use App\Models\Seller\SellerOrder;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use App\Services\Maps\MapDataService;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class MapboxComponentTest extends TestCase
{
    public function test_shared_map_component_renders_safe_controls_and_marker_data(): void
    {
        config(['mapbox.public_token' => 'pk.test-token']);

        $html = Blade::render('<x-shared.mapbox :markers="$markers" />', [
            'markers' => [[
                'id' => 'destination-1',
                'kind' => 'destination',
                'title' => 'Delivery destination',
                'latitude' => 14.5995,
                'longitude' => 120.9842,
                'popup' => 'Manila',
            ]],
        ]);

        $this->assertStringContainsString('data-likhae-map', $html);
        $this->assertStringContainsString('Use my location', $html);
        $this->assertStringContainsString('Open full map', $html);
        $this->assertStringContainsString('pk.test-token', $html);
        $this->assertStringContainsString('destination-1', $html);
    }

    public function test_map_data_service_uses_order_destination_coordinates_without_customer_details(): void
    {
        $address = new OrderAddress([
            'barangay_name' => 'Barangay Test',
            'municipality_name' => 'Test City',
            'province_name' => 'Test Province',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'recipient_name' => 'Private Customer',
        ]);
        $order = new Order();
        $order->setRelation('address', $address);
        $sellerOrder = new SellerOrder();
        $sellerOrder->setRelation('order', $order);
        $shipment = new Shipment();
        $shipment->setRelation('sellerOrder', $sellerOrder);
        $shipment->setRelation('riderAssignments', collect());
        $shipment->setRelation('logisticsCenter', null);

        $markers = app(MapDataService::class)->forShipment($shipment);

        $this->assertCount(1, $markers);
        $this->assertSame(14.5995, $markers[0]['latitude']);
        $this->assertSame(120.9842, $markers[0]['longitude']);
        $this->assertArrayNotHasKey('recipient_name', $markers[0]);
        $this->assertStringNotContainsString('Private Customer', json_encode($markers));
    }

    public function test_rider_map_exposes_location_update_endpoint_before_first_gps_coordinate(): void
    {
        $riderUser = new User(['account_type' => User::TYPE_RIDER]);
        $riderUser->id = 701;
        $rider = new RiderProfile(['user_id' => $riderUser->id]);
        $rider->id = 801;
        $rider->setRelation('user', $riderUser);

        $assignment = new RiderAssignment([
            'shipment_id' => 901,
            'rider_profile_id' => $rider->id,
            'assignment_type' => RiderAssignment::TYPE_PICKUP,
            'status' => 'ACCEPTED',
        ]);
        $assignment->id = 1001;
        $assignment->setRelation('riderProfile', $rider);
        $assignment->setRelation('liveLocation', null);

        $address = new OrderAddress([
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'barangay_name' => 'Barangay Test',
            'municipality_name' => 'Test City',
            'province_name' => 'Test Province',
        ]);
        $order = new Order();
        $order->setRelation('address', $address);
        $sellerOrder = new SellerOrder();
        $sellerOrder->setRelation('order', $order);
        $sellerOrder->setRelation('sellerProfile', null);

        $shipment = new Shipment([
            'tracking_number' => 'TRACK-901',
            'current_status' => 'READY_FOR_PICKUP',
        ]);
        $shipment->id = 901;
        $shipment->setRelation('sellerOrder', $sellerOrder);
        $shipment->setRelation('riderAssignments', collect([$assignment]));
        $shipment->setRelation('logisticsCenter', null);
        $shipment->setRelation('serviceArea', null);

        $this->actingAs($riderUser);
        $markers = app(MapDataService::class)->forShipment($shipment);
        $marker = collect($markers)->firstWhere('id', 'rider-assignment-1001');

        $this->assertNotNull($marker);
        $this->assertArrayNotHasKey('latitude', $marker);
        $this->assertSame(route('rider.assignments.location', ['assignment' => 1001]), $marker['location_update_endpoint']);
    }

    public function test_accepted_delivery_map_routes_to_the_saved_order_destination(): void
    {
        $riderUser = new User(['account_type' => User::TYPE_RIDER]);
        $riderUser->id = 702;
        $rider = new RiderProfile(['user_id' => $riderUser->id]);
        $rider->id = 802;
        $rider->setRelation('user', $riderUser);

        $assignment = new RiderAssignment([
            'shipment_id' => 902,
            'rider_profile_id' => $rider->id,
            'assignment_type' => RiderAssignment::TYPE_DELIVERY,
            'status' => 'ACCEPTED',
        ]);
        $assignment->id = 1002;
        $assignment->setRelation('riderProfile', $rider);
        $assignment->setRelation('liveLocation', null);

        $buyerAddress = new OrderAddress([
            'latitude' => 14.6101,
            'longitude' => 120.9901,
            'barangay_name' => 'Buyer Barangay',
            'municipality_name' => 'Buyer City',
            'province_name' => 'Buyer Province',
        ]);
        $order = new Order();
        $order->setRelation('address', $buyerAddress);
        $seller = new SellerProfile();
        $seller->setRelation('businessAddress', new OrderAddress([
            'latitude' => 14.5801,
            'longitude' => 120.9701,
        ]));
        $sellerOrder = new SellerOrder();
        $sellerOrder->setRelation('order', $order);
        $sellerOrder->setRelation('sellerProfile', $seller);

        $shipment = new Shipment([
            'tracking_number' => 'TRACK-902',
            'current_status' => 'ASSIGNED_TO_RIDER',
        ]);
        $shipment->id = 902;
        $shipment->setRelation('sellerOrder', $sellerOrder);
        $shipment->setRelation('riderAssignments', collect([$assignment]));
        $shipment->setRelation('logisticsCenter', null);
        $shipment->setRelation('serviceArea', null);

        $this->actingAs($riderUser);
        $service = app(MapDataService::class);
        $markers = $service->forShipment($shipment, true, true);
        $tracking = $service->trackingState($shipment, $riderUser);
        $riderMarker = collect($markers)->firstWhere('id', 'rider-assignment-1002');

        $this->assertSame('buyer', $tracking['active_destination_kind']);
        $this->assertSame(1002, $tracking['active_assignment_id']);
        $this->assertSame(14.6101, $tracking['destination']['latitude']);
        $this->assertSame(120.9901, $tracking['destination']['longitude']);
        $this->assertNotNull($riderMarker);
        $this->assertSame('buyer', $riderMarker['active_destination_kind']);
        $this->assertSame(route('rider.assignments.location', ['assignment' => 1002]), $riderMarker['location_update_endpoint']);
        $this->assertSame(14.6101, collect($markers)->firstWhere('target_kind', 'buyer')['latitude']);
    }
}
