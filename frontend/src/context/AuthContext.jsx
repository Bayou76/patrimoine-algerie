/**
 * AuthContext — gère l'utilisateur connecté dans toute l'app.
 *
 * Fournit : user (objet ou null), loading (booléen pendant la vérif du token),
 * login, register, logout.
 *
 * Fonctionnement :
 *   - Au chargement, s'il existe un token en localStorage, on tente de récupérer
 *     l'utilisateur avec GET /api/user. Si ça échoue (token expiré/révoqué),
 *     on nettoie le token pour ne pas rester dans un état zombie.
 *   - Login/register stockent le token retourné par l'API et mettent à jour user.
 *   - Logout révoque le token côté serveur et le supprime en local.
 */

import { createContext, useContext, useEffect, useState } from 'react'
import { api, getToken, setToken, clearToken } from '../services/api'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true) // true tant qu'on n'a pas vérifié

  // useEffect avec [] = exécuté 1 seule fois au montage.
  useEffect(() => {
    if (!getToken()) {
      setLoading(false)
      return
    }

    api
      .getCurrentUser()
      .then(setUser)
      .catch(() => clearToken()) // Token invalide → on nettoie
      .finally(() => setLoading(false))
  }, [])

  const register = async (payload) => {
    const data = await api.register(payload)
    setToken(data.token)
    setUser(data.user)
  }

  const login = async (payload) => {
    const data = await api.login(payload)
    setToken(data.token)
    setUser(data.user)
  }

  const logout = async () => {
    // On tente de révoquer le token côté serveur mais on ne bloque pas
    // sur l'erreur : même si le serveur ne répond pas, on doit déconnecter
    // localement (sinon l'utilisateur est bloqué).
    await api.logout().catch(() => {})
    clearToken()
    setUser(null)
  }

  return (
    <AuthContext.Provider value={{ user, loading, register, login, logout }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  return useContext(AuthContext)
}
