<?php

namespace App\Services\Maps;

use App\Models\Logistics\LogisticsCenter;
use App\Models\Logistics\Shipment;
use App\Models\Rider\RiderAssignment;

class MapDataService
{
    /**
     * Return a deliberately small, role-safe marker payload for the browser.
     * Do not add names, phone numbers, tokens, or full customer addresses here.
     */
    public function forShipment(?Shipment $shipment, bool $includeDestination = true): array
    {
        if (! $shipment) {
            return [];
        }

        $markers = [];

        if ($includeDestination) {
            $address = $shipment->sellerOrder?->order?->address;
            $marker = $this->addressMarker($address, 'destination-'.$shipment->id, 'Delivery destination');
            if ($marker) {
                $markers[] = $marker;
            }
        }

        foreach ($shipment->riderAssignments ?? [] as $assignment) {
            $marker = $this->assignmentMarker($assignment);
            if ($marker) {
                $markers[] = $marker;
            }
        }

        $center = $shipment->logisticsCenter;
        $marker = $this->centerMarker($center);
        if ($marker) {
            $markers[] = $marker;
        }

        return $markers;
    }

    public function forShipments(iterable $shipments): array
    {
        return collect($shipments)
            ->flatMap(fn ($shipment) => $this->forShipment($shipment))
            ->unique('id')
            ->values()
            ->all();
    }

    public function forAssignments(iterable $assignments): array
    {
        return collect($assignments)
            ->flatMap(function (RiderAssignment $assignment): array {
                $markers = $this->forShipment($assignment->shipment);
                if ($assignment->shipment) {
                    $markers[] = $this->assignmentMarker($assignment);
                }
                return array_filter($markers);
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
            'label' => $assignment->assignment_type === RiderAssignment::TYPE_PICKUP ? 'Pickup rider' : 'Delivery rider',
            'title' => 'Current rider location',
            'latitude' => round((float) $location->latitude, 7),
            'longitude' => round((float) $location->longitude, 7),
            'popup' => $location->recorded_at?->diffForHumans() ? 'Updated '.$location->recorded_at->diffForHumans() : 'Live assignment location',
        ];
    }
}
