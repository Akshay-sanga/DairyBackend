<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Admin API Routes
Route::post('/create-admin', [AdminController::class, 'create']);
Route::post('/login-admin', [AdminController::class, 'login']);