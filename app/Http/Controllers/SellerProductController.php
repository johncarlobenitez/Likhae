<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveSellerProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SellerProductController extends Controller
{
    public function index(Request $request): View
    {
        $seller = $this->seller($request);
        $products = $seller->products()->with(['category.parent', 'variants', 'images', 'specifications', 'orderItems', 'reviews'])->latest()->get();

        return view('Seller.products', [
            'mode' => $request->input('mode', 'list'),
            'selectedProduct' => $request->input('product'),
            'sellerProducts' => $products->map(fn (Product $product) => $this->viewData($product)),
            'inventoryProducts' => $request->input('mode') === 'inventory'
                ? $seller->products()->with(['category', 'variants', 'images'])->latest()->get()->map(fn (Product $product) => $this->viewData($product))
                : collect(),
            'lineOfBusinessCategory' => null,
            'categories' => Category::query()->whereNotNull('parent_id')->where('is_active', true)
                ->whereHas('parent', fn ($query) => $query->where('is_active', true))->with('parent')->orderBy('name')->get(),
        ]);
    }

    public function store(SaveSellerProductRequest $request): RedirectResponse
    {
        $product = DB::transaction(function () use ($request) {
            $seller = $this->seller($request);
            $product = $seller->products()->create($this->productAttributes($request));
            $this->syncVariants($product, $request);
            $this->syncSpecifications($product, $request);
            $this->storeImages($product, $request);
            return $product;
        });

        return redirect()->route('seller.products', ['mode' => 'inventory'])->with('status', 'Product published successfully and added to inventory.');
    }

    public function update(SaveSellerProductRequest $request, Product $product): RedirectResponse
    {
        $this->owns($request, $product);
        DB::transaction(function () use ($request, $product) {
            $product->update($this->productAttributes($request, $product));
            $this->syncVariants($product, $request);
            $this->syncSpecifications($product, $request);
            $this->storeImages($product, $request);
        });

        return back()->with('status', 'Product changes saved.');
    }

    public function stock(Request $request, Product $product): RedirectResponse
    {
        $this->owns($request, $product);
        $data = $request->validate(['stock' => ['required', 'integer', 'min:0', 'max:999999']]);
        abort_unless($product->variants()->count() === 1, 409, 'Edit each variant stock from the product editor.');
        $product->variants()->firstOrFail()->update(['stock' => $data['stock']]);
        return back()->with('status', 'Stock updated.');
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $this->owns($request, $product);
        $product->update(['is_active' => ! $product->is_active]);
        return back()->with('status', $product->is_active ? 'Product restored.' : 'Product archived.');
    }

    public function export(Request $request): StreamedResponse
    {
        $products = $this->seller($request)->products()->with(['category', 'variants'])->latest()->get();
        return response()->streamDownload(function () use ($products) {
            $out = fopen('php://output', 'wb');
            fputcsv($out, ['ID', 'Product', 'Category', 'Minimum price', 'Stock', 'Status']);
            foreach ($products as $product) {
                fputcsv($out, [$product->id, $product->name, $product->category?->name, number_format($product->min_price_minor / 100, 2, '.', ''), $product->variants->sum('stock'), $product->is_active ? 'Active' : 'Archived']);
            }
            fclose($out);
        }, 'seller-products-'.now()->format('Ymd-His').'.csv');
    }

    private function seller(Request $request): Seller
    {
        return $request->user()->sellers()->where('status', 'approved')->firstOrFail();
    }

    private function owns(Request $request, Product $product): void
    {
        abort_unless($product->seller_id === $this->seller($request)->id, 403);
    }

    private function productAttributes(SaveSellerProductRequest $request, ?Product $product = null): array
    {
        $base = Str::slug($request->string('name')->toString()) ?: 'product';
        $slug = $base;
        for ($i = 2; Product::withTrashed()->where('slug', $slug)->when($product, fn ($q) => $q->whereKeyNot($product->id))->exists(); $i++) $slug = $base.'-'.$i;
        return ['category_id' => $request->integer('category_id'), 'name' => $request->string('name')->toString(), 'slug' => $slug,
            'description' => $request->string('description')->toString(), 'is_active' => $request->input('listing_status') === 'active'];
    }

    private function syncVariants(Product $product, SaveSellerProductRequest $request): void
    {
        $rows = [];
        foreach ($request->input('variation_name', []) as $i => $name) {
            $name = trim((string) $name); $value = trim((string) $request->input("variation_value.$i"));
            if ($name === '' || $value === '') continue;
            $rows[] = ['id' => $request->input("variation_id.$i"), 'name' => "$name / $value", 'options' => [$name => $value],
                'sku' => trim((string) $request->input("variation_sku.$i")) ?: 'LK-'.$product->id.'-'.strtoupper(Str::random(6)),
                'price_minor' => $this->minor($request->input("variation_price.$i") ?: $request->input('price')),
                'stock' => (int) ($request->input("variation_stock.$i") ?? $request->input('stock')),
                'weight_grams' => (int) ($request->input("variation_weight_grams.$i") ?: $request->integer('weight_grams')), 'is_active' => true];
        }
        if ($rows === []) $rows[] = ['id' => $product->variants()->value('id'), 'name' => 'Default', 'options' => [], 'sku' => $product->variants()->value('sku') ?: 'LK-'.$product->id.'-DEFAULT', 'price_minor' => $this->minor($request->input('price')), 'stock' => $request->integer('stock'), 'weight_grams' => $request->integer('weight_grams'), 'is_active' => true];

        $kept = [];
        foreach ($rows as $row) {
            $id = $row['id']; unset($row['id']);
            if (ProductVariant::where('sku', $row['sku'])->when($id, fn ($q) => $q->whereKeyNot($id))->exists()) throw ValidationException::withMessages(['variation_sku' => 'Every variant SKU must be unique.']);
            $variant = $id ? $product->variants()->withTrashed()->whereKey($id)->firstOrFail() : new ProductVariant(['product_id' => $product->id]);
            $variant->fill($row); $variant->deleted_at = null; $variant->save(); $kept[] = $variant->id;
        }
        $product->variants()->whereNotIn('id', $kept)->delete();
        $product->update(['min_price_minor' => min(array_column($rows, 'price_minor'))]);
    }

    private function syncSpecifications(Product $product, SaveSellerProductRequest $request): void
    {
        $product->specifications()->delete();
        foreach (preg_split('/\R+/', trim((string) $request->input('specifications_text'))) as $line) {
            if (! str_contains($line, ':')) continue;
            [$name, $value] = array_map('trim', explode(':', $line, 2));
            if ($name !== '' && $value !== '') $product->specifications()->create(['name' => Str::limit($name, 120, ''), 'value' => Str::limit($value, 500, '')]);
        }
    }

    private function storeImages(Product $product, SaveSellerProductRequest $request): void
    {
        $files = array_values(array_filter([
            $request->file('image'),
            ...($request->file('images') ?? []),
        ], fn ($file) => $file !== null && $file->isValid()));
        if ($product->images()->count() + count($files) > 8) throw ValidationException::withMessages(['images' => 'A product may have at most 8 images.']);
        foreach ($files as $file) {
            $path = $file->store("sellers/{$product->seller_id}/products/{$product->id}", 'public');
            $product->images()->create(['path' => $path, 'sort_order' => (int) $product->images()->max('sort_order') + 1]);
        }
    }

    private function minor(string|int|float $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', (string) $amount, 2), 2, '');
        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }

    private function viewData(Product $product): array
    {
        $variants = $product->variants;
        return ['db_id' => $product->id, 'id' => (string) $product->id, 'sku' => $variants->pluck('sku')->filter()->join(', '), 'name' => $product->name,
            'category' => $product->category?->name ?? 'Uncategorized', 'category_id' => $product->category_id, 'price' => $product->min_price_minor / 100,
            'stock' => $variants->sum('stock'), 'sold' => $product->orderItems->sum('quantity'), 'rating' => (float) $product->reviews->avg('rating'),
            'status' => $product->is_active ? 'Active' : 'Archived', 'description' => $product->description,
            'image' => ($path = $product->images->first()?->path) ? Storage::url($path) : null,
            'weight_grams' => (int) ($variants->first()?->weight_grams ?? 1),
            'variation_rows' => $variants->map(fn ($v) => ['id' => $v->id, 'name' => $v->option_name, 'value' => $v->value, 'sku' => $v->sku, 'price' => $v->price, 'stock' => $v->stock, 'weight_grams' => $v->weight_grams])->all(),
            'specification_rows' => $product->specifications->map->only(['name', 'value'])->all()];
    }
}
