<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seller\Product;
use App\Services\Marketplace\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function __construct(private readonly ProductCatalogService $catalog) {}

    public function index(Request $request): JsonResponse
    {
        $products = $this->catalog->paginated($request, (int) min(max($request->integer('per_page', 20), 1), 50));

        return response()->json([
            'success' => true,
            'data' => $products->getCollection()
                ->map(fn (Product $product): array => $this->catalog->productPayload($product))
                ->values(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->catalog->productPayload($this->catalog->findVisibleBySlug($slug), true),
        ]);
    }

    public function store(string $seller, Request $request): JsonResponse
    {
        $sellerProfile = $this->catalog->sellerByRouteKey($seller);
        $query = Product::query()
            ->visible()
            ->where('seller_profile_id', $sellerProfile->id)
            ->with($this->catalog->productRelations());

        $products = $this->catalog->applySort(
            $this->catalog->applyFilters($query, $request),
            $request->query('sort')
        )->paginate((int) min(max($request->integer('per_page', 20), 1), 50));

        return response()->json([
            'success' => true,
            'data' => [
                'seller' => [
                    'id' => $sellerProfile->id,
                    'business_name' => $sellerProfile->business_name,
                    'category' => $sellerProfile->primaryCategory?->name,
                ],
                'products' => $products->getCollection()
                    ->map(fn (Product $product): array => $this->catalog->productPayload($product))
                    ->values(),
            ],
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }
}
