<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Seller;
use App\Support\BuyerMarketplace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductApiController extends Controller
{
    /**
     * GET /api/v1/products
     *
     * Return marketplace products that already satisfy the same
     * visibility rules used by the existing LIKHAE Buyer website.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:160',
            ],
            'category' => [
                'nullable',
                'string',
                'max:255',
            ],
            'seller' => [
                'nullable',
                'string',
                'max:255',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],
        ]);

        $query = $this->marketplaceQuery();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = trim(
            (string) ($validated['search'] ?? '')
        );

        if ($search !== '') {
            $query->where(function (Builder $productQuery) use ($search): void {
                $productQuery
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere(
                        'description',
                        'like',
                        '%'.$search.'%'
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        |
        | Accept either the child category slug or its parent category slug.
        |
        */

        $category = trim(
            (string) ($validated['category'] ?? '')
        );

        if ($category !== '') {
            $query->whereHas(
                'category',
                function (Builder $categoryQuery) use ($category): void {
                    $categoryQuery
                        ->where('slug', $category)
                        ->orWhereHas(
                            'parent',
                            fn (Builder $parentQuery) =>
                                $parentQuery->where(
                                    'slug',
                                    $category
                                )
                        );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Seller
        |--------------------------------------------------------------------------
        |
        | Accept:
        |
        | - real seller slug
        | - seller database ID
        | - existing LIKHAE generated slug ending in "-{seller_id}"
        |
        */

        $seller = trim(
            (string) ($validated['seller'] ?? '')
        );

        if ($seller !== '') {
            $sellerId = $this->sellerIdFromInput($seller);

            if ($sellerId === null) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'meta' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => (int) (
                            $validated['per_page'] ?? 20
                        ),
                        'total' => 0,
                    ],
                ]);
            }

            $query->where(
                'seller_id',
                $sellerId
            );
        }

        $perPage = (int) (
            $validated['per_page'] ?? 20
        );

        $products = $query
            ->latest('products.created_at')
            ->paginate($perPage);

        $data = collect(
            $products->items()
        )
            ->map(
                fn (Product $product) =>
                    $this->productPayload($product)
            )
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' =>
                    $products->currentPage(),

                'last_page' =>
                    $products->lastPage(),

                'per_page' =>
                    $products->perPage(),

                'total' =>
                    $products->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/products/{slug}
     *
     * Return one visible/purchasable marketplace product.
     */
    public function show(
        string $slug
    ): JsonResponse {
        $product = $this->marketplaceQuery()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->productPayload(
                $product
            ),
        ]);
    }

    /**
     * GET /api/v1/stores/{seller}
     *
     * Return one approved seller together with currently visible products.
     */
    public function store(
        string $seller
    ): JsonResponse {
        $shop = Seller::query()
            ->with([
                'owner',
                'pickupAddress',
            ])
            ->where(
                'status',
                'approved'
            )
            ->whereHas(
                'owner',
                fn (Builder $ownerQuery) =>
                    $ownerQuery->where(
                        'status',
                        'active'
                    )
            )
            ->where(function (Builder $query) use ($seller): void {
                $query->where(
                    'slug',
                    $seller
                );

                if (ctype_digit($seller)) {
                    $query->orWhereKey(
                        (int) $seller
                    );
                }

                if (
                    preg_match(
                        '/-(\d+)$/',
                        $seller,
                        $match
                    )
                ) {
                    $query->orWhereKey(
                        (int) $match[1]
                    );
                }
            })
            ->firstOrFail();

        $products = $this->marketplaceQuery()
            ->where(
                'seller_id',
                $shop->id
            )
            ->latest('products.created_at')
            ->get();

        $mappedProducts = $products
            ->map(
                fn (Product $product) =>
                    $this->productPayload($product)
            )
            ->values();

        $reviewCount = $mappedProducts->sum(
            fn (array $product) =>
                (int) ($product['reviews'] ?? 0)
        );

        $rating = null;

        if ($reviewCount > 0) {
            $weightedRatingTotal =
                $mappedProducts->sum(
                    fn (array $product) =>
                        ((float) (
                            $product['rating'] ?? 0
                        )) *
                        ((int) (
                            $product['reviews'] ?? 0
                        ))
                );

            $rating = round(
                $weightedRatingTotal /
                $reviewCount,
                1
            );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'seller' => [
                    'id' => $shop->id,
                    'user_id' => $shop->user_id,
                    'name' => $shop->name,
                    'slug' => $shop->slug,
                    'description' =>
                        $shop->description,

                    'logo_url' =>
                        $this->publicFileUrl(
                            $shop->logo_path
                        ),

                    'banner_url' =>
                        $this->publicFileUrl(
                            $shop->banner_path
                        ),

                    'location' =>
                        $this->sellerLocation(
                            $shop
                        ),

                    'joined_year' =>
                        $shop->created_at?->format(
                            'Y'
                        ),

                    'rating' => $rating,

                    'review_count' =>
                        $reviewCount,
                ],

                'products' =>
                    $mappedProducts->all(),
            ],
        ]);
    }

    /**
     * Use the exact same marketplace eligibility rules and eager-loaded
     * relationships already used by the existing Buyer website.
     */
    private function marketplaceQuery(): Builder
    {
        return Product::visible()
            ->with([
                'seller.owner',
                'seller.pickupAddress',
                'category.parent',
                'variants',
                'images',
                'specifications',
                'reviews.buyer',
                'orderItems.sellerOrder.order',
            ])
            ->whereHas(
                'seller.owner',
                fn (Builder $ownerQuery) =>
                    $ownerQuery->where(
                        'status',
                        'active'
                    )
            );
    }

    /**
     * Reuse BuyerMarketplace::product() so web and mobile use the same
     * price, stock, variation, review, sold-count and seller calculations.
     *
     * Then add API-oriented image metadata and normalize URLs.
     *
     * @return array<string, mixed>
     */
    private function productPayload(
        Product $product
    ): array {
        $payload = BuyerMarketplace::product(
            $product
        );

        /*
        |--------------------------------------------------------------------------
        | Normalize existing mapper URLs
        |--------------------------------------------------------------------------
        */

        $payload['image'] =
            $this->absoluteUrl(
                $payload['image'] ?? null
            );

        $payload['image_url'] =
            $this->absoluteUrl(
                $payload['image_url'] ?? null
            );

        $payload['seller_avatar'] =
            $this->absoluteUrl(
                $payload['seller_avatar'] ?? null
            );

        $payload['gallery'] = collect(
            $payload['gallery'] ?? []
        )
            ->map(
                fn ($url) =>
                    $this->absoluteUrl(
                        is_string($url)
                            ? $url
                            : null
                    )
            )
            ->filter()
            ->unique()
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Raw product image metadata
        |--------------------------------------------------------------------------
        |
        | Flutter can use image_url directly while path remains available
        | for debugging/storage-reference purposes.
        |
        */

        $payload['images'] =
            $product->images
                ->map(
                    fn ($image) => [
                        'id' => $image->id,

                        'product_id' =>
                            $image->product_id,

                        'path' =>
                            $image->path,

                        'sort_order' =>
                            (int) $image->sort_order,

                        'image_url' =>
                            $this->publicFileUrl(
                                $image->path
                            ),
                    ]
                )
                ->values()
                ->all();

        return $payload;
    }

    /**
     * Resolve an existing seller ID from the formats already accepted by
     * the web marketplace.
     */
    private function sellerIdFromInput(
        string $value
    ): ?int {
        if (ctype_digit($value)) {
            return Seller::query()
                ->whereKey((int) $value)
                ->where(
                    'status',
                    'approved'
                )
                ->whereHas(
                    'owner',
                    fn (Builder $ownerQuery) =>
                        $ownerQuery->where(
                            'status',
                            'active'
                        )
                )
                ->value('id');
        }

        $sellerId = Seller::query()
            ->where(
                'status',
                'approved'
            )
            ->where(
                'slug',
                $value
            )
            ->whereHas(
                'owner',
                fn (Builder $ownerQuery) =>
                    $ownerQuery->where(
                        'status',
                        'active'
                    )
            )
            ->value('id');

        if ($sellerId) {
            return (int) $sellerId;
        }

        if (
            preg_match(
                '/-(\d+)$/',
                $value,
                $match
            )
        ) {
            return Seller::query()
                ->whereKey(
                    (int) $match[1]
                )
                ->where(
                    'status',
                    'approved'
                )
                ->whereHas(
                    'owner',
                    fn (Builder $ownerQuery) =>
                        $ownerQuery->where(
                            'status',
                            'active'
                        )
                )
                ->value('id');
        }

        return null;
    }

    /**
     * Return seller location using existing stored data only.
     */
    private function sellerLocation(
        Seller $seller
    ): ?string {
        $settingsLocation = trim(
            (string) data_get(
                $seller->settings,
                'location',
                ''
            )
        );

        if ($settingsLocation !== '') {
            return $settingsLocation;
        }

        $address = $seller->pickupAddress;

        if (! $address) {
            return null;
        }

        $location = collect([
            $address->barangay,
            $address->city,
            $address->province,
        ])
            ->filter(
                fn ($part) =>
                    trim((string) $part) !== ''
            )
            ->implode(', ');

        return $location !== ''
            ? $location
            : null;
    }

    /**
     * Convert a public storage path or already-public URL into a URL suitable
     * for the API response.
     */
    private function publicFileUrl(
        ?string $path
    ): ?string {
        if (
            $path === null ||
            trim($path) === ''
        ) {
            return null;
        }

        if (
            Str::startsWith(
                $path,
                [
                    'http://',
                    'https://',
                ]
            )
        ) {
            return $path;
        }

        return $this->absoluteUrl(
            Storage::url($path)
        );
    }

    /**
     * Convert relative URLs such as "/storage/products/example.jpg"
     * into an absolute Laravel URL.
     */
    private function absoluteUrl(
        ?string $value
    ): ?string {
        if (
            $value === null ||
            trim($value) === ''
        ) {
            return null;
        }

        if (
            Str::startsWith(
                $value,
                [
                    'http://',
                    'https://',
                ]
            )
        ) {
            return $value;
        }

        return url(
            '/'.ltrim(
                $value,
                '/'
            )
        );
    }
}