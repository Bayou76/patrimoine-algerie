<?php

/**
 * Configuration CORS — autorise le frontend (Vercel) à appeler cette API.
 *
 * L'app utilise des tokens Bearer (Sanctum), pas des cookies de session,
 * donc supports_credentials reste à false — pas besoin de cookies cross-site.
 *
 * allowed_origins est piloté par la variable d'env FRONTEND_URL pour ne pas
 * avoir à modifier le code à chaque changement de domaine Vercel.
 */

return [
    'paths' => ['api/*', 'sitemap.xml', 'robots.txt'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter([
        env('FRONTEND_URL'),
    ]),

    // Autorise tous les déploiements de preview Vercel (*.vercel.app), et en
    // local n'importe quel port localhost (pratique quand le port 5174 est
    // déjà pris par un autre process : le serveur de test peut en choisir un autre).
    'allowed_origins_patterns' => array_filter([
        '#^https://.*\.vercel\.app$#',
        env('APP_ENV') !== 'production' ? '#^http://localhost:\d+$#' : null,
    ]),

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
