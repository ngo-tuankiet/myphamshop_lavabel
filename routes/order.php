<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


// Order API
Route::post('/checkout', [OrderController::class, 'placeOrder']);
Route::get('/orders/list/{userId}', [OrderController::class, 'listOrders']);
Route::get('/orders/detail/{id}', [OrderController::class, 'orderDetail']);
Route::put('/orders/cancel/{id}', [OrderController::class, 'cancelOrder']);
