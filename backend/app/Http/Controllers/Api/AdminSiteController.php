<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur AdminSiteController — endpoints de l'espace administration.
 *
 * Tous les endpoints sont protégés par authorizeAdmin() qui renvoie 403
 * si l'utilisateur n'a pas is_admin = true.
 *
 * Chaque site est composé de 3 choses côté admin :
 *   - Les champs de base (slug, coords, catégorie…) → table sites
 *   - Les traductions dans 3 langues → table site_translations
 *   - La galerie d'images → table site_images
 *   - Les événements de la chronologie → table site_timeline_events
 *
 * Les méthodes syncXxx() gèrent le principe « remplacer entièrement »
 * (delete puis recreate) plutôt que patcher élément par élément.
 * Simple, sûr, et acceptable pour un CMS de petite taille.
 */
class AdminSiteController extends Controller
{
    private const LANGUAGES = ['fr', 'ar', 'en'];
    private const PRIMARY_LANGUAGE = 'fr';
    private const SECONDARY_LANGUAGES = ['ar', 'en'];

    /** POST /api/admin/sites — création d'un site. */
    public function store(Request $request)
    {
        $this->authorizeAdmin($request);
        $data = $this->validatePayload($request);

        // DB::transaction : toutes les insertions dans un même « lot ».
        // Si l'une échoue, TOUT est annulé (rollback) : pas de site orphelin
        // sans traductions ni images.
        return DB::transaction(function () use ($data) {
            $site = Site::create($this->siteAttributes($data));
            $this->syncTranslations($site, $data['translations']);
            $this->syncImages($site, $data['images'] ?? []);
            $this->syncTimeline($site, $data['timeline'] ?? []);

            return response()->json(['slug' => $site->slug], 201);
        });
    }

    /** PUT /api/admin/sites/{site} — mise à jour. */
    public function update(Request $request, Site $site)
    {
        $this->authorizeAdmin($request);
        $data = $this->validatePayload($request, $site->id);

        return DB::transaction(function () use ($site, $data) {
            $site->update($this->siteAttributes($data));
            $this->syncTranslations($site, $data['translations']);
            $this->syncImages($site, $data['images'] ?? []);
            $this->syncTimeline($site, $data['timeline'] ?? []);

            return response()->json(['slug' => $site->slug]);
        });
    }

    /** DELETE /api/admin/sites/{site} — suppression (cascade en BDD). */
    public function destroy(Request $request, Site $site)
    {
        $this->authorizeAdmin($request);
        $site->delete();

        return response()->noContent();
    }

    /**
     * GET /api/admin/stats — dashboard admin avec stats agrégées par site.
     * Une seule requête SQL grâce à withCount + withAvg (pas de N+1).
     */
    public function stats(Request $request)
    {
        $this->authorizeAdmin($request);

        $sites = Site::query()
            ->with(['translations' => fn ($q) => $q->where('language_code', 'fr')])
            ->withCount([
                'reviews',
                // « as » renomme la colonne calculée. On peut ainsi compter
                // 2 fois userInteractions avec des conditions différentes.
                'userInteractions as favorites_count' => fn ($q) => $q->where('is_favorite', true),
                'userInteractions as visited_count' => fn ($q) => $q->where('is_visited', true),
            ])
            ->withAvg('reviews', 'rating')
            ->get();

        return $sites->map(fn (Site $site) => [
            'id' => $site->id,
            'slug' => $site->slug,
            'name' => $site->translations->first()?->name ?? $site->slug,
            'category' => $site->category,
            'wilaya' => $site->wilaya,
            'image_path' => $site->image_path,
            'favorites_count' => $site->favorites_count,
            'visited_count' => $site->visited_count,
            'reviews_count' => $site->reviews_count,
            'average_rating' => $site->reviews_avg_rating ? round($site->reviews_avg_rating, 1) : null,
        ]);
    }

    /**
     * GET /api/admin/sites/{site} — récupère un site pour l'éditer.
     * Format différent de l'endpoint public : structure groupée par langue,
     * plus pratique pour le formulaire multi-onglets.
     */
    public function edit(Request $request, Site $site)
    {
        $this->authorizeAdmin($request);
        $site->load(['translations', 'images', 'timelineEvents']);

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
            'affiliate_activities' => $site->affiliate_activities ?? [],
            'affiliate_hotel_url' => $site->affiliate_hotel_url,
            // keyBy('language_code') : { fr: {...}, ar: {...}, en: {...} }
            // pour que le frontend accède directement à translations.fr.name.
            'translations' => $site->translations->keyBy('language_code')->map(fn ($t) => [
                'name' => $t->name,
                'description' => $t->description,
                'history' => $t->history,
                'visit_info' => $t->visit_info,
            ]),
            'images' => $site->images->map(fn ($img) => [
                'path' => $img->path,
                'caption' => $img->caption,
            ]),
            'timeline' => collect($this->groupTimelineByYear($site->timelineEvents))->values(),
        ];
    }

    /** Vérifie que l'utilisateur est admin, sinon 403. */
    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }

    /**
     * Grosses règles de validation partagées entre store et update.
     * On génère dynamiquement les règles par langue pour éviter la duplication.
     */
    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        // En update, l'unicité du slug doit ignorer le site courant.
        $slugRule = ['required', 'string', 'max:255'];
        $slugRule[] = $ignoreId
            ? "unique:sites,slug,{$ignoreId}"
            : 'unique:sites,slug';

        $rules = [
            'slug' => $slugRule,
            'category' => ['required', 'string', 'max:100'],
            'wilaya' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'image_path' => ['nullable', 'string'],
            'opening_hours' => ['nullable', 'string'],
            'unesco_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'entry_fee' => ['nullable', 'string'],
            'affiliate_activities' => ['nullable', 'array'],
            'affiliate_activities.*.label' => ['required', 'string', 'max:100'],
            'affiliate_activities.*.url' => ['required', 'url', 'max:255'],
            'affiliate_hotel_url' => ['nullable', 'url', 'max:255'],
            'translations' => ['required', 'array'],
            'images' => ['nullable', 'array'],
            'images.*.path' => ['required', 'string'],
            'images.*.caption' => ['nullable', 'string', 'max:255'],
            'timeline' => ['nullable', 'array'],
            'timeline.*.year' => ['required', 'integer'],

            // Le français est obligatoire (langue de référence).
            'translations.' . self::PRIMARY_LANGUAGE . '.name' => ['required', 'string', 'max:255'],
            'translations.' . self::PRIMARY_LANGUAGE . '.description' => ['nullable', 'string'],
            'translations.' . self::PRIMARY_LANGUAGE . '.history' => ['nullable', 'string'],
            'translations.' . self::PRIMARY_LANGUAGE . '.visit_info' => ['nullable', 'string'],

            'timeline.*.' . self::PRIMARY_LANGUAGE . '.period_label' => ['required', 'string', 'max:255'],
            'timeline.*.' . self::PRIMARY_LANGUAGE . '.title' => ['required', 'string', 'max:255'],
            'timeline.*.' . self::PRIMARY_LANGUAGE . '.description' => ['nullable', 'string'],
        ];

        // Les langues secondaires (ar, en) sont optionnelles.
        foreach (self::SECONDARY_LANGUAGES as $lang) {
            $rules["translations.$lang.name"] = ['nullable', 'string', 'max:255'];
            $rules["translations.$lang.description"] = ['nullable', 'string'];
            $rules["translations.$lang.history"] = ['nullable', 'string'];
            $rules["translations.$lang.visit_info"] = ['nullable', 'string'];
            $rules["timeline.*.$lang.period_label"] = ['nullable', 'string', 'max:255'];
            $rules["timeline.*.$lang.title"] = ['nullable', 'string', 'max:255'];
            $rules["timeline.*.$lang.description"] = ['nullable', 'string'];
        }

        return $request->validate($rules);
    }

    /** Sépare les champs qui vont dans la table sites (vs translations, images…). */
    private function siteAttributes(array $data): array
    {
        return collect($data)
            ->only(['slug', 'category', 'wilaya', 'latitude', 'longitude', 'image_path', 'opening_hours', 'entry_fee', 'unesco_year', 'affiliate_activities', 'affiliate_hotel_url'])
            ->toArray();
    }

    /**
     * Sync des traductions : pour chaque langue, si le nom est vide on supprime
     * (on n'affichera pas de traduction incomplète), sinon on updateOrCreate.
     */
    private function syncTranslations(Site $site, array $translations): void
    {
        foreach (self::LANGUAGES as $lang) {
            $payload = $translations[$lang] ?? null;
            if (! $payload || empty($payload['name'])) {
                $site->translations()->where('language_code', $lang)->delete();
                continue;
            }
            $site->translations()->updateOrCreate(
                ['language_code' => $lang],
                [
                    'name' => $payload['name'],
                    'description' => $payload['description'] ?? '',
                    'history' => $payload['history'] ?? null,
                    'visit_info' => $payload['visit_info'] ?? null,
                ]
            );
        }
    }

    /** Sync images : approche « delete-all-then-recreate ». */
    private function syncImages(Site $site, array $images): void
    {
        $site->images()->delete();
        foreach ($images as $index => $image) {
            $site->images()->create([
                'path' => $image['path'],
                'caption' => $image['caption'] ?? null,
                'position' => $index + 1, // 1-based, plus lisible côté humain
            ]);
        }
    }

    /**
     * Sync timeline : chaque « événement » est en fait N lignes (une par langue).
     * On efface tout puis on recrée. Coût minime (peu d'événements par site).
     */
    private function syncTimeline(Site $site, array $events): void
    {
        $site->timelineEvents()->delete();
        foreach ($events as $event) {
            foreach (self::LANGUAGES as $lang) {
                $payload = $event[$lang] ?? null;
                if (! $payload || empty($payload['title'])) {
                    continue;
                }
                $site->timelineEvents()->create([
                    'language_code' => $lang,
                    'year' => $event['year'],
                    'period_label' => $payload['period_label'],
                    'title' => $payload['title'],
                    'description' => $payload['description'] ?? '',
                ]);
            }
        }
    }

    /**
     * Regroupe les événements de timeline par année et langue pour renvoyer
     * la structure attendue par le formulaire d'édition :
     *   [{ year: 100, fr: {...}, ar: {...}, en: {...} }, ...]
     */
    private function groupTimelineByYear($events): array
    {
        $grouped = [];
        foreach ($events as $event) {
            $key = $event->year;
            if (! isset($grouped[$key])) {
                $grouped[$key] = ['year' => $event->year];
                foreach (self::LANGUAGES as $lang) {
                    $grouped[$key][$lang] = null;
                }
            }
            $grouped[$key][$event->language_code] = [
                'period_label' => $event->period_label,
                'title' => $event->title,
                'description' => $event->description,
            ];
        }
        ksort($grouped); // trie par année croissante

        return array_values($grouped);
    }
}
