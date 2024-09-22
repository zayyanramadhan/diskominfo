<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrdersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


    Route::prefix('products')->group(function () {
        Route::post('/', [ProductsController::class, 'create'])->name('products.create');
        Route::get('/', [ProductsController::class, 'index'])->name('products.index');
        Route::get('/{product}', [ProductsController::class, 'detail'])->name('products.detail');
        Route::put('/{product}', [ProductsController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductsController::class, 'delete'])->name('products.delete');   
    });

    Route::prefix('orders')->group(function () {
        Route::post('/', [OrdersController::class, 'create'])->name('orders.create');
        Route::get('/', [OrdersController::class, 'index'])->name('orders.index');
        Route::get('/{orders}', [OrdersController::class, 'detail'])->name('orders.detail');
        Route::put('/{orders}', [OrdersController::class, 'update'])->name('orders.update');
        Route::delete('/{orders}', [OrdersController::class, 'delete'])->name('orders.delete');   
    });
