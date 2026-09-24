<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = ['user_id', 'label', 'recipient', 'phone', 'line1', 'region', 'region_code', 'barangay', 'barangay_code', 'city', 'city_code', 'province', 'province_code', 'postal_code', 'landmark', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRecipientNameAttribute(): string
    {
        return $this->recipient;
    }

    public function getContactNumberAttribute(): string
    {
        return $this->phone;
    }

    public function getMunicipalityAttribute(): string
    {
        return $this->city;
    }

    public function getHouseNumberAttribute(): string
    {
        return $this->line1;
    }

    public function getStreetAttribute(): string
    {
        return '';
    }

    public function formatted(): string
    {
        return collect([
            $this->line1,
            $this->barangay,
            $this->city,
            $this->province,
            $this->postal_code,
        ])->filter(fn ($part) => filled($part))->implode(', ');
    }
}
