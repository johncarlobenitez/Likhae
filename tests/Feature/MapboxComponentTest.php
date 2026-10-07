<?php

namespace Tests\Feature;

use App\Models\Buyer\Order;
use App\Models\Buyer\OrderAddress;
use App\Models\Logistics\Shipment;
use App\Models\Seller\SellerOrder;
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
}
