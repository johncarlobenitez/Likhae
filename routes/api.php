<?php

use App\Http\Controllers\Api\LookupApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\TrackingApiController;
use App\Http\Controllers\Api\WorkflowApiController;
use App\Http\Middleware\AuthenticateApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'LIKHAE API is running.',
            'timestamp' => now()->toIso8601String(),
        ]);
    })->name('api.v1.health');

    Route::post('/auth/login', [MobileAuthController::class, 'login'])->middleware('throttle:10,1')->name('api.v1.auth.login');

    Route::get('/categories', [LookupApiController::class, 'categories'])->name('api.v1.categories.index');
    Route::get('/logistics-centers', [LookupApiController::class, 'logisticsCenters'])->name('api.v1.logistics-centers.index');

    Route::get('/products', [ProductApiController::class, 'index'])->name('api.v1.products.index');
    Route::get('/products/{slug}', [ProductApiController::class, 'show'])->name('api.v1.products.show');
    Route::get('/stores/{seller}', [ProductApiController::class, 'store'])->name('api.v1.stores.show');

    Route::get('/tracking/{trackingNumber}', [TrackingApiController::class, 'show'])->name('api.v1.tracking.show');

    Route::middleware(AuthenticateApiToken::class)->group(function (): void {
        Route::get('/auth/me', [MobileAuthController::class, 'me'])->name('api.v1.auth.me');
        Route::post('/auth/logout', [MobileAuthController::class, 'logout'])->name('api.v1.auth.logout');
        Route::patch('/seller/orders/{sellerOrder}', [WorkflowApiController::class, 'sellerOrder'])->name('api.v1.seller.orders.transition');
        Route::patch('/rider/assignments/{assignment}', [WorkflowApiController::class, 'riderAssignment'])->name('api.v1.rider.assignments.transition');
        Route::post('/logistics/shipments/{shipment}/receive', [WorkflowApiController::class, 'receiveParcel'])->name('api.v1.logistics.shipments.receive');
        Route::post('/logistics/shipments/{shipment}/sort', [WorkflowApiController::class, 'sortParcel'])->name('api.v1.logistics.shipments.sort');
        Route::post('/logistics/shipments/{shipment}/assign-rider', [WorkflowApiController::class, 'assignRider'])->name('api.v1.logistics.shipments.assign-rider');
        Route::post('/buyer/orders/{order}/received', [WorkflowApiController::class, 'confirmReceipt'])->name('api.v1.buyer.orders.received');
    });
});

Route::fallback(function (Request $request) {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found.',
        'path' => $request->path(),
    ], 404);
});
