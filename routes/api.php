<?php

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LIKHAE Mobile API
|--------------------------------------------------------------------------
|
| Flutter currently supports:
|
| - Buyer
| - Rider
|
| Seller, Admin, and Logistics continue to use the existing Laravel web
| application.
|
*/

Route::prefix('v1')->group(function (): void {

    /*
    |--------------------------------------------------------------------------
    | Health Check
    |--------------------------------------------------------------------------
    |
    | GET /api/v1/health
    |
    */

    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' =>
                'LIKHAE API is running.',
            'timestamp' =>
                now()->toIso8601String(),
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Public Marketplace
    |--------------------------------------------------------------------------
    |
    | These endpoints reuse the existing LIKHAE Product::visible() rules and
    | BuyerMarketplace product mapper.
    |
    */

    Route::get(
        '/products',
        [
            ProductApiController::class,
            'index',
        ]
    )->name('api.v1.products.index');

    Route::get(
        '/products/{slug}',
        [
            ProductApiController::class,
            'show',
        ]
    )->name('api.v1.products.show');

    Route::get(
        '/stores/{seller}',
        [
            ProductApiController::class,
            'store',
        ]
    )->name('api.v1.stores.show');

    /*
    |--------------------------------------------------------------------------
    | Mobile Authentication
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')
        ->group(function (): void {

            /*
            |------------------------------------------------------------------
            | Public Login
            |------------------------------------------------------------------
            */

            Route::post(
                '/login',
                [
                    MobileAuthController::class,
                    'login',
                ]
            )->name(
                'api.v1.auth.login'
            );

            /*
            |------------------------------------------------------------------
            | Protected Authentication Routes
            |------------------------------------------------------------------
            */

            Route::middleware(
                'auth:sanctum'
            )->group(function (): void {

                Route::get(
                    '/me',
                    [
                        MobileAuthController::class,
                        'me',
                    ]
                )->name(
                    'api.v1.auth.me'
                );

                Route::post(
                    '/logout',
                    [
                        MobileAuthController::class,
                        'logout',
                    ]
                )->name(
                    'api.v1.auth.logout'
                );
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Buyer Protected Mobile API
    |--------------------------------------------------------------------------
    |
    | Next:
    |
    | /api/v1/buyer/wishlist
    | /api/v1/buyer/cart
    | /api/v1/buyer/checkout
    | /api/v1/buyer/orders
    | /api/v1/buyer/messages
    | /api/v1/buyer/notifications
    | /api/v1/buyer/rewards
    | /api/v1/buyer/account
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Rider Protected Mobile API
    |--------------------------------------------------------------------------
    |
    | Later these endpoints will reuse the existing two-leg shipment workflow:
    |
    | Seller
    |   ↓
    | Pickup Rider
    |   ↓
    | Logistics Hub
    |   ↓
    | Delivery Rider
    |   ↓
    | Buyer
    |
    */
});


/*
|--------------------------------------------------------------------------
| API Fallback
|--------------------------------------------------------------------------
|
| Unknown API endpoints must return JSON rather than Laravel HTML.
|
*/

Route::fallback(function (Request $request) {
    return response()->json([
        'success' => false,
        'message' =>
            'API endpoint not found.',
        'path' =>
            $request->path(),
    ], 404);
});