/**
 * Analytics — petit wrapper autour de Google Analytics (gtag.js).
 *
 * Le script gtag est chargé dans index.html avec send_page_view: false,
 * car Athar est une SPA : react-router change les pages sans recharger
 * le document, donc Analytics ne verrait qu'une seule vue (le chargement
 * initial) si on ne lui envoie pas manuellement chaque changement de route.
 */

/** Envoie une vue de page à GA4. À appeler à chaque changement de route. */
export function trackPageView(path, title) {
  if (typeof window.gtag !== 'function') return
  window.gtag('event', 'page_view', {
    page_path: path,
    page_title: title,
    page_location: window.location.href,
  })
}

/** Envoie un événement personnalisé (ex: clic sur "Partager", ajout favori). */
export function trackEvent(name, params = {}) {
  if (typeof window.gtag !== 'function') return
  window.gtag('event', name, params)
}
