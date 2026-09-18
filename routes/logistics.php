<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\LogisticsController;
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

        Route::get('/', fn () => view('logistics.landing'))->name('home');

        Route::get('/login', function () {
            return view('auth.workspace-login', [
                'workspace' => 'logistics',
                'accountLabel' => 'Logistics & Rider Portal',
                'headline' => 'Coordinate every parcel movement.',
                'description' => 'Logistics centers manage intake and assignments. Riders manage only their own pickup and delivery work.',
                'homeRoute' => route('logistics.home'),
                'loginRoute' => route('logistics.login.store'),
                'registerRoute' => route('register', ['role' => 'logistics']),
            ]);
        })->middleware('guest')->name('login');

        Route::post('/login', [AuthenticationController::class, 'store'])->middleware(['guest', 'throttle:30,1'])->name('login.store');

        Route::middleware(['auth', 'workspace.role:logistics'])->group(function () {

            /*
            |--------------------------------------------------------------------------
            | DASHBOARD
            |--------------------------------------------------------------------------
            */

            Route::get('/dashboard', [LogisticsController::class, 'dashboard'])->name('dashboard')->middleware(['auth', EnsureWorkspaceRole::class.':logistics']);

            /*
            |--------------------------------------------------------------------------
            | PARCELS
            |--------------------------------------------------------------------------
            */

            Route::get('/parcels', [LogisticsController::class, 'parcels'])->name('parcels');

            Route::get('/pickups', [LogisticsController::class, 'pickupRequests'])->name('pickups');
            Route::post('/pickups/{delivery}/assign', [LogisticsController::class, 'assignPickupRider'])->name('pickups.assign');

            Route::get('/parcels/receive', [LogisticsController::class, 'receive'])->name('parcels.receive');
            Route::post('/parcels/{delivery}/receive', [LogisticsController::class, 'confirmReceived'])->name('parcels.receive.confirm');

            Route::get('/parcels/tracking', [LogisticsController::class, 'tracking'])->name('parcels.tracking');

            Route::get('/scanner', [LogisticsController::class, 'scanner'])->name('scanner');
            Route::post('/scanner', [LogisticsController::class, 'scan'])->name('scanner.scan');

            Route::get('/waybills/{tracking}', [LogisticsController::class, 'waybill'])->name('waybills.show');

            Route::get('/parcels/{delivery}', [LogisticsController::class, 'parcelShow'])->name('parcels.show');

            /*
            |--------------------------------------------------------------------------
            | SORTING CENTER
            |--------------------------------------------------------------------------
            */

            Route::get('/sorting', [LogisticsController::class, 'sorting'])->name('sorting');
            Route::post('/sorting/{delivery}/sort', [LogisticsController::class, 'markSorted'])->name('sorting.sort');

            /*
            |--------------------------------------------------------------------------
            | RIDER ASSIGNMENTS
            |--------------------------------------------------------------------------
            */

            Route::get('/assignments', [LogisticsController::class, 'assignments'])->name('assignments');
            Route::post('/assignments/{delivery}/assign', [LogisticsController::class, 'assignRider'])->name('assignments.assign-rider');
            Route::post('/assignments/{delivery}/release', [LogisticsController::class, 'releaseToRider'])->name('assignments.release');

            Route::get('/assignments/{delivery}/assign', fn () => redirect()->route('logistics.assignments'))->name('assignments.assign');

            /*
            |--------------------------------------------------------------------------
            | RIDERS
            |--------------------------------------------------------------------------
            */

            Route::get('/riders', [LogisticsController::class, 'riders'])->name('riders');

            /*
            |--------------------------------------------------------------------------
            | RIDER APPLICATIONS
            |--------------------------------------------------------------------------
            */

            Route::get('/riders/applications', [LogisticsController::class, 'riderApplications'])->name('riders.applications');

            Route::get('/riders/applications/{user}', [LogisticsController::class, 'riderShow'])->name('riders.applications.show');

            /*
            |--------------------------------------------------------------------------
            | VIEW RIDER PROFILE
            |--------------------------------------------------------------------------
            */

            Route::get('/riders/{user}', [LogisticsController::class, 'riderShow'])->name('riders.show');

            /*
            |--------------------------------------------------------------------------
            | APPROVE RIDER
            |--------------------------------------------------------------------------
            */

            Route::post('/riders/{user}/approve', [LogisticsController::class, 'approveRider'])->name('riders.approve');

            /*
            |--------------------------------------------------------------------------
            | REJECT RIDER
            |--------------------------------------------------------------------------
            */

            Route::post('/riders/{user}/reject', [LogisticsController::class, 'rejectRider'])->name('riders.reject');

            /*
            |--------------------------------------------------------------------------
            | DELIVERY AREAS
            |--------------------------------------------------------------------------
            */

            Route::get('/delivery-areas', [LogisticsController::class, 'deliveryAreas'])->name('delivery-areas');

            /*
            |--------------------------------------------------------------------------
            | MESSAGES
            |--------------------------------------------------------------------------
            */

            Route::get('/messages', [LogisticsController::class, 'messages'])->name('messages');
            Route::post('/messages', [LogisticsController::class, 'sendMessage'])->name('messages.send');

            /*
            |--------------------------------------------------------------------------
            | REPORTS
            |--------------------------------------------------------------------------
            */

            Route::get('/reports', [LogisticsController::class, 'reports'])->name('reports');

            /*
            |--------------------------------------------------------------------------
            | PROFILE
            |--------------------------------------------------------------------------
            */

            Route::get('/profile', [LogisticsController::class, 'profile'])->name('profile');

            /*
            |--------------------------------------------------------------------------
            | ACCOUNT (alias for profile)
            |--------------------------------------------------------------------------
            */

            Route::get('/account', [LogisticsController::class, 'profile'])->name('account');

        });
    });
