<?php

namespace App\Models\Buyer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['seller_order_id', 'product_id', 'product_variant_id', 'product_name', 'variant_name', 'sku', 'quantity', 'unit_price_minor', 'subtotal_minor'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function sellerOrder(): BelongsTo
    {
        return $this->belongsTo(SellerOrder::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(ProductReview::class);
    }
}
