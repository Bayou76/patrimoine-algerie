/**
 * ThemeToggle — bouton 🌙 / 🌞 qui bascule le thème clair/sombre.
 *
 * Astuce : le key={theme} sur le <span> force React à recréer l'élément
 * quand le thème change, ce qui relance l'animation animate-fade-in.
 * Résultat : petit effet de crossfade agréable sur le changement d'icône.
 */

import { useTheme } from '../context/ThemeContext'

function ThemeToggle() {
  const { theme, toggleTheme } = useTheme()
  const isDark = theme === 'dark'

  return (
    <button
      type="button"
      onClick={toggleTheme}
      aria-label={isDark ? 'Passer en mode clair' : 'Passer en mode sombre'}
      className="text-lg leading-none rounded-full w-9 h-9 flex items-center justify-center bg-white/10 hover:bg-white/20 transition"
    >
      <span className="inline-block animate-fade-in" key={theme}>
        {isDark ? '🌞' : '🌙'}
      </span>
    </button>
  )
}

export default ThemeToggle
