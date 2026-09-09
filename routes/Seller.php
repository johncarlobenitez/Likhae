<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| Seller Center demo data
|--------------------------------------------------------------------------
| Frontend-only sample data. Replace these arrays with controller data when
| the Seller backend is connected.
*/

$sellerProducts = collect([
    ['id' => 'PRD-TEST-0001', 'sku' => 'LK-TEST-SHIRT', 'name' => 'LIKHAE Test Handcrafted Shirt', 'category' => "Women's Apparel", 'price' => 1080, 'stock' => 28, 'sold' => 0, 'rating' => 0, 'status' => 'Test / Demo', 'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=160&q=80'],
    ['id' => 'PRD-1001', 'sku' => 'MON-27-BLK', 'name' => '27-inch Borderless Monitor', 'category' => 'Electronics', 'price' => 12990, 'stock' => 20, 'sold' => 148, 'rating' => 4.9, 'status' => 'Active', 'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=160&q=80'],
    ['id' => 'PRD-1002', 'sku' => 'KEY-MECH-87', 'name' => 'Mechanical Keyboard 87 Keys', 'category' => 'Electronics', 'price' => 2790, 'stock' => 3, 'sold' => 316, 'rating' => 4.8, 'status' => 'Active', 'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=160&q=80'],
    ['id' => 'PRD-1003', 'sku' => 'MSE-GM-BLK', 'name' => 'Wireless Gaming Mouse', 'category' => 'Electronics', 'price' => 1490, 'stock' => 5, 'sold' => 527, 'rating' => 4.9, 'status' => 'Active', 'image' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=160&q=80'],
    ['id' => 'PRD-1004', 'sku' => 'HDP-BT-NVY', 'name' => 'Studio Wireless Headphones', 'category' => 'Audio', 'price' => 3490, 'stock' => 18, 'sold' => 204, 'rating' => 4.7, 'status' => 'Active', 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=160&q=80'],
    ['id' => 'PRD-1005', 'sku' => 'LMP-DSK-WHT', 'name' => 'Adjustable LED Desk Lamp', 'category' => 'Home Office', 'price' => 990, 'stock' => 42, 'sold' => 189, 'rating' => 4.6, 'status' => 'Active', 'image' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=160&q=80'],
    ['id' => 'PRD-1006', 'sku' => 'HUB-USBC-07', 'name' => '7-in-1 USB-C Hub', 'category' => 'Accessories', 'price' => 1890, 'stock' => 0, 'sold' => 94, 'rating' => 4.5, 'status' => 'Archived', 'image' => 'https://images.unsplash.com/photo-1625842268584-8f3296236761?w=160&q=80'],
]);

$sellerOrders = collect([
    ['id' => 'ORD-TEST-0001', 'tracking' => 'LH-TEST-0001', 'buyer' => 'Test Buyer', 'product' => 'LIKHAE Test Handcrafted Shirt', 'variant' => 'Black / Medium', 'quantity' => 2, 'payment' => 'Cash on Delivery', 'shipping' => 'LIKHAE Logistics', 'total' => 2160, 'date' => 'Sep 07, 2026 · 10:00 AM', 'status' => 'To Process', 'status_key' => 'to-process', 'is_demo' => true],
    ['id' => '10001', 'buyer' => 'Angela Cruz', 'product' => '27-inch Borderless Monitor', 'quantity' => 1, 'payment' => 'GCash', 'shipping' => 'J&T Express', 'total' => 12990, 'date' => 'Sep 04, 2026 · 9:30 AM', 'status' => 'To Process', 'status_key' => 'to-process'],
    ['id' => '10002', 'buyer' => 'Marco Reyes', 'product' => 'Mechanical Keyboard 87 Keys', 'quantity' => 2, 'payment' => 'Cash on Delivery', 'shipping' => 'Flash Express', 'total' => 5580, 'date' => 'Sep 04, 2026 · 8:42 AM', 'status' => 'To Prepare', 'status_key' => 'to-prepare'],
    ['id' => '10003', 'buyer' => 'Sarah Lim', 'product' => 'Wireless Gaming Mouse', 'quantity' => 1, 'payment' => 'Maya', 'shipping' => 'J&T Express', 'total' => 1490, 'date' => 'Sep 03, 2026 · 5:10 PM', 'status' => 'Ready Pickup', 'status_key' => 'ready-pickup'],
    ['id' => '10004', 'buyer' => 'Daniel Tan', 'product' => 'Studio Wireless Headphones', 'quantity' => 1, 'payment' => 'Credit Card', 'shipping' => 'LBC', 'total' => 3490, 'date' => 'Sep 03, 2026 · 2:15 PM', 'status' => 'Shipping', 'status_key' => 'shipping'],
    ['id' => '10005', 'buyer' => 'Patricia Go', 'product' => 'Adjustable LED Desk Lamp', 'quantity' => 2, 'payment' => 'GCash', 'shipping' => 'Local Courier', 'total' => 1980, 'date' => 'Sep 02, 2026 · 4:35 PM', 'status' => 'Completed', 'status_key' => 'completed'],
    ['id' => '10006', 'buyer' => 'John Villanueva', 'product' => '7-in-1 USB-C Hub', 'quantity' => 1, 'payment' => 'Cash on Delivery', 'shipping' => 'Flash Express', 'total' => 1890, 'date' => 'Sep 01, 2026 · 11:20 AM', 'status' => 'Returns / Refunds', 'status_key' => 'returns'],
]);

View::share([
    'sellerProducts' => $sellerProducts,
    'sellerOrders' => $sellerOrders,
]);

Route::prefix('seller')->name('seller.')->middleware(['auth', \App\Http\Middleware\EnsureWorkspaceRole::class.':seller'])->group(function () {
    Route::get('/', fn () => redirect()->route('seller.dashboard'));
    Route::get('/dashboard', fn () => view('Seller.dashboard'))->name('dashboard');

    Route::get('/products', fn () => view('Seller.products', [
        'mode' => request('mode', 'list'),
        'selectedProduct' => request('product'),
    ]))->name('products');

    Route::get('/orders', fn () => view('Seller.orders', [
        'pageMode' => 'orders',
        'mode' => request('mode', 'index'),
        'status' => request('status', 'all'),
        'selectedOrder' => request('order'),
    ]))->name('orders');

    Route::get('/logistics', fn () => view('Seller.orders', [
        'pageMode' => 'logistics',
        'mode' => request('view', 'couriers'),
        'status' => request('status', 'all'),
        'selectedOrder' => request('order'),
    ]))->name('logistics');

    Route::get('/messages', fn () => view('Seller.messages', ['mode' => 'messages']))->name('messages');
    Route::get('/reviews', fn () => view('Seller.messages', ['mode' => 'reviews']))->name('reviews');

    Route::get('/marketing', fn () => view('Seller.marketing', [
        'tab' => request('tab', 'discounts'),
    ]))->name('marketing');

    Route::get('/finance', fn () => view('Seller.finance', [
        'tab' => request('tab', 'sales'),
    ]))->name('finance');

    Route::get('/reports', fn () => view('Seller.reports', [
        'report' => request('report', 'sales'),
    ]))->name('reports');

    Route::get('/store', fn () => view('Seller.store', [
        'tab' => request('tab', 'profile'),
    ]))->name('store');

    Route::get('/account', fn () => view('Seller.account', [
        'tab' => request('tab', 'profile'),
    ]))->name('account');

    Route::get('/notifications', fn () => view('Seller.notifications'))->name('notifications');
});
