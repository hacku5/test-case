<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SystemTestController;

Route::post('/database/reset', [\App\Http\Controllers\Api\DatabaseController::class, 'reset']);

Route::apiResource('customers', CustomerController::class);

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::post('/products', 'store');
    Route::patch('/products/{id}', 'update');
});

Route::controller(OrderController::class)->group(function () {
    Route::get('/orders', 'index');
    Route::post('/orders', 'store');
    Route::patch('/orders/{id}/status', 'updateStatus');
});

Route::controller(SystemTestController::class)->group(function () {
    Route::get('/system-test/run', 'run');
});