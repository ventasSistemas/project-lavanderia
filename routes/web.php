<?php

use Illuminate\Support\Facades\Route;
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

    });

});