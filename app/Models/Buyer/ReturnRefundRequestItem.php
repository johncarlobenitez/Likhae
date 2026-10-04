<?php

namespace App\Models\Buyer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRefundRequestItem extends Model
{
    protected $fillable = [
        'return_refund_request_id',
        'order_item_id',
        'quantity',
        'refundable_amount',
    ];

    protected function casts(): array
    {
        return ['refundable_amount' => 'decimal:2'];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(ReturnRefundRequest::class, 'return_refund_request_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
