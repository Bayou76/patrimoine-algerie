/**
 * ItineraryDetailPage — page /itineraires/:slug : détail d'un itinéraire.
 *
 * Structure :
 *   1. Hero avec image de couverture + titre + tags (durée, difficulté, étapes)
 *   2. Description longue
 *   3. Carte Leaflet des étapes reliées par une ligne
 *   4. Liste ordonnée des étapes avec pastille numérotée
 *      (jour, nom du site, catégorie, wilaya, note du guide)
 *
 * SEO : JSON-LD schema.org TouristTrip avec la liste ordonnée des sites.
 */

import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { api } from '../services/api'
import { useLanguage } from '../context/LanguageContext'
import Reveal from '../components/Reveal'
import ItineraryMap from '../components/ItineraryMap'
import ShareButton from '../components/ShareButton'
import { SkeletonSiteGrid } from '../components/Skeleton'
import { usePageMeta, buildItineraryJsonLd } from '../utils/pageMeta'

function ItineraryDetailPage() {
  const { slug } = useParams()
  const { language } = useLanguage()
  const { t } = useTranslation()
  const [itinerary, setItinerary] = useState(null)
  const [notFound, setNotFound] = useState(false)

  useEffect(() => {
    setItinerary(null)
    setNotFound(false)
    api
      .getItinerary(slug, language)
      .then(setItinerary)
      .catch((err) => {
        // On distingue 404 (slug inconnu) des autres erreurs pour afficher un
        // message différent (page « pas trouvé » vs message d'erreur).
        if (err?.status === 404) setNotFound(true)
      })
  }, [slug, language])

  usePageMeta({
    title: itinerary?.title,
    description: itinerary?.summary,
    image: itinerary?.cover_image,
    type: 'article',
    language,
    jsonLdId: 'itinerary',
    jsonLd: buildItineraryJsonLd(itinerary),
  })

  // Cas 1 : slug inconnu
  if (notFound) {
    return (
      <div className="max-w-3xl mx-auto p-6">
        <p className="text-teal-900/70">{t('list.empty')}</p>
        <Link to="/itineraires" className="text-terracotta-500 mt-4 inline-block">
          ← {t('itineraries_page.back')}
        </Link>
      </div>
    )
  }

  // Cas 2 : chargement en cours → skeleton
  if (!itinerary) {
    return (
      <div className="max-w-3xl mx-auto p-6">
        <SkeletonSiteGrid count={3} />
      </div>
    )
  }

  return (
    <div>
      {/* --- Hero avec image de couverture --- */}
      <div
        className="relative bg-cover bg-center px-6 py-24"
        style={itinerary.cover_image ? { backgroundImage: `url(${itinerary.cover_image})` } : undefined}
      >
        <div className="absolute inset-0 bg-gradient-to-b from-teal-950/70 to-teal-950/90" />
        <div className="relative max-w-4xl mx-auto text-white">
          <Link to="/itineraires" className="text-sand-100/80 hover:text-terracotta-400 text-sm">
            ← {t('itineraries_page.back')}
          </Link>
          {itinerary.is_community && (
            <span className="inline-block mt-3 text-xs font-600 bg-gold-400 text-teal-950 px-2.5 py-1 rounded-full">
              🌍 {t('itineraries_page.proposed_by', { name: itinerary.creator_name })}
            </span>
          )}
          <h1 className="font-display font-800 text-4xl sm:text-5xl mt-3 tracking-tight">
            {itinerary.title}
          </h1>
          <p className="text-sand-100/90 mt-3 text-lg max-w-2xl">{itinerary.summary}</p>
          {/* Rangée de tags : durée, difficulté, nb étapes, bouton partager */}
          <div className="flex flex-wrap items-center gap-2 mt-4">
            <span className="text-sm font-600 rounded-full px-3 py-1 bg-white/10 border border-white/20">
              ⏱️ {itinerary.duration}
            </span>
            {itinerary.difficulty && (
              <span className="text-sm font-600 rounded-full px-3 py-1 bg-white/10 border border-white/20">
                {t(`itineraries_page.difficulties.${itinerary.difficulty}`, itinerary.difficulty)}
              </span>
            )}
            <span className="text-sm font-600 rounded-full px-3 py-1 bg-white/10 border border-white/20">
              📍 {itinerary.sites.length} {t('itineraries_page.stops')}
            </span>
            <div className="ms-1">
              <ShareButton title={itinerary.title} text={itinerary.summary} />
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-4xl mx-auto p-6">
        {itinerary.description && (
          <Reveal>
            <p className="text-teal-900/90 text-lg leading-relaxed mb-10">{itinerary.description}</p>
          </Reveal>
        )}

        {/* Carte des étapes reliées par une ligne pointillée */}
        <Reveal delay={80}>
          {/* isolate + z-0 pour ne pas polluer le stacking context du drawer mobile */}
          <div className="relative isolate z-0 mb-10 rounded-2xl overflow-hidden shadow-lg border border-sand-200">
            <ItineraryMap sites={itinerary.sites} />
          </div>
        </Reveal>

        <h2 className="font-display font-700 text-2xl text-teal-950 mb-6">
          {t('itineraries_page.stages')}
        </h2>

        {/* Liste ordonnée avec ligne verticale à gauche et pastilles numérotées */}
        <ol className="relative border-s-2 border-sand-200 ms-3">
          {itinerary.sites.map((site, idx) => (
            <Reveal key={site.id} delay={Math.min(idx * 60, 300)}>
              <li className="relative mb-8 ms-6">
                {/* Pastille circulaire terracotta avec le numéro d'étape */}
                <div className="absolute -start-[34px] mt-1 w-8 h-8 rounded-full bg-terracotta-500 text-white flex items-center justify-center text-sm font-700 ring-4 ring-sand-50">
                  {idx + 1}
                </div>
                {site.day_label && (
                  <div className="text-xs font-700 uppercase tracking-wider text-terracotta-600">
                    {site.day_label}
                  </div>
                )}
                {/* Carte cliquable vers la fiche du site */}
                <Link
                  to={`/sites/${site.slug}`}
                  className="group flex gap-4 mt-2 bg-white rounded-xl border border-sand-200 hover:border-terracotta-400 hover:shadow-md transition overflow-hidden"
                >
                  <div
                    className="w-24 sm:w-40 shrink-0 bg-cover bg-center bg-sand-200"
                    style={site.image_path ? { backgroundImage: `url(${site.image_path})` } : undefined}
                  />
                  <div className="p-4 flex-1">
                    <h3 className="font-display font-700 text-lg text-teal-950 group-hover:text-terracotta-500 transition">
                      {site.name}
                    </h3>
                    <div className="text-xs text-teal-900/60 mt-0.5">
                      {t(`categories.${site.category}`, site.category)} · {site.wilaya}
                    </div>
                    {/* Note libre de l'itinéraire (« La Pompéi africaine » etc.) */}
                    {site.note && (
                      <p className="text-sm text-teal-900/80 mt-2">{site.note}</p>
                    )}
                  </div>
                </Link>
              </li>
            </Reveal>
          ))}
        </ol>
      </div>
    </div>
  )
}

export default ItineraryDetailPage
