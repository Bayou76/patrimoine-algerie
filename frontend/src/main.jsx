/**
 * Point d'entrée de l'application React.
 *
 * Vite lit ce fichier depuis index.html (<script src="/src/main.jsx">).
 * Ici on monte l'app React dans <div id="root"> et on empile les Providers
 * qui doivent être disponibles partout dans l'arbre React.
 *
 * Ordre des Providers (de l'extérieur vers l'intérieur) :
 *   BrowserRouter  → active react-router (navigation SPA)
 *   ThemeProvider  → thème clair/sombre, doit être le plus haut pour éviter
 *                    un flash de mauvaise couleur au chargement
 *   LanguageProvider → langue fr/ar/en + i18n
 *   AuthProvider   → utilisateur connecté, tokens
 *   ToastProvider  → notifications éphémères
 *
 * StrictMode : mode strict de React qui aide à détecter les bugs pendant
 * le développement (n'a aucun effet en production).
 */

import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import './index.css'
import App from './App.jsx'
import './i18n' // Initialise i18next (import de config à effet de bord)
import { registerSW } from 'virtual:pwa-register'

// Enregistre le service worker généré par vite-plugin-pwa.
// autoUpdate + updateOnReload : la nouvelle version s'active au prochain refresh.
registerSW({ immediate: true })
import { LanguageProvider } from './context/LanguageContext.jsx'
import { AuthProvider } from './context/AuthContext.jsx'
import { ToastProvider } from './context/ToastContext.jsx'
import { ThemeProvider } from './context/ThemeContext.jsx'

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <BrowserRouter>
      <ThemeProvider>
        <LanguageProvider>
          <AuthProvider>
            <ToastProvider>
              <App />
            </ToastProvider>
          </AuthProvider>
        </LanguageProvider>
      </ThemeProvider>
    </BrowserRouter>
  </StrictMode>,
)
