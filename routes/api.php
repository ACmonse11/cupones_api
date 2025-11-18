<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\LogoController;

// ============================
// CRUD PRINCIPALES
// ============================

Route::apiResource('coupons', CouponController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('categories', CategoryController::class);

// ============================
// BANNERS (FUNCIONANDO)
// ============================

Route::get('/banners/activos', [BannerController::class, 'activeBanners']);
Route::apiResource('banners', BannerController::class);

// ============================
// LOGOS (AHORA IGUAL A BANNERS)
// ============================

Route::get('/logos/activos', [LogoController::class, 'activeLogos']);
Route::apiResource('logos', LogoController::class);

// ============================
// AUTH
// ============================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/contacto', [ContactController::class, 'enviar']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
