<?php

use App\Http\Controllers\PhilippineAddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest & Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('guest.home'))->name('guest.home');
Route::get('/products', fn () => view('guest.products'))->name('guest.products');
Route::get('/products/{slug}', fn (string $slug) => view('guest.product-details', compact('slug')))->name('guest.product-details');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', fn () => view('guest.auth.login'))->name('login');
Route::post('/login', function (Request $request) {
    // Front-end demo credentials. Replace with Laravel Auth when backend database is linked.
    $roles = [
        'admin@likhae.com'   => ['password' => 'admin',   'role' => 'admin'],
        'buyer@likhae.com'   => ['password' => 'buyer',   'role' => 'buyer'],
        'seller@likhae.com'  => ['password' => 'seller',  'role' => 'seller'],
        'courier@likhae.com' => ['password' => 'courier', 'role' => 'courier'],
    ];

    $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = $roles[$request->email] ?? null;
    if (!$user || $user['password'] !== $request->password) {
        return back()->withErrors(['email' => 'Invalid demo credentials. Use buyer@likhae.com / buyer'])->withInput();
    }

    $request->session()->regenerate();
    $request->session()->put('demo_user', ['email' => $request->email, 'role' => $user['role']]);

    return redirect(match ($user['role']) {
        'buyer'   => '/buyer/home',
        'seller'  => '/seller/home',
        'admin'   => '/admin/home',
        'courier' => '/courier/home',
        default   => '/',
    });
})->name('login.post');

Route::get('/continue-as-guest', function (Request $request) {
    $request->session()->forget('demo_user');
    $request->session()->put('demo_guest', true);
    return redirect()->route('guest.home');
})->name('guest.continue');

Route::get('/auth/google', function () {
    return redirect()->route('login')->with('status', 'Google sign-in is ready for Laravel Socialite integration. Add provider credentials before enabling live OAuth.');
})->name('google.placeholder');

/*
|--------------------------------------------------------------------------
| Buyer Multi-Step Registration Routes
|--------------------------------------------------------------------------
*/
Route::get('/register', fn () => view('Registration.register'))->name('register');
Route::post('/register', function (Request $request) {
    $request->validate([
        'last_name'       => 'required',
        'first_name'      => 'required',
        'sex'             => 'required',
        'birthday'        => 'required|date',
        'email'           => 'required|email',
        'contact'         => 'required',
        'province'        => 'required',
        'municipality'    => 'required',
        'barangay'        => 'required',
        'street'          => 'required',
        'verification_id' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf',
    ]);

    $request->session()->put('registration_status', 'pending');
    return redirect()->route('registration.pending');
})->name('register.store');

Route::get('/registration/pending', fn () => view('Registration.pending'))->name('registration.pending');

/*
|--------------------------------------------------------------------------
| Philippine Address Cascading API
|--------------------------------------------------------------------------
*/
Route::prefix('address/philippines')->name('address.philippines.')->group(function () {
    Route::get('/provinces', [PhilippineAddressController::class, 'provinces'])->name('provinces');
    Route::get('/provinces/{province}/municipalities', [PhilippineAddressController::class, 'municipalities'])->name('municipalities');
    Route::get('/municipalities/{municipality}/barangays', [PhilippineAddressController::class, 'barangays'])->name('barangays');
});

/*
|--------------------------------------------------------------------------
| Session & Logout
|--------------------------------------------------------------------------
*/
Route::post('/logout', function (Request $request) {
    $request->session()->forget(['demo_user', 'demo_guest']);
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Existing Role Routes (Preserved)
|--------------------------------------------------------------------------
*/
Route::get('/admin/home', fn () => view('Admin.home'))->name('admin.home');
Route::get('/courier/home', fn () => view('Courier.home'))->name('courier.home');
Route::get('/courier/application-status', fn () => view('Seller.auth.application-status'))->name('courier.application-status');