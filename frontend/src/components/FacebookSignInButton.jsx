/**
 * FacebookSignInButton — bouton « Se connecter avec Facebook ».
 *
 * Contrairement à Google (bouton officiel rendu par leur script), Facebook
 * ne fournit pas de bouton stylé équivalent facilement intégrable : on garde
 * donc notre propre bouton, et FB.login() ouvre la popup Facebook au clic.
 */

import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { loadFacebookSdk } from '../utils/facebookSdk'

function FacebookSignInButton({ onError }) {
  const { loginWithFacebook } = useAuth()
  const navigate = useNavigate()
  const [loading, setLoading] = useState(false)

  const handleClick = async () => {
    setLoading(true)
    try {
      const FB = await loadFacebookSdk()

      // Le SDK Facebook vérifie que ce callback est bien une fonction "normale"
      // et refuse une fonction async directement (erreur "Expression is of
      // type asyncfunction, not function") : la logique async est donc
      // déplacée dans une fonction séparée, appelée depuis un callback sync.
      const handleLoginResponse = (response) => {
        if (response.authResponse?.accessToken) {
          loginWithFacebook(response.authResponse.accessToken)
            .then(() => navigate('/'))
            .catch((e) => {
              console.error('[FB.login] échec de /api/auth/facebook :', e)
              onError?.('Connexion Facebook impossible.')
            })
            .finally(() => setLoading(false))
        } else {
          onError?.('Connexion Facebook annulée.')
          setLoading(false)
        }
      }

      FB.login(handleLoginResponse, { scope: 'email,public_profile' })
    } catch (e) {
      console.error('[FB.login] exception avant ouverture de la popup :', e)
      onError?.('Connexion Facebook impossible.')
      setLoading(false)
    }
  }

  return (
    <button
      type="button"
      onClick={handleClick}
      disabled={loading}
      className="w-full flex items-center justify-center gap-2 bg-[#1877F2] hover:bg-[#166FE5] disabled:opacity-60 text-white font-600 rounded-full px-4 py-2.5 transition"
    >
      <svg viewBox="0 0 24 24" className="w-5 h-5 fill-current" aria-hidden="true">
        <path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.91h2.54V9.85c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.44 2.91h-2.34V22c4.78-.79 8.44-4.94 8.44-9.94Z" />
      </svg>
      Se connecter avec Facebook
    </button>
  )
}

export default FacebookSignInButton
