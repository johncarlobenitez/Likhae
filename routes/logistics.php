<?php

use Illuminate\Support\Facades\Route;



Route::prefix('logistics')
    ->name('logistics.')
    ->group(function () {



        /*
        |--------------------------------------------------------------------------
        | HOME
        |--------------------------------------------------------------------------
        */

        Route::get('/', function () {

            return redirect()
                ->route('logistics.dashboard');

        })->name('home');




        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', function () {

            return view(
                'logistics.dashboard.index'
            );

        })->name('dashboard');







        /*
        |--------------------------------------------------------------------------
        | PARCELS
        |--------------------------------------------------------------------------
        */


        Route::get('/parcels', function () {

            return view(
                'logistics.parcels.index'
            );

        })->name('parcels');



        Route::get('/parcels/receive', function () {

            return view(
                'logistics.parcels.receive'
            );

        })->name('parcels.receive');




        Route::get('/parcels/tracking', function () {

            return view(
                'logistics.tracking.index'
            );

        })->name('parcels.tracking');




        Route::get('/parcels/{id}', function ($id) {

            return view(
                'logistics.parcels.show',
                [
                    'id'=>$id
                ]
            );

        })->name('parcels.show');







        /*
        |--------------------------------------------------------------------------
        | SORTING CENTER
        |--------------------------------------------------------------------------
        */


        Route::get('/sorting', function () {

            return view(
                'logistics.sorting.index'
            );

        })->name('sorting');







        /*
        |--------------------------------------------------------------------------
        | RIDER ASSIGNMENTS
        |--------------------------------------------------------------------------
        */


        Route::get('/assignments', function () {

            return view(
                'logistics.assignments.index'
            );

        })->name('assignments');



        Route::get('/assignments/{id}/assign', function ($id) {


            return view(
                'logistics.assignments.assign',
                [
                    'id'=>$id
                ]
            );


        })->name('assignments.assign');







        /*
        |--------------------------------------------------------------------------
        | RIDERS
        |--------------------------------------------------------------------------
        */


        Route::get('/riders', function () {

            return view(
                'logistics.riders.index'
            );

        })->name('riders');




        /*
        |--------------------------------------------------------------------------
        | RIDER APPLICATIONS
        |--------------------------------------------------------------------------
        */


        Route::get('/riders/applications', function () {

            return view('logistics.riders.application.index');

        })->name('riders.applications');



        Route::get('/riders/applications/{id}', function ($id) {

            $rider = [
                'id'      => $id,
                'name'    => 'Juan Dela Cruz',
                'status'  => session('rider_status', 'Pending Approval'),
                'email'   => 'juan@email.com',
                'contact' => '09175551234',
                'vehicle' => 'Motorcycle',
                'plate'   => 'ABC-1234',
            ];

            return view('logistics.riders.application.show', compact('rider'));

        })->name('riders.applications.show');







        /*
        |--------------------------------------------------------------------------
        | VIEW RIDER PROFILE
        |--------------------------------------------------------------------------
        */


        Route::get('/riders/{id}', function ($id) {



            return view(
                'logistics.riders.show',
                [

                    'rider'=>[

                        'id'=>$id,

                        'name'=>'Juan Dela Cruz',

                        'status'=>session(
                            'rider_status',
                            'Pending Approval'
                        ),


                        'email'=>'juan@email.com',

                        'contact'=>'09175551234',

                        'vehicle'=>'Motorcycle',

                        'plate'=>'ABC-1234'

                    ]


                ]
            );



        })->name('riders.show');







        /*
        |--------------------------------------------------------------------------
        | APPROVE RIDER
        |--------------------------------------------------------------------------
        */


        Route::post('/riders/{id}/approve', function ($id) {

            session(['rider_status' => 'Approved']);

            return redirect()
                ->route('logistics.riders.applications.show', $id)
                ->with('success', 'Rider application approved successfully.');

        })->name('riders.approve');







        /*
        |--------------------------------------------------------------------------
        | REJECT RIDER
        |--------------------------------------------------------------------------
        */


        Route::post('/riders/{id}/reject', function ($id) {

            session(['rider_status' => 'Rejected']);

            return redirect()
                ->route('logistics.riders.applications.show', $id)
                ->with('success', 'Rider application rejected.');

        })->name('riders.reject');







        /*
        |--------------------------------------------------------------------------
        | DELIVERY AREAS
        |--------------------------------------------------------------------------
        */


        Route::get('/delivery-areas', function () {


            return view(
                'logistics.delivery-areas.index'
            );


        })->name('delivery-areas');







        /*
        |--------------------------------------------------------------------------
        | MESSAGES
        |--------------------------------------------------------------------------
        */


        Route::get('/messages', function () {


            return view(
                'logistics.messages.index'
            );


        })->name('messages');







        /*
        |--------------------------------------------------------------------------
        | REPORTS
        |--------------------------------------------------------------------------
        */


        Route::get('/reports', function () {


            return view(
                'logistics.reports.index'
            );


        })->name('reports');







        /*
        |--------------------------------------------------------------------------
        | PROFILE
        |--------------------------------------------------------------------------
        */


        Route::get('/profile', function () {


            return view(
                'logistics.profile.index'
            );


        })->name('profile');




        /*
        |--------------------------------------------------------------------------
        | ACCOUNT (alias for profile)
        |--------------------------------------------------------------------------
        */


        Route::get('/account', function () {


            return view(
                'logistics.profile.index'
            );


        })->name('account');


});
