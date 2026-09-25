<?php

use App\Http\Controllers\Rider\RiderController;
use App\Http\Controllers\Rider\RiderShipmentController;
use Illuminate\Support\Facades\Route;


Route::prefix('rider')
    ->name('rider.')
    ->middleware(['auth', 'verified', \App\Http\Middleware\EnsureWorkspaceRole::class.':rider', 'rider.active'])
    ->group(function () {

        Route::get('/', function () {
            return redirect()->route('rider.dashboard');
        })->name('home');

        Route::get('/dashboard', [RiderController::class, 'dashboard'])->name('dashboard');
        Route::get('/shipments', [RiderShipmentController::class,'index'])->name('shipments');
        Route::patch('/shipments/{shipment}', [RiderShipmentController::class,'transition'])->name('shipments.transition');
        Route::get('/parcels', [RiderController::class, 'pickups'])->name('parcels.index');
        Route::get('/parcels/{shipment}', [RiderController::class, 'pickupShow'])->name('parcels.show');

        // PICKUPS
        Route::get('/pickups', [RiderController::class, 'pickups'])->name('pickups');
        Route::get('/pickups/scan', [RiderController::class, 'pickups'])->name('pickups.scan');
        Route::get('/pickups/{shipment}', [RiderController::class, 'pickupShow'])->name('pickups.show');

        // DELIVERIES
        Route::get('/deliveries', [RiderController::class, 'deliveries'])->name('deliveries');
        Route::get('/deliveries/{shipment}', [RiderController::class, 'deliveryShow'])->name('deliveries.show');

        // EARNINGS
        Route::get('/earnings', [RiderController::class, 'earnings'])->name('earnings');

        // HISTORY
        Route::get('/history', [RiderController::class, 'history'])->name('history');

        // PROFILE
        Route::get('/profile', [RiderController::class, 'profile'])->name('profile');
        Route::get('/messages', [RiderController::class, 'messages'])->name('messages');
        Route::post('/messages', [RiderController::class, 'sendMessage'])->name('messages.send');

        // ACCOUNT (alias for profile, used by sidebar)
        Route::get('/account', [RiderController::class, 'profile'])->name('account');

    });
