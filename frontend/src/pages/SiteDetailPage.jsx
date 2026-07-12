/**
 * SiteDetailPage — fiche complète d'un site (/sites/:slug).
 *
 * Structure verticale :
 *   1. Hero avec image en fond + zoom lent + dégradé sombre
 *      Bouton retour, titre, badge catégorie, wilaya, note moyenne
 *   2. Description courte
 *   3. Barre d'actions : ❤️ favoris, ✅ visité, 🧭 Y aller, 🌐 Vue 360°, 🔗 Partager
 *   4. Histoire (long texte) + Timeline chronologique
 *   5. Comment visiter (horaires, tarif, infos pratiques)
 *   6. Galerie d'images
 *   7. Avis des utilisateurs + formulaire d'ajout
 *   8. Sites similaires suggérés
 *
 * SEO : injection JSON-LD schema.org (ArchaeologicalSite, PlaceOfWorship…)
 * pour que Google affiche des rich results avec image, localisation, note.
 */

import { useCallback, useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { api } from '../services/api'
import { useLanguage } from '../context/LanguageContext'
import { useAuth } from '../context/AuthContext'
import { resolveTranslation } from '../utils/translations'
import { resolveTimeline } from '../utils/timeline'
import ReviewForm from '../components/ReviewForm'
import Timeline from '../components/Timeline'
import SiteActions from '../components/SiteActions'
import { SkeletonHero, SkeletonLine } from '../components/Skeleton'
import Reveal from '../components/Reveal'
import SimilarSites from '../components/SimilarSites'
import DirectionsButton from '../components/DirectionsButton'
import StreetViewButton from '../components/StreetViewButton'
import ShareButton from '../components/ShareButton'
import { usePageMeta, buildSiteJsonLd } from '../utils/pageMeta'

function SiteDetailPage() {
  const { slug } = useParams() // Récupère :slug depuis l'URL
  const navigate = useNavigate()

  /**
   * Retour intelligent : si l'utilisateur est arrivé ici via une navigation
   * interne (Link), navigate(-1) le ramène à la page précédente AVEC ses
   * filtres. Si c'est une arrivée directe (URL partagée), on l'envoie à /.
   */
  const goBack = () => {
    if (window.history.length > 1) navigate(-1)
    else navigate('/')
  }
  const { language } = useLanguage()
  const { user } = useAuth()
  const { t } = useTranslation()
  const [site, setSite] = useState(null)
  const [error, setError] = useState(null)

  // useCallback pour que loadSite garde la même référence entre les renders,
  // ce qui évite les boucles infinies avec useEffect.
  const loadSite = useCallback(() => {
    api
      .getSite(slug)
      .then(setSite)
      .catch(() => setError(t('detail.not_found')))
  }, [slug, t])

  useEffect(() => {
    loadSite()
  }, [loadSite])

  // Toujours calculer la traduction avant les hooks (règle React : appeler
  // les hooks dans le même ordre à chaque render). Null tant que site n'est
  // pas chargé.
  const translation = site ? resolveTranslation(site.translations, language) : null
  usePageMeta({
    title: translation?.name,
    description: translation?.description,
    image: site?.image_path,
    type: 'article',
    language,
    jsonLdId: 'site',
    // buildSiteJsonLd (voir pageMeta.js) choisit le type schema.org selon la
    // catégorie (ArchaeologicalSite pour romain, PlaceOfWorship pour religieux…).
    jsonLd: buildSiteJsonLd(site, translation),
  })

  if (error) return <p className="p-6 text-terracotta-600">{error}</p>

  // Chargement : skeleton hero + lignes.
  if (!site) {
    return (
      <div>
        <SkeletonHero />
        <div className="max-w-3xl mx-auto p-6 space-y-3">
          <SkeletonLine className="w-2/3 h-6" />
          <SkeletonLine className="w-full" />
          <SkeletonLine className="w-11/12" />
          <SkeletonLine className="w-10/12" />
        </div>
      </div>
    )
  }

  const timeline = resolveTimeline(site.timeline, language)

  return (
    <div>
      {/* --- Hero avec image en fond + zoom animé --- */}
      <div className="h-72 sm:h-[28rem] relative overflow-hidden">
        <div
          className="absolute inset-0 bg-cover bg-center animate-hero-zoom"
          style={{ backgroundImage: `url(${site.image_path})` }}
        />
        {/* Overlay dégradé pour lisibilité du texte blanc sur photo */}
        <div className="absolute inset-0 flex items-end">
          <div className="w-full bg-gradient-to-t from-teal-950 via-teal-950/60 to-transparent p-6 pt-24">
            <div className="max-w-3xl mx-auto animate-fade-up">
              {/* Bouton retour en pilule glassmorphism */}
              <button
                type="button"
                onClick={goBack}
                className="inline-flex items-center gap-2 rounded-full px-3 py-1.5 bg-white/10 hover:bg-white/20 text-sand-100 text-sm font-600 backdrop-blur-sm border border-white/20 transition"
              >
                {t('detail.back_to_list')}
              </button>
              <h1 className="font-display font-800 text-3xl sm:text-5xl text-white mt-1 tracking-tight">
                {translation?.name}
              </h1>
              <p className="text-sm text-sand-100/90 mt-2 flex items-center gap-2">
                <span className="bg-terracotta-500 px-2.5 py-0.5 rounded-full text-xs font-600 capitalize">
                  {t(`categories.${site.category}`, site.category)}
                </span>
                {site.wilaya}
                {site.average_rating !== null && (
                  <span className="text-gold-400 font-600">★ {site.average_rating}</span>
                )}
              </p>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-3xl mx-auto p-6">
        {/* Description courte, mise en avant en gros et léger */}
        {translation?.description && (
          <Reveal>
            <p className="text-xl text-teal-900 mb-6 font-display leading-relaxed">
              {translation.description}
            </p>
          </Reveal>
        )}

        {/* Barre d'actions : 5 boutons alignés qui wrappent en flex-wrap */}
        <Reveal delay={80}>
          <div className="mb-10 flex flex-wrap gap-3">
            <SiteActions siteId={site.id} interaction={site.user_interaction} />
            <DirectionsButton
              latitude={site.latitude}
              longitude={site.longitude}
              name={translation?.name}
            />
            <StreetViewButton
              latitude={site.latitude}
              longitude={site.longitude}
            />
            <ShareButton
              title={translation?.name}
              text={translation?.description}
            />
          </div>
        </Reveal>

        {/* Histoire + timeline chronologique du site */}
        {translation?.history && (
          <Reveal>
            <section className="mb-10">
              <h2 className="font-display font-700 text-2xl text-teal-950 mb-4">{t('detail.history')}</h2>
              {/* whitespace-pre-line : conserve les sauts de ligne du texte long */}
              <p className="text-teal-900/90 whitespace-pre-line mb-6 leading-relaxed">
                {translation.history}
              </p>
              <Timeline events={timeline} />
            </section>
          </Reveal>
        )}

        {/* Section « Comment visiter » : horaires, tarif, conseils pratiques */}
        {(translation?.visit_info || site.opening_hours || site.entry_fee) && (
          <Reveal>
            <section className="mb-10 bg-terracotta-50 border border-terracotta-400/30 rounded-2xl p-5">
              <h2 className="font-display font-700 text-2xl text-teal-950 mb-4">{t('detail.how_to_visit')}</h2>
              <div className="flex flex-wrap gap-6 mb-4 text-sm">
                {site.opening_hours && (
                  <div>
                    <span className="font-700 text-teal-950">{t('detail.opening_hours')}</span>
                    <p className="text-teal-900/80">{site.opening_hours}</p>
                  </div>
                )}
                {site.entry_fee && (
                  <div>
                    <span className="font-700 text-teal-950">{t('detail.entry_fee')}</span>
                    <p className="text-teal-900/80">{site.entry_fee}</p>
                  </div>
                )}
              </div>
              {translation?.visit_info && (
                <p className="text-teal-900/90 whitespace-pre-line">{translation.visit_info}</p>
              )}
            </section>
          </Reveal>
        )}

        {/* Galerie d'images secondaires (grille 2/3 colonnes selon largeur) */}
        {site.images.length > 0 && (
          <Reveal>
            <section className="mb-10">
              <h2 className="font-display font-700 text-2xl text-teal-950 mb-4">{t('detail.gallery')}</h2>
              <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                {site.images.map((image, idx) => (
                  <Reveal key={image.id} delay={idx * 80}>
                    <figure className="group overflow-hidden rounded-xl shadow-sm">
                      <img
                        src={image.path}
                        alt={image.caption ?? translation?.name}
                        className="w-full h-32 object-cover transition-transform duration-500 group-hover:scale-110"
                      />
                      {image.caption && (
                        <figcaption className="text-xs text-teal-900/60 mt-1">
                          {image.caption}
                        </figcaption>
                      )}
                    </figure>
                  </Reveal>
                ))}
              </div>
            </section>
          </Reveal>
        )}

        {/* --- Avis utilisateurs --- */}
        <Reveal>
        <section>
          <h2 className="font-display font-700 text-2xl text-teal-950 mb-4">
            {t('detail.reviews_count', { count: site.reviews.length })}
          </h2>

          <div className="flex flex-col gap-3 mb-6">
            {site.reviews.map((review) => (
              <div key={review.id} className="border border-sand-200 bg-white rounded-xl p-4 shadow-sm flex justify-between items-start gap-3">
                <div>
                  <p className="text-sm font-700 text-teal-950">
                    {review.user_name} — <span className="text-gold-500">★ {review.rating}</span>
                    {review.is_verified && (
                      <span className="ml-2 text-xs text-teal-800 bg-teal-800/10 px-2 py-0.5 rounded-full font-600">
                        {t('detail.verified_visit')}
                      </span>
                    )}
                  </p>
                  {review.comment && (
                    <p className="text-sm text-teal-900/80 mt-1">{review.comment}</p>
                  )}
                </div>
                {/* Seul un admin peut supprimer les avis (modération) */}
                {user?.is_admin && (
                  <button
                    type="button"
                    onClick={async () => {
                      if (!confirm(t('detail.confirm_delete_review'))) return
                      await api.deleteReview(review.id)
                      loadSite() // Refresh la liste après suppression
                    }}
                    className="text-xs text-terracotta-600 hover:text-terracotta-700 font-600 whitespace-nowrap"
                  >
                    {t('detail.delete_review')}
                  </button>
                )}
              </div>
            ))}
            {site.reviews.length === 0 && (
              <p className="text-sm text-teal-900/60">{t('detail.no_reviews')}</p>
            )}
          </div>

          {/* Formulaire d'avis pour connectés, sinon lien vers /login */}
          {user ? (
            <ReviewForm siteId={site.id} onReviewAdded={loadSite} />
          ) : (
            <p className="text-sm text-teal-900/80">
              <Link to="/login" className="text-terracotta-500 hover:text-terracotta-600 font-600">
                {t('detail.connect_to_review_link')}
              </Link>
              {t('detail.connect_to_review_suffix')}
            </p>
          )}
        </section>
        </Reveal>

        {/* Suggestions de sites similaires (cf. SimilarSites) */}
        <SimilarSites sites={site.similar} />
      </div>
    </div>
  )
}

export default SiteDetailPage
