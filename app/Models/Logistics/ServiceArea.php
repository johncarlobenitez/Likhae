<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceArea extends Model
{
    protected $fillable = ['logistics_provider_id', 'province', 'city', 'province_code', 'city_code', 'base_fee_minor', 'per_kg_fee_minor', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function provider(): BelongsTo { return $this->belongsTo(LogisticsProvider::class, 'logistics_provider_id'); }
    public function scopeForAddress(Builder $query, Address $address): Builder
    {
        return $query->where(function (Builder $locations) use ($address): void {
            if ($address->city_code) $locations->where('city_code', $address->city_code);
            $locations->orWhere(function (Builder $names) use ($address): void {
                $names->where('city', $address->city)->where('province', $address->province);
                if ($address->city_code) $names->whereNull('city_code');
            });
        });
    }
    public function feeFor(int $weightGrams): int
    {
        $additionalKilos = max(0, (int) ceil(max(0, $weightGrams - 1000) / 1000));
        return $this->base_fee_minor + ($additionalKilos * $this->per_kg_fee_minor);
    }
}
