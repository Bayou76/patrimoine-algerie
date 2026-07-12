/**
 * CategoryTiles — grille de 9 tuiles pour filtrer les sites par catégorie.
 *
 * Props :
 *   - active : la catégorie actuellement sélectionnée ('' = aucune)
 *   - onSelect : callback appelé avec la nouvelle valeur
 *
 * Cliquer une tuile déjà active la désélectionne (toggle),
 * ce qui évite un bouton « réinitialiser » supplémentaire.
 */

import { useTranslation } from 'react-i18next'

// Configuration statique : ajouter/retirer une catégorie = 1 ligne ici.
// La clé i18n correspondante doit exister dans les 3 JSON de traduction.
const CATEGORIES = [
  { value: '', key: 'all', icon: '✨' },
  { value: 'romain', key: 'romain', icon: '🏛️' },
  { value: 'naturel', key: 'naturel', icon: '🏞️' },
  { value: 'religieux', key: 'religieux', icon: '🕌' },
  { value: 'casbah', key: 'casbah', icon: '🏘️' },
  { value: 'islamique', key: 'islamique', icon: '🏰' },
  { value: 'colonial', key: 'colonial', icon: '🛡️' },
  { value: 'moderne', key: 'moderne', icon: '🔥' },
  { value: 'prehistorique', key: 'prehistorique', icon: '🪨' },
]

function CategoryTiles({ active, onSelect }) {
  const { t } = useTranslation()

  return (
    <div className="grid grid-cols-3 sm:grid-cols-5 gap-3">
      {CATEGORIES.map((category) => (
        <button
          key={category.key}
          type="button"
          // Toggle : si déjà actif, on renvoie '' pour désélectionner.
          onClick={() => onSelect(active === category.value ? '' : category.value)}
          className={`flex flex-col items-center gap-1.5 rounded-2xl border p-3.5 text-sm font-600 transition-all ${
            active === category.value
              ? 'border-terracotta-500 bg-terracotta-500 text-white shadow-md shadow-terracotta-500/30 scale-105'
              : 'border-sand-200 bg-white text-teal-950 hover:border-terracotta-400 hover:-translate-y-0.5 shadow-sm'
          }`}
        >
          <span className="text-2xl">{category.icon}</span>
          {t(`categories.${category.key}`)}
        </button>
      ))}
    </div>
  )
}

export default CategoryTiles
