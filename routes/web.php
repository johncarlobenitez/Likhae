<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PhilippineAddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Route Files
|--------------------------------------------------------------------------
|
| Keep role-specific routes separated.
|
*/

require __DIR__ . '/admin.php';
require __DIR__ . '/seller.php';
require __DIR__ . '/Buyer.php';


/*
|--------------------------------------------------------------------------
| Guest Marketplace
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('guest.home');
})->name('home');


Route::get('/products', function () {
    return view('guest.products');
})->name('products');


Route::get('/products/{slug}', function (string $slug) {
    $product = collect(view()->shared('buyerProducts', []))
        ->firstWhere('slug', $slug);
    abort_if(!$product, 404);
    return view('guest.product-details', ['product' => $product]);
})->name('products.show');


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
|
| Current structure:
|
| resources/views/auth/login.blade.php
| resources/views/auth/register.blade.php
| resources/views/auth/pending.blade.php
| resources/views/auth/seller.blade.php
|
*/


/*
|--------------------------------------------------------------------------
| Login Page
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login');


Route::post('/login', function (Request $request) {

    $request->validate([
        'email' => [
            'required',
            'email',
        ],

        'password' => [
            'required',
            'string',
        ],
    ]);


    $roles = [

        'admin@likhae.com' => [
            'password' => 'admin',
            'role' => 'admin',
        ],

        'admintest@likhae.com' => [
            'password' => 'admin',
            'role' => 'admin',
        ],

        'buyer@likhae.com' => [
            'password' => 'buyer',
            'role' => 'buyer',
        ],

        'seller@likhae.com' => [
            'password' => 'seller',
            'role' => 'seller',
        ],

        'courier@likhae.com' => [
            'password' => 'courier',
            'role' => 'courier',
        ],

    ];


    $user =
        $roles[$request->email]
        ?? null;


    if (
        !$user
        ||
        $user['password'] !== $request->password
    ) {

        return back()
            ->withErrors([
                'email' => 'Invalid email or password.',
            ])
            ->withInput(
                $request->only('email')
            );

    }


    /*
    |--------------------------------------------------------------------------
    | Regenerate Session
    |--------------------------------------------------------------------------
    */

    $request
        ->session()
        ->regenerate();


    /*
    |--------------------------------------------------------------------------
    | Store Demo User
    |--------------------------------------------------------------------------
    */

    $request
        ->session()
        ->put(
            'demo_user',
            [
                'email' => $request->email,
                'role' => $user['role'],
            ]
        );


    /*
    |--------------------------------------------------------------------------
    | Role Redirects
    |--------------------------------------------------------------------------
    */

    $redirects = [

        'admin' =>
            route('admin.dashboard'),

        'buyer' =>
            route('buyer.home'),

        'seller' =>
            route('seller.dashboard'),

        'courier' =>
            route('courier.home'),

    ];


    return redirect(
        $redirects[$user['role']]
    )->with(
        'status',
        'Signed in as ' . ucfirst($user['role']) . '.'
    );

})->name('login.post');


/*
|--------------------------------------------------------------------------
| Registration
|--------------------------------------------------------------------------
|
| Shared registration page:
|
| Buyer  -> personal information + ID
| Seller -> personal information + business information + permit
|
*/

Route::get('/register', function () {
    return view('auth.register');
})->name('register');


Route::post(
    '/register',
    [
        RegistrationController::class,
        'store',
    ]
)->name('register.store');


/*
|--------------------------------------------------------------------------
| Philippine Address API
|--------------------------------------------------------------------------
|
| Province
|      â†“
| Municipality / City
|      â†“
| Barangay
|
*/

Route::prefix('address/philippines')
    ->name('address.philippines.')
    ->group(function () {
        Route::get('/regions',                                    [PhilippineAddressController::class, 'regions'])->name('regions');
        Route::get('/regions/{region}/provinces',                 [PhilippineAddressController::class, 'provinces'])->name('provinces');
        Route::get('/provinces/{province}/municipalities',        [PhilippineAddressController::class, 'municipalities'])->name('municipalities');
        Route::get('/municipalities/{municipality}/barangays',    [PhilippineAddressController::class, 'barangays'])->name('barangays');
    });


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', function (Request $request) {

    /*
    |--------------------------------------------------------------------------
    | Remove Demo Login
    |--------------------------------------------------------------------------
    */

    $request
        ->session()
        ->forget('demo_user');


    /*
    |--------------------------------------------------------------------------
    | Invalidate Current Session
    |--------------------------------------------------------------------------
    */

    $request
        ->session()
        ->invalidate();


    /*
    |--------------------------------------------------------------------------
    | Regenerate CSRF Token
    |--------------------------------------------------------------------------
    */

    $request
        ->session()
        ->regenerateToken();


    return redirect()
        ->route('login')
        ->with(
            'status',
            'You have been signed out.'
        );

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

Route::get('/guest/continue', function (Request $request) {

    $request
        ->session()
        ->put(
            'demo_user',
            [
                'email' => 'guest',
                'role' => 'guest',
            ]
        );


    return redirect()
        ->route('home');

})->name('guest.continue');


/*
|--------------------------------------------------------------------------
| Admin Compatibility Redirect
|--------------------------------------------------------------------------
|
| Your real Admin dashboard route comes from routes/admin.php.
|
*/

Route::get('/admin/home', function () {

    return redirect()
        ->route('admin.dashboard');

})->name('admin.home');


/*
|--------------------------------------------------------------------------
| Courier Frontend Preview
|--------------------------------------------------------------------------
|
| Courier does not currently have its own route file in the structure
| you showed, so these remain here temporarily.
|
*/

Route::get('/courier/home', function () {

    /*
    |--------------------------------------------------------------
    | Change this once resources/views/Courier/home.blade.php
    | is finalized.
    |--------------------------------------------------------------
    */

    if (view()->exists('Courier.home')) {
        return view('Courier.home');
    }


    return view('auth.pending', [
        'accountType' => 'Courier',
    ]);

})->name('courier.home');


Route::get('/courier/application-status', function () {

    return view('auth.pending', [
        'accountType' => 'Courier',
    ]);

})->name('courier.application-status');
