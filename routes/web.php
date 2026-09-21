<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\GuestMarketplaceController;
use App\Http\Controllers\GoogleAuthenticationController;
use App\Http\Controllers\PhilippineAddressController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SellerApplicationController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\AccountRecoveryController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\LogisticsProviderApplicationController;
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
Route::get('/track/{trackingCode}', [TrackingController::class, 'show'])->middleware('throttle:30,1')->name('tracking.show');
Route::get('/delivery-events/{event}/proof', [TrackingController::class, 'proof'])->middleware('auth')->name('delivery-events.proof');

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
Route::get('/forgot-password', [AccountRecoveryController::class, 'request'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [AccountRecoveryController::class, 'email'])->middleware(['guest','throttle:5,1'])->name('password.email');
Route::get('/reset-password/{token}', [AccountRecoveryController::class, 'reset'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AccountRecoveryController::class, 'update'])->middleware('guest')->name('password.update');
Route::get('/email/verify', fn () => view('auth.verify-email'))->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) { $request->fulfill(); return redirect()->route($request->user()->workspaceRoute()); })->middleware(['auth','signed','throttle:6,1'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) { $request->user()->sendEmailVerificationNotification(); return back()->with('status','verification-link-sent'); })->middleware(['auth','throttle:6,1'])->name('verification.send');

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

Route::get('/register', [RegistrationController::class, 'create'])->middleware('guest')->name('register');

Route::post('/register', [RegistrationController::class, 'store'])
    ->middleware(['guest', 'throttle:10,1'])->name('register.store');

Route::middleware('auth')->group(function () {
    Route::get('/sell', [SellerApplicationController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellerApplicationController::class, 'store'])->name('sell.store');
    Route::get('/partner', [LogisticsProviderApplicationController::class, 'create'])->name('partner.create');
    Route::post('/partner', [LogisticsProviderApplicationController::class, 'store'])->name('partner.store');
    Route::get('/partner/status', [LogisticsProviderApplicationController::class, 'status'])->name('partner.status');
});

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

Route::get('/auth/google', [GoogleAuthenticationController::class, 'redirect'])
    ->middleware('guest')->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthenticationController::class, 'callback'])
    ->middleware('guest')->name('google.callback');

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
