<?php

namespace App\Services\Maps;

use App\Events\RiderLocationUpdated;
use App\Models\Rider\RiderAssignment;
use App\Models\Rider\RiderAssignmentLocation;
use App\Models\User;

class RiderLocationService
{
    public function update(RiderAssignment $assignment, User $user, float $latitude, float $longitude): RiderAssignmentLocation
    {
        $assignment->loadMissing('riderProfile', 'shipment');
        abort_unless((int) $assignment->riderProfile?->user_id === (int) $user->id, 403, 'This assignment does not belong to the rider.');

        $shipment = $assignment->shipment;
        $allowed = in_array($assignment->status, ['ACCEPTED', 'IN_PROGRESS'], true)
            && (($assignment->assignment_type === RiderAssignment::TYPE_PICKUP && $shipment?->current_status === 'READY_FOR_PICKUP')
                || ($assignment->assignment_type === RiderAssignment::TYPE_DELIVERY && $shipment?->current_status === 'OUT_FOR_DELIVERY'))
            || ($assignment->assignment_type === RiderAssignment::TYPE_PICKUP
                && $assignment->status === 'COMPLETED'
                && $shipment?->current_status === 'PICKED_UP');

        if (! $allowed) {
            abort(409, 'Live location is only accepted while the rider is traveling to the seller, sorting center, or buyer delivery address.');
        }

        $location = $assignment->liveLocation()->updateOrCreate([], [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'recorded_at' => now(),
        ]);

        RiderLocationUpdated::dispatch($location->load('riderAssignment.shipment'));

        return $location;
    }
}
