/**
 * Footer — pied de page présent sur toutes les pages via App.jsx.
 *
 * 3 colonnes en desktop :
 *   1. Logo + tagline
 *   2. Raccourcis « Explorer » (les filtres passent par ?category= sur home)
 *   3. Bloc À propos
 *
 * En bas : copyright + crédit auteur.
 */

import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'

function Footer() {
  const { t } = useTranslation()

  return (
    <footer className="bg-teal-950 text-sand-100 mt-16">
      <div className="max-w-6xl mx-auto px-6 py-12 grid grid-cols-1 sm:grid-cols-3 gap-8">
        {/* Colonne 1 : logo + tagline */}
        <div>
          <Link to="/" className="font-display font-800 text-xl text-white tracking-tight">
            🏺 Athar
          </Link>
          <p className="text-sand-200/70 text-sm mt-3 leading-relaxed">
            {t('footer.tagline')}
          </p>
        </div>

        {/* Colonne 2 : raccourcis vers la home pré-filtrée.
            HomePage lit ?category= via useSearchParams et applique le filtre. */}
        <div>
          <h3 className="font-display font-700 text-white mb-3">{t('footer.explore')}</h3>
          <ul className="space-y-2 text-sm">
            <li>
              <Link to="/" className="text-sand-200/80 hover:text-terracotta-400 transition">
                {t('footer.all_sites')}
              </Link>
            </li>
            <li>
              <Link to="/?category=romain" className="text-sand-200/80 hover:text-terracotta-400 transition">
                {t('footer.roman_sites')}
              </Link>
            </li>
            <li>
              <Link to="/?category=naturel" className="text-sand-200/80 hover:text-terracotta-400 transition">
                {t('footer.natural_sites')}
              </Link>
            </li>
            <li>
              <Link to="/?category=religieux" className="text-sand-200/80 hover:text-terracotta-400 transition">
                {t('footer.sacred_places')}
              </Link>
            </li>
          </ul>
        </div>

        {/* Colonne 3 : à propos */}
        <div>
          <h3 className="font-display font-700 text-white mb-3">{t('footer.about')}</h3>
          <p className="text-sand-200/70 text-sm leading-relaxed">
            {t('footer.about_text')}
          </p>
        </div>
      </div>

      {/* Bandeau bas avec copyright + crédit auteur */}
      <div className="border-t border-white/10">
        <div className="max-w-6xl mx-auto px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-sand-200/60">
          <p>© {new Date().getFullYear()} Athar — {t('footer.credit')}</p>
          <p>
            {t('footer.designed_by', { name: 'Baya SEBIA' })} 🇩🇿
          </p>
        </div>
      </div>
    </footer>
  )
}

export default Footer
