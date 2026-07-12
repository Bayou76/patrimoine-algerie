/**
 * AdminSiteEditPage — page /admin/sites/:id/modifier : édition d'un site.
 *
 * Charge d'abord les données du site via l'API admin (structure groupée par
 * langue et par année pour la timeline), puis passe le tout à AdminSiteForm
 * qui va préremplir les champs.
 */

import { useEffect, useState } from 'react'
import { Link, Navigate, useNavigate, useParams } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { api } from '../services/api'
import { useAuth } from '../context/AuthContext'
import AdminSiteForm from '../components/AdminSiteForm'

function AdminSiteEditPage() {
  const { id } = useParams() // Récupère :id depuis l'URL
  const { user, loading } = useAuth()
  const { t } = useTranslation()
  const navigate = useNavigate()
  const [initial, setInitial] = useState(null)
  const [error, setError] = useState(null)

  useEffect(() => {
    // On charge seulement si admin (évite un 403 dans les logs).
    if (!user?.is_admin) return
    api.adminGetSite(id).then(setInitial).catch(() => setError(t('detail.not_found')))
  }, [user, id, t])

  // Cascade de gardes de sécurité + états.
  if (loading) return <p className="p-6 text-teal-900">{t('me.loading')}</p>
  if (!user) return <Navigate to="/login" replace />
  if (!user.is_admin) return <Navigate to="/" replace />
  if (error) return <p className="p-6 text-terracotta-600">{error}</p>
  if (!initial) return <p className="p-6 text-teal-900">{t('me.loading')}</p>

  const handleSubmit = async (payload) => {
    // Note : le slug peut avoir changé pendant l'édition, on utilise celui
    // renvoyé par l'API pour rediriger vers la bonne URL.
    const res = await api.adminUpdateSite(id, payload)
    navigate(`/sites/${res.slug}`)
  }

  return (
    <div className="max-w-4xl mx-auto p-6">
      <Link to="/admin" className="text-sm text-teal-900/70 hover:text-teal-950">
        {t('admin.back_to_admin')}
      </Link>
      <h1 className="font-display font-800 text-3xl text-teal-950 mt-2 mb-6">
        {t('admin.edit_site_title', { name: initial.translations?.fr?.name ?? initial.slug })}
      </h1>
      {/* initial rempli → le formulaire s'ouvre pré-rempli (mode édition) */}
      <AdminSiteForm initial={initial} onSubmit={handleSubmit} submitLabel={t('admin.saving')} />
    </div>
  )
}

export default AdminSiteEditPage
