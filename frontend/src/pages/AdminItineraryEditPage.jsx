/**
 * AdminItineraryEditPage — page /admin/itineraires/:id/modifier.
 * L'admin peut éditer aussi bien les itinéraires officiels que les
 * propositions communautaires (utile pour corriger une coquille avant
 * de mettre en avant une bonne proposition).
 */

import { useEffect, useState } from 'react'
import { Link, Navigate, useNavigate, useParams } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { api } from '../services/api'
import ItineraryForm from '../components/ItineraryForm'

function AdminItineraryEditPage() {
  const { id } = useParams()
  const { user, loading } = useAuth()
  const navigate = useNavigate()
  const [initial, setInitial] = useState(null)
  const [allSites, setAllSites] = useState([])
  const [error, setError] = useState(null)

  useEffect(() => {
    if (!user?.is_admin) return
    api.adminGetItinerary(id).then(setInitial).catch(() => setError('Itinéraire introuvable.'))
    api.getSites({ lang: 'fr' }).then(setAllSites)
  }, [user, id])

  if (loading) return <p className="p-6 text-teal-900">Chargement...</p>
  if (!user) return <Navigate to="/login" replace />
  if (!user.is_admin) return <Navigate to="/" replace />
  if (error) return <p className="p-6 text-terracotta-600">{error}</p>
  if (!initial) return <p className="p-6 text-teal-900">Chargement...</p>

  const handleSubmit = async (payload) => {
    const res = await api.adminUpdateItinerary(id, payload)
    navigate(`/itineraires/${res.slug}`)
  }

  return (
    <div className="max-w-4xl mx-auto p-6">
      <Link to="/admin/itineraires" className="text-sm text-teal-900/70 hover:text-teal-950">
        ← Retour aux itinéraires
      </Link>
      <h1 className="font-display font-800 text-3xl text-teal-950 mt-2 mb-6">
        Modifier « {initial.translations?.fr?.title ?? initial.slug} »
      </h1>
      <ItineraryForm initial={initial} allSites={allSites} onSubmit={handleSubmit} submitLabel="Enregistrer" />
    </div>
  )
}

export default AdminItineraryEditPage
