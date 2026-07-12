<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Itinerary;
use Illuminate\Http\Request;

/**
 * Trait SyncsItinerary — logique partagée entre la création admin
 * (AdminItineraryController) et la soumission publique par un utilisateur
 * connecté (ItineraryController::store).
 *
 * Évite de dupliquer la validation et la synchronisation des traductions
 * et des sites entre les deux points d'entrée.
 */
trait SyncsItinerary
{
    private const LANGUAGES = ['fr', 'ar', 'en'];
    private const PRIMARY_LANGUAGE = 'fr';
    private const THEMES = ['romain', 'sud', 'villes', 'spirituel', 'naturel'];
    private const DIFFICULTIES = ['facile', 'moyen', 'soutenu'];

    /** Règles de validation communes. */
    private function validateItineraryPayload(Request $request, ?int $ignoreId = null): array
    {
        $slugRule = ['required', 'string', 'max:255'];
        $slugRule[] = $ignoreId
            ? "unique:itineraries,slug,{$ignoreId}"
            : 'unique:itineraries,slug';

        return $request->validate([
            'slug' => $slugRule,
            'duration' => ['required', 'string', 'max:100'],
            'difficulty' => ['nullable', 'string', 'in:' . implode(',', self::DIFFICULTIES)],
            'theme' => ['required', 'string', 'in:' . implode(',', self::THEMES)],
            'cover_image' => ['nullable', 'string'],
            'translations' => ['required', 'array'],
            'translations.' . self::PRIMARY_LANGUAGE . '.title' => ['required', 'string', 'max:255'],
            'translations.' . self::PRIMARY_LANGUAGE . '.summary' => ['required', 'string', 'max:500'],
            'translations.' . self::PRIMARY_LANGUAGE . '.description' => ['nullable', 'string'],
            'translations.ar.title' => ['nullable', 'string', 'max:255'],
            'translations.ar.summary' => ['nullable', 'string', 'max:500'],
            'translations.ar.description' => ['nullable', 'string'],
            'translations.en.title' => ['nullable', 'string', 'max:255'],
            'translations.en.summary' => ['nullable', 'string', 'max:500'],
            'translations.en.description' => ['nullable', 'string'],
            'sites' => ['required', 'array', 'min:2'],
            'sites.*.site_id' => ['required', 'integer', 'exists:sites,id'],
            'sites.*.day_label' => ['nullable', 'string', 'max:100'],
            'sites.*.note' => ['nullable', 'string', 'max:500'],
        ]);
    }

    /** Champs qui vont directement dans la table itineraries. */
    private function itineraryAttributes(array $data): array
    {
        return collect($data)
            ->only(['slug', 'duration', 'difficulty', 'theme', 'cover_image'])
            ->toArray();
    }

    /** Sync des traductions, même logique que pour les sites (delete si vide, upsert sinon). */
    private function syncItineraryTranslations(Itinerary $itinerary, array $translations): void
    {
        foreach (self::LANGUAGES as $lang) {
            $payload = $translations[$lang] ?? null;
            if (! $payload || empty($payload['title'])) {
                $itinerary->translations()->where('language_code', $lang)->delete();
                continue;
            }
            $itinerary->translations()->updateOrCreate(
                ['language_code' => $lang],
                [
                    'title' => $payload['title'],
                    'summary' => $payload['summary'] ?? '',
                    'description' => $payload['description'] ?? null,
                ]
            );
        }
    }

    /** Sync des sites de l'itinéraire : delete-all puis recrée dans le bon ordre. */
    private function syncItinerarySites(Itinerary $itinerary, array $sites): void
    {
        $itinerary->sites()->detach();
        foreach ($sites as $index => $site) {
            $itinerary->sites()->attach($site['site_id'], [
                'position' => $index + 1,
                'day_label' => $site['day_label'] ?? null,
                'note' => $site['note'] ?? null,
            ]);
        }
    }
}
