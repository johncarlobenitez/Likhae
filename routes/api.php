<?php

use App\Http\Controllers\Api\LookupApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\TrackingApiController;
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

    Route::get('/categories', [LookupApiController::class, 'categories'])->name('api.v1.categories.index');
    Route::get('/logistics-centers', [LookupApiController::class, 'logisticsCenters'])->name('api.v1.logistics-centers.index');

    Route::get('/products', [ProductApiController::class, 'index'])->name('api.v1.products.index');
    Route::get('/products/{slug}', [ProductApiController::class, 'show'])->name('api.v1.products.show');
    Route::get('/stores/{seller}', [ProductApiController::class, 'store'])->name('api.v1.stores.show');

    Route::get('/tracking/{trackingNumber}', [TrackingApiController::class, 'show'])->name('api.v1.tracking.show');
});

Route::fallback(function (Request $request) {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found.',
        'path' => $request->path(),
    ], 404);
});
