<?php

namespace App\Models\Buyer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequest extends Model
{
    protected $fillable = ['seller_order_id', 'buyer_id', 'reason', 'details', 'photos', 'status', 'seller_response', 'admin_decision', 'resolved_by', 'cod_repayment_status', 'cod_repayment_reference', 'stock_restored_at'];
    protected function casts(): array { return ['photos' => 'array', 'stock_restored_at' => 'datetime']; }
    public function sellerOrder(): BelongsTo { return $this->belongsTo(SellerOrder::class); }
    public function buyer(): BelongsTo { return $this->belongsTo(User::class, 'buyer_id'); }
    public function resolver(): BelongsTo { return $this->belongsTo(User::class, 'resolved_by'); }
}
