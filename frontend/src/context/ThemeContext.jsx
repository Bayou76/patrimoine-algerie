/**
 * ThemeContext — gère le mode clair/sombre.
 *
 * Le thème est appliqué en posant data-theme="dark" (ou "light") sur <html>.
 * Le CSS (index.css) définit alors les bonnes variables --app-bg, --app-text…
 * pour toute l'interface.
 *
 * Choix initial :
 *   1. localStorage (préférence utilisateur mémorisée)
 *   2. Sinon : préférence système via prefers-color-scheme
 */

import { createContext, useContext, useEffect, useState } from 'react'

const ThemeContext = createContext(null)

function initialTheme() {
  const stored = localStorage.getItem('theme')
  if (stored === 'light' || stored === 'dark') return stored
  // matchMedia interroge la préférence système (macOS/Windows/Android)
  return window.matchMedia?.('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
}

export function ThemeProvider({ children }) {
  const [theme, setTheme] = useState(initialTheme)

  // À chaque changement, on met à jour <html data-theme="..."> et localStorage.
  useEffect(() => {
    document.documentElement.setAttribute('data-theme', theme)
    localStorage.setItem('theme', theme)
  }, [theme])

  const toggleTheme = () => setTheme((t) => (t === 'dark' ? 'light' : 'dark'))

  return (
    <ThemeContext.Provider value={{ theme, toggleTheme }}>
      {children}
    </ThemeContext.Provider>
  )
}

// Le fallback ?? { theme: 'light', toggleTheme: () => {} } évite un crash
// si le hook est utilisé hors Provider (ex: dans un test unitaire).
export function useTheme() {
  return useContext(ThemeContext) ?? { theme: 'light', toggleTheme: () => {} }
}
