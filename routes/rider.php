<?php

use Illuminate\Support\Facades\Route;


Route::prefix('rider')
    ->name('rider.')
    ->middleware(['auth', \App\Http\Middleware\EnsureWorkspaceRole::class.':rider'])
    ->group(function () {

        Route::get('/', function () {
            return redirect()->route('rider.dashboard');
        })->name('home');

        Route::get('/dashboard', function () {
            return view('rider.dashboard');
        })->name('dashboard');

        Route::get('/parcels', function () {
            return view('rider.parcels.index');
        })->name('parcels.index');

        Route::get('/parcels/{tracking}', function ($tracking) {
            return view('rider.parcels.show', ['tracking' => $tracking]);
        })->name('parcels.show');

        // PICKUPS
        Route::get('/pickups', function () {
            return view('rider.pickups.index');
        })->name('pickups');

        Route::get('/pickups/scan', function () {
            return view('rider.pickups.scan');
        })->name('pickups.scan');

        Route::get('/pickups/{id}', function ($id) {
            return view('rider.pickups.show', ['id' => $id]);
        })->name('pickups.show');

        // DELIVERIES
        Route::get('/deliveries', function () {
            return view('rider.deliveries.index');
        })->name('deliveries');

        Route::get('/deliveries/{id}', function ($id) {
            return view('rider.deliveries.show', ['id' => $id]);
        })->name('deliveries.show');

        Route::get('/deliveries/{id}/tracking', function ($id) {
            return view('rider.deliveries.tracking', ['id' => $id]);
        })->name('deliveries.tracking');

        // EARNINGS
        Route::get('/earnings', function () {
            return view('rider.earnings.index');
        })->name('earnings');

        // HISTORY
        Route::get('/history', function () {
            return view('rider.history.index');
        })->name('history');

        // PROFILE
        Route::get('/profile', function () {
            return view('rider.profile.index');
        })->name('profile');

        // ACCOUNT (alias for profile, used by sidebar)
        Route::get('/account', function () {
            return view('rider.profile.index');
        })->name('account');

    });
