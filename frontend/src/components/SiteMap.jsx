/**
 * SiteMap — carte de l'Algérie affichant tous les sites en pins cliquables.
 *
 * Utilise react-leaflet (wrapper React de Leaflet, la lib de carte open-source).
 * Chaque pin ouvre un popup avec image, catégorie, description et bouton
 * « Voir le site » qui navigue vers la fiche.
 *
 * Le bloc « delete + mergeOptions » corrige un bug connu de Leaflet avec les
 * bundlers modernes : les URLs des icônes de pins ne se résolvent pas
 * correctement, on les recharge manuellement depuis les fichiers du package.
 */

import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet'
import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png'
import markerIcon from 'leaflet/dist/images/marker-icon.png'
import markerShadow from 'leaflet/dist/images/marker-shadow.png'

// Patch icônes par défaut (bug bundler → chemins d'images perdus)
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
})

// Centre géographique de l'Algérie (à peu près).
const ALGERIA_CENTER = [28.0, 2.0]

function SiteMap({ sites }) {
  const { t } = useTranslation()

  return (
    <MapContainer
      center={ALGERIA_CENTER}
      zoom={5} // Vue globale du pays
      scrollWheelZoom={false} // Empêche la molette de zoomer par accident
      className="h-96 w-full rounded-lg"
    >
      {/* Fond de carte OpenStreetMap (gratuit, pas de clé nécessaire) */}
      <TileLayer
        attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
      />
      {sites.map((site) => (
        <Marker key={site.id} position={[site.latitude, site.longitude]}>
          <Popup minWidth={220} maxWidth={260}>
            <div className="w-56">
              {site.image_path && (
                <div
                  className="w-full h-24 rounded-md bg-cover bg-center mb-2"
                  style={{ backgroundImage: `url(${site.image_path})` }}
                />
              )}
              <div className="font-display font-700 text-teal-950 text-base leading-tight mb-1">
                {site.name}
              </div>
              <div className="text-xs text-teal-900/70 mb-2">
                <span className="bg-terracotta-500 text-white px-1.5 py-0.5 rounded-full text-[10px] font-600 mr-1 capitalize">
                  {t(`categories.${site.category}`, site.category)}
                </span>
                {site.wilaya}
              </div>
              {site.description && (
                <p className="text-xs text-teal-900/80 line-clamp-2 mb-2">
                  {site.description}
                </p>
              )}
              <Link
                to={`/sites/${site.slug}`}
                className="inline-block text-xs bg-teal-800 hover:bg-teal-900 text-white font-600 rounded-full px-3 py-1 transition"
              >
                {t('map.view_site')}
              </Link>
            </div>
          </Popup>
        </Marker>
      ))}
    </MapContainer>
  )
}

export default SiteMap
