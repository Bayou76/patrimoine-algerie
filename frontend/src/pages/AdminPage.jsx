/**
 * AdminPage — page /admin : dashboard des administrateurs.
 *
 * Affiche :
 *   1. 3 grosses tuiles avec les totaux (favoris, visites, avis) tous sites confondus
 *   2. Sélecteur de tri (nom, favoris, visites, avis, note moyenne)
 *   3. Liste de chaque site avec ses stats + actions (voir, éditer, supprimer)
 *
 * Sécurité :
 *   - Si l'utilisateur n'est pas connecté → redirection /login
 *   - Si l'utilisateur n'est pas admin → message d'accès refusé
 */

import { useEffect, useMemo, useState } from 'react'
import { Link, Navigate } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { api } from '../services/api'
import { useAuth } from '../context/AuthContext'

/** Petit badge de statistique (icône + valeur) avec un ton coloré. */
function StatBadge({ icon, value, tone }) {
  const toneClasses = {
    favorite: 'bg-terracotta-50 text-terracotta-600',
    visited: 'bg-teal-800/10 text-teal-800',
    reviews: 'bg-sand-100 text-teal-950',
    rating: 'bg-gold-400/15 text-gold-500',
  }
  return (
    <span
      className={`inline-flex items-center gap-1 text-xs font-600 rounded-full px-2 py-0.5 ${toneClasses[tone] ?? ''}`}
    >
      <span>{icon}</span>
      {value}
    </span>
  )
}

function AdminPage() {
  const { user, loading } = useAuth()
  const { t } = useTranslation()
  const [sites, setSites] = useState([])
  const [sitesLoading, setSitesLoading] = useState(true)
  const [sortBy, setSortBy] = useState('name')

  const loadSites = () => {
    setSitesLoading(true)
    api
      .adminGetStats()
      .then(setSites)
      .finally(() => setSitesLoading(false))
  }

  useEffect(() => {
    // Sans être admin on ne charge pas — l'API renverrait 403.
    if (!user?.is_admin) return
    loadSites()
  }, [user])

  // Tri en mémoire selon le critère choisi. On ne mute pas `sites` (spread).
  const sortedSites = useMemo(() => {
    const arr = [...sites]
    if (sortBy === 'name') arr.sort((a, b) => a.name.localeCompare(b.name))
    if (sortBy === 'favorites') arr.sort((a, b) => b.favorites_count - a.favorites_count)
    if (sortBy === 'visited') arr.sort((a, b) => b.visited_count - a.visited_count)
    if (sortBy === 'reviews') arr.sort((a, b) => b.reviews_count - a.reviews_count)
    // Pour la note : null passe après tout le monde grâce à -1 par défaut.
    if (sortBy === 'rating')
      arr.sort((a, b) => (b.average_rating ?? -1) - (a.average_rating ?? -1))
    return arr
  }, [sites, sortBy])

  // Totaux affichés dans les tuiles du haut.
  // reduce = accumulateur qui somme tous les compteurs de chaque site.
  const totals = useMemo(() => ({
    favorites: sites.reduce((s, x) => s + x.favorites_count, 0),
    visited: sites.reduce((s, x) => s + x.visited_count, 0),
    reviews: sites.reduce((s, x) => s + x.reviews_count, 0),
  }), [sites])

  // --- Gardes de sécurité ---
  if (loading) return <p className="p-6 text-teal-900">{t('me.loading')}</p>
  if (!user) return <Navigate to="/login" replace />
  if (!user.is_admin) {
    return <p className="p-6 text-terracotta-600">{t('admin.access_denied')}</p>
  }

  const handleDelete = async (site) => {
    if (!confirm(t('admin.confirm_delete', { name: site.name }))) return
    try {
      await api.adminDeleteSite(site.id)
      loadSites() // Refresh la liste après suppression
    } catch {
      alert(t('admin.delete_failed'))
    }
  }

  return (
    <div className="max-w-5xl mx-auto p-6">
      {/* Header : titre + bouton nouveau site */}
      <div className="flex flex-wrap justify-between items-center gap-4 mb-6">
        <div>
          <h1 className="font-display font-800 text-3xl text-teal-950">{t('admin.title')}</h1>
          <p className="text-teal-900/70 text-sm mt-1">
            {t('admin.total_sites', { count: sites.length })}
          </p>
        </div>
        <div className="flex gap-2">
          <Link
            to="/admin/itineraires"
            className="bg-teal-800 hover:bg-teal-900 text-white font-600 rounded-full px-4 py-2 transition"
          >
            🗺️ Itinéraires
          </Link>
          <Link
            to="/admin/sites/nouveau"
            className="bg-terracotta-500 hover:bg-terracotta-600 text-white font-600 rounded-full px-4 py-2 transition"
          >
            {t('admin.new_site')}
          </Link>
        </div>
      </div>

      {/* Tuiles totaux globaux */}
      <div className="grid grid-cols-3 gap-3 mb-6">
        <div className="bg-white rounded-2xl border border-sand-200 shadow-sm p-4 text-center">
          <div className="text-2xl">❤️</div>
          <div className="font-display font-700 text-2xl text-teal-950 mt-1">{totals.favorites}</div>
          <div className="text-xs text-teal-900/60">{t('admin.total_favorites')}</div>
        </div>
        <div className="bg-white rounded-2xl border border-sand-200 shadow-sm p-4 text-center">
          <div className="text-2xl">✅</div>
          <div className="font-display font-700 text-2xl text-teal-950 mt-1">{totals.visited}</div>
          <div className="text-xs text-teal-900/60">{t('admin.total_visited')}</div>
        </div>
        <div className="bg-white rounded-2xl border border-sand-200 shadow-sm p-4 text-center">
          <div className="text-2xl">💬</div>
          <div className="font-display font-700 text-2xl text-teal-950 mt-1">{totals.reviews}</div>
          <div className="text-xs text-teal-900/60">{t('admin.total_reviews')}</div>
        </div>
      </div>

      {/* Sélecteur de tri */}
      <div className="flex justify-end mb-4">
        <label className="text-sm text-teal-900/70 flex items-center gap-2">
          {t('admin.sort_by')}
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value)}
            className="border border-sand-200 bg-white rounded-full px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-terracotta-400"
          >
            <option value="name">{t('admin.sort_name')}</option>
            <option value="favorites">{t('admin.sort_favorites')}</option>
            <option value="visited">{t('admin.sort_visited')}</option>
            <option value="reviews">{t('admin.sort_reviews')}</option>
            <option value="rating">{t('admin.sort_rating')}</option>
          </select>
        </label>
      </div>

      {/* Liste des sites */}
      {sitesLoading ? (
        <p className="text-teal-900">{t('admin.loading_sites')}</p>
      ) : (
        <div className="flex flex-col gap-2">
          {sortedSites.map((site) => (
            <div
              key={site.id}
              className="bg-white rounded-xl border border-sand-200 shadow-sm p-3 flex items-center gap-3"
            >
              <div
                className="w-16 h-16 rounded-lg bg-cover bg-center bg-sand-200 shrink-0"
                style={site.image_path ? { backgroundImage: `url(${site.image_path})` } : undefined}
              />
              <div className="flex-1 min-w-0">
                <p className="font-display font-700 text-teal-950 truncate">{site.name}</p>
                <p className="text-xs text-teal-900/60 capitalize mb-2">
                  {t(`categories.${site.category}`, site.category)} · {site.wilaya}
                </p>
                <div className="flex flex-wrap gap-1.5">
                  <StatBadge icon="❤️" value={site.favorites_count} tone="favorite" />
                  <StatBadge icon="✅" value={site.visited_count} tone="visited" />
                  <StatBadge icon="💬" value={site.reviews_count} tone="reviews" />
                  {site.average_rating !== null && (
                    <StatBadge icon="★" value={site.average_rating} tone="rating" />
                  )}
                </div>
              </div>
              {/* Actions : voir, éditer, supprimer */}
              <div className="flex gap-2 shrink-0">
                <Link
                  to={`/sites/${site.slug}`}
                  className="text-sm text-teal-900/70 hover:text-teal-950 px-2 py-1"
                >
                  👁 {t('admin.view')}
                </Link>
                <Link
                  to={`/admin/sites/${site.id}/modifier`}
                  className="text-sm bg-teal-800 hover:bg-teal-900 text-white font-600 rounded-full px-3 py-1.5 transition"
                >
                  {t('admin.edit')}
                </Link>
                <button
                  type="button"
                  onClick={() => handleDelete(site)}
                  className="text-sm bg-white border border-terracotta-400 hover:bg-terracotta-50 text-terracotta-600 font-600 rounded-full px-3 py-1.5 transition"
                >
                  {t('admin.delete')}
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}

export default AdminPage
