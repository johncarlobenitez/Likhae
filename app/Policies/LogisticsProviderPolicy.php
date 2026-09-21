<?php

namespace App\Policies;

use App\Models\LogisticsProvider;
use App\Models\User;

class LogisticsProviderPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function view(User $user, LogisticsProvider $provider): bool
    {
        return $provider->user_id === $user->id;
    }

    public function update(User $user, LogisticsProvider $provider): bool
    {
        return $this->view($user, $provider);
    }
}
