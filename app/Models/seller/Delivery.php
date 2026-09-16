<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'rider_id', 'pickup_rider_id', 'address', 'status', 'assigned_at', 'delivered_at',
        'provider', 'tracking_number', 'pickup_window', 'pickup_note', 'requested_at',
        'pickup_assigned_at', 'pickup_accepted_at', 'picked_up_at', 'arrived_at_sorting_center_at',
        'delivery_picked_up_at', 'failed_at', 'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'delivered_at' => 'datetime',
            'requested_at' => 'datetime',
            'pickup_assigned_at' => 'datetime',
            'pickup_accepted_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'arrived_at_sorting_center_at' => 'datetime',
            'delivery_picked_up_at' => 'datetime',
            'failed_at' => 'datetime',
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

    public function pickupRider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pickup_rider_id');
    }
}
