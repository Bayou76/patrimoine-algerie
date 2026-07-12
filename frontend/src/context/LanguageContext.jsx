/**
 * LanguageContext — gère la langue courante (fr, ar, en) dans toute l'app.
 *
 * Un « Context » React permet de partager une valeur entre tous les composants
 * sans passer par props. Ici : la langue active + une fonction pour la changer.
 *
 * Persistance : la langue choisie est stockée dans localStorage pour être
 * retenue entre les visites.
 *
 * Effet de bord : quand la langue change, on met à jour l'attribut lang et dir
 * de <html> pour que le navigateur et les lecteurs d'écran comprennent la
 * langue et que l'arabe s'affiche de droite à gauche (RTL).
 */

import { createContext, useContext, useEffect, useState } from 'react'
import i18n from '../i18n'

const LanguageContext = createContext(null)

// Métadonnées des langues supportées. rtl = right-to-left (arabe).
export const LANGUAGES = [
  { code: 'fr', label: 'Français', rtl: false },
  { code: 'ar', label: 'العربية', rtl: true },
  { code: 'en', label: 'English', rtl: false },
]

export function LanguageProvider({ children }) {
  // useState(fn) : la fonction n'est exécutée qu'au 1er rendu (lazy init).
  // Sinon localStorage serait lu à chaque render (inutile).
  const [language, setLanguage] = useState(
    () => localStorage.getItem('language') || 'fr',
  )

  const changeLanguage = (code) => {
    localStorage.setItem('language', code)
    setLanguage(code)
    i18n.changeLanguage(code) // Notifie i18next (traductions de l'interface).
  }

  // À chaque changement de langue, on met à jour <html lang="..." dir="...">.
  // Important pour l'accessibilité, le SEO et le rendu correct de l'arabe.
  useEffect(() => {
    const lang = LANGUAGES.find((l) => l.code === language)
    document.documentElement.lang = language
    document.documentElement.dir = lang?.rtl ? 'rtl' : 'ltr'
  }, [language])

  return (
    <LanguageContext.Provider value={{ language, changeLanguage }}>
      {children}
    </LanguageContext.Provider>
  )
}

// Hook custom pour consommer facilement le contexte dans n'importe quel composant.
// Usage : const { language, changeLanguage } = useLanguage()
export function useLanguage() {
  return useContext(LanguageContext)
}
