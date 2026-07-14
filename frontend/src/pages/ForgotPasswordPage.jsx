/**
 * ForgotPasswordPage — écran /mot-de-passe-oublie.
 *
 * Formulaire à un seul champ (email). Le backend renvoie toujours un message
 * de succès générique (même si l'email n'existe pas), donc on affiche
 * simplement ce message sans jamais révéler si le compte existe.
 */

import { useState } from 'react'
import { Link } from 'react-router-dom'
import { api } from '../services/api'

function ForgotPasswordPage() {
  const [email, setEmail] = useState('')
  const [sent, setSent] = useState(false)
  const [error, setError] = useState(null)
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (event) => {
    event.preventDefault()
    setError(null)
    setLoading(true)
    try {
      await api.forgotPassword(email)
      setSent(true)
    } catch (err) {
      setError(err.message ?? 'Une erreur est survenue.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="max-w-sm mx-auto p-6 mt-10">
      <h1 className="font-display font-800 text-2xl text-teal-950 mb-6">Mot de passe oublié</h1>

      {sent ? (
        <div className="bg-white rounded-2xl shadow-md p-6 border border-sand-200">
          <p className="text-teal-900/80 leading-relaxed">
            Si un compte existe avec cette adresse, un email contenant un lien de réinitialisation vient
            d'être envoyé. Pense à vérifier tes spams si tu ne le vois pas rapidement.
          </p>
          <Link to="/login" className="inline-block mt-4 text-sm text-terracotta-600 underline">
            Retour à la connexion
          </Link>
        </div>
      ) : (
        <form onSubmit={handleSubmit} className="flex flex-col gap-3 bg-white rounded-2xl shadow-md p-6 border border-sand-200">
          <p className="text-sm text-teal-900/70 mb-1">
            Indique l'adresse email de ton compte : on t'enverra un lien pour choisir un nouveau mot de
            passe.
          </p>
          <input
            type="email"
            placeholder="Email"
            required
            value={email}
            onChange={(event) => setEmail(event.target.value)}
            className="border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
          />
          {error && <p className="text-terracotta-600 text-sm">{error}</p>}
          <button
            type="submit"
            disabled={loading}
            className="bg-terracotta-500 hover:bg-terracotta-600 disabled:opacity-60 text-white font-600 rounded-full px-3 py-2 transition"
          >
            {loading ? 'Envoi...' : 'Envoyer le lien'}
          </button>
          <Link to="/login" className="text-sm text-teal-900/70 hover:text-teal-950 text-center mt-1">
            Retour à la connexion
          </Link>
        </form>
      )}
    </div>
  )
}

export default ForgotPasswordPage
