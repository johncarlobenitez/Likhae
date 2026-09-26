<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveSellerProductRequest;
use App\Models\Seller\Category;
use App\Models\Seller\Product;
use App\Models\Seller\ProductImage;
use App\Models\Seller\ProductVariant;
use App\Models\Seller\SellerProfile;
use App\Services\Marketplace\ProductCatalogService;
use App\Services\Marketplace\SellerCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SellerProductController extends Controller
{
    public function __construct(
        private readonly SellerCatalogService $sellerCatalog,
        private readonly ProductCatalogService $catalog,
    ) {}

    public function index(Request $request): View
    {
        $seller = $this->seller($request);
        $products = $seller->products()
            ->with(['category', 'images', 'options.values', 'variants.optionValues.option', 'orderItems.review'])
            ->latest()
            ->get();

        $selected = null;
        if ($request->filled('product')) {
            $selected = $products->firstWhere('id', (int) $request->query('product'));
        }

        return view('Seller.products', [
            'mode' => $request->query('mode', 'list'),
            'seller' => $seller,
            'products' => $products,
            'sellerProducts' => $products->map(fn (Product $product) => $this->viewData($product)),
            'selectedProduct' => $selected,
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(SaveSellerProductRequest $request): RedirectResponse
    {
        $product = $this->sellerCatalog->save($this->seller($request), $request);

        return redirect()
            ->route('seller.products', ['mode' => 'edit', 'product' => $product->id])
            ->with('status', 'Product saved to the final 57-table catalog.');
    }

    public function update(SaveSellerProductRequest $request, Product $product): RedirectResponse
    {
        $this->sellerCatalog->save($this->seller($request), $request, $product);

        return back()->with('status', 'Product updated.');
    }

    public function stock(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'stock' => ['required', 'integer', 'min:0', 'max:999999'],
        ]);

        $this->sellerCatalog->updateSingleStock($this->seller($request), $product, (int) $data['stock']);

        return back()->with('status', 'Stock updated.');
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $updated = $this->sellerCatalog->archiveOrRestore($this->seller($request), $product);

        return back()->with('status', $updated->status === 'ARCHIVED' ? 'Product archived.' : 'Product restored.');
    }

    public function deleteImage(Request $request, Product $product, ProductImage $image): RedirectResponse
    {
        $this->sellerCatalog->deleteImage($this->seller($request), $product, $image);

        return back()->with('status', 'Product image removed.');
    }

    public function export(Request $request): StreamedResponse
    {
        $products = $this->seller($request)
            ->products()
            ->with(['category', 'variants'])
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($products): void {
            $out = fopen('php://output', 'wb');
            fputcsv($out, ['ID', 'Product', 'Category', 'Minimum Price', 'Total Stock', 'Status']);

            foreach ($products as $product) {
                $activeVariants = $product->variants->where('is_active', true);
                fputcsv($out, [
                    $product->id,
                    $product->name,
                    $product->category?->name,
                    number_format((float) $activeVariants->min('price'), 2, '.', ''),
                    $activeVariants->sum('stock'),
                    $product->status,
                ]);
            }

            fclose($out);
        }, 'seller-products-'.now()->format('Ymd-His').'.csv');
    }

    private function seller(Request $request): SellerProfile
    {
        return $request->user()
            ->sellerProfile()
            ->where('status', 'ACTIVE')
            ->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function viewData(Product $product): array
    {
        $product->loadMissing(['category', 'images', 'variants.optionValues.option']);
        $activeVariants = $product->variants->where('is_active', true)->values();
        $firstImage = $product->images->sortByDesc('is_primary')->first();

        return [
            'db_id' => $product->id,
            'id' => (string) $product->id,
            'sku' => $activeVariants->pluck('sku')->filter()->join(', '),
            'name' => $product->name,
            'category' => $product->category?->name ?? 'Uncategorized',
            'category_id' => $product->category_id,
            'price' => (float) ($activeVariants->min('price') ?? 0),
            'stock' => (int) $activeVariants->sum('stock'),
            'sold' => (int) $product->orderItems->sum('quantity'),
            'rating' => (float) $product->orderItems->pluck('review.rating')->filter()->avg(),
            'status' => $product->status,
            'description' => $product->description,
            'image' => $firstImage?->file_path ? Storage::url($firstImage->file_path) : null,
            'variation_rows' => $activeVariants->map(fn (ProductVariant $variant): array => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'stock' => $variant->stock,
                'values' => $variant->optionValues->pluck('value')->join(', '),
                'description' => $variant->description,
                'is_active' => $variant->is_active,
            ])->all(),
        ];
    }
}
