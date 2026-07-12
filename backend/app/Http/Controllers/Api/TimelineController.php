<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteTimelineEvent;
use Illuminate\Http\Request;

/**
 * Contrôleur TimelineController — expose la chronologie globale de tous les sites.
 *
 * GET /api/timeline?lang=fr → renvoie 158 événements triés par année,
 * de -20 000 000 (formation du Hoggar) à 2020 (record du minaret d'Alger).
 *
 * Affichés sur /chronologie côté frontend, groupés par grande époque.
 */
class TimelineController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->query('lang', 'fr');
        $fallback = 'fr';

        // On charge les événements dans la langue demandée + fr (secours),
        // avec les sites associés et leurs traductions pour afficher le nom.
        $events = SiteTimelineEvent::query()
            ->whereIn('language_code', [$lang, $fallback])
            ->with(['site' => fn ($q) => $q->with('translations')])
            ->orderBy('year')
            ->get();

        // Dédoublonnage par (site_id, year, title) :
        // si on a le même événement en fr ET en ar, on garde la version
        // dans la langue demandée. Cette astuce évite les doublons quand
        // la langue demandée n'a pas de traduction et qu'on retombe sur fr.
        $grouped = [];
        foreach ($events as $event) {
            $key = $event->site_id.'-'.$event->year.'-'.$event->title;
            if (! isset($grouped[$key]) || $event->language_code === $lang) {
                $grouped[$key] = $event;
            }
        }

        // Tri final par année croissante + formatage JSON.
        return collect(array_values($grouped))
            ->sortBy('year')
            ->values()
            ->map(function (SiteTimelineEvent $event) use ($lang) {
                $site = $event->site;
                $siteTranslation = $site->translation($lang);

                return [
                    'year' => $event->year,
                    'period_label' => $event->period_label,
                    'title' => $event->title,
                    'description' => $event->description,
                    'site' => [
                        'id' => $site->id,
                        'slug' => $site->slug,
                        'name' => $siteTranslation?->name ?? $site->slug,
                        'category' => $site->category,
                        'wilaya' => $site->wilaya,
                        'image_path' => $site->image_path,
                    ],
                ];
            });
    }
}
