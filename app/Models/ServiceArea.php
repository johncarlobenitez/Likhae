<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceArea extends Model
{
    protected $fillable = ['logistics_provider_id', 'province', 'city', 'province_code', 'city_code', 'base_fee_minor', 'per_kg_fee_minor', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function provider(): BelongsTo { return $this->belongsTo(LogisticsProvider::class, 'logistics_provider_id'); }
    public function feeFor(int $weightGrams): int
    {
        $additionalKilos = max(0, (int) ceil(max(0, $weightGrams - 1000) / 1000));
        return $this->base_fee_minor + ($additionalKilos * $this->per_kg_fee_minor);
    }
}
