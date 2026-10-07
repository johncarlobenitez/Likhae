<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Communication\Conversation;
use App\Models\Logistics\Shipment;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversations.{conversation}', function ($user, Conversation $conversation) {
    return $conversation->participants()
        ->whereKey($user->id)
        ->exists();
});

Broadcast::channel('shipments.{shipment}', function ($user, Shipment $shipment) {
    $shipment->loadMissing([
        'sellerOrder.order',
        'sellerOrder.sellerProfile',
        'logisticsCenter',
        'serviceArea.logisticsCenter',
        'riderAssignments.riderProfile',
    ]);

    if ($user->isAccountType('ADMIN')) {
        return true;
    }

    if ($user->isAccountType('BUYER')) {
        return (int) $shipment->sellerOrder?->order?->buyer_user_id === (int) $user->id;
    }

    if ($user->isAccountType('SELLER')) {
        return (int) $shipment->sellerOrder?->sellerProfile?->user_id === (int) $user->id;
    }

    if ($user->isAccountType('LOGISTICS')) {
        $center = $shipment->logisticsCenter ?: $shipment->serviceArea?->logisticsCenter;
        return (int) $center?->owner_user_id === (int) $user->id;
    }

    if ($user->isAccountType('RIDER')) {
        return $shipment->riderAssignments
            ->contains(fn ($assignment) => (int) $assignment->riderProfile?->user_id === (int) $user->id);
    }

    return false;
});
