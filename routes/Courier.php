<?php

use Illuminate\Support\Facades\Route;

Route::prefix('courier')->name('courier.')->middleware('workspace.role:courier')->group(function () {
    Route::get('/', fn () => redirect()->route('courier.home'));
    Route::view('/home', 'Courier.home')->name('home');
    Route::get('/pickups', fn () => view('Courier.home', ['mode' => 'pickups']))->name('pickups');
    Route::get('/deliveries', fn () => view('Courier.home', ['mode' => 'deliveries']))->name('deliveries');
    Route::get('/history', fn () => view('Courier.home', ['mode' => 'history']))->name('history');
    Route::get('/earnings', fn () => view('Courier.home', ['mode' => 'earnings']))->name('earnings');
    Route::get('/messages', fn () => view('Courier.home', ['mode' => 'messages']))->name('messages');
    Route::get('/account', fn () => view('Courier.home', ['mode' => 'account']))->name('account');
});

Route::prefix('courier')->name('courier.')->group(function () {
    Route::get('/application-status', fn () => view('auth.pending', ['accountType' => 'Rider']))->name('application-status');
});