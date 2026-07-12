/**
 * DirectionsButton — bouton 🧭 « Y aller » sur la fiche d'un site.
 *
 * Ouvre Google Maps directement en mode itinéraire vers les coordonnées GPS
 * du site. Pas d'API key nécessaire : on utilise l'URL publique.
 */

import { useTranslation } from 'react-i18next'

function DirectionsButton({ latitude, longitude, name }) {
  const { t } = useTranslation()

  // Sécurité : si les coords sont absentes, on n'affiche rien plutôt qu'un
  // bouton cassé.
  if (latitude == null || longitude == null) return null

  // Format Google Maps directions URL API :
  //   /maps/dir/?api=1&destination=LAT,LNG
  // travelmode=driving (défaut) + dir_action=navigate ouvre en mode navigation
  // sur mobile (utile pour un touriste en voiture).
  const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}${
    name ? `&destination_place_id=&travelmode=driving&dir_action=navigate` : ''
  }`

  return (
    <a
      href={googleMapsUrl}
      target="_blank"
      rel="noopener noreferrer" // sécurité : pas d'accès au window.opener
      className="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-600 border border-sand-200 bg-white text-teal-950 hover:border-terracotta-400 shadow-sm transition-all"
      title={t('detail.open_in_maps_hint')}
    >
      <span className="text-lg">🧭</span>
      {t('detail.get_directions')}
    </a>
  )
}

export default DirectionsButton
