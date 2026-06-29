<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataPlpsController;

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\AdminManagementController;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Auth routes
Route::get('/care', [AdminLoginController::class, 'showLoginForm'])->name('login');
Route::post('/care', [AdminLoginController::class, 'login']);
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

// captcha step superadmin (cm bisa diakses pas sesi pending)
Route::get('/care/captcha', [AdminLoginController::class, 'showCaptchaForm'])->name('login.captcha');
Route::post('/care/captcha', [AdminLoginController::class, 'verifyCaptcha'])->name('login.captcha.verify');

// Refresh captcha image ajax
Route::get('/refresh-captcha', [AdminLoginController::class, 'refreshCaptcha']);

// Superadmin routes
Route::middleware(['auth:admin', 'super_admin'])->group(function () {
    Route::resource('admins', AdminManagementController::class)->except(['show']);
});

// Guest/Admin Dashboard routes
Route::get('/dashboard', [DataPlpsController::class, 'index']);
Route::get('/api/filter-options', [DataPlpsController::class, 'getFilterOptions']);
Route::get('/api/table-data', [DataPlpsController::class, 'tableData']);
Route::get('/api/export-excel', [DataPlpsController::class, 'exportExcel']);
Route::get('/api/export-pdf', [DataPlpsController::class, 'exportPdf']);
Route::put('/api/data-plps/{id}', [DataPlpsController::class, 'updateRow'])->middleware('auth:admin');
Route::post('/api/data-plps/bulk-delete', [DataPlpsController::class, 'bulkDelete'])->middleware('auth:admin');

// Data Input routes
Route::get('/input-data', [DataPlpsController::class, 'inputData'])->middleware('auth:admin');
Route::post('/input-data/validate', [DataPlpsController::class, 'validateImport'])->middleware('auth:admin')->name('input.validate');
Route::post('/input-data/upload', [DataPlpsController::class, 'uploadTempFile'])->middleware('auth:admin')->name('input.upload');
Route::post('/input-data/process-chunk', [DataPlpsController::class, 'processChunk'])->middleware('auth:admin')->name('input.process-chunk');
Route::get('/input-data/confirm', [DataPlpsController::class, 'showConfirmImport'])->middleware('auth:admin')->name('input.confirm.show');
Route::post('/input-data/confirm', [DataPlpsController::class, 'confirmImport'])->middleware('auth:admin')->name('input.confirm');
Route::get('/input-data/template', [DataPlpsController::class, 'downloadTemplate'])->middleware('auth:admin')->name('input.template');