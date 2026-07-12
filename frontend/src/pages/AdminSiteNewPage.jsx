/**
 * AdminSiteNewPage — page /admin/sites/nouveau : création d'un site.
 *
 * Simple wrapper autour de AdminSiteForm : il fournit un onSubmit vide et
 * redirige vers la fiche du site créé en cas de succès.
 * La logique complexe (validation, upload, multilingue) est dans le composant.
 */

import { Link, Navigate, useNavigate } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { api } from '../services/api'
import { useAuth } from '../context/AuthContext'
import AdminSiteForm from '../components/AdminSiteForm'

function AdminSiteNewPage() {
  const { user, loading } = useAuth()
  const { t } = useTranslation()
  const navigate = useNavigate()

  // Gardes de sécurité identiques à AdminPage.
  if (loading) return <p className="p-6 text-teal-900">{t('me.loading')}</p>
  if (!user) return <Navigate to="/login" replace />
  if (!user.is_admin) return <Navigate to="/" replace />

  const handleSubmit = async (payload) => {
    const res = await api.adminCreateSite(payload)
    // L'API renvoie { slug }, on redirige direct vers la fiche pour vérifier.
    navigate(`/sites/${res.slug}`)
  }

  return (
    <div className="max-w-4xl mx-auto p-6">
      <Link to="/admin" className="text-sm text-teal-900/70 hover:text-teal-950">
        {t('admin.back_to_admin')}
      </Link>
      <h1 className="font-display font-800 text-3xl text-teal-950 mt-2 mb-6">
        {t('admin.new_site_title')}
      </h1>
      {/* Pas de `initial` prop → formulaire vide (mode création) */}
      <AdminSiteForm onSubmit={handleSubmit} submitLabel={t('admin.creating')} />
    </div>
  )
}

export default AdminSiteNewPage
