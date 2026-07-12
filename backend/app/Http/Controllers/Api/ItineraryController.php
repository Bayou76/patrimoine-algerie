<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Itinerary;
use Illuminate\Http\Request;

/**
 * Contrôleur ItineraryController — endpoints publics des itinéraires thématiques.
 *
 * GET /api/itineraries         → liste avec version allégée
 * GET /api/itineraries/{slug}  → détail complet avec sites ordonnés
 */
class ItineraryController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->query('lang', 'fr');

        // Eager load des traductions + sites.translations pour éviter le N+1
        // (sinon Laravel referait une requête par itinéraire pour ses sites).
        return Itinerary::with(['translations', 'sites.translations'])
            ->get()
            ->map(fn (Itinerary $itinerary) => $this->formatList($itinerary, $lang));
    }

    public function show(string $slug, Request $request)
    {
        $lang = $request->query('lang', 'fr');

        $itinerary = Itinerary::with(['translations', 'sites.translations'])
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->formatDetail($itinerary, $lang);
    }

    /**
     * Choisit la meilleure traduction disponible avec fallback :
     * langue demandée → français → n'importe laquelle.
     * Évite un null qui casserait l'affichage.
     */
    private function pickTranslation($translations, string $lang)
    {
        return $translations->firstWhere('language_code', $lang)
            ?? $translations->firstWhere('language_code', 'fr')
            ?? $translations->first();
    }

    /** Format compact pour la page liste (grille de cartes). */
    private function formatList(Itinerary $itinerary, string $lang): array
    {
        $t = $this->pickTranslation($itinerary->translations, $lang);
        // Si aucune cover_image explicite, on utilise l'image du 1er site.
        // Ça garantit qu'une carte n'est jamais vide.
        $cover = $itinerary->cover_image ?: optional($itinerary->sites->first())->image_path;

        return [
            'slug' => $itinerary->slug,
            'duration' => $itinerary->duration,
            'difficulty' => $itinerary->difficulty,
            'theme' => $itinerary->theme,
            'cover_image' => $cover,
            'title' => $t?->title,
            'summary' => $t?->summary,
            'sites_count' => $itinerary->sites->count(),
            'wilayas' => $itinerary->sites->pluck('wilaya')->unique()->values(),
        ];
    }

    /** Format complet pour la page détail avec sites ordonnés et métadonnées. */
    private function formatDetail(Itinerary $itinerary, string $lang): array
    {
        $t = $this->pickTranslation($itinerary->translations, $lang);

        // Pour chaque site, on récupère les infos de la table pivot
        // (position, day_label, note) via $site->pivot.
        $sites = $itinerary->sites->map(function ($site) use ($lang) {
            $siteTr = $this->pickTranslation($site->translations, $lang);

            return [
                'id' => $site->id,
                'slug' => $site->slug,
                'name' => $siteTr?->name ?? $site->slug,
                'category' => $site->category,
                'wilaya' => $site->wilaya,
                'image_path' => $site->image_path,
                'latitude' => $site->latitude,
                'longitude' => $site->longitude,
                'position' => $site->pivot->position,
                'day_label' => $site->pivot->day_label,
                'note' => $site->pivot->note,
            ];
        });

        $cover = $itinerary->cover_image ?: optional($itinerary->sites->first())->image_path;

        return [
            'slug' => $itinerary->slug,
            'duration' => $itinerary->duration,
            'difficulty' => $itinerary->difficulty,
            'theme' => $itinerary->theme,
            'cover_image' => $cover,
            'title' => $t?->title,
            'summary' => $t?->summary,
            'description' => $t?->description,
            'sites' => $sites,
        ];
    }
}
