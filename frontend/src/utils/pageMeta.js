import { useEffect } from 'react'

function setMeta(selector, attr, value) {
  let el = document.head.querySelector(selector)
  if (!el) {
    el = document.createElement('meta')
    const [key, val] = selector.replace('meta[', '').replace(']', '').split('=')
    el.setAttribute(key, val.replaceAll('"', ''))
    document.head.appendChild(el)
  }
  el.setAttribute(attr, value ?? '')
}

function setLink(rel, href, extraAttrs = {}) {
  const attrsSelector = Object.entries(extraAttrs)
    .map(([k, v]) => `[${k}="${v}"]`)
    .join('')
  const selector = `link[rel="${rel}"]${attrsSelector}`
  let el = document.head.querySelector(selector)
  if (!el) {
    el = document.createElement('link')
    el.setAttribute('rel', rel)
    for (const [k, v] of Object.entries(extraAttrs)) el.setAttribute(k, v)
    document.head.appendChild(el)
  }
  el.setAttribute('href', href)
}

function setJsonLd(id, data) {
  let el = document.head.querySelector(`script[data-jsonld="${id}"]`)
  if (data == null) {
    if (el) el.remove()
    return
  }
  if (!el) {
    el = document.createElement('script')
    el.setAttribute('type', 'application/ld+json')
    el.setAttribute('data-jsonld', id)
    document.head.appendChild(el)
  }
  el.textContent = JSON.stringify(data)
}

const LANGS = ['fr', 'ar', 'en']

export function usePageMeta({
  title,
  description,
  image,
  url,
  type = 'article',
  language,
  jsonLd = null,
  jsonLdId = 'page',
}) {
  useEffect(() => {
    const fullTitle = title ? `${title} — Athar` : 'Athar — Guide du patrimoine algérien'
    const canonicalUrl = url || window.location.origin + window.location.pathname

    document.title = fullTitle
    if (language) document.documentElement.lang = language
    document.documentElement.dir = language === 'ar' ? 'rtl' : 'ltr'

    setMeta('meta[name="description"]', 'content', description || '')
    setMeta('meta[property="og:site_name"]', 'content', 'Athar')
    setMeta('meta[property="og:title"]', 'content', fullTitle)
    setMeta('meta[property="og:description"]', 'content', description || '')
    setMeta('meta[property="og:type"]', 'content', type)
    setMeta('meta[property="og:url"]', 'content', canonicalUrl)
    setMeta('meta[property="og:locale"]', 'content',
      language === 'ar' ? 'ar_DZ' : language === 'en' ? 'en_US' : 'fr_FR')
    if (image) setMeta('meta[property="og:image"]', 'content', image)
    setMeta('meta[name="twitter:card"]', 'content', image ? 'summary_large_image' : 'summary')
    setMeta('meta[name="twitter:title"]', 'content', fullTitle)
    setMeta('meta[name="twitter:description"]', 'content', description || '')
    if (image) setMeta('meta[name="twitter:image"]', 'content', image)

    setLink('canonical', canonicalUrl)
    for (const lang of LANGS) {
      const u = new URL(canonicalUrl)
      u.searchParams.set('lang', lang)
      setLink('alternate', u.toString(), { hreflang: lang })
    }
    const uDefault = new URL(canonicalUrl)
    setLink('alternate', uDefault.toString(), { hreflang: 'x-default' })

    setJsonLd(jsonLdId, jsonLd)
  }, [title, description, image, url, type, language, jsonLd, jsonLdId])
}

const CATEGORY_SCHEMA = {
  romain: 'ArchaeologicalSite',
  prehistorique: 'ArchaeologicalSite',
  religieux: 'PlaceOfWorship',
  islamique: 'PlaceOfWorship',
  casbah: 'TouristAttraction',
  colonial: 'LandmarksOrHistoricalBuildings',
  moderne: 'LandmarksOrHistoricalBuildings',
  naturel: 'TouristAttraction',
}

export function buildSiteJsonLd(site, translation) {
  if (!site) return null
  const type = CATEGORY_SCHEMA[site.category] || 'TouristAttraction'
  return {
    '@context': 'https://schema.org',
    '@type': type,
    name: translation?.name || site.slug,
    description: translation?.description,
    image: site.image_path ? [site.image_path] : undefined,
    address: {
      '@type': 'PostalAddress',
      addressRegion: site.wilaya,
      addressCountry: 'DZ',
    },
    geo: site.latitude != null && site.longitude != null ? {
      '@type': 'GeoCoordinates',
      latitude: site.latitude,
      longitude: site.longitude,
    } : undefined,
    aggregateRating: site.reviews_avg_rating ? {
      '@type': 'AggregateRating',
      ratingValue: Number(site.reviews_avg_rating).toFixed(1),
      reviewCount: site.reviews_count || 1,
    } : undefined,
  }
}

export function buildItineraryJsonLd(itinerary) {
  if (!itinerary) return null
  return {
    '@context': 'https://schema.org',
    '@type': 'TouristTrip',
    name: itinerary.title,
    description: itinerary.summary,
    image: itinerary.cover_image ? [itinerary.cover_image] : undefined,
    touristType: itinerary.theme,
    itinerary: {
      '@type': 'ItemList',
      itemListElement: itinerary.sites.map((site, idx) => ({
        '@type': 'ListItem',
        position: idx + 1,
        item: {
          '@type': 'TouristAttraction',
          name: site.name,
          address: {
            '@type': 'PostalAddress',
            addressRegion: site.wilaya,
            addressCountry: 'DZ',
          },
        },
      })),
    },
  }
}
