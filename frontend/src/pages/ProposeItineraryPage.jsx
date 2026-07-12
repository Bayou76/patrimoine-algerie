/**
 * ProposeItineraryPage — page /itineraires/proposer.
 * Accessible à tout utilisateur connecté (pas besoin d'être admin).
 * Publié immédiatement à la validation, tagué « proposé par la communauté »
 * (voir ItinerariesPage / ItineraryDetailPage pour l'affichage du badge).
 */

import { useEffect, useState } from 'react'
import { Link, Navigate, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { api } from '../services/api'
import ItineraryForm from '../components/ItineraryForm'

function ProposeItineraryPage() {
  const { user, loading } = useAuth()
  const navigate = useNavigate()
  const [allSites, setAllSites] = useState([])

  useEffect(() => {
    api.getSites({ lang: 'fr' }).then(setAllSites)
  }, [])

  if (loading) return <p className="p-6 text-teal-900">Chargement...</p>
  if (!user) return <Navigate to="/login" replace />

  const handleSubmit = async (payload) => {
    const res = await api.proposeItinerary(payload)
    navigate(`/itineraires/${res.slug}`)
  }

  return (
    <div className="max-w-4xl mx-auto p-6">
      <Link to="/itineraires" className="text-sm text-teal-900/70 hover:text-teal-950">
        ← Retour aux itinéraires
      </Link>
      <h1 className="font-display font-800 text-3xl text-teal-950 mt-2 mb-2">
        🌍 Proposer un itinéraire
      </h1>
      <p className="text-sm text-teal-900/70 mb-6">
        Partage ton parcours préféré avec la communauté Athar. Il sera publié
        immédiatement avec la mention « Proposé par {user.name} ».
      </p>
      <ItineraryForm allSites={allSites} onSubmit={handleSubmit} submitLabel="Publier mon itinéraire" />
    </div>
  )
}

export default ProposeItineraryPage
