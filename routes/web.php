<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Landlord\DashboardController as LandlordDashboard;
use App\Http\Controllers\Admin\LandlordController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

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

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

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
    | Super Admin
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Super Admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [AdminDashboard::class, 'index'])
                ->name('dashboard');

            Route::resource('landlords', LandlordController::class);

            Route::patch('/landlords/{landlord}/status', [LandlordController::class, 'toggleStatus'])
                ->name('landlords.status');

            Route::post('/landlords/{landlord}/reset-password', [LandlordController::class, 'resetPassword'])
                ->name('landlords.reset-password');
        });

    /*
    |--------------------------------------------------------------------------
    | Landlord
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Landlord')
        ->prefix('landlord')
        ->name('landlord.')
        ->group(function () {

            Route::get('/dashboard', [LandlordDashboard::class, 'index'])
                ->name('dashboard');

        });

});

require __DIR__.'/auth.php';