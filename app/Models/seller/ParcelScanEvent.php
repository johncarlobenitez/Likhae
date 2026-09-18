<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParcelScanEvent extends Model
{
    public $timestamps = false;

    protected $fillable = ['delivery_id', 'tracking_number', 'scan_type', 'scanned_by_user_id', 'scanned_by_role', 'logistics_center_id', 'assignment_id', 'created_at'];

    protected function casts(): array { return ['created_at' => 'datetime']; }
    public function delivery(): BelongsTo { return $this->belongsTo(Delivery::class); }
    public function scanner(): BelongsTo { return $this->belongsTo(User::class, 'scanned_by_user_id'); }
    public function assignment(): BelongsTo { return $this->belongsTo(ParcelAssignment::class); }
}
