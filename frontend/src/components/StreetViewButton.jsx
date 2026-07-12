/**
 * StreetViewButton — bouton 🌐 « Vue 360° » sur la fiche d'un site.
 *
 * Ouvre Google Street View sur les coordonnées du site. C'est notre
 * alternative gratuite à un vrai visualiseur 360° maison, qui demanderait
 * de produire les photos panoramiques + une lib comme Panellum.
 * Fonctionne pour tous les sites algériens couverts par Street View
 * (Casbah, Tipaza, Timgad, mosquées, etc.).
 */

import { useTranslation } from 'react-i18next'

function StreetViewButton({ latitude, longitude }) {
  const { t } = useTranslation()

  if (latitude == null || longitude == null) return null

  // URL Google Maps :
  //   map_action=pano ouvre le mode Street View
  //   viewpoint=LAT,LNG centre la caméra sur le point
  const url = `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${latitude},${longitude}`

  return (
    <a
      href={url}
      target="_blank"
      rel="noopener noreferrer"
      className="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-600 border border-sand-200 bg-white text-teal-950 hover:border-terracotta-400 shadow-sm transition-all"
      title={t('detail.open_streetview_hint')}
    >
      <span className="text-lg">🌐</span>
      {t('detail.view_360')}
    </a>
  )
}

export default StreetViewButton
