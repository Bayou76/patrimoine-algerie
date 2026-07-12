/**
 * GlobalTimelinePage — page /chronologie : les 158 événements historiques
 * de tous les sites algériens, groupés par grande époque et triés par année.
 *
 * De -20 000 000 (formation du Hoggar) à 2020 (record du minaret d'Alger).
 * Filtres par catégorie pour ne montrer que les événements d'un type de site.
 */

import { useEffect, useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { api } from '../services/api'
import { useLanguage } from '../context/LanguageContext'
import Reveal from '../components/Reveal'
import { SkeletonSiteGrid } from '../components/Skeleton'
import { usePageMeta } from '../utils/pageMeta'

const CATEGORIES = ['romain', 'naturel', 'religieux', 'casbah', 'islamique', 'colonial', 'moderne', 'prehistorique']

/**
 * Groupe les événements par grande époque historique.
 * Le tableau `label(year)` définit les frontières :
 *   < -100k : formation géologique
 *   < -3000 : préhistoire
 *   < 0     : antiquité av. J.-C.
 *   < 700   : antiquité apr. J.-C. (Rome, byzantins)
 *   < 1500  : Moyen Âge (dynasties berbères, almoravides…)
 *   < 1830  : époque moderne (ottomans, régence d'Alger)
 *   < 1962  : période coloniale française
 *   sinon   : Algérie contemporaine
 *
 * Retourne un Array de [era, events[]] utilisable dans .map().
 */
function groupByEra(events) {
  const groups = new Map()
  const label = (year) => {
    if (year < -100000) return 'geological'
    if (year < -3000) return 'prehistory'
    if (year < 0) return 'antiquity_bc'
    if (year < 700) return 'antiquity_ad'
    if (year < 1500) return 'medieval'
    if (year < 1830) return 'early_modern'
    if (year < 1962) return 'colonial'
    return 'contemporary'
  }
  for (const event of events) {
    const key = label(event.year)
    if (!groups.has(key)) groups.set(key, [])
    groups.get(key).push(event)
  }
  // Map.entries() → array de [key, value] pour .map() dans le JSX.
  return Array.from(groups.entries())
}

function GlobalTimelinePage() {
  const { language } = useLanguage()
  const { t } = useTranslation()
  const [events, setEvents] = useState(null)
  const [category, setCategory] = useState('')

  useEffect(() => {
    setEvents(null)
    api.getGlobalTimeline(language).then(setEvents)
  }, [language])

  usePageMeta({
    title: t('timeline_page.title'),
    description: t('timeline_page.subtitle'),
    type: 'website',
    language,
  })

  const filtered = useMemo(() => {
    if (!events) return null
    if (!category) return events
    return events.filter((e) => e.site.category === category)
  }, [events, category])

  // Regroupement par époque, recalculé seulement quand filtered change.
  const groups = useMemo(() => (filtered ? groupByEra(filtered) : []), [filtered])

  return (
    <div>
      <div className="relative bg-cover bg-center px-6 py-20 text-center bg-teal-950">
        <div className="absolute inset-0 bg-gradient-to-b from-teal-950/40 to-teal-950" />
        <div className="relative">
          <h1 className="font-display font-800 text-4xl sm:text-5xl text-white tracking-tight">
            📜 {t('timeline_page.title')}
          </h1>
          <p className="text-sand-100/80 mt-3 max-w-2xl mx-auto text-lg">
            {t('timeline_page.subtitle')}
          </p>
        </div>
      </div>

      <div className="max-w-4xl mx-auto p-6">
        {/* Barre de filtres catégorie */}
        <div className="mb-8 flex flex-wrap gap-2">
          <button
            type="button"
            onClick={() => setCategory('')}
            className={`text-sm font-600 rounded-full px-3 py-1.5 transition ${
              !category
                ? 'bg-terracotta-500 text-white'
                : 'bg-white border border-sand-200 text-teal-950 hover:border-terracotta-400'
            }`}
          >
            {t('categories.all')}
          </button>
          {CATEGORIES.map((cat) => (
            <button
              key={cat}
              type="button"
              onClick={() => setCategory(cat)}
              className={`text-sm font-600 rounded-full px-3 py-1.5 transition ${
                category === cat
                  ? 'bg-terracotta-500 text-white'
                  : 'bg-white border border-sand-200 text-teal-950 hover:border-terracotta-400'
              }`}
            >
              {t(`categories.${cat}`, cat)}
            </button>
          ))}
        </div>

        {filtered === null ? (
          <SkeletonSiteGrid count={3} />
        ) : filtered.length === 0 ? (
          <p className="text-teal-900/70">{t('list.empty')}</p>
        ) : (
          <div>
            {/* Une section par grande époque */}
            {groups.map(([era, evts]) => (
              <section key={era} className="mb-12">
                <Reveal>
                  <h2 className="font-display font-700 text-xl text-terracotta-500 mb-4 border-b border-sand-200 pb-2">
                    {t(`timeline_page.eras.${era}`, era)}
                    <span className="ms-2 text-teal-900/50 text-base font-400">({evts.length})</span>
                  </h2>
                </Reveal>

                {/* Liste ordonnée avec ligne verticale + pastilles (comme Timeline.jsx) */}
                <ol className="relative border-s-2 border-sand-200 ms-2">
                  {evts.map((event, idx) => (
                    <Reveal key={idx} delay={Math.min(idx * 40, 200)}>
                      <li className="relative mb-6 ms-4">
                        <div className="absolute w-3 h-3 bg-terracotta-500 rounded-full -start-[23px] mt-1.5 ring-4 ring-sand-50" />
                        <time className="text-sm font-700 text-terracotta-600">{event.period_label}</time>
                        <h3 className="font-display font-700 text-base text-teal-950 mt-0.5">
                          {event.title}
                        </h3>
                        <p className="text-sm text-teal-900/80 mt-1">{event.description}</p>
                        {/* Miniature du site cliquable → sa fiche */}
                        <Link
                          to={`/sites/${event.site.slug}`}
                          className="inline-flex items-center gap-2 mt-2 text-xs text-teal-900/70 hover:text-terracotta-500 transition"
                        >
                          <span
                            className="w-6 h-6 rounded-md bg-cover bg-center bg-sand-200 shrink-0"
                            style={
                              event.site.image_path
                                ? { backgroundImage: `url(${event.site.image_path})` }
                                : undefined
                            }
                          />
                          {event.site.name}
                        </Link>
                      </li>
                    </Reveal>
                  ))}
                </ol>
              </section>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}

export default GlobalTimelinePage
