<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FirebaseTestController;
use App\Http\Controllers\UserController;

Route::get('v1/env', function () {
    return env('AUTH_DRIVER');
});

// Route::post('v1/users', [UserController::class, 'store']);


Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/token/refresh', [AuthController::class, 'refreshToken']);
});

Route::middleware(['auth:api', 'check.auth'])->prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::apiResource('/users', UserController::class)->only(['index', 'store', 'show']);
    Route::patch('/users/{id}', [UserController::class, 'update'])->name('users.update');



    
  



    // Ajoutez votre route protégée ici
    Route::get('/route-protegee', function () {
        return 'Bienvenue sur la route protégée !';
    });
});














