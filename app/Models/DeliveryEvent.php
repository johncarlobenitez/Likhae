<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryEvent extends Model
{
    protected $fillable = ['shipment_id', 'status', 'attempt', 'user_id', 'note', 'receiver_name', 'photo_path', 'occurred_at', 'source_type', 'source_id'];

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
