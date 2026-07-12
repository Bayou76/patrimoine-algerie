<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;

/**
 * Contrôleur MeController — expose GET /api/me, la page « Mon compte ».
 *
 * Renvoie en un seul appel tout ce que la page a besoin :
 *   - user   : le profil
 *   - favorites, visited : listes de sites
 *   - reviews : avis publiés par l'utilisateur
 *   - badges : les 9 badges d'exploration avec leur progression
 */
class MeController extends Controller
{
    public function show(Request $request)
    {
        $userId = $request->user()->id;

        // On récupère toutes les interactions en une seule requête,
        // puis on sépare favoris / visités côté PHP.
        $interactions = \App\Models\SiteUserInteraction::where('user_id', $userId)->get();
        $favoriteIds = $interactions->where('is_favorite', true)->pluck('site_id');
        $visitedIds = $interactions->where('is_visited', true)->pluck('site_id');

        $favorites = Site::whereIn('id', $favoriteIds)->with('translations')->get();
        $visited = Site::whereIn('id', $visitedIds)->with('translations')->get();

        // latest() = orderBy('created_at', 'desc'). Avis les plus récents en premier.
        $reviews = $request->user()
            ->reviews()
            ->with('site.translations')
            ->latest()
            ->get();

        return [
            'user' => $request->user(),
            'favorites' => $favorites->map(fn ($site) => $this->formatSite($site)),
            'visited' => $visited->map(fn ($site) => $this->formatSite($site)),
            'reviews' => $reviews->map(fn ($review) => [
                'id' => $review->id,
                'site' => $this->formatSite($review->site),
                'rating' => $review->rating,
                'comment' => $review->comment,
                'is_verified' => $review->is_verified,
                'created_at' => $review->created_at,
            ]),
            'badges' => $this->computeBadges($visited, $reviews->count()),
        ];
    }

    /**
     * Calcule les 9 badges + leur progression.
     * Chaque badge a un seuil (threshold) et un compteur (progress).
     * unlocked = true si progress >= threshold.
     */
    private function computeBadges($visited, int $reviewCount): array
    {
        $visitedCount = $visited->count();
        $categories = $visited->pluck('category')->unique()->count();
        $wilayas = $visited->pluck('wilaya')->unique()->count();
        // groupBy + count = combien de sites visités par catégorie.
        $categoryCounts = $visited->groupBy('category')->map->count();

        // Définition déclarative des badges. Facile à modifier :
        // ajouter un badge = ajouter une entrée dans ce tableau.
        $definitions = [
            ['key' => 'first_step',   'icon' => '👣',  'threshold' => 1,  'progress' => $visitedCount],
            ['key' => 'explorer',     'icon' => '🧭',  'threshold' => 5,  'progress' => $visitedCount],
            ['key' => 'adventurer',   'icon' => '🎒',  'threshold' => 10, 'progress' => $visitedCount],
            ['key' => 'globetrotter', 'icon' => '🌍',  'threshold' => 20, 'progress' => $visitedCount],
            ['key' => 'nomad',        'icon' => '🗺️',  'threshold' => 5,  'progress' => $wilayas],
            ['key' => 'polyglot',     'icon' => '🎨',  'threshold' => 4,  'progress' => $categories],
            ['key' => 'critic',       'icon' => '✍️',  'threshold' => 5,  'progress' => $reviewCount],
            ['key' => 'roman_lover',  'icon' => '🏛️',  'threshold' => 3,  'progress' => $categoryCounts->get('romain', 0)],
            ['key' => 'nature_lover', 'icon' => '🏞️',  'threshold' => 3,  'progress' => $categoryCounts->get('naturel', 0)],
        ];

        // On ajoute juste le champ « unlocked » à chaque badge.
        return array_map(function ($b) {
            return [
                'key' => $b['key'],
                'icon' => $b['icon'],
                'threshold' => $b['threshold'],
                'progress' => $b['progress'],
                'unlocked' => $b['progress'] >= $b['threshold'],
            ];
        }, $definitions);
    }

    /** Formatage compact d'un site pour les listes (favoris, visités). */
    private function formatSite(Site $site): array
    {
        // On force la langue fr pour cette API car MePage récupère les noms
        // par le nom principal — le multilingue se fait par langue de l'UI.
        $translation = $site->translation('fr');

        return [
            'id' => $site->id,
            'slug' => $site->slug,
            'name' => $translation?->name ?? $site->slug,
            'category' => $site->category,
            'wilaya' => $site->wilaya,
            'image_path' => $site->image_path,
        ];
    }
}
