<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataPlpsController;

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\AdminManagementController;

Route::get('/', function () {
    return redirect('/login');
});

// Auth routes
Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminLoginController::class, 'login']);
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

// captcha step superadmin (cm bisa diakses pas sesi pending)
Route::get('/login/captcha', [AdminLoginController::class, 'showCaptchaForm'])->name('login.captcha');
Route::post('/login/captcha', [AdminLoginController::class, 'verifyCaptcha'])->name('login.captcha.verify');

// Refresh captcha image ajax
Route::get('/refresh-captcha', [AdminLoginController::class, 'refreshCaptcha']);

// Superadmin routes
Route::middleware(['auth:admin', 'super_admin'])->group(function () {
    Route::resource('admins', AdminManagementController::class)->except(['show']);
});

// Admin routes
Route::get('/dashboard', [DataPlpsController::class, 'index'])->middleware('auth:admin');
Route::post('/import', [DataPlpsController::class, 'import'])->middleware('auth:admin');