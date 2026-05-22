<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// public routes
Route::prefix('auth')->group(function () {

    // register: endpoint registrasi user
    Route::post('/register', [AuthController::class, 'register']);

    // login: endpoint login user
    Route::post('/login', [AuthController::class, 'login']);
});

// protected routes
Route::middleware('auth:sanctum')->group(function () {

    // logout: endpoint logout user
    Route::post('/logout', [AuthController::class, 'logout']);

    // me: endpoint ambil data user login
    Route::get('/me', [AuthController::class, 'me']);
});