<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\GuestMarketplaceController;
use App\Http\Controllers\PhilippineAddressController;
use App\Http\Controllers\RegistrationController;
use App\Models\Category;
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

require __DIR__.'/Admin.php';
require __DIR__.'/Seller.php';
require __DIR__.'/Buyer.php';
require __DIR__.'/logistics.php';
require __DIR__.'/rider.php';

/*
|--------------------------------------------------------------------------
| Guest Marketplace
|--------------------------------------------------------------------------
*/

Route::get('/', [GuestMarketplaceController::class, 'home'])->name('home');
Route::get('/guest-account', [GuestMarketplaceController::class, 'products'])->name('guest.home');
Route::get('/products', [GuestMarketplaceController::class, 'products'])->name('products');
Route::get('/products/{slug}', [GuestMarketplaceController::class, 'show'])->name('products.show');

/*
|--------------------------------------------------------------------------
| Marketplace Login
|--------------------------------------------------------------------------
|
| Marketplace login handles Admin, Buyer, and Seller accounts.
| Logistics and Rider accounts use the separate Logistics Portal.
| Database credentials and administrator approval determine access.
|
*/

Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/login', [AuthenticationController::class, 'store'])->middleware(['guest', 'throttle:30,1'])->name('login.post');

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
        'sellerLineOfBusinessCategories' => Category::whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(),
    ]);
})->middleware('guest')->name('register');

Route::get('/registration/pending', function (Request $request) {
    return view('auth.pending', ['accountType' => $request->query('type', 'LIKHAE')]);
})->name('registration.pending');

Route::get('/register/{role}', function (string $role) {
    abort_unless(in_array($role, ['buyer', 'seller', 'logistics', 'rider'], true), 404);

    return redirect()->route('register', ['role' => $role === 'rider' ? 'courier' : $role]);
})->name('register.role');

Route::post('/register', [RegistrationController::class, 'store'])
    ->middleware(['guest', 'throttle:10,1'])->name('register.store');

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

        Route::get('/postal-code', [PhilippineAddressController::class, 'postalCode'])
            ->name('postal-code');
    });

/*
|--------------------------------------------------------------------------
| Main Marketplace Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthenticationController::class, 'destroy'])->name('logout');

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
