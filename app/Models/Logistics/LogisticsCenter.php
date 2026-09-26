<?php

namespace App\Models\Logistics;

use App\Models\Buyer\Address;
use App\Models\Rider\RiderApplicationData;
use App\Models\Rider\RiderProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogisticsCenter extends Model
{
    protected $table = 'logistics_centers';

    protected $fillable = [
        'owner_user_id',
        'address_id',
        'code',
        'business_name',
        'business_registration_number',
        'dti_registration_number',
        'status',
        'approved_by_user_id',
        'approved_at',
    ];

    protected function casts(): array
    {
        return ['approved_at' => 'datetime'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function riders(): HasMany
    {
        return $this->hasMany(RiderProfile::class);
    }

    public function riderApplications(): HasMany
    {
        return $this->hasMany(RiderApplicationData::class, 'target_logistics_center_id');
    }

    public function serviceAreas(): HasMany
    {
        return $this->hasMany(ServiceArea::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'ACTIVE');
    }
}
