<?php

namespace App\Services\Fulfillment;

use App\Models\Buyer\Order;
use App\Models\Logistics\DeliveryAttempt;
use App\Models\Logistics\LogisticsCenter;
use App\Models\Logistics\ParcelScan;
use App\Models\Logistics\PickupRequest;
use App\Models\Logistics\ServiceArea;
use App\Models\Logistics\ServiceAreaLocation;
use App\Models\Logistics\Shipment;
use App\Models\Logistics\ShipmentEvent;
use App\Models\Logistics\Waybill;
use App\Models\Rider\RiderAssignment;
use App\Models\Rider\RiderEarning;
use App\Models\Rider\RiderProfile;
use App\Models\Seller\SellerOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ShipmentWorkflowService
{
    public function sellerTransition(SellerOrder $sellerOrder, string $action, User $actor): SellerOrder
    {
        return DB::transaction(function () use ($sellerOrder, $action, $actor): SellerOrder {
            $sellerOrder->loadMissing('shipment', 'order.address');
            $shipment = $this->ensureShipment($sellerOrder);

            match ($action) {
                'confirm' => $this->confirmSellerOrder($sellerOrder, $shipment, $actor),
                'prepare' => $this->prepareSellerOrder($sellerOrder, $shipment, $actor),
                'ready' => $this->readyForPickup($sellerOrder, $shipment, $actor),
                'cancel' => $this->cancelSellerOrder($sellerOrder, $shipment, $actor),
                default => throw new RuntimeException('Unsupported seller order action.'),
            };

            return $sellerOrder->refresh();
        });
    }

    public function ensureShipment(SellerOrder $sellerOrder): Shipment
    {
        $sellerOrder->loadMissing('shipment', 'order.address');

        if ($sellerOrder->shipment) {
            return $sellerOrder->shipment;
        }

        $address = $sellerOrder->order?->address;

        if (! $address) {
            throw new RuntimeException('Order delivery address is missing.');
        }

        $serviceArea = $this->findServiceAreaForAddress(
            (string) $address->province_code,
            (string) $address->municipality_code,
            (string) $address->barangay_code,
        );

        $shipment = Shipment::create([
            'seller_order_id' => $sellerOrder->id,
            'tracking_number' => $this->newTrackingNumber(),
            'logistics_center_id' => $serviceArea?->logistics_center_id,
            'service_area_id' => $serviceArea?->id,
            'destination_province_code' => $address->province_code,
            'destination_province_name' => $address->province_name,
            'destination_municipality_code' => $address->municipality_code,
            'destination_municipality_name' => $address->municipality_name,
            'destination_barangay_code' => $address->barangay_code,
            'destination_barangay_name' => $address->barangay_name,
            'current_status' => 'PLACED',
        ]);

        $this->recordEvent($shipment, 'PLACED', null, 'Shipment created from seller order.');

        return $shipment;
    }

    public function approvePickup(PickupRequest $pickupRequest, User $reviewer, ?string $notes = null): PickupRequest
    {
        return DB::transaction(function () use ($pickupRequest, $reviewer, $notes): PickupRequest {
            $pickupRequest->update([
                'status' => 'APPROVED',
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_at' => now(),
                'notes' => $notes ?: $pickupRequest->notes,
            ]);

            $shipment = $pickupRequest->shipment;
            $this->recordEvent($shipment, $shipment->current_status, $reviewer, 'Pickup request approved.');

            return $pickupRequest->refresh();
        });
    }

    public function rejectPickup(PickupRequest $pickupRequest, User $reviewer, string $reason): PickupRequest
    {
        return DB::transaction(function () use ($pickupRequest, $reviewer, $reason): PickupRequest {
            $pickupRequest->update([
                'status' => 'REJECTED',
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $this->recordEvent($pickupRequest->shipment, $pickupRequest->shipment->current_status, $reviewer, 'Pickup request rejected: '.$reason);

            return $pickupRequest->refresh();
        });
    }

    public function assignRider(Shipment $shipment, RiderProfile $rider, string $assignmentType, User $assigner): RiderAssignment
    {
        return DB::transaction(function () use ($shipment, $rider, $assignmentType, $assigner): RiderAssignment {
            $assignmentType = strtoupper($assignmentType);

            if (! in_array($assignmentType, ['PICKUP', 'DELIVERY'], true)) {
                throw new RuntimeException('Assignment type must be PICKUP or DELIVERY.');
            }

            if ($rider->status !== 'ACTIVE') {
                throw new RuntimeException('Only active riders can receive assignments.');
            }

            $shipment->riderAssignments()
                ->where('assignment_type', $assignmentType)
                ->whereIn('status', ['ASSIGNED', 'ACCEPTED', 'IN_PROGRESS'])
                ->update([
                    'status' => 'CANCELLED',
                    'cancelled_at' => now(),
                    'rejection_reason' => 'Replaced by a newer assignment.',
                ]);

            $assignment = RiderAssignment::create([
                'shipment_id' => $shipment->id,
                'rider_profile_id' => $rider->id,
                'assignment_type' => $assignmentType,
                'status' => 'ASSIGNED',
                'assigned_by_user_id' => $assigner->id,
                'assigned_at' => now(),
            ]);

            if ($assignmentType === 'DELIVERY') {
                $shipment->update(['current_status' => 'ASSIGNED_TO_RIDER']);
                $this->recordEvent($shipment, 'ASSIGNED_TO_RIDER', $assigner, 'Delivery rider assigned.', $assignment);
            } else {
                $this->recordEvent($shipment, $shipment->current_status, $assigner, 'Pickup rider assigned.', $assignment);
            }

            return $assignment->refresh();
        });
    }

    public function receiveAtCenter(Shipment $shipment, LogisticsCenter $center, User $actor, string $code, string $method = 'MANUAL'): Shipment
    {
        return DB::transaction(function () use ($shipment, $center, $actor, $code, $method): Shipment {
            $this->recordScan($shipment, $actor, 'CENTER_RECEIVE', $method, $code, 'SUCCESS', $center);
            $shipment->update([
                'logistics_center_id' => $center->id,
                'current_status' => 'AT_SORTING_CENTER',
            ]);
            $this->recordEvent($shipment, 'AT_SORTING_CENTER', $actor, 'Parcel received at logistics center.', null, $center);

            $shipment->sellerOrder?->update(['status' => 'PICKED_UP']);

            return $shipment->refresh();
        });
    }

    public function sortShipment(Shipment $shipment, LogisticsCenter $center, User $actor, ?ServiceArea $serviceArea = null): Shipment
    {
        return DB::transaction(function () use ($shipment, $center, $actor, $serviceArea): Shipment {
            $area = $serviceArea ?: $this->findServiceAreaForAddress(
                (string) $shipment->destination_province_code,
                (string) $shipment->destination_municipality_code,
                (string) $shipment->destination_barangay_code,
                $center,
            );

            $shipment->update([
                'logistics_center_id' => $center->id,
                'service_area_id' => $area?->id,
                'current_status' => 'SORTED',
            ]);

            $this->recordEvent($shipment, 'SORTED', $actor, $area ? 'Parcel sorted to '.$area->name.'.' : 'Parcel sorted, but no service area matched.', null, $center);

            return $shipment->refresh();
        });
    }

    public function riderTransition(RiderAssignment $assignment, string $action, User $actor, array $payload = []): RiderAssignment
    {
        return DB::transaction(function () use ($assignment, $action, $actor, $payload): RiderAssignment {
            $assignment->loadMissing('shipment');
            $shipment = $assignment->shipment;

            match ($action) {
                'accept' => $this->acceptAssignment($assignment, $shipment, $actor),
                'start' => $this->startAssignment($assignment, $shipment, $actor),
                'pickup_complete' => $this->completePickup($assignment, $shipment, $actor, $payload),
                'delivery_success' => $this->completeDelivery($assignment, $shipment, $actor, $payload),
                'delivery_failed' => $this->failDelivery($assignment, $shipment, $actor, $payload),
                'reject' => $this->rejectAssignment($assignment, $shipment, $actor, (string) ($payload['reason'] ?? 'Rejected by rider.')),
                default => throw new RuntimeException('Unsupported rider assignment action.'),
            };

            return $assignment->refresh();
        });
    }

    public function recordScan(
        Shipment $shipment,
        ?User $actor,
        string $scanType,
        string $method,
        string $code,
        string $result = 'SUCCESS',
        ?LogisticsCenter $center = null,
        ?RiderAssignment $assignment = null,
        ?string $notes = null,
    ): ParcelScan {
        return ParcelScan::create([
            'shipment_id' => $shipment->id,
            'scanned_by_user_id' => $actor?->id,
            'logistics_center_id' => $center?->id,
            'rider_assignment_id' => $assignment?->id,
            'scan_type' => strtoupper($scanType),
            'scan_method' => strtoupper($method),
            'scanned_code' => $code,
            'result' => strtoupper($result),
            'notes' => $notes,
            'scanned_at' => now(),
        ]);
    }

    public function recordEvent(
        Shipment $shipment,
        string $status,
        ?User $actor = null,
        ?string $notes = null,
        ?RiderAssignment $assignment = null,
        ?LogisticsCenter $center = null,
    ): ShipmentEvent {
        return ShipmentEvent::create([
            'shipment_id' => $shipment->id,
            'status' => strtoupper($status),
            'actor_user_id' => $actor?->id,
            'logistics_center_id' => $center?->id ?: $shipment->logistics_center_id,
            'rider_assignment_id' => $assignment?->id,
            'notes' => $notes,
            'occurred_at' => now(),
        ]);
    }

    public function findServiceAreaForAddress(string $provinceCode, string $municipalityCode, string $barangayCode, ?LogisticsCenter $center = null): ?ServiceArea
    {
        $query = ServiceAreaLocation::query()
            ->where('province_code', $provinceCode)
            ->where('municipality_code', $municipalityCode)
            ->where('barangay_code', $barangayCode)
            ->whereHas('serviceArea', function ($areaQuery) use ($center): void {
                $areaQuery->where('is_active', true);

                if ($center) {
                    $areaQuery->where('logistics_center_id', $center->id);
                }
            })
            ->with('serviceArea');

        return $query->first()?->serviceArea;
    }

    private function confirmSellerOrder(SellerOrder $sellerOrder, Shipment $shipment, User $actor): void
    {
        $sellerOrder->update(['status' => 'CONFIRMED']);
        $shipment->update(['current_status' => 'CONFIRMED']);
        $this->recordEvent($shipment, 'CONFIRMED', $actor, 'Seller confirmed the order.');
    }

    private function prepareSellerOrder(SellerOrder $sellerOrder, Shipment $shipment, User $actor): void
    {
        $sellerOrder->update(['status' => 'PREPARING']);
        $shipment->update(['current_status' => 'PREPARING']);
        $this->recordEvent($shipment, 'PREPARING', $actor, 'Seller is preparing the parcel.');
    }

    private function readyForPickup(SellerOrder $sellerOrder, Shipment $shipment, User $actor): void
    {
        $sellerOrder->update(['status' => 'READY_FOR_PICKUP']);
        $shipment->update(['current_status' => 'READY_FOR_PICKUP']);

        PickupRequest::firstOrCreate(
            ['shipment_id' => $shipment->id, 'status' => 'PENDING'],
            [
                'requested_by_user_id' => $actor->id,
                'requested_at' => now(),
                'notes' => 'Seller marked parcel ready for pickup.',
            ],
        );

        $this->generateWaybill($shipment, $actor);
        $this->recordEvent($shipment, 'READY_FOR_PICKUP', $actor, 'Seller marked parcel ready for pickup.');
    }

    private function cancelSellerOrder(SellerOrder $sellerOrder, Shipment $shipment, User $actor): void
    {
        $sellerOrder->update(['status' => 'CANCELLED']);
        $shipment->update(['current_status' => 'RETURNED']);
        $this->recordEvent($shipment, 'RETURNED', $actor, 'Seller cancelled the seller order.');
    }

    private function generateWaybill(Shipment $shipment, User $actor): Waybill
    {
        return Waybill::firstOrCreate(
            ['shipment_id' => $shipment->id, 'format_version' => 1],
            [
                'generated_by_user_id' => $actor->id,
                'label_path' => 'waybills/'.$shipment->tracking_number.'.pdf',
                'generated_at' => now(),
            ],
        );
    }

    private function acceptAssignment(RiderAssignment $assignment, Shipment $shipment, User $actor): void
    {
        $assignment->update([
            'status' => 'ACCEPTED',
            'accepted_at' => now(),
        ]);

        $this->recordEvent($shipment, $shipment->current_status, $actor, $assignment->assignment_type.' rider accepted the assignment.', $assignment);
    }

    private function startAssignment(RiderAssignment $assignment, Shipment $shipment, User $actor): void
    {
        $assignment->update([
            'status' => 'IN_PROGRESS',
            'started_at' => now(),
        ]);

        if ($assignment->assignment_type === 'DELIVERY') {
            $shipment->update(['current_status' => 'OUT_FOR_DELIVERY']);
            $this->recordEvent($shipment, 'OUT_FOR_DELIVERY', $actor, 'Delivery rider is out for delivery.', $assignment);
        } else {
            $this->recordEvent($shipment, $shipment->current_status, $actor, 'Pickup rider started pickup.', $assignment);
        }
    }

    private function completePickup(RiderAssignment $assignment, Shipment $shipment, User $actor, array $payload): void
    {
        if ($assignment->assignment_type !== 'PICKUP') {
            throw new RuntimeException('This assignment is not a pickup assignment.');
        }

        $this->recordScan($shipment, $actor, 'PICKUP_COLLECTED', strtoupper((string) ($payload['scan_method'] ?? 'MANUAL')), (string) ($payload['scanned_code'] ?? $shipment->tracking_number), 'SUCCESS', null, $assignment);

        $assignment->update([
            'status' => 'COMPLETED',
            'completed_at' => now(),
        ]);

        $shipment->update(['current_status' => 'PICKED_UP']);
        $shipment->pickupRequests()->where('status', 'APPROVED')->update(['status' => 'FULFILLED']);
        $this->recordEvent($shipment, 'PICKED_UP', $actor, 'Pickup rider collected the parcel from seller.', $assignment);
        $this->createEarning($assignment, '50.00');
    }

    private function completeDelivery(RiderAssignment $assignment, Shipment $shipment, User $actor, array $payload): void
    {
        if ($assignment->assignment_type !== 'DELIVERY') {
            throw new RuntimeException('This assignment is not a delivery assignment.');
        }

        $attemptNumber = (int) $shipment->deliveryAttempts()->count() + 1;

        DeliveryAttempt::create([
            'shipment_id' => $shipment->id,
            'rider_assignment_id' => $assignment->id,
            'attempt_number' => $attemptNumber,
            'status' => 'DELIVERED',
            'proof_path' => $payload['proof_path'] ?? null,
            'attempted_at' => now(),
        ]);

        $assignment->update([
            'status' => 'COMPLETED',
            'completed_at' => now(),
        ]);

        $shipment->update(['current_status' => 'DELIVERED']);
        $shipment->sellerOrder?->update(['status' => 'COMPLETED']);
        $this->syncParentOrder($shipment->sellerOrder?->order);
        $this->recordEvent($shipment, 'DELIVERED', $actor, 'Delivery rider delivered the parcel.', $assignment);
        $this->createEarning($assignment, '75.00');
    }

    private function failDelivery(RiderAssignment $assignment, Shipment $shipment, User $actor, array $payload): void
    {
        if ($assignment->assignment_type !== 'DELIVERY') {
            throw new RuntimeException('This assignment is not a delivery assignment.');
        }

        $reason = (string) ($payload['failure_reason'] ?? 'Delivery failed.');
        $attemptNumber = (int) $shipment->deliveryAttempts()->count() + 1;
        $status = strtoupper((string) ($payload['attempt_status'] ?? 'FAILED'));

        if (! in_array($status, ['FAILED', 'RESCHEDULED', 'RETURNED'], true)) {
            $status = 'FAILED';
        }

        DeliveryAttempt::create([
            'shipment_id' => $shipment->id,
            'rider_assignment_id' => $assignment->id,
            'attempt_number' => $attemptNumber,
            'status' => $status,
            'failure_reason' => $reason,
            'proof_path' => $payload['proof_path'] ?? null,
            'attempted_at' => now(),
            'next_attempt_at' => $payload['next_attempt_at'] ?? null,
        ]);

        $shipmentStatus = $status === 'RETURNED' ? 'RETURNED' : 'DELIVERY_FAILED';

        $shipment->update(['current_status' => $shipmentStatus]);
        $assignment->update([
            'status' => $status === 'RESCHEDULED' ? 'ACCEPTED' : 'COMPLETED',
            'completed_at' => $status === 'RESCHEDULED' ? null : now(),
        ]);

        $this->recordEvent($shipment, $shipmentStatus, $actor, $reason, $assignment);
    }

    private function rejectAssignment(RiderAssignment $assignment, Shipment $shipment, User $actor, string $reason): void
    {
        $assignment->update([
            'status' => 'REJECTED',
            'cancelled_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $this->recordEvent($shipment, $shipment->current_status, $actor, $assignment->assignment_type.' assignment rejected: '.$reason, $assignment);
    }

    private function createEarning(RiderAssignment $assignment, string $amount): void
    {
        RiderEarning::firstOrCreate(
            ['rider_assignment_id' => $assignment->id],
            [
                'rider_profile_id' => $assignment->rider_profile_id,
                'amount' => $amount,
                'status' => 'PENDING',
                'earned_at' => now(),
            ],
        );
    }

    private function syncParentOrder(?Order $order): void
    {
        if (! $order) {
            return;
        }

        $order->load('sellerOrders');

        if ($order->sellerOrders->every(fn (SellerOrder $sellerOrder): bool => $sellerOrder->status === 'COMPLETED')) {
            $order->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
            ]);
        }
    }

    private function newTrackingNumber(): string
    {
        do {
            $value = 'LKH-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (Shipment::where('tracking_number', $value)->exists());

        return $value;
    }
}
