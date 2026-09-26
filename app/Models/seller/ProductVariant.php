<?php

namespace App\Models\Seller;

use App\Models\Buyer\CartItem;
use App\Models\Buyer\OrderItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_option_values',
            'product_variant_id',
            'product_option_value_id',
        )->withTimestamps();
    }

    public function optionValueLinks(): HasMany
    {
        return $this->hasMany(ProductVariantOptionValue::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('stock', '>', 0);
    }

    public function getDescriptionAttribute(): string
    {
        $values = $this->relationLoaded('optionValues')
            ? $this->optionValues
            : $this->optionValues()->with('option')->get();

        return $values
            ->sortBy(fn (ProductOptionValue $value) => $value->option?->sort_order ?? 0)
            ->map(fn (ProductOptionValue $value) => trim(($value->option?->name ? $value->option->name.': ' : '').$value->value))
            ->implode(' / ');
    }
}
