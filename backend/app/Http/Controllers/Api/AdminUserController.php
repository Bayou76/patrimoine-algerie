<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Contrôleur AdminUserController — gestion des comptes utilisateurs
 * depuis l'espace admin (liste, modification, suppression).
 *
 * Même principe que les autres contrôleurs admin : authorizeAdmin()
 * renvoie 403 si l'utilisateur courant n'a pas is_admin = true.
 */
class AdminUserController extends Controller
{
    /** GET /api/admin/users — liste pour le tableau de gestion. */
    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        return User::withCount(['reviews', 'siteInteractions'])
            ->latest()
            ->get(['id', 'name', 'email', 'is_admin', 'created_at']);
    }

    /** PUT /api/admin/users/{user} — modification (nom, email, statut admin). */
    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'is_admin' => ['required', 'boolean'],
        ]);

        $user->update($validated);

        return $user;
    }

    /** DELETE /api/admin/users/{user} — suppression du compte. */
    public function destroy(Request $request, User $user)
    {
        $this->authorizeAdmin($request);

        // On empêche un admin de se supprimer lui-même : ça éviterait de se
        // retrouver sans aucun compte admin restant pour gérer le site.
        abort_if($user->id === $request->user()->id, 422, 'Vous ne pouvez pas supprimer votre propre compte.');

        $user->delete();

        return response()->noContent();
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }
}
