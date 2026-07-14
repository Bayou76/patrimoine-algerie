/**
 * AdminUsersPage — page /admin/utilisateurs : liste des comptes inscrits.
 * Permet de modifier le nom/email/statut admin d'un compte, ou de le supprimer.
 * Édition faite directement en ligne (pas de page séparée), vu la simplicité
 * des champs concernés.
 */

import { useEffect, useState } from 'react'
import { Link, Navigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { api } from '../services/api'

function EditRow({ user, onCancel, onSaved }) {
  const [name, setName] = useState(user.name)
  const [email, setEmail] = useState(user.email)
  const [isAdmin, setIsAdmin] = useState(user.is_admin)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState(null)

  const handleSave = async () => {
    setSaving(true)
    setError(null)
    try {
      const updated = await api.adminUpdateUser(user.id, { name, email, is_admin: isAdmin })
      onSaved(updated)
    } catch {
      setError("Enregistrement impossible (email peut-être déjà utilisé).")
    } finally {
      setSaving(false)
    }
  }

  return (
    <div className="bg-white rounded-xl border border-terracotta-300 shadow-sm p-4 flex flex-col gap-3">
      <div className="grid sm:grid-cols-2 gap-3">
        <label className="flex flex-col gap-1 text-sm text-teal-900/70">
          Nom
          <input
            value={name}
            onChange={(e) => setName(e.target.value)}
            className="border border-sand-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
          />
        </label>
        <label className="flex flex-col gap-1 text-sm text-teal-900/70">
          Email
          <input
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="border border-sand-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
          />
        </label>
      </div>
      <label className="flex items-center gap-2 text-sm text-teal-900">
        <input type="checkbox" checked={isAdmin} onChange={(e) => setIsAdmin(e.target.checked)} />
        Administrateur
      </label>
      {error && <p className="text-sm text-terracotta-600">{error}</p>}
      <div className="flex gap-2 justify-end">
        <button
          type="button"
          onClick={onCancel}
          className="text-sm text-teal-900/70 hover:text-teal-950 px-3 py-1.5"
        >
          Annuler
        </button>
        <button
          type="button"
          disabled={saving}
          onClick={handleSave}
          className="text-sm bg-teal-800 hover:bg-teal-900 disabled:opacity-50 text-white font-600 rounded-full px-4 py-1.5 transition"
        >
          {saving ? 'Enregistrement...' : 'Enregistrer'}
        </button>
      </div>
    </div>
  )
}

function AdminUsersPage() {
  const { user, loading } = useAuth()
  const [users, setUsers] = useState([])
  const [usersLoading, setUsersLoading] = useState(true)
  const [editingId, setEditingId] = useState(null)

  const load = () => {
    setUsersLoading(true)
    api.adminGetUsers().then(setUsers).finally(() => setUsersLoading(false))
  }

  useEffect(() => {
    if (!user?.is_admin) return
    load()
  }, [user])

  if (loading) return <p className="p-6 text-teal-900">Chargement...</p>
  if (!user) return <Navigate to="/login" replace />
  if (!user.is_admin) return <p className="p-6 text-terracotta-600">Accès réservé aux administrateurs.</p>

  const handleDelete = async (target) => {
    if (!confirm(`Supprimer définitivement le compte de « ${target.name} » ?`)) return
    try {
      await api.adminDeleteUser(target.id)
      setUsers((prev) => prev.filter((u) => u.id !== target.id))
    } catch (err) {
      alert(err?.message || 'Suppression impossible.')
    }
  }

  return (
    <div className="max-w-4xl mx-auto p-6">
      <div className="mb-6">
        <Link to="/admin" className="text-sm text-teal-900/70 hover:text-teal-950">← Retour à l'administration</Link>
        <h1 className="font-display font-800 text-3xl text-teal-950 mt-2">Utilisateurs</h1>
        <p className="text-teal-900/70 text-sm mt-1">{users.length} compte{users.length > 1 ? 's' : ''} inscrit{users.length > 1 ? 's' : ''}</p>
      </div>

      {usersLoading ? (
        <p className="text-teal-900">Chargement...</p>
      ) : (
        <div className="flex flex-col gap-2">
          {users.map((u) =>
            editingId === u.id ? (
              <EditRow
                key={u.id}
                user={u}
                onCancel={() => setEditingId(null)}
                onSaved={(updated) => {
                  setUsers((prev) => prev.map((x) => (x.id === updated.id ? updated : x)))
                  setEditingId(null)
                }}
              />
            ) : (
              <div key={u.id} className="bg-white rounded-xl border border-sand-200 shadow-sm p-4 flex items-center gap-3">
                <div className="flex-1 min-w-0">
                  <p className="font-display font-700 text-teal-950 truncate">
                    {u.name}
                    {u.is_admin && (
                      <span className="ml-2 inline-block text-[10px] font-600 bg-gold-400/20 text-gold-600 px-2 py-0.5 rounded-full align-middle">
                        Admin
                      </span>
                    )}
                  </p>
                  <p className="text-xs text-teal-900/60 mb-1">{u.email}</p>
                  <p className="text-[11px] text-teal-900/50">
                    {u.reviews_count} avis · {u.site_interactions_count} interaction{u.site_interactions_count > 1 ? 's' : ''} · inscrit le{' '}
                    {new Date(u.created_at).toLocaleDateString('fr-FR')}
                  </p>
                </div>
                <div className="flex gap-2 shrink-0">
                  <button
                    type="button"
                    onClick={() => setEditingId(u.id)}
                    className="text-sm bg-teal-800 hover:bg-teal-900 text-white font-600 rounded-full px-3 py-1.5 transition"
                  >
                    Modifier
                  </button>
                  <button
                    type="button"
                    onClick={() => handleDelete(u)}
                    className="text-sm bg-white border border-terracotta-400 hover:bg-terracotta-50 text-terracotta-600 font-600 rounded-full px-3 py-1.5 transition"
                  >
                    Supprimer
                  </button>
                </div>
              </div>
            )
          )}
          {users.length === 0 && <p className="text-sm text-teal-900/60">Aucun utilisateur.</p>}
        </div>
      )}
    </div>
  )
}

export default AdminUsersPage
