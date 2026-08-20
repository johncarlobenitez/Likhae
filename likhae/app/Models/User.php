<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'first_name',
    'last_name',
    'middle_initial',
    'sex',
    'birthday',
    'contact_number',
    'province',
    'municipality',
    'barangay',
    'house_number',
    'street',
    'valid_id_path',
    'business_name',
    'store_name',
    'line_of_business',
    'business_type',
    'dti_sec_number',
    'tin',
    'business_permit_path',
    'vehicle_type',
    'plate_number',
    'or_cr_path',
    'drivers_license_path',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
