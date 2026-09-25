<?php

namespace App\Models\Seller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    protected $fillable = ['user_id', 'name', 'slug', 'description', 'logo_path', 'permit_path', 'banner_path', 'settings', 'status', 'rejection_reason', 'approved_by', 'approved_at', 'commission_bps', 'pickup_address_id'];

    protected function casts(): array
    {
        return ['approved_at' => 'datetime', 'settings' => 'array'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SellerOrder::class);
    }

    public function pickupAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'pickup_address_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class, 'account_id')->where('account_type', 'seller');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function balanceMinor(): int
    {
        return (int) $this->ledgerEntries()->sum('amount_minor');
    }
}
