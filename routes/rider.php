<?php

use App\Http\Controllers\RiderController;
use Illuminate\Support\Facades\Route;


Route::prefix('rider')
    ->name('rider.')
    ->middleware(['auth', \App\Http\Middleware\EnsureWorkspaceRole::class.':rider'])
    ->group(function () {

        Route::get('/', function () {
            return redirect()->route('rider.dashboard');
        })->name('home');

        Route::get('/dashboard', [RiderController::class, 'dashboard'])->name('dashboard');
        Route::get('/parcels', fn () => redirect()->route('rider.deliveries'))->name('parcels.index');
        Route::get('/parcels/{delivery}', fn (int $delivery) => redirect()->route('rider.deliveries.show', $delivery))->name('parcels.show');

        // PICKUPS
        Route::get('/pickups', [RiderController::class, 'pickups'])->name('pickups');
        Route::get('/pickups/scan', fn () => redirect()->route('rider.pickups'))->name('pickups.scan');
        Route::get('/pickups/{delivery}', [RiderController::class, 'pickupShow'])->name('pickups.show');
        Route::post('/pickups/{delivery}/accept', [RiderController::class, 'acceptPickup'])->name('pickups.accept');
        Route::post('/pickups/{delivery}/confirm', [RiderController::class, 'confirmPickedUp'])->name('pickups.confirm');
        Route::post('/pickups/{delivery}/sorting-center', [RiderController::class, 'deliverToSorting'])->name('pickups.sorting-center');

        // DELIVERIES
        Route::get('/deliveries', [RiderController::class, 'deliveries'])->name('deliveries');
        Route::get('/deliveries/{delivery}', [RiderController::class, 'deliveryShow'])->name('deliveries.show');
        Route::post('/deliveries/{delivery}/pickup-from-sorting', [RiderController::class, 'pickupFromSorting'])->name('deliveries.pickup-sorting');
        Route::post('/deliveries/{delivery}/delivered', [RiderController::class, 'markDelivered'])->name('deliveries.delivered');
        Route::post('/deliveries/{delivery}/failed', [RiderController::class, 'markFailed'])->name('deliveries.failed');
        Route::get('/deliveries/{delivery}/tracking', [RiderController::class, 'tracking'])->name('deliveries.tracking');

        // EARNINGS
        Route::get('/earnings', [RiderController::class, 'earnings'])->name('earnings');

        // HISTORY
        Route::get('/history', [RiderController::class, 'history'])->name('history');

        // PROFILE
        Route::get('/profile', [RiderController::class, 'profile'])->name('profile');

        // ACCOUNT (alias for profile, used by sidebar)
        Route::get('/account', [RiderController::class, 'profile'])->name('account');

    });
