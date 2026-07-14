<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\FindsOrCreatesSocialUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

/**
 * Contrôleur FacebookAuthController — connexion « Se connecter avec Facebook ».
 *
 * Le frontend utilise le SDK JS Facebook (FB.login) qui renvoie un
 * `accessToken` de courte durée. On ne fait confiance à ce token qu'après
 * l'avoir fait valider par Facebook lui-même (endpoint debug_token, avec
 * notre propre app access token) — ça garantit qu'il a bien été émis pour
 * NOTRE app et pas une autre.
 */
class FacebookAuthController extends Controller
{
    use FindsOrCreatesSocialUser;

    /** POST /api/auth/facebook — connexion ou création de compte via Facebook. */
    public function login(Request $request)
    {
        $request->validate(['access_token' => ['required', 'string']]);
        $userToken = $request->string('access_token');

        $appId = config('services.facebook.client_id');
        $appSecret = config('services.facebook.client_secret');

        // On vérifie que ce token a bien été émis pour notre app, avant de
        // s'en servir pour récupérer les infos du profil.
        $debug = Http::get('https://graph.facebook.com/debug_token', [
            'input_token' => $userToken,
            'access_token' => "{$appId}|{$appSecret}",
        ]);

        $debugData = $debug->json('data');

        if (! $debug->ok() || ! ($debugData['is_valid'] ?? false) || ($debugData['app_id'] ?? null) !== $appId) {
            throw ValidationException::withMessages([
                'access_token' => ['Jeton Facebook invalide.'],
            ]);
        }

        $profile = Http::get('https://graph.facebook.com/me', [
            'fields' => 'id,name,email',
            'access_token' => $userToken,
        ]);

        if (! $profile->ok() || ! $profile->json('email')) {
            // Facebook autorise un compte sans email public : sans email on ne
            // peut pas créer/relier de compte de façon fiable.
            throw ValidationException::withMessages([
                'access_token' => ["Impossible de récupérer l'email de ce compte Facebook."],
            ]);
        }

        $payload = $profile->json();

        $user = $this->findOrCreateSocialUser(
            'facebook_id',
            $payload['id'],
            $payload['email'],
            $payload['name'] ?? explode('@', $payload['email'])[0],
        );

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ]);
    }
}
