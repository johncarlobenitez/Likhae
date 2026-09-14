<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'rider_id', 'address', 'status', 'assigned_at', 'delivered_at',
        'provider', 'tracking_number', 'pickup_window', 'pickup_note', 'requested_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'delivered_at' => 'datetime',
            'requested_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rider_id');
    }
}
