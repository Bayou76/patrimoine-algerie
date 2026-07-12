/**
 * ItinerariesPage — page /itineraires : liste des itinéraires thématiques.
 *
 * Grille de cartes cliquables avec filtre par thème (romain, sud, villes,
 * spirituel, naturel). Le clic sur une carte navigue vers /itineraires/:slug
 * qui affiche le détail avec la carte des étapes.
 */

import { useEffect, useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { api } from '../services/api'
import { useLanguage } from '../context/LanguageContext'
import { useAuth } from '../context/AuthContext'
import Reveal from '../components/Reveal'
import { SkeletonSiteGrid } from '../components/Skeleton'
import { usePageMeta } from '../utils/pageMeta'

// Thèmes disponibles. Doivent correspondre aux valeurs stockées en BDD
// (voir seeder ItinerariesSeeder) et aux clés i18n.
const THEMES = ['romain', 'sud', 'villes', 'spirituel', 'naturel']

function ItinerariesPage() {
  const { language } = useLanguage()
  const { user } = useAuth()
  const { t } = useTranslation()
  const [items, setItems] = useState(null) // null = pas encore chargé
  const [theme, setTheme] = useState('') // '' = tous

  // Rechargement à chaque changement de langue.
  useEffect(() => {
    setItems(null) // Force l'affichage du skeleton
    api.getItineraries(language).then(setItems)
  }, [language])

  usePageMeta({
    title: t('itineraries_page.title'),
    description: t('itineraries_page.subtitle'),
    type: 'website',
    language,
  })

  // Filtrage côté client (les données arrivent déjà légères).
  const filtered = useMemo(() => {
    if (!items) return null
    if (!theme) return items
    return items.filter((i) => i.theme === theme)
  }, [items, theme])

  return (
    <div>
      {/* Hero */}
      <div className="relative bg-cover bg-center px-6 py-20 text-center bg-teal-950">
        <div className="absolute inset-0 bg-gradient-to-b from-teal-950/40 to-teal-950" />
        <div className="relative">
          <h1 className="font-display font-800 text-4xl sm:text-5xl text-white tracking-tight">
            🗺️ {t('itineraries_page.title')}
          </h1>
          <p className="text-sand-100/80 mt-3 max-w-2xl mx-auto text-lg">
            {t('itineraries_page.subtitle')}
          </p>
          {user && (
            <Link
              to="/itineraires/proposer"
              className="inline-flex items-center gap-2 mt-5 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-600 rounded-full px-5 py-2.5 text-sm transition"
            >
              🌍 {t('itineraries_page.propose')}
            </Link>
          )}
        </div>
      </div>

      <div className="max-w-6xl mx-auto p-6">
        {/* Filtres par thème (pilules) */}
        <div className="mb-8 flex flex-wrap gap-2">
          <button
            type="button"
            onClick={() => setTheme('')}
            className={`text-sm font-600 rounded-full px-3 py-1.5 transition ${
              !theme
                ? 'bg-terracotta-500 text-white'
                : 'bg-white border border-sand-200 text-teal-950 hover:border-terracotta-400'
            }`}
          >
            {t('categories.all')}
          </button>
          {THEMES.map((th) => (
            <button
              key={th}
              type="button"
              onClick={() => setTheme(th)}
              className={`text-sm font-600 rounded-full px-3 py-1.5 transition ${
                theme === th
                  ? 'bg-terracotta-500 text-white'
                  : 'bg-white border border-sand-200 text-teal-950 hover:border-terracotta-400'
              }`}
            >
              {t(`itineraries_page.themes.${th}`, th)}
            </button>
          ))}
        </div>

        {/* 3 états : chargement → skeleton, vide → message, sinon → grille */}
        {filtered === null ? (
          <SkeletonSiteGrid count={3} />
        ) : filtered.length === 0 ? (
          <p className="text-teal-900/70">{t('list.empty')}</p>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filtered.map((item, idx) => (
              // Cascade par colonne (idx % 3), plafonnée à 200ms.
              <Reveal key={item.slug} delay={Math.min((idx % 3) * 100, 200)}>
                <Link
                  to={`/itineraires/${item.slug}`}
                  className="group block bg-white rounded-2xl overflow-hidden border border-sand-200 hover:border-terracotta-400 hover:shadow-xl transition h-full"
                >
                  <div className="relative">
                    <div
                      className="h-48 bg-cover bg-center bg-sand-200 group-hover:scale-105 transition-transform duration-500"
                      style={item.cover_image ? { backgroundImage: `url(${item.cover_image})` } : undefined}
                    />
                    {item.is_community && (
                      <span className="absolute top-2 right-2 text-[10px] font-600 bg-gold-400 text-teal-950 px-2 py-0.5 rounded-full shadow">
                        🌍 {t('itineraries_page.community')}
                      </span>
                    )}
                  </div>
                  <div className="p-5">
                    {/* Rangée de tags : durée, difficulté, nombre d'étapes */}
                    <div className="flex flex-wrap gap-2 mb-2">
                      <span className="text-xs font-600 rounded-full px-2 py-0.5 bg-terracotta-50 text-terracotta-600 border border-terracotta-200">
                        ⏱️ {item.duration}
                      </span>
                      {item.difficulty && (
                        <span className="text-xs font-600 rounded-full px-2 py-0.5 bg-sand-100 text-teal-950 border border-sand-200">
                          {t(`itineraries_page.difficulties.${item.difficulty}`, item.difficulty)}
                        </span>
                      )}
                      <span className="text-xs font-600 rounded-full px-2 py-0.5 bg-teal-950/5 text-teal-950 border border-teal-950/10">
                        📍 {item.sites_count}
                      </span>
                    </div>
                    <h3 className="font-display font-700 text-xl text-teal-950 group-hover:text-terracotta-500 transition">
                      {item.title}
                    </h3>
                    <p className="text-sm text-teal-900/80 mt-2 line-clamp-3">{item.summary}</p>
                  </div>
                </Link>
              </Reveal>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}

export default ItinerariesPage
