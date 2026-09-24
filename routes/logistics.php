<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\LogisticsPortalController;
use App\Http\Controllers\ProviderRiderController;
use App\Http\Controllers\DispatchController;
use App\Http\Middleware\EnsureWorkspaceRole;
use Illuminate\Support\Facades\Route;

Route::prefix('logistics')
    ->name('logistics.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | HOME
        |--------------------------------------------------------------------------
        */

        Route::get('/', fn () => view('Logistics.landing'))->name('home');

        Route::get('/login', function () {
            return view('auth.workspace-login', [
                'workspace' => 'logistics',
                'accountLabel' => 'Logistics & Rider Portal',
                'headline' => 'Coordinate every parcel movement.',
                'description' => 'Logistics centers manage intake and assignments. Riders manage only their own pickup and delivery work.',
                'homeRoute' => route('logistics.home'),
                'loginRoute' => route('logistics.login.store'),
                'registerRoute' => route('register'),
            ]);
        })->middleware('guest')->name('login');

        Route::post('/login', [AuthenticationController::class, 'store'])->middleware(['guest', 'throttle:30,1'])->name('login.store');

        Route::middleware(['auth', 'verified', 'workspace.role:logistics', 'provider.approved'])->group(function () {
            Route::get('/dispatch', [DispatchController::class,'index'])->name('dispatch');
            Route::post('/dispatch/{shipment}/assign', [DispatchController::class,'assign'])->name('dispatch.assign');

            /*
            |--------------------------------------------------------------------------
            | DASHBOARD
            |--------------------------------------------------------------------------
            */

            Route::get('/dashboard', [LogisticsPortalController::class, 'dashboard'])->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | PARCELS
            |--------------------------------------------------------------------------
            */

            Route::get('/parcels', [DispatchController::class, 'parcels'])->name('parcels');
            Route::get('/pickups', [DispatchController::class, 'pickups'])->name('pickups');
            Route::get('/parcels/receive', [DispatchController::class, 'receive'])->name('parcels.receive');
            Route::get('/parcels/tracking', [DispatchController::class, 'tracking'])->name('parcels.tracking');
            Route::post('/pickups/{delivery}/assign', [DispatchController::class, 'assignPickup'])->name('pickups.assign');
            Route::post('/parcels/receive/{delivery}/confirm', [DispatchController::class, 'confirmReceive'])->name('parcels.receive.confirm');
            Route::post('/sorting/{delivery}/sort', [DispatchController::class, 'sortParcel'])->name('sorting.sort');

            Route::get('/scanner', function () {
                return redirect()->route('logistics.parcels.receive');
            })->name('scanner');

            Route::get('/waybills/{tracking}', [DispatchController::class, 'waybill'])->name('waybills.show');
            Route::get('/parcels/{shipment}', [DispatchController::class, 'show'])->name('parcels.show');

            /*
            |--------------------------------------------------------------------------
            | SORTING CENTER
            |--------------------------------------------------------------------------
            */

            Route::get('/sorting', [DispatchController::class, 'sorting'])->name('sorting');

            /*
            |--------------------------------------------------------------------------
            | RIDER ASSIGNMENTS
            |--------------------------------------------------------------------------
            */

            Route::get('/assignments', [DispatchController::class, 'index'])->name('assignments');
            Route::get('/assignments/{shipment}/assign', [DispatchController::class, 'index'])->name('assignments.assign');

            /*
            |--------------------------------------------------------------------------
            | RIDERS
            |--------------------------------------------------------------------------
            */

            Route::get('/riders', [ProviderRiderController::class, 'index'])->name('riders');
            Route::post('/riders', [ProviderRiderController::class, 'store'])->name('riders.store');
            Route::get('/riders/{rider}/edit', [ProviderRiderController::class, 'edit'])->name('riders.edit');
            Route::patch('/riders/{rider}/activate', [ProviderRiderController::class, 'activate'])->name('riders.activate');
            Route::patch('/riders/{rider}/deactivate', [ProviderRiderController::class, 'deactivate'])->name('riders.deactivate');
            Route::patch('/riders/{rider}', [ProviderRiderController::class, 'update'])->name('riders.update');

            /*
            |--------------------------------------------------------------------------
            | RIDER APPLICATIONS
            |--------------------------------------------------------------------------
            */
            Route::get('/riders/applications', [LogisticsPortalController::class, 'riderApplications'])->name('riders.applications');
            Route::get('/riders/applications/{rider}', [LogisticsPortalController::class, 'riderApplicationShow'])->name('riders.application.show');
            Route::post('/riders/{rider}/approve', [LogisticsPortalController::class, 'approveRider'])->name('riders.approve');
            Route::post('/riders/{rider}/reject', [LogisticsPortalController::class, 'rejectRider'])->name('riders.reject');

            /*
            |--------------------------------------------------------------------------
            | VIEW RIDER PROFILE
            |--------------------------------------------------------------------------
            */

            Route::get('/riders/{rider}', [LogisticsPortalController::class, 'riderShow'])->name('riders.show');

            /*
            |--------------------------------------------------------------------------
            | APPROVE RIDER
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | REJECT RIDER
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | DELIVERY AREAS
            |--------------------------------------------------------------------------
            */

            Route::get('/delivery-areas', [LogisticsPortalController::class, 'deliveryAreas'])->name('delivery-areas');
            Route::post('/delivery-areas', [LogisticsPortalController::class, 'saveDeliveryArea'])->name('delivery-areas.store');
            Route::patch('/delivery-areas/{area}', [LogisticsPortalController::class, 'toggleDeliveryArea'])->name('delivery-areas.toggle');

            /*
            |--------------------------------------------------------------------------
            | MESSAGES
            |--------------------------------------------------------------------------
            */

            Route::get('/messages', [LogisticsPortalController::class, 'messages'])->name('messages');
            Route::post('/messages', [LogisticsPortalController::class, 'sendMessage'])->name('messages.send');

            /*
            |--------------------------------------------------------------------------
            | REPORTS
            |--------------------------------------------------------------------------
            */

            Route::get('/reports', [LogisticsPortalController::class, 'reports'])->name('reports');

            /*
            |--------------------------------------------------------------------------
            | PROFILE
            |--------------------------------------------------------------------------
            */

            Route::get('/profile', [LogisticsPortalController::class, 'profile'])->name('profile');

            /*
            |--------------------------------------------------------------------------
            | ACCOUNT (alias for profile)
            |--------------------------------------------------------------------------
            */

            Route::get('/account', [LogisticsPortalController::class, 'profile'])->name('account');

        });
    });
