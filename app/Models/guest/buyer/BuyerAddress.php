<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id', 'label', 'recipient_name', 'contact_number', 'region', 'province',
        'municipality', 'barangay', 'house_number', 'street', 'postal_code', 'landmark', 'is_default',
    ];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function formatted(): string
    {
        return collect([
            $this->house_number,
            $this->street,
            $this->barangay,
            $this->municipality,
            $this->province,
            $this->region,
            $this->postal_code,
        ])->filter()->implode(', ');
    }
}
