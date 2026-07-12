/**
 * Configuration i18next — internationalisation de l'interface.
 *
 * i18next est la lib qui traduit les textes de l'UI (menus, boutons, titres…).
 * Les fiches sites/itinéraires, elles, sont traduites en base côté backend.
 *
 * Usage dans un composant :
 *   const { t } = useTranslation()
 *   <button>{t('nav.login')}</button>
 *
 * Les JSON fr/ar/en contiennent tous exactement la même arborescence de clés.
 */

import i18n from 'i18next'
import { initReactI18next } from 'react-i18next'
import fr from './fr.json'
import ar from './ar.json'
import en from './en.json'

i18n.use(initReactI18next).init({
  resources: {
    fr: { translation: fr },
    ar: { translation: ar },
    en: { translation: en },
  },
  // Langue initiale : celle mémorisée par l'utilisateur, sinon français.
  lng: localStorage.getItem('language') || 'fr',
  // Si une clé manque dans la langue active, on retombe sur le français.
  fallbackLng: 'fr',
  interpolation: {
    // React échappe déjà les XSS. Pas besoin qu'i18next le refasse.
    escapeValue: false,
  },
})

export default i18n
