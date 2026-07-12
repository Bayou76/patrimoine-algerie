/**
 * ShareButton — bouton 🔗 « Partager » sur les fiches sites et itinéraires.
 *
 * Stratégie progressive :
 *   1. Sur mobile → navigator.share ouvre la feuille de partage native
 *      (WhatsApp, SMS, Instagram, AirDrop…). C'est l'expérience idéale.
 *   2. Sur desktop → navigator.clipboard.writeText copie l'URL et on affiche
 *      « ✅ Lien copié ! » pendant 2s.
 *   3. Si le clipboard est bloqué (context http, permissions) → window.prompt
 *      pour que l'utilisateur puisse sélectionner et copier manuellement.
 */

import { useState } from 'react'
import { useTranslation } from 'react-i18next'

function ShareButton({ title, text }) {
  const { t } = useTranslation()
  const [copied, setCopied] = useState(false)

  const handleShare = async () => {
    const url = window.location.href
    const data = { title: title || document.title, text: text || '', url }

    // navigator.share n'existe pas partout (surtout absent sur desktop).
    // On tente en premier car c'est la meilleure UX quand disponible.
    if (navigator.share) {
      try {
        await navigator.share(data)
        return
      } catch {
        // L'utilisateur a annulé le partage — comportement normal, pas d'erreur.
        return
      }
    }

    // Fallback desktop : copie dans le presse-papiers.
    try {
      await navigator.clipboard.writeText(url)
      setCopied(true)
      // Le badge « copié » disparaît après 2s.
      setTimeout(() => setCopied(false), 2000)
    } catch {
      // Fallback ultime : prompt qui permet de copier manuellement (Ctrl+C).
      window.prompt(t('share.copy_manual'), url)
    }
  }

  return (
    <button
      type="button"
      onClick={handleShare}
      className="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-600 border border-sand-200 bg-white text-teal-950 hover:border-terracotta-400 shadow-sm transition-all"
      title={t('share.hint')}
    >
      <span className="text-lg">{copied ? '✅' : '🔗'}</span>
      {copied ? t('share.copied') : t('share.button')}
    </button>
  )
}

export default ShareButton
