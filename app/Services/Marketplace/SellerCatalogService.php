<?php

namespace App\Services\Marketplace;

use App\Http\Requests\SaveSellerProductRequest;
use App\Models\Seller\Product;
use App\Models\Seller\ProductImage;
use App\Models\Seller\ProductOptionValue;
use App\Models\Seller\ProductVariant;
use App\Models\Seller\SellerProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SellerCatalogService
{
    public function save(SellerProfile $seller, SaveSellerProductRequest $request, ?Product $product = null): Product
    {
        return DB::transaction(function () use ($seller, $request, $product): Product {
            $product ??= new Product(['seller_profile_id' => $seller->id]);

            if ((int) $product->seller_profile_id !== (int) $seller->id) {
                abort(403);
            }

            $status = strtoupper((string) $request->input('status', 'DRAFT'));

            $product->fill([
                'seller_profile_id' => $seller->id,
                'category_id' => (int) $request->input('category_id'),
                'name' => $request->string('name')->toString(),
                'slug' => $this->uniqueSlug($request->string('name')->toString(), $product),
                'description' => $request->input('description'),
                'status' => $status,
                'published_at' => $status === 'ACTIVE' ? ($product->published_at ?: now()) : $product->published_at,
                'archived_at' => $status === 'ARCHIVED' ? now() : null,
            ]);

            $product->save();

            $optionValuesByName = $this->syncOptions($product, (array) $request->input('options', []));
            $this->syncVariants($product, $request, $optionValuesByName);
            $this->storeImages($seller, $product, $request->file('image'), $request->file('images', []));

            return $product->load(['category', 'images', 'options.values', 'variants.optionValues.option']);
        });
    }

    public function archiveOrRestore(SellerProfile $seller, Product $product): Product
    {
        abort_unless((int) $product->seller_profile_id === (int) $seller->id, 403);

        $newStatus = $product->status === 'ARCHIVED' ? 'ACTIVE' : 'ARCHIVED';
        $product->update([
            'status' => $newStatus,
            'archived_at' => $newStatus === 'ARCHIVED' ? now() : null,
            'published_at' => $newStatus === 'ACTIVE' ? ($product->published_at ?: now()) : $product->published_at,
        ]);

        return $product;
    }

    public function updateSingleStock(SellerProfile $seller, Product $product, int $stock): void
    {
        abort_unless((int) $product->seller_profile_id === (int) $seller->id, 403);
        abort_unless($product->variants()->where('is_active', true)->count() === 1, 409, 'Edit variant stock from the product editor.');

        $product->variants()->where('is_active', true)->firstOrFail()->update(['stock' => $stock]);
    }

    /** @return array<string, ProductOptionValue> */
    private function syncOptions(Product $product, array $optionRows): array
    {
        $valuesByName = [];
        $sort = 0;

        foreach ($optionRows as $row) {
            $name = trim((string) Arr::get($row, 'name'));
            $rawValues = trim((string) Arr::get($row, 'values'));

            if ($name === '' || $rawValues === '') {
                continue;
            }

            $option = $product->options()->updateOrCreate(
                ['name' => $name],
                ['sort_order' => $sort++],
            );

            $valueSort = 0;
            foreach ($this->splitValues($rawValues) as $value) {
                $valueModel = $option->values()->updateOrCreate(
                    ['value' => $value],
                    ['sort_order' => $valueSort++],
                );
                $valuesByName[mb_strtolower($value)] = $valueModel;
            }
        }

        return $valuesByName;
    }

    /** @param array<string, ProductOptionValue> $optionValuesByName */
    private function syncVariants(Product $product, SaveSellerProductRequest $request, array $optionValuesByName): void
    {
        $variantRows = collect((array) $request->input('variants', []))
            ->filter(fn ($row) => filled(Arr::get($row, 'sku')) || filled(Arr::get($row, 'values')) || filled(Arr::get($row, 'price')) || filled(Arr::get($row, 'stock')))
            ->values();

        if ($variantRows->isEmpty() && $optionValuesByName !== []) {
            $variantRows = $this->cartesianVariantRows($product, $request, $optionValuesByName);
        }

        if ($variantRows->isEmpty()) {
            $variantRows = collect([[
                'sku' => $request->input('sku') ?: 'LK-'.$product->id.'-DEFAULT',
                'price' => $request->input('price'),
                'stock' => $request->input('stock'),
                'values' => '',
                'is_active' => true,
            ]]);
        }

        $keptIds = [];
        $index = 0;

        foreach ($variantRows as $row) {
            $sku = trim((string) Arr::get($row, 'sku')) ?: 'LK-'.$product->id.'-'.Str::upper(Str::random(6));
            $variantId = Arr::get($row, 'id');

            $duplicateSku = ProductVariant::query()
                ->where('sku', $sku)
                ->when($variantId, fn ($query) => $query->whereKeyNot((int) $variantId))
                ->exists();

            if ($duplicateSku) {
                throw ValidationException::withMessages([
                    'variants' => "Variant SKU {$sku} is already used.",
                ]);
            }

            $variant = $variantId
                ? $product->variants()->whereKey((int) $variantId)->first()
                : null;

            $variant ??= new ProductVariant(['product_id' => $product->id]);
            $variant->fill([
                'product_id' => $product->id,
                'sku' => $sku,
                'price' => (float) (Arr::get($row, 'price') ?: $request->input('price')),
                'stock' => (int) (Arr::get($row, 'stock') ?? $request->input('stock')),
                'is_default' => $index === 0,
                'is_active' => filter_var(Arr::get($row, 'is_active', true), FILTER_VALIDATE_BOOLEAN),
            ]);
            $variant->save();

            $valueIds = $this->variantValueIds((string) Arr::get($row, 'values', ''), $optionValuesByName);
            $variant->optionValues()->sync($valueIds);
            $keptIds[] = $variant->id;
            $index++;
        }

        $product->variants()->whereNotIn('id', $keptIds)->update(['is_active' => false, 'is_default' => false]);
    }

    /** @return Collection<int, array<string, mixed>> */
    private function cartesianVariantRows(Product $product, SaveSellerProductRequest $request, array $optionValuesByName): Collection
    {
        $options = $product->options()->with('values')->orderBy('sort_order')->get();
        $sets = $options->map(fn ($option) => $option->values->pluck('value')->all())->filter()->values()->all();

        if ($sets === []) {
            return collect();
        }

        $combinations = [[]];
        foreach ($sets as $set) {
            $next = [];
            foreach ($combinations as $combination) {
                foreach ($set as $value) {
                    $next[] = [...$combination, $value];
                }
            }
            $combinations = $next;
        }

        return collect($combinations)->map(function (array $values) use ($product, $request): array {
            $suffix = collect($values)->map(fn ($value) => Str::slug($value))->implode('-');
            return [
                'sku' => ($request->input('sku') ?: 'LK-'.$product->id).'-'.Str::upper($suffix ?: Str::random(5)),
                'price' => $request->input('price'),
                'stock' => $request->input('stock'),
                'values' => implode(',', $values),
                'is_active' => true,
            ];
        });
    }

    /** @param array<string, ProductOptionValue> $optionValuesByName */
    private function variantValueIds(string $raw, array $optionValuesByName): array
    {
        if ($raw === '') {
            return [];
        }

        return collect($this->splitValues($raw))
            ->map(fn ($value) => $optionValuesByName[mb_strtolower($value)]->id ?? null)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /** @return array<int, string> */
    private function splitValues(string $raw): array
    {
        return collect(preg_split('/[,|]+/', $raw) ?: [])
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn ($value) => $value !== '')
            ->unique(fn ($value) => mb_strtolower($value))
            ->values()
            ->all();
    }

    private function storeImages(SellerProfile $seller, Product $product, ?UploadedFile $mainImage, array|UploadedFile|null $extraImages): void
    {
        $files = collect([$mainImage])
            ->merge(is_array($extraImages) ? $extraImages : [$extraImages])
            ->filter(fn ($file) => $file instanceof UploadedFile && $file->isValid())
            ->values();

        if ($files->isEmpty()) {
            return;
        }

        $existing = $product->images()->count();
        if ($existing + $files->count() > 8) {
            throw ValidationException::withMessages([
                'images' => 'A product may have at most 8 images.',
            ]);
        }

        foreach ($files as $file) {
            $sort = (int) $product->images()->max('sort_order') + 1;
            $path = $file->store("sellers/{$seller->id}/products/{$product->id}", 'public');

            $product->images()->create([
                'file_path' => $path,
                'alt_text' => $product->name,
                'is_primary' => $product->images()->count() === 0,
                'sort_order' => $sort,
            ]);
        }
    }

    public function deleteImage(SellerProfile $seller, Product $product, ProductImage $image): void
    {
        abort_unless((int) $product->seller_profile_id === (int) $seller->id, 403);
        abort_unless((int) $image->product_id === (int) $product->id, 403);

        if (filled($image->file_path)) {
            Storage::disk('public')->delete($image->file_path);
        }

        $image->delete();
    }

    private function uniqueSlug(string $name, Product $product): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $counter = 2;

        while (Product::withTrashed()->where('slug', $slug)->whereKeyNot($product->id ?? 0)->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
