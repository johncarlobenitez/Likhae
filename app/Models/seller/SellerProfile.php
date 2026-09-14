<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'line_of_business_category_id', 'shop_name', 'tagline', 'description',
        'location', 'business_days', 'business_hours', 'processing_days', 'order_cutoff',
        'vacation_mode', 'auto_accept_orders', 'store_visibility', 'notification_preferences',
        'avatar_path', 'banner_path',
    ];

    protected function casts(): array
    {
        return [
            'vacation_mode' => 'boolean',
            'auto_accept_orders' => 'boolean',
            'store_visibility' => 'boolean',
            'notification_preferences' => 'array',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function lineOfBusinessCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'line_of_business_category_id');
    }
}
