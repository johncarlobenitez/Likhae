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

View::share('buyerProducts', collect($buyerProducts));

Route::prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/pending', fn () => redirect()->route('buyer.home'))->name('pending');

    Route::get('/home', fn () => view('Buyer.home'))->name('home');

    Route::get('/products', fn () => view('Buyer.products', ['mode' => 'grid']))->name('products');
    Route::get('/products/{slug}', fn (string $slug) => view('Buyer.products', [
        'mode' => 'details',
        'selectedSlug' => $slug,
    ]))->name('product-details');

    Route::get('/flash-deals', fn () => view('Buyer.products', [
        'mode' => 'grid',
        'focus' => 'deals',
    ]))->name('flash-deals');
    Route::get('/local-finds', fn () => view('Buyer.products', [
        'mode' => 'grid',
        'focus' => 'local',
    ]))->name('local-finds');

    Route::get('/cart', fn () => view('Buyer.cart'))->name('cart');
    Route::get('/checkout', fn () => view('Buyer.checkout'))->name('checkout');
    Route::post('/checkout', fn () => view('Buyer.checkout'))->name('checkout.post');
    Route::post('/order', fn () => redirect()->route('buyer.orders.success'))->name('order.store');

    Route::get('/orders/success', fn () => view('Buyer.orders', ['mode' => 'success']))->name('orders.success');
    Route::get('/orders', fn () => view('Buyer.orders', ['mode' => 'index']))->name('orders');
    Route::get('/orders/{id}', fn (string $id) => view('Buyer.orders', [
        'mode' => 'show',
        'selectedOrderId' => $id,
    ]))->name('orders.show');
    Route::get('/orders/{id}/review', fn (string $id) => view('Buyer.orders', [
        'mode' => 'review',
        'selectedOrderId' => $id,
    ]))->name('orders.review');

    Route::get('/messages', fn () => view('Buyer.messages'))->name('messages');
    Route::get('/wishlist', fn () => view('Buyer.wishlist'))->name('wishlist');
    Route::get('/notifications', fn () => view('Buyer.notifications'))->name('notifications');

    Route::get('/shop/{seller}', fn (string $seller) => view('Buyer.shop', [
        'sellerSlug' => $seller,
    ]))->name('shop');

    Route::get('/account', fn () => view('Buyer.account', [
        'tab' => request('tab', 'profile'),
    ]))->name('account');
    Route::get('/account/profile', fn () => view('Buyer.account', ['tab' => 'profile']))->name('account.profile');
    Route::get('/account/addresses', fn () => view('Buyer.account', ['tab' => 'addresses']))->name('account.addresses');
    Route::get('/account/security', fn () => view('Buyer.account', ['tab' => 'security']))->name('account.security');
    Route::get('/account/reviews', fn () => view('Buyer.account', ['tab' => 'reviews']))->name('account.reviews');
});
