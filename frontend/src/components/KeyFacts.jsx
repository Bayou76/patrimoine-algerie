/**
 * KeyFacts — bloc "⭐ À retenir" sur la fiche d'un site.
 *
 * Beaucoup de visiteurs lisent en diagonale : ce bloc résume en 3-5 puces
 * les infos essentielles, dérivées des données déjà en base (pas de nouveau
 * champ admin à remplir) :
 *   - catégorie + wilaya
 *   - période/année la plus ancienne de la chronologie du site
 *   - horaires si renseignés
 *   - tarif si renseigné
 *   - note moyenne si des avis existent
 */

import { useTranslation } from 'react-i18next'
import Reveal from './Reveal'

function KeyFacts({ site, timeline }) {
  const { t } = useTranslation()

  // La chronologie est déjà triée par année (resolveTimeline) ; le premier
  // événement est donc le plus ancien connu pour ce site.
  const oldestEvent = timeline?.[0]

  const facts = [
    site.unesco_year && { icon: '🏛️', text: t('detail.unesco_since', { year: site.unesco_year }) },
    { icon: '🏷️', text: t(`categories.${site.category}`, site.category) },
    { icon: '📍', text: site.wilaya },
    oldestEvent && { icon: '📜', text: oldestEvent.period_label },
    site.opening_hours && { icon: '🕒', text: site.opening_hours },
    site.entry_fee && { icon: '💵', text: site.entry_fee },
    site.average_rating !== null && { icon: '⭐', text: `${site.average_rating} / 5` },
  ].filter(Boolean)

  if (facts.length === 0) return null

  return (
    <Reveal delay={40}>
      <div className="mb-10 bg-teal-950 rounded-2xl p-5 text-white">
        <h2 className="font-display font-700 text-lg mb-3 flex items-center gap-2">
          <span>⭐</span> {t('detail.key_facts')}
        </h2>
        <ul className="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-2 text-sm">
          {facts.map((fact, idx) => (
            <li key={idx} className="flex items-center gap-2 text-sand-100/95">
              <span className="text-base">{fact.icon}</span>
              <span className="capitalize">{fact.text}</span>
            </li>
          ))}
        </ul>
      </div>
    </Reveal>
  )
}

export default KeyFacts
