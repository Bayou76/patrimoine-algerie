/**
 * SimilarSites — 3 cartes de suggestions en bas d'une fiche site.
 *
 * Les données viennent du backend (SiteController::show, champ `similar`)
 * qui les calcule via un scoring SQL : même catégorie > même wilaya.
 * Cascade d'apparition (delay = idx * 80) pour un effet fluide.
 */

import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import Reveal from './Reveal'

function SimilarSites({ sites }) {
  const { t } = useTranslation()

  // Si le backend n'a rien renvoyé (site isolé), on n'affiche pas la section.
  if (!sites || sites.length === 0) return null

  return (
    <section className="mt-12 mb-6">
      <h2 className="font-display font-700 text-2xl text-teal-950 mb-4">
        {t('detail.similar_sites')}
      </h2>
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {sites.map((site, idx) => (
          <Reveal key={site.id} delay={idx * 80}>
            <Link
              to={`/sites/${site.slug}`}
              className="group block rounded-2xl overflow-hidden bg-white shadow-md hover:shadow-xl transition-all hover:-translate-y-1"
            >
              <div className="relative h-32 overflow-hidden bg-sand-200">
                {/* Zoom au hover via group-hover:scale-110 sur le fond. */}
                <div
                  className="h-full w-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110"
                  style={site.image_path ? { backgroundImage: `url(${site.image_path})` } : undefined}
                />
                <span className="absolute bottom-2 left-2 bg-terracotta-500 text-white text-[10px] font-600 px-2 py-0.5 rounded-full capitalize shadow">
                  {t(`categories.${site.category}`, site.category)}
                </span>
              </div>
              <div className="p-3">
                <p className="font-display font-700 text-teal-950 text-sm truncate">{site.name}</p>
                <p className="text-xs text-teal-900/60">{site.wilaya}</p>
              </div>
            </Link>
          </Reveal>
        ))}
      </div>
    </section>
  )
}

export default SimilarSites
