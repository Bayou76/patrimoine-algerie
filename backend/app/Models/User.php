<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modèle User — un compte utilisateur d'Athar.
 *
 * Hérite de Authenticatable (fourni par Laravel) : ça donne accès aux
 * fonctions de login/logout/hash de mot de passe sans les réécrire.
 *
 * HasApiTokens (Sanctum) : permet de générer des tokens Bearer pour l'API.
 * C'est ce qui authentifie le frontend React auprès du backend Laravel.
 */
#[Fillable(['name', 'email', 'password', 'is_admin'])]
#[Hidden(['password', 'remember_token'])] // Ces champs ne sortent jamais en JSON
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Conversions automatiques : quand Laravel lit `is_admin` en BDD,
     * il renvoie un vrai booléen PHP (true/false), pas 0/1. `password` est
     * automatiquement hashé quand on écrit, on n'a rien à faire nous-mêmes.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /** Les avis publiés par cet utilisateur. */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /** Les favoris + « déjà visités » de cet utilisateur. */
    public function siteInteractions()
    {
        return $this->hasMany(SiteUserInteraction::class);
    }

    /** Les étapes de « Mon voyage », ordonnées. */
    public function tripSites()
    {
        return $this->hasMany(UserTripSite::class)->orderBy('position');
    }
}
