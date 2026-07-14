/**
 * Client API — encapsule tous les appels HTTP vers le backend Laravel.
 *
 * Avantage : un seul endroit qui gère l'URL de base, les headers, le token
 * et le format des réponses. Les composants React n'ont qu'à appeler api.xxx()
 * sans se soucier des détails HTTP.
 */

// VITE_API_URL est défini dans le fichier .env du frontend (ex: http://localhost:8001/api).
// Vite expose les variables préfixées par VITE_ à l'app via import.meta.env.
const API_URL = import.meta.env.VITE_API_URL

// --- Gestion du token Sanctum en localStorage ---
export function getToken() {
  return localStorage.getItem('token')
}

export function setToken(token) {
  localStorage.setItem('token', token)
}

export function clearToken() {
  localStorage.removeItem('token')
}

/**
 * Fonction request : wrapper unique autour de fetch().
 * Ajoute automatiquement le token d'auth, parse le JSON, gère les erreurs.
 */
async function request(path, options = {}) {
  const headers = {
    Accept: 'application/json',
    // Content-Type ajouté seulement s'il y a un body (sinon GET est bruyant).
    ...(options.body ? { 'Content-Type': 'application/json' } : {}),
    ...options.headers,
  }

  const token = getToken()
  if (token) headers.Authorization = `Bearer ${token}`

  const response = await fetch(`${API_URL}${path}`, {
    ...options,
    headers,
    // On sérialise nous-mêmes le body en JSON (fetch ne le fait pas).
    body: options.body ? JSON.stringify(options.body) : undefined,
  })

  // 204 No Content : succès sans body (ex: DELETE réussi).
  if (response.status === 204) return null

  // catch(() => null) : si la réponse n'est pas du JSON, on n'échoue pas ici.
  const data = await response.json().catch(() => null)

  if (!response.ok) {
    // On lance une erreur structurée que les composants peuvent inspecter :
    // - err.status : code HTTP
    // - err.errors : erreurs de validation Laravel (par champ)
    // - err.message : message principal
    throw { status: response.status, errors: data?.errors, message: data?.message }
  }

  return data
}

// --- Objet api : catalogue de toutes les routes du backend ---
// Chaque fonction correspond à un endpoint de routes/api.php côté Laravel.
export const api = {
  // Public — sites
  getSites: (params = {}) => request(`/sites?${new URLSearchParams(params)}`),
  getSite: (slug) => request(`/sites/${slug}`),

  // Public — auth
  register: (payload) => request('/register', { method: 'POST', body: payload }),
  login: (payload) => request('/login', { method: 'POST', body: payload }),
  loginWithGoogle: (credential) => request('/auth/google', { method: 'POST', body: { credential } }),
  loginWithFacebook: (accessToken) =>
    request('/auth/facebook', { method: 'POST', body: { access_token: accessToken } }),
  logout: () => request('/logout', { method: 'POST' }),
  getCurrentUser: () => request('/user'),
  forgotPassword: (email) => request('/forgot-password', { method: 'POST', body: { email } }),
  resetPassword: (payload) => request('/reset-password', { method: 'POST', body: payload }),

  // Connecté — reviews et interactions (favoris/visité)
  createReview: (siteId, payload) =>
    request(`/sites/${siteId}/reviews`, { method: 'POST', body: payload }),
  deleteReview: (reviewId) => request(`/reviews/${reviewId}`, { method: 'DELETE' }),
  toggleInteraction: (siteId, type) =>
    request(`/sites/${siteId}/interactions/${type}`, { method: 'POST' }),
  getMe: () => request('/me'),

  // Public — chronologie globale
  getGlobalTimeline: (lang) => request(`/timeline?lang=${lang}`),

  // Public — itinéraires
  getItineraries: (lang) => request(`/itineraries?lang=${lang}`),
  getItinerary: (slug, lang) => request(`/itineraries/${slug}?lang=${lang}`),
  // Connecté — proposer un itinéraire (publié tout de suite, tagué communauté)
  proposeItinerary: (payload) => request('/itineraries', { method: 'POST', body: payload }),

  // Connecté — « Mon voyage » (itinéraire personnel privé)
  getMyTrip: (lang) => request(`/my-trip?lang=${lang}`),
  addToMyTrip: (siteId) => request('/my-trip', { method: 'POST', body: { site_id: siteId } }),
  reorderMyTrip: (siteIds) => request('/my-trip/reorder', { method: 'PUT', body: { site_ids: siteIds } }),
  updateMyTripNote: (siteId, note) => request(`/my-trip/${siteId}`, { method: 'PUT', body: { note } }),
  removeFromMyTrip: (siteId) => request(`/my-trip/${siteId}`, { method: 'DELETE' }),

  // Admin — CRUD complet + stats du dashboard
  adminGetStats: () => request('/admin/stats'),
  adminGetSite: (siteId) => request(`/admin/sites/${siteId}`),
  adminCreateSite: (payload) => request('/admin/sites', { method: 'POST', body: payload }),
  adminUpdateSite: (siteId, payload) =>
    request(`/admin/sites/${siteId}`, { method: 'PUT', body: payload }),
  adminDeleteSite: (siteId) =>
    request(`/admin/sites/${siteId}`, { method: 'DELETE' }),

  // Admin — CRUD itinéraires
  adminGetItineraries: () => request('/admin/itineraries'),
  adminGetItinerary: (id) => request(`/admin/itineraries/${id}`),
  adminCreateItinerary: (payload) => request('/admin/itineraries', { method: 'POST', body: payload }),
  adminUpdateItinerary: (id, payload) =>
    request(`/admin/itineraries/${id}`, { method: 'PUT', body: payload }),
  adminDeleteItinerary: (id) =>
    request(`/admin/itineraries/${id}`, { method: 'DELETE' }),

  // Admin — gestion des utilisateurs
  adminGetUsers: () => request('/admin/users'),
  adminUpdateUser: (id, payload) =>
    request(`/admin/users/${id}`, { method: 'PUT', body: payload }),
  adminDeleteUser: (id) =>
    request(`/admin/users/${id}`, { method: 'DELETE' }),
}
