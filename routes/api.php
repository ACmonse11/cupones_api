<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;

// 🔹 CRUDs públicos (solo para pruebas)
Route::apiResource('coupons', CouponController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('categories', CategoryController::class);

// 🔹 Registro y login públicos
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🔒 Rutas protegidas (solo logout por ahora)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
