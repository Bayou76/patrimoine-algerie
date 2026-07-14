/**
 * GoogleSignInButton — bouton « Se connecter avec Google ».
 *
 * Utilise Google Identity Services (script chargé dans index.html), qui gère
 * lui-même la popup/redirection Google et nous renvoie un `credential` (un
 * ID Token JWT). On l'envoie tel quel à /api/auth/google — c'est le backend
 * qui le fait valider par Google, jamais le frontend.
 */

import { useEffect, useRef } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

function GoogleSignInButton({ onError }) {
  const { loginWithGoogle } = useAuth()
  const navigate = useNavigate()
  const buttonRef = useRef(null)

  // loginWithGoogle/navigate/onError sont recréés à chaque rendu (AuthProvider
  // notamment redéfinit ses fonctions à chaque render) : si on les mettait dans
  // le tableau de dépendances de l'effet ci-dessous, google.accounts.id.initialize()
  // serait réappelé en boucle à chaque rendu, ce qui saturait le navigateur.
  // On passe donc par des refs, mises à jour à chaque rendu mais lues uniquement
  // au moment du clic (jamais utilisées comme dépendances d'effet).
  const loginWithGoogleRef = useRef(loginWithGoogle)
  loginWithGoogleRef.current = loginWithGoogle
  const navigateRef = useRef(navigate)
  navigateRef.current = navigate
  const onErrorRef = useRef(onError)
  onErrorRef.current = onError

  useEffect(() => {
    // Le script Google est chargé en `async defer` dans index.html : il peut
    // ne pas être prêt au premier rendu, on réessaie brièvement s'il manque.
    let cancelled = false

    const render = () => {
      if (cancelled) return
      if (!window.google?.accounts?.id) {
        setTimeout(render, 100)
        return
      }

      window.google.accounts.id.initialize({
        client_id: import.meta.env.VITE_GOOGLE_CLIENT_ID,
        callback: async ({ credential }) => {
          try {
            await loginWithGoogleRef.current(credential)
            navigateRef.current('/')
          } catch {
            onErrorRef.current?.('Connexion Google impossible.')
          }
        },
      })

      if (buttonRef.current) {
        window.google.accounts.id.renderButton(buttonRef.current, {
          type: 'standard',
          theme: 'outline',
          size: 'large',
          shape: 'pill',
          width: 320,
        })
      }
    }

    render()
    return () => {
      cancelled = true
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps -- volontaire, voir commentaire ci-dessus.
  }, [])

  return <div ref={buttonRef} className="flex justify-center" />
}

export default GoogleSignInButton
