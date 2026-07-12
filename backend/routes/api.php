<?php

/**
 * Fichier de routes API — définit toutes les URLs préfixées par /api.
 *
 * Structure :
 *   - Routes publiques (pas besoin d'être connecté)
 *   - Routes protégées par auth:sanctum (token requis)
 *   - Routes admin (dans le groupe protégé + vérif is_admin dans le contrôleur)
 *
 * Route::get(path, [ClasseContrôleur, 'méthode']) associe une URL à une méthode.
 */

use App\Http\Controllers\Api\AdminItineraryController;
use App\Http\Controllers\Api\AdminSiteController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InteractionController;
use App\Http\Controllers\Api\ItineraryController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\TimelineController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Routes publiques ---
Route::get('/sites', [SiteController::class, 'index']);
Route::get('/sites/{slug}', [SiteController::class, 'show']);
Route::get('/timeline', [TimelineController::class, 'index']);
Route::get('/itineraries', [ItineraryController::class, 'index']);
Route::get('/itineraries/{slug}', [ItineraryController::class, 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Routes protégées : nécessitent un token Sanctum valide ---
// Le middleware auth:sanctum vérifie le header Authorization avant d'exécuter
// la méthode. Si pas de token → 401.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/sites/{site}/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    Route::post('/sites/{site}/interactions/{type}', [InteractionController::class, 'toggle']);

    // Proposition d'itinéraire par un utilisateur inscrit (pas besoin d'être admin).
    Route::post('/itineraries', [ItineraryController::class, 'store']);

    Route::get('/me', [MeController::class, 'show']);
    // Route utilitaire pour récupérer l'utilisateur connecté (utile côté React).
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Sous-groupe admin : les URLs commencent par /api/admin.
    // La vérification is_admin se fait dans les contrôleurs (403 sinon).
    Route::prefix('admin')->group(function () {
        Route::get('/stats', [AdminSiteController::class, 'stats']);
        Route::get('/sites/{site}', [AdminSiteController::class, 'edit']);
        Route::post('/sites', [AdminSiteController::class, 'store']);
        Route::put('/sites/{site}', [AdminSiteController::class, 'update']);
        Route::delete('/sites/{site}', [AdminSiteController::class, 'destroy']);

        Route::get('/itineraries', [AdminItineraryController::class, 'index']);
        Route::get('/itineraries/{itinerary}', [AdminItineraryController::class, 'edit']);
        Route::post('/itineraries', [AdminItineraryController::class, 'store']);
        Route::put('/itineraries/{itinerary}', [AdminItineraryController::class, 'update']);
        Route::delete('/itineraries/{itinerary}', [AdminItineraryController::class, 'destroy']);
    });
});
