<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Seller\Product;
use App\Services\Marketplace\ProductCatalogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestMarketplaceController extends Controller
{
    public function __construct(private readonly ProductCatalogService $catalog) {}

    public function home(Request $request): View
    {
        $products = $this->catalog->paginated($request, 12);

        return view('guest.home', [
            'products' => $products,
            'buyerProducts' => $products->getCollection()->map(fn (Product $product) => $this->catalog->productPayload($product)),
            'categories' => $this->catalog->categories(),
        ]);
    }

    public function products(Request $request): View
    {
        $products = $this->catalog->paginated($request, 24);

        return view('guest.products', [
            'products' => $products,
            'buyerProducts' => $products->getCollection()->map(fn (Product $product) => $this->catalog->productPayload($product)),
            'categories' => $this->catalog->categories(),
        ]);
    }

    public function show(string $slug): View
    {
        $product = $this->catalog->findVisibleBySlug($slug);

        return view('guest.product-details', [
            'productModel' => $product,
            'product' => $this->catalog->productPayload($product, true),
            'relatedProducts' => $this->catalog->visibleQuery()
                ->whereKeyNot($product->id)
                ->where('category_id', $product->category_id)
                ->limit(4)
                ->get()
                ->map(fn (Product $item) => $this->catalog->productPayload($item)),
        ]);
    }
}
