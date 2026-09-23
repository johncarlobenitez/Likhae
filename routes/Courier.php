<?php

use Illuminate\Support\Facades\Route;

// Preserve old courier links while using the authenticated rider workspace.
Route::prefix('courier')->name('courier.')->middleware(['auth', 'verified', 'workspace.role:rider', 'rider.active'])->group(function () {
    Route::get('/', fn () => redirect()->route('rider.dashboard'));
    foreach (['home' => 'dashboard', 'pickups' => 'pickups', 'deliveries' => 'deliveries', 'history' => 'history', 'earnings' => 'earnings', 'messages' => 'messages', 'account' => 'account'] as $legacy => $page) {
        Route::get('/'.$legacy, fn () => redirect()->route('rider.'.$page))->name($legacy);
    }
    Route::get('/application-status', fn () => redirect()->route('rider.dashboard'))->name('application-status');
});
