/**
 * LoginPage — écran de connexion.
 *
 * Petit formulaire email + mot de passe. En cas de succès, on redirige
 * vers l'accueil (/). En cas d'échec, on affiche le message d'erreur
 * renvoyé par le backend.
 */

import { useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { useAuth } from '../context/AuthContext'
import GoogleSignInButton from '../components/GoogleSignInButton'
// FacebookSignInButton retiré temporairement de l'UI (voir git blame) : la
// connexion Facebook nécessite une vérification business Meta bloquée par
// l'absence de nom de domaine personnalisé. Le composant et le backend
// restent en place, prêts à être réaffichés une fois cette étape faite.

function LoginPage() {
  const { login } = useAuth() // Fonction fournie par AuthContext
  const { t } = useTranslation()
  const navigate = useNavigate() // Pour rediriger après login réussi
  const [form, setForm] = useState({ email: '', password: '' })
  const [error, setError] = useState(null)

  const handleSubmit = async (event) => {
    event.preventDefault()
    setError(null)

    try {
      await login(form) // Stocke le token + met à jour user dans AuthContext
      navigate('/') // Redirection vers l'accueil
    } catch (err) {
      // ValidationException Laravel → err.message contient « Identifiants incorrects. ».
      setError(err.message ?? t('auth.wrong_credentials'))
    }
  }

  return (
    <div className="max-w-sm mx-auto p-6 mt-10">
      <h1 className="font-display font-800 text-2xl text-teal-950 mb-6">{t('auth.login_title')}</h1>
      <form onSubmit={handleSubmit} className="flex flex-col gap-3 bg-white rounded-2xl shadow-md p-6 border border-sand-200">
        <input
          type="email"
          placeholder={t('auth.email')}
          required // Validation HTML5 basique côté navigateur
          value={form.email}
          onChange={(event) => setForm({ ...form, email: event.target.value })}
          className="border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
        />
        <input
          type="password"
          placeholder={t('auth.password')}
          required
          value={form.password}
          onChange={(event) => setForm({ ...form, password: event.target.value })}
          className="border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
        />
        {error && <p className="text-terracotta-600 text-sm">{error}</p>}
        <button type="submit" className="bg-terracotta-500 hover:bg-terracotta-600 text-white font-600 rounded-full px-3 py-2 transition">
          {t('auth.login_button')}
        </button>
        <Link to="/mot-de-passe-oublie" className="text-sm text-teal-900/70 hover:text-teal-950 text-center">
          Mot de passe oublié ?
        </Link>
      </form>

      <div className="flex items-center gap-3 my-5">
        <div className="flex-1 h-px bg-sand-200" />
        <span className="text-xs text-teal-900/50 uppercase tracking-wide">ou</span>
        <div className="flex-1 h-px bg-sand-200" />
      </div>

      <div className="flex flex-col gap-2">
        <GoogleSignInButton onError={setError} />
      </div>
    </div>
  )
}

export default LoginPage
