/**
 * MePage — page /me « Mon compte » (utilisateur connecté uniquement).
 *
 * Affiche : profil, badges d'exploration, favoris, sites visités, avis publiés.
 * Toutes ces données arrivent en un seul appel API (GET /api/me).
 *
 * Sécurité : si l'utilisateur n'est pas connecté, redirection vers /login.
 */

import { useEffect, useMemo, useState } from 'react'
import { Link, Navigate } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { api } from '../services/api'
import { useAuth } from '../context/AuthContext'
import { useLanguage } from '../context/LanguageContext'
import BadgesGrid from '../components/BadgesGrid'
import PassportSection from '../components/PassportSection'

/** Petite carte horizontale d'un site (utilisée dans les listes de la page). */
function SiteCard({ site }) {
  const { t } = useTranslation()
  return (
    <Link
      to={`/sites/${site.slug}`}
      className="group flex items-center gap-3 bg-white rounded-xl border border-sand-200 shadow-sm hover:shadow-md transition p-2"
    >
      <div
        className="w-16 h-16 rounded-lg bg-cover bg-center bg-sand-200 shrink-0"
        style={site.image_path ? { backgroundImage: `url(${site.image_path})` } : undefined}
      />
      {/* min-w-0 : sans ça, le contenu forcerait la largeur et truncate ne marcherait pas */}
      <div className="min-w-0">
        <p className="font-display font-700 text-teal-950 truncate">{site.name}</p>
        <p className="text-xs text-teal-900/60 capitalize">
          {t(`categories.${site.category}`, site.category)} · {site.wilaya}
        </p>
      </div>
    </Link>
  )
}

function MePage() {
  const { user, loading } = useAuth()
  const { language } = useLanguage()
  const { t } = useTranslation()
  const [data, setData] = useState(null)
  const [dataLoading, setDataLoading] = useState(true)
  const [allSites, setAllSites] = useState([])

  useEffect(() => {
    // Sans user connecté on ne fait pas l'appel (évite un 401 dans les logs).
    if (!user) return
    api
      .getMe()
      .then(setData)
      .finally(() => setDataLoading(false))
    // Catalogue complet pour le Passeport — indépendant du chargement de /me.
    api.getSites({ lang: language }).then(setAllSites)
  }, [user, language])

  // Set des ids visités : lookup O(1) au lieu de .find() dans la boucle du passeport.
  const visitedIds = useMemo(
    () => new Set((data?.visited ?? []).map((s) => s.id)),
    [data],
  )

  // Attente de la résolution du token → écran de chargement neutre.
  if (loading) return <p className="p-6 text-teal-900">{t('me.loading')}</p>
  // Pas connecté → redirection définitive (replace évite le back qui rerevient ici).
  if (!user) return <Navigate to="/login" replace />
  if (dataLoading) return <p className="p-6 text-teal-900">{t('me.loading')}</p>
  if (!data) return null

  return (
    <div className="max-w-4xl mx-auto p-6">
      <h1 className="font-display font-800 text-3xl text-teal-950 mb-2">
        {t('me.title')}
      </h1>
      <p className="text-teal-900/70 mb-8">
        {t('me.greeting', { name: data.user.name })}
        {data.user.is_admin && (
          <span className="ml-2 bg-teal-800 text-white text-xs font-600 px-2 py-0.5 rounded-full">
            {t('me.admin_badge')}
          </span>
        )}
      </p>

      {/* Section badges : grille des 9 badges avec leur progression */}
      <BadgesGrid badges={data.badges} />

      {/* Passeport Athar : checklist visuelle de tous les sites */}
      <PassportSection allSites={allSites} visitedIds={visitedIds} />

      {/* Section favoris */}
      <section className="mb-10">
        <h2 className="font-display font-700 text-2xl text-teal-950 mb-4 flex items-center gap-2">
          <span>❤️</span> {t('me.favorites')}
          <span className="text-teal-900/50 text-base font-400">({data.favorites.length})</span>
        </h2>
        {data.favorites.length === 0 ? (
          <p className="text-sm text-teal-900/60">
            {t('me.no_favorites')}
          </p>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            {data.favorites.map((site) => (
              <SiteCard key={site.id} site={site} />
            ))}
          </div>
        )}
      </section>

      {/* Section sites visités */}
      <section className="mb-10">
        <h2 className="font-display font-700 text-2xl text-teal-950 mb-4 flex items-center gap-2">
          <span>✅</span> {t('me.visited')}
          <span className="text-teal-900/50 text-base font-400">({data.visited.length})</span>
        </h2>
        {data.visited.length === 0 ? (
          <p className="text-sm text-teal-900/60">
            {t('me.no_visited')}
          </p>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            {data.visited.map((site) => (
              <SiteCard key={site.id} site={site} />
            ))}
          </div>
        )}
      </section>

      {/* Section avis publiés par l'utilisateur */}
      <section>
        <h2 className="font-display font-700 text-2xl text-teal-950 mb-4 flex items-center gap-2">
          <span>💬</span> {t('me.my_reviews')}
          <span className="text-teal-900/50 text-base font-400">({data.reviews.length})</span>
        </h2>
        {data.reviews.length === 0 ? (
          <p className="text-sm text-teal-900/60">{t('me.no_reviews')}</p>
        ) : (
          <div className="flex flex-col gap-3">
            {data.reviews.map((review) => (
              <div key={review.id} className="bg-white rounded-xl border border-sand-200 shadow-sm p-4">
                <div className="flex justify-between items-start gap-3">
                  <Link
                    to={`/sites/${review.site.slug}`}
                    className="font-display font-700 text-teal-950 hover:text-terracotta-500 transition"
                  >
                    {review.site.name}
                  </Link>
                  <span className="text-gold-500 font-600 shrink-0">★ {review.rating}</span>
                </div>
                {review.comment && (
                  <p className="text-sm text-teal-900/80 mt-1">{review.comment}</p>
                )}
                {/* Badge « Visite vérifiée » si l'utilisateur avait marqué le site
                    comme visité avant d'écrire l'avis (voir ReviewController). */}
                {review.is_verified && (
                  <span className="inline-block mt-2 text-xs text-teal-800 bg-teal-800/10 px-2 py-0.5 rounded-full font-600">
                    {t('detail.verified_visit')}
                  </span>
                )}
              </div>
            ))}
          </div>
        )}
      </section>
    </div>
  )
}

export default MePage
