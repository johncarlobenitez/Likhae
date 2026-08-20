<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Guest.home');
});

Route::get('/products', function () {
    return view('Guest.products');
});

Route::get('/products/{slug}', function (string $slug) {
    return view('Guest.product-details');
});

Route::get('/login', function () {
    return view('Guest.auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = [
        'email'    => $request->email,
        'password' => $request->password,
    ];

    // Demo role-based login (replace with real Auth + roles later)
    $roles = [
        'admin@likhae.com'   => ['password' => 'admin',   'role' => 'admin'],
        'buyer@likhae.com'   => ['password' => 'buyer',   'role' => 'buyer'],
        'seller@likhae.com'  => ['password' => 'seller',  'role' => 'seller'],
        'courier@likhae.com' => ['password' => 'courier', 'role' => 'courier'],
    ];

    $user = $roles[$request->email] ?? null;

    if (!$user || $user['password'] !== $request->password) {
        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }

    $redirects = [
        'admin'   => '/admin/home',
        'buyer'   => '/buyer/home',
        'seller'  => '/seller/home',
        'courier' => '/courier/home',
    ];

    return redirect($redirects[$user['role']]);
})->name('login.post');

Route::get('/register', function () {
    return view('Guest.auth.register');
})->name('register');

Route::get('/buyer/home', function () {
    return view('Buyer.home');
})->name('buyer.home');

Route::get('/buyer/products', function () {
    return view('Buyer.products.products');
})->name('buyer.products');

Route::get('/buyer/products/{slug}', function (string $slug) {
    return view('Buyer.products.product-details');
})->name('buyer.product-details');

Route::get('/buyer/flash-deals', function () {
    return view('Buyer.flash-deals.index');
})->name('buyer.flash-deals');

Route::get('/buyer/local-finds', function () {
    return view('Buyer.local-finds.index');
})->name('buyer.local-finds');

Route::get('/buyer/cart', function () {
    return view('Buyer.cart.index');
})->name('buyer.cart');

Route::get('/buyer/notifications', function () {
    return view('Buyer.notifications.index');
})->name('buyer.notifications');

Route::get('/buyer/wishlist', function () {
    return view('Buyer.wishlist.index');
})->name('buyer.wishlist');


Route::get('/buyer/checkout', function () {
    return view('Buyer.checkout.index');
})->name('buyer.checkout');

Route::post('/buyer/checkout', function () {
    return view('Buyer.checkout.index');
})->name('buyer.checkout.post');

Route::post('/buyer/order', function () {
    // Replace with real order creation logic later
    return redirect('/buyer/orders/success');
})->name('buyer.order.store');

Route::get('/buyer/orders/success', function () {
    return view('Buyer.orders.success');
})->name('buyer.orders.success');

Route::get('/buyer/orders', function () {
    return view('Buyer.orders.index');
})->name('buyer.orders');

Route::get('/buyer/orders/{id}', function (string $id) {
    return view('Buyer.orders.show');
})->name('buyer.orders.show');

Route::get('/buyer/orders/{id}/review', function (string $id) {
    return view('Buyer.orders.review');
})->name('buyer.orders.review');

Route::get('/buyer/messages', function () {
    return view('Buyer.messages.index');
})->name('buyer.messages');

Route::get('/buyer/account', function () {
    return view('Buyer.account.profile');
})->name('buyer.account');

Route::get('/buyer/account/profile', function () {
    return view('Buyer.account.profile');
})->name('buyer.account.profile');

Route::get('/buyer/account/addresses', function () {
    return view('Buyer.account.addresses');
})->name('buyer.account.addresses');

Route::get('/buyer/account/security', function () {
    return view('Buyer.account.security');
})->name('buyer.account.security');

Route::get('/buyer/local-finds', function () {
    return view('Buyer.local-finds.index');
})->name('buyer.local-finds');

Route::post('/logout', function () {
    return redirect('/login');
})->name('logout');

Route::get('/admin/home', function () {
    return view('Admin.home');
})->name('admin.home');

Route::get('/seller/home', function () {
    return view('Seller.home');
})->name('seller.home');

Route::get('/courier/home', function () {
    return view('Courier.home');
})->name('courier.home');