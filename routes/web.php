<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\ComplementaryProductCategoryController;
use App\Http\Controllers\Admin\ComplementaryProductController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\PaymentSubmethodController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\OrderItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderStatusController;
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

        // Order Status
        Route::prefix('order-status')->name('order-status.')->group(function () {
            Route::get('/', [OrderStatusController::class, 'index'])->name('index');
            Route::post('/store', [OrderStatusController::class, 'store'])->name('store');
            Route::put('/update/{orderStatus}', [OrderStatusController::class, 'update'])->name('update');
            Route::delete('/delete/{orderStatus}', [OrderStatusController::class, 'destroy'])->name('destroy');
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/create', [OrderController::class, 'create'])->name('create');
            Route::post('/store', [OrderController::class, 'store'])->name('store');
            Route::get('/show/{order}', [OrderController::class, 'show'])->name('show');
            Route::get('/edit/{order}', [OrderController::class, 'edit'])->name('edit');
            Route::put('/update/{order}', [OrderController::class, 'update'])->name('update');
            Route::delete('/delete/{order}', [OrderController::class, 'destroy'])->name('destroy');
            //Ticket
            Route::get('/ticket/{id}', [TicketController::class, 'show'])->name('ticket');
            //Llevar Lavanaderia / Estados
            Route::post('/change-status', [OrderController::class, 'changeStatus'])->name('changeStatus');
            Route::get('/change-status', [OrderController::class, 'changeStatusView'])->name('changeStatus.view');

        });

        // Order Items
        Route::prefix('order-items')->name('order-items.')->group(function () {
            Route::get('/{order_id}', [OrderItemController::class, 'index'])->name('index');
            Route::post('/store', [OrderItemController::class, 'store'])->name('store');
            Route::put('/update/{orderItem}', [OrderItemController::class, 'update'])->name('update');
            Route::delete('/delete/{orderItem}', [OrderItemController::class, 'destroy'])->name('destroy');
        });

        // Payment Methods
        Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
            Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
            Route::get('/create', [PaymentMethodController::class, 'create'])->name('create');
            Route::post('/store', [PaymentMethodController::class, 'store'])->name('store');
            Route::get('/{paymentMethod}/edit', [PaymentMethodController::class, 'edit'])->name('edit');
            Route::put('/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('update');
            Route::delete('/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('destroy');
        });

        // Payment Submethods
        Route::prefix('payment-submethods')->name('payment-submethods.')->group(function () {
            Route::get('/', [PaymentSubmethodController::class, 'index'])->name('index');
            Route::get('/by-method/{methodId}', [PaymentSubmethodController::class, 'getByMethod'])->name('byMethod');
            Route::get('/create', [PaymentSubmethodController::class, 'create'])->name('create');
            Route::post('/store', [PaymentSubmethodController::class, 'store'])->name('store');
            Route::get('/{paymentSubmethod}/edit', [PaymentSubmethodController::class, 'edit'])->name('edit');
            Route::put('/{paymentSubmethod}', [PaymentSubmethodController::class, 'update'])->name('update');
            Route::delete('/{paymentSubmethod}', [PaymentSubmethodController::class, 'destroy'])->name('destroy');
        });

        // Complementary Product Categories
        Route::prefix('complementary-product-categories')->name('complementary-product-categories.')->group(function () {
            Route::get('/', [ComplementaryProductCategoryController::class, 'index'])->name('index');
            Route::post('/store', [ComplementaryProductCategoryController::class, 'store'])->name('store');
            Route::put('/update/{category}', [ComplementaryProductCategoryController::class, 'update'])->name('update');
            Route::delete('/delete/{category}', [ComplementaryProductCategoryController::class, 'destroy'])->name('destroy');
        });

        // Complementary Products
        Route::prefix('complementary-products')->name('complementary-products.')->group(function () {
            Route::get('/{category_id}', [ComplementaryProductController::class, 'index'])->name('index');
            Route::post('/store', [ComplementaryProductController::class, 'store'])->name('store');
            Route::put('/update/{product}', [ComplementaryProductController::class, 'update'])->name('update');
            Route::delete('/delete/{product}', [ComplementaryProductController::class, 'destroy'])->name('destroy');
        });

        //POS
        Route::prefix('pos')->name('pos.')->group(function () {
            Route::get('/', [PosController::class, 'index'])->name('index');
            Route::get('/{orderNumber}/details', [PosController::class, 'findByNumber'])->name('findByNumber');
            Route::get('/buscar-cliente', [PosController::class, 'buscarCliente'])->name('buscarCliente');
        });
    });

});