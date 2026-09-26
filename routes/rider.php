<?php

use App\Http\Controllers\Rider\RiderController;
use App\Http\Controllers\Rider\RiderShipmentController;
use App\Http\Middleware\EnsureWorkspaceRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rider / Courier Workspace
|--------------------------------------------------------------------------
|
| Rider and Courier are the same workspace role. All pickup, sorting-center
| handoff, delivery, history, earnings, messaging, and account routes live
| here under the single canonical `rider.*` route namespace.
|
*/

Route::prefix('rider')
    ->name('rider.')
    ->middleware(['auth', 'verified', EnsureWorkspaceRole::class.':rider', 'rider.active'])
    ->group(function (): void {
        Route::get('/', fn () => redirect()->route('rider.dashboard'))->name('home');
        Route::get('/dashboard', [RiderController::class, 'dashboard'])->name('dashboard');

        Route::get('/shipments', [RiderShipmentController::class, 'index'])->name('shipments');
        Route::patch('/assignments/{assignment}', [RiderShipmentController::class, 'transition'])->name('assignments.transition');
        Route::patch('/shipments/{assignment}', [RiderShipmentController::class, 'transition'])->name('shipments.transition');

        Route::get('/parcels', [RiderController::class, 'shipments'])->name('parcels.index');
        Route::get('/parcels/{assignment}', [RiderController::class, 'pickupShow'])->name('parcels.show');

        Route::get('/pickups', [RiderController::class, 'pickups'])->name('pickups');
        Route::get('/pickups/scan', [RiderController::class, 'pickups'])->name('pickups.scan');
        Route::get('/pickups/{assignment}', [RiderController::class, 'pickupShow'])->name('pickups.show');

        Route::get('/deliveries', [RiderController::class, 'deliveries'])->name('deliveries');
        Route::get('/deliveries/{assignment}', [RiderController::class, 'deliveryShow'])->name('deliveries.show');

        Route::get('/earnings', [RiderController::class, 'earnings'])->name('earnings');
        Route::get('/history', [RiderController::class, 'history'])->name('history');
        Route::get('/profile', [RiderController::class, 'profile'])->name('profile');
        Route::get('/account', [RiderController::class, 'profile'])->name('account');
        Route::get('/messages', [RiderController::class, 'messages'])->name('messages');
        Route::post('/messages', [RiderController::class, 'sendMessage'])->name('messages.send');
    });
