/**
 * SiteActions — les 2 boutons ❤️ favoris et ✅ visité sur une fiche site.
 *
 * Si l'utilisateur n'est pas connecté, on affiche à la place une invitation
 * à se connecter. Sinon on gère l'état local + appels API + toasts.
 *
 * L'animation heart-pop se relance à chaque clic grâce au key={popKey} qui
 * force React à recréer le <span> (compteur incrémenté à chaque action).
 */

import { useState } from 'react'
import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { api } from '../services/api'
import { useAuth } from '../context/AuthContext'
import { useToast } from '../context/ToastContext'

/**
 * Bouton d'action générique factorisé pour éviter la duplication entre
 * les 2 boutons (favoris et visité) qui ont le même style.
 */
function ActionButton({ active, activeIcon, inactiveIcon, activeLabel, inactiveLabel, onClick, activeColor, popKey }) {
  return (
    <button
      type="button"
      onClick={onClick}
      className={`inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-600 border transition-all ${
        active
          ? `${activeColor} text-white border-transparent shadow-md`
          : 'bg-white text-teal-950 border-sand-200 hover:border-terracotta-400 shadow-sm'
      }`}
    >
      {/* key={popKey} : force le remount du span → relance l'animation
          heart-pop à chaque clic (sinon elle ne joue qu'une seule fois). */}
      <span key={popKey} className={`text-lg inline-block ${popKey ? 'animate-heart-pop' : ''}`}>
        {active ? activeIcon : inactiveIcon}
      </span>
      {active ? activeLabel : inactiveLabel}
    </button>
  )
}

function SiteActions({ siteId, interaction, onInteractionChange }) {
  const { user } = useAuth()
  const { t } = useTranslation()
  const { showToast } = useToast()
  // État local synchronisé avec ce que le backend a renvoyé.
  const [state, setState] = useState(interaction ?? { is_favorite: false, is_visited: false })
  const [busy, setBusy] = useState(false) // Anti double-clic pendant appel API.
  // Compteurs par type : chaque incrément relance l'animation via key={}.
  const [popTicks, setPopTicks] = useState({ favorite: 0, visited: 0 })

  // Utilisateur non connecté → CTA vers /login.
  if (!user) {
    return (
      <div className="text-sm text-teal-900/80 bg-white border border-sand-200 rounded-2xl px-4 py-3 shadow-sm">
        <Link to="/login" className="text-terracotta-500 font-600 hover:text-terracotta-600">
          {t('actions.connect_link')}
        </Link>
        {t('actions.connect_suffix')}
      </div>
    )
  }

  const toggle = async (type) => {
    if (busy) return
    setBusy(true)
    try {
      // L'API renvoie l'état à jour des 2 booléens.
      const next = await api.toggleInteraction(siteId, type)
      setState(next)
      // Notifie le parent (utile pour rafraîchir les badges).
      onInteractionChange?.(next)
      // Incrémente le tick pour rejouer l'animation.
      setPopTicks((prev) => ({ ...prev, [type]: prev[type] + 1 }))
      if (type === 'favorite') {
        showToast(
          next.is_favorite ? t('actions.toast_favorite_added') : t('actions.toast_favorite_removed'),
          next.is_favorite ? '❤️' : '🤍',
        )
      } else {
        showToast(
          next.is_visited ? t('actions.toast_visited_added') : t('actions.toast_visited_removed'),
          next.is_visited ? '✅' : '📍',
        )
      }
    } catch {
      // On avale l'erreur silencieusement : l'utilisateur peut réessayer.
    } finally {
      setBusy(false)
    }
  }

  return (
    <div className="flex flex-wrap gap-3">
      <ActionButton
        active={state.is_favorite}
        activeIcon="❤️"
        inactiveIcon="🤍"
        activeLabel={t('actions.in_favorites')}
        inactiveLabel={t('actions.add_favorite')}
        activeColor="bg-terracotta-500"
        onClick={() => toggle('favorite')}
        popKey={popTicks.favorite}
      />
      <ActionButton
        active={state.is_visited}
        activeIcon="✅"
        inactiveIcon="📍"
        activeLabel={t('actions.visited')}
        inactiveLabel={t('actions.add_visited')}
        activeColor="bg-teal-800"
        onClick={() => toggle('visited')}
        popKey={popTicks.visited}
      />
    </div>
  )
}

export default SiteActions
