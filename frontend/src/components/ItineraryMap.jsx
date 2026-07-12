/**
 * ItineraryMap — carte des étapes d'un itinéraire, reliées par une ligne.
 *
 * Différence avec SiteMap :
 *   - Icônes personnalisées : cercles terracotta numérotés (1, 2, 3...) au
 *     lieu des pins Leaflet par défaut. Fait via L.divIcon (HTML brut).
 *   - Polyline en pointillés qui relie les étapes dans l'ordre.
 *   - FitBounds : recentre + zoome automatiquement pour englober toutes les
 *     étapes (peu importe leur dispersion : Alger 1 jour vs Grand Sud).
 */

import { MapContainer, TileLayer, Marker, Polyline, Popup, useMap } from 'react-leaflet'
import { useEffect } from 'react'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

/**
 * Crée une icône Leaflet HTML custom : un cercle terracotta avec un chiffre.
 * L.divIcon accepte du HTML brut, ce qui permet d'utiliser CSS et emojis
 * sans avoir à générer une image PNG.
 */
function numberedIcon(n) {
  return L.divIcon({
    className: 'itinerary-pin',
    html: `<div style="
      width: 34px; height: 34px;
      border-radius: 50%;
      background: #c1502e;
      color: white;
      font: 700 15px/34px system-ui, sans-serif;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.35), 0 0 0 4px #fbf7f0;
    ">${n}</div>`,
    iconSize: [34, 34],
    iconAnchor: [17, 17], // Point d'ancrage au centre de l'icône
    popupAnchor: [0, -18],
  })
}

/**
 * Composant utilitaire qui zoome automatiquement pour montrer toutes les
 * étapes. Doit être un composant enfant de MapContainer pour accéder à useMap().
 */
function FitBounds({ points }) {
  const map = useMap()
  useEffect(() => {
    if (!points.length) return
    // latLngBounds calcule le rectangle géographique englobant tous les points.
    const bounds = L.latLngBounds(points)
    // padding évite que les pins collent aux bords de la carte.
    map.fitBounds(bounds, { padding: [40, 40] })
  }, [map, points])
  return null // Ce composant ne rend rien lui-même, il pilote juste la carte.
}

function ItineraryMap({ sites }) {
  // Sécurité : on filtre les étapes sans coordonnées valides.
  const points = sites
    .filter((s) => s.latitude != null && s.longitude != null)
    .map((s) => [Number(s.latitude), Number(s.longitude)])

  if (points.length === 0) return null

  return (
    <MapContainer
      center={points[0]}
      zoom={6}
      scrollWheelZoom={false}
      className="h-80 sm:h-96 w-full rounded-2xl"
    >
      <TileLayer
        attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
      />
      {/* Trait en pointillés terracotta qui relie les étapes dans l'ordre. */}
      <Polyline
        positions={points}
        pathOptions={{ color: '#c1502e', weight: 3, opacity: 0.8, dashArray: '6 8' }}
      />
      {sites.map((site, idx) => (
        <Marker
          key={site.id}
          position={[site.latitude, site.longitude]}
          icon={numberedIcon(idx + 1)}
        >
          <Popup>
            <div className="font-700 text-teal-950">{site.name}</div>
            {site.day_label && (
              <div className="text-xs text-terracotta-600 font-600 mt-0.5">{site.day_label}</div>
            )}
            <div className="text-xs text-teal-900/60 mt-0.5">{site.wilaya}</div>
          </Popup>
        </Marker>
      ))}
      <FitBounds points={points} />
    </MapContainer>
  )
}

export default ItineraryMap
