/**
 * BadgesGrid — grille des 9 badges d'exploration sur la page /me.
 *
 * Chaque badge affiche : icône, titre, description, barre de progression.
 * Les badges verrouillés sont en niveaux de gris + opacité 60%.
 * Les données viennent du backend (MeController::computeBadges).
 */

import { useTranslation } from 'react-i18next'
import Reveal from './Reveal'

/** Carte d'un badge unique. */
function BadgeCard({ badge, index }) {
  const { t } = useTranslation()
  // Pourcentage plafonné à 100 (au cas où le compteur dépasse le seuil).
  const percent = Math.min((badge.progress / badge.threshold) * 100, 100)

  return (
    // index * 60 = cascade d'apparition au scroll
    <Reveal delay={index * 60}>
      <div
        className={`bg-white rounded-2xl border border-sand-200 shadow-sm p-4 flex flex-col items-center text-center transition ${
          badge.unlocked ? '' : 'opacity-60'
        }`}
      >
        <div
          className={`text-5xl mb-2 transition-transform ${badge.unlocked ? '' : 'grayscale'}`}
          // grayscale(1) : filtre CSS qui désature l'emoji pour signifier « verrouillé »
          style={{ filter: badge.unlocked ? 'none' : 'grayscale(1)' }}
        >
          {badge.icon}
        </div>
        {/* Fallback badge.key : si la traduction manque, on affiche la clé brute
            plutôt qu'un texte vide. */}
        <p className="font-display font-700 text-teal-950 text-sm">
          {t(`badges.${badge.key}.title`, badge.key)}
        </p>
        <p className="text-xs text-teal-900/60 mt-1 line-clamp-2">
          {t(`badges.${badge.key}.description`, '')}
        </p>
        {/* Barre de progression : or si débloqué, terracotta sinon */}
        <div className="mt-3 w-full">
          <div className="h-1.5 bg-sand-200 rounded-full overflow-hidden">
            <div
              className={`h-full rounded-full transition-all duration-700 ${
                badge.unlocked ? 'bg-gold-400' : 'bg-terracotta-400'
              }`}
              style={{ width: `${percent}%` }}
            />
          </div>
          <p className="text-[10px] text-teal-900/60 mt-1">
            {badge.progress} / {badge.threshold}
          </p>
        </div>
      </div>
    </Reveal>
  )
}

/** Grille complète : titre + compteur (débloqués/total) + les cartes. */
function BadgesGrid({ badges }) {
  const { t } = useTranslation()

  if (!badges || badges.length === 0) return null

  const unlockedCount = badges.filter((b) => b.unlocked).length

  return (
    <section className="mb-10">
      <h2 className="font-display font-700 text-2xl text-teal-950 mb-4 flex items-center gap-2">
        <span>🏆</span> {t('me.badges')}
        <span className="text-teal-900/50 text-base font-400">
          ({unlockedCount}/{badges.length})
        </span>
      </h2>
      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
        {badges.map((badge, idx) => (
          <BadgeCard key={badge.key} badge={badge} index={idx} />
        ))}
      </div>
    </section>
  )
}

export default BadgesGrid
