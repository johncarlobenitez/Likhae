<?php

use App\Http\Controllers\AdminRegistrationController;
use App\Http\Middleware\EnsureWorkspaceRole;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', EnsureWorkspaceRole::class.':admin'])->group(function () {
    Route::view('/', 'Admin.dashboard')->name('dashboard');

    Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations');
    Route::post('/registrations/{user}/approve', [AdminRegistrationController::class, 'approve'])->name('registrations.approve');
    Route::post('/registrations/{user}/reject', [AdminRegistrationController::class, 'reject'])->name('registrations.reject');
    Route::get('/registrations/{user}/documents/{document}', [AdminRegistrationController::class, 'document'])->name('registrations.document');
    Route::view('/users', 'Admin.users')->name('users');
    Route::view('/products', 'Admin.products')->name('products');
    Route::view('/compliance', 'Admin.compliance')->name('compliance');
    Route::view('/complaints', 'Admin.complaints')->name('complaints');
    Route::view('/finance', 'Admin.finance')->name('finance');
    Route::view('/reports', 'Admin.reports')->name('reports');
    Route::view('/messages', 'Admin.messages')->name('messages');
    Route::view('/settings', 'Admin.settings')->name('settings');
    Route::view('/account', 'Admin.account')->name('account');
    Route::view('/notifications', 'Admin.notifications')->name('notifications');

    // Compatibility aliases for the original Admin frontend routes.
    Route::get('/sellers/approvals', fn () => redirect()->route('admin.registrations', ['type' => 'sellers']))->name('sellers.approvals');
    Route::get('/riders', fn () => redirect()->route('admin.users', ['role' => 'riders']))->name('riders');
    Route::get('/categories', fn () => redirect()->route('admin.products', ['view' => 'categories']))->name('categories');
    Route::get('/payments', fn () => redirect()->route('admin.finance', ['tab' => 'payments']))->name('payments');
    Route::get('/refunds', fn () => redirect()->route('admin.complaints', ['tab' => 'returns']))->name('refunds');
});
