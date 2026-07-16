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

// Landlord Controllers
use App\Http\Controllers\Landlord\DashboardController as LandlordDashboard;
use App\Http\Controllers\Landlord\PropertyController as LandlordPropertyController;
use App\Http\Controllers\Landlord\TenantController as LandlordTenantController;
use App\Http\Controllers\Landlord\PaymentController as LandlordPaymentController;
use App\Http\Controllers\Landlord\MoveOutNoticeController as LandlordMoveOutNoticeController;

// Tenant Controllers
use App\Http\Controllers\Tenant\PaymentController;
use App\Http\Controllers\Tenant\MoveOutNoticeController;
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
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])
    ->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])
    ->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])
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
            Route::get('/dashboard', [
                AdminDashboard::class,
                'index'
            ])
            ->name('dashboard');

            Route::resource(
                'properties',
                PropertyController::class
            );

            Route::resource(
                'landlords',
                LandlordController::class
            );

            Route::patch('/landlords/{landlord}/status', [
                LandlordController::class,
                'toggleStatus'
            ])
            ->name('landlords.status');

            Route::post('/landlords/{landlord}/reset-password', [
                LandlordController::class,
                'resetPassword'
            ])
            ->name('landlords.reset-password');

            Route::resource(
                'tenants',
                TenantController::class
            );
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
            Route::get('/dashboard', [
                LandlordDashboard::class,
                'index'
            ])
            ->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Properties
            |--------------------------------------------------------------------------
            */
            Route::resource(
                'properties',
                LandlordPropertyController::class
            );

            Route::patch('/properties/{property}/status', [
                LandlordPropertyController::class,
                'toggleStatus'
            ])
            ->name('properties.status');

            /*
            |--------------------------------------------------------------------------
            | Tenants
            |--------------------------------------------------------------------------
            */
            Route::resource(
                'tenants',
                LandlordTenantController::class
            );

            // ADD THIS ROUTE FOR GENERATING REGISTRATION LINKS
            Route::post('/tenants/generate-link', [
                LandlordTenantController::class,
                'generateRegistrationLink'
            ])
            ->name('tenants.generate-link');

            Route::patch('/tenants/{tenant}/move-out', [
                LandlordTenantController::class,
                'moveOut'
            ])
            ->name('tenants.moveout');

            Route::patch('/tenants/{tenant}/reactivate', [
                LandlordTenantController::class,
                'reactivate'
            ])
            ->name('tenants.reactivate');

            /*
            |--------------------------------------------------------------------------
            | Payments
            |--------------------------------------------------------------------------
            */
            Route::get('/payments', [
                LandlordPaymentController::class,
                'index'
            ])
            ->name('payments.index');

            Route::get('/payments/{payment}', [
                LandlordPaymentController::class,
                'show'
            ])
            ->name('payments.show');

            Route::patch('/payments/{payment}/approve', [
                LandlordPaymentController::class,
                'approve'
            ])
            ->name('payments.approve');

            Route::patch('/payments/{payment}/reject', [
                LandlordPaymentController::class,
                'reject'
            ])
            ->name('payments.reject');

            /*
            |--------------------------------------------------------------------------
            | Move Out Notices
            |--------------------------------------------------------------------------
            */
            Route::get('/move-out-notices', [
                LandlordMoveOutNoticeController::class,
                'index'
            ])
            ->name('moveouts.index');

            Route::patch('/move-out-notices/{notice}/confirm', [
                LandlordMoveOutNoticeController::class,
                'confirm'
            ])
            ->name('moveouts.confirm');

            Route::patch('/move-out-notices/{notice}/cancel', [
                LandlordMoveOutNoticeController::class,
                'cancel'
            ])
            ->name('moveouts.cancel');
        });
});

/*
|--------------------------------------------------------------------------
| Admin Login
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [
        AdminLoginController::class,
        'create'
    ])
    ->name('admin.login');

    Route::post('/admin/login', [
        AdminLoginController::class,
        'store'
    ]);
});

/*
|--------------------------------------------------------------------------
| Landlord Login
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/landlord/login', [
        LandlordLoginController::class,
        'create'
    ])
    ->name('landlord.login');

    Route::post('/landlord/login', [
        LandlordLoginController::class,
        'store'
    ]);
});

/*
|--------------------------------------------------------------------------
| Tenant Public Portal
|--------------------------------------------------------------------------
*/
Route::prefix('tenant')
    ->name('tenant.')
    ->group(function () {
        Route::get('/', [
            PaymentController::class,
            'index'
        ])
        ->name('payments.index');

        Route::get('/payments/create', [
            PaymentController::class,
            'create'
        ])
        ->name('payments.create');

        Route::post('/payments', [
            PaymentController::class,
            'store'
        ])
        ->name('payments.store');

        Route::get('/payments/history', [
            PaymentController::class,
            'history'
        ])
        ->name('payments.history');

        Route::post('/payments/history', [
            PaymentController::class,
            'search'
        ])
        ->name('payments.search');

        Route::get('/move-out', [
            MoveOutNoticeController::class,
            'create'
        ])
        ->name('moveout.create');

        Route::post('/move-out', [
            MoveOutNoticeController::class,
            'store'
        ])
        ->name('moveout.store');
    });

// For landlord password reset routes
Route::prefix('landlord')->name('landlord.')->group(function() {
    Route::get('password/reset', [App\Http\Controllers\Landlord\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [App\Http\Controllers\Landlord\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [App\Http\Controllers\Landlord\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [App\Http\Controllers\Landlord\ResetPasswordController::class, 'reset'])->name('password.update');
});

// Tenant Registration Routes
Route::get('/register/property/{token}', [TenantRegistrationController::class, 'show'])
    ->name('tenant.register');

Route::post('/register/property/{token}', [TenantRegistrationController::class, 'store'])
    ->name('tenant.register.store');

Route::get(
    '/registration-success/{tenant}',
    [TenantRegistrationController::class, 'success']
)->name('tenant.registration.success');

Route::get(
    '/tenants/property/{property}/registration-link',
    [TenantController::class,'registrationLink']
)->name('landlord.tenants.registration-link');

Route::get('/tenant/move-out/success', [MoveOutNoticeController::class, 'success'])
    ->name('tenant.moveout.success');

require __DIR__.'/auth.php';