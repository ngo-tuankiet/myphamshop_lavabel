<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [OrderController::class, 'placeOrder'])->name('checkout.place');
Route::get('/orders/success', [OrderController::class, 'success']);
Route::get('/order-detail/{id}', [OrderController::class, 'orderDetail']);
