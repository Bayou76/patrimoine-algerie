/**
 * AudioGuideButton — bouton 🎧 qui lit l'histoire d'un site à voix haute,
 * avec pause/reprise, recul/avance et vitesse de lecture.
 *
 * Utilise l'API Web Speech (SpeechSynthesis), native au navigateur et
 * gratuite — pas de clé API, pas de coût, fonctionne hors-ligne une fois
 * la page chargée.
 *
 * Contrainte technique : SpeechSynthesis n'a pas de vraie notion de
 * "position" dans un texte (pas de seek par seconde comme une vidéo), et son
 * pause()/resume() natif est peu fiable selon les navigateurs. On contourne
 * ça en découpant le texte en phrases et en lisant phrase par phrase :
 * reculer/avancer = rejouer depuis une phrase antérieure/suivante, et la
 * vitesse s'applique en relançant la phrase courante avec le nouveau débit.
 *
 * La voix suit la langue active du site (fr-FR, ar-DZ, en-US) pour une
 * prononciation correcte.
 */

import { useEffect, useRef, useState } from 'react'
import { useTranslation } from 'react-i18next'

const LOCALE_MAP = { fr: 'fr-FR', ar: 'ar-SA', en: 'en-US' }
const RATES = [0.75, 1, 1.25, 1.5]

// Découpe un texte en phrases sur . ! ? tout en conservant la ponctuation,
// pour que chaque morceau se lise naturellement à l'oral.
function splitIntoSentences(text) {
  const matches = text.match(/[^.!?]+[.!?]+(\s+|$)|[^.!?]+$/g)
  return (matches ?? [text]).map((s) => s.trim()).filter(Boolean)
}

function AudioGuideButton({ text, language }) {
  const { t } = useTranslation()
  const [playing, setPlaying] = useState(false)
  const [supported, setSupported] = useState(true)
  const [rateIndex, setRateIndex] = useState(1) // index dans RATES, 1 = vitesse normale

  // Refs (pas de re-render nécessaire) : phrases découpées + position courante.
  const sentencesRef = useRef([])
  const indexRef = useRef(0)

  useEffect(() => {
    setSupported(typeof window !== 'undefined' && 'speechSynthesis' in window)
  }, [])

  useEffect(() => {
    sentencesRef.current = text ? splitIntoSentences(text) : []
    indexRef.current = 0
  }, [text])

  // Coupe la lecture si l'utilisateur quitte la page.
  useEffect(() => {
    return () => window.speechSynthesis?.cancel()
  }, [])

  if (!supported || !text) return null

  const speakFrom = (index, rate) => {
    const sentences = sentencesRef.current
    if (index < 0 || index >= sentences.length) {
      setPlaying(false)
      return
    }

    window.speechSynthesis.cancel()
    indexRef.current = index

    const utterance = new SpeechSynthesisUtterance(sentences[index])
    utterance.lang = LOCALE_MAP[language] || 'fr-FR'
    utterance.rate = rate
    utterance.onend = () => {
      // Enchaîne automatiquement sur la phrase suivante, sauf si on vient
      // d'être arrêté manuellement (indexRef aura changé entre-temps).
      if (indexRef.current === index) speakFrom(index + 1, rate)
    }
    utterance.onerror = () => setPlaying(false)

    window.speechSynthesis.speak(utterance)
    setPlaying(true)
  }

  const handlePlayPause = () => {
    if (playing) {
      window.speechSynthesis.cancel()
      setPlaying(false)
      return
    }
    speakFrom(indexRef.current, RATES[rateIndex])
  }

  const handleSkip = (delta) => {
    const nextIndex = Math.min(
      Math.max(indexRef.current + delta, 0),
      sentencesRef.current.length - 1
    )
    if (playing) {
      speakFrom(nextIndex, RATES[rateIndex])
    } else {
      indexRef.current = nextIndex
    }
  }

  const handleSpeedToggle = () => {
    const nextRateIndex = (rateIndex + 1) % RATES.length
    setRateIndex(nextRateIndex)
    if (playing) speakFrom(indexRef.current, RATES[nextRateIndex])
  }

  return (
    <div className="inline-flex items-center gap-1.5 flex-wrap">
      <button
        type="button"
        onClick={handlePlayPause}
        className={`inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-600 border transition-all ${
          playing
            ? 'bg-teal-800 text-white border-transparent shadow-md'
            : 'bg-white text-teal-950 border-sand-200 hover:border-terracotta-400 shadow-sm'
        }`}
      >
        <span className="text-lg">{playing ? '⏸️' : '🎧'}</span>
        {playing ? t('detail.audio_stop') : t('detail.audio_listen')}
      </button>

      {(playing || indexRef.current > 0) && (
        <>
          <button
            type="button"
            onClick={() => handleSkip(-1)}
            aria-label={t('detail.audio_rewind')}
            title={t('detail.audio_rewind')}
            className="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-sand-200 hover:border-terracotta-400 shadow-sm text-teal-950"
          >
            ⏪
          </button>
          <button
            type="button"
            onClick={() => handleSkip(1)}
            aria-label={t('detail.audio_forward')}
            title={t('detail.audio_forward')}
            className="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-sand-200 hover:border-terracotta-400 shadow-sm text-teal-950"
          >
            ⏩
          </button>
          <button
            type="button"
            onClick={handleSpeedToggle}
            aria-label={t('detail.audio_speed')}
            title={t('detail.audio_speed')}
            className="h-9 px-2.5 flex items-center justify-center rounded-full bg-white border border-sand-200 hover:border-terracotta-400 shadow-sm text-teal-950 text-xs font-700"
          >
            {RATES[rateIndex]}×
          </button>
        </>
      )}
    </div>
  )
}

export default AudioGuideButton
