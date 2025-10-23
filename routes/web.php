<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceItemController;
use App\Http\Controllers\Admin\ServiceComboController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    return view('public.home');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta protegida /dashboard
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Admin
    Route::prefix('admin')->name('admin.')->group(function () {

        // Dasboard
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::put('/update/{user}', [UserController::class, 'update'])->name('update');
        });

        // Branch
        Route::prefix('branches')->name('branches.')->group(function () {
            Route::get('/', [BranchController::class, 'index'])->name('index');
            Route::post('/store', [BranchController::class, 'store'])->name('store');
            Route::put('/update/{branch}', [BranchController::class, 'update'])->name('update');
        });

        // Customers
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::post('/store', [CustomerController::class, 'store'])->name('store');
            Route::put('/update/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/delete/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        });

        // Service categories
        Route::prefix('service-categories')->name('service-categories.')->group(function () {
            Route::get('/', [ServiceCategoryController::class, 'index'])->name('index');
            Route::post('/store', [ServiceCategoryController::class, 'store'])->name('store');
            Route::put('/update/{serviceCategory}', [ServiceCategoryController::class, 'update'])->name('update');
            Route::delete('/delete/{serviceCategory}', [ServiceCategoryController::class, 'destroy'])->name('destroy');
        });

        // Services
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/', [ServiceController::class, 'index'])->name('index');
            Route::post('/store', [ServiceController::class, 'store'])->name('store');
            Route::put('/update/{service}', [ServiceController::class, 'update'])->name('update');
            Route::delete('/delete/{service}', [ServiceController::class, 'destroy'])->name('destroy');
        });

        // Service items
        Route::prefix('service-items')->name('service-items.')->group(function () {
            Route::get('/{service_id}', [ServiceItemController::class, 'index'])->name('index');
            Route::post('/store', [ServiceItemController::class, 'store'])->name('store');
            Route::put('/update/{serviceItem}', [ServiceItemController::class, 'update'])->name('update');
            Route::delete('/delete/{serviceItem}', [ServiceItemController::class, 'destroy'])->name('destroy');
        });

        // Service combos
        Route::prefix('service-combos')->name('service-combos.')->group(function () {
            Route::get('/', [ServiceComboController::class, 'index'])->name('index');
            Route::post('/store', [ServiceComboController::class, 'store'])->name('store');
            Route::put('/update/{serviceCombo}', [ServiceComboController::class, 'update'])->name('update');
            Route::delete('/delete/{serviceCombo}', [ServiceComboController::class, 'destroy'])->name('destroy');
        });

    });

});