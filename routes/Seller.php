<?php

use App\Http\Controllers\SellerController;
use App\Http\Middleware\EnsureWorkspaceRole;
use Illuminate\Support\Facades\Route;

Route::prefix('seller')
    ->name('seller.')
    ->middleware(['auth', EnsureWorkspaceRole::class.':seller'])
    ->group(function () {
        Route::get('/', fn () => redirect()->route('seller.dashboard'));

        Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');

        Route::get('/products', [SellerController::class, 'products'])->name('products');
        Route::post('/products', [SellerController::class, 'storeProduct'])->name('products.store');
        Route::put('/products/{product}', [SellerController::class, 'updateProduct'])->name('products.update');
        Route::patch('/products/{product}/stock', [SellerController::class, 'updateStock'])->name('products.stock');
        Route::patch('/products/{product}/toggle', [SellerController::class, 'toggleProduct'])->name('products.toggle');
        Route::get('/products-export', [SellerController::class, 'exportProducts'])->name('products.export');

        Route::get('/orders', [SellerController::class, 'orders'])->name('orders');
        Route::patch('/orders/{order}/status', [SellerController::class, 'updateOrderStatus'])->name('orders.status');
        Route::get('/orders/{order}/waybill', [SellerController::class, 'waybill'])->name('orders.waybill');
        Route::get('/orders-export', [SellerController::class, 'exportOrders'])->name('orders.export');

        Route::get('/logistics', [SellerController::class, 'logistics'])->name('logistics');
        Route::post('/logistics/{order}/pickup', [SellerController::class, 'requestPickup'])->name('logistics.pickup');

        Route::get('/messages', [SellerController::class, 'messages'])->name('messages');
        Route::post('/messages', [SellerController::class, 'sendMessage'])->name('messages.send');

        Route::get('/reviews', [SellerController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/{review}/reply', [SellerController::class, 'replyReview'])->name('reviews.reply');
        Route::get('/reviews-export', [SellerController::class, 'exportReviews'])->name('reviews.export');

        Route::get('/marketing', [SellerController::class, 'marketing'])->name('marketing');
        Route::post('/marketing/campaigns', [SellerController::class, 'storeCampaign'])->name('marketing.campaigns.store');
        Route::patch('/marketing/campaigns/{campaign}/toggle', [SellerController::class, 'toggleCampaign'])->name('marketing.campaigns.toggle');

        Route::get('/finance', [SellerController::class, 'finance'])->name('finance');
        Route::get('/finance/statement', [SellerController::class, 'exportStatement'])->name('finance.statement');

        Route::get('/reports', [SellerController::class, 'reports'])->name('reports');
        Route::get('/reports/download', [SellerController::class, 'downloadReport'])->name('reports.download');

        Route::get('/store', [SellerController::class, 'store'])->name('store');
        Route::put('/store', [SellerController::class, 'updateStore'])->name('store.update');

        Route::get('/account', [SellerController::class, 'account'])->name('account');
        Route::put('/account/profile', [SellerController::class, 'updateAccountProfile'])->name('account.profile');
        Route::put('/account/business', [SellerController::class, 'updateBusiness'])->name('account.business');
        Route::put('/account/password', [SellerController::class, 'updatePassword'])->name('account.password');
        Route::put('/account/notifications', [SellerController::class, 'updateNotificationPreferences'])->name('account.notifications');

        Route::get('/notifications', [SellerController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/read-all', [SellerController::class, 'markNotificationsRead'])->name('notifications.read-all');
    });
