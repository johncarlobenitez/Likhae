<?php

namespace App\Policies;

use App\Models\Rider;
use App\Models\User;

class RiderPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function view(User $user, Rider $rider): bool
    {
        return $rider->logistics_provider_id === $user->logisticsProvider()->value('id');
    }

    public function update(User $user, Rider $rider): bool
    {
        return $this->view($user, $rider);
    }
}
