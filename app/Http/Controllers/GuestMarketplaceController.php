<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuestMarketplaceController extends Controller
{
    public function home(): View
    {
        return view('guest.home', ['buyerProducts' => $this->productRows()]);
    }

    public function products(): View
    {
        return view('guest.products', ['buyerProducts' => $this->productRows()]);
    }

    public function show(string $slug): View
    {
        $product = $this->query()->where('slug', $slug)->firstOrFail();
        return view('guest.product-details', [
            'product' => $this->previewProduct($product),
            'buyerProducts' => $this->productRows(),
        ]);
    }

    private function productRows()
    {
        if (! Schema::hasTable('products') || ! Schema::hasTable('users')) {
            return collect();
        }

        return $this->query()->latest()->get()->map(fn (Product $product) => $this->previewProduct($product));
    }

    private function query()
    {
        return Product::query()
            ->with(['seller', 'category.parent'])
            ->where('stock', '>', 0)
            ->where(function ($query) {
                $query->where('listing_status', 'active')->orWhere(function ($q) {
                    $q->whereNull('listing_status')->where('status', 'active');
                });
            })
            ->where(function ($query) {
                $query->whereNull('admin_status')->orWhere('admin_status', 'approved');
            })
            ->whereHas('seller', fn ($query) => $query->where('role', 'seller')->where('status', 'active'))
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')->from('seller_profiles')
                    ->whereColumn('seller_profiles.seller_id', 'products.seller_id')
                    ->where(function ($profile) {
                        $profile->where('seller_profiles.store_visibility', false)
                            ->orWhere('seller_profiles.vacation_mode', true);
                    });
            });
    }

    private function previewProduct(Product $product): array
    {
        $sellerName = $product->seller?->store_name
            ?: $product->seller?->business_name
            ?: $product->seller?->name
            ?: 'LIKHAE Seller';

        $image = $product->image_path
            ? (Str::startsWith($product->image_path, ['http://', 'https://'])
                ? $product->image_path
                : Storage::url($product->image_path))
            : asset('images/product-placeholder.svg');

        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'category' => $product->category?->name ?: 'Uncategorized',
            'category_slug' => $product->category?->slug ?: Str::slug($product->category?->name ?: 'uncategorized'),
            'parent_category' => $product->category?->parent?->name,
            'parent_category_slug' => $product->category?->parent?->slug,
            'seller' => $sellerName,
            'seller_slug' => Str::slug($sellerName).'-'.$product->seller_id,
            'location' => $this->publicLocation($product),
            'image' => $image,
            'image_url' => $image,
            'gallery' => [$image],
            'description' => Str::limit(strip_tags((string) $product->description), 220),
        ];
    }

    private function publicLocation(Product $product): string
    {
        return collect([
            $product->seller?->municipality,
            $product->seller?->province,
        ])->filter()->implode(', ') ?: 'Philippines';
    }
}
