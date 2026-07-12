/**
 * MyTripPage — page /mon-voyage : itinéraire personnel et privé, construit
 * par l'utilisateur à partir de ses favoris. Contrairement aux itinéraires
 * publics (officiels ou proposés par la communauté), ce voyage n'est visible
 * que par son propriétaire.
 */

import { useEffect, useState } from 'react'
import { Link, Navigate } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { useAuth } from '../context/AuthContext'
import { useLanguage } from '../context/LanguageContext'
import { api } from '../services/api'
import ItineraryMap from '../components/ItineraryMap'
import Reveal from '../components/Reveal'

function MyTripPage() {
  const { user, loading } = useAuth()
  const { language } = useLanguage()
  const { t } = useTranslation()
  const [trip, setTrip] = useState(null)
  const [favorites, setFavorites] = useState([])
  const [siteToAdd, setSiteToAdd] = useState('')
  const [busy, setBusy] = useState(false)

  const load = () => {
    api.getMyTrip(language).then(setTrip)
    api.getMe().then((data) => setFavorites(data.favorites))
  }

  useEffect(() => {
    if (!user) return
    load()
  }, [user, language])

  if (loading) return <p className="p-6 text-teal-900">{t('me.loading')}</p>
  if (!user) return <Navigate to="/login" replace />
  if (!trip) return <p className="p-6 text-teal-900">{t('me.loading')}</p>

  const availableFavorites = favorites.filter((f) => !trip.some((s) => s.id === f.id))

  const handleAdd = async () => {
    if (!siteToAdd) return
    setBusy(true)
    try {
      await api.addToMyTrip(Number(siteToAdd))
      setSiteToAdd('')
      load()
    } finally {
      setBusy(false)
    }
  }

  const handleRemove = async (siteId) => {
    setBusy(true)
    try {
      await api.removeFromMyTrip(siteId)
      load()
    } finally {
      setBusy(false)
    }
  }

  const handleMove = async (index, direction) => {
    const target = index + direction
    if (target < 0 || target >= trip.length) return
    const next = [...trip]
    ;[next[index], next[target]] = [next[target], next[index]]
    setTrip(next) // maj optimiste, plus réactif à l'oeil
    await api.reorderMyTrip(next.map((s) => s.id))
  }

  const handleNoteChange = (siteId, note) => {
    setTrip((prev) => prev.map((s) => (s.id === siteId ? { ...s, note } : s)))
  }

  const handleNoteBlur = async (siteId, note) => {
    await api.updateMyTripNote(siteId, note)
  }

  return (
    <div>
      <div className="relative bg-teal-950 px-6 py-20 text-center">
        <div className="absolute inset-0 bg-gradient-to-b from-teal-950/40 to-teal-950" />
        <div className="relative">
          <h1 className="font-display font-800 text-4xl sm:text-5xl text-white tracking-tight">
            🧭 {t('my_trip.title')}
          </h1>
          <p className="text-sand-100/80 mt-3 max-w-2xl mx-auto text-lg">
            {t('my_trip.subtitle')}
          </p>
        </div>
      </div>

      <div className="max-w-3xl mx-auto p-6">
        {/* Ajout depuis les favoris */}
        <div className="bg-white rounded-2xl border border-sand-200 shadow-sm p-4 mb-8">
          <h2 className="font-display font-700 text-teal-950 mb-3">{t('my_trip.add_from_favorites')}</h2>
          {availableFavorites.length === 0 ? (
            <p className="text-sm text-teal-900/60">
              {favorites.length === 0 ? t('my_trip.no_favorites_yet') : t('my_trip.all_favorites_added')}
            </p>
          ) : (
            <div className="flex gap-2">
              <select
                value={siteToAdd}
                onChange={(e) => setSiteToAdd(e.target.value)}
                className="flex-1 border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
              >
                <option value="">{t('my_trip.choose_site')}</option>
                {availableFavorites.map((f) => (
                  <option key={f.id} value={f.id}>{f.name} — {f.wilaya}</option>
                ))}
              </select>
              <button
                type="button"
                onClick={handleAdd}
                disabled={!siteToAdd || busy}
                className="bg-terracotta-500 hover:bg-terracotta-600 text-white font-600 px-4 py-2 rounded-lg transition disabled:opacity-50"
              >
                + {t('my_trip.add')}
              </button>
            </div>
          )}
        </div>

        {trip.length === 0 ? (
          <p className="text-sm text-teal-900/60">{t('my_trip.empty')}</p>
        ) : (
          <>
            <Reveal>
              <div className="relative isolate z-0 mb-8 rounded-2xl overflow-hidden shadow-lg border border-sand-200">
                <ItineraryMap sites={trip} />
              </div>
            </Reveal>

            <div className="flex flex-col gap-3">
              {trip.map((stop, idx) => (
                <Reveal key={stop.id} delay={Math.min(idx * 40, 240)}>
                  <div className="bg-white rounded-xl border border-sand-200 shadow-sm p-3 flex gap-3">
                    <div className="flex flex-col gap-1 shrink-0">
                      <button type="button" onClick={() => handleMove(idx, -1)} disabled={idx === 0} className="text-xs px-2 py-1 border border-sand-200 rounded disabled:opacity-30">↑</button>
                      <button type="button" onClick={() => handleMove(idx, 1)} disabled={idx === trip.length - 1} className="text-xs px-2 py-1 border border-sand-200 rounded disabled:opacity-30">↓</button>
                    </div>
                    <Link
                      to={`/sites/${stop.slug}`}
                      className="w-20 h-20 rounded-lg bg-cover bg-center bg-sand-200 shrink-0"
                      style={stop.image_path ? { backgroundImage: `url(${stop.image_path})` } : undefined}
                    />
                    <div className="flex-1 min-w-0">
                      <div className="flex items-start justify-between gap-2">
                        <div>
                          <Link to={`/sites/${stop.slug}`} className="font-display font-700 text-teal-950 hover:text-terracotta-500 transition">
                            {idx + 1}. {stop.name}
                          </Link>
                          <p className="text-xs text-teal-900/60">{stop.wilaya}</p>
                        </div>
                        <button
                          type="button"
                          onClick={() => handleRemove(stop.id)}
                          className="text-xs text-terracotta-600 hover:text-terracotta-700 font-600 shrink-0"
                        >
                          {t('my_trip.remove')}
                        </button>
                      </div>
                      <input
                        type="text"
                        value={stop.note ?? ''}
                        onChange={(e) => handleNoteChange(stop.id, e.target.value)}
                        onBlur={(e) => handleNoteBlur(stop.id, e.target.value)}
                        placeholder={t('my_trip.note_placeholder')}
                        className="w-full mt-2 text-sm border border-sand-200 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
                      />
                    </div>
                  </div>
                </Reveal>
              ))}
            </div>
          </>
        )}
      </div>
    </div>
  )
}

export default MyTripPage
