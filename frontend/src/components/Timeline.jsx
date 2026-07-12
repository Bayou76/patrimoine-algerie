/**
 * Timeline — chronologie verticale utilisée sur la fiche d'un site.
 *
 * Rendu comme une liste ordonnée avec :
 *   - une bordure verticale (border-s-2) qui trace la ligne de la timeline
 *   - une pastille terracotta à chaque événement (position absolute -start-[23px])
 *   - le texte à droite : période, titre, description
 *
 * Chaque événement s'anime en cascade (delay = index * 80).
 */

import Reveal from './Reveal'

function Timeline({ events }) {
  if (events.length === 0) return null

  return (
    // border-s-2 = border-inline-start : s'adapte automatiquement au RTL/LTR.
    <ol className="relative border-s-2 border-sand-200 ms-2">
      {events.map((event, index) => (
        <Reveal key={index} delay={index * 80}>
          <li className="relative mb-6 ms-4">
            {/* Pastille positionnée sur la ligne verticale via -start-[23px] */}
            <div className="absolute w-3 h-3 bg-terracotta-500 rounded-full -start-[23px] mt-1.5 ring-4 ring-sand-50" />
            <time className="text-sm font-700 text-terracotta-600">{event.period_label}</time>
            <h3 className="font-display font-700 text-base text-teal-950">{event.title}</h3>
            <p className="text-sm text-teal-900/80">{event.description}</p>
          </li>
        </Reveal>
      ))}
    </ol>
  )
}

export default Timeline
