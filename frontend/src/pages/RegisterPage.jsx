/**
 * RegisterPage — écran d'inscription.
 *
 * Formulaire nom + email + mot de passe + confirmation. Backend valide la
 * confirmation via la règle Laravel 'confirmed' qui matche automatiquement
 * password et password_confirmation.
 */

import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { useAuth } from '../context/AuthContext'

function RegisterPage() {
  const { register } = useAuth()
  const { t } = useTranslation()
  const navigate = useNavigate()
  const [form, setForm] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '', // Nom exact attendu par Laravel
  })
  const [error, setError] = useState(null)

  const handleSubmit = async (event) => {
    event.preventDefault()
    setError(null)

    try {
      await register(form)
      navigate('/')
    } catch (err) {
      // Récupère la première erreur de validation si disponible,
      // sinon message générique. err.errors = { email: ["déjà utilisé"], ... }
      const firstError = Object.values(err.errors ?? {})[0]?.[0]
      setError(firstError ?? err.message ?? t('auth.register_failed'))
    }
  }

  return (
    <div className="max-w-sm mx-auto p-6 mt-10">
      <h1 className="font-display font-800 text-2xl text-teal-950 mb-6">{t('auth.register_title')}</h1>
      <form onSubmit={handleSubmit} className="flex flex-col gap-3 bg-white rounded-2xl shadow-md p-6 border border-sand-200">
        <input
          type="text"
          placeholder={t('auth.name')}
          required
          value={form.name}
          onChange={(event) => setForm({ ...form, name: event.target.value })}
          className="border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
        />
        <input
          type="email"
          placeholder={t('auth.email')}
          required
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
        <input
          type="password"
          placeholder={t('auth.password_confirmation')}
          required
          value={form.password_confirmation}
          onChange={(event) =>
            setForm({ ...form, password_confirmation: event.target.value })
          }
          className="border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
        />
        {error && <p className="text-terracotta-600 text-sm">{error}</p>}
        <button type="submit" className="bg-terracotta-500 hover:bg-terracotta-600 text-white font-600 rounded-full px-3 py-2 transition">
          {t('auth.register_button')}
        </button>
      </form>
    </div>
  )
}

export default RegisterPage
