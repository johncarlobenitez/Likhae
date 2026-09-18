<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'rider_id', 'pickup_rider_id', 'address', 'status', 'assigned_at', 'delivered_at',
        'provider', 'tracking_number', 'pickup_window', 'pickup_note', 'requested_at',
        'pickup_assigned_at', 'pickup_accepted_at', 'picked_up_at', 'arrived_at_sorting_center_at',
        'delivery_picked_up_at', 'failed_at', 'failure_reason',
        'handover_method', 'logistics_center_id', 'seller_address', 'waybill_generated_at',
        'received_at', 'received_by', 'sorted_at', 'delivery_area',
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
            'waybill_generated_at' => 'datetime',
            'received_at' => 'datetime',
            'sorted_at' => 'datetime',
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

    public function logisticsCenter(): BelongsTo { return $this->belongsTo(User::class, 'logistics_center_id'); }
    public function receiver(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
    public function assignments(): HasMany { return $this->hasMany(ParcelAssignment::class); }
    public function statusHistory(): HasMany { return $this->hasMany(ParcelStatusHistory::class)->orderBy('created_at'); }
    public function scanEvents(): HasMany { return $this->hasMany(ParcelScanEvent::class)->orderBy('created_at'); }
}
