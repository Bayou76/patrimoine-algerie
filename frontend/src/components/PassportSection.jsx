/**
 * PassportSection — « Passeport Athar » : grille de tous les sites avec une
 * coche sur ceux déjà visités + barre de progression globale.
 *
 * Différent des badges (paliers d'accomplissement) : ici c'est une checklist
 * complète, façon passeport à tamponner, qui donne envie de « compléter la
 * collection ». Réutilise les données déjà chargées (liste complète des
 * sites + sites visités de l'utilisateur), aucun nouvel appel API dédié.
 */

import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import Reveal from './Reveal'

function PassportSection({ allSites, visitedIds }) {
  const { t } = useTranslation()

  if (!allSites || allSites.length === 0) return null

  const visitedCount = allSites.filter((s) => visitedIds.has(s.id)).length
  const percent = Math.round((visitedCount / allSites.length) * 100)

  return (
    <section className="mb-10">
      <h2 className="font-display font-700 text-2xl text-teal-950 mb-1 flex items-center gap-2">
        <span>🇩🇿</span> {t('me.passport')}
      </h2>
      <p className="text-sm text-teal-900/60 mb-4">
        {t('me.passport_progress', { visited: visitedCount, total: allSites.length })}
      </p>

      {/* Barre de progression globale */}
      <div className="h-2.5 bg-sand-200 rounded-full overflow-hidden mb-5">
        <div
          className="h-full bg-gradient-to-r from-terracotta-500 to-gold-400 rounded-full transition-all duration-700"
          style={{ width: `${percent}%` }}
        />
      </div>

      <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
        {allSites.map((site, idx) => {
          const visited = visitedIds.has(site.id)
          return (
            <Reveal key={site.id} delay={Math.min(idx * 20, 300)}>
              <Link
                to={`/sites/${site.slug}`}
                className={`relative block rounded-xl overflow-hidden border aspect-square transition ${
                  visited
                    ? 'border-terracotta-400 shadow-sm'
                    : 'border-sand-200 grayscale opacity-50 hover:opacity-80'
                }`}
                title={site.name}
              >
                <div
                  className="absolute inset-0 bg-cover bg-center bg-sand-200"
                  style={site.image_path ? { backgroundImage: `url(${site.image_path})` } : undefined}
                />
                {visited && (
                  <span className="absolute top-1 right-1 w-5 h-5 rounded-full bg-terracotta-500 text-white text-xs flex items-center justify-center shadow">
                    ✓
                  </span>
                )}
                <span className="absolute bottom-0 left-0 right-0 bg-teal-950/80 text-white text-[10px] font-600 px-1.5 py-1 truncate">
                  {site.name}
                </span>
              </Link>
            </Reveal>
          )
        })}
      </div>
    </section>
  )
}

export default PassportSection
