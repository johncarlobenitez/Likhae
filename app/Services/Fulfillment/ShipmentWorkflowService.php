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
use Illuminate\Validation\ValidationException;
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
                'handover_confirm' => $this->confirmSellerHandover($sellerOrder, $shipment, $actor),
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
            if ($pickupRequest->status !== 'PENDING') {
                throw new RuntimeException('Only pending pickup requests can be approved.');
            }

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
            if ($pickupRequest->status !== 'PENDING') {
                throw new RuntimeException('Only pending pickup requests can be rejected.');
            }

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

            $shipment->loadMissing('serviceArea');
            $shipmentCenterId = $shipment->logistics_center_id ?: $shipment->serviceArea?->logistics_center_id;
            if ($shipmentCenterId && (int) $rider->logistics_center_id !== (int) $shipmentCenterId) {
                throw new RuntimeException('The rider does not belong to this shipment logistics center.');
            }

            if ($assignmentType === 'PICKUP') {
                $this->requireShipmentStatus($shipment, ['READY_FOR_PICKUP'], 'assign a pickup rider');
            } else {
                $this->requireShipmentStatus($shipment, ['SORTED', 'DELIVERY_FAILED'], 'assign a delivery rider');

                if (! $shipment->service_area_id || ! $rider->areaAssignments()->where('service_area_id', $shipment->service_area_id)->where('is_active', true)->exists()) {
                    throw new RuntimeException('The rider is not assigned to the parcel destination area.');
                }
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
            $this->requireShipmentStatus($shipment, ['PICKED_UP'], 'receive the parcel at the sorting center');

            if ($shipment->logistics_center_id && (int) $shipment->logistics_center_id !== (int) $center->id) {
                throw new RuntimeException('This parcel belongs to another logistics center.');
            }
            $shipment->loadMissing('serviceArea');
            if (! $shipment->logistics_center_id && (int) $shipment->serviceArea?->logistics_center_id !== (int) $center->id) {
                throw new RuntimeException('This parcel is not assigned to this logistics center.');
            }

            $code = trim($code);
            if ($code === '' || ! hash_equals($shipment->tracking_number, $code)) {
                throw ValidationException::withMessages(['scanned_code' => 'The scanned parcel code does not match the shipment tracking number.']);
            }

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
            $this->requireShipmentStatus($shipment, ['AT_SORTING_CENTER'], 'sort the parcel');
            if ($shipment->logistics_center_id && (int) $shipment->logistics_center_id !== (int) $center->id) {
                throw new RuntimeException('This parcel belongs to another logistics center.');
            }
            $area = $serviceArea ?: $this->findServiceAreaForAddress(
                (string) $shipment->destination_province_code,
                (string) $shipment->destination_municipality_code,
                (string) $shipment->destination_barangay_code,
                $center,
            );

            if (! $area || (int) $area->logistics_center_id !== (int) $center->id) {
                throw new RuntimeException('No active delivery area at this center matches the parcel destination.');
            }

            $matchesDestination = $area->locations()
                ->where('province_code', $shipment->destination_province_code)
                ->where('municipality_code', $shipment->destination_municipality_code)
                ->where('barangay_code', $shipment->destination_barangay_code)
                ->exists();

            if (! $matchesDestination) {
                throw new RuntimeException('The selected delivery area does not match the parcel destination.');
            }

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
        $this->requireSellerAndShipmentStatus($sellerOrder, $shipment, 'PLACED', 'confirm the order');
        $sellerOrder->update(['status' => 'CONFIRMED']);
        $shipment->update(['current_status' => 'CONFIRMED']);
        $this->recordEvent($shipment, 'CONFIRMED', $actor, 'Seller confirmed the order.');
    }

    private function prepareSellerOrder(SellerOrder $sellerOrder, Shipment $shipment, User $actor): void
    {
        $this->requireSellerAndShipmentStatus($sellerOrder, $shipment, 'CONFIRMED', 'prepare the order');
        $sellerOrder->update(['status' => 'PREPARING']);
        $shipment->update(['current_status' => 'PREPARING']);
        $this->recordEvent($shipment, 'PREPARING', $actor, 'Seller is preparing the parcel.');
    }

    private function readyForPickup(SellerOrder $sellerOrder, Shipment $shipment, User $actor): void
    {
        $this->requireSellerAndShipmentStatus($sellerOrder, $shipment, 'PREPARING', 'mark the order ready for pickup');
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
        if (in_array($sellerOrder->status, ['PICKED_UP', 'COMPLETED', 'CANCELLED'], true)) {
            throw new RuntimeException('This order can no longer be cancelled by the seller.');
        }
        $sellerOrder->update(['status' => 'CANCELLED']);
        $shipment->update(['current_status' => 'RETURNED']);
        $this->recordEvent($shipment, 'RETURNED', $actor, 'Seller cancelled the seller order.');
    }

    private function confirmSellerHandover(SellerOrder $sellerOrder, Shipment $shipment, User $actor): void
    {
        $this->requireSellerAndShipmentStatus($sellerOrder, $shipment, 'READY_FOR_PICKUP', 'confirm parcel handover');

        $assignment = $shipment->riderAssignments()
            ->where('assignment_type', 'PICKUP')
            ->whereIn('status', ['ACCEPTED', 'IN_PROGRESS'])
            ->latest()
            ->first();

        if (! $assignment) {
            throw new RuntimeException('The pickup rider must accept the assignment before handover can be confirmed.');
        }

        $this->recordEvent($shipment, 'READY_FOR_PICKUP', $actor, 'Seller confirmed parcel handover to the pickup rider.', $assignment);
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
        $this->requireAssignmentStatus($assignment, ['ASSIGNED'], 'accept this assignment');
        $assignment->update([
            'status' => 'ACCEPTED',
            'accepted_at' => now(),
        ]);

        $this->recordEvent($shipment, $shipment->current_status, $actor, $assignment->assignment_type.' rider accepted the assignment.', $assignment);
    }

    private function startAssignment(RiderAssignment $assignment, Shipment $shipment, User $actor): void
    {
        $this->requireAssignmentStatus($assignment, ['ACCEPTED'], 'start this assignment');

        if ($assignment->assignment_type === 'PICKUP') {
            $this->requireShipmentStatus($shipment, ['READY_FOR_PICKUP'], 'start pickup');
        } else {
            $this->requireShipmentStatus($shipment, ['ASSIGNED_TO_RIDER', 'DELIVERY_FAILED'], 'start delivery');
        }
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
        $this->requireAssignmentStatus($assignment, ['IN_PROGRESS'], 'complete pickup');
        $this->requireShipmentStatus($shipment, ['READY_FOR_PICKUP'], 'complete pickup');

        $scannedCode = trim((string) ($payload['scanned_code'] ?? ''));
        if ($scannedCode === '' || ! hash_equals($shipment->tracking_number, $scannedCode)) {
            throw ValidationException::withMessages(['scanned_code' => 'The scanned parcel code does not match the shipment tracking number.']);
        }

        $this->recordScan($shipment, $actor, 'PICKUP_COLLECTED', strtoupper((string) ($payload['scan_method'] ?? 'MANUAL')), $scannedCode, 'SUCCESS', null, $assignment);

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
        $this->requireAssignmentStatus($assignment, ['IN_PROGRESS'], 'complete delivery');
        $this->requireShipmentStatus($shipment, ['OUT_FOR_DELIVERY'], 'complete delivery');

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
        $this->recordEvent($shipment, 'DELIVERED', $actor, 'Delivery rider delivered the parcel.', $assignment);
        $this->createEarning($assignment, '75.00');
    }

    private function failDelivery(RiderAssignment $assignment, Shipment $shipment, User $actor, array $payload): void
    {
        if ($assignment->assignment_type !== 'DELIVERY') {
            throw new RuntimeException('This assignment is not a delivery assignment.');
        }
        $this->requireAssignmentStatus($assignment, ['IN_PROGRESS'], 'record a failed delivery');
        $this->requireShipmentStatus($shipment, ['OUT_FOR_DELIVERY'], 'record a failed delivery');

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
        $this->requireAssignmentStatus($assignment, ['ASSIGNED', 'ACCEPTED'], 'reject this assignment');

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

    private function requireSellerAndShipmentStatus(SellerOrder $sellerOrder, Shipment $shipment, string $status, string $action): void
    {
        if ($sellerOrder->status !== $status || $shipment->current_status !== $status) {
            throw new RuntimeException("The order must be {$status} to {$action}.");
        }
    }

    private function requireShipmentStatus(Shipment $shipment, array $statuses, string $action): void
    {
        if (! in_array($shipment->current_status, $statuses, true)) {
            throw new RuntimeException('The shipment must be '.implode(' or ', $statuses).' to '.$action.'.');
        }
    }

    private function requireAssignmentStatus(RiderAssignment $assignment, array $statuses, string $action): void
    {
        if (! in_array($assignment->status, $statuses, true)) {
            throw new RuntimeException('The assignment must be '.implode(' or ', $statuses).' to '.$action.'.');
        }
    }
}
