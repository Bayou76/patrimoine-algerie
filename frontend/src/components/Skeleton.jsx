/**
 * Skeleton — placeholders animés affichés pendant le chargement.
 *
 * Beaucoup mieux qu'un « Chargement… » : ça donne à l'utilisateur une idée
 * de la structure qui va apparaître. Le shimmer (voir index.css) suggère
 * que quelque chose est en cours.
 *
 * La classe .skeleton est définie dans index.css avec une animation de dégradé.
 */

/** Carte squelette pour un site (utilisée dans les listes). */
export function SkeletonCard() {
  return (
    <div className="rounded-2xl overflow-hidden bg-white shadow-md">
      <div className="h-44 skeleton" />
      <div className="p-4 space-y-2">
        <div className="skeleton h-5 w-2/3 rounded" />
        <div className="skeleton h-3 w-1/3 rounded" />
        <div className="skeleton h-3 w-full rounded mt-2" />
        <div className="skeleton h-3 w-5/6 rounded" />
      </div>
    </div>
  )
}

/** Grille de N cartes squelettes, responsive comme la vraie grille. */
export function SkeletonSiteGrid({ count = 6 }) {
  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      {/* Array.from({length: N}) crée un tableau vide de N cases,
          plus court à écrire que `new Array(N).fill()`. */}
      {Array.from({ length: count }).map((_, i) => (
        <SkeletonCard key={i} />
      ))}
    </div>
  )
}

/** Grande bannière de hero pendant le chargement d'une page détail. */
export function SkeletonHero() {
  return (
    <div className="h-72 sm:h-[28rem] skeleton" />
  )
}

/** Ligne de texte squelette (largeur passée en classe). */
export function SkeletonLine({ className = '' }) {
  return <div className={`skeleton h-3 rounded ${className}`} />
}
