/**
 * AdminSiteForm — formulaire complet de création/édition d'un site (admin).
 *
 * Structure du formulaire, en 4 sections :
 *   1. Informations générales (slug, coords, catégorie, wilaya, image, horaires)
 *   2. Contenu multilingue avec onglets fr/ar/en (nom, description, histoire, visite)
 *   3. Galerie d'images (URL + légende, ajout/suppression dynamique)
 *   4. Frise chronologique (événements avec année + traductions par langue)
 *
 * Fonctionne à la fois pour la création (`initial` vide) et l'édition (initial rempli).
 * Le parent (AdminSiteNewPage / AdminSiteEditPage) fournit onSubmit qui appelle l'API.
 */

import { useState } from 'react'
import { LANGUAGES } from '../context/LanguageContext'

const CATEGORIES = ['romain', 'naturel', 'religieux', 'casbah', 'islamique', 'colonial', 'moderne', 'prehistorique']
const PRIMARY_LANG = 'fr' // Langue de référence obligatoire

/**
 * slugify : transforme un nom lisible en identifiant URL-safe.
 * « Djemila » → « djemila », « Souk Ahras » → « souk-ahras ».
 * normalize NFD + regex enlève les accents (é → e).
 */
function slugify(name) {
  return name
    .toLowerCase()
    .normalize('NFD')
    .replace(/[̀-ͯ]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

// Fabriques d'objets vides pour initialiser proprement les nouvelles entrées.
const emptyTranslation = () => ({ name: '', description: '', history: '', visit_info: '' })
const emptyEventTranslation = () => ({ period_label: '', title: '', description: '' })
const emptyImage = () => ({ path: '', caption: '' })
const emptyEvent = () => ({
  year: '',
  // Object.fromEntries : construit un objet depuis un tableau de [clé, valeur].
  // Ici, on crée { fr: {...}, ar: {...}, en: {...} } dynamiquement.
  ...Object.fromEntries(LANGUAGES.map((l) => [l.code, emptyEventTranslation()])),
})

/** Champ texte réutilisable (input ou textarea). Petit composant local. */
function TextField({ label, value, onChange, type = 'text', required, textarea, rows = 3, placeholder }) {
  return (
    <label className="block text-sm">
      <span className="text-teal-950 font-600 block mb-1">
        {label}
        {required && <span className="text-terracotta-500 ml-1">*</span>}
      </span>
      {textarea ? (
        <textarea
          value={value ?? ''}
          onChange={(e) => onChange(e.target.value)}
          className="w-full border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
          rows={rows}
          placeholder={placeholder}
        />
      ) : (
        <input
          type={type}
          value={value ?? ''}
          onChange={(e) => onChange(e.target.value)}
          className="w-full border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
          placeholder={placeholder}
        />
      )}
    </label>
  )
}

/** Onglets pour basculer entre les langues de contenu. */
function LanguageTabs({ active, onChange }) {
  return (
    <div className="flex flex-wrap gap-2 mb-4">
      {LANGUAGES.map((lang) => (
        <button
          key={lang.code}
          type="button"
          onClick={() => onChange(lang.code)}
          className={`text-sm font-600 rounded-full px-3 py-1.5 transition ${
            active === lang.code
              ? 'bg-teal-950 text-white'
              : 'bg-white border border-sand-200 text-teal-950 hover:border-terracotta-400'
          }`}
        >
          {lang.label}
          {/* Étoile rouge sur la langue principale obligatoire */}
          {lang.code === PRIMARY_LANG && <span className="ml-1 text-terracotta-400">*</span>}
        </button>
      ))}
    </div>
  )
}

function AdminSiteForm({ initial, onSubmit, submitLabel }) {
  // --- Initialisation de l'état à partir de `initial` (édition) ou vide (création) ---
  const buildTranslations = () => {
    const out = {}
    for (const lang of LANGUAGES) {
      out[lang.code] = initial?.translations?.[lang.code] ?? emptyTranslation()
    }
    return out
  }

  const buildTimeline = () => {
    if (!initial?.timeline?.length) return []
    return initial.timeline.map((event) => {
      const complete = { year: event.year }
      for (const lang of LANGUAGES) {
        complete[lang.code] = event[lang.code] ?? emptyEventTranslation()
      }
      return complete
    })
  }

  // useState avec une fonction : lazy init, exécutée 1 seule fois.
  const [form, setForm] = useState(() => ({
    slug: initial?.slug ?? '',
    category: initial?.category ?? 'romain',
    wilaya: initial?.wilaya ?? '',
    latitude: initial?.latitude ?? '',
    longitude: initial?.longitude ?? '',
    image_path: initial?.image_path ?? '',
    opening_hours: initial?.opening_hours ?? '',
    entry_fee: initial?.entry_fee ?? '',
    translations: buildTranslations(),
    images: initial?.images?.length ? [...initial.images] : [],
    timeline: buildTimeline(),
  }))
  const [activeLang, setActiveLang] = useState(PRIMARY_LANG) // onglet actif
  const [submitting, setSubmitting] = useState(false)
  const [error, setError] = useState(null)

  // --- Setters partiels (patterns React classiques d'immutabilité) ---
  // On ne mute jamais l'ancien state, on crée toujours un nouvel objet.
  const set = (key, value) => setForm((prev) => ({ ...prev, [key]: value }))
  const setTranslation = (lang, field, value) =>
    setForm((prev) => ({
      ...prev,
      translations: {
        ...prev.translations,
        [lang]: { ...prev.translations[lang], [field]: value },
      },
    }))
  const setImage = (idx, field, value) => {
    setForm((prev) => {
      const images = [...prev.images]
      images[idx] = { ...images[idx], [field]: value }
      return { ...prev, images }
    })
  }
  const setEvent = (idx, lang, field, value) => {
    setForm((prev) => {
      const timeline = [...prev.timeline]
      if (field === 'year') {
        timeline[idx] = { ...timeline[idx], year: value }
      } else {
        timeline[idx] = {
          ...timeline[idx],
          [lang]: { ...timeline[idx][lang], [field]: value },
        }
      }
      return { ...prev, timeline }
    })
  }

  /**
   * Astuce UX : quand on tape le nom français, on auto-remplit le slug
   * (uniquement si on est en création et que l'utilisateur n'a pas encore
   * modifié le slug manuellement).
   */
  const handleFrenchNameChange = (value) => {
    setTranslation(PRIMARY_LANG, 'name', value)
    if (!initial?.slug && !form.slug) {
      set('slug', slugify(value))
    }
  }

  const handleSubmit = async (event) => {
    event.preventDefault()
    setError(null)
    setSubmitting(true)

    try {
      // On convertit les strings en nombres avant d'envoyer à l'API.
      await onSubmit({
        ...form,
        latitude: Number(form.latitude),
        longitude: Number(form.longitude),
        timeline: form.timeline.map((e) => ({ ...e, year: Number(e.year) })),
      })
    } catch (err) {
      const first = Object.values(err.errors ?? {})[0]?.[0]
      setError(first ?? err.message ?? "Impossible d'enregistrer.")
    } finally {
      setSubmitting(false)
    }
  }

  const currentLang = LANGUAGES.find((l) => l.code === activeLang) ?? LANGUAGES[0]
  const isPrimary = activeLang === PRIMARY_LANG

  return (
    <form onSubmit={handleSubmit} className="flex flex-col gap-8">
      {/* --- Section 1 : Informations générales --- */}
      <section className="bg-white rounded-2xl border border-sand-200 shadow-sm p-6">
        <h2 className="font-display font-700 text-xl text-teal-950 mb-4">Informations générales</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <TextField
            label="Nom du site (français)"
            value={form.translations[PRIMARY_LANG].name}
            onChange={handleFrenchNameChange}
            required
          />
          <TextField
            label="Slug (URL)"
            value={form.slug}
            onChange={(v) => set('slug', v)}
            required
            placeholder="djemila"
          />
          <label className="block text-sm">
            <span className="text-teal-950 font-600 block mb-1">
              Catégorie<span className="text-terracotta-500 ml-1">*</span>
            </span>
            <select
              value={form.category}
              onChange={(e) => set('category', e.target.value)}
              className="w-full border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
            >
              {CATEGORIES.map((c) => (
                <option key={c} value={c}>{c}</option>
              ))}
            </select>
          </label>
          <TextField label="Wilaya" value={form.wilaya} onChange={(v) => set('wilaya', v)} required />
          <TextField
            label="Latitude"
            type="number"
            value={form.latitude}
            onChange={(v) => set('latitude', v)}
            required
            placeholder="36.3167"
          />
          <TextField
            label="Longitude"
            type="number"
            value={form.longitude}
            onChange={(v) => set('longitude', v)}
            required
            placeholder="5.7333"
          />
          <div className="sm:col-span-2">
            <TextField
              label="URL de l'image principale"
              value={form.image_path}
              onChange={(v) => set('image_path', v)}
              placeholder="https://upload.wikimedia.org/..."
            />
          </div>
          <TextField label="Horaires" value={form.opening_hours} onChange={(v) => set('opening_hours', v)} />
          <TextField label="Tarif" value={form.entry_fee} onChange={(v) => set('entry_fee', v)} />
        </div>
      </section>

      {/* --- Section 2 : Contenu multilingue avec onglets --- */}
      <section className="bg-white rounded-2xl border border-sand-200 shadow-sm p-6">
        <h2 className="font-display font-700 text-xl text-teal-950 mb-2">Contenu multilingue</h2>
        <p className="text-xs text-teal-900/60 mb-3">
          <span className="text-terracotta-500">*</span> Français : langue de référence (obligatoire).
          Les autres langues sont optionnelles — le site retombe automatiquement sur le français si une traduction manque.
        </p>
        <LanguageTabs active={activeLang} onChange={setActiveLang} />

        {/* dir=rtl si arabe pour un rendu correct des inputs */}
        <div className="flex flex-col gap-4" dir={currentLang.rtl ? 'rtl' : 'ltr'}>
          {/* Le nom est déjà édité dans la section 1 pour la langue principale. */}
          {!isPrimary && (
            <TextField
              label="Nom"
              value={form.translations[activeLang].name}
              onChange={(v) => setTranslation(activeLang, 'name', v)}
            />
          )}
          <TextField
            label="Résumé court"
            value={form.translations[activeLang].description}
            onChange={(v) => setTranslation(activeLang, 'description', v)}
            textarea
            rows={2}
          />
          <TextField
            label="Histoire (texte long)"
            value={form.translations[activeLang].history}
            onChange={(v) => setTranslation(activeLang, 'history', v)}
            textarea
            rows={8}
          />
          <TextField
            label="Comment visiter"
            value={form.translations[activeLang].visit_info}
            onChange={(v) => setTranslation(activeLang, 'visit_info', v)}
            textarea
            rows={4}
          />
        </div>
      </section>

      {/* --- Section 3 : Galerie d'images dynamique --- */}
      <section className="bg-white rounded-2xl border border-sand-200 shadow-sm p-6">
        <div className="flex justify-between items-center mb-4">
          <h2 className="font-display font-700 text-xl text-teal-950">Galerie</h2>
          <button
            type="button"
            onClick={() => set('images', [...form.images, emptyImage()])}
            className="text-sm bg-terracotta-500 hover:bg-terracotta-600 text-white font-600 px-3 py-1.5 rounded-full transition"
          >
            + Ajouter une image
          </button>
        </div>
        <div className="flex flex-col gap-3">
          {form.images.map((img, idx) => (
            <div key={idx} className="flex gap-3 items-start">
              <div className="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                <TextField label="URL" value={img.path} onChange={(v) => setImage(idx, 'path', v)} />
                <TextField label="Légende" value={img.caption} onChange={(v) => setImage(idx, 'caption', v)} />
              </div>
              <button
                type="button"
                // filter avec (_, i) => i !== idx retire l'élément à cet index.
                onClick={() => set('images', form.images.filter((_, i) => i !== idx))}
                className="mt-6 text-xs text-terracotta-600 hover:text-terracotta-700 font-600"
              >
                Retirer
              </button>
            </div>
          ))}
          {form.images.length === 0 && <p className="text-sm text-teal-900/60">Aucune image.</p>}
        </div>
      </section>

      {/* --- Section 4 : Frise chronologique par événement --- */}
      <section className="bg-white rounded-2xl border border-sand-200 shadow-sm p-6">
        <div className="flex justify-between items-center mb-4">
          <h2 className="font-display font-700 text-xl text-teal-950">Frise chronologique</h2>
          <button
            type="button"
            onClick={() => set('timeline', [...form.timeline, emptyEvent()])}
            className="text-sm bg-terracotta-500 hover:bg-terracotta-600 text-white font-600 px-3 py-1.5 rounded-full transition"
          >
            + Ajouter un événement
          </button>
        </div>
        <p className="text-xs text-teal-900/60 mb-3">
          Événements édités dans la langue active : <strong>{currentLang.label}</strong>. Utilisez les onglets ci-dessus pour changer de langue.
        </p>
        <div className="flex flex-col gap-4">
          {form.timeline.map((event, idx) => (
            <div key={idx} className="border border-sand-200 rounded-xl p-4 flex flex-col gap-3">
              <div className="flex justify-between items-start gap-3">
                <div className="w-32">
                  <TextField
                    label="Année"
                    type="number"
                    value={event.year}
                    onChange={(v) => setEvent(idx, null, 'year', v)}
                    required
                    placeholder="100 ou -6000"
                  />
                </div>
                <button
                  type="button"
                  onClick={() => set('timeline', form.timeline.filter((_, i) => i !== idx))}
                  className="mt-6 text-xs text-terracotta-600 hover:text-terracotta-700 font-600"
                >
                  Retirer
                </button>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-2" dir={currentLang.rtl ? 'rtl' : 'ltr'}>
                <TextField
                  label="Libellé période"
                  value={event[activeLang].period_label}
                  onChange={(v) => setEvent(idx, activeLang, 'period_label', v)}
                  required={isPrimary}
                />
                <TextField
                  label="Titre"
                  value={event[activeLang].title}
                  onChange={(v) => setEvent(idx, activeLang, 'title', v)}
                  required={isPrimary}
                />
                <TextField
                  label="Description"
                  value={event[activeLang].description}
                  onChange={(v) => setEvent(idx, activeLang, 'description', v)}
                  textarea
                  rows={2}
                />
              </div>
            </div>
          ))}
          {form.timeline.length === 0 && <p className="text-sm text-teal-900/60">Aucun événement.</p>}
        </div>
      </section>

      {error && (
        <p className="bg-terracotta-50 border border-terracotta-400/30 text-terracotta-700 rounded-xl px-4 py-2 text-sm">
          {error}
        </p>
      )}

      <button
        type="submit"
        disabled={submitting}
        className="self-start bg-terracotta-500 hover:bg-terracotta-600 text-white font-600 rounded-full px-6 py-3 disabled:opacity-50 transition"
      >
        {submitting ? 'Enregistrement...' : submitLabel}
      </button>
    </form>
  )
}

export default AdminSiteForm
