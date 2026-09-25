<?php

namespace App\Models\Seller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerOrderEvent extends Model
{
    protected $fillable = ['seller_order_id', 'from_status', 'to_status', 'user_id', 'note'];

    public function sellerOrder(): BelongsTo { return $this->belongsTo(SellerOrder::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
