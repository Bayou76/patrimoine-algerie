/**
 * AffiliateButtons — boutons optionnels « Réserver une activité » / « Voir
 * les hôtels à proximité », affichés uniquement si le site a un lien
 * d'affiliation renseigné (GetYourGuide / Booking.com) côté admin.
 *
 * rel="sponsored" : attribut recommandé par Google pour les liens affiliés
 * (transmet l'info que c'est un lien commercial, sans nuire au référencement
 * du site — contrairement à ne rien mettre du tout).
 */

import { useTranslation } from 'react-i18next'

function AffiliateButtons({ activityUrl, hotelUrl }) {
  const { t } = useTranslation()

  if (!activityUrl && !hotelUrl) return null

  return (
    <>
      {activityUrl && (
        <a
          href={activityUrl}
          target="_blank"
          rel="noopener noreferrer sponsored"
          className="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-600 border border-sand-200 bg-white text-teal-950 hover:border-terracotta-400 shadow-sm transition-all"
        >
          <span className="text-lg">🎟️</span>
          {t('detail.book_activity')}
        </a>
      )}
      {hotelUrl && (
        <a
          href={hotelUrl}
          target="_blank"
          rel="noopener noreferrer sponsored"
          className="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-600 border border-sand-200 bg-white text-teal-950 hover:border-terracotta-400 shadow-sm transition-all"
        >
          <span className="text-lg">🏨</span>
          {t('detail.book_hotel')}
        </a>
      )}
    </>
  )
}

export default AffiliateButtons
