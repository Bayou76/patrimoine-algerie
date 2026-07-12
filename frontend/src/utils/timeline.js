/**
 * Filtre les événements de chronologie dans la langue demandée, avec fallback fr,
 * puis les trie par année croissante (les plus anciens en premier).
 *
 * Note : le tri se fait sur une COPIE (spread [...source]) car sort() modifie
 * le tableau en place — modifier un tableau reçu de props causerait des bugs.
 */
export function resolveTimeline(events, language) {
  const filtered = events.filter((event) => event.language_code === language)
  const source = filtered.length > 0 ? filtered : events.filter((event) => event.language_code === 'fr')

  return [...source].sort((a, b) => a.year - b.year)
}
