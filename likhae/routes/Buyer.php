<?php

use Illuminate\Support\Facades\Route;

Route::prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/pending', function () {
        return redirect()->route('buyer.home');
    })->name('pending');

    Route::get('/home', function () {
        return view('Buyer.home');
    })->name('home');

    Route::get('/products', function () {
        return view('Buyer.products.products');
    })->name('products');

    Route::get('/products/{slug}', function (string $slug) {
        return view('Buyer.products.product-details');
    })->name('product-details');

    Route::get('/flash-deals', function () {
        return view('Buyer.flash-deals.index');
    })->name('flash-deals');

    Route::get('/local-finds', function () {
        return view('Buyer.local-finds.index');
    })->name('local-finds');

    Route::get('/cart', function () {
        return view('Buyer.cart.index');
    })->name('cart');

    Route::get('/notifications', function () {
        return view('Buyer.notifications.index');
    })->name('notifications');

    Route::get('/wishlist', function () {
        return view('Buyer.wishlist.index');
    })->name('wishlist');

    Route::get('/checkout', function () {
        return view('Buyer.checkout.index');
    })->name('checkout');

    Route::post('/checkout', function () {
        return view('Buyer.checkout.index');
    })->name('checkout.post');

    Route::post('/order', function () {
        return redirect('/buyer/orders/success');
    })->name('order.store');

    Route::get('/orders/success', function () {
        return view('Buyer.orders.success');
    })->name('orders.success');

    Route::get('/orders', function () {
        return view('Buyer.orders.index');
    })->name('orders');

    Route::get('/orders/{id}', function (string $id) {
        return view('Buyer.orders.show');
    })->name('orders.show');

    Route::get('/orders/{id}/review', function (string $id) {
        return view('Buyer.orders.review');
    })->name('orders.review');

    Route::get('/messages', function () {
        return view('Buyer.messages.index');
    })->name('messages');

    Route::get('/account', function () {
        return view('Buyer.account.profile');
    })->name('account');

    Route::get('/account/profile', function () {
        return view('Buyer.account.profile');
    })->name('account.profile');

    Route::get('/account/addresses', function () {
        return view('Buyer.account.addresses');
    })->name('account.addresses');

    Route::get('/account/security', function () {
        return view('Buyer.account.security');
    })->name('account.security');
});
