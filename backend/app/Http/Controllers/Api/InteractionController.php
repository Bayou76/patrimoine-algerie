<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;

/**
 * Contrôleur InteractionController — bascule les états favori/visité d'un site.
 *
 * Un seul endpoint : POST /api/sites/{site}/interactions/{type}
 * où type = "favorite" ou "visited". Un appel = un toggle (on/off).
 *
 * Requiert l'authentification (voir routes/api.php, dans le groupe auth:sanctum).
 */
class InteractionController extends Controller
{
    public function toggle(Request $request, Site $site, string $type)
    {
        // Sécurité : on n'accepte que ces 2 types. Sinon 404.
        // Sans ça, un appelant pourrait injecter n'importe quel nom de colonne.
        abort_unless(in_array($type, ['favorite', 'visited'], true), 404);

        // On construit dynamiquement le nom de colonne : is_favorite ou is_visited.
        $column = "is_{$type}";
        $userId = $request->user()->id;

        // firstOrNew : cherche une ligne existante pour ce (user, site), sinon
        // en crée une (non sauvée). Comme ça un utilisateur peut avoir 1 seule
        // ligne d'interaction par site avec les 2 booléens dedans.
        $interaction = $site->userInteractions()->firstOrNew(['user_id' => $userId]);
        $interaction->{$column} = ! $interaction->{$column}; // toggle
        $interaction->save();

        // On renvoie les 2 booléens pour que le frontend rafraîchisse l'UI
        // (état des 2 boutons « favoris » et « visité »).
        return [
            'is_favorite' => (bool) $interaction->is_favorite,
            'is_visited' => (bool) $interaction->is_visited,
        ];
    }
}
