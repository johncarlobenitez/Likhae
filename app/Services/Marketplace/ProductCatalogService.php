<?php

namespace App\Services\Marketplace;

use App\Models\Seller\Category;
use App\Models\Seller\Product;
use App\Models\Seller\ProductImage;
use App\Models\Seller\ProductOptionValue;
use App\Models\Seller\ProductVariant;
use App\Models\Seller\SellerProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ProductCatalogService
{
    /** @return array<int, string> */
    public function productRelations(): array
    {
        return [
            'category',
            'sellerProfile.user',
            'images',
            'options.values',
            'variants.optionValues.option',
        ];
    }

    public function visibleQuery(): Builder
    {
        return Product::query()
            ->visible()
            ->with($this->productRelations());
    }

    public function applyFilters(Builder $query, Request $request): Builder
    {
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $query->where(function (Builder $inner) use ($search): void {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('sellerProfile', fn (Builder $seller) => $seller->where('business_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->query('category_id'));
        }

        if ($request->filled('seller_profile_id')) {
            $query->where('seller_profile_id', (int) $request->query('seller_profile_id'));
        }

        if ($request->boolean('available')) {
            $query->whereHas('variants', fn (Builder $variant) => $variant->where('is_active', true)->where('stock', '>', 0));
        }

        if ($request->filled('min_price')) {
            $query->whereHas('variants', fn (Builder $variant) => $variant->where('price', '>=', (float) $request->query('min_price')));
        }

        if ($request->filled('max_price')) {
            $query->whereHas('variants', fn (Builder $variant) => $variant->where('price', '<=', (float) $request->query('max_price')));
        }

        return $query;
    }

    public function applySort(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'name' => $query->orderBy('name'),
            'price-low' => $query->orderBy(
                ProductVariant::selectRaw('MIN(price)')
                    ->whereColumn('product_variants.product_id', 'products.id')
                    ->where('is_active', true)
            ),
            'price-high' => $query->orderByDesc(
                ProductVariant::selectRaw('MAX(price)')
                    ->whereColumn('product_variants.product_id', 'products.id')
                    ->where('is_active', true)
            ),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };
    }

    public function paginated(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->applySort(
            $this->applyFilters($this->visibleQuery(), $request),
            $request->query('sort')
        );

        return $query->paginate($perPage)->withQueryString();
    }

    public function findVisibleBySlug(string $slug): Product
    {
        return $this->visibleQuery()->where('slug', $slug)->firstOrFail();
    }

    /** @return array<string, mixed> */
    public function productPayload(Product $product, bool $includeDetails = false): array
    {
        $product->loadMissing($this->productRelations());

        $images = $product->images
            ->map(fn (ProductImage $image): array => [
                'id' => $image->id,
                'file_path' => $image->file_path,
                'url' => $this->publicUrl($image->file_path),
                'alt_text' => $image->alt_text,
                'is_primary' => (bool) $image->is_primary,
                'sort_order' => (int) $image->sort_order,
            ])
            ->values();

        $primaryImage = $images->firstWhere('is_primary', true) ?: $images->first();
        $activeVariants = $product->variants->where('is_active', true)->values();
        $minPrice = $activeVariants->min(fn (ProductVariant $variant) => (float) $variant->price);

        $payload = [
            'id' => $product->id,
            'seller_profile_id' => $product->seller_profile_id,
            'category_id' => $product->category_id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $includeDetails ? $product->description : str($product->description ?? '')->limit(180)->toString(),
            'status' => $product->status,
            'min_price' => $minPrice === null ? null : number_format((float) $minPrice, 2, '.', ''),
            'stock' => $activeVariants->sum('stock'),
            'primary_image' => $primaryImage,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'seller' => $product->sellerProfile ? [
                'id' => $product->sellerProfile->id,
                'business_name' => $product->sellerProfile->business_name,
                'status' => $product->sellerProfile->status,
            ] : null,
            'images' => $images,
            'variants' => $activeVariants
                ->map(fn (ProductVariant $variant): array => $this->variantPayload($variant))
                ->values(),
        ];

        if ($includeDetails) {
            $payload['options'] = $product->options
                ->map(fn ($option): array => [
                    'id' => $option->id,
                    'name' => $option->name,
                    'sort_order' => (int) $option->sort_order,
                    'values' => $option->values
                        ->map(fn (ProductOptionValue $value): array => [
                            'id' => $value->id,
                            'value' => $value->value,
                            'sort_order' => (int) $value->sort_order,
                        ])
                        ->values(),
                ])
                ->values();
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public function variantPayload(ProductVariant $variant): array
    {
        $variant->loadMissing('optionValues.option');

        return [
            'id' => $variant->id,
            'sku' => $variant->sku,
            'price' => number_format((float) $variant->price, 2, '.', ''),
            'stock' => (int) $variant->stock,
            'is_default' => (bool) $variant->is_default,
            'is_active' => (bool) $variant->is_active,
            'description' => $variant->description ?: 'Default',
            'option_values' => $variant->optionValues
                ->map(fn (ProductOptionValue $value): array => [
                    'id' => $value->id,
                    'option_id' => $value->product_option_id,
                    'option' => $value->option?->name,
                    'value' => $value->value,
                ])
                ->values(),
        ];
    }

    public function sellerByRouteKey(string|int $seller): SellerProfile
    {
        return SellerProfile::query()
            ->active()
            ->with(['user', 'primaryCategory'])
            ->where(function (Builder $query) use ($seller): void {
                if (ctype_digit((string) $seller)) {
                    $query->whereKey((int) $seller);
                    return;
                }

                $name = str_replace('-', ' ', (string) $seller);
                $query->where('business_name', $name)
                    ->orWhere('business_name', (string) $seller);
            })
            ->firstOrFail();
    }

    public function categories()
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function publicUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::url($path);
    }
}
