<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\ParcelAssignment;
use App\Models\User;
use App\Support\BuyerMarketplace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParcelWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_logistics_assigns_pickup_and_only_assigned_rider_can_accept(): void
    {
        [$delivery, $logistics] = $this->parcel('awaiting_pickup_assignment');
        $assigned = User::factory()->create(['role' => 'rider', 'status' => 'active']);
        $other = User::factory()->create(['role' => 'rider', 'status' => 'active']);

        $this->actingAs($logistics)->post(route('logistics.pickups.assign', $delivery), ['rider_id' => $assigned->id])->assertRedirect();

        $this->assertDatabaseHas('parcel_assignments', ['delivery_id' => $delivery->id, 'rider_id' => $assigned->id, 'assignment_type' => 'seller_pickup']);
        $this->actingAs($assigned)->get(route('rider.pickups.show', $delivery))->assertOk()->assertSee('Accept Pickup');
        $this->actingAs($other)->post(route('rider.pickups.accept', $delivery))->assertNotFound();
        $this->actingAs($assigned)->post(route('rider.pickups.accept', $delivery))->assertRedirect();
        $this->assertSame('pickup_accepted', $delivery->fresh()->status);

        $this->actingAs($assigned)->post(route('rider.pickups.confirm', $delivery), ['tracking' => $delivery->tracking_number])->assertRedirect();
        $this->actingAs($assigned)->post(route('rider.pickups.confirm', $delivery), ['tracking' => $delivery->tracking_number])->assertRedirect()->assertSessionHas('status', 'Parcel pickup was already confirmed.');
        $this->assertSame('picked_up', $delivery->fresh()->status);
        $this->assertDatabaseCount('parcel_scan_events', 1);
    }

    public function test_pickup_rider_cannot_receive_parcel_into_sorting_center(): void
    {
        [$delivery] = $this->parcel('picked_up');
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'active']);
        $delivery->update(['pickup_rider_id' => $rider->id]);
        ParcelAssignment::create(['delivery_id' => $delivery->id, 'rider_id' => $rider->id, 'assignment_type' => 'seller_pickup', 'status' => 'picked_up', 'assigned_at' => now()]);

        $this->actingAs($rider)->post(route('rider.pickups.sorting-center', $delivery))->assertRedirect();

        $this->assertSame('picked_up', $delivery->fresh()->status);
        $this->assertNull($delivery->fresh()->received_at);
    }

    public function test_logistics_receiving_records_scan_and_status_history(): void
    {
        [$delivery, $logistics] = $this->parcel('awaiting_dropoff');
        $delivery->update(['handover_method' => 'seller_dropoff']);

        $this->actingAs($logistics)->post(route('logistics.parcels.receive.confirm', $delivery), ['tracking' => $delivery->tracking_number])->assertRedirect();

        $this->assertSame('at_sorting_center', $delivery->fresh()->status);
        $this->assertDatabaseHas('parcel_scan_events', ['delivery_id' => $delivery->id, 'scan_type' => 'sorting_center_received', 'scanned_by_user_id' => $logistics->id]);
        $this->assertDatabaseHas('parcel_status_histories', ['delivery_id' => $delivery->id, 'new_status' => 'at_sorting_center', 'performed_by' => $logistics->id]);

        $this->actingAs($logistics)->post(route('logistics.parcels.receive.confirm', $delivery), ['tracking' => $delivery->tracking_number])->assertRedirect()->assertSessionHas('status', 'This parcel has already been received.');
        $this->assertDatabaseCount('parcel_scan_events', 1);
    }

    public function test_sorting_and_final_release_require_matching_scans_and_separate_assignment(): void
    {
        [$delivery, $logistics] = $this->parcel('at_sorting_center');
        $pickupRider = User::factory()->create(['role' => 'rider', 'status' => 'active']);
        $finalRider = User::factory()->create(['role' => 'rider', 'status' => 'active']);
        ParcelAssignment::create(['delivery_id' => $delivery->id, 'rider_id' => $pickupRider->id, 'assignment_type' => 'seller_pickup', 'status' => 'completed', 'assigned_at' => now(), 'completed_at' => now()]);

        $this->actingAs($logistics)->post(route('logistics.sorting.sort', $delivery), ['tracking' => 'WRONG'])->assertStatus(422);
        $this->actingAs($logistics)->post(route('logistics.sorting.sort', $delivery), ['tracking' => $delivery->tracking_number])->assertRedirect();
        $this->assertSame('sorted', $delivery->fresh()->status);
        $this->assertDatabaseHas('parcel_scan_events', ['delivery_id' => $delivery->id, 'scan_type' => 'sorting_scan']);

        $this->actingAs($logistics)->post(route('logistics.assignments.assign-rider', $delivery), ['rider_id' => $finalRider->id])->assertRedirect();
        $this->assertDatabaseHas('parcel_assignments', ['delivery_id' => $delivery->id, 'rider_id' => $pickupRider->id, 'assignment_type' => 'seller_pickup']);
        $this->assertDatabaseHas('parcel_assignments', ['delivery_id' => $delivery->id, 'rider_id' => $finalRider->id, 'assignment_type' => 'final_delivery']);

        $this->actingAs($logistics)->post(route('logistics.assignments.release', $delivery), ['tracking' => $delivery->tracking_number])->assertRedirect()->assertSessionHasErrors('release');
        $this->actingAs($finalRider)->post(route('rider.deliveries.accept', $delivery))->assertRedirect();
        $this->actingAs($finalRider)->post(route('rider.deliveries.pickup-sorting', $delivery), ['tracking' => $delivery->tracking_number])->assertStatus(422);
        $this->actingAs($logistics)->post(route('logistics.assignments.release', $delivery), ['tracking' => $delivery->tracking_number])->assertRedirect();
        $this->actingAs($logistics)->post(route('logistics.assignments.release', $delivery), ['tracking' => $delivery->tracking_number])->assertRedirect()->assertSessionHas('status', 'This parcel was already authorized for release.');
        $this->actingAs($finalRider)->post(route('rider.deliveries.pickup-sorting', $delivery), ['tracking' => $delivery->tracking_number])->assertRedirect();

        $this->assertSame('out_for_delivery', $delivery->fresh()->status);
        $this->assertDatabaseHas('parcel_scan_events', ['delivery_id' => $delivery->id, 'scan_type' => 'delivery_release_scan', 'scanned_by_user_id' => $logistics->id]);
        $this->assertDatabaseHas('parcel_scan_events', ['delivery_id' => $delivery->id, 'scan_type' => 'final_rider_scan', 'scanned_by_user_id' => $finalRider->id]);
    }

    public function test_legacy_shipping_order_can_choose_handover_while_parcel_is_still_requested(): void
    {
        [$delivery] = $this->parcel('requested');
        $seller = $delivery->order->seller;
        $delivery->order->update(['status' => 'shipping']);

        $this->actingAs($seller)->post(route('seller.logistics.pickup', $delivery->order), [
            'handover_method' => 'logistics_pickup',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_window' => '10:00 AM - 12:00 PM',
            'pickup_note' => 'Package is sealed and the waybill is attached.',
        ])->assertRedirect();

        $this->assertSame('awaiting_pickup_assignment', $delivery->fresh()->status);
        $this->assertSame('logistics_pickup', $delivery->fresh()->handover_method);
    }

    public function test_buyer_confirmation_completes_order_parcel_and_status_history(): void
    {
        [$delivery] = $this->parcel('delivered');
        $delivery->update(['delivered_at' => now()]);
        $buyer = $delivery->order->buyer;

        $this->actingAs($buyer)->post(route('buyer.orders.received', $delivery->order->order_number))
            ->assertRedirect(route('buyer.orders.review', $delivery->order->order_number))
            ->assertSessionHas('buyer_notice', 'Order received and completed. You can now review the product.');

        $order = $delivery->order->fresh()->load('delivery');
        $this->assertSame('completed', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('completed', $order->delivery->status);
        $this->assertSame('completed', BuyerMarketplace::order($order)['status']);
        $this->assertDatabaseHas('parcel_status_histories', ['delivery_id' => $delivery->id, 'new_status' => 'completed', 'performed_by' => $buyer->id]);

        $this->actingAs($buyer)->post(route('buyer.orders.received', $order->order_number))->assertRedirect()->assertSessionHas('buyer_notice', 'This order was already completed.');
    }

    private function parcel(string $status): array
    {
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'active']);
        $logistics = User::factory()->create(['role' => 'logistics', 'status' => 'active']);
        $order = Order::create(['order_number' => 'LKH-'.uniqid(), 'buyer_id' => $buyer->id, 'seller_id' => $seller->id, 'total_amount' => 100, 'payment_method' => 'cod', 'status' => 'ready_for_pickup']);
        $delivery = Delivery::create(['order_id' => $order->id, 'tracking_number' => 'LKH-TEST-'.uniqid(), 'logistics_center_id' => $logistics->id, 'address' => 'Buyer address', 'seller_address' => 'Seller address', 'provider' => 'LIKHAE Logistics', 'status' => $status]);

        return [$delivery, $logistics];
    }
}
