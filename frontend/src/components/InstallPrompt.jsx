/**
 * InstallPrompt — bannière d'installation PWA, adaptée par plateforme.
 *
 * Trois modes possibles :
 *
 *   1. Chrome / Edge / Samsung Internet (Android + desktop)
 *      L'événement natif 'beforeinstallprompt' se déclenche. On l'intercepte
 *      et on affiche une bannière avec bouton « Installer » qui rejoue le
 *      prompt natif du navigateur.
 *
 *   2. Safari iOS (iPhone / iPad)
 *      Apple refuse ce standard (voir README PWA). On détecte l'iPhone/iPad
 *      et on affiche une bannière avec des instructions visuelles :
 *      « Appuie sur 🔗 puis « Sur l'écran d'accueil » ».
 *
 *   3. Safari macOS (17+)
 *      Pas d'API JS ici non plus, mais Safari macOS sait installer une
 *      « Web App » depuis le manifest via le menu Fichier. On affiche
 *      juste l'instruction : « Fichier → Ajouter au Dock ».
 *
 * Dans tous les cas :
 *   - Si l'app est déjà installée (mode standalone), la bannière ne s'affiche pas.
 *   - Si l'utilisateur a cliqué ✕ une fois, on ne l'embête plus (localStorage).
 */

import { useEffect, useState } from 'react'
import { useTranslation } from 'react-i18next'

const DISMISS_KEY = 'athar_install_dismissed'

/** Détecte iPhone / iPad / iPod. */
function isIOS() {
  return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream
}

/** Détecte macOS (hors iPad qui s'annonce parfois comme Mac depuis iPadOS 13+, exclu via maxTouchPoints). */
function isMacOS() {
  const ua = navigator.userAgent
  const isMacUA = /Macintosh|Mac OS X/.test(ua)
  const isTouchMac = isMacUA && navigator.maxTouchPoints > 1 // iPad en mode "desktop UA"
  return isMacUA && !isTouchMac
}

/** Détecte Safari (exclut Chrome/Edge/Firefox qui tournent aussi sous WebKit/Mac). */
function isSafari() {
  const ua = navigator.userAgent
  return /^((?!chrome|android|crios|fxios|edg|opr).)*safari/i.test(ua)
}

/** L'utilisateur a-t-il déjà ajouté Athar à son écran d'accueil / Dock ? */
function isStandalone() {
  return (
    window.matchMedia?.('(display-mode: standalone)').matches ||
    window.navigator.standalone === true // Propriété propriétaire iOS
  )
}

function InstallPrompt() {
  const { t } = useTranslation()
  const [deferredPrompt, setDeferredPrompt] = useState(null)
  // mode : 'native' (Chrome/Edge…), 'ios' (Safari iPhone/iPad) ou 'macos' (Safari Mac)
  const [mode, setMode] = useState(null)

  useEffect(() => {
    // Déjà installée ou déjà refusée → on ne dérange plus.
    if (localStorage.getItem(DISMISS_KEY) || isStandalone()) return

    // --- Chemin 1 : navigateurs supportant beforeinstallprompt ---
    const handler = (e) => {
      e.preventDefault()
      setDeferredPrompt(e)
      setMode('native')
    }
    window.addEventListener('beforeinstallprompt', handler)

    const installedHandler = () => {
      setMode(null)
      localStorage.setItem(DISMISS_KEY, '1')
    }
    window.addEventListener('appinstalled', installedHandler)

    // --- Chemin 2 et 3 : Safari (pas d'événement natif) ---
    // On attend 2s avant d'afficher pour ne pas assaillir dès l'arrivée.
    // Override debug via ?debugInstall=ios|macos pour tester sans le bon appareil.
    const debugParam = new URLSearchParams(window.location.search).get('debugInstall')
    let timer
    if (debugParam === 'ios' || (isIOS() && isSafari())) {
      timer = setTimeout(() => setMode('ios'), debugParam ? 100 : 2000)
    } else if (debugParam === 'macos' || (isMacOS() && isSafari())) {
      timer = setTimeout(() => setMode('macos'), debugParam ? 100 : 2000)
    }

    return () => {
      window.removeEventListener('beforeinstallprompt', handler)
      window.removeEventListener('appinstalled', installedHandler)
      if (timer) clearTimeout(timer)
    }
  }, [])

  const handleInstall = async () => {
    if (!deferredPrompt) return
    deferredPrompt.prompt()
    await deferredPrompt.userChoice
    setDeferredPrompt(null)
    setMode(null)
  }

  const handleDismiss = () => {
    setMode(null)
    localStorage.setItem(DISMISS_KEY, '1')
  }

  if (!mode) return null

  return (
    <div className="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:bottom-6 sm:max-w-sm z-[900] bg-teal-950 text-sand-100 rounded-2xl shadow-2xl border border-white/10 p-4 animate-slide-down">
      <div className="flex items-start gap-3">
        <span className="text-3xl">🏺</span>
        <div className="flex-1 min-w-0">
          <p className="font-display font-700 text-white">{t('pwa.install_title')}</p>
          <p className="text-xs text-sand-100/80 mt-1">
            {mode === 'ios' && t('pwa.install_ios_body')}
            {mode === 'macos' && t('pwa.install_macos_body')}
            {mode === 'native' && t('pwa.install_body')}
          </p>
        </div>
        <button
          type="button"
          onClick={handleDismiss}
          aria-label="Fermer"
          className="text-sand-100/60 hover:text-white text-lg leading-none"
        >
          ✕
        </button>
      </div>

      {mode === 'ios' && (
        // Version iOS : instructions visuelles étape par étape
        <div className="mt-3 bg-white/5 rounded-xl p-3 text-xs text-sand-100/90 space-y-1.5">
          <p>
            <span className="inline-block w-5 text-center font-700 text-terracotta-400">1.</span>
            {t('pwa.install_ios_step1')} <span className="inline-block bg-white/10 rounded px-1.5 py-0.5 mx-0.5">⬆️</span>
          </p>
          <p>
            <span className="inline-block w-5 text-center font-700 text-terracotta-400">2.</span>
            {t('pwa.install_ios_step2')} <span className="font-600">« {t('pwa.install_ios_action')} »</span>
          </p>
          <p>
            <span className="inline-block w-5 text-center font-700 text-terracotta-400">3.</span>
            {t('pwa.install_ios_step3')}
          </p>
        </div>
      )}

      {mode === 'macos' && (
        // Version Safari macOS : instruction unique via le menu Fichier
        <div className="mt-3 bg-white/5 rounded-xl p-3 text-xs text-sand-100/90 space-y-1.5">
          <p>
            <span className="inline-block w-5 text-center font-700 text-terracotta-400">1.</span>
            {t('pwa.install_macos_step1')} <span className="font-600">« {t('pwa.install_macos_action')} »</span>
          </p>
          <p>
            <span className="inline-block w-5 text-center font-700 text-terracotta-400">2.</span>
            {t('pwa.install_macos_step2')}
          </p>
        </div>
      )}

      {mode === 'native' && (
        // Version desktop/Android : bouton d'action direct
        <div className="flex gap-2 mt-3">
          <button
            type="button"
            onClick={handleInstall}
            className="flex-1 bg-terracotta-500 hover:bg-terracotta-600 text-white font-600 rounded-full px-4 py-2 text-sm transition"
          >
            📲 {t('pwa.install_button')}
          </button>
          <button
            type="button"
            onClick={handleDismiss}
            className="text-sm text-sand-100/70 hover:text-sand-100 px-3"
          >
            {t('pwa.install_later')}
          </button>
        </div>
      )}
    </div>
  )
}

export default InstallPrompt
