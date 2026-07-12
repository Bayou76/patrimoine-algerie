<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Contrôleur AuthController — gère l'inscription, la connexion, la déconnexion.
 *
 * On utilise Laravel Sanctum : à chaque login/register, on renvoie un token
 * que le frontend stocke dans localStorage et envoie dans le header
 * Authorization: Bearer <token> pour toutes les requêtes authentifiées.
 */
class AuthController extends Controller
{
    /** POST /api/register — création d'un compte. */
    public function register(Request $request)
    {
        // Validation : Laravel renvoie automatiquement une 422 avec les erreurs
        // si les règles ne sont pas respectées. `confirmed` cherche un champ
        // password_confirmation dans la requête et vérifie qu'il matche.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Le mot de passe est automatiquement hashé grâce au cast 'password' => 'hashed'
        // défini dans le modèle User.
        $user = User::create($validated);

        // On crée un token Sanctum à la volée. plainTextToken = le token en clair,
        // à ne renvoyer qu'ici (après il n'existe que hashé en base).
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ], 201);
    }

    /** POST /api/login — connexion et génération d'un nouveau token. */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Hash::check compare un mot de passe en clair avec sa version hashée.
        // On lance manuellement une ValidationException pour renvoyer une 422
        // avec un joli message d'erreur côté frontend.
        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects.'],
            ]);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ]);
    }

    /** POST /api/logout — révoque le token courant (protège du vol). */
    public function logout(Request $request)
    {
        // currentAccessToken() = le token qui a servi à authentifier la requête.
        // Le supprimer le rend inutilisable même si quelqu'un l'a intercepté.
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Deconnecte.']);
    }
}
