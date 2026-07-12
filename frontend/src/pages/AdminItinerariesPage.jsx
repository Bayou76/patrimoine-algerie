/**
 * AdminItinerariesPage — page /admin/itineraires : liste de gestion.
 * Distingue les itinéraires officiels des propositions communautaires
 * (badge + nom de l'auteur) et permet d'éditer/supprimer chacun.
 */

import { useEffect, useState } from 'react'
import { Link, Navigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { api } from '../services/api'

function AdminItinerariesPage() {
  const { user, loading } = useAuth()
  const [items, setItems] = useState([])
  const [itemsLoading, setItemsLoading] = useState(true)

  const load = () => {
    setItemsLoading(true)
    api.adminGetItineraries().then(setItems).finally(() => setItemsLoading(false))
  }

  useEffect(() => {
    if (!user?.is_admin) return
    load()
  }, [user])

  if (loading) return <p className="p-6 text-teal-900">Chargement...</p>
  if (!user) return <Navigate to="/login" replace />
  if (!user.is_admin) return <p className="p-6 text-terracotta-600">Accès réservé aux administrateurs.</p>

  const handleDelete = async (item) => {
    if (!confirm(`Supprimer définitivement « ${item.title} » ?`)) return
    try {
      await api.adminDeleteItinerary(item.id)
      load()
    } catch {
      alert('Suppression impossible.')
    }
  }

  return (
    <div className="max-w-4xl mx-auto p-6">
      <div className="flex flex-wrap justify-between items-center gap-4 mb-6">
        <div>
          <Link to="/admin" className="text-sm text-teal-900/70 hover:text-teal-950">← Retour à l'administration</Link>
          <h1 className="font-display font-800 text-3xl text-teal-950 mt-2">Itinéraires</h1>
        </div>
        <Link
          to="/admin/itineraires/nouveau"
          className="bg-terracotta-500 hover:bg-terracotta-600 text-white font-600 rounded-full px-4 py-2 transition"
        >
          + Nouvel itinéraire
        </Link>
      </div>

      {itemsLoading ? (
        <p className="text-teal-900">Chargement...</p>
      ) : (
        <div className="flex flex-col gap-2">
          {items.map((item) => (
            <div key={item.id} className="bg-white rounded-xl border border-sand-200 shadow-sm p-4 flex items-center gap-3">
              <div className="flex-1 min-w-0">
                <p className="font-display font-700 text-teal-950 truncate">{item.title}</p>
                <p className="text-xs text-teal-900/60 capitalize mb-1">
                  {item.theme} · {item.sites_count} étapes
                </p>
                {item.is_community ? (
                  <span className="inline-block text-[10px] font-600 bg-gold-400/20 text-gold-600 px-2 py-0.5 rounded-full">
                    🌍 Proposé par {item.creator_name}
                  </span>
                ) : (
                  <span className="inline-block text-[10px] font-600 bg-teal-800/10 text-teal-800 px-2 py-0.5 rounded-full">
                    Officiel Athar
                  </span>
                )}
              </div>
              <div className="flex gap-2 shrink-0">
                <Link
                  to={`/itineraires/${item.slug}`}
                  className="text-sm text-teal-900/70 hover:text-teal-950 px-2 py-1"
                >
                  👁 Voir
                </Link>
                <Link
                  to={`/admin/itineraires/${item.id}/modifier`}
                  className="text-sm bg-teal-800 hover:bg-teal-900 text-white font-600 rounded-full px-3 py-1.5 transition"
                >
                  Modifier
                </Link>
                <button
                  type="button"
                  onClick={() => handleDelete(item)}
                  className="text-sm bg-white border border-terracotta-400 hover:bg-terracotta-50 text-terracotta-600 font-600 rounded-full px-3 py-1.5 transition"
                >
                  Supprimer
                </button>
              </div>
            </div>
          ))}
          {items.length === 0 && <p className="text-sm text-teal-900/60">Aucun itinéraire.</p>}
        </div>
      )}
    </div>
  )
}

export default AdminItinerariesPage
