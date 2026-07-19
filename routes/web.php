<?php

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
use App\Http\Controllers\Tenant\MoveOutNoticeController as TenantMoveOutNoticeController;
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
    Route::get('/admin/login', [AdminLoginController::class, 'create'])
        ->name('admin.login');

    Route::post('/admin/login', [AdminLoginController::class, 'store'])
        ->name('admin.login.store');
});

/*
|--------------------------------------------------------------------------
| Landlord Login
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/landlord/login', [LandlordLoginController::class, 'create'])
        ->name('landlord.login');

    Route::post('/landlord/login', [LandlordLoginController::class, 'store'])
        ->name('landlord.login.store');
});

/*
|--------------------------------------------------------------------------
| Landlord Password Reset Routes
|--------------------------------------------------------------------------
*/
Route::prefix('landlord')
    ->name('landlord.')
    ->group(function() {
        Route::get('password/reset', [App\Http\Controllers\Landlord\ForgotPasswordController::class, 'showLinkRequestForm'])
            ->name('password.request');
        Route::post('password/email', [App\Http\Controllers\Landlord\ForgotPasswordController::class, 'sendResetLinkEmail'])
            ->name('password.email');
        Route::get('password/reset/{token}', [App\Http\Controllers\Landlord\ResetPasswordController::class, 'showResetForm'])
            ->name('password.reset');
        Route::post('password/reset', [App\Http\Controllers\Landlord\ResetPasswordController::class, 'reset'])
            ->name('password.update');
    });

/*
|--------------------------------------------------------------------------
| Tenant Registration Routes (Public Access)
|--------------------------------------------------------------------------
*/
Route::prefix('register')
    ->name('tenant.')
    ->group(function () {
        Route::get('/property/{token}', [TenantRegistrationController::class, 'show'])
            ->name('registration');
        
        Route::post('/property/{token}', [TenantRegistrationController::class, 'store'])
            ->name('registration.store');
        
        Route::get('/success/{tenant}', [TenantRegistrationController::class, 'success'])
            ->name('registration.success');
        
        Route::get('/full/{property}', [TenantRegistrationController::class, 'full'])
            ->name('registration.full');
    });

/*
|--------------------------------------------------------------------------
| Tenant Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
Route::prefix('tenant')
    ->name('tenant.')
    ->group(function () {
        // Payment routes - accessible without login
        Route::get('/payments', [TenantPaymentController::class, 'index'])
            ->name('payments.index');
        
        Route::get('/payments/create', [TenantPaymentController::class, 'create'])
            ->name('payments.create');
        
        Route::post('/payments', [TenantPaymentController::class, 'store'])
            ->name('payments.store');
        
        Route::get('/payments/history', [TenantPaymentController::class, 'history'])
            ->name('payments.history');
        
        Route::post('/payments/search', [TenantPaymentController::class, 'search'])
            ->name('payments.search');
        
        // Public payment search
        Route::post('/payments/public-search', [TenantPaymentController::class, 'publicSearch'])
            ->name('payments.public-search');
        
        // Public payment history with reference
        Route::get('/payments/history/{reference}', [TenantPaymentController::class, 'publicHistory'])
            ->name('payments.public-history');
        
        // Move out routes - accessible without login
        Route::get('/move-out', [TenantMoveOutNoticeController::class, 'create'])
            ->name('moveout.create');
        
        Route::post('/move-out', [TenantMoveOutNoticeController::class, 'store'])
            ->name('moveout.store');
        
        Route::get('/move-out/success', [TenantMoveOutNoticeController::class, 'success'])
            ->name('moveout.success');
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
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Super Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:Super Admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminDashboard::class, 'index'])
                ->name('dashboard');

            Route::resource('properties', PropertyController::class);
            Route::resource('landlords', LandlordController::class);
            Route::resource('tenants', TenantController::class);

            Route::patch('/landlords/{landlord}/status', [LandlordController::class, 'toggleStatus'])
                ->name('landlords.status');

            Route::post('/landlords/{landlord}/reset-password', [LandlordController::class, 'resetPassword'])
                ->name('landlords.reset-password');

            /*
            |--------------------------------------------------------------------------
            | Admin Settings
            |--------------------------------------------------------------------------
            */
            Route::get('/settings', [SettingsController::class, 'index'])
                ->name('settings.index');

            Route::post('/settings/backup', [SettingsController::class, 'backup'])
                ->name('settings.backup');

            Route::get('/settings/download/{file}', [SettingsController::class, 'download'])
                ->name('settings.download');

            Route::delete('/settings/delete/{file}', [SettingsController::class, 'delete'])
                ->name('settings.delete');
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

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */
            Route::get('/dashboard', [LandlordDashboard::class, 'index'])
                ->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Properties
            |--------------------------------------------------------------------------
            */
            Route::resource('properties', LandlordPropertyController::class);

            Route::patch('/properties/{property}/status', [LandlordPropertyController::class, 'toggleStatus'])
                ->name('properties.status');

            /*
            |--------------------------------------------------------------------------
            | Tenants
            |--------------------------------------------------------------------------
            */
            Route::resource('tenants', LandlordTenantController::class);

            Route::post('/tenants/generate-link', [LandlordTenantController::class, 'generateRegistrationLink'])
                ->name('tenants.generate-link');

            Route::patch('/tenants/{tenant}/move-out', [LandlordTenantController::class, 'moveOut'])
                ->name('tenants.moveout');

            Route::patch('/tenants/{tenant}/reactivate', [LandlordTenantController::class, 'reactivate'])
                ->name('tenants.reactivate');

            /*
            |--------------------------------------------------------------------------
            | Tenant Exports
            |--------------------------------------------------------------------------
            */
            Route::get('/tenants/export/excel', [LandlordTenantController::class, 'exportExcel'])
                ->name('tenants.export.excel');

            Route::get('/tenants/export/pdf', [LandlordTenantController::class, 'exportPdf'])
                ->name('tenants.export.pdf');

            /*
            |--------------------------------------------------------------------------
            | Payments
            |--------------------------------------------------------------------------
            */
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

/*
|--------------------------------------------------------------------------
| Additional Routes
|--------------------------------------------------------------------------
*/
Route::get('/tenants/property/{property}/registration-link', [TenantController::class, 'registrationLink'])
    ->name('landlord.tenants.registration-link');

require __DIR__.'/auth.php';