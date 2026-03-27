<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\LogoController;
use App\Http\Controllers\DashboardController;

// ============================
// CRUD PRINCIPALES
// ============================
Route::apiResource('coupons', CouponController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('categories', CategoryController::class);

// ============================
// BANNERS
// ============================
Route::get('/banners/activos', [BannerController::class, 'activeBanners']);
Route::apiResource('banners', BannerController::class);

// ============================
// LOGOS
// ============================
Route::get('/logos/activos', [LogoController::class, 'activeLogos']);
Route::apiResource('logos', LogoController::class);

// ============================
// AUTH (PÚBLICO)
// ============================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/contacto', [ContactController::class, 'enviar']);

// ============================
// ESTADÍSTICAS — PÚBLICAS
// ============================
// (Los clientes pueden ver cuántas descargas tiene cada cupón)
Route::get('/coupon-stats', [CouponController::class, 'stats']);

// ============================
// RUTAS QUE REQUIEREN LOGIN
// ============================
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Registrar descarga de cupones
    Route::post('/coupon/{coupon}/download', [CouponController::class, 'registerDownload']);

    // Dashboard admin resumen
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
});
