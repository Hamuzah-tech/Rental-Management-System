<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\LandlordController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LandlordLoginController;
use App\Http\Controllers\Landlord\DashboardController as LandlordDashboard;


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
    | Common Dashboard
    |--------------------------------------------------------------------------
    */

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


            /*
            | Dashboard
            */

            Route::get('/dashboard', [AdminDashboard::class, 'index'])
                ->name('dashboard');


            /*
            | Property Management
            */

            Route::resource('properties', PropertyController::class);



            /*
            | Landlord Management
            */

            Route::resource('landlords', LandlordController::class);


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

            Route::resource('tenants', TenantController::class);
        });




   /*
|--------------------------------------------------------------------------
| Landlord Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:Landlord'])
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
            \App\Http\Controllers\Landlord\PropertyController::class
        );


        Route::patch('/properties/{property}/status', [
            \App\Http\Controllers\Landlord\PropertyController::class,
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
            \App\Http\Controllers\Landlord\TenantController::class
        );


        Route::patch('/tenants/{tenant}/move-out', [
            \App\Http\Controllers\Landlord\TenantController::class,
            'moveOut'
        ])
        ->name('tenants.moveout');


        Route::patch('/tenants/{tenant}/reactivate', [
            \App\Http\Controllers\Landlord\TenantController::class,
            'reactivate'
        ])
        ->name('tenants.reactivate');

    });
});

/*
|--------------------------------------------------------------------------
| Admin Login
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/admin/login', [AdminLoginController::class, 'create'])
        ->name('admin.login');

    Route::post('/admin/login', [AdminLoginController::class, 'store']);

});


/*
|--------------------------------------------------------------------------
| Landlord Login
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/landlord/login', [LandlordLoginController::class, 'create'])
        ->name('landlord.login');

    Route::post('/landlord/login', [LandlordLoginController::class, 'store']);

});
require __DIR__.'/auth.php';