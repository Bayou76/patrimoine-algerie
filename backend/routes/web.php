<?php

/**
 * Fichier de routes « web » — pour tout ce qui n'est pas /api.
 *
 * Ici on n'a besoin que du sitemap.xml et robots.txt (côté SEO), servis
 * directement par Laravel plutôt que via l'API pour respecter la convention
 * (les moteurs cherchent toujours ces fichiers à la racine du domaine).
 */

use App\Http\Controllers\Api\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// /sitemap.xml généré dynamiquement à partir de la BDD (voir SitemapController).
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

// robots.txt : le fichier statique dans public/ prend le dessus sur cette route.
// Elle sert uniquement de fallback si le fichier est supprimé.
Route::get('/robots.txt', function () {
    $baseUrl = rtrim(env('APP_URL', 'http://localhost:8001'), '/');
    $body = "User-agent: *\n"
        ."Allow: /\n"
        ."Disallow: /admin\n"
        ."Disallow: /api\n\n"
        ."Sitemap: {$baseUrl}/sitemap.xml\n";

    return response($body, 200, ['Content-Type' => 'text/plain']);
});
