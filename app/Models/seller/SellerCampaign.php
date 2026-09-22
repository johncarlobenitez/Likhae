<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'type', 'name', 'code', 'discount_type', 'discount_bps',
        'discount_minor', 'minimum_spend_minor', 'usage_limit', 'uses', 'starts_at', 'ends_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function getDiscountValueAttribute(): float
    {
        return $this->discount_type === 'percent' ? $this->discount_bps / 100 : $this->discount_minor / 100;
    }

    public function getMinimumSpendAttribute(): float
    {
        return $this->minimum_spend_minor / 100;
    }
}
