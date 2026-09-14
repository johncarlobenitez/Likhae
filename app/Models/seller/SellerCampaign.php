<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'type', 'name', 'code', 'discount_type', 'discount_value',
        'minimum_spend', 'usage_limit', 'uses', 'starts_at', 'ends_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'minimum_spend' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
