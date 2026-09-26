<?php

namespace App\Models\Seller;

use App\Models\Admin\SellerComplianceCase;
use App\Models\Buyer\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerProfile extends Model
{
    protected $table = 'seller_profiles';

    protected $fillable = [
        'user_id',
        'primary_category_id',
        'business_address_id',
        'business_name',
        'business_registration_number',
        'status',
        'approved_by_user_id',
        'approved_at',
    ];

    protected function casts(): array
    {
        return ['approved_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Transitional alias. */
    public function owner(): BelongsTo
    {
        return $this->user();
    }

    public function primaryCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'primary_category_id');
    }

    public function businessAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'business_address_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sellerOrders(): HasMany
    {
        return $this->hasMany(SellerOrder::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function complianceCases(): HasMany
    {
        return $this->hasMany(SellerComplianceCase::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'ACTIVE');
    }
}
