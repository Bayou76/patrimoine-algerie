/**
 * AudioGuideButton — bulle 🎧 flottante et déplaçable qui lit l'histoire
 * d'un site à voix haute, avec pause/reprise, recul/avance et vitesse.
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
 * La bulle est le seul point d'entrée (pas de bouton "Écouter l'histoire"
 * séparé) : un clic dessus lance la lecture et déplie les commandes, un
 * clic sur l'icône casque du panneau replie. Elle est aussi déplaçable
 * (glisser-déposer) et reste fixée à l'écran pendant le scroll, à l'endroit
 * où on l'a laissée.
 *
 * La voix suit la langue active du site (fr-FR, ar-DZ, en-US) pour une
 * prononciation correcte.
 */

import { useEffect, useRef, useState } from 'react'
import { useTranslation } from 'react-i18next'

const LOCALE_MAP = { fr: 'fr-FR', ar: 'ar-SA', en: 'en-US' }
const RATES = [0.75, 1, 1.25, 1.5]
const BUBBLE_SIZE = 48
const MARGIN = 16

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
  const [started, setStarted] = useState(false)
  const [expanded, setExpanded] = useState(false)
  // {x, y} en coordonnées viewport une fois déplacée au moins une fois ;
  // tant que null, la bulle reste ancrée en bas-droite via CSS (fixed +
  // bottom/right), ce qui évite tout calcul de position fragile au montage
  // (window.innerWidth/Height pas toujours fiable au premier rendu).
  const [position, setPosition] = useState(null)

  // Refs (pas de re-render nécessaire) : phrases découpées + position courante.
  const sentencesRef = useRef([])
  const indexRef = useRef(0)

  // Refs de glisser-déposer : détecte un drag (pour ne pas déclencher le
  // clic play/pause en même temps) et mémorise le point de départ.
  const dragRef = useRef({ dragging: false, moved: false, startX: 0, startY: 0, originX: 0, originY: 0 })

  useEffect(() => {
    setSupported(typeof window !== 'undefined' && 'speechSynthesis' in window)
  }, [])

  useEffect(() => {
    sentencesRef.current = text ? splitIntoSentences(text) : []
    indexRef.current = 0
  }, [text])

  // Si la fenêtre est redimensionnée/tournée après un déplacement manuel,
  // on recadre la position pour qu'elle ne se retrouve jamais hors écran.
  // (Tant qu'on n'a pas déplacé la bulle, position reste null et c'est le
  // CSS bottom/right qui gère l'ancrage — rien à recadrer dans ce cas.)
  useEffect(() => {
    const handleResize = () => {
      setPosition((prev) => (prev ? clampPosition(prev.x, prev.y) : prev))
    }
    window.addEventListener('resize', handleResize)
    return () => window.removeEventListener('resize', handleResize)
  }, [])

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

  // --- Glisser-déposer de la bulle (souris + tactile via Pointer Events) ---
  const clampPosition = (x, y) => ({
    x: Math.min(Math.max(x, MARGIN), window.innerWidth - BUBBLE_SIZE - MARGIN),
    y: Math.min(Math.max(y, MARGIN), window.innerHeight - BUBBLE_SIZE - MARGIN),
  })

  const handlePointerDown = (event) => {
    // Avant le premier glissement, position est null (ancrage CSS) : on lit
    // la position réelle à l'écran du bouton pour partir du bon endroit.
    const rect = event.currentTarget.getBoundingClientRect()
    dragRef.current = {
      dragging: true,
      moved: false,
      startX: event.clientX,
      startY: event.clientY,
      originX: position?.x ?? rect.left,
      originY: position?.y ?? rect.top,
    }
    event.currentTarget.setPointerCapture(event.pointerId)
  }

  const handlePointerMove = (event) => {
    if (!dragRef.current.dragging) return
    const deltaX = event.clientX - dragRef.current.startX
    const deltaY = event.clientY - dragRef.current.startY
    if (Math.abs(deltaX) > 4 || Math.abs(deltaY) > 4) dragRef.current.moved = true
    setPosition(clampPosition(dragRef.current.originX + deltaX, dragRef.current.originY + deltaY))
  }

  const handlePointerUp = () => {
    dragRef.current.dragging = false
  }

  // Le clic ne déclenche l'action que si la bulle n'a pas été déplacée
  // (sinon on vient de faire un glisser-déposer, pas un vrai clic).
  const handleBubbleClick = () => {
    if (dragRef.current.moved) return
    if (!started) {
      handlePlayPause()
      setExpanded(true)
    } else {
      setExpanded((prev) => !prev)
    }
  }

  const dragHandlers = {
    onPointerDown: handlePointerDown,
    onPointerMove: handlePointerMove,
    onPointerUp: handlePointerUp,
    onPointerCancel: handlePointerUp,
  }

  return (
    <div
      className={`fixed z-40 touch-none select-none ${position ? '' : 'bottom-4 right-4'}`}
      style={position ? { left: position.x, top: position.y } : undefined}
    >
      {!expanded && (
        <button
          type="button"
          {...dragHandlers}
          onClick={handleBubbleClick}
          aria-label={started ? t('detail.audio_expand') : t('detail.audio_listen')}
          title={started ? t('detail.audio_expand') : t('detail.audio_listen')}
          className={`w-12 h-12 flex items-center justify-center rounded-full shadow-lg cursor-grab active:cursor-grabbing transition-colors ${
            playing ? 'bg-teal-800 text-white animate-pulse' : 'bg-white text-teal-950 border border-sand-200'
          }`}
        >
          <span className="text-xl">🎧</span>
        </button>
      )}

      {expanded && (
        <div className="flex items-center gap-1.5 bg-white/95 backdrop-blur border border-sand-200 rounded-full shadow-lg px-3 py-2">
          <button
            type="button"
            {...dragHandlers}
            onClick={handleBubbleClick}
            aria-label={t('detail.audio_collapse')}
            title={t('detail.audio_collapse')}
            className="w-10 h-10 shrink-0 flex items-center justify-center rounded-full bg-teal-950/5 text-teal-950 cursor-grab active:cursor-grabbing"
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
          <button
            type="button"
            onClick={() => handleSkip(-1)}
            aria-label={t('detail.audio_rewind')}
            title={t('detail.audio_rewind')}
            className="w-9 h-9 shrink-0 flex items-center justify-center rounded-full bg-white border border-sand-200 hover:border-terracotta-400 shadow-sm text-teal-950"
          >
            ⏪
          </button>
          <button
            type="button"
            onClick={() => handleSkip(1)}
            aria-label={t('detail.audio_forward')}
            title={t('detail.audio_forward')}
            className="w-9 h-9 shrink-0 flex items-center justify-center rounded-full bg-white border border-sand-200 hover:border-terracotta-400 shadow-sm text-teal-950"
          >
            ⏩
          </button>
          <button
            type="button"
            onClick={handleSpeedToggle}
            aria-label={t('detail.audio_speed')}
            title={t('detail.audio_speed')}
            className="h-9 px-2.5 shrink-0 flex items-center justify-center rounded-full bg-white border border-sand-200 hover:border-terracotta-400 shadow-sm text-teal-950 text-xs font-700"
          >
            {RATES[rateIndex]}×
          </button>
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
    </div>
  )
}

export default AudioGuideButton
