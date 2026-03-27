<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 🔥 Servir imágenes de coupons con CORS habilitado
Route::get('/image/coupon/{filename}', function ($filename) {

    $path = public_path("uploads/coupons/" . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    // 🔥 Devuelve la imagen con headers CORS
    return response()->file($path, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept'
    ]);
});
