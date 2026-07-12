/**
 * ReviewForm — formulaire pour publier un avis sur un site.
 *
 * Affiché en bas de la fiche site uniquement si l'utilisateur est connecté.
 * L'API renvoie is_verified=true si l'utilisateur avait marqué le site comme
 * visité avant de publier (badge « Visite vérifiée »).
 *
 * Props :
 *   - siteId : id du site sur lequel poster l'avis
 *   - onReviewAdded : callback appelé après création réussie (refresh de la liste)
 */

import { useState } from 'react'
import { useTranslation } from 'react-i18next'
import { api } from '../services/api'

function ReviewForm({ siteId, onReviewAdded }) {
  const { t } = useTranslation()
  const [rating, setRating] = useState(5) // Note par défaut : 5 étoiles
  const [comment, setComment] = useState('')
  const [error, setError] = useState(null)
  const [submitting, setSubmitting] = useState(false)

  const handleSubmit = async (event) => {
    event.preventDefault() // Empêche le rechargement de la page
    setError(null)
    setSubmitting(true)

    try {
      await api.createReview(siteId, { rating: Number(rating), comment })
      setComment('') // Reset le champ après succès
      onReviewAdded() // Prévient le parent pour rafraîchir la liste
    } catch (err) {
      // Récupère la 1ère erreur de validation Laravel s'il y en a,
      // sinon un message générique i18n.
      const firstError = Object.values(err.errors ?? {})[0]?.[0]
      setError(firstError ?? t('review_form.error'))
    } finally {
      // finally exécuté même si try réussit ou échoue → on débloque le bouton.
      setSubmitting(false)
    }
  }

  return (
    <form onSubmit={handleSubmit} className="flex flex-col gap-3 max-w-md">
      <label className="text-sm text-teal-900">
        {t('review_form.rating')}
        <select
          value={rating}
          onChange={(event) => setRating(event.target.value)}
          className="border border-sand-200 rounded-lg px-2 py-1 ml-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
        >
          {/* 5, 4, 3, 2, 1 → les plus hautes en premier (biais UX positif) */}
          {[5, 4, 3, 2, 1].map((value) => (
            <option key={value} value={value}>
              {value} ★
            </option>
          ))}
        </select>
      </label>

      <textarea
        placeholder={t('review_form.comment_placeholder')}
        value={comment}
        onChange={(event) => setComment(event.target.value)}
        className="border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
        rows={3}
      />

      {error && <p className="text-terracotta-600 text-sm">{error}</p>}

      <button
        type="submit"
        // disabled empêche le double-clic pendant l'envoi.
        disabled={submitting}
        className="bg-terracotta-500 hover:bg-terracotta-600 text-white font-600 rounded-full px-4 py-2 disabled:opacity-70 transition flex items-center justify-center gap-2"
      >
        {submitting && <span className="spinner w-4 h-4" />}
        {submitting ? t('review_form.submitting') : t('review_form.submit')}
      </button>
    </form>
  )
}

export default ReviewForm
