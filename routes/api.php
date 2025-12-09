<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// IMPORT CONTROLLERS
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Các API này sẽ tự động có prefix /api/ phía trước
| Ví dụ: POST /api/register
|--------------------------------------------------------------------------
*/

// ==================== AUTH ====================
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/change-password', [AuthController::class, 'changePassword']);
// ==================== CART ====================
Route::post('/cart/add', [CartController::class, 'add']);
Route::post('/cart/update', [CartController::class, 'update']);
Route::post('/cart/remove', [CartController::class, 'remove']);
Route::post('/cart/get', [CartController::class, 'getCart']);
Route::post('/cart/clear', [CartController::class, 'clear']);

// ==================== ORDER ====================
Route::post('/orders/check',[OrderController::class, 'placeOrder']);
Route::get('/orders/list/{userId}', [OrderController::class, 'listOrders']);
Route::get('/orders/detail/{id}', [OrderController::class, 'orderDetail']);
Route::put('/orders/cancel/{id}', [OrderController::class, 'cancelOrder']);
//===================== PAYMENT ====================
Route::post('/payments/create', [PaymentController::class, 'createPayment']);
Route::post('/payments/webhook', [PaymentController::class, 'webhook']);