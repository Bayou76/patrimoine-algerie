/**
 * Configuration Vite — le bundler du frontend.
 * Plugins activés :
 *   - @vitejs/plugin-react : JSX + Fast Refresh
 *   - @tailwindcss/vite : Tailwind CSS v4
 *   - vite-plugin-pwa : génère automatiquement le service worker + manifest,
 *     ce qui rend Athar installable et disponible hors-ligne.
 */

import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
  plugins: [
    react(),
    tailwindcss(),
    VitePWA({
      // registerType 'autoUpdate' : le SW se met à jour tout seul en arrière-plan.
      // Les utilisateurs récupèrent la dernière version à la prochaine visite.
      registerType: 'autoUpdate',
      // Fichiers statiques ajoutés au manifest.
      includeAssets: ['favicon.svg', 'robots.txt', 'icon.svg', 'icon-maskable.svg'],
      manifest: {
        name: 'Athar — Guide du patrimoine algérien',
        short_name: 'Athar',
        description: 'Sites romains, casbahs, oasis et lieux sacrés — le guide du patrimoine algérien.',
        theme_color: '#0f2a30',       // Couleur de la barre système (teal)
        background_color: '#fbf7f0',  // Couleur de fond du splash screen (sable)
        display: 'standalone',        // Ouverture sans barre navigateur = feeling d'app native
        orientation: 'portrait-primary',
        start_url: '/',
        scope: '/',
        lang: 'fr',
        categories: ['travel', 'education', 'reference'],
        icons: [
          // SVG : une seule icône vectorielle qui scale à toutes les tailles.
          // Plus pratique qu'une batterie de PNG à générer.
          { src: '/icon.svg', sizes: 'any', type: 'image/svg+xml' },
          // maskable = version pleine surface pour les icônes adaptives Android
          // (le launcher masque les bords selon la forme choisie par le user).
          { src: '/icon-maskable.svg', sizes: 'any', type: 'image/svg+xml', purpose: 'maskable' },
        ],
      },
      // Active le SW et le manifest en dev (`npm run dev`).
      // Sans ça, le PWA n'est actif qu'après `vite build && vite preview`.
      devOptions: {
        enabled: true,
        type: 'module',
      },
      workbox: {
        // Ressources précachées au 1er chargement pour rendre l'app dispo hors-ligne.
        globPatterns: ['**/*.{js,css,html,svg,png,ico,woff2}'],
        // Stratégies runtime : quand une requête sort de la liste précachée.
        runtimeCaching: [
          {
            // Photos Wikimedia : network-first (fraîches si en ligne, cache si offline).
            urlPattern: /^https:\/\/upload\.wikimedia\.org\/.*/i,
            handler: 'CacheFirst',
            options: {
              cacheName: 'wikimedia-images',
              expiration: { maxEntries: 200, maxAgeSeconds: 60 * 60 * 24 * 30 },
              cacheableResponse: { statuses: [0, 200] },
            },
          },
          {
            // Tuiles OpenStreetMap pour la carte.
            urlPattern: /^https:\/\/[a-c]\.tile\.openstreetmap\.org\/.*/i,
            handler: 'CacheFirst',
            options: {
              cacheName: 'osm-tiles',
              expiration: { maxEntries: 300, maxAgeSeconds: 60 * 60 * 24 * 7 },
            },
          },
          {
            // Appels API : network-first, fallback cache si offline.
            urlPattern: /\/api\/.*/i,
            handler: 'NetworkFirst',
            options: {
              cacheName: 'api-cache',
              networkTimeoutSeconds: 5,
              expiration: { maxEntries: 100, maxAgeSeconds: 60 * 60 * 24 },
            },
          },
        ],
      },
    }),
  ],
})
