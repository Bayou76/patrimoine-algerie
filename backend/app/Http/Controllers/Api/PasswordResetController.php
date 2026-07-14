<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordEmail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Contrôleur PasswordResetController — mot de passe oublié.
 *
 * Utilise le broker Password de Laravel (table password_reset_tokens, déjà
 * migrée depuis le départ) mais avec un envoi d'email et une URL de reset
 * personnalisés : Laravel génère le token, nous décidons de l'URL (qui pointe
 * vers le FRONTEND React, pas une route Blade backend) et du template email.
 */
class PasswordResetController extends Controller
{
    /** POST /api/forgot-password — génère un token et envoie l'email de reset. */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink(
            $request->only('email'),
            function (User $user, string $token) {
                $resetUrl = sprintf(
                    '%s/reinitialiser-mot-de-passe?token=%s&email=%s',
                    rtrim(env('FRONTEND_URL', 'https://patrimoine-algerie.vercel.app'), '/'),
                    $token,
                    urlencode($user->email),
                );

                Mail::to($user->email)->send(new ResetPasswordEmail($resetUrl));
            }
        );

        // On renvoie toujours un succès générique, que l'email existe ou non :
        // ça évite qu'un attaquant devine quels emails sont inscrits sur le site.
        return response()->json([
            'message' => 'Si un compte existe avec cet email, un lien de réinitialisation a été envoyé.',
        ]);
    }

    /** POST /api/reset-password — vérifie le token et enregistre le nouveau mot de passe. */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password) {
                $user->update(['password' => $password]);
                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => ['Ce lien de réinitialisation est invalide ou a expiré.'],
            ]);
        }

        return response()->json(['message' => 'Mot de passe mis à jour.']);
    }
}
