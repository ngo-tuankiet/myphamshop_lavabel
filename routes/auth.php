<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Models\PendingRegistration;
use App\Models\User;


Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');


Route::get('/verify-email/{token}', function ($token) {
    $pending = PendingRegistration::where('token', $token)->first();

    if (!$pending) {
        return redirect('/register')->with(' Link xác minh không hợp lệ hoặc đã hết hạn.');
    }

    User::create([
        'username' => $pending->username,
        'fullname' => $pending->fullname,
        'email' => $pending->email,
        'password' => $pending->password,
        'email_verified_at' => now(),
        
    ]);

    $pending->delete();

    return redirect('/login')->with('Xác minh thành công Bạn có thể đăng nhập ngay.');
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
