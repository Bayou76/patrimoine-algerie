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
        env('APP_ENV') !== 'production' ? 'http://localhost:5174' : null,
    ]),

    // Autorise aussi tous les déploiements de preview Vercel (*.vercel.app)
    'allowed_origins_patterns' => [
        '#^https://.*\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
