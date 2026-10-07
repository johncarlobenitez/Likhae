<?php

namespace App\Services\Maps;

use App\Models\Logistics\LogisticsCenter;
use App\Models\Logistics\Shipment;
use App\Models\Rider\RiderAssignment;
use App\Models\User;

class MapDataService
{
    /**
     * Return a deliberately small, role-safe marker payload for the browser.
     * Do not add names, phone numbers, tokens, or full customer addresses here.
     */
    public function forShipment(?Shipment $shipment, bool $includeDestination = true, bool $includeSeller = false, ?User $viewer = null): array
    {
        if (! $shipment) {
            return [];
        }

        $viewer ??= auth()->user();
        $shipment->loadMissing([
            'sellerOrder.order.address',
            'sellerOrder.sellerProfile.businessAddress',
            'riderAssignments.riderProfile',
            'riderAssignments.liveLocation',
            'logisticsCenter.address',
            'serviceArea.logisticsCenter.address',
        ]);
        $tracking = $this->trackingState($shipment, $viewer);
        $markers = [];

        if ($includeDestination) {
            $address = $shipment->sellerOrder?->order?->address;
            $marker = $this->addressMarker($address, 'destination-'.$shipment->id, 'Buyer delivery address');
            if ($marker) {
                $marker['target_kind'] = 'buyer';
                $marker['shipment_id'] = $shipment->id;
                $markers[] = $marker;
            }
        }

        if ($includeSeller) {
            $marker = $this->addressMarker($shipment->sellerOrder?->sellerProfile?->businessAddress, 'seller-'.$shipment->id, 'Seller pickup address');
            if ($marker) {
                $marker['kind'] = 'seller';
                $marker['target_kind'] = 'seller';
                $marker['shipment_id'] = $shipment->id;
                $markers[] = $marker;
            }
        }

        foreach ($shipment->riderAssignments ?? [] as $assignment) {
            $canSeeLive = $this->canSeeLive($shipment, $assignment, $viewer);
            $canPublishLocation = $this->canPublishLocation($shipment, $assignment, $viewer);
            if (! $canSeeLive && ! $canPublishLocation) {
                continue;
            }

            $marker = $this->assignmentMarker($assignment);
            if (! $marker && $canPublishLocation) {
                $marker = $this->pendingAssignmentMarker($assignment);
            }
            if ($marker) {
                $marker['shipment_id'] = $shipment->id;
                $marker['active_rider'] = (int) $assignment->id === (int) ($tracking['active_assignment_id'] ?? 0);
                if ($canPublishLocation) {
                    $marker['location_update_endpoint'] = route('rider.assignments.location', ['assignment' => $assignment->id]);
                }
                $markers[] = $marker;
            }
        }

        $center = $shipment->logisticsCenter ?: $shipment->serviceArea?->logisticsCenter;
        $marker = $this->centerMarker($center);
        if ($marker) {
            $marker['id'] = 'logistics-center-'.($center?->id ?? 'unknown').'-shipment-'.$shipment->id;
            $marker['shipment_id'] = $shipment->id;
            $marker['target_kind'] = 'logistics';
            $markers[] = $marker;
        }

        return collect($markers)
            ->map(fn (array $marker): array => array_merge($marker, [
                'tracking_endpoint' => $tracking['endpoint'],
                'tracking_channel' => $tracking['channel'],
                'tracking_status' => $tracking['status'],
                'active_destination_kind' => $tracking['active_destination_kind'],
            ]))
            ->values()
            ->all();
    }

    public function trackingPayload(?Shipment $shipment, ?User $viewer = null): array
    {
        if (! $shipment) {
            return ['markers' => [], 'tracking' => []];
        }

        $viewer ??= auth()->user();

        return [
            'markers' => $this->forShipment($shipment, true, true, $viewer),
            'tracking' => $this->trackingState($shipment, $viewer),
        ];
    }

    public function forShipments(iterable $shipments, bool $includeSeller = false, ?User $viewer = null): array
    {
        return collect($shipments)
            ->flatMap(fn ($shipment) => $this->forShipment($shipment, true, $includeSeller, $viewer))
            ->unique('id')
            ->values()
            ->all();
    }

    public function forAssignments(iterable $assignments, bool $includeSeller = false, ?User $viewer = null): array
    {
        return collect($assignments)
            ->flatMap(function (RiderAssignment $assignment) use ($includeSeller, $viewer): array {
                return $this->forShipment($assignment->shipment, true, $includeSeller, $viewer);
            })
            ->unique('id')
            ->values()
            ->all();
    }

    public function addressMarker(?object $address, string $id, string $label): ?array
    {
        if (! $address || ! is_numeric($address->latitude) || ! is_numeric($address->longitude)) {
            return null;
        }

        return [
            'id' => $id,
            'kind' => 'destination',
            'label' => $label,
            'title' => $label,
            'latitude' => round((float) $address->latitude, 7),
            'longitude' => round((float) $address->longitude, 7),
            'popup' => collect([
                $address->barangay_name,
                $address->municipality_name,
                $address->province_name,
            ])->filter()->implode(', '),
        ];
    }

    public function centerMarker(?LogisticsCenter $center): ?array
    {
        $marker = $this->addressMarker($center?->address, 'logistics-center-'.($center?->id ?? 'unknown'), 'Logistics center');
        if ($marker) {
            $marker['kind'] = 'center';
        }
        return $marker;
    }

    public function assignmentMarker(?RiderAssignment $assignment): ?array
    {
        $location = $assignment?->liveLocation;
        if (! $assignment || ! $location || ! is_numeric($location->latitude) || ! is_numeric($location->longitude)) {
            return null;
        }

        return [
            'id' => 'rider-assignment-'.$assignment->id,
            'kind' => 'rider',
            'assignment_id' => $assignment->id,
            'shipment_id' => $assignment->shipment_id,
            'assignment_type' => $assignment->assignment_type,
            'label' => $assignment->assignment_type === RiderAssignment::TYPE_PICKUP ? 'Pickup rider' : 'Delivery rider',
            'title' => 'Current rider location',
            'latitude' => round((float) $location->latitude, 7),
            'longitude' => round((float) $location->longitude, 7),
            'popup' => $location->recorded_at?->diffForHumans() ? 'Updated '.$location->recorded_at->diffForHumans() : 'Live assignment location',
        ];
    }

    private function pendingAssignmentMarker(RiderAssignment $assignment): array
    {
        return [
            'id' => 'rider-assignment-'.$assignment->id,
            'kind' => 'rider',
            'assignment_id' => $assignment->id,
            'shipment_id' => $assignment->shipment_id,
            'assignment_type' => $assignment->assignment_type,
            'label' => $assignment->assignment_type === RiderAssignment::TYPE_PICKUP ? 'Pickup rider' : 'Delivery rider',
            'title' => 'Waiting for current rider location',
            'popup' => 'Allow device location access to begin live tracking.',
            'location_pending' => true,
        ];
    }

    public function trackingState(Shipment $shipment, ?User $viewer = null): array
    {
        $viewer ??= auth()->user();
        $shipment->loadMissing([
            'sellerOrder.order.address',
            'sellerOrder.sellerProfile.businessAddress',
            'riderAssignments.riderProfile',
            'riderAssignments.liveLocation',
            'logisticsCenter.address',
            'serviceArea.logisticsCenter.address',
        ]);

        $assignment = $this->activeAssignment($shipment)
            ?: $this->acceptedDeliveryAssignment($shipment);
        $destinationKind = $this->destinationKind($shipment, $assignment);
        $destination = match ($destinationKind) {
            'seller' => $shipment->sellerOrder?->sellerProfile?->businessAddress,
            'logistics' => ($shipment->logisticsCenter ?: $shipment->serviceArea?->logisticsCenter)?->address,
            'buyer' => $shipment->sellerOrder?->order?->address,
            default => null,
        };
        $visible = $assignment && $this->canSeeLive($shipment, $assignment, $viewer);
        $location = $visible ? $this->assignmentMarker($assignment) : null;

        return [
            'shipment_id' => $shipment->id,
            'tracking_number' => $shipment->tracking_number,
            'status' => $shipment->current_status,
            'active_destination_kind' => $destinationKind,
            'active_assignment_id' => $assignment?->id,
            'active_assignment_type' => $assignment?->assignment_type,
            'live' => (bool) $location,
            'last_location' => $location ? [
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'recorded_at' => $assignment->liveLocation?->recorded_at?->toIso8601String(),
            ] : null,
            'destination' => $destination && is_numeric($destination->latitude) && is_numeric($destination->longitude) ? [
                'kind' => $destinationKind,
                'latitude' => (float) $destination->latitude,
                'longitude' => (float) $destination->longitude,
            ] : null,
            'endpoint' => filled($shipment->tracking_number)
                ? route('tracking.live', ['trackingCode' => $shipment->tracking_number])
                : null,
            'channel' => 'shipments.'.$shipment->id,
        ];
    }

    private function destinationKind(Shipment $shipment, ?RiderAssignment $assignment): ?string
    {
        if ($assignment) {
            return match ($assignment->assignment_type) {
                'PICKUP' => $assignment->status === 'COMPLETED' || $shipment->current_status === 'PICKED_UP'
                    ? 'logistics'
                    : 'seller',
                'DELIVERY' => 'buyer',
                default => null,
            };
        }

        return match ($shipment->current_status) {
            'READY_FOR_PICKUP' => 'seller',
            'PICKED_UP' => 'logistics',
            'OUT_FOR_DELIVERY' => 'buyer',
            default => null,
        };
    }

    private function acceptedDeliveryAssignment(Shipment $shipment): ?RiderAssignment
    {
        if ($shipment->current_status !== 'ASSIGNED_TO_RIDER') {
            return null;
        }

        return $shipment->riderAssignments
            ->where('assignment_type', 'DELIVERY')
            ->whereIn('status', ['ACCEPTED', 'IN_PROGRESS'])
            ->sortByDesc('id')
            ->first();
    }

    private function activeAssignment(Shipment $shipment): ?RiderAssignment
    {
        return match ($shipment->current_status) {
            'READY_FOR_PICKUP' => $shipment->riderAssignments
                ->where('assignment_type', RiderAssignment::TYPE_PICKUP)
                ->whereIn('status', ['ACCEPTED', 'IN_PROGRESS'])
                ->sortByDesc('id')
                ->first(),
            'PICKED_UP' => $shipment->riderAssignments
                ->where('assignment_type', RiderAssignment::TYPE_PICKUP)
                ->where('status', 'COMPLETED')
                ->sortByDesc('id')
                ->first(),
            'OUT_FOR_DELIVERY' => $shipment->riderAssignments
                ->where('assignment_type', RiderAssignment::TYPE_DELIVERY)
                ->whereIn('status', ['ACCEPTED', 'IN_PROGRESS'])
                ->sortByDesc('id')
                ->first(),
            default => null,
        };
    }

    private function canSeeLive(Shipment $shipment, RiderAssignment $assignment, ?User $viewer): bool
    {
        if (! $viewer || ! $assignment->liveLocation) {
            return false;
        }

        if ($viewer->isAccountType('ADMIN')) {
            return true;
        }

        $phaseVisible = match ($shipment->current_status) {
            'READY_FOR_PICKUP', 'PICKED_UP' => $assignment->assignment_type === RiderAssignment::TYPE_PICKUP,
            'ASSIGNED_TO_RIDER', 'OUT_FOR_DELIVERY' => $assignment->assignment_type === RiderAssignment::TYPE_DELIVERY,
            default => false,
        };

        if (! $phaseVisible) {
            return false;
        }

        if ($viewer->isAccountType('RIDER')) {
            return (int) $assignment->riderProfile?->user_id === (int) $viewer->id;
        }

        if ($viewer->isAccountType('BUYER')) {
            return $shipment->current_status === 'OUT_FOR_DELIVERY'
                && (int) $shipment->sellerOrder?->order?->buyer_user_id === (int) $viewer->id;
        }

        if ($viewer->isAccountType('SELLER')) {
            return $shipment->current_status === 'READY_FOR_PICKUP'
                && (int) $shipment->sellerOrder?->sellerProfile?->user_id === (int) $viewer->id;
        }

        if ($viewer->isAccountType('LOGISTICS')) {
            $center = $shipment->logisticsCenter ?: $shipment->serviceArea?->logisticsCenter;
            return (int) $center?->owner_user_id === (int) $viewer->id;
        }

        return false;
    }

    private function canPublishLocation(Shipment $shipment, RiderAssignment $assignment, ?User $viewer): bool
    {
        if (! $viewer?->isAccountType('RIDER')
            || (int) $assignment->riderProfile?->user_id !== (int) $viewer->id) {
            return false;
        }

        if ($assignment->assignment_type === RiderAssignment::TYPE_PICKUP
            && $shipment->current_status === 'READY_FOR_PICKUP'
            && in_array($assignment->status, ['ACCEPTED', 'IN_PROGRESS'], true)) {
            return true;
        }

        if ($assignment->assignment_type === RiderAssignment::TYPE_DELIVERY
            && in_array($shipment->current_status, ['ASSIGNED_TO_RIDER', 'OUT_FOR_DELIVERY'], true)
            && in_array($assignment->status, ['ACCEPTED', 'IN_PROGRESS'], true)) {
            return true;
        }

        return $assignment->assignment_type === RiderAssignment::TYPE_PICKUP
            && $assignment->status === 'COMPLETED'
            && $shipment->current_status === 'PICKED_UP';
    }
}
