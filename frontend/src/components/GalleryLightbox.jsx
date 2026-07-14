/**
 * GalleryLightbox — visionneuse plein écran pour une galerie d'images.
 *
 * Ouverte en cliquant sur une vignette (voir SiteDetailPage), avec navigation
 * précédent/suivant (clic, flèches clavier) et fermeture (croix, Échap, clic
 * sur le fond).
 */

import { useEffect } from 'react'
import { useTranslation } from 'react-i18next'
import { getWikimediaCreditUrl } from '../utils/wikimediaCredit'

function GalleryLightbox({ images, index, onClose, onNavigate }) {
  const { t } = useTranslation()
  const image = images[index]
  const creditUrl = image ? getWikimediaCreditUrl(image.path) : null

  // Navigation clavier : flèches gauche/droite + Échap pour fermer.
  useEffect(() => {
    const handleKeyDown = (event) => {
      if (event.key === 'Escape') onClose()
      if (event.key === 'ArrowRight') onNavigate((index + 1) % images.length)
      if (event.key === 'ArrowLeft') onNavigate((index - 1 + images.length) % images.length)
    }
    window.addEventListener('keydown', handleKeyDown)
    return () => window.removeEventListener('keydown', handleKeyDown)
  }, [index, images.length, onClose, onNavigate])

  if (!image) return null

  return (
    <div
      className="fixed inset-0 z-50 bg-black/90 flex items-center justify-center"
      onClick={onClose}
    >
      <button
        type="button"
        onClick={onClose}
        aria-label="Fermer"
        className="absolute top-4 right-4 text-white/80 hover:text-white text-4xl leading-none w-10 h-10 flex items-center justify-center"
      >
        &times;
      </button>

      {images.length > 1 && (
        <button
          type="button"
          onClick={(event) => {
            event.stopPropagation()
            onNavigate((index - 1 + images.length) % images.length)
          }}
          aria-label="Image précédente"
          className="absolute left-2 sm:left-6 text-white/80 hover:text-white text-5xl leading-none w-12 h-12 flex items-center justify-center"
        >
          &#8249;
        </button>
      )}

      <figure
        className="max-w-[90vw] max-h-[85vh] flex flex-col items-center"
        onClick={(event) => event.stopPropagation()}
      >
        <img
          src={image.path}
          alt={image.caption ?? ''}
          className="max-w-full max-h-[75vh] object-contain rounded-lg"
        />
        {image.caption && (
          <figcaption className="text-white/80 text-sm mt-3 text-center">{image.caption}</figcaption>
        )}
        <div className="flex items-center gap-3 mt-2">
          {images.length > 1 && (
            <p className="text-white/50 text-xs">{index + 1} / {images.length}</p>
          )}
          {creditUrl && (
            <a
              href={creditUrl}
              target="_blank"
              rel="noopener noreferrer nofollow"
              onClick={(event) => event.stopPropagation()}
              className="text-white/50 hover:text-white text-xs underline underline-offset-2"
            >
              {t('detail.photo_credit')}
            </a>
          )}
        </div>
      </figure>

      {images.length > 1 && (
        <button
          type="button"
          onClick={(event) => {
            event.stopPropagation()
            onNavigate((index + 1) % images.length)
          }}
          aria-label="Image suivante"
          className="absolute right-2 sm:right-6 text-white/80 hover:text-white text-5xl leading-none w-12 h-12 flex items-center justify-center"
        >
          &#8250;
        </button>
      )}
    </div>
  )
}

export default GalleryLightbox
