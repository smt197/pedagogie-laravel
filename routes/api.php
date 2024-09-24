<?php

use App\Http\Controllers\ApprenantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReferentielController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('v1/env', function () {
    return env('AUTH_DRIVER');
});


Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/token/refresh', [AuthController::class, 'refreshToken']);
});

Route::middleware(['auth:api', 'check.auth'])->prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('/users', UserController::class)->only(['index', 'store', 'show']);
    Route::patch('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('/v1/users/export', [UserController::class, 'exportExcel']);
    
    Route::post('/referentiels', [ReferentielController::class, 'store']);
    Route::get('/referentiel', [ReferentielController::class, 'index']);
    Route::patch('/referentiels/{id}', [ReferentielController::class, 'update']);
    Route::get('/referentiels/{id}', [ReferentielController::class,'show']);
    Route::delete('/referentiels/{id}', [ReferentielController::class,'destroy']);
    Route::get('/archive/referentiels', [ReferentielController::class, 'archived']);

    Route::post('/promotions', [PromotionController::class, 'createPromotion']);
    Route::patch('/promotions/{id}', [PromotionController::class, 'updatePromotion'])->middleware('check_promotion_state:id');
    Route::delete('/promotions/{id}', [PromotionController::class, 'deletePromotion']);
    Route::get('/promotions/deleted', [PromotionController::class, 'getDeletedPromotions']);
    Route::get('/promotions', [PromotionController::class, 'getAllPromotions']);
  
    Route::patch('promotions/{id}/referentiels', [PromotionController::class, 'updatePromotionReferentiels']);
    Route::patch('promotions/{id}/etat', [PromotionController::class, 'updatePromotionEtat'])->middleware('check_promotion_state:id');
    Route::get('promotions/encours', [PromotionController::class, 'getActivePromotion']);
    Route::get('promotions/{id}/referentiels', [PromotionController::class, 'getPromotionReferentiels']);
    Route::patch('promotions/{id}/cloturer', [PromotionController::class, 'closePromotion'])->middleware('check_promotion_state:id');

    Route::post('apprenants', [ApprenantController::class, 'store']);
    Route::get('apprenants', [ApprenantController::class, 'getAllApprenants']);


















    // Ajoutez votre route protégée ici
    Route::get('/route-protegee', function () {
        return 'Bienvenue sur la route protégée !';
    });
});














