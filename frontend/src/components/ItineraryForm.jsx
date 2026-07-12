/**
 * ItineraryForm — formulaire réutilisable pour créer/éditer un itinéraire.
 *
 * Utilisé à deux endroits :
 *   - AdminItineraryNewPage / AdminItineraryEditPage (admin, itinéraire officiel)
 *   - ProposeItineraryPage (utilisateur inscrit, publié comme « proposé
 *     par la communauté »)
 *
 * Le même composant sert aux deux car la structure des données est
 * identique ; seul le endpoint appelé par onSubmit change.
 */

import { useState } from 'react'
import { LANGUAGES } from '../context/LanguageContext'

const THEMES = ['romain', 'sud', 'villes', 'spirituel', 'naturel']
const DIFFICULTIES = ['facile', 'moyen', 'soutenu']
const PRIMARY_LANG = 'fr'

function slugify(text) {
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[̀-ͯ]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

const emptyTranslation = () => ({ title: '', summary: '', description: '' })

/** Champ texte réutilisable (input ou textarea). */
function TextField({ label, value, onChange, required, textarea, rows = 3, placeholder }) {
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
          value={value ?? ''}
          onChange={(e) => onChange(e.target.value)}
          className="w-full border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
          placeholder={placeholder}
        />
      )}
    </label>
  )
}

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
          {lang.code === PRIMARY_LANG && <span className="ml-1 text-terracotta-400">*</span>}
        </button>
      ))}
    </div>
  )
}

/**
 * ItineraryForm — props :
 *   initial   : données existantes en édition (undefined = création)
 *   allSites  : catalogue complet des sites (pour le picker)
 *   onSubmit  : async (payload) => void — appelle l'API correspondante
 *   submitLabel : texte du bouton
 */
function ItineraryForm({ initial, allSites, onSubmit, submitLabel }) {
  const buildTranslations = () => {
    const out = {}
    for (const lang of LANGUAGES) {
      out[lang.code] = initial?.translations?.[lang.code] ?? emptyTranslation()
    }
    return out
  }

  const [form, setForm] = useState(() => ({
    slug: initial?.slug ?? '',
    duration: initial?.duration ?? '',
    difficulty: initial?.difficulty ?? 'facile',
    theme: initial?.theme ?? 'romain',
    cover_image: initial?.cover_image ?? '',
    translations: buildTranslations(),
    sites: initial?.sites?.map((s) => ({ site_id: s.site_id, name: s.name, day_label: s.day_label ?? '', note: s.note ?? '' })) ?? [],
  }))
  const [activeLang, setActiveLang] = useState(PRIMARY_LANG)
  const [siteToAdd, setSiteToAdd] = useState('')
  const [submitting, setSubmitting] = useState(false)
  const [error, setError] = useState(null)

  const set = (key, value) => setForm((prev) => ({ ...prev, [key]: value }))
  const setTranslation = (lang, field, value) =>
    setForm((prev) => ({
      ...prev,
      translations: { ...prev.translations, [lang]: { ...prev.translations[lang], [field]: value } },
    }))

  const handleTitleChange = (value) => {
    setTranslation(PRIMARY_LANG, 'title', value)
    if (!initial?.slug && !form.slug) set('slug', slugify(value))
  }

  const addSite = () => {
    if (!siteToAdd) return
    const site = allSites.find((s) => String(s.id) === siteToAdd)
    if (!site || form.sites.some((s) => s.site_id === site.id)) return
    set('sites', [...form.sites, { site_id: site.id, name: site.name, day_label: '', note: '' }])
    setSiteToAdd('')
  }

  const removeSite = (siteId) => set('sites', form.sites.filter((s) => s.site_id !== siteId))

  const moveSite = (index, direction) => {
    const next = [...form.sites]
    const target = index + direction
    if (target < 0 || target >= next.length) return
    ;[next[index], next[target]] = [next[target], next[index]]
    set('sites', next)
  }

  const updateSiteField = (siteId, field, value) => {
    set('sites', form.sites.map((s) => (s.site_id === siteId ? { ...s, [field]: value } : s)))
  }

  const handleSubmit = async (event) => {
    event.preventDefault()
    setError(null)

    if (form.sites.length < 2) {
      setError('Ajoutez au moins 2 sites à l\'itinéraire.')
      return
    }

    setSubmitting(true)
    try {
      await onSubmit({
        ...form,
        sites: form.sites.map(({ site_id, day_label, note }) => ({ site_id, day_label, note })),
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
  const availableSites = allSites.filter((s) => !form.sites.some((fs) => fs.site_id === s.id))

  return (
    <form onSubmit={handleSubmit} className="flex flex-col gap-8">
      <section className="bg-white rounded-2xl border border-sand-200 shadow-sm p-6">
        <h2 className="font-display font-700 text-xl text-teal-950 mb-4">Informations générales</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <TextField label="Titre (français)" value={form.translations[PRIMARY_LANG].title} onChange={handleTitleChange} required />
          <TextField label="Slug (URL)" value={form.slug} onChange={(v) => set('slug', v)} required placeholder="mon-itineraire" />
          <label className="block text-sm">
            <span className="text-teal-950 font-600 block mb-1">Thème<span className="text-terracotta-500 ml-1">*</span></span>
            <select value={form.theme} onChange={(e) => set('theme', e.target.value)} className="w-full border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400">
              {THEMES.map((t) => <option key={t} value={t}>{t}</option>)}
            </select>
          </label>
          <label className="block text-sm">
            <span className="text-teal-950 font-600 block mb-1">Difficulté</span>
            <select value={form.difficulty} onChange={(e) => set('difficulty', e.target.value)} className="w-full border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400">
              {DIFFICULTIES.map((d) => <option key={d} value={d}>{d}</option>)}
            </select>
          </label>
          <TextField label="Durée" value={form.duration} onChange={(v) => set('duration', v)} required placeholder="3 jours" />
          <TextField label="Image de couverture (URL, optionnel)" value={form.cover_image} onChange={(v) => set('cover_image', v)} placeholder="https://... (sinon l'image du 1er site est utilisée)" />
        </div>
      </section>

      <section className="bg-white rounded-2xl border border-sand-200 shadow-sm p-6">
        <h2 className="font-display font-700 text-xl text-teal-950 mb-2">Contenu multilingue</h2>
        <p className="text-xs text-teal-900/60 mb-3">
          <span className="text-terracotta-500">*</span> Français obligatoire. Arabe/anglais optionnels.
        </p>
        <LanguageTabs active={activeLang} onChange={setActiveLang} />
        <div className="flex flex-col gap-4" dir={currentLang.rtl ? 'rtl' : 'ltr'}>
          {!isPrimary && (
            <TextField label="Titre" value={form.translations[activeLang].title} onChange={(v) => setTranslation(activeLang, 'title', v)} />
          )}
          <TextField label="Résumé court" value={form.translations[activeLang].summary} onChange={(v) => setTranslation(activeLang, 'summary', v)} textarea rows={2} required={isPrimary} />
          <TextField label="Description longue" value={form.translations[activeLang].description} onChange={(v) => setTranslation(activeLang, 'description', v)} textarea rows={5} />
        </div>
      </section>

      <section className="bg-white rounded-2xl border border-sand-200 shadow-sm p-6">
        <h2 className="font-display font-700 text-xl text-teal-950 mb-4">
          Étapes de l'itinéraire <span className="text-terracotta-500">*</span> (minimum 2)
        </h2>
        <div className="flex gap-2 mb-4">
          <select
            value={siteToAdd}
            onChange={(e) => setSiteToAdd(e.target.value)}
            className="flex-1 border border-sand-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
          >
            <option value="">Choisir un site à ajouter…</option>
            {availableSites.map((s) => (
              <option key={s.id} value={s.id}>{s.name} — {s.wilaya}</option>
            ))}
          </select>
          <button type="button" onClick={addSite} className="bg-terracotta-500 hover:bg-terracotta-600 text-white font-600 px-4 py-2 rounded-lg transition">
            + Ajouter
          </button>
        </div>

        <div className="flex flex-col gap-3">
          {form.sites.map((site, idx) => (
            <div key={site.site_id} className="border border-sand-200 rounded-xl p-3 flex flex-col sm:flex-row gap-3">
              <div className="flex sm:flex-col gap-1 shrink-0">
                <button type="button" onClick={() => moveSite(idx, -1)} disabled={idx === 0} className="text-xs px-2 py-1 border border-sand-200 rounded disabled:opacity-30">↑</button>
                <button type="button" onClick={() => moveSite(idx, 1)} disabled={idx === form.sites.length - 1} className="text-xs px-2 py-1 border border-sand-200 rounded disabled:opacity-30">↓</button>
              </div>
              <div className="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div className="sm:col-span-3 font-600 text-teal-950 text-sm">{idx + 1}. {site.name}</div>
                <TextField label="Jour / étape" value={site.day_label} onChange={(v) => updateSiteField(site.site_id, 'day_label', v)} placeholder="Jour 1" />
                <div className="sm:col-span-2">
                  <TextField label="Note" value={site.note} onChange={(v) => updateSiteField(site.site_id, 'note', v)} placeholder="Ce qu'on y fait" />
                </div>
              </div>
              <button type="button" onClick={() => removeSite(site.site_id)} className="text-xs text-terracotta-600 hover:text-terracotta-700 font-600 self-start">
                Retirer
              </button>
            </div>
          ))}
          {form.sites.length === 0 && <p className="text-sm text-teal-900/60">Aucun site ajouté pour l'instant.</p>}
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

export default ItineraryForm
