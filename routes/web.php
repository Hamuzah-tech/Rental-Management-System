<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Authentication Controllers
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LandlordLoginController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\LandlordController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\SettingsController;

// Landlord Controllers
use App\Http\Controllers\Landlord\DashboardController as LandlordDashboard;
use App\Http\Controllers\Landlord\PropertyController as LandlordPropertyController;
use App\Http\Controllers\Landlord\TenantController as LandlordTenantController;
use App\Http\Controllers\Landlord\PaymentController as LandlordPaymentController;

// Tenant Controllers
use App\Http\Controllers\Tenant\PaymentController as TenantPaymentController;
use App\Http\Controllers\TenantRegistrationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| Admin Login
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'store'])->name('admin.login.store');
});

/*
|--------------------------------------------------------------------------
| Landlord Login
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/landlord/login', [LandlordLoginController::class, 'create'])->name('landlord.login');
    Route::post('/landlord/login', [LandlordLoginController::class, 'store'])->name('landlord.login.store');
});

/*
|--------------------------------------------------------------------------
| Landlord Password Reset Routes
|--------------------------------------------------------------------------
*/
Route::prefix('landlord')->name('landlord.')->group(function() {
    Route::get('password/reset', [App\Http\Controllers\Landlord\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [App\Http\Controllers\Landlord\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [App\Http\Controllers\Landlord\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [App\Http\Controllers\Landlord\ResetPasswordController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Tenant Registration Routes (Public Access)
|--------------------------------------------------------------------------
*/
Route::prefix('register')->name('tenant.')->group(function () {
    Route::get('/property/{token}', [TenantRegistrationController::class, 'show'])->name('registration');
    Route::post('/property/{token}', [TenantRegistrationController::class, 'store'])->name('registration.store');
    Route::get('/success/{tenant}', [TenantRegistrationController::class, 'success'])->name('registration.success');
    Route::get('/full/{property}', [TenantRegistrationController::class, 'full'])->name('registration.full');
});

/*
|--------------------------------------------------------------------------
| Tenant Phone Check Route (Public - For Uniqueness Validation)
|--------------------------------------------------------------------------
*/
Route::post('/tenant/check-phone', [TenantRegistrationController::class, 'checkPhone'])
    ->name('tenant.check-phone');

/*
|--------------------------------------------------------------------------
| Tenant Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
Route::prefix('tenant')->name('tenant.')->group(function () {
    // Payment Routes
    Route::get('/payments', [TenantPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [TenantPaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [TenantPaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/history', [TenantPaymentController::class, 'history'])->name('payments.history');
    Route::post('/payments/search', [TenantPaymentController::class, 'search'])->name('payments.search');
    Route::post('/payments/public-search', [TenantPaymentController::class, 'publicSearch'])->name('payments.public-search');
    Route::get('/payments/history/{reference}', [TenantPaymentController::class, 'publicHistory'])->name('payments.public-history');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Super Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:Super Admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

            // PROPERTIES with Soft Delete
            Route::get('/properties/trashed', [PropertyController::class, 'trashed'])
                ->name('trash.properties');
            Route::patch('/properties/trashed/{id}/restore', [PropertyController::class, 'restore'])
                ->name('trash.properties.restore');
            Route::delete('/properties/trashed/{id}/force-delete', [PropertyController::class, 'forceDelete'])
                ->name('trash.properties.force-delete');
            Route::resource('properties', PropertyController::class);

            // TENANTS with Soft Delete
            Route::get('/tenants/trashed', [TenantController::class, 'trashed'])
                ->name('trash.tenants');
            Route::patch('/tenants/trashed/{id}/restore', [TenantController::class, 'restore'])
                ->name('trash.tenants.restore');
            Route::delete('/tenants/trashed/{id}/force-delete', [TenantController::class, 'forceDelete'])
                ->name('trash.tenants.force-delete');
            Route::resource('tenants', TenantController::class);

            // LANDLORDS with Soft Delete
            Route::get('/landlords/trashed', [LandlordController::class, 'trashed'])
                ->name('trash.landlords');
            Route::patch('/landlords/trashed/{id}/restore', [LandlordController::class, 'restore'])
                ->name('trash.landlords.restore');
            Route::delete('/landlords/trashed/{id}/force-delete', [LandlordController::class, 'forceDelete'])
                ->name('trash.landlords.force-delete');
            Route::resource('landlords', LandlordController::class);

            Route::patch('/landlords/{landlord}/status', [LandlordController::class, 'toggleStatus'])
                ->name('landlords.status');
            Route::post('/landlords/{landlord}/reset-password', [LandlordController::class, 'resetPassword'])
                ->name('landlords.reset-password');

            // Admin Settings
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
            Route::post('/settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
            Route::get('/settings/download/{file}', [SettingsController::class, 'download'])->name('settings.download');
            Route::delete('/settings/delete/{file}', [SettingsController::class, 'delete'])->name('settings.delete');
        });

    /*
    |--------------------------------------------------------------------------
    | Landlord Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:Landlord')
        ->prefix('landlord')
        ->name('landlord.')
        ->group(function () {
            // Dashboard
            Route::get('/dashboard', [LandlordDashboard::class, 'index'])->name('dashboard');

            // Properties with Soft Delete & PDF Export
            Route::get('/properties/trashed', [LandlordPropertyController::class, 'trashed'])
                ->name('properties.trashed');
            Route::patch('/properties/{id}/restore', [LandlordPropertyController::class, 'restore'])
                ->name('properties.restore');
            Route::resource('properties', LandlordPropertyController::class);
            Route::patch('/properties/{property}/status', [LandlordPropertyController::class, 'toggleStatus'])
                ->name('properties.status');
            Route::get('/properties/{property}/export-pdf', [LandlordPropertyController::class, 'exportPdf'])
                ->name('properties.export.pdf');

            // Tenants with Soft Delete
            Route::get('/tenants/trashed', [LandlordTenantController::class, 'trashed'])
                ->name('tenants.trashed');
            Route::patch('/tenants/{id}/restore', [LandlordTenantController::class, 'restore'])
                ->name('tenants.restore');
            Route::resource('tenants', LandlordTenantController::class);
            Route::post('/tenants/generate-link', [LandlordTenantController::class, 'generateRegistrationLink'])
                ->name('tenants.generate-link');
            Route::post('/tenants/copy-link', [LandlordTenantController::class, 'copyRegistrationLink'])
                ->name('tenants.copy-link');
            Route::patch('/tenants/{tenant}/move-out', [LandlordTenantController::class, 'moveOut'])
                ->name('tenants.moveout');
            Route::patch('/tenants/{tenant}/reactivate', [LandlordTenantController::class, 'reactivate'])
                ->name('tenants.reactivate');

            // Tenant Exports
            Route::get('/tenants/export/excel', [LandlordTenantController::class, 'exportExcel'])
                ->name('tenants.export.excel');
            Route::get('/tenants/export/pdf', [LandlordTenantController::class, 'exportPdf'])
                ->name('tenants.export.pdf');

            // Payments
            Route::get('/payments', [LandlordPaymentController::class, 'index'])
                ->name('payments.index');
            Route::get('/payments/{payment}', [LandlordPaymentController::class, 'show'])
                ->name('payments.show');
            Route::patch('/payments/{payment}/approve', [LandlordPaymentController::class, 'approve'])
                ->name('payments.approve');
            Route::patch('/payments/{payment}/reject', [LandlordPaymentController::class, 'reject'])
                ->name('payments.reject');
        });
});

Route::get('/tenants/property/{property}/registration-link', [TenantController::class, 'registrationLink'])
    ->name('landlord.tenants.registration-link');

require __DIR__.'/auth.php';