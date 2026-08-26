<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PhilippineAddressController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/admin.php';

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
    // Demo role-based login (replace with real Auth + roles later)
    $roles = [
        'admin@likhae.com'     => ['password' => 'admin',     'role' => 'admin'],
        'admintest@likhae.com' => ['password' => 'admin',     'role' => 'admin'],
        'buyer@likhae.com'     => ['password' => 'buyer',     'role' => 'buyer'],
        'seller@likhae.com'    => ['password' => 'seller',    'role' => 'seller'],
        'courier@likhae.com'   => ['password' => 'courier',   'role' => 'courier'],
    ];

    $user = $roles[$request->email] ?? null;

    if (!$user || $user['password'] !== $request->password) {
        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }

    $request->session()->regenerate();
    $request->session()->put('demo_user', [
        'email' => $request->email,
        'role' => $user['role'],
    ]);

    $redirects = [
        'admin'   => '/admin/dashboard',
        'buyer'   => '/buyer/home',
        'seller'  => '/seller/home',
        'courier' => '/courier/home',
    ];

    return redirect($redirects[$user['role']])->with('status', 'Signed in as '.$user['role'].'.');
})->name('login.post');

Route::get('/register', function () {
    return view('Registration.register');
})->name('register');

Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');

Route::prefix('address/philippines')->name('address.philippines.')->group(function () {
    Route::get('/provinces', [PhilippineAddressController::class, 'provinces'])->name('provinces');
    Route::get('/provinces/{province}/municipalities', [PhilippineAddressController::class, 'municipalities'])->name('municipalities');
    Route::get('/municipalities/{municipality}/barangays', [PhilippineAddressController::class, 'barangays'])->name('barangays');
});

Route::post('/logout', function () {
    return redirect('/login');
})->name('logout');

Route::get('/admin/home', fn () => redirect('/admin/dashboard'))->name('admin.home');

Route::get('/courier/home', function () {
    return view('Courier.home');
})->name('courier.home');

Route::get('/courier/application-status', function () {
    return view('Seller.auth.application-status');
})->name('courier.application-status');

Route::get('/buyer/pending', function () {
    return view('Buyer.pending');
})->name('buyer.pending');

Route::get('/seller/application-status', function () {
    return view('Seller.auth.application-status');
})->name('seller.application-status');