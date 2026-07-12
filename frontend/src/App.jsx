/**
 * App.jsx — le composant racine.
 *
 * Responsable de deux choses :
 *   1. La barre de navigation (Nav) — desktop horizontale ou drawer mobile
 *   2. Le routeur : quelle page afficher selon l'URL
 *
 * Structure :
 *   <div>
 *     <Nav />
 *     <main><Routes>...</Routes></main>
 *     <Footer />
 *   </div>
 */

import { useEffect, useState } from 'react'
import { Link, Route, Routes, useLocation } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { useAuth } from './context/AuthContext'
import HomePage from './pages/HomePage'
import SiteDetailPage from './pages/SiteDetailPage'
import GlobalTimelinePage from './pages/GlobalTimelinePage'
import ItinerariesPage from './pages/ItinerariesPage'
import ItineraryDetailPage from './pages/ItineraryDetailPage'
import PracticalInfoPage from './pages/PracticalInfoPage'
import LoginPage from './pages/LoginPage'
import RegisterPage from './pages/RegisterPage'
import MePage from './pages/MePage'
import AdminPage from './pages/AdminPage'
import AdminSiteNewPage from './pages/AdminSiteNewPage'
import AdminSiteEditPage from './pages/AdminSiteEditPage'
import AdminItinerariesPage from './pages/AdminItinerariesPage'
import AdminItineraryNewPage from './pages/AdminItineraryNewPage'
import AdminItineraryEditPage from './pages/AdminItineraryEditPage'
import ProposeItineraryPage from './pages/ProposeItineraryPage'
import LanguageSwitcher from './components/LanguageSwitcher'
import ThemeToggle from './components/ThemeToggle'
import Footer from './components/Footer'
import InstallPrompt from './components/InstallPrompt'
import { trackPageView } from './utils/analytics'

/**
 * Nav — la barre du haut, responsive.
 *   ≥ md (768px) : liens horizontaux classiques
 *   < md         : logo + burger ☰ qui ouvre un drawer plein écran
 */
function Nav() {
  const { user, logout } = useAuth()
  const { t } = useTranslation()
  const [open, setOpen] = useState(false) // drawer mobile ouvert ?
  const location = useLocation() // URL courante fournie par react-router

  // À chaque changement d'URL, on ferme automatiquement le drawer.
  // Sinon l'utilisateur clique un lien mais le menu reste ouvert.
  useEffect(() => {
    setOpen(false)
  }, [location.pathname])

  // Bloque le scroll du body quand le drawer est ouvert.
  // Le return est la fonction de cleanup : si le composant est démonté avec
  // le drawer ouvert, on restaure quand même le scroll.
  useEffect(() => {
    document.body.style.overflow = open ? 'hidden' : ''
    return () => { document.body.style.overflow = '' }
  }, [open])

  return (
    <nav className="sticky top-0 z-[1000] bg-teal-950 text-sand-100 shadow-md">
      <div className="flex items-center justify-between gap-4 px-5 py-3">
        <Link to="/" className="font-display font-800 text-lg text-white tracking-tight whitespace-nowrap">
          🏺 Athar
        </Link>

        {/* --- Version desktop (>=768px) --- */}
        {/* hidden md:flex : caché en mobile, flex à partir de md */}
        <div className="hidden md:flex items-center gap-3 text-sm">
          <Link to="/chronologie" className="text-sand-100 hover:text-terracotta-400 transition">
            📜 {t('nav.timeline')}
          </Link>
          <Link to="/itineraires" className="text-sand-100 hover:text-terracotta-400 transition">
            🗺️ {t('nav.itineraries')}
          </Link>
          <Link to="/infos-pratiques" className="text-sand-100 hover:text-terracotta-400 transition">
            ℹ️ {t('nav.practical_info')}
          </Link>
          <ThemeToggle />
          <LanguageSwitcher />
          {/* Affichage conditionnel selon le statut de connexion */}
          {user ? (
            <>
              {user.is_admin && (
                <Link to="/admin" className="text-gold-400 hover:text-gold-500 font-600 transition">
                  {t('nav.administration')}
                </Link>
              )}
              <Link to="/me" className="text-sand-200 hover:text-terracotta-400 transition">
                {t('nav.greeting', { name: user.name })}
              </Link>
              <button onClick={logout} className="text-terracotta-400 hover:text-terracotta-500 font-600 transition">
                {t('nav.logout')}
              </button>
            </>
          ) : (
            <>
              <Link to="/login" className="hover:text-terracotta-400 transition">
                {t('nav.login')}
              </Link>
              <Link
                to="/register"
                className="bg-terracotta-500 hover:bg-terracotta-600 text-white px-3 py-1.5 rounded-full font-600 transition"
              >
                {t('nav.register')}
              </Link>
            </>
          )}
        </div>

        {/* --- Version mobile : bouton burger --- */}
        <div className="md:hidden flex items-center gap-2">
          <ThemeToggle />
          <button
            type="button"
            onClick={() => setOpen((v) => !v)}
            aria-label="Menu"
            aria-expanded={open}
            className="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 text-white text-xl transition"
          >
            {open ? '✕' : '☰'}
          </button>
        </div>
      </div>

      {/* --- Drawer mobile qui glisse depuis le haut --- */}
      {open && (
        <>
          {/* Overlay flouté qui ferme le drawer au clic. */}
          <div
            className="md:hidden fixed inset-0 top-[64px] bg-teal-950/70 backdrop-blur-sm z-[998]"
            onClick={() => setOpen(false)}
          />
          {/* Panneau des liens */}
          <div className="md:hidden fixed left-0 right-0 top-[64px] bg-teal-950 border-t border-white/10 shadow-xl z-[999] animate-slide-down">
            <div className="flex flex-col px-5 py-4 gap-1 text-base">
              <Link to="/chronologie" className="py-3 border-b border-white/5 hover:text-terracotta-400 transition">
                📜 {t('nav.timeline')}
              </Link>
              <Link to="/itineraires" className="py-3 border-b border-white/5 hover:text-terracotta-400 transition">
                🗺️ {t('nav.itineraries')}
              </Link>
              <Link to="/infos-pratiques" className="py-3 border-b border-white/5 hover:text-terracotta-400 transition">
                ℹ️ {t('nav.practical_info')}
              </Link>
              <div className="py-3 border-b border-white/5">
                <LanguageSwitcher />
              </div>
              {user ? (
                <>
                  {user.is_admin && (
                    <Link to="/admin" className="py-3 border-b border-white/5 text-gold-400 font-600">
                      {t('nav.administration')}
                    </Link>
                  )}
                  <Link to="/me" className="py-3 border-b border-white/5 text-sand-200">
                    {t('nav.greeting', { name: user.name })}
                  </Link>
                  <button
                    onClick={() => { setOpen(false); logout() }}
                    className="py-3 text-start text-terracotta-400 font-600"
                  >
                    {t('nav.logout')}
                  </button>
                </>
              ) : (
                <div className="flex gap-2 pt-3">
                  <Link to="/login" className="flex-1 text-center py-2 rounded-full border border-white/20">
                    {t('nav.login')}
                  </Link>
                  <Link to="/register" className="flex-1 text-center py-2 rounded-full bg-terracotta-500 text-white font-600">
                    {t('nav.register')}
                  </Link>
                </div>
              )}
            </div>
          </div>
        </>
      )}
    </nav>
  )
}

/**
 * App — assemble la structure globale : Nav + zone principale routée + Footer.
 * <main> prend l'espace restant (flex-1) pour pousser le footer en bas
 * même quand la page est courte.
 */
function App() {
  const location = useLocation()

  // Envoie une vue de page à Google Analytics à chaque changement de route.
  // Nécessaire car send_page_view est désactivé dans index.html (voir
  // src/utils/analytics.js pour le pourquoi).
  useEffect(() => {
    trackPageView(location.pathname + location.search, document.title)
  }, [location.pathname, location.search])

  return (
    <div className="min-h-screen flex flex-col">
      <Nav />
      <main className="flex-1">
        {/* Routes = déclare quelle page afficher selon l'URL.
            :slug et :id sont des paramètres dynamiques capturés par useParams. */}
        <Routes>
          <Route path="/" element={<HomePage />} />
          <Route path="/sites/:slug" element={<SiteDetailPage />} />
          <Route path="/chronologie" element={<GlobalTimelinePage />} />
          <Route path="/itineraires" element={<ItinerariesPage />} />
          <Route path="/itineraires/proposer" element={<ProposeItineraryPage />} />
          <Route path="/itineraires/:slug" element={<ItineraryDetailPage />} />
          <Route path="/infos-pratiques" element={<PracticalInfoPage />} />
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />
          <Route path="/me" element={<MePage />} />
          <Route path="/admin" element={<AdminPage />} />
          <Route path="/admin/sites/nouveau" element={<AdminSiteNewPage />} />
          <Route path="/admin/sites/:id/modifier" element={<AdminSiteEditPage />} />
          <Route path="/admin/itineraires" element={<AdminItinerariesPage />} />
          <Route path="/admin/itineraires/nouveau" element={<AdminItineraryNewPage />} />
          <Route path="/admin/itineraires/:id/modifier" element={<AdminItineraryEditPage />} />
        </Routes>
      </main>
      <Footer />
      <InstallPrompt />
    </div>
  )
}

export default App
