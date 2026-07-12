/**
 * HomePage — la page d'accueil d'Athar (/).
 *
 * Structure :
 *   1. Hero avec image de Djemila en fond
 *   2. Barre de recherche libre (filtre côté client sur nom/wilaya/desc)
 *   3. Tuiles de catégorie (filtre côté serveur)
 *   4. Filtre wilaya + bouton reset
 *   5. Carte Leaflet des sites filtrés
 *   6. Grille des cartes de sites
 *
 * Les filtres category/wilaya sont dans l'URL (?category=romain&wilaya=Alger)
 * pour que la home soit partageable et que le bouton retour du navigateur
 * fonctionne correctement.
 */

import { useEffect, useMemo, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { api } from '../services/api'
import { useLanguage } from '../context/LanguageContext'
import { WILAYAS } from '../utils/wilayas'
import SiteMap from '../components/SiteMap'
import SiteList from '../components/SiteList'
import CategoryTiles from '../components/CategoryTiles'
import { usePageMeta } from '../utils/pageMeta'

function HomePage() {
  const { language } = useLanguage()
  const { t } = useTranslation()
  const [sites, setSites] = useState([])
  // useSearchParams : hook react-router pour lire/écrire les query params.
  // Source de vérité : l'URL, pas un état local.
  const [searchParams, setSearchParams] = useSearchParams()
  const category = searchParams.get('category') || ''
  const wilaya = searchParams.get('wilaya') || ''
  const [search, setSearch] = useState('') // recherche libre — état local car pas partageable

  // Helper pour changer un query param sans écraser les autres.
  const updateParam = (key, value) => {
    const next = new URLSearchParams(searchParams)
    if (value) next.set(key, value)
    else next.delete(key) // Valeur vide → on retire le param de l'URL
    setSearchParams(next, { replace: false })
  }
  const setCategory = (v) => updateParam('category', v)
  const setWilaya = (v) => updateParam('wilaya', v)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  // À chaque changement de langue/filtre, on refetch depuis l'API.
  // Le filtrage catégorie/wilaya se fait côté serveur (SQL WHERE), plus efficace.
  useEffect(() => {
    setLoading(true)
    setError(null)

    api
      .getSites({ lang: language, ...(category && { category }), ...(wilaya && { wilaya }) })
      .then(setSites)
      .catch(() => setError(t('list.error')))
      .finally(() => setLoading(false))
  }, [language, category, wilaya])

  // La recherche libre s'applique côté client sur ce qu'on a déjà reçu.
  // useMemo évite de refiltrer à chaque render.
  const filteredSites = useMemo(() => {
    if (!search.trim()) return sites
    const q = search.trim().toLowerCase()
    return sites.filter((s) =>
      s.name?.toLowerCase().includes(q) ||
      s.wilaya?.toLowerCase().includes(q) ||
      s.description?.toLowerCase().includes(q)
    )
  }, [sites, search])

  // Meta SEO : titre, description, JSON-LD schema.org WebSite pour Google.
  usePageMeta({
    title: null, // null → titre par défaut « Athar — Guide du patrimoine algérien »
    description: t('home.tagline'),
    type: 'website',
    language,
    jsonLdId: 'home',
    jsonLd: {
      '@context': 'https://schema.org',
      '@type': 'WebSite',
      name: 'Athar',
      url: window.location.origin,
      inLanguage: [language, 'fr', 'ar', 'en'],
    },
  })

  // On ne montre dans le select que les wilayas qui ont au moins un site.
  // Évite un menu déroulant avec 58 options dont 40 vides.
  const availableWilayas = useMemo(
    () => WILAYAS.filter((w) => sites.some((s) => s.wilaya === w)),
    [sites],
  )

  return (
    <div>
      {/* --- Hero avec dégradé sombre en overlay --- */}
      <div
        className="relative bg-cover bg-center px-6 py-24 text-center"
        style={{
          backgroundImage:
            "url(https://upload.wikimedia.org/wikipedia/commons/thumb/3/34/Roman_Ruins_of_Djemila_in_S%C3%A9tif%2C_Algeria.jpg/500px-Roman_Ruins_of_Djemila_in_S%C3%A9tif%2C_Algeria.jpg)",
        }}
      >
        {/* Le dégradé fond le hero avec la couleur sable du contenu en dessous */}
        <div className="absolute inset-0 bg-gradient-to-b from-teal-950/85 via-teal-950/75 to-sand-50" />
        <div className="relative">
          <h1 className="font-display font-800 text-4xl sm:text-6xl text-white tracking-tight">
            {t('home.title')}
          </h1>
          <p className="text-sand-100/90 mt-4 max-w-xl mx-auto text-lg">
            {t('home.tagline')}
          </p>
        </div>
      </div>

      {/* -mt-8 : la zone de contenu chevauche le hero, effet « carte flottante ». */}
      <div className="max-w-6xl mx-auto px-6 pb-16 -mt-8 relative">
        {/* --- Barre de recherche libre --- */}
        <div className="mb-8 bg-white rounded-2xl shadow-md border border-sand-200 p-3 flex items-center gap-2">
          <span className="text-teal-900/50 text-xl px-2">🔍</span>
          <input
            type="search"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder={t('home.search_placeholder')}
            className="flex-1 bg-transparent focus:outline-none text-teal-950 placeholder:text-teal-900/50"
          />
          {search && (
            <button
              type="button"
              onClick={() => setSearch('')}
              className="text-sm text-teal-900/60 hover:text-terracotta-500 px-2"
            >
              ✕
            </button>
          )}
        </div>

        {/* --- Tuiles catégorie --- */}
        <h2 className="font-display font-700 text-2xl text-teal-950 mb-4">{t('home.explore_by_category')}</h2>
        <CategoryTiles active={category} onSelect={setCategory} />

        {/* --- Filtres secondaires : reset + wilaya --- */}
        <div className="flex flex-wrap gap-3 justify-end mt-4 mb-10">
          {(category || wilaya || search) && (
            <button
              type="button"
              onClick={() => {
                setSearchParams({}, { replace: false }) // Vide tous les params
                setSearch('')
              }}
              className="text-sm text-terracotta-500 hover:text-terracotta-600 font-600 px-3 py-2"
            >
              {t('home.reset_filters')}
            </button>
          )}
          <select
            value={wilaya}
            onChange={(e) => setWilaya(e.target.value)}
            className="border border-sand-200 bg-white rounded-full px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-terracotta-400"
          >
            <option value="">{t('home.all_wilayas')}</option>
            {availableWilayas.map((w) => (
              <option key={w} value={w}>{w}</option>
            ))}
          </select>
        </div>

        {/* --- Carte Leaflet des sites filtrés --- */}
        <h2 className="font-display font-700 text-2xl text-teal-950 mb-4">{t('home.explore_on_map')}</h2>
        {/* isolate + z-0 : nouveau contexte d'empilement, évite que les
            popups Leaflet passent au-dessus du menu burger. */}
        <div className="relative isolate z-0 mb-12 rounded-2xl overflow-hidden shadow-lg border border-sand-200">
          <SiteMap sites={filteredSites} />
        </div>

        {/* --- Grille des cartes de sites --- */}
        <h2 className="font-display font-700 text-2xl text-teal-950 mb-4">
          {t('home.sites_to_discover')}{' '}
          <span className="text-teal-900/50 text-base font-400">
            ({filteredSites.length})
          </span>
        </h2>
        <SiteList sites={filteredSites} loading={loading} error={error} />
      </div>
    </div>
  )
}

export default HomePage
