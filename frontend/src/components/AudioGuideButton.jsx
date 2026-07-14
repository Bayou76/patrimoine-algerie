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
 *
 * Une fois la lecture démarrée, une petite bulle 🎧 reste collée à l'écran
 * (position fixed) pendant qu'on scrolle. Un clic dessus déplie les
 * commandes (pause/reprise, recul, avance, vitesse, fermer) ; un second clic
 * sur l'icône casque les replie, sans jamais interrompre la lecture.
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
  // Devient true au premier lancement, fait apparaître le lecteur flottant ;
  // reste true tant qu'on ne ferme pas explicitement ce lecteur (la pause
  // seule ne le fait pas disparaître, pour pouvoir reprendre facilement).
  const [started, setStarted] = useState(false)
  // La bulle flottante démarre repliée (juste l'icône casque) ; un clic
  // dessus la déplie pour révéler les commandes.
  const [expanded, setExpanded] = useState(false)

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
    setStarted(true)
    if (playing) {
      window.speechSynthesis.cancel()
      setPlaying(false)
      return
    }
    speakFrom(indexRef.current, RATES[rateIndex])
  }

  const handleClose = () => {
    window.speechSynthesis.cancel()
    setPlaying(false)
    setStarted(false)
    setExpanded(false)
    indexRef.current = 0
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

  // Boutons recul/avance/vitesse : identiques dans la barre d'actions et
  // dans le lecteur flottant, donc factorisés ici.
  const transportControls = (
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
  )

  return (
    <>
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

        {started && !playing && transportControls}
      </div>

      {/* Bulle flottante : apparaît dès le premier lancement et reste collée
          à l'écran pendant le scroll. Repliée par défaut (juste l'icône
          casque) ; un clic la déplie pour révéler les commandes. */}
      {started && !expanded && (
        <button
          type="button"
          onClick={() => setExpanded(true)}
          aria-label={t('detail.audio_expand')}
          title={t('detail.audio_expand')}
          className={`fixed bottom-4 right-4 z-40 w-12 h-12 flex items-center justify-center rounded-full shadow-lg transition-all ${
            playing ? 'bg-teal-800 text-white animate-pulse' : 'bg-white text-teal-950 border border-sand-200'
          }`}
        >
          <span className="text-xl">🎧</span>
        </button>
      )}

      {started && expanded && (
        <div className="fixed bottom-4 inset-x-4 sm:inset-x-auto sm:right-4 sm:left-auto z-40 flex items-center gap-1.5 bg-white/95 backdrop-blur border border-sand-200 rounded-full shadow-lg px-3 py-2 sm:max-w-fit mx-auto sm:mx-0">
          <button
            type="button"
            onClick={() => setExpanded(false)}
            aria-label={t('detail.audio_collapse')}
            title={t('detail.audio_collapse')}
            className="w-10 h-10 shrink-0 flex items-center justify-center rounded-full bg-teal-950/5 text-teal-950"
          >
            <span className="text-lg">🎧</span>
          </button>
          <button
            type="button"
            onClick={handlePlayPause}
            aria-label={playing ? t('detail.audio_stop') : t('detail.audio_resume')}
            title={playing ? t('detail.audio_stop') : t('detail.audio_resume')}
            className={`w-10 h-10 shrink-0 flex items-center justify-center rounded-full transition-all ${
              playing ? 'bg-teal-800 text-white' : 'bg-terracotta-500 text-white'
            }`}
          >
            <span className="text-lg">{playing ? '⏸️' : '▶️'}</span>
          </button>
          {transportControls}
          <button
            type="button"
            onClick={handleClose}
            aria-label={t('detail.audio_close')}
            title={t('detail.audio_close')}
            className="w-9 h-9 shrink-0 flex items-center justify-center rounded-full text-teal-900/50 hover:text-terracotta-600"
          >
            ✕
          </button>
        </div>
      )}
    </>
  )
}

export default AudioGuideButton
