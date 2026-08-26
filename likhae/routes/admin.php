<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',         fn () => view('Admin.dashboard'))->name('dashboard');
    Route::get('/users',             fn () => view('Admin.users.index'))->name('users');
    Route::get('/sellers/approvals', fn () => view('Admin.sellers.approvals'))->name('sellers.approvals');
    Route::get('/riders',            fn () => view('Admin.riders.index'))->name('riders');
    Route::get('/products',          fn () => view('Admin.products.index'))->name('products');
    Route::get('/categories',        fn () => view('Admin.categories.index'))->name('categories');
    Route::get('/orders',            fn () => view('Admin.orders.index'))->name('orders');
    Route::get('/delivery',          fn () => view('Admin.delivery.index'))->name('delivery');
    Route::get('/payments',          fn () => view('Admin.payments.index'))->name('payments');
    Route::get('/refunds',           fn () => view('Admin.refunds.index'))->name('refunds');
    Route::get('/reports',           fn () => view('Admin.reports.index'))->name('reports');
    Route::get('/settings',          fn () => view('Admin.settings.index'))->name('settings');
});
