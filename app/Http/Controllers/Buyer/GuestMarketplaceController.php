<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;

use App\Models\Seller\Product;
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
        return Product::visible()
            ->with([
                'seller.owner',
                'seller.pickupAddress',
                'category.parent',
                'variants' => fn ($query) => $query->where('is_active', true)->where('stock', '>', 0),
                'images',
            ])
            ;
    }

    private function previewProduct(Product $product): array
    {
        $seller = $product->seller;
        $sellerName = $seller?->name
            ?: $seller?->owner?->name
            ?: 'LIKHAE Seller';

        $imagePath = $product->images->first()?->path;
        $image = $imagePath
            ? (Str::startsWith($imagePath, ['http://', 'https://'])
                ? $imagePath
                : Storage::url($imagePath))
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
            $product->seller?->pickupAddress?->city,
            $product->seller?->pickupAddress?->province,
        ])->filter()->implode(', ') ?: 'Philippines';
    }
}
