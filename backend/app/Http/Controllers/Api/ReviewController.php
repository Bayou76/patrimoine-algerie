<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Site;
use Illuminate\Http\Request;

/**
 * Contrôleur ReviewController — création et suppression d'avis.
 *
 * POST   /api/sites/{site}/reviews    → tout utilisateur connecté
 * DELETE /api/reviews/{review}        → admin uniquement
 */
class ReviewController extends Controller
{
    public function store(Request $request, Site $site)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ]);

        $userId = $request->user()->id;

        // Astuce importante : on tag l'avis comme « vérifié » si l'utilisateur
        // avait marqué le site comme visité AVANT d'écrire l'avis. Ça crédibilise
        // les avis affichés (badge « Visite vérifiée »).
        $hasVisited = $site->userInteractions()
            ->where('user_id', $userId)
            ->where('is_visited', true)
            ->exists();

        // ...$validated fusionne les champs validés dans le tableau.
        $review = $site->reviews()->create([
            ...$validated,
            'user_id' => $userId,
            'is_verified' => $hasVisited,
        ]);

        // load('user') charge la relation user pour que le frontend puisse
        // afficher le nom de l'auteur immédiatement sans nouvelle requête.
        return response()->json($review->load('user'), 201);
    }

    public function destroy(Request $request, Review $review)
    {
        // Autorisation manuelle : seuls les admins peuvent supprimer un avis.
        abort_unless($request->user()->is_admin, 403);

        $review->delete();

        return response()->noContent(); // 204 : succès sans body
    }
}
