<?php

use App\Http\Middleware\EnsureWorkspaceRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerApplicationController;
use App\Http\Controllers\SellerOrderController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\SellerOperationsController;
use App\Http\Controllers\SellerAccountController;
use App\Http\Controllers\SellerEngagementController;

Route::get('/seller', [SellerApplicationController::class, 'entry'])->middleware('auth')->name('seller.entry');

Route::prefix('seller')
    ->name('seller.')
    ->middleware(['auth', 'verified', EnsureWorkspaceRole::class.':seller', 'seller.approved'])
    ->group(function () {
        Route::get('/dashboard', [SellerOperationsController::class, 'dashboard'])->name('dashboard');

        Route::get('/products', [SellerProductController::class, 'index'])->name('products');
        Route::post('/products', [SellerProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [SellerProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}/stock', [SellerProductController::class, 'stock'])->name('products.stock');
        Route::patch('/products/{product}/toggle', [SellerProductController::class, 'toggle'])->name('products.toggle');
        Route::get('/products-export', [SellerProductController::class, 'export'])->name('products.export');

        Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders');
        Route::patch('/orders/{sellerOrder}/status', [SellerOrderController::class, 'transition'])->name('orders.status');
        Route::get('/orders/{sellerOrder}/waybill', [SellerOperationsController::class, 'waybill'])->name('orders.waybill');
        Route::get('/orders-export', [SellerOperationsController::class, 'exportOrders'])->name('orders.export');

        Route::get('/logistics', [SellerOperationsController::class, 'logistics'])->name('logistics');
        Route::post('/logistics/{sellerOrder}/pickup', fn () => redirect()->route('seller.logistics')->with('status', 'Shipment creation is controlled by Ready to Ship.'))->name('logistics.pickup');

        Route::get('/messages', [SellerEngagementController::class, 'messages'])->name('messages');
        Route::get('/messages/stream', [SellerEngagementController::class, 'stream'])->name('messages.stream');
        Route::post('/messages', [SellerEngagementController::class, 'sendMessage'])->name('messages.send');

        Route::get('/reviews', [SellerEngagementController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/{review}/reply', [SellerEngagementController::class, 'replyReview'])->name('reviews.reply');
        Route::get('/reviews-export', [SellerEngagementController::class, 'exportReviews'])->name('reviews.export');

        Route::get('/marketing', [SellerEngagementController::class, 'marketing'])->name('marketing');
        Route::post('/marketing/campaigns', [SellerEngagementController::class, 'storeCampaign'])->name('marketing.campaigns.store');
        Route::patch('/marketing/campaigns/{campaign}/toggle', [SellerEngagementController::class, 'toggleCampaign'])->name('marketing.campaigns.toggle');

        Route::get('/finance', [SellerOperationsController::class, 'finance'])->name('finance');
        Route::post('/finance/payouts', [SellerOperationsController::class, 'requestPayout'])->name('finance.payouts.store');
        Route::get('/finance/statement', [SellerOperationsController::class, 'exportStatement'])->name('finance.statement');

        Route::get('/reports', [SellerOperationsController::class, 'reports'])->name('reports');
        Route::get('/reports/download', [SellerOperationsController::class, 'downloadReport'])->name('reports.download');

        Route::get('/store', [SellerAccountController::class, 'store'])->name('store');
        Route::put('/store', [SellerAccountController::class, 'updateStore'])->name('store.update');

        Route::get('/account', [SellerAccountController::class, 'account'])->name('account');
        Route::put('/account/profile', [SellerAccountController::class, 'updateProfile'])->name('account.profile');
        Route::put('/account/business', [SellerAccountController::class, 'updateBusiness'])->name('account.business');
        Route::put('/account/password', [SellerAccountController::class, 'updatePassword'])->name('account.password');
        Route::put('/account/notifications', [SellerAccountController::class, 'updateNotifications'])->name('account.notifications');

        Route::get('/notifications', [SellerEngagementController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/read-all', [SellerEngagementController::class, 'readAll'])->name('notifications.read-all');
    });
