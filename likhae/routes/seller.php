<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LIKHAE Seller Center - UI Preview Routes
|--------------------------------------------------------------------------
| Replace closure routes with controllers when backend wiring begins.
*/

Route::prefix('seller')->name('seller.')->group(function () {
    Route::view('/login', 'Seller.auth.login')->name('login');
    Route::view('/register', 'Registration.seller')->name('register');
    Route::view('/application-status', 'Seller.auth.application-status')->name('application-status');
    Route::view('/home', 'Seller.dashboard')->name('home');

    Route::view('/dashboard', 'Seller.dashboard')->name('dashboard');

    Route::view('/orders', 'Seller.orders.index')->name('orders.index');
    Route::view('/orders/returns', 'Seller.orders.returns')->name('orders.returns');
    Route::view('/orders/{order}', 'Seller.orders.show')->name('orders.show');
    Route::view('/orders/{order}/prepare', 'Seller.orders.prepare')->name('orders.prepare');

    Route::view('/shipping/pickups', 'Seller.shipping.pickups')->name('shipping.pickups');

    Route::view('/products', 'Seller.products.index')->name('products.index');
    Route::view('/products/create', 'Seller.products.create')->name('products.create');
    Route::view('/inventory', 'Seller.inventory.index')->name('inventory.index');

    Route::view('/marketing', 'Seller.marketing.index')->name('marketing.index');
    Route::view('/marketing/flash-deals', 'Seller.marketing.flash-deals')->name('marketing.flash-deals');

    Route::view('/reviews', 'Seller.customers.reviews')->name('reviews.index');
    Route::view('/messages', 'Seller.messages.index')->name('messages.index');

    Route::view('/finance', 'Seller.finance.index')->name('finance.index');
    Route::view('/reports', 'Seller.analytics.reports')->name('reports.index');

    Route::view('/store/profile', 'Seller.store.profile')->name('store.profile');
    Route::view('/account/verification', 'Seller.account.verification')->name('account.verification');
    Route::view('/account/security', 'Seller.account.security')->name('account.security');

    Route::view('/notifications', 'Seller.notifications.index')->name('notifications.index');
    Route::view('/help', 'Seller.help.index')->name('help.index');
});
