/**
 * SiteList — grille des sites sur la HomePage.
 *
 * Gère 3 états :
 *   - loading → grille de squelettes shimmer
 *   - error   → message rouge
 *   - vide    → « aucun site trouvé »
 *
 * Sinon : grille responsive (1 / 2 / 3 colonnes) avec animation en cascade
 * (delay = (idx % 3) * 100 pour que chaque colonne ait un décalage).
 */

import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { SkeletonSiteGrid } from './Skeleton'
import Reveal from './Reveal'

function SiteList({ sites, loading, error }) {
  const { t } = useTranslation()

  if (loading) return <SkeletonSiteGrid />
  if (error) return <p className="text-terracotta-600">{error}</p>
  if (sites.length === 0) return <p className="text-teal-900/70">{t('list.empty')}</p>

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      {sites.map((site, idx) => (
        // idx % 3 = 0/1/2 pour délai en cascade colonne par colonne.
        <Reveal key={site.id} delay={(idx % 3) * 100}>
          <Link
            to={`/sites/${site.slug}`}
            className="group block rounded-2xl overflow-hidden bg-white shadow-md hover:shadow-xl transition-all hover:-translate-y-1"
          >
            <div className="relative h-44 overflow-hidden bg-sand-200">
              <div
                className="h-full w-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110"
                style={site.image_path ? { backgroundImage: `url(${site.image_path})` } : undefined}
              />
              <span className="absolute bottom-2 left-2 bg-terracotta-500 text-white text-xs font-600 px-2.5 py-1 rounded-full capitalize shadow">
                {t(`categories.${site.category}`, site.category)}
              </span>
            </div>
            <div className="p-4">
              <h2 className="font-display font-700 text-lg text-teal-950">{site.name}</h2>
              <p className="text-sm text-teal-900/60">{site.wilaya}</p>
              {/* line-clamp-2 : coupe à 2 lignes avec « … » */}
              <p className="text-sm text-teal-900/80 mt-2 line-clamp-2">{site.description}</p>
              {site.average_rating !== null && (
                <p className="text-sm text-gold-500 font-600 mt-2">★ {site.average_rating}</p>
              )}
            </div>
          </Link>
        </Reveal>
      ))}
    </div>
  )
}

export default SiteList
