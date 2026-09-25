<?php

namespace App\Policies;

use App\Models\Seller\Seller;
use App\Models\User;

class SellerPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function view(User $user, Seller $seller): bool
    {
        return $seller->user_id === $user->id;
    }

    public function update(User $user, Seller $seller): bool
    {
        return $this->view($user, $seller);
    }
}
