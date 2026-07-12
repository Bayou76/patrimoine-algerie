/**
 * PracticalInfoPage — page /infos-pratiques (FAQ voyageur).
 *
 * 8 sections (visa, saison, transport, sécurité, argent, ramadan, connexion,
 * étiquette) dont le contenu est stocké dans les JSON i18n fr/ar/en.
 *
 * SEO bonus : on injecte du JSON-LD schema.org de type FAQPage. Google
 * peut alors afficher les questions/réponses directement dans les résultats
 * de recherche (rich snippet FAQ). C'est un gros bonus de visibilité.
 */

import { useTranslation } from 'react-i18next'
import Reveal from '../components/Reveal'
import { useLanguage } from '../context/LanguageContext'
import { usePageMeta } from '../utils/pageMeta'

// Liste des sections + leur icône emoji.
// Ajouter une section = ajouter une entrée ici + les clés i18n correspondantes.
const SECTIONS = [
  { key: 'visa', icon: '🛂' },
  { key: 'best_season', icon: '☀️' },
  { key: 'transport', icon: '🚗' },
  { key: 'travel_times', icon: '⏱️' },
  { key: 'safety', icon: '🛡️' },
  { key: 'money', icon: '💵' },
  { key: 'ramadan', icon: '🌙' },
  { key: 'connectivity', icon: '📶' },
  { key: 'etiquette', icon: '🤝' },
  { key: 'prep', icon: '🎒' },
  { key: 'heritage', icon: '🏛️' },
]

function PracticalInfoPage() {
  const { t } = useTranslation()
  const { language } = useLanguage()

  usePageMeta({
    title: t('practical_info.title'),
    description: t('practical_info.subtitle'),
    type: 'website',
    language,
    jsonLdId: 'faq',
    // JSON-LD FAQPage : chaque section devient une question schema.org.
    // Google peut afficher un accordéon FAQ dans ses résultats.
    jsonLd: {
      '@context': 'https://schema.org',
      '@type': 'FAQPage',
      mainEntity: SECTIONS.map((s) => ({
        '@type': 'Question',
        name: t(`practical_info.sections.${s.key}.title`),
        acceptedAnswer: {
          '@type': 'Answer',
          text: t(`practical_info.sections.${s.key}.body`),
        },
      })),
    },
  })

  return (
    <div>
      {/* Hero sombre */}
      <div className="relative bg-teal-950 px-6 py-20 text-center">
        <div className="absolute inset-0 bg-linear-to-b from-teal-950/40 to-teal-950" />
        <div className="relative">
          <h1 className="font-display font-800 text-4xl sm:text-5xl text-white tracking-tight">
            ℹ️ {t('practical_info.title')}
          </h1>
          <p className="text-sand-100/80 mt-3 max-w-2xl mx-auto text-lg">
            {t('practical_info.subtitle')}
          </p>
        </div>
      </div>

      <div className="max-w-3xl mx-auto p-6">
        <div className="grid grid-cols-1 gap-5">
          {SECTIONS.map((section, idx) => (
            // Math.min plafonne le délai — sinon les 8 sections tardent trop.
            <Reveal key={section.key} delay={Math.min(idx * 60, 240)}>
              <section className="bg-white rounded-2xl border border-sand-200 p-6 hover:border-terracotta-400 hover:shadow-md transition">
                <h2 className="font-display font-700 text-xl text-teal-950 flex items-center gap-3">
                  <span className="text-2xl">{section.icon}</span>
                  {t(`practical_info.sections.${section.key}.title`)}
                </h2>
                {/* whitespace-pre-line : conserve les \n du JSON i18n (sauts de ligne
                    entre paragraphes) sans avoir à écrire du HTML dans les traductions. */}
                <p className="text-teal-900/80 mt-3 leading-relaxed whitespace-pre-line">
                  {t(`practical_info.sections.${section.key}.body`)}
                </p>
              </section>
            </Reveal>
          ))}
        </div>

        <Reveal delay={200}>
          <p className="text-xs text-teal-900/50 mt-8 text-center">
            {t('practical_info.disclaimer')}
          </p>
        </Reveal>
      </div>
    </div>
  )
}

export default PracticalInfoPage
