<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParcelStatusHistory extends Model
{
    public $timestamps = false;

    protected $fillable = ['delivery_id', 'order_id', 'previous_status', 'new_status', 'performed_by', 'performed_by_role', 'remarks', 'created_at'];

    protected function casts(): array { return ['created_at' => 'datetime']; }
    public function delivery(): BelongsTo { return $this->belongsTo(Delivery::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'performed_by'); }
}
