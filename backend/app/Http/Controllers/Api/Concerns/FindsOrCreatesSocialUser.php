<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Logique commune à toutes les connexions sociales (Google, Facebook...) :
 * retrouver un compte déjà lié au provider, sinon le relier à un compte
 * existant avec le même email, sinon en créer un nouveau. Envoie l'email
 * de bienvenue uniquement pour une vraie création.
 */
trait FindsOrCreatesSocialUser
{
    private function findOrCreateSocialUser(string $idColumn, string $providerId, string $email, string $name): User
    {
        $user = User::where($idColumn, $providerId)->first();

        if ($user) {
            return $user;
        }

        // Un compte existe peut-être déjà avec cet email (inscrit classiquement,
        // ou via un autre provider) : on le relie plutôt que de créer un doublon.
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([$idColumn => $providerId]);

            return $user;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            $idColumn => $providerId,
            'password' => Str::random(40), // Jamais utilisé pour se connecter, mais évite un champ vide.
        ]);

        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Throwable $e) {
            report($e);
        }

        return $user;
    }
}
