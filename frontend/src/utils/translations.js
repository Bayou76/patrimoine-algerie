/**
 * Choisit la traduction d'un site/itinéraire pour la langue demandée,
 * avec fallback vers le français si absente.
 *
 * Évite les null qui casseraient l'affichage : mieux vaut montrer la version
 * française qu'un écran vide.
 */
export function resolveTranslation(translations, language) {
  return (
    translations.find((t) => t.language_code === language) ??
    translations.find((t) => t.language_code === 'fr')
  )
}
