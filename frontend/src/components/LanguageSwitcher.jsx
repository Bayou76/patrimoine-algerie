/**
 * LanguageSwitcher — sélecteur déroulant fr / ar / en.
 *
 * Consomme LanguageContext pour lire la langue courante et la changer.
 * L'effet est immédiat : toutes les traductions de l'interface (via t()) et
 * les appels API (qui passent ?lang=xxx) se mettent à jour instantanément.
 */

import { LANGUAGES, useLanguage } from '../context/LanguageContext'

function LanguageSwitcher() {
  const { language, changeLanguage } = useLanguage()

  return (
    <select
      value={language}
      onChange={(event) => changeLanguage(event.target.value)}
      className="bg-white/10 border border-white/20 text-white rounded-full px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-terracotta-400"
    >
      {LANGUAGES.map((lang) => (
        <option key={lang.code} value={lang.code}>
          {lang.label}
        </option>
      ))}
    </select>
  )
}

export default LanguageSwitcher
