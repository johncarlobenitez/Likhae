<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    'region',
    'province',
    'municipality',
    'barangay',
    'house_number',
    'street',
    'postal_code',
    'landmark',
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
    public const PUBLIC_ROLES = ['buyer', 'seller', 'logistics', 'courier', 'rider'];

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
            'birthday' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    public function workspaceRoute(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'buyer' => 'buyer.home',
            'seller' => 'seller.dashboard',
            'logistics' => 'logistics.dashboard',
            'rider', 'courier' => 'rider.dashboard',
            default => 'home',
        };
    }

    public function inactiveMessage(): string
    {
        return match ($this->status) {
            'pending' => 'Your account is pending administrator approval.',
            'rejected' => 'Your registration has been rejected. Please contact support for assistance.',
            'suspended' => 'Your account has been suspended. Please contact support for assistance.',
            default => 'Your account is not active. Please contact support for assistance.',
        };
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reviewed_by');
    }
}
