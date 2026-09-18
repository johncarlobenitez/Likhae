<?php

use App\Http\Controllers\AdminRegistrationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminOperationsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Middleware\EnsureWorkspaceRole;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', EnsureWorkspaceRole::class.':admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations');
    Route::post('/registrations/{user}/approve', [AdminRegistrationController::class, 'approve'])->name('registrations.approve');
    Route::post('/registrations/{user}/reject', [AdminRegistrationController::class, 'reject'])->name('registrations.reject');
    Route::get('/registrations/{user}/documents/{document}', [AdminRegistrationController::class, 'document'])->name('registrations.document');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/reactivate', [AdminUserController::class, 'reactivate'])->name('users.reactivate');
    Route::get('/products', [AdminOperationsController::class, 'products'])->name('products');
    Route::get('/compliance', [AdminOperationsController::class, 'compliance'])->name('compliance');
    Route::get('/complaints', [AdminOperationsController::class, 'complaints'])->name('complaints');
    Route::get('/finance', [AdminOperationsController::class, 'finance'])->name('finance');
    Route::get('/reports', [AdminOperationsController::class, 'reports'])->name('reports');
    Route::get('/messages', [AdminOperationsController::class, 'messages'])->name('messages');
    Route::get('/settings', [AdminOperationsController::class, 'settings'])->name('settings');
    Route::get('/account', [AdminOperationsController::class, 'account'])->name('account');
    Route::get('/notifications', [AdminOperationsController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/read-all', [AdminOperationsController::class, 'markNotificationsRead'])->name('notifications.read-all');

    Route::post('/products/{product}/moderate', [AdminOperationsController::class, 'moderateProduct'])->name('products.moderate');
    Route::post('/categories', [AdminOperationsController::class, 'storeCategory'])->name('categories.store');
    Route::patch('/categories/{category}', [AdminOperationsController::class, 'updateCategory'])->name('categories.update');
    Route::get('/products-export', [AdminOperationsController::class, 'exportProducts'])->name('products.export');
    Route::patch('/refunds/{refund}', [AdminOperationsController::class, 'updateRefund'])->name('refunds.update');
    Route::get('/finance-export', [AdminOperationsController::class, 'exportFinance'])->name('finance.export');
    Route::get('/reports-export', [AdminOperationsController::class, 'exportReport'])->name('reports.export');
    Route::post('/messages/send', [AdminOperationsController::class, 'sendMessage'])->name('messages.send');
    Route::post('/announcements', [AdminOperationsController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::patch('/settings', [AdminOperationsController::class, 'updateSettings'])->name('settings.update');
    Route::post('/policies', [AdminOperationsController::class, 'storePolicy'])->name('policies.store');
    Route::patch('/policies/{policy}', [AdminOperationsController::class, 'updatePolicy'])->name('policies.update');
    Route::get('/audit-export', [AdminOperationsController::class, 'exportAuditLogs'])->name('audit.export');
    Route::patch('/account', [AdminOperationsController::class, 'updateAccount'])->name('account.update');
    Route::patch('/account/password', [AdminOperationsController::class, 'updatePassword'])->name('account.password');
    Route::patch('/account/preferences', [AdminOperationsController::class, 'updatePreferences'])->name('account.preferences');
    Route::post('/notifications/{notification}/read', [AdminOperationsController::class, 'markNotificationRead'])->name('notifications.read');

    // Compatibility aliases for the original Admin frontend routes.
    Route::get('/sellers/approvals', fn () => redirect()->route('admin.registrations', ['type' => 'sellers']))->name('sellers.approvals');
    Route::get('/riders', fn () => redirect()->route('admin.users', ['role' => 'riders']))->name('riders');
    Route::get('/categories', fn () => redirect()->route('admin.products', ['view' => 'categories']))->name('categories');
    Route::get('/payments', fn () => redirect()->route('admin.finance', ['tab' => 'payments']))->name('payments');
    Route::get('/refunds', fn () => redirect()->route('admin.complaints', ['tab' => 'returns']))->name('refunds');
});
