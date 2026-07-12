/**
 * AudioGuideButton — bouton 🎧 qui lit l'histoire d'un site à voix haute.
 *
 * Utilise l'API Web Speech (SpeechSynthesis), native au navigateur et
 * gratuite — pas de clé API, pas de coût, fonctionne hors-ligne une fois
 * la page chargée. Idéal pour un visiteur sur place qui veut écouter les
 * mains libres, ou pour l'accessibilité.
 *
 * La voix suit la langue active du site (fr-FR, ar-DZ, en-US) pour une
 * prononciation correcte.
 */

import { useEffect, useState } from 'react'
import { useTranslation } from 'react-i18next'

const LOCALE_MAP = { fr: 'fr-FR', ar: 'ar-SA', en: 'en-US' }

function AudioGuideButton({ text, language }) {
  const { t } = useTranslation()
  const [speaking, setSpeaking] = useState(false)
  const [supported, setSupported] = useState(true)

  // La synthèse vocale n'existe pas sur tous les navigateurs (rare, mais
  // certains vieux navigateurs ou webviews ne l'implémentent pas).
  useEffect(() => {
    setSupported(typeof window !== 'undefined' && 'speechSynthesis' in window)
  }, [])

  // Coupe la lecture si l'utilisateur quitte la page (évite une voix qui
  // continue de parler après navigation vers une autre fiche).
  useEffect(() => {
    return () => window.speechSynthesis?.cancel()
  }, [])

  if (!supported || !text) return null

  const handleToggle = () => {
    if (speaking) {
      window.speechSynthesis.cancel()
      setSpeaking(false)
      return
    }

    const utterance = new SpeechSynthesisUtterance(text)
    utterance.lang = LOCALE_MAP[language] || 'fr-FR'
    utterance.rate = 0.95 // légèrement plus lent qu'un débit naturel, plus clair à l'oral
    utterance.onend = () => setSpeaking(false)
    utterance.onerror = () => setSpeaking(false)

    window.speechSynthesis.cancel() // coupe toute lecture précédente
    window.speechSynthesis.speak(utterance)
    setSpeaking(true)
  }

  return (
    <button
      type="button"
      onClick={handleToggle}
      className={`inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-600 border transition-all ${
        speaking
          ? 'bg-teal-800 text-white border-transparent shadow-md'
          : 'bg-white text-teal-950 border-sand-200 hover:border-terracotta-400 shadow-sm'
      }`}
    >
      <span className="text-lg">{speaking ? '⏸️' : '🎧'}</span>
      {speaking ? t('detail.audio_stop') : t('detail.audio_listen')}
    </button>
  )
}

export default AudioGuideButton
