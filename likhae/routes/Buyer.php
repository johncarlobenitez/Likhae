<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

$buyerProducts = [
    ['id'=>'wireless-headphones','slug'=>'wireless-headphones','name'=>'Premium Wireless Headphones','category'=>'Electronics','seller'=>'Metro Finds PH','location'=>'Makati City','price'=>2499,'old_price'=>2999,'discount'=>17,'rating'=>4.8,'reviews'=>128,'sold'=>250,'stock'=>18,'image'=>'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80'],
    ['id'=>'classic-backpack','slug'=>'classic-backpack','name'=>'Classic Everyday Backpack','category'=>'Bags','seller'=>'Urban Carry Co.','location'=>'Quezon City','price'=>999,'old_price'=>1299,'discount'=>23,'rating'=>4.7,'reviews'=>92,'sold'=>410,'stock'=>26,'image'=>'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&q=80'],
    ['id'=>'running-shoes','slug'=>'running-shoes','name'=>'Lightweight Running Shoes','category'=>'Sports & Outdoors','seller'=>'Stride PH','location'=>'Pasig City','price'=>1799,'old_price'=>2199,'discount'=>18,'rating'=>4.9,'reviews'=>205,'sold'=>540,'stock'=>14,'image'=>'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80'],
    ['id'=>'smart-watch','slug'=>'smart-watch','name'=>'Everyday Smart Watch','category'=>'Electronics','seller'=>'Tech Avenue','location'=>'Taguig City','price'=>2190,'old_price'=>2690,'discount'=>19,'rating'=>4.7,'reviews'=>164,'sold'=>325,'stock'=>20,'image'=>'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80'],
    ['id'=>'skincare-set','slug'=>'skincare-set','name'=>'Daily Skincare Essentials Set','category'=>'Beauty & Health','seller'=>'Glow Market','location'=>'Manila','price'=>899,'old_price'=>1099,'discount'=>18,'rating'=>4.8,'reviews'=>110,'sold'=>290,'stock'=>31,'image'=>'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=400&q=80'],
    ['id'=>'mechanical-keyboard','slug'=>'mechanical-keyboard','name'=>'Compact Mechanical Keyboard','category'=>'Electronics','seller'=>'KeyHub PH','location'=>'Mandaluyong City','price'=>1899,'old_price'=>2299,'discount'=>17,'rating'=>4.9,'reviews'=>180,'sold'=>460,'stock'=>17,'image'=>'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&q=80'],
    ['id'=>'coffee-maker','slug'=>'coffee-maker','name'=>'Compact Home Coffee Maker','category'=>'Home & Living','seller'=>'Kitchen+ Manila','location'=>'Manila','price'=>1599,'old_price'=>1899,'discount'=>16,'rating'=>4.6,'reviews'=>84,'sold'=>170,'stock'=>11,'image'=>'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&q=80'],
    ['id'=>'desk-lamp','slug'=>'desk-lamp','name'=>'Minimal Adjustable Desk Lamp','category'=>'Home & Living','seller'=>'Home Basics MNL','location'=>'Marikina City','price'=>749,'old_price'=>899,'discount'=>17,'rating'=>4.6,'reviews'=>71,'sold'=>180,'stock'=>22,'image'=>'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&q=80'],
    ['id'=>'basketball','slug'=>'basketball','name'=>'Indoor / Outdoor Basketball','category'=>'Sports & Outdoors','seller'=>'Playground PH','location'=>'Cavite','price'=>699,'old_price'=>849,'discount'=>18,'rating'=>4.7,'reviews'=>89,'sold'=>201,'stock'=>34,'image'=>'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=400&q=80'],
    ['id'=>'book-set','slug'=>'book-set','name'=>'Modern Reading Essentials Set','category'=>'Books & Stationery','seller'=>'Paper Trail PH','location'=>'Laguna','price'=>559,'old_price'=>699,'discount'=>20,'rating'=>4.8,'reviews'=>74,'sold'=>150,'stock'=>42,'image'=>'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=400&q=80'],
    ['id'=>'pet-supplies','slug'=>'pet-supplies','name'=>'Everyday Pet Care Starter Set','category'=>'Pet Supplies','seller'=>'Happy Paws PH','location'=>'Quezon City','price'=>799,'old_price'=>949,'discount'=>16,'rating'=>4.9,'reviews'=>98,'sold'=>220,'stock'=>28,'image'=>'https://images.unsplash.com/photo-1601758124510-52d02ddb7cbd?w=400&q=80'],
];

View::share('buyerProducts', $buyerProducts);

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

    Route::get('/account/reviews', function () {
        return view('Buyer.account.reviews');
    })->name('account.reviews');
});
