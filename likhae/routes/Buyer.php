<?php

use Illuminate\Support\Facades\Route;

Route::prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/pending', fn () => redirect()->route('buyer.home'))->name('pending');
    Route::get('/home', fn () => view('Buyer.home'))->name('home');
    Route::get('/products', fn () => view('Buyer.products.products'))->name('products');
    Route::get('/products/{slug}', fn (string $slug) => view('Buyer.products.product-details', compact('slug')))->name('product-details');
    Route::get('/flash-deals', fn () => view('Buyer.flash-deals.index'))->name('flash-deals');
    Route::get('/local-finds', fn () => view('Buyer.local-finds.index'))->name('local-finds');
    Route::get('/cart', fn () => view('Buyer.cart.index'))->name('cart');
    Route::get('/notifications', fn () => view('Buyer.notifications.index'))->name('notifications');
    Route::get('/wishlist', fn () => view('Buyer.wishlist.index'))->name('wishlist');
    Route::get('/checkout', fn () => view('Buyer.checkout.index'))->name('checkout');
    Route::post('/checkout', fn () => view('Buyer.checkout.index'))->name('checkout.post');
    Route::post('/order', fn () => redirect('/buyer/orders/success'))->name('order.store');
    Route::get('/orders/success', fn () => view('Buyer.orders.success'))->name('orders.success');
    Route::get('/orders', fn () => view('Buyer.orders.index'))->name('orders');
    Route::get('/orders/{id}', fn (string $id) => view('Buyer.orders.show', compact('id')))->name('orders.show');
    Route::get('/orders/{id}/review', fn (string $id) => view('Buyer.orders.review', compact('id')))->name('orders.review');
    Route::get('/messages', fn () => view('Buyer.messages.index'))->name('messages');
    Route::get('/account', fn () => view('Buyer.account.profile'))->name('account');
    Route::get('/account/profile', fn () => view('Buyer.account.profile'))->name('account.profile');
    Route::get('/account/addresses', fn () => view('Buyer.account.addresses'))->name('account.addresses');
    Route::get('/account/reviews', fn () => view('Buyer.account.reviews'))->name('account.reviews');
    Route::get('/account/security', fn () => view('Buyer.account.security'))->name('account.security');
});
