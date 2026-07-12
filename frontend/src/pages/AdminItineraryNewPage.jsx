/**
 * AdminItineraryNewPage — page /admin/itineraires/nouveau : création
 * d'un itinéraire officiel (created_by_user_id reste null côté backend).
 */

import { useEffect, useState } from 'react'
import { Link, Navigate, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { api } from '../services/api'
import ItineraryForm from '../components/ItineraryForm'

function AdminItineraryNewPage() {
  const { user, loading } = useAuth()
  const navigate = useNavigate()
  const [allSites, setAllSites] = useState([])

  useEffect(() => {
    api.getSites({ lang: 'fr' }).then(setAllSites)
  }, [])

  if (loading) return <p className="p-6 text-teal-900">Chargement...</p>
  if (!user) return <Navigate to="/login" replace />
  if (!user.is_admin) return <Navigate to="/" replace />

  const handleSubmit = async (payload) => {
    const res = await api.adminCreateItinerary(payload)
    navigate(`/itineraires/${res.slug}`)
  }

  return (
    <div className="max-w-4xl mx-auto p-6">
      <Link to="/admin/itineraires" className="text-sm text-teal-900/70 hover:text-teal-950">
        ← Retour aux itinéraires
      </Link>
      <h1 className="font-display font-800 text-3xl text-teal-950 mt-2 mb-6">Nouvel itinéraire</h1>
      <ItineraryForm allSites={allSites} onSubmit={handleSubmit} submitLabel="Créer l'itinéraire" />
    </div>
  )
}

export default AdminItineraryNewPage
