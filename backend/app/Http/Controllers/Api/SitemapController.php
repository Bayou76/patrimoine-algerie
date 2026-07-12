<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Itinerary;
use App\Models\Site;
use Illuminate\Http\Response;

/**
 * Contrôleur SitemapController — génère dynamiquement /sitemap.xml.
 *
 * Le sitemap est lu par Google, Bing, DuckDuckGo… pour indexer toutes les
 * pages du site. Sans lui, les moteurs mettent des semaines/mois à trouver
 * chaque page. Avec, tout est indexé en quelques jours.
 *
 * On inclut :
 *   - Pages statiques (accueil, chronologie, itinéraires, infos pratiques)
 *   - Toutes les fiches sites (générées depuis la BDD)
 *   - Tous les itinéraires
 *   - Les 3 langues via hreflang (xhtml:link rel="alternate")
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        // FRONTEND_URL doit pointer vers l'URL publique du frontend en prod.
        // En local on utilise localhost:5174.
        $baseUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5174')), '/');
        $today = now()->toDateString();

        // Pages statiques : priorité fixe, changefreq indicative.
        $urls = [
            ['loc' => $baseUrl.'/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl.'/chronologie', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl.'/itineraires', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl.'/infos-pratiques', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        // Une URL par site avec lastmod = updated_at pour que Google sache
        // ce qui a changé récemment (indexation plus fraîche).
        foreach (Site::query()->select('slug', 'updated_at')->get() as $site) {
            $urls[] = [
                'loc' => $baseUrl.'/sites/'.$site->slug,
                'lastmod' => optional($site->updated_at)->toDateString() ?? $today,
                'priority' => '0.9',
                'changefreq' => 'monthly',
            ];
        }

        foreach (Itinerary::query()->select('slug', 'updated_at')->get() as $itinerary) {
            $urls[] = [
                'loc' => $baseUrl.'/itineraires/'.$itinerary->slug,
                'lastmod' => optional($itinerary->updated_at)->toDateString() ?? $today,
                'priority' => '0.8',
                'changefreq' => 'monthly',
            ];
        }

        // Construction manuelle du XML (pas besoin d'une lib pour ça).
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '
            .'xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($urls as $entry) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($entry['loc']).'</loc>'."\n";
            if (isset($entry['lastmod'])) {
                $xml .= '    <lastmod>'.$entry['lastmod'].'</lastmod>'."\n";
            }
            $xml .= '    <changefreq>'.$entry['changefreq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$entry['priority'].'</priority>'."\n";
            // Balises hreflang : indiquent à Google qu'une même page existe
            // en plusieurs langues. Le moteur sert la bonne selon la géo.
            foreach (['fr', 'ar', 'en'] as $lang) {
                $altUrl = $entry['loc'].(str_contains($entry['loc'], '?') ? '&' : '?').'lang='.$lang;
                $xml .= '    <xhtml:link rel="alternate" hreflang="'.$lang.'" href="'.htmlspecialchars($altUrl).'"/>'."\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
