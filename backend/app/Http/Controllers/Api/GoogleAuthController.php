<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\FindsOrCreatesSocialUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

/**
 * Contrôleur GoogleAuthController — connexion « Se connecter avec Google ».
 *
 * Le frontend utilise Google Identity Services (bouton JS de Google) qui
 * renvoie un `credential` : un ID Token JWT signé par Google, PAS un mot de
 * passe. On ne fait confiance à ce token qu'après l'avoir fait valider par
 * Google lui-même (endpoint tokeninfo) — on ne décode jamais le JWT nous-mêmes.
 *
 * Avantage de cette approche vs. Socialite « redirect flow » classique :
 * pas de session serveur à gérer entre le frontend (Vercel) et l'API
 * (Railway), qui sont deux domaines différents — tout reste stateless,
 * cohérent avec le reste de l'auth (Sanctum tokens).
 */
class GoogleAuthController extends Controller
{
    use FindsOrCreatesSocialUser;

    /** POST /api/auth/google — connexion ou création de compte via Google. */
    public function login(Request $request)
    {
        $request->validate(['credential' => ['required', 'string']]);

        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->string('credential'),
        ]);

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'credential' => ['Jeton Google invalide.'],
            ]);
        }

        $payload = $response->json();

        // aud = à qui Google a délivré ce token. S'il ne correspond pas à
        // notre client_id, quelqu'un essaie de nous faire accepter un token
        // émis pour une autre application.
        if (($payload['aud'] ?? null) !== config('services.google.client_id')) {
            throw ValidationException::withMessages([
                'credential' => ['Jeton Google invalide.'],
            ]);
        }

        $user = $this->findOrCreateSocialUser(
            'google_id',
            $payload['sub'],
            $payload['email'],
            $payload['name'] ?? explode('@', $payload['email'])[0],
        );

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ]);
    }
}
