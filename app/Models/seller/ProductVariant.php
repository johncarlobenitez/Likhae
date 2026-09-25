<?php

namespace App\Models\Seller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = ['product_id', 'sku', 'name', 'options', 'price_minor', 'stock', 'weight_grams', 'is_active'];

    protected function casts(): array
    {
        return ['options' => 'array', 'is_active' => 'boolean', 'weight_grams' => 'integer'];
    }

    public function getOptionNameAttribute(): string
    {
        return (string) (Arr::first(array_keys($this->options ?? [])) ?: Str::before($this->name, ' / '));
    }

    public function getValueAttribute(): string
    {
        return (string) (Arr::first(array_values($this->options ?? [])) ?: Str::after($this->name, ' / '));
    }

    public function getPriceAttribute(): string
    {
        return number_format($this->price_minor / 100, 2, '.', '');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
