/**
 * ResetPasswordPage — écran /reinitialiser-mot-de-passe?token=...&email=...
 *
 * Le token et l'email arrivent en query params (voir le lien envoyé par
 * email, généré côté backend dans PasswordResetController::forgotPassword).
 * On les renvoie tels quels au backend avec le nouveau mot de passe.
 */

import { useState } from 'react'
import { useNavigate, useSearchParams, Link } from 'react-router-dom'
import { api } from '../services/api'

function ResetPasswordPage() {
  const [searchParams] = useSearchParams()
  const navigate = useNavigate()
  const token = searchParams.get('token') ?? ''
  const email = searchParams.get('email') ?? ''

  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
  const [error, setError] = useState(null)
  const [loading, setLoading] = useState(false)
  const [done, setDone] = useState(false)

  const handleSubmit = async (event) => {
    event.preventDefault()
    setError(null)
    setLoading(true)
    try {
      await api.resetPassword({
        token,
        email,
        password,
        password_confirmation: passwordConfirmation,
      })
      setDone(true)
      setTimeout(() => navigate('/login'), 2500)
    } catch (err) {
      const firstError = Object.values(err.errors ?? {})[0]?.[0]
      setError(firstError ?? err.message ?? 'Une erreur est survenue.')
    } finally {
      setLoading(false)
    }
  }

  if (!token || !email) {
    return (
      <div className="max-w-sm mx-auto p-6 mt-10">
        <p className="text-terracotta-600">Ce lien de réinitialisation est invalide.</p>
        <Link to="/mot-de-passe-oublie" className="text-sm text-terracotta-600 underline mt-2 inline-block">
          Demander un nouveau lien
        </Link>
      </div>
    )
  }

  return (
    <div className="max-w-sm mx-auto p-6 mt-10">
      <h1 className="font-display font-800 text-2xl text-teal-950 mb-6">Nouveau mot de passe</h1>

      {done ? (
        <div className="bg-white rounded-2xl shadow-md p-6 border border-sand-200">
          <p className="text-teal-900/80">
            Ton mot de passe a été mis à jour. Redirection vers la connexion...
          </p>
        </div>
      ) : (
        <form onSubmit={handleSubmit} className="flex flex-col gap-3 bg-white rounded-2xl shadow-md p-6 border border-sand-200">
          <input
            type="password"
            placeholder="Nouveau mot de passe"
            required
            value={password}
            onChange={(event) => setPassword(event.target.value)}
            className="border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
          />
          <input
            type="password"
            placeholder="Confirmer le mot de passe"
            required
            value={passwordConfirmation}
            onChange={(event) => setPasswordConfirmation(event.target.value)}
            className="border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
          />
          {error && <p className="text-terracotta-600 text-sm">{error}</p>}
          <button
            type="submit"
            disabled={loading}
            className="bg-terracotta-500 hover:bg-terracotta-600 disabled:opacity-60 text-white font-600 rounded-full px-3 py-2 transition"
          >
            {loading ? 'Enregistrement...' : 'Changer le mot de passe'}
          </button>
        </form>
      )}
    </div>
  )
}

export default ResetPasswordPage
