<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\MasterController;
use App\Http\Middleware\ApiToken;
use App\Http\Middleware\WebAuth;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

// ===================== WEB ADMIN & PEMILIK =====================
Route::get('/', fn () => view('app'));

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);   // dipakai juga oleh aplikasi gudang
Route::get('/api/me', [AuthController::class, 'me']);

Route::middleware(WebAuth::class . ':admin,pemilik')->group(function () {
    Route::get('/api/data', [DataController::class, 'index']);
});

Route::middleware(WebAuth::class . ':admin')->group(function () {
    Route::post('/api/produk', [MasterController::class, 'saveProduk']);
    Route::delete('/api/produk/{id}', [MasterController::class, 'deleteProduk']);
    Route::post('/api/master/{type}', [MasterController::class, 'store']);
    Route::delete('/api/master/{type}/{id}', [MasterController::class, 'destroy']);
    Route::post('/api/akun', [MasterController::class, 'storeAkun']);
    Route::delete('/api/akun/{role}/{id}', [MasterController::class, 'destroyAkun']);
});

// ===================== APLIKASI STAFF GUDANG =====================
Route::get('/gudang', fn () => view('gudang'));
Route::post('/gudang/login', [GudangController::class, 'login']);
Route::get('/gudang/api/me', [GudangController::class, 'me']);

Route::middleware(WebAuth::class . ':gudang')->prefix('gudang/api')->group(function () {
    Route::get('/data', [GudangController::class, 'data']);
    Route::get('/cari', [GudangController::class, 'cari']);
    Route::post('/inbound', [GudangController::class, 'inbound']);
    Route::post('/outbound', [GudangController::class, 'outbound']);
    Route::post('/rusak', [GudangController::class, 'rusak']);
    Route::post('/opname', [GudangController::class, 'opname']);
});

// ===================== API APLIKASI FLUTTER (token, tanpa cookie/CSRF) =====================
Route::prefix('mobile')
    ->withoutMiddleware([ValidateCsrfToken::class, VerifyCsrfToken::class])
    ->group(function () {
        Route::post('/login', [MobileController::class, 'login']);

        Route::middleware(ApiToken::class)->group(function () {
            Route::get('/me', [MobileController::class, 'me']);
            Route::post('/logout', [MobileController::class, 'logout']);
            Route::get('/data', [MobileController::class, 'data']);
            Route::get('/cari', [MobileController::class, 'cari']);
            Route::post('/inbound', [MobileController::class, 'inbound']);
            Route::post('/outbound', [MobileController::class, 'outbound']);
            Route::post('/rusak', [MobileController::class, 'rusak']);
            Route::post('/opname', [MobileController::class, 'opname']);
            Route::post('/adjustment', [MobileController::class, 'adjustment']);
        });
    });
