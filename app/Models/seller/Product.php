<?php

namespace App\Models\Seller;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'seller_id', 'category_id', 'name', 'slug', 'description', 'min_price_minor', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereHas('seller', fn (Builder $seller) => $seller->where('status', 'approved')
                ->where(fn ($settings) => $settings->whereNull('settings->vacation_mode')->orWhere('settings->vacation_mode', false))
                ->where(fn ($settings) => $settings->whereNull('settings->store_visibility')->orWhere('settings->store_visibility', true)))
            ->whereHas('seller.owner', fn (Builder $owner) => $owner->where('status', 'active')->where('is_suspended', false))
            ->whereHas('category', fn (Builder $category) => $category->where('is_active', true)
                ->whereHas('parent', fn (Builder $parent) => $parent->where('is_active', true)))
            ->whereHas('variants', fn (Builder $variant) => $variant->where('is_active', true)->where('stock', '>', 0));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('name');
    }
}
