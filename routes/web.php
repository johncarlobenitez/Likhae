<?php

use App\Http\Controllers\PhilippineAddressController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Role Route Files
|--------------------------------------------------------------------------
|
| Guest/Admin/Seller/Buyer keep their existing route files.
| Logistics and Rider/Courier are connected through their own route files.
|
*/

require __DIR__ . '/Admin.php';
require __DIR__ . '/Seller.php';
require __DIR__ . '/Buyer.php';
require __DIR__ . '/logistics.php';
require __DIR__ . '/rider.php';

/*
|--------------------------------------------------------------------------
| Guest Marketplace
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('guest.home');
})->name('home');

Route::get('/guest-account', function () {
    return view('guest.products');
})->name('guest.home');

Route::get('/products', function () {
    return view('guest.products');
})->name('products');

Route::get('/products/{slug}', function (string $slug) {
    $product = collect(view()->shared('buyerProducts', []))
        ->firstWhere('slug', $slug);

    abort_if(!$product, 404);

    return view('guest.product-details', [
        'product' => $product,
    ]);
})->name('products.show');

/*
|--------------------------------------------------------------------------
| Shared Login — All Roles
|--------------------------------------------------------------------------
|
| ONE login page handles Admin, Buyer, Seller, Logistics, and Rider.
| The demo account map determines the role automatically from the email.
|
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (! $user || ! \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
    }

    if ($user->status === 'pending') {
        return back()->withErrors(['email' => 'Your account is pending approval.'])->withInput($request->only('email'));
    }

    if ($user->status === 'rejected') {
        return back()->withErrors(['email' => 'Your account has been rejected.'])->withInput($request->only('email'));
    }

    if ($user->status === 'suspended') {
        return back()->withErrors(['email' => 'Your account has been suspended.'])->withInput($request->only('email'));
    }

    if (in_array($user->role, ['logistics', 'rider', 'courier'], true)) {
        return back()->withErrors(['email' => 'This account belongs to the Logistics Portal. Use the Logistics Portal sign in page.'])->withInput($request->only('email'));
    }

    \Illuminate\Support\Facades\Auth::login($user, $request->boolean('remember'));
    $request->session()->regenerate();

    $redirects = [
        'admin'   => route('admin.dashboard'),
        'buyer'   => route('buyer.home'),
        'seller'  => route('seller.dashboard'),
    ];

    return redirect($redirects[$user->role] ?? route('home'))
        ->with('status', 'Signed in as ' . ucfirst($user->role) . '.');
})->name('login.post');

/*
|--------------------------------------------------------------------------
| Registration — All Roles
|--------------------------------------------------------------------------
|
| ONE shared registration page for all four public account types:
| Buyer, Seller, Logistics, Rider.
|
| Optional ?role=xxx preselects the account type in the UI.
|
*/

Route::get('/register', function (Request $request) {
    return view('auth.register', [
        'preselectedRole' => $request->query('role', 'buyer'),
    ]);
})->name('register');

Route::get('/registration/pending', function (\Illuminate\Http\Request $request) {
    return view('auth.pending', ['accountType' => $request->query('type', 'LIKHAE')]);
})->name('registration.pending');

Route::get('/register/{role}', function (string $role) {
    abort_unless(in_array($role, ['buyer', 'seller', 'logistics', 'rider'], true), 404);

    return redirect()->route('register', ['role' => $role === 'rider' ? 'courier' : $role]);
})->name('register.role');

Route::post('/register', [RegistrationController::class, 'store'])
    ->name('register.store');

/*
|--------------------------------------------------------------------------
| Philippine Address API
|--------------------------------------------------------------------------
*/

Route::prefix('address/philippines')
    ->name('address.philippines.')
    ->group(function () {
        Route::get('/regions', [PhilippineAddressController::class, 'regions'])
            ->name('regions');

        Route::get('/regions/{region}/provinces', [PhilippineAddressController::class, 'provinces'])
            ->name('provinces');

        Route::get('/provinces/{province}/municipalities', [PhilippineAddressController::class, 'municipalities'])
            ->name('municipalities');

        Route::get('/municipalities/{municipality}/barangays', [PhilippineAddressController::class, 'barangays'])
            ->name('barangays');
    });

/*
|--------------------------------------------------------------------------
| Main Marketplace Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', function (Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->with('status', 'You have been signed out.');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Social Login Placeholder
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', function () {
    return back()->with(
        'status',
        'Google login is not available yet.'
    );
})->name('google.placeholder');

/*
|--------------------------------------------------------------------------
| Continue as Guest
|--------------------------------------------------------------------------
*/

Route::get('/continue-as-guest', function (Request $request) {
    $request->session()->put('demo_user', [
        'email' => 'guest',
        'role' => 'guest',
    ]);

    return redirect()->route('guest.home');
})->name('guest.continue');

/*
|--------------------------------------------------------------------------
| Admin Compatibility Redirect
|--------------------------------------------------------------------------
*/

Route::get('/admin/home', function () {
    return redirect()->route('admin.dashboard');
})->name('admin.home');
