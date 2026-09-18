<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParcelAssignment extends Model
{
    protected $fillable = ['delivery_id', 'rider_id', 'assigned_by', 'assignment_type', 'status', 'assigned_at', 'accepted_at', 'picked_up_at', 'completed_at'];

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime', 'accepted_at' => 'datetime', 'picked_up_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function delivery(): BelongsTo { return $this->belongsTo(Delivery::class); }
    public function rider(): BelongsTo { return $this->belongsTo(User::class, 'rider_id'); }
    public function assigner(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by'); }
}
