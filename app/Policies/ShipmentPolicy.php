<?php

namespace App\Policies;

use App\Models\Logistics\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function viewProof(User $user, Shipment $shipment): bool
    {
        $shipment->loadMissing('sellerOrder.order', 'sellerOrder.seller', 'provider');
        return $user->hasRole('admin')
            || $shipment->sellerOrder->order->buyer_id === $user->id
            || $shipment->sellerOrder->seller->user_id === $user->id
            || $shipment->provider?->user_id === $user->id
            || $shipment->rider?->user_id === $user->id;
    }
}
