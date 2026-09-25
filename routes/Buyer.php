<?php

use App\Http\Controllers\Buyer\BuyerController;
use App\Http\Controllers\Buyer\BuyerOrderController;
use App\Http\Controllers\Buyer\CheckoutController;
use App\Http\Middleware\EnsureWorkspaceRole;
use Illuminate\Support\Facades\Route;

Route::prefix('buyer')
    ->name('buyer.')
    ->middleware(['auth', 'verified', EnsureWorkspaceRole::class.':buyer'])
    ->group(function () {
        Route::get('/pending', fn () => redirect()->route('buyer.home'))->name('pending');
        Route::get('/home', [BuyerController::class, 'home'])->name('home');

        Route::get('/products', [BuyerController::class, 'products'])->name('products');
        Route::get('/products/{slug}', [BuyerController::class, 'product'])->name('product-details');
        Route::get('/flash-deals', fn () => redirect()->route('buyer.products', ['focus' => 'deals']))->name('flash-deals');
        Route::get('/local-finds', fn () => redirect()->route('buyer.products', ['focus' => 'local']))->name('local-finds');

        Route::get('/cart', [BuyerController::class, 'cart'])->name('cart');
        Route::post('/cart/{product}', [BuyerController::class, 'addCart'])->name('cart.add');
        Route::patch('/cart/items/{item}', [BuyerController::class, 'updateCart'])->name('cart.update');
        Route::delete('/cart/items/{item}', [BuyerController::class, 'removeCart'])->name('cart.remove');
        Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
        Route::post('/checkout', [CheckoutController::class, 'select'])->name('checkout.post');
        Route::post('/order', [CheckoutController::class, 'store'])->name('order.store');

        Route::get('/orders/success', fn () => app(BuyerOrderController::class)->index(request(), 'success'))->name('orders.success');
        Route::get('/orders', [BuyerOrderController::class, 'index'])->name('orders');
        Route::post('/orders/cancel', [BuyerOrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{id}/received', [BuyerOrderController::class, 'received'])->name('orders.received');
        Route::get('/orders/{id}/return', fn (string $id) => app(BuyerOrderController::class)->index(request(), 'return', $id))->name('orders.return');
        Route::post('/orders/{id}/return', [BuyerOrderController::class, 'requestReturn'])->name('orders.return.store');
        Route::get('/orders/{id}/review', fn (string $id) => app(BuyerOrderController::class)->index(request(), 'review', $id))->name('orders.review');
        Route::post('/orders/{id}/review', [BuyerOrderController::class, 'review'])->name('orders.review.store');
        Route::get('/orders/{id}', fn (string $id) => app(BuyerOrderController::class)->index(request(), 'show', $id))->name('orders.show');

        Route::get('/messages', [BuyerController::class, 'messages'])->name('messages');
        Route::get('/messages/stream', [BuyerController::class, 'messageStream'])->name('messages.stream');
        Route::post('/messages', [BuyerController::class, 'sendMessage'])->name('messages.send');

        Route::get('/wishlist', [BuyerController::class, 'wishlist'])->name('wishlist');
        Route::post('/wishlist/{product}', [BuyerController::class, 'toggleWishlist'])->name('wishlist.toggle');

        Route::get('/notifications', [BuyerController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/read-all', [BuyerController::class, 'markNotificationsRead'])->name('notifications.read-all');

        Route::get('/shop/{seller}', [BuyerController::class, 'shop'])->name('shop');

        Route::get('/account', [BuyerController::class, 'account'])->name('account');
        Route::get('/account/profile', fn () => redirect()->route('buyer.account', ['tab' => 'profile']))->name('account.profile');
        Route::get('/account/addresses', fn () => redirect()->route('buyer.account', ['tab' => 'addresses']))->name('account.addresses');
        Route::get('/account/security', fn () => redirect()->route('buyer.account', ['tab' => 'password']))->name('account.security');
        Route::get('/account/reviews', fn () => redirect()->route('buyer.account', ['tab' => 'reviews']))->name('account.reviews');
        Route::put('/account/profile', [BuyerController::class, 'saveProfile'])->name('account.profile.update');
        Route::put('/account/password', [BuyerController::class, 'savePassword'])->name('account.password.update');
        Route::post('/account/addresses', [BuyerController::class, 'saveAddress'])->name('account.addresses.store');
        Route::delete('/account/addresses/{address}', [BuyerController::class, 'deleteAddress'])->name('account.addresses.destroy');

        Route::get('/rewards', [BuyerController::class, 'rewards'])->name('rewards');
    });
