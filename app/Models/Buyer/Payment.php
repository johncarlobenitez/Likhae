<?php

namespace App\Models\Buyer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['order_id', 'method', 'amount_minor', 'status', 'provider_ref'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
