<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;

/**
 * Contrôleur SiteController — expose 2 endpoints publics :
 *   GET /api/sites          → liste tous les sites (avec filtres)
 *   GET /api/sites/{slug}   → détail complet d'un site
 *
 * En Laravel, un contrôleur reçoit une requête HTTP et renvoie une réponse
 * (souvent du JSON pour une API). Chaque méthode publique = un endpoint.
 */
class SiteController extends Controller
{
    /**
     * Liste des sites, filtrable par category et wilaya.
     * URL exemple : /api/sites?lang=fr&category=romain&wilaya=Batna
     */
    public function index(Request $request)
    {
        // Langue demandée par le frontend (défaut : français).
        $lang = $request->query('lang', 'fr');

        $sites = Site::query()
            // with('translations') : eager loading, charge les traductions en 1 requête
            // au lieu de N (une par site). Évite le classique problème « N+1 queries ».
            ->with('translations')
            // withAvg calcule la note moyenne en SQL (AVG(rating)), pas en PHP.
            ->withAvg('reviews', 'rating')
            // when() applique le filtre seulement si la valeur est présente.
            // Plus élégant qu'un if/else autour du query builder.
            ->when($request->query('category'), fn ($query, $category) => $query->where('category', $category))
            ->when($request->query('wilaya'), fn ($query, $wilaya) => $query->where('wilaya', $wilaya))
            ->get();

        // map() transforme chaque site en tableau prêt pour JSON.
        return $sites->map(fn (Site $site) => $this->formatListItem($site, $lang));
    }

    /**
     * Détail d'un site avec toutes ses relations + suggestions de sites similaires.
     */
    public function show(Request $request, string $slug)
    {
        $lang = $request->query('lang', 'fr');

        // firstOrFail() : renvoie automatiquement une 404 si le slug n'existe pas.
        // On charge en une fois tout ce dont la page a besoin (translations,
        // reviews avec user, images, timeline) pour éviter les requêtes multiples.
        $site = Site::where('slug', $slug)
            ->with(['translations', 'reviews.user', 'images', 'timelineEvents'])
            ->firstOrFail();

        // Sites similaires : même catégorie OU même wilaya, ordonnés par pertinence.
        // orderByRaw avec un score binaire : (category = ?) desc met les sites
        // de la même catégorie EN PREMIER, puis même wilaya. Limite à 3.
        $similar = Site::query()
            ->with('translations')
            ->where('id', '!=', $site->id) // On exclut le site courant
            ->where(function ($q) use ($site) {
                $q->where('category', $site->category)
                  ->orWhere('wilaya', $site->wilaya);
            })
            ->orderByRaw('(category = ?) desc, (wilaya = ?) desc', [$site->category, $site->wilaya])
            ->limit(3)
            ->get();

        // Si l'utilisateur est connecté via Sanctum, on récupère son interaction
        // avec ce site (favori ? visité ?). Sinon on renvoie false partout.
        $user = auth('sanctum')->user();
        $userInteraction = null;
        if ($user) {
            $userInteraction = $site->userInteractions()
                ->where('user_id', $user->id)
                ->first();
        }

        // Réponse JSON complète : on formate manuellement plutôt que de renvoyer
        // le modèle brut, pour maîtriser exactement ce que le frontend reçoit.
        return [
            'id' => $site->id,
            'slug' => $site->slug,
            'category' => $site->category,
            'wilaya' => $site->wilaya,
            'latitude' => $site->latitude,
            'longitude' => $site->longitude,
            'image_path' => $site->image_path,
            'opening_hours' => $site->opening_hours,
            'entry_fee' => $site->entry_fee,
            'unesco_year' => $site->unesco_year,
            'translations' => $site->translations->map(fn ($translation) => [
                'language_code' => $translation->language_code,
                'name' => $translation->name,
                'description' => $translation->description,
                'history' => $translation->history,
                'visit_info' => $translation->visit_info,
            ]),
            'images' => $site->images->map(fn ($image) => [
                'id' => $image->id,
                'path' => $image->path,
                'caption' => $image->caption,
            ]),
            'timeline' => $site->timelineEvents->map(fn ($event) => [
                'language_code' => $event->language_code,
                'year' => $event->year,
                'period_label' => $event->period_label,
                'title' => $event->title,
                'description' => $event->description,
            ]),
            'average_rating' => $this->roundedOrNull($site->averageRating()),
            'user_interaction' => [
                'is_favorite' => (bool) ($userInteraction?->is_favorite),
                'is_visited' => (bool) ($userInteraction?->is_visited),
            ],
            'reviews' => $site->reviews->map(fn ($review) => [
                'id' => $review->id,
                'user_name' => $review->user->name,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'is_verified' => $review->is_verified,
                'created_at' => $review->created_at,
            ]),
            'similar' => $similar->map(fn (Site $s) => [
                'id' => $s->id,
                'slug' => $s->slug,
                'name' => $s->translation($lang)?->name ?? $s->slug,
                'category' => $s->category,
                'wilaya' => $s->wilaya,
                'image_path' => $s->image_path,
            ]),
        ];
    }

    /** Version allégée d'un site pour la liste (moins de champs, plus rapide). */
    private function formatListItem(Site $site, string $lang): array
    {
        $translation = $site->translation($lang);

        return [
            'id' => $site->id,
            'slug' => $site->slug,
            'name' => $translation?->name,
            'description' => $translation?->description,
            'category' => $site->category,
            'wilaya' => $site->wilaya,
            'latitude' => $site->latitude,
            'longitude' => $site->longitude,
            'image_path' => $site->image_path,
            // reviews_avg_rating est ajouté par withAvg() plus haut.
            'average_rating' => $this->roundedOrNull($site->reviews_avg_rating),
        ];
    }

    /** Petite aide pour arrondir à 1 décimale, sans planter si null. */
    private function roundedOrNull(?float $value): ?float
    {
        return $value !== null ? round($value, 1) : null;
    }
}
